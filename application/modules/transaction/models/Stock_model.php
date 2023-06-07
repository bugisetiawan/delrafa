<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_model extends CI_Model 
{
	public function get_product($search)
	{		
		return $this->db->select('product.id, product.barcode, product.code, product.name')
					->from('product')
					->like('product.barcode', $search, 'both')
					->or_like('product.code', $search, 'both')
					->or_like('product.name', $search, 'both')
					->group_by('product.id')
					->order_by('product.id', 'ASC')
					->where('product.status', 1)
					->where('product.deleted', 0)
					->get();
	}

	public function get_unit($where)
    {	
		return $this->db->select('unit.id AS id_u, unit.name AS name_u, value, default')
						->from('product_unit')
						->join('unit','unit.id=product_unit.unit_id')
						->where($where)
						->get();
	}

	public function get_hpp($product_code)
	{
		$data = $this->db->select('hpp')
						->from('product')
						->where('code', $product_code)												
						->get()->row_array();
		return $data['hpp'];
	}

	// PRODUCTION
	public function product_production($product_id)
	{
		return $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, 
						bundle.id AS id_b, bundle.code AS code_b, bundle.name AS name_b, bundle.hpp AS hpp_b,
						product_bundle.qty AS qty_b, unit.id AS id_u, unit.name AS name_u, product_unit.value AS value_ub')
						->from('product')
						->join('product_bundle', 'product_bundle.master_product_id = product.id')
						->join('product AS bundle', 'bundle.id = product_bundle.product_id')
						->join('product_unit', 'product_unit.product_id = product_bundle.product_id AND product_unit.unit_id = product_bundle.unit_id')
						->join('unit', 'unit.id = product_bundle.unit_id')
						->where('product.id', $product_id)
						->group_by('bundle.id')
						->get()->result_array();
	}

	public function production_code()
	{
		$data = $this->db->select('code')->from('production')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "PRD".$tahun.date('m').date('d');
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

	public function detail_production($production_id)
	{
		return $this->db->select('production.id AS id_pro, production.code AS code_pro, production.date AS date_pro, production.qty_produce, production.qty_result, production.status, 
						product.id AS id_p, product.code AS code_p, product.name AS name_p, warehouse.id AS id_w, warehouse.name AS name_w')
						->from('production')
						->join('product', 'product.id = production.product_id')
						->join('warehouse', 'warehouse.id = production.warehouse_id')
						->where('production.id', $production_id)
						->get()->row_array();
	}

	// REPACKING
	public function repacking_code()
	{
		$data = $this->db->select('code')->from('repacking')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "RPK".$tahun.date('m').date('d');
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

	public function detail_repacking($repacking_id)
	{
		return $this->db->select('repacking.id AS id_rp, repacking.date, repacking.code AS code_rp, operator.code AS code_op, operator.name AS name_op,
						product.id AS id_p, product.code AS code_p, product.name AS name_p, repacking.from_qty AS qty, unit.name AS name_u, warehouse.name AS name_w')
						->from('repacking')
						->join('employee AS operator', 'operator.code = repacking.employee_code')
						->join('product', 'product.id = repacking.from_product_id')
						->join('unit', 'unit.id = repacking.from_unit_id')
						->join('warehouse', 'warehouse.id = repacking.from_warehouse_id')
						->where('repacking.id', $repacking_id)
						->group_by('repacking.id')
						->get()->row_array();
	}

	public function detail_product_repacking($repacking_id)
	{
		return $this->db->select('from_product.name AS name_fp, repacking_detail.from_qty, from_unit.name AS from_unit, from_warehouse.name AS from_warehouse,
						to_product.name AS name_tp, repacking_detail.to_qty, to_unit.name AS to_unit, to_warehouse.name AS to_warehouse')
						->from('repacking_detail')
						->where('repacking_detail.repacking_id', $repacking_id)						
						->join('product AS to_product', 'to_product.id = repacking_detail.to_product_id')
						->join('unit AS to_unit', 'to_unit.id = repacking_detail.to_unit_id')
						->join('warehouse AS to_warehouse', 'to_warehouse.id = repacking_detail.to_warehouse_id')
						->group_by('repacking_detail.id')
						->get()->result_array();
	}

	// STOCK OPNAME
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

	// MUTATION
	public function mutation_code()
	{
		$data = $this->db->select('code')->from('mutation')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "MTN".$tahun.date('m').date('d');
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

	public function detail_mutation($mutation_id)
	{
		return $this->db->select('mutation.id AS id, mutation.code AS code, mutation.date, 
						checker.code AS code_c, checker.name AS name_c, operator.code AS code_o, operator.name AS name_o, 
						mutation.total_product, mutation.total_qty, do_status')
						->from('mutation')
						->join('employee AS checker', 'checker.code = mutation.checker')
						->join('employee AS operator', 'operator.code = mutation.operator')						
						->where('mutation.deleted', 0)
						->where('mutation.id', $mutation_id)
						->get()->row_array();
	}

	public function detail_product_mutation($mutation_id)
	{
		return $this->db->select('mutation_detail.product_id, mutation_detail.product_code, mutation_detail.unit_id, mutation_detail.from_warehouse_id, mutation_detail.to_warehouse_id, product.id, product.code AS code_p, product.name AS name_p, mutation_detail.qty, 
						from_warehouse.name AS name_fw, to_warehouse.code AS code_tw, to_warehouse.name AS name_tw,
						unit.code AS code_u, unit.name AS name_u')
					    ->from('mutation_detail')
						->join('product', 'product.id = mutation_detail.product_id')
						->join('unit', 'unit.id = mutation_detail.unit_id')
						->join('warehouse AS from_warehouse', 'from_warehouse.id = mutation_detail.from_warehouse_id')
						->join('warehouse AS to_warehouse', 'to_warehouse.id = mutation_detail.to_warehouse_id')
						->where('mutation_detail.mutation_id', $mutation_id)
						->group_by('mutation_detail.id')
						->get()->result_array();
	}
}