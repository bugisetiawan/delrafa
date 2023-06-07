<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repacking_model extends CI_Model 
{	
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
						product.id AS id_p, product.code AS code_p, product.name AS name_p, repacking.qty AS qty, unit.name AS name_u, warehouse.name AS name_w')
						->from('repacking')
						->join('employee AS operator', 'operator.code = repacking.employee_code')
						->join('product', 'product.id = repacking.product_id')
						->join('unit', 'unit.id = repacking.unit_id')
						->join('warehouse', 'warehouse.id = repacking.warehouse_id')
						->where('repacking.id', $repacking_id)
						->group_by('repacking.id')
						->get()->row_array();
	}

	public function detail_product_repacking($repacking_id)
	{
		return $this->db->select('to_product.name AS name_tp, repacking_detail.to_qty, to_unit.name AS to_unit, to_warehouse.name AS to_warehouse')
						->from('repacking_detail')
						->where('repacking_detail.repacking_id', $repacking_id)						
						->join('product AS to_product', 'to_product.id = repacking_detail.to_product_id')
						->join('unit AS to_unit', 'to_unit.id = repacking_detail.to_unit_id')
						->join('warehouse AS to_warehouse', 'to_warehouse.id = repacking_detail.to_warehouse_id')
						->group_by('repacking_detail.id')
						->get()->result_array();
	}
}