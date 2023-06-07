<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class user_model extends CI_Model 
{    
    public function user_code()
    {
        $data   = $this->db->select('code')
                           ->from('employee')
                           ->where('is_user', 1)
                           ->limit(1)->order_by('id', 'DESC')
                           ->get()->row_array();
        $sub    = (int) substr($data['code'], 5, 10);
        $max    = $sub+1;
        $hasil    = "ADMN-".sprintf("%05s", $max);
        return $hasil;
    }

    public function get_employee()
    {
        return $this->db->select('employee.id, employee.code, employee.name')
						->from('employee')->join('user', 'user.employee_code = employee.code', 'left outer')
                        ->where('user.employee_code', null)->where('employee.id >', 3)->where('employee.deleted', 0)->get()->result();
    }

    public function category_master($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 1)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 1)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 1);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }    
    
    public function category_purchase($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 2)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 2)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 2);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }

    public function category_sales($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 3)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 3)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 3);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }

    public function category_inventory($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 4)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 4)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 4);
        }        
        return $this->db->get()->result_array();
    }

    public function category_finance($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 5)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 5)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 5);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }

    public function category_accounting($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 6)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 6)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 6);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }

    public function category_report($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 7)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 7)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 7);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }

    public function category_other($user_id = null)
    {              
        if($user_id != null)
        {
            $this->db->select('access.id AS id_a, access.user_id AS id_u, module.id AS id, module.name AS name, module.method AS module_method, access.method AS access_method')
                    ->from('module')
                    ->join('access', 'access.module_url = module.url', 'left')
                    ->where('module.active', 1)->where('module.category', 8)->where('access.user_id', $user_id)
                    ->or_where('module.active', 1)->where('module.category', 8)->where('access.user_id', null)
                    ->group_by('module.id');
        }
        else
        {            
            $this->db->select('*')->from('module')->where('module.active', 1)->where('module.category', 8);
        }        
        return $this->db->order_by('module.id', 'asc')->get()->result_array();
    }

    public function check_access($user_id, $module_id)
    {
        return $this->db->select('id')
                        ->from('access')
                        ->where('user_id', $user_id)
                        ->where('module_id', $module_id)
                        ->get();
    }

    public function get_detail($user_id)
    {
        return $this->db->select('user.id AS id_u, user.code AS code_u, user.start_time, user.end_time, employee.code AS code_e, employee.name AS name_e')
                        ->from('user')                        
                        ->join('employee', 'employee.code = user.employee_code')
                        ->where('user.id', $user_id)
                        ->get()->row_array();
    }
}