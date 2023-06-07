<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mutation extends System_Controller 
{	
    public function __construct()
	{
		parent::__construct();
		$this->load->model('transaction/Purchase_model','purchase');
		$this->load->model('transaction/Sales_model','sales');
		$this->load->model('Mutation_model','mutation');
	}
		
    public function index()
    {
		if($this->system->check_access('mutation', 'read'))
        {
			if($this->input->is_ajax_request())
			{                                
				$post = $this->input->post();
				$this->datatables->select('mutation.id, mutation.code AS code, mutation.date AS date, mutation.total_product, checker.name AS checker, operator.name AS operator, mutation.do_status AS do_status,
								   mutation.code AS search_code');
				$this->datatables->from('mutation');
				$this->datatables->join('employee AS checker', 'checker.code = mutation.checker');
				$this->datatables->join('employee AS operator', 'operator.code = mutation.operator');
				$this->datatables->where('mutation.deleted', 0);
				if($post['do_status'] != "")
				{
					if($post['do_status'] == 0)
					{
						$this->datatables->where('mutation.do_status', $post['do_status']);
					}
					elseif($post['do_status'] == 1)
					{
						$this->datatables->where('mutation.do_status', $post['do_status']);
						$this->datatables->where('DATE(mutation.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
						$this->datatables->where('DATE(mutation.created) <=', date('Y-m-d'));
					}
				}
				else
				{
					$this->datatables->where('DATE(mutation.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
					$this->datatables->where('DATE(mutation.created) <=', date('Y-m-d'));					
				}
				$this->datatables->group_by('mutation.id');		
				$this->datatables->add_column('code',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('mutation/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
                ', 'encrypt_custom(id), code');
                header('Content-Type: application/json');
				echo $this->datatables->generate();
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT DAFTAR MUTASI',
					'method'      => 1,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array(
					"title" => "Mutasi"
				  );  
				$footer = array("script" => ['inventory/mutation/mutation.js']);                                                       
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('mutation/mutation');        
				$this->load->view('include/footer', $footer);
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}  
	
	public function get_to_warehouse_mutation()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$warehouse = $this->sales->get_warehouse($post['product_code'], $post['unit_id']);
			$option		= '';		
			foreach($warehouse as $data)
			{
				if($data['id_w']==$post['from_warehouse_id'])
				{
					continue;
				}
				else
				{
					$option .= "<option value='".$data['id_w']."'>".$data['code_w']." | ".number_format($data['stock'], 2, '.', ',')."</option>";
				}			
			}		
			$result = array
			(
				'option'=>$option
			);
			echo json_encode($result);
		}
	}
    
    public function create()
	{
		if($this->system->check_access('mutation', 'create'))
        {
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->form_validation->set_rules('date', 'Tanggal Mutasi', 'trim|required|xss_clean');
				$this->form_validation->set_rules('checker_code', 'Petugas', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
				if($this->form_validation->run() == FALSE)
				{
					$header = array(
						"title" => "Mutasi Baru"
					);                                                          
					$footer = array("script" => ['inventory/mutation/create_mutation.js']);
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('mutation/create_mutation');            
					$this->load->view('include/footer',$footer);
				}
				else
				{
					$this->db->trans_start();
					$code = $this->mutation->mutation_code();
					$data_mutation = array(
						'code' 				=> $code,
						'date' 				=> format_date($post['date']),
						'checker'			=> $post['checker_code'],
						'operator'			=> $this->session->userdata('code_e'),
						'total_product'  	=> $post['total_product'],
						'total_qty' 		=> $post['total_qty']
					);					
					$mutation_id = $this->crud->insert_id('mutation', $data_mutation);
					if($mutation_id != null)
					{
						foreach($post['product'] as $info)
						{
							$res = 0;
							$product_id = $this->crud->get_product_id($info['product_code']);
							$product_unit = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
							$data_mutation_detail = array(
								'mutation_id'  => $mutation_id,
								'product_id'   => $product_id,
								'product_code' => $info['product_code'],
								'qty' 		   => format_amount($info['qty']),
								'unit_id' 	   => $info['unit_id'],
								'unit_value'   => ($product_unit['value'] != null) ? $product_unit['value'] : 1,
								'from_warehouse_id' => $info['from_warehouse_id'],
								'to_warehouse_id'   => $info['to_warehouse_id']
							);
							if($this->crud->insert('mutation_detail', $data_mutation_detail))
							{
								$res = 1;
							}
							else
							{
								break;
							}
						}
					}
					$this->db->trans_complete();
					if($res == 1 && $this->db->trans_status() === TRUE)
					{
						$this->db->trans_commit();
						$data_activity = [
							'information' => 'MEMBUAT MUTASI BARU',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];						
						$this->crud->insert('activity', $data_activity);
						$this->session->set_flashdata('success', 'Mutasi berhasil tersimpan');
						redirect(site_url('mutation/detail/'.encrypt_custom($mutation_id)));
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Mutasi gagal disimpan');
						redirect(site_url('mutation'));
					}					
				}
			}
			else
			{
				$header = array(
					"title" => "Mutasi Baru"
				);                                                          
				$footer = array("script" => ['inventory/mutation/create_mutation.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('mutation/create_mutation');            
				$this->load->view('include/footer',$footer);				
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('mutation'));
        }        
    }
	
	public function detail($mutation_id)
	{
		if($this->system->check_access('mutation', 'detail'))
        {
			if($this->input->is_ajax_request())
			{				
				header('Content-Type: application/json');
				$this->datatables->select('product.id, product.code AS code_p, product.name AS name_p, mutation_detail.qty, unit.name AS name_u, from_warehouse.name AS name_fw, to_warehouse.name AS name_tw');
				$this->datatables->from('mutation_detail');
				$this->datatables->join('product', 'product.id = mutation_detail.product_id');
				$this->datatables->join('unit', 'unit.id = mutation_detail.unit_id');
				$this->datatables->join('warehouse AS from_warehouse', 'from_warehouse.id = mutation_detail.from_warehouse_id');
				$this->datatables->join('warehouse AS to_warehouse', 'to_warehouse.id = mutation_detail.to_warehouse_id');
				$this->datatables->where('mutation_detail.mutation_id', $mutation_id);
				$this->datatables->group_by('mutation_detail.id');
				$this->datatables->add_column('code_p',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(code_p), code_p');
				echo $this->datatables->generate();
			}
			else
			{
				$mutation = $this->mutation->detail_mutation(decrypt_custom($mutation_id));
				$data_activity = [
					'information' => 'MELIHAT DETAIL MUTASI (CODE '.$mutation['code'].')',
					'method'      => 2,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Detail Mutasi");
				$data = array(
					'mutation' => $mutation
				);
				$footer = array("script" => ['inventory/mutation/detail_mutation.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('mutation/detail_mutation', $data);
				$this->load->view('include/footer', $footer);
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }						
	}

	public function create_do()
    {
		if($this->system->check_access('mutation','create'))
		{			
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$mutation = $this->mutation->detail_mutation($post['mutation_id']);
				if($mutation['do_status'] == 0)
				{
					$mutation_detail = $this->mutation->detail_mutation_detail($mutation['id']);
					$check_stock_mutation_do = $this->mutation->check_stock_mutation_do($mutation, $mutation_detail);
					if($check_stock_mutation_do['total'] == 0)
					{
						foreach($mutation_detail as $info)
						{
							$qty_convert = $info['qty']*$info['unit_value'];
							// FROM WAREHOUSE
							$check_from_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['from_warehouse_id']]);
							if($check_from_stock->num_rows() == 1)
							{
								$data_from_stock = $check_from_stock->row_array();
								$from_qty = $data_from_stock['qty'];
								$where_stock = array(
									'product_code'  => $info['product_code'],
									'warehouse_id'  => $info['from_warehouse_id']
								);       
								$qty = $from_qty - $qty_convert;                                
								$stock = array(                                
									'product_id'    => $info['product_id'],
									'qty'           => $qty,
								);
								$minus_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{                                                               
								$qty = 0 - $qty_convert;
								$stock = array(                                
									'product_id'    => $info['product_id'],
									'product_code'  => $info['product_code'],                                                        
									'qty'           => $qty,
									'warehouse_id'  => $info['from_warehouse_id']
								);
								$minus_stock = $this->crud->insert('stock', $stock);
							}                            
							if($minus_stock)
							{
								$data_stock_card = array(
									'date'			  => $mutation['date'],
									'transaction_id'  => $mutation['id'],
									'invoice'         => $mutation['code'],
									'product_id'      => $info['product_id'],
									'product_code'    => $info['product_code'],
									'qty'             => $qty_convert,
									'information'     => 'MUTASI',
									'note'			  => $mutation['code'],
									'type'            => 9, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Stock Opname, 9:Mutation												
									'method'          => 2, // 1:IN, 2:OUT
									'stock'           => $qty,
									'warehouse_id'    => $info['from_warehouse_id'],
									'user_id'         => $this->session->userdata('id_u')
								);							
								if($this->crud->insert('stock_card',$data_stock_card))
								{
									// TO WAREHOUSE
									$check_to_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['to_warehouse_id']]);
									if($check_to_stock->num_rows() == 1)
									{
										$data_to_stock = $check_to_stock->row_array();
										$to_qty = $data_to_stock['qty'];
										$where_stock = array(
											'product_code'  => $info['product_code'],
											'warehouse_id'  => $info['to_warehouse_id']
										);       
										$qty = $to_qty + $qty_convert;                                
										$stock = array(                                
											'product_id'    => $info['product_id'],
											'qty'           => $qty,
										);
										$plus_stock = $this->crud->update('stock', $stock, $where_stock);
									}
									else
									{                                                               
										$qty = 0 + $qty_convert;
										$stock = array(                                
											'product_id'    => $info['product_id'],
											'product_code'  => $info['product_code'],                                                        
											'qty'           => $qty,
											'warehouse_id'  => $info['to_warehouse_id']
										);
										$plus_stock = $this->crud->insert('stock', $stock);
									}                            
									if($plus_stock)
									{
										$data_stock_card = array(
											'date'			  => $mutation['date'],
											'transaction_id'  => $mutation['id'],
											'invoice'         => $mutation['code'],
											'product_id'      => $info['product_id'],
											'product_code'    => $info['product_code'],
											'qty'             => $qty_convert,
											'information'     => 'MUTASI',
											'note'         	  => $mutation['code'],
											'type'            => 9, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Stock Opname, 9:Mutation												
											'method'          => 1, // 1:IN, 2:OUT
											'stock'           => $qty,
											'warehouse_id'    => $info['to_warehouse_id'],
											'user_id'         => $this->session->userdata('id_u')
										);							
										if($this->crud->insert('stock_card',$data_stock_card))
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
							else
							{
								break;
							}
						}
						$this->crud->update('mutation', ['do_status' => 1], ['id' => $mutation['id']]);
						$data_activity = [
							'information' => 'MEMBUAT MUTASI (CETAK DO)',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];						
						$this->crud->insert('activity', $data_activity);					
						$this->session->set_userdata('create_mutation_do', '1');
						$response   =   [
							'mutation_id' => encrypt_custom($mutation['id']),
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
						$response   =   [
							'status'    => [
								'code'      => 401,
								'message'   => 'Gagal',
							],
							'response'  => ''
						];
						$this->session->set_flashdata('error', 'Mohon Maaf, Cetak DO Gagal karena terdapat Stok yang Kurang, harap periksa kembali');
						$this->session->set_flashdata('min_product', $check_stock_mutation_do['found']);
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
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('mutation'));
		}		
	}

	public function print_do($mutation_id)
	{
		if($this->session->userdata('create_mutation_do') == 1)
		{
			$this->session->unset_userdata('create_mutation_do');
			$mutation  = $this->mutation->detail_mutation(decrypt_custom($mutation_id));			
			$from_warehouse = $this->db->select('warehouse.id, warehouse.code, warehouse.name')
							->from('mutation_detail')->join('warehouse', 'warehouse.id = mutation_detail.from_warehouse_id')								
							->where('mutation_detail.mutation_id', $mutation['id'])
							->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
			foreach($from_warehouse AS $info_fw)
			{
				$to_warehouse = $this->db->select('warehouse.id, warehouse.code, warehouse.name')
								->from('mutation_detail')->join('warehouse', 'warehouse.id = mutation_detail.to_warehouse_id')								
								->where('mutation_detail.mutation_id', $mutation['id'])								
								->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
				foreach($to_warehouse AS $info_tw)								
				{
					$data_product = [];
					$product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, mutation_detail.qty AS qty, unit.code AS code_u')
							->from('mutation_detail')
							->join('product', 'product.id = mutation_detail.product_id')							
							->join('unit', 'unit.id = mutation_detail.unit_id')
							->where('mutation_detail.from_warehouse_id', $info_fw['id'])
							->where('mutation_detail.to_warehouse_id', $info_tw['id'])
							->where('mutation_detail.mutation_id', $mutation['id'])														
							->group_by('mutation_detail.id')->order_by('product_code', 'asc')->get()->result_array();
					if(count($product) > 0)
					{
						foreach($product AS $info_p)
						{
							$data_product[] = array(
								'id_p'   => $info_p['id_p'],
								'code_p' => $info_p['code_p'],
								'name_p' => $info_p['name_p'],
								'qty'	 => $info_p['qty'],
								'code_u' => $info_p['code_u'],
							);
						}					

						$sot[] = array(															
							'code_fw' 	 => $info_fw['code'],
							'code_tw' 	 => $info_tw['code'],
							'product'	 => $data_product
						);
					}	
					else
					{
						continue;
					}											
				}									
			}		
			$data = array(
				'perusahaan' => $this->global->company(),
				'mutation'   => $mutation,
				'sot'        => $sot
			);
			$this->load->view('mutation/print_mutation_do', $data);
		}
	}

	public function cancel_do($mutation_id)
	{
		$mutation = $this->mutation->detail_mutation(decrypt_custom($mutation_id));
		$detail_mutation = $this->mutation->detail_mutation_detail($mutation['id']);
		foreach($detail_mutation AS $info)
		{
			// ADD STOCK
			$where_stock = [
				'product_code'	=> $info['product_code'],
				'warehouse_id'	=> $info['from_warehouse_id']
			];
			$from_product_unit = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id']])->row_array();
			$stock = $this->crud->get_where('stock', $where_stock)->row_array();
			$update_stock = [
				'qty' => $stock['qty'] + ($info['qty']*$from_product_unit['value'])
			];
			$this->crud->update('stock', $update_stock, $where_stock);

			// UPDATE AND DELETE STOCK CARD
			$where_stock_card = [
				'transaction_id' => $mutation['id'],
				'product_code'	 => $info['product_code'],
				'type'			 => 9,
				'method'		 => 2,
				'warehouse_id'	 => $info['from_warehouse_id']
			];								
			$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
			$where_after_stock_card = [
				'id >'          => $stock_card['id'],
				'product_code'	=> $info['product_code'],
				'warehouse_id'	=> $info['from_warehouse_id'],
				'deleted'		=> 0
			];
			$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.id', 'ASC')->get()->result_array();
			foreach($after_stock_cards AS $info_stock_card)
			{
				$update_stock_card = [
					'stock' => $info_stock_card['stock'] + ($info['qty']*$from_product_unit['value'])
				];
				$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
			}
			$this->crud->delete('stock_card', ['id' => $stock_card['id']]);

			// MINUS STOCK
			$where_stock = [
				'product_code'	=> $info['product_code'],
				'warehouse_id'	=> $info['to_warehouse_id']
			];
			$to_product_unit = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id']])->row_array();
			$stock = $this->crud->get_where('stock', $where_stock)->row_array();
			$update_stock = [
				'qty' => $stock['qty']-($info['qty']*$to_product_unit['value'])
			];
			$this->crud->update('stock', $update_stock, $where_stock);

			// UPDATE AND DELETE STOCK CARD
			$where_stock_card = [
				'transaction_id' => $mutation['id'],
				'product_code'	 => $info['product_code'],
				'type'			 => 9,
				'method'		 => 1,
				'warehouse_id'	 => $info['to_warehouse_id']
			];								
			$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
			$where_after_stock_card = [
				'id >'          => $stock_card['id'],
				'product_code'	=> $info['product_code'],
				'warehouse_id'	=> $info['to_warehouse_id'],
				'deleted'		=> 0
			];
			$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.id', 'ASC')->get()->result_array();
			foreach($after_stock_cards AS $info_stock_card)
			{
				$update_stock_card = [
					'stock' => $info_stock_card['stock']-($info['qty']*$from_product_unit['value'])
				];
				$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
			}
			$this->crud->delete('stock_card', ['id' => $stock_card['id']]);			
		}
		$this->crud->update('mutation', ['do_status' => 0], ['id' => $mutation['id']]);
		$this->session->set_flashdata('success', 'DO Mutasi berhasil dibatalkan');
		redirect(site_url('mutation/detail/'.encrypt_custom($mutation['id'])));
	}			

	public function update($mutation_id)
	{
		if($this->system->check_access('mutation', 'update'))
        {					
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->db->trans_start();
				$mutation = $this->mutation->detail_mutation(decrypt_custom($post['mutation_id']));
				$data_mutation = array(					
					'date' 				=> format_date($post['date']),
					'checker'			=> $post['checker_code'],
					'operator'			=> $this->session->userdata('code_e'),
					'total_product'  	=> $post['total_product'],
					'total_qty' 		=> $post['total_qty']
				);					
				$this->crud->update('mutation', $data_mutation, ['id' => $mutation['id']]);
				$this->crud->delete('mutation_detail', ['mutation_id' => $mutation['id']]);
				foreach($post['product'] as $info)
				{
					$res = 0;
					$product_id = $this->crud->get_product_id($info['product_code']);
					$product_unit = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id']])->row_array();
					if($product_id != null)
					{
						$data_mutation_detail = array(
							'mutation_id'  => $mutation['id'],
							'product_id'   => $product_id,
							'product_code' => $info['product_code'],
							'qty' 		   => format_amount($info['qty']),
							'unit_id' 	   => $info['unit_id'],
							'unit_value'   => ($product_unit['value'] != null) ? $product_unit['value'] : 1,
							'from_warehouse_id' => $info['from_warehouse_id'],
							'to_warehouse_id'   => $info['to_warehouse_id']
						);
						if($this->crud->insert('mutation_detail', $data_mutation_detail))
						{
							$res = 1;
						}
						else
						{
							break;
						}
					}
					else
					{
						continue;
					}					
				}
				$this->db->trans_complete();
				if($res == 1 && $this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$data_activity = [
						'information' => 'MEMPERBARUI MUTASI',
						'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);
					$this->session->set_flashdata('success', 'Mutasi berhasil diperbarui');
					redirect(site_url('mutation/detail/'.encrypt_custom($mutation['id'])));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Mutasi gagal diperbarui');
					redirect(site_url('mutation'));
				}
			}
			else
			{
				$mutation = $this->mutation->detail_mutation(decrypt_custom($mutation_id));
				$header = array("title" => "Perbarui Mutasi");
				$data = array(
					'mutation' => $mutation,
					'mutation_detail' => $this->mutation->detail_mutation_detail($mutation['id'])
				);
				$footer = array("script" => ['inventory/mutation/update_mutation.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('mutation/update_mutation', $data);
				$this->load->view('include/footer', $footer);							
			}							
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }						
	}

	public function delete()
	{		
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post 		 = $this->input->post();
			$mutation 	 = $this->crud->get_where('mutation', ['id' => $post['mutation_id']])->row_array();
			// DELETE MUTATION
			$this->crud->delete('mutation_detail', ['mutation_id' => $mutation['id']]);			
			// DELETE MUTATION DETAIL
			$this->crud->delete('mutation', ['id' => $mutation['id']]);
			$data_activity = [
				'information' => 'MENGHAPUS MUTASI',
				'method'      => 5,
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
			$this->session->set_flashdata('success', 'Mutasi terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}
}
