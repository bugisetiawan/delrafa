<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repacking extends System_Controller 
{	
    public function __construct()
	{
		parent::__construct();		
		$this->load->model('Repacking_model','repacking');
	}

	public function index()
	{
		if($this->system->check_access('repacking','read'))
		{
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$this->datatables->select('repacking.id AS id_rp, repacking.date, repacking.code AS code_rp, product.code AS code_p, product.name AS name_p, repacking.repacker, repacking.employee_code AS operator');
				$this->datatables->from('repacking');			
				$this->datatables->join('employee AS repacker', 'repacker.code = repacking.repacker', 'left');
				$this->datatables->join('employee AS operator', 'operator.code = repacking.employee_code');
				$this->datatables->join('product', 'product.code = repacking.product_code');
				$this->datatables->where('repacking.deleted', 0);
				// $this->datatables->where('DATE(repacking.created)', date('Y-m-d'));
				$this->datatables->group_by('repacking.id');
				$this->datatables->add_column('code_rp', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('repacking/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(id_rp),code_rp');
				$this->datatables->add_column('code_p', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(code_p),code_p');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Repacking");	
				$footer = array("script" => ['inventory/repacking/repacking.js']);	
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('repacking/repacking');
				$this->load->view('include/footer', $footer);
			} 			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url(''));
		}
	}

	public function create_repacking()
    {
		if($this->system->check_access('repacking','create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$res  = 0;
				$repacking_code  = $this->repacking->repacking_code();
				$product_id  = $this->crud->get_product_id($post['product_code_1']);
				$convert 	 = $this->crud->get_where('product_unit', ['product_code' => $post['product_code_1'], 'unit_id' => $post['unit_id_1']])->row_array();			
				$qty_convert = $post['qty_1']*$convert['value'];
				$from_hpp 	 = $this->crud->get_where_select('hpp', 'product', ['code' => $post['product_code_1']])->row_array();
				$total_from_hpp = $from_hpp['hpp']*$qty_convert;
				$data_repacking = [
					'date' 				=> format_date($post['date']),
					'code' 				=> $repacking_code,
					'repacker'  		=> $post['repacker_code'],
					'employee_code' 	=> $this->session->userdata('code_e'),
					'product_id'   		=> $product_id,
					'product_code' 		=> $post['product_code_1'],
					'qty' 				=> $post['qty_1'],
					'unit_id' 			=> $post['unit_id_1'],
					'unit_value'		=> $convert['value'],
					'hpp'          		=> $from_hpp['hpp'],
					'warehouse_id' 		=> $post['warehouse_id_1']
				];
				$repacking_id = $this->crud->insert_id('repacking', $data_repacking);
				if($repacking_id != null)
				{
					// FROM PRODUCT
					$i = 1;
					$check_stock = $this->crud->get_where('stock', ['product_code' => $post['product_code_'.$i], 'warehouse_id' => $post['warehouse_id_'.$i]]);					
					if($check_stock->num_rows() == 1)
					{
						$data_stock = $check_stock->row_array();
						$where_stock = array(
							'product_code'  => $post['product_code_'.$i],
							'warehouse_id'  => $post['warehouse_id_'.$i]
						);
						$qty = $data_stock['qty']-$qty_convert;
						$stock = array(
							'qty'           => $qty,
						);
						$update_stock = $this->crud->update('stock', $stock, $where_stock);
					}
					else
					{   				
						$qty = 0-$qty_convert;								
						$stock = array(                                
							'product_id'    => $product_id,
							'product_code'  => $post['product_code_'.$i],
							'qty'           => $qty,
							'warehouse_id'  => $post['warehouse_id_'.$i]
						);
						$update_stock = $this->crud->insert('stock', $stock);
					}
					if($update_stock)
					{	
						// STOCK CARD
						$where_last_stock_card = [
							'date <='      => format_date($post['date']),
							'product_id'   => $product_id,
							'warehouse_id' => $post['warehouse_id_'.$i],
							'deleted'      => 0
						];
						$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$data_stock_card = array(
							'type'            => 7, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
							'information'     => 'REPACKING',
							'note'			  => $repacking_code,
							'date'			  => format_date($post['date']),
							'transaction_id'  => $repacking_id,								
							'invoice'         => $repacking_code,
							'product_id'      => $product_id,
							'product_code'    => $post['product_code_'.$i],
							'qty'             => $qty_convert,
							'method'          => 2, // 1:In, 2:Out
							'stock'           => $last_stock_card['stock']-$qty_convert,
							'warehouse_id'    => $post['warehouse_id_'.$i],
							'user_id'         => $this->session->userdata('id_u')
						);
						$this->crud->insert('stock_card',$data_stock_card);
						$where_after_stock_card = [
							'date >'       => format_date($post['date']),
							'product_id'   => $product_id,
							'warehouse_id' => $post['warehouse_id_'.$i],
							'deleted'      => 0
						];                    
						$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_stock_card  AS $info_after_stock_card)
						{
							$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']-$qty_convert], ['id' => $info_after_stock_card['id']]);
						}
						// STOCK MOVEMENT
						$where_last_stock_movement = [
							'product_id'   => $product_id,
							'date <='      => format_date($post['date']),
							'deleted'      => 0
						];
						$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$data_stock_movement = [
							'type'            => 7, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
							'information'     => 'REPACKING',
							'note'			  => $repacking_code,
							'date'            => format_date($post['date']),
							'transaction_id'  => $repacking_id,
							'invoice'         => $repacking_code,
							'product_id'      => $product_id,
							'product_code'    => $post['product_code_'.$i],
							'qty'             => $qty_convert,
							'method'          => 2, // 1:In, 2:Out
							'stock'           => $last_stock_movement['stock']-$qty_convert,
							'hpp'			  => $from_hpp['hpp'],
							'employee_code'   => $this->session->userdata('code_e')
						];
						$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
						$where_after_stock_movement = [
							'product_id'   => $product_id,
							'date >'       => format_date($post['date']),
							'deleted'      => 0
						];                    
						$after_stock_movement = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_stock_movement  AS $info_after_stock_movement)
						{
							$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']-$qty_convert], ['id' => $info_after_stock_movement['id']]);
						}

						// TO PRODUCT
						foreach($post['product'] as $info)
						{	
							$res=0; $i=2;					
							$product_id  = $this->crud->get_product_id($info['product_code_'.$i]);
							$convert 	 = $this->crud->get_where('product_unit', ['product_code' => $info['product_code_'.$i], 'unit_id' => $info['unit_id_'.$i]])->row_array();
							$qty_convert = $info['qty_'.$i]*$convert['value'];	
							$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code_'.$i], 'warehouse_id' => $info['warehouse_id_'.$i]]);
							if($check_stock->num_rows() == 1)
							{
								$data_stock = $check_stock->row_array();
								$where_stock = array(
									'product_code'  => $info['product_code_'.$i],
									'warehouse_id'  => $info['warehouse_id_'.$i]
								);       														
								$qty = $data_stock['qty']+$qty_convert;
								$stock = array(
									'qty'           => $qty,
								);
								$update_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{   							
								$qty = 0+$qty_convert;							
								$stock = array(                                
									'product_id'    => $product_id,
									'product_code'  => $info['product_code_'.$i],
									'qty'           => $qty,
									'warehouse_id'  => $info['warehouse_id_'.$i]
								);
								$update_stock = $this->crud->insert('stock', $stock);
							}
							if($update_stock)
							{
								// STOCK CARD
								$where_last_stock_card = [
									'date <='      => format_date($post['date']),
									'product_id'   => $product_id,
									'warehouse_id' => $info['warehouse_id_'.$i],
									'deleted'      => 0
								];
								$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_card = array(
									'type'            => 7, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
									'information'     => 'REPACKING',
									'note'			  => $repacking_code,
									'date'			  => format_date($post['date']),
									'transaction_id'  => $repacking_id,								
									'invoice'         => $repacking_code,
									'product_id'      => $product_id,
									'product_code'    => $info['product_code_'.$i],
									'qty'             => $qty_convert,
									'method'          => 1, // 1:In, 2:Out
									'stock'           => $last_stock_card['stock']+$qty_convert,
									'warehouse_id'    => $info['warehouse_id_'.$i],
									'user_id'         => $this->session->userdata('id_u')
								);
								$this->crud->insert('stock_card',$data_stock_card);
								$where_after_stock_card = [
									'date >'       => format_date($post['date']),
									'product_id'   => $product_id,
									'warehouse_id' => $info['warehouse_id_'.$i],
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
									'type'            => 7, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
									'information'     => 'REPACKING',
									'note'			  => $repacking_code,
									'date'            => format_date($post['date']),
									'transaction_id'  => $repacking_id,
									'invoice'         => $repacking_code,
									'product_id'      => $product_id,
									'product_code'    => $info['product_code_'.$i],
									'qty'             => $qty_convert,
									'method'          => 1, // 1:In, 2:Out
									'stock'           => $last_stock_movement['stock']+$qty_convert,
									'price'			  => $from_hpp['hpp']/$qty_convert,
									'hpp'			  => $from_hpp['hpp']/$qty_convert,
									'employee_code'   => $this->session->userdata('code_e')
								];
								$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
								$where_after_stock_movement = [
									'product_id'   => $product_id,
									'date >'       => format_date($post['date']),
									'deleted'      => 0
								];                    
								$after_stock_movement = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_stock_movement  AS $info_after_stock_movement)
								{
									$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']+$qty_convert], ['id' => $info_after_stock_movement['id']]);
								}
								$data_repacking_detail = array(
									'repacking_id' 	=> $repacking_id,											
									'product_id'    => $product_id,
									'product_code'  => $info['product_code_2'],					
									'qty' 			=> $info['qty_2'],
									'unit_id' 		=> $info['unit_id_2'],
									'unit_value'	=> $convert['value'],
									'hpp'			=> $from_hpp['hpp']/$qty_convert,
									'warehouse_id'  => $info['warehouse_id_2']
								);
								if($this->crud->insert('repacking_detail', $data_repacking_detail))
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
				if($res == 1)
				{
					$this->session->set_flashdata('success', 'Data Repacking berhasil ditambahkan');
				}
				else
				{
					$this->session->set_flashdata('error', 'Data Repacking gagal ditambahkan');
				}
				redirect(base_url('repacking'));
			}
			else
			{
				$header = array("title" => "Repacking Baru");
				$footer = array("script" => ['inventory/repacking/create_repacking.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('repacking/create_repacking');
				$this->load->view('include/footer', $footer);				
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('stock/repacking'));
		}        
	}

	public function datatable_detail_repacking($repacking_id)
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, repacking_detail.qty AS qty, unit.name AS name_u, warehouse.name AS name_w');
			$this->datatables->from('repacking_detail');
			$this->datatables->join('product', 'product.code = repacking_detail.product_code');
			$this->datatables->join('unit', 'unit.id = repacking_detail.unit_id');
			$this->datatables->join('warehouse', 'warehouse.id = repacking_detail.warehouse_id');
			$this->datatables->where('repacking_detail.repacking_id', $repacking_id);
			$this->datatables->group_by('repacking_detail.id');
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

	public function detail_repacking($repacking_id)
	{
		if($this->system->check_access('repacking', 'detail'))
		{
			$repacking_id = $this->global->decrypt($repacking_id);
			$header = array("title" => "Detail Repacking");		
			$data = array(
				'repacking' => $this->repacking->detail_repacking($repacking_id)				
			);
			$footer = array("script" => ['inventory/repacking/detail_repacking.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('repacking/detail_repacking', $data);
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('stock/repacking'));
		}
	}

	public function delete_repacking()
	{		
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post 		 = $this->input->post();
			$repacking = $this->crud->get_where('repacking', ['id' => $post['repacking_id']])->row_array();
			$repacking_detail = $this->crud->get_where('repacking_detail', ['repacking_id' => $repacking['id']])->result_array();
			// FROM PRODUCT
			// ADD STOCK
			$where_stock = [
				'product_code'	=> $repacking['product_code'],
				'warehouse_id'	=> $repacking['warehouse_id']
			];
			$stock = $this->crud->get_where('stock', $where_stock)->row_array();
			$update_stock = [
				'qty' => $stock['qty']+($repacking['qty']*$repacking['unit_value'])
			];
			$this->crud->update('stock', $update_stock, $where_stock);
			// UPDATE AND DELETE STOCK CARD
			$where_stock_card = [
				'transaction_id' => $repacking['id'],
				'product_code'	 => $repacking['product_code'],
				'type'			 => 7,
				'warehouse_id'	 => $repacking['warehouse_id']
			];
			$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
			$where_after_stock_card = [
				'date >='       => $stock_card['date'],
				'product_code'	=> $repacking['product_code'],
				'warehouse_id'	=> $repacking['warehouse_id'],
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
						'stock' => $info_stock_card['stock']+($repacking['qty']*$repacking['unit_value'])
					];
					$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
				}										
			}
			$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
			// UPDATE AND DELETE STOCK MOVEMENT
			$where_stock_movement = [
				'transaction_id' => $repacking['id'],
				'product_code'	 => $repacking['product_code'],
				'type'			 => 7
			];								
			$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
			$where_after_stock_movement = [
				'date >='       => $stock_movement['date'],
				'product_code'	=> $repacking['product_code'],
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
						'stock' => $info_stock_movement['stock']+($repacking['qty']*$repacking['unit_value'])
					];
					$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
				}
			}
			$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);
			
			// PRODUCT REPACK
			foreach($repacking_detail AS $info_repacking_detail)
			{
				// MINUS STOCK
				$where_stock = [
					'product_code'	=> $info_repacking_detail['product_code'],
					'warehouse_id'	=> $info_repacking_detail['warehouse_id']
				];
				$stock = $this->crud->get_where('stock', $where_stock)->row_array();
				$update_stock = [
					'qty' => $stock['qty']-($info_repacking_detail['qty']*$info_repacking_detail['unit_value'])
				];
				$this->crud->update('stock', $update_stock, $where_stock);
				// UPDATE AND DELETE STOCK CARD
				$where_stock_card = [
					'transaction_id' => $repacking['id'],
					'product_code'	 => $info_repacking_detail['product_code'],
					'type'			 => 7,				
					'warehouse_id'	 => $info_repacking_detail['warehouse_id']
				];
				$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
				$where_after_stock_card = [
					'date >='       => $stock_card['date'],
					'product_code'	=> $info_repacking_detail['product_code'],
					'warehouse_id'	=> $info_repacking_detail['warehouse_id'],
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
							'stock' => $info_stock_card['stock']-($info_repacking_detail['qty']*$info_repacking_detail['unit_value'])
						];
						$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
					}										
				}
				$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
				// UPDATE AND DELETE STOCK MOVEMENT
				$where_stock_movement = [
					'transaction_id' => $repacking['id'],
					'product_code'	 => $info_repacking_detail['product_code'],
					'type'			 => 7
				];								
				$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
				$where_after_stock_movement = [
					'date >='       => $stock_movement['date'],
					'product_code'	=> $info_repacking_detail['product_code'],
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
							'stock' => $info_stock_movement['stock']-($info_repacking_detail['qty']*$info_repacking_detail['unit_value'])
						];
						$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
					}
				}
				$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);				
			}

			$data_activity = [
				'information' => 'MENGHAPUS REPACKING PRODUK',
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
			$this->session->set_flashdata('success', 'BERHASIL! Repacking Produk Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}
}