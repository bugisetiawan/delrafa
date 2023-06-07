<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model 
{
	// GENERAL FUNCTION
	public function get_product($search, $ppn = null)
	{		
		$this->db->select('product.id, product.barcode, product.code, product.name')
					->from('product')
					->like('product.code', $search)->where('product.hpp >=', 0)->where('product.status', 1)->where('product.deleted', 0);
		if($ppn != null)
		{
			$this->db->where('ppn', $ppn);
		}					
		$this->db->or_like('product.name', $search)->where('product.hpp >=', 0)->where('product.status', 1)->where('product.deleted', 0);
		if($ppn != null)
		{
			$this->db->where('ppn', $ppn);
		}					
		$this->db->group_by('product.id')->order_by('product.id', 'ASC');
		return $this->db->get();
	}

	public function get_unit($where)
    {	
		return $this->db->select('unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, value, default')
						->from('product_unit')->join('unit','unit.id = product_unit.unit_id')->where($where)->get();
	}

	public function get_sellprice($where, $customer_code)
	{
		if($customer_code == "CUST-00000")
		{
			$name ="price_1";			
		}
		else
		{
			$class = $this->crud->get_where('customer', ['code' => $customer_code])->row_array();
			$name = "price_".$class['price_class'];
		}
		$data = $this->db->select($name)
						->from('sellprice')
						->where($where)
						->where('deleted', 0)
						->get()->row_array();												
		return $data[$name];
	}

	public function get_warehouse($product_code = null, $unit_id = null)
	{
		$warehouses = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w, warehouse.default')
		                       ->from('warehouse')->where('deleted', 0)->get()->result_array();
		$product_unit = $this->crud->get_where('product_unit', ['product_code' => $product_code, 'unit_id' => $unit_id, 'deleted' => 0])->row_array();
		foreach($warehouses AS $warehouse)
		{			
			$stock = $this->crud->get_where('stock', ['product_code' => $product_code, 'warehouse_id' => $warehouse['id_w'], 'deleted' => 0])->row_array();
			$qty = ($stock != null) ? $stock['qty'] / $product_unit['value'] : 0;
			$data[] =[
				'id_w'   => $warehouse['id_w'],
				'code_w' => $warehouse['code_w'],
				'name_w' => $warehouse['name_w'],
				'default'=> $warehouse['default'],
				'stock'  => $qty
			];
		}
		return $data;
	}	

	// SALES ORDER
	public function sales_order_code()
	{
		$data = $this->db->select('invoice')->from('sales_order')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "SO".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['invoice'], 2, 2);
			$no 		 = substr($data['invoice'], -3,3);
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
	
	public function get_detail_sales_order($sales_order_id)
    {
		return $this->db->select('sales_order.*, 
						sales.code AS code_s, sales.id AS id_s, sales.name AS name_s,
						employee.code AS code_e, employee.id AS id_e, employee.name AS name_e,
						customer.code AS code_c, customer.id AS id_c, customer.name AS name_c, customer.address AS address_c, customer.telephone AS telephone_c')
						->from('sales_order')
						->join('employee','employee.code = sales_order.employee_code')
						->join('employee AS sales','sales.code = sales_order.sales_code')
						->join('customer','customer.code = sales_order.customer_code')
						->where('sales_order.deleted', 0)->where('sales_order.id', $sales_order_id)
						->get()->row_array();
	}	

	public function get_detail_sales_order_detail($sales_order_id)
	{
		return $this->db->select('sales_order_detail.*, 
						product.id AS id_p, product.code AS code_p, product.name AS name_p, product.ppn AS ppn,
						unit.id AS id_u, unit.code AS code_u, unit.name AS name_u,
						warehouse.id AS id_w, warehouse.name AS name_w')
						->from('sales_order_detail')
						->join('product', 'product.code = sales_order_detail.product_code')
						->join('unit', 'unit.id = sales_order_detail.unit_id')
						->join('warehouse', 'warehouse.id = sales_order_detail.warehouse_id')						
						->where('sales_order_detail.deleted', 0)->where('sales_order_detail.sales_order_id', $sales_order_id)
						->group_by('sales_order_detail.id')
						->get()->result_array();
	}
	
	// SALES ORDER TAKING
	public function sot_code()
	{
		$data = $this->db->select('code')->from('sales_order_taking')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "SOT".date('d').date('m').$tahun;
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

	public function get_sales_order_taking()
	{
		return $this->db->select('sales_order.id AS id, sales_order.date, sales_order.invoice, count(sales_order_detail.id) AS total_product, sales_order.taking_method AS taking, sales_order.grandtotal, customer.name AS name_c, employee.name AS name_s, sales_order.sales_order_status AS status_so')
								->from('sales_order')->join('employee', 'employee.code = sales_order.sales_code')
								->join('customer', 'customer.code = sales_order.customer_code')->join('sales_order_detail', 'sales_order_detail.sales_order_id = sales_order.id')
								->where('sales_order.date', date('Y-m-d'))->where('sales_order.sales_order_status', 1)->where('sales_order.so_taking_id', null)
								->where('sales_order.deleted', 0)->where('sales_order_detail.deleted', 0)
								->group_by('sales_order.id')
								->get()->result_array();
	}

	// SALES INVOICE
	public function check_stock_sales_invoice_do($sales_invoice)
	{
		$check_stock_sales_invoice_detail = $this->db->select('product_id, product_code, product.name AS name_p, sum(qty) AS qty, unit_id, unit_value, warehouse_id')
															 ->from('sales_invoice_detail')
															 ->join('product', 'product.id = sales_invoice_detail.product_id')
															 ->where('sales_invoice_id', $sales_invoice['id'])
															 ->group_by('product_id')
															 ->group_by('unit_id')
															 ->group_by('warehouse_id')
															 ->order_by('product_code', 'ASC')
															 ->get()->result_array();
		$found = []; $min_stock = 0;													 
		foreach($check_stock_sales_invoice_detail AS $info)
		{
			$qty_convert = $info['qty']*$info['unit_value'];
			$where_last_stock_movement = [
				'date <='	   => $sales_invoice['date'],
				'product_id'   => $info['product_id'],
				'warehouse_id' => $info['warehouse_id']
			];
			$last_stock_movement = $this->db->select('stock')->from('stock_card')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			if(round($last_stock_movement['stock'], 2) < round($qty_convert,2))
			{
				$min_stock++;
				$found[] = [
					'code_p' => $info['product_code'],
					'name_p' => $info['name_p']
				];
			}
		}	
		
		$result = [
			'min_stock' => $min_stock,
			'found'		=> $found
		];
		return $result;
	}

	public function sales_invoice_code()
	{
		$data = $this->db->select('invoice')->from('sales_invoice')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$code_user = $this->session->userdata('code_u'); $tahun = substr(date('Y'),2,2);
		$format = $code_user."INV".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['invoice'], 6, 2);
			$no 		 = substr($data['invoice'], -3,3);
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

	public function get_detail_sales_invoice($sales_invoice_id)
    {
		return $this->db->select('sales_invoice.*,
						sales.code AS code_s, sales.id AS id_s, sales.name AS name_s,
						employee.code AS code_e, employee.id AS id_e, employee.name AS name_e,
						customer.code AS code_c, customer.id AS id_c, customer.name AS name_c, customer.address AS address_c, customer.telephone AS telephone_c')
						->from('sales_invoice')
						->join('employee','employee.code = sales_invoice.employee_code')
						->join('employee AS sales','sales.code = sales_invoice.sales_code')
						->join('customer','customer.code = sales_invoice.customer_code')
						->where('sales_invoice.id', $sales_invoice_id)
						->get()->row_array();
	}	

	public function get_detail_sales_invoice_detail($sales_invoice_id, $sales_invoice_detail_id=null)
	{
		$this->db->select('sales_invoice_detail.*,
				product.id AS id_p, product.code AS code_p, product.name AS name_p, product.ppn AS ppn,
				unit.id AS id_u, unit.code AS code_u, unit.name AS name_u,
				warehouse.id AS id_w, warehouse.name AS name_w')
				->from('sales_invoice_detail')
				->join('product', 'product.code = sales_invoice_detail.product_code')
				->join('unit', 'unit.id = sales_invoice_detail.unit_id')
				->join('warehouse', 'warehouse.id = sales_invoice_detail.warehouse_id')												
				->where('sales_invoice_detail.deleted', 0)->where('sales_invoice_detail.sales_invoice_id', $sales_invoice_id);
		if($sales_invoice_detail_id !=null)
		{
			$this->db->where_in('sales_invoice_detail.id', $sales_invoice_detail_id);
		}
		return $this->db->group_by('sales_invoice_detail.id')->get()->result_array();
	}

	// SALES RETURN
	public function sales_return_code()
    {        
		$data = $this->db->select('code')->from('sales_return')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$code_user = $this->session->userdata('code_u'); $tahun = substr(date('Y'),2,2);
		$format = $code_user."SR".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['code'], 5, 2);
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

	public function get_product_return($search, $customer_code, $ppn)
	{		
		return $this->db->select('product.id, product.barcode, product.code, product.name')
					->from('product')
					->join('sales_invoice_detail', 'sales_invoice_detail.product_id = product.id')
					->join('sales_invoice', 'sales_invoice.id = sales_invoice_detail.sales_invoice_id')										
					->like('product.code', $search, 'both')
					->where('sales_invoice.customer_code', $customer_code)
					->where('sales_invoice.do_status', 1)->where('product.ppn', $ppn)->where('product.status', 1)->where('product.deleted', 0)->where('sales_invoice_detail.deleted', 0)->where('sales_invoice.deleted', 0)
					->or_like('product.name', $search, 'both')
					->where('sales_invoice.customer_code', $customer_code)
					->where('sales_invoice.do_status', 1)->where('product.ppn', $ppn)->where('product.status', 1)->where('product.deleted', 0)->where('sales_invoice_detail.deleted', 0)->where('sales_invoice.deleted', 0)
					->group_by('product.id')
					->order_by('product.id', 'ASC')
					->get();
	}

	public function get_invoice_return($customer_code)
	{
		$where_sales_invoice = [
			'do_status' => 1,
			'account_payable !=' => 0,
			'payment_status !=' => 1,
			'customer_code' => $customer_code
		];
		return $this->db->select('id, date, invoice, account_payable')
						->from('sales_invoice')->where($where_sales_invoice)
						->get();
	}

	public function get_account_payable($sales_id)
	{
		$data = $this->db->select('account_payable')
						->from('sales_invoice')->where('deleted', 0)->where('payment', 2)						
						->where('account_payable !=', 0)->where('payment_status !=', 1)						
						->where('id', $sales_id)
						->get()->row_array();
		return $data['account_payable'];
	}

	public function get_detail_sales_return($sales_return_id)
	{
		return $this->db->select('sales_return.*,
						sales_invoice.invoice,
						employee.name AS name_e, customer.name AS name_c, customer.address AS address_c')
						->from('sales_return')						
						->join('sales_invoice', 'sales_invoice.id = sales_return.sales_invoice_id', 'left')
						->join('employee', 'employee.code = sales_return.employee_code')
						->join('customer', 'customer.code = sales_return.customer_code')
						->where('sales_return.id', $sales_return_id)
						->where('sales_return.deleted', 0)
						->get()->row_array();
	}

	public function get_detail_sales_return_detail($sales_return_id)
	{
		return $this->db->select('sales_return_detail.*,
						product.code AS code_p, product.name AS name_p,
						unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, 
						warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
					  	->from('sales_return_detail')
					  	->join('product', 'product.code = sales_return_detail.product_code')
					  	->join('unit', 'unit.id = sales_return_detail.unit_id')
					  	->join('warehouse', 'warehouse.id = sales_return_detail.warehouse_id')
					  	->group_by('sales_return_detail.product_code')
					  	->group_by('sales_return_detail.unit_id')
					  	->group_by('sales_return_detail.warehouse_id')
					  	->where('sales_return_detail.sales_return_id', $sales_return_id)
						->where('sales_return_detail.deleted', 0)
						->get()->result_array();
	}

	// SALES BILLING
	public function sales_billing_code()
	{
		$data = $this->db->select('code')->from('sales_billing')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun  = substr(date('Y'),2,2);
		$format = "SBL".date('d').date('m').$tahun;
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

	public function detail_sales_billing($sales_billing_id)
	{
		return $this->db->select('sales_billing.*, sales.name AS name_s')
					->from('sales_billing')
					->join('employee AS sales', 'sales.code = sales_billing.sales_code')
					->where('sales_billing.id', $sales_billing_id)
					->get()->row_array();
	}

	public function detail_sales_billing_detail($sales_billing_id)
	{
		return $this->db->select('sales_invoice.date, sales_invoice.invoice, sales_invoice.due_date, customer.code AS code_c, customer.name AS name_c, customer.address,
						sales_billing_detail.remaining_time, sales_billing_detail.account_payable')
						->from('sales_invoice')
						->join('customer', 'customer.code = sales_invoice.customer_code')						
						->join('sales_billing_detail', 'sales_billing_detail.sales_invoice_id = sales_invoice.id')
						->where('sales_billing_detail.sales_billing_id', $sales_billing_id)
						->group_by('sales_billing_detail.id')
						->order_by('customer.name', 'ASC')
						->get()->result_array();
	}
}