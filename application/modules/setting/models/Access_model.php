<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access_model extends CI_Model 
{    

    public function check_access($role_id, $module_id)
    {
        return $this->db->select('id')
						->from('access')
                        ->where('role_id', $role_id)
                        ->where('module_id', $module_id)    
						->get();
    }   
}