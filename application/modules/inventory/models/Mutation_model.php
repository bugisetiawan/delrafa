<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mutation_model extends CI_Model 
{
	// MUTATION
	public function mutation_code()
	{
		$data = $this->db->select('code')->from('mutation')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$code_user = $this->session->userdata('code_u'); $tahun = substr(date('Y'),2,2);
		$format = $code_user."MTN".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['code'], 6, 2);
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

	public function check_stock_mutation_do($mutation, $mutation_detail)
    {        
		$found = [];
		foreach($mutation_detail AS $info_mutation_detail)
		{
			$qty_convert = $info_mutation_detail['qty']*$info_mutation_detail['unit_value'];
			$where_last_stock_movement = [
				'date <='	   => $mutation['date'],
				'product_id'   => $info_mutation_detail['product_id'],
				'warehouse_id' => $info_mutation_detail['from_warehouse_id']
			];
			$last_stock_movement = $this->db->select('stock')->from('stock_card')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			if(round($last_stock_movement['stock'], 8) < round($qty_convert, 8))
			{				
				$found[] = [
					'code' => $info_mutation_detail['product_code'],
					'name' => $info_mutation_detail['name_p']
				];
			}
		}	
		
		$result = [
			'total' => count($found),
			'found'	=> $found
		];
		return $result;
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

	public function detail_mutation_detail($mutation_id)
	{
		return $this->db->select('mutation_detail.product_id, mutation_detail.product_code, mutation_detail.qty, mutation_detail.unit_id, mutation_detail.unit_value,
						mutation_detail.from_warehouse_id, mutation_detail.to_warehouse_id, 
						product.id, product.code AS code_p, product.name AS name_p, 
						from_warehouse.id AS id_fw, from_warehouse.name AS name_fw, 
						to_warehouse.id AS id_tw, to_warehouse.code AS code_tw, to_warehouse.name AS name_tw,
						unit.id AS id_u, unit.code AS code_u, unit.name AS name_u')
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