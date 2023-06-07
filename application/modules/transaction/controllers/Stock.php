<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends System_Controller 
{	
    public function __construct()
	{
		parent::__construct();
		$this->load->model('transaction/Sales_model','sales');
		$this->load->model('Stock_model','stock');
	}		

	// STOCK CARD
	public function stock_card()
	{
		if($this->system->check_access('stock/card', 'read'))
        {
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$this->datatables->select('stock_card.id As id_sc, stock_card.transaction_id, stock_card.invoice, product.code AS code_p, product.name AS name_p, stock_card.qty, stock_card.information, stock_card.type, 
								stock_card.method, stock_card.stock, stock_card.created, warehouse.code AS code_w, warehouse.name AS name_w, employee.code AS code_e, employee.name AS name_e,
								stock_card.invoice AS search_invoice, product.code AS search_code_p');
				$this->datatables->from('stock_card');
				$this->datatables->join('product', 'product.id = stock_card.product_id');
				$this->datatables->join('warehouse', 'warehouse.id = stock_card.warehouse_id');
				$this->datatables->join('user', 'user.id = stock_card.user_id');
				$this->datatables->join('employee', 'employee.code = user.employee_code');
				$this->datatables->where('date(stock_card.created)', date('Y-m-d'));
				$this->datatables->where('stock_card.deleted', 0);
				$this->datatables->group_by('stock_card.id');
				$this->datatables->add_column('transaction_id', 
				'
					$1		                
				', 'encrypt_custom(transaction_id)');
				$this->datatables->add_column('code_p', 
				'
					<a class="text-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(code_p), code_p');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Daftar kartu Stok"); 
				$footer = array("script" => ['transaction/stock/stock_card.js']);                          		
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('stock/stock_card');
				$this->load->view('include/footer', $footer);
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }		
	}

	// PRODUCTION
	public function production()
	{
		if($this->system->check_access('stock/production', 'read'))
        {
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$this->datatables->select('production.id AS id_pro, production.date AS date_pro, production.code AS code_pro, product.name AS name_p, warehouse.name AS name_w, production.status');
				$this->datatables->from('production');
				$this->datatables->join('product', 'product.id = production.product_id');
				$this->datatables->join('warehouse', 'warehouse.id = production.warehouse_id');
				$this->datatables->where('production.date', date('Y-m-d'));
				$this->datatables->group_by('production.id');
				$this->datatables->add_column('code',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('stock/production/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_pro),code_pro');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Daftar Produksi");
				$footer = array("script" => ['transaction/stock/production/production.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('stock/production/production');
				$this->load->view('include/footer', $footer);
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}

	public function add_production($product_id)
	{
		if($this->system->check_access('stock/production', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$data_production = [
					'code'			=> $this->stock->production_code(),
					'date' 			=> date('Y-m-d', strtotime($post['date'])),					
					'warehouse_id'  => $post['warehouse_id'],
					'employee_code' => $this->session->userdata('code_e'),
					'product_id'	=> $this->crud->get_product_id($post['product_code']),
					'product_code'  => $post['product_code'],
					'qty_produce'   => $post['qty'],
					'hpp'		    => $post['subtotal_hpp']
				];
				if($this->crud->insert('production', $data_production))
				{
					$this->session->set_flashdata('success', 'Data Produksi Produk berhasil ditambahkan');
				}
				else
				{
					$this->session->set_flashdata('error', 'Data Produksi Produk gagal ditambahkan');
				}
				redirect(site_url('stock/production'));

			}
			else
			{
				$product_id = $this->global->decrypt($product_id);
				$header = array("title" => "Produksi Baru");                           
				$data = array( 'product_production' => $this->stock->product_production($product_id));
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('stock/production/add_production', $data);
				$this->load->view('include/footer');
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
		}        
	}		
	
	public function detail_production($production_id)
	{
		if($this->system->check_access('stock/production', 'detail'))
        {
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();	
				$production = $this->stock->detail_production($post['production_id']);
				$this->crud->update('production', ['status' => 1, 'qty_result' => $post['qty_result']], ['id' => $post['production_id']]);				
				// MASTER PRODUCT		
				$where_stock_master = array(
					'product_code' => $production['code_p'], 
					'warehouse_id' => $production['id_w'],
					'deleted'      => 0
				);
				$check_stock_master = $this->crud->get_where('stock', $where_stock_master);
				if($check_stock_master->num_rows() > 0)
				{
					$stock_master = $check_stock_master->row_array();
					$qty_master = $post['qty_result'] + $stock_master['qty'];
					$data_stock_master = array(
						'qty' => $qty_master
					);
					$this->crud->update('stock', $data_stock_master, $where_stock_master);
				}
				else
				{
					$qty_master = $post['qty_result'];
					$data_stock_master = array(
						'product_id'   => $production['id_p'],
						'product_code' => $production['code_p'], 
						'warehouse_id' => $production['id_w'],
						'qty'          => $qty_master
					);
					$this->db->insert('stock', $data_stock_master);
				}
				$data_stock_card_master = array(
					'transaction_id'  => $production['id_pro'],
					'invoice'         => $production['code_pro'],
					'product_id'      => $production['id_p'],
					'product_code'    => $production['code_p'],
					'qty'             => $post['qty_result'],
					'information'     => 'PRODUKSI',
					'type'            => 6, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
					'method'          => 1, // 1:In, 2:Out
					'stock'           => $qty_master,
					'warehouse_id'    => $production['id_w'],
					'user_id'         => $this->session->userdata('id_u')
				);
				$this->crud->insert('stock_card',$data_stock_card_master);

				// BUNDLE PRODUCT
				foreach($post['product'] AS $info)
				{
					$res = 0;
					$where_stock_bundle = array(
						'product_code' => $info['product_code'], 
						'warehouse_id' => $post['warehouse_id'],
						'deleted' => 0
					);
					$check_stock_bundle = $this->crud->get_where('stock', $where_stock_bundle);
					if($check_stock_bundle->num_rows() > 0)
					{
						$stock_bundle = $check_stock_bundle->row_array();
						$qty_product = $stock_bundle['qty'] - ($post['qty_result'] * $info['qty_convert']);
						$data_stock_bundle = array(
							'qty' => $qty_product
						);
						$this->crud->update('stock', $data_stock_bundle, $where_stock_bundle);			
						$res = 1;
					}
					else
					{
						$qty_product = 0 - ($post['qty_result'] * $info['qty_convert']);
						$data_stock_bundle = array(
							'product_id'   => $this->crud->get_product_id($info['product_code']),
							'product_code' => $info['product_code'], 
							'warehouse_id' => $post['warehouse_id'],
							'qty'          => $qty_product
						);
						$this->db->insert('stock', $data_stock_bundle);
						$res = 1;
					}
					$data_stock_card = array(
						'transaction_id'  => $production['id_pro'],
						'invoice'         => $production['code_pro'],
						'product_id'      => $this->crud->get_product_id($info['product_code']),
						'product_code'    => $info['product_code'],
						'qty'             => $post['qty_result'] * $info['qty_convert'],
						'information'     => 'PRODUKSI',
						'type'            => 6, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Stock Opname, 9:Mutation
						'method'          => 2, // 1:In, 2:Out
						'stock'           => $qty_product,
						'warehouse_id'    => $production['id_w'],
						'user_id'         => $this->session->userdata('id_u')
					);
					$this->crud->insert('stock_card',$data_stock_card);
				}

				if($res == 1)
				{
					$this->session->set_flashdata('success', 'Data Produksi Produk berhasil diselesaikan');
				}
				else
				{
					$this->session->set_flashdata('error', 'Data Produksi Produk gagal diselesaikan');
				}
				redirect(base_url('stock/production'));
			}
			else
			{
				$production_id = $this->global->decrypt($production_id);
				$header = array("title" => "Detail Produksi");                           
				$production = $this->stock->detail_production($production_id);
				$data = array( 
					'production' => $production,
					'product_production' => $this->stock->product_production($production['id_p'])
				);
				$footer = array("script" => ['transaction/stock/production/crud_production.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('stock/production/detail_production', $data);
				$this->load->view('include/footer',$footer);
								
			}				
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('stock/production'));
        }				
	}

	// REPACKING
	public function repacking()
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
				$this->datatables->join('product', 'product.code = repacking.from_product_code');
				$this->datatables->where('repacking.deleted', 0);
				$this->datatables->where('DATE(repacking.created)', date('Y-m-d'));
				$this->datatables->group_by('repacking.id');
				$this->datatables->add_column('code_rp', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('stock/repacking/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(id_rp),code_rp');
				$this->datatables->add_column('code_p', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(code_p),code_p');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Pengemesan Produk");	
				$footer = array("script" => ['transaction/stock/repacking/repacking.js']);	
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('stock/repacking/repacking');
				$this->load->view('include/footer', $footer);
			} 			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url(''));
		}
	}

	public function add_repacking()
    {
		if($this->system->check_access('repacking','create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$res = 0;
				$repacking_code = $this->stock->repacking_code();
				$data_repacking = array(
					'date' 				=> format_date($post['date']),
					'code' 				=> $repacking_code,
					'repacker'  		=> $this->session->userdata('code_e'),
					'employee_code' 	=> $this->session->userdata('code_e'),
					'from_product_id'   => $this->crud->get_product_id($post['product_code_1']),
					'from_product_code' => $post['product_code_1'],
					'from_qty' 			=> $post['qty_1'],
					'from_unit_id' 		=> $post['unit_id_1'],
					'from_warehouse_id' => $post['warehouse_id_1'],
				);
				$repacking_id = $this->crud->insert_id('repacking', $data_repacking);
				if($repacking_id != null)
				{
					$i = 1;			
					$product_id  = $this->crud->get_product_id($post['product_code_'.$i]);			
					$convert 	 = $this->crud->get_where('product_unit', ['product_code' => $post['product_code_'.$i], 'unit_id' => $post['unit_id_'.$i]])->row_array();			
					$qty_convert = $post['qty_'.$i] * $convert['value'];	
					$check_stock = $this->crud->get_where('stock', ['product_code' => $post['product_code_'.$i], 'warehouse_id' => $post['warehouse_id_'.$i]]);
					if($check_stock->num_rows() == 1)
					{
						$data_stock = $check_stock->row_array();
						$old_qty = $data_stock['qty'];
						$where_stock = array(
							'product_code'  => $post['product_code_'.$i],
							'warehouse_id'  => $post['warehouse_id_'.$i]
						);       				
						$qty = $old_qty - $qty_convert;				
						$stock = array(
							'qty'           => $qty,
						);
						$update_stock = $this->crud->update('stock', $stock, $where_stock);
					}
					else
					{   				
						$qty = 0 - $qty_convert;								
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
						
						foreach($post['product'] as $info)
						{	
							$res =0; $i=2;					
							$product_id = $this->crud->get_product_id($info['product_code_'.$i]);
							$convert 	 = $this->crud->get_where('product_unit', ['product_code' => $info['product_code_'.$i], 'unit_id' => $info['unit_id_'.$i]])->row_array();
							$qty_convert = $info['qty_'.$i] * $convert['value'];	
							$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code_'.$i], 'warehouse_id' => $info['warehouse_id_'.$i]]);
							if($check_stock->num_rows() == 1)
							{
								$data_stock = $check_stock->row_array();
								$old_qty = $data_stock['qty'];
								$where_stock = array(
									'product_code'  => $info['product_code_'.$i],
									'warehouse_id'  => $info['warehouse_id_'.$i]
								);       														
								$qty = $old_qty + $qty_convert;											
								$stock = array(
									'qty'           => $qty,
								);
								$insert_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{   							
								$qty = 0 + $qty_convert;							
								$stock = array(                                
									'product_id'    => $product_id,
									'product_code'  => $info['product_code_'.$i],
									'qty'           => $qty,
									'warehouse_id'  => $info['warehouse_id_'.$i]
								);
								$insert_stock = $this->crud->insert('stock', $stock);
							}
							if($insert_stock)
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
									'repacking_id' 		=> $repacking_id,											
									'to_product_id'     => $product_id,
									'to_product_code'   => $info['product_code_2'],					
									'to_qty' 			=> $info['qty_2'],
									'to_unit_id' 		=> $info['unit_id_2'],
									'to_warehouse_id'   => $info['warehouse_id_2']
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
				redirect(base_url('stock/repacking'));
			}
			else
			{
				$header = array("title" => "Repacking Baru");
				$footer = array("script" => ['transaction/stock/repacking/crud_repacking.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('stock/repacking/add_repacking');
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
			$this->datatables->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, repacking_detail.to_qty AS qty, unit.name AS name_u, warehouse.name AS name_w');
			$this->datatables->from('repacking_detail');
			$this->datatables->join('product', 'product.code = repacking_detail.to_product_code');
			$this->datatables->join('unit', 'unit.id = repacking_detail.to_unit_id');
			$this->datatables->join('warehouse', 'warehouse.id = repacking_detail.to_warehouse_id');
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
				'repacking' => $this->stock->detail_repacking($repacking_id)				
			);
			$footer = array("script" => ['transaction/stock/repacking/detail_repacking.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('stock/repacking/detail_repacking', $data);
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('stock/repacking'));
		}				
	}			

    public function get_product()
	{
		if($this->input->is_ajax_request())
		{
			$search 		= urldecode($this->uri->segment(4));	
			$data           = $this->stock->get_product($search);
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
    
    public function get_unit()
    {
		if($this->input->is_ajax_request())
		{
			$code = $this->input->post('code');
			$where = array(
				'product_unit.product_code'  => $code,
				'product_unit.deleted'       => 0
			);
			$unit       = $this->stock->get_unit($where)->result_array();
			$option		= "<option value=''>-- Pilih Satuan --</option>";		
			foreach($unit as $data)
			{
				if($data['default']==1)
				{
					$option .= "<option value='".$data['id_u']."' selected>".$data['name_u']."</option>";
				}
				else
				{
					$option .= "<option value='".$data['id_u']."'>".$data['name_u']."</option>";
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
	
	public function get_product_warehouse()
    {        
        $warehouse  = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
        $option		= "<option value=''>-- Pilih gudang --</option>";		
		foreach($warehouse as $data)
		{
			if($data['default']==1)
			{
				$option .= "<option value='".$data['id']."' selected>".$data['name']."</option>";
			}
			else
			{
				$option .= "<option value='".$data['id']."'>".$data['name']."</option>";
			}
		}		
		$result = array
		(
			'option'=>$option
		);
		echo json_encode($result);
	}

	// STOCK OPNAME
	public function datatable_stock_opname()
    {  
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('stock_opname.id, stock_opname.date, stock_opname.code, stock_opname.total_product, warehouse.name AS warehouse, checker.name AS checker, operator.name AS operator, stock_opname.status');
			$this->datatables->from('stock_opname');
			$this->datatables->join('employee AS checker', 'checker.code = stock_opname.checker');
			$this->datatables->join('employee AS operator', 'operator.code = stock_opname.operator');
			$this->datatables->join('warehouse', 'warehouse.id = stock_opname.warehouse_id');
			$this->datatables->where('stock_opname.deleted', 0);
			$this->datatables->where('DATE(stock_opname.created)', date('Y-m-d'));
			$this->datatables->group_by('stock_opname.id');
			$this->datatables->add_column('code',
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('stock/opname/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id),code');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}                 
	}

    public function stock_opname()
    {
		if($this->system->check_access('stock/opname','read'))
		{
			$header = array("title" => "Stok Opname");
			$footer = array("script" => ['transaction/stock/opname/stock_opname.js']);
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('stock/opname/stock_opname');            
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('dashboard'));
		}        
	}

    public function add_stock_opname()
	{
		if($this->system->check_access('stock/opname', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();								
				if($post['total_product'] == "" || $post['total_product'] == 0)
				{
					$header = array("title" => "Stok Opname Baru");
					$footer = array("script" => ['transaction/stock/crud_stock_opname.js']);
					$data   = array(
						'products' => $this->stock->product_stock_opname($post['department_code'], $post['subdepartment_code'])
					);
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('stock/add_stock_opname', $data);            
					$this->load->view('include/footer', $footer);					
				}
				else
				{					
					$this->form_validation->set_rules('date', 'Tanggal Stock Opname', 'trim|required|xss_clean');
					$this->form_validation->set_rules('employee_code', 'Petugas SO', 'trim|required|xss_clean');
					$this->form_validation->set_rules('warehouse_id', 'Gudang', 'trim|required|xss_clean');		
					$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
					if($this->form_validation->run() == FALSE)
					{
						$header = array("title" => "Stok Opname Baru");                     
						$footer = array("script" => ['transaction/stock/crud_stock_opname.js']);                                     
						$data   = array(
							'products' => $this->stock->product_stock_opname($post['department_code'], $post['subdepartment_code'])
						);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('stock/add_stock_opname', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{			
						$code 	 = $this->stock->stock_opname_code();
						$data_sto = array(
							'date'		    => format_date($post['date']),				
							'code'		    => $code,
							'checker'	    => $post['employee_code'],
							'operator'	    => $this->session->userdata('code_e'),
							'warehouse_id'  => $post['warehouse_id'],
							'total_product' => $post['total_product'],
							'status'        => 2,
						);		
						$sto_id = $this->crud->insert_id('stock_opname', $data_sto);		
						$res = 0;
						if($sto_id != null)
						{
							foreach($post['product'] AS $info)
							{
								$product_id = $this->crud->get_product_id($info);
								$unit = $this->crud->get_where('product_unit', ['product_code' => $info, 'default' => 1, 'deleted' => 0])->row_array();
								$data_sto_detail=array(
									'stock_opname_id' => $sto_id,
									'product_id'	  => $product_id,
									'product_code'	  => $info,
									'unit_id'	      => $unit['unit_id']
								);		
								if($this->crud->insert('stock_opname_detail', $data_sto_detail))
								{
									$res = 1;
									continue;
								}
								else
								{
									break;
								}
							}
							if($res == 1)
							{
								$data_activity = array (
									'information' => 'MENAMBAH DATA STOCK OPNAME',
									'method'	  => 3,
									'user_id' 	  => $this->session->userdata('id_u')
								);
								$activity = $this->crud->insert('activity',$data_activity);
								if($activity)
								{
									$res = 1;					
								}
								else
								{
									$res = 0;
								}
							}
						}
						else
						{
							$res = 0;				
						}												
					}	
					
					if($res == 1)
					{
						$this->session->set_flashdata('success', 'Data Stock Opname berhasil ditambahkan');
					}
					else
					{
						$this->session->set_flashdata('error', 'Data Stock Opname gagal ditambahkan');
					}
					redirect(base_url('stock/opname'));
				}				
			}
			else
			{
				$header = array("title" => "Stok Opname Baru");
				$footer = array("script" => ['transaction/stock/crud_stock_opname.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('stock/add_stock_opname');            
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('stock/opname'));
		}        
	}    
	
	public function datatable_detail_stock_opname($stock_opname_id)
	{
		if($this->input->is_ajax_request())
		{
			function encrypt_dso($string)
			{
				$output = false; $encrypt_method = "AES-256-CBC"; $secret_key = 'BUGI SETIAWAN'; $secret_iv = 'Setiawan Bugi';
				$key = hash('sha256', $secret_key); $iv = substr(hash('sha256', $secret_iv), 0, 16); $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
				$output = base64_encode($output); return $output;
			}
			header('Content-Type: application/json');
			$this->datatables->select('stock_opname_detail.id, product.code, product.name, unit.name AS unit, stock_opname_detail.stock, stock_opname_detail.adjust, stock_opname_detail.hpp AS hpp, (stock_opname_detail.hpp * stock_opname_detail.adjust ) AS total_hpp');
			$this->datatables->from('stock_opname_detail');
			$this->datatables->join('product', 'product.code = stock_opname_detail.product_code');
			$this->datatables->join('unit', 'unit.id = stock_opname_detail.unit_id');
			$this->datatables->where('stock_opname_detail.stock_opname_id', $stock_opname_id);
			$this->datatables->where('stock_opname_detail.deleted', 0);
			$this->datatables->group_by('stock_opname_detail.id');
			$this->datatables->add_column('code',
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'"><b>$2</b></a>
			', 'encrypt_dso(code),code');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function detail_stock_opname($stock_opname_id)
    {
		if($this->system->check_access('stock/opname','detail'))
		{
			$stock_opname_id = $this->global->decrypt($stock_opname_id);
			$stock_opname = $this->stock->detail_stock_opname($stock_opname_id);
			if($stock_opname != null)
			{
				$header = array("title" => "Detail Stok Opname");					
				$product      = $this->stock->detail_product_stock_opname($stock_opname_id, $stock_opname['warehouse_id']);
				$total_hpp    = 0;
				foreach($product AS $info)
				{
					$total_hpp = $total_hpp + ($info['adjust'] * $info['hpp']);
				}
				$data = array(
					"stock_opname" => $stock_opname,			
					"total_hpp"	   => $total_hpp
				);
				$footer = array("script" => ['transaction/stock/opname/detail_stock_opname.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('stock/detail_stock_opname', $data);
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
			redirect(base_url('stock/opname'));
		}		
	}

	public function print_stock_opname($stock_opname_id)
	{
		if($this->system->check_access('stock/opname','create'))
		{
			$stock_opname = $this->stock->detail_stock_opname($this->global->decrypt($stock_opname_id));
			$data = array(
				'perusahaan'		  => $this->global->company(),
				'stock_opname'        => $stock_opname,
				'stock_opname_detail' => $this->stock->detail_product_stock_opname($stock_opname['id'], $stock_opname['warehouse_id'])
			);
			$this->load->view('stock/opname/print_stock_opname', $data);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('stock/opname'));
		}		
	}
	
	// ADJUSMENT STOCK
	public function add_adjusment_stock($stock_opname_id)
    {
		if($this->system->check_access('stock/opname','create'))
		{
			if($this->input->method() === 'post')
			{
				$this->form_validation->set_rules('stock_opname_id', 'Nomor Stock Opname', 'trim|required|xss_clean');
				$this->form_validation->set_rules('stock_opname_code', 'Kode Stock Opname', 'trim|required|xss_clean');

				$post  = $this->input->post();
				$so_id = $post['stock_opname_id'];
				if($this->form_validation->run() == FALSE)
				{	
					$stock_opname = $this->stock->detail_stock_opname($this->global->decrypt($so_id));
					$header = array( "title" => "Penyesuaian Stok");		
					$data = array(
						"stock_opname" => $stock_opname,
						"product"      => $this->stock->detail_product_stock_opname($stock_opname['id'], $stock_opname['warehouse_id']),
					);
					$footer = array("script" => ['transaction/stock/crud_adjusment_stock.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('stock/add_adjusment_stock', $data);
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$data_so = array('status'=> 1); 
					if($this->crud->update('stock_opname', $data_so, ['id' => $so_id]))
					{
						$res = 0;
						foreach($post['product'] AS $info)
						{
							$product_id = $this->crud->get_product_id($info['product_code']);
							$where_so_detail = array(
								'stock_opname_id' => $so_id,
								'product_id'	  => $product_id,
								'product_code'	  => $info['product_code'],
								'unit_id'         => $info['unit_id']
							);
							$data_so_detail=array(												
								'stock'	  	=> $info['stock'],
								'adjust'  	=> $info['adjust'],
								'end_stock'	=> $info['end_stock'],
								'hpp'		=> $this->stock->get_hpp($info['product_code'])
							);		
							if($this->crud->update('stock_opname_detail', $data_so_detail, $where_so_detail))
							{
								$where_stock = array(
									'product_id'	  => $product_id,
									'product_code'	  => $info['product_code'],
									'warehouse_id'    => $post['warehouse_id']
								);
								$stock = $this->crud->get_where('stock', $where_stock)->row_array();
								$new_stock = $stock['qty'] + $info['adjust'];
								$data_stock = array (
									'qty' => $new_stock							
								);
								if($this->crud->update('stock', $data_stock, $where_stock))
								{							
									$method = ($info['adjust'] >= 0) ? 1 : 2;
									$data_stock_card = array(
										'transaction_id'  => $so_id,
										'invoice'         => $post['stock_opname_code'],
										'product_id'      => $product_id,
										'product_code'    => $info['product_code'],
										'qty'             => $info['adjust'],
										'information'     => 'PENYESUAIAN STOK',
										'type'            => 8, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation												
										'method'          => $method,
										'stock'           => $new_stock,
										'warehouse_id'    => $post['warehouse_id'],
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

						if($res == 1)
						{
							$data_activity = array (
								'information' => 'MELAKUKAN ADJUSMENT STOCK',
								'method'	  => 3,
								'user_id' 	  => $this->session->userdata('id_u')
							);
							$activity = $this->crud->insert('activity',$data_activity);
							if($activity)
							{
								$res = 1;					
							}
							else
							{
								$res = 0;
							}
						}
					}
					else
					{
						$res = 0;				
					}												
				}	
				
				if($res == 1)
				{
					$this->session->set_flashdata('success', 'Data Penyesuaian Stok berhasil ditambahkan');
				}
				else
				{
					$this->session->set_flashdata('error', 'Data Penyesuaian Stok gagal ditambahkan');
				}
				redirect(base_url('stock/opname'));
			}
			else
			{
				$stock_opname = $this->stock->detail_stock_opname($this->global->decrypt($stock_opname_id));
				$header = array( "title" => "Penyesuaian Stok");		
				$data = array(
					"stock_opname" => $stock_opname,
					"product"      => $this->stock->detail_product_stock_opname($stock_opname['id'], $stock_opname['warehouse_id']),
				);
				$footer = array("script" => ['transaction/stock/crud_adjusment_stock.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('stock/add_adjusment_stock', $data);
				$this->load->view('include/footer', $footer);				
			}		
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('stock/opname'));
		}		
	}	
}