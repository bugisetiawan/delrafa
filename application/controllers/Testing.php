<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends System_Controller 
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
	
	function testing_date_diff()
	{
		$start = strtotime("02-03-2023");
		$end   = strtotime("16-03-2023");
		$diff  = $start - $end;
		echo json_encode(floor($diff / (60 * 60 * 24)));
	}

	public function sync_product_unit_purchase_invoice_detail()
	{
		
	}
	
	public function checking_sales_invoice()
	{
		$from_date = format_date("2023-05-01");
		$to_date   = format_date("2023-16-31");
		$found=[];
		$where_sales_invoice = [
			'date >=' => $from_date,
			'date <=' => $to_date,
			'do_status' => 1
		];
		$sales_invoice = $this->crud->get_where('sales_invoice', $where_sales_invoice)->result_array();
		foreach($sales_invoice AS $info_sales_invoice)
		{
			$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $info_sales_invoice['id']])->result_array();
			$total_price = 0;
			foreach($sales_invoice_detail AS $info_sales_invoice_detail)
			{
				$total = 0;
				$total = ($info_sales_invoice_detail['qty']*$info_sales_invoice_detail['price']);			
				$total_price = $total_price + ($total-($total*floatval($info_sales_invoice_detail['disc_product'])/100));
			}
			$grandtotal = $total_price - $info_sales_invoice['discount_rp'];
			if(round($info_sales_invoice['grandtotal'], 2) != round($grandtotal,2))
			{
				$found[] = [
					'Tanggal' 		=> $info_sales_invoice['date'],
					'No.Transaksi' 	=> $info_sales_invoice['invoice'],
					'Total Awal'	=> number_format(round($total_price, 2), 2, '.', ','),
					'Diskon' 	  	=> number_format($info_sales_invoice['discount_rp'], 2, '.', ','),
					'Total Akhir'  	=> number_format($info_sales_invoice['grandtotal'], 2, '.', ','),
					'Selisih'		=> number_format($info_sales_invoice['grandtotal']-$grandtotal, 2, '.', ','),
					'Status'		=> ($info_sales_invoice['payment_status'] == 1) ? "LUNAS" : "BELUM LUNAS"
				];			
			}
		}
		echo json_encode($found);
	}

	public function checking_sales_invoice_general_ledger()
	{	
		$post['date'] = "2023-05-01";
		$from_date = format_date($post['date']);
		$to_date   = format_date($post['date']);
		$where_sales_invoice = [
			'date >=' => $from_date,
			'date <=' => $to_date,
			'do_status' => 1
		];
		$sales_invoice = $this->crud->get_where('sales_invoice', $where_sales_invoice)->result_array();
		foreach($sales_invoice AS $info_sales_invoice)
		{
			$customer = $this->crud->get_where('customer', ['code' => $info_sales_invoice['customer_code']])->row_array();
			if($info_sales_invoice['do_status'] == 0)
			{
			}
			elseif($info_sales_invoice['do_status'] == 1)
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
				// GENERAL LEDGER -> PENJUALAN (K)
				$where_general_ledger = [
					'invoice'		   => $info_sales_invoice['invoice'],
					'coa_account_code' => "40101",
					'information'      => "PENJUALAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_sales_invoice['date'],
						'coa_account_code'  => "40101",
						'transaction_id'    => $info_sales_invoice['id'],
						'invoice'           => $info_sales_invoice['invoice'],
						'information'       => 'PENJUALAN',
						'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
						'credit'           	=> $grandtotal					
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => 0, 'credit' => $grandtotal], ['id' => $general_ledger['id']]);
				}
				// GENERAL LEDGER -> PIUTANG USAHA (D)
				$where_general_ledger = [
					'invoice'		   => $info_sales_invoice['invoice'],
					'coa_account_code' => "10201",
					'information'      => "PENJUALAN",
				];
				$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				if($general_ledger == null)
				{				
					$data = [
						'date'              => $info_sales_invoice['date'],
						'coa_account_code'  => "10201",
						'transaction_id'    => $info_sales_invoice['id'],
						'invoice'           => $info_sales_invoice['invoice'],
						'information'       => 'PENJUALAN',
						'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
						'debit'           	=> $grandtotal					
					];									
					$this->crud->insert('general_ledger', $data);
				}
				else
				{
					$this->crud->update('general_ledger', ['debit' => $grandtotal, 'credit' => 0], ['id' => $general_ledger['id']]);
				}
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
				// // GENERAL LEDGER -> BEBAN PENDAPATAN (D)
				// $where_general_ledger = [
				// 	'invoice'		   => $info_sales_invoice['invoice'],
				// 	'coa_account_code' => "50001",
				// 	'information'      => "PENJUALAN",
				// ];
				// $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
				// if($general_ledger == null)
				// {				
				// 	$data = [
				// 		'date'              => $info_sales_invoice['date'],
				// 		'coa_account_code'  => "50001",
				// 		'transaction_id'    => $info_sales_invoice['id'],
				// 		'invoice'           => $info_sales_invoice['invoice'],
				// 		'information'       => 'PENJUALAN',
				// 		'note'		        => 'PENJUALAN_'.$info_sales_invoice['invoice'].'_'.$customer['name'],
				// 		'debit'           	=> $total_hpp					
				// 	];									
				// 	$this->crud->insert('general_ledger', $data);
				// }
				// else
				// {
				// 	$this->crud->update('general_ledger', ['debit' => $total_hpp, 'credit' => 0], ['id' => $general_ledger['id']]);
				// }
			}			
		}
		echo json_encode($found);
	}

	public function check_transaction_detail_id()
	{
		$found = [];
		$stock_card = $this->db->select('invoice, product_id, product_code, transaction_detail_id, count(transaction_detail_id) AS total')
							   ->from('stock_card')
							   ->where('type', 1)
							   ->group_by('transaction_detail_id')
							   ->group_by('warehouse_id')
							   ->get()->result_array();
		foreach($stock_card AS $info_stock_card)
		{
			if($info_stock_card['total'] != 1)
			{
				$found[] = 'Total: '.$info_stock_card['total'].' | '.$info_stock_card['invoice'].' | PRODUCT ID: '.$info_stock_card['product_id'].' | Transacion detail id:'.$info_stock_card['transaction_detail_id'];
				// $where_update_stock_card = [
				// 	'invoice' => $info_stock_card['invoice'],
				// 	'transaction_detail_id' => $info_stock_card['transaction_detail_id'],
				// 	'id !=' => $info_stock_card['id'] 
				// ];
				// $update_stock_card = $this->crud->get_where('stock_card', $where_update_stock_card)->result_array();
				// foreach($update_stock_card AS $info_update_stock_card)
				// {
				// 	$where_sales_invoice_detail = [
				// 		'invoice' => $info_update_stock_card['invoice'],
				// 		'product_id' => $info_update_stock_card['product_id']
				// 	];
				// 	$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', $where_sales_invoice_detail)->result_array();
				// 	foreach($sales_invoice_detail AS $info_sales_invoice_detail)
				// 	{
				// 		if($info_update_stock_card['transaction_detail_id'] != $info_sales_invoice_detail['id'])
				// 		{
				// 			$this->crud->update('stock_card', ['transaction_detail_id' => $info_sales_invoice_detail['id']], ['id' => $info_update_stock_card['id']]);
				// 		}
				// 	}
				// }
			}
		}
		echo json_encode($found);
	}

	public function insert_transaction_detail_id_ngehe()
	{
		$where_stock_card = [
			'transaction_detail_id' => NULL,
			'type' => 1
		];
		$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->result_array();
		foreach($stock_card AS $info_stock_card)
		{
			if($info_stock_card['type'] == 1)
			{
				$where_purchase_invoice_detail = [
					'purchase_invoice_id' => $info_stock_card['transaction_id'],
					'invoice' => $info_stock_card['invoice'],
					'product_id' => $info_stock_card['product_id'],
					'product_code' => $info_stock_card['product_code'],
					'warehouse_id' => $info_stock_card['warehouse_id']
				];
				$purchase_invoice_detail = $this->crud->get_where('purchase_invoice_detail', $where_purchase_invoice_detail)->row_array();
				$this->crud->update('stock_card', ['transaction_detail_id' => $purchase_invoice_detail['id']], ['id' => $info_stock_card['id']]);
			}
			// if($info_stock_card['type'] == 2)
			// {
			// 	$where_purchase_return_detail = [
			// 		'purchase_return_id' => $info_stock_card['transaction_id'],
			// 		'product_id' => $info_stock_card['product_id'],
			// 		'product_code' => $info_stock_card['product_code'],
			// 		'warehouse_id' => $info_stock_card['warehouse_id']
			// 	];
			// 	$purchase_return_detail = $this->crud->get_where('purchase_return_detail', $where_purchase_return_detail)->row_array();
			// 	$this->crud->update('stock_card', ['transaction_detail_id' => $purchase_return_detail['id']], ['id' => $info_stock_card['id']]);
			// }
			// if($info_stock_card['type'] == 4)
			// {
			// 	$where_sales_invoice_detail = [
			// 		'sales_invoice_id' => $info_stock_card['transaction_id'],
			// 		'product_id' => $info_stock_card['product_id'],
			// 		'product_code' => $info_stock_card['product_code'],
			// 		'warehouse_id' => $info_stock_card['warehouse_id']
			// 	];
			// 	$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', $where_sales_invoice_detail)->row_array();
			// 	$this->crud->update('stock_card', ['transaction_detail_id' => $sales_invoice_detail['id']], ['id' => $info_stock_card['id']]);
			// }
			// if($info_stock_card['type'] == 5)
			// {
			// 	$where_sales_return_detail = [
			// 		'sales_return_id' => $info_stock_card['transaction_id'],
			// 		'product_id' => $info_stock_card['product_id'],
			// 		'product_code' => $info_stock_card['product_code'],
			// 		'warehouse_id' => $info_stock_card['warehouse_id']
			// 	];
			// 	$sales_return_detail = $this->crud->get_where('sales_return_detail', $where_sales_return_detail)->row_array();
			// 	$this->crud->update('stock_card', ['transaction_detail_id' => $sales_return_detail['id']], ['id' => $info_stock_card['id']]);
			// }
		}
		echo "SELESAI";
	}

	public function cl_to_gl()
	{
		$cash_ledger = $this->db->select('*')->from('cash_ledger')->where('cl_type', 4)->like('invoice', "CDP2317040")->group_by('invoice')->get()->result_array();
		
		foreach($cash_ledger AS $info_cash_ledger)
		{
			$customer = $this->crud->get_where_select('name', 'customer', ['id' => $info_cash_ledger['account_id']])->row_array();
			$information ="PENERIMAAN UANG MUKA PENJUALAN";
			$note ="PENERIMAAN_UANG_MUKA_PENJUALAN_".$customer['name'];
			$data = [
				'coa_account_code' => "10101",
				'date'        => format_date($info_cash_ledger['date']),										
				'transaction_id' => $info_cash_ledger['id'],
				'invoice'     => $info_cash_ledger['invoice'],
				'information' => $information,
				'note'		  => $note,
				'debit'       => $info_cash_ledger['amount']
			];									
			$this->crud->insert('general_ledger', $data);

			$data = [
				'coa_account_code' => "20201",
				'date'        => format_date($info_cash_ledger['date']),										
				'transaction_id' => $info_cash_ledger['id']+1,
				'invoice'     => $info_cash_ledger['invoice'],
				'information' => $information,
				'note'		  => $note,
				'credit'      => $info_cash_ledger['amount']
			];	
			$this->crud->insert('general_ledger', $data);
		}
	}

	public function find_diff_penjualan()
	{
		$where_general_ledger = [
			'coa_account_code' => 10201,
			'information' 	   => "RETUR PENJUALAN",
			'date >='		   => "2022-11-01",
			'date <='		   => "2023-03-31"
		];
		$found = [];
		$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
		foreach($general_ledger AS $info_general_ledger)
		{
			$total_general_ledger = $info_general_ledger['debit']+$info_general_ledger['credit'];
			$sales_invoice = $this->crud->get_where_select('total_return', 'sales_return', ['id' => $info_general_ledger['transaction_id']])->row_array();
			if($total_general_ledger != $sales_invoice['total_return'])
			{
				$found[] = $info_general_ledger['invoice'];
			}
		}
		echo json_encode($found);
	}
		
	public function recalculate_hpp_sales_return()
	{		
		$sales_return = $this->crud->get_where('sales_return', ['deleted' => 0])->result_array();
		foreach($sales_return AS $info_sales_return)
		{
			$sales_return_detail = $this->crud->get_where('sales_return_detail', ['sales_return_id' => $info_sales_return['id']])->result_array();
			$total_hpp = 0;
			foreach($sales_return_detail AS $info_sales_return_detail)
			{
				$total_hpp = $total_hpp + ($info_sales_return_detail['hpp']*$info_sales_return_detail['qty']*$info_sales_return_detail['unit_value']);
			}
			$this->crud->update('sales_return', ['total_hpp' => $total_hpp], ['id' => $info_sales_return['id']]);
		}
	}

	public function insert_general_ledger_sales_return()
	{
		$sales_return = $this->crud->get_where('sales_return', ['deleted' => 0])->result_array();
		foreach($sales_return AS $info_sales_return)
		{
			// GENERAL LEDGER -> PENJUALAN (K)
			$where_last_balance = [
				'coa_account_code' => "40103",
				'date <='        => $info_sales_return['date'],                    
				'deleted'        => 0
			];
			$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $info_sales_return['total_return']) : add_balance(0, $info_sales_return['total_return']);
			$data = [
				'date'        => $info_sales_return['date'],
				'coa_account_code'  => "40103",
				'transaction_id' => $info_sales_return['id'],
				'invoice'     => $info_sales_return['invoice'],
				'information' => 'RETUR PENJUALAN',
				'note'		  => 'RETUR PENJUALAN_'.$info_sales_return['invoice'].'_'.$customer['name'],
				'credit'      => $info_sales_return['total_return'],
				'balance'     => $balance
			];									
			if($this->crud->insert('general_ledger', $data))
			{
				$where_after_balance = [
					'coa_account_code'=> "40103",
					'date >'        => $info_sales_return['date'],
					'deleted'       => 0
				];
				$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_balance  AS $info)
				{
					$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $info_sales_return_detail['total_return'])], ['id' => $info['id']]);
				}                            
			}
		}		
	}

	public function insert_general_ledger_ngehe()
	{
		ob_end_clean();
		$sales_returns = $this->crud->get_where('sales_return', ['code' => "SR301222004"])->result_array();
		foreach($sales_returns AS $sales_return)
		{
			$customer = $this->crud->get_where('customer', ['code' => $sales_return['customer_code']])->row_array();
			// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
			$where_last_balance = [
				'coa_account_code' => "10301",
				'date <='          => $sales_return['date'],                    
				'deleted'          => 0
			];
			$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_return['total_return']) : add_balance(0, $sales_return['total_return']);
			$data = [
				'date'              => $sales_return['date'],
				'coa_account_code'  => "10301",
				'transaction_id'    => $sales_return['id'],
				'invoice'           => $sales_return['code'],
				'information'       => 'RETUR PENJUALAN',
				'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
				'debit'            => $sales_return['total_return'],
				'balance'     		=> $balance
			];									
			if($this->crud->insert('general_ledger', $data))
			{
				$where_after_balance = [
					'coa_account_code'=> "10301",
					'date >'        => $sales_return['date'],
					'deleted'       => 0
				];
				$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_balance  AS $info)
				{
					$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_return['total_return'])], ['id' => $info['id']]);
				}
			}
			// GENERAL LEDGER -> KAS (K)
			$where_last_balance = [
				'coa_account_code' => "10101",
				'date <='          => $sales_return['date'],                    
				'deleted'          => 0
			];
			$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_return['total_return']) : sub_balance(0, $sales_return['total_return']);
			$data = [
				'date'              => $sales_return['date'],
				'coa_account_code'  => "10101",
				'transaction_id'    => $sales_return['id'],
				'invoice'           => $sales_return['code'],
				'information'       => 'RETUR PENJUALAN',
				'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
				'credit'             => $sales_return['total_return'],
				'balance'     		=> $balance
			];									
			if($this->crud->insert('general_ledger', $data))
			{
				$where_after_balance = [
					'coa_account_code'=> "10101",
					'date >'        => $sales_return['date'],
					'deleted'       => 0
				];
				$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_balance  AS $info)
				{
					$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_return['total_return'])], ['id' => $info['id']]);
				}
			}
		}	
		
		echo "SELESAI";
	}
	
	public function find_complied_select()
	{
		$coa_account_code = "10101";
		echo $this->db->select('gl.id, gl.date, coa.code, coa.name, gl.information, gl.invoice, gl.note, gl.debit, gl.credit, gl.balance')
						->from('general_ledger AS gl')
						->join('coa_account AS coa', 'coa.code = gl.coa_account_code')
						->where('coa_account_code', $coa_account_code)
						->order_by('gl.date', 'ASC')
						->order_by('gl.id', 'ASC')
						->get_compiled_select();
	}
	
	
	public function generate_start_balance()
	{
		$coa_account = $this->crud->get_where('coa_account', ['deleted' => 0])->result_array();
		foreach($coa_account AS $info_coa_account)
		{
			$data_general_ledger = [
				'date' => date('Y-m-d'),
				'coa_account_code' => $info_coa_account['code'],
				'information' => "SALDO AWAL",
				'invoice' => 'SLDOAWL'.date('dmY'),
				'note' => 'SALDO_AWAL_'.date('dmY'),
				'debit' => 0,
				'credit' => 0,
				'balance' => 0
			];
			$this->crud->insert('general_ledger', $data_general_ledger);
		}
	}

	public function module_url()
	{
		$access = $this->crud->get('access')->result_array();
		foreach($access AS $info)
		{
			$module = $this->crud->get_where('module', ['id' => $info['module_id']])->row_array();
			$this->crud->update('access', ['module_url' => $module['url']], ['id' => $info['id']]);
		}
		
		echo "SELESAI";
	}

	public function reset_stock()
	{
		$products = $this->crud->get_where('stock', ['qty >' => 0, 'deleted' => 0])->result_array();
		foreach($products AS $product)
		{
			$qty_convert = $product['qty'];
			// STOCK
			$check_stock = $this->crud->get_where('stock', ['product_code' => $product['product_code'], 'warehouse_id' => $product['warehouse_id']]);
			if($check_stock->num_rows() == 1)
			{
				$data_stock = $check_stock->row_array();
				$where_stock = array(
					'product_code'  => $product['product_code'],
					'warehouse_id'  => $product['warehouse_id']
				);
				$stock = array(                                
					'product_id' => $product['product_id'],
					'qty'        => $qty_convert-$data_stock['qty'],
				);
				$update_stock = $this->crud->update('stock', $stock, $where_stock);

				// STOCK CARD
				$where_last_stock_card = [
					'date <='      => date('Y-m-d'),
					'product_id'   => $product['product_id'],																						
					'warehouse_id' => $product['warehouse_id'],
					'deleted'      => 0											
				];
				$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
				$data_stock_card = array(
					'type'            => NULL, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
					'information'     => 'RESETSTOK'.date('Ymd'),
					'note'			  => 'RESETSTOK'.date('Ymd'),
					'date'            => date('Y-m-d'),
					'transaction_id'  => NULL,
					'invoice'         => 'RESETSTOK'.date('Ymd'),
					'product_id'      => $product['product_id'],
					'product_code'    => $product['product_code'],
					'qty'             => $qty_convert,																						
					'method'          => 2, // 1:In, 2:Out
					'stock'           => $qty_convert-$last_stock_card['stock'],
					'warehouse_id'    => $product['warehouse_id'],
					'employee_code'   => $this->session->userdata('code_e')
				);
				$this->crud->insert('stock_card',$data_stock_card);
				$where_after_stock_card = [
					'date >'       => date('Y-m-d'),
					'product_id'   => $product['product_id'],				
					'warehouse_id' => $product['warehouse_id'],
					'deleted'      => 0
				];                    
				$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_stock_card  AS $info_after_stock_card)
				{
					$this->crud->update('stock_card', ['stock' => $qty_convert-$info_after_stock_card['stock']], ['id' => $info_after_stock_card['id']]);
				}	
				// STOCK MOVEMENT
				$where_last_stock_movement = [
					'product_id'   => $product['product_id'],
					'date <='      => date('Y-m-d'),
					'deleted'      => 0
				];
				$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
				$data_stock_movement = [
					'type'            => NULL, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
					'information'     => 'RESETSTOK'.date('Ymd'),
					'note'			  => 'RESETSTOK'.date('Ymd'),
					'date'            => date('Y-m-d'),
					'transaction_id'  => NULL,
					'invoice'         => 'RESETSTOK'.date('Ymd'),
					'product_id'      => $product['product_id'],
					'product_code'    => $product['product_code'],
					'qty'             => $qty_convert,
					'method'          => 2, // 1:In, 2:Out
					'stock'           => $qty_convert-$last_stock_movement['stock'],
					'employee_code'   => $this->session->userdata('code_e')
				];
				$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
				$where_after_stock_movement = [
					'product_id'   => $product['product_id'],
					'date >'       => date('Y-m-d'),
					'deleted'      => 0
				];                    
				$after_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_stock_movement  AS $info_after_stock_movement)
				{
					$this->crud->update('stock_movement', ['stock' => $qty_convert-$info_after_stock_movement['stock']], ['id' => $info_after_stock_movement['id']]);
				}
			}			
		}			    
	    echo "selesai";
	}	
}