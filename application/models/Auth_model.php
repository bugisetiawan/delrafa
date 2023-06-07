<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model 
{   
    public function check_employee($name)
    {        
        return $this->db->select('employee.code AS code_e, employee.name AS name_e, position.id AS id_p, position.name AS name_p')
                        ->from('employee')->join('position', 'position.id = employee.position_id')
                        ->where('employee.deleted', 0)->where('employee.is_user', 1)->where('employee.name', $name)->get();
    }

    public function check_user($employee_code)
    {        
        return $this->db->select('user.id AS id_u, user.code AS code_u, password, active, start_time, end_time')
                        ->from('user')                        
                        ->where('employee_code', $employee_code)
                        ->get();
    }

    public function verify_user_password($user_id, $module_url, $action)
	{
		return $this->db->select('user.id AS id_u, user.employee_code AS code_e, user.password, access.read, access.detail, access.create, access.update, access.delete')
						->from('user')
						->join('access', 'access.user_id = user.id')
                        ->where('access.user_id', $user_id)->where('access.module_url', $module_url)->where($action, 1)                        
						->group_by('user.id')->order_by('user.id', 'asc')
						->get()->row_array();
    }
    
    public function bypass_module_password()
	{
        $bypass_user_id = [1, 2, 3, 4, 14, 17];
		return $this->db->select('user.id AS id_u, user.employee_code AS code_e, user.password')->from('user')->where_in('id', $bypass_user_id)->get()->result_array();
    }
    
    public function verify_module_password($module_url, $action)
	{
		return $this->db->select('user.id AS id_u, user.employee_code AS code_e, user.password, 
                        access.method, access.read, access.detail, access.create, access.update, access.delete')
						->from('user')
						->join('access', 'access.user_id = user.id', 'left')
						->where('access.module_url', $module_url)->where($action, 1)
						->group_by('user.id')->order_by('user.id', 'asc')
						->get()->result_array();
	}
}