<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promotion_model extends CI_Model 
{	
    public function get_products($department_code = null, $subdepartment_code = null)
    {
        $this->db->select('product.id, product.barcode, product.code, product.name, unit.name AS unit, sum(stock.qty) AS qty,
                sellprice.price_1 AS sellprice,  department.name AS name_d, subdepartment.name AS name_sub_d,
                product.ppn')
                 ->from('product')
                 ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
                 ->join('unit', 'unit.id = product_unit.unit_id', 'inner')
                 ->join('stock', 'stock.product_code = product.code', 'left')		         
		         ->join('department', 'department.code = product.department_code')
                 ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner')
                 ->join('sellprice', 'sellprice.product_code = product.code AND sellprice.default = 1')
                 ->where('product.deleted', 0)
                 ->where('product.status', 1);
        if($department_code != "")
        {
            $this->db->where('department.code', $department_code);
        }   
        if($department_code != "" && $subdepartment_code != "")
        {
            $this->db->where('department.code', $department_code);
            $this->db->where('subdepartment.code', $subdepartment_code);
        }                   

        return $this->db->group_by('product.code')->get()->result_array();
    }
}