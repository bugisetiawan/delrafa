<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends System_Controller 
{	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Payment_model', 'payment');
		$this->load->model('transaction/Purchase_model','purchase');
		$this->load->model('transaction/Sales_model','sales');
	}

	// GET CHEQUE
	public function get_cheque($transaction_type)
	{
		if($this->input->is_ajax_request())
		{
			if($transaction_type == 1){
				$where = [
					'transaction_type' => $transaction_type,
					'cheque_status'    => 2,
					'deleted'          => 0
				];
			}
			elseif($transaction_type == 2){
				$where = [
					'transaction_type' => $transaction_type,
					'cheque_status'    => 2,
					'to_pl'            => null,
					'deleted'          => 0
				];
			}            
			$data = $this->db->select('payment_ledger.id, payment_ledger.cheque_number, SUM(payment_ledger.cheque) AS cheque')
							 ->from('payment_ledger')->where($where)
							 ->like('payment_ledger.payment', 3)->group_by('payment_ledger.cheque_number')->get()->result();
			if($data)
			{
				$response   = [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data
				];
				echo json_encode($response);
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 404,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => ''
				];
				echo json_encode($response);
			}
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}
	
	public function get_cheque_all($transaction_type)
	{
		if($this->input->is_ajax_request())
		{
			if($transaction_type == 1){
				$where = [
					'transaction_type' => $transaction_type,                    
					'deleted'          => 0
				];
			}
			elseif($transaction_type == 2){
				$where = [
					'transaction_type' => $transaction_type,
					'deleted'          => 0
				];
			}            
			$data = $this->db->select('payment_ledger.id, payment_ledger.cheque_number, SUM(payment_ledger.cheque) AS cheque')
							 ->from('payment_ledger')->where($where)
							 ->like('payment_ledger.payment', 3)->group_by('payment_ledger.cheque_number')->get()->result();
			if($data)
			{
				$response   = [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data
				];
				echo json_encode($response);
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 404,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => ''
				];
				echo json_encode($response);
			}
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}
	
	// PAYMENT OF DEBT
	public function create_payment_of_debt($purchase_invoice_id)
	{
		if($this->system->check_access('payment/debt', 'create'))
		{
			if($this->input->method() === 'post')
			{				
				$post = $this->input->post();
				$purchase_invoice = $this->purchase->get_detail_purchase_invoice($post['purchase_id']);
				$supplier   = $this->crud->get_where('supplier', ['code' => $purchase_invoice['supplier_code']])->row_array();
				$pod_code   = $this->payment->pod_code();                
				$cash       = floatval(format_amount($post['cash'])); $transfer = floatval(format_amount($post['transfer'])); $cheque = floatval(format_amount($post['cheque'])); $deposit = floatval(format_amount($post['deposit'])); $move_cheque = floatval(format_amount($post['move_cheque']));
				$grandtotal = $cash+$transfer+$cheque+$deposit+$move_cheque;
				$this->db->trans_start();
				// PAYMENT LEDGER
				$data_payment_of_debt = [
					'is_multi'        => 0,
					'transaction_type'=> 1,
					'code'            => $pod_code,
					'date'            => format_date($post['payment_date']),
					'payment'         => json_encode($post['payment']),
					'information'     => $post['information'],
					'cash_account_id' => ($cash > 0) ? $post['cash_account_id'] : null,
					'cash'            => $cash,
					'transfer_account_id'  => ($transfer > 0) ? $post['transfer_account_id'] : null,
					'transfer'       => $transfer,
					'cheque_account_id' => ($cheque > 0) ? $post['cheque_account_id'] : null,
					'cheque_number'  => ($cheque > 0) ? $post['cheque_number'] : null,
					'cheque_open_date'  => ($cheque > 0) ? date('Y-m-d', strtotime($post['cheque_open_date'])) : null,
					'cheque_close_date' => ($cheque > 0) ? date('Y-m-d', strtotime($post['cheque_close_date'])) : null,
					'cheque_status'  => ($cheque > 0) ? 2 : null,
					'cheque'         => $cheque,
					'deposit'        => $deposit,
					'move_cheque_number' => ($move_cheque > 0) ? $post['move_cheque_number'] : null,
					'move_cheque'    => $move_cheque,
					'move_cheque_status'  => ($move_cheque > 0) ? 2 : null,
					'grandtotal'     => $grandtotal,                    
					'employee_code'  => $this->session->userdata('code_e')
				];                
				$pod_id = $this->crud->insert_id('payment_ledger', $data_payment_of_debt);
				if($pod_id != null)
				{       
					// PAYMENT LEDGER DETAIL
					$data_pod_detail = [
						'pl_id'          => $pod_id,
						'transaction_id' => $purchase_invoice['id'],
						'cash'           => $cash,
						'transfer'       => $transfer,
						'cheque'         => $cheque,
						'deposit'        => $deposit,
						'move_cheque'    => $move_cheque,
						'grandtotal'     => $grandtotal
					];
					$this->crud->insert('payment_ledger_detail', $data_pod_detail);
					// GENERAL LEDGER -> HUTANG USAHA (D)
					$where_last_balance = [
						'coa_account_code' => "20101",
						'date <='        => format_date($post['payment_date']),                    
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
					$data = [
						'coa_account_code'  => "20101",
						'date'        => format_date($post['payment_date']),
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
							'date >'        => format_date($post['payment_date']),
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
						}
					}		
					// ---------------------------------------------
					foreach($post['payment'] AS $payment_info)
					{
						// CASH LEDGER
						$res=0;
						switch($payment_info){
							case 1:
								$from_where_last_balance = [
									'cl_type'    => 1,
									'account_id' => $post['cash_account_id'],
									'date <='    => format_date($post['payment_date']),
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$cash : 0-$cash;
								$data = [
									'cl_type'     => 1,
									'account_id'  => $post['cash_account_id'],
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'date'        => format_date($post['payment_date']),
									'amount'      => $cash,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 1,
										'account_id' => $post['cash_account_id'],
										'date >'     => format_date($post['payment_date']),
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$cash], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> KAS (K)
								$where_last_balance = [
									'coa_account_code' => "10101",
									'date <='        => format_date($post['payment_date']),
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $cash) : sub_balance(0, $cash);
								$data = [										
									'coa_account_code' => "10101",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'credit'      => $cash,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10101",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $cash)], ['id' => $info['id']]);
									}                            
								}
								$res=1;
								break;
							case 2:
								$from_where_last_balance = [
									'cl_type'    => 2,
									'account_id' => $post['transfer_account_id'],
									'date <='    => format_date($post['payment_date']),
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$transfer : 0-$transfer;
								$data = [
									'cl_type'     => 2,
									'account_id'  => $post['transfer_account_id'],
									'transaction_id'   => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'date'        => format_date($post['payment_date']),
									'amount'      => $transfer,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 2,
										'account_id' => $post['transfer_account_id'],
										'date >'     => date('Y-m-d', strtotime($post['payment_date'])),
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$transfer], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> BANK (K)
								$where_last_balance = [
									'coa_account_code' => "10102",
									'date <='        => format_date($post['payment_date']),
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $transfer) : sub_balance(0, $transfer);
								$data = [										
									'coa_account_code' => "10102",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'credit'      => $transfer,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10102",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $transfer)], ['id' => $info['id']]);
									}                            
								}
								$res=1;
								break;
							case 3:
								$data_cheque_payable = ['cheque_payable' => $purchase_invoice['cheque_payable']+$cheque];
								$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);
								// GENERAL_LEDGER -> HUTANG CEK/GIRO (K)
								$where_last_balance = [
									'coa_account_code' => "20102",
									'date <='        => format_date($post['payment_date']),
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $cheque) : add_balance(0, $cheque);
								$data = [										
									'coa_account_code' => "20102",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'credit'      => $cheque,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "20102",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $cheque)], ['id' => $info['id']]);
									}
								}
								$res=1;
								break;
							case 4:
								$from_where_last_balance = [
									'cl_type'    => 3,
									'account_id' => $purchase_invoice['supplier_code'],
									'date <='    => format_date($post['payment_date']),
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$deposit : 0-$deposit;
								$data = [
									'cl_type'     => 3,
									'account_id'  => $purchase_invoice['supplier_code'],
									'date'        => format_date($post['payment_date']),
									'transaction_id'   => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'amount'      => $deposit,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'       => 3,
										'account_id'    => $purchase_invoice['supplier_code'],
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$deposit], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> UANG MUKA PEMBELIAN (K)
								$where_last_balance = [
									'coa_account_code' => "10401",
									'date <='        => format_date($post['payment_date']),
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $deposit) : sub_balance(0, $deposit);
								$data = [										
									'coa_account_code' => "10401",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $pod_id,
									'invoice'     => $pod_code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
									'credit'      => $deposit,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10401",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $deposit)], ['id' => $info['id']]);
									}                            
								}
								$res=1;
								break;
							case 5:
								$to_pl = ['to_pl'	=> json_encode([$pod_id])];
								$this->crud->update('payment_ledger', $to_pl, ['cheque_number' => $post['move_cheque_number']]);
								$data_cheque_payable = array('cheque_payable' => $purchase_invoice['cheque_payable']+$move_cheque);
								$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);
								$res=1;
								break;
							default:
								break;
						}
					}
					$account_payable = $purchase_invoice['account_payable']-$grandtotal;
					if($account_payable == 0)
					{
						$data_purchase_invoice = array(
							'account_payable' => $account_payable,
							'payment_status'  => 1
						);
					}
					else
					{
						$data_purchase_invoice = array('account_payable' => $account_payable);
					}
					$this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $post['purchase_id']]);
					$this->db->trans_complete();
					if($this->db->trans_status() === TRUE && $res == 1)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('success', 'Pembayaran Pembelian Berhasil');
						redirect(site_url('payment/debt/detail/'.encrypt_custom($pod_id)));
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Pembayaran Pembelian Gagal');
						redirect(site_url('report/finance/purchase_payable/'));
					}
				}
				else
				{
					$this->session->set_flashdata('error', 'Pembayaran Pembelian Gagal');
					redirect(site_url('report/finance/purchase_payable/'));
				}                
			}
			else
			{                                
				$purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_invoice_id));
				if($purchase_invoice != null)
				{
					if($purchase_invoice['payment_status'] != 1)
					{
						$header = array("title" => "Pembayaran Pembelian Baru");
						$footer = array("script" => ['finance/pod/create_payment_of_debt.js']);
						$data = array(
							'purchase_invoice' => $purchase_invoice
						);
						$this->load->view('include/header', $header);
						$this->load->view('include/menubar');
						$this->load->view('include/topbar');
						$this->load->view('finance/pod/create_payment_of_debt', $data);
						$this->load->view('include/footer', $footer);   
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, tidak dapat melakukan Transaksi Pembayaran Hutang dikarenakan pembelian sudah LUNAS. Terima kasih');
						redirect(site_url('report/finance/purchase_payable'));
					}
				}
				else
				{
					$this->load->view('auth/show_404');
				}                                             
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}        
	}    

	public function create_multi_payment_of_debt()
	{
		if($this->system->check_access('payment/debt/multi', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post   = $this->input->post();
				if(isset($post['datatable_type']) && $post['datatable_type'] == "first")
				{
					header('Content-Type: application/json');
					$this->datatables->select('purchase_invoice.id AS id, purchase_invoice.id AS choose, purchase_invoice.date, purchase_invoice.code AS code, purchase_invoice.invoice, purchase_invoice.payment, purchase_invoice.due_date, DATEDIFF(purchase_invoice.due_date, CURRENT_DATE()) AS remaining_time, purchase_invoice.grandtotal, (purchase_invoice.account_payable-purchase_invoice.cheque_payable) AS account_payable, purchase_invoice.payment_status');
					$this->datatables->from('purchase_invoice');                    
					$this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');                    
					$this->datatables->where('purchase_invoice.deleted', 0);
					$this->datatables->where('purchase_invoice.payment', 2);
					$this->datatables->where('purchase_invoice.payment_status !=', 1);
					$this->datatables->where('(purchase_invoice.account_payable-purchase_invoice.cheque_payable) >', 0);
					$this->datatables->where('purchase_invoice.supplier_code', $post['supplier_code']);
					$this->datatables->group_by('purchase_invoice.id');	
					$this->datatables->add_column('choose',
					'			
						<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
						<input type="checkbox" name="purchase_id[]" value="$1" class="choose">&nbsp;<span></span>
						</label>
					', 'id');	
					$this->datatables->add_column('invoice', 
					'
						<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>
					', 'encrypt_custom(id),invoice');
					echo $this->datatables->generate();
				}
				elseif(isset($post['post_type']) && $post['post_type'] == "second_form")
				{
					// echo json_encode($post); die;
					$code = $this->payment->pod_code();
					$cash = floatval(format_amount($post['cash'])); $transfer = floatval(format_amount($post['transfer'])); $cheque = floatval(format_amount($post['cheque'])); $deposit = floatval(format_amount($post['deposit'])); $move_cheque = floatval(format_amount($post['move_cheque']));
					$to_pl_list = [];
					// CASH LEDGER
					foreach($post['payment'] AS $payment_info)
					{
						switch ($payment_info){
							case 1:
								if($cash > 0)
								{
									$from_where_last_balance = [
										'cl_type'    => $post['from_cl_type'],
										'account_id' => $post['from_account_id'],
										'date <='    => format_date($post['payment_date']),
										'deleted'    => 0
									];
									$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
									$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$cash : 0-$cash;
									$data = [
										'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
										'account_id'  => $post['from_account_id'],
										'transaction_type' => 3, //1:DEPOSIT, 2:CASH MUTATION, 3:PURCHASE, 4:PURCHASE RETURN, 5:SALES INVOICE, 6:SALES RETURN, 7:EXPENSE               
										'transaction_id'   => $pod_id,
										'invoice'     => $code,
										'information' => 'PEMBAYARAN PEMBELIAN',
										'note'        => $supplier['name'],
										'date'        => format_date($post['payment_date']),
										'amount'      => $cash,
										'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
										'balance'     => $from_balance
									];
									$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
									if($from_cl_id)
									{
										$from_where_after_balance = [
											'cl_type'       => $post['from_cl_type'],
											'account_id'    => $post['from_account_id'],
											'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
											'deleted'       => 0
										];                    
										$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
										foreach($from_after_balance  AS $info)
										{                        
											$balance = $info['balance'] - $cash;
											$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
										}                            
									}
								}                                    
								break;
							case 2:
								if($transfer > 0)
								{
									$from_where_last_balance = [
										'cl_type'    => 3,
										'account_id' => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
										'date <='    => format_date($post['payment_date']),
										'deleted'    => 0
									];
									$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
									$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$transfer : 0-$transfer;
									$data = [
										'cl_type'     => 3, //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
										'account_id'  => $post['transfer_account_id'],
										'transaction_type' => 3,
										'transaction_id'   => $pod_id,
										'invoice'     => $code,
										'information' => 'PEMBAYARAN PEMBELIAN',
										'note'        => $supplier['name'],
										'date'        => format_date($post['payment_date']),
										'amount'      => $transfer,
										'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
										'balance'     => $from_balance
									];
									$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
									if($from_cl_id)
									{
										$from_where_after_balance = [
											'cl_type'       => 3,
											'account_id'    => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
											'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
											'deleted'       => 0
										];                    
										$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
										foreach($from_after_balance  AS $info)
										{
											$balance = $info['balance'] - $transfer;
											$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
										}
									}
								}                                    
								break;
							case 3:
								if($cheque > 0)
								{
									$total_cheque_payable = $total_cheque_payable + $cheque;
									$data_cheque_payable = array(
										'cheque_payable' => $total_cheque_payable
									);
									$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);                                        
								}
								break;
							case 4:
								if($deposit > 0)
								{
									$from_where_last_balance = [
										'cl_type'    => 4,
										'account_id' => $purchase_invoice['supplier_code'],
										'date <='    => format_date($post['payment_date']),
										'deleted'    => 0
									];
									$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
									$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$deposit : 0-$deposit;
									$data = [
										'cl_type'     => 4,
										'transaction_type' => 5,
										'account_id'  => $purchase_invoice['supplier_code'],
										'date'        => format_date($post['payment_date']),
										'invoice'     => $code,
										'information' => 'PEMBAYARAN PEMBELIAN',
										'note'        => $supplier['name'],
										'amount'      => $deposit,
										'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
										'balance'     => $from_balance
									];
									$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
									if($from_cl_id)
									{
										$from_where_after_balance = [
											'cl_type'       => 4,
											'account_id'    => $purchase_invoice['supplier_code'],
											'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
											'deleted'       => 0
										];                    
										$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
										foreach($from_after_balance  AS $info)
										{
											$balance = $info['balance']-$deposit;
											$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
										}
									}
								}                                    
								break;
							case 5:
								if($move_cheque > 0)
								{
									$to_pl[] = $pod_id;
									$total_cheque_payable = $total_cheque_payable + $move_cheque;
									$data_cheque_payable = array(
										'cheque_payable' => $total_cheque_payable
									);
									$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);
								}                                    
								break;
							default:
								break;
						}
					}  
					// PAYMENT LEDGER
					foreach($post['purchase'] AS $info)
					{                        
						$purchase_invoice = $this->purchase->get_detail_purchase_invoice($info['purchase_id']);
						$supplier = $this->crud->get_where('supplier', ['code' => $purchase_invoice['supplier_code']])->row_array();
						$pay_cash = 0; $pay_transfer = 0; $pay_cheque = 0; $pay_deposit = 0; $pay_move_cheque = 0;
						$total_pay = format_amount($info['pay']); $total_cheque_payable = $purchase_invoice['cheque_payable'];
						$payment_option = []; 
						foreach($post['payment'] AS $payment_info)
						{
							if($total_pay > 0)
							{
								switch ($payment_info){
									case 1:
										if($cash >= $total_pay)
										{                                        
											$pay_cash = $total_pay;
											$cash = $cash - $pay_cash; $total_pay = $total_pay - $pay_cash;
										}
										elseif($cash > 0)
										{
											$pay_cash = $cash;
											$cash = $cash - $pay_cash; $total_pay = $total_pay - $pay_cash;
										}                                    
										break;
									case 2:
										if($transfer >= $total_pay)
										{                                        
											$pay_transfer = $total_pay;
											$transfer = $transfer - $pay_transfer; $total_pay = $total_pay - $pay_transfer;
										}
										elseif($transfer > 0)
										{                                        
											$pay_transfer = $transfer;
											$transfer = $transfer - $pay_transfer; $total_pay = $total_pay - $pay_transfer;
										}                                    
										break;
									case 3:
										if($cheque >= $total_pay)
										{                                        
											$pay_cheque = $total_pay;
											$cheque = $cheque - $pay_cheque; $total_pay = $total_pay - $pay_cheque;
										}
										elseif($cheque > 0)
										{
											$pay_cheque = $cheque;
											$cheque = $cheque - $pay_cheque; $total_pay = $total_pay - $pay_cheque;
										}
										break;
									case 4:                                    
										if($deposit >= $total_pay)
										{                                        
											$pay_deposit = $total_pay;
											$deposit = $deposit - $pay_deposit; $total_pay = $total_pay - $pay_deposit;
										}
										elseif($deposit > 0)
										{
											$pay_deposit = $deposit;
											$deposit = $deposit - $pay_deposit; $total_pay = $total_pay - $pay_deposit;
										}
										break;
									case 5:
										if($move_cheque >= $total_pay)
										{                                        
											$pay_move_cheque = $total_pay;
											$move_cheque = $move_cheque - $pay_move_cheque; $total_pay = $total_pay - $pay_move_cheque;
										}
										elseif($move_cheque > 0)
										{
											$pay_move_cheque = $move_cheque;
											$move_cheque = $move_cheque - $pay_move_cheque; $total_pay = $total_pay - $pay_move_cheque;
										}
										break;
									default:
										break;
								}
							}
							else
							{
								continue;
							}                            
						}
						$data_payment_ledger = array(
							'is_multi'    => 1,
							'transaction_type'=> 1, //1:PURCHASE, 2:SALES INVOICE
							'transaction_id'  => $info['purchase_id'],
							'code'            => $code,
							'date'            => format_date($post['payment_date']),
							'payment'         => json_encode($post['payment']),
							'cash_cl_type'    => ($post['from_cl_type'] != "" && $pay_cash > 0) ? $post['from_cl_type'] : null,
							'cash_account_id' => ($post['from_account_id'] != "" && $pay_cash > 0) ? $post['from_account_id'] : null,
							'cash'            => $pay_cash,
							'transfer_account_id'=> ($post['transfer_account_id'] != "" && $pay_transfer > 0) ? $post['transfer_account_id'] : null,
							'transfer'        => $pay_transfer,
							'cheque_account_id'  => ($post['cheque_account_id'] != "" && $pay_cheque > 0) ? $post['cheque_account_id'] : null,
							'cheque_number'   => ($post['cheque_number'] && $pay_cheque > 0) ? $post['cheque_number'] : null,
							'cheque_open_date'  => ($post['cheque_open_date'] != "" && $pay_cheque > 0) ? date('Y-m-d', strtotime($post['cheque_open_date'])) : null,
							'cheque_close_date' => ($post['cheque_close_date'] != "" && $pay_cheque > 0) ? date('Y-m-d', strtotime($post['cheque_close_date'])) : null,
							'cheque_status'  => ($post['cheque_account_id'] != "" && $pay_cheque > 0) ? 2 : null,
							'cheque'         => $pay_cheque,
							'deposit'        => $pay_deposit,
							'move_cheque_number' => ($post['move_cheque_number'] != "" && $pay_move_cheque) ? $post['move_cheque_number'] : null,
							'move_cheque'    => $pay_move_cheque,
							'move_cheque_status'  => ($post['move_cheque_number'] != "") ? 2 : null,
							'grandtotal'     => $pay_cash+$pay_transfer+$pay_cheque+$pay_deposit+$pay_move_cheque,
							'is_multi'       => 1,
							'employee_code'  => $this->session->userdata('code_e')
						);
						$pod_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
						$account_payable = (int)$purchase_invoice['account_payable']-(int)$pay_cash-(int)$pay_transfer-(int)$pay_deposit;
						if($account_payable == 0)
						{
							$data_purchase_invoice = array(
								'account_payable' => $account_payable,
								'payment_status'  => 1
							);
						}
						else
						{
							$data_purchase_invoice = array('account_payable' => $account_payable);
						} 
						$this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $purchase_invoice['id']]);
					}
					$this->crud->update('payment_ledger', ['to_pl' => json_encode($to_pl)], ['cheque_number' => $post['move_cheque_number']]);                
					$this->session->set_flashdata('success', 'Pembayaran Hutang Pembelian berhasil');
					redirect(site_url('report/finance/payment_of_debt/'));
				}
				else                        
				{
					$supplier = $this->crud->get_where('supplier', ['code' => $post['supplier_code']])->row_array();
					$purchase = $this->db->select('purchase_invoice.id AS id, purchase_invoice.id AS choose, purchase_invoice.date, purchase_invoice.code, purchase_invoice.invoice, purchase_invoice.payment, purchase_invoice.due_date, DATEDIFF(purchase_invoice.due_date, CURRENT_DATE()) AS remaining_time, purchase_invoice.grandtotal, (purchase_invoice.account_payable-purchase_invoice.cheque_payable) AS account_payable, supplier.name AS name_c, employee.name AS name_s, purchase_invoice.payment_status')
									 ->from('purchase_invoice')
									 ->join('employee', 'employee.code = purchase_invoice.employee_code')
									 ->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
									 ->where('purchase_invoice.deleted', 0)
									 ->where('purchase_invoice.payment', 2)
									 ->where('purchase_invoice.payment_status !=', 1)
									 ->where('purchase_invoice.supplier_code', $post['supplier_code'])
									 ->where_in('purchase_invoice.id', $post['purchase_id'])
									 ->group_by('purchase_invoice.id')->get()->result_array();                                     
					$header = array("title" => "Pembayaran Banyak Pembelian");
					$footer = array("script" => ['finance/pod/create_multi_payment_of_debt.js']);
					$data = [
						'post_type' => 'second_form',
						'supplier' => $supplier,
						'purchase' => $purchase                        
					];
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('finance/pod/create_multi_payment_of_debt', $data);
					$this->load->view('include/footer', $footer);                		
				}         
			}
			else
			{
				$header = array("title" => "Pembayaran Banyak Pembelian");
				$footer = array("script" => ['finance/pod/create_multi_payment_of_debt.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/pod/create_multi_payment_of_debt');
				$this->load->view('include/footer', $footer);                		
			}   
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}            
	}	

	public function create_cheque_pod_acquittance()
	{
		if($this->system->check_access('payment/debt', 'create'))
		{
			if($this->input->is_ajax_request())
			{
				if($this->input->method() === 'post')
				{
					$post   = $this->input->post();
					$this->db->trans_start();
					$pod = $this->payment->get_detail_pod(decrypt_custom($post['pod_id']));					
					// PAYMENT LEDGER
					$data = [
						'cheque_acquittance_date' => format_date($post['cheque_acquittance_date']),
						'cheque_status'           => 1
					];
					$this->crud->update('payment_ledger', $data, ['cheque_number' => $pod['cheque_number']]);
					// CASH LEDGER 
					$from_where_last_balance = [
						'cl_type'    => 2,
						'account_id' => $pod['cheque_account_id'],
						'date <='    => format_date($post['cheque_acquittance_date']),
						'deleted'    => 0
					];
					$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $pod['cheque']) : sub_balance(0, $pod['cheque']);
					$data = [
						'cl_type'     => 2,
						'account_id'  => $pod['cheque_account_id'],
						'date'        => format_date($post['cheque_acquittance_date']),
						'transaction_id' => $pod['id'],
						'invoice'     => $pod['code'],
						'information' => 'PEMBAYARAN PEMBELIAN',
						'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod['code'].'_'.$supplier['name'],
						'amount'      => $pod['cheque'],
						'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
						'balance'     => $from_balance
					];
					$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
					if($from_cl_id)
					{
						$from_where_after_balance = [
							'cl_type'       => 2,
							'account_id'    => $pod['cheque_account_id'],
							'date >'        => format_date($post['cheque_acquittance_date']),
							'deleted'       => 0
						];                    
						$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($from_after_balance  AS $info_after_balance)
						{
							$this->crud->update('cash_ledger', ['balance' => sub_balance($info_after_balance['balance']-$pod['cheque'])], ['id' => $info_after_balance['id']]);
						}
					}
					// POD DETAIL
					$pod_detail = $this->payment->get_detail_pod_detail($pod['id']);
					foreach($pod_detail AS $info_pod_detail)
					{
						$res = 0;
						$purchase_invoice = $this->purchase->get_detail_purchase_invoice($info_pod_detail['transaction_id']);
						$account_payable = $purchase_invoice['account_payable']-$info['cheque'];
						$cheque_payable = $purchase_invoice['cheque_payable']-$info['cheque'];
						if($account_payable == 0)
						{
							$data_purchase = array(
								'account_payable' => $account_payable,
								'cheque_payable'  => $cheque_payable,
								'payment_status'  => 1
							);
						}
						else
						{
							$data_purchase = array(
								'account_payable' => $account_payable,
								'cheque_payable'  => $cheque_payable
							);
						} 
						if($this->crud->update('purchase_invoice', $data_purchase, ['id' => $purchase['id']]))
						{
							$res = 1;
							continue;
						}
						else
						{							
							break;
						}
					}
					$this->db->trans_complete();
					if($this->db->trans_status() === 'TRUE' && $res == 1)
					{
						$this->db->trans_commit();
						$response = [
							'status' => [
								'code'      => 200,
								'message'   => 'Berhasil Menambahkan Data',
							],
							'response'  => ''
						];         
						$this->session->set_flashdata('success', 'BERHASIL! Pelunasan Cek/Giro berhasil');
					}
					else
					{
						$this->db->trans_rollback();
						$response = [
							'status' => [
								'code'      => 401,
								'message'   => 'Gagal Menambahkan Data',
							],
							'response'  => ''
						];                    
						$this->session->set_flashdata('error', 'Mohon maaf, Pelunasan Cek/Giro gagal');
					}
				}
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Mohon maaf, terjadi kesalahan. Terima kasih',
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
						'message'   => 'Mohon maaf, terjadi kesalahan. Terima kasih',
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
					'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
				],
				'response'  => ''
			]; 
		}
		echo json_encode($response);
	}

	public function add_move_cheque_pod_acquittance()
	{
		if($this->system->check_access('payment/debt', 'create'))
		{
			if($this->input->is_ajax_request())
			{
				if($this->input->method() === 'post')
				{
					$post   = $this->input->post();
					$data = [
						'move_cheque_acquittance_date' => date('Y-m-d', strtotime($post['cheque_acquittance_date'])),
						'move_cheque_status'           => 1
					];
					$pod = $this->payment->get_detail_pod(decrypt_custom($post['pod_id']));
					if($this->crud->update('payment_ledger', $data, ['move_cheque_number' => $pod['move_cheque_number']])){
						$pods = $this->crud->get_where('payment_ledger', ['move_cheque_number' => $pod['move_cheque_number']])->result_array();
						foreach($pods AS $info_pod)
						{
							$purchase = $this->crud->get_where('purchase_invoice', ['id' => $info_pod['transaction_id']])->row_array();
							$account_payable = $purchase['account_payable']-$info_pod['move_cheque'];
							$cheque_payable = $purchase['cheque_payable']-$info_pod['move_cheque'];
							if($account_payable == 0) {
								$data_purchase = array(
									'account_payable' => $account_payable,
									'cheque_payable'  => $cheque_payable,
									'payment_status'  => 1
								);
							}
							else {
								$data_purchase = array(
									'account_payable' => $account_payable,
									'cheque_payable'  => $cheque_payable
								);
							} 
							$this->crud->update('purchase_invoice', $data_purchase, ['id' => $purchase['id']]);
						}

						$update_por = [
							'cheque_acquittance_date' => date('Y-m-d', strtotime($post['cheque_acquittance_date'])),
							'cheque_status'           => 1
						];
						$pors = $this->crud->get_where('payment_ledger', ['cheque_number' => $pod['move_cheque_number']])->result_array();
						foreach($pors AS $info_por)
						{                            
							$this->crud->update('payment_ledger', $update_por, ['id' => $info_por['id']]);
							$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $info_por['transaction_id']])->row_array();
							$account_payable = $sales_invoice['account_payable']-$info_por['cheque'];
							$cheque_payable = $sales_invoice['cheque_payable']-$info_por['cheque'];
							if($account_payable == 0)
							{
								$data_sales_invoice = array(
									'account_payable' => $account_payable,
									'cheque_payable'  => $cheque_payable,
									'payment_status'  => 1
								);
							}
							else
							{
								$data_sales_invoice = array(
									'account_payable' => $account_payable,
									'cheque_payable'  => $cheque_payable
								);
							} 
							$this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
						}                                                                        
						$response = [
							'status' => [
								'code'      => 200,
								'message'   => 'Berhasil Menambahkan Data',
							],
							'response'  => ''
						];         
						$this->session->set_flashdata('success', 'BERHASIL! Pelunasan Oper Cek/Giro berhasil');
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
						$this->session->set_flashdata('error', 'Mohon maaf, Pelunasan Oper Cek/Giro gagal');
					}      
				}
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Mohon maaf, terjadi kesalahan. Terima kasih',
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
						'message'   => 'Mohon maaf, terjadi kesalahan. Terima kasih',
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
					'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
				],
				'response'  => ''
			]; 
		}
		echo json_encode($response);
	}

	public function detail_payment_of_debt($pod_id)
	{
		if($this->system->check_access('payment/debt', 'detail'))
		{
			$pod = $this->payment->get_detail_pod(decrypt_custom($pod_id));
			if($pod != null)
			{
				$header = array("title" => "Detail Pembayaran Pembelian");
				$footer = array("script" => ['finance/payment/detail_payment_of_debt.js']);
				$data = array(
					'pod'         => $pod,
					'pod_detail'  => $this->payment->get_detail_pod_detail($pod['id'])
				);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/pod/detail_payment_of_debt', $data);
				$this->load->view('include/footer', $footer);                
			}
			else
			{
				$this->load->view('auth/show_404');                
			}            
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}       
	}

	public function get_payment_option($pod_id)
	{
		if($this->input->is_ajax_request())
		{
			$pod = $this->payment->get_detail_pod($pod_id);
			echo json_encode(json_decode($pod['payment']));
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}
	public function update_payment_of_debt($pod_id)
	{        
		if($this->input->method() === 'post')
		{
			$this->db->trans_start();
			$post = $this->input->post();
			$pod = $this->payment->get_detail_pod($post['pod_id']);
			$purchase_invoice = $this->purchase->get_detail_purchase_invoice($post['purchase_id']);
			$supplier   = $this->crud->get_where('supplier', ['code' => $purchase_invoice['supplier_code']])->row_array();
			$cash       = floatval(format_amount($post['cash'])); $transfer = floatval(format_amount($post['transfer'])); $cheque = floatval(format_amount($post['cheque'])); $deposit = floatval(format_amount($post['deposit'])); $move_cheque = floatval(format_amount($post['move_cheque']));
			$grandtotal = $cash+$transfer+$cheque+$deposit+$move_cheque;
			$data_payment_of_debt = array(                                                          
				'date'            => format_date($post['payment_date']),
				'payment'         => json_encode($post['payment']),
				'cash_cl_type'    => ($cash > 0) ? $post['cash_cl_type'] : null,
				'cash_account_id' => ($cash > 0) ? $post['cash_account_id'] : null,
				'cash'            => $cash,
				'transfer_account_id'  => (isset($post['transfer_account_id']) && $post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
				'transfer'       => $transfer,
				'cheque_account_id' => (isset($post['cheque_account_id']) && $post['cheque_account_id'] != "") ? $post['cheque_account_id'] : null,
				'cheque_number'  => ($post['cheque_number']) ? $post['cheque_number'] : null,
				'cheque_open_date'  => ($post['cheque_open_date'] != "") ? date('Y-m-d', strtotime($post['cheque_open_date'])) : null,
				'cheque_close_date' => ($post['cheque_close_date'] != "") ? date('Y-m-d', strtotime($post['cheque_close_date'])) : null,
				'cheque_status'  => (isset($post['cheque_account_id']) && $post['cheque_account_id'] != "") ? 2 : null,
				'cheque'         => $cheque,
				'deposit'        => $deposit,
				'move_cheque_number' => ($post['move_cheque_number'] != "") ? $post['move_cheque_number'] : null,
				'move_cheque'    => $move_cheque,
				'move_cheque_status'  => ($post['move_cheque_number'] != "") ? 2 : null,
				'grandtotal'     => $grandtotal,                    
				'employee_code'  => $this->session->userdata('code_e')
			);                            
			if($this->crud->update('payment_ledger', $data_payment_of_debt, ['id' => $pod['id']]))
			{
				// DELETE CASH LEDGER
				$where_cash_ledger = [
					'transaction_type' => 3,
					'transaction_id'  => $pod['id']                    
				];
				$total_payment = 0;
				$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger);
				if($cash_ledger->num_rows() > 0)
				{
					foreach($cash_ledger->result_array() AS $info_cash_ledger)
					{
						$total_payment = $total_payment+$info_cash_ledger['amount'];
						$where_after_balance = [
							'cl_type'    => $info_cash_ledger['cl_type'],
							'account_id' => $info_cash_ledger['account_id'],
							'date >='    => $info_cash_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info_after_balance)
						{
							if($info_after_balance['date'] == $info_cash_ledger['date'] && $info_after_balance['id'] < $info_cash_ledger['id'])
							{
								continue;
							}
							else
							{
								if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
								{
									$balance = $info_after_balance['balance']-$info_cash_ledger['amount'];
								}
								else
								{
									$balance = $info_after_balance['balance']+$info_cash_ledger['amount'];
								}
								$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
							}
						}
						$this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
					}
				}           
				$account_payable = $purchase_invoice['account_payable']+$total_payment; 
				$cheque_payable = ($pod['cheque_status'] != 3 ) ? $purchase_invoice['cheque_payable']-($pod['grandtotal']-$total_payment) : $purchase_invoice['cheque_payable'];                
				if($pod['move_cheque_status'] != null)
				{
					$this->crud->update('payment_ledger', ['cheque_status' => 2, 'to_pl' => null], ['cheque_number' => $pod['move_cheque_number']]);
					if($pod['move_cheque_status'] != 2)
					{
						$cheque_payable = $cheque_payable+$pod['move_cheque'];
					}
					switch ($pod['move_cheque_status']){
						case 1:                                                        
							$por = $this->crud->get_where('payment_ledger', ['cheque_number' => $pod['move_cheque_number']])->row_array();
							$sales_invoice = $this->sales->get_detail_sales_invoice($por['transaction_id']);
							$update_sales_invoice = [
								'account_payable' => $sales_invoice['account_payable']+$pod['move_cheque'],
								'cheque_payable' => $sales_invoice['cheque_payable']+$pod['move_cheque']
							];
							$this->crud->update('sales_invoice', $update_sales_invoice, ['id' =>$sales_invoice['id']]);                            
							$account_payable = $account_payable+$pod['move_cheque'];
							break;
						case 3:   
							$por = $this->crud->get_where('payment_ledger', ['cheque_number' => $pod['move_cheque_number']])->row_array();
							$sales_invoice = $this->sales->get_detail_sales_invoice($por['transaction_id']);
							$update_sales_invoice = [
								'cheque_payable' => $sales_invoice['cheque_payable']+$pod['move_cheque']
							];
							$this->crud->update('sales_invoice', $update_sales_invoice, ['id' =>$sales_invoice['id']]);                            
							break;                        
					}
				}	
				$update_purchase_invoice = [
					'account_payable' => $account_payable,
					'cheque_payable'  => $cheque_payable,
					'payment_status'  => 2
				];                
				$this->crud->update('purchase_invoice', $update_purchase_invoice, ['id' => $purchase_invoice['id']]);
				// CREATE CASH LEDGER
				foreach($post['payment'] AS $payment_info)
				{
					$res=0;
					switch ($payment_info){
						case 1:                            
							$from_where_last_balance = [
								'cl_type'    => $post['cash_cl_type'],
								'account_id' => $post['cash_account_id'],
								'date <='    => format_date($post['payment_date']),
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$cash : 0-$cash;
							$data = [
								'cl_type'     => $post['cash_cl_type'], //1:BIG CASH, 2:SMALL CASH
								'account_id'  => $post['cash_account_id'],
								'transaction_type' => 3, //1:DEPOSIT, 2:CASH MUTATION, 3:PURCHASE, 4:PURCHASE RETURN, 5:SALES INVOICE, 6:SALES RETURN, 7:EXPENSE               
								'transaction_id'   => $pod['id'],
								'invoice'     => $pod['code'],
								'information' => 'PEMBAYARAN PEMBELIAN',
								'note'        => $supplier['name'],
								'date'        => format_date($post['payment_date']),
								'amount'      => $cash,
								'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'       => $post['cash_cl_type'],
									'account_id'    => $post['cash_account_id'],
									'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
									'deleted'       => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{                        
									$balance = $info['balance'] - $cash;
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}                            
							}
							$res=1;
							break;
						case 2:
							$from_where_last_balance = [
								'cl_type'    => 3,
								'account_id' => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
								'date <='    => format_date($post['payment_date']),
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$transfer : 0-$transfer;
							$data = [
								'cl_type'     => 3, //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
								'account_id'  => $post['transfer_account_id'],
								'transaction_type' => 3,
								'transaction_id'   => $pod['id'],
								'invoice'     => $pod['code'],
								'information' => 'PEMBAYARAN PEMBELIAN',
								'note'        => $supplier['name'],
								'date'        => format_date($post['payment_date']),
								'amount'      => $transfer,
								'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'       => 3,
									'account_id'    => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
									'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
									'deleted'       => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{
									$balance = $info['balance'] - $transfer;
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}
							}
							$res=1;
							break;
						case 3:
							$data_cheque_payable = array(
								'cheque_payable' => $purchase_invoice['cheque_payable']-($pod['grandtotal']-$total_payment)+$cheque
							);
							$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);
							$res=1;
							break;
						case 4:
							$from_where_last_balance = [
								'cl_type'    => 4,
								'account_id' => $purchase_invoice['supplier_code'],
								'date <='    => format_date($post['payment_date']),
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$deposit : 0-$deposit;
							$data = [
								'cl_type'     => 4,
								'transaction_type' => 5,
								'account_id'  => $purchase_invoice['supplier_code'],
								'date'        => format_date($post['payment_date']),
								'transaction_id' => $pod['id'],
								'invoice'     => $pod['code'],
								'information' => 'PEMBAYARAN PEMBELIAN',
								'note'        => $supplier['name'],
								'amount'      => $deposit,
								'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'       => 4,
									'account_id'    => $purchase_invoice['supplier_code'],
									'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
									'deleted'       => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{
									$balance = $info['balance']-$deposit;
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}
							}
							$res=1;
							break;
						case 5:                                
							$to_pl = [
								'to_pl'	=> json_encode([$pod['id']])
							];
							$this->crud->update('payment_ledger', $to_pl, ['cheque_number' => $post['move_cheque_number']]);
							$data_cheque_payable = array(
								'cheque_payable' => $cheque_payable+$move_cheque
							);
							$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);
							$res=1;
							break;
						default:
							break;
					}
				}                                     
				$account_payable = floatval($account_payable)-$cash-$transfer-$deposit;
				// echo json_encode($account_payable); die;
				if($account_payable == 0)
				{
					$data_purchase_invoice = array(
						'account_payable' => $account_payable,
						'payment_status'  => 1
					);
				}
				else
				{
					$data_purchase_invoice = array('account_payable' => $account_payable);
				}
				$this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $post['purchase_id']]);
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE && $res == 1)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'Pembayaran Pembelian Berhasil');
					redirect(site_url('payment/debt/detail/'.encrypt_custom($pod['id'])));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Pembayaran Pembelian Gagal');
					redirect(site_url('report/finance/purchase_payable/'));
				}
			}
			else
			{
				$this->session->set_flashdata('error', 'Pembayaran Pembelian Gagal');
				redirect(site_url('report/finance/purchase_payable/'));
			}                
		}
		else
		{
			// if($this->session->userdata('verifypassword') == 1)
			if(true)
			{
				$this->session->unset_userdata('verifypassword');
				$header = array("title" => "Perbarui Pembayaran Pembelian");
				$footer = array("script" => ['finance/pod/update_payment_of_debt.js']);
				$pod = $this->payment->get_detail_pod(decrypt_custom($pod_id));
				$data = [
					'pod' => $pod,
					'pod_detail' => $this->payment->get_detail_pod_detail($pod['id'])
				];
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/pod/update_payment_of_debt', $data);
				$this->load->view('include/footer', $footer);			
			}                        
			else
			{
				$this->load->view('auth/show_404');
			}
		}
	}

	public function delete_payment_of_debt()
	{    
		if($this->input->is_ajax_request())
		{
			if($this->session->userdata('verifypassword') == 1)
			{
				$this->session->unset_userdata('verifypassword');
				$this->db->trans_start();
				$post = $this->input->post();
				$pod  = $this->payment->get_detail_pod(decrypt_custom($post['pod_id']));
				$pod_detail = $this->payment->get_detail_pod_detail($pod['id']);
				// GENERAL LEDGER
				$where_general_ledger = [
					'invoice'		=> $pod['code']
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
				// DELETE POD & POD DETAIL
				$this->db->delete('payment_ledger', ['id' => $pod['id']]);
				$this->db->delete('payment_ledger_detail', ['pl_id' => $pod['id']]);
				// DELETE CASH_LEDGER
				$where_cash_ledger = [
					'invoice'  => $pod['code']
				];
				$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger);
				if($cash_ledger->num_rows() > 0)
				{
					foreach($cash_ledger->result_array() AS $info_cash_ledger)
					{
						$where_after_balance = [
							'cl_type'    => $info_cash_ledger['cl_type'],
							'account_id' => $info_cash_ledger['account_id'],
							'date >='    => $info_cash_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info_after_balance)
						{
							if($info_after_balance['date'] == $info_cash_ledger['date'] && $info_after_balance['id'] < $info_cash_ledger['id'])
							{
								continue;
							}
							else
							{
								if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
								{
									$balance = $info_after_balance['balance']-$info_cash_ledger['amount'];
								}
								else
								{
									$balance = $info_after_balance['balance']+$info_cash_ledger['amount'];
								}
								$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
							}
						}
						$this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
					}
				}
				foreach($pod_detail AS $info_pod_detail)
				{
					$purchase_invoice = $this->purchase->get_detail_purchase_invoice($info_pod_detail['transaction_id']);
					$account_payable = $purchase_invoice['account_payable']+$info_pod_detail['cash']+$info_pod_detail['transfer']+$info_pod_detail['deposit'];
					$cheque_payable  = $purchase_invoice['cheque_payable'];
					if($pod['cheque_status'] != null)
					{
						$cheque_payable  = $cheque_payable-$info_pod_detail['cheque'];
					}
					$data_purchase_invoice = [
						'account_payable' => $account_payable,
						'cheque_payable'  => $cheque_payable,
						'payment_status'  => 2
					];
					$this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $purchase_invoice['id']]);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$data_activity = [
						'information' => 'MENGHAPUS PEMBAYARAN PEMBELIAN (NO. TRANSAKSI '.$pod['code'].')',
						'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];
					$this->crud->insert('activity', $data_activity);
					$this->session->set_flashdata('success', 'BERHASIL! Pembayaran Pembelian Terhapus');
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
					$this->session->set_flashdata('error', 'Mohon Maaf! Pembayaran Pembelian gagal terhapus');
					$response   =   [
						'status'    => [
							'code'      => 400,
							'message'   => 'Gagal',
						],
						'response'  => ''
					];
				}                    
			}    
			else
			{
				$this->session->set_flashdata('error', 'Mohon Maaf! Pembayaran Pembelian gagal terhapus');
				$response   =   [
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
			}        
			echo json_encode($response);
		}	
		else
		{
			$this->load->view('auth/show_404');
		}	       
	}

	public function delete_multi_payment_of_debt()
	{        
		if($this->input->method() === 'post')
		{
			if($this->session->userdata('verifypassword') == 1)
			{
				$this->session->unset_userdata('verifypassword');
				$post = $this->input->post();
				$this->db->trans_start();
				$pod = $this->payment->get_detail_pod(decrypt_custom($post['pod_id']));
				echo json_encode($pod);
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
				}
				else
				{
					$this->db->trans_rollback();
				}                

			}
			else
			{

			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));                
		}
	}

	//PAYMENT OF RECEIVABLE
	public function create_payment_of_receivable($sales_invoice_id)
	{
		if($this->system->check_access('payment/receivable', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post   = $this->input->post();
				// echo json_encode($post); die;
				$por_code   = $this->payment->por_code();  
				$sales_invoice = $this->sales->get_detail_sales_invoice($post['sales_invoice_id']);              
				$customer      = $this->crud->get_where('customer', ['code' => $sales_invoice['customer_code']])->row_array();
				$cash       = floatval(format_amount($post['cash'])); $transfer = floatval(format_amount($post['transfer'])); $cheque = floatval(format_amount($post['cheque'])); $deposit = floatval(format_amount($post['deposit']));
				$grandtotal = $cash+$transfer+$cheque+$deposit;
				$data_payment_ledger = [
					'is_multi' 		  => 0,
					'transaction_type'=> 2,
					'code'            => $por_code,
					'date'            => format_date($post['payment_date']),
					'payment'         => json_encode($post['payment']),
					'information'     => $post['information'],
					'cash_account_id' => ($cash > 0) ? $post['cash_account_id'] : null,
					'cash'            => $cash,
					'transfer_account_id' => ($transfer > 0) ? $post['transfer_account_id'] : null,
					'transfer'        => $transfer,
					'cheque_account_id'  => ($cheque > 0) ? $post['cheque_account_id'] : null,
					'cheque_number'      => ($cheque > 0) ? $post['cheque_number'] : null,
					'cheque_open_date'   => ($cheque > 0) ? format_date($post['cheque_open_date']) : null,
					'cheque_close_date'  => ($cheque > 0) ? format_date($post['cheque_close_date']) : null,
					'cheque_status'  => ($cheque > 0) ? 2 : null,
					'cheque'         => $cheque,
					'deposit'        => $deposit,
					'grandtotal'     => $grandtotal,
					'employee_code'  => $this->session->userdata('code_e')
				];
				$por_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
				if($por_id != null)
				{       
					$data_por_detail = [
						'pl_id' 		  => $por_id,
						'transaction_id'  => $sales_invoice['id'],
						'cash'            => $cash,
						'transfer'        => $transfer,
						'cheque'          => $cheque,
						'deposit'         => $deposit,
						'grandtotal'      => $grandtotal
					];
					$this->crud->insert('payment_ledger_detail', $data_por_detail);
					// GENERAL LEDGER -> PIUTANG USAHA (K)
					$where_last_balance = [
						'coa_account_code' => "10201",
						'date <='        => format_date($post['payment_date']),                    
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
					$data = [
						'coa_account_code'  => "10201",
						'date'        => format_date($post['payment_date']),
						'transaction_id' => $por_id,
						'invoice'     => $por_code,
						'information' => 'PEMBAYARAN PENJUALAN',
						'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
						'credit'      => $grandtotal,
						'balance'     => $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "10201",
							'date >'        => format_date($post['payment_date']),
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
						}
					}		
					// ---------------------------------------------
					foreach($post['payment'] AS $payment_info)
					{
						switch ($payment_info) {
							case 1:
								$from_where_last_balance = [
									'cl_type'    => 1,
									'account_id'    => $post['cash_account_id'],
									'date <='    => format_date($post['payment_date']),
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ? add_balance($from_last_balance['balance'], $cash) : add_balance(0, $cash);
								$data = [
									'cl_type'     => 1,									
									'account_id'    => $post['cash_account_id'],
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN'.$sales_invoice['invoice'].'_'.$customer['name'],
									'amount'      => $cash,
									'method'      => 1,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'       => 1,
										'account_id'    => $post['cash_account_id'],
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{                        										
										$this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $cash)], ['id' => $info['id']]);
									}                            
								}
								// GENERAL LEDGER -> KAS (D)
								$where_last_balance = [
									'coa_account_code' => "10101",
									'date <='        => format_date($post['payment_date']),                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $cash) : add_balance(0, $cash);
								$data = [
									'coa_account_code'  => "10101",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'debit'      => $cash,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "10101",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $cash)], ['id' => $info['id']]);
									}
								}
								break;
							case 2:
								$from_where_last_balance = [
									'cl_type'    => 2,
									'account_id' => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
									'date <='    => format_date($post['payment_date']),
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$transfer : 0+$transfer;
								$data = [
									'cl_type'     => 2,
									'account_id'  => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN'.$sales_invoice['invoice'].'_'.$customer['name'],
									'amount'      => $transfer,
									'method'      => 1,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'       => 2,
										'account_id'    => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $transfer)], ['id' => $info['id']]);
									}
								}
								// GENERAL LEDGER -> BANK (D)
								$where_last_balance = [
									'coa_account_code' => "10102",
									'date <='        => format_date($post['payment_date']),                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $transfer) : add_balance(0, $transfer);
								$data = [
									'coa_account_code'  => "10102",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'debit'      => $transfer,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "10102",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $transfer)], ['id' => $info['id']]);
									}
								}
								break;
							case 3:
								$data_cheque_payable = array(
									'cheque_payable' => $sales_invoice['cheque_payable']+$cheque
								);
								$this->crud->update('sales_invoice', $data_cheque_payable, ['id' => $sales_invoice['id']]);
								// GENERAL LEDGER -> PIUTANG CEK/GIRO (D)
								$where_last_balance = [
									'coa_account_code' => "10202",
									'date <='        => format_date($post['payment_date']),                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $cheque) : add_balance(0, $cheque);
								$data = [
									'coa_account_code'  => "10202",
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'debit'      => $cheque,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "10202",
										'date >'        => format_date($post['payment_date']),
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $cheque)], ['id' => $info['id']]);
									}
								}
								break;
							case 4:
								$from_where_last_balance = [
									'cl_type'    => 4,
									'account_id' => $sales_invoice['customer_code'],
									'date <='    => format_date($post['payment_date']),
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$deposit : 0-$deposit;
								$data = [
									'cl_type'     => 4,
									'account_id'  => $sales_invoice['customer_code'],
									'date'        => format_date($post['payment_date']),
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJULAN',
									'note'        => $customer['name'],
									'amount'      => $deposit,
									'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'       => 4,
										'account_id'    => $sales_invoice['customer_code'],
										'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
										'deleted'       => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$balance = $info['balance']-$deposit;
										$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
									}
								}
								break;
							default:
								break;
						}
					}
					$account_payable = $sales_invoice['account_payable']-$grandtotal;
					if($account_payable == 0)
					{
						$data_sales_invoice = array(
							'account_payable' => $account_payable,
							'payment_status'  => 1
						);
					}
					else
					{
						$data_sales_invoice = array('account_payable' => $account_payable);
					}
					if($this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $post['sales_invoice_id']]))
					{
						$this->session->set_flashdata('success', 'Pembayaran Piutang Penjualan berhasil');
						redirect(site_url('payment/receivable/detail/'.encrypt_custom($por_id)));
					}
					else
					{
						$this->session->set_flashdata('error', 'Pembayaran Piutang Penjualan gagal');
						redirect(site_url('report/finance/sales_receivable/'));
					}
				}
				else
				{
					$this->session->set_flashdata('error', 'Pembayaran Piutang Penjualan gagal');
					redirect(site_url('report/finance/sales_receivable/'));
				}                
			}
			else
			{
				$sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));
				if($sales_invoice != null)
				{
					if($sales_invoice['payment_status'] != 1)
					{                        
						if($sales_invoice['account_payable'] <= $sales_invoice['grandtotal']){
							$header = array("title" => "Pembayaran Piutang Penjualan");
							$footer = array("script" => ['finance/por/create_payment_of_receivable.js']);
							$data = array(
								'sales_invoice' => $sales_invoice
							);            
							$this->load->view('include/header', $header);        
							$this->load->view('include/menubar');
							$this->load->view('include/topbar');
							$this->load->view('finance/por/create_payment_of_receivable', $data);
							$this->load->view('include/footer', $footer);
						}
						else
						{
							$this->session->set_flashdata('error', 'Mohon maaf, tidak dapat melakukan Transaksi Pembayaran Piutang. Harap Periksa Kembali Cek/Giro yang blm dikonfirmasi');
							redirect(site_url('report/finance/payment_of_receivable'));
						}                        
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, tidak dapat melakukan Transaksi Pembayaran Piutang dikarenakan penjualan sudah LUNAS. Terima kasih');
						redirect(site_url('report/finance/sales_receivable'));
					}                    	
				}
				else
				{
					$this->load->view('auth/show_404');
				}                		
			}   
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}            
	}

	public function add_multi_payment_of_receivable()
	{
		if($this->system->check_access('payment/receivable', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post   = $this->input->post();
				if(isset($post['datatable_type']) && $post['datatable_type'] == "first")
				{
					header('Content-Type: application/json');
					$this->datatables->select('sales_invoice.id AS id, sales_invoice.id AS choose, sales_invoice.date, sales_invoice.invoice, sales_invoice.payment, sales_invoice.due_date, DATEDIFF(sales_invoice.due_date, CURRENT_DATE()) AS remaining_time, sales_invoice.grandtotal, (sales_invoice.account_payable-sales_invoice.cheque_payable) AS account_payable, customer.name AS name_c, employee.name AS name_s, sales_invoice.payment_status');
					$this->datatables->from('sales_invoice');
					$this->datatables->join('employee', 'employee.code = sales_invoice.sales_code');
					$this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
					$this->datatables->where('sales_invoice.do_status', 1);
					$this->datatables->where('sales_invoice.deleted', 0);
					$this->datatables->where('sales_invoice.payment', 2);
					$this->datatables->where('sales_invoice.payment_status !=', 1);
					$this->datatables->where('(sales_invoice.account_payable-sales_invoice.cheque_payable) >', 0);
					$this->datatables->where('sales_invoice.customer_code', $post['customer_code']);
					$this->datatables->group_by('sales_invoice.id');	
					$this->datatables->add_column('choose',
					'			
						<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
						<input type="checkbox" name="sales_invoice_id[]" value="$1" class="choose">&nbsp;<span></span>
						</label>
					', 'id');	
					$this->datatables->add_column('invoice', 
					'
						<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>
					', 'encrypt_custom(id),invoice');
					echo $this->datatables->generate();
				}
				elseif(isset($post['post_type']) && $post['post_type'] == "second_form")
				{
					// echo json_encode($post);                    
					$cash = format_amount($post['cash']); $transfer = format_amount($post['transfer']); $cheque = format_amount($post['cheque']); $deposit = format_amount($post['deposit']);
					foreach($post['sales_invoice'] AS $info)
					{
						$code = $this->payment->por_code();
						$sales_invoice = $this->sales->get_detail_sales_invoice($info['sales_invoice_id']);                        
						$customer = $this->crud->get_where('customer', ['code' => $sales_invoice['customer_code']])->row_array();
						$pay_cash = 0; $pay_transfer = 0; $pay_cheque = 0; $pay_deposit = 0;
						$total_pay = format_amount($info['pay']); $payment_option = [];
						foreach($post['payment'] AS $payment_info)
						{
							if($total_pay > 0)
							{
								switch ($payment_info){
									case 1:
										if($cash >= $total_pay)
										{                                        
											$pay_cash = $total_pay;
											$cash = $cash - $pay_cash; $total_pay = $total_pay - $pay_cash;
											$payment_option[] = 1;
										}
										elseif($cash > 0)
										{
											$pay_cash = $cash;
											$cash = $cash - $pay_cash; $total_pay = $total_pay - $pay_cash;
											$payment_option[] = 1;
										}                                    
										break;
									case 2:
										if($transfer >= $total_pay)
										{                                        
											$pay_transfer = $total_pay;
											$transfer = $transfer - $pay_transfer; $total_pay = $total_pay - $pay_transfer;
											$payment_option[] = 2;
										}
										elseif($transfer > 0)
										{                                        
											$pay_transfer = $transfer;
											$transfer = $transfer - $pay_transfer; $total_pay = $total_pay - $pay_transfer;
											$payment_option[] = 2;
										}                                    
										break;
									case 3:
										if($cheque >= $total_pay)
										{                                        
											$pay_cheque = $total_pay;
											$cheque = $cheque - $pay_cheque; $total_pay = $total_pay - $pay_cheque;
											$payment_option[] = 3;
										}
										elseif($cheque > 0)
										{
											$pay_cheque = $cheque;
											$cheque = $cheque - $pay_cheque; $total_pay = $total_pay - $pay_cheque;
											$payment_option[] = 3;
										}
										break;
									case 4:                                    
										if($deposit >= $total_pay)
										{                                        
											$pay_deposit = $total_pay;
											$deposit = $deposit - $pay_deposit; $total_pay = $total_pay - $pay_deposit;
											$payment_option[] = 4;
										}
										elseif($deposit > 0)
										{
											$pay_deposit = $deposit;
											$deposit = $deposit - $pay_deposit; $total_pay = $total_pay - $pay_deposit;										
											$payment_option[] = 4;
										}
										break;
									default:
										break;
								}
							}
							else
							{
								continue;
							}                            
						}
						$data_payment_ledger = array(
							'transaction_type'=> 2, //1:PURCHASE, 2:SALES INVOICE
							'transaction_id'  => $info['sales_invoice_id'],
							'code'            => $code,
							'date'            => format_date($post['payment_date']),
							'payment'         => json_encode($payment_option),
							'from_cl_type'    => ($post['from_cl_type'] != "" && $pay_cash > 0) ? $post['from_cl_type'] : null,
							'from_account_id' => ($post['from_account_id'] != "" && $pay_cash > 0) ? $post['from_account_id'] : null,
							'cash'            => $pay_cash,
							'transfer_account_id'=> ($post['transfer_account_id'] != "" && $pay_transfer > 0) ? $post['transfer_account_id'] : null,
							'transfer'        => $pay_transfer,
							'cheque_account_id'  => ($post['cheque_account_id'] != "" && $pay_cheque > 0) ? $post['cheque_account_id'] : null,
							'cheque_number'   => ($post['cheque_number'] && $pay_cheque > 0) ? $post['cheque_number'] : null,
							'cheque_open_date'  => ($post['cheque_open_date'] != "" && $pay_cheque > 0) ? date('Y-m-d', strtotime($post['cheque_open_date'])) : null,
							'cheque_close_date' => ($post['cheque_close_date'] != "" && $pay_cheque > 0) ? date('Y-m-d', strtotime($post['cheque_close_date'])) : null,
							'cheque_status'  => ($post['cheque_account_id'] != "" && $pay_cheque > 0) ? 2 : null,
							'cheque'         => $pay_cheque,
							'deposit'   => $pay_deposit,
							'grandtotal'     => $pay_cash+$pay_transfer+$pay_cheque+$pay_deposit,
							'employee_code'  => $this->session->userdata('code_e')
						);
						$por_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
						foreach($post['payment'] AS $payment_info)
						{
							switch ($payment_info) {
								case 1:
									if($pay_cash > 0)
									{
										$from_where_last_balance = [
											'cl_type'    => $post['from_cl_type'],
											'account_id' => $post['from_account_id'],
											'date <='    => format_date($post['payment_date']),
											'deleted'    => 0
										];
										$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
										$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$pay_cash : 0+$pay_cash;
										$data = [
											'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
											'transaction_type' => 5,
											'account_id'  => $post['from_account_id'],
											'date'        => format_date($post['payment_date']),
											'invoice'     => $code,
											'information' => 'PEMBAYARAN PIUTANG PENJULAN (TUNAI)',
											'note'        => $customer['name'],
											'amount'      => $pay_cash,
											'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
											'balance'     => $from_balance
										];
										$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
										if($from_cl_id)
										{
											$from_where_after_balance = [
												'cl_type'       => $post['from_cl_type'],
												'account_id'    => $post['from_account_id'],
												'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
												'deleted'       => 0
											];                    
											$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
											foreach($from_after_balance  AS $info)
											{                        
												$balance = $info['balance'] + $pay_cash;
												$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
											}                            
										}
									}
									break;                                    
								case 2:
									if($pay_transfer > 0)
									{
										$from_where_last_balance = [
											'cl_type'    => 3,
											'account_id' => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
											'date <='    => format_date($post['payment_date']),
											'deleted'    => 0
										];
										$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
										$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$pay_transfer : 0+$pay_transfer;
										$data = [
											'cl_type'     => 3, //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
											'transaction_type' => 5,
											'account_id'  => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
											'date'        => format_date($post['payment_date']),
											'invoice'     => $code,
											'information' => 'PEMBAYARAN PIUTANG PENJUALAN (TRANSFER)',
											'note'        => $customer['name'],
											'amount'      => $pay_transfer,
											'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
											'balance'     => $from_balance
										];
										$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
										if($from_cl_id)
										{
											$from_where_after_balance = [
												'cl_type'       => 3,
												'account_id'    => ($post['transfer_account_id'] != "") ? $post['transfer_account_id'] : null,
												'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
												'deleted'       => 0
											];                    
											$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
											foreach($from_after_balance  AS $info)
											{
												$balance = $info['balance'] + $pay_transfer;
												$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
											}
										}
									}                                    
									break;
								case 3:
									if($pay_cheque > 0)
									{
										$data_cheque_payable = array(
											'cheque_payable' => $sales_invoice['cheque_payable']+(int)$pay_cheque
										);
										$this->crud->update('sales_invoice', $data_cheque_payable, ['id' => $sales_invoice['id']]);
									}
									break;
								case 4:
									if($pay_deposit > 0)
									{
										$from_where_last_balance = [
											'cl_type'    => 5, //	1:BIG CASH, 2:SMALL CASH, 3:BANK CASH, 4:SUPPLIER DEPOSIT, 5:CUSTOMER DEPOSIT
											'account_id' => $sales_invoice['customer_code'],
											'date <='    => format_date($post['payment_date']),
											'deleted'    => 0
										];
										$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
										$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$pay_deposit : 0-$pay_deposit;
										$data = [
											'cl_type'     => 5, //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH, 4:CUSTOMER'S DEPOSIT
											'transaction_type' => 5,
											'account_id'  => $sales_invoice['customer_code'],
											'date'        => format_date($post['payment_date']),
											'invoice'     => $code,
											'information' => 'PEMBAYARAN PIUTANG PENJUALAN (DEPOSIT)',
											'note'        => $customer['name'],
											'amount'      => $pay_deposit,
											'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
											'balance'     => $from_balance
										];
										$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
										if($from_cl_id)
										{
											$from_where_after_balance = [
												'cl_type'       => 5,
												'account_id'    => $sales_invoice['customer_code'],
												'date >'        => date('Y-m-d', strtotime($post['payment_date'])),
												'deleted'       => 0
											];                    
											$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
											foreach($from_after_balance  AS $info)
											{
												$balance = $info['balance']-$pay_deposit;
												$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
											}
										}
									}                                    
									break;
								default:
									break;
							}
						}
						$account_payable = $sales_invoice['account_payable']-(int)$pay_cash-(int)$pay_transfer-(int)$pay_deposit;
						if($account_payable == 0)
						{
							$data_sales_invoice = array(
								'account_payable' => $account_payable,
								'payment_status'  => 1
							);
						}
						else
						{
							$data_sales_invoice = array('account_payable' => $account_payable);
						}
						$this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
					}
					
					$this->session->set_flashdata('success', 'Pembayaran Piutang Penjualan berhasil');
					redirect(site_url('report/finance/payment_of_receivable/'));
				}
				else                        
				{
					$customer = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
					$sales_invoice = $this->db->select('sales_invoice.id AS id, sales_invoice.id AS choose, sales_invoice.date, sales_invoice.invoice, sales_invoice.payment, sales_invoice.due_date, DATEDIFF(sales_invoice.due_date, CURRENT_DATE()) AS remaining_time, sales_invoice.grandtotal, (sales_invoice.account_payable-sales_invoice.cheque_payable) AS account_payable, customer.name AS name_c, employee.name AS name_s, sales_invoice.payment_status')
									 ->from('sales_invoice')
									 ->join('employee', 'employee.code = sales_invoice.sales_code')
									 ->join('customer', 'customer.code = sales_invoice.customer_code')
									 ->where('sales_invoice.do_status', 1)
									 ->where('sales_invoice.deleted', 0)
									 ->where('sales_invoice.payment', 2)
									 ->where('sales_invoice.payment_status !=', 1)
									 ->where('sales_invoice.customer_code', $post['customer_code'])
									 ->where_in('sales_invoice.id', $post['sales_invoice_id'])
									 ->group_by('sales_invoice.id')->get()->result_array();                                     
					$header = array("title" => "Pembayaran (Banyak Transaksi) Piutang Penjualan");
					$footer = array("script" => ['finance/por/create_multi_payment_of_receivable.js']);
					$data = [
						'post_type' => 'second_form',
						'customer' => $customer,
						'sales_invoice' => $sales_invoice                        
					];
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('finance/por/create_multi_payment_of_receivable', $data);
					$this->load->view('include/footer', $footer);                		
				}         
			}
			else
			{
				$header = array("title" => "Pembayaran (Banyak Transaksi) Piutang Penjualan");
				$footer = array("script" => ['finance/por/create_multi_payment_of_receivable.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/por/create_multi_payment_of_receivable');
				$this->load->view('include/footer', $footer);                		
			}   
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}            
	}

	public function detail_payment_of_receivable($por_id)
	{
		if($this->system->check_access('payment/receivable', 'detail'))
		{
			$por = $this->payment->get_detail_por(decrypt_custom($por_id));
			if($por != null)
			{
				$header = array("title" => "Detail Pembayaran Penjualan");
				$footer = array("script" => ['finance/payment/detail_payment_of_receivable.js']);
				$data = array(
					'por'         => $por,
					'por_detail'  => $this->payment->get_detail_por_detail($por['id'])
				);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/por/detail_payment_of_receivable', $data);
				$this->load->view('include/footer', $footer);                
			}
			else
			{
				$this->load->view('auth/show_404');
			}                               
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}        
	}

	public function add_cheque_por_acquittance()
	{
		if($this->system->check_access('payment/receivable', 'create'))
		{
			if($this->input->is_ajax_request())
			{
				if($this->input->method() === 'post')
				{
					$post = $this->input->post();                    
					$por = $this->payment->get_detail_por(decrypt_custom($post['por_id']));
					$data = [
						'cheque_acquittance_date' => date('Y-m-d', strtotime($post['cheque_acquittance_date'])),
						'cheque_status'           => 1
					];                
					if($this->crud->update('payment_ledger', $data, ['cheque_number' => $por['cheque_number']]))
					{                           
						$pors = $this->crud->get_where('payment_ledger', ['cheque_number' => $por['cheque_number']])->result_array();
						foreach($pors AS $info)
						{
							$from_where_last_balance = [
								'cl_type'    => 3,
								'account_id' => $info['cheque_account_id'],
								'date <='    => $info['date'],
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$info['cheque'] : 0+$info['cheque'];
							$data = [
								'cl_type'     => 3, //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
								'transaction_type' => 5,
								'account_id'  => $info['cheque_account_id'],
								'date'        => $info['date'],
								'invoice'     => $info['code'],
								'information' => 'PEMBAYARAN PIUTANG PENJUALAN (CEK/GIRO)',
								'amount'      => $info['cheque'],
								'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'       => 3,
									'account_id'    => $info['cheque_account_id'],
									'date >'        => $info['date'],
									'deleted'       => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{
									$balance = $info['balance']+$info['cheque'];
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}
							}
							$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $info['transaction_id']])->row_array();
							$account_payable = $sales_invoice['account_payable']-$info['cheque'];
							$cheque_payable = $sales_invoice['cheque_payable']-$info['cheque'];
							if($account_payable == 0)
							{
								$data_sales_invoice = array(
									'account_payable' => $account_payable,
									'cheque_payable'  => $cheque_payable,
									'payment_status'  => 1
								);
							}
							else
							{
								$data_sales_invoice = array(
									'account_payable' => $account_payable,
									'cheque_payable' => $cheque_payable
								);
							} 
							$this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
						}                        
						$response = [
							'status' => [
								'code'      => 200,
								'message'   => 'Berhasil Menambahkan Data',
							],
							'response'  => ''
						];         
						$this->session->set_flashdata('success', 'BERHASIL! Pelunasan Cek/Giro berhasil');
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
						$this->session->set_flashdata('error', 'Mohon maaf, Pelunasan Cek/Giro gagal');
					}      
				}
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Mohon maaf, terjadi kesalahan. Terima kasih',
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
						'message'   => 'Mohon maaf, terjadi kesalahan. Terima kasih',
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
					'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
				],
				'response'  => ''
			]; 
		}
		echo json_encode($response);
	}

	public function cancel_cheque()
	{
		if($this->input->is_ajax_request())
		{            
			$post = $this->input->post();
			$data_payment_ledger = [
				'cheque_status' => 3
			];
			$payment_ledger = $this->crud->get_where('payment_ledger', ['id' => $post['pl_id']])->row_array();
			if($this->crud->update('payment_ledger', $data_payment_ledger, ['cheque_number' => $payment_ledger['cheque_number']]))
			{
				$data_cheque = $this->crud->get_where('payment_ledger', ['cheque_number' => $payment_ledger['cheque_number']])->result_array();
				foreach($data_cheque AS $info)
				{
					if($info['transaction_type'] == 1)
					{
						$purchase_invoice = $this->crud->get_where('purchase_invoice', ['id' => $info['transaction_id']])->row_array();
						$data_purchase_invoice = [
							'cheque_payable' => $purchase_invoice['cheque_payable']-$info['cheque']
						];
						$this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $purchase_invoice['id']]);
					}
					else if($info['transaction_type'] == 2)
					{
						$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $info['transaction_id']])->row_array();
						$data_sales_invoice = [
							'cheque_payable' => $sales_invoice['cheque_payable']-$info['cheque']
						];
						$this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
					}
				}                
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Berhasil Menambahkan Data',
					],
					'response'  => ''
				];
				$this->session->set_flashdata('success', 'BERHASIL! Penolakan Cek/Giro berhasil');
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
				$this->session->set_flashdata('error', 'Mohon maaf, Pennolakan Cek/Giro gagal');                
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function cancel_move_cheque()
	{
		if($this->input->is_ajax_request())
		{            
			$post = $this->input->post();
			$data = [                
				'move_cheque_status' => 3
			];
			$pod = $this->payment->get_detail_pod($post['pl_id']);
			if($this->crud->update('payment_ledger', $data, ['move_cheque_number' => $pod['move_cheque_number']])){
				$pods = $this->crud->get_where('payment_ledger', ['move_cheque_number' => $pod['move_cheque_number']])->result_array();
				foreach($pods AS $info_pod)
				{
					$purchase = $this->crud->get_where('purchase_invoice', ['id' => $info_pod['transaction_id']])->row_array();                    
					$cheque_payable = $purchase['cheque_payable']-$info_pod['move_cheque'];                    
					$data_purchase = array(                            
						'cheque_payable'  => $cheque_payable
					);                    
					$this->crud->update('purchase_invoice', $data_purchase, ['id' => $purchase['id']]);
				}

				$update_por = [                    
					'cheque_status' => 3
				];
				$pors = $this->crud->get_where('payment_ledger', ['cheque_number' => $pod['move_cheque_number']])->result_array();
				foreach($pors AS $info_por)
				{                            
					$this->crud->update('payment_ledger', $update_por, ['id' => $info_por['id']]);
					$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $info_por['transaction_id']])->row_array();                    
					$cheque_payable = $sales_invoice['cheque_payable']-$info_por['cheque'];                    
					$data_sales_invoice = array(
						'cheque_payable'  => $cheque_payable
					);                    
					$this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
				}                                                                        
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Berhasil Menambahkan Data',
					],
					'response'  => ''
				];         
				$this->session->set_flashdata('success', 'BERHASIL! Penolakan Oper Cek/Giro berhasil');
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
				$this->session->set_flashdata('error', 'Mohon maaf, Penolakan Oper Cek/Giro gagal');
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function delete_payment_of_receivable()
	{    
		if($this->input->is_ajax_request())
		{
			if($this->session->userdata('verifypassword') == 1)
			{
				$this->session->unset_userdata('verifypassword');
				$this->db->trans_start();
				$post = $this->input->post();
				$por  = $this->payment->get_detail_pod(decrypt_custom($post['por_id']));
				$por_detail = $this->payment->get_detail_pod_detail($por['id']);
				// GENERAL LEDGER
				$where_general_ledger = [
					'invoice'		=> $por['code']
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
								if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
								{
									$balance = $info_after_balance['balance']-$info_general_ledger['debit'];
								}
								elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
								{
									$balance = $info_after_balance['balance']+$info_general_ledger['credit'];
								}
								$this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
							}
						}
						$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
					}
				}
				// DELETE POD & POD DETAIL
				$this->db->delete('payment_ledger', ['id' => $por['id']]);
				$this->db->delete('payment_ledger_detail', ['pl_id' => $por['id']]);				
				foreach($por_detail AS $info_pod_detail)
				{
					$sales_invoice = $this->purchase->sales_invoice($info_pod_detail['transaction_id']);
					// DELETE CASH_LEDGER
					$where_cash_ledger = [
						'invoice'		=> $sales_invoice['invoice']
					];
					$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger);
					if($cash_ledger->num_rows() > 0)
					{
						foreach($cash_ledger->result_array() AS $info_cash_ledger)
						{
							$where_after_balance = [
								'cl_type'    => $info_cash_ledger['cl_type'],
								'account_id' => $info_cash_ledger['account_id'],
								'date >='    => $info_cash_ledger['date'],
								'deleted'    => 0
							];
							$after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance AS $info_after_balance)
							{
								if($info_after_balance['date'] == $info_cash_ledger['date'] && $info_after_balance['id'] < $info_cash_ledger['id'])
								{
									continue;
								}
								else
								{
									if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
									{
										$balance = $info_after_balance['balance']-$info_cash_ledger['amount'];
									}
									else
									{
										$balance = $info_after_balance['balance']+$info_cash_ledger['amount'];
									}
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
								}
							}
							$this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
						}
					}
					$account_payable = $sales_invoice['account_payable']+$info_pod_detail['cash']+$info_pod_detail['transfer']+$info_pod_detail['deposit'];
					$cheque_payable  = $sales_invoice['cheque_payable'];
					if($por['cheque_status'] != null)
					{
						switch ($por['cheque_status']) {
							case 1:
								$account_payable = $account_payable+$info_pod_detail['cheque'];
								$cheque_payable  = $cheque_payable-$info_pod_detail['cheque'];
							  break;
							case 2:
								$cheque_payable  = $cheque_payable - $info_pod_detail['cheque'];
							  break;
						  }
					}
					$sales_invoice = [
						'account_payable' => $account_payable,
						'cheque_payable'  => $cheque_payable,
						'payment_status'  => 2
					];
					$this->crud->update('sales_invoice', $sales_invoice, ['id' => $sales_invoice['id']]);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$data_activity = [
						'information' => 'MENGHAPUS PEMBAYARAN PEMBELIAN (NO. TRANSAKSI '.$por['code'].')',
						'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];
					$this->crud->insert('activity', $data_activity);
					$this->session->set_flashdata('success', 'BERHASIL! Pembayaran Pembelian Terhapus');
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
					$this->session->set_flashdata('error', 'Mohon Maaf! Pembayaran Pembelian gagal terhapus');
					$response   =   [
						'status'    => [
							'code'      => 400,
							'message'   => 'Gagal',
						],
						'response'  => ''
					];
				}                    
			}    
			else
			{
				$this->session->set_flashdata('error', 'Mohon Maaf! Pembayaran Pembelian gagal terhapus');
				$response   =   [
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
			}        
			echo json_encode($response);
		}	
		else
		{
			$this->load->view('auth/show_404');
		}	       
	}
}