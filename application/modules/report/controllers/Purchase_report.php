<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_report extends System_Controller 
{	
    public function __construct()
  	{
        parent::__construct();
      	$this->load->model('Purchase_report_model', 'purchase_report');
    }

    // PURCHASE INVOICE
    public function get_supplier_purchase_invoice_report()
    {
		if($this->input->is_ajax_request())
		{
            $data = $this->db->select('supplier.id, supplier.code, supplier.name')
                             ->from('supplier')
                             ->join('purchase_invoice', 'purchase_invoice.supplier_code = supplier.code')
                             ->where('purchase_invoice.deleted', 0)->group_by('supplier.id')
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

    public function total_purchase_invoice_report()
    {  
        if($this->system->check_access('report/purchase/invoice/total', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post           = $this->input->post();                
                $filter = [
                    'from_date' => format_date($post['from_date']),
                    'to_date'   => format_date($post['to_date']),
                    'payment'   => $post['payment'],
                    'supplier_code' => $post['supplier_code'],
                    'ppn'       => $post['ppn'],
                    'payment_status' => $post['payment_status'],
                    'search_product' => $post['search_product']                    
                ];
                $total_purchase_invoice = $this->purchase_report->total_purchase_invoice($filter);
                header('Content-Type: application/json');
                $result = array(
                    'grandtotal'        => number_format($total_purchase_invoice['grandtotal'], 2,".",","),
                    'account_payable'   => number_format($total_purchase_invoice['account_payable'], 2,".",",")
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

    public function chart_purchase_invoice()
    {
        if($this->system->check_access('report/purchase/invoice/chart', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();            
                header('Content-Type: application/json');
                $filter = [
                    'view_type' => $post['view_type'],
                    'from_date' => format_date($post['from_date']),
                    'to_date'   => format_date($post['to_date']),
                    'payment'   => $post['payment'],
                    'supplier_code' => $post['supplier_code'],
                    'ppn' => $post['ppn'],
                    'payment_status' => $post['payment_status'],
                    'search_product' => $post['search_product']
                ];
                $result = $this->purchase_report->chart_purchase_invoice($filter);
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

    public function purchase_invoice()
    {   
        if($this->system->check_access('report/purchase/invoice', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                header('Content-Type: application/json');
                $post           = $this->input->post();
                $from_date      = (!isset($post['from_date']))     ? null : $post['from_date'];
                $to_date        = (!isset($post['to_date']))       ? null : $post['to_date'];
                $payment        = (!isset($post['payment']))       ? null : $post['payment'];
                $supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
                $ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
                $payment_status = (!isset($post['payment_status'])) ? null : $post['payment_status'];
                $search_product = (!isset($post['search_product'])) ? null : $post['search_product'];
                $this->datatables->select('purchase_invoice.id, purchase_invoice.date, purchase_invoice.code, purchase_invoice.invoice, purchase_invoice.payment, purchase_invoice.due_date, purchase_invoice.grandtotal, (purchase_invoice.account_payable+purchase_invoice.cheque_payable) AS account_payable, supplier.name AS supplier, purchase_invoice.ppn, purchase_invoice.payment_status,
                                purchase_invoice.invoice AS search_invoice');
                $this->datatables->from('purchase_invoice');
                $this->datatables->join('purchase_invoice_detail AS pid', 'pid.purchase_invoice_id = purchase_invoice.id');
                $this->datatables->join('product', 'product.id = pid.product_id');
                $this->datatables->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
                $this->datatables->where('purchase_invoice.deleted', 0);
                if($from_date != "")
                {
                    $this->datatables->where('purchase_invoice.date >=', date('Y-m-d', strtotime($post['from_date'])));
                }
                if($to_date != "")
                {
                    $this->datatables->where('purchase_invoice.date <=', date('Y-m-d', strtotime($post['to_date'])));
                }
                if($payment != "")
                {                    
                    $this->datatables->where('purchase_invoice.payment', $payment);
                }                
                if($supplier_code != "")
                {
                    $this->datatables->where('purchase_invoice.supplier_code', $supplier_code);
                }
                if($ppn != "")
                {
                    $this->datatables->where('purchase_invoice.ppn', $ppn);
                }
                if($payment_status != "")
                {
                    if($payment_status != 3)
                    {
                        $this->datatables->where('purchase_invoice.payment_status', $payment_status);
                    }                    
                    else
                    {
                        $this->datatables->where('purchase_invoice.payment_status !=', 1);
                        $this->datatables->where('purchase_invoice.due_date <', date('Y-m-d'));
                    }
                }
                if($search_product != "")
                {
                    $this->datatables->like('product.name', $search_product);
                }
                $this->datatables->group_by('purchase_invoice.id');
                $this->datatables->add_column('invoice', 
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(id),invoice');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Daftar Pembelian");
                $footer = array("script" => ['report/purchase/invoice/purchase_invoice_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('purchase/invoice/purchase_invoice_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

	public function print_purchase_invoice_report()
    {
		if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DAFTAR PEMBELIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
			$payment        = (!isset($post['payment']))       ? null : $post['payment'];
			$supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
			$ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
            $payment_status = (!isset($post['payment_status'])) ? null : $post['payment_status'];

            switch ($payment){
                case "1":
                    $payment_filter = 'TUNAI';
                  break;
                case "2":
                    $payment_filter = 'KREDIT';
                  break;                
                default:
                    $payment_filter = 'TUNAI & KREDIT';
            }

            switch ($payment_status){
                case "1":
                    $payment_status_filter = 'LUNAS';
                  break;
                case "2":
                    $payment_status_filter = 'BELUM LUNAS';
                  break;                
                  case "3":
                    $payment_status_filter = 'JATUH TEMPO';
                  break;                
                default:
                    $payment_status_filter = 'SEMUA';
            }

            switch ($ppn){
                case "0":
                    $ppn_filter = 'NON';
                  break;
                case "1":
                    $ppn_filter = 'PPN';
                  break;                
                  case "2":
                    $ppn_filter = 'FINAL';
                  break;                
                default:
                    $ppn_filter = 'SEMUA';
            }

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'payment_filter' => $payment_filter,
                'supplier_code' => ($supplier_code == '') ? 'SEMUA SUPPLIER' : $supplier_code,                
                'ppn_filter' => $ppn_filter,
                'payment_status_filter' => $payment_status_filter,
            ];
			$data = [
                'title'      => 'Laporan Daftar Pembelian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->purchase_report->print_purchase_invoice_report($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 297],
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
					<u>LAPORAN DAFTAR PEMBELIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td colspan="4">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Supplier: '.$filter['supplier_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Pembayaran: '.$filter['payment_filter'].' </td>
                            <td style="border-bottom: 1px solid black;" class="text-right">PPN: '.$filter['ppn_filter'].'</td>
                            <td style="border-bottom: 1px solid black;" class="text-right">Status: '.$filter['payment_status_filter'].'</td>
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
			$data = $this->load->view('purchase/invoice/print_purchase_invoice_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }
    
    public function print_purchase_invoice_detail_report()
    {
		if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DETAIL PEMBELIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
			$payment        = (!isset($post['payment']))       ? null : $post['payment'];
			$supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
			$ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
            $payment_status = (!isset($post['payment_status'])) ? null : $post['payment_status'];

            switch ($payment){
                case "1":
                    $payment_filter = 'TUNAI';
                  break;
                case "2":
                    $payment_filter = 'KREDIT';
                  break;                
                default:
                    $payment_filter = 'TUNAI & KREDIT';
            }

            switch ($payment_status){
                case "1":
                    $payment_status_filter = 'LUNAS';
                  break;
                case "2":
                    $payment_status_filter = 'BELUM LUNAS';
                  break;                
                  case "3":
                    $payment_status_filter = 'JATUH TEMPO';
                  break;                
                default:
                    $payment_status_filter = 'SEMUA';
            }

            switch ($ppn){
                case "0":
                    $ppn_filter = 'NON';
                  break;
                case "1":
                    $ppn_filter = 'PPN';
                  break;                
                  case "2":
                    $ppn_filter = 'FINAL';
                  break;                
                default:
                    $ppn_filter = 'SEMUA';
            }

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'payment_filter' => $payment_filter,
                'supplier_code' => ($supplier_code == '') ? 'SEMUA SUPPLIER' : $supplier_code,                
                'ppn_filter' => $ppn_filter,
                'payment_status_filter' => $payment_status_filter,
            ];
			$data = [
                'title'      => 'Laporan Detail Pembelian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->purchase_report->print_purchase_invoice_detail_report($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
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
					<u>LAPORAN DETAIL PEMBELIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td colspan="4">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td  style="border-bottom: 1px solid black;">Supplier: '.$filter['supplier_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Pembayaran: '.$filter['payment_filter'].' </td>
                            <td  style="border-bottom: 1px solid black;" class="text-right">PPN: '.$filter['ppn_filter'].'</td>
                            <td  style="border-bottom: 1px solid black;" class="text-right">Status: '.$filter['payment_status_filter'].'</td>
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
			$data = $this->load->view('purchase/invoice/print_purchase_invoice_detail_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    public function print_purchase_invoice_daily_report()
    {
		if($this->input->method() === 'post')
		{
			$data_activity = [
				'information' => 'MENCETAK LAPORAN PEMBELIAN HARIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
			$payment        = (!isset($post['payment']))       ? null : $post['payment'];
			$supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
			$ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
            $payment_status = (!isset($post['payment_status'])) ? null : $post['payment_status'];

            switch ($payment){
                case "1":
                    $payment_filter = 'TUNAI';
                  break;
                case "2":
                    $payment_filter = 'KREDIT';
                  break;                
                default:
                    $payment_filter = 'TUNAI & KREDIT';
            }

            switch ($payment_status){
                case "1":
                    $payment_status_filter = 'LUNAS';
                  break;
                case "2":
                    $payment_status_filter = 'BELUM LUNAS';
                  break;                
                  case "3":
                    $payment_status_filter = 'JATUH TEMPO';
                  break;                
                default:
                    $payment_status_filter = 'SEMUA';
            }

            switch ($ppn){
                case "0":
                    $ppn_filter = 'NON';
                  break;
                case "1":
                    $ppn_filter = 'PPN';
                  break;                
                  case "2":
                    $ppn_filter = 'FINAL';
                  break;                
                default:
                    $ppn_filter = 'SEMUA';
            }

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'payment_filter' => $payment_filter,
                'supplier_code' => ($supplier_code == '') ? 'SEMUA SUPPLIER' : $supplier_code,                
                'ppn_filter' => $ppn_filter,
                'payment_status_filter' => $payment_status_filter,
            ];
			$data = [
                'title'      => 'Laporan Pembelian Harian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->purchase_report->print_purchase_invoice_daily_report($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 297],
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
					<u>LAPORAN PEMBELIAN HARIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td colspan="4">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td  style="border-bottom: 1px solid black;">Supplier: '.$filter['supplier_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Pembayaran: '.$filter['payment_filter'].' </td>
                            <td  style="border-bottom: 1px solid black;" class="text-right">PPN: '.$filter['ppn_filter'].'</td>
                            <td  style="border-bottom: 1px solid black;" class="text-right">Status: '.$filter['payment_status_filter'].'</td>
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
			$data = $this->load->view('purchase/invoice/print_purchase_invoice_daily_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    // PURCHASE TAX INVOICE
    public function get_supplier_purchase_tax_invoice_report()
    {
		if($this->input->is_ajax_request())
		{
            $data = $this->db->select('supplier.id, supplier.code, supplier.name')
                             ->from('supplier')
                             ->join('purchase_invoice', 'purchase_invoice.supplier_code = supplier.code')
                             ->where('purchase_invoice.ppn', 1)->where('purchase_invoice.deleted', 0)->group_by('supplier.id')
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

    public function get_total_purchase_tax_invoice_report()
    {  
        if($this->system->check_access('report/purchase/purchase_tax_invoice', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $post           = $this->input->post();
                $from_date      = (!isset($post['from_date']))     ? null : $post['from_date'];
                $to_date        = (!isset($post['to_date']))       ? null : $post['to_date'];
                $supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
                $tax_invoice_status = (!isset($post['tax_invoice_status'])) ? null : $post['tax_invoice_status'];
    
                $this->db->select('grandtotal, tax_invoice_dpp');
                $this->db->from('purchase_invoice');
                $this->db->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
                $this->db->join('purchase_invoice_detail', 'purchase_invoice_detail.purchase_invoice_id = purchase_invoice.id');            
                $this->db->where('purchase_invoice.ppn', 1);
                $this->db->where('purchase_invoice.deleted', 0); $this->db->where('purchase_invoice_detail.deleted', 0);
                if($from_date != "")
                {
                    $this->db->where('purchase_invoice.date >=', date('Y-m-d', strtotime($post['from_date'])));
                }
                if($to_date != "")
                {
                    $this->db->where('purchase_invoice.date <=', date('Y-m-d', strtotime($post['to_date'])));
                }                
                if($supplier_code != "")
                {
                    $this->db->where('purchase_invoice.supplier_code', $supplier_code);
                }
                if($tax_invoice_status != "")
                {
                    $this->db->where('purchase_invoice.tax_invoice_number'. $tax_invoice_status, null);
                }
                $this->db->group_by('purchase_invoice.id');;
                $data = $this->db->get()->result_array();   
                $grandtotal =0; $tax_invoice_dpp =0;
                foreach($data AS $info)
                {
                    $grandtotal      = $grandtotal + $info['grandtotal'];
                    $tax_invoice_dpp = $tax_invoice_dpp + $info['tax_invoice_dpp'];
                }
                header('Content-Type: application/json');
                $result = array(
                    'grandtotal'        => number_format($grandtotal,0,".",","),
                    'tax_invoice_dpp'   => number_format($tax_invoice_dpp,0,".",",")
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

    public function purchase_tax_invoice()
    {   
        if($this->system->check_access('report/purchase/invoice/tax', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post           = $this->input->post();
                header('Content-Type: application/json');
                $this->datatables->select('pi.id, pi.date, pi.code, pi.invoice, 
                                 tax_invoice.id, tax_invoice.date AS tax_invoice_date, tax_invoice.number AS tax_invoice_number, tax_invoice.dpp AS tax_invoice_dpp, tax_invoice.ppn AS tax_invoice_ppn, (tax_invoice.dpp+tax_invoice.ppn) AS total_tax_invoice, tax_invoice.is_used,
                                 supplier.name AS supplier,
                                 pi.code AS search_code, pi.invoice AS search_invoice');
                $this->datatables->from('purchase_invoice AS pi');
                $this->datatables->join('supplier', 'supplier.code = pi.supplier_code');
                $this->datatables->join('tax_invoice', 'tax_invoice.transaction_id = pi.id AND tax_invoice.transaction_type = 1', 'left');
                $this->datatables->where('pi.ppn', 1);
                $this->datatables->where('pi.deleted', 0);
                if($post['from_date'] != "")
                {
                    $this->datatables->where('pi.date >=', format_date($post['from_date']));
                }
                if($post['to_date'] != "")
                {
                    $this->datatables->where('pi.date <=', format_date($post['to_date']));
                }
                if($post['supplier_code'] != "")
                {
                    $this->datatables->where('pi.supplier_code', $post['supplier_code']);
                }
                if($post['tax_invoice_status'] != "")
                {
                    $this->datatables->where('tax_invoice.number'.$post['tax_invoice_status'], NULL);
                }
                if($post['is_used'] != "")
                {
                    $this->datatables->where('tax_invoice.is_used', $post['is_used']);
                }
                $this->datatables->group_by('pi.id');
                $this->datatables->group_by('tax_invoice.id');
                $this->datatables->add_column('invoice', 
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(id),invoice');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Faktur Pajak Pembelian");        
                $footer = array("script" => ['report/purchase/tax_invoice/purchase_tax_invoice_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('purchase/tax_invoice/purchase_tax_invoice_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

	// PRODUCT PURCHASE
	public function get_total_product_purchase_report()
    {   
        if($this->system->check_access('report/purchase/invoice/product/total', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       		= $this->input->post();        
                $search     		= $post['search'];
                $from_date  		= $post['from_date'] == "" ? null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    		= $post['to_date']   == "" ? null : date('Y-m-d', strtotime($post['to_date']));
                $department_code    = !isset($post['department_code']) 	  ? null : $post['department_code'];
                $subdepartment_code = !isset($post['subdepartment_code']) ? null : $post['subdepartment_code'];
                $supplier_code 		= !isset($post['supplier_code']) 	  ? null : $post['supplier_code'];
                $ppn 		        = !isset($post['ppn']) 	  ? null : $post['ppn'];
                $product    		= $this->purchase_report->get_product_purchase_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn)->result_array();
                $grandtotal = 0; $total_product = 0; $total_qty = 0;
                foreach($product AS $info)
                {
                    $purchase_detail = $this->purchase_report->get_product_purchase_detail_report($info['code'], $from_date, $to_date, $supplier_code);
                    if($purchase_detail->num_rows() > 0)
                    {
                        $total_purchase = 0;
                        foreach($purchase_detail->result_array() AS $info2)
                        {        
                            $where_convert = array(
                                'product_code' => $info2['product_code'],
                                'unit_id'      => $info2['unit_id']
                            );
                            $convert = $this->crud->get_where('product_unit', $where_convert);
                            if($convert->num_rows() == 1)
                            {
                                $convert = $convert->row_array();
                                $total_qty = $total_qty + ($info2['qty']*$convert['value']);
                            }
                            else
                            {
                                $total_qty = $total_qty + ($info2['qty']*1);
                            }                    
                            $total_purchase = $total_purchase + $info2['total'];					
                        }

                        $grandtotal = $grandtotal + $total_purchase;
                    }
                    else
                    {				
                        continue;
                    }
                    $total_product++;         
                }
                $output = array(
                    'grandtotal'        => number_format($grandtotal,0,".",","),
                    'total_product'     => number_format($total_product,0,".",","),
                    'total_qty'         => number_format($total_qty,2,".",","),
                );
                header('Content-Type: application/json');        
                echo json_encode($output);
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

    public function product_purchase()
    {     
        if($this->system->check_access('report/purchase/invoice/product', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       		= $this->input->post();        
                $search     		= $post['search'];
                $from_date  		= $post['from_date'] == "" 			  ? null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    		= $post['to_date'] == "" 			  ? null : date('Y-m-d', strtotime($post['to_date']));
                $department_code    = !isset($post['department_code']) 	  ? null : $post['department_code'];
                $subdepartment_code = !isset($post['subdepartment_code']) ? null : $post['subdepartment_code'];
                $supplier_code 		= !isset($post['supplier_code']) 	  ? null : $post['supplier_code'];
                $ppn 		        = !isset($post['ppn']) 	              ? null : $post['ppn'];
                $draw      			= !isset($post['draw']) 			  ? 0 : $post['draw'];
                $iLength    		= !isset($post['length']) 			  ? null : $post['length'];
                $iStart     		= !isset($post['start']) 			  ? null : $post['start'];
                $iOrder     		= !isset($post['order']) 			  ? null : $post['order'];

                $total      = $this->purchase_report->get_product_purchase_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn)->num_rows();
                $product    = $this->purchase_report->get_product_purchase_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn, $iLength, $iStart, $iOrder)->result_array();
                $data 		= array();
                foreach($product AS $info)
                {			
                    $purchase_detail = $this->purchase_report->get_product_purchase_detail_report($info['code'], $from_date, $to_date, $supplier_code);
                    if($purchase_detail->num_rows() > 0)
                    {
                        $total_purchase = 0; $total_qty = 0;
                        foreach($purchase_detail->result_array() AS $info2)
                        {
                            $where_convert = array(
                                'product_code' => $info2['product_code'],
                                'unit_id'      => $info2['unit_id']
                            );
                            $convert = $this->crud->get_where('product_unit', $where_convert);
                            if($convert->num_rows() == 1)
                            {
                                $convert = $convert->row_array();
                                $total_qty = $total_qty + ($info2['qty']*$convert['value']);
                            }
                            else
                            {
                                $total_qty = $total_qty + ($info2['qty']*1);
                            }

                            $total_purchase = $total_purchase + $info2['total'];
                        }

                        if($total_qty == 0)
                        {
                            $average_purchase = 0;
                        }
                        else
                        {
                            $average_purchase = $total_purchase / $total_qty;
                        }
                        
                        $where_stock = array(
                            'product_id'	=> $info['id'],
                            'product_code'	=> $info['code'],
                            'deleted'		=> 0
                        );
                        $data_stock = $this->crud->get_where('stock', $where_stock)->result_array();
                        $total_stock = 0;
                        foreach($data_stock AS $stock)
                        {
                            $total_stock = $total_stock + $stock['qty'];
                        }

                        $data[] = array(
                            'id'               => $info['id'],
                            'barcode'          => $info['barcode'],
                            'code' 		       => '<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/'.encrypt_custom($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
                            'name'             => $info['name'],
                            'total_qty'        => $total_qty,
                            'total_stock'	   => number_format($total_stock, 2, '.', ','),
                            'unit'             => $info['unit'],					
                            'total_purchase'   => $total_purchase, 
                            'average_purchase' => $average_purchase,
                            'search_code'      => $info['code']
                        );
                    }
                    else
                    {				
                        continue;
                    }            
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
                $header = array("title" => "Pembelian Per Produk");        
                $footer = array("script" => ['report/purchase/product_purchase_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('purchase/product_purchase_report');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }           
    }    

    // PURCHASE RETURN
    public function get_supplier_purchase_return_report()
    {
		if($this->input->is_ajax_request())
		{
            $data = $this->db->select('supplier.id, supplier.code, supplier.name')
                             ->from('supplier')
                             ->join('purchase_return', 'purchase_return.supplier_code = supplier.code')
                             ->where('purchase_return.deleted', 0)->group_by('supplier.id')
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

    public function get_total_purchase_return_report()
    {      
        if($this->system->check_access('report/purchase/return/total', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();        
                $this->db->select('purchase_return.total_return')
                         ->from('purchase_return')
                         ->join('purchase_invoice', 'purchase_invoice.id = purchase_return.purchase_invoice_id', 'left')
                         ->join('supplier', 'supplier.code = purchase_return.supplier_code')
                         ->where('purchase_return.do_status', 1)
                         ->where('purchase_return.deleted', 0);
                if($post['from_date'] != "")
                {
                    $this->db->where('purchase_return.date >=', date('Y-m-d', strtotime($post['from_date'])));
                }
                if($post['to_date'] != "")
                {
                    $this->db->where('purchase_return.date <=', date('Y-m-d', strtotime($post['to_date'])));
                }                
                if($post['supplier_code'] != "")
                {
                    $this->db->where('purchase_return.supplier_code', $post['supplier_code']);
                } 
                $this->db->group_by('purchase_return.id');
                $data = $this->db->get()->result_array();   
                $grandtotal =0;
                foreach($data AS $info)
                {
                    $grandtotal      = $grandtotal + $info['total_return'];
                }                
                $result = array(
                    'grandtotal'        => number_format($grandtotal,0,".",",")
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
        
    public function purchase_return()
    {
        if($this->system->check_access('report/purchase/return', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                header('Content-Type: application/json');
                $this->datatables->select('purchase_return.id AS id_pr, purchase_return.code AS code_pr, purchase_return.code AS search_code, purchase_return.date, purchase_invoice.invoice, purchase_return.method, total_return,supplier.name AS name_s,');
                $this->datatables->from('purchase_return');
                $this->datatables->join('purchase_invoice', 'purchase_invoice.id = purchase_return.purchase_invoice_id', 'left');
                $this->datatables->join('supplier', 'supplier.code = purchase_return.supplier_code');
                $this->datatables->where('purchase_return.do_status', 1);
                $this->datatables->where('purchase_return.deleted', 0);
                if($post['from_date'] != "")
                {
                    $this->datatables->where('purchase_return.date >=', date('Y-m-d', strtotime($post['from_date'])));
                }
                if($post['to_date'] != "")
                {
                    $this->datatables->where('purchase_return.date <=', date('Y-m-d', strtotime($post['to_date'])));
                }                
                if($post['supplier_code'] != "")
                {
                    $this->datatables->where('purchase_return.supplier_code', $post['supplier_code']);
                }
                $this->datatables->group_by('purchase_return.id');
                $this->datatables->add_column('code_pr', 
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/return/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(id_pr) ,code_pr');                
                echo $this->datatables->generate(); 
            }
            else
            {
                $header = array("title" => "Retur Pembelian");
                $footer = array("script" => ['report/purchase/return/purchase_return_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('purchase/return/purchase_return_report');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function print_purchase_return_report()
    {
		if($this->input->method() === 'post')
		{
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DAFTAR RETUR PEMBELIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
			$supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'supplier_code' => ($supplier_code == '') ? 'SEMUA SUPPLIER' : $supplier_code
            ];
			$data = [
                'title'      => 'Laporan Daftar Retur Pembelian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->purchase_report->print_purchase_return_report($from_date, $to_date, $supplier_code)
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 297],
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
					<u>LAPORAN DAFTAR RETUR PEMBELIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Supplier: '.$filter['supplier_code'].'</td>
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
			// $mpdf->DefHTMLFooterByName(
			// 	'LastPageFooter',
			// 	'
			// 		<table style="width:25%; text-align:center;" border="0">
			// 			<tr>
			// 				<td>HORMAT KAMI</td>
			// 				<td>PENERIMA</td>
			// 			</tr>
			// 			<tr>
			// 				<td style="height:50px;">&nbsp;</td>
			// 			</tr>
			// 			<tr>
			// 				<td><p>(___________________________)</p></td>
			// 				<td><p>(___________________________)</p></td>
			// 			</tr>
			// 			<tr>
			// 				<td style="height:5px;">&nbsp;</td>
			// 			</tr>
			// 		</table>
			// 		<table width="100%">
			// 			<tr>
			// 				<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
			// 				<td align="center">{PAGENO}/{nbpg}</td>
			// 			</tr>
			// 		</table>
			// 	'
			// );
			$data = $this->load->view('purchase/return/print_purchase_return_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }
    
    public function print_purchase_return_detail_report()
    {
		if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DETAIL RETUR PEMBELIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
            $supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
            
			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,                
                'supplier_code' => ($supplier_code == '') ? 'SEMUA SUPPLIER' : $supplier_code
            ];
			$data = [
                'title'      => 'Laporan Detail Retur Pembelian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->purchase_report->print_purchase_return_detail_report($from_date, $to_date, $supplier_code)
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 297],
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
					<u>LAPORAN DETAIL RETUR PEMBELIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td >Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Supplier: '.$filter['supplier_code'].'</td>
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
			// $mpdf->DefHTMLFooterByName(
			// 	'LastPageFooter',
			// 	'
			// 		<table style="width:25%; text-align:center;" border="0">
			// 			<tr>
			// 				<td>HORMAT KAMI</td>
			// 				<td>PENERIMA</td>
			// 			</tr>
			// 			<tr>
			// 				<td style="height:50px;">&nbsp;</td>
			// 			</tr>
			// 			<tr>
			// 				<td><p>(___________________________)</p></td>
			// 				<td><p>(___________________________)</p></td>
			// 			</tr>
			// 			<tr>
			// 				<td style="height:5px;">&nbsp;</td>
			// 			</tr>
			// 		</table>
			// 		<table width="100%">
			// 			<tr>
			// 				<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
			// 				<td align="center">{PAGENO}/{nbpg}</td>
			// 			</tr>
			// 		</table>
			// 	'
			// );
			$data = $this->load->view('purchase/return/print_purchase_return_detail_report', $data, true);
			$mpdf->WriteHTML($data);
            $mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    public function print_purchase_return_daily_report()
    {
		if($this->input->method() === 'post')
		{
			$data_activity = [
				'information' => 'MENCETAK LAPORAN RETUR PEMBELIAN HARIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));			
            $supplier_code  = (!isset($post['supplier_code'])) ? null : $post['supplier_code'];
            
			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,                
                'supplier_code' => ($supplier_code == '') ? 'SEMUA SUPPLIER' : $supplier_code
            ];
			$data = [
                'title'      => 'Laporan Retur Pembelian Harian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->purchase_report->print_purchase_return_daily_report($from_date, $to_date, $supplier_code)
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 297],
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
					<u>LAPORAN RETUR PEMBELIAN HARIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Supplier: '.$filter['supplier_code'].'</td>
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
			// $mpdf->DefHTMLFooterByName(
			// 	'LastPageFooter',
			// 	'
			// 		<table style="width:25%; text-align:center;" border="0">
			// 			<tr>
			// 				<td>HORMAT KAMI</td>
			// 				<td>PENERIMA</td>
			// 			</tr>
			// 			<tr>
			// 				<td style="height:50px;">&nbsp;</td>
			// 			</tr>
			// 			<tr>
			// 				<td><p>(___________________________)</p></td>
			// 				<td><p>(___________________________)</p></td>
			// 			</tr>
			// 			<tr>
			// 				<td style="height:5px;">&nbsp;</td>
			// 			</tr>
			// 		</table>
			// 		<table width="100%">
			// 			<tr>
			// 				<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | Opt. '.$this->session->userdata('name_e').'</small></td>
			// 				<td align="center">{PAGENO}/{nbpg}</td>
			// 			</tr>
			// 		</table>
			// 	'
			// );
			$data = $this->load->view('purchase/return/print_purchase_return_daily_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    // PRODUCT PURCHASE RETURN
	public function get_total_product_purchase_return_report()
    {    
        if($this->system->check_access('report/purchase/return/product/total', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       = $this->input->post();        		
                $from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    = ($post['to_date'] == "") ?    null : date('Y-m-d', strtotime($post['to_date']));        
                $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $supplier_code = (!isset($post['supplier_code'])) ?   null : $post['supplier_code'];
                $ppn        = (!isset($post['ppn'])) ?   null : $post['ppn'];
                $search     = $post['search'];
                $product    = $this->purchase_report->get_product_purchase_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn)->result_array();
                $grandtotal = 0; $total_product = 0; $total_qty = 0;
                foreach($product AS $info)
                {
                    $purchase_return_detail = $this->purchase_report->get_product_purchase_return_detail_report($info['code'], $from_date, $to_date, $supplier_code);
                    if($purchase_return_detail->num_rows() > 0)
                    {
                        $total_purchase_return = 0;
                        foreach($purchase_return_detail->result_array() AS $info2)
                        {        
                            $where_convert = array(
                                'product_code' => $info2['product_code'],
                                'unit_id'      => $info2['unit_id']
                            );
                            $convert = $this->crud->get_where('product_unit', $where_convert);
                            if($convert->num_rows() == 1)
                            {
                                $convert = $convert->row_array();
                                $total_qty = $total_qty + ($info2['qty']*$convert['value']);
                            }
                            else
                            {
                                $total_qty = $total_qty + ($info2['qty']*1);
                            }                    
                            $total_purchase_return = $total_purchase_return + $info2['total'];					
                        }

                        $grandtotal = $grandtotal + $total_purchase_return;
                                    
                    }
                    else
                    {				
                        continue;
                    }   
                    $total_product++;         
                }
                
                $output = array(
                    'grandtotal'        => number_format($grandtotal,0,".",","),
                    'total_product'     => number_format($total_product,0,".","."),
                    'total_qty'         => number_format($total_qty,2,".",","),
                );
                header('Content-Type: application/json');        
                echo json_encode($output);  
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

    public function product_purchase_return()
    {  
        if($this->system->check_access('report/purchase/return/product', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       = $this->input->post();                    
                $from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    = ($post['to_date'] == "") ?    null : date('Y-m-d', strtotime($post['to_date']));
                $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $supplier_code = (!isset($post['supplier_code'])) ?   null : $post['supplier_code'];
                $ppn        = (!isset($post['ppn'])) ?   null : $post['ppn'];
                $search     = $post['search'];
                $draw       = (!isset($post['draw'])) ?        0 : $post['draw'];
                $iLength    = (!isset($post['length'])) ?   null : $post['length'];
                $iStart     = (!isset($post['start'])) ?    null : $post['start'];
                $iOrder     = (!isset($post['order'])) ?    null : $post['order'];

                $total      = $this->purchase_report->get_product_purchase_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn)->num_rows();
                $product    = $this->purchase_report->get_product_purchase_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn, $iLength, $iStart, $iOrder)->result_array();
                $data 		= array();
                foreach($product AS $info)
                {			
                    $purchase_detail = $this->purchase_report->get_product_purchase_return_detail_report($info['code'], $from_date, $to_date, $supplier_code);
                    if($purchase_detail->num_rows() > 0)
                    {
                        $total_purchase_return = 0; $total_qty = 0;
                        foreach($purchase_detail->result_array() AS $info2)
                        {
                            $where_convert = array(
                                'product_code' => $info2['product_code'],
                                'unit_id'      => $info2['unit_id']
                            );
                            $convert = $this->crud->get_where('product_unit', $where_convert);
                            if($convert->num_rows() == 1)
                            {
                                $convert = $convert->row_array();
                                $total_qty = $total_qty + ($info2['qty']*$convert['value']);
                            }
                            else
                            {
                                $total_qty = $total_qty + ($info2['qty']*1);
                            }

                            $total_purchase_return = $total_purchase_return + $info2['total'];
                        }

                        if($total_qty == 0)
                        {
                            $average_purchase_return = 0;
                        }
                        else
                        {
                            $average_purchase_return = $total_purchase_return / $total_qty;
                        }

                        $data[] = array(
                            'id'                    => $info['id'],
                            'barcode'               => $info['barcode'],
                            'code' 		            => '<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/'.encrypt_custom($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
                            'name'                  => $info['name'],
                            'total_qty'             => $total_qty,
                            'unit'                  => $info['unit'],
                            'total_purchase_return' => $total_purchase_return, 
                            'average_purchase_return' => $average_purchase_return
                        );
                    }
                    else
                    {				
                        continue;
                    }            
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
                $header = array("title" => "Retur Pembelian Per Produk");        
                $footer = array("script" => ['report/purchase/return/product_purchase_return_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('purchase/return/product_purchase_return_report');
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