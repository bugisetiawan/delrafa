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
    
    public function get_transaction($transaction_type, $account_code)
    {
        if($this->input->is_ajax_request())
		{
            if($transaction_type == 1) //PURCHASE INVOICE
            {                
                $search = urldecode($this->uri->segment(6));
                $transaction = $this->payment->get_transaction($transaction_type, $account_code, $search);
                $response = [];
                if($transaction->num_rows() > 0){
                    foreach($transaction->result_array() as $info)
                    {
                        $response[] = array(
                            'id'         => $info['id'],
                            'date'       => date('d-m-Y', strtotime($info['date'])),
                            'code'       => $info['code'],
                            'invoice'    => $info['invoice'],
                            'grandtotal' => number_format($info['grandtotal'], 2, '.', ','),
                            'account_payable' => number_format($info['account_payable'], 2, '.', ',')
                        );
                    }
                }                
            }
            elseif($transaction_type == 2) //SALES INVOICE
            {
				$search = urldecode($this->uri->segment(6));
                $transaction = $this->payment->get_transaction($transaction_type, $account_code, $search);
                $response = [];
                if($transaction->num_rows() > 0){
                    foreach($transaction->result_array() as $info)
                    {
                        $response[] = array(
                            'id'         => $info['id'],
                            'date'       => date('d-m-Y', strtotime($info['date'])),
                            'invoice'    => $info['invoice'],
                            'grandtotal' => number_format($info['grandtotal'], 2, '.', ','),
                            'account_payable' => number_format($info['account_payable'], 2, '.', ',')
                        );
                    }
                }                 
            }
            echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

	// POD
    public function payment_of_debt()
    {
        if($this->system->check_access('payment/debt', 'read'))
        {
            if($this->input->is_ajax_request())
            {                
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('pod.id AS id_pod, pod.code AS code_pod, pod.date, pod.method, pod.grandtotal,
								supplier.name AS supplier,
								pod.code AS search_code_pod');
				$this->datatables->from('payment_ledger AS pod');
				$this->datatables->join('payment_ledger_transaction AS pod_transaction', 'pod_transaction.pl_id = pod.id');
				$this->datatables->join('purchase_invoice AS pi', 'pi.id = pod_transaction.transaction_id');
				$this->datatables->join('supplier', 'supplier.code = pi.supplier_code');
				$this->datatables->where('pod.transaction_type', 1);
				$this->datatables->where('pod.deleted', 0);
				// $this->datatables->where('DATE(pod.date) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
				// $this->datatables->where('DATE(pod.date) <=', date('Y-m-d'));
				$this->datatables->group_by('pod.id');
				$this->datatables->add_column('code_pod',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_pod), code_pod');
				echo $this->datatables->generate();                
            }
            else
            {
                $header = array("title" => "Daftar Pembayaran Pembelian");
                $footer = array("script" => ['finance/pod/payment_of_debt.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('finance/pod/payment_of_debt');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function create_payment_of_debt($purchase_invoice_id = null)
    {
        if($this->system->check_access('payment/debt', 'create'))
        {
            if($this->input->method() === 'post')
            {
                $post = $this->input->post();
                $code = $this->payment->pod_code();
                $date = format_date($post['date']);
                if($purchase_invoice_id != null)
                {
                    $purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_invoice_id));                    
                    $supplier = $this->crud->get_where('supplier', ['code' => $purchase_invoice['supplier_code']])->row_array();
                    $payment_method=[]; $cash=0; $transfer=0; $cheque=0; $deposit=0; $move_cheque=0; $cost=(format_amount($post['cost']) > 0) ? format_amount($post['cost']) : 0;
                    $grandtotal=$cost;
                    // PAYMENT LEDGER
                    $data_payment_ledger = [                        
                        'transaction_type'=> 1,
                        'code'           => $code,
                        'date'           => $date,
                        'information'    => $post['information'],
                        'cost'           => $cost,
                        'employee_code'  => $this->session->userdata('code_e')
                    ];                
                    $pl_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
                    // PAYMENT LEDGER DETAIL
                    for($i=0;$i<sizeof($post['payment_method']);$i++)
                    {
                        if(!in_array($post['payment_method'][$i], $payment_method))
                        {
                            $payment_method[] = $post['payment_method'][$i];
                        }
                        $payment_pay = floatval(format_amount($post['payment_pay'][$i]));
                        $grandtotal = $grandtotal+$payment_pay;
                        $data_payment_ledger_detail = [
                            'pl_id' => $pl_id,
                            'method' 	 => json_encode([$post['payment_method'][$i]]),
                            'account_id' => $post['account_id'][$i],
                            'cheque_number' => ($post['cheque_number'][$i] != "") ? $post['cheque_number'][$i] : NULL,
                            'cheque_open_date' => ($post['cheque_open_date'][$i] != "") ? format_date($post['cheque_open_date'][$i]) : NULL,
                            'cheque_close_date' => ($post['cheque_close_date'][$i] != "") ? format_date($post['cheque_close_date'][$i]) : NULL,
                            'cheque_status'     => ($post['cheque_number'][$i] != "") ? 2 : NULL,
                            'amount' => $payment_pay
                        ];
                        $pld_id = $this->crud->insert('payment_ledger_detail', $data_payment_ledger_detail);                        
                        switch($post['payment_method'][$i]){
                            case 1:                                
                                $cash=$cash+$payment_pay;
                                // CASH LEDGER
                                $from_where_last_balance = [
									'cl_type'    => 1,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$payment_pay : 0-$payment_pay;
								$data = [
									'cl_type'     => 1,
									'account_id'  => $post['account_id'][$i],
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 1,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> KAS (K)
								$where_last_balance = [
									'coa_account_code' => "10101",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10101",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10101",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 2:								                              
                                $transfer=$transfer+$payment_pay;
                                // CASH LEDGER
                                $from_where_last_balance = [
									'cl_type'    => 2,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$payment_pay : 0-$payment_pay;
								$data = [
									'cl_type'     => 2,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 2,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> BANK (K)
								$where_last_balance = [
									'coa_account_code' => "10102",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10102",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10102",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 3:                                
                                $cheque=$cheque+$payment_pay;
                                $data_cheque_payable = ['cheque_payable' => $purchase_invoice['cheque_payable']+$payment_pay];
								$this->crud->update('purchase_invoice', $data_cheque_payable, ['id' => $purchase_invoice['id']]);
								// GENERAL_LEDGER -> HUTANG CEK/GIRO (K)
								$where_last_balance = [
									'coa_account_code' => "20102",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "20102",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "20102",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
							case 4:
								$deposit=$deposit+$payment_pay;
								// CASH LEDGER -> SUPPLIER DEPOSIT
                                $from_where_last_balance = [
									'cl_type'    => 3,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [
									'cl_type'     => 3,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 3,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> UANG MUKA PEMBELIAN (C)
								$where_last_balance = [
									'coa_account_code' => "10401",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10401",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'       => $payment_pay,
									'balance'     => $balance
								];
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10401",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
                            case 5:
                                $move_cheque=$move_cheque+$payment_pay;
								break;
							default:
								break;
						}                        
                    }
                    $update_data_payment_ledger = [
                        'method'   	=> json_encode($payment_method),
                        'cash'     	=> $cash,
                        'transfer' 	=> $transfer,
                        'cheque' 	=> $cheque,
                        'deposit' 	=> $deposit,
                        'move_cheque' => $move_cheque,
                        'grandtotal'  => $grandtotal
                    ];
                    $this->crud->update('payment_ledger', $update_data_payment_ledger, ['id' => $pl_id]);
                    // PAYMENT LEDGER TRANSACTION
                    $transaction_pay = floatval(format_amount($post['transaction_pay']));
                    $data_payment_ledger_transaction = [
                        'pl_id'        	 => $pl_id,
						'transaction_id' => $purchase_invoice['id'],
						'cash'     		=> $cash,
                        'transfer' 		=> $transfer,
                        'cheque' 		=> $cheque,
                        'deposit' 		=> $deposit,
                        'move_cheque' 	=> $move_cheque,
                        'amount'        => $transaction_pay
                    ];
                    $this->crud->insert('payment_ledger_transaction', $data_payment_ledger_transaction);
                    $account_payable = $purchase_invoice['account_payable']-$transaction_pay;
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
                    // GENERAL LEDGER -> HUTANG USAHA (D)
					$where_last_balance = [
						'coa_account_code' => "20101",
						'date <='        => $date,                    
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $transaction_pay) : sub_balance(0, $transaction_pay);
					$data = [
						'coa_account_code'  => "20101",
						'date'        => $date,
						'transaction_id' => $pl_id,
						'invoice'     => $code,
						'information' => 'PEMBAYARAN PEMBELIAN',
						'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
						'debit'       => $transaction_pay,
						'balance'     => $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "20101",
							'date >'        => $date,
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $transaction_pay)], ['id' => $info['id']]);
						}
                    }
                    if($cost > 0)
                    {
                        // GENERAL LEDGER -> BIAYA (D)
                        $where_last_balance = [
                            'coa_account_code' => "50123",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $cost) : add_balance(0, $cost);
                        $data = [
                            'coa_account_code'  => "50123",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PEMBELIAN',
                            'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
                            'debit'       => $cost,
                            'balance'     => $balance
                        ];									
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "50123",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $cost)], ['id' => $info['id']]);
                            }
                        }
                    }
                    $this->session->set_flashdata('success', 'BERHASIL! Data Pembayaran berhasil ditambahkan');
                    redirect(site_url('payment/debt/detail/'.encrypt_custom($pl_id)));
                }
                else
                {
                    $supplier = $this->crud->get_where('supplier', ['code' => $post['supplier_code']])->row_array();
                    $payment_method=[]; $cash=0; $transfer=0; $cheque=0; $deposit=0; $move_cheque=0; $cost=(format_amount($post['cost']) > 0) ? format_amount($post['cost']) : 0;
                    $grandtotal=$cost;
                    // PAYMENT LEDGER
                    $data_payment_ledger = [                        
                        'transaction_type'=> 1,
                        'code'           => $code,
                        'date'           => $date,
                        'information'    => $post['information'],
                        'cost'           => $cost,
                        'employee_code'  => $this->session->userdata('code_e')
                    ];                
                    $pl_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
                    // PAYMENT LEDGER DETAIL
                    for($i=0;$i<sizeof($post['payment_method']);$i++)
                    {
                        if(!in_array($post['payment_method'][$i], $payment_method))
                        {
                            $payment_method[] = $post['payment_method'][$i];
                        }
                        $payment_pay = floatval(format_amount($post['payment_pay'][$i]));
                        $grandtotal  = $grandtotal+$payment_pay;
                        $data_payment_ledger_detail = [
                            'pl_id' 	 => $pl_id,
                            'method' 	 => json_encode([$post['payment_method'][$i]]),
                            'account_id' => $post['account_id'][$i],
                            'cheque_number' 	=> ($post['cheque_number'][$i] != "") ? $post['cheque_number'][$i] : NULL,
                            'cheque_open_date' 	=> ($post['cheque_open_date'][$i] != "") ? format_date($post['cheque_open_date'][$i]) : NULL,
                            'cheque_close_date' => ($post['cheque_close_date'][$i] != "") ? format_date($post['cheque_close_date'][$i]) : NULL,
                            'cheque_status'     => ($post['cheque_number'][$i] != "") ? 2 : NULL,
                            'amount' => $payment_pay
                        ];
                        $pld_id = $this->crud->insert('payment_ledger_detail', $data_payment_ledger_detail);                        
                        switch($post['payment_method'][$i]){
                            case 1:                                
                                $cash=$cash+$payment_pay;
                                // CASH LEDGER
                                $from_where_last_balance = [
									'cl_type'    => 1,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$payment_pay : 0-$payment_pay;
								$data = [
									'cl_type'     => 1,
									'account_id'  => $post['account_id'][$i],
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 1,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> KAS (K)
								$where_last_balance = [
									'coa_account_code' => "10101",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10101",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10101",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 2:								                              
                                $transfer=$transfer+$payment_pay;
                                // CASH LEDGER
                                $from_where_last_balance = [
									'cl_type'    => 2,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$payment_pay : 0-$payment_pay;
								$data = [
									'cl_type'     => 2,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 2,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> BANK (K)
								$where_last_balance = [
									'coa_account_code' => "10102",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10102",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10102",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 3:            
                                $cheque=$cheque+$payment_pay;                    
								// GENERAL_LEDGER -> HUTANG CEK/GIRO (K)
								$where_last_balance = [
									'coa_account_code' => "20102",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "20102",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "20102",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
							case 4:
								$deposit=$deposit+$payment_pay;
								// CASH LEDGER -> SUPPLIER DEPOSIT
                                $from_where_last_balance = [
									'cl_type'    => 3,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [
									'cl_type'     => 3,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 3,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> UANG MUKA PEMBELIAN (C)
								$where_last_balance = [
									'coa_account_code' => "10401",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10401",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PEMBELIAN',
									'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
									'credit'       => $payment_pay,
									'balance'     => $balance
								];
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10401",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
                            case 5:
                                $move_cheque=$move_cheque+$payment_pay;
								break;
							default:
								break;
						}                        
                    }
                    $update_data_payment_ledger = [
                        'method' => json_encode($payment_method),
                        'cash' => $cash,
                        'transfer' => $transfer,
                        'cheque' => $cheque,
                        'deposit' => $deposit,
                        'move_cheque' => $move_cheque,
                        'grandtotal' => $grandtotal
                    ];
                    $this->crud->update('payment_ledger', $update_data_payment_ledger, ['id' => $pl_id]);
                    // PAYMENT_LEDGER_TRANSACTION
                    foreach($post['transaction'] AS $info_transaction)
                    {
                        $purchase_invoice = $this->purchase->get_detail_purchase_invoice($info_transaction['transaction_id']);
						$transaction_pay  = format_amount($info_transaction['transaction_pay']);
						$pay_cash = 0; $pay_transfer = 0; $pay_cheque = 0; $pay_deposit = 0; $pay_move_cheque = 0;
						if($cash > 0 && $transaction_pay > 0)
                        {
                            if($cash >= $transaction_pay)
                            {                                        
                                $pay_cash = $transaction_pay;
                                $cash = $cash - $pay_cash; $transaction_pay = $transaction_pay - $pay_cash;
                            }
                            else
                            {
                                $pay_cash = $cash;
                                $cash = $cash - $pay_cash; $transaction_pay = $transaction_pay - $pay_cash;
                            }                            
						}
						if($transfer > 0 && $transaction_pay > 0)
                        {
                            if($transfer >= $transaction_pay)
                            {                                        
                                $pay_transfer = $transaction_pay;
                                $transfer = $transfer - $pay_transfer; $transaction_pay = $transaction_pay - $pay_transfer;
                            }
                            else
                            {
                                $pay_cheque = $cheque;
                                $cheque = $cheque - $pay_cheque; $transaction_pay = $transaction_pay - $pay_cheque;
                            }
                        }
						if($cheque > 0 && $transaction_pay > 0)
                        {
                            if($cheque >= $transaction_pay)
                            {                                        
                                $pay_cheque = $transaction_pay;
                                $cheque = $cheque - $pay_cheque; $transaction_pay = $transaction_pay - $pay_cheque;
                            }
                            else
                            {
                                $pay_cheque = $cheque;
                                $cheque = $cheque - $pay_cheque; $transaction_pay = $transaction_pay - $pay_cheque;
                            }                            
                        }
						if($deposit > 0 && $transaction_pay > 0)
                        {
                            if($deposit >= $transaction_pay)
                            {                                        
                                $pay_deposit = $transaction_pay;
                                $deposit = $deposit - $pay_deposit; $transaction_pay = $transaction_pay - $pay_deposit;
                            }
                            else
                            {
                                $pay_deposit = $deposit;
                                $deposit = $deposit - $pay_deposit; $transaction_pay = $transaction_pay - $pay_deposit;
                            }                            
                        }
                        if($move_cheque > 0 && $transaction_pay > 0)
                        {
                            if($move_cheque >= $transaction_pay)
                            {                                        
                                $pay_move_cheque = $transaction_pay;
                                $move_cheque = $move_cheque - $pay_move_cheque; $transaction_pay = $transaction_pay - $pay_move_cheque;
                            }
                            else
                            {
                                $pay_move_cheque = $move_cheque;
                                $move_cheque = $move_cheque - $pay_move_cheque; $transaction_pay = $transaction_pay - $pay_move_cheque;
                            }
						}
						$transaction_pay = $pay_cash+$pay_transfer+$pay_cheque+$pay_deposit+$pay_move_cheque;
                        $data_payment_ledger_transaction = [
                            'pl_id' => $pl_id,
							'transaction_id' => $purchase_invoice['id'],
							'cash'     		=> $pay_cash,
							'transfer' 		=> $pay_transfer,
							'cheque' 		=> $pay_cheque,
							'deposit' 		=> $pay_deposit,
							'move_cheque' 	=> $pay_move_cheque,
                            'amount'        => $transaction_pay
                        ];
                        $this->crud->insert('payment_ledger_transaction', $data_payment_ledger_transaction);
						$account_payable = $purchase_invoice['account_payable']-$transaction_pay;
						$cheque_payable = $purchase_invoice['cheque_payable']+$pay_cheque+$pay_move_cheque;
                        if($account_payable == 0 && $cheque_payable == 0)
                        {
                            $data_purchase_invoice = [
								'account_payable' => $account_payable,
								'cheque_payable'  => $cheque_payable,
                                'payment_status'  => 1
                            ];
                        }
                        else
                        {
							$data_purchase_invoice = [
								'account_payable' => $account_payable,
								'cheque_payable'  => $cheque_payable,
                            ];                            
                        }
                        $this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $purchase_invoice['id']]);
                        // GENERAL LEDGER -> HUTANG USAHA (D)
                        $where_last_balance = [
                            'coa_account_code' => "20101",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $transaction_pay) : sub_balance(0, $transaction_pay);
                        $data = [
                            'coa_account_code'  => "20101",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PEMBELIAN',
                            'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
                            'debit'       => $transaction_pay,
                            'balance'     => $balance
                        ];									
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "20101",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $transaction_pay)], ['id' => $info['id']]);
                            }
                        }
                    }
                    if($cost > 0)
                    {
                        // GENERAL LEDGER -> BIAYA (D)
                        $where_last_balance = [
                            'coa_account_code' => "50123",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $cost) : add_balance(0, $cost);
                        $data = [
                            'coa_account_code'  => "50123",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PEMBELIAN',
                            'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$code.'_'.$supplier['name'],
                            'debit'       => $cost,
                            'balance'     => $balance
                        ];									
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "50123",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $cost)], ['id' => $info['id']]);
                            }
                        }
                    }
                    $this->session->set_flashdata('success', 'BERHASIL! Data Pembayaran berhasil ditambahkan');
                    redirect(site_url('payment/debt/detail/'.encrypt_custom($pl_id)));
                }                
            }
            else
            {
                if($purchase_invoice_id != null)
                {
                    $purchase_invoice = $this->purchase->get_detail_purchase_invoice(decrypt_custom($purchase_invoice_id));
                    $header = array("title" => "Pembayaran Pembelian Baru");
                    $data = [
                        'purchase_invoice' => $purchase_invoice
                    ];
                    $footer = array("script" => ['finance/pod/create_payment_of_debt.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('finance/pod/create_payment_of_debt', $data);
                    $this->load->view('include/footer', $footer);                    
                }
                else
                {   
                    $header = array("title" => "Pembayaran Pembelian Baru");
                    $footer = array("script" => ['finance/pod/create_payment_of_debt.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('finance/pod/create_payment_of_debt');
                    $this->load->view('include/footer', $footer);
                }                
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
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
					'pod'         	  => $pod,
					'pod_transaction' => $this->payment->get_detail_pod_transaction($pod['id']),
					'pod_detail'  	  => $this->payment->get_detail_pod_detail($pod['id'])
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
				// GENERAL_LEDGER
				$general_ledger = $this->crud->get_where('general_ledger', ['invoice' => $pod['code']]);
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
								if(in_array($coa_category, [1, 5, 7]))
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
                                elseif(in_array($coa_category, [2, 3, 4, 6]))
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
				// CASH_LEDGER
				$cash_ledger = $this->crud->get_where('cash_ledger', ['invoice' => $pod['code']]);
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
				// POD_TRANSACTION
				$pod_transaction = $this->crud->get_where('payment_ledger_transaction', ['pl_id' => $pod['id']])->result_array();
				foreach($pod_transaction AS $info_pod_transaction)
				{
					$purchase_invoice = $this->purchase->get_detail_purchase_invoice($info_pod_transaction['transaction_id']);					
					$account_payable = $purchase_invoice['account_payable']+$info_pod_transaction['amount'];
					$cheque_payable  = $purchase_invoice['cheque_payable']-$info_pod_transaction['cheque']-$info_pod_transaction['move_cheque'];
					$data_purchase_invoice = [
						'account_payable' => $account_payable,
						'cheque_payable'  => $cheque_payable,
						'payment_status'  => 2
					];
					$this->crud->update('purchase_invoice', $data_purchase_invoice, ['id' => $purchase_invoice['id']]);
				}
				$this->db->delete('payment_ledger', ['id' => $pod['id']]);
				$this->db->delete('payment_ledger_transaction', ['pl_id' => $pod['id']]);
				$this->db->delete('payment_ledger_detail', ['pl_id' => $pod['id']]);
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

	// POR
	public function payment_of_receivable()
    {
        if($this->system->check_access('payment/receivable', 'read'))
        {
            if($this->input->is_ajax_request())
            {                
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('por.id AS id_por, por.code AS code_por, por.date, por.method, por.grandtotal,
								customer.name AS customer,
								por.code AS search_code_por');
				$this->datatables->from('payment_ledger AS por');
				$this->datatables->join('payment_ledger_transaction AS por_transaction', 'por_transaction.pl_id = por.id');
				$this->datatables->join('sales_invoice AS si', 'si.id = por_transaction.transaction_id');
				$this->datatables->join('customer', 'customer.code = si.customer_code');
				$this->datatables->where('por.transaction_type', 2);
				$this->datatables->where('por.deleted', 0);
				$this->datatables->where('DATE(por.date) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
				$this->datatables->where('DATE(por.date) <=', date('Y-m-d'));
				$this->datatables->group_by('por.id');
				$this->datatables->add_column('code_por',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_por), code_por');
				echo $this->datatables->generate();                
            }
            else
            {
                $header = array("title" => "Daftar Pembayaran Penjualan");
                $footer = array("script" => ['finance/por/payment_of_receivable.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('finance/por/payment_of_receivable');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}
	
	public function create_payment_of_receivable($sales_invoice_id = null)
    {
        if($this->system->check_access('payment/receivable', 'create'))
        {
            if($this->input->method() === 'post')
            {
				$post = $this->input->post();
				// echo json_encode($post); die;
                $code = $this->payment->por_code();
                $date = format_date($post['date']);
                if($sales_invoice_id != null)
                {
                    $sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));                    
                    $customer = $this->crud->get_where('customer', ['code' => $sales_invoice['customer_code']])->row_array();
					$payment_method = []; $cash=0; $transfer = 0; $cheque = 0; $deposit = 0; $move_cheque = 0;
					$cost = (format_amount($post['cost']) > 0) ? format_amount($post['cost']) : 0;
                    $grandtotal=0;
                    // PAYMENT LEDGER
                    $data_payment_ledger = [
                        'transaction_type'=> 2,
                        'code'           => $code,
                        'date'           => $date,
                        'information'    => $post['information'],
						'cost'           => $cost,
                        'employee_code'  => $this->session->userdata('code_e')
                    ];
                    $pl_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
                    // PAYMENT LEDGER DETAIL
                    for($i=0;$i<sizeof($post['payment_method']);$i++)
                    {
                        if(!in_array($post['payment_method'][$i], $payment_method))
                        {
                            $payment_method[] = $post['payment_method'][$i];
                        }
                        $payment_pay = floatval(format_amount($post['payment_pay'][$i]));
                        $grandtotal = $grandtotal+$payment_pay;
                        $data_payment_ledger_detail = [
                            'pl_id'  => $pl_id,
                            'method' => json_encode([$post['payment_method'][$i]]),
                            'account_id'    => $post['account_id'][$i],
                            'cheque_number' => ($post['cheque_number'][$i] != "") ? $post['cheque_number'][$i] : NULL,
                            'cheque_open_date'  => ($post['cheque_open_date'][$i] != "") ? format_date($post['cheque_open_date'][$i]) : NULL,
                            'cheque_close_date' => ($post['cheque_close_date'][$i] != "") ? format_date($post['cheque_close_date'][$i]) : NULL,
                            'cheque_status'     => ($post['cheque_number'][$i] != "") ? 2 : NULL,
                            'amount'            => $payment_pay
                        ];
                        $pld_id = $this->crud->insert('payment_ledger_detail', $data_payment_ledger_detail);                        
                        switch($post['payment_method'][$i]){
                            case 1:                                
                                $cash=$cash+$payment_pay;
                                // CASH LEDGER -> CASH
                                $from_where_last_balance = [
									'cl_type'    => 1,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$payment_pay : 0+$payment_pay;
								$data = [
									'cl_type'     => 1,
									'account_id'  => $post['account_id'][$i],
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 1,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 1,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']+$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> KAS (D)
								$where_last_balance = [
									'coa_account_code' => "10101",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10101",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10101",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 2:								                              
                                $transfer=$transfer+$payment_pay;
                                // CASH LEDGER -> BANK
                                $from_where_last_balance = [
									'cl_type'    => 2,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$payment_pay : 0+$payment_pay;
								$data = [
									'cl_type'     => 2,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 1,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 2,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']+$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> BANK (D)
								$where_last_balance = [
									'coa_account_code' => "10102",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10102",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10102",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 3:                                
                                $cheque=$cheque+$payment_pay;
                                $data_cheque_payable = ['cheque_payable' => $sales_invoice['cheque_payable']+$payment_pay];
								$this->crud->update('sales_invoice', $data_cheque_payable, ['id' => $sales_invoice['id']]);
								// GENERAL_LEDGER -> PIUTANG CEK/GIRO (D)
								$where_last_balance = [
									'coa_account_code' => "10202",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10202",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10202",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
							case 4:
								$deposit=$deposit+$payment_pay;
								// CASH LEDGER -> CUSTOMER'S DEPOSIT
                                $from_where_last_balance = [
									'cl_type'    => 4,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [
									'cl_type'     => 4,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 4,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> UANG MUKA PENJUALAN (D)
								$where_last_balance = [
									'coa_account_code' => "20201",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "20201",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'       => $payment_pay,
									'balance'     => $balance
								];
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "20201",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
                            case 5:
                                $move_cheque=$move_cheque+$payment_pay;
								break;
							default:
								break;
						}                        
                    }
                    $update_data_payment_ledger = [
                        'method'   	=> json_encode($payment_method),
                        'cash'     	=> $cash,
                        'transfer' 	=> $transfer,
                        'cheque' 	=> $cheque,
                        'deposit' 	=> $deposit,
                        'move_cheque' => $move_cheque,
                        'grandtotal'  => $grandtotal
                    ];
                    $this->crud->update('payment_ledger', $update_data_payment_ledger, ['id' => $pl_id]);
                    // PAYMENT LEDGER TRANSACTION
					$transaction_pay = floatval(format_amount($post['transaction_pay']));
					$transaction_disc_rp = floatval(format_amount($post['transaction_disc_rp']));
                    $data_payment_ledger_transaction = [
                        'pl_id'        	 => $pl_id,
						'transaction_id' => $sales_invoice['id'],
						'disc_rp'     	=> $transaction_disc_rp,
						'cash'     		=> $cash,
                        'transfer' 		=> $transfer,
                        'cheque' 		=> $cheque,
                        'deposit' 		=> $deposit,
                        'move_cheque' 	=> $move_cheque,
                        'amount'        => $transaction_pay
                    ];
                    $this->crud->insert('payment_ledger_transaction', $data_payment_ledger_transaction);
					$account_payable = $sales_invoice['account_payable']-$transaction_pay-$transaction_disc_rp;
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
                    // GENERAL LEDGER -> PIUTANG USAHA (K)
					$where_last_balance = [
						'coa_account_code' => "10201",
						'date <='        => $date,                    
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $transaction_pay+$transaction_disc_rp) : sub_balance(0, $transaction_pay+$transaction_disc_rp);
					$data = [
						'coa_account_code'  => "10201",
						'date'        => $date,
						'transaction_id' => $pl_id,
						'invoice'     => $code,
						'information' => 'PEMBAYARAN PENJUALAN',
						'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
						'credit'       => $transaction_pay+$transaction_disc_rp,
						'balance'     => $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "10201",
							'date >'        => $date,
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $transaction_pay+$transaction_disc_rp)], ['id' => $info['id']]);
						}
					}
					if($transaction_disc_rp > 0)
                    {
                        // GENERAL LEDGER -> BEBAN LAIN-LAIN (K)
                        $where_last_balance = [
                            'coa_account_code' => "50130",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $transaction_disc_rp) : add_balance(0, $transaction_disc_rp);
                        $data = [
                            'coa_account_code'  => "50130",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PENJUALAN',
                            'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
                            'debit'       => $transaction_disc_rp,
                            'balance'     => $balance
                        ];
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "50130",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $transaction_disc_rp)], ['id' => $info['id']]);
                            }
                        }
                    }
                    if($cost > 0)
                    {
                        // GENERAL LEDGER -> PENDAPATAN LAIN-LAIN (K)
                        $where_last_balance = [
                            'coa_account_code' => "60101",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $cost) : add_balance(0, $cost);
                        $data = [
                            'coa_account_code'  => "60101",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PENJUALAN',
                            'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
                            'credit'      => $cost,
                            'balance'     => $balance
                        ];									
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "60101",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $cost)], ['id' => $info['id']]);
                            }
                        }
					}
                    $this->session->set_flashdata('success', 'BERHASIL! Data Pembayaran berhasil ditambahkan');
                    redirect(site_url('payment/receivable/detail/'.encrypt_custom($pl_id)));
                }
                else
                {
                    $customer = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
					$payment_method=[]; $cash=0; $transfer=0; $cheque=0; $deposit=0; $move_cheque=0;
					$cost = (floatval(format_amount($post['cost'])) > 0) ? floatval(format_amount($post['cost'])) : 0;
                    $grandtotal = $cost;
                    // PAYMENT LEDGER
                    $data_payment_ledger = [                        
                        'transaction_type'=> 2,
                        'code'           => $code,
                        'date'           => $date,
                        'information'    => $post['information'],
						'cost'           => round($cost, 2),
                        'employee_code'  => $this->session->userdata('code_e')
					];
                    $pl_id = $this->crud->insert_id('payment_ledger', $data_payment_ledger);
                    // PAYMENT LEDGER DETAIL
                    for($i=0;$i<sizeof($post['payment_method']);$i++)
                    {
                        if(!in_array($post['payment_method'][$i], $payment_method))
                        {
                            $payment_method[] = $post['payment_method'][$i];
                        }
                        $payment_pay = round(floatval(format_amount($post['payment_pay'][$i])), 2);
                        $grandtotal  = $grandtotal+$payment_pay;
                        $data_payment_ledger_detail = [
                            'pl_id' 	 => $pl_id,
                            'method' 	 => json_encode([$post['payment_method'][$i]]),
                            'account_id' => $post['account_id'][$i],
                            'cheque_number' 	=> ($post['cheque_number'][$i] != "") ? $post['cheque_number'][$i] : NULL,
                            'cheque_open_date' 	=> ($post['cheque_open_date'][$i] != "") ? format_date($post['cheque_open_date'][$i]) : NULL,
                            'cheque_close_date' => ($post['cheque_close_date'][$i] != "") ? format_date($post['cheque_close_date'][$i]) : NULL,
                            'cheque_status'     => ($post['cheque_number'][$i] != "") ? 2 : NULL,
                            'amount' 	 => $payment_pay
                        ];
                        $pld_id = $this->crud->insert('payment_ledger_detail', $data_payment_ledger_detail);                        
                        switch($post['payment_method'][$i]){
                            case 1:                                
                                $cash=$cash+$payment_pay;
                                // CASH LEDGER
                                $from_where_last_balance = [
									'cl_type'    => 1,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  add_balance($from_last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [
									'cl_type'     => 1,
									'account_id'  => $post['account_id'][$i],
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 1,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 1,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => $info['balance']-$payment_pay], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> KAS (D)
								$where_last_balance = [
									'coa_account_code' => "10101",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10101",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10101",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 2:								                              
                                $transfer=$transfer+$payment_pay;
                                // CASH LEDGER
                                $from_where_last_balance = [
									'cl_type'    => 2,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ? add_balance($from_last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [
									'cl_type'     => 2,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 1,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 2,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> BANK (D)
								$where_last_balance = [
									'coa_account_code' => "10102",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10102",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'      => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10102",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}                            
								}
								break;
                            case 3:            
                                $cheque=$cheque+$payment_pay;                    
								// GENERAL_LEDGER -> PIUTANG CEK/GIRO (D)
								$where_last_balance = [
									'coa_account_code' => "10202",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_pay) : add_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "10202",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'       => $payment_pay,
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "10202",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
							case 4:
								$deposit=$deposit+$payment_pay;
								// CASH LEDGER -> CUSTOMER'S DEPOSIT
                                $from_where_last_balance = [
									'cl_type'    => 4,
									'account_id' => $post['account_id'][$i],
									'date <='    => $date,
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [
									'cl_type'     => 4,
									'account_id'  => $post['account_id'][$i],
									'transaction_id'   => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'date'        => $date,
									'amount'      => $payment_pay,
									'method'      => 2,
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => 4,
										'account_id' => $post['account_id'][$i],
										'date >'     => $date,
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{
										$this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								// GENERAL_LEDGER -> UANG MUKA PENJUALAN (D)
								$where_last_balance = [
									'coa_account_code' => "20201",
									'date <='        => $date,
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_pay) : sub_balance(0, $payment_pay);
								$data = [										
									'coa_account_code' => "20201",
									'date'        => $date,
									'transaction_id' => $pl_id,
									'invoice'     => $code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
									'debit'       => $payment_pay,
									'balance'     => $balance
								];
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => "20201",
										'date >'        => $date,
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_pay)], ['id' => $info['id']]);
									}
								}
								break;
                            case 5:
                                $move_cheque=$move_cheque+$payment_pay;
								break;
							default:
								break;
						}                        
                    }
                    $update_data_payment_ledger = [
                        'method' 	  => json_encode($payment_method),
                        'cash' 	 	  => $cash,
                        'transfer' 	  => $transfer,
                        'cheque' 	  => $cheque,
                        'deposit' 	  => $deposit,
                        'move_cheque' => $move_cheque,
                        'grandtotal'  => $grandtotal
                    ];
                    $this->crud->update('payment_ledger', $update_data_payment_ledger, ['id' => $pl_id]);
                    // PAYMENT_LEDGER_TRANSACTION
                    foreach($post['transaction'] AS $info_transaction)
                    {
                        $sales_invoice       = $this->crud->get_where('sales_invoice', ['id' => $info_transaction['transaction_id']])->row_array();
						$transaction_pay     = format_amount($info_transaction['transaction_pay']);
						$transaction_disc_rp = format_amount($info_transaction['transaction_disc_rp']);
						$pay_cash = 0; $pay_transfer = 0; $pay_cheque = 0; $pay_deposit = 0; $pay_move_cheque = 0;
						if($cash > 0 && $transaction_pay > 0)
                        {
							$pay_cash = ($cash >= $transaction_pay) ? $transaction_pay : $cash;
							$cash = $cash - $pay_cash; $transaction_pay = $transaction_pay - $pay_cash;
						}
						if($transfer > 0 && $transaction_pay > 0)
                        {
							$pay_transfer = ($transfer >= $transaction_pay) ? $transaction_pay : $transfer;
							$transfer = $transfer - $pay_transfer; $transaction_pay = $transaction_pay - $pay_transfer;
                        }
						if($cheque > 0 && $transaction_pay > 0)
                        {
							$pay_cheque = ($cheque >= $transaction_pay) ? $transaction_pay : $cheque;
							$cheque = $cheque - $pay_cheque; $transaction_pay = $transaction_pay - $pay_cheque;
						}
						if($deposit > 0 && $transaction_pay > 0)
                        {
							$pay_deposit = ($deposit >= $transaction_pay) ? $transaction_pay : $deposit;
							$deposit = $deposit - $pay_deposit; $transaction_pay = $transaction_pay - $pay_deposit;
                        }
                        if($move_cheque > 0 && $transaction_pay > 0)
                        {
							$pay_move_cheque = ($move_cheque >= $transaction_pay) ? $transaction_pay : $move_cheque;
							$move_cheque = $move_cheque - $pay_move_cheque; $transaction_pay = $transaction_pay - $pay_move_cheque;
						}
						$transaction_pay = $pay_cash+$pay_transfer+$pay_cheque+$pay_deposit+$pay_move_cheque;
                        $data_payment_ledger_transaction = [
                            'pl_id' => $pl_id,
							'transaction_id' => $sales_invoice['id'],
							'cash'     		=> $pay_cash,
							'transfer' 		=> $pay_transfer,
							'cheque' 		=> $pay_cheque,
							'deposit' 		=> $pay_deposit,
							'move_cheque' 	=> $pay_move_cheque,
							'disc_rp'		=> $transaction_disc_rp,
                            'amount'        => $transaction_disc_rp+$transaction_pay
                        ];
                        $this->crud->insert('payment_ledger_transaction', $data_payment_ledger_transaction);
						$account_payable = $sales_invoice['account_payable']-$transaction_pay-$transaction_disc_rp;
						$cheque_payable = $sales_invoice['cheque_payable']+$pay_cheque+$pay_move_cheque;
                        if($account_payable == 0 && $cheque_payable == 0)
                        {
                            $data_sales_invoice = [
								'account_payable' => $account_payable,
								'cheque_payable'  => $cheque_payable,
                                'payment_status'  => 1
                            ];
                        }
                        else
                        {
							$data_sales_invoice = [
								'account_payable' => $account_payable,
								'cheque_payable'  => $cheque_payable,
                            ];                            
                        }
                        $this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
                        // GENERAL LEDGER -> PIUTANG USAHA (K)
                        $where_last_balance = [
                            'coa_account_code' => "10201",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $transaction_pay+$transaction_disc_rp) : sub_balance(0, $transaction_pay+$transaction_disc_rp);
                        $data = [
                            'coa_account_code'  => "10201",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PENJUALAN',
                            'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$supplier['name'],
                            'credit'       => $transaction_pay+$transaction_disc_rp,
                            'balance'     => $balance
                        ];									
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "10201",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $transaction_pay+$transaction_disc_rp)], ['id' => $info['id']]);
                            }
						}
						if($transaction_disc_rp > 0)
						{
							// GENERAL LEDGER -> BEBAN LAIN-LAIN (K)
							$where_last_balance = [
								'coa_account_code' => "50130",
								'date <='        => $date,                    
								'deleted'        => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $transaction_disc_rp) : add_balance(0, $transaction_disc_rp);
							$data = [
								'coa_account_code'  => "50130",
								'date'        => $date,
								'transaction_id' => $pl_id,
								'invoice'     => $code,
								'information' => 'PEMBAYARAN PENJUALAN',
								'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
								'debit'       => $transaction_disc_rp,
								'balance'     => $balance
							];									
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "50130",
									'date >'        => $date,
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $transaction_disc_rp)], ['id' => $info['id']]);
								}
							}
						}
                    }
                    if($cost > 0)
                    {
                        // GENERAL LEDGER -> PENDAPATAN LAIN-LAIN (K)
                        $where_last_balance = [
                            'coa_account_code' => "60101",
                            'date <='        => $date,                    
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $cost) : add_balance(0, $cost);
                        $data = [
                            'coa_account_code'  => "60101",
                            'date'        => $date,
                            'transaction_id' => $pl_id,
                            'invoice'     => $code,
                            'information' => 'PEMBAYARAN PENJUALAN',
                            'note'		  => 'PEMBAYARAN_PENJUALAN_'.$code.'_'.$customer['name'],
                            'credit'      => $cost,
                            'balance'     => $balance
                        ];									
                        if($this->crud->insert('general_ledger', $data))
                        {
                            $where_after_balance = [
                                'coa_account_code'=> "60101",
                                'date >'        => $date,
                                'deleted'       => 0
                            ];
                            $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($after_balance  AS $info)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $cost)], ['id' => $info['id']]);
                            }
                        }
					}
                    $this->session->set_flashdata('success', 'BERHASIL! Data Pembayaran berhasil ditambahkan');
                    redirect(site_url('payment/receivable/detail/'.encrypt_custom($pl_id)));
                }                
            }
            else
            {
                if($sales_invoice_id != null)
                {
					$sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));
					if($sales_invoice['account_payable'] > 0 && $sales_invoice['do_status'] == 1)
					{
						$header = array("title" => "Pembayaran Penjualan Baru");
						$data = [
							'sales_invoice' => $sales_invoice
						];
						$footer = array("script" => ['finance/por/create_payment_of_receivable.js']);
						$this->load->view('include/header', $header);
						$this->load->view('include/menubar');
						$this->load->view('include/topbar');
						$this->load->view('finance/por/create_payment_of_receivable', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Pembayaran tidak dapat dilakukan');
            			redirect(site_url('sales/invoice/detail/'.encrypt_custom($sales_invoice['id'])));
					}                    
                }
                else
                {   
                    $header = array("title" => "Pembayaran Penjualan Baru");
                    $footer = array("script" => ['finance/por/create_payment_of_receivable.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('finance/por/create_payment_of_receivable');
                    $this->load->view('include/footer', $footer);
                }                
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
					'por_transaction'  => $this->payment->get_detail_por_transaction($por['id']),
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

	public function delete_payment_of_receivable()
	{    
		if($this->input->is_ajax_request())
		{
			if($this->session->userdata('verifypassword') == 1)
			{
				$this->session->unset_userdata('verifypassword');
				$this->db->trans_start();
				$post = $this->input->post();
				$por  = $this->payment->get_detail_por(decrypt_custom($post['por_id']));
				// GENERAL_LEDGER
				$general_ledger = $this->crud->get_where('general_ledger', ['invoice' => $por['code']]);
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
								if(in_array($coa_category, [1, 5, 7]))
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
                                elseif(in_array($coa_category, [2, 3, 4, 6]))
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
				// CASH_LEDGER
				$cash_ledger = $this->crud->get_where('cash_ledger', ['invoice' => $por['code']]);
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
				// POR_TRANSACTION
				$por_transaction = $this->crud->get_where('payment_ledger_transaction', ['pl_id' => $por['id']])->result_array();
				foreach($por_transaction AS $info_por_transaction)
				{
					$sales_invoice = $this->sales->get_detail_sales_invoice($info_por_transaction['transaction_id']);					
					$account_payable = $sales_invoice['account_payable']+$info_por_transaction['disc_rp']+$info_por_transaction['amount'];
					$cheque_payable  = $sales_invoice['cheque_payable']-$info_por_transaction['cheque']-$info_por_transaction['move_cheque'];
					$data_sales_invoice = [
						'account_payable' => $account_payable,
						'cheque_payable'  => $cheque_payable,
						'payment_status'  => 2
					];
					$this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]);
				}
				$this->db->delete('payment_ledger', ['id' => $por['id']]);
				$this->db->delete('payment_ledger_transaction', ['pl_id' => $por['id']]);
				$this->db->delete('payment_ledger_detail', ['pl_id' => $por['id']]);
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$data_activity = [
						'information' => 'MENGHAPUS PEMBAYARAN PENJUALAN (NO. TRANSAKSI '.$por['code'].')',
						'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];
					$this->crud->insert('activity', $data_activity);
					$this->session->set_flashdata('success', 'BERHASIL! Pembayaran Penjualan Terhapus');
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
					$this->session->set_flashdata('error', 'Mohon Maaf! Pembayaran Penjualan gagal terhapus');
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
				$this->session->set_flashdata('error', 'Mohon Maaf! Pembayaran Penjualan gagal terhapus');
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

	// CHEQUE
	public function cheque_acquittance()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$date = format_date($post['cheque_acquittance_date']);
			$payment_ledger_detail = $this->crud->get_where('payment_ledger_detail', ['id' => $post['pl_detail_id']])->row_array();
			$payment_ledger = $this->crud->get_where('payment_ledger', ['id' => $payment_ledger_detail['pl_id']])->row_array();
			$payment_ledger_transaction = $this->crud->get_where('payment_ledger_transaction', ['pl_id' => $payment_ledger['id'], 'cheque >' => 0])->result_array();			
			if($post['cheque_status'] == 1)
			{
				if($payment_ledger['transaction_type'] == 1) //PURCHASE INVOICE
				{
					$supplier = $this->db->select('supplier.name')
										 ->from('payment_ledger_transaction AS pod_transaction')
										 ->join('purchase_invoice', 'purchase_invoice.id = pod_transaction.transaction_id')
										 ->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
									 	 ->where('pod_transaction.pl_id', $payment_ledger['id'])
										 ->group_by('pod_transaction.id')
										 ->get()->row_array();
					// CASH LEDGER -> BANK
					$where_last_balance = [
						'cl_type'    => 2,
						'account_id' => $payment_ledger_detail['account_id'],
						'date <='    => $date,
						'deleted'    => 0
					];
					$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $payment_ledger_detail['amount']) : sub_balance(0, $payment_ledger_detail['amount']);
					$data = [
						'cl_type'     => 2,
						'account_id'  => $payment_ledger_detail['account_id'],
						'transaction_id' => $payment_ledger['id'],
						'invoice'     => $payment_ledger['code'],
						'information' => 'PENCAIRAN HUTANG CEK/GIRO',
						'note'		  => 'PENCAIRAN HUTANG CEK/GIRO_'.$payment_ledger_detail['cheque_number'].'_'.$payment_ledger['code'].'_'.$supplier['name'],
						'date'        => $date,
						'amount'      => $payment_ledger_detail['amount'],
						'method'      => 2,
						'balance'     => $balance
					];
					$cl_id = $this->crud->insert_id('cash_ledger', $data);
					if($cl_id)
					{
						$where_after_balance = [
							'cl_type'    => 2,
							'account_id' => $payment_ledger_detail['account_id'],
							'date >'     => $date,
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $payment_ledger_detail['amount'])], ['id' => $info['id']]);
						}
					}
					// GENERAL_LEDGER -> BANK (D)
					$where_last_balance = [
						'coa_account_code' => "10102",
						'date <='        => $date,
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_ledger_detail['amount']) : sub_balance(0, $payment_ledger_detail['amount']);
					$data = [										
						'coa_account_code' => "10102",
						'date'        => $date,
						'transaction_id' => $payment_ledger['id'],
						'invoice'     => $payment_ledger['code'],
						'information' => 'PENCAIRAN HUTANG CEK/GIRO',
						'note'		  => 'PENCAIRAN HUTANG CEK/GIRO_'.$payment_ledger_detail['cheque_number'].'_'.$payment_ledger['code'].'_'.$supplier['name'],
						'credit'       => $payment_ledger_detail['amount'],
						'balance'     => $balance
					];		
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code' => "10102",
							'date >'        => $date,
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_ledger_detail['amount'])], ['id' => $info['id']]);
						}                            
					}
					// GENERAL_LEDGER -> HUTANG CEK/GIRO (K)
					$where_last_balance = [
						'coa_account_code' => "20102",
						'date <='        => $date,
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_ledger_detail['amount']) : sub_balance(0, $payment_ledger_detail['amount']);
					$data = [										
						'coa_account_code' => "20102",
						'date'        => $date,
						'transaction_id' => $payment_ledger['id'],
						'invoice'     => $payment_ledger['code'],
						'information' => 'PENCAIRAN HUTANG CEK/GIRO',
						'note'		  => 'PENCAIRAN HUTANG CEK/GIRO_'.$payment_ledger_detail['cheque_number'].'_'.$payment_ledger['code'].'_'.$supplier['name'],
						'debit'       => $payment_ledger_detail['amount'],
						'balance'     => $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code' => "20102",
							'date >'        => $date,
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_ledger_detail['amount'])], ['id' => $info['id']]);
						}                            
					}
					$pay_cheque = $payment_ledger_detail['amount'];
					foreach($payment_ledger_transaction AS $info_payment_ledger_transaction)
					{	
						if($pay_cheque > 0)
						{
							if($info_payment_ledger_transaction['cheque_acquittance'] < $info_payment_ledger_transaction['cheque'])
							{
								$remains_cheque = $info_payment_ledger_transaction['cheque']-$info_payment_ledger_transaction['cheque_acquittance'];
								$cheque_acquittance = ($pay_cheque >= $remains_cheque) ? $info_payment_ledger_transaction['cheque_acquittance']+$remains_cheque : $info_payment_ledger_transaction['cheque_acquittance']+$pay_cheque;
								$update_payment_ledger_transaction = [
									'cheque_acquittance' => $cheque_acquittance
								];
								$this->crud->update('payment_ledger_transaction', $update_payment_ledger_transaction, ['id' => $info_payment_ledger_transaction['id']]);
								$purchase_invoice = $this->crud->get_where('purchase_invoice', ['id' => $info_payment_ledger_transaction['transaction_id']])->row_array();																					
								$cheque_payable = ($pay_cheque >= $remains_cheque) ? $purchase_invoice['cheque_payable']-$remains_cheque : $purchase_invoice['cheque_payable']-$pay_cheque;
								$update_purchase_invoice = [
									'cheque_payable' => $cheque_payable,
									'payment_status' => ($cheque_payable == 0) ? 1 : 2
								];													
								$this->crud->update('purchase_invoice', $update_purchase_invoice, ['id' => $purchase_invoice['id']]);
								$pay_cheque = ($pay_cheque >= $remains_cheque) ? $pay_cheque-$remains_cheque : 0;
							}
						}					
					}
					$update_payment_ledger_detail = [
						'cheque_acquittance_date' => $date,
						'cheque_status' => 1
					];
					$this->crud->update('payment_ledger_detail', $update_payment_ledger_detail, ['id' => $payment_ledger_detail['id']]);
					$response   =   [
						'status'    => [
							'code'      => 200,
							'message'   => 'Berhasil',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('success', 'Pembayaran Cek/Giro berhasil di konfirmasi');
				}	
				elseif($payment_ledger['transaction_type'] == 2) //SALES_INVOICE
				{		
					$customer = $this->db->select('customer.name')
										 ->from('payment_ledger_transaction AS por_transaction')
										 ->join('sales_invoice', 'sales_invoice.id = por_transaction.transaction_id')
										 ->join('customer', 'customer.code = sales_invoice.customer_code')
									 	 ->where('por_transaction.pl_id', $payment_ledger['id'])
										 ->group_by('por_transaction.id')										 
										 ->get()->row_array();
					// CASH LEDGER -> BANK
					$where_last_balance = [
						'cl_type'    => 2,
						'account_id' => $payment_ledger_detail['account_id'],
						'date <='    => $date,
						'deleted'    => 0
					];
					$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $payment_ledger_detail['amount']) : add_balance(0, $payment_ledger_detail['amount']);
					$data = [
						'cl_type'     => 2,
						'account_id'  => $payment_ledger_detail['account_id'],
						'transaction_id' => $payment_ledger['id'],
						'invoice'     => $payment_ledger['code'],
						'information' => 'PENCAIRAN CEK/GIRO',
						'note'		  => 'PENCAIRAN CEK/GIRO_'.$payment_ledger_detail['cheque_number'].'_'.$payment_ledger['code'].'_'.$customer['name'],
						'date'        => $date,
						'amount'      => $payment_ledger_detail['amount'],
						'method'      => 1,
						'balance'     => $balance
					];
					$cl_id = $this->crud->insert_id('cash_ledger', $data);
					if($cl_id)
					{
						$where_after_balance = [
							'cl_type'    => 2,
							'account_id' => $payment_ledger_detail['account_id'],
							'date >'     => $date,
							'deleted'    => 0
						];                    
						$after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $payment_ledger_detail['amount'])], ['id' => $info['id']]);
						}
					}
					// GENERAL_LEDGER -> BANK (D)
					$where_last_balance = [
						'coa_account_code' => "10102",
						'date <='        => $date,
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ? add_balance($last_balance['balance'], $payment_ledger_detail['amount']) : add_balance(0, $payment_ledger_detail['amount']);
					$data = [										
						'coa_account_code' => "10102",
						'date'        => $date,
						'transaction_id' => $payment_ledger['id'],
						'invoice'     => $payment_ledger['code'],
						'information' => 'PENCAIRAN CEK/GIRO',
						'note'		  => 'PENCAIRAN CEK/GIRO_'.$payment_ledger_detail['cheque_number'].'_'.$payment_ledger['code'].'_'.$customer['name'],
						'debit'      => $payment_ledger_detail['amount'],
						'balance'     => $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code' => "10102",
							'date >'        => $date,
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $payment_ledger_detail['amount'])], ['id' => $info['id']]);
						}                            
					}
					// GENERAL_LEDGER -> PIUTANG CEK/GIRO (K)
					$where_last_balance = [
						'coa_account_code' => "10202",
						'date <='        => $date,
						'deleted'        => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ? sub_balance($last_balance['balance'], $payment_ledger_detail['amount']) : sub_balance(0, $payment_ledger_detail['amount']);
					$data = [										
						'coa_account_code' => "10202",
						'date'        => $date,
						'transaction_id' => $payment_ledger['id'],
						'invoice'     => $payment_ledger['code'],
						'information' => 'PENCAIRAN CEK/GIRO',
						'note'		  => 'PENCAIRAN CEK/GIRO_'.$payment_ledger_detail['cheque_number'].'_'.$payment_ledger['code'].'_'.$customer['name'],
						'credit'      => $payment_ledger_detail['amount'],
						'balance'     => $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code' => "10202",
							'date >'        => $date,
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $payment_ledger_detail['amount'])], ['id' => $info['id']]);
						}                            
					}
					$pay_cheque = $payment_ledger_detail['amount'];
					foreach($payment_ledger_transaction AS $info_payment_ledger_transaction)
					{	
						if($pay_cheque > 0)
						{
							if($info_payment_ledger_transaction['cheque_acquittance'] < $info_payment_ledger_transaction['cheque'])
							{
								$remains_cheque = $info_payment_ledger_transaction['cheque']-$info_payment_ledger_transaction['cheque_acquittance'];
								$cheque_acquittance = ($pay_cheque >= $remains_cheque) ? $info_payment_ledger_transaction['cheque_acquittance']+$remains_cheque : $info_payment_ledger_transaction['cheque_acquittance']+$pay_cheque;
								$update_payment_ledger_transaction = [
									'cheque_acquittance' => $cheque_acquittance
								];
								$this->crud->update('payment_ledger_transaction', $update_payment_ledger_transaction, ['id' => $info_payment_ledger_transaction['id']]);
								$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $info_payment_ledger_transaction['transaction_id']])->row_array();																					
								$cheque_payable = ($pay_cheque >= $remains_cheque) ? $sales_invoice['cheque_payable']-$remains_cheque : $sales_invoice['cheque_payable']-$pay_cheque;
								$update_sales_invoice = [
									'cheque_payable' => $cheque_payable,
									'payment_status' => ($cheque_payable == 0) ? 1 : 2
								];														
								$this->crud->update('sales_invoice', $update_sales_invoice, ['id' => $sales_invoice['id']]);
								$pay_cheque = ($pay_cheque >= $remains_cheque) ? $pay_cheque-$remains_cheque : 0;
							}
						}					
					}
					$update_payment_ledger_detail = [
						'cheque_acquittance_date' => $date,
						'cheque_status' => 1
					];
					$this->crud->update('payment_ledger_detail', $update_payment_ledger_detail, ['id' => $payment_ledger_detail['id']]);
					$response   =   [
						'status'    => [
							'code'      => 200,
							'message'   => 'Berhasil',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('success', 'Pembayaran Cek/Giro berhasil di konfirmasi');
				}				
			}
			elseif($post['cheque_status'] == 3)
			{
				
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}
}