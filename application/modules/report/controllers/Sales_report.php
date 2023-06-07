<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_report extends System_Controller 
{	
    public function __construct()
  	{
        parent::__construct();
      	$this->load->model('Sales_report_model', 'sales_report');
    }        
    
    public function get_cashier()
    {
        $data       = $this->sales_report->get_cashier();
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

    // SALES INVOICE
    public function get_total_sales_invoice_report()
    {
        if($this->system->check_access('report/sales/invoice/total', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();            
                $this->db->select('sales_invoice.grandtotal AS grandtotal, sales_invoice.account_payable')
                        ->from('sales_invoice')
                        ->join('employee', 'employee.code = sales_invoice.sales_code')
                        ->join('customer', 'customer.code = sales_invoice.customer_code')
                        ->where('sales_invoice.do_status', 1)
                        ->where('sales_invoice.deleted', 0);
                if($post['from_date'] != "")
                {
                    $this->db->where('sales_invoice.date >=',date('Y-m-d', strtotime($post['from_date'])));
                }
                if($post['to_date'] != "")
                {
                    $this->db->where('sales_invoice.date <=',date('Y-m-d', strtotime($post['to_date'])));
                }
                if($post['payment'] != "")
                {
                    $this->db->where('sales_invoice.payment', $post['payment']);
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
                $account_payable = 0; $grandtotal =0; $total_transaction =0;
                foreach($data AS $info)
                {
                    $grandtotal      = $grandtotal + $info['grandtotal'];
                    $account_payable = $account_payable + $info['account_payable'];
                    $total_transaction++;
                }
                header('Content-Type: application/json');
                $result = array(
                    'grandtotal'         => number_format($grandtotal, 2, ".", ","),
                    'account_payable'    => number_format($account_payable, 2, ".", ","),
                    'total_transaction'  => $total_transaction
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

    public function chart_sales_invoice()
    {
        if($this->system->check_access('report/sales/invoice/chart', 'A'))
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
                    'customer_code' => $post['customer_code'],
                    'sales_code' => $post['sales_code'],
                    'payment_status' => $post['payment_status']
                ];
                $result = $this->sales_report->chart_sales_invoice($filter);
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

    public function sales_invoice()
    {
        if($this->system->check_access('report/sales/invoice', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                header('Content-Type: application/json');
                $this->datatables->select('sales_invoice.id AS id, sales_invoice.date, sales_invoice.invoice, sales_invoice.information, sales_invoice.payment, sales_invoice.due_date, sales_invoice.grandtotal, (sales_invoice.account_payable+sales_invoice.cheque_payable) AS account_payable, customer.name AS name_c, employee.name AS name_s, sales_invoice.payment_status,
                                sales_invoice.invoice AS search_invoice');
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
                if($post['payment'] != "")
                {
                    $this->datatables->where('sales_invoice.payment', $post['payment']);
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
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(id),invoice');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array( "title" => "Daftar Penjualan");
                $footer = array("script" => ['report/sales/invoice/sales_invoice_report.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('sales/invoice/sales_invoice_report');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        } 
    }

    public function print_sales_invoice_report()
    {
		if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DAFTAR PENJUALAN',
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
            $customer_code  = (!isset($post['customer_code'])) ? null : $post['customer_code'];
            $sales_code     = (!isset($post['sales_code'])) ? null : $post['sales_code'];
            // $ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
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

            // switch ($ppn){
            //     case "0":
            //         $ppn_filter = 'NON';
            //       break;
            //     case "1":
            //         $ppn_filter = 'PPN';
            //       break;                
            //       case "2":
            //         $ppn_filter = 'FINAL';
            //       break;                
            //     default:
            //         $ppn_filter = 'SEMUA';
            // }

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'payment_filter' => $payment_filter,
                'customer_code' => ($customer_code == '') ? 'SEMUA PELANGGAN' : $customer_code,
                'sales_code' => ($sales_code == '') ? 'SEMUA SALES' : $sales_code,
                // 'ppn_filter' => $ppn_filter,
                'payment_status_filter' => $payment_status_filter,
            ];

			$data = [
                'title'      => 'Laporan Daftar Penjualan',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->sales_report->print_sales_invoice_report($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
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
					<u>LAPORAN DAFTAR PENJUALAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td colspan="4">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>
                        </tr>
                            <tr>
                            <td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Sales: '.$filter['sales_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Pembayaran: '.$filter['payment_filter'].' </td>                            
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
			$data = $this->load->view('sales/invoice/print_sales_invoice_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }
    
    public function print_sales_invoice_detail_report()
    {
		if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DETAIL PENJUALAN',
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
            $customer_code  = (!isset($post['customer_code'])) ? null : $post['customer_code'];
            $sales_code     = (!isset($post['sales_code'])) ? null : $post['sales_code'];
            // $ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
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

            // switch ($ppn){
            //     case "0":
            //         $ppn_filter = 'NON';
            //       break;
            //     case "1":
            //         $ppn_filter = 'PPN';
            //       break;                
            //       case "2":
            //         $ppn_filter = 'FINAL';
            //       break;                
            //     default:
            //         $ppn_filter = 'SEMUA';
            // }

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'payment_filter' => $payment_filter,
                'customer_code'  => ($customer_code == '') ? 'SEMUA PELANGGAN' : $customer_code,
                'sales_code' => ($sales_code == '') ? 'SEMUA SALES' : $sales_code,
                // 'ppn_filter' => $ppn_filter,
                'payment_status_filter' => $payment_status_filter,
            ];

			$data = [
                'title'      => 'Laporan Detail Penjualan',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->sales_report->print_sales_invoice_detail_report($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
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
					<u>LAPORAN DETAIL PENJUALAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td colspan="4">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Sales: '.$filter['sales_code'].'</td>
                            <td style="border-bottom: 1px solid black;">Pembayaran: '.$filter['payment_filter'].' </td>
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
			$data = $this->load->view('sales/invoice/print_sales_invoice_detail_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    public function print_sales_invoice_daily_report()
    {
		if($this->input->method() === 'post')
		{
			$data_activity = [
				'information' => 'MENCETAK LAPORAN PENJUALAN HARIAN',
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
            $customer_code  = (!isset($post['customer_code'])) ? null : $post['customer_code'];
            $sales_code     = (!isset($post['sales_code'])) ? null : $post['sales_code'];
            // $ppn            = (!isset($post['ppn'])) ? null : $post['ppn'];
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

            // switch ($ppn){
            //     case "0":
            //         $ppn_filter = 'NON';
            //       break;
            //     case "1":
            //         $ppn_filter = 'PPN';
            //       break;                
            //       case "2":
            //         $ppn_filter = 'FINAL';
            //       break;                
            //     default:
            //         $ppn_filter = 'SEMUA';
            // }

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'payment_filter' => $payment_filter,
                'customer_code'  => ($customer_code == '') ? 'SEMUA PELANGGAN' : $customer_code,
                'sales_code' => ($sales_code == '') ? 'SEMUA SALES' : $sales_code,
                // 'ppn_filter' => $ppn_filter,
                'payment_status_filter' => $payment_status_filter,
			];
			
			$data = [
                'title'      => 'Laporan Penjualan Harian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->sales_report->print_sales_invoice_daily_report($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
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
					<u>LAPORAN PENJUALAN HARIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td colspan="4">Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>
                        </tr>
						<tr>
							<td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
							<td style="border-bottom: 1px solid black;">Sales: '.$filter['sales_code'].'</td>
							<td style="border-bottom: 1px solid black;">Pembayaran: '.$filter['payment_filter'].'</td>
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
			$data = $this->load->view('sales/invoice/print_sales_invoice_daily_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    // PRODUCT SALES
    public function get_total_product_sales_report()
    { 
        if($this->system->check_access('report/sales/invoice/product/total', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       = $this->input->post();
                $from_date  = ($post['from_date'] == "") ? null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    = ($post['to_date'] == "")   ? null : date('Y-m-d', strtotime($post['to_date']));
                $department_code    = (!isset($post['department_code']))    ? null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ? null : $post['subdepartment_code'];
                $customer_code      = $post['customer_code'];
                $sales_code         = $post['sales_code'];
                $ppn        = 0;
                $search     = $post['search'];
                if($search != "" || $department_code !="")
                {
                    $total_product = $this->sales_report->get_product_sales_report($from_date, $to_date, $department_code, $subdepartment_code, $customer_code, $sales_code, $ppn, $search)->num_rows();
                    $product       = $this->sales_report->get_product_sales_report($from_date, $to_date, $department_code, $subdepartment_code, $customer_code, $sales_code, $ppn, $search)->result_array();
                    $total_qty = 0; $total_sales = 0;
                    foreach($product AS $info)
                    {	
                        $sales_invoice = $this->sales_report->get_product_sales_invoice_detail_report($info['code'], $from_date, $to_date, $customer_code, $sales_code);                         
                        foreach($sales_invoice->result_array() AS $info3)
                        {					
                            $total_qty = $total_qty + ($info3['qty']*$info3['unit_value']);
                            $total_sales = $total_sales + $info3['total'];
                        }
                    }
                    $output = array(
                        'grandtotal'        => number_format($total_sales,0,".",","),
                        'total_product'     => number_format($total_product,0,".",","),
                        'total_qty'         => number_format($total_qty,2,".",","),
                    );
                }
                else
                {
                    $output = array(
                        'grandtotal'        => number_format(0, 2,".",","),
                        'total_product'     => number_format(0, 2,".",","),
                        'total_qty'         => number_format(0, 2,".",","),
                    );
                }			
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
    
    public function chart_product_sales()
    {
        if($this->system->check_access('report/sales/invoice/product/chart', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();            
                header('Content-Type: application/json');
                $filter = [
                    'view_type' => $post['view_type'],
                    'from_date' => format_date($post['from_date']),
                    'to_date'   => format_date($post['to_date']),
                    'department_code'   => $post['department_code'],
                    'subdepartment_code'=> $post['subdepartment_code'],
                    'customer_code' => $post['customer_code'],
                    'sales_code' => $post['sales_code'],
                    'search' => $post['search']
                ];
                $result = $this->sales_report->chart_product_sales($filter);
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
    
    public function product_sales()
    {     
        if($this->system->check_access('report/sales/invoice/product', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       = $this->input->post();        
                $from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    = ($post['to_date'] == "")   ?  null : date('Y-m-d', strtotime($post['to_date']));                        
                $department_code    = (!isset($post['department_code']))    ?   null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $customer_code      = $post['customer_code'];
                $sales_code         = $post['sales_code'];
                $ppn        = 0;
                $search     = $post['search'];                
                $draw       = (!isset($post['draw']))    ?        0 : $post['draw'];
                $iLength    = (!isset($post['length']))  ?   null : $post['length'];
                $iStart     = (!isset($post['start']))   ?    null : $post['start'];
                $iOrder   	= (!isset($post['order']))   ? null : $post['order'];
                if($search != "" || $department_code !="")
                {
                    $total      = $this->sales_report->get_product_sales_report($from_date, $to_date, $department_code, $subdepartment_code,  $customer_code, $sales_code, $ppn, $search) ->num_rows();
                    $product    = $this->sales_report->get_product_sales_report($from_date, $to_date, $department_code, $subdepartment_code,  $customer_code, $sales_code, $ppn, $search,$iLength, $iStart)->result_array();
                    $data 		= array();
                    foreach($product AS $info)
                    {
                        $total_sales = 0; $total_qty = 0;
                        $sales_invoice = $this->sales_report->get_product_sales_invoice_detail_report($info['code'], $from_date, $to_date, $customer_code, $sales_code);
                        foreach($sales_invoice->result_array() AS $info3)
                        {
                            $total_qty = $total_qty + ($info3['qty']*$info3['unit_value']);
                            $total_sales = $total_sales + $info3['total'];
                        }
                        $average_sales = ($total_qty == 0) ? 0 : $total_sales / $total_qty;
                        $data[] = array(
                            'id'           => $info['id'],
                            'barcode'      => $info['barcode'],
                            'code' 		   => '<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/'.encrypt_custom($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
                            'name'         => $info['name'],
                            'total_qty'    => $total_qty,
                            'unit'         => $info['unit'],
                            'total_sales'  => $total_sales,
                            'average_sales'=> $average_sales
                        );
                    }				
                    if($iOrder != null)
                    {
                        $column_option = array(null, null, 'name', 'total_qty', null, 'total_sales', 'average_sales');
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
                $header = array("title" => "Penjualan Per Produk");        
                $footer = array("script" => ['report/sales/product_sales_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('sales/product_sales_report');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    // CUSTOMER SALES
    public function total_customer_sales()
	{
        if($this->system->check_access('report/sales/invoice/customer/total', 'A'))
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
                                ->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1);
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
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
	}

	public function customer_sales()
    {        
        if($this->system->check_access('report/sales/invoice/customer', 'A'))
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
								->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1);
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
				$header = array( "title" => "Penjualan Per Pelanggan");
				$footer = array("script" => ['report/sales/customer_sales_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('sales/customer_sales_report');
				$this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        } 
	}

	public function print_customer_sales_report()
    {
		if($this->system->check_access('report/print_customer_global_sales_receivable_report', 'read'))
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

    // SALES RETURN
    public function get_total_sales_return_report()
    {      
        if($this->system->check_access('report/sales/return/total', 'A'))
        {
            if($this->input->is_ajax_request())
			{
                $post           = $this->input->post();
                $from_date      = $post['from_date'];
                $to_date        = $post['to_date'];        
                $customer_code  = $post['customer_code'];                

                $this->db->select('sales_return.total_return');
                $this->db->from('sales_return');
                $this->db->join('sales_invoice', 'sales_invoice.id = sales_return.sales_invoice_id', 'left');
                $this->db->join('customer', 'customer.code = sales_return.customer_code');		
                $this->db->join('sales_return_detail', 'sales_return_detail.sales_return_id = sales_return.id');
                $this->db->where('sales_return.do_status', 1);
                $this->db->where('sales_return.deleted', 0);
                $this->db->where('sales_return_detail.deleted', 0);
                $this->db->group_by('sales_return.id');
                if($from_date != "")
                {
                    $this->db->where('sales_return.date >=', date('Y-m-d', strtotime($from_date)));
                }
                if($to_date != "")
                {
                    $this->db->where('sales_return.date <=', date('Y-m-d', strtotime($to_date)));
                }                
                if($customer_code != "")
                {
                    $this->db->where('sales_return.customer_code', $customer_code);
                }
                $data = $this->db->get()->result_array();   
                $grandtotal =0;
                foreach($data AS $info)
                {
                    $grandtotal      = $grandtotal + $info['total_return'];
                }
                
                header('Content-Type: application/json');
                $result = array(
                    'grandtotal' => number_format($grandtotal, 2, ".", ",")
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
    
    public function sales_return()
    {
        if($this->system->check_access('report/sales/return', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post           = $this->input->post();
                $from_date      = $post['from_date'];
                $to_date        = $post['to_date'];
                $customer_code  = $post['customer_code'];                

                header('Content-Type: application/json');
                $this->datatables->select('sales_return.id AS id_sr, sales_return.code AS code_sr, sales_return.date, sales_invoice.invoice, sales_return.total_product, total_return, customer.name AS name_c');
                $this->datatables->from('sales_return');
                $this->datatables->join('sales_invoice', 'sales_invoice.id = sales_return.sales_invoice_id', 'left');
                $this->datatables->join('customer', 'customer.code = sales_return.customer_code');		
                $this->datatables->join('sales_return_detail', 'sales_return_detail.sales_return_id = sales_return.id');
                $this->datatables->where('sales_return.deleted', 0);
                $this->datatables->where('sales_return.do_status', 1);
                $this->datatables->where('sales_return_detail.deleted', 0);                
                if($from_date != "")
                {
                    $this->datatables->where('sales_return.date >=', date('Y-m-d', strtotime($from_date)));
                }
                if($to_date != "")
                {
                    $this->datatables->where('sales_return.date <=', date('Y-m-d', strtotime($to_date)));
                }                
                if($customer_code != "")
                {
                    $this->datatables->where('sales_return.customer_code', $customer_code);
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
                $footer = array("script" => ['report/sales/return/sales_return_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('sales/return/sales_return_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}
	
	public function print_sales_return_report()
    {
		if($this->input->method() === 'post')
		{
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DAFTAR RETUR PENJUALAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
			$customer_code  = (!isset($post['customer_code'])) ? null : $post['customer_code'];

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'customer_code' => ($customer_code == '') ? 'SEMUA PELANGGAN' : $customer_code
            ];
			$data = [
                'title'      => 'Laporan Daftar Retur Penjualan',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->sales_report->print_sales_return_report($from_date, $to_date, $customer_code)
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
					<u>LAPORAN DAFTAR RETUR PENJUALAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
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
			$data = $this->load->view('sales/return/print_sales_return_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }
    
    public function print_sales_return_detail_report()
    {
		if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DETAIL RETUR PENJUALAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
            $customer_code  = (!isset($post['customer_code'])) ? null : $post['customer_code'];
            
			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,                
                'customer_code' => ($customer_code == '') ? 'SEMUA PELANGGAN' : $customer_code
            ];
			$data = [
                'title'      => 'Laporan Detail Retur Penjualan',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->sales_report->print_sales_return_detail_report($from_date, $to_date, $customer_code)
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
					<u>LAPORAN DETAIL RETUR PENJUALAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td >Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
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
			$data = $this->load->view('sales/return/print_sales_return_detail_report', $data, true);
			$mpdf->WriteHTML($data);
            $mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }

    public function print_sales_return_daily_report()
    {
		if($this->input->method() === 'post')
		{
			$data_activity = [
				'information' => 'MENCETAK LAPORAN RETUR PENJUALAN HARIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));			
            $customer_code  = (!isset($post['customer_code'])) ? null : $post['customer_code'];
            
			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,                
                'customer_code' => ($customer_code == '') ? 'SEMUA PELANGGAN' : $customer_code
            ];
			$data = [
                'title'      => 'Laporan Retur Penjualan Harian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->sales_report->print_sales_return_daily_report($from_date, $to_date, $customer_code)
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
					<u>LAPORAN RETUR PENJUALAN HARIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">Pelanggan: '.$filter['customer_code'].'</td>
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
			$data = $this->load->view('sales/return/print_sales_return_daily_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
	}
	
    // PRODUCT SALES RETURN
	public function get_total_product_sales_return_report()
    {   
        if($this->system->check_access('report/sales/return/product/total', 'A'))
        {
            if($this->input->is_ajax_request())
			{
                $post       = $this->input->post();        
                $search     = $post['search'];
                $from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    = ($post['to_date'] == "") ?    null : date('Y-m-d', strtotime($post['to_date']));        
                $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $product    = $this->sales_report->get_product_sales_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code)->result_array();
                $grandtotal = 0; $total_product = 0; $total_qty = 0;
                foreach($product AS $info)
                {
                    $sales_return_detail = $this->sales_report->get_product_sales_return_detail_report($info['code'], $from_date, $to_date);
                    if($sales_return_detail->num_rows() > 0)
                    {
                        $total_sales_return = 0;
                        foreach($sales_return_detail->result_array() AS $info2)
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
                            $total_sales_return = $total_sales_return + $info2['total'];					
                        }

                        $grandtotal = $grandtotal + $total_sales_return;
                                    
                    }
                    else
                    {				
                        continue;
                    }   
                    $total_product++;         
                }
                
                $output = array(
                    'grandtotal'        => number_format($grandtotal,0,",","."),
                    'total_product'     => number_format($total_product,0,",","."),
                    'total_qty'         => number_format($total_qty,2,",","."),
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

    public function product_sales_return()
    {
        if($this->system->check_access('report/sales/return/product', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post       = $this->input->post();        
                $search     = $post['search'];
                $from_date  = ($post['from_date'] == "") ?  null : date('Y-m-d', strtotime($post['from_date']));
                $to_date    = ($post['to_date'] == "") ?    null : date('Y-m-d', strtotime($post['to_date']));
                $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $draw       = (!isset($post['draw'])) ?        0 : $post['draw'];
                $iLength    = (!isset($post['length'])) ?   null : $post['length'];
                $iStart     = (!isset($post['start'])) ?    null : $post['start'];
                $iOrder     = (!isset($post['order'])) ?    null : $post['order'];

                $total      = $this->sales_report->get_product_sales_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code)->num_rows();
                $product    = $this->sales_report->get_product_sales_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $iLength, $iStart, $iOrder)->result_array();
                $data 		= array();
                foreach($product AS $info)
                {			
                    $sales_detail = $this->sales_report->get_product_sales_return_detail_report($info['code'], $from_date, $to_date);
                    if($sales_detail->num_rows() > 0)
                    {
                        $total_sales_return = 0; $total_qty = 0;
                        foreach($sales_detail->result_array() AS $info2)
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

                            $total_sales_return = $total_sales_return + $info2['total'];
                        }

                        if($total_qty == 0)
                        {
                            $average_sales_return = 0;
                        }
                        else
                        {
                            $average_sales_return = $total_sales_return / $total_qty;
                        }

                        $data[] = array(
                            'id'                    => $info['id'],
                            'barcode'               => $info['barcode'],
                            'code' 		            => '<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/'.encrypt_custom($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
                            'name'                  => $info['name'],
                            'total_qty'             => $total_qty,
                            'unit'                  => $info['unit'],
                            'total_sales_return'    => $total_sales_return, 
                            'average_sales_return'  => $average_sales_return
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
                $header = array("title" => "Retur Penjualan Per Produk");        
                $footer = array("script" => ['report/sales/return/product_sales_return_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('sales/return/product_sales_return_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }            
    }

     // INACTIVE CUSTOMER SALES
     public function inactive_customer_sales()
     {     
         if($this->system->check_access('repot/sales/inactive_customer', 'read'))
         {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();                                
                $inactive_customer_sales_id = $this->sales_report->inactive_customer_id($post['from_date'], $post['to_date']);
                $this->datatables->select('customer.id, customer.code AS search_code, customer.code, customer.name, customer.address, customer.contact, customer.telephone, customer.phone, zone.code AS zone, customer.pkp')
                                 ->from('customer')
                                 ->join('zone', 'zone.id = customer.zone_id', 'left')
                                 ->where('customer.id !=', 1)->where('customer.status', 1)
                                 ->group_by('customer.id');
                if(count($inactive_customer_sales_id) > 0)
                {
                    $this->datatables->where_in('customer.id', $inactive_customer_sales_id);
                }                                
                if($post['zone_id'] != "")
                {
                    $this->datatables->where('customer.zone_id', $post['zone_id']);
                }
                $this->datatables->add_column('code', 
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('customer/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(code),code');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array( "title" => "Penjualan Pelanggan Tidak Aktif");
                $footer = array("script" => ['report/sales/inactive_customer_sales_report.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('sales/inactive_customer_sales_report');
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