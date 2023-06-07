<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_usage extends System_Controller 
{	
    public function __construct()
	{
		parent::__construct();		
		$this->load->model('Product_usage_model','product_usage');
	}
	
	public function index()
	{
		if($this->system->check_access('product_usage','read'))
		{
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$post = $this->input->post();
				$this->datatables->select('pug.id AS id_pug, pug.date, pug.code AS code_pug, pug.grandtotal, operator.name AS operator, pug.do_status,
								pug.code AS search_code_pug')
							     ->from('product_usage AS pug')
								 ->join('employee AS operator', 'operator.code = pug.employee_code');
				if($post['do_status'] == "" || $post['do_status'] != 0)
				{
					$this->datatables->where('DATE(pug.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
					$this->datatables->where('DATE(pug.created) <=', date('Y-m-d'));
					if($post['do_status'] == 1)
					{
						$this->datatables->where('pug.do_status', $post['do_status']);						
					}										
				}
				else
				{					
					$this->datatables->where('pug.do_status', $post['do_status']);
				}									
				$this->datatables->add_column('code_pug', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('product_usage/detail/$1').'" target="_blank" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(id_pug),code_pug');				
				$this->datatables->where('pug.deleted', 0)->group_by('pug.id');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Pemakaian");	
				$footer = array("script" => ['inventory/product_usage/product_usage.js']);	
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product_usage/product_usage');
				$this->load->view('include/footer', $footer);
			} 			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url(''));
		}
	}
	
	public function get_product()
	{
		if($this->input->is_ajax_request())
		{
			$search = urldecode($this->uri->segment(4));	
			$ppn    = urldecode($this->uri->segment(5));
			$data   = $this->product_usage->get_product($search, $ppn);
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
		else
		{
			$this->load->view('auth/show_404');
		}		
	}
	
	public function get_sellprice()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$price_class = 1;
			$where_sellprice = [
				'product_code'	=> $post['product_code'],
				'unit_id'		=> $post['unit_id']
			];
			$sellprice = $this->crud->get_where('sellprice', $where_sellprice )->row_array();
			$option = '';
			for($i=1; $i<=1 ; $i++)
			{
				if($this->system->check_access('view_sellprice_'.$i, 'read'))
				{					
					$option .= "<option value='".$sellprice['price_'.$i]."' class='".'H'.$i."' selected>".'H'.$i." | ".number_format($sellprice['price_'.$i], 0, '.', ',')."</option>";					
				}							
			}
			$response = [
				'option' => $option
			];
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	public function create_product_usage()
	{
		if($this->system->check_access('product_usage', 'create'))
        {
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');												
				$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Pemakaian Baru");	
					$footer = array("script" => ['inventory/product_usage/create_product_usage.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('product_usage/create_product_usage');
					$this->load->view('include/footer', $footer);					
				}
				else
				{
					$this->db->trans_start();
					$product_usage_code	= $this->product_usage->product_usage_code();
					$created			= date('Y-m-d H:i:s');
					$data_product_usage = [
						'date'				=> format_date($post['date']),
						'code'				=> $product_usage_code,
						'information'		=> $post['information'],
						'total_product'		=> $post['total_product'],
						'total_qty'			=> $post['total_qty'],
						'employee_code'		=> $this->session->userdata('code_e'),
						'created'			=> $created
					];
					if($this->crud->get_where('product_usage', ['created' => $created])->num_rows() == 0)
					{
						// PRODUCT_USAGE
						$product_usage_id = $this->crud->insert_id('product_usage', $data_product_usage);
						if($product_usage_id != null)
						{
							$grandtotal = 0;
							// PRODUCT_USAGE_DETAIL
							foreach($post['product'] AS $info)
							{
								$res = 0;
								$product = $this->crud->get_where_select('id, code, hpp', 'product', ['code' => $info['product_code']])->row_array();
								$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);								
								$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
								$price = $product['hpp']*$convert['value'];
								$total = $price*$qty;
								$grandtotal = $grandtotal+$total;
								$data_sales_invoice_detail= [
									'product_usage_id' => $product_usage_id,
									'product_id'	=> $product['id'],
									'product_code'	=> $product['code'],
									'qty'			=> $qty,
									'unit_id'		=> $info['unit_id'],
									'unit_value'    => ($convert['value'] != null) ? $convert['value'] : 1,
									'price'			=> $price,
									'warehouse_id'	=> $info['warehouse_id'],
									'total'			=> $total
								];
								$this->crud->insert('product_usage_detail', $data_sales_invoice_detail);
								$res = 1;
							}
							$this->crud->update('product_usage', ['grandtotal' => $grandtotal], ['id' => $product_usage_id]);
						}
						else
						{
							$res = 0;
						}
					}
					else
					{
						$res = 0;
					}
					$this->db->trans_complete();					
					if($res==1 && $this->db->trans_status()===TRUE)
					{
						$this->db->trans_commit();
						$data_activity = [
							'information' => 'MEMBUAT PEMAKAIAN BARU (NO. TRANSAKSI '.$product_usage_code.')',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];						
						$this->crud->insert('activity', $data_activity);
						$this->session->set_flashdata('success', 'Data Pemakaian berhasil ditambahkan');
						redirect(site_url('product_usage/detail/'.encrypt_custom($product_usage_id)));
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Data Pemakaian gagal ditambahkan');
						redirect(site_url('product_usage'));
					}												
				}
			}
			else
			{
				$header = array("title" => "Pemakaian Baru");	
				$footer = array("script" => ['inventory/product_usage/create_product_usage.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product_usage/create_product_usage');
				$this->load->view('include/footer', $footer);				
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }		
	}

	public function create_product_usage_do()
	{
		if($this->system->check_access('product_usage','create'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$product_usage = $this->product_usage->detail_product_usage($post['product_usage_id']);
				if($product_usage['do_status'] == 0)
				{
					$product_usage_detail = $this->product_usage->detail_product_usage_detail($product_usage['id']);				
					$check_stock_product_usage_do = $this->product_usage->check_stock_product_usage_do($product_usage, $product_usage_detail);
					if($check_stock_product_usage_do['total'] == 0)
					{
						$this->db->trans_start();
						// GENERAL LEDGER -> PERSEDIAAN BARANG (K)
						$where_last_balance = [
							'coa_account_code' => "10301",
							'date <='        => $product_usage['date'],
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $product_usage['grandtotal']) : sub_balance(0, $product_usage['grandtotal']);
						$data = [
							'date'        => $product_usage['date'],
							'coa_account_code'  => "10301",
							'transaction_id' => $product_usage['id'],
							'invoice'     => $product_usage['code'],
							'information' => 'PEMAKAIAN',
							'note'		  => 'PEMAKAIAN_'.$product_usage['code'],
							'credit'      => $product_usage['grandtotal'],
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "10301",
								'date >'        => $product_usage['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $product_usage['grandtotal'])], ['id' => $info['id']]);
							}
						}
						// GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (D)
						$where_last_balance = [
							'coa_account_code' => "50129",
							'date <='        => $product_usage['date'],                    
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $product_usage['grandtotal']) : add_balance(0, $product_usage['grandtotal']);
						$data = [
							'date'        => $product_usage['date'],
							'coa_account_code'  => "50129",
							'transaction_id' => $product_usage['id'],
							'invoice'     => $product_usage['code'],
							'information' => 'PEMAKAIAN',
							'note'		  => 'PEMAKAIAN_'.$product_usage['code'],
							'debit'       => $product_usage['grandtotal'],
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "50129",
								'date >'        => $product_usage['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $product_usage['grandtotal'])], ['id' => $info['id']]);
							}
						}
						// PRODUCT_USAGE_DETAIL
						foreach($product_usage_detail AS $info_product_usage_detail)
						{
							$res = 0;
							// STOCK
							$qty_convert = $info_product_usage_detail['qty']*$info_product_usage_detail['unit_value'];
							$check_stock = $this->crud->get_where('stock', ['product_code' => $info_product_usage_detail['product_code'], 'warehouse_id' => $info_product_usage_detail['warehouse_id']]);
							if($check_stock->num_rows() == 1)
							{	
								$stock = $check_stock->row_array();
								$where_stock = array(
									'product_code'  => $info_product_usage_detail['product_code'],
									'warehouse_id'  => $info_product_usage_detail['warehouse_id']
								);       							
								$stock = array(                                
									'product_id'    => $info_product_usage_detail['product_id'],
									'qty'           => $stock['qty']-$qty_convert
								);
								$update_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{							
								$stock = array(                                
									'product_id'    => $info_product_usage_detail['product_id'],
									'product_code'  => $info_product_usage_detail['product_code'],                                                        
									'qty'           => 0-$qty,
									'warehouse_id'  => $info_product_usage_detail['warehouse_id']
								);
								$update_stock = $this->crud->insert('stock', $stock);
							}
							if($update_stock)
							{
								// STOCK CARD
								$where_last_stock_card = [
									'date <='      => $product_usage['date'],
									'product_id'   => $info_product_usage_detail['product_id'],
									'warehouse_id' => $info_product_usage_detail['warehouse_id'],
									'deleted'      => 0
								];
								$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_card = array(
									'type'            => 10, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation, 10: Product_usage
									'information'     => 'PEMAKAIAN',
									'note'			  => 'PEMAKAIAN',
									'date'			  => $product_usage['date'],
									'transaction_id'  => $product_usage['id'],								
									'invoice'         => $product_usage['code'],
									'product_id'      => $info_product_usage_detail['product_id'],
									'product_code'    => $info_product_usage_detail['product_code'],
									'qty'             => $qty_convert,
									'method'          => 2, // 1:In, 2:Out
									'stock'           => $last_stock_card['stock']-$qty_convert,
									'warehouse_id'    => $info_product_usage_detail['warehouse_id'],
									'user_id'         => $this->session->userdata('id_u')
								);
								$this->crud->insert('stock_card',$data_stock_card);
								$where_after_stock_card = [
									'date >'       => $product_usage['date'],
									'product_id'   => $info_product_usage_detail['product_id'],
									'warehouse_id' => $info_product_usage_detail['warehouse_id'],
									'deleted'      => 0
								];                    
								$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_stock_card  AS $info_after_stock_card)
								{
									$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']-$qty_convert], ['id' => $info_after_stock_card['id']]);
								}
								// STOCK MOVEMENT
								$where_last_stock_movement = [
									'product_id'   => $info_product_usage_detail['product_id'],
									'date <='      => $product_usage['date'],
									'deleted'      => 0
								];
								$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_movement = [
									'type'            => 10, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
									'information'     => 'PEMAKAIAN',
									'note'			  => 'PEMAKAIAN',
									'date'            => $product_usage['date'],
									'transaction_id'  => $product_usage['id'],
									'invoice'         => $product_usage['code'],
									'product_id'      => $info_product_usage_detail['product_id'],
									'product_code'    => $info_product_usage_detail['product_code'],
									'qty'             => $qty_convert,
									'method'          => 2, // 1:In, 2:Out
									'stock'           => $last_stock_movement['stock']-$qty_convert,
									'employee_code'   => $this->session->userdata('code_e')
								];
								$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
								$where_after_stock_movement = [
									'product_id'   => $info_product_usage_detail['product_id'],
									'date >'       => $product_usage['date'],
									'deleted'      => 0
								];                    
								$after_stock_movement = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_stock_movement  AS $info_after_stock_movement)
								{
									$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']-$qty_convert], ['id' => $info_after_stock_movement['id']]);
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
						if($res == 1 && $this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
							$this->crud->update('product_usage', ['do_status' => 1], ['id' => $product_usage['id']]);
							$data_activity = [
								'information' => 'MEMBUAT PENJUALAN (CETAK DO) (NO. TRANSAKSI: '.$product_usage['code'].')',
								'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];
							$this->crud->insert('activity', $data_activity);					
							$this->session->set_userdata('create_product_usage_do', '1');
							$this->session->set_flashdata('success', 'Cetak DO Pemakaian Berhasil');
							$response   =   [
								'product_usage_id' => encrypt_custom($product_usage['id']),
								'status'    => [
									'code'      => 200,
									'message'   => 'Berhasil',
								],
								'response'  => ''
							];
							$this->session->set_flashdata('success', 'Cetak DO Berhasil');
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
							$this->session->set_flashdata('error', 'Cetak DO Gagal');
							
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
						$this->session->set_flashdata('error', 'Mohon Maaf, Cetak DO Gagal karena terdapat stok yang Kurang, harap periksa kembali');
						$this->session->set_flashdata('min_product', $check_stock_product_usage_do['found']);
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
					$this->session->set_flashdata('error', 'Mohon Maaf, Cetak DO Gagal. DO Sudah tercetak');
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
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales'));
		}
	}

	public function cancel_product_usage_do()
	{
		if($this->input->is_ajax_request())
		{			
			if($this->session->userdata('verifypassword') == 1)
			{
				$this->session->unset_userdata('verifypassword');
				$post = $this->input->post();
				$product_usage = $this->product_usage->detail_product_usage($post['product_usage_id']);
				$product_usage_detail = $this->product_usage->detail_product_usage_detail($product_usage['id']);
				$this->db->trans_start();
				// GENERAL LEDGER
                $where_general_ledger = [
                    'invoice'		=> $product_usage['code']
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
				// SALES INVOICE DETAIL			
				foreach($product_usage_detail AS $info_product_usage_detail)
				{					
					// ADD STOCK
					$where_stock = [
						'product_code'	=> $info_product_usage_detail['product_code'],
						'warehouse_id'	=> $info_product_usage_detail['warehouse_id']
					];
					$stock = $this->crud->get_where('stock', $where_stock)->row_array();
					$update_stock = [
						'qty' => $stock['qty'] + ($info_product_usage_detail['qty']*$info_product_usage_detail['unit_value'])
					];
					$this->crud->update('stock', $update_stock, $where_stock);
					// UPDATE AND DELETE STOCK CARD
					$where_stock_card = [
						'invoice'        => $product_usage['code'],
						'product_code'	 => $info_product_usage_detail['product_code'],
						'type'			 => 10,
						'method'		 => 2,
						'warehouse_id'	 => $info_product_usage_detail['warehouse_id']
					];
					$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
					$where_after_stock_card = [
						'date >='       => $stock_card['date'],
						'product_code'	=> $info_product_usage_detail['product_code'],
						'warehouse_id'	=> $info_product_usage_detail['warehouse_id'],
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
								'stock' => $info_stock_card['stock']+($info_product_usage_detail['qty']*$info_product_usage_detail['unit_value'])
							];
							$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
						}										
					}
					$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
					// UPDATE AND DELETE STOCK MOVEMENT
					$where_stock_movement = [
						'invoice'        => $product_usage['code'],
						'product_code'	 => $info_product_usage_detail['product_code'],
						'type'			 => 10, // 1: Purchase, 2: Purchase Return, 3: POS, 4: Sales, 5: Sales Return, 6: Production, 7: Repacking, 8: Adjusment Stock, 9: Mutation
						'method'		 => 2, // 1:IN, 2:OUT
					];								
					$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
					$where_after_stock_movement = [
						'date >='       => $stock_movement['date'],
						'product_code'	=> $info_product_usage_detail['product_code'],
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
								'stock' => $info_stock_movement['stock']+($info_product_usage_detail['qty']*$info_product_usage_detail['unit_value'])
							];
							$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
						}
					}
					$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);
				}			
				$this->crud->update('product_usage', ['do_status' => 0], ['id' => $product_usage['id']]);
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'DO Penjualan berhasil dibatalkan');				
					$response   =   [
						'product_usage_id' => encrypt_custom($product_usage['id']),
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
					$this->session->set_flashdata('error', 'Transaksi Pembelian gagal diperbarui');
					$response   =   [
						'sales_invoice_id' => encrypt_custom($sales_invoice['id']),
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
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
				$response   =   [
					'sales_invoice_id' => encrypt_custom($sales_invoice['id']),
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
			}
			echo json_encode($response);
		}
	}

	public function print_product_usage_do($product_usage_id)
	{
		if($this->session->userdata('create_product_usage_do') == 1)
		{
			$this->session->unset_userdata('create_product_usage_do');
			$product_usage = $this->product_usage->detail_product_usage(decrypt_custom($product_usage_id));
			$warehouse     = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
									  ->from('product_usage_detail AS pugd')->join('warehouse', 'warehouse.id = pugd.warehouse_id')
									  ->where('pugd.product_usage_id', $product_usage['id'])
									  ->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
			foreach($warehouse AS $info_w)
			{
				$data_so = $this->db->select('product_usage.code')
									->from('product_usage')->join('product_usage_detail', 'product_usage_detail.product_usage_id = product_usage.id')
									->where('product_usage_detail.warehouse_id', $info_w['id_w'])
									->where('product_usage_detail.product_usage_id', $product_usage['id'])
									->where('product_usage_detail.deleted', 0)
									->group_by('product_usage.id')->order_by('product_usage.id', 'asc')->get()->result_array();
				$product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, product_usage_detail.qty AS qty, unit.code AS code_u')
								->from('product_usage_detail')->join('product', 'product.id = product_usage_detail.product_id')
								->where('product_usage_detail.warehouse_id', $info_w['id_w'])
								->where('product_usage_detail.product_usage_id', $product_usage['id'])
								->where('product.deleted', 0)->where('product_usage_detail.deleted', 0)
								->join('unit', 'unit.id = product_usage_detail.unit_id')
								->group_by('product_usage_detail.id')->order_by('product_code', 'asc')->get()->result_array();
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
					'code_sot' 	 => $product_usage['code'],
					'data_so'    => $data_so,
					'id_w' 		 => $info_w['id_w'],
					'code_w' 	 => $info_w['code_w'],
					'name_w' 	 => $info_w['name_w'],
					'product'	 => $data_product
				);
			}		
			$data = array(
				'perusahaan' 	=> $this->global->company(),
				'product_usage' => $product_usage,
				'sot'        	=> $sot
			);
			$this->load->view('product_usage/print_product_usage_do', $data);
		}
	}

	public function datatable_detail_product_usage($product_usage_id)
	{
		if($this->system->check_access('product_usage','detail'))
        {
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$this->datatables->select('product_usage_detail.id AS id, product.code AS code_p, product.name AS name_p, unit.name AS name_u, warehouse.name AS name_w, product_usage_detail.qty, product_usage_detail.price, product_usage_detail.total,
								product.code AS search_code_p');
				$this->datatables->from('product_usage_detail');
				$this->datatables->join('product', 'product.code = product_usage_detail.product_code');
				$this->datatables->join('unit', 'unit.id = product_usage_detail.unit_id');
				$this->datatables->join('warehouse', 'warehouse.id = product_usage_detail.warehouse_id');
				$this->datatables->where('product_usage_detail.product_usage_id', $product_usage_id);
				$this->datatables->where('product_usage_detail.deleted', 0);
				$this->datatables->group_by('product_usage_detail.id');
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
        else
        {
            $this->load->view('auth/show_404');
        }		      
	}

	public function detail_product_usage($product_usage_id)
    {
		if($this->system->check_access('product_usage','detail'))
		{
			$product_usage = $this->product_usage->detail_product_usage(decrypt_custom($product_usage_id));
			if($product_usage != null)
			{
				$data_activity = [
					'information' => 'MELIHAT DETAIL PEMAKAIAN (NO.TRANSAKSI: '.$product_usage['code'].')',
					'method'      => 2, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Detail Pemakaian");
				$data = [
					'product_usage' => $product_usage
				];
				$footer = array("script" => ['inventory/product_usage/detail_product_usage.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product_usage/detail_product_usage', $data);
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
			redirect(site_url('sales/invoice'));
		}		
	}	

	public function delete_product_usage()
	{
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post = $this->input->post();
			$product_usage = $this->product_usage->detail_product_usage($post['product_usage_id']);
			// DELETE_PRODUCT_USAGE_DETAIL
			$this->crud->delete('product_usage_detail', ['product_usage_id' => $product_usage['id']]);			
			// DELETE_PRODUCT_USAGE
			$this->crud->delete('product_usage', ['id' => $product_usage['id']]);
			$data_activity = [
				'information' => 'MENGHAPUS PEMAKAIAN (NO.TRANSAKSI '.$product_usage['code'].')',
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
			$this->session->set_flashdata('success', 'BERHASIL! Pemakaian Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}								
	}
}