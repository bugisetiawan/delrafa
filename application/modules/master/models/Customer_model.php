<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model 
{
    private $table   = 'customer';

    public function customer_code()
    {
        $data   =  $this->db->select_max('code')->from('customer')->get()->row_array();
        $sub    = (int) substr($data['code'], 5, 10);
        $max    = $sub+1;
        $hasil    = "CUST-".sprintf("%05s", $max);
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

    public function detail_customer($code)
    {
        return $this->db->select('customer.*, province.name AS province, city.name AS city, zone.code AS zone')
                        ->from('customer')
                        ->join('province', 'province.id = customer.province_id', 'left')
                        ->join('city', 'city.id = customer.city_id', 'left')
                        ->join('zone', 'zone.id = customer.zone_id', 'left')
                        ->where('customer.deleted', 0)
                        ->where('customer.code', $code)                        
                        ->get()->row_array();
    }
}