<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model 
{
    private $table   = 'employee';

    public function custom_increment()
    {        
        $data   = $this->db->select('code')
                           ->from($this->table)
                           ->where('is_user', 0)
                           ->limit(1)->order_by('id', 'DESC')
                           ->get()->row_array();
        $sub    = (int) substr($data['code'], 5, 10);
        $max    = $sub+1;
        $hasil    = "EMPL-".sprintf("%05s", $max);
        return $hasil;
    } 

    public function get_province($province_id)
    {
        $this->db->select('*');
        $this->db->from('province');
        $this->db->where('id', $province_id);
        return $this->db->get()->row_array();
    }

    public function get_city($province_id, $city_id)
    {
        $this->db->select('name');
        $this->db->from('city');
        $this->db->where('province_id', $province_id);
        $this->db->where('id', $city_id);
        return $this->db->get()->row_array();
    }

    public function get_religion($religion_id)
    {
        $this->db->select('name');
        $this->db->from('religion');        
        $this->db->where('id', $religion_id);
        return $this->db->get()->row_array();
    }

    public function get_education($education_id)
    {
        $this->db->select('name');
        $this->db->from('education');        
        $this->db->where('id', $education_id);
        return $this->db->get()->row_array();
    }

    public function get_born($born_id)
    {
        $this->db->select('name');
        $this->db->from('city');        
        $this->db->where('id', $born_id);
        return $this->db->get()->row_array();
    }

    public function get_position($position_id)
    {
        $this->db->select('name');
        $this->db->from('position');        
        $this->db->where('id', $position_id);
        return $this->db->get()->row_array();
    }
}


