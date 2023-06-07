<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashier extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('master/Product_model','product');
		$this->load->model('master/Customer_model','customer');
		$this->load->model('Cashier_model','cashier');
	}

	public function open()
	{
		if($this->system->check_access('pos', 'create'))
        {
			if($this->input->method() === 'post')
			{
				$this->form_validation->set_rules('modal', 'Modal Kasir', 'trim|required|xss_clean');			
				$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "POS");		
					$footer = array("script" => ['pos/cashier/opencashier.js']);	
					$this->load->view('include/header', $header);
					$this->load->view('include/topbar-cashier');		
					$this->load->view('cashier/newopen');
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$post = $this->input->post();
					$data = array(
						'date'			=> date('Y-m-d'),
						'open_time'		=> date('H:i:s'),
						'modal' 		=> format_amount($post['modal']),
						'cashier' 		=> $this->session->userdata('code_e'),
						'status'		=> 0
					);
					$insert = $this->crud->insert('cashier', $data);
					if($insert)
					{
						$data_activity = array (
							'information' => 'MELAKUKAN PEMBUKAAN KASIR ( CODE - '.$this->session->userdata('code_e').')',
							'method'	  => 3,
							'user_id' 	  => $this->session->userdata('id_u')
						);
						$this->crud->insert('activity',$data_activity);
						$this->session->set_flashdata('success', 'Modal Kasir berhasil ditambahkan. Selamat beraktivitas kembali, jangan lupa tersenyum hari ini...');
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Modal Kasir gagal untuk ditambahkan. Silahkan hubungi admin, terima kasih.');
					}			
					redirect(site_url('pos/cashier'));
				}
			}
			else
			{
				$where = array(				
					'cashier' 		=> $this->session->userdata('code_e'),	
					'status'		=> 0			
				);
				$check_cashier = $this->crud->get_where('cashier', $where);
				if($check_cashier->num_rows() > 0)
				{
					redirect(site_url('pos/cashier'));
				}
				else
				{								
					$header = array("title" => "POS");		
					$footer = array("script" => ['pos/cashier/opencashier.js']);	
					$this->load->view('include/header', $header);
					$this->load->view('include/topbar-cashier');		
					$this->load->view('cashier/newopen');
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

	public function get_product()
	{
		if($this->input->is_ajax_request())
		{
			$search = urldecode($this->uri->segment(4));	
			$data = $this->cashier->get_product($search);
			$response 	    = array();
			if($data->num_rows() > 0){
				foreach($data->result_array() as $info)
				{
					$response[] = array(
						'barcode' => $info['barcode_p'],
						'code'    => $info['code_p'],
						'name'    => $info['name_p'],
						'price_1' => $info['price_1']
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
	
	public function scan_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			if($post['product_code'] != null || $post['product_code'] != "")
			{
				
				$data = $this->cashier->scan_product($post['customer_code'],$post['product_code'], 'barcode');
				if($data !=null)
				{
					$response   = [
						'status'    => [
							'code'      => 200,
							'message'   => 'Data Ditemukan',
						],
						'data'      => $data
					];
				}				
				else
				{
					$data2 = $this->cashier->scan_product($post['customer_code'],$post['product_code'], 'code');
					if($data2 != null)
					{
						$response   = [
							'status'    => [
								'code'      => 200,
								'message'   => 'Data Ditemukan',
							],
							'data'      => $data2
						];
					}
					else
					{
						$response   = [
							'status'    => [
								'code'      => 400,
								'message'   => 'Data Tidak Ditemukan',
							],
							'data'      => ''
						];
					}			
				}
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 400,
						'message'   => 'Data Tidak Ditemukan',
					],
					'data'      => ''
				];
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
			$product_code = $this->input->post('product_code');
			$where = array(
				'product_unit.product_code'  => $product_code,
				'product_unit.deleted'       => 0
			);
			$unit       = $this->cashier->get_unit($where)->result_array();$option = null;
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

	public function get_sellprice()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$response = array(
				'price' => $this->cashier->get_sellprice($post['customer_code'], $post['product_code'], $post['unit_id'])
			);
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}				
	}

	public function check_sellprice()
	{
		if($this->input->is_ajax_request())
		{
			$result = 1; $post = $this->input->post();
			$last_buyprice = $this->product->last_buyprice($post['product_code']);
			$where_product_unit = array(
				'product_code' => $post['product_code'],
				'unit_id'	=> $post['unit_id'],
				'deleted'	=> 0
			);
			$product_unit = $this->crud->get_where('product_unit', $where_product_unit)->row_array();
			$buyprice = $last_buyprice['price']*$product_unit['value'];
			if($post['price'] <= $buyprice)
			{
				$result = 0;
			}
			echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function get_bank()
	{
		if($this->input->is_ajax_request())
		{
			$bank = $this->crud->get_where('bank',['deleted'=>0])->result_array();		
			$lists = "<option value=''>-- PILIH BANK --</option>";		
			foreach($bank as $data)
			{
				
				$lists .= "<option value='".$data['id']."'>".$data['name']."</option>";
				
			}		
			$callback = array
			(
				'list_bank'=>$lists
			);
			echo json_encode($callback);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function close()
	{
		if($this->system->check_access('pos', 'create'))
        {
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				// Data Cashier
				$cashier= $this->crud->get_where('cashier', ['status' => 0, 'cashier' => $this->session->userdata('code_e')])->row_array();
				// Data DP
				$where_dp = [
					'date' 			=> $cashier['date'], 
					'employee_code' => $cashier['cashier'],
					'payment'       => 2,
					'created >=' 	=> $cashier['date'].' '.$cashier['open_time'], 
					'created <=' 	=> $cashier['date'].' 23:59:59',
					'deleted' 		=> 0
				];
				$data_dp = $this->crud->get_where('sales_invoice', $where_dp)->result_array();
				$total_dp = 0;
				foreach($data_dp AS $info_dp)
				{
					$total_dp = $total_dp + $info_dp['down_payment'];
				}
				// Data Sales
				$where_sales = [
					'date' 			=> $cashier['date'], 
					'employee_code' => $cashier['cashier'],
					'payment'       => 1,
					'created >=' 	=> $cashier['date'].' '.$cashier['open_time'], 
					'created <=' 	=> $cashier['date'].' 23:59:59',	
					'deleted' 		=> 0
				];
				$data_sales = $this->crud->get_where('sales_invoice', $where_sales)->result_array();
				$total_sales = 0;
				foreach($data_sales AS $info_sales)
				{
					$total_sales = $total_sales + $info_sales['grandtotal'];
				}
				// Data POS
				$where_pos = [
					'date' => $cashier['date'], 
					'time >=' => $cashier['open_time'], 
					'time <=' => '23:59:59',
					'cashier' => $cashier['cashier'],
					'deleted' => 0
				];
				$data_pos= $this->crud->get_where('pos', $where_pos)->result_array();
				$total_pos = 0;
				foreach($data_pos AS $info_pos)
				{
					$total_pos = $total_pos + $info_pos['grandtotal'];
				}
				// Data Retur Jual
				$where_sales_return = [
					'date' => $cashier['date'], 
					'employee_code' => $cashier['cashier'],
					'created >=' => $cashier['date'].' '.$cashier['open_time'], 
					'created <=' => $cashier['date'].' 23:59:59',
					'deleted'    => 0
				];
				$data_sales_return= $this->crud->get_where('sales_return', $where_sales_return)->result_array();
				$total_sales_return = 0;
				foreach($data_sales_return AS $info_sales_return)
				{
					$total_sales_return = $total_sales_return + $info_sales_return['total_return'];
				}
				// Data Biaya
				$where_expense = [
					'date' => $cashier['date'], 
					'employee_code' => $cashier['cashier'],
					'created >=' => $cashier['date'].' '.$cashier['open_time'],
					'created <=' => $cashier['date'].' 23:59:59',
					'deleted' 	 => 0
				];
				$data_expense= $this->crud->get_where('expense', $where_expense)->result_array();
				$total_expense = 0;
				foreach($data_expense AS $info_expense)
				{
					$total_expense = $total_expense + $info_expense['amount'];
				}
				// Data Collect
				$where_collect = [
					'date' => $cashier['date'], 
					'cashier' => $cashier['cashier'],
					'time >=' => $cashier['open_time'], 
					'time <=' => "23:59:59", 
					'deleted' => 0				
				];
				$data_collect= $this->crud->get_where('collect', $where_collect)->result_array();
				$total_collect = 0;
				foreach($data_collect AS $info_collect)
				{
					$total_collect = $total_collect + $info_collect['total'];
				}						
				$grandtotal = $cashier['modal'] + $total_dp + $total_sales + $total_pos - $total_sales_return - $total_collect - $total_expense;
				$data = array(
					'employee_code'		 => $post['close_code_e'],
					'close_time'		 => "23:59:59",
					'total_dp'		     => $total_dp,
					'total_sales'		 => $total_sales,
					'total_pos'		     => $total_pos,
					'total_sales_return' => $total_sales_return,
					'total_expense'		 => $total_expense,
					'total_collect'		 => $total_collect,
					'grandtotal'		 => $grandtotal,
					'status'			 => 1
				);
				$update = $this->crud->update('cashier', $data, ['id' => $cashier['id']]);
				if($update)
				{
					$data_activity = array (
						'information' => 'MELAKUKAN PENUTUPAN KASIR OTOMATIS ( CODE - '.$this->session->userdata('code_e').')',
						'method'	  => 3,
						'user_id' 	  => $this->session->userdata('id_u')
					);
					$this->crud->insert('activity',$data_activity);
					redirect(base_url('pos/cashier/summary/'. $this->global->encrypt($cashier['id'])));
				}
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

	public function index()
	{
		if($this->system->check_access('pos', 'create'))
        {
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();			
				$invoice = $this->cashier->invoice();
				$customer = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
				if($post['payment'] != 0)
				{
					$data_card=array(
						'bank_id'		=> $post['bank_id'],
						'card_number'	=> $post['card_number'],
						'card_holder'	=> $post['card_holder'],
						'type'			=> $post['payment']
					);
					$card_id = $this->crud->insert_id('card',$data_card);
				}
				else
				{
					$card_id = null;
				}
				$total_qty = 0;
				foreach($post['qty'] AS $info_qty)
				{
					$total_qty = $total_qty + $info_qty;
				}
				$ppn		    = (!isset($post['ppn'])) ?  0 : $post['ppn'];
				$data_pos=array(
					'date'			=> date("Y-m-d"),
					'time'			=> date("H:i:s"),
					'cashier'		=> $this->session->userdata('code_e'),
					'customer_code'	=> $post['customer_code'],
					'invoice'		=> $invoice,
					'total_product'	=> sizeof($post['product_code']),
					'total_qty'		=> $total_qty,
					'grandtotal'	=> format_amount($post['grandtotal']),
					'pay'			=> format_amount($post['pay']),
					'payment'		=> $post['payment'],
					'card_id'		=> $card_id,
					'ppn'			=> $ppn
				);
				$pos_id=$this->crud->insert_id('pos',$data_pos);
				if($pos_id != null)
				{
					$warehouse_id = $this->crud->get_where('warehouse', ['deleted' => 0, 'default' =>1])->row_array();
					$total_hpp = 0;
					for($i=0;$i<sizeof($post['product_code']);$i++)
					{
						$res = 0;
						$product_id = $this->crud->get_product_id($post['product_code'][$i]);
						$hpp = $this->product->hpp($post['product_code'][$i]);
						$convert     = $this->crud->get_where('product_unit', ['product_code' => $post['product_code'][$i], 'unit_id' => $post['unit_id'][$i], 'deleted' => 0])->row_array();
						$data_pos_detail = array(
							'pos_id'		=> $pos_id,
							'invoice'		=> $invoice,
							'product_id'	=> $product_id,
							'product_code'	=> $post['product_code'][$i],
							'qty'			=> format_amount($post['qty'][$i]),
							'unit_id'		=> $post['unit_id'][$i],
							'unit_value'	=> ($convert['value'] != null) ? $convert['value'] : 1,
							'warehouse_id'  => $warehouse_id['id'],
							'price'			=> format_amount($post['price'][$i]),
							'discount_p'	=> (isset($post['discount_p']))? format_amount($post['discount_p'][$i]) : 0,
							'total'			=> format_amount($post['total'][$i]),
							'hpp'			=> $hpp,
							'ppn'			=> $ppn
						);
						if($this->crud->insert('pos_detail', $data_pos_detail))
						{
							$check_stock = $this->crud->get_where('stock', ['product_code' => $post['product_code'][$i], 'warehouse_id' => $warehouse_id['id']]); $stock = $check_stock->row_array();							
							$qty 		 = $stock['qty'] - (format_amount($post['qty'][$i])*$convert['value']);
							$total_hpp   = $total_hpp + ($hpp*(format_amount($post['qty'][$i])*$convert['value']));
							if($check_stock->num_rows() == 1)
							{														
								$where_stock = array(
									'product_code'  => $post['product_code'][$i],
									'warehouse_id'  => $warehouse_id['id']
								);       							
								$stock = array(                                
									'product_id'    => $product_id,
									'qty'           => $qty,
								);
								$update_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{
								$stock = array(                                
									'product_id'    => $product_id,
									'product_code'  => $post['product_code'][$i],                                                        
									'qty'           => $qty,
									'warehouse_id'  => $warehouse_id['id']
								);
								$update_stock = $this->crud->insert('stock', $stock);
							}                            
							if($update_stock)
							{
								$data_stock_card = array(
									'transaction_id'  => $pos_id,
									'invoice'         => $invoice,
									'product_id'      => $product_id,
									'product_code'    => $post['product_code'][$i],
									'qty'             => format_amount($post['qty'][$i])*$convert['value'],
									'information'     => 'POS ('.$customer['name'].')',
									'type'            => 3, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Stock Opname, 9:Mutation
									'method'          => 2, // 1:In, 2:Out
									'stock'           => $qty,
									'warehouse_id'    => $warehouse_id['id'],
									'user_id'         => $this->session->userdata('id_u')
								);
								$stock_card= $this->crud->insert('stock_card',$data_stock_card);
								if($stock_card)
								{
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
						else
						{
							break;
						}
					}

					$this->crud->update('pos', ['total_hpp' => $total_hpp], ['id' => $pos_id]);
					if($res == 1)
					{
						$this->session->set_flashdata('success', 'Data Transaksi POS berhasil ditambahkan');
						redirect(site_url('pos/cashier/result/'.$this->global->encrypt($pos_id)));
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Data Transaksi POS gagal ditambahkan');
						redirect(site_url('pos/cashier/'));
					}
				}
				else
				{
					echo "GAGAL";
				}
				// echo json_encode($this->input->post());
			}
			else
			{
				$where = array(			
					'cashier' 		=> $this->session->userdata('code_e'),
					'status'		=> 0
				);
				$check_cashier = $this->crud->get_where('cashier', $where);
				if($check_cashier->num_rows() > 0)
				{
					$header = array("title" => "POS");
					$cashier = $check_cashier->row_array();		
					// $footer = array("script" => ['pos/cashier/cashier.js?v='.md5(time()), 'pos/cashier/panelcashier.js?v='.md5(time())]);
					$footer = array("script" => ['pos/cashier/cashier.js', 'pos/cashier/panelcashier.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/topbar-cashier');
					$this->load->view('cashier/cashier', $cashier);
					$this->load->view('include/footer', $footer);
				}
				else
				{
					redirect(site_url('pos/cashier/open'));
				}			
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }				
	}

	public function result($pos_id)
	{
		$pos = $this->cashier->detail_pos($this->global->decrypt($pos_id));
		if($pos != null)
		{
			$header = array("title" => "POS");				
			$data = array(
				'pos' => $pos,
				'pos_detail' => $this->cashier->detail_pos_detail($pos['id_p'])
			);
			$footer = array("script" => ['pos/cashier/result_cashier.js']);	
			$this->load->view('include/header', $header);
			$this->load->view('include/topbar-cashier');		
			$this->load->view('cashier/result_cashier', $data);
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function print_bill($pos_id)
	{
		if($this->system->check_access('pos', 'create'))
        {
			$pos = $this->cashier->detail_pos($this->global->decrypt($pos_id));
			if($pos != null)
			{
				$data = array(
					'perusahaan' => $this->global->perusahaan(),
					'pos' => $pos,
					'pos_detail' => $this->cashier->detail_pos_detail($pos['id_p'])
				);
				$this->load->view('cashier/print_bill',$data);
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

	public function add_customer()
	{
		if($this->system->check_access('customer', 'create'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$code       = $this->customer->customer_code();
				$data_cust  = [
					'code'          => $code,
					'name'          => $post['name_customer'],                                
					'address'       => null,
					'province_id'   => (!isset($post['province'])) ?  null : $post['province'],
					'city_id'       => (!isset($post['city'])) ?  null : $post['city'],
					'phone'         => (!isset($post['phone'])) ?  null : $post['phone'],
					'telephone'     => (!isset($post['telephone'])) ?  null : $post['telephone'],                
					'contact'       => (!isset($post['contact'])) ?  null : $post['contact'],
					'email'         => (!isset($post['email'])) ?  null : $post['email'],
					'credit'        => 0,
					'dueday'        => 0,
					'price_class'   => $post['price_class'],  
					'pkp'           => (!isset($post['pkp'])) ?  0 : $post['pkp'],
					'npwp'          => (!isset($post['pkp'])) ?  null : $post['npwp'],
					'zone_id'       => (!isset($post['zone'])) ?  null : $post['zone']
				];
				
				if($this->crud->insert('customer', $data_cust))
				{
					$data_activity = [
						'information' => 'MEMBUAT PELANGGAN BARU (CODE - '.$code.')',
						'method'      => 3,
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];
					$this->crud->insert('activity', $data_activity);                        
					$response   = [
						'status'    => [
							'code'      => 200,
							'message'   => 'SUKSES! Pelanggan berhasil disimpan',
						]
					];
					
				}
				else
				{	
					$response   = [
						'status'    => [
							'code'      => 401,
							'message'   => 'Mohon Maaf, pelanggan gagal disimpan',
						]				
					];				
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

        }		
	}
	public function collect()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$data_collect = [
				'date' 		=> date('Y-m-d'),
				'time'   	=> date('H:i:s'),
				'collector' => $post['employee_code'],
				'cashier' 	=> $this->session->userdata('code_e'),
				'total' 	=> format_amount($post['collect_amount']),
			];
			$collect_id = $this->crud->insert_id('collect', $data_collect);
			if($collect_id != null)
			{
				$response   = [
					'status'    => [
						'code'      => 200,
						'message'   => 'SUKSES! Data Collect berhasil ditambahkan',
					],
					'collect_id'  => $this->global->encrypt($collect_id)
				];
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 401,
						'message'   => 'Mohon Maaf, transaksi collect gagal ditambahkan',
					]				
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function print_collect($collect_id)
	{
		$collect = $this->cashier->detail_collect($this->global->decrypt($collect_id));
		if($collect != null)
		{
			$data = [
				'perusahaan' => $this->global->perusahaan(),
				'collect'    => $collect
			];
			$this->load->view('cashier/print_collect', $data);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function summary($cashier_id = null)
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			// Data Cashier
			$cashier= $this->crud->get_where('cashier', ['status' => 0, 'cashier' => $this->session->userdata('code_e')])->row_array();
			// Data DP
			$where_dp = [
				'date' 			=> $cashier['date'], 
				'employee_code' => $cashier['cashier'],
				'payment'       => 2,
				'created >=' 	=> $cashier['date'].' '.$cashier['open_time'], 
				'created <=' 	=> date('Y-m-d H:i:s'),
				'deleted' 		=> 0		
			];
			$data_dp = $this->crud->get_where('sales_invoice', $where_dp)->result_array();
			$total_dp = 0;
			foreach($data_dp AS $info_dp)
			{
				$total_dp = $total_dp + $info_dp['down_payment'];
			}
			// Data Sales
			$where_sales = [
				'date' 			=> $cashier['date'], 
				'employee_code' => $cashier['cashier'],
				'payment'       => 1,
				'created >=' 	=> $cashier['date'].' '.$cashier['open_time'], 
				'created <=' 	=> date('Y-m-d H:i:s'),
				'deleted' 		=> 0		
			];
			$data_sales = $this->crud->get_where('sales_invoice', $where_sales)->result_array();
			$total_sales = 0;
			foreach($data_sales AS $info_sales)
			{
				$total_sales = $total_sales + $info_sales['grandtotal'];
			}
			// Data POS
			$where_pos = [
				'date' => $cashier['date'], 
				'time >=' => $cashier['open_time'], 
				'time <=' => date('H:i:s'),
				'cashier' => $cashier['cashier'],
				'deleted' => 0
			];
			$data_pos= $this->crud->get_where('pos', $where_pos)->result_array();
			$total_pos = 0;
			foreach($data_pos AS $info_pos)
			{
				$total_pos = $total_pos + $info_pos['grandtotal'];
			}
			// Data Retur Jual
			$where_sales_return = [
				'date' => $cashier['date'], 
				'employee_code' => $cashier['cashier'],
				'created >=' => $cashier['date'].' '.$cashier['open_time'], 
				'created <=' => date('Y-m-d H:i:s'),
				'deleted' 	 => 0
			];
			$data_sales_return= $this->crud->get_where('sales_return', $where_sales_return)->result_array();
			$total_sales_return = 0;
			foreach($data_sales_return AS $info_sales_return)
			{
				$total_sales_return = $total_sales_return + $info_sales_return['total_return'];
			}
			// Data Biaya
			$where_expense = [
				'date' => $cashier['date'], 
				'employee_code' => $cashier['cashier'],
				'created >=' => $cashier['date'].' '.$cashier['open_time'],
				'created <=' => date('Y-m-d H:i:s'),
				'deleted' 	 => 0			
			];
			$data_expense= $this->crud->get_where('expense', $where_expense)->result_array();
			$total_expense = 0;
			foreach($data_expense AS $info_expense)
			{
				$total_expense = $total_expense + $info_expense['amount'];
			}
			// Data Collect
			$where_collect = [
				'date' => $cashier['date'], 
				'cashier' => $cashier['cashier'],
				'time >=' => $cashier['open_time'], 
				'time <=' => date('H:i:s'),
				'deleted' => 0			
			];
			$data_collect= $this->crud->get_where('collect', $where_collect)->result_array();
			$total_collect = 0;
			foreach($data_collect AS $info_collect)
			{
				$total_collect = $total_collect + $info_collect['total'];
			}						
			$grandtotal = $cashier['modal'] + $total_dp + $total_sales + $total_pos - $total_sales_return - $total_collect - $total_expense;
			$data = array(
				'employee_code'		 => $post['close_code_e'],
				'close_time'		 => date('H:i:s'),
				'total_dp'		     => $total_dp,
				'total_sales'		 => $total_sales,
				'total_pos'		     => $total_pos,
				'total_sales_return' => $total_sales_return,
				'total_expense'		 => $total_expense,
				'total_collect'		 => $total_collect,
				'grandtotal'		 => $grandtotal,
				'status'			 => 1
			);
			$update = $this->crud->update('cashier', $data, ['id' => $cashier['id']]);
			if($update)
			{
				$data_activity = array (
					'information' => 'MELAKUKAN PENUTUPAN KASIR ( CODE - '.$this->session->userdata('code_e').')',
					'method'	  => 3,
					'user_id' 	  => $this->session->userdata('id_u')
				);
				$this->crud->insert('activity',$data_activity);
				redirect(base_url('pos/cashier/summary/'. $this->global->encrypt($cashier['id'])));
			}						
		}
		else
		{
			$cashier	= $this->cashier->detail_cashier($this->global->decrypt($cashier_id));
			if($cashier['status'] == 0)
			{
				redirect(site_url('pos/cashier'));
			}
			elseif($cashier['status'] == 1)
			{				
				$header = array(
					"title" => "POS"
				);				
				$open_time = $cashier['open_time']; $close_time = $cashier['close_time'];
				$data = array(
					'cashier'      => $cashier,					
					'dp'           => $this->cashier->summary_dp($cashier['date'], $cashier['cashier'], $open_time, $close_time),
					'sales'        => $this->cashier->summary_sales($cashier['date'], $cashier['cashier'], $open_time, $close_time),
					'pos'      	   => $this->cashier->summary_pos($cashier['date'], $cashier['cashier'], $open_time, $close_time),
					'sales_return' => $this->cashier->summary_sales_return($cashier['date'], $cashier['cashier'], $open_time, $close_time),
					'expense'      => $this->cashier->summary_expense($cashier['date'], $cashier['cashier'], $open_time, $close_time),
					'collect'      => $this->cashier->summary_collect($cashier['date'], $cashier['cashier'], $open_time, $close_time),
				);
				$footer = array("script" => ['pos/cashier/summary.js']);							
				$this->load->view('include/header', $header);
				$this->load->view('include/topbar-cashier');		
				$this->load->view('cashier/summary_cashier', $data);
				$this->load->view('include/footer', $footer);			
			}
			else
			{
				$this->load->view('auth/show_404');
			}
		}
	}

	public function print_summary($cashier_id = null)
	{
		$cashier	= $this->cashier->detail_cashier(decrypt_custom($cashier_id));
		if($cashier != null)
		{
			$open_time = $cashier['open_time']; $close_time = $cashier['close_time'];
			$data = array(
				'perusahaan'   => $this->global->perusahaan(),
				'cashier'      => $cashier,					
				'dp'           => $this->cashier->summary_dp($cashier['date'], $cashier['cashier'], $open_time, $close_time),
				'sales'        => $this->cashier->summary_sales($cashier['date'], $cashier['cashier'], $open_time, $close_time),
				'pos'      	   => $this->cashier->summary_pos($cashier['date'], $cashier['cashier'], $open_time, $close_time),
				'sales_return' => $this->cashier->summary_sales_return($cashier['date'], $cashier['cashier'], $open_time, $close_time),
				'expense'      => $this->cashier->summary_expense($cashier['date'], $cashier['cashier'], $open_time, $close_time),
				'collect'      => $this->cashier->summary_collect($cashier['date'], $cashier['cashier'], $open_time, $close_time),
			);
			$mpdf = new \Mpdf\Mpdf([
				'format' => [76, 297],
				'orientation' => 'P',
				'margin_left' => 7,
				'margin_right' => 7,
				'margin_top' => 4,
				'margin_bottom' => 4,
				'margin_header' => 2,
				'margin_footer' => 2,
			]);
			$data = $this->load->view('cashier/print_summary', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}		
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	function verify_discount_p_password()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post(); $res = 0;
			$module_url = 'discount_pos'; $action="read";
			$bypass = $this->cashier->bypass_module_password();
			foreach($bypass AS $info_bypass)
			{
				if(password_verify($post['verifypassword'], $info_bypass['password']))
				{
					$res++;
				}			
			}
			$data_user = $this->cashier->verify_module_password($module_url, $action);
			foreach($data_user AS $info_user)
			{
				if(password_verify($post['verifypassword'], $info_user['password']))
				{
					$res++;
				}				
			}
			if($res > 0)
			{
				$response   = [
					'status'    => [
						'code'      => 200,
						'message'   => 'SUKSES! Verif berhasil',
					]					
				];
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 401,
						'message'   => 'Mohon Maaf, verif gagal',
					]				
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	function verify_close_cashier_password()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post(); $res = 0;
			$module_url = $post['module_url']; $action = $post['action'];
			$bypass = $this->cashier->bypass_module_password();
			foreach($bypass AS $info_bypass)
			{
				if(password_verify($post['verifypassword'], $info_bypass['password']))
				{
					$res++;
					$code_e = $info_bypass['code_e'];
					break;					
				}		
			}
			if($res==0)
			{
				$data_user = $this->cashier->verify_module_password($module_url, $action);
				foreach($data_user AS $info_user)
				{
					if(password_verify($post['verifypassword'], $info_user['password']))
					{
						$res++;
						$code_e = $info_bypass['code_e'];
						break;
					}							
				}
				if($res > 0)
				{
					$response   = [
						'status'    => [
							'code'      => 200,
							'code_e'    => $code_e,
							'message'   => 'SUKSES! Verifikasi berhasil',
						]					
					];
				}
				else
				{
					$response   = [
						'status'    => [
							'code'      => 401,
							'message'   => 'Mohon Maaf, verifikasi gagal',
						]				
					];
				}				
			}	
			else
			{
				$response   = [
					'status'    => [
						'code'      => 200,
						'code_e'    => $code_e,
						'message'   => 'SUKSES! Verifikasi berhasil',
					]					
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
