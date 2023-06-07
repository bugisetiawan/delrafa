<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_model extends CI_Model 
{	
    public function get_module($url)
    {
        return $this->db->select('id, url')->from('module')->where('url', $url)->get()->row_array();

    }
    
    public function get_access($user_id, $module_url)
    {        
        return $this->db->select('user_id, module_url, method, read, detail, create, update, delete, printout')
                        ->from('access')
                        ->where('user_id', $user_id)->where('module_url', $module_url)
                        ->get();
    }    
}
