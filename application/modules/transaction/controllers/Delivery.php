<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
        $this->load->model('Delivery_model', 'delivery');
	}

    public function index()
    {
        if($this->system->check_access('delivery', 'A'))
        {
            if($this->input->is_ajax_request())
			{
                $this->datatables->select('delivery.id, delivery.date, delivery.code')
                                 ->from('delivery')                         
                                 ->group_by('delivery.id');
                $this->datatables->add_column('code', 
                '<a class="kt-font-primary kt-link text-center" href="'.site_url('delivery/detail/$1').'" target="_blank"><b>$2</b></a>
                ', 'encrypt_custom(id), code');
                header('Content-Type: application/json');
                echo $this->datatables->generate();                
			}
			else
			{
				$data_activity = [
                    'information' => 'MELIHAT DAFTAR PENGIRIMAN',
                    'method'      => 1,
                    'code_e'      => $this->session->userdata('code_e'),
                    'name_e'      => $this->session->userdata('name_e'),
                    'user_id'     => $this->session->userdata('id_u')
                ];						
                $this->crud->insert('activity', $data_activity);
                $header = array("title" => "Pengiriman");
                $footer = array("script" => ['transaction/delivery/delivery.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('delivery/delivery');
                $this->load->view('include/footer', $footer);
			}
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }   
    }

    public function create()
    {
        if($this->system->check_access('delivery', 'C'))
        {
            if($this->input->is_ajax_request())
			{
                $post = $this->input->post();
                $date = format_date($post['date']);            
                $this->datatables->select('sales_invoice.id, sales_invoice.date, sales_invoice.invoice, customer.name AS name_c, sales.name AS name_s, sales_invoice.grandtotal')
                                     ->from('sales_invoice')
                                     ->join('customer', 'customer.code = sales_invoice.customer_code')
                                     ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')                                     
                                     ->where('sales_invoice.date', $date)
                                     ->where('sales_invoice.deleted', 0)->where('sales_invoice.delivery_status', 0);
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
                    $code = $this->delivery->delivery_code();
                    $data_delivery = [
                        'date' => format_date($post['date']),
                        'code' => $code,
                        'employee_code' => $this->session->userdata('code_e')
                    ];
                    $delivery_id = $this->crud->insert_id('delivery', $data_delivery);
                    if($delivery_id != null)
                    {
                        foreach($post['sales_invoice_id'] AS $info_sales_invoice_id)
                        {
                            $data_delivery_detail = [
                                'delivery_id' => $delivery_id,
                                'sales_invoice_id' => $info_sales_invoice_id
                            ];
                            $this->crud->insert('delivery_detail', $data_delivery_detail);
                            $this->crud->update('sales_invoice', ['delivery_status' => 1], ['id' => $info_sales_invoice_id]);
                        }
                    }
                    $data_activity = [
                        'information' => 'MEMBUAT PENGIRIMAN BARU (NO. TRANSAKSI '.$code.')',
                        'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];						
                    $this->crud->insert('activity', $data_activity);
                    $this->session->set_flashdata('success', 'Pengiriman berhasil disimpan');
                    redirect(site_url('delivery/detail/'.encrypt_custom($delivery_id)));
                }
                else
                {
                    $header = array("title" => "Pengiriman Baru");
                    $footer = array("script" => ['transaction/delivery/create_delivery.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('delivery/create_delivery');
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

    public function datatable_detail_delivery()
    {
        if($this->input->is_ajax_request())
        {
            $post = $this->input->post();
            $this->datatables->select('sales_invoice.id, sales_invoice.date, sales_invoice.invoice, customer.name AS name_c, sales.name AS name_s, sales_invoice.grandtotal')
                                    ->from('sales_invoice')
                                    ->join('customer', 'customer.code = sales_invoice.customer_code')
                                    ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
                                    ->join('delivery_detail', 'delivery_detail.sales_invoice_id = sales_invoice.id')
                                    ->where('delivery_detail.delivery_id', $post['delivery_id'])
                                    ->where('sales_invoice.deleted', 0);
            $this->datatables->group_by('sales_invoice.id');
            $this->datatables->add_column('invoice', 
            '<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/$1').'" target="_blank"><b>$2</b></a>
            ', 'encrypt_custom(id), invoice');
            header('Content-Type: application/json');
            echo $this->datatables->generate();
        }
    }

    public function detail($delivery_id)
    {
        if($this->system->check_access('delivery', 'R'))
        {
            $delivery = $this->delivery->detail_delivery(decrypt_custom($delivery_id));
            $data_activity = [
                'information' => 'MELIHAT DETAIL PENGIRIMAN (NO. TRANSAKSI '.$delivery['code'].')',
                'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                'code_e'      => $this->session->userdata('code_e'),
                'name_e'      => $this->session->userdata('name_e'),
                'user_id'     => $this->session->userdata('id_u')
            ];						
            $this->crud->insert('activity', $data_activity);
            $header = array("title" => "Detail Pengiriman");
            $footer = array("script" => ['transaction/delivery/detail_delivery.js']);
            $data = [
                'delivery' => $delivery                
            ];
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('delivery/detail_delivery', $data);
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function print($delivery_id)
    {
        if($this->system->check_access('delivery', 'R'))
        {
            $delivery = $this->delivery->detail_delivery(decrypt_custom($delivery_id));
            $data_activity = [
                'information' => 'CETAK DETAIL PENGIRIMAN (NO. TRANSAKSI '.$delivery['code'].')',
                'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                'code_e'      => $this->session->userdata('code_e'),
                'name_e'      => $this->session->userdata('name_e'),
                'user_id'     => $this->session->userdata('id_u')
            ];						
            $this->crud->insert('activity', $data_activity);
            $data = [
                'delivery' => $delivery,
                'delivery_detail' => $this->delivery->detail_delivery_detail($delivery['id'])
            ];
            $this->load->view('delivery/print_delivery', $data);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function delete()
    {
        if($this->system->check_access('delivery', 'D'))
        {

        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }
}