<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Product extends System_Controller 
{	
    public function __construct()
  	{
        parent::__construct();		
        $this->load->model('Product_model', 'product');
	}

    public function index()
    {
		if($this->system->check_access('product', 'A'))
		{
			if($this->input->is_ajax_request())
			{				
				$post         = $this->input->post();
				$search       = (!isset($post['search'])) ? null : trim($post['search']);
				$department_code    = (!isset($post['department_code']))    ? null : $post['department_code'];
				$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
				$input_order  = (!isset($post['input_order']))  ? null : $post['input_order'];
				$product_type = (!isset($post['product_type'])) ? null : $post['product_type'];				
				$ppn 		  = (!isset($post['ppn'])) 		? null : $post['ppn'];
				$status 	  = (!isset($post['status'])) 	? null : $post['status'];
				$warehouse_id = (!isset($post['warehouse_id'])) ? null : $post['warehouse_id'];
				$draw         = (!isset($post['draw'])) 	? 0 : $post['draw'];
				$iLength      = (!isset($post['length'])) 	? null : $post['length'];
				$iStart   	  = (!isset($post['start'])) 	? null : $post['start'];
				$iOrder   	  = (!isset($post['order'])) 	? null : $post['order'];
				if($search != "" || $department_code !="")
				{
					$total	      = $this->product->datatable($search, $department_code, $subdepartment_code, $product_type, $ppn, $status, $input_order)->num_rows();
					$product      = $this->product->datatable($search, $department_code, $subdepartment_code, $product_type, $ppn, $status, $input_order, $iLength, $iStart, $iOrder)->result_array();
					$data    = [];
					foreach($product AS $info)
					{
						$where_stock = array(
							'product_code' => $info['code'],					
							'deleted'	   => 0
						);				
						$data_stock = $this->crud->get_where_select('qty', 'stock', $where_stock)->result_array();
						$stock = 0;
						foreach($data_stock AS $info_stock)
						{
							$stock = $stock+$info_stock['qty'];
						}
						// $link_stock = ($stock > 0) ? '<a class="kt-font-primary kt-link text-center stock" href="javascript:void(0);" data-id="'.encrypt_custom($info['code']).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk melihat detail stock">'.$stock.'</a>' : '<a class="kt-font-danger kt-link text-center stock" href="javascript:void(0);" data-id="'.encrypt_custom($info['code']).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk melihat detail stock">'.$stock.'</a>';
						$link_stock = '<a class="text-primary kt-link stock" href="javascript:void(0);" data-id="'.encrypt_custom($info['code']).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk melihat detail stok">'.$stock.'</a>';
						$sellprice = ($info['sellprice'] != null) ? $info['sellprice'] : 0;
						$link_sellprice = '<a class="text-primary kt-link sellprice" href="javascript:void(0);" data-id="'.encrypt_custom($info['code']).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk melihat detail harga jual">'.number_format($sellprice, 2, '.', ',').'</a>';

						$data[] = array(
							'id' 	  	 => $info['id'],
							'barcode' 	 => $info['barcode'],
							'code' 		 => '<a class="text-primary kt-link text-center" href="'.site_url('product/detail/'.encrypt_custom($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
							'name' 		 => $info['name'],
							'stock' 	 => $link_stock,
							'unit' 		 => $info['unit'],
							'sellprice'  => $link_sellprice,
							'ppn' 		 => $info['ppn'],
						);
					}

					$output = array(
						'draw'            => $draw,
						'recordsTotal'    => $total,
						'recordsFiltered' => $total,
						'data'            => $data
					);
				}
				else
				{
					$draw   = (!isset($post['draw']))   ? 0 : $post['draw'];
                    $output = array(
                        'draw'            => $draw,
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => []
                    );
				}	
				header('Content-Type: application/json');			        
				echo json_encode($output);
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT DAFTAR PRODUK',
					'method'      => 1,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
	
				$header = array("title" => "Produk");        
				$data = array(
					'warehouse'      => $this->crud->get('warehouse')->result_array(),
					'lower_buyprice' => $this->product->lower_buyprice()
				);
				$footer = array("script" => ['master/product/product.js']);			
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');
				$this->load->view('product/product', $data);
				$this->load->view('include/footer', $footer);
				// $this->output->enable_profiler(TRUE);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));			
		}        
	}
	  
	public function detail_stock()
	{
		if($this->input->is_ajax_request())
		{
			$product_code = decrypt_custom($this->input->post('product_code'));
			$stock = $this->product->detail_stock($product_code);		
			$html="";
			foreach($stock AS $info)
			{					
				if($info['total_stock'] > 0)
				{
					$html .= '<div class="col-md-3"><p class="kt-font-primary text-center">'.$info['warehouse'].' | '.$info['location'].'</p>			
					<table class="table table-sm table-bordered kt-font-bold text-dark text-center"';
					foreach($info['stock'] AS $info2)
					{
						$html .= '<tr><td>'.$info2['value'].'</td><td>'.$info2['unit'].'</td></tr>';
					}
					$html .= '</table></div>';
				}
			}		
			echo json_encode($html);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function detail_sellprice()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$sellprice = $this->product->detail_sellprice(decrypt_custom($post['product_code']));
			$html = "";
			$html .= '<h6 class="text-dark font-weight-bold">'.$sellprice[0]['name_p'].'</h6><table class="table table-bordered"><thead><tr><th>SATUAN</th>';
			for($i=1; $i<=5; $i++)
			{
				if($this->system->check_access('view_sellprice_'.$i, 'A'))
				{
					$html.='<th class="text-center">HRG. '.$i.'</th>';
				}
			}
			$html.='</tr></thead><tbody>';				
			foreach($sellprice AS $info_sellprice)
			{
				$html.='<tr><td>'.$info_sellprice['name_u'].'</td>';
				for($j=1; $j<=5; $j++)
				{
					if($this->system->check_access('view_sellprice_'.$j, 'A'))
					{
						$html.='<td class="text-right">'.number_format($info_sellprice['price_'.$j], 2, '.', ',').'</td>';
					}
				}
				$html.='</tr>';
			}
			$html.='</tbody></table>';
			echo json_encode($html);		
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

    public function create()
    {
		if($this->system->check_access('product', 'C'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->form_validation->set_rules('department_code', 'Departemen', 'trim|required|xss_clean');
				$this->form_validation->set_rules('subdepartment_code', 'Subdepartemen', 'trim|required|xss_clean');
				$this->form_validation->set_rules('barcode', 'Barcode', 'trim|xss_clean');
				$this->form_validation->set_rules('name', 'Nama Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('productid', 'Identitas Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('description', 'Deskripsi Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('minimal', 'Stok Minimal', 'trim|required|xss_clean');
				$this->form_validation->set_rules('maximal', 'Stok Maksimal', 'trim|required|xss_clean');
				$this->form_validation->set_rules('status', 'Status Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('unit_id', 'Satuan Dasar Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('weight', 'Berat Dasar Produk', 'numeric|trim|xss_clean');
				$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');								       
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Produk Baru");
					$footer = array("script" => ['master/product/crud_product.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('product/add_product');      
					$this->load->view('include/footer', $footer);
				}
				else
				{			
					$code = $this->product->generate_code($post['department_code'], $post['subdepartment_code']);
					$photo = null;
					if(!empty($_FILES['photo']['name']))
					{				
						$config		= ([
							'upload_path'			=> './assets/media/system/products',
							'allowed_types'			=> 'jpg|jpeg|png',
							'max_size'				=> 1024,
							'remove_space'			=> TRUE,
							'file_name'				=> $code,
							'overwrite'				=> TRUE
						]);
						$this->load->library('upload', $config);
						$this->upload->initialize($config);
						
						if($this->upload->do_upload('photo'))
						{
							$this->load->library('image_lib');					
							$resize=$this->upload->data();
							$configer =  array(
								'image_library'   => 'gd2',
								'source_image'    => $resize['full_path'],
								'create_thumb' 	  => FALSE,
								'maintain_ratio'  => FALSE,
								'quality'         => '60%',
								'width'           => 600,
								'height'          => 600,
								);
							$this->image_lib->clear();
							$this->image_lib->initialize($configer);
											
							if($this->image_lib->resize())
							{	
								$photo = $this->upload->data('file_name');
							}
						}						
					}
					$data = [                
						'code'               => $code,
						'department_code'    => $post['department_code'],
						'subdepartment_code' => $post['subdepartment_code'],
						'type'               => $post['product_type'],
						'ppn'                => $post['ppn'],
						'barcode'            => $post['barcode'],
						'name'               => $post['name'],
						'productid'          => $post['productid'],
						'description'        => $post['description'],
						'photo'              => $photo,
						'minimal'         	 => $post['minimal'],
						'maximal'         	 => $post['maximal'],
						'commission_sales' 	 => $post['commission_sales'],
						'status'             => $post['status']
					];
					$product_id = $this->crud->insert_id('product',$data);                                     					
					if($product_id != null)
					{
						$data_unit = array(
							'product_id' 	=> $product_id,
							'product_code'	=> $code,
							'unit_id' 		=> $post['unit_id'],
							'value'			=> 1,
							'weight' 		=> $post['weight'],
							'default'		=> 1
						);
						if($this->crud->insert('product_unit', $data_unit))
						{	
							$data_sellprice = [
								'product_id' 	=> $product_id,
								'product_code'  => $code,
								'unit_id'   => $post['unit_id'],
								'default'   => 1,
								'price_1'	=> format_amount($post['price_1']),
								'price_2'	=> ($post['price_2'] != "") ? format_amount($post['price_2']) : 0,
								'price_3'	=> ($post['price_3'] != "") ? format_amount($post['price_3']) : 0,
								'price_4'	=> ($post['price_4'] != "") ? format_amount($post['price_4']) : 0,
								'price_5'	=> ($post['price_5'] != "") ? format_amount($post['price_5']) : 0
							];
							$this->crud->insert('sellprice', $data_sellprice);
							$warehouse = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
							foreach($warehouse AS $info_warehouse)
							{
								$data_stock = [
									'product_id' 	=> $product_id,
									'product_code' 	=> $code,
									'qty'			=> 0,
									'warehouse_id' 	=> $info_warehouse['id']
								];
								$this->crud->insert('stock', $data_stock);
							}
							if($post['product_type'] == 2)
							{
								foreach($post['product'] AS $info)
								{
									$data_bundle = array(
										'master_product_id' => $product_id,
										'product_id'   => $this->crud->get_product_id($info['product_code']),
										'product_code' => $info['product_code'],
										'qty'     	   => $info['qty'],
										'unit_id'  	   => $info['unit_id'],
									);
									$this->crud->insert('product_bundle', $data_bundle);
								}
							}
							$data_activity = [
								'information' => 'MEMBUAT PRODUK BARU (CODE '.$code.')',
								'method'      => 3,
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];
							$this->crud->insert('activity', $data_activity);
							$this->session->set_flashdata('success', 'Data Produk berhasil ditambahkan');
							redirect(site_url('product/detail/'.encrypt_custom($code)));							
						}
						else
						{
							$this->session->set_flashdata('error', 'Mohon maaf, Data Unit Produk gagal ditambahkan');
							redirect(site_url('product'));
						}												
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Data Informasi Produk gagal ditambahkan');
						redirect(site_url('product'));
					}					
				}
			}
			else
			{
				$header = array("title" => "Produk Baru");        
				$footer = array("script" => ['master/product/crud_product.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('product/create_product');      
				$this->load->view('include/footer', $footer);				
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('product'));
		}		        
	}
	
	// STOCK CARD
	public function datatable_stock_card($product_id)
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();            
			$this->datatables->select('stock_card.id AS id_sc, stock_card.transaction_id, DATE_FORMAT(stock_card.date, "%d-%m-%Y") AS date, stock_card.invoice, stock_card.qty, stock_card.information, stock_card.note, stock_card.type,
							stock_card.method, stock_card.stock, stock_card.created, warehouse.code AS code_w, warehouse.name AS name_w');
			$this->datatables->from('stock_card');
			$this->datatables->join('warehouse', 'warehouse.id = stock_card.warehouse_id');
			$this->datatables->where('stock_card.deleted', 0);
			$this->datatables->where('stock_card.product_id', $product_id);
			if($post['warehouse_id_sc'] != "")
			{
				$this->datatables->where('stock_card.warehouse_id', $post['warehouse_id_sc']);
			}
			if($post['transaction_type'] != "")
			{
				$this->datatables->where('stock_card.type', $post['transaction_type']);
			}
			$this->datatables->group_by('stock_card.id');
			$this->datatables->order_by('stock_card.date', 'DESC');
			$this->datatables->order_by('stock_card.id', 'DESC');
            $this->datatables->add_column('transaction_id', 
			'$1', 'encrypt_custom(transaction_id)');
			header('Content-Type: application/json');
            echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function print_stock_card($code)
	{
		$code = decrypt_custom($code);
		$data = array(
			'product' 		=> $this->product->detail_product($code),				
			'stock_card' 	   => $this->product->stock_card($code)				
		);
		$this->load->view('product/print_stock_card', $data);
	}

	// STOCK MOVEMENT
	public function datatable_stock_movement($product_id)
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();            
			$this->datatables->select('stock_movement.id AS id_sc, stock_movement.transaction_id, DATE_FORMAT(stock_movement.date, "%d-%m-%Y") AS date, stock_movement.invoice, stock_movement.qty, stock_movement.information, stock_movement.note, stock_movement.type,
							stock_movement.method, stock_movement.stock, stock_movement.price, stock_movement.hpp, stock_movement.created');
			$this->datatables->from('stock_movement');
			$this->datatables->where('stock_movement.deleted', 0);
			$this->datatables->where('stock_movement.product_id', $product_id);	
			if($post['transaction_type'] != "")
			{
				$this->datatables->where('stock_movement.type', $post['transaction_type']);
			}		
			$this->datatables->group_by('stock_movement.id');
			$this->datatables->order_by('stock_movement.date', 'DESC');
			$this->datatables->order_by('stock_movement.id', 'DESC');
            $this->datatables->add_column('transaction_id', 
			'
				$1			                
			', 'encrypt_custom(transaction_id)');
			header('Content-Type: application/json');
            echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	// LIST OF PURCHASE INVOICE
	public function datatable_list_of_purchase_invoice($product_id)
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('purchase_invoice_detail.id, purchase_invoice.id AS id_pi, purchase_invoice.date, purchase_invoice.code AS code_pi, supplier.name AS name_s, product.code AS code_p, product.name AS name_p, unit.name AS name_u, purchase_invoice_detail.qty, purchase_invoice_detail.price, purchase_invoice_detail.disc_product, purchase_invoice_detail.total');
			$this->datatables->from('purchase_invoice_detail');
			$this->datatables->join('product', 'product.code = purchase_invoice_detail.product_code');
			$this->datatables->join('unit', 'unit.id = purchase_invoice_detail.unit_id');
			$this->datatables->join('purchase_invoice', 'purchase_invoice.id = purchase_invoice_detail.purchase_invoice_id');
			$this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');		
			$this->datatables->where('product.id', $product_id);
			$this->datatables->group_by('purchase_invoice_detail.id');
			$this->datatables->add_column('code_pi','<a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/$1').'"><b>$2</b></a>', 'encrypt_custom(id_pi), code_pi');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	// LIST OF SALES INVOICE
	public function datatable_list_of_sales_invoice($product_id)
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('sales_invoice_detail.id, sales_invoice.id AS id_si, sales_invoice.date, sales_invoice.invoice AS code_si, customer.name AS name_c, product.code AS code_p, product.name AS name_p, unit.name AS name_u, sales_invoice_detail.qty, sales_invoice_detail.price, sales_invoice_detail.disc_product, sales_invoice_detail.total');
			$this->datatables->from('sales_invoice_detail');
			$this->datatables->join('product', 'product.code = sales_invoice_detail.product_code');
			$this->datatables->join('unit', 'unit.id = sales_invoice_detail.unit_id');
			$this->datatables->join('sales_invoice', 'sales_invoice.id = sales_invoice_detail.sales_invoice_id');
			$this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');		
			$this->datatables->where('product.id', $product_id);
			$this->datatables->group_by('sales_invoice_detail.id');
			$this->datatables->add_column('code_si','<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>', 'encrypt_custom(id_si), code_si');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

    public function detail($code)
    {
		if($this->system->check_access('product', 'R'))
		{
			$code = decrypt_custom($code);
			$data_activity = [
				'information' => 'MELIHAT DETAIL PRODUK (CODE '.$code.')',
				'method'      => 2,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
			$this->crud->insert('activity', $data_activity);
		
			$header = array("title" => "Detail Produk");
			$data = array(
				'product' 			=> $this->product->detail_product($code),
				'last_buyprice'     => $this->product->last_buyprice($code),
				'hpp'    		    => $this->product->hpp($code),
				'unit_product'		=> $this->product->get_unit_product($code),
				'multi_price' 		=> $this->product->multi_price($code),
				'unit_option'		=> $this->product->get_unit_option_product($code),
				'multi_unit' 		=> $this->product->multi_unit($code),
				'warehouse_option'  => $this->product->get_product_warehouse($code),
				'product_location'  => $this->product->product_location($code),
				'warehouse_stock_card' => $this->product->get_warehouse_stock_card($code),
				'list_of_purchase_invoice' => $this->product->list_of_purchase_invoice($code),
				'list_of_sales_invoice' => $this->product->list_of_sales_invoice($code)
			);
			$footer = array("script" => ['master/product/detail_product.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('product/detail_product', $data);
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('product'));
		}        
    }
	
	public function check_stock()
	{
		if($this->input->is_ajax_request())
		{
			$product_code = $this->input->post('product_code');
			$stock 	  = $this->crud->get_where('stock', ['product_code' => $product_code, 'deleted' => 0])->result_array();
			$total_qty = 0;
			foreach($stock as $info)		
			{
				$total_qty = $total_qty + $info['qty'];
			}
			if($total_qty == 0)
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

    public function update($code)
    {		
		if($this->system->check_access('product', 'U'))
		{
			if($this->input->method() === 'post')
			{
				$this->form_validation->set_rules('barcode', 'Barcode', 'trim|xss_clean');
				$this->form_validation->set_rules('name', 'Nama Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('productid', 'Identitas Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('description', 'Deskripsi Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('minimal', 'Stok Minimal', 'trim|required|xss_clean');
				$this->form_validation->set_rules('maximal', 'Stok Maksimal', 'trim|required|xss_clean');
				$this->form_validation->set_rules('status', 'Status Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('weight', 'Berat Dasar Produk', 'numeric|trim|xss_clean');
				$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
				
				$post = $this->input->post(); $product_id = $post['product_id']; $product_code = $post['product_code'];
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Perbarui Produk");
					$data = array(
						'product'        => $this->product->detail_product($product_code),
					);
					$footer = array("script" => ['master/product/crud_product.js', 'master/product/update_product.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('product/update_product', $data);
					$this->load->view('include/footer', $footer);
				}
				else
				{
					if(!empty($_FILES['photo']['name']))
					{                
						$config		= ([
							'upload_path'			=> './assets/media/system/products',
							'allowed_types'			=> 'jpg|jpeg|png',
							'max_size'				=> 1024,
							'remove_space'			=> TRUE,
							'file_name'				=> $code,
							'overwrite'				=> TRUE,
						]);
						$this->load->library('upload', $config);
						$this->upload->initialize($config);				
						if($this->upload->do_upload('photo'))
						{
							$this->load->library('image_lib');					
							$resize=$this->upload->data();
							$configer =  array(
								'image_library'   => 'gd2',
								'source_image'    => $resize['full_path'],
								'create_thumb' 	  => FALSE,
								'maintain_ratio'  => FALSE,
								'width'           => 600,
								'height'          => 600,
								);
							$this->image_lib->clear();
							$this->image_lib->initialize($configer);
							$this->image_lib->resize();
							$data = [                					
								'department_code'    => $post['department_code'],
								'subdepartment_code' => $post['subdepartment_code'],
								'type'               => $post['product_type'],
								'ppn'                => $post['ppn'],								
								'barcode'            => $post['barcode'],
								'name'               => $post['name'],
								'productid'          => $post['productid'],
								'description'        => $post['description'],
								'photo'				 => $this->upload->data('file_name'),
								'minimal'            => $post['minimal'],
								'maximal'            => $post['maximal'],
								'commission_sales'    => $post['commission_sales'],
								'status'             => $post['status']								
							];
														         
						}
						else
						{
							return false;
						}
					}
					else
					{					
						$data = [
							'department_code'    => $post['department_code'],
							'subdepartment_code' => $post['subdepartment_code'],
							'type'               => $post['product_type'],
							'ppn'                => $post['ppn'],
							'barcode'            => $post['barcode'],
							'name'               => $post['name'],
							'productid'          => $post['productid'],
							'description'        => $post['description'],
							'minimal'            => $post['minimal'],
							'maximal'            => $post['maximal'],
							'commission_sales'    => $post['commission_sales'],							
							'status'             => $post['status']
						];
					}								
					if($this->crud->update_by_id('product', $data, $product_id))
					{
						$data_weight = ['weight'=> $post['weight']];						
						$where_unit = array(
							'product_id'	=> $product_id,
							'default'		=> 1,
						);
						if($this->crud->update('product_unit', $data_weight, $where_unit))
						{
							$weight = $this->crud->get_where('product_unit', ['product_id' => $product_id, 'default !=' => 1])->result_array();
							foreach($weight AS $info)
							{
								$data_weight = array(						
									'weight' 		=> $info['value']*$post['weight'],
								);
								$this->crud->update('product_unit', $data_weight,['id' => $info['id']]);
							}
							$data_activity = [
								'information' => 'MEMPERBARUI DATA PRODUK (CODE '.$code.')',
								'method'      => 4,
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];
							$this->crud->insert('activity', $data_activity);
							$this->session->set_flashdata('success', 'Data Produk berhasil diperbarui');
						}
						else
						{
							$this->session->set_flashdata('error', 'Mohon maaf, Data Berat Dasar Produk gagal diperbarui');

						}					
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Data Produk gagal diperbarui');
					}
					redirect(site_url('product'));
				}
			}
			else
			{
				$code = decrypt_custom($code);
				$header = array("title" => "Perbarui Produk");
				$data = array(
					'product'        => $this->product->detail_product($code)
				);
				$footer = array("script" => ['master/product/crud_product.js', 'master/product/update_product.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product/update_product', $data);
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('product'));
		}        
    }
	
	public function verify_delete_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$product_code = $post['product_code'];
			$stock_card = $this->crud->get_where('stock_card', ['deleted' => 0, 'product_code' => $product_code])->num_rows();			
			if($stock_card > 0)
			{
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Produk tidak dapat dihapus, sudah memiliki transaksi',
					],
					'response'  => ''
				];
			}
			else
			{
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Produk dapat dihapus, tidak memiliki transaksi',
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

    public function delete()
    {
		if($this->input->is_ajax_request())
		{
			if($this->system->check_access('product', 'D'))
			{
				$code     = $this->input->get('code');
				$data = array(
					'deleted' => 1
				);
				$where = array(
					'product_code' => $code				
				);
				$delete_product_unit = $this->crud->update('product_unit', $data, $where);
				if($delete_product_unit)
				{				
					$delete = $this->crud->update_by_code('product', $data, $code);				
					if($delete)
					{
						$data_activity = [
							'information' => 'MENGHAPUS DATA PRODUK (CODE'.$code.')',
							'method'      => 5,
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];
						$this->crud->insert('activity', $data_activity);

						$response = [
							'status'	=> [
								'code'  	=> 200,
								'message'   => 'Berhasil Menghapus Data',
							],
							'response'  => ''
						];
						$this->session->set_flashdata('success', 'Data Produk berhasil dihapus');					
					}
					else
					{
						$response   =   [
							'status'    => [
								'code'      => 401,
								'message'   => 'Gagal Menghapus Data',
							],
							'response'  => ''
						];
						$this->session->set_flashdata('error', 'Mohon maaf, Data Produk gagal dihapus');					
					}
				}
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Gagal Menghapus Data',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('error', 'Mohon maaf, Data Produk gagal dihapus');				
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
		else
		{
			$this->load->view('auth/show_404');
		}		  
	}  	

	// UPDATE BUYPRICE HPP
	public function get_detail_buyprice_hpp()
	{	
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$where = [
				'code' => $post['product_code'],
				'deleted'	   => 0
			];
			$data = $this->crud->get_where('product', $where)->row_array();
			echo json_encode($data);
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function update_buyprice_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$data = [
				'buyprice' => format_amount($post['buyprice_product'])
			];
			if($this->crud->update('product', $data, ['deleted' => 0, 'code' => $post['product_code']]))
			{
				$data_activity = [
					'information' => 'MEMPERBARUI Harga Beli Terakhir PRODUK (CODE '.$post['product_code'].')',
					'method'      => 4,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Data Harga Beli Terakhir Produk berhasil diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('success', 'Data Harga Beli Terakhir Produk berhasil diperbaharui');
			}
			else
			{
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Data Harga Beli Terakhir Produk gagal diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('error', 'Data Harga Beli Terakhir Produk gagal diperbaharui');
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function update_hpp_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$data = [
				'hpp' => format_amount($post['hpp_product'])
			];
			if($this->crud->update('product', $data, ['deleted' => 0, 'code' => $post['product_code']]))
			{
				$data_activity = [
					'information' => 'MEMPERBARUI HPP PRODUK (CODE '.$post['product_code'].')',
					'method'      => 4,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Data HPP Produk berhasil diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('success', 'Data HPP Produk berhasil diperbaharui');
			}
			else
			{
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Data HPP Produk gagal diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('error', 'Data HPP Produk gagal diperbaharui');
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	// MULTIPRICE
	public function get_product_unit_value()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();		
			$data = $this->product->get_product_unit_value($post['product_code'], $post['unit_id']);
			echo json_encode($data);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function multi_price()
	{
		if($this->input->is_ajax_request())
		{
			$post   = $this->input->post();
			$this->form_validation->set_rules('product_code', 'Kode Produk', 'trim|required|xss_clean');
			$this->form_validation->set_rules('unit_id', 'Satuan', 'trim|required|xss_clean');
			$this->form_validation->set_rules('price_1', 'Harga Jual 1', 'trim|required|xss_clean');
			$this->form_validation->set_rules('price_2', 'Harga Jual 2', 'trim|xss_clean');
			$this->form_validation->set_rules('price_3', 'Harga Jual 3', 'trim|xss_clean');
			$this->form_validation->set_rules('price_4', 'Harga Jual 4', 'trim|xss_clean');
			$this->form_validation->set_rules('price_5', 'Harga Jual 5', 'trim|xss_clean');

			$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');        
			if($this->form_validation->run() == FALSE)
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Data Harga Jual Produk gagal ditambahkan/diperbaharui',
					],
					'response'  => ''
				];
				$this->session->set_flashdata('error', 'Data Harga Jual Produk gagal ditambahkan');
			}
			else
			{	
				$res = 0;
				$code = $post['product_code'];
				$product_id = $this->product->get_product_id($code);
				$where = array(
					'product_id' 	=> $product_id,
					'product_code' 	=> $code,
					'unit_id'  		=> $post['unit_id'],
					'deleted' 		=> 0
				);
				for($i=1;$i<=5;$i++)
				{
					$name   	= 'price_'.$i;
					if($post[$name] != "")
					{
						$last_price = $post[$name];
					}
					$data   = ($post[$name] == "") ?  $last_price : $post[$name];
					$price  = format_amount($data);
					$check_data = $this->crud->get_where('sellprice', $where);
					$where_unit = array(
						'product_code' 	=> $code,
						'unit_id'  		=> $post['unit_id'],
					);
					$product_unit = $this->crud->get_where('product_unit', $where_unit)->row_array();									
					if($check_data->num_rows() == 0)
					{						
						$data = [
							'product_id' 	=> $product_id,
							'product_code' 	=> $code,
							'unit_id'  		=> $post['unit_id'],
							'default'  		=> $product_unit['default'],
							$name 			=> $price
						];
						if($this->crud->insert('sellprice', $data))
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
						$data = [				
							$name		=> $price
						];						
						if($this->crud->update('sellprice', $data, $where))
						{			
							$res = 1;									
							continue;
						}
						else
						{							
							break;
						}
					}
				}
				if($res == 1)
				{
					$data_activity = [
						'information' => 'MEMBUAT/MEMPERBARUI HARGA JUAL PRODUK (CODE '.$post['product_code'].') - (ID SATUAN '.$post['unit_id'].')',
						'method'      => 3,
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];
					$this->crud->insert('activity', $data_activity);
					$this->session->set_flashdata('success', 'Data Harga Jual Produk berhasil ditambahkan');
					$response   =   [
						'status'    => [
							'code'      => 200,
							'message'   => 'Data Harga Jual Produk berhasil ditambahkan/diperbaharui',
						],
						'response'  => ''
					];
				}						
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Data Harga Jual Produk gagal ditambahkan/diperbaharui',
						],
						'response'  => ''
					];
					$this->session->set_flashdata('error', 'Data Harga Jual Produk gagal diperbaharui');
				}
				echo json_encode($response);
			}
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function get_detail_sellprice()
	{		
		$id=$this->input->get('id');
		$data = $this->product->get_detail_sellprice($id);				
        echo json_encode($data);
	}
	
	public function delete_multi_price()
	{		
		$id = $this->input->get('id');
		$sellprice = $this->crud->get_where('sellprice', ['id' => $id])->row_array();
		if($sellprice['default'] == 1)
		{
			$response = [
				'status' => [
					'code'      => 401,
					'message'   => 'Harga Jual Satuan Dasar tidak dapat dihapus',
				],
				'response'  => ''
			];			
		}
		else
		{
			$delete	= $this->crud->delete('sellprice', ['id' => $id]);
			if($delete)
			{
				$data_activity = [
					'information' => 'MENGHAPUS DATA HARGA JUAL PRODUK (ID '.$id.')',
					'method'      => 5,
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
					'response'  => ''
				];
				$this->session->set_flashdata('success', 'Data Harga Jual berhasil di hapus');			
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
				$this->session->set_flashdata('error', 'Data Harga Jual gagal di hapus');			
			}
		}						                    
		echo json_encode($response);
	}

	// MULTIUNIT
	public function add_multi_unit()
	{
		$post   = $this->input->post();
		$this->form_validation->set_rules('product_code', 'Kode Produk', 'trim|required|xss_clean');
        $this->form_validation->set_rules('unit_id', 'Satuan', 'trim|required|xss_clean');
        $this->form_validation->set_rules('value', 'Jumlah Satuan Dasar', 'trim|required|xss_clean');

        $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');        
        if($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('error', 'Data Multi Satuan gagal ditambahkan');
			redirect(site_url('product/detail/'.$post['product_code']));
        }
        else
        {
			$product_id = $this->product->get_product_id($post['product_code']);
			$product_weight = $this->product->get_product_weight($post['product_code']);
            $data = [
				'product_id' 	=> $product_id,
				'product_code' 	=> $post['product_code'],
                'unit_id'  		=> $post['unit_id'],
				'value'			=> $post['value'],
				'weight'		=> $post['value']*$product_weight,
				'default'		=> 0
            ];
			$insert	= $this->crud->insert('product_unit', $data);						
            if($insert)
            {
				$data_activity = [
					'information' => 'MENAMBAH SATUAN PRODUK (CODE '.$post['product_code'].') - (ID SATUAN '.$post['unit_id'].')',
					'method'      => 3,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Data Satuan Produk berhasil ditambahkan',
					],
					'response'  => ''
				];								
                $this->session->set_flashdata('success', 'Data Satuan Produk berhasil ditambahkan');				
            }
            else
            {
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Data Satuan Produk gagal ditambahkan',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('error', 'Data Satuan Produk gagal ditambahkan');				
            }
		}	
		echo json_encode($response);	
	}

	public function get_detail_multi_unit()
	{		
		$id=$this->input->get('id');
		$data = $this->product->get_detail_multi_unit($id);				
        echo json_encode($data);
	}

	public function update_multi_unit()
	{
		$post   = $this->input->post();		
		$this->form_validation->set_rules('id_mu', 'ID Multi Unit', 'trim|required|xss_clean');
		$this->form_validation->set_rules('product_code', 'Kode Produk', 'trim|required|xss_clean');
        $this->form_validation->set_rules('value', 'Jumlah Satuan Dasar', 'trim|required|xss_clean');
        $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');        
        if($this->form_validation->run() == FALSE)
        {
            $response = [
				'status' => [
					'code'      => 401,
					'message'   => 'Data Satuan Produk gagal diperbaharui',
				],
				'response'  => ''
			];			
        }
        else
        {
			$where = array(
				'id' => $post['id_mu']
			);
			$product_weight = $this->product->get_product_weight($post['product_code']);
            $data = [				
				'value'			=> $post['value'],
				'weight'		=> $post['value']*$product_weight
            ];
			$update	= $this->crud->update('product_unit', $data, $where);			
            if($update)
            {
				$data_activity = [
					'information' => 'MEMPERBAHARUI SATUAN PRODUK (CODE '.$post['product_code'].')',
					'method'      => 4,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Data Satuan Produk berhasil diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('success', 'Data Satuan Produk berhasil diperbaharui');
            }
            else
            {
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Data Satuan Produk gagal diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('error', 'Data Satuan Produk gagal diperbaharui');
            }
		}
		echo json_encode($response);
	}

	public function delete_multi_unit()
	{
		$id = $this->input->get('id');
		$data = ['deleted' => 1];
		$delete	= $this->crud->update_by_id('product_unit', $data, $id);
		if($delete)
		{
			$data_activity = [
				'information' => 'MENGHAPUS SATUAN PRODUK (ID '.$id.')',
				'method'      => 5,
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
				'response'  => ''
			];
			$this->session->set_flashdata('success', 'Data Multi Satuan berhasil di hapus');			
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
			$this->session->set_flashdata('error', 'Data Multi Satuan gagal di hapus');			
		}	
		echo json_encode($response);			                    
	}

	// PRODUCT LOCATION
	public function add_product_location()
	{
		$post   = $this->input->post();
		$this->form_validation->set_rules('product_code', 'Kode Produk', 'trim|required|xss_clean');
        $this->form_validation->set_rules('warehouse_id', 'Gudang', 'trim|required|xss_clean');
        $this->form_validation->set_rules('location', 'Keterangan Lokasi', 'trim|required|xss_clean');

        $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');        
        if($this->form_validation->run() == FALSE)
        {
            $this->session->set_flashdata('error', 'Data Lokasi Penyimpanan gagal ditambahkan');
			redirect(site_url('product/detail/'.$post['product_code']));
        }
        else
        {
			$product_id = $this->product->get_product_id($post['product_code']);
            $data = [
				'product_id' 	=> $product_id,
				'product_code' 	=> $post['product_code'],
                'warehouse_id'  => $post['warehouse_id'],
				'location'		=> $post['location'],				
            ];			
            if($this->crud->insert('product_location', $data))
            {
				$data_activity = [
					'information' => 'MENAMBAH LOKASI PRODUK (CODE '.$post['product_code'].')',
					'method'      => 3,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);			
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Data Satuan Produk berhasil ditambahkan',
					],
					'response'  => ''
				];								
                $this->session->set_flashdata('success', 'Data Lokasi Penyimpanan Produk berhasil ditambahkan');				
            }
            else
            {
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Data Satuan Produk gagal ditambahkan',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('error', 'Data Lokasi Penyimpanan gagal ditambahkan');				
            }
		}	
		echo json_encode($response);	
	}

	public function get_detail_product_location()
	{		
		$id=$this->input->get('id');
		$data = $this->product->get_detail_product_location($id);				
        echo json_encode($data);
	}

	public function update_product_location()
	{
		$post   = $this->input->post();	
		$this->form_validation->set_rules('id_pl', 'ID Product Location', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('product_code', 'Kode Produk', 'trim|required|xss_clean');
        $this->form_validation->set_rules('location', 'Jumlah Satuan Dasar', 'trim|required|xss_clean');
        $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');        
        if($this->form_validation->run() == FALSE)
        {
            $response = [
				'status' => [
					'code'      => 401,
					'message'   => 'Data Satuan Produk gagal diperbaharui',
				],
				'response'  => ''
			];			
        }
        else
        {
			$where = array('id' => $post['id_pl']);
            $data = ['location'			=> $post['location']];
            if($this->crud->update('product_location', $data, $where))
            {
				$data_activity = [
					'information' => 'MEMPERBAHARUI LOKASI PRODUK (CODE '.$post['product_code'].')',
					'method'      => 4,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Data Lokasi Penyimpnan berhasil diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('success', 'Data Lokasi Penyimpnan berhasil diperbaharui');
            }
            else
            {
				$response = [
					'status' => [
						'code'      => 401,
						'message'   => 'Data Lokasi Penyimpnan gagal diperbaharui',
					],
					'response'  => ''
				];
                $this->session->set_flashdata('error', 'Data Lokasi Penyimpnan gagal diperbaharui');				
            }
		}
		echo json_encode($response);
	}

	public function delete_product_location()
	{
		$id = $this->input->get('id');
		$data = ['deleted' => 1];		
		if($this->crud->update_by_id('product_location', $data, $id))
		{			
			$data_activity = [
				'information' => 'MENGHAPUS LOKASI PRODUK (ID '.$id.')',
				'method'      => 1,
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
				'response'  => ''
			];
			$this->session->set_flashdata('success', 'Data Lokasi Penyimpanan berhasil di hapus');			
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
			$this->session->set_flashdata('error', 'Data Lokasi Penyimpanan gagal di hapus');			
		}	
		echo json_encode($response);			                    
	}

	// BARCODE
	public function form_barcode($code)
    {   		
		$code = decrypt_custom($code);
		$header = array(
			"title" => 'Barcode'
			);        
		$data = array(
			'product' => $this->product->detail_product($code),
			'supplier' => $this->product->get_supplier($code)				
		);
		$this->load->view('include/header', $header);        
		$this->load->view('include/menubar');        
		$this->load->view('include/topbar');        
		$this->load->view('product/barcode/form_barcode', $data);
		$this->load->view('include/footer');	        
	}

	private function bar128($text)
	{        
        $char128asc=' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'; 
        $char128wid = array(
        '212222','222122','222221','121223','121322','131222','122213','122312','132212','221213', // 0-9 
        '221312','231212','112232','122132','122231','113222','123122','123221','223211','221132', // 10-19 
        '221231','213212','223112','312131','311222','321122','321221','312212','322112','322211', // 20-29 
        '212123','212321','232121','111323','131123','131321','112313','132113','132311','211313', // 30-39 
        '231113','231311','112133','112331','132131','113123','113321','133121','313121','211331', // 40-49 
        '231131','213113','213311','213131','311123','311321','331121','312113','312311','332111', // 50-59 
        '314111','221411','431111','111224','111422','121124','121421','141122','141221','112214', // 60-69 
        '112412','122114','122411','142112','142211','241211','221114','413111','241112','134111', // 70-79 
        '111242','121142','121241','114212','124112','124211','411212','421112','421211','212141', // 80-89 
        '214121','412121','111143','111341','131141','114113','114311','411113','411311','113141', // 90-99
        '114131','311141','411131','211412','211214','211232','23311120' );
        
        $w = $char128wid[$sum = 104]; // START symbol
        $onChar=1;
        for($x=0;$x<strlen($text);$x++) // GO THRU TEXT GET LETTERS
        if (!( ($pos = strpos($char128asc,$text[$x])) === false )){ // SKIP NOT FOUND CHARS
        $w.= $char128wid[$pos];
        $sum += $onChar++ * $pos;
        } 
        $w.= $char128wid[ $sum % 103 ].$char128wid[106]; //Check Code, then END
        //Part 2, Write rows
        $html="<table cellpadding=0 cellspacing=0><tr>"; 
        for($x=0;$x<strlen($w);$x+=2) // code 128 widths: black border, then white space
        $html .= "<td><div class=\"b128\" style=\"border-left-width:{$w[$x]};width:{$w[$x+1]}\"></div></td>"; 
        return "$html<tr><td colspan=".strlen($w)." align=center></td></tr></table>"; 
	}

	public function print_barcode()
    {   		
		$this->form_validation->set_rules('product_code', 'Kode Produk', 'trim|required|xss_clean');
		$this->form_validation->set_rules('print_qty', 'Jumlah Print', 'numeric|trim|required|xss_clean');
		$this->form_validation->set_message('numeric', 'Maaf! <b>%s</b> Harus Berupa Angka');
		$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
		
		$post = $this->input->post();
		$code 	= $post['product_code'];
		if($this->form_validation->run() == FALSE)
		{				
			$header = array(
				"title" => 'Barcode'
				);        
			$data = array(
				'product' => $this->product->detail_product($code)					
			);
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('product/barcode/form_barcode', $data);
			$this->load->view('include/footer');
		}
		else
		{				
			$product = $this->product->detail_product($code);
			$name 	  = $product['name_p'];
			$barcode  = $product['code_p'];
			$rate     = 'Rp. '.number_format($product['sellprice'],'0','0','.');
			$supplier  = $post['supplier_code'];
			for($i=1;$i<=$post['print_qty'];$i++)
			{
				$data['barcode'][] = "<p class='inline' align=center><span><b>$name</b></span>".$this->bar128(stripcslashes($barcode))."<span class='rate'><b>".$rate." </b></span><span class='rate'>".$supplier."</span></p>&nbsp&nbsp&nbsp&nbsp";
			}

			$data_activity = [
				'information' => 'MENCETAK BARCODE PRODUK (CODE '.$product['code_p'].')',
				'method'      => 6,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
			$this->crud->insert('activity',$data_activity);
			$this->load->view('product/barcode/print_barcode', $data);				
		}
	}			

	public function print_stock_product()
    {   		
		if($this->system->check_access('product/print_stock', 'A'))
		{	
			if($this->input->method() === 'post')
			{
				$data_activity = [
					'information' => 'MENCETAK DAFTAR STOK PRODUK',
					'method'      => 6,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);

				$post  	= $this->input->post();
				$filter = [
					'search' 			=> $post['search'],
					'department_code' 	=> $post['department_code'],
					'subdepartment_code'=> $post['subdepartment_code'],
					'warehouse_id' 		=> $post['warehouse_id'],
					'min'				=> $post['min'],
					'max'				=> $post['max']
				];
				
				$department = $this->db->select('name')->from('department')->where('code', $filter['department_code'])->get()->row_array();
				$subdepartment = $this->db->select('name')->from('subdepartment')->where('department_code', $filter['department_code'])->where('code', $filter['subdepartment_code'])->get()->row_array();
				$warehouse = $this->crud->get_where('warehouse', ['id' => $filter['warehouse_id']])->row_array();
				$data = array(
				    'filter'		=> $filter,
				    'department'     => (isset($department)) ? $department['name'] : "SEMUA",
				    'subdepartment'  => (isset($subdepartment)) ? $subdepartment['name'] : "SEMUA",
				    'warehouse'     => (isset($warehouse)) ? $warehouse['name'] : "SEMUA",
					'stock_product' => $this->product->get_stock_product($filter)
				);
				$this->load->view('product/stock/print_stock_product', $data);
				// echo json_encode($data['stock_product']);
			}
			else
			{
				$header = array("title" => 'Cetak Daftar Stok Produk');
				$footer = array("script" => ['master/product/stock_product.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product/stock/stock_product');
				$this->load->view('include/footer', $footer);				
			}					
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('product'));
		}
	}

	// PRICE LIST
	public function price_list()
    {
		$access_user_id = [1, 3, 14, 17];
		if(in_array($this->session->userdata('id_u'), $access_user_id))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				if($post['search_product'] != "")
                {					
					$this->datatables->select('product.id, product.code, product.name, round((sum(stock.qty)/product_unit.value), 2) AS stock,
											product_unit.value, unit.name AS unit,  (product.hpp*product_unit.value) AS hpp, (product.buyprice*product_unit.value) AS buyprice, product_unit.value,
											sellprice.price_1 AS price_1, sellprice.price_2 AS price_2, sellprice.price_3 AS price_3, sellprice.price_4 AS price_4, sellprice.price_5 AS price_5')
											->from('product')
											->join('stock', 'stock.product_code = product.code', 'left')
											->join('product_unit', 'product_unit.product_code = product.code')
											->join('unit', 'unit.id = product_unit.unit_id')
											->join('sellprice', 'sellprice.product_code = product.code AND sellprice.unit_id = product_unit.unit_id', 'left')
											->where('product.deleted', 0)->where('product_unit.deleted', 0)->where('unit.deleted', 0);
					if($post['subdepartment_code'] != "")
					{
						$this->datatables->where('product.subdepartment_code', $post['subdepartment_code']);
					}
					if($post['search_product'] != "")
					{
						$this->datatables->like('product.name', $post['search_product']);
						$this->datatables->or_like('product.code', $post['search_product']);
					}
					$this->datatables->group_by('product_unit.id')->order_by('product.code', 'asc')->order_by('product_unit.value', 'asc');
					$this->datatables->add_column('code',
					'
						<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'"><b>$2</b></a>
					', 'encrypt_custom(code), code');
					header('Content-Type: application/json');
					echo $this->datatables->generate();
                }
                else
                {					
                    $draw   = (!isset($post['draw']))   ? 0 : $post['draw'];
                    $output = array(
                        'draw'            => $draw,
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => []
					);
					header('Content-Type: application/json');
                    echo json_encode($output);
                }
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT DAFTAR HARGA PRODUK (JUAL, BELI, HPP)',
					'method'      => 1,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Daftar Harga Produk (Jual, Beli, HPP)");
				$data = array(
					'warehouse'      => $this->crud->get('warehouse')->result_array(),
					'lower_buyprice' => $this->product->lower_buyprice()
				);
				$footer = array("script" => ['master/product/price_list/price_list.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product/price_list/price_list', $data);
				$this->load->view('include/footer', $footer);
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));			
		}        
	}

	public function print_price_list()
    {   		
		if($this->system->check_access('product/print_pricelist', 'A'))
		{
			if($this->input->method() === 'post')
			{
				$data_activity = [
					'information' => 'MENCETAK DAFTAR HARGA PRODUK',
					'method'      => 6,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);

				$post = $this->input->post();						
				$coloumn = array('product.name', 'round(sum(stock.qty),2) AS qty', 'product_unit.value', 'unit.name AS name_u', 'sellprice.price_1 AS price_1');			
				$name = array_keys($post);						
				$no=1; 
				foreach($post AS $info)
				{	
					if($no <= 3)
					{
						$no++;
						continue;
					}
					else
					{					
						$coloumn[] = $info;					
					}				
				}
				$filter = [
					'search' 			=> $post['search'],
					'department_code' 	=> $post['department_code'],
					'subdepartment_code' => $post['subdepartment_code']
				];			
				$data = array(						
					'name'		 => $name,
					'price_list' => $this->product->get_price_list($filter, $coloumn)
				);
				$this->load->view('product/price_list/print_price_list', $data);
			}
			else
			{
				$header = array("title" => 'Cetak Daftar Harga Produk');
				$footer = array("script" => ['master/product/price_list/form_price_list.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('product/price_list/form_price_list');
				$this->load->view('include/footer', $footer);				
			}
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	// IMPORT/EXPORT SELLPRICE PRODUCT
	public function datatable_export_product_sellprice()
    {
		if($this->input->is_ajax_request())
		{			
			$post         = $this->input->post();
			$search_product     = (!isset($post['search_product'])) ?   null : $post['search_product'];
			$department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
			$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];        
			if($search_product != "" || $department_code != "")
			{
				$this->datatables->select('product.id, product.code AS choose,  product.code, product.name AS name_p, department.name AS name_d, subdepartment.name AS name_sd');
				$this->datatables->from('product');			
				$this->datatables->join('department', 'department.code = product.department_code');
				$this->datatables->join('subdepartment', 'subdepartment.department_code = department.code AND subdepartment.code = product.subdepartment_code');
				$this->datatables->where('product.deleted', 0);
				if($search_product != "")
				{
					$this->db->like('product.name', $search_product);
				}
				if($department_code != "")
				{
					$this->db->where('department.code', $department_code);
				}   
				if($department_code != "" && $subdepartment_code != "")
				{
					$this->db->where('department.code', $department_code);
					$this->db->where('subdepartment.code', $subdepartment_code);
				}
				$this->datatables->group_by('product.id');
				$this->datatables->add_column('choose',
				'			
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
					<input type="checkbox" name="product[]" value="$1" class="choose">&nbsp;<span></span>
					</label>
				', 'code');
				header('Content-Type: application/json');
				echo $this->datatables->generate();
			}
			else
			{
				$draw   = (!isset($post['draw']))   ? 0 : $post['draw'];
				$output = array(
					'draw'            => $draw,
					'recordsTotal'    => 0,
					'recordsFiltered' => 0,
					'data'            => []
				);
				header('Content-Type: application/json');
				echo json_encode($output);
			}
		}
		else
		{
			$this->load->view('auth/show_404');
		}		   
	}

	public function export_product_sellprice()
	{
		if($this->system->check_access('product/sellprice/export', 'A'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$access_user_id = [1, 3, 14, 17];
				$data_export = array();
				foreach($post['product'] AS $info_p)
				{
					$product_unit_sellprice = $this->db->select('product.code AS code_p, product.name AS name_p, (product.buyprice*product_unit.value) AS buyprice, (product.hpp*product_unit.value) AS hpp,
											unit.id AS id_u, unit.name AS name_u, product_unit.value,
											(round(sum(stock.qty), 2)/product_unit.value) AS stock,
											 sellprice.price_1, sellprice.price_2, sellprice.price_3, sellprice.price_4, sellprice.price_5')
											 ->from('product')
											 ->join('stock', 'stock.product_code = product.code', 'left')
											 ->join('product_unit', 'product_unit.product_code = product.code')
											 ->join('unit', 'unit.id = product_unit.unit_id')											 
											 ->join('sellprice', 'sellprice.product_code = product.code AND sellprice.unit_id = product_unit.unit_id', 'left')
											 ->where('product.deleted', 0)->where('product_unit.deleted', 0)->where('unit.deleted', 0)											
											 ->where('product.code', $info_p)
											 ->group_by('product_unit.id')
											 ->order_by('product.code', 'asc')
											 ->order_by('product_unit.value', 'asc')
											 ->get()->result_array();

					foreach($product_unit_sellprice AS $info_pus)
					{
						$data_export[] = array(
							'code_p' => $info_pus['code_p'],
							'name_p' => $info_pus['name_p'],
							'id_u' => $info_pus['id_u'],
							'name_u' => $info_pus['name_u'],
							'price_1' => $info_pus['price_1'],
							'price_2' => $info_pus['price_2'],
							'price_3' => $info_pus['price_3'],
							'price_4' => $info_pus['price_4'],
							'price_5' => $info_pus['price_5'],
							'buyprice' => $info_pus['buyprice'],
							'hpp'     => $info_pus['hpp'],
							'stock'   => $info_pus['stock'],
						);
					}					
				}				

				$spreadsheet = new Spreadsheet();				
				// Set document properties
				$spreadsheet->getProperties()->setCreator('TRUST System')
				->setLastModifiedBy('TRUST System')
				->setTitle('Export Harga Jual Produk')
				->setSubject('Export Harga Jual Produk')
				->setDescription('Export Harga Jual Produk');
				// Add some data
				$spreadsheet->setActiveSheetIndex(0)
							->setCellValue('A1', 'KODE PRODUK')
							->setCellValue('B1', 'NAMA')
							->setCellValue('C1', 'ID SATUAN')
							->setCellValue('D1', 'SATUAN')
							->setCellValue('E1', 'HARGA JUAL 1')
							->setCellValue('F1', 'HARGA JUAL 2')
							->setCellValue('G1', 'HARGA JUAL 3')
							->setCellValue('H1', 'HARGA JUAL 4')
							->setCellValue('I1', 'HARGA JUAL 5');
				if(in_array($this->session->userdata('id_u'), $access_user_id))
				{
					$spreadsheet->setActiveSheetIndex(0)->setCellValue('J1', 'HARGA BELI')
								->setCellValue('K1', 'HPP')
								->setCellValue('L1', 'STOK');
				}
				$i=2; 
				foreach($data_export as $info_export) 
				{
					$spreadsheet->setActiveSheetIndex(0)
								->setCellValue('A'.$i, $info_export['code_p'])
								->setCellValue('B'.$i, $info_export['name_p'])
								->setCellValue('C'.$i, $info_export['id_u'])
								->setCellValue('D'.$i, $info_export['name_u'])
								->setCellValue('E'.$i, $info_export['price_1'])
								->setCellValue('F'.$i, $info_export['price_2'])
								->setCellValue('G'.$i, $info_export['price_3'])
								->setCellValue('H'.$i, $info_export['price_4'])
								->setCellValue('I'.$i, $info_export['price_5']);
					if(in_array($this->session->userdata('id_u'), $access_user_id))
					{
						$spreadsheet->setActiveSheetIndex(0)->setCellValue('J'.$i, $info_export['buyprice'])						
															->setCellValue('K'.$i, $info_export['hpp'])
															->setCellValue('L'.$i, $info_export['stock']);
					}								
					;
					$i++;
				}

				// Rename worksheet
				$filename = 'TRUST System - Harga Jual Produk '.date('d-m-Y').'.xlsx';
				$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$spreadsheet->setActiveSheetIndex(0);

				$data_activity = [
					'information' => 'EXPORT HARGA JUAL PRODUK',
					'method'      => 3,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				ob_end_clean();
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
				$writer = new Xlsx($spreadsheet);								
				$writer->save('php://output');
			}
			else
			{
				$header = array("title" => "Export Harga Jual Produk");
				$footer = array("script" => ['master/product/export_product_sellprice.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('product/sellprice/export_product_sellprice');        
				$this->load->view('include/footer', $footer);				
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}

	public function import_product_sellprice()
	{
		if($this->system->check_access('product/sellprice/import', 'A'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();	
				$filename = 'ImportProductSellprice-'.$this->session->userdata('code_e');			
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				if(isset($post['form_type']) && $post['form_type'] == 0)
				{
					$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
					{
						$config['upload_path']   = "./assets/upload/product/";
						$config['allowed_types'] = "xlsx";
						$config['max_size']      = "10240";
						$config['remove_space']  = TRUE;
						$config['overwrite'] = true;
						$config['file_name'] = $filename;
						$this->load->library('upload', $config);
						if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
						{ 
							$upload = array(
								'result' => 'success',
								'error' => ''
							);
						}
						else
						{
							$upload = array(
								'result' => 'failed',								
								'error' => $this->upload->display_errors()
							);
						}
						if($upload['result'] == "success")
						{				
							$spreadsheet = $reader->load('assets/upload/product/'.$filename.'.xlsx');
							$sheetData = $spreadsheet->getActiveSheet()->toArray();
							$header = array("title" => "Import Harga Jual Produk");
							$data = array('sheet' => $sheetData);
							$footer = array("script" => ['master/product/import_product_sellprice.js']);
							$this->load->view('include/header', $header);        
							$this->load->view('include/menubar');        
							$this->load->view('include/topbar');        
							$this->load->view('product/sellprice/import_product_sellprice', $data);
							$this->load->view('include/footer', $footer);
						}
						else
						{
							$header = array("title" => "Import Harga Jual Produk");				
							$data = array('error' => $upload['error']);
							$this->load->view('include/header', $header);        
							$this->load->view('include/menubar');        
							$this->load->view('include/topbar');        
							$this->load->view('product/sellprice/import_product_sellprice', $data);
							$this->load->view('include/footer');							
						}
					}
					else
					{
						$header = array("title" => "Import Harga Jual Produk");				
						$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe <b>EXCELL</b>! Terima Kasih');
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('product/sellprice/import_product_sellprice', $data);
						$this->load->view('include/footer');
					}					
				}
				else
				{				
					$spreadsheet = $reader->load('assets/upload/product/'.$filename.'.xlsx');
					$sheetData = $spreadsheet->getActiveSheet()->toArray();		
					for($i = 1;$i < count($sheetData);$i++)
					{
						$res = 1;
						$last_price = 0;
						for($j=4;$j<=8;$j++)
						{
							if($sheetData[$i][$j] >0)
							{
								$last_price = format_amount($sheetData[$i][$j]);
							}
						}
						$where = array(
							'product_code' => $sheetData[$i][0],
							'unit_id'      => $sheetData[$i][2],
							'deleted' 	   => 0
						);
						$result = $this->crud->get_where('sellprice', $where)->row_array();
						if($result != null)
						{
							$data = array(
								'price_1' 	   => (format_amount($sheetData[$i][4]) > 0) ? format_amount($sheetData[$i][4]) : $last_price,
								'price_2'      => (format_amount($sheetData[$i][5]) > 0) ? format_amount($sheetData[$i][5]) : $last_price,
								'price_3'      => (format_amount($sheetData[$i][6]) > 0) ? format_amount($sheetData[$i][6]) : $last_price,
								'price_4'      => (format_amount($sheetData[$i][7]) > 0) ? format_amount($sheetData[$i][7]) : $last_price,
								'price_5'      => (format_amount($sheetData[$i][8]) > 0) ? format_amount($sheetData[$i][8]) : $last_price
							);
							if($this->crud->update('sellprice', $data, $where))
							{
								
								continue;
							}
							else
							{
								$res = 0;
								break;
							}
						}
						else
						{
							$where_pu = [
								'product_code' => $sheetData[$i][0],
								'unit_id'      => $sheetData[$i][2],
								'deleted'	   => 0
							];
							$product_unit = $this->crud->get_where('product_unit', $where_pu)->row_array();							
							$data = array(
								'product_code' => $sheetData[$i][0],
								'product_id'   => $this->product->get_product_id($sheetData[$i][0]),
								'unit_id'      => $sheetData[$i][2],
								'default'	   => $product_unit['default'],
								'price_1' 	   => (format_amount($sheetData[$i][4]) > 0) ? format_amount($sheetData[$i][4]) : $last_price,
								'price_2'      => (format_amount($sheetData[$i][5]) > 0) ? format_amount($sheetData[$i][5]) : $last_price,
								'price_3'      => (format_amount($sheetData[$i][6]) > 0) ? format_amount($sheetData[$i][6]) : $last_price,
								'price_4'      => (format_amount($sheetData[$i][7]) > 0) ? format_amount($sheetData[$i][7]) : $last_price,
								'price_5'      => (format_amount($sheetData[$i][8]) > 0) ? format_amount($sheetData[$i][8]) : $last_price,
							);	
							if($this->crud->insert('sellprice', $data))
							{
								continue;
							}
							else
							{
								$res = 0;
								break;
							}

						}						
					}
					if($res == 1)
					{
						$data_activity = [
							'information' => 'IMPORT HARGA JUAL PRODUK',
							'method'      => 3,
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];
						$this->crud->insert('activity', $data_activity);
						$this->session->set_flashdata('success', 'Sukses, Import Harga Jual Produk berhasil');
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Import Harga Jual Produk gagal');
					}
					redirect(site_url('product'));					
				}				
			}
			else
			{
				$header = array("title" => "Import Harga Jual Produk");				
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('product/sellprice/import_product_sellprice');
				$this->load->view('include/footer');
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}	

	public function export_min_max_stock()
	{
		if($this->system->check_access('product/min_max_stock/export', 'A'))
		{
			if($this->input->method() === 'post')
			{
				ob_end_clean();
				$post = $this->input->post();
				$data_export = array();
				foreach($post['product'] AS $info_p)
				{
					$product_unit_sellprice = $this->db->select('product.code AS code_p, product.name AS name_p, product.minimal, product.maximal,
											 unit.id AS id_u, unit.name AS name_u, 
											 product_unit.value')
											 ->from('product')
											 ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
											 ->join('unit', 'unit.id = product_unit.unit_id')
											 ->where('product.deleted', 0)											 
											 ->where('product_unit.deleted', 0)
											 ->where('unit.deleted', 0)											
											 ->where('product.code', $info_p)
											 ->group_by('product_unit.id')
											 ->order_by('product.code', 'asc')
											 ->order_by('product_unit.value', 'asc')
											 ->get()->result_array();
					foreach($product_unit_sellprice AS $info_pus)
					{
						$data_export[] = array(
							'code_p' => $info_pus['code_p'],
							'name_p' => $info_pus['name_p'],
							'id_u' => $info_pus['id_u'],
							'name_u' => $info_pus['name_u'],
							'minimal' => $info_pus['minimal'],
							'maximal' => $info_pus['maximal']
						);
					}					
				}				
				
				$spreadsheet = new Spreadsheet();				
				// Set document properties
				$spreadsheet->getProperties()->setCreator('TRUST System')
				->setLastModifiedBy('TRUST System')
				->setTitle('Export Stok Minimal dan Maksimal')
				->setSubject('Export Stok Minimal dan Maksimal')
				->setDescription('Export Stok Minimal dan Maksimal');
				// Add some data
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A1', 'KODE PRODUK')
				->setCellValue('B1', 'NAMA')
				->setCellValue('C1', 'ID SATUAN')
				->setCellValue('D1', 'SATUAN')
				->setCellValue('E1', 'STOK MINIMAL')
				->setCellValue('F1', 'STOK MAKSIMAL')
				;
				$i=2; 
				foreach($data_export as $info_export) 
				{
					$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$i, $info_export['code_p'])
					->setCellValue('B'.$i, $info_export['name_p'])
					->setCellValue('C'.$i, $info_export['id_u'])
					->setCellValue('D'.$i, $info_export['name_u'])
					->setCellValue('E'.$i, $info_export['minimal'])
					->setCellValue('F'.$i, $info_export['maximal']);					
					$i++;
				}

				// Rename worksheet
				$filename = 'TRUST System - Stok Minimal dan Maksimal '.date('d-m-Y').'.xlsx';
				$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$spreadsheet->setActiveSheetIndex(0);

				$data_activity = [
					'information' => 'EXPORT STOK MINIMAL DAN MAKSIMAL',
					'method'      => 3,
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);								
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
				header('Cache-Control: max-age=0');
				$writer = new Xlsx($spreadsheet);								
				$writer->save('php://output');
			}
			else
			{
				$header = array("title" => "Export Stok Minimal & Maksimal Produk");
				$footer = array("script" => ['master/product/export_min_max_stock.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('product/stock/export_min_max_stock');        
				$this->load->view('include/footer', $footer);				
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}

	public function import_min_max_stock()
	{
		if($this->system->check_access('product/min_max_stock/import', 'A'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();	
				$filename = 'ImportMinMaxStock-'.$this->session->userdata('code_e');			
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				if(isset($post['form_type']) && $post['form_type'] == 0)
				{
					$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
					{
						$config['upload_path']   = "./assets/upload/product/";
						$config['allowed_types'] = "xlsx";
						$config['max_size']      = "10240";
						$config['remove_space']  = TRUE;
						$config['overwrite'] = true;
						$config['file_name'] = $filename;
						$this->load->library('upload', $config);
						if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
						{ 
							$upload = array(
								'result' => 'success',
								'error' => ''
							);
						}
						else
						{
							$upload = array(
								'result' => 'failed',								
								'error' => $this->upload->display_errors()
							);
						}
						if($upload['result'] == "success")
						{				
							$spreadsheet = $reader->load('assets/upload/product/'.$filename.'.xlsx');
							$sheetData = $spreadsheet->getActiveSheet()->toArray();
							$header = array("title" => "Import Stok Minimal & Maksimal");
							$data = array('sheet' => $sheetData);
							$footer = array("script" => ['master/product/import_min_max_stock.js']);
							$this->load->view('include/header', $header);        
							$this->load->view('include/menubar');        
							$this->load->view('include/topbar');        
							$this->load->view('product/stock/import_min_max_stock', $data);
							$this->load->view('include/footer', $footer);
						}
						else
						{
							$header = array("title" => "Import Stok Minimal & Maksimal");				
							$data = array('error' => $upload['error']);
							$this->load->view('include/header', $header);        
							$this->load->view('include/menubar');        
							$this->load->view('include/topbar');        
							$this->load->view('product/stock/import_min_max_stock', $data);
							$this->load->view('include/footer');							
						}
					}
					else
					{
						$header = array("title" => "Import Stok Minimal & Maksimal");				
						$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe <b>EXCELL</b>! Terima Kasih');
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('product/stock/import_min_max_stock', $data);
						$this->load->view('include/footer');
					}					
				}
				else
				{				
					$spreadsheet = $reader->load('assets/upload/product/'.$filename.'.xlsx');
					$sheetData = $spreadsheet->getActiveSheet()->toArray();		
					for($i = 1;$i < count($sheetData);$i++)
					{
						$res = 1;	
						$where_min_max = [
							'code' => $sheetData[$i][0],
							'deleted' 	   => 0
						];			
						$data_min_max = [
							'minimal' => $sheetData[$i][4],
							'maximal'      => $sheetData[$i][5]
						];		
						if($this->crud->update('product', $data_min_max, $where_min_max))
						{
							continue;
						}
						else
						{
							$res=0;
							break;
						}
					}
					if($res == 1)
					{
						$data_activity = [
							'information' => 'IMPORT STOK MINIMAL & MAKSIMAL',
							'method'      => 3,
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];
						$this->crud->insert('activity', $data_activity);
						$this->session->set_flashdata('success', 'SUKSES! Import Stok Minimal & Maksimal Produk berhasil');
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon maaf, Import Stok Minimal & Maksimal Produk gagal');
					}
					redirect(site_url('product'));					
				}				
			}
			else
			{
				$header = array("title" => "Import Stok Minimal dan Maksimal Produk");				
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('product/stock/import_min_max_stock');
				$this->load->view('include/footer');
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}		

	public function check_unbalance_stock_card_movement_product($product_id)
	{
		$product= $this->crud->get_where('product', ['id' => $product_id])->row_Array();
		$found = []; $double_found = [];
		$stock_card = $this->db->select('invoice, qty')->from('stock_card')->where('product_id', $product['id'])->get()->result_array();
		foreach($stock_card AS $info_stock_card)
		{
			$stock_movement = $this->db->query("SELECT invoice, qty FROM stock_movement WHERE product_id = $product[id] AND invoice='$info_stock_card[invoice]' AND CAST(qty AS DECIMAL) = CAST($info_stock_card[qty] AS DECIMAL)")->row_array();
			if($stock_movement == null)
			{
				$found[] = 'NULL|'.$info_stock_card['invoice'].'|STOCK CARD '.$info_stock_card['qty'].'|STOCK MOVEMENT '.$stock_movement['qty'];
			}
			else
			{
				if($stock_movement['qty'] != $info_stock_card['qty'])
				{
					$found[] = 'DIFF|'.$info_stock_card['invoice'].'|STOCK CARD '.$info_stock_card['qty'].'|STOCK MOVEMENT '.$stock_movement['qty'];
				}
			}			
		}
		$stock_movement = $this->db->select('invoice, qty')->from('stock_movement')->where('product_id', $product['id'])->get()->result_array();
		foreach($stock_movement AS $info_stock_movement)
		{
			$total_stock_card = $this->db->select('COUNT(id) AS total')->from('stock_card')->where('product_id', $product['id'])->where('invoice', $info_stock_movement['invoice'])->get()->row_array();
			if($total_stock_card['total'] != 1)
			{
				$double_found[] = $info_stock_movement['invoice'];
			}
			$stock_card = $this->db->query("SELECT invoice, qty FROM stock_movement WHERE product_id = $product[id] AND invoice='$info_stock_movement[invoice]' AND CAST(qty AS DECIMAL) = CAST($info_stock_movement[qty] AS DECIMAL)")->row_array();
			if($stock_card == null)
			{
				$found[] = 'NULL|'.$info_stock_movement['invoice'].'|STOCK MOVEMENT '.$info_stock_movement['qty'].'|STOCK CARD '.$stock_card['qty'];
			}
			else
			{
				if($stock_card['qty'] != $info_stock_movement['qty'])
				{
					$found[] = 'DIFF|'.$info_stock_movement['invoice'].'|STOCK MOVEMENT '.$info_stock_movement['qty'].'|STOCK CARD '.$stock_card['qty'];
				}
			}			
		}
		$result = [
			'Title' => 'QTY Stock Card Tidak Sama Dengan Stock Movement',
			'id' => $product['id'],
			'code' => $product['code'],
			'name' => $product['name'],
			'found' => $found,
			'double_found' => $double_found
		];
		echo json_encode($result); die;
	}
	
	public function validate_stock_card_movement_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$product = $this->crud->get_where_select('id, code, name', 'product', ['id' => $post['product_id']])->row_Array();
			// STOCK CARD
			$where_stock_card = [
				'product_id' => $product['id']
			];
			$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->result_array();
			foreach($stock_card AS $info_stock_card)
			{
				if(in_array($info_stock_card['type'], [4]))
				{
					$stock_movement = $this->db->query("SELECT * FROM stock_movement WHERE stock_movement.type = $info_stock_card[type] AND stock_movement.transaction_id = $info_stock_card[transaction_id] AND stock_movement.transaction_detail_id = $info_stock_card[transaction_detail_id] AND product_id = $product[id] AND CAST(qty AS DECIMAL) = CAST($info_stock_card[qty] AS DECIMAL) AND method = $info_stock_card[method]")->row_array();
				}
				else
				{
					$stock_movement = $this->db->query("SELECT * FROM stock_movement WHERE stock_movement.type = $info_stock_card[type] AND stock_movement.transaction_id = $info_stock_card[transaction_id] AND product_id = $product[id] AND CAST(qty AS DECIMAL) = CAST($info_stock_card[qty] AS DECIMAL) AND method = $info_stock_card[method]")->row_array();
				}
				if($stock_movement == null)
				{
					$data_stock_movement = [
						'type'            => $info_stock_card['type'],
						'information'     => $info_stock_card['information'],
						'note'			  => $info_stock_card['note'],
						'date'            => $info_stock_card['date'],
						'transaction_id'  => $info_stock_card['transaction_id'],
						'invoice'         => $info_stock_card['invoice'],
						'product_id'      => $product['id'],
						'product_code'    => $product['code'],
						'qty'             => $info_stock_card['qty'],
						'method'          => $info_stock_card['method'],
						'employee_code'   => $info_stock_card['employee_code']
					];
					$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
					if($info_stock_card['type'] == 1)
					{
						$where_purchase_invoice_detail = [
							'invoice'	 => $info_stock_card['invoice'],
							'product_id' => $product['id'],
							'warehouse_id' => $info_stock_card['warehouse_id']
						];
						$purchase_invoice_detail = $this->crud->get_where('purchase_invoice_detail', $where_purchase_invoice_detail)->row_array();
						$subtotal = $purchase_invoice_detail['total'];
						$disc_p = explode(',', $purchase_invoice_detail['disc_product']);
						if($disc_p != null && $disc_p != "" )
						{
							foreach($disc_p AS $info_disc_p)
							{
								$subtotal = $subtotal - ($subtotal*-$info_disc_p/100);
							}
						}
						$price = $subtotal/($purchase_invoice_detail['qty']*$purchase_invoice_detail['unit_value']);
						$this->crud->update('stock_movement', ['price' => $price], ['id' => $stock_movement_id]);
					}
					elseif($info_stock_card['type'] == 7)
					{
						$where_repacking = [
							'code' => $info_stock_card['invoice']
						];
						$repacking = $this->crud->get_where('repacking', $where_repacking)->row_array();
						if($info_stock_card['method'] == 1)
						{
							$where_repacking_detail = [
								'repacking_id' => $repacking['id'],
								'product_code' => $info_stock_card['product_code']
							];
							$repacking_detail = $this->crud->get_where('repacking_detail', $where_repacking_detail)->row_array();
							$repacking_hpp = ($repacking['qty']*$repacking['unit_value']*$repacking['hpp']) / ($repacking_detail['qty']*$repacking_detail['unit_value']);
							$this->crud->update('stock_movement', ['price' => $repacking_hpp], ['id' => $stock_movement_id]);
						}	
						elseif($info_stock_card['method'] == 2)
						{
							$this->crud->update('stock_movement', ['hpp' => $repacking['hpp']], ['id' => $stock_movement_id]);
						}
					}
				}
				else
				{
					$this->crud->update('stock_movement', ['date' => $info_stock_card['date']], ['id' => $stock_movement['id']]);
					if($stock_movement['qty'] != $info_stock_card['qty'])
					{
						$data_stock_movement = [						
							'qty' => $info_stock_card['qty']
						];
						$this->crud->update('stock_movement', $data_stock_movement, ['id' => $stock_movement['id']]);
					}
					if($info_stock_card['type'] == 1)
					{
						$where_purchase_invoice_detail = [
							'invoice'	 	=> $info_stock_card['invoice'],
							'product_id' 	=> $product['id'],
							'warehouse_id' 	=> $info_stock_card['warehouse_id']
						];
						$purchase_invoice_detail = $this->crud->get_where('purchase_invoice_detail', $where_purchase_invoice_detail)->row_array();
						$price = $purchase_invoice_detail['total']/($purchase_invoice_detail['qty']*$purchase_invoice_detail['unit_value']);
						$this->crud->update('stock_movement', ['price' => $price], ['id' => $stock_movement['id']]);
					}
					elseif($info_stock_card['type'] == 7)
					{
						$where_repacking = [
							'code' => $info_stock_card['invoice']
						];
						$repacking = $this->crud->get_where('repacking', $where_repacking)->row_array();
						if($info_stock_card['method'] == 1)
						{
							$where_repacking_detail = [
								'repacking_id' => $repacking['id'],
								'product_code' => $info_stock_card['product_code']
							];
							$repacking_detail = $this->crud->get_where('repacking_detail', $where_repacking_detail)->row_array();
							$repacking_hpp = ($repacking['qty']*$repacking['unit_value']*$repacking['hpp']) / ($repacking_detail['qty']*$repacking_detail['unit_value']);
							$this->crud->update('stock_movement', ['price' => $repacking_hpp], ['id' => $stock_movement['id']]);
						}	
						elseif($info_stock_card['method'] == 2)
						{
							$this->crud->update('stock_movement', ['hpp' => $repacking['hpp']], ['id' => $stock_movement['id']]);
						}
					}
				}			
			}
			// STOCK MOVEMENT
			$where_stock_movement = [
				'product_id' => $product['id']
			];
			$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->result_array();
			foreach($stock_movement AS $info_stock_movement)
			{
				$stock_card = $this->db->query("SELECT id, invoice, qty FROM stock_movement WHERE product_id = $product[id] AND invoice='$info_stock_movement[invoice]' AND CAST(qty AS DECIMAL) = CAST($info_stock_movement[qty] AS DECIMAL) AND method = $info_stock_movement[method]")->row_array();
				if($stock_card == null)
				{
					$this->crud->delete('stock_movement', ['id' => $info_stock_movement['id']]);
				}
			}	
			$this->session->set_flashdata('success', 'VALIDASI KARTU DAN PERGERAKAN STOK SELESAI');
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

	public function sort_inventory_product()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();				
			$where_product = [
				'id' 	  => $post['product_id'],
				'deleted' => 0
			];
			$products = $this->crud->get_where('product', $where_product)->result_array();
			$warehouses = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
			foreach($products AS $product)
			{
				// STOCK_MOVEMENT
				$stock = 0;
				$where_stock_movement = [
					'product_id'   => $product['id'],
					'product_code' => $product['code']
				];
				$stock_movements = $this->db->select('*')->from('stock_movement')->where($where_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();            
				foreach($stock_movements AS $stock_movement)
				{
					if($stock_movement['stock'] < 0)
					{
						$new_stock_movement = [
							'type' => $stock_movement['type'],
							'information' => $stock_movement['information'],
							'note' => $stock_movement['note'],
							'date' => $stock_movement['date'],
							'transaction_id' => $stock_movement['transaction_id'],
							'invoice' => $stock_movement['invoice'],
							'product_id' => $stock_movement['product_id'],
							'product_code' => $stock_movement['product_code'],
							'qty' => $stock_movement['qty'],
							'method' => $stock_movement['method'],
							'price' => $stock_movement['price'],
							'hpp' => $stock_movement['price'],
							'user_id' => $stock_movement['user_id'],
							'employee_code' => $stock_movement['employee_code'],
						];
						$this->crud->insert('stock_movement', $new_stock_movement);
						$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);
					}
					continue;
				}
			}
			$this->session->set_flashdata('success', 'MENGURUTKAN PERSEDIAAN PRODUK SELESAI');
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

	public function recalculate_inventory_product()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();				
			$where_product = [
				'id' 	  => $post['product_id'],
				'deleted' => 0
			];
			$products = $this->crud->get_where('product', $where_product)->result_array();
			$warehouses = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
			foreach($products AS $product)
			{
				// STOCK_CARD
				foreach($warehouses AS $warehouse)
				{
					$stock = 0;
					$where_stock_card = [
						'product_id' => $product['id'], 
						'product_code' => $product['code'], 
						'warehouse_id' => $warehouse['id']
					];
					$stock_cards = $this->db->select('*')->from('stock_card')->where($where_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
					foreach($stock_cards AS $stock_card)
					{
						$stock = ($stock_card['method'] == 1) ? floatval($stock+$stock_card['qty']) : floatval($stock-$stock_card['qty']);
						$this->crud->update('stock_card', ['stock' => floatval($stock)], ['id' => $stock_card['id']]);
					}
					$this->crud->update('stock', ['qty' => $stock], $where_stock_card);
				} 
				// STOCK_MOVEMENT
				$stock = 0;
				$where_stock_movement = [
					'product_id'   => $product['id'],
					'product_code' => $product['code']
				];
				$stock_movements = $this->db->select('*')->from('stock_movement')->where($where_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();            
				foreach($stock_movements AS $stock_movement)
				{
					$stock = ($stock_movement['method'] == 1) ? $stock+$stock_movement['qty'] : $stock-$stock_movement['qty'];
					$this->crud->update('stock_movement', ['stock' => $stock], ['id' => $stock_movement['id']]);
				}
			}
			$this->session->set_flashdata('success', 'HITUNG ULANG PERSEDIAAN PRODUK SELESAI');
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

	public function recalculate_hpp_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$product = $this->crud->get_where('product', ['id' => $post['product_id']])->row_array();
			$where_stock_movement = [
				'product_id'   => $product['id'],
				'product_code' => $product['code']
			];
			$stock = 0; $last_buyprice=0; $last_hpp = 0;
			$stock_movements = $this->db->select('*')->from('stock_movement')->where($where_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
			foreach($stock_movements AS $stock_movement)
			{
				if($stock_movement['type'] == 1)
				{
					$old_inventory_value = $stock*$last_hpp;
					$new_inventory_value = $stock_movement['qty']*$stock_movement['price'];
					$hpp= [
						'price' => $stock_movement['price'],
						'hpp' => ($old_inventory_value+$new_inventory_value)/($stock+$stock_movement['qty'])
					];
					$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement['id']]);
					$last_buyprice = $hpp['price'];
					$last_hpp = $hpp['hpp'];					
				}
				elseif($stock_movement['type'] == 4)
				{
					$where_sales_invoice_detail=[
						'sales_invoice_id' => $stock_movement['transaction_id'],
						'product_id'	   => $stock_movement['product_id']
					];
					$this->crud->update('sales_invoice_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
					$hpp= [					
						'hpp' => $last_hpp
					];
					$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement['id']]);

					// RECALCULATE TOTAL HPP IN SALES INVOICE
					$sales_invoice_detail = $this->crud->get_where('sales_invoice_detail', ['sales_invoice_id' => $stock_movement['transaction_id']])->result_array();
					$total_hpp_sales_invoice=0;
					foreach($sales_invoice_detail AS $info_sales_invoice_detail)
					{						
						$total_hpp_sales_invoice=$total_hpp_sales_invoice+($info_sales_invoice_detail['qty']*$info_sales_invoice_detail['unit_value']*$info_sales_invoice_detail['hpp']);
					}
					$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp_sales_invoice], ['id' => $stock_movement['transaction_id']]);
				}
				elseif($stock_movement['type'] == 7 && $stock_movement['method'] == 1)
				{
					$old_inventory_value = $stock*$last_hpp;
					$new_inventory_value = $stock_movement['qty']*$stock_movement['price'];
					$hpp= [
						'price' => $stock_movement['price'],
						'hpp' => ($old_inventory_value+$new_inventory_value)/($stock+$stock_movement['qty'])
					];
					$this->crud->update('stock_movement', $hpp, ['id' => $stock_movement['id']]);
					$last_buyprice = $hpp['price'];
					$last_hpp = $hpp['hpp'];
				}
				$stock = ($stock_movement['method'] == 1) ? $stock+$stock_movement['qty'] : $stock-$stock_movement['qty'];
				$this->crud->update('stock_movement', ['stock' => $stock], ['id' => $stock_movement['id']]);
			}
			$this->crud->update('product', ['buyprice' => $last_buyprice, 'hpp' => $last_hpp], ['id' => $product['id']]);
			$this->session->set_flashdata('success', 'PERHITUNGAN ULANG HPP PRODUK SELESAI');
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
}