<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_report_model extends CI_Model 
{    
    // INVENTORY VALUE
    public function get_inventory_value_report($search, $department_code, $subdepartment_code, $ppn, $iLength = null, $iStart= null, $iOrder= null)
    {
        $column = array(null, 'code', 'name', 'qty');
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, product.hpp, unit.name AS unit')
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                         ->join('unit', 'unit.id = product_unit.unit_id')
                         ->where('product.deleted', 0)->where('product.status', 1);
        if($search != "")
        {
			$this->db->like('product.code', $search);
			$this->db->or_like('product.name', $search);
        }     
        if($department_code != "")
        {
            $this->db->where('product.department_code', $department_code);
        }   
        if($department_code != "" && $subdepartment_code != "")
        {
            $this->db->where('product.department_code', $department_code);
            $this->db->where('product.subdepartment_code', $subdepartment_code);
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
        return $this->db->group_by('product.code')->get();
    }

    public function product_usage($from_date, $to_date, $user_code)
    {
        $this->db->select('pug.*, user.code AS code_u, user.name AS name_u')
				 ->from('product_usage AS pug')
				 ->join('employee AS user', 'user.code = pug.employee_code')
                 ->where('pug.deleted', 0)->where('pug.do_status', 1);
        if($from_date != "")                 
        {
            $this->db->where('pug.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('pug.date <=', $to_date);            
		}		
        if($user_code != "")                 
        {
            $this->db->where('pug.employee_code', $user_code);
		}
        return $this->db->group_by('pug.id')->order_by('pug.date', 'ASC')->get()->result_array();
    }

    public function detail_product_usage($product_usage_id)
    {
        return $this->db->select('product.code AS code_p, product.name AS name_p, 
                    warehouse.code AS code_w, warehouse.name AS name_w, 
                    product_usage_detail.qty, unit.code AS code_u, 
                    unit.name AS name_u, product_usage_detail.price, product_usage_detail.total')
                        ->from('product_usage_detail')
                        ->join('product', 'product.id = product_usage_detail.product_id')
                        ->join('warehouse', 'warehouse.id = product_usage_detail.warehouse_id')
                        ->join('unit', 'unit.id = product_usage_detail.unit_id')
                        ->where('product_usage_detail.product_usage_id', $product_usage_id)
                        ->group_by('product_usage_detail.id')->order_by('product.code', 'ASC')
                        ->get()->result_array();
    }
    
    public function print_product_usage_detail_report($from_date, $to_date, $user_code)
    {
        $product_usage = $this->inventory_report->product_usage($from_date, $to_date, $user_code);
        $data = array();
        foreach($product_usage AS $info_product_usage)
        {
            $data[] = [
                'product_usage'		   => $info_product_usage,
                'detail_product_usage' => $this->inventory_report->detail_product_usage($info_product_usage['id'])
            ];
        }                
        return $data;
    }
}