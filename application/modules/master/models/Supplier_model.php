<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model 
{
    private $table   = 'supplier';

    public function supplier_code()
    {
        $data   = $this->db->select_max('code')->from($this->table)->get()->row_array();
        $sub    = (int) substr($data['code'], 5, 10);
        $max    = $sub+1;
        $hasil    = "SUPL-".sprintf("%05s", $max);
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
}