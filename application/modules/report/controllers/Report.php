<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends System_Controller 
{	
    public function __construct()
  	{
      parent::__construct();
      $this->load->model('master/Product_model', 'product');
      $this->load->model('Report_model', 'report');
    }

    public function index()
    {
        $header = array(
            "title" => "Daftar Laporan"
            );        
        $this->load->view('include/header', $header);        
        $this->load->view('include/menubar');        
        $this->load->view('include/topbar');        
        $this->load->view('report');
        $this->load->view('include/footer');
    }    

    // CASHIER RECAP
    public function get_cashier()
    {
        $data       = $this->report->get_cashier();
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
}
