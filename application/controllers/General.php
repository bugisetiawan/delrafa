<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends System_Controller 
{	
	public function __construct()
	{
		parent::__construct();		
		$this->load->model('General_model', 'general');
	}

	public function low_selling_price()
	{		
		$data_activity = [
			'information' => 'MELIHAT DAFTAR HARGA JUAL <= HARGA BELI',
			'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
			'code_e'      => $this->session->userdata('code_e'),
			'name_e'      => $this->session->userdata('name_e'),
			'user_id'     => $this->session->userdata('id_u')
		];						
		$this->crud->insert('activity', $data_activity);
		$header = array("title" => "Daftar Harga Jual Produk Kurang Dari/Sama Dengan Harga Beli");
		$data  = [
			'low_selling_price' => $this->general->low_selling_price()
		];
		$footer = array("script" => ['general/low_selling_price.js']);
		$this->load->view('include/header', $header);
		$this->load->view('include/menubar');
		$this->load->view('include/topbar');
		$this->load->view('general/low_selling_price', $data);
		$this->load->view('include/footer', $footer);			
	}
	
	public function out_stock_product()
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$post = $this->input->post();
			$filter = [
				'search_product'	 => $post['search_product'],
				'department_code' 	 => $post['department_code'],
				'subdepartment_code' => $post['subdepartment_code']
			];
			$product_id = $this->general->get_out_stock_product_id($filter);
			$this->datatables->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, sum(stock.qty) AS total_stock, unit.name AS name_u, department.name AS name_d, subdepartment.name AS name_sd');
			$this->datatables->from('product');
			$this->datatables->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1');
			$this->datatables->join('unit', 'unit.id = product_unit.unit_id');
			$this->datatables->join('stock', 'stock.product_code = product.code');			
			$this->datatables->join('department', 'department.code = product.department_code');
			$this->datatables->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code');
			$this->datatables->where_in('product.id', $product_id);
			$this->datatables->group_by('product.id'); 
			$this->datatables->add_column('code_p', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
			', 'encrypt_custom(code_p), code_p');       
			echo $this->datatables->generate(); 
		}
		else
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR STOK PRODUK SEGERA/SUDAH KOSONG',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);
			$header = array("title" => "Daftar Stok Produk Segera dan Sudah Kosong");
			$footer = array("script" => ['general/out_stock_product.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/out_stock_product');
			$this->load->view('include/footer', $footer);			
		}
	}

	public function more_stock_product()
	{
		if($this->input->is_ajax_request())
		{
			$product_id = $this->general->get_more_stock_product_id();			
			header('Content-Type: application/json');
			$this->datatables->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, sum(stock.qty) AS total_stock, unit.name AS name_u, department.name AS name_d, subdepartment.name AS name_sd');
			$this->datatables->from('product');
			$this->datatables->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1');
			$this->datatables->join('unit', 'unit.id = product_unit.unit_id');
			$this->datatables->join('stock', 'stock.product_code = product.code', 'left');			
			$this->datatables->join('department', 'department.code = product.department_code');
			$this->datatables->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code');
			$this->datatables->where_in('product.id', $product_id);
			$this->datatables->group_by('product.id'); 
			$this->datatables->add_column('code_p', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
			', 'encrypt_custom(code_p), code_p');       
			echo $this->datatables->generate();
		}
		else
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR STOK PRODUK BERLEBIH',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);
			$header = array("title" => "Daftar Stok Produk Berlebih");
			$footer = array("script" => ['general/more_stock_product.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/more_stock_product');
			$this->load->view('include/footer', $footer);
		}			 
	} 

	public function due_purchase_invoice()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
            header('Content-Type: application/json');
            $this->datatables->select('purchase_invoice.id AS id, purchase_invoice.date, purchase_invoice.code AS code, purchase_invoice.invoice, purchase_invoice.payment, purchase_invoice.due_date, DATEDIFF(purchase_invoice.due_date, CURRENT_DATE()) AS remaining_time, purchase_invoice.grandtotal, purchase_invoice.account_payable, supplier.name AS supplier, purchase_invoice.ppn, purchase_invoice.payment_status');
            $this->datatables->from('purchase_invoice');
            $this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
            $this->datatables->where('purchase_invoice.deleted', 0);
            $this->datatables->where('purchase_invoice.payment_status !=', 1);
			$this->datatables->where('purchase_invoice.account_payable !=', 0);
			$this->datatables->where('purchase_invoice.due_date <=', date('Y-m-d'));
            $this->datatables->group_by('purchase_invoice.id');
            $this->datatables->add_column('invoice', 
            '
                <a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/$1').'"><b>$2</b></a>
            ', 'encrypt_custom(id), invoice');            
            echo $this->datatables->generate();
		}
		else
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR HUTANG JATUH TEMPO',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Daftar Hutang Jatuh Tempo");
			$footer = array("script" => ['general/due_purchase_invoice.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/due_purchase_invoice');
			$this->load->view('include/footer', $footer);
		}			 
	}

	public function due_sales_invoice()
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$post = $this->input->post();
            $this->datatables->select('sales_invoice.id AS id, sales_invoice.date, sales_invoice.invoice, sales_invoice.payment, sales_invoice.due_date, DATEDIFF(sales_invoice.due_date, CURRENT_DATE()) AS remaining_time, sales_invoice.grandtotal, sales_invoice.account_payable, customer.name AS name_c, employee.name AS name_s, sales_invoice.payment_status');
            $this->datatables->from('sales_invoice');
            $this->datatables->join('employee', 'employee.code = sales_invoice.sales_code');
            $this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
            $this->datatables->where('sales_invoice.deleted', 0);
			$this->datatables->where('sales_invoice.payment', 2);
			$this->datatables->where('sales_invoice.do_status', 1);
            $this->datatables->where('sales_invoice.payment_status !=', 1);
			$this->datatables->where('sales_invoice.due_date <=', date('Y-m-d'));
			if($post['customer_code'] != "")
			{
				$this->datatables->where('sales_invoice.customer_code', $post['customer_code']);
			}
			if($post['sales_code'] != "")
			{
				$this->datatables->where('sales_invoice.sales_code', $post['sales_code']);
			}
            $this->datatables->group_by('sales_invoice.id');		
            $this->datatables->add_column('invoice', 
            '
                <a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>
            ', 'encrypt_custom(id),invoice');
            echo $this->datatables->generate();
		}
		else
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR PIUTANG JATUH TEMPO',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Daftar Piutang Jatuh Tempo");
			$footer = array("script" => ['general/due_sales_invoice.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/due_sales_invoice');
			$this->load->view('include/footer', $footer);
		}		
	}		

	public function detail_sellprice()
	{
		if($this->input->is_ajax_request())
		{
			if($this->system->check_access('product', 'read'))
			{
				$post = $this->input->post();
				$sellprice = $this->general->detail_sellprice(decrypt_custom($post['product_code']));
				$html = "";
				$html .= '<h6 class="text-dark font-weight-bold">'.$sellprice[0]['name_p'].'</h6><table class="table table-bordered"><thead><tr><th>SATUAN</th>';
				for($i=1; $i<=4; $i++)
				{
					if($this->system->check_access('view_sellprice_'.$i, 'read'))
					{
						$html.='<th class="text-center">HRG. '.$i.'</th>';
					}
				}
				$html.='</tr></thead><tbody>';				
				foreach($sellprice AS $info_sellprice)
				{
					$html.='<tr><td>'.$info_sellprice['name_u'].'</td>';
					for($j=1; $j<=4; $j++)
					{
						if($this->system->check_access('view_sellprice_'.$j, 'read'))
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
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
				redirect(site_url('dashboard'));
			}			
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

    public function product_list()
    {
		if($this->input->is_ajax_request())
		{
			$post         = $this->input->post();
			$search       = (!isset($post['search'])) ? null : $post['search'];
			$department_code    = (!isset($post['department_code']))    ? null : $post['department_code'];
			$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
			$draw         = (!isset($post['draw'])) 	? 0 : $post['draw'];
			$iLength      = (!isset($post['length'])) 	? null : $post['length'];
			$iStart   	  = (!isset($post['start'])) 	? null : $post['start'];
			$iOrder   	  = (!isset($post['order'])) 	? null : $post['order'];
			if($search != "" || $department_code !="")
			{
				$total	 = $this->general->datatable($search, $department_code, $subdepartment_code)->num_rows();
				$product = $this->general->datatable($search, $department_code, $subdepartment_code, $iLength, $iStart, $iOrder)->result_array();
				$data    = array();
				foreach($product AS $info_product)
				{
					$total_primary_stock=0; $total_secondary_stock=0; $total_stock = 0;
					if($this->system->check_access('view_global_stock', 'read'))
					{
						$primary_stocks = $this->general->get_stock_primary($info_product['name']);
						if($primary_stocks->num_rows() > 0)
						{
							foreach($primary_stocks->result_array() AS $info_stock)
							{
								$total_primary_stock = $total_primary_stock+$info_stock['qty'];
								$total_stock = $total_stock + $info_stock['qty'];
							}
						}

						$secondary_stocks = $this->general->get_stock_secondary($info_product['name']);
						if($secondary_stocks->num_rows() > 0)
						{
							foreach($secondary_stocks->result_array() AS $info_stock)
							{
								$total_secondary_stock = $total_secondary_stock+$info_stock['qty'];
								$total_stock = $total_stock + $info_stock['qty'];
							}
						}
					}
					else
					{														
						$stocks = $this->general->get_stock_primary($info_product['name']);
						if($stocks->num_rows() > 0)
						{
							foreach($stocks->result_array() AS $info_stock)
							{
								$total_primary_stock = $total_primary_stock+$info_stock['qty'];
								$total_stock = $total_stock + $info_stock['qty'];
							}
						}
					}        									
					$link_sellprice = '<a class="text-primary kt-link sellprice" href="javascript:void(0);" data-id="'.encrypt_custom($info_product['code']).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk melihat detail harga jual">'.number_format($info_product['sellprice_1'], 0, '.', ',').'</a>';
					$data[] = array(
						'id' 	  	 => $info_product['id'],
						'code' 		 => $info_product['code'],
						'name' 		 => $info_product['name'],
						'primary_stock' => $total_primary_stock,
						'secondary_stock' => $total_secondary_stock,
						'stock' 	 => $total_stock,					
						'unit' 		 => $info_product['unit'],
						'sellprice_1'  => $link_sellprice,
						'sellprice_2'  => $info_product['sellprice_2'],
						'sellprice_3'  => $info_product['sellprice_3'],
						'sellprice_4'  => $info_product['sellprice_4']
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
			echo json_encode($output);
		}
		else
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR UMUM PRODUK',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Daftar Produk");
			$footer = array("script" => ['general/product_list.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/product_list');
			$this->load->view('include/footer', $footer);
		}        
	}		

	public function product_sales_history()
    {     
        if($this->input->is_ajax_request())
		{
			$post       = $this->input->post();
			$department_code    = (!isset($post['department_code']))    ?   null : $post['department_code'];
			$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
			$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
			$customer_code 		= (!isset($post['customer_code'])) ?   null : $post['customer_code'];
			if($customer_code != "" && $customer_code != null)
			{
				$this->datatables->select('product.id AS id_p, product.barcode AS barcode_p, product.code AS code_p, product.name AS name_p,
								MAX(sid.id) AS id_sid, sid.qty AS qty, unit.code AS code_u, sid.price, sid.disc_product, sid.total');
				$this->datatables->from('product');
				$this->datatables->join('sales_invoice_detail AS sid', 'sid.product_id = product.id');
				$this->datatables->join('sales_invoice', 'sales_invoice.id = sid.sales_invoice_id');
				$this->datatables->join('unit', 'unit.id = sid.unit_id');
                $this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
				$this->datatables->where('product.deleted', 0);
				$this->datatables->where('sid.deleted', 0);
				$this->datatables->where('sales_invoice.deleted', 0);
				$this->datatables->where('sales_invoice.do_status', 1);
				$this->datatables->where('sales_invoice.customer_code', $customer_code);
				$this->datatables->group_by('product.id');
				$this->datatables->order_by('sid.id', 'DESC');
                $this->datatables->add_column('action',
				'
					<label class="col-form-label"><a href="javascript:void(0);" class="text-warning text-center kt-font-bold sellprice_history" data-code="$1"><i class="fa fa-clock"></i></a></label>
				', 'code_p');
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
				'information' => 'MELIHAT DAFTAR RIWAYAT PENJUALAN PRODUK',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Riwayat Penjualan Produk");
			$footer = array("script" => ['general/product_sales_history.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/product_sales_history');
			$this->load->view('include/footer', $footer);
		}           
	}
	
	public function cancel_do()
    {     
        if($this->input->is_ajax_request())
		{
			$post       = $this->input->post();
			
		}
		else
		{
			$data_activity = [
				'information' => 'PEMBATALAN DO',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);
			$header = array("title" => "Pembatalan DO");
			$footer = array("script" => ['general/cancel_do.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('general/cancel_do');
			$this->load->view('include/footer', $footer);
		}           
    }
}