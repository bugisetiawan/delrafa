<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_report extends System_Controller 
{	
    public function __construct()
  	{
        parent::__construct();
      	$this->load->model('Chart_report_model', 'chart_report');
    }

    public function sales_invoice()
    {       
        if($this->input->is_ajax_request())
        {
			$post = $this->input->post();            
			header('Content-Type: application/json');
			$from_date = format_date($post['from_date']);
			$to_date   = format_date($post['to_date']);
			$payment   = $post['payment'];
			$customer_code = $post['customer_code'];
			$sales_code    = $post['sales_code'];
			$result = $this->chart_report->sales_invoice($post['view_type'], $from_date, $to_date);
          	echo json_encode($result);
        }
		else
		{
			if($this->system->check_access('report/chart/sales', 'read'))
            {
                $header = array("title" => "Grafik/Statistik Penjualan");
				$footer = array("script" => ['report/chart/sales_invoice_chart_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');
				$this->load->view('chart/sales_invoice_chart_report');
				$this->load->view('include/footer', $footer);
            }
            else
            {
                $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
                redirect(site_url('dashboard'));
            }
		}
    }
}
