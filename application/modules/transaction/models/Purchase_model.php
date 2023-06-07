<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model 
{
	// PURCHASE INVOICE
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
		return $this->db->get();
	}

	public function get_unit($where)
    {	
		return $this->db->select('unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, value, default')->from('product_unit')
						->join('unit','unit.id=product_unit.unit_id')
						->where($where)->order_by('unit.id', 'DESC')->get();
	}

	public function get_buyprice($product_code, $unit_id)
	{
		$product = $this->db->select('buyprice')
							 ->from('product')->where('deleted', 0)
							 ->where('code', $product_code)->get()->row_array();
		$product_unit = $this->db->select('*')
							 ->from('product_unit')->where('deleted', 0)
							 ->where('product_code', $product_code)->where('unit_id', $unit_id)->get()->row_array();
		return $product['buyprice']*$product_unit['value'];
	}

	public function get_warehouse($product_code = null, $unit_id = null)
	{
		$warehouses = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w, warehouse.default')
		                       ->from('warehouse')->where('deleted', 0)->get()->result_array();
		$product_unit = $this->crud->get_where('product_unit', ['product_code' => $product_code, 'unit_id' => $unit_id, 'deleted' => 0])->row_array();
		foreach($warehouses AS $warehouse)
		{			
			$stock = $this->crud->get_where('stock', ['product_code' => $product_code, 'warehouse_id' => $warehouse['id_w'], 'deleted' => 0])->row_array();
			$qty = ($stock != null) ? round($stock['qty']/$product_unit['value'], 2) : 0;
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

	public function purchase_invoice_code()
	{
		$data = $this->db->select('code')->from('purchase_invoice')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "PI".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['code'], 2, 2);
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
	
	public function get_detail_purchase_invoice($purchase_id)
    {
		return $this->db->select('purchase_invoice.*,
						supplier.code AS code_s, supplier.id AS id_s, supplier.name AS name_s, supplier.address AS address_s, supplier.telephone AS telephone_s,
						employee.code AS code_e, employee.id AS id_e, employee.name AS name_e, employee.address AS address_e, employee.telephone AS telephone_e')
						->from('purchase_invoice')
						->join('employee','employee.code = purchase_invoice.employee_code')
						->join('supplier','supplier.code = purchase_invoice.supplier_code')
						->where('purchase_invoice.id', $purchase_id)
						->get()->row_array();
	}	

	public function get_detail_purchase_invoice_detail($purchase_id)
	{
		return $this->db->select('purchase_invoice_detail.*, 
						product.code AS code_p, product.name AS name_p,
						unit.id AS id_u, unit.code AS code_u, unit.name AS name_u,
						warehouse_id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
						->from('purchase_invoice_detail')
						->join('product', 'product.code = purchase_invoice_detail.product_code')
						->join('unit', 'unit.id = purchase_invoice_detail.unit_id')
						->join('warehouse', 'warehouse.id = purchase_invoice_detail.warehouse_id')
						->where('purchase_invoice_detail.deleted', 0)->where('purchase_invoice_detail.purchase_invoice_id', $purchase_id)						
						->group_by('purchase_invoice_detail.id')->order_by('purchase_invoice_detail.id', 'ASC')
						->get()->result_array();
	}

	// PURCHASE RETURN
	public function get_product_return($search, $supplier_code, $ppn)
	{		
		return $this->db->select('product.id, product.barcode, product.code, product.name')
					->from('product')
					->join('stock', 'stock.product_id = product.id')
					->join('purchase_invoice_detail', 'purchase_invoice_detail.product_id = product.id')
					->join('purchase_invoice', 'purchase_invoice.id = purchase_invoice_detail.purchase_invoice_id')															
					->like('product.code', $search, 'both')
					->where('purchase_invoice.supplier_code', $supplier_code)
					->where('product.ppn', $ppn)->where('stock.qty >', 0)->where('product.status', 1)->where('product.deleted', 0)->where('purchase_invoice_detail.deleted', 0)->where('purchase_invoice.deleted', 0)
					->or_like('product.name', $search, 'both')
					->where('purchase_invoice.supplier_code', $supplier_code)
					->where('product.ppn', $ppn)->where('stock.qty >', 0)->where('product.status', 1)->where('product.deleted', 0)->where('purchase_invoice_detail.deleted', 0)->where('purchase_invoice.deleted', 0)
					->group_by('product.id')
					->order_by('product.id', 'ASC')
					->get();
	}

	public function get_buyprice_return($product_code, $unit_id, $supplier_code)
	{
		$product = $this->db->select('*')
							->from('purchase_invoice_detail')
							->join('purchase_invoice', 'purchase_invoice.id = purchase_invoice_detail.purchase_invoice_id')
							->where('purchase_invoice_detail.deleted', 0)->where('purchase_invoice.deleted', 0)
							->where('product_code', $product_code)->where('supplier_code', $supplier_code)
							->order_by('purchase_invoice.date', 'desc')->limit(1)
							->get()->row_array();
		$product_unit_buy = $this->db->select('*')->from('product_unit')->where('deleted', 0)
							->where('product_code', $product_code)->where('unit_id', $product['unit_id'])->get()->row_array();
		$convert = ($product_unit_buy['value'] != null) ? $product_unit_buy['value'] : 1;
		$buyprice = $product['price']/$convert;
		$product_unit = $this->db->select('*')->from('product_unit')->where('deleted', 0)->where('product_code', $product_code)->where('unit_id', $unit_id)->get()->row_array();
		return $buyprice*$product_unit['value'];
	}
	
	public function get_invoice_return($supplier_code)
	{
		return $this->db->select('id, code, invoice, account_payable')
						->from('purchase_invoice')
						->where('payment', 2)->where('account_payable !=', 0)->where('payment_status', 2)->where('deleted', 0)->where('supplier_code', $supplier_code)
						->get();
	}

	public function get_account_payable($purchase_id)
	{
		$data = $this->db->select('account_payable')
						->from('purchase_invoice')->where('payment', 2)->where('account_payable !=', 0)->where('payment_status', 2)->where('deleted', 0)->where('id', $purchase_id)
						->get()->row_array();
		return $data['account_payable'];
	}

	public function check_stock_purchase_return_do($purchase_return)
	{
		$check_stock_purchase_return_detail = $this->db->select('product_id, product_code, product.name AS name_p, sum(qty) AS qty, unit_id, unit_value, warehouse_id')
															 ->from('purchase_return_detail')
															 ->join('product', 'product.id = purchase_return_detail.product_id')
															 ->where('purchase_return_id', $purchase_return['id'])
															 ->group_by('product_id')->group_by('unit_id')->group_by('warehouse_id')->order_by('product_code', 'ASC')
															 ->get()->result_array();
		$found = []; $min_stock = 0;													 
		foreach($check_stock_purchase_return_detail AS $info)
		{
			$qty_convert = $info['qty']*$info['unit_value'];
			$where_last_stock_movement = [
				'date <='	   => $purchase_return['date'],
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

	public function purchase_return_code()
    {        
        $data = $this->db->select('code')->from('purchase_return')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "PR".date('d').date('m').$tahun;
		if($data)
		{
			$sub_tanggal = substr($data['code'], 2, 2);
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
	
	public function get_detail_purchase_return($purchase_return_id)
	{
		return $this->db->select('purchase_return.*,
						employee.name AS name_e, supplier.name AS name_s, 						
						purchase_invoice.invoice AS invoice')
						->from('purchase_return')						
						->join('purchase_invoice', 'purchase_invoice.id = purchase_return.purchase_invoice_id', 'left')
						->join('employee', 'employee.code = purchase_return.employee_code')
						->join('supplier', 'supplier.code = purchase_return.supplier_code')
						->where('purchase_return.deleted', 0)->where('purchase_return.id', $purchase_return_id)
						->get()->row_array();
	}

	public function get_detail_purchase_return_detail($purchase_return_id)
	{
		return $this->db->select('purchase_return_detail.*,
						product.id AS id_p, product.code AS code_p, product.name AS name_p, 
						unit.id AS id_u, unit.name AS name_u,
						warehouse_id AS id_w, warehouse.name AS name_w')
					  	->from('purchase_return_detail')
					  	->join('product', 'product.code = purchase_return_detail.product_code')
					  	->join('unit', 'unit.id = purchase_return_detail.unit_id')
					  	->join('warehouse', 'warehouse.id = purchase_return_detail.warehouse_id')					  						  	
					  	->where('purchase_return_detail.purchase_return_id', $purchase_return_id)
						->where('purchase_return_detail.deleted', 0)
						->group_by('purchase_return_detail.id')
						->get()->result_array();
	}

	
}
