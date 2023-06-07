<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('master/Product_model', 'product');
		if($this->session->userdata('code_e') != "DEVL-00001")
        {
            redirect(site_url('dashboard', 'refresh'));
        }                
	}

	public function synchronize_product_warehouse()
	{
		$product = $this->crud->get_where('product', ['deleted' => 0])->result_array();
		$warehouse = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
		foreach($product AS $info_product)
		{
			foreach($warehouse AS $info_warehouse)
			{
				$stock = $this->crud->get_where('stock', ['product_code' => $info_product['code'], 'warehouse_id' => $info_warehouse['id']])->num_rows();
				if($stock == 0)
				{
					$data_stock = [
						'product_id' => $info_product['id'],
						'product_code' => $info_product['code'],
						'qty'	=> 0,
						'warehouse_id' => $info_warehouse['id']
					];
					$this->crud->insert('stock', $data_stock);
				}	
			}
		}
	}

	public function synchronize_user_access()
	{
		$users = $this->crud->get_where('user', ['id >' => 3, 'deleted' => 0])->result_array();
		foreach($users AS $user)
		{
			$modules = $this->crud->get_where('module', ['active' => 1])->result_array();
			foreach($modules AS $module)
			{
				$where_access = [
					'user_id'	 => $user['id'],
					'module_url' => $module['url']
				];
				$check_access = $this->crud->get_where('access', $where_access)->row_array();
				if($check_access == null)
				{
					$data_access = [
						'user_id'       => $user['id'],
						'module_url'    => $module['url'],
						'method'  		=> json_encode([])
					];
					$this->crud->insert('access', $data_access);
				}
			}
		}
		echo "SINKRONASI HAK AKSES USER SELESAI";
	}	

	public function synchronize_stock_opname()
	{	
		$from_date = format_date("2022-11-01");
		$to_date   = format_date("2023-03-31");
		$where_stock_opname = [
			'date >=' => $from_date,
			'date <=' => $to_date
		];
		$stock_opname = $this->crud->get_where('stock_opname', $where_stock_opname)->result_array();
		foreach($stock_opname AS $info_stock_opname)
		{
			$stock_opname_detail = $this->crud->get_where('stock_opname_detail', ['stock_opname_id' => $info_stock_opname['id']])->result_array();
			$grandtotal = 0;
			foreach($stock_opname_detail AS $info_stock_opname_detail)
			{
				$grandtotal = $grandtotal + ($info_stock_opname_detail['hpp']*$info_stock_opname_detail['adjust']);
			}
			$this->crud->update('stock_opname', ['grandtotal' => $grandtotal], ['id' => $info_stock_opname['id']]);
			// ALGORITHM GENERAL LEDGER
			/*					
			-GENERAL LEDGER -> PENYESUAIAN PERSEDIAAN
			-GENERAL LEDGER -> PERSEDIAAN BARANG
			*/	
			if($grandtotal >= 0)
			{
				// GENERAL LEDGER -> PENYESUAIAN PERSEDIAAN (K)
				$where_general_ledger = [
					'invoice'		   => $info_stock_opname['code'],
					'coa_account_code' => "70101",
					'information'      => "PENYESUAIAN PERSEDIAAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_stock_opname['date'],
						'coa_account_code'  => "70101",
						'transaction_id'    => $info_stock_opname['id'],
						'invoice'           => $info_stock_opname['code'],
						'information'       => 'PENYESUAIAN PERSEDIAAN',
						'note'		        => 'PENYESUAIAN_PERSEDIAAN_'.$info_stock_opname['code'],
						'credit'            => abs($grandtotal)
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['credit' => abs($grandtotal)], ['id' => $general_ledger['id']]);
				}
				// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
				$where_general_ledger = [
					'invoice'		   => $info_stock_opname['code'],
					'coa_account_code' => "10301",
					'information'      => "PENYESUAIAN PERSEDIAAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{					
					$data = [
						'date'              => $info_stock_opname['date'],
						'coa_account_code'  => "10301",
						'transaction_id'    => $info_stock_opname['id'],
						'invoice'           => $info_stock_opname['code'],
						'information'       => 'PENYESUAIAN PERSEDIAAN',
						'note'		        => 'PENYESUAIAN_PERSEDIAAN_'.$info_stock_opname['code'],
						'debit'           	=> abs($grandtotal)
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => abs($grandtotal)], ['id' => $general_ledger['id']]);
				}
			}
			else
			{
				// GENERAL LEDGER -> PENYESUAIAN PERSEDIAAN (D)
				$where_general_ledger = [
					'invoice'		   => $info_stock_opname['code'],
					'coa_account_code' => "70101",
					'information'      => "PENYESUAIAN PERSEDIAAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_stock_opname['date'],
						'coa_account_code'  => "70101",
						'transaction_id'    => $info_stock_opname['id'],
						'invoice'           => $info_stock_opname['code'],
						'information'       => 'PENYESUAIAN PERSEDIAAN',
						'note'		        => 'PENYESUAIAN_PERSEDIAAN_'.$info_stock_opname['code'],
						'debit'            	=> abs($grandtotal)
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => abs($grandtotal)], ['id' => $general_ledger['id']]);
				}
				// GENERAL LEDGER -> PERSEDIAAN BARANG (K)
				$where_general_ledger = [
					'invoice'		   => $info_stock_opname['code'],
					'coa_account_code' => "10301",
					'information'      => "PENYESUAIAN PERSEDIAAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{					
					$data = [
						'date'              => $info_stock_opname['date'],
						'coa_account_code'  => "10301",
						'transaction_id'    => $info_stock_opname['id'],
						'invoice'           => $info_stock_opname['code'],
						'information'       => 'PENYESUAIAN PERSEDIAAN',
						'note'		        => 'PENYESUAIAN_PERSEDIAAN_'.$info_stock_opname['code'],
						'credit'           	=> abs($grandtotal)
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['credit' => abs($grandtotal)], ['id' => $general_ledger['id']]);
				}
			}
		}
		echo "SELESAI";
	}

	public function check_unbalance_stock_and_stockcard()
	{
		$where_product = [
			'department_code' 	 => "001"
		];		
		$product = $this->crud->get_where('product', $where_product)->result_array();
		$warehouse = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
		$found = [];
		foreach($product AS $info_product)
		{
			foreach($warehouse AS $info_warehouse)
			{
				$stock = $this->crud->get_where('stock', ['product_id' => $info_product['id'], 'warehouse_id' => $info_warehouse['id']])->row_array();
				$stock_card = $this->db->select('*')->from('stock_card')->where('product_id', $info_product['id'])->where('warehouse_id', $info_warehouse['id'])
									  ->order_by('date', 'DESC')->order_by('id', 'DESC')->get()->row_array();
				if($stock != null && $stock_card != null)
				{
					if($stock['qty'] != $stock_card['stock'])
					{
						$found[] = $info_product['id'].'|'.$info_product['code'].'|'.$info_product['name'].'| Stok '.$stock['qty'].'| Kartu '.$stock_card['stock'].'|'.$info_warehouse['code'];
					}
				}				
			}
		}
		echo json_encode($found);
	}

	public function check_total_purchase_invoice()
	{
		$found = [];
		$purchase_invoice = $this->crud->get_where('purchase_invoice', ['deleted' => 0])->result_array();
		foreach($purchase_invoice AS $info_purchase_invoice)
		{
			$purchase_invoice_detail = $this->crud->get_where('purchase_invoice_detail', ['purchase_invoice_id' => $info_purchase_invoice['id']])->result_array();
			$total_price = 0;
			foreach($purchase_invoice_detail AS $info_purchase_invoice_detail)
			{
				$total_price = $total_price + $info_purchase_invoice_detail['total'];
			}
			if($info_purchase_invoice['total_price'] != $total_price)
			{
				$found[] = $info_purchase_invoice['date'].' | '.$info_purchase_invoice['code'].' | '.$info_purchase_invoice['supplier_code'];
			}
		}
		$result = [
			'total' => count($found),
			'data'  => $found
		];
		echo json_encode($result);
	}

	public function synchronize_purchase_invoice()
	{	
		$from_date = format_date("2022-11-01");
		$to_date   = format_date("2023-01-31");

		// TABLE SALES_INVOICE
		$where_sales_invoice = [
			'date >=' => $from_date,
			'date <=' => $to_date
		];
		$sales_invoice = $this->crud->get_where('sales_invoice', $where_sales_invoice)->result_array();
		foreach($sales_invoice AS $info_sales_invoice)
		{
			$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $info_sales_invoice['id']])->result_array();
			$total_hpp = 0;
			foreach($sales_invoice_detail AS $info_sales_invoice_detail)
			{
				$total_hpp = $total_hpp + ($info_sales_invoice_detail['hpp']*$info_sales_invoice_detail['qty']*$info_sales_invoice_detail['unit_value']);
			}
			$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp], ['id' => $info_sales_invoice['id']]);
		}

		// ALGORITHM GENERAL LEDGER
		/*					
		-GENERAL LEDGER -> PENJUALAN (K)
		-GENERAL LEDGER -> PPN KELUARAN (K)
		-GENERAL LEDGER -> PIUTANG USAHA (D)
		-------------------------------
		-GENERAL LEDGER -> PERSEDIAAN BARANG (K)
		-GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (D)
		-------------------------------		
		*/		

		// GENERAL LEDGER -> PENJUALAN (K)
		$where_general_ledger = [
			'date >='		   => $from_date,
			'date <='		   => $to_date,
			'coa_account_code' => "40101",
			'information'      => "PENJUALAN",
		];
		$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$sales_invoice = $this->crud->get_where_select('grandtotal', 'sales_invoice', ['invoice' => $info_general_ledger['invoice']])->row_array();
			$this->crud->update('general_ledger', ['credit' => $sales_invoice['grandtotal']], ['id' => $info_general_ledger['id']]);
		}

		// GENERAL LEDGER -> PIUTANG USAHA (D)
		$where_general_ledger = [
			'date >='		   => $from_date,
			'date <='		   => $to_date,
			'coa_account_code' => "10201",
			'information'      => "PENJUALAN",
		];
		$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$sales_invoice = $this->crud->get_where_select('grandtotal', 'sales_invoice', ['invoice' => $info_general_ledger['invoice']])->row_array();
			$this->crud->update('general_ledger', ['debit' => $sales_invoice['grandtotal']], ['id' => $info_general_ledger['id']]);
		}

		// GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (D)
		$where_general_ledger = [
			'date >='		   => $from_date,
			'date <='		   => $to_date,
			'coa_account_code' => "50001",
			'information'      => "PENJUALAN",
		];
		$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$sales_invoice = $this->crud->get_where_select('total_hpp', 'sales_invoice', ['invoice' => $info_general_ledger['invoice']])->row_array();
			$this->crud->update('general_ledger', ['debit' => $sales_invoice['total_hpp']], ['id' => $info_general_ledger['id']]);
		}

		// GENERAL LEDGER -> PERSEDIAAN BARANG (K)
		$where_general_ledger = [
			'date >='		   => $from_date,
			'date <='		   => $to_date,
			'coa_account_code' => "10301",
			'information'      => "PENJUALAN",
		];
		$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$sales_invoice = $this->crud->get_where_select('total_hpp', 'sales_invoice', ['invoice' => $info_general_ledger['invoice']])->row_array();
			$this->crud->update('general_ledger', ['credit' => $sales_invoice['total_hpp']], ['id' => $info_general_ledger['id']]);
		}

		echo "SELESAI";
	}	

	public function synchronize_sales_invoice()
	{	
		$from_date = format_date("2023-05-08");
		$to_date   = format_date("2023-05-08");
		$where_sales_invoice = [
			'date >=' => $from_date,
			'date <=' => $to_date,
			'do_status' => 1
		];
		$sales_invoice = $this->crud->get_where('sales_invoice', $where_sales_invoice)->result_array();
		foreach($sales_invoice AS $info_sales_invoice)
		{
			$customer = $this->crud->get_where('customer', ['code' => $info_sales_invoice['customer_code']])->row_array();
			if($info_sales_invoice['do_status'] == 1)
			{
				// $sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $info_sales_invoice['id']])->result_array();
				// $total_price = 0; $total_hpp = 0;
				// foreach($sales_invoice_detail AS $info_sales_invoice_detail)
				// {
				// 	$total = ($info_sales_invoice_detail['qty']*$info_sales_invoice_detail['price']);			
				// 	$total_price = $total_price + ($total-($total*floatval($info_sales_invoice_detail['disc_product'])/100));
				// 	$total_hpp = $total_hpp + ($info_sales_invoice_detail['hpp']*$info_sales_invoice_detail['qty']*$info_sales_invoice_detail['unit_value']);
				// }
				// $grandtotal = $total_price - $info_sales_invoice['discount_rp'];
				// $this->crud->update('sales_invoice', ['grandtotal' => $grandtotal, 'total_hpp' => $total_hpp], ['id' => $info_sales_invoice['id']]);

				// ALGORITHM GENERAL LEDGER
				/*					
				-GENERAL LEDGER -> PENJUALAN (K)
				-GENERAL LEDGER -> PPN KELUARAN (K)
				-GENERAL LEDGER -> PIUTANG USAHA (D)
				-------------------------------
				-GENERAL LEDGER -> PERSEDIAAN BARANG (K)
				-GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (D)
				-------------------------------		
				*/		
				$grandtotal = $info_sales_invoice['grandtotal'];
				$total_hpp = $info_sales_invoice['total_hpp'];
				// // GENERAL LEDGER -> PENJUALAN (K)
				// $where_general_ledger = [
				// 	'invoice'		   => $info_sales_invoice['invoice'],
				// 	'coa_account_code' => "40101",
				// 	'information'      => "PENJUALAN",
				// ];
				// $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				// if($general_ledger == null)
				// {				
				// 	$data = [
				// 		'date'              => $info_sales_invoice['date'],
				// 		'coa_account_code'  => "40101",
				// 		'transaction_id'    => $info_sales_invoice['id'],
				// 		'invoice'           => $info_sales_invoice['invoice'],
				// 		'information'       => 'PENJUALAN',
				// 		'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
				// 		'credit'           	=> $grandtotal					
				// 	];									
				// 	$this->crud->insert('general_ledger', $data);
				// }
				// else
				// {
				// 	$this->crud->update('general_ledger', ['debit' => 0, 'credit' => $grandtotal], ['id' => $general_ledger['id']]);
				// }
				// // GENERAL LEDGER -> PIUTANG USAHA (D)
				// $where_general_ledger = [
				// 	'invoice'		   => $info_sales_invoice['invoice'],
				// 	'coa_account_code' => "10201",
				// 	'information'      => "PENJUALAN",
				// ];
				// $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				// if($general_ledger == null)
				// {				
				// 	$data = [
				// 		'date'              => $info_sales_invoice['date'],
				// 		'coa_account_code'  => "10201",
				// 		'transaction_id'    => $info_sales_invoice['id'],
				// 		'invoice'           => $info_sales_invoice['invoice'],
				// 		'information'       => 'PENJUALAN',
				// 		'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
				// 		'debit'           	=> $grandtotal					
				// 	];									
				// 	$this->crud->insert('general_ledger', $data);
				// }
				// else
				// {
				// 	$this->crud->update('general_ledger', ['debit' => $grandtotal, 'credit' => 0], ['id' => $general_ledger['id']]);
				// }
				// // GENERAL LEDGER -> PERSEDIAAN BARANG (K)
				// $where_general_ledger = [
				// 	'invoice'		   => $info_sales_invoice['invoice'],
				// 	'coa_account_code' => "10301",
				// 	'information'      => "PENJUALAN",
				// ];
				// $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				// if($general_ledger == null)
				// {				
				// 	$data = [
				// 		'date'              => $info_sales_invoice['date'],
				// 		'coa_account_code'  => "10301",
				// 		'transaction_id'    => $info_sales_invoice['id'],
				// 		'invoice'           => $info_sales_invoice['invoice'],
				// 		'information'       => 'PENJUALAN',
				// 		'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
				// 		'credit'           	=> $total_hpp					
				// 	];									
				// 	$this->crud->insert('general_ledger', $data);
				// }
				// else
				// {
				// 	$this->crud->update('general_ledger', ['debit' => 0, 'credit' => $total_hpp], ['id' => $general_ledger['id']]);
				// }
				// GENERAL LEDGER -> BEBAN PENDAPATAN (D)
				$where_general_ledger = [
					'invoice'		   => $info_sales_invoice['invoice'],
					'coa_account_code' => "50001",
					'information'      => "PENJUALAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_sales_invoice['date'],
						'coa_account_code'  => "50001",
						'transaction_id'    => $info_sales_invoice['id'],
						'invoice'           => $info_sales_invoice['invoice'],
						'information'       => 'PENJUALAN',
						'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
						'debit'           	=> $total_hpp					
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => $total_hpp, 'credit' => 0], ['id' => $general_ledger['id']]);
				}
			}			
		}
		echo "SINKORNASI BUKU BESAR PENJUALAN SELESAI ".$from_date.' s.d '.$to_date;
	}

	public function synchronize_sales_return()
	{	
		$from_date = format_date("2023-05-08");
		$to_date   = format_date("2023-05-08");
		$where_sales_return = [
			'date >='   => $from_date,
			'date <='   => $to_date,
			'do_status' => 1,
		];
		$sales_return = $this->crud->get_where('sales_return', $where_sales_return)->result_array();
		foreach($sales_return AS $info_sales_return)
		{
			$customer = $this->crud->get_where_select('name', 'customer', ['code' => $info_sales_return['customer_code']])->row_array();
			// $this->db->delete('general_ledger', ['invoice' => $info_sales_return['code']]);
			// $this->db->delete('cash_ledger', ['invoice' => $info_sales_return['code']]);
			if($info_sales_return['do_status'] == 1)
			{
				$sales_return_detail = $this->crud->get_where('sales_return_detail', ['sales_return_id' => $info_sales_return['id']])->result_array();
				$total_return = 0; $total_hpp = 0;
				foreach($sales_return_detail AS $info_sales_return_detail)
				{
					$total_return = $total_return + ($info_sales_return_detail['price']*$info_sales_return_detail['qty']);
					$total_hpp = $total_hpp + ($info_sales_return_detail['hpp']*$info_sales_return_detail['qty']*$info_sales_return_detail['unit_value']);
				}
				$this->crud->update('sales_return', ['total_return' => $total_return, 'total_hpp' => $total_hpp], ['id' => $info_sales_return['id']]);
				// ALGORITHM GENERAL LEDGER
				/*					
				-GENERAL LEDGER -> RETUR PENJUALAN (D)
				-GENERAL LEDGER -> PIUTANG USAHA (K) / KAS (K)
				-----------------------------------	
				-GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (K)
				-GENERAL LEDGER -> PERSEDIAAN BARANG (D)			
				*/	
				
				// $total_return = $info_sales_return['total_return'];
				// $total_hpp    = $info_sales_return['total_hpp'];
				// GENERAL LEDGER -> RETUR PENJUALAN (D)
				$where_general_ledger = [
					'invoice'		   => $info_sales_return['code'],
					'coa_account_code' => "40103",
					'information'      => "RETUR PENJUALAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_sales_return['date'],
						'coa_account_code'  => "40103",
						'transaction_id'    => $info_sales_return['id'],
						'invoice'           => $info_sales_return['code'],
						'information'       => 'RETUR PENJUALAN',
						'note'		        => 'RETUR_PENJUALAN_'.$info_sales_return['code'].'_'.$customer['name'],
						'debit'            	=> $total_return					
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => $total_return], ['id' => $general_ledger['id']]);
				}
				// GENERAL LEDGER -> KAS (K) / PIUTANG USAHA (K)
				if($info_sales_return['method'] == 1)
				{
					$where_general_ledger = [
						'invoice'		   => $info_sales_return['code'],
						'coa_account_code' => ($info_sales_return['cl_type'] == 1) ? "10101" : "10102",
						'information'      => "RETUR PENJUALAN"
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
					if($general_ledger == null)
					{					
						$data = [
							'date'              => $info_sales_return['date'],
							'coa_account_code'  => ($info_sales_return['cl_type'] == 1) ? "10101" : "10102",
							'transaction_id'    => $info_sales_return['id'],
							'invoice'           => $info_sales_return['code'],
							'information'       => 'RETUR PENJUALAN',
							'note'		        => 'RETUR_PENJUALAN_'.$info_sales_return['code'].'_'.$customer['name'],
							'credit'            	=> $total_return						
						];									
						$this->crud->insert('general_ledger', $data);
					}
					else
					{
						$this->crud->update('general_ledger', ['credit' => $total_return], ['id' => $general_ledger['id']]);
					}
				}
				else if($info_sales_return['method'] == 2)
				{
					$where_general_ledger = [
						'invoice'		   => $info_sales_return['code'],
						'coa_account_code' => "10201",
						'information'      => "RETUR PENJUALAN",
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
					if($general_ledger == null)
					{
						$data = [
							'date'              => $info_sales_return['date'],
							'coa_account_code'  => "10201",
							'transaction_id'    => $info_sales_return['id'],
							'invoice'           => $info_sales_return['code'],
							'information'       => 'RETUR PENJUALAN',
							'note'		        => 'RETUR_PENJUALAN_'.$info_sales_return['code'].'_'.$customer['name'],
							'credit'            => $total_return						
						];									
						$this->crud->insert('general_ledger', $data);
					}
					else
					{
						$this->crud->update('general_ledger', ['credit' => $total_return], ['id' => $general_ledger['id']]);
					}
				}
				// GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (k)
				$where_general_ledger = [
					'invoice'		   => $info_sales_return['code'],
					'coa_account_code' => "50001",
					'information'      => "RETUR PENJUALAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_sales_return['date'],
						'coa_account_code'  => "50001",
						'transaction_id'    => $info_sales_return['id'],
						'invoice'           => $info_sales_return['code'],
						'information'       => 'RETUR PENJUALAN',
						'note'		        => 'RETUR_PENJUALAN_'.$info_sales_return['code'].'_'.$customer['name'],
						'credit'           	=> $info_sales_return['total_hpp']
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['credit' => $info_sales_return['total_hpp']], ['id' => $general_ledger['id']]);
				}
				// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
				$where_general_ledger = [
					'invoice'		   => $info_sales_return['code'],
					'coa_account_code' => "10301",
					'information'      => "RETUR PENJUALAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_sales_return['date'],
						'coa_account_code'  => "10301",
						'transaction_id'    => $info_sales_return['id'],
						'invoice'           => $info_sales_return['code'],
						'information'       => 'RETUR PENJUALAN',
						'note'		        => 'RETUR_PENJUALAN_'.$info_sales_return['code'].'_'.$customer['name'],
						'debit'            	=> $info_sales_return['total_hpp']
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => $info_sales_return['total_hpp']], ['id' => $general_ledger['id']]);
				}
			}			
		}
		echo "SINKRONASI BUKU BESAR RETUR PENJUALAN ".$from_date." s.d ".$to_date." SELESAI";
	}

	public function rescync_user_access()
	{
		$users = $this->crud->get_where('user', ['id >' => 3, 'deleted' => 0])->result_array();
		foreach($users AS $user)
		{
			$modules = $this->crud->get_where('module', ['status' => 1])->result_array();
			foreach($modules AS $module)
			{
				$where_access = [
					'user_id'	 => $user['id'],
					'module_url' => $module['url']
				];
				$check_access = $this->crud->get_where('access', $where_access)->row_array();
				if($check_access == null)
				{
					$data_access = [
						'user_id'       => $user['id'],
						'module_url'    => $module['url'],
						'read'			=> 0,
						'detail'		=> 0,
						'create'		=> 0,
						'update'		=> 0,
						'delete'		=> 0,
						'printout'		=> 0,
					];
					$this->crud->insert('access', $data_access);
				}
			}
		}				
		$this->session->set_flashdata('success', 'SINKRONASI HAK AKSES USER SELESAI');
        redirect(site_url('dashboard'));
	}		

	public function check_unbalance_cash_and_general_ledger()
	{
		$from_date = format_date("2023-04-01");
		$to_date   = format_date("2023-04-31");
		$found_cl_to_gl = []; $found_gl_to_cl = [];
		$cl_type = 1; $coa_account_code = "10101";
		// CASH LEDGER TO GENERAL LEDGER
		$where_cash_ledger = [
			'cl_type' => $cl_type,
			'date >=' => $from_date,
			'date <=' => $to_date
		];		
		$cash_ledger = $this->crud->get_where_select('date, invoice, amount', 'cash_ledger', $where_cash_ledger)->result_array();
		foreach($cash_ledger AS $info_cash_ledger)
		{
			$general_ledger = $this->crud->get_where_select('invoice', 'general_ledger', ['coa_account_code' => $coa_account_code, 'invoice' => $info_cash_ledger['invoice']])->row_array();
			if($general_ledger == null)
			{
				$found_cl_to_gl[] = $info_cash_ledger['date'].'|'.$info_cash_ledger['invoice'].'|'.$info_cash_ledger['amount'];
			}
		}
		// GENERAL LEDGER TO CASH LEDGER
		$where_general_ledger = [			
			'coa_account_code' => $coa_account_code,
			'date >=' => $from_date,
			'date <=' => $to_date
		];		
		$general_ledger = $this->crud->get_where_select('date, invoice, debit, credit', 'general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$cash_ledger = $this->crud->get_where_select('invoice', 'cash_ledger', ['cl_type' => $cl_type, 'invoice' => $info_general_ledger['invoice']])->row_array();
			if($cash_ledger == null)
			{
				$found_gl_to_cl[] = $info_general_ledger['date'].'|'.$info_general_ledger['invoice'].'|'.$info_general_ledger['debit'].'|'.$info_general_ledger['credit'];
			}
		}
		$result = [
			'title'			 => "check_unbalance_cash_and_general_ledger",
			'from_date'		 => $from_date,
			'to_date'		 => $to_date,
			'found_cl_to_gl' => $found_cl_to_gl,
			'found_gl_to_cl' => $found_gl_to_cl
		];
		echo json_encode($result);
	}

	public function check_unbalance_bank_and_general_ledger()
	{
		$found_cl_to_gl = []; $found_gl_to_cl = [];
		$cl_type = 2; $coa_account_code = "10102";
		// CASH LEDGER TO GENERAL LEDGER
		$where_cash_ledger = [
			'cl_type' => $cl_type,
			'date >=' => "2022-11-01",
			'date <=' => "2022-12-31",
		];		
		$cash_ledger = $this->crud->get_where_select('invoice', 'cash_ledger', $where_cash_ledger)->result_array();
		foreach($cash_ledger AS $info_cash_ledger)
		{
			$general_ledger = $this->crud->get_where_select('invoice', 'general_ledger', ['coa_account_code' => $coa_account_code, 'invoice' => $info_cash_ledger['invoice']])->row_array();
			if($general_ledger == null)
			{
				$found_cl_to_gl[] = $info_cash_ledger['invoice'];
			}
		}
		// GENERAL LEDGER TO CASH LEDGER
		$where_general_ledger = [			
			'coa_account_code' => $coa_account_code
		];		
		$general_ledger = $this->crud->get_where_select('invoice', 'general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$cash_ledger = $this->crud->get_where_select('invoice', 'cash_ledger', ['cl_type' => $cl_type, 'invoice' => $info_general_ledger['invoice']])->row_array();
			if($cash_ledger == null)
			{
				$found_gl_to_cl[] = $info_general_ledger['date'].'|'.$info_general_ledger['invoice'].'|'.$info_general_ledger['debit'].'|'.$info_general_ledger['credit'];
			}
		}
		$result = [
			'found_cl_to_gl' => $found_cl_to_gl,
			'found_gl_to_cl' => $found_gl_to_cl
		];
		echo json_encode($result);
	}

	public function recalculate_cash_ledger()
	{
		$cla_accounts = $this->crud->get_where('cash_ledger_account', ['deleted' => 0])->result_array();
		foreach($cla_accounts AS $cla_account)
		{
			$balance = 0;
			$where_cash_ledgers = [
				'cl_type' => $cla_account['type'],
				'account_id' => $cla_account['id']
			];
			$cash_ledgers = $this->db->select('*')->from('cash_ledger')->where($where_cash_ledgers)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
			foreach($cash_ledgers AS $cash_ledger)
			{
				$balance = ($cash_ledger['method'] == 1) ? $balance+$cash_ledger['amount'] : $balance-$cash_ledger['amount'];
				$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $cash_ledger['id']]);
			}
		}
		$this->session->set_flashdata('success', 'PERAWATAN BUKU KAS & BANK SELESAI');
        redirect(site_url('dashboard'));
	}

	public function check_unbalance_general_ledger()
	{
		$general_ledgers = $this->db->select('date, invoice, sum(debit) AS total_debit, sum(credit) AS total_credit')->from('general_ledger')->where('deleted', 0)->group_by('invoice')->get()->result_array();
		$found =[];
		foreach($general_ledgers AS $general_ledger)
		{
			if($general_ledger['total_debit'] != $general_ledger['total_credit'])			
			{
				$found[] = $general_ledger['date'].' | '.$general_ledger['invoice'];
			}
		}
		echo json_encode($found); die;
	}

	public function recalculate_general_ledger()
	{
		$where_coa_account = [
			'deleted' => 0
		];
		$coa_accounts = $this->crud->get_where('coa_account', $where_coa_account)->result_array();
		foreach($coa_accounts AS $coa_account)
		{
			$balance = 0;
			$where_general_ledgers = [
				'coa_account_code' => $coa_account['code']
			];
			$general_ledgers = $this->db->select('*')->from('general_ledger')->where($where_general_ledgers)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
			foreach($general_ledgers AS $general_ledger)
			{
				if(in_array($coa_account['coa_category_code'], [1, 5, 7]))
				{
					if($general_ledger['debit'] != 0 && $general_ledger['credit'] == 0)
					{
						$balance = $balance+$general_ledger['debit'];
					}
					elseif($general_ledger['debit'] == 0 && $general_ledger['credit'] != 0)
					{
						$balance = $balance-$general_ledger['credit'];
					}
				}
				elseif(in_array($coa_account['coa_category_code'], [2, 3, 4, 6]))
				{
					if($general_ledger['debit'] != 0 && $general_ledger['credit'] == 0)
					{
						$balance = $balance-$general_ledger['debit'];
					}
					elseif($general_ledger['debit'] == 0 && $general_ledger['credit'] != 0)
					{
						$balance = $balance+$general_ledger['credit'];
					}
				}
				$this->crud->update('general_ledger', ['balance' => $balance], ['id' => $general_ledger['id']]);
			}
		}
		$this->session->set_flashdata('success', 'PERAWATAN BUKU BESAR SELESAI');
        redirect(site_url('dashboard'));
	}
}