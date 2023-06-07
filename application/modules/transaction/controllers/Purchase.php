<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('master/Product_model','product');
		$this->load->model('finance/Payment_model','payment');
		$this->load->model('Purchase_model','purchase');
	}

    public function index()
    {
		if($this->system->check_access('purchase/order', 'A') == true || $this->system->check_access('purchase/invoice', 'A') == true )
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR PEMESANAN/PEMBELIAN',
				'method'      => 1,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Pembelian");			  
			$footer = array("script" => ['transaction/purchase/purchase.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('purchase/purchase');
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('dashboard'));
		}
	}

	// PURCHASE INVOICE
	public function purchase_invoice()
	{
		if($this->system->check_access('purchase/invoice', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');				
				$this->datatables->select('purchase_invoice.id AS id, purchase_invoice.code, purchase_invoice.date, purchase_invoice.invoice, purchase_invoice.payment, purchase_invoice.due_date,  purchase_invoice.grandtotal, (purchase_invoice.account_payable+purchase_invoice.cheque_payable) AS account_payable, purchase_invoice.payment_status, supplier.name AS name_s, purchase_invoice.ppn,
									purchase_invoice.code AS search_code');
				$this->datatables->from('purchase_invoice');
				$this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
				$this->datatables->where('purchase_invoice.deleted', 0);
				$this->datatables->where('DATE(purchase_invoice.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
				$this->datatables->where('DATE(purchase_invoice.created) <=', date('Y-m-d'));
				$this->datatables->group_by('purchase_invoice.id');
				$this->datatables->add_column('code', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/$1').'" target="_blank"><b>$2</b></a>
				', 'encrypt_custom(id), code');
				echo $this->datatables->generate();
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT DAFTAR PEMBELIAN',
					'method'      => 1,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
	
				$header = array("title" => "Pembelian");			  
				$footer = array("script" => ['transaction/purchase/invoice/purchase_invoice.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('purchase/invoice/purchase_invoice');
				$this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
            redirect(site_url('dashboard'));
        }        
	}

	public function check_invoice()
	{
		if($this->input->is_ajax_request())
		{
			$where = array(
				'invoice' => $this->input->post('invoice'),
				'deleted' => 0
			);
			$data = $this->crud->get_where('purchase_invoice', $where);
			if($data->num_rows() >= 1)
			{
				$response = array(
					'result' => 1
				);
			}
			else
			{
				$response = array(
					'result' => 0
				);
	
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}				
	}

	public function get_supplier()
    {
		if($this->input->is_ajax_request())
		{
			$data = $this->crud->get_where('supplier', ['deleted' => '0'])->result();
			if($data)
			{
				$response   =   [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data,
				];
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => '',
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	public function check_supplier()
	{
		if($this->input->is_ajax_request())
		{
			$where = array(
				'code' => $this->input->post('supplier_code'),
				'deleted' => 0,
			);
			$response = $this->crud->get_where('supplier', $where)->row_array();
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function get_payment_due()
	{
		if($this->input->is_ajax_request())
		{
			$supplier = $this->crud->get_where('supplier', ['code' => $this->input->post('supplier_code')])->row_array();
			$response = array(
				'dueday' => $supplier['dueday']
			);
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	} 
	
	public function get_product()
	{
		if($this->input->is_ajax_request())
		{
			$search = urldecode($this->uri->segment(4));	
			$ppn    = urldecode($this->uri->segment(5));
			$data   = $this->purchase->get_product($search, $ppn);
			$response = array();
			if($data->num_rows() > 0){
				foreach($data->result_array() as $info)
				{
					$response[] = array(
						'barcode' => $info['barcode'],
						'code' => $info['code'],
						'name' => $info['name'],
					);
				}            
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function get_unit()
    {
		if($this->input->is_ajax_request())
		{
			$code = $this->input->post('code');
			$where = array(
				'product_unit.product_code'  => $code,
				'product_unit.deleted'       => 0
			);
			$unit       = $this->purchase->get_unit($where)->result_array();
			$option		= "";		
			foreach($unit as $data)
			{
				if($data['default']==1)
				{
					$option .= "<option value='".$data['id_u']."' selected>".$data['code_u']."</option>";
				}
				else
				{
					$option .= "<option value='".$data['id_u']."'>".$data['code_u']."</option>";
				}
			}		
			$result = array
			(
				'option'=>$option
			);
			echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}       
	}
	
	public function get_buyprice()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$product_code = $post['product_code'];
			$unit_id      = $post['unit_id'];
			$result = array(
				'buyprice' => $this->purchase->get_buyprice($product_code, $unit_id)
			);
			echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function get_warehouse()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$warehouse = $this->purchase->get_warehouse($post['product_code'], $post['unit_id']);
			$option = null;
			foreach($warehouse as $data)
			{
				if($data['default']==1)
				{
					$option .= "<option value='".$data['id_w']."' selected>".$data['code_w']." | ".$data['stock']."</option>";
				}
				else
				{
					$option .= "<option value='".$data['id_w']."'>".$data['code_w']." | ".$data['stock']."</option>";
				}
			}		
			$result = array
			(
				'option'=>$option
			);
			echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

    public function create_purchase_invoice()
	{
		if($this->system->check_access('purchase/invoice', 'C'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->form_validation->set_rules('date', 'Tanggal Pembelian', 'trim|required|xss_clean');
				$this->form_validation->set_rules('invoice', 'No. Refrensi Pembelian', 'trim|required|xss_clean');
				$this->form_validation->set_rules('supplier_code', 'Supplier', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment', 'Jenis Pembayaran', 'trim|required|xss_clean');
				$this->form_validation->set_rules('ppn', 'PPN', 'trim|xss_clean');
				if($post['payment'] == 2)
				{
					$this->form_validation->set_rules('payment_due', 'Jatuh Tempo', 'trim|required|xss_clean');
				}
				$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
				$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|xss_clean');
				$this->form_validation->set_rules('account_payable', 'Hutang Dagang', 'trim|required|xss_clean');
				$this->form_validation->set_rules('discount_p', 'Diskon (%)', 'trim|required|xss_clean');
				$this->form_validation->set_rules('discount_rp', 'Diskon (Rp)', 'trim|required|xss_clean');
				$this->form_validation->set_rules('grandtotal', 'grandtotal', 'trim|required|xss_clean');				
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Pembelian Baru");
					$footer = array("script" => ['transaction/purchase/invoice/create_purchase_invoice.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('purchase/invoice/create_purchase_invoice');
					$this->load->view('include/footer', $footer);
				}
				else
				{
					// ALGORITHM
					/*
						-TABLE PURCHASE_INVOICE
						-GENERAL LEDGER -> PERSEDIAAN BARANG (D)
						-GENERAL LEDGER -> PPN MASUKAN (D)
						-GENERAL LEDGER -> HUTANG USAHA	(K)
						-------------------------------					 
						-TABLE PAYMENT_LEDGER -> CASH_LEDGER -> GENERAL_LEDGER (KAS, HUTANG USAHA)
						-TABLE PURCHASE_INVOICE_DETAIL
					 */						
					$purchase_invoice_code = $this->purchase->purchase_invoice_code();
					$supplier 			= $this->crud->get_where('supplier', ['code' => $post['supplier_code']])->row_array();					
					$plus 			    = $post['payment_due'];
					$ppn 			    = $post['ppn'];
					$price_include_tax  = (!isset($post['price_include_tax'])) ?  0 : $post['price_include_tax'];
					$total_price  		= format_amount($post['subtotal']);
					$discount_rp 		= format_amount($post['discount_rp']);
					$total_tax  		= format_amount($post['total_tax']);
					$delivery_cost 		= format_amount($post['delivery_cost']);
					$grandtotal  		= format_amount($post['grandtotal']);
					$down_payment  		= format_amount($post['down_payment']);
					$account_payable	= format_amount($post['account_payable']);
					$created			= date('Y-m-d H:i:s');				
					// PURCHASE INVOICE
					$data_purchase		= [
						'code'				=> $purchase_invoice_code,
						'date'				=> format_date($post['date']),
						'employee_code'		=> $this->session->userdata('code_e'),
						'supplier_code'		=> $post['supplier_code'],
						'invoice'			=> $post['invoice'],
						'payment'			=> $post['payment'],
						'payment_due'		=> $post['payment_due'],
						'total_product'		=> $post['total_product'],
						'total_qty'			=> $post['total_qty'],
						'total_price'		=> $total_price,
						'discount_p'		=> $post['discount_p'],
						'discount_rp'		=> $discount_rp,
						'ppn'				=> $ppn,
						'price_include_tax' => $price_include_tax,
						'total_tax' 		=> $total_tax,
						'delivery_cost'		=> $delivery_cost,
						'grandtotal'		=> $grandtotal,
						'account_payable'	=> ($post['payment'] == 1) ? 0 : $account_payable,
						'payment_status'	=> $post['payment'],
						'due_date'          => date('Y-m-d',strtotime(format_date($post['date']) . "+$plus days")),
						'information'		=> $post['information'],
						'created'			=> $created
					];
					if($this->crud->get_where('purchase_invoice', ['created' => $created])->num_rows() == 0)
					{			
						// 1st TRANSACTION
						$this->db->trans_start();
						$purchase_invoice_id = $this->crud->insert_id('purchase_invoice', $data_purchase);						
						// GENERAL LEDGER -> PERSEDIAAN BARANG 10301 (D)
						$coa_inventory_value = ($ppn == 0) ? $grandtotal : ($grandtotal/1.11);
						$where_last_balance = [
							'coa_account_code' => "10301",
							'date <='        => format_date($post['date']),
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $coa_inventory_value) : add_balance(0, $coa_inventory_value);
						$data = [
							'date'              => format_date($post['date']),
							'coa_account_code'  => "10301",
							'transaction_id'    => $purchase_invoice_id,
							'invoice'     		=> $purchase_invoice_code,
							'information' 		=> 'PEMBELIAN',
							'note'		  		=> 'PEMBELIAN_'.$purchase_invoice_code.'_'.$supplier['name'],
							'debit'      		=> $coa_inventory_value,
							'balance'     		=> $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code' => "10301",
								'date >'           => format_date($post['date']),
								'deleted'          => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $coa_inventory_value)], ['id' => $info['id']]);
							}
						}
						// GENERAL LEDGER -> PPN MASUKAN (D)
						if($ppn != 0)
						{
							$coa_ppn_in = $grandtotal - ($grandtotal/1.11);
							$where_last_balance = [
								'coa_account_code' => "10601",
								'date <='        => format_date($post['date']),                    
								'deleted'        => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $coa_ppn_in) : add_balance(0, $coa_ppn_in);
							$data = [
								'date'        => format_date($post['date']),
								'coa_account_code'  => "10601",
								'transaction_id' => $purchase_invoice_id,
								'invoice'     => $purchase_invoice_code,
								'information' => 'PEMBELIAN',
								'note'		  => 'PEMBELIAN_'.$purchase_invoice_code.'_'.$supplier['name'],
								'debit'      => $coa_ppn_in,
								'balance'     => $balance
							];									
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "10601",
									'date >'        => format_date($post['date']),
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $coa_ppn_in)], ['id' => $info['id']]);
								}
							}
						}
						// GENERAL LEDGER -> HUTANG USAHA (K)
						$where_last_balance = [
							'coa_account_code' => "20101",
							'date <='        => format_date($post['date']),                    
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $grandtotal) : add_balance(0, $grandtotal);
						$data = [
							'date'        => format_date($post['date']),
							'coa_account_code'  => "20101",
							'transaction_id' => $purchase_invoice_id,
							'invoice'     => $purchase_invoice_code,
							'information' => 'PEMBELIAN',
							'note'		  => 'PEMBELIAN_'.$purchase_invoice_code.'_'.$supplier['name'],
							'credit'      => $grandtotal,
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "20101",
								'date >'        => format_date($post['date']),
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
							}                            
						}
						$this->db->trans_complete();
						if($this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
						}
						else
						{
							$this->db->trans_rollback();
							$this->session->set_flashdata('error', 'GAGAL! Pembelian gagal tersimpan');
							redirect(site_url('purchase/invoice'));
						}
						// 2nd TRANSACTION
						$this->db->trans_start();							
						// PAYMENT_LEDGER & CASH_LEDGER
						if($post['payment'] == 1)
						{
							// PAYMENT_LEDGER
							$pod_code = $this->payment->pod_code();
							$data_pod = [                        
								'transaction_type'=> 1,
								'code'           => $pod_code,
								'date'           => format_date($post['date']),								
								'information'    => $post['information'],
								'method'         => ($post['from_cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
								'cash'           => ($post['from_cl_type'] == 1) ? $grandtotal : 0,
								'transfer'       => ($post['from_cl_type'] == 2) ? $grandtotal : 0,
								'grandtotal'     => $grandtotal,
								'employee_code'  => $this->session->userdata('code_e')
							];
							$pod_id = $this->crud->insert_id('payment_ledger', $data_pod);
							if($pod_id != null)
							{									
								$data_pod_detail = [
									'pl_id'      => $pod_id,
									'method'     => ($post['from_cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
									'account_id' => $post['from_account_id'],
									'amount'     => $grandtotal
								];
								$this->crud->insert('payment_ledger_detail', $data_pod_detail);
								$data_pod_transaction = [
									'pl_id'      => $pod_id,
									'transaction_id'=> $purchase_invoice_id,										
									'cash'           => ($post['from_cl_type'] == 1) ? $grandtotal : 0,
									'transfer'       => ($post['from_cl_type'] == 2) ? $grandtotal : 0,
									'amount'     => $grandtotal
								];
								$this->crud->insert('payment_ledger_transaction', $data_pod_transaction);
								// CASH_LEDGER
								$from_where_last_balance = [
									'cl_type'    => $post['from_cl_type'],
									'account_id' => $post['from_account_id'],
									'date <='    => format_date($post['date']),                    
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$grandtotal : 0-$grandtotal;
								$data = [
									'cl_type'     => $post['from_cl_type'],
									'account_id'  => $post['from_account_id'],
									'transaction_id'   => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'date'        => format_date($post['date']),
									'amount'      => $grandtotal,
									'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
									'balance'     => $from_balance
								];									
								if($this->crud->insert('cash_ledger', $data))
								{
									$from_where_after_balance = [
										'cl_type'       => $post['from_cl_type'],
										'account_id'    => $post['from_account_id'],
										'date >'        => format_date($post['date']),
										'deleted'       => 0
									];
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$grandtotal], ['id' => $info['id']]);
									}                            
								}
								// GENERAL_LEDGER -> KAS & BANK (K)
								$where_last_balance = [
									'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
									'date <='        => format_date($post['date']),                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
								$data = [
									'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
									'date'        => format_date($post['date']),										
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'credit'      => $grandtotal,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
										'date >'        => format_date($post['date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
									}                            
								}
								// GENERAL LEDGER -> HUTANG USAHA (D)
								$where_last_balance = [
									'coa_account_code' => "20101",
									'date <='        => format_date($post['date']),                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
								$data = [
									'coa_account_code'  => "20101",
									'date'        => format_date($post['date']),
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'debit'       => $grandtotal,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "20101",
										'date >'        => format_date($post['date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
									}
								}
							}
						}
						elseif($post['payment'] == 2 && $down_payment > 0)
						{
							// PAYMENT_LEDGER
							$pod_code = $this->payment->pod_code();
							$data_pod = [                        
								'transaction_type'=> 1,
								'code'           => $pod_code,
								'date'           => format_date($post['date']),
								'information'    => $post['information'],
								'method'         => ($post['from_cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
								'cash'           => ($post['from_cl_type'] == 1) ? $down_payment : 0,
								'transfer'       => ($post['from_cl_type'] == 2) ? $down_payment : 0,
								'grandtotal'     => $down_payment,
								'employee_code'  => $this->session->userdata('code_e')
							];
							$pod_id = $this->crud->insert_id('payment_ledger', $data_pod);
							if($pod_id != null)
							{									
								$data_pod_detail = [
									'pl_id'      => $pod_id,
									'method'     => ($post['from_cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
									'account_id' => $post['from_account_id'],
									'amount'     => $down_payment
								];
								$this->crud->insert('payment_ledger_detail', $data_pod_detail);
								$data_pod_transaction = [
									'pl_id'      => $pod_id,
									'transaction_id'=> $purchase_invoice_id,
									'cash'           => ($post['from_cl_type'] == 1) ? $down_payment : 0,
									'transfer'       => ($post['from_cl_type'] == 2) ? $down_payment : 0,
									'amount'     => $down_payment
								];
								$this->crud->insert('payment_ledger_transaction', $data_pod_transaction);
								// CASH_LEDGER
								$from_where_last_balance = [
									'cl_type'    => $post['from_cl_type'],
									'account_id' => $post['from_account_id'],
									'date <='    => date('Y-m-d', strtotime($post['date'])),                    
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$down_payment : 0-$down_payment;
								$data = [
									'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
									'account_id'  => $post['from_account_id'],
									'transaction_id'   => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'date'        => date('Y-m-d', strtotime($post['date'])),
									'amount'      => $down_payment,
									'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'       => $post['from_cl_type'],
										'account_id'    => $post['from_account_id'],
										'date >'        => date('Y-m-d', strtotime($post['date'])),
										'deleted'       => 0
									];
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$down_payment], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> KAS & BANK (K)
								$where_last_balance = [
									'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
									'date <='        => format_date($post['date']),
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $down_payment) : sub_balance(0, $down_payment);
								$data = [
									'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
									'date'        => format_date($post['date']),
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'credit'      => $down_payment,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
										'date >'        => format_date($post['date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $down_payment)], ['id' => $info['id']]);
									}                            
								}
								// GENERAL LEDGER -> HUTANG USAHA (D)
								$where_last_balance = [
									'coa_account_code' => "20101",
									'date <='        => format_date($post['date']),                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $down_payment) : sub_balance(0, $down_payment);
								$data = [
									'coa_account_code'  => "20101",
									'date'        => format_date($post['date']),										
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'debit'      => $down_payment,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "20101",
										'date >'        => format_date($post['date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $down_payment)], ['id' => $info['id']]);
									}
								}
							}
						}
						$this->db->trans_complete();
						if($this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
						}
						else
						{
							$this->db->trans_rollback();
							$this->session->set_flashdata('error', 'GAGAL! Pembelian gagal tersimpan');
							redirect(site_url('purchase/invoice'));
						}
						// 3rd Transaction
						// PURCHASE INVOICE DETAIL
						$this->db->trans_start();
						foreach($post['product'] AS $info)
						{
							$res = 0;
							$product_id = $this->crud->get_product_id($info['product_code']);
							$qty	 = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);								
							$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
							$data_purchase_detail= [
								'purchase_invoice_id' => $purchase_invoice_id,
								'invoice'		=> $purchase_invoice_code,
								'product_id'	=> $product_id,
								'product_code'	=> $info['product_code'],											
								'qty'			=> $qty,
								'unit_id'		=> $info['unit_id'],
								'unit_value'    => isset($convert) ? $convert['value'] : 1,
								'warehouse_id'	=> $info['warehouse_id'],
								'price'			=> $price,
								'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
								'total'			=> $total,
								'ppn'			=> $ppn
							];
							$purchase_invoice_detail_id = $this->crud->insert_id('purchase_invoice_detail', $data_purchase_detail);
							if($purchase_invoice_detail_id != null)
							{
								// CONVERT QTY & PRICE
								$qty_convert = (isset($convert)) ? $qty*$convert['value'] : $qty;								
								if($ppn == 0)
								{
									$price_convert = ($total/$qty_convert)+($delivery_cost/$post['total_product']/$qty_convert);
								}
								elseif($ppn == 1)
								{
									if($price_include_tax == 0)
									{
										$price_convert = ($total/$qty_convert)+($delivery_cost/$post['total_product']/$qty_convert);
									}
									elseif($price_include_tax == 1)
									{
										$price_convert = ($total/1.11/$qty_convert)+($delivery_cost/$post['total_product']/$qty_convert);
									}										
								}
								// STOCK
								$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
								if($check_stock->num_rows() == 1)
								{
									$data_stock = $check_stock->row_array();
									$where_stock = array(
										'product_code'  => $info['product_code'],
										'warehouse_id'  => $info['warehouse_id']
									);
									$stock = array(                                
										'product_id' => $product_id,
										'qty'        => $data_stock['qty']+$qty_convert,
									);
									$update_stock = $this->crud->update('stock', $stock, $where_stock);
								}
								else
								{
									$stock = array(                                
										'product_id'    => $product_id,
										'product_code'  => $info['product_code'],                                                        
										'qty'           => $qty_convert,
										'warehouse_id'  => $info['warehouse_id']
									);
									$update_stock = $this->crud->insert('stock', $stock);
								}                            
								if($update_stock)
								{
									// STOCK CARD
									$where_last_stock_card = [
										'date <='      => format_date($post['date']),
										'product_id'   => $product_id,																						
										'warehouse_id' => $info['warehouse_id'],
										'deleted'      => 0											
									];
									$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$data_stock_card = array(
										'type'            => 1,
										'information'     => 'PEMBELIAN',
										'note'			  => $supplier['name'],
										'date'            => format_date($post['date']),
										'transaction_id'  => $purchase_invoice_id,
										'invoice'         => $purchase_invoice_code,
										'transaction_detail_id' => $purchase_invoice_detail_id,
										'product_id'      => $product_id,
										'product_code'    => $info['product_code'],
										'qty'             => $qty_convert,																						
										'method'          => 1,
										'stock'           => $last_stock_card['stock']+$qty_convert,
										'warehouse_id'    => $info['warehouse_id'],
										'employee_code'   => $this->session->userdata('code_e')
									);
									$this->crud->insert('stock_card',$data_stock_card);
									$where_after_stock_card = [
										'date >'       => format_date($post['date']),
										'product_id'   => $product_id,				
										'warehouse_id' => $info['warehouse_id'],
										'deleted'      => 0
									];                    
									$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_card  AS $info_after_stock_card)
									{
										$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']+$qty_convert], ['id' => $info_after_stock_card['id']]);
									}
									// STOCK MOVEMENT
									$where_last_stock_movement = [
										'product_id'   => $product_id,
										'date <='      => format_date($post['date']),
										'deleted'      => 0
									];
									$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$data_stock_movement = [
										'type'            => 1, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
										'information'     => 'PEMBELIAN',
										'note'			  => $supplier['name'],
										'date'            => format_date($post['date']),
										'transaction_id'  => $purchase_invoice_id,
										'invoice'         => $purchase_invoice_code,
										'transaction_detail_id' => $purchase_invoice_detail_id,
										'product_id'      => $product_id,
										'product_code'    => $info['product_code'],
										'qty'             => $qty_convert,
										'method'          => 1, // 1:In, 2:Out
										'stock'           => $last_stock_movement['stock']+$qty_convert,
										'employee_code'   => $this->session->userdata('code_e')
									];
									$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
									$where_after_stock_movement = [
										'product_id'   => $product_id,
										'date >'       => format_date($post['date']),
										'deleted'      => 0
									];                    
									$after_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_movement  AS $info_after_stock_movement)
									{
										$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']+$qty_convert], ['id' => $info_after_stock_movement['id']]);
									}
									// LAST BUYPRICE AND HPP
									// CHANGE HPP BY STOCK MOVEMENT
									// Find the last purchase
									$sub_where_last_purchase_invoice = [										
										'type'    => 1,
										'date <=' => format_date($post['date']),
										'product_code' => $info['product_code'],
										'deleted' => 0
									];
									$sub_last_purchase_invoice = $this->db->select('*')->from('stock_movement')->where($sub_where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('transaction_id', 'DESC')->get_compiled_select();
									$where_last_purchase_invoice = [
										'transaction_id <' => $purchase_invoice_id
									];
									$last_purchase_invoice = $this->db->select('*')->from("($sub_last_purchase_invoice) as result")->where($where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('result.id', 'DESC')->limit(1)->get()->row_array();
									// CALCULATE THE NEW HPP
									if($last_purchase_invoice == null)
									{
										$hpp=[
											'price' => $price_convert,
											'hpp'   => $price_convert
										];
										$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement_id]);
										$last_hpp = $price_convert;
									}
									else
									{
										// (QTY STOCK YANG ADA * HPP) + (qty baru * harga baru) / (qty lama + qty baru)
										$old_inventory_value = $last_stock_movement['stock']*$last_purchase_invoice['hpp'];
										$new_inventory_Value = $qty_convert*$price_convert;
										$new_stock = $last_stock_movement['stock']+$qty_convert;
										$total_new_stock = ($new_stock > 0) ? $new_stock : 1;
										$hpp = [
											'price' => $price_convert,
											'hpp'   => ($old_inventory_value+$new_inventory_Value)/$total_new_stock
										];
										$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement_id]);
										$last_hpp = $hpp['hpp'];
									}	
									// CHANGE ALL HPP AFTER STOCK MOVEMENT
									if($after_stock_movement == null)
									{
										$update_detail_product = array(
											'hpp' => $last_hpp,
											'supplier_code' => $post['supplier_code'],
											'buyprice' => $price_convert
										);
										$this->crud->update('product', $update_detail_product, ['code' => $info['product_code']]);																								
									}
									else
									{
										foreach($after_stock_movement  AS $info_after_stock_movement)
										{
											switch ($info_after_stock_movement['type']) {
												case 1: // PURCHASE _INVOICE
													// (QTY STOCK YANG ADA * HARGA BELI TERAKHIR) + (qty baru * harga baru) / (qty lama + qty baru)
													$old_inventory_value = $last_hpp*($info_after_stock_movement['stock']-$info_after_stock_movement['qty']+$qty_convert);
													$new_inventory_Value = $info_after_stock_movement['qty']*$info_after_stock_movement['price'];													
													$update_detail_product = array(
														'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert),
														'supplier_code' => $post['supplier_code'],
														'buyprice' => $info_after_stock_movement['price']
													);
													$this->crud->update('product', $update_detail_product, ['id' => $info_after_stock_movement['product_id']]);
													$hpp=[
														'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert)
													];
													$this->crud->update('stock_movement', $hpp, ['id' => $info_after_stock_movement['id']]);
													$last_hpp = $hpp['hpp'];
													break;
												case 2: // PURCHASE RETURN
													break;
												case 4: // SALES INVOICE
													$where_sales_invoice_detail=[
														'sales_invoice_id' => $info_after_stock_movement['transaction_id'],
														'product_id'	   => $info_after_stock_movement['product_id']
													];
													$this->crud->update('sales_invoice_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
													$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
													// RECALCULATE TOTAL HPP IN SALES INVOICE
													$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $info_after_stock_movement['transaction_id']])->result_array();
													$total_hpp_sales_invoice=0;
													foreach($sales_invoice_detail AS $info_sales_invoice_detail)
													{						
														$total_hpp_sales_invoice=$total_hpp_sales_invoice+($info_sales_invoice_detail['qty']*$info_sales_invoice_detail['unit_value']*$info_sales_invoice_detail['hpp']);
													}
													$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp_sales_invoice], ['id' => $info_after_stock_movement['transaction_id']]);
													$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
													break;
												case 5: // SALES RETURN
													$where_sales_return_detail=[
														'sales_return_id' => $info_after_stock_movement['transaction_id'],
														'product_id'	   => $info_after_stock_movement['product_id']
													];
													$this->crud->update('sales_return_detail', ['hpp' => $last_hpp], $where_sales_return_detail);
													$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
													break;
												case 7: // REPACKING
													break;
												case 8: // ADJUSMENT STOCK
													break;
												case 9: // MUTATION
													break;
												case 10: // PRODUCT USAGE
													break;
												default:
													break;
											}
										}	
									}
									$res = 1;
									continue;
								}
								else
								{
									break;
								}
							}
							else
							{						
								break;
							}
						}	
						$this->db->trans_complete();
					}					
					if($res == 1 && $this->db->trans_status() === TRUE)
					{
						$this->db->trans_commit();
						$data_activity = [
							'information' => 'MEMBUAT PEMBELIAN (NO. TRANSAKSI '.$purchase_invoice_code.')',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];
						$this->crud->insert('activity',$data_activity);
						$this->session->set_flashdata('success', 'SUKSES! Pembelian berhasil tersimpan');
						redirect(site_url('purchase/invoice/detail/'.encrypt_custom($purchase_invoice_id)));
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'GAGAL! Pembelian gagal tersimpan');
						redirect(site_url('purchase/invoice'));
					}
				}
			}
			else
			{
				$header = array("title" => "Pembelian Baru");
				$footer = array("script" => ['transaction/purchase/invoice/create_purchase_invoice.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('purchase/invoice/create_purchase_invoice');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/invoice'));
		}        
	}
	
	public function datatable_detail_purchase_invoice($purchase_id)
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('purchase_invoice_detail.id, product.code AS code_p, product.name AS name_p, unit.name AS name_u,warehouse.name AS name_w, purchase_invoice_detail.qty, purchase_invoice_detail.price, purchase_invoice_detail.disc_product, purchase_invoice_detail.total,
							product.code AS search_code_p');
			$this->datatables->from('purchase_invoice_detail');
			$this->datatables->join('product', 'product.code = purchase_invoice_detail.product_code');
			$this->datatables->join('unit', 'unit.id = purchase_invoice_detail.unit_id');
			$this->datatables->join('warehouse', 'warehouse.id = purchase_invoice_detail.warehouse_id');		
			$this->datatables->where('purchase_invoice_detail.purchase_invoice_id', $purchase_id);
			$this->datatables->where('purchase_invoice_detail.deleted', 0);
			$this->datatables->group_by('purchase_invoice_detail.id');
			$this->datatables->add_column('code_p', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(code_p),code_p');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	public function datatable_detail_purchase_invoice_payment($purchase_id)
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();		
			header('Content-Type: application/json');
			$this->datatables->select('pod.id AS id, pod.code AS code_pod, pod.date AS date, pod_transaction.amount AS amount,
							   pod.code AS search_code_pod');
			$this->datatables->from('payment_ledger AS pod');
			$this->datatables->join('payment_ledger_transaction AS pod_transaction', 'pod_transaction.pl_id = pod.id');
			$this->datatables->where('pod.transaction_type', 1);
			$this->datatables->where('pod_transaction.transaction_id', $purchase_id);
			$this->datatables->where('pod.deleted', 0);
			$this->datatables->group_by('pod.id');
			$this->datatables->order_by('pod.date', 'DESC');
			$this->datatables->order_by('pod.id', 'DESC');
			$this->datatables->add_column('code_pod',
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id), code_pod');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function datatable_detail_purchase_invoice_purchase_return($purchase_invoice_id) 
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('purchase_return.id AS id_pr, purchase_return.code AS code_pr, purchase_return.date, total_return,
							purchase_return.code AS search_code');
			$this->datatables->from('purchase_return');
			$this->datatables->where('purchase_return.method', 2);
			$this->datatables->where('purchase_return.do_status', 1);
			$this->datatables->where('purchase_return.deleted', 0);			
			$this->datatables->where('purchase_return.purchase_invoice_id', $purchase_invoice_id);
			$this->datatables->group_by('purchase_return.id');
			$this->datatables->order_by('purchase_return.date', 'DESC');
			$this->datatables->order_by('purchase_return.id', 'DESC');
			$this->datatables->add_column('code_pr', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/return/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id_pr) ,code_pr');                
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function datatable_detail_purchase_invoice_tax_invoice($purchase_invoice_id) 
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('id, date, number, dpp, ppn, grandtotal,
							number AS search_number');
			$this->datatables->from('tax_invoice');
			$this->datatables->where('transaction_type', 1);
			$this->datatables->where('transaction_id', $purchase_invoice_id);
			$this->datatables->order_by('date', 'DESC');
			$this->datatables->order_by('id', 'DESC');
			$this->datatables->add_column('action', 
			'
				<a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
					<i class="fa fa-times"></i>
				</a>            
			', 'id');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function detail_purchase_invoice($purchase_id)
    {
		if($this->system->check_access('purchase/invoice', 'R'))
		{
			$purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_id));			
			if($purchase_invoice != null)
			{
				$data_activity = [
					'information' => 'MELIHAT DETAIL PEMBELIAN (NO. TRANSAKSI '.$purchase_invoice['code'].')',
					'method'      => 2, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Detail Pembelian");
				$footer = array("script" => ['transaction/purchase/invoice/detail_purchase_invoice.js']);
				$data = array(
					'purchase_invoice' => $purchase_invoice
				);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('purchase/invoice/detail_purchase_invoice', $data);        
				$this->load->view('include/footer', $footer);
			}
			else
			{
				$this->load->view('auth/show_404');
			}								
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/invoice'));
		}		
	}

	public function add_purchase_tax_invoice()
	{
		if(true)
        {
            $post   = $this->input->post();
			$data = [
				'transaction_type' => 1,
				'transaction_id' => $post['purchase_invoice_id'],
				'date'   => date('Y-m-d', strtotime($post['date'])),
				'number' => $post['number'],
				'dpp'    => format_amount($post['dpp']),
				'ppn'    => format_amount($post['ppn']),
				'grandtotal' => format_amount($post['dpp'])+format_amount($post['ppn'])
			];                			
			if($this->crud->insert('tax_invoice', $data))
			{
				$purchase_invoice = $this->crud->get_where('purchase_invoice', ['id' => $post['purchase_invoice_id']])->row_array();
				$data_activity = [
					'information' => 'MEMBUAT FAKTUR PAJAK PEMBELIAN (NO.TRANSAKSI '.$purchase_invoice['code'].')',
					'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);

				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Berhasil Menambahkan Data',
					],
					'response'  => '',
					'purchase_invoice_id'  => encrypt_custom($purchase_invoice['id'])
				];         
				$this->session->set_flashdata('success', 'BERHASIL! Faktur Pajak berhasil ditambahkan');           
			}
			else
			{
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Gagal Menambahkan Data',
					],
					'response'  => ''
				];                    
				$this->session->set_flashdata('error', 'Mohon maaf, Faktur Pajak gagal ditambahkan');
			}
        }
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses',
                ],
                'response'  => ''
            ];                        
        }                
        echo json_encode($response);
	}

	public function update_purchase_tax_invoice()
	{
		if(true)
        {
			$post   = $this->input->post();			              			
			$purchase_tax_invoice = $this->crud->get_where('tax_invoice', ['id' => $post['id']])->row_array();
			if($this->crud->delete('tax_invoice', ['id' => $post['id']]))
			{				
				$data_activity = [
					'information' => 'MENGHAPUS FAKTUR PAJAK PEMBELIAN (NO.TRANSAKSI '.$purchase_tax_invoice['number'].')',
					'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);

				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Berhasil Menghapus Data',
					],
					'response'  => '',
				];
			}
			else
			{
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Gagal Menghapus Data',
					],
					'response'  => ''
				];
			}
        }
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses',
                ],
                'response'  => ''
            ];                        
        }                
        echo json_encode($response);
	}

	public function delete_purchase_tax_invoice()
	{
		if(true)
        {
			$post   = $this->input->post();			              			
			$purchase_tax_invoice = $this->crud->get_where('tax_invoice', ['id' => $post['id']])->row_array();
			if($this->crud->delete('tax_invoice', ['id' => $post['id']]))
			{				
				$data_activity = [
					'information' => 'MENGHAPUS FAKTUR PAJAK PEMBELIAN (NO.TRANSAKSI '.$purchase_tax_invoice['number'].')',
					'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);

				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Berhasil Menghapus Data',
					],
					'response'  => '',
				];
			}
			else
			{
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Gagal Menghapus Data',
					],
					'response'  => ''
				];
			}
        }
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses',
                ],
                'response'  => ''
            ];                        
        }                
        echo json_encode($response);
	}
		
	public function update_purchase_invoice($purchase_invoice_id)
    {
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_invoice_id));
			$purchase_invoice_detail = $this->purchase->get_detail_purchase_invoice_detail($purchase_invoice['id']);
			// echo json_encode($post); die;
			$this->form_validation->set_rules('purchase_id', 'ID Transaksi Pembelian', 'trim|required|xss_clean');
			$this->form_validation->set_rules('date', 'Tanggal Pembelian', 'trim|required|xss_clean');
			$this->form_validation->set_rules('invoice', 'No. Refrensi Pembelian', 'trim|required|xss_clean');
			$this->form_validation->set_rules('supplier_code', 'Supplier', 'trim|required|xss_clean');
			$this->form_validation->set_rules('payment', 'Jenis Pembayaran', 'trim|required|xss_clean');
			$this->form_validation->set_rules('ppn', 'PPN', 'trim|xss_clean');
			if($post['payment'] == 2)
			{
				$this->form_validation->set_rules('payment_due', 'Jatuh Tempo', 'trim|required|xss_clean');
			}
			$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
			$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
			$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|xss_clean');
			$this->form_validation->set_rules('account_payable', 'Hutang Dagang', 'trim|required|xss_clean');
			$this->form_validation->set_rules('discount_p', 'Diskon (%)', 'trim|required|xss_clean');
			$this->form_validation->set_rules('discount_rp', 'Diskon (Rp)', 'trim|required|xss_clean');
			$this->form_validation->set_rules('grandtotal', 'grandtotal', 'trim|required|xss_clean');
			if($this->form_validation->run() == FALSE)
			{					
				if($purchase_invoice != null)
				{
					$where_payment_ledger = [
						'transaction_type' => 1,
						'transaction_id'   => $purchase_invoice['id']
					];
					$payment_ledger = $this->db->select('sum(grandtotal) AS grandtotal')->from('payment_ledger')->where($where_payment_ledger)->get()->row_array();
					$header = array("title" => "Perbarui Pembelian");
					$footer = array("script" => ['transaction/purchase/invoice/update_purchase_invoice.js']);
					$data = array(
						'purchase_invoice' => $purchase_invoice,
						'purchase_invoice_detail' => $this->purchase->get_detail_purchase_invoice_detail($purchase_invoice['id']),
						'payment_ledger' => $payment_ledger
					);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('purchase/invoice/update_purchase_invoice', $data);        
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$this->load->view('auth/show_404');
				}
			}
			else
			{
				// ALGORITHM
				/*					
					-TABLE PURCHASE_INVOICE
					-GENERAL LEDGER -> PERSEDIAAN BARANG (D)
					-GENERAL LEDGER -> PPN MASUKAN (D)
					-GENERAL LEDGER -> HUTANG USAHA	(K)					
					-------------------------------					 
					-TABLE PAYMENT_LEDGER -> CASH_LEDGER -> GENERAL_LEDGER (KAS, HUTANG USAHA)
					-TABLE PURCHASE__INVOICE_DETAIL
				*/
				$this->db->trans_start();
				$date 				= format_date($post['date']);
				$supplier 			= $this->crud->get_where('supplier', ['code' => $post['supplier_code']])->row_array();				
				$plus 			    = $post['payment_due'];
				$ppn 			    = $post['ppn'];
				$price_include_tax  = (!isset($post['price_include_tax'])) ?  0 : $post['price_include_tax'];
				$total_price  		= format_amount($post['subtotal']);
				$discount_rp 		= format_amount($post['discount_rp']);
				$account_payable	= format_amount($post['account_payable']);
				$total_tax  		= format_amount($post['total_tax']);
				$grandtotal  		= format_amount($post['grandtotal']);
				$data_purchase=[						
					'date'				=> $date,
					'employee_code'		=> $this->session->userdata('code_e'),
					'supplier_code'		=> $post['supplier_code'],
					'invoice'			=> $post['invoice'],
					'payment'			=> $post['payment'],
					'payment_due'		=> $post['payment_due'],
					'total_product'		=> $post['total_product'],
					'total_qty'			=> $post['total_qty'],
					'total_price'		=> $total_price,
					'discount_p'		=> $post['discount_p'],
					'discount_rp'		=> $discount_rp,
					'ppn'				=> $ppn,
					'price_include_tax' => $price_include_tax,
					'total_tax' 		=> $total_tax,
					'grandtotal'		=> $grandtotal,
					'account_payable'	=> $account_payable,
					'payment_status'	=> ($account_payable == 0) ? 1 : 2,
					'due_date'          => date('Y-m-d', strtotime($date . "+$plus days")),
					'information'		=> $post['information']
				];
				// UPDATE PURCHASE
				if($this->crud->update('purchase_invoice', $data_purchase, ['id' => $purchase_invoice['id']]))
				{
					// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
					$coa_inventory_value = ($ppn == 0) ? $grandtotal : ($grandtotal/1.11);
					$where_general_ledger = [
						'coa_account_code' => "10301",
						'invoice' => $purchase_invoice['code']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
					$update_general_ledger = [
						'date'  => $date,
						'debit' => $coa_inventory_value
					];
					$this->crud->update('general_ledger', $update_general_ledger, ['id' => $general_ledger['id']]);					
					// GENERAL LEDGER -> PPN MASUKAN (D)
					if($ppn != 0)
					{
						$coa_ppn_in = $grandtotal - ($grandtotal/1.11);
						$where_general_ledger = [
							'coa_account_code' => "10601",
							'invoice' => $purchase_invoice['code']
						];
						$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
						$update_general_ledger = [
							'date'  => $date,
							'debit' => $coa_ppn_in
						];
						$this->crud->update('general_ledger', $update_general_ledger, ['id' => $general_ledger['id']]);
					}
					// GENERAL LEDGER -> HUTANG USAHA (K)
					$where_general_ledger = [
						'coa_account_code' => "20101",
						'invoice' => $purchase_invoice['code']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->row_array();
					$update_general_ledger = [
						'date'   => $date,
						'credit' => $grandtotal
					];
					$this->crud->update('general_ledger', $update_general_ledger, ['id' => $general_ledger['id']]);
					// LIST OLD PRODUCT, AND NEW PRODUCT
					$old_purchase_detail_id = []; $new_purchase_detail_id = [];
					foreach($purchase_invoice_detail AS $info_old_product)
					{
						$old_purchase_detail_id[] = $info_old_product['id'];						
					}
					foreach($post['product'] AS $info_new_product)
					{
						if(isset($info_new_product['purchase_detail_id']))
						{
							$new_purchase_detail_id[] = $info_new_product['purchase_detail_id'];
						}
					}
					// CHECK AND DELETE OLD PRODUCT WHERE NOT LISTED IN NEW LIST PRODUCT						
					foreach($purchase_invoice_detail AS $info_old_product)
					{
						if(in_array($info_old_product['id'], $new_purchase_detail_id))
						{
							continue;
						}
						else
						{
							// REDUCE STOCK								
							$where_stock = [
								'product_code'	=> $info_old_product['product_code'],
								'warehouse_id'	=> $info_old_product['warehouse_id']
							];
							$stock = $this->crud->get_where('stock', $where_stock)->row_array();
							$update_stock = [
								'qty' => $stock['qty']-($info_old_product['qty']*$info_old_product['unit_value'])
							];
							$this->crud->update('stock', $update_stock, $where_stock);

							// UPDATE AFTER STOCK CARD AND DELETE OLD STOCK CARD
							$where_stock_card = [
								'transaction_id' => $purchase_invoice['id'],
								'product_code'	 => $info_old_product['product_code'],
								'type'			 => 1,
								'method'		 => 1,
								'warehouse_id'	 => $info_old_product['warehouse_id']
							];								
							$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
							$where_after_stock_card = [
								'id >'          => $stock_card['id'],
								'product_code'	=> $info_old_product['product_code'],
								'warehouse_id'	=> $info_old_product['warehouse_id'],
								'deleted'		=> 0
							];
							$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.id', 'ASC')->get()->result_array();
							foreach($after_stock_cards AS $info_stock_card)
							{
								$update_stock_card = [
									'stock' => $info_stock_card['stock'] - ($info_old_product['qty']*$info_old_product['unit_value'])
								];
								$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
							}
							$this->crud->delete('stock_card', ['id' =>  $stock_card['id']]);
							
							// UPDATE AFTER STOCK MOVEMENT AND DELETE OLD STOCK MOVEMENT
							$where_stock_movement = [
								'transaction_id' => $purchase_invoice['id'],
								'product_code'	 => $info_old_product['product_code'],
								'type'			 => 1,
								'method'		 => 1,
							];								
							$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
							$where_after_stock_movement = [
								'id >'          => $stock_movement['id'],
								'product_code'	=> $info_old_product['product_code'],
								'deleted'		=> 0
							];
							$after_stock_movements = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('stock_movement.id', 'ASC')->get()->result_array();
							foreach($after_stock_movements AS $info_stock_movement)
							{
								$update_stock_movement = [
									'stock' => $info_stock_movement['stock'] - ($info_old_product['qty']*$info_old_product['unit_value'])
								];
								$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
							}
							$this->crud->delete('stock_movement', ['id' =>  $stock_movement['id']]);

							// DELETE PURCHASE DETAIL ID
							$where_purchase_detail = [
								'id'	=> $info_old_product['id']
							];
							$this->crud->delete('purchase_invoice_detail', $where_purchase_detail);
						}
					}
					foreach($post['product'] AS $info)
					{
						$res=0;
						// SKIP THE FIRST ROW
						if($info['product_code'] == ""  && $info['qty'] == "" && $info['price'] == "" && $info['total'] == "")
						{																	
							continue;
						}
						else
						{									
							if(isset($info['purchase_detail_id'])) // IF OLD PRODUCT
							{		
								$i = array_search($info['purchase_detail_id'], array_column($purchase_invoice_detail, 'id'));
								// UPDATE PURCHASE DETAIL
								$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
								$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
								$qty_convert = isset($convert) ? $qty*$convert['value'] : $qty;
								$price_convert = $total / $qty_convert;
								$data_purchase_detail= [											
									'qty'			=> $info['qty'],
									'unit_id'		=> $info['unit_id'],
									'unit_value'    => isset($convert) ? $convert['value'] : 1,
									'warehouse_id'	=> $info['warehouse_id'],
									'price'			=> $price,
									'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
									'total'			=> $total,
									'ppn'			=> $ppn
								];
								$this->crud->update('purchase_invoice_detail', $data_purchase_detail, ['id' => $info['purchase_detail_id']]);
								// MINUS STOCK IN OLD WAREHOUSE
								$where_stock = [
									'product_code'	=> $info['product_code'],
									'warehouse_id'	=> $purchase_invoice_detail[$i]['warehouse_id']
								];
								$stock = $this->crud->get_where('stock', $where_stock)->row_array();
								$update_stock = [
									'qty' => $stock['qty']-($purchase_invoice_detail[$i]['qty']*$purchase_invoice_detail[$i]['unit_value'])
								];
								$this->crud->update('stock', $update_stock, $where_stock);
								// PLUS STOCK IN NEW WARENOUSE
								$where_stock = [
									'product_code'	=> $info['product_code'],
									'warehouse_id'	=> $info['warehouse_id']
								];
								$stock = $this->crud->get_where('stock', $where_stock)->row_array();
								if($stock == null)
								{
									$data_stock = array(                                
										'product_id'    => $product_id,
										'product_code'  => $info['product_code'],                                                        
										'qty'           => $qty_convert,
										'warehouse_id'  => $info['warehouse_id']
									);
									$this->crud->insert('stock', $data_stock);
								}
								else
								{
									$update_stock = [
										'qty' => $stock['qty'] + $qty_convert
									];
									$this->crud->update('stock', $update_stock, $where_stock);
								}	
								// UPDATE STOCK CARD
								$where_stock_card = [
									'type'	=> 1,
									'transaction_id' => $purchase_invoice['id'],
									'transaction_detail_id' => $purchase_invoice_detail[$i]['id'],
									'product_code'	 => $info['product_code']
								];								
								$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
								$data_stock_card = [
									'note'	=> $supplier['name'],
									'date'	=> $date,
									'qty'	=> $qty_convert,
									'warehouse_id' => $info['warehouse_id']
								];
								$this->crud->update('stock_card', $data_stock_card, ['id' => $stock_card['id']]);
								// UPDATE STOCK MOVEMENT
								$where_stock_movement = [
									'type'	=> 1,
									'transaction_id' => $purchase_invoice['id'],
									'transaction_detail_id' => $purchase_invoice_detail[$i]['id'],
									'product_code'	 => $info['product_code']
								];								
								$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
								$data_stock_movement = [
									'note'	=> $supplier['name'],
									'date'	=> $date,
									'qty'	=> $qty_convert,	
									'price' => $price								
								];
								$this->crud->update('stock_movement', $data_stock_movement, ['id' => $stock_movement['id']]);							
								// RECALCULATE STOCK CARD AND MOVEMENT							
								if($date >= $purchase_invoice['date'])
								{	
									$from_date = date('Y-m-d',strtotime($purchase_invoice['date']."-1 days"));	
									// OLD WAREHOUSE
									$where_last_stock_card = [
										'date <='      => $from_date,
										'product_code' => $info['product_code'],
										'warehouse_id' => $purchase_invoice_detail[$i]['warehouse_id'],
										'deleted'      => 0
									];
									$last_stock_card = $this->db->select('*')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$stock = $last_stock_card['stock'];
									$where_after_stock_card = [			
										'date >='	   => $purchase_invoice['date'],						
										'product_code' => $info['product_code'], 
										'warehouse_id' => $purchase_invoice_detail[$i]['warehouse_id'],
									];
									$safter_stock_card = $this->db->select('*')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($safter_stock_card AS $info_after_stock_card)
									{
										$stock = ($info_after_stock_card['method'] == 1) ? $stock+$info_after_stock_card['qty'] : $stock-$info_after_stock_card['qty'];
										$this->crud->update('stock_card', ['stock' => $stock], ['id' => $info_after_stock_card['id']]);
									}
									// NEW WAREHOUSE
									$where_last_stock_card = [
										'date <='      => $from_date,
										'product_code' => $info['product_code'],
										'warehouse_id' => $info['warehouse_id'],
										'deleted'      => 0
									];
									$last_stock_card = $this->db->select('*')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$stock = $last_stock_card['stock'];
									$where_after_stock_card = [			
										'date >='	   => $purchase_invoice['date'],						
										'product_code' => $info['product_code'], 
										'warehouse_id' => $info['warehouse_id']
									];
									$after_stock_card = $this->db->select('*')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_card AS $info_after_stock_card)
									{
										$stock = ($info_after_stock_card['method'] == 1) ? $stock+$info_after_stock_card['qty'] : $stock-$info_after_stock_card['qty'];
										$this->crud->update('stock_card', ['stock' => $stock], ['id' => $info_after_stock_card['id']]);
									}
									// RECALCULATE STOCK MOVEMENT
									$where_last_stock_movement = [
										'date <='      => $from_date,
										'product_code' => $info['product_code'],										
										'deleted'      => 0
									];
									$last_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$stock = $last_stock_movement['stock'];
									$where_after_stock_movement = [			
										'date >='	   => $purchase_invoice['date'],						
										'product_code' => $info['product_code']										
									];									
									$after_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									// echo json_encode($after_stock_movement); die;
									foreach($after_stock_movement AS $info_after_stock_movement)
									{
										$stock = ($info_after_stock_movement['method'] == 1) ? $stock+$info_after_stock_movement['qty'] : $stock-$info_after_stock_movement['qty'];
										$this->crud->update('stock_movement', ['stock' => $stock], ['id' => $info_after_stock_movement['id']]);
									}	
								}
								elseif($date < $purchase_invoice['date'])
								{
									$from_date = date('Y-m-d',strtotime($date."-1 days"));
									// OLD WAREHOUSE
									$where_last_stock_card = [
										'date <='      => $from_date,
										'product_code' => $info['product_code'],
										'warehouse_id' => $purchase_invoice_detail[$i]['warehouse_id'],
										'deleted'      => 0
									];
									$last_stock_card = $this->db->select('*')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$stock = $last_stock_card['stock'];
									$where_after_stock_card = [			
										'date >='	   => $date,						
										'product_code' => $info['product_code'], 
										'warehouse_id' => $purchase_invoice_detail[$i]['warehouse_id'],
									];
									$after_stock_card = $this->db->select('*')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_card AS $info_after_stock_card)
									{
										$stock = ($info_after_stock_card['method'] == 1) ? $stock+$info_after_stock_card['qty'] : $stock-$info_after_stock_card['qty'];
										$this->crud->update('stock_card', ['stock' => $stock], ['id' => $info_after_stock_card['id']]);
									}
									// NEW WAREHOUSE
									$where_last_stock_card = [
										'date <='      => $from_date,
										'product_code' => $info['product_code'],
										'warehouse_id' => $info['warehouse_id'],
										'deleted'      => 0
									];
									$last_stock_card = $this->db->select('*')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$stock = $last_stock_card['stock'];
									$where_after_stock_card = [			
										'date >='	   => $date,						
										'product_code' => $info['product_code'], 
										'warehouse_id' => $info['warehouse_id']
									];
									$after_stock_card = $this->db->select('*')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_card AS $info_after_stock_card)
									{
										$stock = ($info_after_stock_card['method'] == 1) ? $stock+$info_after_stock_card['qty'] : $stock-$info_after_stock_card['qty'];
										$this->crud->update('stock_card', ['stock' => $stock], ['id' => $info_after_stock_card['id']]);
									}
									// RECALCULATE STOCK MOVEMENT
									$where_last_stock_movement = [
										'date <='      => $from_date,
										'product_code' => $info['product_code'],										
										'deleted'      => 0
									];
									$last_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$stock = $last_stock_movement['stock'];
									$where_after_stock_movement = [			
										'date >='	   => $date,						
										'product_code' => $info['product_code']										
									];
									$after_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_movement AS $info_after_stock_movement)
									{
										$stock = ($info_after_stock_movement['method'] == 1) ? $stock+$info_after_stock_movement['qty'] : $stock-$info_after_stock_movement['qty'];
										$this->crud->update('stock_movement', ['stock' => $stock], ['id' => $info_after_stock_movement['id']]);
									}	
								}												
								// RECALCULATE BUYRPICE AND HPP								
								// Find the last purchase
								$sub_where_last_purchase_invoice = [										
									'type'    => 1,
									'date <=' => $from_date,
									'product_code' => $info['product_code'],
									'deleted' => 0
								];
								$sub_last_purchase_invoice = $this->db->select('*')->from('stock_movement')->where($sub_where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('transaction_id', 'DESC')->get_compiled_select();
								$where_last_purchase_invoice = [
									'transaction_id <' => $purchase_invoice['id']
								];
								$last_purchase_invoice = $this->db->select('*')->from("($sub_last_purchase_invoice) as result")->where($where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('result.id', 'DESC')->limit(1)->get()->row_array();
								// echo json_encode($after_stock_movement); die;
								// CALCULATE THE NEW HPP
								if($last_purchase_invoice == null)
								{
									$hpp = [
										'price' => $price_convert,
										'hpp'   => $price_convert
									];
									$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement['id']]);
									$last_hpp = $price_convert;
								}
								else
								{
									// (QTY STOCK YANG ADA * HPP) + (qty baru * harga baru) / (qty lama + qty baru)
									$old_inventory_value = $last_stock_movement['stock']*$last_purchase_invoice['hpp'];
									$new_inventory_Value = $qty_convert*$price_convert;
									$new_stock = $last_stock_movement['stock']+$qty_convert;
									$total_new_stock = ($new_stock > 0) ? $new_stock : 1;
									$hpp = [
										'price' => $price_convert,
										'hpp'   => ($old_inventory_value+$new_inventory_Value)/$total_new_stock
									];
									$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement['id']]);
									$last_hpp = $hpp['hpp'];
								}	
								// CHANGE ALL HPP AFTER STOCK MOVEMENT
								if($after_stock_movement == null)
								{
									$update_detail_product = array(
										'hpp' => $last_hpp,
										'supplier_code' => $post['supplier_code'],
										'buyprice' => $price_convert
									);
									$this->crud->update('product', $update_detail_product, ['code' => $info['product_code']]);																								
								}
								else
								{
									foreach($after_stock_movement  AS $info_after_stock_movement)
									{
										switch ($info_after_stock_movement['type']) {
											case 1: // PURCHASE _INVOICE
												if($info_after_stock_movement['date'] == $date && $info_after_stock_movement['transaction_id'] <= $purchase_invoice['id'] )
												{
													break;											
												}												
												else
												{
													// (QTY STOCK YANG ADA * HARGA BELI TERAKHIR) + (qty baru * harga baru) / (qty lama + qty baru)
													$old_inventory_value = $last_hpp*($info_after_stock_movement['stock']-$info_after_stock_movement['qty']+$qty_convert);
													$new_inventory_Value = $info_after_stock_movement['qty']*$info_after_stock_movement['price'];													
													$update_detail_product = array(
														'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert),
														'supplier_code' => $post['supplier_code'],
														'buyprice' => $info_after_stock_movement['price']
													);
													$this->crud->update('product', $update_detail_product, ['id' => $info_after_stock_movement['product_id']]);
													$hpp=[
														'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert)
													];
													$this->crud->update('stock_movement', $hpp, ['id' => $info_after_stock_movement['id']]);
													$last_hpp = $hpp['hpp'];												
												}
												break;
											case 2: // PURCHASE RETURN
												break;
											case 4: // SALES INVOICE
												$where_sales_invoice_detail=[
													'sales_invoice_id' => $info_after_stock_movement['transaction_id'],
													'product_id'	   => $info_after_stock_movement['product_id']
												];
												$this->crud->update('sales_invoice_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
												$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
												// RECALCULATE TOTAL HPP IN SALES INVOICE
												$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $info_after_stock_movement['transaction_id']])->result_array();
												$total_hpp_sales_invoice=0;
												foreach($sales_invoice_detail AS $info_sales_invoice_detail)
												{						
													$total_hpp_sales_invoice=$total_hpp_sales_invoice+($info_sales_invoice_detail['qty']*$info_sales_invoice_detail['unit_value']*$info_sales_invoice_detail['hpp']);
												}
												$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp_sales_invoice], ['id' => $info_after_stock_movement['transaction_id']]);
												$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
												break;
											case 5: // SALES RETURN
												$where_sales_return_detail=[
													'sales_return_id' => $info_after_stock_movement['transaction_id'],
													'product_id'	   => $info_after_stock_movement['product_id']
												];
												$this->crud->update('sales_return_detail', ['hpp' => $last_hpp], $where_sales_return_detail);
												$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
												break;
											case 7: // REPACKING
												break;
											case 8: // ADJUSMENT STOCK
												break;
											case 9: // MUTATION
												break;
											case 10: // PRODUCT USAGE
												break;
											default:
												break;
										}
									}	
								}
								$res = 1;
							}
							else // IF NEW PRODUCT
							{
								$product_id = $this->crud->get_product_id($info['product_code']);
								$qty	 = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);								
								$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
								$data_purchase_detail= [
									'purchase_invoice_id' => $purchase_invoice['id'],
									'invoice'		=> $purchase_invoice['code'],
									'product_id'	=> $product_id,
									'product_code'	=> $info['product_code'],											
									'qty'			=> $qty,
									'unit_id'		=> $info['unit_id'],
									'unit_value'    => isset($convert) ? $convert['value'] : 1,
									'warehouse_id'	=> $info['warehouse_id'],
									'price'			=> $price,
									'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
									'total'			=> $total,
									'ppn'			=> $ppn
								];
								$purchase_invoice_detail_id = $this->crud->insert_id('purchase_invoice_detail', $data_purchase_detail);
								if($purchase_invoice_detail_id != null)
								{
									// CONVERT QTY & PRICE
									$qty_convert = (isset($convert)) ? $qty*$convert['value'] : $qty;								
									if($ppn == 0)
									{
										$price_convert = ($total/$qty_convert)+($delivery_cost/$post['total_product']/$qty_convert);
									}
									elseif($ppn == 1)
									{
										if($price_include_tax == 0)
										{
											$price_convert = ($total/$qty_convert)+($delivery_cost/$post['total_product']/$qty_convert);
										}
										elseif($price_include_tax == 1)
										{
											$price_convert = ($total/1.11/$qty_convert)+($delivery_cost/$post['total_product']/$qty_convert);
										}										
									}
									// STOCK
									$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
									if($check_stock->num_rows() == 1)
									{
										$data_stock = $check_stock->row_array();
										$where_stock = array(
											'product_code'  => $info['product_code'],
											'warehouse_id'  => $info['warehouse_id']
										);
										$stock = array(                                
											'product_id' => $product_id,
											'qty'        => $data_stock['qty']+$qty_convert,
										);
										$update_stock = $this->crud->update('stock', $stock, $where_stock);
									}
									else
									{
										$stock = array(                                
											'product_id'    => $product_id,
											'product_code'  => $info['product_code'],                                                        
											'qty'           => $qty_convert,
											'warehouse_id'  => $info['warehouse_id']
										);
										$update_stock = $this->crud->insert('stock', $stock);
									}                            
									if($update_stock)
									{
										// STOCK CARD
										$where_last_stock_card = [
											'date <='      => format_date($post['date']),
											'product_id'   => $product_id,																						
											'warehouse_id' => $info['warehouse_id'],
											'deleted'      => 0											
										];
										$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
										$data_stock_card = array(
											'type'            => 1,
											'information'     => 'PEMBELIAN',
											'note'			  => $supplier['name'],
											'date'            => format_date($post['date']),
											'transaction_id'  => $purchase_invoice['id'],
											'invoice'         => $purchase_invoice['code'],
											'transaction_detail_id' => $purchase_invoice_detail_id,
											'product_id'      => $product_id,
											'product_code'    => $info['product_code'],
											'qty'             => $qty_convert,																						
											'method'          => 1,
											'stock'           => $last_stock_card['stock']+$qty_convert,
											'warehouse_id'    => $info['warehouse_id'],
											'employee_code'   => $this->session->userdata('code_e')
										);
										$this->crud->insert('stock_card',$data_stock_card);
										$where_after_stock_card = [
											'date >'       => format_date($post['date']),
											'product_id'   => $product_id,				
											'warehouse_id' => $info['warehouse_id'],
											'deleted'      => 0
										];                    
										$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
										foreach($after_stock_card  AS $info_after_stock_card)
										{
											$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']+$qty_convert], ['id' => $info_after_stock_card['id']]);
										}
										// STOCK MOVEMENT
										$where_last_stock_movement = [
											'product_id'   => $product_id,
											'date <='      => format_date($post['date']),
											'deleted'      => 0
										];
										$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
										$data_stock_movement = [
											'type'            => 1, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
											'information'     => 'PEMBELIAN',
											'note'			  => $supplier['name'],
											'date'            => format_date($post['date']),
											'transaction_id'  => $purchase_invoice['id'],
											'invoice'         => $purchase_invoice['code'],
											'transaction_detail_id' => $purchase_invoice_detail_id,
											'product_id'      => $product_id,
											'product_code'    => $info['product_code'],
											'qty'             => $qty_convert,
											'method'          => 1, // 1:In, 2:Out
											'stock'           => $last_stock_movement['stock']+$qty_convert,
											'employee_code'   => $this->session->userdata('code_e')
										];
										$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
										$where_after_stock_movement = [
											'product_id'   => $product_id,
											'date >'       => format_date($post['date']),
											'deleted'      => 0
										];                    
										$after_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
										foreach($after_stock_movement  AS $info_after_stock_movement)
										{
											$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']+$qty_convert], ['id' => $info_after_stock_movement['id']]);
										}
										// LAST BUYPRICE AND HPP
										// CHANGE HPP BY STOCK MOVEMENT
										// Find the last purchase
										$sub_where_last_purchase_invoice = [										
											'type'    => 1,
											'date <=' => format_date($post['date']),
											'product_code' => $info['product_code'],
											'deleted' => 0
										];
										$sub_last_purchase_invoice = $this->db->select('*')->from('stock_movement')->where($sub_where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('transaction_id', 'DESC')->get_compiled_select();
										$where_last_purchase_invoice = [
											'transaction_id <' => $purchase_invoice['id']
										];
										$last_purchase_invoice = $this->db->select('*')->from("($sub_last_purchase_invoice) as result")->where($where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('result.id', 'DESC')->limit(1)->get()->row_array();
										// CALCULATE THE NEW HPP
										if($last_purchase_invoice == null)
										{
											$hpp=[
												'price' => $price_convert,
												'hpp'   => $price_convert
											];
											$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement_id]);
											$last_hpp = $price_convert;
										}
										else
										{
											// (QTY STOCK YANG ADA * HPP) + (qty baru * harga baru) / (qty lama + qty baru)
											$old_inventory_value = $last_stock_movement['stock']*$last_purchase_invoice['hpp'];
											$new_inventory_Value = $qty_convert*$price_convert;
											$new_stock = $last_stock_movement['stock']+$qty_convert;
											$total_new_stock = ($new_stock > 0) ? $new_stock : 1;
											$hpp = [
												'price' => $price_convert,
												'hpp'   => ($old_inventory_value+$new_inventory_Value)/$total_new_stock
											];
											$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement_id]);
											$last_hpp = $hpp['hpp'];
										}	
										// CHANGE ALL HPP AFTER STOCK MOVEMENT
										if($after_stock_movement == null)
										{
											$update_detail_product = array(
												'hpp' => $last_hpp,
												'supplier_code' => $post['supplier_code'],
												'buyprice' => $price_convert
											);
											$this->crud->update('product', $update_detail_product, ['code' => $info['product_code']]);																								
										}
										else
										{
											foreach($after_stock_movement  AS $info_after_stock_movement)
											{
												switch ($info_after_stock_movement['type']) {
													case 1: // PURCHASE _INVOICE
														// (QTY STOCK YANG ADA * HARGA BELI TERAKHIR) + (qty baru * harga baru) / (qty lama + qty baru)
														$old_inventory_value = $last_hpp*($info_after_stock_movement['stock']-$info_after_stock_movement['qty']+$qty_convert);
														$new_inventory_Value = $info_after_stock_movement['qty']*$info_after_stock_movement['price'];													
														$update_detail_product = array(
															'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert),
															'supplier_code' => $post['supplier_code'],
															'buyprice' => $info_after_stock_movement['price']
														);
														$this->crud->update('product', $update_detail_product, ['id' => $info_after_stock_movement['product_id']]);
														$hpp=[
															'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert)
														];
														$this->crud->update('stock_movement', $hpp, ['id' => $info_after_stock_movement['id']]);
														$last_hpp = $hpp['hpp'];
														break;
													case 2: // PURCHASE RETURN
														break;
													case 4: // SALES INVOICE
														$where_sales_invoice_detail=[
															'sales_invoice_id' => $info_after_stock_movement['transaction_id'],
															'product_id'	   => $info_after_stock_movement['product_id']
														];
														$this->crud->update('sales_invoice_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
														$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
														// RECALCULATE TOTAL HPP IN SALES INVOICE
														$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $info_after_stock_movement['transaction_id']])->result_array();
														$total_hpp_sales_invoice=0;
														foreach($sales_invoice_detail AS $info_sales_invoice_detail)
														{						
															$total_hpp_sales_invoice=$total_hpp_sales_invoice+($info_sales_invoice_detail['qty']*$info_sales_invoice_detail['unit_value']*$info_sales_invoice_detail['hpp']);
														}
														$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp_sales_invoice], ['id' => $info_after_stock_movement['transaction_id']]);
														$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
														break;
													case 5: // SALES RETURN
														$where_sales_return_detail=[
															'sales_return_id' => $info_after_stock_movement['transaction_id'],
															'product_id'	   => $info_after_stock_movement['product_id']
														];
														$this->crud->update('sales_return_detail', ['hpp' => $last_hpp], $where_sales_return_detail);
														$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
														break;
													case 7: // REPACKING
														break;
													case 8: // ADJUSMENT STOCK
														break;
													case 9: // MUTATION
														break;
													case 10: // PRODUCT USAGE
														break;
													default:
														break;
												}
											}	
										}
										$res = 1;
									}
									else
									{
										break;
									}
								}
								else
								{						
									break;
								}								
							}					
						}
					}
					$data_activity = [
						'information' => 'MEMPERBARUI PEMBELIAN (NO. TRANSAKSI '.$purchase_invoice['code'].')',
						'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);
				}
				else
				{
					$res = 0;
				}
				$this->db->trans_complete();
				if($res == 1 && $this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					// RECALCULATE GENERAL LEDGER				
					$to_date  = date('Y-m-d');					
					$where_coa_account_code = ["10301","10601", "20101"];														
					$coa_accounts = $this->db->select('*')->from('coa_account')->where_in('code', $where_coa_account_code)->get()->result_array();
					foreach($coa_accounts AS $coa_account)
					{
						$where_last_balance = [
							'coa_account_code' => $coa_account['code'],
							'date <='          => $from_date,
							'deleted'          => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  $last_balance['balance'] : 0;
						$where_general_ledgers = [
							'coa_account_code' => $coa_account['code'],
							'date >'           => $from_date,
							'date <='          => $to_date
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
					$this->session->set_flashdata('success', 'Transaksi Pembelian berhasil diperbarui');
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Transaksi Pembelian gagal diperbarui');
				}
				redirect(site_url('purchase/invoice/detail/'.encrypt_custom($purchase_invoice['id'])));
			}				
		}
		else
		{		
			//$this->session->userdata('verifypassword') == 1	
			if(true)
			{			
				$this->session->unset_userdata('verifypassword');
				$purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_invoice_id));
				$payment_ledger = $this->db->select('sum(payment_ledger_transaction.amount) AS grandtotal')
										   ->from('payment_ledger')
										   ->join('payment_ledger_transaction', 'payment_ledger.id = payment_ledger_transaction.pl_id')
										   ->where('payment_ledger.transaction_type', 1)
										   ->where('payment_ledger_transaction.transaction_id', $purchase_invoice['id'])
										   ->get()->row_array();
				if($payment_ledger['grandtotal'] == 0)
				{
					$header = array("title" => "Perbarui Pembelian");
					$footer = array("script" => ['transaction/purchase/invoice/update_purchase_invoice.js']);
					$data = array(
						'purchase_invoice' => $purchase_invoice,
						'purchase_invoice_detail' => $this->purchase->get_detail_purchase_invoice_detail($purchase_invoice['id']),
						'payment_ledger' => $payment_ledger
					);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('purchase/invoice/update_purchase_invoice', $data);        
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$this->session->set_flashdata('error', 'Mohon maaf, pembelian tidak dapat diperbarui. Sudah terdapat data Pembayaran');
					redirect(urldecode($this->agent->referrer()));
				}				
			}
			else
			{
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
				redirect(urldecode($this->agent->referrer()));
			}						
		}
	}

	public function delete_purchase_invoice()
  	{
		//MASIH ADA BUG, DIPERHITUNGAN HPP
		if($this->input->is_ajax_request())
		{
			if($this->session->userdata('verifypassword') == 1)			
			{
				$this->session->unset_userdata('verifypassword');
				$post = $this->input->post();				
				$purchase_invoice 	     = $this->purchase->get_detail_purchase_invoice($post['purchase_invoice_id']);
				$purchase_invoice_detail = $this->purchase->get_detail_purchase_invoice_detail($purchase_invoice['id']);
				$payment_ledger = $this->db->select('plt.id')->from('payment_ledger AS pl')
											->join('payment_ledger_transaction AS plt', 'plt.pl_id = pl.id')
											->where('pl.transaction_type', 1)
											->where('plt.transaction_id', $purchase_invoice['id'])
											->group_by('pl.id')->get()->num_rows();
				if($payment_ledger != 0)
				{
					$this->session->set_flashdata('error', 'Pembelian tidak dapat dihapus, terdapat data pembayaran');
					$response   =   [
						'status'    => [
							'code'      => 400,
							'message'   => 'Berhasil',
						],
						'response'  => ''
					];
				}	
				else
				{					
					$this->db->trans_start();
					// GENERAL LEDGER
					$where_general_ledger = [
						'invoice'		=> $purchase_invoice['code']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger);
					if($general_ledger->num_rows() > 0)
					{
						foreach($general_ledger->result_array() AS $info_general_ledger)
						{
							$where_after_balance = [
								'coa_account_code'=> $info_general_ledger['coa_account_code'],
								'date >='    => $info_general_ledger['date'],
								'deleted'    => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance AS $info_after_balance)
							{
								if($info_after_balance['date'] == $info_general_ledger['date'] && $info_after_balance['id'] < $info_general_ledger['id'])
								{
									continue;
								}
								else
								{
									$coa_category = substr($info_general_ledger['coa_account_code'], 0, 1);
									if(in_array($coa_category, [1]))
									{
										if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
										{
											$balance = $info_after_balance['balance']-$info_general_ledger['debit'];
										}
										elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
										{
											$balance = $info_after_balance['balance']+$info_general_ledger['credit'];
										}
									}
									elseif(in_array($coa_category, [2]))
									{
										if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
										{
											$balance = $info_after_balance['balance']+$info_general_ledger['debit'];
										}
										elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
										{
											$balance = $info_after_balance['balance']-$info_general_ledger['credit'];
										}
									}
									$this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
								}
							}
							$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
						}
					}
					// DELETE PURCHASE INVOICE DETAIL
					foreach($purchase_invoice_detail AS $info_purchase_invoice_detail)
					{
						$res = 0;
						$qty_convert = $info_purchase_invoice_detail['qty']*$info_purchase_invoice_detail['unit_value'];
						// MINUS STOCK IN WAREHOUSE
						$where_stock = [
							'product_code'	=> $info_purchase_invoice_detail['product_code'],
							'warehouse_id'	=> $info_purchase_invoice_detail['warehouse_id']
						];
						$stock = $this->crud->get_where('stock', $where_stock)->row_array();
						$update_stock = [
							'qty' => $stock['qty']-($info_purchase_invoice_detail['qty']*$info_purchase_invoice_detail['unit_value'])
						];
						$this->crud->update('stock', $update_stock, $where_stock);
						// UPDATE AFTER STOCK CARD AND DELETE OLD STOCK CARD
						$where_stock_card = [
							'transaction_id' => $purchase_invoice['id'],
							'product_code'	 => $info_purchase_invoice_detail['product_code'],
							'type'			 => 1,
							'method'		 => 1,
							'warehouse_id'	 => $info_purchase_invoice_detail['warehouse_id']
						];
						$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();				
						$where_after_stock_card = [
							'date >='       => $stock_card['date'],
							'product_code'	=> $info_purchase_invoice_detail['product_code'],
							'warehouse_id'	=> $info_purchase_invoice_detail['warehouse_id'],
							'deleted'		=> 0
						];
						$after_stock_card = $this->db->select('id, date, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('stock_card.id', 'ASC')->get()->result_array();
						foreach($after_stock_card AS $info_after_stock_card)
						{
							if($info_after_stock_card['date'] == $stock_card['date'] && $info_after_stock_card['id'] <= $stock_card['id'])
							{
								continue;
							}
							else
							{
								$update_stock_card = [
									'stock' => $info_after_stock_card['stock']-($info_purchase_invoice_detail['qty']*$info_purchase_invoice_detail['unit_value'])
								];
								$this->crud->update('stock_card', $update_stock_card, ['id' => $info_after_stock_card['id']]);
							}					
						}
						$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
						// UPDATE AFTER STOCK MOVEMENT AND DELETE OLD STOCK movement
						$where_stock_movement = [
							'transaction_id' => $purchase_invoice['id'],
							'product_code'	 => $info_purchase_invoice_detail['product_code'],
							'type'			 => 1,
							'method'		 => 1,
						];
						$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
						// $sub_where_after_stock_movement = [
						// 	'date >='       => $stock_movement['date'],
						// 	'product_code'	=> $info_purchase_invoice_detail['product_code'],
						// 	'deleted'		=> 0
						// ];
						// $sub_after_stock_movement = $this->db->select('*')->from('stock_movement')->where($sub_where_after_stock_movement)->order_by('date', 'ASC')->order_by('stock_movement.id', 'ASC')->get_compiled_select();
						// $where_after_stock_movement = [
						// 	'date >=' => $stock_movement['date']
						// ];
						// $after_stock_movement = $this->db->select('*')->from("($sub_after_stock_movement) as result")->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('result.id', 'ASC')->get()->result_array();						
						$where_after_stock_movement = [
							'date >='       => $stock_movement['date'],
							'product_code'	=> $info_purchase_invoice_detail['product_code'],
							'deleted'		=> 0
						];
						$after_stock_movement = $this->db->select('*')->from("stock_movement")->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('stock_movement.id', 'ASC')->get()->result_array();
						foreach($after_stock_movement AS $info_after_stock_movement)
						{
							if($info_after_stock_movement['date'] == $stock_movement['date'] && $info_after_stock_movement['id'] <= $stock_movement['id'])
							{
								continue;
							}
							else
							{
								$update_stock_movement = [
									'stock' => $info_after_stock_movement['stock']-($info_purchase_invoice_detail['qty']*$info_purchase_invoice_detail['unit_value'])
								];
								$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_after_stock_movement['id']]);
							}
						}
						$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);			
						// CHANGE HPP BY STOCK MOVEMENT
						// Find the last purchase
						$sub_where_last_purchase_invoice = [										
							'type'    => 1,
							'date <=' => $purchase_invoice['date'],
							'product_code' => $info_purchase_invoice_detail['product_code'],
							'deleted' => 0
						];
						$sub_last_purchase_invoice = $this->db->select('*')->from('stock_movement')->where($sub_where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('transaction_id', 'DESC')->get_compiled_select();
						$where_last_purchase_invoice = [
							'transaction_id <' => $purchase_invoice['id']
						];
						$last_purchase_invoice = $this->db->select('*')->from("($sub_last_purchase_invoice) as result")->where($where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('result.id', 'DESC')->limit(1)->get()->row_array();						
						$last_hpp = ($last_purchase_invoice == null) ? 0 : $last_purchase_invoice['hpp'];
						if($after_stock_movement == null)
						{					
							$supplier = $this->crud->get_where('supplier', ['name' => $last_purchase_invoice['note']])->row_array();
							$update_detail_product = array(
								'hpp' => $last_hpp,
								'supplier_code' => $supplier['code'],
								'buyprice' => $last_purchase_invoice['price']
							);
							$this->crud->update('product', $update_detail_product, ['code' => $info_purchase_invoice_detail['product_code']]);																								
						}
						else
						{
							foreach($after_stock_movement  AS $info_after_stock_movement)
							{
								if($info_after_stock_movement['date'] == $stock_movement['date'] && $info_after_stock_movement['id'] <= $stock_movement['id'])
								{
									continue;
								}
								else
								{							
									switch ($info_after_stock_movement['type']) {
										case 1:
											// (QTY STOCK YANG ADA * HARGA BELI TERAKHIR) + (qty baru * harga baru) / (qty lama + qty baru)
											$old_stock = $info_after_stock_movement['stock']-$info_after_stock_movement['qty']-$stock_movement['qty'];
											$old_inventory_value = $last_hpp*($info_after_stock_movement['stock']-$info_after_stock_movement['qty']-$stock_movement['qty']);
											$new_inventory_Value = $info_after_stock_movement['qty']*$info_after_stock_movement['price'];																		
											$hpp=[
												'hpp' => ($old_inventory_value+$new_inventory_Value)/($old_stock+$info_after_stock_movement['qty'])
											];
											$this->crud->update('stock_movement', $hpp, ['id' => $info_after_stock_movement['id']]);
											$last_hpp = $hpp['hpp'];
											// PURCHASE _INVOICE
											$supplier = $this->crud->get_where('supplier', ['name' => $info_after_stock_movement['note']])->row_array();
											$update_detail_product = array(
												'hpp' => $last_hpp,
												'supplier_code' => $supplier['code'],
												'buyprice' => $info_after_stock_movement['price']
											);
											$this->crud->update('product', $update_detail_product, ['id' => $info_after_stock_movement['product_id']]);											
											break;
										case 4:
											// SALES INVOICE
											$where_sales_invoice_detail=[
												'sales_invoice_id' => $info_after_stock_movement['transaction_id'],
												'product_id'	   => $info_after_stock_movement['product_id']
											];
											$this->crud->update('sales_invoice_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
											$this->crud->update('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
											break;
										default:
											break;
									}
								}						
							}	
						}
						// DELETE PURCHASE DETAIL ID
						$where_purchase_detail = [
							'id'	=> $info_purchase_invoice_detail['id']
						];
						$this->crud->delete('purchase_invoice_detail', $where_purchase_detail);
						$res = 1;
					}			
					// DELETE PURCHASE INVOICE
					$this->crud->delete('purchase_invoice', ['id' => $purchase_invoice['id']]);
					$this->db->trans_complete();
					if($res = 1 && $this->db->trans_status() === TRUE)
					{
						$this->db->trans_commit();
						$data_activity = [
							'information' => 'MENGHAPUS PEMBELIAN (NO. TRANSAKSI '.$purchase_invoice['code'].')',
							'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];
						$this->crud->insert('activity', $data_activity);
						$this->session->set_flashdata('success', 'BERHASIL! Pembelian Terhapus');
						$response   =   [
							'status'    => [
								'code'      => 200,
								'message'   => 'Berhasil',
							],
							'response'  => ''
						];
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Mohon Maaf! Pembelian gagal terhapus');
						$response   =   [
							'status'    => [
								'code'      => 400,
								'message'   => 'Gagal',
							],
							'response'  => ''
						];
					}
				}				
				echo json_encode($response);
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
				echo json_encode($response);
			}			
		}								
	}

	public function print_purchase_invoice($purchase_id)
	{
		if($this->system->check_access('purchase/invoice', 'C'))
		{
			$purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_id));
			$data_activity = [
				'information' => 'MENCETAK PEMBELIAN (NO. TRANSAKSI '.$purchase_invoice['code'].')',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);			
			$data = array(
				'perusahaan'      		  => $this->global->company(),
				'purchase_invoice'        => $purchase_invoice,
				'purchase_invoice_detail' => $this->purchase->get_detail_purchase_invoice_detail($purchase_invoice['id'])
			);			
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 139.7],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 22,
				'margin_bottom' => 35,
				'margin_header' => 5,
				'margin_footer' => 5,
				'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-weight: bold; font-size:14px;">
					PEMBELIAN | No.Transaksi '.$purchase_invoice['code'].'
				</div>
				<table style="width:100%; font-size:14px;">
					<tbody>
						<tr>
							<td>Tgl. Transaksi</td>
							<td>: '.date('d-m-Y', strtotime($purchase_invoice['date'])).'</td>
							<td>Supplier</td>
							<td>: '.$purchase_invoice['name_s'].'</td>
						</tr>
						<tr>
							<td>No. Refrensi</td>
							<td>: '.$purchase_invoice['invoice'].'</td>
							<td>Alamat</td>
							<td>: '.$purchase_invoice['address_s'].'</td>
						</tr>
					</tbody>
				</table>
			');
			$mpdf->SetHTMLFooter('
				<table width="100%">
					<tr>
						<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
						<td align="center">{PAGENO}/{nbpg}</td>
					</tr>
				</table>'
			);
			$data = $this->load->view('purchase/invoice/print_purchase_invoice', $data, true);;
			$mpdf->SetJS('this.print();');
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}	
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/invoice'));
		} 
	}	

	// PURCHASE RETURN
    public function purchase_return()
	{
		if($this->system->check_access('purchase/return', 'A'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('purchase_return.id, purchase_return.code, purchase_return.date, purchase_return.method,
								 purchase_invoice.invoice AS invoice, purchase_return.total_return, supplier.name AS name_s, purchase_return.do_status')
							     ->from('purchase_return')
							     ->join('purchase_invoice', 'purchase_invoice.id = purchase_return.purchase_invoice_id', 'left')
							     ->join('supplier', 'supplier.code = purchase_return.supplier_code');
				if($post['do_status'] == "" || $post['do_status'] != 0)
				{
					$this->datatables->where('DATE(purchase_return.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
					$this->datatables->where('DATE(purchase_return.created) <=', date('Y-m-d'));
					if($post['do_status'] == 1)
					{
						$this->datatables->where('purchase_return.do_status', $post['do_status']);						
					}										
				}
				else
				{					
					$this->datatables->where('purchase_return.do_status', $post['do_status']);
				}
				$this->datatables->where('purchase_return.deleted', 0);
				$this->datatables->group_by('purchase_return.id');				
				$this->datatables->add_column('code',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/return/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id), code');
				echo $this->datatables->generate();
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT DAFTAR RETUR PEMBELIAN',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
	
				$header = array("title" => "Retur Pembelian");
				$footer = array("script" => ['transaction/purchase/return/purchase_return.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('purchase/return/purchase_return');
				$this->load->view('include/footer', $footer);
			} 			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('dashboard'));
		}		
	}

	public function get_product_return()
	{
		$search 		= urldecode($this->uri->segment(4));
		$supplier_code  = $this->uri->segment(5);
		$ppn  			= $this->uri->segment(6);
		$data           = $this->purchase->get_product_return($search, $supplier_code, $ppn);
		$response 	    = array();
        if($data->num_rows() > 0){
			foreach($data->result_array() as $info)
			{
				$response[] = array(
					'barcode' => $info['barcode'],
					'code' => $info['code'],
					'name' => $info['name'],
				);
			}            
		}
        echo json_encode($response);
	}

	public function get_buyprice_return()
	{
		$post = $this->input->post();
		$product_code	= $post['product_code'];
		$unit_id		= $post['unit_id'];
		$supplier_code	= $post['supplier_code'];
		$result = array(
			'buyprice' => $this->purchase->get_buyprice_return($product_code, $unit_id, $supplier_code)
		);
		echo json_encode($result);
	}    
	
	public function get_warehouse_return()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$warehouse = $this->purchase->get_warehouse($post['product_code'], $post['unit_id']);
			$option = null;
			foreach($warehouse as $data)
			{
				if($data['stock'] > 0)
				{
					if($data['default']==1)
					{
						$option .= "<option value='".$data['id_w']."' selected>".$data['code_w']." | ".$data['stock']."</option>";
					}
					else
					{
						$option .= "<option value='".$data['id_w']."'>".$data['code_w']." | ".$data['stock']."</option>";
					}
				}	
				else
				{
					continue;
				}			
			}		
			$result = array
			(
				'option'=>$option
			);
			echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	public function get_invoice_return($supplier_code)
	{	
		if($this->input->is_ajax_request())
		{
			$data          = $this->purchase->get_invoice_return($supplier_code)->result();
			if($data)
			{
				$response   =   [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data,
				];
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => '',
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function get_account_payable()
	{
		$purchase_id = $this->input->post('id');
		$result = array(
			'account_payable' => $this->purchase->get_account_payable($purchase_id)
		);
		echo json_encode($result);
	}
	
	public function create_purchase_return()
	{
		if($this->system->check_access('purchase/return', 'C'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->form_validation->set_rules('date', 'Tanggal Retur Pembelian', 'trim|required|xss_clean');
				$this->form_validation->set_rules('supplier_code', 'Supplier', 'trim|required|xss_clean');
				$this->form_validation->set_rules('method', 'Jenis Retur', 'trim|xss_clean');
				$this->form_validation->set_rules('product[]', 'Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_product', 'Identitas Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Deskripsi Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('total_return', 'Stok Minimal', 'trim|required|xss_clean');
				if($post['method'] == 2)
				{
					$this->form_validation->set_rules('purchase_invoice_id', 'Faktur', 'trim|required|xss_clean');
					$this->form_validation->set_rules('account_payable', 'Nilai Faktur', 'trim|required|xss_clean');
					$this->form_validation->set_rules('grandtotal', 'Sisa Faktur', 'trim|required|xss_clean');    	
				}        
				$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');								
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Retur Pembelian Baru");
					$footer = array("script" => ['transaction/purchase/return/create_purchase_return.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('purchase/return/create_purchase_return');
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$this->db->trans_start();
					$code             = $this->purchase->purchase_return_code();
					$supplier	      = $this->crud->get_where('supplier', ['code' => $post['supplier_code']])->row_array();
					$ppn			  = (!isset($post['ppn'])) ?  0 : $post['ppn'];
					$total_return 	  = format_amount($post['total_return']);
					$account_payable  = format_amount($post['account_payable']);
					$grandtotal 	  = format_amount($post['grandtotal']);
					$data_purchase_return = [
						'date' 				=> format_date($post['date']),
						'code'				=> $code,				
						'employee_code'		=> $this->session->userdata('code_e'),
						'supplier_code'		=> $post['supplier_code'],						
						'method' 			=> $post['method'],
						'cl_type' 			=> isset($post['from_cl_type']) ? $post['from_cl_type'] : null,
						'account_id' 		=> isset($post['from_account_id']) ? $post['from_account_id']: null,
						'total_product' 	=> $post['total_product'],
						'total_qty' 		=> $post['total_qty'],
						'total_return'		=> $total_return,
						'purchase_invoice_id' => ($post['method'] == 2) ? $post['purchase_invoice_id'] : null,
						'account_payable' 	=> ($post['method'] == 2) ? $account_payable : null,
						'grandtotal' 		=> ($post['method'] == 2) ? $grandtotal : null,
						'ppn'				=> $ppn
					];
					$purchase_return_id = $this->crud->insert_id('purchase_return', $data_purchase_return);			
					if($purchase_return_id)
					{										
						foreach($post['product'] AS $info)
						{
							$product_id = $this->crud->get_product_id($info['product_code']);
							$qty = format_amount($info['qty']);
							$where_unit = array(
								'product_code' => $info['product_code'],
								'unit_id' 	   => $info['unit_id'],
								'deleted'	   => 0
							);																		
							$convert = $this->crud->get_where('product_unit', $where_unit)->row_array();
							$data_purchase_return_detail = array(
								'purchase_return_id' => $purchase_return_id,
								'product_id'		 => $product_id,
								'product_code'		 => $info['product_code'],
								'unit_id'		 	 => $info['unit_id'],
								'unit_value'		 => ($convert['value'] != null) ? $convert['value'] : 1,
								'warehouse_id'		 => $info['warehouse_id'],
								'qty'		 		 => $qty,
								'price'		 		 => format_amount($info['price']),
								'total'		 		 => format_amount($info['total']),
								'ppn'				 => $ppn,
								'information'		 => $info['information']
							);							
							if($this->crud->insert('purchase_return_detail', $data_purchase_return_detail))
							{								
								continue;
							}
							else
							{
								break;
							}
						}
						$data_activity = [
							'information' => 'MEMBUAT RETUR PEMBELIAN (NO. TRANSAKSI '.$code.')',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];						
						$this->crud->insert('activity', $data_activity);												
					}
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'Transaksi Retur Pembelian berhasil ditambahkan');
					redirect(site_url('purchase/return/detail/'.encrypt_custom($purchase_return_id)));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Transaksi Pembelian gagal ditambahkan');
					redirect(site_url('purchase/return'));
				}				
			}
			else
			{
				$header = array("title" => "Retur Pembelian Baru");
				$footer = array("script" => ['transaction/purchase/return/create_purchase_return.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('purchase/return/create_purchase_return');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/return'));
		}		
	}

	public function create_purchase_return_do()
    {
		if($this->system->check_access('purchase/return/do', 'C'))
		{
			if($this->input->is_ajax_request())
			{				
				$post = $this->input->post();
				$this->db->trans_start();
				$purchase_return = $this->purchase->get_detail_purchase_return($post['purchase_return_id']);
				if($purchase_return['do_status'] == 0)
				{
					$purchase_return_detail = $this->purchase->get_detail_purchase_return_detail($purchase_return['id']);
					$supplier = $this->crud->get_where('supplier', ['code' => $purchase_return['supplier_code']])->row_array();
					$check_stock_purchase_return_do = $this->purchase->check_stock_purchase_return_do($purchase_return);
					if($check_stock_purchase_return_do['min_stock'] == 0)
					{
						if($purchase_return['method'] == 1)
						{
							// GENERAL LEDGER -> KAS (D)
							$where_last_balance = [
								'coa_account_code' => "10101",
								'date <='          => $purchase_return['date'],                    
								'deleted'          => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $purchase_return['total_return']) : add_balance(0, $purchase_return['total_return']);
							$data = [
								'date'              => $purchase_return['date'],
								'coa_account_code'  => "10101",
								'transaction_id'    => $purchase_return['id'],
								'invoice'           => $purchase_return['code'],
								'information'       => 'RETUR PEMBELIAN',
								'note'		        => 'RETUR_PEMBELIAN_'.$purchase_return['code'].'_'.$supplier['name'],
								'debit'             => $purchase_return['total_return'],
								'balance'     		=> $balance
							];									
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "10101",
									'date >'        => $purchase_return['date'],
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $purchase_return['total_return'])], ['id' => $info['id']]);
								}
							}
							// GENERAL LEDGER -> PERSEDIAAN BARANG (K)
							$where_last_balance = [
								'coa_account_code' => "10301",
								'date <='          => $purchase_return['date'],                    
								'deleted'          => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $purchase_return['total_return']) : sub_balance(0, $purchase_return['total_return']);
							$data = [
								'date'              => $purchase_return['date'],
								'coa_account_code'  => "10301",
								'transaction_id'    => $purchase_return['id'],
								'invoice'           => $purchase_return['code'],
								'information'       => 'RETUR PEMBELIAN',
								'note'		        => 'RETUR_PEMBELIAN_'.$purchase_return['code'].'_'.$supplier['name'],
								'credit'            => $purchase_return['total_return'],
								'balance'     		=> $balance
							];									
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "10301",
									'date >'        => $purchase_return['date'],
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $purchase_return['total_return'])], ['id' => $info['id']]);
								}
							}
							// CASH LEDGER
							$from_where_last_balance = [
								'cl_type'    => $purchase_return['cl_type'],
								'account_id' => $purchase_return['account_id'],
								'date <='    => $purchase_return['date'],                    
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$purchase_return['total_return'] : 0+$purchase_return['total_return'];
							$data = [
								'cl_type'     => $purchase_return['cl_type'],
								'information' => 'RETUR PEMBELIAN',
								'transaction_id'  => $purchase_return['id'],
								'account_id'  => $purchase_return['account_id'],
								'date'        => $purchase_return['date'],
								'invoice'     => $purchase_return['code'],						
								'note'		  => 'RETUR_PEMBELIAN_'.$purchase_return['code'].'_'.$supplier['name'],
								'amount'      => $purchase_return['total_return'],
								'method'      => 1,
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'       => $purchase_return['cl_type'],
									'account_id'    => $purchase_return['account_id'],
									'date >'        => $purchase_return['date'],
									'deleted'       => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{                        
									$balance = $info['balance'] + $purchase_return['total_return'];
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}                            
							}
						}
						else
						{
							// GENERAL LEDGER -> HUTANG USAHA (D)
							$where_last_balance = [
								'coa_account_code' => "20101",
								'date <='          => $purchase_return['date'],                    
								'deleted'          => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $purchase_return['total_return']) : sub_balance(0, $purchase_return['total_return']);
							$data = [
								'date'              => $purchase_return['date'],
								'coa_account_code'  => "20101",
								'transaction_id'    => $purchase_return['id'],
								'invoice'           => $purchase_return['code'],
								'information'       => 'RETUR PEMBELIAN',
								'note'		        => 'RETUR_PEMBELIAN_'.$purchase_return['code'].'_'.$supplier['name'],
								'debit'             => $purchase_return['total_return'],
								'balance'     		=> $balance
							];									
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "20101",
									'date >'        => $purchase_return['date'],
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $purchase_return['total_return'])], ['id' => $info['id']]);
								}
							}
							// GENERAL LEDGER -> PERSEDIAAN BARANG (K)
							$where_last_balance = [
								'coa_account_code' => "10301",
								'date <='          => $purchase_return['date'],                    
								'deleted'          => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $purchase_return['total_return']) : sub_balance(0, $purchase_return['total_return']);
							$data = [
								'date'              => $purchase_return['date'],
								'coa_account_code'  => "10301",
								'transaction_id'    => $purchase_return['id'],
								'invoice'           => $purchase_return['code'],
								'information'       => 'RETUR PEMBELIAN',
								'note'		        => 'RETUR_PEMBELIAN_'.$purchase_return['code'].'_'.$supplier['name'],
								'credit'            => $purchase_return['total_return'],
								'balance'     		=> $balance
							];									
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "10301",
									'date >'        => $purchase_return['date'],
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $purchase_return['total_return'])], ['id' => $info['id']]);
								}
							}
							// ACCOUNT PAYABLE
							$old_account_payable = $this->purchase->get_account_payable($purchase_return['purchase_invoice_id']);
							$new_account_payable = $old_account_payable - $purchase_return['total_return'];					
							if($new_account_payable == 0)
							{
								$data_new_account_payable = array(
									'payment_status'  => 1,
									'account_payable' => $new_account_payable
								);
							}
							else
							{
								$data_new_account_payable = array(
									'account_payable' => $new_account_payable
								);
							}
							$this->crud->update_by_id('purchase_invoice', $data_new_account_payable, $purchase_return['purchase_invoice_id']);
						}
						// PURCHASE RETURN DETAIL
						foreach($purchase_return_detail AS $info)
						{							
							$res = 0; $qty_convert = $info['qty']*$info['unit_value'];
							// STOCK
							$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
							if($check_stock->num_rows() == 1)
							{	
								$stock = $check_stock->row_array();
								$qty   = $stock['qty']-$qty_convert;												
								$where_stock = array(
									'product_code'  => $info['product_code'],
									'warehouse_id'  => $info['warehouse_id']
								);       							
								$stock = array(                                
									'product_id'    => $info['product_id'],
									'qty'           => $qty,
								);
								$update_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{
								$qty = 0-$qty_convert;
								$stock = array(                                
									'product_id'    => $info['product_id'],
									'product_code'  => $info['product_code'],                                                        
									'qty'           => $qty,
									'warehouse_id'  => $info['warehouse_id']
								);
								$update_stock = $this->crud->insert('stock', $stock);
							}
							if($update_stock)
							{
								// STOCK CARD
								$where_last_stock_card = [
									'date <='      => $purchase_return['date'],
									'product_id'   => $info['product_id'],
									'warehouse_id' => $info['warehouse_id'],
									'deleted'      => 0
								];
								$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_card = array(
									'date'			  => $purchase_return['date'],
									'transaction_id'  => $purchase_return['id'],
									'invoice'         => $purchase_return['code'],
									'product_id'      => $info['product_id'],
									'product_code'    => $info['product_code'],
									'qty'             => $qty_convert,
									'information'     => 'RETUR PEMBELIAN',
									'note'			  => $supplier['name'],
									'type'            => 2,
									'method'          => 2,
									'stock'           => $last_stock_card['stock']-$qty_convert,
									'warehouse_id'    => $info['warehouse_id'],
									'user_id'         => $this->session->userdata('id_u')
								);									
								$this->crud->insert('stock_card',$data_stock_card);
								$where_after_stock_card = [
									'date >'       => $purchase_return['date'],
									'product_id'   => $info['product_id'],		
									'warehouse_id' => $info['warehouse_id'],
									'deleted'      => 0
								];                    
								$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_stock_card  AS $info_after_stock_card)
								{
									$stock = $info_after_stock_card['stock']-$qty_convert;
									$this->crud->update('stock_card', ['stock' => $stock], ['id' => $info_after_stock_card['id']]);
								}
								// STOCK MOVEMENT
								$where_last_stock_movement = [
									'product_id'   => $info['product_id'],
									'date <='      => $purchase_return['date'],
									'deleted'      => 0
								];
								$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_movement = [
									'type'            => 2,
									'information'     => 'RETUR PEMBELIAN',
									'note'			  => $supplier['name'],
									'date'            => $purchase_return['date'],
									'transaction_id'  => $purchase_return['id'],
									'invoice'         => $purchase_return['code'],
									'product_id'      => $info['product_id'],
									'product_code'    => $info['product_code'],
									'qty'             => $qty_convert,
									'method'          => 2, // 1:In, 2:Out
									'stock'           => $last_stock_movement['stock']-$qty_convert,
									'employee_code'   => $this->session->userdata('code_e')
								];
								$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
								$where_after_stock_movement = [
									'product_id'   => $info['product_id'],
									'date >'       => $purchase_return['date'],
									'deleted'      => 0
								];                    
								$after_stock_movement = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_stock_movement  AS $info_after_stock_movement)
								{
									$stock = $info_after_stock_movement['stock']-$qty_convert;
									$this->crud->update('stock_movement', ['stock' => $stock], ['id' => $info_after_stock_movement['id']]);
								}
								$res = 1;
								continue;
							}
							else
							{							
								break;
							}
						}
						$this->db->trans_complete();
						if($this->db->trans_status() === TRUE && $res == 1)
						{
							$this->db->trans_commit();
							$this->crud->update('purchase_return', ['do_status' => 1], ['id' => $purchase_return['id']]);
							$data_activity = [
								'information' => 'MEMBUAT RETUR PEMBELIAN (CETAK DO) (NO. TRANSAKSI '.$purchase_return['code'].')',
								'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];						
							$this->crud->insert('activity', $data_activity);
							$this->session->set_userdata('create_purchase_return_do', '1');
							$response   = [
								'purchase_return_id' => encrypt_custom($purchase_return['id']),
								'status'    => [
									'code'      => 200,
									'message'   => 'Berhasil',
								],
								'response'  => ''
							];
							$this->session->set_flashdata('success', 'Cetak DO Retur Pembelian Berhasil');
						}		
						else
						{
							$this->db->trans_rollback();
							$response   =   [
								'status'    => [
									'code'      => 401,
									'message'   => 'Gagal',
								],
								'response'  => ''
							];
							$this->session->set_flashdata('error', 'Cetak DO Retur Pembelian Gagal');
							
						}
					}
					else
					{
						$response   =   [
							'status'    => [
								'code'      => 401,
								'message'   => 'Gagal',
							],
							'response'  => ''
						];
						$this->session->set_flashdata('error', 'Mohon Maaf, Cetak DO Gagal karena terdapat Stok yang Kurang, harap periksa kembali');
						$this->session->set_flashdata('min_product', $check_stock_purchase_return_do['found']);

					}			
				}
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Gagal',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('error', 'Mohon Maaf, Cetak DO Gagal. DO sudah tercetak');					
				}															
				echo json_encode($response);
			}
			else
			{
				$this->load->view('auth/show_404');
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/return'));
		}		
	}

	public function cancel_purchase_return_do($purchase_return_id)
	{
		$purchase_return = $this->purchase->get_detail_purchase_return(decrypt_custom($purchase_return_id));
		if($this->session->userdata('verifypassword') == 1)
		{
			$this->session->unset_userdata('verifypassword');
			$this->db->trans_start();
			// GENERAL LEDGER
			$where_general_ledger = [
				'invoice'		=> $purchase_return['code']
			];
			$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger);
			if($general_ledger->num_rows() > 0)
			{
				foreach($general_ledger->result_array() AS $info_general_ledger)
				{
					$where_after_balance = [
						'coa_account_code'=> $info_general_ledger['coa_account_code'],
						'date >='    => $info_general_ledger['date'],
						'deleted'    => 0
					];
					$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
					foreach($after_balance AS $info_after_balance)
					{
						if($info_after_balance['date'] == $info_general_ledger['date'] && $info_after_balance['id'] < $info_general_ledger['id'])
						{
							continue;
						}
						else
						{
							$coa_category = substr($info_general_ledger['coa_account_code'], 0, 1);
							if(in_array($coa_category, [1]))
							{
								if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
								{
									$balance = $info_after_balance['balance']-$info_general_ledger['debit'];
								}
								elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
								{
									$balance = $info_after_balance['balance']+$info_general_ledger['credit'];
								}
							}
							elseif(in_array($coa_category, [2]))
							{
								if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
								{
									$balance = $info_after_balance['balance']+$info_general_ledger['debit'];
								}
								elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
								{
									$balance = $info_after_balance['balance']-$info_general_ledger['credit'];
								}
							}
							$this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
						}
					}
					$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
				}
			}
			if($purchase_return['method'] == 1)
			{
				// DELETE CASH LEDGER
				$where_cash_ledger = [
					'transaction_id'  => $purchase_return['id'],
					'invoice'		  => $purchase_return['code']
				];
				$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->result_array();
				if($cash_ledger)
				{
					foreach($cash_ledger AS $info_cash_ledger)
					{
						$where_after_balance = [
							'cl_type'    => $info_cash_ledger['cl_type'],
							'account_id' => $info_cash_ledger['account_id'],
							'date >='    => $info_cash_ledger['date'],                
							'deleted'    => 0
						];
						$data   = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
						foreach($data AS $info)
						{
							if($info['date'] == $info_cash_ledger['date'] && $info['id'] < $info_cash_ledger['id'])
							{
								continue;
							}
							else
							{
								if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
								{
									$balance = $info['balance']-$info_cash_ledger['amount'];
								}
								else
								{
									$balance = $info['balance']+$info_cash_ledger['amount'];
								}
								$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
							}
						}
						$this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
					}
				}				
			}
			else
			{
				$old_account_payable = $this->purchase->get_account_payable($purchase_return['purchase_invoice_id']);
				$new_account_payable = $old_account_payable+$purchase_return['total_return'];					
				$data_new_account_payable = array(
					'payment_status'  => 2,
					'account_payable' => $new_account_payable
				);
				$this->crud->update_by_id('purchase_invoice', $data_new_account_payable, $purchase_return['purchase_invoice_id']);
			}			
			// PURCHASE RETURN DETAIL
			$purchase_return_detail = $this->purchase->get_detail_purchase_return_detail($purchase_return['id']);
			foreach($purchase_return_detail AS $info_purchase_return)
			{
				// ADD STOCK
				$where_stock = [
					'product_code'	=> $info_purchase_return['product_code'],
					'warehouse_id'	=> $info_purchase_return['warehouse_id']
				];
				$stock = $this->crud->get_where('stock', $where_stock)->row_array();
				$update_stock = [
					'qty' => $stock['qty']+($info_purchase_return['qty']*$info_purchase_return['unit_value'])
				];
				$this->crud->update('stock', $update_stock, $where_stock);
	
				// UPDATE AND DELETE STOCK CARD
				$where_stock_card = [
					'transaction_id' => $purchase_return['id'],
					'product_code'	 => $info_purchase_return['product_code'],
					'type'			 => 2, // 1: Purchase, 2: Purchase Return, 3: POS, 4: Sales, 5: Sales Return, 6: Production, 7: Repacking, 8: Adjusment Stock, 9: Mutation
					'method'		 => 2, // 1:IN, 2:OUT
					'warehouse_id'	 => $info_purchase_return['warehouse_id']
				];
				$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();				
				$where_after_stock_card = [
					'date >='          => $stock_card['date'],
					'product_code'	=> $info_purchase_return['product_code'],
					'warehouse_id'	=> $info_purchase_return['warehouse_id'],
					'deleted'		=> 0
				];
				$after_stock_cards = $this->db->select('id, date, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.date', 'ASC')->order_by('stock_card.id', 'ASC')->get()->result_array();				
				foreach($after_stock_cards AS $info_stock_card)
				{
					if($stock_card['date'] == $info_stock_card['date'] && $stock_card['id'] > $info_stock_card['id'])
					{
						continue;
					}
					else
					{
						$update_stock_card = [
							'stock' => $info_stock_card['stock']+($info_purchase_return['qty']*$info_purchase_return['unit_value'])
						];
						$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
					}										
				}
				$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
				// UPDATE AND DELETE STOCK MOVEMENT
				$where_stock_movement = [
					'transaction_id' => $purchase_return['id'],
					'product_code'	 => $info_purchase_return['product_code'],
					'type'			 => 2, // 1: Purchase, 2: Purchase Return, 3: POS, 4: Sales, 5: Sales Return, 6: Production, 7: Repacking, 8: Adjusment Stock, 9: Mutation
					'method'		 => 2, // 1:IN, 2:OUT
				];								
				$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
				$where_after_stock_movement = [
					'date >='       => $stock_movement['date'],
					'product_code'	=> $info_purchase_return['product_code'],
					'deleted'		=> 0
				];
				$after_stock_movements = $this->db->select('id, date, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('stock_movement.date', 'ASC')->order_by('stock_movement.id', 'ASC')->get()->result_array();
				foreach($after_stock_movements AS $info_stock_movement)
				{
					if($stock_movement['date'] == $info_stock_movement['date'] && $stock_movement['id'] > $info_stock_movement['id'])
					{
						continue;
					}
					else
					{
						$update_stock_movement = [
							'stock' => $info_stock_movement['stock']+($info_purchase_return['qty']*$info_purchase_return['unit_value'])
						];
						$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
					}					
				}
				$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);
			}			
			$this->db->trans_complete();
			if($this->db->trans_status() === TRUE)
			{
				$this->db->trans_commit();
				$this->crud->update('purchase_return', ['do_status' => 0], ['id' => $purchase_return['id']]);
				$data_activity = [
					'information' => 'MEMBATALKAN RETUR PEMBELIAN (BATAL DO) (NO. TRANSAKSI '.$purchase_return['code'].')',
					'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$this->session->set_flashdata('success', 'DO Retur Pembelian berhasil dibatalkan');
				redirect(site_url('purchase/return/detail/'.encrypt_custom($purchase_return['id'])));
			}
			else
			{
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Mohon Maaf, DO Retur Pembelian gagal dibatalkan');
				redirect(site_url('purchase/return/detail/'.encrypt_custom($purchase_return['id'])));
			}			
		}
		else
		{			
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/return/detail/'.encrypt_custom($sales_return['id'])));
		}		
	}

	public function print_purchase_return_do($purchase_return_id)
	{
		if($this->session->userdata('create_purchase_return_do') == 1)
		{
			$this->session->unset_userdata('create_purchase_return_do');
			$purchase_return = $this->purchase->get_detail_purchase_return(decrypt_custom($purchase_return_id));
			$supplier      = $this->crud->get_where('supplier', ['code' => $purchase_return['supplier_code']])->row_array();
			$warehouse     = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
								->from('purchase_return_detail')->join('warehouse', 'warehouse.id = purchase_return_detail.warehouse_id')								
								->where('purchase_return_detail.purchase_return_id', $purchase_return['id'])
								->where('warehouse.deleted', 0)->where('purchase_return_detail.deleted', 0)
								->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
			foreach($warehouse AS $info_w)
			{
				$data_so = $this->db->select('purchase_return.code')
									->from('purchase_return')->join('purchase_return_detail', 'purchase_return_detail.purchase_return_id = purchase_return.id')
									->where('purchase_return_detail.warehouse_id', $info_w['id_w'])
									->where('purchase_return_detail.purchase_return_id', $purchase_return['id'])
									->where('purchase_return_detail.deleted', 0)
									->group_by('purchase_return.id')->order_by('purchase_return.code', 'asc')->get()->result_array();
				$product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, purchase_return_detail.qty AS qty, unit.code AS code_u')
								->from('purchase_return_detail')->join('product', 'product.id = purchase_return_detail.product_id')
								->where('purchase_return_detail.warehouse_id', $info_w['id_w'])
								->where('purchase_return_detail.purchase_return_id', $purchase_return['id'])
								->where('product.deleted', 0)->where('purchase_return_detail.deleted', 0)
								->join('unit', 'unit.id = purchase_return_detail.unit_id')
								->group_by('purchase_return_detail.id')->order_by('product_code', 'asc')->get()->result_array();
				$data_product = array();
				foreach($product AS $info_p)
				{
					$data_product[] = array(
						'id_p'   => $info_p['id_p'],
						'code_p' => $info_p['code_p'],
						'name_p' => $info_p['name_p'],
						'qty'	 => $info_p['qty'],
						'code_u' => $info_p['code_u']
					);
				}
				
				
				$sot[] = array(
					'code_sot' 	 => $purchase_return['code'],
					'data_so'    => $data_so,
					'id_w' 		 => $info_w['id_w'],
					'code_w' 	 => $info_w['code_w'],
					'name_w' 	 => $info_w['name_w'],
					'product'	 => $data_product
				);
			}		
			$data = array(
				'perusahaan' => $this->global->company(),
				'purchase_return' => $purchase_return,
				'supplier'    => $supplier,
				'sot'        => $sot
			);
			$this->load->view('purchase/return/print_purchase_return_do', $data);
		}
		else
		{

		}		
	}		

	public function datatable_detail_purchase_return($purchase_return_id)
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('purchase_return_detail.id AS id, product.code AS code_p, product.name AS name_p, unit.name AS name_u, warehouse.name AS name_w, purchase_return_detail.qty AS qty, purchase_return_detail.price, purchase_return_detail.total AS total,purchase_return_detail.information');
			$this->datatables->from('purchase_return_detail');		
			$this->datatables->join('product', 'product.code = purchase_return_detail.product_code');
			$this->datatables->join('unit', 'unit.id = purchase_return_detail.unit_id');
			$this->datatables->join('warehouse', 'warehouse.id = purchase_return_detail.warehouse_id');		
			$this->datatables->where('purchase_return_detail.purchase_return_id', $purchase_return_id);
			$this->datatables->where('purchase_return_detail.deleted', 0);		
			$this->datatables->group_by('purchase_return_detail.id');
			$this->datatables->add_column('code_p',
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(code_p),code_p');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}
	
	public function detail_purchase_return($purchase_return_id)
    {
		if($this->system->check_access('purchase/return', 'R'))
		{			
			$header = array("title" => "Detail Retur Pembelian");
			$data = array('purchase_return' => $this->purchase->get_detail_purchase_return(decrypt_custom($purchase_return_id)));
			$footer = array("script" => ['transaction/purchase/return/detail_purchase_return.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('purchase/return/detail_purchase_return', $data);
			$this->load->view('include/footer', $footer);
		}	
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/return'));
		}        
	}
	
	public function update_purchase_return($purchase_return_id)
    {
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$purchase_return = $this->purchase->get_detail_purchase_return(decrypt_custom($purchase_return_id));
			$purchase_return_detail = $this->purchase->get_detail_purchase_return_detail($purchase_return['id']);
			$this->form_validation->set_rules('date', 'Tanggal Retur Pembelian', 'trim|required|xss_clean');
			$this->form_validation->set_rules('supplier_code', 'Supplier', 'trim|required|xss_clean');
			$this->form_validation->set_rules('method', 'Jenis Retur', 'trim|xss_clean');
			$this->form_validation->set_rules('product[]', 'Daftar Produk', 'trim|required|xss_clean');
			$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|xss_clean');
			$this->form_validation->set_rules('total_qty', 'Total Kuantitas', 'trim|xss_clean');
			$this->form_validation->set_rules('total_return', 'Total Retur', 'trim|required|xss_clean');
			if($post['method'] == 2)
			{
				$this->form_validation->set_rules('purchase_invoice_id', 'No. Pembelian', 'trim|required|xss_clean');
				$this->form_validation->set_rules('account_payable', 'Hutang Pembelian', 'trim|required|xss_clean');
				$this->form_validation->set_rules('grandtotal', 'Sisa Tagihan', 'trim|required|xss_clean');    	
			}        
			$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');								
			if($this->form_validation->run() == FALSE)
			{
				
				$header = ["title" => "Perbarui Retur Pembelian"];
				$data   = [
					'purchase_return' => $purchase_return,
					'purchase_return_detail' => $purchase_return_detail
				];
				$footer = array("script" => ['transaction/purchase/return/update_purchase_return.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('purchase/return/update_purchase_return', $data);
				$this->load->view('include/footer', $footer);
			}
			else
			{
				$this->db->trans_start();
				$supplier	      = $this->crud->get_where('supplier', ['code' => $post['supplier_code']])->row_array();
				$ppn			  = (!isset($post['ppn'])) ?  0 : $post['ppn'];
				$total_return 	  = format_amount($post['total_return']);
				$account_payable  = format_amount($post['account_payable']);
				$grandtotal 	  = format_amount($post['grandtotal']);
				$data_purchase_return = [
					'date' 				=> format_date($post['date']),
					'employee_code'		=> $this->session->userdata('code_e'),
					'supplier_code'		=> $post['supplier_code'],
					'method' 			=> $post['method'],
					'cl_type' 			=> isset($post['from_cl_type']) ? $post['from_cl_type'] : null,
					'account_id' 		=> isset($post['from_account_id']) ? $post['from_account_id']: null,
					'total_product' 	=> $post['total_product'],
					'total_qty' 		=> $post['total_qty'],
					'total_return'		=> $total_return,
					'purchase_invoice_id' => ($post['method'] == 2) ? $post['purchase_invoice_id'] : null,
					'account_payable' 	=> ($post['method'] == 2) ? $account_payable : null,
					'grandtotal' 		=> ($post['method'] == 2) ? $grandtotal : null,
					'ppn'				=> $ppn
				];
				if($this->crud->update('purchase_return', $data_purchase_return, ['id' => $purchase_return['id']]))
				{	
					$this->crud->delete('purchase_return_detail', ['purchase_return_id' => $purchase_return['id']]);
					foreach($post['product'] AS $info)
					{
						if($info['product_code'] == "")
						{
							continue;							
						}
						$product_id = $this->crud->get_product_id($info['product_code']);
						$qty = format_amount($info['qty']);
						$where_unit = array(
							'product_code' => $info['product_code'],
							'unit_id' 	   => $info['unit_id'],
							'deleted'	   => 0
						);																		
						$convert = $this->crud->get_where('product_unit', $where_unit)->row_array();
						$data_purchase_return_detail = array(
							'purchase_return_id' => $purchase_return['id'],
							'product_id'		 => $product_id,
							'product_code'		 => $info['product_code'],
							'unit_id'		 	 => $info['unit_id'],
							'unit_value'		 => ($convert['value' != null]) ? $convert['value'] : 1,
							'warehouse_id'		 => $info['warehouse_id'],
							'qty'		 		 => $qty,
							'price'		 		 => format_amount($info['price']),
							'total'		 		 => format_amount($info['total']),
							'ppn'				 => $ppn,
							'information'		 => $info['information']
						);							
						if($this->crud->insert('purchase_return_detail', $data_purchase_return_detail))
						{								
							continue;
						}
						else
						{
							break;
						}
					}
					$data_activity = [
						'information' => 'MEMPERBARUI RETUR PEMBELIAN',
						'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);												
				}
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === TRUE)
			{
				$this->db->trans_commit();
				$data_activity = [
					'information' => 'MEMPERBARUI RETUR PEMBELIAN (NO. TRANSAKSI '.$purchase_return['code'].')',
					'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$this->session->set_flashdata('success', 'Retur Pembelian berhasil diperbarui');
				redirect(site_url('purchase/return/detail/'.encrypt_custom($purchase_return['id'])));
			}
			else
			{
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Retur Pembelian gagal diperbarui');
				redirect(site_url('purchase/return'));
			}				
		}
		else
		{
			//$this->session->userdata('verifypassword') == 1	
			if(true)
			{			
				$this->session->unset_userdata('verifypassword');
				$purchase_return = $this->purchase->get_detail_purchase_return(decrypt_custom($purchase_return_id));
				$header = ["title" => "Perbarui Retur Pembelian"];
				$data   = [
					'purchase_return' => $purchase_return,
					'purchase_return_detail' => $this->purchase->get_detail_purchase_return_detail($purchase_return['id'])
				];
				$footer = array("script" => ['transaction/purchase/return/update_purchase_return.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('purchase/return/update_purchase_return', $data);
				$this->load->view('include/footer', $footer);
				
			}
			else
			{
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
				redirect(urldecode($this->agent->referrer()));
			}						
		}		
	}

	public function delete_purchase_return()
	{
		if($this->input->is_ajax_request())
		{
			//$this->session->userdata('verifypassword') == 1
			if(true)
			{
				$this->session->unset_userdata('verifypassword');
				$post = $this->input->post();
				$this->db->trans_start();
				$purchase_return = $this->purchase->get_detail_purchase_return($post['purchase_return_id']);
				// DELETE PURCHASE RETURN DETAIL
				$this->crud->delete('purchase_return_detail', ['purchase_return_id' => $purchase_return['id']]);			
				// DELETE PURCHASE RETURN
				$this->crud->delete('purchase_return', ['id' => $purchase_return['id']]);
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$data_activity = [
						'information' => 'MENGHAPUS RETUR PEMBELIAN (NO. TRANSAKSI '.$purchase_return['code'].')',
						'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);
					$response   =   [
						'status'    => [
							'code'      => 200,
							'message'   => 'Berhasil',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('success', 'BERHASIL! Retur Pembelian Terhapus');
				}			
				else
				{
					$this->db->trans_rollback();
					$response   =   [
						'status'    => [
							'code'      => 400,
							'message'   => 'Gagal',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('error', 'Mohon Maaf, Retur Pembelian gagal Terhapus');

				}
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
				$this->session->set_flashdata('error', 'Mohon Maaf, anda tidak memiliki akses');
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}	
	}

	public function print_purchase_return($purchase_return_id)
	{
		if($this->system->check_access('purchase/return', 'C'))
		{					
			$purchase_return = $this->purchase->get_detail_purchase_return(decrypt_custom($purchase_return_id));
			if($purchase_return != null)
			{
				$data = array(
					'purchase_return'		 => $purchase_return,
					'purchase_return_detail' => $this->purchase->get_detail_purchase_return_detail($purchase_return['id_pr'])
				);
				$this->load->view('purchase/return/print_purchase_return', $data);
			}
			else
			{
				$this->load->view('auth/show_404');
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses');
			redirect(site_url('purchase/return'));
		}
	}
}