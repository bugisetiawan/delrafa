<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('Sales_model', 'sales');
		$this->load->model('master/Product_model', 'product');
		$this->load->model('finance/Payment_model', 'payment');
		$this->load->model('Purchase_model','purchase');
	}

	private function format_date($date)
    {
        $explode = explode('-',$date);
        $array = array($explode[2],$explode[1],$explode[0]);
        $implode = implode('-',$array);
        return $implode;
	}		
	
	public function get_employee()
    {
		if($this->input->is_ajax_request())
		{
			$where = array(
				'id >' => 3,
				'is_user' => 0,
				'deleted' => 0
			);
			$data = $this->crud->get_where('employee', $where)->result();
			if($data)
			{
				$response   =   [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data,
				];
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => '',
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}

	public function get_customer()
    {
		if($this->input->is_ajax_request())
		{
			$data       = $this->crud->get_where('customer', ['deleted' => '0'])->result();
			if($data)
			{
				$response   =   [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data,
				];
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => '',
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}
	
	public function check_customer()
	{
		if($this->input->is_ajax_request())
		{
			$where = array(
				'code' => $this->input->post('customer_code'),
				'deleted' => 0,
			);
			$response = $this->crud->get_where('customer', $where)->row_array();
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function check_customer_receivable()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			if(in_array($post['customer_code'], ['CUST-00001', 'CUST-00002', 'CUST-00003']))
			{
				$response = 200;
			}
			else 
			{
				$where = array(
					'code' 	  => $post['customer_code'],
					'deleted' => 0,
				);
				$customer = $this->crud->get_where('customer', $where)->row_array();			
				$where_sales_invoice = [
					'customer_code' 	=> $post['customer_code'],				
					'payment_status !=' => 1,
					'due_date <=' 		=> date('Y-m-d'),
					'do_status' 		=> 1
				];
				$sales_invoice = $this->crud->get_where('sales_invoice', $where_sales_invoice)->num_rows();
				// 	CHECK DUE SALES INVOICE
				if($sales_invoice == 0)
				{				
					$where_sales_invoice = [
						'customer_code' 	=> $post['customer_code'],				
						'payment_status !=' => 1,
						'do_status' 		=> 1
					];
					$sales_invoice = $this->crud->get_where('sales_invoice', $where_sales_invoice)->result_array();
					$total_account_payable = format_amount($post['account_payable']);
					foreach($sales_invoice AS $info)
					{
						$total_account_payable = $total_account_payable + $info['account_payable'];
					}
					if($total_account_payable <= $customer['credit'])
					{
						$response = 200;
					}
					else
					{
						$response = 401;
					}
				}
				else
				{
					$response = 401;
				}				
			}						
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function get_payment_due()
	{
		if($this->input->is_ajax_request())
		{
			$customer = $this->crud->get_where('customer', ['code' => $this->input->post('customer_code')])->row_array();
			$response = array(
				'dueday' => $customer['dueday']
			);
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	} 

    public function get_product()
	{
		if($this->input->is_ajax_request())
		{
			$search = urldecode($this->uri->segment(4));	
			$ppn    = urldecode($this->uri->segment(5));
			$data   = $this->sales->get_product($search, $ppn);
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
			$unit       = $this->sales->get_unit($where)->result_array();
			$option		= "";		
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

	public function get_hpp_product()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$product = $this->db->select('hpp')->from('product')->where('code', $post['product_code'])->get()->row_array();
			$product_unit = $this->db->select('value')->from('product_unit')->where('product_code', $post['product_code'])->where('unit_id', $post['unit_id'])->get()->row_array();
			$response = [
				'hpp' => $product['hpp']*$product_unit['value']
			];
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
			$customer = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
			$price_class = ($customer != null ) ? $customer['price_class'] : 1;
			$where_sellprice = [
				'product_code'	=> $post['product_code'],
				'unit_id'		=> $post['unit_id']
			];
			$sellprice = $this->crud->get_where('sellprice', $where_sellprice )->row_array();
			$option = '';
			for($i=1; $i<=5 ; $i++)
			{
				if($this->system->check_access('view_sellprice_'.$i, 'A'))
				{
					if($price_class == $i)
					{
						$option .= "<option value='".$sellprice['price_'.$i]."' class='".'H'.$i."' selected>".'H'.$i." | ".number_format($sellprice['price_'.$i], 0, '.', ',')."</option>";
					}
					else
					{
						$option .= "<option value='".$sellprice['price_'.$i]."' class='".'H'.$i."'>".'H'.$i." | ".number_format($sellprice['price_'.$i], 0, '.', ',')."</option>";
					}	
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

	public function get_sellprice_history()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$product_code  = $post['product_code'];
			$customer_code = ($post['customer_code'] != NULL) ? $post['customer_code'] : 'CUST-00006';
			$data = $this->db->select('si.date, si.invoice, sid.qty, unit.code AS code_u, unit.name AS name_u, sid.price, sid.disc_product, sid.total')
							 ->from('sales_invoice_detail AS sid')
							 ->join('sales_invoice AS si', 'si.id = sid.sales_invoice_id')
							 ->join('unit', 'unit.id = sid.unit_id')
							 ->where('si.deleted', 0)->where('si.do_status', 1)
							 ->where('sid.product_code', $product_code)
							 ->where('si.customer_code', $customer_code)
							 ->group_by('sid.id')
							 ->order_by('sid.id', 'DESC')
							 ->limit(10)
							 ->get()->result_array();
			$html ="";
			$html .= '<table class="table table-sm table-bordered">
						<thead>
							<tr class="text-center">
								<th>TANGGAL</th>
								<th>NO. TRANSAKSI</th>
								<th>QTY</th>
								<th>SATUAN</th>
								<th>HARGA</th>
								<th>DISKON(%)</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>';
			foreach($data AS $info)
			{	
				$html.='<tr>
							<td class="text-center">'.date('d-m-Y', strtotime($info['date'])).'</td>
							<td class="text-center">'.$info['invoice'].'</td>
							<td class="text-right">'.$info['qty'].'</td>
							<td class="text-left">'.$info['code_u'].'</td>
							<td class="text-right">'.number_format($info['price'], 0, '.', ',').'</td>
							<td class="text-right">'.$info['disc_product'].'</td>
							<td class="text-right">'.number_format($info['total'], 0, '.', ',').'</td>
						</tr>';
			}
			$html .= '</tbody></table>';
			echo json_encode($html);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

    public function get_warehouse()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$warehouse = $this->sales->get_warehouse($post['product_code'], $post['unit_id']);
			$option		= '';		
			foreach($warehouse as $data)
			{
				if($data['stock'] >= 0)
				{
					if($data['default']==1)
					{
						$option .= "<option value='".$data['id_w']."' selected>".$data['code_w']." | ".number_format($data['stock'], 2, '.', ',')."</option>";
					}
					else
					{
						$option .= "<option value='".$data['id_w']."'>".$data['code_w']." | ".number_format($data['stock'], 2, '.', ',')."</option>";
					}
				}
				else
				{
					continue;
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

	// SALES ORDER
	public function datatable_sales_order()
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('sales_order.id AS id, sales_order.date, sales_order.invoice, sales_order.taking_method AS taking, sales_order.grandtotal, customer.name AS name_c, employee.name AS name_s, sales_order.sales_order_status AS status_so');
			$this->datatables->from('sales_order');
			$this->datatables->join('employee', 'employee.code = sales_order.sales_code');
			$this->datatables->join('customer', 'customer.code = sales_order.customer_code');
			$this->datatables->join('sales_order_detail', 'sales_order_detail.sales_order_id = sales_order.id');
			$this->datatables->where('sales_order.deleted', 0);
			$this->datatables->where('DATE(sales_order.created)', date('Y-m-d'));						
			$this->datatables->group_by('sales_order.id');		
			$this->datatables->add_column('invoice', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/order/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id), invoice');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}
	
    public function sales_order()
    {
		if($this->system->check_access('sales/order','read'))
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR PEMESANAN PENJUALAN',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Daftar Pemesanan Penjualan"); 
			$footer = array("script" => ['transaction/sales/order/sales_order.js']);
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('sales/order/sales_order');        
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}
				
	public function add_sales_order()
    {
		if($this->system->check_access('sales/order', 'create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();		
				$this->form_validation->set_rules('date', 'Tanggal Penjualan', 'trim|required|xss_clean');
				$this->form_validation->set_rules('sales_code', 'Sales', 'trim|required|xss_clean');
				$this->form_validation->set_rules('customer_code', 'Konsumen', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
				$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|xss_clean');
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Pemesanan Penjualan Baru");
					$footer = array("script" => ['transaction/sales/order/crud_sales_order.js']);
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('sales/order/add_sales_order');        
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$total_price  		= format_amount($post['subtotal']);
					$discount_rp 		= format_amount($post['discount_rp']);			
					$grandtotal  		= format_amount($post['grandtotal']);
					$invoice 			= $this->sales->sales_order_code();
					$data_sales=array(
						'date'				=> format_date($post['date']),				
						'employee_code'		=> $this->session->userdata('code_e'),
						'sales_code'		=> $post['sales_code'],
						'customer_code'		=> $post['customer_code'],
						'taking_method'		=> $post['taking'],
						'delivery_address'	=> $post['delivery_address'],
						'invoice'			=> $invoice,				
						'total_product'		=> $post['total_product'],
						'total_qty'			=> $post['total_qty'],
						'total_price'		=> $total_price,
						'discount_p'		=> $post['discount_p'],
						'discount_rp'		=> $discount_rp,
						'grandtotal'		=> $grandtotal,
						'sales_order_status'=> "1", // 1:SALES ORDER, 2: SALES INVOICE
					);		
					$sales_order_id = $this->crud->insert_id('sales_order', $data_sales);					
					if($sales_order_id != null)
					{
						foreach($post['product'] AS $info)
						{
							$res = 0;
							$product_id = $this->crud->get_product_id($info['product_code']);
							$data_sales_detail=array(
								'sales_order_id'=> $sales_order_id,
								'invoice'		=> $invoice,
								'product_id'	=> $product_id,
								'product_code'	=> $info['product_code'],
								'qty'			=> format_amount($info['qty']),
								'unit_id'		=> $info['unit_id'],
								'price'			=> format_amount($info['price']),
								'warehouse_id'	=> $info['warehouse_id'],
								'total'			=> format_amount($info['total'])
							);

							if($this->crud->insert('sales_order_detail', $data_sales_detail))
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
							$data_activity = [
								'information' => 'MEMBUAT PEMESANAN PENJUALAN BARU',
								'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];													
							$this->crud->insert('activity', $data_activity);
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
				}

				if($res == 1)
				{
					$this->session->set_flashdata('success', 'Transaksi Pemesanan Penjualan Berhasil');					
				}
				else
				{
					$this->session->set_flashdata('error', 'Transaksi Pemesanan Penjualan Gagal');					
				}
				redirect(site_url('sales'));
			}
			else
			{
				$header = array("title" => "Pemesanan Penjualan Baru");
				$footer = array("script" => ['transaction/sales/order/crud_sales_order.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/order/add_sales_order');        
				$this->load->view('include/footer', $footer);		
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales'));
		}		
	}
	
	public function datatable_detail_sales_order($sales_order_id)
	{		
		if($this->input->is_ajax_request())
		{			
			header('Content-Type: application/json');
			$this->datatables->select('sales_order_detail.id AS id, product.code AS code_p, product.name AS name_p, sales_order_detail.qty AS qty, 
							unit.name AS name_u, sales_order_detail.price AS buyprice, warehouse.name AS name_w, sales_order_detail.total AS subtotal');
			$this->datatables->from('sales_order_detail');
			$this->datatables->join('product', 'product.code = sales_order_detail.product_code');
			$this->datatables->join('unit', 'unit.id = sales_order_detail.unit_id');
			$this->datatables->join('warehouse', 'warehouse.id = sales_order_detail.warehouse_id');
			$this->datatables->where('sales_order_detail.deleted', 0);
			$this->datatables->where('sales_order_detail.sales_order_id', $sales_order_id);						
			$this->datatables->group_by('sales_order_detail.id');
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

	public function detail_sales_order($sales_order_id)
    {
		if($this->system->check_access('sales/order','detail'))
		{
			$sales_order = $this->sales->get_detail_sales_order(decrypt_custom($sales_order_id));
			if($sales_order != null)
			{
				$data_activity = [
					'information' => 'MELIHAT DETAIL PEMESANAN PENJUALAN',
					'method'      => 2, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);

				$header = array("title" => "Detail Pemesanan Penjualan");		
				$data = array(
					'sales_order' => $sales_order
				);	
				$footer = array("script" => ['transaction/sales/order/detail_sales_order.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/order/detail_sales_order', $data);        
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
			redirect(site_url('sales/order'));
		}		
	}

	public function update_sales_order($sales_order_id)
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$sales_order        = $this->sales->get_detail_sales_order(decrypt_custom($post['sales_order_id']));
			$sales_order_detail = $this->sales->get_detail_sales_order_detail($sales_order['id']);
			$this->form_validation->set_rules('date', 'Tanggal Penjualan', 'trim|required|xss_clean');
			$this->form_validation->set_rules('sales_code', 'Sales', 'trim|required|xss_clean');
			$this->form_validation->set_rules('customer_code', 'Konsumen', 'trim|required|xss_clean');
			$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
			$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
			$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|xss_clean');
			if($this->form_validation->run() == FALSE)
			{
				$header = array("title" => "Perbarui Pemesanan Penjualan");		
				$data = array(
					'sales_order' => $sales_order,
					'sales_order_detail' => $sales_order_detail
				);	
				$footer = array("script" => ['transaction/sales/order/update_sales_order.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/order/update_sales_order', $data);        
				$this->load->view('include/footer', $footer);
			}
			else
			{
				$total_price  		= format_amount($post['subtotal']);
				$discount_rp 		= format_amount($post['discount_rp']);
				$grandtotal  		= format_amount($post['grandtotal']);
				$data_sales_order=array(
					'date'				=> format_date($post['date']),										
					'sales_code'		=> $post['sales_code'],
					'customer_code'		=> $post['customer_code'],
					'taking_method'		=> $post['taking'],
					'delivery_address'	=> $post['delivery_address'],										
					'total_product'		=> $post['total_product'],
					'total_qty'			=> $post['total_qty'],
					'total_price'		=> $total_price,
					'discount_p'		=> $post['discount_p'],
					'discount_rp'		=> $discount_rp,
					'grandtotal'		=> $grandtotal						
				);	
				if($this->crud->update('sales_order', $data_sales_order, ['id' => $sales_order['id']]))
				{
					// LIST OLD PRODUCT, AND NEW PRODUCT
					$old_sales_order_detail_id = []; $new_sales_order_detail_id = [];
					foreach($sales_order_detail AS $info_old_product)
					{
						$old_sales_order_detail_id[] = $info_old_product['id'];						
					}
					foreach($post['product'] AS $info_new_product)
					{
						$new_sales_order_detail_id[] = isset($info_new_product['sales_order_detail_id']) ? $info_new_product['sales_order_detail_id'] : null;
					}

					// CHECK AND DELETE OLD PRODUCT WHERE NOT LISTED IN NEW LIST PRODUCT						
					foreach($sales_order_detail AS $info_old_product)
					{
						if(in_array($info_old_product['id'], $new_sales_order_detail_id))
						{
							continue;
						}
						else
						{
							// DELETE SALES ORDER DETAIL ID
							$where_sales_order_detail = [
								'id'	=> $info_old_product['id']
							];
							$this->crud->delete('sales_order_detail', $where_sales_order_detail);
						}
					}
					
					foreach($post['product'] AS $info)
					{
						// SKIP THE FIRST PRODUCT, BECAUSE IS TEMPLATE
						if($info['product_code'] == ""  && $info['qty'] == "" && $info['price'] == "" && $info['total'] == "")
						{																	
							continue;
						}
						else
						{									
							if(isset($info['sales_order_detail_id'])) // IF OLD PRODUCT
							{									
								$product_id = $this->crud->get_product_id($info['product_code']);
								// UPDATE SALES ORDER DETAIL
								$data_sales_order_detail=array(
									'qty'			=> format_amount($info['qty']),
									'unit_id'		=> $info['unit_id'],
									'price'			=> format_amount($info['price']),
									'warehouse_id'	=> $info['warehouse_id'],
									'total'			=> format_amount($info['total'])
								);
								$this->crud->update('sales_order_detail', $data_sales_order_detail, ['id' => $info['sales_order_detail_id']]);
								$res = 1;
								
							}
							else // IF NEW PRODUCT
							{	
								$product_id = $this->crud->get_product_id($info['product_code']);
								$data_sales_detail=array(
									'sales_order_id'=> $sales_order['id'],
									'invoice'		=> $sales_order['invoice'],
									'product_id'	=> $product_id,
									'product_code'	=> $info['product_code'],
									'qty'			=> format_amount($info['qty']),
									'unit_id'		=> $info['unit_id'],
									'price'			=> format_amount($info['price']),
									'warehouse_id'	=> $info['warehouse_id'],
									'total'			=> format_amount($info['total'])
								);

								if($this->crud->insert('sales_order_detail', $data_sales_detail))
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
					}

					$data_activity = [
						'information' => 'MEMPERBARUI PEMESANAN PENJUALAN',
						'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);
				}
				else
				{
					$res = 0;
				}

				if($res == 1)
				{
					$this->session->set_flashdata('success', 'Transaksi Sales Order Berhasil Diperbarui');
				}
				else
				{
					$this->session->set_flashdata('error', 'Transaksi Sales Order Gagal Diperbarui');
				}
				redirect(site_url('sales/order/detail/'.encrypt_custom($sales_order['id'])));
			}
		}
		else
		{
			if($this->session->userdata('verifypassword') == 1)
			{								
				$this->session->unset_userdata('verifypassword');
				$sales_order = $this->sales->get_detail_sales_order(decrypt_custom($sales_order_id));
				$header = array("title" => "Perbarui Pemesanan Penjualan");		
				$data = array(
					'sales_order' => $sales_order,
					'sales_order_detail' => $this->sales->get_detail_sales_order_detail($sales_order['id'])
				);	
				$footer = array("script" => ['transaction/sales/order/update_sales_order.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/order/update_sales_order', $data);        
				$this->load->view('include/footer', $footer);
			}
			else
			{
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
				redirect(urldecode($this->agent->referrer()));
			}					
		}
	}

	public function delete_sales_order()
	{
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post = $this->input->post();
			$this->crud->delete('sales_order_detail', ['sales_order_id' => $post['sales_order_id']]);
			$this->crud->delete('sales_order', ['id' => $post['sales_order_id']]);

			$data_activity = [
				'information' => 'MENGHAPUS PEMESANAN PENJUALAN',
				'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
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
			$this->session->set_flashdata('success', 'BERHASIL! Pemesanan Penjualan Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	public function print_sales_order($sales_order_id)
	{
		if($this->system->check_access('sales/order', 'create'))
		{
			$sales_order    = $this->sales->get_detail_sales_order($this->global->decrypt($sales_order_id));
			if($sales_order != null)
			{
				$data = array(
					'perusahaan'         => $this->global->company(),
					'sales_order' 		 => $sales_order,
					'sales_order_detail' => $this->sales->get_detail_sales_order_detail($sales_order['id'])
				);	
				$this->load->view('sales/order/print_sales_order', $data);
			}
			else
			{
				$this->load->view('auth/show_404');
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/order'));
		}
	}

	// SALES ORDER TAKING
	public function datatable_sales_order_taking()
	{
		if($this->input->is_ajax_request())
		{			
			header('Content-Type: application/json');
			$this->datatables->select('so_taking.id, so_taking.date, so_taking.code, count(sales_order.id) AS total_so');
			$this->datatables->from('sales_order_taking AS so_taking');			
			$this->datatables->join('sales_order', 'sales_order.so_taking_id = so_taking.id');
			$this->datatables->where('so_taking.deleted', 0);
			$this->datatables->where('DATE(so_taking.created)', date('Y-m-d'));
			$this->datatables->group_by('so_taking.id');		
			$this->datatables->add_column('code', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/order/taking/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id), code');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}
	
    public function sales_order_taking()
    {
		if($this->system->check_access('sales/order/taking','read'))
		{
			$data_activity = [
				'information' => 'MELIHAT DAFTAR PENGAMBILAN PRODUK',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$header = array("title" => "Daftar Pengambilan Produk"); 
			$footer = array("script" => ['transaction/sales/order/taking/sales_order_taking.js']);
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('sales/order/taking/sales_order_taking');        
			$this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}

	public function add_sales_order_taking()
	{
		if($this->system->check_access('sales/order/taking','create'))
		{
			if($this->input->method() === "post")
			{
				$post = $this->input->post();
				$data_so_taking = array(
					'date'	=> date('Y-m-d'),
					'code'	=> $this->sales->sot_code(),
				);
				$sot_id = $this->crud->insert_id('sales_order_taking', $data_so_taking);
				if($sot_id != null)
				{
					foreach($post['so_id'] AS $info)
					{
						$data_so = array(
							'so_taking_id' => $sot_id
						);						
						$this->crud->update('sales_order', $data_so, ['id' => $info]);
					
					}

					$data_activity = [
						'information' => 'MEMBUAT PENGAMBILAN PRODUK',
						'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);

					$this->session->set_flashdata('success', 'Transaksi Pengambilan Produk Berhasil');
					redirect(site_url('sales/order/taking/detail/'.$this->global->encrypt($sot_id)));
				}
				else
				{
					$this->session->set_flashdata('error', 'Transaksi Pengambilan Produk Gagal');
					redirect(site_url('sales'));
				}
			}
			else
			{
				$header = array("title" => "Pengambilan Produk Dari Gudang");				
				$data = array(
					'sales_order_taking' => $this->sales->get_sales_order_taking()
				);
				$footer = array("script" => ['transaction/sales/order/taking/crud_sales_order_taking.js']);
				$this->load->view('include/header', $header);  
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/order/taking/add_sales_order_taking', $data);
				$this->load->view('include/footer', $footer);
			}	
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales'));
		}					
	}

	public function datatable_detail_sales_order_taking($so_taking_id)
	{
		if($this->input->is_ajax_request())
		{		
			$so_taking_id = $this->global->decrypt($so_taking_id);				
			header('Content-Type: application/json');
			$this->datatables->select('sales_order.id AS id, sales_order.date, sales_order.invoice, sales_order.taking_method AS taking, sales_order.grandtotal, customer.name AS name_c, employee.name AS name_s, sales_order.sales_order_status AS status_so');
			$this->datatables->from('sales_order');
			$this->datatables->join('employee', 'employee.code = sales_order.sales_code');
			$this->datatables->join('customer', 'customer.code = sales_order.customer_code');
			$this->datatables->join('sales_order_detail', 'sales_order_detail.sales_order_id = sales_order.id');
			$this->datatables->where('sales_order.so_taking_id', $so_taking_id);
			$this->datatables->where('sales_order.deleted', 0);
			$this->datatables->where('sales_order_detail.deleted', 0);
			$this->datatables->group_by('sales_order.id');
			$this->datatables->add_column('invoice', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/order/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id), invoice');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}		
	}
	
    public function detail_sales_order_taking($so_taking_id)
    {
		if($this->system->check_access('sales/order/taking','detail'))
		{			
			$dsot = $this->crud->get_where('sales_order_taking', ['id' => $this->global->decrypt($so_taking_id)])->row_array();
			if($dsot != null)
			{	
				$data_activity = [
					'information' => 'MELIHAT DETAIL PENGAMBILAN PRODUK',
					'method'      => 2, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);		

				$header = array("title" => "Detail Pengambilan Produk");
				$data = array(
					'dsot' => $dsot
				);
				$footer = array("script" => ['transaction/sales/order/taking/detail_sales_order_taking.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/order/taking/detail_sales_order_taking', $data);
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
	
	public function print_sales_order_taking($so_taking_id)
	{
		if($this->system->check_access('sales/order/taking','create'))
		{
			$so_taking = $this->crud->get_where('sales_order_taking', ['id' => $this->global->decrypt($so_taking_id)])->row_array();
			$sales_order = $this->crud->get_where('sales_order', ['so_taking_id' => $this->global->decrypt($so_taking_id)])->result_array();
			$so_id = array();
			foreach($sales_order AS $info_so)
			{
				$so_id[] = $info_so['id'];
			}
			$sot = array();
			$warehouse = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
								->from('sales_order_detail')->join('warehouse', 'warehouse.id = sales_order_detail.warehouse_id')								
								->where_in('sales_order_detail.sales_order_id', $so_id)
								->where('warehouse.deleted', 0)->where('sales_order_detail.deleted', 0)
								->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
			foreach($warehouse AS $info_w)
			{
				$data_so = $this->db->select('sales_order.invoice')
									->from('sales_order')->join('sales_order_detail', 'sales_order_detail.sales_order_id = sales_order.id')
									->where('sales_order_detail.warehouse_id', $info_w['id_w'])
									->where_in('sales_order_detail.sales_order_id', $so_id)
									->where('sales_order_detail.deleted', 0)
									->group_by('sales_order.id')->order_by('sales_order.invoice', 'asc')->get()->result_array();
				$product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p')
								->from('sales_order_detail')->join('product', 'product.id = sales_order_detail.product_id')
								->where('sales_order_detail.warehouse_id', $info_w['id_w'])
								->where_in('sales_order_detail.sales_order_id', $so_id)
								->where('product.deleted', 0)->where('sales_order_detail.deleted', 0)
								->group_by('product.id')->order_by('product_code', 'asc')->get()->result_array();
				$data_product = array();
				foreach($product AS $info_p)
				{
					$data_qty = $this->db->select('sales_order_detail.qty, sales_order_detail.unit_id')->from('sales_order_detail')
										->where('sales_order_detail.warehouse_id', $info_w['id_w'])
										->where('sales_order_detail.product_id', $info_p['id_p'])
										->where_in('sales_order_detail.sales_order_id', $so_id)
										->where('sales_order_detail.deleted', 0)
										->group_by('sales_order_detail.id')
										->get()->result_array();				
					$qty_convert = 0;
					foreach($data_qty AS $info_q)
					{
						$convert = $this->crud->get_where('product_unit', ['product_code' => $info_p['code_p'], 'unit_id' => $info_q['unit_id']])->row_array();
						if($convert['value'] != null)
						{
							$qty_convert = $qty_convert + ($info_q['qty'] * $convert['value']);
						}						
						else
						{
							$qty_convert = $qty_convert + $info_q['qty'];
						}
					}

					$product_unit = $this->db->select('unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, product_unit.value')
											->from('product_unit')->join('unit', 'unit.id = product_unit.unit_id')
											->where('product_code', $info_p['code_p']) ->where('product_unit.deleted', 0)
											->order_by('product_unit.value', 'desc')
											->get()->result_array();
					$qty = array();					
					foreach($product_unit AS $pu)
					{							
						$modulo = $qty_convert % $pu['value'];						
						if($modulo == 0)
						{
							$amount = $qty_convert / $pu['value'];
							$qty[] = array(
								'id_u' => $pu['id_u'],
								'code_u' => $pu['code_u'],
								'name_u' => $pu['name_u'],
								'qty' => $amount
							);
							break;
						}
						else
						{							
							$amount = $qty_convert / $pu['value'];
							if($amount < 1)
							{
								continue;
							}							
							else
							{
								$qty[] = array(
									'id_u' => $pu['id_u'],
									'code_u' => $pu['code_u'],
									'name_u' => $pu['name_u'],
									'qty' => (int)$amount
								);												
							}	
							$qty_convert = $qty_convert - ((int)$amount*$pu['value']);
									
						}						
					}

					$data_product[] = array(
						'id_p'   => $info_p['id_p'],
						'code_p' => $info_p['code_p'],
						'name_p' => $info_p['name_p'],
						'qty'	 => $qty
					);
				}
				
				
				$sot[] = array(
					'code_sot' 	 => $so_taking['code'],
					'data_so'    => $data_so,
					'id_w' 		 => $info_w['id_w'],
					'code_w' 	 => $info_w['code_w'],
					'name_w' 	 => $info_w['name_w'],
					'product'	 => $data_product
				);
			}

			$data_activity = [
				'information' => 'MENCETAK PENGAMBILAN PRODUK',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$data = array(
				'perusahaan' => $this->global->company(),
				'sot'        => $sot
			);
			$this->load->view('sales/order/taking/print_sales_order_taking', $data);
		}			
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/order/taking'));
		}					
	}

	// SALES INVOICE
    public function sales_invoice()
    {
		if($this->system->check_access('sales/invoice','read'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$this->datatables->select('sales_invoice.id AS id, sales_invoice.date, sales_invoice.invoice, sales_invoice.payment, sales_invoice.due_date, sales_invoice.grandtotal, sales_invoice.account_payable, customer.name AS name_c, sales.name AS name_s, sales_invoice.information, sales_invoice.ppn, sales_invoice.payment_status, sales_invoice.do_status, 
								sales_invoice.invoice AS search_invoice');
				$this->datatables->from('sales_invoice');
				$this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
				$this->datatables->join('employee AS sales', 'sales.code = sales_invoice.sales_code');
				$this->datatables->where('sales_invoice.deleted', 0);			
				if($post['do_status'] == "" || $post['do_status'] != 0)
				{
					$this->datatables->where('DATE(sales_invoice.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
					$this->datatables->where('DATE(sales_invoice.created) <=', date('Y-m-d'));
					if($post['do_status'] == 1)
					{
						$this->datatables->where('sales_invoice.do_status', $post['do_status']);						
					}										
				}
				else
				{					
					$this->datatables->where('sales_invoice.do_status', $post['do_status']);
				}
				$this->datatables->group_by('sales_invoice.id');		
				$this->datatables->add_column('invoice', 
				'<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'" target="_blank"><b>$2</b></a>
				', 'encrypt_custom(id),invoice');
				header('Content-Type: application/json');
				echo $this->datatables->generate();
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT DAFTAR PENJUALAN',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Penjualan");
				$footer = array("script" => ['transaction/sales/invoice/sales_invoice.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/invoice/sales_invoice');
				$this->load->view('include/footer', $footer);
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}  
		
	}

	public function create_sales_invoice($sales_order_id = null)
    {
		if($this->system->check_access('sales/invoice','create'))
		{
			if($this->input->method() === 'post')
			{							
				$post = $this->input->post();
				$this->form_validation->set_rules('date', 'Tanggal Invoice', 'trim|required|xss_clean');
				$this->form_validation->set_rules('sales_code', 'Sales', 'trim|required|xss_clean');
				$this->form_validation->set_rules('customer_code', 'Konsumen', 'trim|required|xss_clean');
				$this->form_validation->set_rules('payment', 'Jenis Pembayaran', 'trim|required|xss_clean');		
				if($this->input->post('payment') == 2)
				{
					$this->form_validation->set_rules('payment_due', 'Jatuh Tempo', 'trim|required|xss_clean');
				}
				$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
				$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|xss_clean');
				$this->form_validation->set_rules('down_payment', 'Uang Muka Pembayaran', 'trim|required|xss_clean');
				$this->form_validation->set_rules('account_payable', 'Hutang Dagang', 'trim|required|xss_clean');
				$this->form_validation->set_rules('discount_p', 'Diskon (%)', 'trim|required|xss_clean');
				$this->form_validation->set_rules('discount_rp', 'Diskon (Rp)', 'trim|required|xss_clean');
				$this->form_validation->set_rules('grandtotal', 'grandtotal', 'trim|required|xss_clean');				
				$sales_order_id = isset($post['sales_order_id']) ? decrypt_custom($post['sales_order_id']) : null;
				if($this->form_validation->run() == FALSE)
				{
					if(isset($post['sales_order_id']))
					{
						$header = array("title" => "Penjualan Baru");
						$sales_order =  $this->sales->get_detail_sales_order($sales_order_id);
						$data = array(
							'sales_order' => $sales_order,
							'sales_order_detail' => $this->sales->get_detail_sales_order_detail($sales_order['id'])
						);	
						$footer = array("script" => ['transaction/sales/invoice/create_sales_invoice.js']);
						$this->load->view('include/header', $header);
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('sales/invoice/create_sales_invoice', $data);
						$this->load->view('include/footer');
					}
					else
					{
						$header = array("title" => "Penjualan Baru");
						$footer = array("script" => ['transaction/sales/invoice/create_sales_invoice.js']);
						$this->load->view('include/header', $header);
						$this->load->view('include/menubar');
						$this->load->view('include/topbar');
						$this->load->view('sales/invoice/create_sales_invoice');
						$this->load->view('include/footer', $footer);
					}
				}
				else
				{
					$this->db->trans_start();					
					$sales_invoice_code = $this->sales->sales_invoice_code();
					$customer		    = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
					$plus 				= isset($post['payment_due']) ? $post['payment_due'] : 0;
					$ppn				= (!isset($post['ppn'])) ?  0 : $post['ppn'];
					$total_price  		= format_amount($post['subtotal']);
					$discount_rp 		= format_amount($post['discount_rp']);
					$delivery_cost 		= format_amount($post['delivery_cost']);
					$grandtotal  		= format_amount($post['grandtotal']);
					$down_payment  		= format_amount($post['down_payment']);
					$account_payable	= format_amount($post['account_payable']);							
					$created			= date('Y-m-d H:i:s');
					$data_sales_invoice =[
						'date'				=> format_date($post['date']),
						'employee_code'		=> $this->session->userdata('code_e'),
						'sales_code'		=> $post['sales_code'],
						'customer_code'		=> $post['customer_code'],
						'invoice'			=> $sales_invoice_code,
						'payment'			=> $post['payment'],
						'cl_type'    		=> isset($post['from_cl_type']) ? $post['from_cl_type'] : null,
						'account_id' 		=> isset($post['from_account_id']) ? $post['from_account_id'] : null,
						'payment_due'		=> $post['payment_due'],
						'information'		=> $post['information'],
						'total_product'		=> $post['total_product'],
						'total_qty'			=> $post['total_qty'],
						'total_price'		=> $total_price,
						'discount_p'		=> $post['discount_p'],
						'discount_rp'		=> $discount_rp,
						'delivery_cost'		=> $delivery_cost,
						'grandtotal'		=> $grandtotal,
						'down_payment'		=> $down_payment,
						'account_payable'	=> $grandtotal,
						'payment_status'	=> $post['payment'],
						'due_date'          => date('Y-m-d',strtotime(format_date($post['date']) . "+$plus days")),
						'ppn'				=> $ppn,
						'created'			=> $created
					];
					if($this->crud->get_where('sales_invoice', ['created' => $created])->num_rows() == 0)
					{
						// SALES INVOICE
						$sales_invoice_id = $this->crud->insert_id('sales_invoice', $data_sales_invoice);
						if($sales_invoice_id != null)
						{
							// SALES INVOICE DETAIL
							$total_hpp = 0;					
							foreach($post['product'] AS $info)
							{
								$res = 0;
								$product_id = $this->crud->get_product_id($info['product_code']);
								$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
								$hpp = $this->product->hpp($info['product_code']);
								$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
								$data_sales_invoice_detail= [
									'sales_invoice_id' => $sales_invoice_id,
									'invoice'		=> $sales_invoice_code,
									'product_id'	=> $product_id,
									'product_code'	=> $info['product_code'],											
									'qty'			=> $qty,
									'unit_id'		=> $info['unit_id'],
									'unit_value'    => ($convert['value'] != null) ? $convert['value'] : 1,
									'price'			=> $price,
									'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
									'hpp'			=> $hpp,
									'warehouse_id'	=> $info['warehouse_id'],									
									'total'			=> $total,
									'ppn'			=> $ppn
								];
								$this->crud->insert('sales_invoice_detail', $data_sales_invoice_detail);
								$total_hpp = $total_hpp + ($hpp*$qty*$convert['value']);
								$res = 1;
							}
							$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp], ['id' => $sales_invoice_id]);
							if($res == 1)
							{
								$data_activity = [
									'information' => 'MEMBUAT PENJUALAN BARU (NO. TRANSAKSI '.$sales_invoice_code.')',
									'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
									'code_e'      => $this->session->userdata('code_e'),
									'name_e'      => $this->session->userdata('name_e'),
									'user_id'     => $this->session->userdata('id_u')
								];						
								$this->crud->insert('activity', $data_activity);
								$res = 1;
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
					}
					else
					{
						$res = 0;
					}
					$this->db->trans_complete();					
					if($res == 1 && $this->db->trans_status() === TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('success', 'Data Transaksi Penjualan berhasil ditambahkan');
						redirect(site_url('sales/invoice/detail/'.encrypt_custom($sales_invoice_id)));
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Data Transaksi Penjualan gagal ditambahkan');
						redirect(site_url('sales'));
					}												
				}												
			}
			else
			{
				if($sales_order_id != null)
				{
					$sales_order_id = $this->global->decrypt($sales_order_id);
					$header = array("title" => "Penjualan Baru");
					$sales_order =  $this->sales->get_detail_sales_order($sales_order_id);
					$data = array(
						'sales_order' => $sales_order,
						'sales_order_detail' => $this->sales->get_detail_sales_order_detail($sales_order['id'])
					);
					$footer = array("script" => ['transaction/sales/invoice/crud_sales_invoice_so.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('sales/invoice/create_sales_invoice', $data);
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$header = array("title" => "Penjualan Baru");
					$footer = array("script" => ['transaction/sales/invoice/create_sales_invoice.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');
					$this->load->view('include/topbar');
					$this->load->view('sales/invoice/create_sales_invoice');
					$this->load->view('include/footer', $footer);
				}				
			}
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales'));
		}		
	}

	public function create_sales_invoice_do()
    {
		if($this->system->check_access('sales/invoice','create'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$sales_invoice = $this->sales->get_detail_sales_invoice($post['sales_invoice_id']);
				if($sales_invoice['do_status'] == 0)
				{
					$sales_invoice_detail = $this->sales->get_detail_sales_invoice_detail($sales_invoice['id']);
					$customer = $this->crud->get_where('customer', ['code' => $sales_invoice['customer_code']])->row_array();
					$check_stock_sales_invoice_do = $this->sales->check_stock_sales_invoice_do($sales_invoice);
					if($check_stock_sales_invoice_do['min_stock'] == 0)
					{
						// ALGORITHM
						/*					
						-GENERAL LEDGER -> PENJUALAN (K)
						-GENERAL LEDGER -> PPN KELUARAN (K)
						-GENERAL LEDGER -> PIUTANG USAHA (D)
						-------------------------------
						-GENERAL LEDGER -> PERSEDIAAN BARANG (K)
						-GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (D)
						-------------------------------
						-TABLE PAYMENT_LEDGER -> CASH_LEDGER -> GENERAL_LEDGER (KAS, PIUTANG USAHA)
						*/
						$this->db->trans_start();
						// GENERAL LEDGER -> PENJUALAN (K)
						$where_last_balance = [
							'coa_account_code' => "40101",
							'date <='        => $sales_invoice['date'],                    
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], ($sales_invoice['ppn'] == 0 ) ? $sales_invoice['grandtotal'] : ($sales_invoice['grandtotal']/1.11)) : add_balance(0, ($sales_invoice['ppn'] == 0 ) ? $sales_invoice['grandtotal'] : ($sales_invoice['grandtotal']/1.11));
						$data = [
							'date'        => $sales_invoice['date'],
							'coa_account_code'  => "40101",
							'transaction_id' => $sales_invoice['id'],
							'invoice'     => $sales_invoice['invoice'],
							'information' => 'PENJUALAN',
							'note'		  => 'PENJUALAN_'.$sales_invoice['invoice'].'_'.$customer['name'],
							'credit'      => ($sales_invoice['ppn'] == 0 ) ? $sales_invoice['grandtotal'] : ($sales_invoice['grandtotal']/1.11),
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "40101",
								'date >'        => $sales_invoice['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], ($sales_invoice['ppn'] == 0 ) ? $sales_invoice['grandtotal'] : ($sales_invoice['grandtotal']/1.11))], ['id' => $info['id']]);
							}                            
						}
						// GENERAL LEDGER -> PPN KELUARAN (K)
						if($sales_invoice['ppn'] != 0)
						{						
							$where_last_balance = [
								'coa_account_code' => "20301",
								'date <='        => $sales_invoice['date'],                    
								'deleted'        => 0
							];
							$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], (11/111*$sales_invoice['grandtotal'])) : add_balance(0, (11/111*$sales_invoice['grandtotal']));
							$data = [
								'date'        => $sales_invoice['date'],
								'coa_account_code'  => "20301",
								'transaction_id' => $sales_invoice['id'],
								'invoice'     => $sales_invoice['invoice'],
								'information' => 'PENJUALAN',
								'note'		  => 'PENJUALAN_'.$sales_invoice['invoice'].'_'.$customer['name'],
								'credit'      => (11/111*$sales_invoice['grandtotal']),
								'balance'     => $balance
							];
							if($this->crud->insert('general_ledger', $data))
							{
								$where_after_balance = [
									'coa_account_code'=> "20301",
									'date >'        => $sales_invoice['date'],
									'deleted'       => 0
								];
								$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_balance  AS $info)
								{
									$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], (11/111*$sales_invoice['grandtotal']))], ['id' => $info['id']]);
								}                            
							}
						}	
						// GENERAL LEDGER -> PIUTANG USAHA (D)
						$where_last_balance = [
							'coa_account_code' => "10201",
							'date <='        => $sales_invoice['date'],                    
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_invoice['grandtotal']) : add_balance(0, $sales_invoice['grandtotal']);
						$data = [
							'date'        => $sales_invoice['date'],
							'coa_account_code'  => "10201",
							'transaction_id' => $sales_invoice['id'],
							'invoice'     => $sales_invoice['invoice'],
							'information' => 'PENJUALAN',
							'note'		  => 'PENJUALAN_'.$sales_invoice['invoice'].'_'.$customer['name'],
							'debit'       => $sales_invoice['grandtotal'],
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "10201",
								'date >'        => $sales_invoice['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_invoice['grandtotal'])], ['id' => $info['id']]);
							}                            
						}
						// GENERAL LEDGER -> PERSEDIAAN BARANG (K)
						$where_last_balance = [
							'coa_account_code' => "10301",
							'date <='        => $sales_invoice['date'],                    
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_invoice['total_hpp']) : sub_balance(0, $sales_invoice['total_hpp']);
						$data = [
							'date'        => $sales_invoice['date'],
							'coa_account_code'  => "10301",
							'transaction_id' => $sales_invoice['id'],
							'invoice'     => $sales_invoice['invoice'],
							'information' => 'PENJUALAN',
							'note'		  => 'PENJUALAN_'.$sales_invoice['invoice'].'_'.$customer['name'],
							'credit'       => $sales_invoice['total_hpp'],
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "10301",
								'date >'        => $sales_invoice['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_invoice['total_hpp'])], ['id' => $info['id']]);
							}
						}
						// GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (D)
						$where_last_balance = [
							'coa_account_code' => "50001",
							'date <='        => $sales_invoice['date'],                    
							'deleted'        => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_invoice['total_hpp']) : add_balance(0, $sales_invoice['total_hpp']);
						$data = [
							'date'        => $sales_invoice['date'],
							'coa_account_code'  => "50001",
							'transaction_id' => $sales_invoice['id'],
							'invoice'     => $sales_invoice['invoice'],
							'information' => 'PENJUALAN',
							'note'		  => 'PENJUALAN_'.$sales_invoice['invoice'].'_'.$customer['name'],
							'debit'       => $sales_invoice['total_hpp'],
							'balance'     => $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "50001",
								'date >'        => $sales_invoice['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_invoice['total_hpp'])], ['id' => $info['id']]);
							}                            
						}				
						$this->db->trans_complete();
						if($this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
						}
						else
						{
							$this->db->trans_rollback();
							$this->session->set_flashdata('error', 'GAGAL! Cetak DO Gagal (General Ledger)');
							redirect(site_url('sales'));
						}
						$this->db->trans_start();
						// POR & CASH LEDGER
						if($sales_invoice['payment'] == 1)
						{
							$por_code = $this->payment->por_code();
							$data_por = array(
								'transaction_type' => 2,
								'code'            => $por_code,
								'date'            => $sales_invoice['date'],
								'method'         => ($sales_invoice['cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),							
								'cash'            => ($sales_invoice['cl_type'] == 1) ? $sales_invoice['grandtotal'] : 0,							
								'transfer'        => ($sales_invoice['cl_type'] == 2) ? $sales_invoice['grandtotal'] : 0,
								'grandtotal'      => $sales_invoice['grandtotal'],
								'employee_code'   => $this->session->userdata('code_e')
							);
							$por_id = $this->crud->insert_id('payment_ledger', $data_por);
							if($por_id != null)
							{
								$data_por_detail = [
									'pl_id'      => $por_id,
									'method'     => ($sales_invoice['cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
									'account_id' => $sales_invoice['account_id'],
									'amount'     => $sales_invoice['grandtotal']
								];
								$this->crud->insert('payment_ledger_detail', $data_por_detail);
								$data_por_transaction = [
									'pl_id'      => $por_id,
									'transaction_id'=> $sales_invoice['id'],
									'cash'            => ($sales_invoice['cl_type'] == 1) ? $sales_invoice['grandtotal'] : 0,
									'transfer'        => ($sales_invoice['cl_type'] == 2) ? $sales_invoice['grandtotal'] : 0,
									'amount'     => $sales_invoice['grandtotal']
								];
								$this->crud->insert('payment_ledger_transaction', $data_por_transaction);
								// CASH LEDGER
								$from_where_last_balance = [
									'cl_type'    => $sales_invoice['cl_type'],
									'account_id' => $sales_invoice['account_id'],
									'date <='    => $sales_invoice['date'],                    
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$sales_invoice['grandtotal'] : 0+$sales_invoice['grandtotal'];
								$data = [
									'cl_type'     => $sales_invoice['cl_type'],
									'account_id'  => $sales_invoice['account_id'],
									'transaction_id'   => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'date'        => $sales_invoice['date'],
									'amount'      => $sales_invoice['grandtotal'],
									'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'    => $sales_invoice['cl_type'],
										'account_id' => $sales_invoice['account_id'],
										'date >'     => $sales_invoice['date'],
										'deleted'    => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{                        
										$balance = $info['balance']+$sales_invoice['grandtotal'];
										$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
									}                            
								}
								// GENERAL_LEDGER -> KAS & BANK (K)
								$where_last_balance = [
									'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
									'date <='        => $sales_invoice['date'],                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_invoice['grandtotal']) : add_balance(0, $sales_invoice['grandtotal']);
								$data = [
									'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
									'date'        => $sales_invoice['date'],										
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'debit'      => $sales_invoice['grandtotal'],
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
										'date >'        => $sales_invoice['date'],
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_invoice['grandtotal'])], ['id' => $info['id']]);
									}                            
								}
								// GENERAL LEDGER -> PIUTANG USAHA (K)
								$where_last_balance = [
									'coa_account_code' => "10201",
									'date <='        => $sales_invoice['date'],                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_invoice['grandtotal']) : sub_balance(0, $sales_invoice['grandtotal']);
								$data = [
									'coa_account_code'  => "10201",
									'date'        => $sales_invoice['date'],
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'credit'       => $sales_invoice['grandtotal'],
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "10201",
										'date >'        => $sales_invoice['date'],
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_invoice['grandtotal'])], ['id' => $info['id']]);
									}
								}
								// ACCOUNT_PAYABALE
								$this->crud->update('sales_invoice', ['account_payable' => 0, 'payment_status' => 1], ['id' => $sales_invoice['id']]);
							}
						}
						else if($sales_invoice['payment'] == 2 && $sales_invoice['down_payment'] > 0)
						{
							$por_code = $this->payment->por_code();
							$data_por = array(
								'transaction_type' => 2,
								'code'            => $por_code,
								'date'            => $sales_invoice['date'],
								'method'         => ($sales_invoice['cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),							
								'cash'            => ($sales_invoice['cl_type'] == 1) ? $sales_invoice['down_payment'] : 0,							
								'transfer'        => ($sales_invoice['cl_type'] == 2) ? $sales_invoice['down_payment'] : 0,
								'grandtotal'      => $sales_invoice['down_payment'],
								'employee_code'   => $this->session->userdata('code_e')
							);
							$por_id = $this->crud->insert_id('payment_ledger', $data_por);
							if($por_id != null)
							{
								$data_por_detail = [
									'pl_id'      => $por_id,
									'method'     => ($sales_invoice['cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
									'account_id' => $sales_invoice['account_id'],
									'amount'     => $sales_invoice['down_payment']
								];
								$this->crud->insert('payment_ledger_detail', $data_por_detail);
								$data_por_transaction = [
									'pl_id'      => $por_id,
									'transaction_id'=> $sales_invoice['id'],
									'cash'            => ($sales_invoice['cl_type'] == 1) ? $sales_invoice['down_payment'] : 0,
									'transfer'        => ($sales_invoice['cl_type'] == 2) ? $sales_invoice['down_payment'] : 0,
									'amount'     => $sales_invoice['down_payment']
								];
								$this->crud->insert('payment_ledger_transaction', $data_por_transaction);
								// CASH LEDGER
								$from_where_last_balance = [
									'cl_type'    => $sales_invoice['cl_type'],
									'account_id' => $sales_invoice['account_id'],
									'date <='    => $sales_invoice['date'],                    
									'deleted'    => 0
								];
								$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
								$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$sales_invoice['down_payment'] : 0+$sales_invoice['down_payment'];
								$data = [
									'cl_type'     => $sales_invoice['cl_type'],
									'account_id'  => $sales_invoice['account_id'],
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'date'        => $sales_invoice['date'],
									'amount'      => $sales_invoice['down_payment'],
									'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
									'balance'     => $from_balance
								];
								$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
								if($from_cl_id)
								{
									$from_where_after_balance = [
										'cl_type'       => $sales_invoice['cl_type'],
										'account_id'    => $sales_invoice['account_id'],
										'date >'        => $sales_invoice['date'],
										'deleted'       => 0
									];                    
									$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
									foreach($from_after_balance  AS $info)
									{                        
										$balance = $info['balance'] + $sales_invoice['down_payment'];
										$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
									}                            
								}
								// GENERAL_LEDGER -> KAS & BANK (K)
								$where_last_balance = [
									'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
									'date <='        => $sales_invoice['date'],                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_invoice['down_payment']) : add_balance(0, $sales_invoice['down_payment']);
								$data = [
									'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
									'date'        => $sales_invoice['date'],										
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'debit'      => $sales_invoice['down_payment'],
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
										'date >'        => $sales_invoice['date'],
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_invoice['down_payment'])], ['id' => $info['id']]);
									}                            
								}
								// GENERAL LEDGER -> PIUTANG USAHA (K)
								$where_last_balance = [
									'coa_account_code' => "10201",
									'date <='        => $sales_invoice['date'],                    
									'deleted'        => 0
								];
								$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_invoice['down_payment']) : sub_balance(0, $sales_invoice['down_payment']);
								$data = [
									'coa_account_code'  => "10201",
									'date'        => $sales_invoice['date'],
									'transaction_id' => $por_id,
									'invoice'     => $por_code,
									'information' => 'PEMBAYARAN PENJUALAN',
									'note'		  => 'PEMBAYARAN_PENJUALAN_'.$por_code.'_'.$customer['name'],
									'credit'       => $sales_invoice['down_payment'],
									'balance'     => $balance
								];									
								if($this->crud->insert('general_ledger', $data))
								{
									$where_after_balance = [
										'coa_account_code'=> "10201",
										'date >'        => $sales_invoice['date'],
										'deleted'       => 0
									];
									$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
									foreach($after_balance  AS $info)
									{
										$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_invoice['down_payment'])], ['id' => $info['id']]);
									}
								}
								// ACCOUNT_PAYABALE
								$this->crud->update('sales_invoice', ['account_payable' => $sales_invoice['grandtotal']-$sales_invoice['down_payment']], ['id' => $sales_invoice['id']]);
							}						
						}
						// SALES INVOICE DETAIL
						foreach($sales_invoice_detail AS $info)
						{							
							$res = 0;						
							// STOCK
							$qty_convert = $info['qty']*$info['unit_value'];
							$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
							if($check_stock->num_rows() == 1)
							{	
								$stock = $check_stock->row_array();
								$where_stock = array(
									'product_code'  => $info['product_code'],
									'warehouse_id'  => $info['warehouse_id']
								);       							
								$stock = array(                                
									'product_id'    => $info['product_id'],
									'qty'           => $stock['qty']-$qty_convert
								);
								$update_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{							
								$stock = array(                                
									'product_id'    => $info['product_id'],
									'product_code'  => $info['product_code'],                                                        
									'qty'           => 0-$qty,
									'warehouse_id'  => $info['warehouse_id']
								);
								$update_stock = $this->crud->insert('stock', $stock);
							}
							if($update_stock)
							{
								// STOCK CARD
								$where_last_stock_card = [
									'date <='      => $sales_invoice['date'],
									'product_id'   => $info['product_id'],
									'warehouse_id' => $info['warehouse_id'],
									'deleted'      => 0
								];
								$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_card = array(
									'type'            => 4,
									'information'     => 'PENJUALAN',
									'note'			  => $customer['name'],
									'date'			  => $sales_invoice['date'],
									'transaction_id'  => $sales_invoice['id'],								
									'invoice'         => $sales_invoice['invoice'],
									'transaction_detail_id' => $info['id'],
									'product_id'      => $info['product_id'],
									'product_code'    => $info['product_code'],
									'qty'             => $qty_convert,
									'method'          => 2, // 1:In, 2:Out
									'stock'           => $last_stock_card['stock']-$qty_convert,
									'warehouse_id'    => $info['warehouse_id'],
									'user_id'         => $this->session->userdata('id_u')
								);
								$this->crud->insert('stock_card',$data_stock_card);
								$where_after_stock_card = [
									'date >'       => $sales_invoice['date'],
									'product_id'   => $info['product_id'],
									'warehouse_id' => $info['warehouse_id'],
									'deleted'      => 0
								];                    
								$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
								foreach($after_stock_card  AS $info_after_stock_card)
								{
									$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']-$qty_convert], ['id' => $info_after_stock_card['id']]);
								}
								// STOCK MOVEMENT
								$where_last_stock_movement = [
									'product_id'   => $info['product_id'],
									'date <='      => $sales_invoice['date'],
									'deleted'      => 0
								];
								$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
								$data_stock_movement = [
									'type'            => 4,
									'information'     => 'PENJUALAN',
									'note'			  => $customer['name'],
									'date'            => $sales_invoice['date'],
									'transaction_id'  => $sales_invoice['id'],
									'invoice'         => $sales_invoice['invoice'],
									'transaction_detail_id' => $info['id'],
									'product_id'      => $info['product_id'],
									'product_code'    => $info['product_code'],
									'qty'             => $qty_convert,
									'method'          => 2,
									'stock'           => $last_stock_movement['stock']-$qty_convert,
									'price'           => $info['price'],
									'hpp'             => $info['hpp'],
									'employee_code'   => $this->session->userdata('code_e')
								];
								$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
								$where_after_stock_movement = [
									'product_id'   => $info['product_id'],
									'date >'       => $sales_invoice['date'],
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
						
						// // MUTATION BERKAHMULYA TO BAJAMULYA
						// if($customer['name'] == "TUNAI")
						// {
						// 	$this->cv_move_to_bm($sales_invoice, $sales_invoice_detail, $customer);
						// }
						
						if($res == 1 && $this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
							$this->crud->update('sales_invoice', ['do_status' => 1], ['id' => $sales_invoice['id']]);
							$data_activity = [
								'information' => 'MEMBUAT PENJUALAN (CETAK DO) (NO. TRANSAKSI: '.$sales_invoice['invoice'].')',
								'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];
							$this->crud->insert('activity', $data_activity);					
							$this->session->set_userdata('create_sales_invoice_do', '1');
							$this->session->set_flashdata('success', 'Cetak DO Penjualan Berhasil');
							$response   =   [
								'sales_invoice_id' => encrypt_custom($sales_invoice['id']),
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
						$this->session->set_flashdata('error', 'Mohon Maaf, Cetak DO Gagal karena terdapat Stok yang Kurang, harap periksa kembali');
						$this->session->set_flashdata('min_product', $check_stock_sales_invoice_do['found']);
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
			redirect(site_url('sales'));
		}		
	}

	private function cv_move_to_bm($sales_invoice, $sales_invoice_detail, $customer)
	{
		$db2 = $this->load->database('bajamulya', TRUE);
		// ALGORITHM
		/*
			-TABLE PURCHASE_INVOICE
			-GENERAL LEDGER -> PERSEDIAAN BARANG (D)
			-GENERAL LEDGER -> PPN MASUKAN (D)
			-GENERAL LEDGER -> HUTANG USAHA	(K)
			-------------------------------					 
			-TABLE PAYMENT_LEDGER -> CASH_LEDGER -> GENERAL_LEDGER (KAS, HUTANG USAHA)
			-TABLE PURCHASE__INVOICE_DETAIL
		*/						
		$purchase_invoice_code = $this->purchase->purchase_invoice_code();
		$post['supplier_code'] = "SUPL-00000";
		$post['payment']	  = 1;
		$supplier 			= $this->crud->get_where('supplier', ['name' => $post['supplier_code']])->row_array();
		$plus 			    = 0;
		$ppn 			    = 0;
		$price_include_tax  = 0;
		$total_price  		= $sales_invoice['total_price'];
		$discount_p 		= $sales_invoice['discount_p'];
		$discount_rp 		= $sales_invoice['discount_rp'];
		$total_tax  		= 0;
		$delivery_cost 		= $sales_invoice['delivery_cost'];
		$grandtotal  		= $sales_invoice['grandtotal'];
		$down_payment  		= $sales_invoice['down_payment'];
		$account_payable	= $sales_invoice['account_payable'];
		$created			= date('Y-m-d H:i:s');				
		// PURCHASE INVOICE
		$data_purchase		= [
			'code'				=> $purchase_invoice_code,
			'date'				=> $sales_invoice['date'],
			'employee_code'		=> $this->session->userdata('code_e'),
			'supplier_code'		=> $post['supplier_code'],
			'invoice'			=> $sales_invoice['invoice'],
			'payment'			=> 1,
			'payment_due'		=> 1,
			'total_product'		=> $sales_invoice['total_product'],
			'total_qty'			=> $sales_invoice['total_qty'],
			'total_price'		=> $total_price,
			'discount_p'		=> $discount_p,
			'discount_rp'		=> $discount_rp,
			'ppn'				=> $ppn,
			'price_include_tax' => $price_include_tax,
			'total_tax' 		=> $total_tax,
			'delivery_cost'		=> $delivery_cost,
			'grandtotal'		=> $grandtotal,
			'account_payable'	=> ($post['payment'] == 1) ? 0 : $account_payable,
			'payment_status'	=> $post['payment'],
			'due_date'          => date('Y-m-d',strtotime($sales_invoice['date'] . "+$plus days")),
			'information'		=> $sales_invoice['information'],
			'created'			=> $created
		];
		if($this->crud->get_where2('purchase_invoice', ['created' => $created])->num_rows() == 0)
		{			
			$db2->trans_start();
			$purchase_invoice_id = $this->crud->insert_id2('purchase_invoice', $data_purchase);						
			// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
			$coa_inventory_value = ($ppn == 0) ? $grandtotal : ($grandtotal/1.11);
			$where_last_balance = [
				'coa_account_code' => "10301",
				'date <='        => $sales_invoice['date'],
				'deleted'        => 0
			];
			$last_balance = $db2->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $coa_inventory_value) : add_balance(0, $coa_inventory_value);
			$data = [
				'date'              => $sales_invoice['date'],
				'coa_account_code'  => "10301",
				'transaction_id'    => $purchase_invoice_id,
				'invoice'     		=> $purchase_invoice_code,
				'information' 		=> 'PEMBELIAN',
				'note'		  		=> 'PEMBELIAN_'.$purchase_invoice_code.'_'.$supplier['name'],
				'debit'      		=> $coa_inventory_value,
				'balance'     		=> $balance
			];									
			if($this->crud->insert2('general_ledger', $data))
			{
				$where_after_balance = [
					'coa_account_code' => "10301",
					'date >'           => $sales_invoice['date'],
					'deleted'          => 0
				];
				$after_balance = $db2->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_balance  AS $info)
				{
					$this->crud->update2('general_ledger', ['balance' => add_balance($info['balance'], $coa_inventory_value)], ['id' => $info['id']]);
				}
			}
			// GENERAL LEDGER -> HUTANG USAHA (K)
			$where_last_balance = [
				'coa_account_code' => "20101",
				'date <='        => $sales_invoice['date'],                    
				'deleted'        => 0
			];
			$last_balance = $db2->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $grandtotal) : add_balance(0, $grandtotal);
			$data = [
				'date'        => $sales_invoice['date'],
				'coa_account_code'  => "20101",
				'transaction_id' => $purchase_invoice_id,
				'invoice'     => $purchase_invoice_code,
				'information' => 'PEMBELIAN',
				'note'		  => 'PEMBELIAN_'.$purchase_invoice_code.'_'.$supplier['name'],
				'credit'      => $grandtotal,
				'balance'     => $balance
			];									
			if($this->crud->insert2('general_ledger', $data))
			{
				$where_after_balance = [
					'coa_account_code'=> "20101",
					'date >'        => $sales_invoice['date'],
					'deleted'       => 0
				];
				$after_balance = $db2->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
				foreach($after_balance  AS $info)
				{
					$this->crud->update2('general_ledger', ['balance' => add_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
				}                            
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === TRUE)
			{
				$this->db->trans_commit();
			}
			else
			{
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'GAGAL! Pembelian gagal tersimpan');
				redirect(site_url('purchase'));
			}			
			if($purchase_invoice_id != null)
			{
				$this->db->trans_start();											
				// PAYMENT_LEDGER
				$pod_code = $this->payment->pod_code();
				$data_pod = [                        
					'transaction_type'=> 1,
					'code'           => $pod_code,
					'date'           => $sales_invoice['date'],
					'information'    => $sales_invoice['information'],
					'method'         => ($sales_invoice['cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
					'cash'           => ($sales_invoice['cl_type'] == 1) ? $grandtotal : 0,
					'transfer'       => ($sales_invoice['cl_type'] == 2) ? $grandtotal : 0,
					'grandtotal'     => $grandtotal,
					'employee_code'  => $this->session->userdata('code_e')
				];
				$pod_id = $this->crud->insert_id2('payment_ledger', $data_pod);
				if($pod_id != null)
				{									
					$data_pod_detail = [
						'pl_id'      => $pod_id,
						'method'     => ($sales_invoice['cl_type'] == 1) ? json_encode(["1"]) : json_encode(["2"]),
						'account_id' => $sales_invoice['account_id'],
						'amount'     => $grandtotal
					];
					$this->crud->insert2('payment_ledger_detail', $data_pod_detail);
					$data_pod_transaction = [
						'pl_id'      => $pod_id,
						'transaction_id'=> $purchase_invoice_id,										
						'cash'           => ($sales_invoice['cl_type'] == 1) ? $grandtotal : 0,
						'transfer'       => ($sales_invoice['cl_type'] == 2) ? $grandtotal : 0,
						'amount'     => $grandtotal
					];
					$this->crud->insert2('payment_ledger_transaction', $data_pod_transaction);
					// CASH_LEDGER
					$from_where_last_balance = [
						'cl_type'    => $sales_invoice['cl_type'],
						'account_id' => $sales_invoice['account_id'],
						'date <='    => $sales_invoice['date'],                    
						'deleted'    => 0
					];
					$from_last_balance = $db2->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$grandtotal : 0-$grandtotal;
					$data = [
						'cl_type'     => $sales_invoice['cl_type'],
						'account_id'  => $sales_invoice['account_id'],
						'transaction_id'   => $pod_id,
						'invoice'     => $pod_code,
						'information' => 'PEMBAYARAN PEMBELIAN',
						'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
						'date'        => $sales_invoice['date'],
						'amount'      => $grandtotal,
						'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
						'balance'     => $from_balance
					];									
					if($this->crud->insert2('cash_ledger', $data))
					{
						$from_where_after_balance = [
							'cl_type'       => $sales_invoice['cl_type'],
							'account_id'    => $sales_invoice['account_id'],
							'date >'        => $sales_invoice['date'],
							'deleted'       => 0
						];
						$from_after_balance = $db2->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($from_after_balance  AS $info)
						{
							$this->crud->update2('cash_ledger', ['balance' => $info['balance']-$grandtotal], ['id' => $info['id']]);
						}                            
					}
					// GENERAL_LEDGER -> KAS & BANK (K)
					$where_last_balance = [
						'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
						'date <='        => $sales_invoice['date'],                    
						'deleted'        => 0
					];
					$last_balance = $db2->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
					$data = [
						'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
						'date'        => $sales_invoice['date'],										
						'transaction_id' => $pod_id,
						'invoice'     => $pod_code,
						'information' => 'PEMBAYARAN PEMBELIAN',
						'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
						'credit'      => $grandtotal,
						'balance'     => $balance
					];									
					if($this->crud->insert2('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code' => ($sales_invoice['cl_type'] == 1) ? "10101" : "10102",
							'date >'        => $sales_invoice['date'],
							'deleted'       => 0
						];
						$after_balance = $db2->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update2('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
						}                            
					}
					// GENERAL LEDGER -> HUTANG USAHA (D)
					$where_last_balance = [
						'coa_account_code' => "20101",
						'date <='        => $sales_invoice['date'],                    
						'deleted'        => 0
					];
					$last_balance = $db2->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
					$data = [
						'coa_account_code'  => "20101",
						'date'        => $sales_invoice['date'],
						'transaction_id' => $pod_id,
						'invoice'     => $pod_code,
						'information' => 'PEMBAYARAN PEMBELIAN',
						'note'		  => 'PEMBAYARAN_PEMBELIAN_'.$pod_code.'_'.$supplier['name'],
						'debit'       => $grandtotal,
						'balance'     => $balance
					];									
					if($this->crud->insert2('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "20101",
							'date >'        => $sales_invoice['date'],
							'deleted'       => 0
						];
						$after_balance = $db2->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update2('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
						}
					}
				}
				// PURCHASE INVOICE DETAIL
				foreach($sales_invoice_detail AS $info)
				{
					$res = 0;
					$product_id = $this->crud->get_product_id2($info['product_code']);
					$qty	 = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);								
					$convert = $this->crud->get_where2('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
					$data_purchase_detail= [
						'purchase_invoice_id' => $purchase_invoice_id,
						'invoice'		=> $purchase_invoice_code,
						'product_id'	=> $product_id,
						'product_code'	=> $info['product_code'],											
						'qty'			=> $qty,
						'unit_id'		=> $info['unit_id'],
						'unit_value'    => isset($convert) ? $convert['value'] : 1,
						'warehouse_id'	=> $info['warehouse_id'],
						'price'			=> $price,
						'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
						'total'			=> $total,
						'ppn'			=> $ppn									
					];
					$purchase_invoice_detail_id = $this->crud->insert_id2('purchase_invoice_detail', $data_purchase_detail);
					if($purchase_invoice_detail_id != null)
					{
						// CONVERT QTY & PRICE
						$qty_convert = (isset($convert)) ? $qty*$convert['value'] : $qty;
						if($ppn == 0)
						{
							$price_convert = ($total/$qty_convert)+($delivery_cost/$sales_invoice['total_product']/$qty_convert);
						}
						elseif($ppn == 1)
						{
							if($price_include_tax == 0)
							{
								$price_convert = ($total/$qty_convert)+($delivery_cost/$sales_invoice['total_product']/$qty_convert);
							}
							elseif($price_include_tax == 1)
							{
								$price_convert = ($total/1.11/$qty_convert)+($delivery_cost/$sales_invoice['total_product']/$qty_convert);
							}										
						}									
						// STOCK
						$check_stock = $this->crud->get_where2('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
						if($check_stock->num_rows() == 1)
						{
							$data_stock = $check_stock->row_array();
							$where_stock = array(
								'product_code'  => $info['product_code'],
								'warehouse_id'  => $info['warehouse_id']
							);
							$stock = array(                                
								'product_id' => $product_id,
								'qty'        => $data_stock['qty']+$qty_convert,
							);
							$update_stock = $this->crud->update2('stock', $stock, $where_stock);
						}
						else
						{
							$stock = array(                                
								'product_id'    => $product_id,
								'product_code'  => $info['product_code'],                                                        
								'qty'           => $qty_convert,
								'warehouse_id'  => $info['warehouse_id']
							);
							$update_stock = $this->crud->insert2('stock', $stock);
						}                            
						if($update_stock)
						{
							// STOCK CARD
							$where_last_stock_card = [
								'date <='      => $sales_invoice['date'],
								'product_id'   => $product_id,																						
								'warehouse_id' => $info['warehouse_id'],
								'deleted'      => 0											
							];
							$last_stock_card = $db2->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$data_stock_card = array(
								'type'            => 1, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
								'information'     => 'PEMBELIAN',
								'note'			  => $supplier['name'],
								'date'            => $sales_invoice['date'],
								'transaction_id'  => $purchase_invoice_id,
								'invoice'         => $purchase_invoice_code,
								'product_id'      => $product_id,
								'product_code'    => $info['product_code'],
								'qty'             => $qty_convert,																						
								'method'          => 1, // 1:In, 2:Out
								'stock'           => $last_stock_card['stock']+$qty_convert,
								'warehouse_id'    => $info['warehouse_id'],
								'employee_code'   => $this->session->userdata('code_e')
							);
							$this->crud->insert2('stock_card',$data_stock_card);
							$where_after_stock_card = [
								'date >'       => $sales_invoice['date'],
								'product_id'   => $product_id,				
								'warehouse_id' => $info['warehouse_id'],
								'deleted'      => 0
							];                    
							$after_stock_card = $db2->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_stock_card  AS $info_after_stock_card)
							{
								$this->crud->update2('stock_card', ['stock' => $info_after_stock_card['stock']+$qty_convert], ['id' => $info_after_stock_card['id']]);
							}
							// STOCK MOVEMENT
							$where_last_stock_movement = [
								'product_id'   => $product_id,
								'date <='      => $sales_invoice['date'],
								'deleted'      => 0
							];
							$last_stock_movement = $db2->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$data_stock_movement = [
								'type'            => 1, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
								'information'     => 'PEMBELIAN',
								'note'			  => $supplier['name'],
								'date'            => $sales_invoice['date'],
								'transaction_id'  => $purchase_invoice_id,
								'invoice'         => $purchase_invoice_code,
								'product_id'      => $product_id,
								'product_code'    => $info['product_code'],
								'qty'             => $qty_convert,
								'method'          => 1, // 1:In, 2:Out
								'stock'           => $last_stock_movement['stock']+$qty_convert,
								'employee_code'   => $this->session->userdata('code_e')
							];
							$stock_movement_id = $this->crud->insert_id2('stock_movement', $data_stock_movement);
							$where_after_stock_movement = [
								'product_id'   => $product_id,
								'date >'       => $sales_invoice['date'],
								'deleted'      => 0
							];                    
							$after_stock_movement = $db2->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_stock_movement  AS $info_after_stock_movement)
							{
								$this->crud->update2('stock_movement', ['stock' => $info_after_stock_movement['stock']+$qty_convert], ['id' => $info_after_stock_movement['id']]);
							}
							// LAST BUYPRICE AND HPP
							// CHANGE HPP BY STOCK MOVEMENT
							// Find the last purchase
							$sub_where_last_purchase_invoice = [										
								'type'    => 1,
								'date <=' => $sales_invoice['date'],
								'product_code' => $info['product_code'],
								'deleted' => 0
							];
							$sub_last_purchase_invoice = $db2->select('*')->from('stock_movement')->where($sub_where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('transaction_id', 'DESC')->get_compiled_select();
							$where_last_purchase_invoice = [
								'transaction_id <' => $purchase_invoice_id
							];
							$last_purchase_invoice = $db2->select('*')->from("($sub_last_purchase_invoice) as result")->where($where_last_purchase_invoice)->order_by('date', 'DESC')->order_by('result.id', 'DESC')->limit(1)->get()->row_array();
							if($last_purchase_invoice == null)
							{
								$hpp=[
									'price' => $price_convert,
									'hpp' => $price_convert
								];
								$this->crud->update2('stock_movement', $hpp, ['id' => $stock_movement_id]);
								$last_hpp = $price_convert;
							}
							else
							{
								// (QTY STOCK YANG ADA * HARGA BELI TERAKHIR) + (qty baru * harga baru) / (qty lama + qty baru)
								$old_inventory_value = $last_purchase_invoice['hpp']*$last_purchase_invoice['stock'];
								$new_inventory_Value = $qty_convert*$price_convert;
								$hpp= [
									'price' => $total/$qty_convert,
									'hpp' => ($old_inventory_value+$new_inventory_Value)/($last_purchase_invoice['stock']+$qty_convert)
								];
								$this->crud->update2('stock_movement', $hpp, ['id' => $stock_movement_id]);
								$last_hpp = $hpp['hpp'];
							}																						
							if($after_stock_movement == null)
							{
								$update_detail_product = array(
									'hpp' => $last_hpp,
									'supplier_code' => $post['supplier_code'],
									'buyprice' => $total/$qty_convert
								);
								$this->crud->update2('product', $update_detail_product, ['code' => $info['product_code']]);																								
							}
							else
							{
								foreach($after_stock_movement  AS $info_after_stock_movement)
								{
									switch ($info_after_stock_movement['type']) {
										case 1:
											// (QTY STOCK YANG ADA * HARGA BELI TERAKHIR) + (qty baru * harga baru) / (qty lama + qty baru)
											$old_inventory_value = $last_hpp*($info_after_stock_movement['stock']+$qty_convert-$info_after_stock_movement['qty']);
											$new_inventory_Value = $info_after_stock_movement['qty']*$info_after_stock_movement['price'];
											// PURCHASE _INVOICE
											$update_detail_product = array(
												'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert),
												'supplier_code' => $post['supplier_code'],
												'buyprice' => $info_after_stock_movement['price']
											);
											$this->crud->update2('product', $update_detail_product, ['id' => $info_after_stock_movement['product_id']]);
											$hpp=[
												'hpp' => ($old_inventory_value+$new_inventory_Value)/($info_after_stock_movement['stock']+$qty_convert)
											];
											$this->crud->update2('stock_movement', $hpp, ['id' => $info_after_stock_movement['id']]);
											$last_hpp = $hpp['hpp'];
											break;
										case 4:
											// SALES INVOICE
											$where_sales_invoice_detail=[
												'sales_invoice_id' => $info_after_stock_movement['transaction_id'],
												'product_id'	   => $info_after_stock_movement['product_id']
											];
											$this->crud->update2('sales_invoice_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
											$this->crud->update2('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
											break;
										case 5:
											// SALES RETURN
											$where_sales_return_detail=[
												'sales_return_id' => $info_after_stock_movement['transaction_id'],
												'product_id'	   => $info_after_stock_movement['product_id']
											];
											$this->crud->update2('sales_return_detail', ['hpp' => $last_hpp], $where_sales_invoice_detail);
											$this->crud->update2('stock_movement', ['hpp' => $last_hpp], ['id' => $info_after_stock_movement['id']]);
											break;
										default:
											break;
									}
								}	
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
			}
			else
			{
				$res = 0;							
			}
		}
		$this->db->trans_complete();
	}

	public function cancel_sales_invoice_do()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$sales_invoice = $this->sales->get_detail_sales_invoice($post['sales_invoice_id']);
			$check_payment_ledger = $this->db->select('plt.id')
											->from('payment_ledger AS pl')
											->where('pl.transaction_type', 2)
											->join('payment_ledger_transaction AS plt', 'plt.pl_id = pl.id')
											->where('plt.transaction_id', $sales_invoice['id'])
											->group_by('pl.id')->get()->num_rows();
			if($check_payment_ledger != 0)
			{
				$this->session->set_flashdata('error', 'Cetak DO tidak dapat dibatalkan. Terdapat Data Pembayaran');
				$response   =   [
					'sales_invoice_id' => encrypt_custom($sales_invoice['id']),
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
			}	
			else
			{
				$sales_invoice_detail = $this->sales->get_detail_sales_invoice_detail($sales_invoice['id']);
				if($this->session->userdata('verifypassword') == 1)
				{
					$this->session->unset_userdata('verifypassword');
					// ALGORITHM
					/*					
					-GENERAL LEDGER -> PENJUALAN (D)
					-GENERAL LEDGER -> PPN KELUARAN (D)
					-GENERAL LEDGER -> PIUTANG USAHA (K)
					-GENERAL LEDGER -> PERSEDIAAN BARANG (D)
					-GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (K)
					-------------------------------			
					*/
					$this->db->trans_start();
					// GENERAL LEDGER -> PENJUALAN (D)
					$where_general_ledger = [
						'coa_account_code'  => "40101",
						'transaction_id'    => $sales_invoice['id'],
						'invoice'		    => $sales_invoice['invoice']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
					foreach($general_ledger AS $info_general_ledger)
					{
						$where_after_balance = [					
							'coa_account_code' => "40101",
							'date >='    => $info_general_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info)
						{
							if($info['date'] == $info_general_ledger['date'] && $info['id'] < $info_general_ledger['id'])
							{
								continue;
							}
							else
							{												
								$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $info_general_ledger['debit']+$info_general_ledger['credit'])], ['id' => $info['id']]);
							}
						}
						$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
					}
					// GENERAL LEDGER -> PPN KELUARAN (D)
					if($sales_invoice['ppn'] != 0)
					{						
						$where_general_ledger = [
							'coa_account_code'  => "20301",
							'transaction_id'    => $sales_invoice['id'],
							'invoice'		    => $sales_invoice['invoice']
						];
						$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
						foreach($general_ledger AS $info_general_ledger)
						{
							$where_after_balance = [					
								'coa_account_code' => "20301",
								'date >='    => $info_general_ledger['date'],
								'deleted'    => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance AS $info)
							{
								if($info['date'] == $info_general_ledger['date'] && $info['id'] < $info_general_ledger['id'])
								{
									continue;
								}
								else
								{												
									$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $info_general_ledger['debit']+$info_general_ledger['credit'])], ['id' => $info['id']]);
								}
							}
							$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
						}
					}	
					// GENERAL LEDGER -> PIUTANG USAHA (K)
					$where_general_ledger = [
						'coa_account_code'  => "10201",
						'transaction_id'    => $sales_invoice['id'],
						'invoice'		    => $sales_invoice['invoice']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
					foreach($general_ledger AS $info_general_ledger)
					{
						$where_after_balance = [					
							'coa_account_code' => "10201",
							'date >='    => $info_general_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info)
						{
							if($info['date'] == $info_general_ledger['date'] && $info['id'] < $info_general_ledger['id'])
							{
								continue;
							}
							else
							{
								if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
								{
									$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $info_general_ledger['debit']+$info_general_ledger['credit'])], ['id' => $info['id']]);
								}	
								elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
								{
									$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $info_general_ledger['debit']+$info_general_ledger['credit'])], ['id' => $info['id']]);
								}																	
							}
						}
						$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
					}
					// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
					$where_general_ledger = [
						'coa_account_code'  => "10301",
						'transaction_id'    => $sales_invoice['id'],
						'invoice'		    => $sales_invoice['invoice']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
					foreach($general_ledger AS $info_general_ledger)
					{
						$where_after_balance = [					
							'coa_account_code' => "10301",
							'date >='    => $info_general_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info)
						{
							if($info['date'] == $info_general_ledger['date'] && $info['id'] < $info_general_ledger['id'])
							{
								continue;
							}
							else
							{												
								$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $info_general_ledger['debit']+$info_general_ledger['credit'])], ['id' => $info['id']]);
							}
						}
						$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
					}
					// GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (K)
					$where_general_ledger = [
						'coa_account_code'  => "50001",
						'transaction_id'    => $sales_invoice['id'],
						'invoice'		    => $sales_invoice['invoice']
					];
					$general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
					foreach($general_ledger AS $info_general_ledger)
					{
						$where_after_balance = [					
							'coa_account_code' => "50001",
							'date >='    => $info_general_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info)
						{
							if($info['date'] == $info_general_ledger['date'] && $info['id'] < $info_general_ledger['id'])
							{
								continue;
							}
							else
							{												
								$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $info_general_ledger['debit']+$info_general_ledger['credit'])], ['id' => $info['id']]);
							}
						}
						$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
					}
					$this->db->trans_complete();
					if($this->db->trans_status() === TRUE)
					{
						$this->db->trans_commit();
						// NEW TRANSACTION
						$this->db->trans_start();
						// SALES INVOICE DETAIL			
						foreach($sales_invoice_detail AS $info)
						{					
							// ADD STOCK
							$where_stock = [
								'product_code'	=> $info['product_code'],
								'warehouse_id'	=> $info['warehouse_id']
							];
							$stock = $this->crud->get_where('stock', $where_stock)->row_array();
							$update_stock = [
								'qty' => $stock['qty'] + ($info['qty']*$info['unit_value'])
							];
							$this->crud->update('stock', $update_stock, $where_stock);
							// UPDATE AND DELETE STOCK CARD
							$where_stock_card = [
								'transaction_id' => $sales_invoice['id'],
								'transaction_detail_id' => $info['id'],
								'product_code'	 => $info['product_code'],
								'type'			 => 4,
								'method'		 => 2,
								'warehouse_id'	 => $info['warehouse_id']
							];
							$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
							$where_after_stock_card = [
								'date >='       => $stock_card['date'],
								'product_code'	=> $info['product_code'],
								'warehouse_id'	=> $info['warehouse_id'],
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
										'stock' => $info_stock_card['stock']+($info['qty']*$info['unit_value'])
									];
									$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
								}										
							}
							$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
							// UPDATE AND DELETE STOCK MOVEMENT
							$where_stock_movement = [
								'transaction_id' => $sales_invoice['id'],
								'transaction_detail_id' => $info['id'],
								'product_code'	 => $info['product_code'],
								'type'			 => 4,
								'method'		 => 2
							];								
							$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
							$where_after_stock_movement = [
								'date >='       => $stock_movement['date'],
								'product_code'	=> $info['product_code'],
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
										'stock' => $info_stock_movement['stock']+($info['qty']*$info['unit_value'])
									];
									$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
								}
							}
							$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);
						}			
						$this->crud->update('sales_invoice', ['do_status' => 0], ['id' => $sales_invoice['id']]);
						$this->db->trans_complete();
						if($this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
							$data_activity = [
								'information' => 'MEMBATALKAN DO PENJUALAN (NO.TRANSAKSI '.$sales_invoice['invoice'].')',
								'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
								'code_e'      => $this->session->userdata('code_e'),
								'name_e'      => $this->session->userdata('name_e'),
								'user_id'     => $this->session->userdata('id_u')
							];						
							$this->crud->insert('activity', $data_activity);
							$this->session->set_flashdata('success', 'DO Penjualan berhasil dibatalkan');				
							$response   =   [
								'sales_invoice_id' => encrypt_custom($sales_invoice['id']),
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
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'GAGAL! Batal Cetak DO Gagal');
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
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}			
	}

	public function print_sales_invoice_do($sales_invoice_id)
	{
		if($this->session->userdata('create_sales_invoice_do') == 1)
		{
			$this->session->unset_userdata('create_sales_invoice_do');
			$sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));
			$customer      = $this->crud->get_where('customer', ['code' => $sales_invoice['customer_code']])->row_array();
			$warehouse     = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
								->from('sales_invoice_detail')->join('warehouse', 'warehouse.id = sales_invoice_detail.warehouse_id')								
								->where('sales_invoice_detail.sales_invoice_id', $sales_invoice['id'])
								->where('warehouse.deleted', 0)->where('sales_invoice_detail.deleted', 0)
								->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
			foreach($warehouse AS $info_w)
			{
				$data_so = $this->db->select('sales_invoice.invoice')
									->from('sales_invoice')->join('sales_invoice_detail', 'sales_invoice_detail.sales_invoice_id = sales_invoice.id')
									->where('sales_invoice_detail.warehouse_id', $info_w['id_w'])
									->where('sales_invoice_detail.sales_invoice_id', $sales_invoice['id'])
									->where('sales_invoice_detail.deleted', 0)
									->group_by('sales_invoice.id')->order_by('sales_invoice.invoice', 'asc')->get()->result_array();
				$product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, sales_invoice_detail.qty AS qty, unit.code AS code_u')
								->from('sales_invoice_detail')->join('product', 'product.id = sales_invoice_detail.product_id')
								->where('sales_invoice_detail.warehouse_id', $info_w['id_w'])
								->where('sales_invoice_detail.sales_invoice_id', $sales_invoice['id'])
								->where('product.deleted', 0)->where('sales_invoice_detail.deleted', 0)
								->join('unit', 'unit.id = sales_invoice_detail.unit_id')
								->group_by('sales_invoice_detail.id')->order_by('product_code', 'asc')->get()->result_array();
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
					'code_sot' 	 => $sales_invoice['invoice'],
					'data_so'    => $data_so,
					'id_w' 		 => $info_w['id_w'],
					'code_w' 	 => $info_w['code_w'],
					'name_w' 	 => $info_w['name_w'],
					'product'	 => $data_product
				);
			}		
			$data = array(
				'perusahaan' => $this->global->company(),
				'sales_invoice' => $sales_invoice,
				'customer'    => $customer,
				'sot'        => $sot
			);
			$this->load->view('sales/invoice/print_sales_invoice_do', $data);
		}
		else
		{

		}		
	}

	public function datatable_detail_sales_invoice($sales_invoice_id)
	{
        header('Content-Type: application/json');
		$this->datatables->select('sales_invoice_detail.id AS id, product.code AS code_p, product.name AS name_p, unit.name AS name_u, warehouse.name AS name_w, sales_invoice_detail.qty, sales_invoice_detail.price, sales_invoice_detail.disc_product, sales_invoice_detail.total,
						 sellprice.price_1, sellprice.price_2, sellprice.price_3, sellprice.price_4, sellprice.price_5,
						 product.code AS search_code_p')
						 ->from('sales_invoice_detail')
						 ->join('product', 'product.code = sales_invoice_detail.product_code')
						 ->join('unit', 'unit.id = sales_invoice_detail.unit_id')
						 ->join('sellprice', 'sellprice.product_id = sales_invoice_detail.product_id AND sellprice.unit_id = sales_invoice_detail.unit_id', 'left')
						 ->join('warehouse', 'warehouse.id = sales_invoice_detail.warehouse_id')
						 ->where('sales_invoice_detail.sales_invoice_id', $sales_invoice_id)
						 ->group_by('sales_invoice_detail.id');
		$this->datatables->add_column('code_p',
		'
			<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/$1').'"><b>$2</b></a>
		', 'encrypt_custom(code_p),code_p');
        echo $this->datatables->generate();
	}

	public function datatable_detail_sales_invoice_payment($sales_invoice_id)
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();		
			header('Content-Type: application/json');
			$this->datatables->select('por.id AS id, por.code AS code_por, por.date AS date, por_transaction.amount AS amount,
							   por.code AS search_code_por');
			$this->datatables->from('payment_ledger AS por');
			$this->datatables->join('payment_ledger_transaction AS por_transaction', 'por_transaction.pl_id = por.id');
			$this->datatables->where('por.transaction_type', 2);
			$this->datatables->where('por_transaction.transaction_id', $sales_invoice_id);
			$this->datatables->where('por.deleted', 0);
			$this->datatables->group_by('por.id');
			$this->datatables->order_by('por.date', 'DESC');
			$this->datatables->order_by('por.id', 'DESC');
			$this->datatables->add_column('code_por',
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id), code_por');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}
	
	public function datatable_detail_sales_invoice_sales_return($sales_invoice_id) 
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('sales_return.id AS id_sr, sales_return.code AS code_sr, sales_return.date, total_return,
							sales_return.code AS search_code');
			$this->datatables->from('sales_return');
			$this->datatables->where('sales_return.method', 2);
			$this->datatables->where('sales_return.do_status', 1);
			$this->datatables->where('sales_return.deleted', 0);			
			$this->datatables->where('sales_return.sales_invoice_id', $sales_invoice_id);
			$this->datatables->group_by('sales_return.id');
			$this->datatables->order_by('sales_return.date', 'DESC');
			$this->datatables->order_by('sales_return.id', 'DESC');
			$this->datatables->add_column('code_sr', 
			'
				<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/return/detail/$1').'"><b>$2</b></a>
			', 'encrypt_custom(id_sr) ,code_sr');                
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function datatable_detail_sales_invoice_tax_invoice($sales_invoice_id) 
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('id, date, number, dpp, ppn, grandtotal,
							number AS search_number');
			$this->datatables->from('tax_invoice');
			$this->datatables->where('transaction_type', 2);
			$this->datatables->where('transaction_id', $sales_invoice_id);
			$this->datatables->order_by('date', 'DESC');
			$this->datatables->order_by('id', 'DESC');
			$this->datatables->add_column('action', 
			'
				<a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
					<i class="fa fa-times"></i>
				</a>            
			', 'id');
			echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function detail_sales_invoice($sales_invoice_id)
    {
		if($this->system->check_access('sales/invoice','detail'))
		{
			$sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));			
			if($sales_invoice != null)
			{
				$data_activity = [
					'information' => 'MELIHAT DETAIL PENJUALAN (NO.TRANSAKSI '.$sales_invoice['invoice'].')',
					'method'      => 2, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];
				$this->crud->insert('activity', $data_activity);
				if($sales_invoice['payment'] == 1)
				{
					
				}
				elseif($sales_invoice['payment'] == 2 && $sales_invoice['down_payment'] > 0)
				{

				}
				$header = array("title" => "Detail Penjualan");
				$data = [
					'sales_invoice' => $sales_invoice
				];	
				$footer = array("script" => ['transaction/sales/invoice/detail_sales_invoice.js']);					
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/invoice/detail_sales_invoice', $data);        
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

	public function create_sales_tax_invoice()
	{
		if(true)
        {
            $post   = $this->input->post();
			$data = [
				'transaction_type' => 2,
				'transaction_id' => $post['sales_invoice_id'],
				'date'   => date('Y-m-d', strtotime($post['date'])),
				'number' => $post['number'],
				'dpp'    => format_amount($post['dpp']),
				'ppn'    => format_amount($post['ppn']),
				'grandtotal' => format_amount($post['dpp'])+format_amount($post['ppn'])
			];                			
			if($this->crud->insert('tax_invoice', $data))
			{
				$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $post['sales_invoice_id']])->row_array();
				$data_activity = [
					'information' => 'MEMBUAT FAKTUR PAJAK PENJUALAN (NO.TRANSAKSI '.$sales_invoice['invoice'].')',
					'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);

				$response = [
					'status' => [
						'code'      => 200,
						'message'   => 'Berhasil Menambahkan Data',
					],
					'response'  => ''
				];         
				$this->session->set_flashdata('success', 'BERHASIL! Faktur Pajak berhasil ditambahkan');           
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
				$this->session->set_flashdata('error', 'Mohon maaf, Faktur Pajak gagal ditambahkan');
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

	public function delete_sales_tax_invoice()
	{
		if(true)
        {
			$post   = $this->input->post();			              			
			$purchase_tax_invoice = $this->crud->get_where('tax_invoice', ['id' => $post['id']])->row_array();
			if($this->crud->delete('tax_invoice', ['id' => $post['id']]))
			{				
				$data_activity = [
					'information' => 'MENGHAPUS FAKTUR PAJAK PEMBELIAN (NO.TRANSAKSI '.$purchase_tax_invoice['number'].')',
					'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
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
					'response'  => '',
				];
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
	
	public function update_sales_invoice($sales_invoice_id)
    {
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$sales_invoice        = $this->sales->get_detail_sales_invoice(decrypt_custom($post['sales_invoice_id']));
			$sales_invoice_detail = $this->sales->get_detail_sales_invoice_detail($sales_invoice['id']);
			$this->form_validation->set_rules('date', 'Tanggal Invoice', 'trim|required|xss_clean');
			$this->form_validation->set_rules('sales_code', 'Sales', 'trim|required|xss_clean');
			$this->form_validation->set_rules('customer_code', 'Konsumen', 'trim|required|xss_clean');
			$this->form_validation->set_rules('payment', 'Jenis Pembayaran', 'trim|required|xss_clean');		
			if($this->input->post('payment') == 2)
			{
				$this->form_validation->set_rules('payment_due', 'Jatuh Tempo', 'trim|required|xss_clean');
			}
			$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|required|xss_clean');
			$this->form_validation->set_rules('total_qty', 'Total Qty', 'trim|required|xss_clean');
			$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|xss_clean');
			$this->form_validation->set_rules('down_payment', 'Uang Muka Pembayaran', 'trim|xss_clean');
			$this->form_validation->set_rules('account_payable', 'Hutang Dagang', 'trim|required|xss_clean');
			$this->form_validation->set_rules('discount_p', 'Diskon (%)', 'trim|required|xss_clean');
			$this->form_validation->set_rules('discount_rp', 'Diskon (Rp)', 'trim|required|xss_clean');
			$this->form_validation->set_rules('grandtotal', 'grandtotal', 'trim|required|xss_clean');				
			if($this->form_validation->run() == FALSE)
			{
				$header = array("title" => "Perbarui Penjualan");
				$data = [
					'sales_invoice' => $sales_invoice,
					'sales_invoice_detail' => $sales_invoice_detail
				];
				$footer = array("script" => ['transaction/sales/invoice/update_sales_invoice.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/invoice/update_sales_invoice', $data);
				$this->load->view('include/footer', $footer);
			}
			else
			{
				$customer 			= $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
				$plus 				= $post['payment_due'];
				$ppn				= (!isset($post['ppn'])) ?  0 : $post['ppn'];
				$total_price  		= format_amount($post['subtotal']);
				$discount_rp 		= format_amount($post['discount_rp']);
				$grandtotal  		= format_amount($post['grandtotal']);
				$down_payment  		= format_amount($post['down_payment']);
				$account_payable	= format_amount($post['account_payable']);
				$data_sales_invoice=array(
					'date'				=> format_date($post['date']),						
					'sales_code'		=> $post['sales_code'],
					'customer_code'		=> $post['customer_code'],						
					'payment'			=> $post['payment'],
					'cl_type'    		=> isset($post['from_cl_type']) ? $post['from_cl_type'] : null,
					'account_id' 		=> isset($post['from_account_id']) ? $post['from_account_id'] : null,
					'payment_due'		=> $post['payment_due'],
					'information'		=> $post['information'],
					'total_product'		=> $post['total_product'],
					'total_qty'			=> $post['total_qty'],
					'total_price'		=> $total_price,
					'discount_p'		=> $post['discount_p'],
					'discount_rp'		=> $discount_rp,
					'ppn'				=> $ppn,
					'grandtotal'		=> $grandtotal,
					'down_payment'		=> $down_payment,
					'account_payable'	=> $account_payable,
					'payment_status'	=> $post['payment'],
					'due_date'          => date('Y-m-d',strtotime(format_date($post['date']) . "+$plus days"))
				);
				// UPDATE SALES INVOICE
				if($this->crud->update('sales_invoice', $data_sales_invoice, ['id' => $sales_invoice['id']]))
				{
					// CHECK DO STATUS
					if($sales_invoice['do_status'] == 1 && $sales_invoice['grandtotal'] != $grandtotal)
					{
						// DELETE OLD CASH LEDGER
						$where_cash_ledger = [
							'transaction_type'=> 5,
							'transaction_id'  => $sales_invoice['id'],
							'invoice'		  => $sales_invoice['invoice']
						];
						$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->result_array();
						if($cash_ledger)
						{
							foreach($cash_ledger AS $info_cash_ledger)
							{
								$where_after_balance = [
									'cl_type'    => $info_cash_ledger['cl_type'],
									'account_id' => $info_cash_ledger['account_id'],
									'date >='    => $info_cash_ledger['date'],                
									'deleted'    => 0
								];
								$data   = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($data AS $info)
								{
									if($info['date'] == $info_cash_ledger['date'] && $info['id'] < $info_cash_ledger['id'])
									{
										continue;
									}
									else
									{
										if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
										{
											$balance = $info['balance'] - $info_cash_ledger['amount'];
										}
										else
										{
											$balance = $info['balance'] + $info_cash_ledger['amount'];
										}
										$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
									}
								}
								$this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
							}
						}
						// NEW CASH LEDGER
						if($post['payment'] == 1)
						{
							$from_where_last_balance = [
								'cl_type'    => $post['from_cl_type'],
								'account_id' => $post['from_account_id'],
								'date <='    => format_date($post['date']),                    
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$grandtotal : 0+$grandtotal;
							$data = [
								'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
								'account_id'  => $post['from_account_id'],
								'transaction_type' => 5,// 1:DEPOSIT, 2:CASH MUTATION, 3:PURCHASE, 4:PURCHASE RETURN, 5:SALES INVOICE, 6:SALES RETURN									
								'transaction_id' => $sales_invoice['id'],
								'invoice'     => $sales_invoice['invoice'],
								'information' => 'PENJUALAN (TUNAI)',
								'date'        => format_date($post['date']),
								'amount'      => $grandtotal,
								'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'    => $post['from_cl_type'],
									'account_id' => $post['from_account_id'],
									'date >'     => format_date($post['date']),
									'deleted'    => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{                        
									$balance = $info['balance'] + $grandtotal;
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}                            
							}
						}
						else if($post['payment'] == 2 && $down_payment > 0)
						{
							$from_where_last_balance = [
								'cl_type'    => $post['from_cl_type'],
								'account_id' => $post['from_account_id'],
								'date <='    => format_date($post['date']),                    
								'deleted'    => 0
							];
							$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
							$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+$down_payment : 0+$down_payment;
							$data = [
								'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
								'account_id'  => $post['from_account_id'],
								'transaction_type' => 5,// 1:DEPOSIT, 2:CASH MUTATION, 3:PURCHASE, 4:PURCHASE RETURN, 5:SALES INVOICE, 6:SALES RETURN									
								'transaction_id'   => $sales_invoice['id'],
								'invoice'     => $sales_invoice['invoice'],
								'information' => 'PENJUALAN (UANG MUKA)',
								'date'        => format_date($post['date']),
								'amount'      => $down_payment,
								'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
								'balance'     => $from_balance
							];
							$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
							if($from_cl_id)
							{
								$from_where_after_balance = [
									'cl_type'       => $post['from_cl_type'],
									'account_id'    => $post['from_account_id'],
									'date >'        => format_date($post['date']),
									'deleted'       => 0
								];                    
								$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
								foreach($from_after_balance  AS $info)
								{                        
									$balance = $info['balance'] + $down_payment;
									$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
								}                            
							}
						}
					}	
					elseif($sales_invoice['do_status'] == 0)
					{
						// LIST OLD PRODUCT, AND NEW PRODUCT
						$old_sales_invoice_detail_id = []; $new_sales_invoice_detail_id = [];
						foreach($sales_invoice_detail AS $info_old_product)
						{
							$old_sales_invoice_detail_id[] = $info_old_product['id'];
						}
						foreach($post['product'] AS $info_new_product)
						{
							$new_sales_invoice_detail_id[] = isset($info_new_product['sales_invoice_detail_id']) ? $info_new_product['sales_invoice_detail_id'] : null;
						}
						// CHECK AND DELETE OLD PRODUCT WHERE NOT LISTED IN NEW LIST PRODUCT						
						foreach($sales_invoice_detail AS $info_old_product)
						{
							if(in_array($info_old_product['id'], $new_sales_invoice_detail_id))
							{
								continue;
							}
							else
							{
								// DELETE SALES INVOICE DETAIL ID
								$where_sales_invoice_detail = [
									'id'	=> $info_old_product['id']
								];
								$this->crud->delete('sales_invoice_detail', $where_sales_invoice_detail);
							}
						}
					}
					$total_hpp = 0;
					foreach($post['product'] AS $info)
					{
						// SKIP THE FIRST PRODUCT, BECAUSE IS TEMPLATE
						if($info['product_code'] == ""  && $info['qty'] == "" && $info['price'] == "" && $info['total'] == "")
						{																	
							continue;
						}
						else
						{									
							if(isset($info['sales_invoice_detail_id'])) // IF OLD PRODUCT
							{								
								$i = array_search($info['sales_invoice_detail_id'], array_column($sales_invoice_detail, 'id'));
								$info['unit_id'] = isset($info['unit_id']) ? $info['unit_id'] : $sales_invoice_detail[$i]['unit_id'];
								$info['warehouse_id'] = isset($info['warehouse_id']) ? $info['warehouse_id'] : $sales_invoice_detail[$i]['warehouse_id'];
								if($info['qty'] == $sales_invoice_detail[$i]['qty'] && $info['unit_id'] == $sales_invoice_detail[$i]['unit_id'] && $info['warehouse_id'] == $sales_invoice_detail[$i]['warehouse_id'])
								{
									$total_hpp = $total_hpp + ($sales_invoice_detail[$i]['qty']*$sales_invoice_detail[$i]['unit_value'])*$sales_invoice_detail[$i]['hpp'];
									$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
									// UPDATE SALES INVOICE DETAIL
									$data_sales_invoice_detail=array(
										'price'			=> $price,
										'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
										'ppn'			=> $ppn,
										'total'			=> $total
									);
									$this->crud->update('sales_invoice_detail', $data_sales_invoice_detail, ['id' => $info['sales_invoice_detail_id']]);
									$res = 1;
								}
								else
								{
									$product_id = $this->crud->get_product_id($info['product_code']);
									$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
									$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();									
									$total_hpp = $total_hpp + ($sales_invoice_detail[$i]['hpp']*$qty*$convert['value']);										

									// UPDATE SALES INVOICE DETAIL
									$data_sales_invoice_detail=array(
										'qty'			=> $qty,
										'unit_id'		=> $info['unit_id'],
										'unit_value'    => ($convert['value'] != null) ? $convert['value'] : 1,
										'price'			=> $price,
										'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
										'warehouse_id'	=> $info['warehouse_id'],
										'total'			=> $total,
										'ppn'			=> $ppn										
									);
									$this->crud->update('sales_invoice_detail', $data_sales_invoice_detail, ['id' => $info['sales_invoice_detail_id']]);
									$res = 1;
								}											
							}
							else // IF NEW PRODUCT
							{
								$product_id = $this->crud->get_product_id($info['product_code']);
								$qty     = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
								$hpp     = $this->product->hpp($info['product_code']);
								$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
								$data_sales_invoice_detail=array(
									'sales_invoice_id' => $sales_invoice['id'],
									'invoice'		=> $sales_invoice['invoice'],
									'product_id'	=> $product_id,
									'product_code'	=> $info['product_code'],
									'qty'			=> $qty,
									'unit_id'		=> $info['unit_id'],
									'unit_value'    => ($convert['value'] != null) ? $convert['value'] : 1,
									'price'			=> $price,
									'disc_product'	=> ($info['disc_product'] != "" || $info['disc_product'] != null) ? $info['disc_product'] : 0,
									'hpp'			=> $hpp,
									'warehouse_id'	=> $info['warehouse_id'],
									'ppn'			=> $ppn,
									'total'			=> $total
									
								);
								if($this->crud->insert('sales_invoice_detail', $data_sales_invoice_detail))
								{									
									$total_hpp   = $total_hpp + ($hpp*$qty*$convert['value']);
									$res = 1;
									continue;
								}
								else
								{
									break;
								}									
							}
						}
					}
					
					// UPDATE TOTAL HPP SALES INVOICE
					$this->crud->update('sales_invoice', ['total_hpp' => $total_hpp], ['id' => $sales_invoice['id']]);

					$data_activity = [
						'information' => 'MEMPERBARUI PENJUALAN (NO.TRANSAKSI '.$sales_invoice['invoice'].')',
						'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);
				}
				else
				{
					$res = 0;
				}

				if($res == 1)
				{
					$this->session->set_flashdata('success', 'Transaksi Penjualan berhasil diperbarui');
				}
				else
				{
					$this->session->set_flashdata('error', 'Transaksi Penjualan gagal diperbarui');
				}												
				redirect(site_url('sales/invoice/detail/'.encrypt_custom($sales_invoice['id'])));
			}
		}
		else
		{
			if(true)
			{
				$this->session->unset_userdata('verifypassword');
				$sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));
				$header = array("title" => "Perbarui Penjualan");
				$data = [
					'sales_invoice' => $sales_invoice,
					'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
				];
				$footer = array("script" => ['transaction/sales/invoice/update_sales_invoice.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/invoice/update_sales_invoice', $data);
				$this->load->view('include/footer', $footer);
				// $sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));
				// if($sales_invoice['payment'] == 1)
				// {
				// 	$where_cash_ledger = [
				// 		'transaction_type' => 5,
				// 		'transaction_id' => $sales_invoice['id'],
				// 		'information'	=> "PEMBAYARAN PENJUALAN (TUNAI)"
				// 	];
				// 	$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->row_array();
				// 	$header = array("title" => "Perbarui Penjualan");
				// 	$data = [
				// 		'sales_invoice' => $sales_invoice,
				// 		'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id']),
				// 		'cash_ledger' => $cash_ledger
				// 	];
				// 	$footer = array("script" => ['transaction/sales/invoice/update_sales_invoice.js']);
				// 	$this->load->view('include/header', $header);
				// 	$this->load->view('include/menubar');
				// 	$this->load->view('include/topbar');
				// 	$this->load->view('sales/invoice/update_sales_invoice', $data);
				// 	$this->load->view('include/footer', $footer);										
				// }
				// else
				// {
				// 	$where_payment_ledger = [
				// 		'transaction_type' => 2, // 1:PURCHASE, 2: SALES_INVOICE
				// 		'transaction_id' => $sales_invoice['id'],
				// 	];
				// 	$payment_ledger = $this->crud->get_where('payment_ledger', $where_payment_ledger)->num_rows();
				// 	if($payment_ledger > 0 )
				// 	{
				// 		$this->session->set_flashdata('error', 'Mohon maaf, transaksi tidak dapat dirubah karena sudah terdapat pembayaran. Terima kasih');
				// 		redirect(urldecode($this->agent->referrer()));
				// 	}
				// 	else
				// 	{
				// 		$where_cash_ledger = [
				// 			'transaction_type' => 5, //1:DEPOSIT, 2:CASH MUTATION, 3:PURCHASE, 4:PURCHASE RETURN, 5:SALES INVOICE, 6:SALES RETURN, 7:EXPENSE
				// 			'transaction_id' => $sales_invoice['id'],
				// 			'information'	=> "PENJUALAN (UANG MUKA)"
				// 		];
				// 		$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->row_array();
				// 		$header = array("title" => "Perbarui Penjualan");
				// 		$data = [
				// 			'sales_invoice' => $sales_invoice,
				// 			'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id']),
				// 			'cash_ledger' => $cash_ledger
				// 		];
				// 		$footer = array("script" => ['transaction/sales/invoice/update_sales_invoice.js']);
				// 		$this->load->view('include/header', $header);
				// 		$this->load->view('include/menubar');
				// 		$this->load->view('include/topbar');
				// 		$this->load->view('sales/invoice/update_sales_invoice', $data);
				// 		$this->load->view('include/footer', $footer);																
				// 	}
				// }
			}
			else
			{
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
				redirect(urldecode($this->agent->referrer()));
			}				
		}			
	}

	public function delete_sales_invoice()
	{
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post 		 = $this->input->post();
			$sales_invoice = $this->sales->get_detail_sales_invoice($post['sales_invoice_id']);
			// DELETE SALES INVOICE DETAIL
			$this->crud->delete('sales_invoice_detail', ['sales_invoice_id' => $sales_invoice['id']]);			
			// DELETE SALES INVOICE
			$this->crud->delete('sales_invoice', ['id' => $sales_invoice['id']]);
			$data_activity = [
				'information' => 'MENGHAPUS PENJUALAN (NO.TRANSAKSI '.$sales_invoice['invoice'].')',
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
			$this->session->set_flashdata('success', 'BERHASIL! Penjualan Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}								
	}

    public function print_sales_invoice_non($sales_invoice_id)
	{
		if($this->system->check_access('sales/invoice','create'))
		{
			$sales_invoice_id = $this->global->decrypt($sales_invoice_id);			
			$sales_invoice    = $this->sales->get_detail_sales_invoice($sales_invoice_id);
			$data = array(
				'perusahaan'	=> $this->global->company(),
				'sales_invoice' => $sales_invoice,
				'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
			);	
			$mpdf = new \Mpdf\Mpdf([
                    'format' => [210, 139.7],
					'orientation' => 'P',
					'margin_left' => 5,
					'margin_right' => 5,
					'margin_top' => 20,
					'margin_bottom' => 30,
					'margin_header' => 7,
					'margin_footer' => 5,
                    'setAutoBottomMargin' => 'stretch'
                ]);
            $mpdf->SetHTMLHeader('
                <div style="font-weight: bold; font-size:12px;">
                    NOTA PENJUALAN
                </div>
                <table style="width:100%; font-size:12px;">
                    <tbody>
                        <tr>
                            <td width="12%">Tgl. Transaksi</td>
                            <td width="20%">: '.date('d-m-Y', strtotime($sales_invoice['date'])).'</td>
                            <td>Sales</td>
                            <td width="20%">:'.$sales_invoice['name_s'].'</td>
                            <td>Pelanggan</td>
                            <td>: '.$sales_invoice['code_c'].' | '.$sales_invoice['name_c'].'</td>
                        </tr>
                        <tr>
                            <td>No. Transaksi</td>
                            <td>: '.$sales_invoice['invoice'].'</td>
                            <td>Operator</td>
                            <td>: '.$sales_invoice['name_e'].'</td>
                            <td>Alamat</td>
                            <td>: '.$sales_invoice['address_c'].'</td>
                        </tr>
                    </tbody>
                </table>
            ');
            $mpdf->SetHTMLFooter('
                <table width="100%">
                    <tr>
                        <td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
                        <td align="center">{PAGENO}/{nbpg}</td>
                    </tr>
                </table>'
            );
            $mpdf->DefHTMLFooterByName(
                  'LastPageFooter',
                  '
                    <table style="width:25%; text-align:center;" border="0">
                        <tr>
                            <td>ADMIN</td>
                            <td>GUDANG</td>
                            <td>PENERIMA</td>
                        </tr>
                        <tr>
                            <td style="height:50px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td><p>(___________________________)</p></td>
                            <td><p>(___________________________)</p></td>
                            <td><p>(___________________________)</p></td>
                        </tr>
                        <tr>
                            <td style="height:5px;">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
                            <td align="center">{PAGENO}/{nbpg}</td>
                        </tr>
                    </table>
                  '
            );
			$data = $this->load->view('sales/invoice/print_sales_invoice_non', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/invoice'));
		}
	}
	
	public function print_sales_invoice_ppn($sales_invoice_id)
	{
		if($this->system->check_access('sales/invoice','create'))
		{
			$sales_invoice_id = $this->global->decrypt($sales_invoice_id);			
			$sales_invoice    = $this->sales->get_detail_sales_invoice($sales_invoice_id);
			$payment = ($sales_invoice['payment'] == 1) ? 'TUNAI' : 'KREDIT';
			$data = array(
				'perusahaan'	=> $this->global->company(),
				'sales_invoice' => $sales_invoice,
				'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
			);	
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 139.7],
				'orientation' => 'P',
				'margin_left' => 5,
				'margin_right' => 5,
				'margin_top' => 25,
				'margin_bottom' => 25,
				'margin_header' => 7,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
            $mpdf->SetHTMLHeader('
                <div style="font-size:14px;">
                    CV.BERKAH MULYA | BUKTI TITIPAN | NO. '.$sales_invoice['invoice'].' | '.date('d-m-Y', strtotime($sales_invoice['date'])).'
                </div>
                <table style="width:100%; font-size:12x;">
                    <tbody>
                        <tr>
                            <td width="20%">PEMBAYARAN</td>
                            <td>: '.$payment.'</td>
                            <td width="15%">SALES</td>
                            <td>: '.$sales_invoice['name_s'].'</td>
                            <td width="15%">Pelanggan</td>
                            <td>: '.$sales_invoice['name_c'].'</td>
                        </tr>
                        <tr>
                            <td>JTH. TEMPO ('.$sales_invoice['payment_due'].') Hari</td>
                            <td>: '.date('d-m-Y', strtotime($sales_invoice['due_date'])).'</td>
                            <td>OPT</td>
                            <td>: '.$sales_invoice['name_e'].'</td>
                            <td>Alamat</td>
                            <td>: '.$sales_invoice['address_c'].'</td>
                        </tr>
                    </tbody>
                </table>
            ');
            $mpdf->SetHTMLFooter(
                '<table style="width:100%; text-align:center;" border="0">
                    <tr>
                        <td>HORMAT KAMI</td>
                        <td>CHECKER</td>
                        <td>PENERIMA</td>
                        <td><small>(barang dianggap sebagai titipan apabila belum lunas)</small></td>
                    </tr>
                    <tr>
                        <td style="height:35px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><p>(___________________________)</p></td>
                        <td><p>(___________________________)</p></td>
                        <td><p>(___________________________)</p></td>
                    </tr>
                    <tr>
                        <td style="height:5px;">&nbsp;</td>
                    </tr>
                </table>
                <table width="100%">
                <tr>
                    <td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
                    <td align="center">{PAGENO}/{nbpg}</td>
                </tr>
                </table>'
            );
            // $mpdf->DefHTMLFooterByName(
            //       'LastPageFooter',
            //       '
            //         <table style="width:25%; text-align:center;" border="0">
            //             <tr>
            //                 <td>HORMAT KAMI</td>
            //                 <td>PENERIMA</td>
            //             </tr>
            //             <tr>
            //                 <td style="height:50px;">&nbsp;</td>
            //             </tr>
            //             <tr>
            //                 <td><p>(___________________________)</p></td>
            //                 <td><p>(___________________________)</p></td>
            //             </tr>
            //             <tr>
            //                 <td style="height:5px;">&nbsp;</td>
            //             </tr>
            //         </table>
            //         <table width="100%">
            //             <tr>
            //                 <td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
            //                 <td align="center">{PAGENO}/{nbpg}</td>
            //             </tr>
            //         </table>
            //       '
            // );
			$data = $this->load->view('sales/invoice/print_sales_invoice_ppn', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/invoice'));
		}
	}
	
    // PRINT SALES INVOICE ORIGINAL
	public function print_sales_invoice($sales_invoice_id)
	{
		if($this->system->check_access('sales/invoice','create'))
		{			
			$sales_invoice_id = $this->global->decrypt($sales_invoice_id);			
			$sales_invoice    = $this->sales->get_detail_sales_invoice($sales_invoice_id);
			$data = array(
				'perusahaan'	=> $this->global->company(),
				'sales_invoice' => $sales_invoice,
				'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
			);	
			$this->load->view('sales/invoice/print_sales_invoice', $data);

			// $mpdf = new \Mpdf\Mpdf();
			// $data = $this->load->view('sales/invoice/print_sales_invoice', $data, true);
			// $mpdf->WriteHTML($data);
			// $mpdf->Output();
			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/order'));
		}
	}

	public function form_delivery_order($sales_invoice_id)
	{
		if($this->system->check_access('sales/invoice', 'create'))
        {
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$sales_invoice    = $this->sales->get_detail_sales_invoice($post['sales_invoice_id']);
				$sales_invoice_detail_id = [];
				foreach($post['product'] AS $info)
				{
					$sales_invoice_detail_id[] = $info['sales_invoice_detail_id'][0];
				}
				$data = array(
					'perusahaan'	=> $this->global->company(),
					'sales_invoice' => $sales_invoice,
					'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'], $sales_invoice_detail_id)
				);	
				$this->load->view('sales/invoice/print_delivery_order', $data);
			}
			else
			{
				$sales_invoice = $this->sales->get_detail_sales_invoice(decrypt_custom($sales_invoice_id));
				$header = array("title" => "Form Surat Jalan");
				$data = [
					'sales_invoice' => $sales_invoice,
					'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
				];
				$footer = array("script" => ['transaction/sales/invoice/delivery_order.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/invoice/form_delivery_order', $data);
				$this->load->view('include/footer', $footer);				
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
		
	}	

	public function print_delivery_order($sales_invoice_id)
	{
		if($this->system->check_access('sales/invoice','create'))
		{
			$sales_invoice_id = $this->global->decrypt($sales_invoice_id);			
			$sales_invoice    = $this->sales->get_detail_sales_invoice($sales_invoice_id);
			$data = array(
				'perusahaan'	=> $this->global->company(),
				'sales_invoice' => $sales_invoice,
				'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
			);	
			$this->load->view('sales/invoice/print_delivery_order', $data);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/order'));
		}
	}

    // SALES RETURN
    public function sales_return()
	{
		if($this->system->check_access('sales/return','read'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('sales_return.id AS id_sr, sales_return.code AS code_sr, sales_return.date, sales_invoice.invoice, sales_return.total_product, total_return, customer.name AS name_c, sales_return.do_status,
								 sales_return.code AS search_code')
								 ->from('sales_return')
								 ->join('sales_invoice', 'sales_invoice.id = sales_return.sales_invoice_id', 'left')
								 ->join('customer', 'customer.code = sales_return.customer_code')
								 ->join('sales_return_detail', 'sales_return_detail.sales_return_id = sales_return.id')
								 ->where('sales_return.deleted', 0);
				if($post['do_status'] == "" || $post['do_status'] != 0)
				{
					$this->datatables->where('DATE(sales_return.created) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")));
					$this->datatables->where('DATE(sales_return.created) <=', date('Y-m-d'));
					if($post['do_status'] == 1)
					{
						$this->datatables->where('sales_return.do_status', $post['do_status']);						
					}										
				}
				else
				{					
					$this->datatables->where('sales_return.do_status', $post['do_status']);
				}
				$this->datatables->group_by('sales_return.id');
				$this->datatables->add_column('code_sr', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/return/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_sr) ,code_sr');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Retur Penjualan");
				$footer = array("script" => ['transaction/sales/return/sales_return.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/return/sales_return');            
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}  		
	}

	public function get_product_return()
	{
		if($this->input->is_ajax_request())
		{
			$search 		= urldecode($this->uri->segment(4));
			$customer_code  = $this->uri->segment(5);
			$ppn  			= $this->uri->segment(6);
			$data           = $this->sales->get_product_return($search, $customer_code, $ppn);
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
	
	public function get_sellprice_return()
	{
		if($this->input->is_ajax_request())
		{
			$customer_code = ($this->input->post('customer_code') != NULL) ? $this->input->post('customer_code') : 'CUST-00001';
			$where=array(
				'product_code'	=> $this->input->post('product_code'),
				'unit_id'		=> $this->input->post('unit_id'),            
			);
			$result = array(
				'sellprice' => $this->sales->get_sellprice($where, $customer_code)
			);
			echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}
    
    public function get_warehouse_return()
    {
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$warehouse = $this->sales->get_warehouse($post['product_code'], $post['unit_id']);
			$option		= '';		
			foreach($warehouse as $data)
			{
				if($data['default']==1)
				{
					$option .= "<option value='".$data['id_w']."' selected>".$data['code_w']." | ".number_format($data['stock'], 2, '.', ',')."</option>";
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
		else
		{
			$this->load->view('auth/show_404');
		}        
	}

	public function get_invoice_return($customer_code)
	{		
		$data          = $this->sales->get_invoice_return($customer_code)->result();
        if($data)
        {
            $response   =   [
                'status'    => [
                    'code'      => 200,
                    'message'   => 'Data Ditemukan',
                ],
                'response'  => $data,
            ];
        }
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Data Tidak Ditemukan',
                ],
                'response'  => '',
            ];
        }
        echo json_encode($response);
	}

	public function get_account_payable()
	{
		$sales_id = $this->input->post('id');
		$result = array(
			'account_payable' => $this->sales->get_account_payable($sales_id)
		);
		echo json_encode($result);
	}
	
	public function create_sales_return()
	{
		if($this->system->check_access('sales/return','create'))
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post();
				$this->form_validation->set_rules('date', 'Tanggal Retur Pembelian', 'trim|required|xss_clean');
				$this->form_validation->set_rules('customer_code', 'Konsumen', 'trim|required|xss_clean');
				$this->form_validation->set_rules('method', 'Jenis Retur', 'trim|xss_clean');
				$this->form_validation->set_rules('product[]', 'Produk', 'trim|required|xss_clean');
				$this->form_validation->set_rules('total_product', 'Identitas Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('total_qty', 'Deskripsi Produk', 'trim|xss_clean');
				$this->form_validation->set_rules('total_return', 'Stok Minimal', 'trim|required|xss_clean');
				if($this->input->post('method') == 2)
				{
					$this->form_validation->set_rules('sales_invoice_id', 'Faktur Penjualan', 'trim|required|xss_clean');
					$this->form_validation->set_rules('account_payable', 'Nilai Faktur', 'trim|required|xss_clean');
					$this->form_validation->set_rules('grandtotal', 'Sisa Faktur', 'trim|required|xss_clean');    	
				}        
				$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
				if($this->form_validation->run() == FALSE)
				{
					$header = array("title" => "Retur Penjualan Baru");
					$footer = array("script" => ['transaction/sales/return/create_sales_return.js']);
					$this->load->view('include/header', $header);
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('sales/return/create_sales_return');            
					$this->load->view('include/footer', $footer);
				}
				else
				{
					$this->db->trans_start();
					$code 			 = $this->sales->sales_return_code();
					$customer		 = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
					$ppn			  = (!isset($post['ppn'])) ?  0 : $post['ppn'];
					$total_return 	 = format_amount($post['total_return']);
					$account_payable = format_amount($post['account_payable']);
					$grandtotal 	 = format_amount($post['grandtotal']);
					$data_sales_return = array(
						'date' 				=> format_date($post['date']),
						'code'				=> $code,				
						'employee_code'		=> $this->session->userdata('code_e'),
						'customer_code'		=> $post['customer_code'],
						'method' 			=> $post['method'],
						'cl_type' 			=> isset($post['from_cl_type']) ? $post['from_cl_type'] : null,
						'account_id' 		=> isset($post['from_account_id']) ? $post['from_account_id']: null,
						'total_product' 	=> $post['total_product'],
						'total_qty' 		=> $post['total_qty'],
						'total_return'		=> $total_return,
						'sales_invoice_id'  => ($post['method'] == 2) ? $post['sales_invoice_id'] : null,
						'account_payable' 	=> ($post['method'] == 2) ? $account_payable : null,
						'grandtotal' 		=> ($post['method'] == 2) ? $grandtotal : null,
						'ppn'				=> $ppn
					);
					$sales_return_id = $this->crud->insert_id('sales_return', $data_sales_return);						
					if($sales_return_id)
					{
						$total_hpp = 0;
						foreach($post['product'] AS $info)
						{
							$res = 0;
							$product_id = $this->crud->get_product_id($info['product_code']);
							$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
							$hpp = $this->product->hpp($info['product_code']);
							$where_unit = array(
								'product_code' => $info['product_code'],
								'unit_id' 	   => $info['unit_id'],
								'deleted'	   => 0
							);																		
							$convert = $this->crud->get_where('product_unit', $where_unit)->row_array();
							$total_hpp = $total_hpp + ($hpp*$qty*$convert['value']);
							$data_sales_return_detail = array(
								'sales_return_id'    => $sales_return_id,
								'product_id'		 => $product_id,
								'product_code'		 => $info['product_code'],
								'unit_id'		 	 => $info['unit_id'],
								'unit_value'		 => ($convert['value'] != null) ? $convert['value'] : 1,
								'warehouse_id'		 => $info['warehouse_id'],
								'qty'		 		 => $qty,
								'price'		 		 => $price,
								'total'		 		 => $total,
								'hpp'			     => $hpp,
								'information'		 => $info['information'],
								'ppn'				 => $ppn
							);
							if($this->crud->insert('sales_return_detail', $data_sales_return_detail))
							{												
								$res = 1;								
								continue;
							}
							else
							{														
								break;
							}
						}
						$this->crud->update('sales_return', ['total_hpp' => $total_hpp], ['id' => $sales_return_id]);
						$data_activity = [
							'information' => 'MEMBUAT RETUR PENJUALAN',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];						
						$this->crud->insert('activity', $data_activity);
					}
					else
					{
						$this->session->set_flashdata('error', 'Data Transaksi Retur Penjualan Gagal');
					}
					
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === TRUE && $res ==1)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'Transaksi Retur Penjualan berhasil ditambahkan');
					redirect(site_url('sales/return/detail/'.encrypt_custom($sales_return_id)));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Transaksi Retur Penjualan gagal ditambahkan');
					redirect(site_url('sales/return'));
				}				
			}
			else
			{
				$header = array("title" => "Retur Penjualan Baru");
				$footer = array("script" => ['transaction/sales/return/create_sales_return.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('sales/return/create_sales_return');            
				$this->load->view('include/footer', $footer);				
			}				
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/return'));
		}		
	}	
	
	public function create_sales_return_do()
    {
		if($this->system->check_access('sales/invoice','create'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$this->db->trans_start();
				$sales_return 		  = $this->sales->get_detail_sales_return($post['sales_return_id']);
				if($sales_return['do_status'] == 0)
				{
					$sales_return_detail  = $this->sales->get_detail_sales_return_detail($sales_return['id']);
					$customer = $this->crud->get_where('customer', ['code' => $sales_return['customer_code']])->row_array();
					// GENERAL LEDGER -> RETUR PENJUALAN (D)
					$where_last_balance = [
						'coa_account_code' => "40103",
						'date <='          => $sales_return['date'],                    
						'deleted'          => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_return['total_return']) : add_balance(0, $sales_return['total_return']);
					$data = [
						'date'              => $sales_return['date'],
						'coa_account_code'  => "40103",
						'transaction_id'    => $sales_return['id'],
						'invoice'           => $sales_return['code'],
						'information'       => 'RETUR PENJUALAN',
						'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
						'debit'            => $sales_return['total_return'],
						'balance'     		=> $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "40103",
							'date >'        => $sales_return['date'],
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_return['total_return'])], ['id' => $info['id']]);
						}
					}
					if($sales_return['method'] == 1)
					{
						// CASH LEDGER
						$from_where_last_balance = [
							'cl_type'    => $sales_return['cl_type'],
							'account_id' => $sales_return['account_id'],
							'date <='    => $sales_return['date'],                    
							'deleted'    => 0
						];
						$from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-$sales_return['total_return'] : 0-$sales_return['total_return'];
						if($from_balance < 0)
						{
							$response   =   [
								'status'    => [
									'code'      => 401,
									'message'   => 'Gagal',
								],
								'response'  => ''
							];
							$this->session->set_flashdata('error', 'Cetak DO Gagal, Kas tidak bisa minus');
							echo json_encode($response);
							die;
						}
						$data = [
							'cl_type'     => $sales_return['cl_type'],
							'transaction_id'  => $sales_return['id'],
							'account_id'  => $sales_return['account_id'],
							'date'        => $sales_return['date'],
							'invoice'     => $sales_return['code'],
							'information' => 'RETUR PENJUALAN',								
							'note'		  => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
							'amount'      => $sales_return['total_return'],
							'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
							'balance'     => $from_balance
						];
						$from_cl_id = $this->crud->insert_id('cash_ledger', $data);
						if($from_cl_id)
						{
							$from_where_after_balance = [
								'cl_type'       => $sales_return['cl_type'],
								'account_id'    => $sales_return['account_id'],
								'date >'        => $sales_return['date'],
								'deleted'       => 0
							];                    
							$from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($from_after_balance  AS $info)
							{
								$balance = $info['balance'] - $sales_return['total_return'];
								$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
							}                            
						}	
						// GENERAL LEDGER -> KAS (K)
						$where_last_balance = [
							'coa_account_code' => ($sales_return['cl_type'] == 1) ? "10101" : "10102",
							'date <='          => $sales_return['date'],                    
							'deleted'          => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_return['total_return']) : sub_balance(0, $sales_return['total_return']);
						$data = [
							'date'              => $sales_return['date'],
							'coa_account_code'  => ($sales_return['cl_type'] == 1) ? "10101" : "10102",
							'transaction_id'    => $sales_return['id'],
							'invoice'           => $sales_return['code'],
							'information'       => 'RETUR PENJUALAN',
							'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
							'credit'             => $sales_return['total_return'],
							'balance'     		=> $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> ($sales_return['cl_type'] == 1) ? "10101" : "10102",
								'date >'        => $sales_return['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_return['total_return'])], ['id' => $info['id']]);
							}
						}
					}				
					elseif($sales_return['method'] == 2)
					{
						$old_account_payable = $this->sales->get_account_payable($sales_return['sales_invoice_id']);
						$new_account_payable = $old_account_payable - $sales_return['total_return'];					
						if($new_account_payable == 0)
						{
							$data_new_account_payable = array(
								'payment_status'  => 1,
								'account_payable' => $new_account_payable
							);
						}
						else
						{
							$data_new_account_payable = array(
								'account_payable' => $new_account_payable
							);
						}
						$this->crud->update_by_id('sales_invoice', $data_new_account_payable, $sales_return['sales_invoice_id']);
						// GENERAL LEDGER -> PIUTANG USAHA (K)
						$where_last_balance = [
							'coa_account_code' => "10201",
							'date <='          => $sales_return['date'],                    
							'deleted'          => 0
						];
						$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_return['total_return']) : sub_balance(0, $sales_return['total_return']);
						$data = [
							'date'              => $sales_return['date'],
							'coa_account_code'  => "10201",
							'transaction_id'    => $sales_return['id'],
							'invoice'           => $sales_return['code'],
							'information'       => 'RETUR PENJUALAN',
							'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
							'credit'             => $sales_return['total_return'],
							'balance'     		=> $balance
						];									
						if($this->crud->insert('general_ledger', $data))
						{
							$where_after_balance = [
								'coa_account_code'=> "10201",
								'date >'        => $sales_return['date'],
								'deleted'       => 0
							];
							$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_balance  AS $info)
							{
								$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_return['total_return'])], ['id' => $info['id']]);
							}
						}
					}
					// GENERAL LEDGER -> PERSEDIAAN BARANG (D)
					$where_last_balance = [
						'coa_account_code' => "10301",
						'date <='          => $sales_return['date'],                    
						'deleted'          => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $sales_return['total_hpp']) : add_balance(0, $sales_return['total_hpp']);
					$data = [
						'date'              => $sales_return['date'],
						'coa_account_code'  => "10301",
						'transaction_id'    => $sales_return['id'],
						'invoice'           => $sales_return['code'],
						'information'       => 'RETUR PENJUALAN',
						'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
						'debit'             => $sales_return['total_hpp'],
						'balance'     		=> $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "10301",
							'date >'        => $sales_return['date'],
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $sales_return['total_hpp'])], ['id' => $info['id']]);
						}
					}
					// GENERAL LEDGER -> BEBAN POKOK PENDAPATAN (K)
					$where_last_balance = [
						'coa_account_code' => "50001",
						'date <='          => $sales_return['date'],                    
						'deleted'          => 0
					];
					$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
					$balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $sales_return['total_hpp']) : sub_balance(0, $sales_return['total_hpp']);
					$data = [
						'date'              => $sales_return['date'],
						'coa_account_code'  => "50001",
						'transaction_id'    => $sales_return['id'],
						'invoice'           => $sales_return['code'],
						'information'       => 'RETUR PENJUALAN',
						'note'		        => 'RETUR_PENJUALAN_'.$sales_return['code'].'_'.$customer['name'],
						'credit'            => $sales_return['total_hpp'],
						'balance'     		=> $balance
					];									
					if($this->crud->insert('general_ledger', $data))
					{
						$where_after_balance = [
							'coa_account_code'=> "50001",
							'date >'        => $sales_return['date'],
							'deleted'       => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance  AS $info)
						{
							$this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $sales_return['total_hpp'])], ['id' => $info['id']]);
						}
					}
					// SALES RETURN DETAIL
					foreach($sales_return_detail AS $info)
					{							
						$res = 0;
						$qty_convert = $info['qty']*$info['unit_value'];
						$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
						if($check_stock->num_rows() == 1)
						{	
							$stock = $check_stock->row_array();
							$where_stock = array(
								'product_code'  => $info['product_code'],
								'warehouse_id'  => $info['warehouse_id']
							);       							
							$stock = array(
								'product_id'    => $info['product_id'],
								'qty'           => $stock['qty']+$qty_convert
							);
							$update_stock_card = $this->crud->update('stock', $stock, $where_stock);
						}
						else
						{						
							$stock = array(                                
								'product_id'    => $info['product_id'],
								'product_code'  => $info['product_code'],                                                        
								'qty'           => 0+$qty_convert,
								'warehouse_id'  => $info['warehouse_id']
							);
							$update_stock_card = $this->crud->insert('stock', $stock);
						}
						if($update_stock_card)
						{
							// STOCK CARD
							$where_last_stock_card = [
								'product_id'   => $info['product_id'],											
								'date <='      => $sales_return['date'],
								'warehouse_id' => $info['warehouse_id'],
								'deleted'      => 0
							];
							$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$data_stock_card = array(
								'date'			  => $sales_return['date'],
								'transaction_id'  => $sales_return['id'],
								'invoice'         => $sales_return['code'],
								'product_id'      => $info['product_id'],
								'product_code'    => $info['product_code'],
								'qty'             => $qty_convert,
								'information'     => 'RETUR PENJUALAN',
								'note'			  => $customer['name'],
								'type'            => 5, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
								'method'          => 1, // 1:In, 2:Out
								'stock'           => $last_stock_card['stock'] + $qty_convert,
								'warehouse_id'    => $info['warehouse_id'],
								'user_id'         => $this->session->userdata('id_u')
							);									
							$this->crud->insert('stock_card',$data_stock_card);
							$where_after_stock_card = [
								'product_id'   => $info['product_id'],
								'date >'       => $sales_return['date'],
								'deleted'      => 0
							];                    
							$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_stock_card  AS $info_after_stock_card)
							{
								$stock = $info_after_stock_card['stock']+$qty_convert;
								$this->crud->update('stock_card', ['stock' => $stock], ['id' => $info_after_stock_card['id']]);
							}
							// STOCK MOVEMENT
							$where_last_stock_movement = [
								'product_id'   => $info['product_id'],
								'date <='      => $sales_return['date'],
								'deleted'      => 0
							];
							$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
							$data_stock_movement = [
								'type'            => 5, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
								'information'     => 'RETUR PENJUALAN',
								'note'			  => $customer['name'],
								'date'            => $sales_return['date'],
								'transaction_id'  => $sales_return['id'],
								'invoice'         => $sales_return['code'],
								'product_id'      => $info['product_id'],
								'product_code'    => $info['product_code'],
								'qty'             => $qty_convert,
								'method'          => 1, // 1:In, 2:Out
								'stock'           => $last_stock_movement['stock']+$qty_convert,
								'employee_code'   => $this->session->userdata('code_e')
							];
							$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
							$where_after_stock_movement = [
								'product_id'   => $info['product_id'],
								'date >'       => $sales_return['date'],
								'deleted'      => 0
							];                    
							$after_stock_movement = $this->db->select('id, stock')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
							foreach($after_stock_movement  AS $info_after_stock_movement)
							{
								$stock = $info_after_stock_movement['stock']+$qty_convert;
								$this->crud->update('stock_movement', ['stock' => $stock], ['id' => $info_after_stock_movement['id']]);
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
						$this->crud->update('sales_return', ['do_status' => 1], ['id' => $sales_return['id']]);
						$data_activity = [
							'information' => 'MEMBUAT PENJUALAN (CETAK DO)',
							'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
							'code_e'      => $this->session->userdata('code_e'),
							'name_e'      => $this->session->userdata('name_e'),
							'user_id'     => $this->session->userdata('id_u')
						];						
						$this->crud->insert('activity', $data_activity);					
						$this->session->set_userdata('create_sales_return_do', '1');
						$this->session->set_flashdata('success', 'DO Penjualan Berhasil');
						$response   =   [
							'sales_return_id' => encrypt_custom($sales_return['id']),
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
			redirect(site_url('sales'));
		}		
	}

	public function cancel_sales_return_do($sales_return_id)
	{
		$sales_return = $this->sales->get_detail_sales_return(decrypt_custom($sales_return_id));
		$sales_return_detail = $this->sales->get_detail_sales_return_detail($sales_return['id']);
		if($this->session->userdata('verifypassword') == 1)
		{
			$this->session->unset_userdata('verifypassword');
			$this->db->trans_start();
			// GENERAL LEDGER
			$where_general_ledger = [
				'invoice' => $sales_return['code']
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
			if($sales_return['method'] == 1)
			{
				// DELETE CASH LEDGER
				$where_cash_ledger = [
					'invoice'		  => $sales_return['code']
				];
				$cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->result_array();
				foreach($cash_ledger AS $info_cash_ledger)
				{
					$where_after_balance = [
						'cl_type'    => $info_cash_ledger['cl_type'],
						'account_id' => $info_cash_ledger['account_id'],
						'date >='    => $info_cash_ledger['date'],                
						'deleted'    => 0
					];
					$data   = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
					foreach($data AS $info)
					{
						if($info['date'] == $info_cash_ledger['date'] && $info['id'] < $info_cash_ledger['id'])
						{
							continue;
						}
						else
						{
							if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
							{
								$balance = $info['balance'] - $info_cash_ledger['amount'];
							}
							else
							{
								$balance = $info['balance'] + $info_cash_ledger['amount'];
							}
							$this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
						}
					}
					$this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
				}			
			}
			else
			{
				$old_account_payable = $this->sales->get_account_payable($sales_return['sales_invoice_id']);
				$new_account_payable = $old_account_payable+$sales_return['total_return'];					
				$data_new_account_payable = array(
					'payment_status'  => 2,
					'account_payable' => $new_account_payable
				);
				$this->crud->update_by_id('sales_invoice', $data_new_account_payable, $sales_return['sales_invoice_id']);								
			}			
			// SALES RETURN DETAIL			
			foreach($sales_return_detail AS $info_sales_return)
			{					
				// ADD STOCK
				$where_stock = [
					'product_code'	=> $info_sales_return['product_code'],
					'warehouse_id'	=> $info_sales_return['warehouse_id']
				];
				$stock = $this->crud->get_where('stock', $where_stock)->row_array();
				$update_stock = [
					'qty' => $stock['qty']-($info_sales_return['qty']*$info_sales_return['unit_value'])
				];
				$this->crud->update('stock', $update_stock, $where_stock);
	
				// UPDATE AND DELETE STOCK CARD
				$where_stock_card = [
					'transaction_id' => $sales_return['id'],
					'product_code'	 => $info_sales_return['product_code'],
					'type'			 => 5,
					'method'		 => 1,
					'warehouse_id'	 => $info_sales_return['warehouse_id']
				];								
				$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
				$where_after_stock_card = [
					'date >='       => $stock_card['date'],
					'product_code'	=> $info_sales_return['product_code'],
					'warehouse_id'	=> $info_sales_return['warehouse_id'],
					'deleted'		=> 0
				];
				$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.date', 'ASC')->order_by('stock_card.id', 'ASC')->get()->result_array();
				foreach($after_stock_cards AS $info_stock_card)
				{
					if($stock_card['date'] == $info_stock_card['date'] && $stock_card['id'] > $info_stock_card['id'])
					{
						continue;
					}
					else
					{
						$update_stock_card = [
							'stock' => $info_stock_card['stock']-($info_sales_return['qty']*$info_sales_return['unit_value'])
						];
						$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
					}					
				}
				$this->crud->delete('stock_card', ['id' => $stock_card['id']]);
				// UPDATE AND DELETE STOCK MOVEMENT
				$where_stock_movement = [
					'transaction_id' => $sales_return['id'],
					'product_code'	 => $info_sales_return['product_code'],
					'type'			 => 5, // 1: Purchase, 2: Purchase Return, 3: POS, 4: Sales, 5: Sales Return, 6: Production, 7: Repacking, 8: Adjusment Stock, 9: Mutation
					'method'		 => 1, // 1:IN, 2:OUT
				];								
				$stock_movement = $this->crud->get_where('stock_movement', $where_stock_movement)->row_array();
				$where_after_stock_movement = [
					'date >='       => $stock_movement['date'],
					'product_code'	=> $info_sales_return['product_code'],
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
							'stock' => $info_stock_movement['stock']-($info_sales_return['qty']*$info_sales_return['unit_value'])
						];
						$this->crud->update('stock_movement', $update_stock_movement, ['id' => $info_stock_movement['id']]);
					}					
				}
				$this->crud->delete('stock_movement', ['id' => $stock_movement['id']]);
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === TRUE)
			{
				$this->db->trans_commit();
				$this->crud->update('sales_return', ['do_status' => 0], ['id' => $sales_return['id']]);								
				$data_activity = [
					'information' => 'MEMBATALKAN RETUR PENJUALAN (BATAL DO) (NO. TRANSAKSI '.$sales_return['code'].')',
					'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$this->session->set_flashdata('success', 'DO Retur Penjualan berhasil dibatalkan');
				redirect(site_url('sales/return/detail/'.encrypt_custom($sales_return['id'])));
			}
			else
			{
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Mohon Maaf, DO Retur Penjualan gagal dibatalkan');
				redirect(site_url('sales/return/detail/'.encrypt_custom($sales_return['id'])));
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/return/detail/'.encrypt_custom($sales_return['id'])));
		}		
	}

	public function print_sales_return_do($sales_return_id)
	{
		if($this->session->userdata('create_sales_return_do') == 1)
		{
			$this->session->unset_userdata('create_sales_return_do');
			$sales_return = $this->sales->get_detail_sales_return(decrypt_custom($sales_return_id));
			$customer      = $this->crud->get_where('customer', ['code' => $sales_return['customer_code']])->row_array();
			$warehouse     = $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
								->from('sales_return_detail')->join('warehouse', 'warehouse.id = sales_return_detail.warehouse_id')								
								->where('sales_return_detail.sales_return_id', $sales_return['id'])
								->where('warehouse.deleted', 0)->where('sales_return_detail.deleted', 0)
								->group_by('warehouse.id')->order_by('warehouse.id', 'asc')->get()->result_array();
			foreach($warehouse AS $info_w)
			{
				$data_so = $this->db->select('sales_return.code')
									->from('sales_return')->join('sales_return_detail', 'sales_return_detail.sales_return_id = sales_return.id')
									->where('sales_return_detail.warehouse_id', $info_w['id_w'])
									->where('sales_return_detail.sales_return_id', $sales_return['id'])
									->where('sales_return_detail.deleted', 0)
									->group_by('sales_return.id')->order_by('sales_return.code', 'asc')->get()->result_array();
				$product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, sales_return_detail.qty AS qty, unit.code AS code_u')
								->from('sales_return_detail')->join('product', 'product.id = sales_return_detail.product_id')
								->where('sales_return_detail.warehouse_id', $info_w['id_w'])
								->where('sales_return_detail.sales_return_id', $sales_return['id'])
								->where('product.deleted', 0)->where('sales_return_detail.deleted', 0)
								->join('unit', 'unit.id = sales_return_detail.unit_id')
								->group_by('sales_return_detail.id')->order_by('product_code', 'asc')->get()->result_array();
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
					'code_sot' 	 => $sales_return['code'],
					'data_so'    => $data_so,
					'id_w' 		 => $info_w['id_w'],
					'code_w' 	 => $info_w['code_w'],
					'name_w' 	 => $info_w['name_w'],
					'product'	 => $data_product
				);
			}		
			$data = array(
				'perusahaan' => $this->global->company(),
				'sales_return' => $sales_return,
				'customer'    => $customer,
				'sot'        => $sot
			);
			$this->load->view('sales/return/print_sales_return_do', $data);
		}
		else
		{

		}		
	}
	
	public function datatable_detail_sales_return($sales_return_id)
	{
		if($this->input->is_ajax_request())
		{
			header('Content-Type: application/json');
			$this->datatables->select('sales_return_detail.id AS id, 
							product.code AS code_p, product.name AS name_p, 
							unit.name AS name_u, warehouse.name AS name_w, sales_return_detail.qty AS qty, 
							sales_return_detail.price, sales_return_detail.total AS total,
							sales_return_detail.information');
			$this->datatables->from('sales_return_detail');
			$this->datatables->join('product', 'product.code = sales_return_detail.product_code');
			$this->datatables->join('unit', 'unit.id = sales_return_detail.unit_id');
			$this->datatables->join('warehouse', 'warehouse.id = sales_return_detail.warehouse_id');
			$this->datatables->where('sales_return_detail.deleted', 0);
			$this->datatables->where('sales_return_detail.sales_return_id', $sales_return_id);			
			$this->datatables->group_by('sales_return_detail.id');
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
	
	public function detail_sales_return($sales_return_id)
    {
		if($this->system->check_access('sales/return','detail'))
		{			
			$header = array("title" => "Detail Retur Penjualan");
			$data = array( 'sales_return' => $this->sales->get_detail_sales_return(decrypt_custom($sales_return_id)));
			$footer = array("script" => ['transaction/sales/return/detail_sales_return.js']);
			$this->load->view('include/header', $header);
			$this->load->view('include/menubar');
			$this->load->view('include/topbar');
			$this->load->view('sales/return/detail_sales_return', $data);
			$this->load->view('include/footer', $footer);
		}	
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/return'));
		}        
	}

	public function update_sales_return($sales_return_id)
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();			
			$sales_return = $this->sales->get_detail_sales_return(decrypt_custom($sales_return_id));
			$sales_return_detail = $this->sales->get_detail_sales_return_detail($sales_return['id']);
			$this->form_validation->set_rules('date', 'Tanggal Retur Penjualan', 'trim|required|xss_clean');
			$this->form_validation->set_rules('customer_code', 'Pelanggan', 'trim|required|xss_clean');
			$this->form_validation->set_rules('method', 'Jenis Retur', 'trim|xss_clean');
			$this->form_validation->set_rules('product[]', 'Daftar Produk', 'trim|required|xss_clean');
			$this->form_validation->set_rules('total_product', 'Total Produk', 'trim|xss_clean');
			$this->form_validation->set_rules('total_qty', 'Total Kuantitas', 'trim|xss_clean');
			$this->form_validation->set_rules('total_return', 'Total Retur', 'trim|required|xss_clean');
			if($post['method'] == 2)
			{
				$this->form_validation->set_rules('sales_invoice_id', 'No. Penjualan', 'trim|required|xss_clean');
				$this->form_validation->set_rules('account_payable', 'Hutang Penjualan', 'trim|required|xss_clean');
				$this->form_validation->set_rules('grandtotal', 'Sisa Tagihan', 'trim|required|xss_clean');    	
			}        
			$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
			if($this->form_validation->run() == FALSE)
			{
				
				$header = ["title" => "Perbarui Retur Pembelian"];
				$data   = [
					'sales_return' => $sales_return,
					'sales_return_detail' => $sales_return_detail
				];
				$footer = array("script" => ['transaction/sales/return/update_sales_return.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/return/update_sales_return', $data);
				$this->load->view('include/footer', $footer);
			}
			else
			{
				$this->db->trans_start();
				$customer		 = $this->crud->get_where('customer', ['code' => $post['customer_code']])->row_array();
				$ppn			 = (!isset($post['ppn'])) ?  0 : $post['ppn'];
				$total_return 	 = format_amount($post['total_return']);
				$account_payable = format_amount($post['account_payable']);
				$grandtotal 	 = format_amount($post['grandtotal']);
				$data_sales_return = array(
					'date' 				=> format_date($post['date']),
					'employee_code'		=> $this->session->userdata('code_e'),
					'customer_code'		=> $post['customer_code'],
					'method' 			=> $post['method'],
					'cl_type' 			=> isset($post['from_cl_type']) ? $post['from_cl_type'] : null,
					'account_id' 		=> isset($post['from_account_id']) ? $post['from_account_id']: null,
					'total_product' 	=> $post['total_product'],
					'total_qty' 		=> $post['total_qty'],
					'total_return'		=> $total_return,
					'sales_invoice_id'  => ($post['method'] == 2) ? $post['sales_invoice_id'] : null,
					'account_payable' 	=> ($post['method'] == 2) ? $account_payable : null,
					'grandtotal' 		=> ($post['method'] == 2) ? $grandtotal : null,
					'ppn'				=> $ppn
				);				
				if($this->crud->update('sales_return', $data_sales_return, ['id' => $sales_return['id']]))
				{
					$this->crud->delete('sales_return_detail', ['sales_return_id' => $sales_return['id']]);
					$total_hpp = 0;
					foreach($post['product'] AS $info)
					{
						if($info['product_code'] == "")
						{
							continue;							
						}
						$res = 0;
						$product_id = $this->crud->get_product_id($info['product_code']);
						$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
						$hpp = $this->product->hpp($info['product_code']);
						$where_unit = array(
							'product_code' => $info['product_code'],
							'unit_id' 	   => $info['unit_id'],
							'deleted'	   => 0
						);																		
						$convert = $this->crud->get_where('product_unit', $where_unit)->row_array();
						echo json_encode($convert); die;
						$total_hpp = $total_hpp + ($hpp*$qty*$convert['value']);
						$data_sales_return_detail = array(
							'sales_return_id'    => $sales_return['id'],
							'product_id'		 => $product_id,
							'product_code'		 => $info['product_code'],
							'unit_id'		 	 => $info['unit_id'],
							'unit_value'		 => ($convert['value'] != null) ? $convert['value'] : 1,
							'warehouse_id'		 => $info['warehouse_id'],
							'qty'		 		 => $qty,
							'price'		 		 => $price,
							'total'		 		 => $total,
							'hpp'			     => $hpp,
							'information'		 => $info['information'],
							'ppn'				 => $ppn
						);
						if($this->crud->insert('sales_return_detail', $data_sales_return_detail))
						{												
							$res = 1;								
							continue;
						}
						else
						{														
							break;
						}
					}
					$this->crud->update('sales_return', ['total_hpp' => $total_hpp], ['id' => $sales_return['id']]);
				}
				$this->db->trans_complete();
				if($res ==1 && $this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$data_activity = [
						'information' => 'MEMPERBARUI RETUR PENJUALAN (NO. TRANSAKSI '.$sales_return['code'].')',
						'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
						'code_e'      => $this->session->userdata('code_e'),
						'name_e'      => $this->session->userdata('name_e'),
						'user_id'     => $this->session->userdata('id_u')
					];						
					$this->crud->insert('activity', $data_activity);
					$this->session->set_flashdata('success', 'Retur Penjualan berhasil diperbarui');
					redirect(site_url('sales/return/detail/'.encrypt_custom($sales_return['id'])));
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Retur Penjualan gagal diperbarui');
					redirect(site_url('sales/return'));
				}
			}							
		}
		else
		{
			if(true)
			{			
				$this->session->unset_userdata('verifypassword');
				$sales_return = $this->sales->get_detail_sales_return(decrypt_custom($sales_return_id));
				$header = ["title" => "Perbarui Retur Penjualan"];
				$data   = [
					'sales_return' => $sales_return,
					'sales_return_detail' => $this->sales->get_detail_sales_return_detail($sales_return['id'])
				];
				$footer = array("script" => ['transaction/sales/return/update_sales_return.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/return/update_sales_return', $data);
				$this->load->view('include/footer', $footer);
				
			}
			else
			{
				$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
				redirect(urldecode($this->agent->referrer()));
			}						
		}	
	}

	public function delete_sales_return()
	{
		if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post 		 = $this->input->post();
			$this->db->trans_start();
			$sales_return = $this->sales->get_detail_sales_return($post['sales_return_id']);
			// DELETE sales RETURN DETAIL
			$this->crud->delete('sales_return_detail', ['sales_return_id' => $sales_return['id']]);			
			// DELETE sales RETURN
			$this->crud->delete('sales_return', ['id' => $sales_return['id']]);
			$this->db->trans_complete();
			if($this->db->trans_status() === TRUE)
			{
				$this->db->trans_commit();
				$data_activity = [
					'information' => 'MENGHAPUS RETUR PENJUALAN (NO. TRANSAKSI '.$sales_return['code'].')',
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
				$this->session->set_flashdata('success', 'BERHASIL! Retur Penjualan Terhapus');
			}			
			else
			{
				$this->db->trans_rollback();
				$response   =   [
					'status'    => [
						'code'      => 400,
						'message'   => 'Gagal',
					],
					'response'  => ''
				];
				$this->session->set_flashdata('error', 'Mohon Maaf, Retur Penjualan gagal Terhapus');

			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}	
	}

	public function print_sales_return($sales_return_id)
	{
		if($this->system->check_access('sales/return','create'))
		{			
			$sales_return = $this->sales->get_detail_sales_return(decrypt_custom($sales_return_id));
			$data = array(
				'sales_return' => $sales_return,
				'sales_return_detail' => $this->sales->get_detail_sales_return_detail($sales_return['id'])
			);
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 139.7],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 25,
				'margin_bottom' => 35,
				'margin_header' => 5,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-size:14px;">
					B | RETUR PENJUALAN | NO. '.$sales_return['code'].' | '.date('d-m-Y', strtotime($sales_return['date'])).'
				</div>
				<table style="width:100%; font-size:12x;">
					<tbody>
						<tr>							
							<td width="15%">Pelanggan</td>
							<td>: '.$sales_return['name_c'].'</td>
							<td>Alamat</td>
							<td>: '.$sales_return['address_c'].'</td>
							<td>OPT</td>
							<td>: '.$sales_return['name_e'].'</td>							
						</tr>
					</tbody>
				</table>
			');
			$mpdf->SetHTMLFooter(
				'<table style="width:100%; text-align:center;" border="0">
					<tr>
						<td>HORMAT KAMI</td>
						<td>CHECKER</td>
						<td>PENERIMA</td>
						<td><small>(barang dianggap sebagai titipan apabila belum lunas)</small></td>
					</tr>
					<tr>
						<td style="height:35px;">&nbsp;</td>
					</tr>
					<tr>
						<td><p>(___________________________)</p></td>
						<td><p>(___________________________)</p></td>
						<td><p>(___________________________)</p></td>
					</tr>
					<tr>
						<td style="height:5px;">&nbsp;</td>
					</tr>
				</table>
				<table width="100%">
				<tr>
					<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
					<td align="center">{PAGENO}/{nbpg}</td>
				</tr>
				</table>'
			);	
			$data = $this->load->view('sales/return/print_sales_return', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
			// $this->load->view('sales/return/print_sales_return', $data);
		}	
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('sales/return'));
		} 
	}

	// SALES BILLING
	public function sales_billing()
    {
        if($this->system->check_access('sales/billing', 'A'))
        {
            if($this->input->is_ajax_request())
			{
                $this->datatables->select('sales_billing.id, sales_billing.date, sales_billing.code')
                                 ->from('sales_billing')                         
                                 ->group_by('sales_billing.id');
                $this->datatables->add_column('code', 
                '<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/billing/detail/$1').'" target="_blank"><b>$2</b></a>
                ', 'encrypt_custom(id), code');
                header('Content-Type: application/json');
                echo $this->datatables->generate();                
			}
			else
			{
				$data_activity = [
                    'information' => 'MELIHAT DAFTAR PENAGIHAN',
                    'method'      => 1,
                    'code_e'      => $this->session->userdata('code_e'),
                    'name_e'      => $this->session->userdata('name_e'),
                    'user_id'     => $this->session->userdata('id_u')
                ];						
                $this->crud->insert('activity', $data_activity);
                $header = array("title" => "Penagihan");
                $footer = array("script" => ['transaction/sales/billing/sales_billing.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('sales/billing/sales_billing');
                $this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }   
    }

	public function create_sales_billing()
    {
        if($this->system->check_access('sales/billing', 'C'))
        {
            if($this->input->is_ajax_request())
			{
                $post = $this->input->post();			                 
				$where_sales_invoice = [
					'sales_invoice.sales_code' => $post['sales_code'],
					'sales_invoice.account_payable !=' => 0,
					'sales_invoice.payment_status !=' => 1,
					// 'do_status'	=> 1,
					'sales_invoice.deleted' => 0
				];          
                $this->datatables->select('sales_invoice.id, sales_invoice.date, sales_invoice.invoice, sales_invoice.due_date, sales_invoice.grandtotal, sales_invoice.account_payable,
									customer.name AS name_c, customer.address, 
									sales.name AS name_s')
                                     ->from('sales_invoice')
                                     ->join('customer', 'customer.code = sales_invoice.customer_code')
                                     ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')                                     
									 ->where($where_sales_invoice);                                     
                $this->datatables->group_by('sales_invoice.id');
                $this->datatables->add_column('choose',
                '			
                    <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand" >
                    <input type="checkbox" name="sales_invoice_id[]" value="$1" class="choose">&nbsp;<span></span>
                    </label>
                ', 'id');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
			}
			else
			{
				if($this->input->method() === 'post')
                {
                    $post = $this->input->post();
                    $code = $this->sales->sales_billing_code();
                    $data_sales_billing = [
                        'date' => format_date($post['date']),
                        'code' => $code,
						'sales_code' => $post['sales_code'],
                        'employee_code' => $this->session->userdata('code_e')
                    ];
                    $sales_billing_id = $this->crud->insert_id('sales_billing', $data_sales_billing);
                    if($sales_billing_id != null)
                    {
                        foreach($post['sales_invoice_id'] AS $info_sales_invoice_id)
                        {
							$sales_invoice = $this->crud->get_where('sales_invoice', ['id' => $info_sales_invoice_id])->row_array();
							$start = strtotime($post['date']);
							$end   = strtotime($sales_invoice['due_date']);
							$diff  = $start - $end;
                            $data_sales_billing_detail = [
                                'sales_billing_id' => $sales_billing_id,
                                'sales_invoice_id' => $info_sales_invoice_id,
								'remaining_time' => floor($diff / (60 * 60 * 24)),
								'account_payable' => $sales_invoice['account_payable']
                            ];
                            $this->crud->insert('sales_billing_detail', $data_sales_billing_detail);                            
                        }
                    }
                    $data_activity = [
                        'information' => 'MEMBUAT PENAGIHAN BARU (NO. TRANSAKSI '.$code.')',
                        'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];						
                    $this->crud->insert('activity', $data_activity);
                    $this->session->set_flashdata('success', 'Penagihan berhasil disimpan');
                    redirect(site_url('sales/billing/detail/'.encrypt_custom($sales_billing_id)));
                }
                else
                {
                    $header = array("title" => "Penagihan Baru");
                    $footer = array("script" => ['transaction/sales/billing/create_sales_billing.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('sales/billing/create_sales_billing');
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

    public function datatable_detail_sales_billing()
    {
        if($this->input->is_ajax_request())
        {
            $post = $this->input->post();
            $this->datatables->select('sales_invoice.id, sales_invoice.date, sales_invoice.invoice, customer.name AS name_c, sales_invoice.due_date, sales_billing_detail.remaining_time, sales_billing_detail.account_payable')
                                    ->from('sales_invoice')
                                    ->join('customer', 'customer.code = sales_invoice.customer_code')                                    
                                    ->join('sales_billing_detail', 'sales_billing_detail.sales_invoice_id = sales_invoice.id')
                                    ->where('sales_billing_detail.sales_billing_id', $post['sales_billing_id'])
                                    ->where('sales_invoice.deleted', 0);
            $this->datatables->group_by('sales_billing_detail.id');
            $this->datatables->add_column('invoice', 
            '<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'" target="_blank"><b>$2</b></a>
            ', 'encrypt_custom(id), invoice');
            header('Content-Type: application/json');
            echo $this->datatables->generate();
        }
    }

    public function detail_sales_billing($sales_billing_id)
    {
        if($this->system->check_access('sales/billing', 'R'))
        {
            $sales_billing = $this->sales->detail_sales_billing(decrypt_custom($sales_billing_id));
            $data_activity = [
                'information' => 'MELIHAT DETAIL PENAGIHAN (NO. TRANSAKSI '.$sales_billing['code'].')',
                'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                'code_e'      => $this->session->userdata('code_e'),
                'name_e'      => $this->session->userdata('name_e'),
                'user_id'     => $this->session->userdata('id_u')
            ];						
            $this->crud->insert('activity', $data_activity);
            $header = array("title" => "Detail Penagihan");
            $footer = array("script" => ['transaction/sales/billing/detail_sales_billing.js']);
            $data = [
                'sales_billing' => $sales_billing                
            ];
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('sales/billing/detail_sales_billing', $data);
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function print_sales_billing($sales_billing_id)
    {
        if($this->system->check_access('sales/billing', 'R'))
        {
            $sales_billing = $this->sales->detail_sales_billing(decrypt_custom($sales_billing_id));
            $data_activity = [
                'information' => 'CETAK DETAIL PENAGIHAN (NO. TRANSAKSI '.$sales_billing['code'].')',
                'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                'code_e'      => $this->session->userdata('code_e'),
                'name_e'      => $this->session->userdata('name_e'),
                'user_id'     => $this->session->userdata('id_u')
            ];						
            $this->crud->insert('activity', $data_activity);
            $data = [
                'sales_billing' => $sales_billing,
                'sales_billing_detail' => $this->sales->detail_sales_billing_detail($sales_billing['id'])
            ];
            $this->load->view('sales/billing//print_sales_billing', $data);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }
}