<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opname_model extends CI_Model 
{	
    // STOCK OPNAME
    public function get_hpp($product_code)
	{
		$data = $this->db->select('hpp')
						->from('product')
						->where('code', $product_code)												
						->get()->row_array();
		return $data['hpp'];
    }
    
	public function stock_opname_code()
	{
        $data = $this->db->select('code')->from('stock_opname')->order_by('id', 'DESC')->limit(1)->get()->row_array();
        $tahun = substr(date('Y'),2,2);
        $format = "STO".$tahun.date('m').date('d');
        if($data)
        {
            $sub_tanggal = substr($data['code'], 7, 2);
            $no 		 = substr($data['code'], -2,2);
            if($sub_tanggal == date('d'))
            {
                $no++;
                $code = $format.sprintf("%02s", $no);
            }
            else
            {
                $no = 1;
                $code = $format.sprintf("%02s", $no);
            }
        }
        else
        {
            $no = 1;
            $code = $format.sprintf("%02s", $no);
        }					 						 
        return $code;
    }

    public function product_stock_opname($department_code = null, $subdepartment_code = null)
    {
        $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, department.name AS name_d, subdepartment.name AS name_sd')
                    ->from('product')
                    ->join('department', 'department.code = product.department_code')
                    ->join('subdepartment', 'subdepartment.department_code = department.code AND subdepartment.code = product.subdepartment_code')
                    ->where('product.deleted', 0)->where('product.status', 1);
        if($department_code != "")
        {
            $this->db->where('department.code', $department_code);
        }   
        if($department_code != "" && $subdepartment_code != "")
        {
            $this->db->where('department.code', $department_code);
            $this->db->where('subdepartment.code', $subdepartment_code);
        }
        return $this->db->group_by('product.id')->get()->result_array();
    }

    public function detail_stock_opname($stock_opname_id)
    {
        return $this->db->select('stock_opname.id, stock_opname.date, stock_opname.code, warehouse.id As warehouse_id,
                        warehouse.name AS warehouse, checker.name As checker, operator.name AS operator, total_product, stock_opname.status')
                        ->from('stock_opname')
                        ->join('warehouse', 'warehouse.id = stock_opname.warehouse_id')
                        ->join('employee AS checker', 'checker.code = stock_opname.checker')
                        ->join('employee AS operator', 'operator.code = stock_opname.operator')						
                        ->where('stock_opname.id', $stock_opname_id)
                        ->get()->row_array();						
    }

    public function detail_product_stock_opname($stock_opname_id, $warehouse_id)
    {
        return $this->db->select('stock_opname_detail.id, product.code AS code_p, product.name AS name_p, unit.id AS id_u,  unit.name AS name_u, 
                        sum(stock.qty) AS stock, stock_opname_detail.adjust, stock_opname_detail.hpp AS hpp')
                        ->from('stock_opname_detail')
                        ->join('product', 'product.code = stock_opname_detail.product_code')
                        ->join('unit', 'unit.id = stock_opname_detail.unit_id', 'inner')
                        ->join('stock', 'stock.product_code = product.code', 'left')
                        ->where('stock.warehouse_id', $warehouse_id)
                        ->where('stock_opname_detail.stock_opname_id', $stock_opname_id)
                        ->group_by('stock_opname_detail.id')
                        ->get()->result_array();
    }
}