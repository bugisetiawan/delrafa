<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_report extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('Finance_report_model', 'finance_report');
		$this->load->model('finance/Cash_ledger_model', 'cash_ledger');
		$this->load->model('transaction/Sales_model', 'sales');
    }

	// PURCHASE PAYABLE	
	public function total_purchase_payable_report()
	{
		if($this->system->check_access('report/purchase_payable/total', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$this->db->select('purchase_invoice.grandtotal, purchase_invoice.account_payable, purchase_invoice.cheque_payable');
				$this->db->from('purchase_invoice');
				$this->db->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
				$this->db->where('purchase_invoice.payment_status !=', 1);
				$this->db->where('purchase_invoice.deleted', 0);
				if($post['from_date'] != "")
				{
					$this->db->where('purchase_invoice.date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->db->where('purchase_invoice.date <=', date('Y-m-d', strtotime($post['to_date'])));
				}                
				if($post['supplier_code'] != "")
				{
					$this->db->where('purchase_invoice.supplier_code', $post['supplier_code']);
				}              
				if($post['ppn'] != "")
				{
					$this->db->where('purchase_invoice.ppn', $post['ppn']);
				}
				if($post['payment_status'] == 3)
				{				
					$this->db->where('purchase_invoice.due_date <', date('Y-m-d'));
				}				
				$purchase_invoice = $this->db->group_by('purchase_invoice.id')->get()->result_array();
				$grandtotal=0; $account_payable=0; $cheque_payable=0;
				foreach($purchase_invoice AS $info_purchase_invoice)
				{
					$grandtotal = $grandtotal+$info_purchase_invoice['grandtotal'];
					$account_payable = $account_payable+$info_purchase_invoice['account_payable'];
					$cheque_payable = $cheque_payable+$info_purchase_invoice['cheque_payable'];
				}
				header('Content-Type: application/json');
				$result = array(
					'grandtotal'        => number_format($grandtotal, 2,".",","),
					'account_payable'   => number_format($account_payable+$cheque_payable, 2,".",",")
				);
				echo json_encode($result);
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
	
	public function purchase_payable()
	{   
		if($this->system->check_access('report/purchase_payable', 'A'))
		{
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('purchase_invoice.id AS id, purchase_invoice.date, purchase_invoice.code AS code, purchase_invoice.invoice AS search_invoice, purchase_invoice.invoice, purchase_invoice.payment, purchase_invoice.due_date, DATEDIFF(purchase_invoice.due_date, CURRENT_DATE()) AS remaining_time, purchase_invoice.grandtotal, purchase_invoice.account_payable, purchase_invoice.cheque_payable, supplier.name AS supplier, purchase_invoice.ppn, purchase_invoice.payment_status');
				$this->datatables->from('purchase_invoice');
				$this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
				$this->datatables->where('purchase_invoice.payment_status !=', 1);
				$this->datatables->where('purchase_invoice.deleted', 0);
				if($post['from_date'] != "")
				{
					$this->datatables->where('purchase_invoice.date >=', format_date($post['from_date']));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('purchase_invoice.date <=', format_date($post['to_date']));
				}                
				if($post['supplier_code'] != "")
				{
					$this->datatables->where('purchase_invoice.supplier_code', $post['supplier_code']);
				}              
				if($post['ppn'] != "")
				{
					$this->datatables->where('purchase_invoice.ppn', $post['ppn']);
				}
				if($post['payment_status'] != "")
				{
					if($post['payment_status'] != 3)
                    {
                        $this->datatables->where('purchase_invoice.payment_status', $post['payment_status']);
                    }                    
                    else
                    {
                        $this->datatables->where('purchase_invoice.payment_status !=', 1);
                        $this->datatables->where('purchase_invoice.due_date <', date('Y-m-d'));
                    }					
				}
				$this->datatables->group_by('purchase_invoice.id');        
				$this->datatables->add_column('invoice', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id), invoice');
				$this->datatables->add_column('pay_action', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/create/$1').'" target="_blank">BAYAR</a>
				', 'encrypt_custom(id)');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Hutang Pembelian");        
				$footer = array("script" => ['report/purchase/purchase_payable_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/purchase_payable_report');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}             
	}

	// SUPPLIER'S PURCHASE PAYABLE
	public function total_supplier_purchase_payable()
	{
		if($this->system->check_access('report/purchase_payable/supplier/total', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->db->select('supplier.id AS id_s, supplier.name AS name_s, sum(purchase_invoice.grandtotal) AS grandtotal, sum(purchase_invoice.account_payable) AS account_payable, sum(purchase_invoice.cheque_payable) AS cheque_payable')
								->from('purchase_invoice')
								->join('supplier', 'supplier.code = purchase_invoice.supplier_code')							
								->where('purchase_invoice.deleted', 0)->where('purchase_invoice.payment_status !=', 1);
				if($post['from_date'] != "")
				{
					$this->db->where('purchase_invoice.date >=', format_date($post['from_date']));
				}
				if($post['to_date'] != "")
				{
					$this->db->where('purchase_invoice.date <=', format_date($post['to_date']));
				}
				if($post['supplier_code'] != "")
				{
					$this->db->where('purchase_invoice.supplier_code', $post['supplier_code']);
				}
				$data = $this->db->group_by('supplier.id')->get()->result_array();
				$grandtotal =0; $account_payable =0; $total_transaction =0;
				foreach($data AS $info)
				{
					$grandtotal      = $grandtotal+$info['grandtotal'];
					$account_payable = $account_payable+$info['account_payable']+$info['cheque_payable'];
					$total_transaction++;
				}            
				$result = array(
					'grandtotal'     	=> number_format($grandtotal, 2,".",","),
					'account_payable'   => number_format($account_payable, 2,".",",")
				);
				header('Content-Type: application/json');
				echo json_encode($result);
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

	public function supplier_purchase_payable()
    {        
        if($this->system->check_access('report/purchase_payable/supplier', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('supplier.id AS id_s, supplier.name AS name_s, sum(purchase_invoice.grandtotal) AS grandtotal, sum(purchase_invoice.account_payable) AS account_payable, sum(purchase_invoice.cheque_payable) AS cheque_payable')
								->from('purchase_invoice')								
								->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
								->where('purchase_invoice.deleted', 0)->where('purchase_invoice.payment_status !=', 1);
				if($post['from_date'] != "")
				{
					$this->datatables->where('purchase_invoice.date >=', format_date($post['from_date']));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('purchase_invoice.date <=', format_date($post['to_date']));
				}
				if($post['supplier_code'] != "")
				{
					$this->datatables->where('purchase_invoice.supplier_code', $post['supplier_code']);
				}
				$this->datatables->group_by('supplier.id');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array( "title" => "Hutang Per Supplier");
				$footer = array("script" => ['report/finance/supplier_purchase_payable_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/supplier_purchase_payable_report');
				$this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        } 
	}

	// PAYMENT OF DEBT
	public function payment_of_debt()
	{
		if($this->system->check_access('report/payment_of_debt', 'A'))
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
				if($post['from_date'] != "")
				{
					$this->datatables->where('pod.date >=', format_date($post['from_date']));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('pod.date <=', format_date($post['to_date']));
				}
				if($post['supplier_code'] != "")
				{
					$this->datatables->where('supplier.code', $post['supplier_code']);
				}
				if(isset($post['method']))
				{
				    foreach($post['method'] AS $info)
				    {
				        $this->datatables->like('pod.method', $info);
				    }
				}
				$this->datatables->group_by('pod.id');
				$this->datatables->add_column('code_pod',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_pod), code_pod');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Pembayaran Pembelian");        
				$footer = array("script" => ['report/purchase/payment_of_debt_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/payment_of_debt_report');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}        
	}

	// CHEQUE OUT
	public function cheque_out()
	{
		if($this->system->check_access('report/cheque_out', 'A'))
		{
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('pod.id AS id_pod, pod.code AS code_pod, pod.date,
								pod_detail.cheque_number, pod_detail.cheque_open_date, pod_detail.cheque_close_date, pod_detail.cheque_status, pod_detail.cheque_acquittance_date, pod_detail.amount, 
								pod.to_pl, supplier.name AS supplier,
								pod.code AS search_code_pod, purchase_invoice.code AS search_code_purchase');
				$this->datatables->from('payment_ledger AS pod');
				$this->datatables->join('payment_ledger_detail AS pod_detail', 'pod_detail.pl_id = pod.id');
				$this->datatables->join('payment_ledger_transaction AS pod_transaction', 'pod_transaction.pl_id= pod.id');
				$this->datatables->join('purchase_invoice', 'purchase_invoice.id = pod_transaction.transaction_id');
				$this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
				$this->datatables->where('pod.transaction_type', 1);
				$this->datatables->where('pod_detail.method', json_encode(["3"]));
				if($post['from_date'] != "")
				{
					$this->datatables->where('pod.date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('pod.date <=', date('Y-m-d', strtotime($post['to_date'])));
				}
				if($post['supplier_code'] != "")
				{
					$this->datatables->where('supplier.code', $post['supplier_code']);
				}	
				if($post['cheque_status'] != "")
				{										
					$this->datatables->where('pod_detail.cheque_status', $post['cheque_status']);					
				}			
				$this->datatables->group_by('pod_detail.id');
				$this->datatables->add_column('code_pod', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_pod), code_pod');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Hutang Cek/Giro (Keluar)");
				$footer = array("script" => ['report/finance/cheque_out_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/cheque_out_report');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}        
	}

	// SALES RECEIVABLE
    public function total_sales_receivable_report()
    {
        if($this->input->is_ajax_request())
		{
            $post = $this->input->post();
            header('Content-Type: application/json');
            $this->db->select('sales_invoice.grandtotal AS grandtotal, (sales_invoice.account_payable+sales_invoice.cheque_payable) AS account_payable');
            $this->db->from('sales_invoice');
            $this->db->join('employee', 'employee.code = sales_invoice.sales_code');
            $this->db->join('customer', 'customer.code = sales_invoice.customer_code');
            $this->db->where('sales_invoice.do_status', 1);
			$this->db->where('sales_invoice.deleted', 0);
			$this->db->where('sales_invoice.payment_status !=', 1);
            if($post['from_date'] != "")
            {
                $this->db->where('sales_invoice.date >=',date('Y-m-d', strtotime($post['from_date'])));
            }
            if($post['to_date'] != "")
            {
                $this->db->where('sales_invoice.date <=',date('Y-m-d', strtotime($post['to_date'])));
            }
            if($post['customer_code'] != "")
            {
                $this->db->where('sales_invoice.customer_code', $post['customer_code']);
			}    
			if($post['sales_code'] != "")
			{
				$this->db->where('sales_invoice.sales_code', $post['sales_code']);
			}
			if($post['payment_status'] != "")
			{
				$this->db->where('sales_invoice.payment_status', $post['payment_status']);
			}			
            $this->db->group_by('sales_invoice.id');
            $data = $this->db->get()->result_array();
            $grandtotal =0; $account_payable =0; $total_transaction =0;
            foreach($data AS $info)
            {
				$grandtotal      = $grandtotal+$info['grandtotal'];
				$account_payable = $account_payable+$info['account_payable'];
                $total_transaction++;
            }            
            $result = array(
				'grandtotal'    => number_format($grandtotal, 0,".",","),
				'account_payable'    => number_format($account_payable, 0,".",","),
                'total_transaction'    => $total_transaction
            );
			header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
    }

    public function sales_receivable()
    {        
        if($this->system->check_access('report/sales_receivable', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('sales_invoice.id AS id, sales_invoice.date, sales_invoice.invoice, sales_invoice.payment, sales_invoice.due_date, DATEDIFF(sales_invoice.due_date, CURRENT_DATE()) AS remaining_time, sales_invoice.grandtotal, sales_invoice.account_payable, sales_invoice.cheque_payable, customer.name AS name_c, employee.name AS name_s, sales_invoice.payment_status,
								sales_invoice.invoice AS search_invoice')
								->from('sales_invoice')
								->join('employee', 'employee.code = sales_invoice.sales_code')
								->join('customer', 'customer.code = sales_invoice.customer_code')
								->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1)->where('sales_invoice.payment_status !=', 1);
				if($post['from_date'] != "")
				{
					$this->datatables->where('sales_invoice.date >=',date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('sales_invoice.date <=',date('Y-m-d', strtotime($post['to_date'])));
				}            
				if($post['customer_code'] != "")
				{
					$this->datatables->where('sales_invoice.customer_code', $post['customer_code']);
				}
				if($post['sales_code'] != "")
				{
					$this->datatables->where('sales_invoice.sales_code', $post['sales_code']);
				}
				if($post['payment_status'] != "")
				{
					$this->datatables->where('sales_invoice.payment_status', $post['payment_status']);
				}				
				$this->datatables->group_by('sales_invoice.id');		
				$this->datatables->add_column('invoice', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'" target="_blank"><b>$2</b></a>
				', 'encrypt_custom(id),invoice');
				$this->datatables->add_column('pay_action', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/create/$1').'" target="_blank">BAYAR</a>
				', 'encrypt_custom(id)');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array( "title" => "Piutang Penjualan");
				$footer = array("script" => ['report/sales/sales_receivable_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/sales_receivable_report');
				$this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        } 
	}	

	public function print_sales_receivable_report()
    {
		if($this->system->check_access('report/print_sales_receivable', 'A'))
        {
			if($this->input->method() === 'post')
			{			                                                
				$data_activity = [
					'information' => 'MENCETAK LAPORAN DAFTAR PIUTANG PENJUALAN',
					'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$post = $this->input->post();
				$customer = ($post['customer_code'] == "") ? "SEMUA PELANGGAN" : $this->crud->get_where_select('name', 'customer', ['code' => $post['customer_code']])->row_array();
				$filter = [
					'from_date' => format_date($post['from_date']),
					'to_date'   => format_date($post['to_date']),
					'customer_code'  => $post['customer_code'],
					'sales_code' => $post['sales_code']
				];				
				$data = [
					'title'      => 'Laporan Daftar Piutang Penjualan',
					'perusahaan' => $this->global->company(),
					'filter'	 => $filter,
					'data'	     => $this->finance_report->print_sales_receivable_report($filter)
				];
				// echo json_encode($data['data']); die;
				// $mpdf = new \Mpdf\Mpdf([
				// 	'orientation' => 'L',
				// 	'margin_left' => 3,
				// 	'margin_right' => 3,
				// 	'margin_top' => 25,
				// 	'margin_bottom' => 10,
				// 	'margin_header' => 5,
				// 	'margin_footer' => 5
				// ]);
				// $mpdf->SetHTMLHeader('
				// 	<div style="font-weight: bold; font-size:16px;">
				// 		<u>LAPORAN DAFTAR PIUTANG PENJUALAN</u>
				// 	</div>
				// 	<table style="width:100%; font-size:14px;">
				// 		<tbody>
				// 			<tr>
				// 				<td colspan="2">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
				// 			</tr>
				// 			<tr>
				// 				<td style="border-bottom: 1px solid black;">Pelanggan: '.$customer.'</td>
				// 				<td style="border-bottom: 1px solid black;">Sales: '.$filter['sales_code'].'</td>
				// 			</tr>
				// 		</tbody>
				// 	</table>
				// ');
				// $mpdf->SetHTMLFooter('
				// 	<table width="100%">
				// 		<tr>
				// 			<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
				// 			<td align="center">{PAGENO}/{nbpg}</td>
				// 		</tr>
				// 	</table>'
				// );
				// $data = $this->load->view('finance/print_sales_receivable_report', $data, true);
				// $mpdf->WriteHTML($data);
				// $mpdf->Output();
				$this->load->view('finance/print_sales_receivable_report', $data);
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

	public function print_sales_billing_report()
    {
		if($this->system->check_access('report/print_sales_billing', 'A'))
        {
			if($this->input->method() === 'post')
			{			                                                
				$data_activity = [
					'information' => 'MENCETAK DAFTAR PENAGIHAN PENJUALAN',
					'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$post = $this->input->post();
				$customer = ($post['customer_code'] == "") ? "SEMUA PELANGGAN" : $this->crud->get_where_select('name', 'customer', ['code' => $post['customer_code']])->row_array();
				$filter = [
					'from_date' => format_date($post['from_date']),
					'to_date'   => format_date($post['to_date']),
					'customer_code'  => $post['customer_code'],
					'sales_code' => $post['sales_code']
				];				
				$data = [
					'title'      => 'Daftar Penagihan Penjualan',
					'perusahaan' => $this->global->company(),
					'filter'	 => $filter,
					'data'	     => $this->finance_report->print_sales_receivable_report($filter)
				];
				$mpdf = new \Mpdf\Mpdf([
					'format' => [210, 330],
					'orientation' => 'L',
					'margin_left' => 3,
					'margin_right' => 3,
					'margin_top' => 3,
					'margin_bottom' => 35,
					'margin_header' => 3,
					'margin_footer' => 5
				]);
				$mpdf->SetHTMLFooter(
					'<table width="100%">
					<tr>
						<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
						<td align="center">{PAGENO}/{nbpg}</td>
					</tr>
					</table>'
				);
				$mpdf->DefHTMLFooterByName(
					'LastPageFooter',
					'						
					<table style="width:100%; text-align:center;" border="0">
						<tr>
							<td>CHECKER</td>
							<td>SALESMAN</td>
							<td>PENERIMA</td>
						</tr>
						<tr>
							<td style="height:35px;">&nbsp;</td>
						</tr>
						<tr>
							<td><p>(___________________________)</p></td>
							<td><p>(___________________________)</p></td>
							<td><p>(___________________________)</p></td>
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
				// $this->load->view('finance/print_sales_billing_report', $data);
				$data = $this->load->view('finance/print_sales_billing_report', $data, true);
				$mpdf->WriteHTML($data);
				$mpdf->Output();				
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

	// CUSTOMER'S SALES RECEIVABLE
	public function total_customer_sales_receivable()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			header('Content-Type: application/json');
			$this->db->select('customer.id AS id_c, customer.name AS name_c, sum(sales_invoice.grandtotal) AS grandtotal, sum(sales_invoice.account_payable) AS account_payable, sum(sales_invoice.cheque_payable) AS cheque_payable, sales.name AS name_s, zone.name AS name_z')
							->from('sales_invoice')
							->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
							->join('customer', 'customer.code = sales_invoice.customer_code')
							->join('zone', 'customer.zone_id = zone.id', 'left')
							->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1)->where('sales_invoice.payment_status !=', 1);
			if($post['from_date'] != "")
			{
				$this->db->where('sales_invoice.date >=', format_date($post['from_date']));
			}
			if($post['to_date'] != "")
			{
				$this->db->where('sales_invoice.date <=', format_date($post['to_date']));
			}
			if($post['customer_code'] != "")
			{
				$this->db->where('sales_invoice.customer_code', $post['customer_code']);
			}
			if($post['sales_code'] != "")
			{
				$this->db->where('sales_invoice.sales_code', $post['sales_code']);
			}
			if($post['zone_id'] != "")
				{
					$this->db->where('customer.zone_id', $post['zone_id']);
				}
			$data = $this->db->group_by('customer.id')->get()->result_array();
			$grandtotal =0; $account_payable =0; $total_transaction =0;
            foreach($data AS $info)
            {
				$grandtotal      = $grandtotal+$info['grandtotal'];
				$account_payable = $account_payable+$info['account_payable']+$info['cheque_payable'];
                $total_transaction++;
            }            
            $result = array(
				'grandtotal'     	=> number_format($grandtotal, 2,".",","),
				'account_payable'   => number_format($account_payable, 2,".",",")
            );
			header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function customer_sales_receivable()
    {        
        if($this->system->check_access('report/customer_sales_receivable', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('customer.id AS id_c, customer.name AS name_c, sum(sales_invoice.grandtotal) AS grandtotal, sum(sales_invoice.account_payable) AS account_payable, sum(sales_invoice.cheque_payable) AS cheque_payable, sales.name AS name_s, zone.name AS name_z')
								->from('sales_invoice')
								->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
								->join('customer', 'customer.code = sales_invoice.customer_code')
								->join('zone', 'customer.zone_id = zone.id', 'left')
								->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1)->where('sales_invoice.payment_status !=', 1);
				if($post['from_date'] != "")
				{
					$this->datatables->where('sales_invoice.date >=', format_date($post['from_date']));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('sales_invoice.date <=', format_date($post['to_date']));
				}
				if($post['customer_code'] != "")
				{
					$this->datatables->where('sales_invoice.customer_code', $post['customer_code']);
				}
				if($post['sales_code'] != "")
				{
					$this->datatables->where('sales_invoice.sales_code', $post['sales_code']);
				}
				if($post['zone_id'] != "")
				{
					$this->datatables->where('customer.zone_id', $post['zone_id']);
				}
				$this->datatables->group_by('customer.id');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array( "title" => "Piutang Per Pelanggan");
				$footer = array("script" => ['report/finance/customer_sales_receivable_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/customer_sales_receivable_report');
				$this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        } 
	}

	public function print_customer_global_sales_receivable_report()
    {
		if($this->system->check_access('report/print_customer_global_sales_receivable_report', 'A'))
        {
			if($this->input->method() === 'post')
			{			                                                
				$data_activity = [
					'information' => 'MENCETAK LAPORAN DAFTAR PIUTANG GLOBAL PER PELANGGAN',
					'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$post = $this->input->post();
				$customer = ($post['customer_code'] == "") ? "SEMUA PELANGGAN" : $this->crud->get_where_select('name', 'customer', ['code' => $post['customer_code']])->row_array();
				$filter = [
					'from_date' => format_date($post['from_date']),
					'to_date'   => format_date($post['to_date']),
					'customer_code'  => $post['customer_code'],
					'sales_code' => $post['sales_code']
				];				
				$data = [
					'title'      => 'Laporan Daftar Piutang Global Per Pelanggan',
					'perusahaan' => $this->global->company(),
					'filter'	 => $filter,
					'data'	     => $this->finance_report->print_customer_global_sales_receivable_report($filter)
				];
				$this->load->view('finance/print_customer_global_sales_receivable_report', $data);
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

    // PAYMENT OF RECEIVEABLE
    public function payment_of_receivable()
    {
        if($this->system->check_access('report/payment_of_receivable', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('por.id AS id_por, por.code AS code_por, por.date, por.method, (por.grandtotal-por.cost) AS grandtotal,
								customer.name AS customer,
								por.code AS search_code_por');
				$this->datatables->from('payment_ledger AS por');
				$this->datatables->join('payment_ledger_transaction AS por_transaction', 'por_transaction.pl_id = por.id');
				$this->datatables->join('sales_invoice AS si', 'si.id = por_transaction.transaction_id');
				$this->datatables->join('customer', 'customer.code = si.customer_code');
				$this->datatables->where('por.transaction_type', 2);
				$this->datatables->where('por.deleted', 0);
				if($post['from_date'] != "")
				{
					$this->datatables->where('por.date >=', format_date($post['from_date']));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('por.date <=', format_date($post['to_date']));
				}
				if($post['customer_code'] != "")
				{
					$this->datatables->where('customer.code', $post['customer_code']);
				}
				if(isset($post['method']))
				{
				    foreach($post['method'] AS $info)
				    {
				        $this->datatables->like('por.method', $info);
				    }
				}
				$this->datatables->group_by('por.id');
				$this->datatables->add_column('code_por',
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_por), code_por');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Pembayaran Penjualan");      
				$footer = array("script" => ['report/sales/payment_of_receivable_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/payment_of_receivable_report');
				$this->load->view('include/footer', $footer);
			}            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}

	// CHEQUE OF RECEIVABLE
	public function cheque_in()
	{
		if($this->system->check_access('report/cheque_in', 'A'))
		{
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('por.id AS id_por, por.code AS code_por, por.date,
								por_detail.cheque_number, por_detail.cheque_open_date, por_detail.cheque_close_date, por_detail.cheque_status, por_detail.cheque_acquittance_date, por_detail.amount, 
								por.to_pl, customer.name AS customer,
								por.code AS search_code_por, sales_invoice.invoice AS search_invoice');
				$this->datatables->from('payment_ledger AS por');
				$this->datatables->join('payment_ledger_detail AS por_detail', 'por_detail.pl_id = por.id');
				$this->datatables->join('payment_ledger_transaction AS por_transaction', 'por_transaction.pl_id= por.id');
				$this->datatables->join('sales_invoice', 'sales_invoice.id = por_transaction.transaction_id');
				$this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
				$this->datatables->where('por.transaction_type', 2);
				$this->datatables->where('por_detail.method', json_encode(["3"]));
				if($post['from_date'] != "")
				{
					$this->datatables->where('por_detail.cheque_close_date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('por_detail.cheque_close_date <=', date('Y-m-d', strtotime($post['to_date'])));
				}
				if($post['customer_code'] != "")
				{
					$this->datatables->where('customer.code', $post['customer_code']);
				}	
				if($post['cheque_status'] != "")
				{										
					$this->datatables->where('por_detail.cheque_status', $post['cheque_status']);					
				}			
				$this->datatables->group_by('por_detail.id');
				$this->datatables->add_column('code_por', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id_por), code_por');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Piutang Cek/Giro (Masuk)");
				$footer = array("script" => ['report/finance/cheque_in_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/cheque_in_report');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}        
	}

	public function cheque_of_receivable()
	{
		if($this->system->check_access('report/cheque_of_receivable', 'A'))
		{
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('por.id AS id, por.code, por.date, sales_invoice.id AS sale_invoice_id, sales_invoice.invoice, por.payment, por.grandtotal, customer.name AS customer, por.cheque_status,
								por.code AS search_code_por, sales_invoice.invoice AS search_invoice');
				$this->datatables->from('payment_ledger AS por');
				$this->datatables->join('sales_invoice', 'sales_invoice.id = por.transaction_id');
				$this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
				$this->datatables->where('por.transaction_type', 2);
				$this->datatables->where('por.deleted', 0);
				$this->datatables->where('por.cheque_account_id !=', 0);
				$this->datatables->where('por.cheque_status', 2);
				if($post['from_date'] != "")
				{
					$this->datatables->where('por.date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('por.date <=', date('Y-m-d', strtotime($post['to_date'])));
				}
				if($post['customer_code'] != "")
				{
					$this->datatables->where('customer.code', $post['customer_code']);
				}
				$this->datatables->group_by('por.id');
				$this->datatables->add_column('code', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id),code');
				$this->datatables->add_column('invoice', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(sale_invoice_id),invoice');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Piutang Cek/Giro");
				$footer = array("script" => ['report/finance/cheque_of_receivable_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/cheque_of_receivable_report');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}        
	}
	
	public function cheque_in_out()
	{
		if($this->system->check_access('report/cheque_in_out', 'A'))
		{
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('pl.id AS id, pl.code, pl.date, sales_invoice.id AS sale_invoice_id, sales_invoice.invoice, pl.payment, pl.grandtotal, customer.name AS customer, pl.cheque_status,
								pl.code AS search_code_pl, sales_invoice.invoice AS search_invoice');
				$this->datatables->from('payment_ledger AS pl');
				// $this->datatables->where('pl.transaction_type', 2);
				$this->datatables->where('pl.deleted', 0);
				$this->datatables->where('pl.cheque_number !=', null);
				// $this->datatables->where('pl.cheque_status', 2);
				if($post['from_date'] != "")
				{
					$this->datatables->where('pl.date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('pl.date <=', date('Y-m-d', strtotime($post['to_date'])));
				}
				$this->datatables->group_by('pl.cheque_number');
				$this->datatables->add_column('code', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/receivable/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id),code');
				$this->datatables->add_column('invoice', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(sale_invoice_id),invoice');
				echo $this->datatables->generate();
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT LAPORAN CEK/GIRO MASUK/KELUAR',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Cek/Giro (Masuk/Keluar)");
				$footer = array("script" => ['report/finance/cheque_in_out_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/cheque_in_out_report');
				$this->load->view('include/footer', $footer);
			}			
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('dashboard'));
		}
	}

	// EXPENSE
    public function get_expense_cost_report()
    {
		if($this->input->is_ajax_request())
		{            
            $data = $this->db->select('cost.id, cost.name')
                             ->from('cost')->join('expense', 'expense.cost_id = cost.id')
                             ->where('cost.deleted', 0)->group_by('cost.id')
                             ->get()->result();
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
    
    public function get_expense_employee_report()
    {
		if($this->input->is_ajax_request())
		{            
            $data = $this->db->select('employee.code, employee.name')
                             ->from('employee')
                             ->join('expense', 'expense.employee_code = employee.code')
                             ->where('employee.deleted', 0)->group_by('employee.id')
                             ->get()->result();
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
	
    public function get_total_expense_report()
    {        
        if($this->input->is_ajax_request())
		{
            $post           = $this->input->post();
            $from_date      = $post['from_date'];
            $to_date        = $post['to_date'];        
            $cost_id        = $post['cost_id'];
            $employee_code  = $post['employee_code'];
            
            $this->db->select('sum(expense.amount) AS grandtotal');
            $this->db->from('expense');
            $this->db->join('cost', 'cost.id = expense.cost_id');
            $this->db->join('employee', 'employee.code = expense.employee_code');
            if($from_date != "")
            {
                $this->db->where('expense.date >=', date('Y-m-d', strtotime($from_date)));
            }
            if($to_date != "")
            {
                $this->db->where('expense.date <=', date('Y-m-d', strtotime($to_date)));
            }   
            if($cost_id != "")
            {
                $this->db->where('expense.cost_id', $cost_id);
            }             
            if($employee_code != "")
            {
                $this->db->where('expense.employee_code', $employee_code);
            }        
            $this->db->where('expense.deleted', 0);
            $data = $this->db->get()->row_array();           
            
            header('Content-Type: application/json');
            $result = array(
                'grandtotal' => number_format($data['grandtotal'],0,".",","),
            );

            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
	}
	
	public function expense()
    {  
        if($this->system->check_access('report/expense', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				$from_date      = $post['from_date'];
				$to_date        = $post['to_date'];        
				$cost_id        = $post['cost_id'];
				$employee_code  = $post['employee_code'];
				header('Content-Type: application/json');        
				$this->datatables->select('expense.id, date, cost.name AS name_c, expense.code, amount, expense.information, employee.name AS name_e');
				$this->datatables->from('expense');
				$this->datatables->join('cost', 'cost.id = expense.cost_id');
				$this->datatables->join('employee', 'employee.code = expense.employee_code');
				if($from_date != "")
				{
					$this->datatables->where('expense.date >=', date('Y-m-d', strtotime($from_date)));
				}
				if($to_date != "")
				{
					$this->datatables->where('expense.date <=', date('Y-m-d', strtotime($to_date)));
				} 
				if($cost_id != "")
				{
					$this->datatables->where('expense.cost_id', $cost_id);
				}                
				if($employee_code != "")
				{
					$this->datatables->where('expense.employee_code', $employee_code);
				}        
				$this->datatables->where('expense.deleted', 0);
				echo $this->datatables->generate(); 
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT LAPORAN BIAYA',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Biaya");        
				$footer = array("script" => ['report/expense_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/expense_report');
				$this->load->view('include/footer', $footer);
			}               
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }              
	}
	
	// COLLECT CASHIER
	public function get_collect_employee_report()
    {
		if($this->input->is_ajax_request())
		{            
            $data = $this->db->select('employee.code, employee.name')
                             ->from('employee')
                             ->join('collect', 'collect.collector = employee.code')
                             ->where('employee.deleted', 0)->group_by('employee.id')
                             ->get()->result();
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

    public function collect()
    {   
        if($this->system->check_access('report/collect', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('collect.id, collect.date, collect.time,  collect.total, collector.name AS collector, cashier.name AS cashier');
				$this->datatables->from('collect');
				$this->datatables->join('employee AS collector', 'collector.code = collect.collector');
				$this->datatables->join('employee AS cashier', 'cashier.code = collect.cashier');
				$this->datatables->group_by('collect.id');
				$this->datatables->where('collect.deleted', 0);
				if($post['from_date'] != "")
				{
					$this->datatables->where('collect.date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('collect.date <=', date('Y-m-d', strtotime($post['to_date'])));
				}
				if($post['collector_code'] != "")
				{
					$this->datatables->where('collect.collector', $post['collector_code']);
				}
				if($post['cashier_code'] != "")
				{
					$this->datatables->where('collect.cashier', $post['cashier_code']);
				}
				echo $this->datatables->generate();
			}
			else
			{
				$header = array( "title" => "Daftar Collect Kasir");
				$footer = array("script" => ['report/finance/collect_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/collect_report');
				$this->load->view('include/footer', $footer);
			}			            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}
	
	// CASHIER
    public function get_cashier()
    {
        $data       = $this->db->select('employee.id, employee.code, employee.name')
							->from('employee')->join('pos', 'pos.cashier = employee.code')
							->where('pos.deleted', 0)->where('employee.deleted', 0)
							->group_by('employee.id')
							->get()->result();
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
    
    public function close_cashier_report()
    {
        if($this->input->is_ajax_request())
		{
                $post = $this->input->post();
				// Data Cashier
				$cashier= $this->crud->get_where('cashier', ['status' => 0, 'cashier' => $post['code_e']])->row_array();
				// Data DP
				$where_dp = [
					'date' 			=> $cashier['date'], 
					'employee_code' => $cashier['cashier'],
					'payment'       => 2,
					'created >=' 	=> $cashier['date'].' '.$cashier['open_time'], 
					'created <=' 	=> $cashier['date'].' 23:59:59'		
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
					'created <=' 	=> $cashier['date'].' 23:59:59'		
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
					'cashier' => $cashier['cashier']
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
					'created <=' => $cashier['date'].' 23:59:59'
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
					'created <=' => $cashier['date'].' 23:59:59'
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
                    $response = [
                        'status'	=> [
                            'code'  	=> 200,
                            'message'   => 'Berhasil Menutup Kasir',
                        ],
                        'response'  => ''
                    ];
                    echo json_encode($response);
                }
                else
                {
                    $response   =   [
                        'status'    => [
                            'code'      => 401,
                            'message'   => 'Gagal Menutup Kasir',
                        ],
                        'response'  => ''
                    ];
                    echo json_encode($response);
                }
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

    public function cashier()
    {  
        if($this->system->check_access('report/cashier', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('cashier.id, cashier.date, cashier.open_time, cashier.close_time, (cashier.modal+cashier.total_dp+cashier.total_account_receivable+cashier.total_sales+cashier.total_pos) AS income, (cashier.total_payment_of_debt+cashier.total_sales_return+cashier.total_expense+cashier.total_collect) AS outcome, cashier.grandtotal, cashier.status AS status_c, employee.code AS code_e, employee.name AS name_e');
				$this->datatables->from('cashier');
				$this->datatables->join('employee', 'employee.code = cashier.cashier');        
				$this->datatables->where('cashier.deleted', 0);        
				if($post['from_date'] != "")
				{
					$this->datatables->where('cashier.date >=', date('Y-m-d', strtotime($post['from_date'])));
				}
				if($post['to_date'] != "")
				{
					$this->datatables->where('cashier.date <=', date('Y-m-d', strtotime($post['to_date'])));
				}
				if($post['cashier_code'] != "")
				{
					$this->datatables->where('cashier.cashier', $post['cashier_code']);
				}                       
				$this->datatables->group_by('cashier.id'); 
				$this->datatables->add_column('action', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('pos/cashier/summary/$1').'"><i class="fa fa-eye"></i></a>
				', 'encrypt_custom(id)');       
				echo $this->datatables->generate();  
			}
			else
			{
				$header = array("title" => "Rekap Kasir");        
				$footer = array("script" => ['report/sales/cashier_report.js?v='.md5(time())]);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/cashier_report');
				$this->load->view('include/footer', $footer);
			}            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }              
	}
	
	// CASH
	public function total_cash()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			if($post['account_id'] != "")
			{
				$where_last_balance = [
					'cl_type'    => 1,
					'account_id' => $post['account_id'],
					'date <'     => format_date($post['from_date']),
					'deleted'    => 0
				];
				$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
				$filter = [
					'cl_type' => 1,
					'from_date' =>  $post['from_date'],
					'to_date' =>  $post['to_date'],
					'account_id' =>  $post['account_id']
				];
				$cash_ledger = $this->finance_report->cash_ledger_transaction($filter);
				$total_debit = 0; $total_credit = 0; $end_balance = $last_balance['balance'];
				foreach($cash_ledger AS $info_cash_ledger)
				{
					if($info_cash_ledger['method'] == 1)
					{
						$total_debit=$total_debit+$info_cash_ledger['amount'];
						$end_balance = $end_balance+$info_cash_ledger['amount'];
					}   					 
					elseif($info_cash_ledger['method'] == 2)
					{
						$total_credit=$total_credit+$info_cash_ledger['amount'];
						$end_balance = $end_balance-$info_cash_ledger['amount'];
					}				
				}
				$result = array(
					'last_balance' => number_format($last_balance['balance'], 2,".",","),
					'total_debit'  => number_format($total_debit, 2,".",","),
					'total_credit' => number_format($total_credit, 2,".",","),
					'end_balance'  => number_format($end_balance, 2,".",",")
				);
			}
			else
			{
				$result = array(
					'last_balance' => number_format(0, 2,".",","),
					'total_debit'  => number_format(0, 2,".",","),
					'total_credit' => number_format(0, 2,".",","),
					'end_balance'  => number_format(0, 2,".",",")
				);
			}
			header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function cash()
    {
        if($this->system->check_access('report/cash_ledger/cash', 'A'))
        {
            if($this->input->is_ajax_request())
            {
				$post = $this->input->post();
				if($post['account_id'] != "")
                {
                    $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                    $this->datatables->from('cash_ledger AS cl');                
                    $this->datatables->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');
                    $this->datatables->where('cl.cl_type', 1);
                    $this->datatables->where('cl.deleted', 0);
					$this->datatables->where('cl.account_id', $post['account_id']);
					if($post['from_date'] != "")
					{
						$this->datatables->where('cl.date >=', format_date($post['from_date']));
					}
					if($post['to_date'] != "")
					{
						$this->datatables->where('cl.date <=', format_date($post['to_date']));
					}
                    $this->datatables->group_by('cl.id');
                    $this->datatables->order_by('cl.date', 'DESC');
					$this->datatables->order_by('cl.id','DESC');
					$this->datatables->add_column('transaction_id', 
                    '$1', 'encrypt_custom(transaction_id)');
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
					'information' => 'MELIHAT LAPORAN BUKU KAS',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Buku Kas");
				$data = [
                    'last_balance_cash' => $this->cash_ledger->last_balance_cash()
                ];
                $footer = array("script" => ['report/finance/cash_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('finance/cash_report', $data);
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}

	public function print_cash_report()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$account = $this->crud->get_where('cash_ledger_account', ['id' => $post['account_id']])->row_array();
			$this->db->select('cl.id AS id_cl, cla.name AS name, date, invoice, information, note, amount, method, balance');
			$this->db->from('cash_ledger AS cl');
			$this->db->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');
			$this->db->where('cl.cl_type', 1);
			$this->db->where('cl.deleted', 0);
			if($post['from_date'] != "")
			{
				$this->db->where('cl.date >=', format_date($post['from_date']));
			}
			if($post['to_date'] != "")
			{
				$this->db->where('cl.date <=', format_date($post['to_date']));
			}
			if($post['account_id'] != "")
			{
				$this->db->where('cl.account_id', $post['account_id']);
			}
			$cash_ledger = $this->db->group_by('cl.id')->order_by('cl.date', 'ASC')->order_by('cl.id','ASC')->get()->result_array();
			$where_last_balance = [
				'cl_type'    => 1,
				'account_id' => $post['account_id'],
				'date <'     => format_date($post['from_date']),
				'deleted'    => 0
			];
			$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$data = [
                'title'      => 'Laporan Kas ',
                'perusahaan' => $this->global->company(),
				'last_balance' => $last_balance,
				'cash_ledger'=> $cash_ledger
			];
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 330],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 25,
				'margin_bottom' => 10,
				'margin_header' => 5,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-weight: bold; font-size:16px;">
					<u>LAPORAN KAS - '.$account['name'].'</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
							<td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>
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
			$data = $this->load->view('finance/print_cash_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
			$this->load->view('auth/show_404');			
		}		
	}
	
	// BANK
	public function total_bank()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			if($post['account_id'] != "")
			{
				$where_last_balance = [
					'cl_type'    => 2,
					'account_id' => $post['account_id'],
					'date <'     => format_date($post['from_date']),
					'deleted'    => 0
				];
				$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
				$filter = [
					'cl_type' => 2,
					'from_date' =>  $post['from_date'],
					'to_date' =>  $post['to_date'],
					'account_id' =>  $post['account_id']
				];
				$cash_ledger = $this->finance_report->cash_ledger_transaction($filter);
				$total_debit = 0; $total_credit = 0; $end_balance = $last_balance['balance'];
				foreach($cash_ledger AS $info_cash_ledger)
				{
					if($info_cash_ledger['method'] == 1)
					{
						$total_debit=$total_debit+$info_cash_ledger['amount'];
						$end_balance = $end_balance+$info_cash_ledger['amount'];
					}   					 
					elseif($info_cash_ledger['method'] == 2)
					{
						$total_credit=$total_credit+$info_cash_ledger['amount'];
						$end_balance = $end_balance-$info_cash_ledger['amount'];
					}				
				}
				$result = array(
					'last_balance' => number_format($last_balance['balance'], 2,".",","),
					'total_debit'  => number_format($total_debit, 2,".",","),
					'total_credit' => number_format($total_credit, 2,".",","),
					'end_balance'  => number_format($end_balance, 2,".",",")
				);
			}
			else
			{
				$result = array(
					'last_balance' => number_format(0, 2,".",","),
					'total_debit'  => number_format(0, 2,".",","),
					'total_credit' => number_format(0, 2,".",","),
					'end_balance'  => number_format(0, 2,".",",")
				);
			}
			header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function bank()
    {
        if($this->system->check_access('report/cash_ledger/bank', 'A'))
        {
            if($this->input->is_ajax_request())
            {
				$post = $this->input->post();
				if($post['account_id'] != null || $post['account_id'] != "")
                {
                    $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                    $this->datatables->from('cash_ledger AS cl');                
                    $this->datatables->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');
                    $this->datatables->where('cl.cl_type', 2);
                    $this->datatables->where('cl.deleted', 0);
					$this->datatables->where('cl.account_id', $post['account_id']);
					if($post['from_date'] != "")
					{
						$this->datatables->where('cl.date >=', format_date($post['from_date']));
					}
					if($post['to_date'] != "")
					{
						$this->datatables->where('cl.date <=', format_date($post['to_date']));
					}
					$this->datatables->add_column('transaction_id', 
                    '$1', 'encrypt_custom(transaction_id)');
                    $this->datatables->group_by('cl.id');
                    $this->datatables->order_by('cl.date', 'DESC');
                    $this->datatables->order_by('cl.id','DESC');
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
					'information' => 'MELIHAT LAPORAN BUKU BANK',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
                $header = array("title" => "Laporan Buku Bank");
                $footer = array("script" => ['report/finance/bank_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('finance/bank_report');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}
	
	public function print_bank_report()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$account = $this->crud->get_where('cash_ledger_account', ['id' => $post['account_id']])->row_array();
			$this->db->select('cl.id AS id_cl, cla.name AS name, date, invoice, information, note, amount, method, balance');
			$this->db->from('cash_ledger AS cl');
			$this->db->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');
			$this->db->where('cl.cl_type', 2);
			$this->db->where('cl.deleted', 0);
			if($post['from_date'] != "")
			{
				$this->db->where('cl.date >=', format_date($post['from_date']));
			}
			if($post['to_date'] != "")
			{
				$this->db->where('cl.date <=', format_date($post['to_date']));
			}
			if($post['account_id'] != "")
			{
				$this->db->where('cl.account_id', $post['account_id']);
			}
			$cash_ledger = $this->db->group_by('cl.id')->order_by('cl.date', 'ASC')->order_by('cl.id','ASC')->get()->result_array();			
			$where_last_balance = [
				'cl_type'    => 2,
				'account_id' => $post['account_id'],
				'date'       => date('Y-m-d',strtotime(format_date($post['from_date']) . "-1 days")),
				'deleted'    => 0
			];
			$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$data = [
                'title'      => 'Laporan Bank ',
                'perusahaan' => $this->global->company(),
				'last_balance' => $last_balance,
				'cash_ledger'=> $cash_ledger
			];
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 330],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 25,
				'margin_bottom' => 10,
				'margin_header' => 5,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-weight: bold; font-size:16px;">
					<u>LAPORAN BANK - '.$account['name'].'</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
							<td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>
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
			$data = $this->load->view('finance/print_bank_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
			$this->load->view('auth/show_404');			
		}		
	}

	// SUPPLIER DEPOSIT
	public function total_supplier_deposit()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			if($post['account_id'] != "")
			{
				$where_last_balance = [
					'cl_type'    => 3,
					'account_id' => $post['account_id'],
					'date <'     => format_date($post['from_date']),
					'deleted'    => 0
				];
				$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
				$filter = [
					'cl_type' => 3,
					'from_date' =>  $post['from_date'],
					'to_date' =>  $post['to_date'],
					'account_id' =>  $post['account_id']
				];
				$cash_ledger = $this->finance_report->cash_ledger_transaction($filter);
				$total_debit = 0; $total_credit = 0; $end_balance = $last_balance['balance'];
				foreach($cash_ledger AS $info_cash_ledger)
				{
					if($info_cash_ledger['method'] == 1)
					{
						$total_debit=$total_debit+$info_cash_ledger['amount'];
						$end_balance = $end_balance+$info_cash_ledger['amount'];
					}   					 
					elseif($info_cash_ledger['method'] == 2)
					{
						$total_credit=$total_credit+$info_cash_ledger['amount'];
						$end_balance = $end_balance-$info_cash_ledger['amount'];
					}				
				}
				$result = array(
					'last_balance' => number_format($last_balance['balance'], 2,".",","),
					'total_debit'  => number_format($total_debit, 2,".",","),
					'total_credit' => number_format($total_credit, 2,".",","),
					'end_balance'  => number_format($end_balance, 2,".",",")
				);
			}
			else
			{
				$result = array(
					'last_balance' => number_format(0, 2,".",","),
					'total_debit'  => number_format(0, 2,".",","),
					'total_credit' => number_format(0, 2,".",","),
					'end_balance'  => number_format(0, 2,".",",")
				);
			}
			header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function supplier_deposit()
    {
        if($this->system->check_access('report/supplier_deposit', 'A'))
        {
            if($this->input->is_ajax_request())
            {
				$post = $this->input->post();
				if($post['account_id'] != "")
                {
                    $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                    $this->datatables->from('cash_ledger AS cl');                
                    $this->datatables->join('supplier AS cla', 'cla.id = cl.account_id');
                    $this->datatables->where('cl.cl_type', 3);
                    $this->datatables->where('cl.deleted', 0);
					$this->datatables->where('cl.account_id', $post['account_id']);
					if($post['from_date'] != "")
					{
						$this->datatables->where('cl.date >=', format_date($post['from_date']));
					}
					if($post['to_date'] != "")
					{
						$this->datatables->where('cl.date <=', format_date($post['to_date']));
					}
                    $this->datatables->group_by('cl.id');
                    $this->datatables->order_by('cl.date', 'DESC');
					$this->datatables->order_by('cl.id','DESC');
					$this->datatables->add_column('transaction_id', 
                    '$1', 'encrypt_custom(transaction_id)');
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
					'information' => 'MELIHAT LAPORAN DEPOSIT SUPPLIER',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
                $header = array("title" => "Laporan Deposit Supplier");
                $footer = array("script" => ['report/finance/supplier_deposit_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('finance/supplier_deposit_report');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}

	public function print_supplier_deposit_report()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$this->db->select('cl.id AS id_cl, cl_type, supplier.name AS name_s, transaction_type, date, invoice, information, note, amount, method, balance');
			$this->db->from('cash_ledger AS cl');
			$this->db->join('supplier', 'supplier.code = cl.account_id');			
			$this->db->where_in('cl.cl_type', 4);
			$this->db->where('cl.deleted', 0);
			if($post['from_date'] != "")
			{
				$this->db->where('cl.date >=', format_date($post['from_date']));
			}
			if($post['to_date'] != "")
			{
				$this->db->where('cl.date <=', format_date($post['to_date']));
			}
			$cl_type = "TITIPAN SUPPLIER";
			if($post['account_id'] != "")
			{				
				$this->db->where('cl.account_id', $post['account_id']);
			}
			$cash_ledger = $this->db->group_by('cl.id')->order_by('cl.date', 'desc')->order_by('cl.id','desc')->get()->result_array();			
			if($post['account_id'] != "")
			{				
				$account = $this->crud->get_where('supplier', ['code' => $post['account_id']])->row_array();
			}			
			$data = [
                'title'      => 'Laporan Kas '.$cl_type,
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'cash_ledger'=> $cash_ledger
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 330],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 25,
				'margin_bottom' => 10,
				'margin_header' => 5,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-weight: bold; font-size:16px;">
					<u>LAPORAN KAS '.strtoupper($cl_type).' </u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
							<td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>
							<td class="text-right">Akun: '.$account['name'].'</td>
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
			$data = $this->load->view('finance/print_big_small_cash_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
			$this->load->view('auth/show_404');			
		}		
	}

	// CUSTOMER DEPOSIT
	public function total_customer_deposit()
	{
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			if($post['account_id'] != "")
			{
				$where_last_balance = [
					'cl_type'    => 4,
					'account_id' => $post['account_id'],
					'date <'     => format_date($post['from_date']),
					'deleted'    => 0
				];
				$last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
				$filter = [
					'cl_type' => 4,
					'from_date' =>  $post['from_date'],
					'to_date' =>  $post['to_date'],
					'account_id' =>  $post['account_id']
				];
				$cash_ledger = $this->finance_report->cash_ledger_transaction($filter);
				$total_debit = 0; $total_credit = 0; $end_balance = $last_balance['balance'];
				foreach($cash_ledger AS $info_cash_ledger)
				{
					if($info_cash_ledger['method'] == 1)
					{
						$total_debit=$total_debit+$info_cash_ledger['amount'];
						$end_balance = $end_balance+$info_cash_ledger['amount'];
					}   					 
					elseif($info_cash_ledger['method'] == 2)
					{
						$total_credit=$total_credit+$info_cash_ledger['amount'];
						$end_balance = $end_balance-$info_cash_ledger['amount'];
					}				
				}
				$result = array(
					'last_balance' => number_format($last_balance['balance'], 2,".",","),
					'total_debit'  => number_format($total_debit, 2,".",","),
					'total_credit' => number_format($total_credit, 2,".",","),
					'end_balance'  => number_format($end_balance, 2,".",",")
				);
			}
			else
			{
				$result = array(
					'last_balance' => number_format(0, 2,".",","),
					'total_debit'  => number_format(0, 2,".",","),
					'total_credit' => number_format(0, 2,".",","),
					'end_balance'  => number_format(0, 2,".",",")
				);
			}
			header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function customer_deposit()
    {
        if($this->system->check_access('report/customer_deposit', 'A'))
        {
            if($this->input->is_ajax_request())
            {
				$post = $this->input->post();
				if($post['account_id'] != "")
                {
                    $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                    $this->datatables->from('cash_ledger AS cl');                
                    $this->datatables->join('customer AS cla', 'cla.id = cl.account_id');
                    $this->datatables->where('cl.cl_type', 4);
                    $this->datatables->where('cl.deleted', 0);
					$this->datatables->where('cl.account_id', $post['account_id']);
					if($post['from_date'] != "")
					{
						$this->datatables->where('cl.date >=', format_date($post['from_date']));
					}
					if($post['to_date'] != "")
					{
						$this->datatables->where('cl.date <=', format_date($post['to_date']));
					}
                    $this->datatables->group_by('cl.id');
                    $this->datatables->order_by('cl.date', 'DESC');
					$this->datatables->order_by('cl.id','DESC');
					$this->datatables->add_column('transaction_id', 
                    '$1', 'encrypt_custom(transaction_id)');
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
					'information' => 'MELIHAT LAPORAN DEPOSIT PELANGGAN',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
                $header = array("title" => "Laporan Deposit Pelanggan");
                $footer = array("script" => ['report/finance/customer_deposit_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('finance/customer_deposit_report');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}

	public function print_customer_deposit_report()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();
			$this->db->select('cl.id AS id_cl, cl_type, customer.name AS name_c, transaction_type, date, invoice, information, note, amount, method, balance');
			$this->db->from('cash_ledger AS cl');
			$this->db->join('customer', 'customer.code = cl.account_id');			
			$this->db->where_in('cl.cl_type', 5);
			$this->db->where('cl.deleted', 0);
			if($post['from_date'] != "")
			{
				$this->db->where('cl.date >=', format_date($post['from_date']));
			}
			if($post['to_date'] != "")
			{
				$this->db->where('cl.date <=', format_date($post['to_date']));
			}
			$cl_type = "TITIPAN PELANGGAN";
			if($post['account_id'] != "")
			{				
				$this->db->where('cl.account_id', $post['account_id']);
			}
			$cash_ledger = $this->db->group_by('cl.id')->order_by('cl.date', 'desc')->order_by('cl.id','desc')->get()->result_array();			
			if($post['account_id'] != "")
			{				
				$account = $this->crud->get_where('customer', ['code' => $post['account_id']])->row_array();
			}			
			$data = [
                'title'      => 'Laporan Kas '.$cl_type,
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'cash_ledger'=> $cash_ledger
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 330],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 25,
				'margin_bottom' => 10,
				'margin_header' => 5,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-weight: bold; font-size:16px;">
					<u>LAPORAN KAS '.strtoupper($cl_type).' </u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
							<td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>
							<td class="text-right">Akun: '.$account['name'].'</td>
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
			$data = $this->load->view('finance/print_big_small_cash_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
			$this->load->view('auth/show_404');			
		}		
	}	
	
	// PRODUCT PROFIT
    public function total_product_profit_report()
    {    
		$post       = $this->input->post();        
		$search     = $post['search'];
        $from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
        $to_date    = ($post['to_date'] == "")   ?  null : date('Y-m-d', strtotime($post['to_date']));
        $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
		$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
		$product            = $this->finance_report->get_product_profit_report($search, $from_date, $to_date, $department_code, $subdepartment_code)->result_array();
        $total_sales        = 0; $total_sales_return = 0; $total_hpp = 0; $profit = 0;
        foreach($product AS $info)
        {
            // SALES INVOICE
            $sales_invoice_detail = $this->finance_report->get_product_profit_sales_invoice_detail_report($info['code'], $from_date, $to_date);
            foreach($sales_invoice_detail AS $info_sales_invoice)
            {               
				$total_hpp = $total_hpp + ($info_sales_invoice['hpp']*($info_sales_invoice['qty']*$info_sales_invoice['unit_value']));
                $total_sales = $total_sales + $info_sales_invoice['total'];
			}             
			            
            // SALES RETURN            
            $sales_return_detail = $this->finance_report->get_product_profit_sales_return_detail_report($info['code'], $from_date, $to_date);
            foreach($sales_return_detail AS $info_sales_return)
            {
				$total_hpp = $total_hpp - ($info_sales_return['hpp']*($info_sales_return['qty']*$info_sales_return['unit_value']));
                $total_sales_return = $total_sales_return + $info_sales_return['total'];
            }
        }
        
        $profit = $total_sales - $total_sales_return - $total_hpp;
        $pembagi = $total_sales - $total_sales_return;
        if($pembagi != 0)
        {
            $prosentase = ($profit / $pembagi)*100;
        }        
        else
        {
            $prosentase = 0;
        }
		
        $output = array(
            'total_sales'        => number_format($total_sales, 0, ".", ","),
            'total_sales_return' => number_format($total_sales_return, 0, ".", ","),
            'total_hpp'          => number_format($total_hpp, 0, ".", ","),
            'profit'             => number_format($profit, 0, ".", ",").' / '.number_format($prosentase, 2, ".", ",").'%'
        );
        header('Content-Type: application/json');        
        echo json_encode($output);   
    }
    		
    public function product_profit()
    {  
        if($this->system->check_access('report/product_profit', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post       = $this->input->post();        
				$search     = $post['search'];
				$from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
				$to_date    = ($post['to_date'] == "")   ?  null : date('Y-m-d', strtotime($post['to_date']));
				$department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
				$subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
				$draw       = (!isset($post['draw']))   ? 0 : $post['draw'];
				$iLength    = (!isset($post['length'])) ? null : $post['length'];
				$iStart     = (!isset($post['start']))  ? null : $post['start'];
				$iOrder   	= (!isset($post['order']))   ? null : $post['order'];

				$total      = $this->finance_report->get_product_profit_report($search, $from_date, $to_date, $department_code, $subdepartment_code)->num_rows();
				$product    = $this->finance_report->get_product_profit_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $iLength, $iStart)->result_array();
				$data 		= array();
				foreach($product AS $info)
				{
					$total_qty_sales = 0;  $total_sales = 0; $total_qty_sales_return = 0; $total_sales_return = 0;  $total_hpp = 0; $total_qty = 0;
					// SALES INVOICE
					$sales_invoice_detail = $this->finance_report->get_product_profit_sales_invoice_detail_report($info['code'], $from_date, $to_date);
					foreach($sales_invoice_detail AS $info_sales_invoice)
					{						
						$total_qty_sales = $total_qty_sales + ($info_sales_invoice['qty']*$info_sales_invoice['unit_value']);
						$total_hpp       = $total_hpp + ($info_sales_invoice['hpp']*($info_sales_invoice['qty']*$info_sales_invoice['unit_value']));
						$total_qty       = $total_qty + ($info_sales_invoice['qty']*$info_sales_invoice['unit_value']);
						$total_sales     = $total_sales + $info_sales_invoice['total'];
					} 					
					
					// SALES RETURN            
					$sales_return_detail = $this->finance_report->get_product_profit_sales_return_detail_report($info['code'], $from_date, $to_date);
					foreach($sales_return_detail  AS $info_sales_return)
					{						
						$total_qty_sales_return = $total_qty_sales_return + ($info_sales_return['qty']*$info_sales_return['unit_value']);
						$total_hpp              = $total_hpp - $info_sales_return['hpp']*($info_sales_return['qty']*$info_sales_return['unit_value']);
						$total_qty              = $total_qty - ($info_sales_return['qty']*$info_sales_return['unit_value']);						
						$total_sales_return     = $total_sales_return + $info_sales_return['total'];
					}

					$profit = $total_sales - $total_sales_return - $total_hpp;
					$pembagi = $total_sales - $total_sales_return;
					$prosentase = ($pembagi != 0) ? $profit/($pembagi)*100 : 0;
					$data[] = array(
						'id'                 => $info['id'],                
						'code' 		         => '<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/'.$this->global->encrypt($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
						'name'               => $info['name'],
						'unit'               => $info['unit'],
						'qty_sales'          => $total_qty_sales,
						'qty_sales_return'   => $total_qty_sales_return,
						'total_qty'          => $total_qty,
						'total_sales'        => $total_sales,
						'total_hpp'          => $total_hpp,
						'profit'             => $profit,
						'prosentase'         => $prosentase
					);                      
				}
				if($iOrder != null)
				{
					$column_option = array(null, null, 'name', null, 'qty_sales', 'qty_sales_return', 'total_qty', 'total_sales', 'total_hpp', 'profit', 'prosentase');
					$column = array_column($data, $column_option[$iOrder['0']['column']]);
					$order = ($iOrder['0']['dir'] == 'asc') ? SORT_ASC : SORT_DESC;
					array_multisort($column, $order, $data);
				}
				$output = array(
					'draw'            => $draw,
					'recordsTotal'    => $total,
					'recordsFiltered' => $total,
					'data'            => $data
				);
				header('Content-Type: application/json');        
				echo json_encode($output);
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT LAPORAN PROFITABILITAS PER PRODUK',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Profitabilitas Per Produk");        
				$footer = array("script" => ['report/product_profit_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/product_profit_report');
				$this->load->view('include/footer', $footer);
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}
	
	// SALES PROFIT
	public function get_total_sales_profit_report()
    {    
		if($this->input->is_ajax_request())
		{
			$post = $this->input->post();
			$from_date = ($post['from_date'] != "") ? date('Y-m-d', strtotime($post['from_date'])) : "";
			$to_date = ($post['to_date'] != "") ? date('Y-m-d', strtotime($post['to_date'])) : "";
			$customer_code = $post['customer_code'];
			$sales_code = $post['sales_code'];

			$total_sales = 0; $total_sales_return = 0;
			$sales_invoice = $this->finance_report->get_sales_invoice_customer_report($from_date, $to_date, $customer_code, $sales_code);
			$sales_return = $this->finance_report->get_sales_return_customer_report($from_date, $to_date, $customer_code, $sales_code); 

			$total_sales = $sales_invoice['grandtotal'];
			$total_sales_return = $sales_return['grandtotal'];
			$total_hpp = $sales_invoice['total_hpp'] - $sales_return['total_hpp'];

			$profit = $total_sales-$total_sales_return-$total_hpp;
			$pembagi = $total_sales-$total_sales_return;
			$prosentase =  ($pembagi != 0) ? ($profit/$pembagi)*100 : 0;
							
			$output = array(
				'total_sales'        => number_format($total_sales, 2, ".", ","),
				'total_sales_return' => number_format($total_sales_return, 2, ".", ","),
				'total_hpp'          => number_format($total_hpp, 2, ".", ","),
				'profit'             => number_format($profit, 2, ".", ",").' / '.number_format($prosentase, 2, ".", ",").'%'
			);
			header('Content-Type: application/json');        
			echo json_encode($output);
		}
		else
		{
			$this->load->view('auth/show_404');
		}		   
    }
    		
    public function sales_profit()
    {
        if($this->system->check_access('report/sales_profit', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post       = $this->input->post();
				if($post['datatable_type'] == 'datatable_sales_invoice')
				{
					header('Content-Type: application/json');
					$this->datatables->select('sales_invoice.id AS id, sales_invoice.date, sales_invoice.invoice AS search_invoice, sales_invoice.invoice AS invoice, sales_invoice.grandtotal AS grandtotal, sales_invoice.total_hpp AS total_hpp, customer.name AS name_c, employee.name AS name_s');
					$this->datatables->from('sales_invoice');
					$this->datatables->join('employee', 'employee.code = sales_invoice.sales_code');
					$this->datatables->join('customer', 'customer.code = sales_invoice.customer_code');
					$this->datatables->where('sales_invoice.do_status', 1);
					$this->datatables->where('sales_invoice.deleted', 0);					
					if($post['from_date'] != "")
					{
						$this->datatables->where('sales_invoice.date >=',date('Y-m-d', strtotime($post['from_date'])));
					}
					if($post['to_date'] != "")
					{
						$this->datatables->where('sales_invoice.date <=',date('Y-m-d', strtotime($post['to_date'])));
					}
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
					$this->datatables->add_column('action', 
					'
						<a class="text-warning kt-link text-center view_detail" href="javascript:void(0);" data-url="'.site_url('report/finance/sales/profit/view_detail/$1').'"><i class="fa fa-eye"></i></a>
					', 'encrypt_custom(id)');
					echo $this->datatables->generate();
				}
				else if($post['datatable_type'] == 'datatable_sales_return')
				{
					header('Content-Type: application/json');
					$this->datatables->select('sales_return.id AS id_sr, sales_return.code AS code_sr, sales_return.date, sales_invoice.invoice, count(sales_return_detail.id) AS total_product, total_return, customer.name AS name_c,');
					$this->datatables->from('sales_return');
					$this->datatables->join('sales_invoice', 'sales_invoice.id = sales_return.sales_invoice_id', 'left');
					$this->datatables->join('customer', 'customer.code = sales_return.customer_code');		
					$this->datatables->join('sales_return_detail', 'sales_return_detail.sales_return_id = sales_return.id');
					$this->datatables->where('sales_return.deleted', 0);
					$this->datatables->where('sales_return_detail.deleted', 0);
					$this->datatables->where('sales_return.do_status', 1);
					if($post['from_date'] != "")
					{
						$this->datatables->where('sales_return.date >=', date('Y-m-d', strtotime($post['from_date'])));
					}
					if($post['to_date'] != "")
					{
						$this->datatables->where('sales_return.date <=', date('Y-m-d', strtotime($post['to_date'])));
					}                
					if($post['customer_code'] != "")
					{
						$this->datatables->where('sales_return.customer_code', $post['customer_code']);
					}
					if($post['sales_code'] != "")
					{
						$this->datatables->where('sales_return.employee_code', $post['sales_code']);
					}					
					$this->datatables->group_by('sales_return.id');
					$this->datatables->add_column('code_sr', 
					'
						<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/return/detail/$1').'"><b>$2</b></a>
					', 'encrypt_custom(id_sr) ,code_sr');
					echo $this->datatables->generate(); 
				}
				     				
			}
			else
			{
				$data_activity = [
					'information' => 'MELIHAT LAPORAN PROFITABILITAS PER PENJUALAN',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Profitabilitas Per Penjualan");        
				$footer = array("script" => ['report/finance/sales_profit_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/sales_profit_report');
				$this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}
	
	public function view_sales_profit_detail_report($sales_invoice_id)
    {
		if($this->system->check_access('report/sales_profit/view_detail', 'A'))
        {
			$data_activity = [
				'information' => 'MELIHAT DETAIL PROFITABILITAS PENJUALAN',
				'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$sales_invoice_id = $this->global->decrypt($sales_invoice_id);			
			$sales_invoice    = $this->sales->get_detail_sales_invoice($sales_invoice_id);			
			$data = array(
				'perusahaan'	=> $this->global->company(),
				'sales_invoice' => $sales_invoice,
				'sales_invoice_detail' => $this->sales->get_detail_sales_invoice_detail($sales_invoice['id'])
			);
			$this->load->view('finance/view_sales_profit_detail_report', $data);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }		
	}
	
	public function print_sales_profit_detail_report()
    {
		if($this->system->check_access('menu', 'A'))
        {
			if($this->input->method() === 'post')
			{			                                                
				$data_activity = [
					'information' => 'MENCETAK LAPORAN DETAIL PROFITABILITAS PENJUALAN',
					'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$post = $this->input->post();
				$filter = [
					'from_date' => format_date($post['from_date']),
					'to_date'   => format_date($post['to_date']),
					'customer_code'  => $post['customer_code'],
					'sales_code' => $post['sales_code']
				];				
				$data = [
					'title'      => 'Laporan Detail Profitabilitas Penjualan',
					'perusahaan' => $this->global->company(),
					'data'	     => $this->finance_report->print_sales_profit_detail_report($filter)
				];
				$mpdf = new \Mpdf\Mpdf([
					'format' => [210, 297],
					'orientation' => 'P',
					'margin_left' => 3,
					'margin_right' => 3,
					'margin_top' => 25,
					'margin_bottom' => 10,
					'margin_header' => 5,
					'margin_footer' => 5
				]);
				$mpdf->SetHTMLHeader('
					<div style="font-weight: bold; font-size:16px;">
						<u>LAPORAN DETAIL PROFITABILITAS PENJUALAN</u>
					</div>
					<table style="width:100%; font-size:14px;">
						<tbody>
							<tr>
								<td colspan="2">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
							</tr>
							<tr>
								<td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
								<td style="border-bottom: 1px solid black;">Sales: '.$filter['sales_code'].'</td>
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
				$data = $this->load->view('finance/print_sales_profit_detail_report', $data, true);
				ini_set("pcre.backtrack_limit", "999999999999");
				$mpdf->WriteHTML($data);
				$mpdf->Output();
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

	// PROFIT AND LOSS
    public function profit_and_loss()
    {   
        // if($this->system->check_access('report/profit_and_loss', 'A'))
		$access_user_id = [1, 3, 14, 17];
		if(in_array($this->session->userdata('id_u'), $access_user_id))
        {
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				$from_date      = (!isset($post['from_date'])) ? null : date('Y-m-d', strtotime($post['from_date']));
				$to_date        = (!isset($post['to_date']))   ? null : date('Y-m-d', strtotime($post['to_date']));
				
				// IN
				$total_sales = $this->finance_report->total_sales_invoice_profit_and_loss($from_date, $to_date);
				$total_sales_return = $this->finance_report->total_sales_return_profit_and_loss($from_date, $to_date);
				$total_other_income = $this->finance_report->total_other_income_profit_and_loss($from_date, $to_date);
				$total_hpp = $this->finance_report->total_hpp_profit_and_loss($from_date, $to_date);				

				// OUT
				$list_of_expense = $this->finance_report->list_of_expense_profit_and_loss($from_date, $to_date);
				$total_expense = 0;
				$html_list_of_expense="";
				foreach($list_of_expense AS $info_list_of_expense)
				{		
					$html_list_of_expense .= '<tr class="list_of_expense"><td width="5%"></td><td width="5%"></td><td>'.ucwords(strtolower($info_list_of_expense['name_c'])).'</td><td class="text-right">'.number_format($info_list_of_expense['total_expense'], '0' , '.' ,',').'</td></tr>';
					$total_expense = $total_expense+$info_list_of_expense['total_expense'];
				}
				$total_stock_opname = $this->finance_report->total_stock_opname_profit_and_loss($from_date, $to_date);

				header('Content-Type: application/json');
				$result = array(
					'total_sales'        => number_format($total_sales, 2,".",","),
					'total_sales_return' => number_format($total_sales_return, 2,".",","),
					'total_other_income' => number_format($total_other_income, 2,".",","),
					'net_sales'          => number_format($total_sales-$total_sales_return+$total_other_income, 2,".",","),
					'total_hpp'          => number_format($total_hpp, 2,".",","),
					'gross_profit'       => number_format($total_sales-$total_sales_return+$total_other_income-$total_hpp, 2,".",","),
					'list_of_expense'    => $html_list_of_expense,
					'total_expense'      => number_format($total_expense, 2,".",","),
					'total_stock_opname' => number_format($total_stock_opname, 2,".",","),
					'net_profit'         => number_format($total_sales-$total_sales_return+$total_other_income-$total_hpp-$total_expense-$total_stock_opname, 2,".",",")
				);
				echo json_encode($result);
			} 
			else
			{
				$data_activity = [
					'information' => 'MELIHAT LAPORAN LABA RUGI',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);
				$header = array("title" => "Laba Rugi");        
				$footer = array("script" => ['report/finance/profit_and_loss_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('finance/profit_and_loss_report');
				$this->load->view('include/footer', $footer);
			}            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}

	// BALANCE SHEET
	public function check_unbalance_general_ledger()
	{
		$general_ledgers = $this->db->select('date, invoice, sum(debit) AS total_debit, sum(credit) AS total_credit')->from('general_ledger')->where('deleted', 0)->group_by('invoice')->get()->result_array();
		$found =[];
		foreach($general_ledgers AS $general_ledger)
		{
			if($general_ledger['total_debit'] != $general_ledger['total_credit'])			
			{
				$found[] = $general_ledger['date'].' | '.$general_ledger['invoice'];
			}
		}
		echo json_encode($found); die;
	}

	public function recalculate_general_ledger()
	{				
		$from_date = date('Y-m-d',strtotime(date('Y-m-d') . "-30 days"));
		$to_date  = date('Y-m-d');
		$where_coa_account = [
			'deleted' => 0
		];
		$coa_accounts = $this->crud->get_where('coa_account', $where_coa_account)->result_array();
		foreach($coa_accounts AS $coa_account)
		{
			$where_last_balance = [
				'coa_account_code' => $coa_account['code'],
				'date <='          => $from_date,
				'deleted'          => 0
			];
			$last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
			$balance = ($last_balance != null) ?  $last_balance['balance'] : 0;
			$where_general_ledgers = [
				'coa_account_code' => $coa_account['code'],
				'date >'           => $from_date,
				'date <='          => $to_date
			];
			$general_ledgers = $this->db->select('*')->from('general_ledger')->where($where_general_ledgers)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
			foreach($general_ledgers AS $general_ledger)
			{
				if(in_array($coa_account['coa_category_code'], [1, 5, 7]))
				{
					if($general_ledger['debit'] != 0 && $general_ledger['credit'] == 0)
					{
						$balance = $balance+$general_ledger['debit'];
					}
					elseif($general_ledger['debit'] == 0 && $general_ledger['credit'] != 0)
					{
						$balance = $balance-$general_ledger['credit'];
					}
				}
				elseif(in_array($coa_account['coa_category_code'], [2, 3, 4, 6]))
				{
					if($general_ledger['debit'] != 0 && $general_ledger['credit'] == 0)
					{
						$balance = $balance-$general_ledger['debit'];
					}
					elseif($general_ledger['debit'] == 0 && $general_ledger['credit'] != 0)
					{
						$balance = $balance+$general_ledger['credit'];
					}
				}
				$this->crud->update('general_ledger', ['balance' => $balance], ['id' => $general_ledger['id']]);
			}
		}
		$this->session->set_flashdata('success', 'Laporan Neraca Berhasil Diperbarui');
        $response = [
			'status'	=> [
				'code'  	=> 200				
			],
			'response'  => ''
		];
		echo json_encode($response);
	}
	
	public function balance_sheet()
    {   
        if($this->system->check_access('report/balance_sheet', 'A'))
        {
			if($this->input->is_ajax_request())
			{
				$post = $this->input->post();
				$date = $post['date'];
				// ASSETS
				$html_aset=""; $total_aset = 0;
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 1])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{
					$html_aset.='<tr class="aset"><td colspan="3" style="border-bottom: 1px solid black;" class="font-weight-bold">'.$subcategory['name'].'</td></tr>';
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 1, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						if($last_balance_coa != 0)
						{
							$html_aset.='<tr class="aset"><td width="5%"></td><td>'.$coa['code'].' | '.$coa['name'].'</td><td class="text-right">'.number_format($last_balance_coa, 2, '.', ',').'</td></tr>';
						}						
						$total_aset = $total_aset+$last_balance_coa;
					}
				}
				// LIABILITIES
				$html_kewajiban=""; $total_kewajiban = 0;
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 2])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{
					$html_kewajiban.='<tr class="kewajiban"><td colspan="3" style="border-bottom: 1px solid black;" class="font-weight-bold">'.$subcategory['name'].'</td></tr>';
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 2, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						if($last_balance_coa != 0)
						{
							$html_kewajiban.='<tr class="kewajiban"><td width="5%"></td><td>'.$coa['code'].' | '.$coa['name'].'</td><td class="text-right">'.number_format($last_balance_coa, 2, '.', ',').'</td></tr>';
						}						
						$total_kewajiban = $total_kewajiban+$last_balance_coa;
					}
				}
				// EQUITY
				$html_ekuitas=""; $total_ekuitas = 0;
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 3])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{
					$html_ekuitas.='<tr class="ekuitas"><td colspan="3" style="border-bottom: 1px solid black;" class="font-weight-bold">'.$subcategory['name'].'</td></tr>';
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 3, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						if($last_balance_coa != 0)
						{
							$html_ekuitas.='<tr class="ekuitas"><td width="5%"></td><td>'.$coa['code'].' | '.$coa['name'].'</td><td class="text-right">'.number_format($last_balance_coa, 2, '.', ',').'</td></tr>';
						}						
						$total_ekuitas = $total_ekuitas+$last_balance_coa;
					}
				}
				// PROFIT&LOSS
				// PENDAPATAN
				$total_profit_and_loss = 0;
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 4])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{					
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 4, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						$total_profit_and_loss = $total_profit_and_loss + $last_balance_coa;
						$total_ekuitas = $total_ekuitas+$last_balance_coa;						
					}
				}
				// HPP, BIAYA/BEBAN
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 5])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{					
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 5, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						$total_profit_and_loss = $total_profit_and_loss-$last_balance_coa;
						$total_ekuitas = $total_ekuitas-$last_balance_coa;
					}
				}
				// PENDAPATAN LAIN-LAIN
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 6])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{					
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 6, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						$total_profit_and_loss = $total_profit_and_loss+$last_balance_coa;
						$total_ekuitas = $total_ekuitas+$last_balance_coa;
					}
				}
				// BEBAN LAIN-LAIN
				$list_of_subcategory = $this->crud->get_where('coa_subcategory', ['coa_category_code' => 7])->result_array();
				foreach($list_of_subcategory AS $subcategory)
				{					
					$list_of_coa = $this->crud->get_where('coa_account', ['coa_category_code' => 7, 'coa_subcategory_code' => $subcategory['code']])->result_array();
					foreach($list_of_coa AS $coa)
					{
						$last_balance_coa = $this->finance_report->get_last_balance_coa($coa['code'], $date);
						$total_profit_and_loss = $total_profit_and_loss-$last_balance_coa;
						$total_ekuitas = $total_ekuitas-$last_balance_coa;
					}
				}

				// ------------------------------------------------
				// IN
				$today = format_date($post['date']);
				$from_date = date('Y-m-01', strtotime($today));
				$to_date = date('Y-m-t', strtotime($today));
				$total_sales = $this->finance_report->total_sales_invoice_profit_and_loss($from_date, $to_date);
				$total_sales_return = $this->finance_report->total_sales_return_profit_and_loss($from_date, $to_date);
				$total_other_income = $this->finance_report->total_other_income_profit_and_loss($from_date, $to_date);
				$total_hpp = $this->finance_report->total_hpp_profit_and_loss($from_date, $to_date);				

				// OUT
				$list_of_expense = $this->finance_report->list_of_expense_profit_and_loss($from_date, $to_date);
				$total_expense = 0;
				$html_list_of_expense="";
				foreach($list_of_expense AS $info_list_of_expense)
				{		
					$html_list_of_expense .= '<tr class="list_of_expense"><td width="5%"></td><td width="5%"></td><td>'.ucwords(strtolower($info_list_of_expense['name_c'])).'</td><td class="text-right">'.number_format($info_list_of_expense['total_expense'], '0' , '.' ,',').'</td></tr>';
					$total_expense = $total_expense+$info_list_of_expense['total_expense'];
				}
				$total_stock_opname = $this->finance_report->total_stock_opname_profit_and_loss($from_date, $to_date);
				$total_profit_and_loss_month = $total_sales-$total_sales_return+$total_other_income-$total_hpp-$total_expense-$total_stock_opname;
				$html_ekuitas.='<tr class="ekuitas"><td width="5%"></td><td>30104 | LABA DITAHAN</td><td class="text-right">'.number_format($total_profit_and_loss-$total_profit_and_loss_month, 2, '.', ',').'</td></tr>';
				$html_ekuitas.='<tr class="ekuitas"><td width="5%"></td><td>30105 | LABA BULAN BERJALAN</td><td class="text-right">'.number_format($total_profit_and_loss_month, 2, '.', ',').'</td></tr>';
				$data = [
					'aset'       => $html_aset,
					'total_aset' => number_format($total_aset, 2, '.', ','),
					'kewajiban'       => $html_kewajiban,
					'ekuitas'       => $html_ekuitas,
					'total_kewajiban_ekuitas' => number_format($total_kewajiban+$total_ekuitas, 2, '.', ',')
				];
				header('Content-Type: application/json');
				echo json_encode($data);
			} 
			else
			{	
				$data_activity = [
					'information' => 'MELIHAT LAPORAN NERACA',
					'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
					'code_e'      => $this->session->userdata('code_e'),
					'name_e'      => $this->session->userdata('name_e'),
					'user_id'     => $this->session->userdata('id_u')
				];						
				$this->crud->insert('activity', $data_activity);			
				$header = array("title" => "Neraca");
				$footer = array("script" => ['report/finance/balance_sheet_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');
				$this->load->view('finance/balance_sheet_report');
				$this->load->view('include/footer', $footer);
			}            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}
}