<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_report_model extends CI_Model 
{    
    // INVENTORY VALUE
    public function get_inventory_value_report($search, $department_code, $subdepartment_code, $ppn, $iLength = null, $iStart= null, $iOrder= null)
    {
        $column = array(null, 'code', 'name', 'qty');
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, product.hpp, unit.name AS unit, sum(stock.qty) AS qty,
                         department.name AS name_d, subdepartment.name AS name_sub_d')
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
                         ->join('unit', 'unit.id = product_unit.unit_id', 'inner')
                         ->join('stock', 'stock.product_code = product.code', 'left')
                         ->join('department', 'department.code = product.department_code', 'inner')
                         ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner');
        if($search != "")
        {
            $this->db->like('product.barcode', $search);
			$this->db->or_like('product.code', $search);
			$this->db->or_like('product.name', $search);
        }     
        if($department_code != "")
        {
            $this->db->where('department.code', $department_code);
        }   
        if($department_code != "" && $subdepartment_code != "")
        {
            $this->db->where('department.code', $department_code);
            $this->db->where('subdepartment.code', $subdepartment_code);
        }
        if($ppn != "")
        {
            $this->db->where('product.ppn', $ppn);
        }                                 
        if($iLength != null && $iStart != null)
        {
            if($iLength != '' && $iLength != '-1')
            {
                $this->db->limit($iLength, ($iStart)? $iStart : 0);        
            }
        } 
        if($iOrder != null)
        {                            
            $this->db->order_by($column[$iOrder['0']['column']], $iOrder['0']['dir']);
        } 
        return $this->db->where('product.deleted', 0)
                        ->where('product.status', 1)
                        ->group_by('product.code')
                        ->get();
    }
}
