<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opname extends System_Controller 
{	
    public function __construct()
	{
		parent::__construct();		
		$this->load->model('Opname_model', 'opname');
	}	

    public function index()
    {
		if($this->system->check_access('opname','read'))
		{
			if($this->input->method() === 'post')
			{
				header('Content-Type: application/json');
				$this->datatables->select('stock_opname.id, stock_opname.date, stock_opname.code, stock_opname.total_product, warehouse.name AS warehouse, checker.name AS checker, operator.name AS operator, stock_opname.status');
				$this->datatables->from('stock_opname');
				$this->datatables->join('employee AS checker', 'checker.code = stock_opname.checker');
				$this->datatables->join('employee AS operator', 'operator.code = stock_opname.operator');
				$this->datatables->join('warehouse', 'warehouse.id = stock_opname.warehouse_id');
				$this->datatables->where('stock_opname.deleted', 0);
				// $this->datatables->where('DATE(stock_opname.created)', date('Y-m-d'));
				$this->datatables->group_by('stock_opname.id');
				$this->datatables->add_column('code',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('opname/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id),code');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Stok Opname");
				$footer = array("script" => ['inventory/opname/stock_opname.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('opname/stock_opname');
				$this->load->view('include/footer', $footer);				
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('dashboard'));
		}        
	}

    public function create_stock_opname()
	{
		if($this->system->check_access('opname', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();								
				if($post['total_product'] == "" || $post['total_product'] == 0)
				{
					$header = array("title" => "Stok Opname Baru");
					$footer = array("script" => ['inventory/opname/create_stock_opname.js']);
					$data   = array(
						'products' => $this->opname->product_stock_opname($post['department_code'], $post['subdepartment_code'])
					);
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('opname/create_stock_opname', $data);            
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
						$footer = array("script" => ['inventory/opname/create_stock_opname.js']);
						$data   = array(
							'products' => $this->opname->product_stock_opname($post['department_code'], $post['subdepartment_code'])
						);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('opname/create_stock_opname', $data);            
						$this->load->view('include/footer', $footer);
					}
					else
					{			
						$code 	 = $this->opname->stock_opname_code();
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
					redirect(site_url('opname'));
				}				
			}
			else
			{
				$header = array("title" => "Stok Opname Baru");
				$footer = array("script" => ['inventory/opname/create_stock_opname.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('opname/create_stock_opname');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('opname'));
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
		if($this->system->check_access('opname','detail'))
		{
			$stock_opname = $this->opname->detail_stock_opname(decrypt_custom($stock_opname_id));
			if($stock_opname != null)
			{
				$header = array("title" => "Detail Stok Opname");					
				$product      = $this->opname->detail_product_stock_opname($stock_opname_id, $stock_opname['warehouse_id']);
				$total_hpp    = 0;
				foreach($product AS $info)
				{
					$total_hpp = $total_hpp + ($info['adjust'] * $info['hpp']);
				}
				$data = array(
					"stock_opname" => $stock_opname,			
					"total_hpp"	   => $total_hpp
				);
				$footer = array("script" => ['inventory/opname/detail_stock_opname.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('opname/detail_stock_opname', $data);
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
			redirect(site_url('opname'));
		}		
	}

	public function print_stock_opname($stock_opname_id)
	{
		if($this->system->check_access('opname','create'))
		{
			$stock_opname = $this->opname->detail_stock_opname(decrypt_custom($stock_opname_id));
			$data = array(
				'perusahaan'		  => $this->global->company(),
				'stock_opname'        => $stock_opname,
				'stock_opname_detail' => $this->opname->detail_product_stock_opname($stock_opname['id'], $stock_opname['warehouse_id'])
			);
			$this->load->view('opname/print_stock_opname', $data);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('opname'));
		}		
	}
	
	public function synchronize_stok_opname()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$stock_opname = $this->opname->detail_stock_opname($post['stock_opname_id']);
			$stock_opname_detail = $this->crud->get_where('stock_opname_detail', ['stock_opname_id' => $stock_opname['id']])->result_array();
			foreach($stock_opname_detail AS $info_stock_opname_detail)
			{
				$stock = $this->crud->get_where('stock', ['product_code' => $info_stock_opname_detail['product_code'], 'warehouse_id' => $stock_opname['warehouse_id']])->num_rows();
				if($stock == 0)
				{
					$data_stock = [
						'product_id' => $info_stock_opname_detail['product_id'],
						'product_code' => $info_stock_opname_detail['product_code'],
						'qty'	=> 0,
						'warehouse_id' => $stock_opname['warehouse_id']
					];
					$this->crud->insert('stock', $data_stock);
				}				
			}
			$this->session->set_flashdata('success', 'Pensikronan stok selesai');
			$response = [
				'status'	=> [
					'code'  	=> 200				
				],
				'response'  => ''
			];
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function delete()
	{
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post = $this->input->post();
			$stock_opname = $this->opname->detail_stock_opname($post['stock_opname_id']);
			// DELETE STOCK OPNAME DETAIL
			$this->crud->delete('stock_opname_detail', ['stock_opname_id' => $stock_opname['id']]);			
			// DELETE STOCK OPNAME
			$this->crud->delete('stock_opname', ['id' => $stock_opname['id']]);
			$data_activity = [
				'information' => 'MENGHAPUS STOK OPNAME (NO. TRANSAKSI '.$stock_opname['code'].')',
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
			$this->session->set_flashdata('success', 'BERHASIL! Stok Opname Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}								
	}

	public function create_adjusment_stock($stock_opname_id)
    {
		if($this->system->check_access('opname','create'))
		{
			if($this->input->method() === 'post')
			{
				$this->form_validation->set_rules('stock_opname_id', 'Nomor Stock Opname', 'trim|required|xss_clean');
				$this->form_validation->set_rules('stock_opname_code', 'Kode Stock Opname', 'trim|required|xss_clean');

				$post  = $this->input->post();
				$stock_opname = $this->opname->detail_stock_opname($post['stock_opname_id']);
				if($this->form_validation->run() == FALSE)
				{						
					$header = array( "title" => "Penyesuaian Stok");		
					$data = array(
						"stock_opname" => $stock_opname,
						"product"      => $this->opname->detail_product_stock_opname($stock_opname['id'], $stock_opname['warehouse_id']),
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
					if($this->crud->update('stock_opname', ['status' => 1], ['id' => $stock_opname['id']]))
					{
						$res = 0;
						foreach($post['product'] AS $info)
						{
							$product_id = $this->crud->get_product_id($info['product_code']);
							$where_so_detail = array(
								'stock_opname_id' => $stock_opname['id'],
								'product_id'	  => $product_id,
								'product_code'	  => $info['product_code'],
								'unit_id'         => $info['unit_id']
							);
							$data_so_detail=array(												
								'stock'	  	=> $info['stock'],
								'adjust'  	=> $info['adjust'],
								'end_stock'	=> $info['end_stock'],
								'hpp'		=> $this->opname->get_hpp($info['product_code'])
							);		
							if($this->crud->update('stock_opname_detail', $data_so_detail, $where_so_detail))
							{
								$where_stock = array(
									'product_id'	  => $product_id,
									'product_code'	  => $info['product_code'],
									'warehouse_id'    => $stock_opname['warehouse_id']
								);
								$stock = $this->crud->get_where('stock', $where_stock)->row_array();
								$data_stock = array (
									'qty' => $stock['qty']+$info['adjust']
								);
								if($this->crud->update('stock', $data_stock, $where_stock))
								{							
									$method = ($info['adjust'] >= 0) ? 1 : 2;
									// STOCK CARD
									$where_last_stock_card = [
										'date <='      => $stock_opname['date'],
										'product_id'   => $product_id,
										'warehouse_id' => $stock_opname['warehouse_id'],
										'deleted'      => 0
									];
									$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$data_stock_card = array(
										'type'            => 8, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
										'information'     => 'PENYESUAIAN STOK',
										'note'			  => $stock_opname['code'],
										'date'			  => $stock_opname['date'],
										'transaction_id'  => $stock_opname['id'],
										'invoice'         => $stock_opname['code'],
										'product_id'      => $product_id,
										'product_code'    => $info['product_code'],
										'qty'             => abs($info['adjust']),
										'method'          => $method, // 1:In, 2:Out
										'stock'           => $last_stock_card['stock']+$info['adjust'],
										'warehouse_id'    => $stock_opname['warehouse_id'],
										'user_id'         => $this->session->userdata('id_u')
									);
									$this->crud->insert('stock_card',$data_stock_card);
									$where_after_stock_card = [
										'date >'       => $stock_opname['date'],
										'product_id'   => $product_id,
										'warehouse_id' => $stock_opname['warehouse_id'],
										'deleted'      => 0
									];                    
									$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_card  AS $info_after_stock_card)
									{
										$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']+$info['adjust']], ['id' => $info_after_stock_card['id']]);
									}
									// STOCK MOVEMENT
									$where_last_stock_movement = [
										'product_id'   => $product_id,
										'date <='      => $stock_opname['date'],
										'deleted'      => 0
									];
									$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
									$data_stock_movement = [
										'type'            => 8,
										'information'     => 'PENYESUAIAN STOK',
										'note'			  => $stock_opname['code'],
										'date'            => $stock_opname['date'],
										'transaction_id'  => $stock_opname['id'],
										'invoice'         => $stock_opname['code'],
										'product_id'      => $product_id,
										'product_code'    => $info['product_code'],
										'qty'             => abs($info['adjust']),
										'method'          => $method, // 1:In, 2:Out
										'stock'           => $last_stock_movement['stock']+$info['adjust'],
										'employee_code'   => $this->session->userdata('code_e')
									];
									$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
									$where_after_stock_movement = [
										'product_id'   => $product_id,
										'date >'       => $stock_opname['date'],
										'deleted'      => 0
									];                    
									$after_stock_movement = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_stock_movement  AS $info_after_stock_movement)
									{
										$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']+$qty_convert], ['id' => $info_after_stock_movement['id']]);
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
				redirect(site_url('opname'));
			}
			else
			{
				$stock_opname = $this->opname->detail_stock_opname(decrypt_custom($stock_opname_id));
				$header = array( "title" => "Penyesuaian Stok");		
				$data = array(
					"stock_opname" => $stock_opname,
					"product"      => $this->opname->detail_product_stock_opname($stock_opname['id'], $stock_opname['warehouse_id']),
				);
				$footer = array("script" => ['inventory/opname/adjusment/create_adjusment_stock.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('opname/adjusment/create_adjusment_stock', $data);
				$this->load->view('include/footer', $footer);				
			}		
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('opname'));
		}		
	}
}