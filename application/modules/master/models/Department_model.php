<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model 
{       
    public function department_code()
    {
        $this->db->select_max('code');
        $this->db->from('department');        
        $data   = $this->db->get()->row_array();
        $sub    = (int) substr($data['code'], 0, 3);
        $max    = $sub+1;
        $hasil    = sprintf("%03s", $max);
        return $hasil;
    }

    public function subdepartment_code($code)
    {
        $this->db->select_max('code');
        $this->db->from('subdepartment');        
        $this->db->where('department_code', $code);
        $data   = $this->db->get()->row_array();
        $sub    = (int) substr($data['code'], 0, 3);
        $max    = $sub+1;
        $hasil    = sprintf("%03s", $max);
        return $hasil;
    }
}


