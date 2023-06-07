<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_usage_model extends CI_Model 
{
    public function get_product($search, $ppn = null)
	{		
		$this->db->select('product.id, product.barcode, product.code, product.name')
					->from('product')
					->like('product.code', $search)->where('product.status', 1)->where('product.deleted', 0);
		if($ppn != null)
		{
			$this->db->where('ppn', $ppn);
		}					
		$this->db->or_like('product.name', $search)->where('product.status', 1)->where('product.deleted', 0);
		if($ppn != null)
		{
			$this->db->where('ppn', $ppn);
		}					
		$this->db->group_by('product.id')->order_by('product.id', 'ASC');
		return $this->db->get();
    }
    
	public function product_usage_code()
	{
        $data = $this->db->select('code')->from('product_usage')->order_by('id', 'DESC')->limit(1)->get()->row_array();
        $tahun = substr(date('Y'),2,2);
        $format = "PUG".$tahun.date('m').date('d');
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

    public function detail_product_usage($product_usage_id)
    {
        return $this->db->select('pug.*, operator.name AS operator')
                        ->from('product_usage AS pug')
                        ->join('employee AS operator', 'operator.code = pug.employee_code')
                        ->where('pug.id', $product_usage_id)
                        ->group_by('pug.id')
                        ->get()->row_array();
    }

    public function detail_product_usage_detail($product_usage_id)
    {
        return $this->db->select('pugd.*, product.id AS id_p, product.code AS code_p, product.name AS name_p')
                        ->from('product_usage_detail AS pugd')
                        ->join('product', 'pugd.product_id = product.id')
                        ->where('pugd.product_usage_id', $product_usage_id)
                        ->group_by('pugd.id')
                        ->get()->result_array();
    }

    public function check_stock_product_usage_do($product_usage, $product_usage_detail)
    {        
		$found = [];
		foreach($product_usage_detail AS $info_product_usage_detail)
		{
			$qty_convert = $info_product_usage_detail['qty']*$info_product_usage_detail['unit_value'];
			$where_last_stock_movement = [
				'date <='	   => $product_usage['date'],
				'product_id'   => $info_product_usage_detail['product_id'],
				'warehouse_id' => $info_product_usage_detail['warehouse_id']
			];
			$last_stock_movement = $this->db->select('stock')->from('stock_card')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			if(round($last_stock_movement['stock'], 5) < round($qty_convert, 5))
			{				
				$found[] = [
					'code' => $info_product_usage_detail['product_code'],
					'name' => $info_product_usage_detail['name_p']
				];
			}
		}	
		
		$result = [
			'total' => count($found),
			'found'	=> $found
		];
		return $result;
    }
}