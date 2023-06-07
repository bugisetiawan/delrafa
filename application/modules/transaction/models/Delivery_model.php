<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_model extends CI_Model 
{
    public function delivery_code()
	{
		$data = $this->db->select('code')->from('delivery')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun  = substr(date('Y'),2,2);
		$format = "DLV".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['code'], 3, 2);
			$no 		 = substr($data['code'], -3,3);
			if($sub_tanggal == date('d'))
			{
				$no++;
				$invoice = $format.sprintf("%03s", $no);
			}
			else
			{
				$no = 1;
				$invoice = $format.sprintf("%03s", $no);
			}
		}	
		else
		{
			$no = 1;
			$invoice = $format.sprintf("%03s", $no);
		}					 						 
		return $invoice;
	}

	public function detail_delivery($delivery_id)
	{
		return $this->db->select('*')
					->from('delivery')
					->where('delivery.id', $delivery_id)
					->get()->row_array();
	}

	public function detail_delivery_detail($delivery_id)
	{
		return $this->db->select('sales_invoice.date, sales_invoice.invoice, sales_invoice.grandtotal, customer.name AS name_c, sales.name AS name_s')
						->from('sales_invoice')
						->join('customer', 'customer.code = sales_invoice.customer_code')
						->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
						->join('delivery_detail', 'delivery_detail.sales_invoice_id = sales_invoice.id')
						->where('delivery_detail.delivery_id', $delivery_id)
						->group_by('sales_invoice.id')
						->order_by('customer.name', 'ASC')
						->get()->result_array();
	}
}