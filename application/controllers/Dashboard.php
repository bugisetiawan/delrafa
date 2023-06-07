<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends System_Controller 
{	
    public function __construct()
  	{
      parent::__construct();
      $this->load->model('Dashboard_model', 'dashboard');
    }
    
    public function index()
    {
        redirect(base_url('dashboard')); 
    }		
	
    public function get_log_user_login()
    {
        $access_user_id = [1, 3, 14, 17];
		if(in_array($this->session->userdata('id_u'), $access_user_id))
		{
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$this->datatables->select('user.id AS id_u, employee.code AS code_e, employee.name AS name_e, last_login')
								 ->from('user')
								 ->join('employee', 'employee.code = user.employee_code')
								 ->where('user.id >', 2)
								 ->where('user.active', 1)
								 ->order_by('user.id', 'ASC');
				echo $this->datatables->generate();		
			}
			else
			{
				$this->load->view('auth/show_404');
			}                  			
		}
		else
		{
			redirect(base_url('denied'));
		}
    }

    public function get_log_user_activity()
    {
        $access_user_id = [1, 3, 14, 17];
		if(in_array($this->session->userdata('id_u'), $access_user_id))
        {
			if($this->input->is_ajax_request())
			{
				header('Content-Type: application/json');
				$this->datatables->select('activity.id AS id_a , information, method, employee.code AS code_e, employee.name AS name_e, activity.created');
				$this->datatables->from('activity');
				$this->datatables->join('user', 'user.id = activity.user_id');
				$this->datatables->join('employee', 'employee.code = user.employee_code');
				$this->datatables->where('DATE(activity.created)', date('Y-m-d'));				
				$this->datatables->where('user.id >', 2);
				// $this->datatables->where('user.id >', $this->session->userdata('id_u'));
				$this->datatables->order_by('activity.id', 'DESC');
				echo $this->datatables->generate();
			}
			else
			{
				$this->load->view('auth/show_404');
			}            
        }
        else
        {
            redirect(base_url('denied'));
        }
    }

    public function get_total_master_data()
    {
        if($this->input->is_ajax_request())
		{
            $data = [
                'total_product'  => $this->crud->get_where('product',  ['deleted' => 0])->num_rows(),
                'total_supplier' => $this->crud->get_where('supplier', ['deleted' => 0])->num_rows(),
                'total_customer' => $this->crud->get_where('customer', ['deleted' => 0])->num_rows(),
                'total_employee' => $this->crud->get_where('employee', ['id >' => 4, 'status' => 1, 'is_user' => 0, 'deleted' => 0])->num_rows()
            ];
            header('Content-Type: application/json');
            echo json_encode($data);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    public function datatable_purchase_invoice()
    {
        if($this->input->is_ajax_request())
        {
            
        }
        else
        {
            $this->load->view('auth/show_404');
        }
    }
    
    public function cheque_of_debt()
    {
        if($this->system->check_access('report/finance/cheque_of_debt', 'read'))
		{
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('pod.id AS id, pod.date, pod.code AS code_pod, pod.cheque_number, pod.cheque_status, pod.cheque,
										   pod.code AS search_code_pod');
				$this->datatables->from('payment_ledger AS pod');
				$this->datatables->where('pod.transaction_type', 1);
				$this->datatables->where('pod.cheque_account_id !=', 0);
				$this->datatables->where('pod.cheque_status', 2);
				$this->datatables->where('pod.deleted', 0);
				$this->datatables->where('DATE(pod.cheque_close_date) <=', date('Y-m-d'));				
				$this->datatables->add_column('code_pod', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id), code_pod');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Hutang Cek/Giro");
				$footer = array("script" => ['report/finance/cheque_of_debt_report.js']);
				$this->load->view('include/header', $header);
				$this->load->view('include/menubar');
				$this->load->view('include/topbar');
				$this->load->view('finance/cheque_of_debt_report');
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
		if($this->system->check_access('report/finance/cheque_of_receivable', 'read'))
		{
			if($this->input->is_ajax_request())
			{
				$post           = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('por.id AS id, por.date, por.code AS code_por, por.cheque_number, por.cheque_status, por.cheque,
										   por.code AS search_code_por');
				$this->datatables->from('payment_ledger AS por');				
				$this->datatables->where('por.transaction_type', 2);				
				$this->datatables->where('por.cheque_account_id !=', 0);
				$this->datatables->where('por.cheque_status', 2);
				$this->datatables->where('por.deleted', 0);
				$this->datatables->where('DATE(por.cheque_close_date) <=', date('Y-m-d'));				
				$this->datatables->add_column('code_por', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('payment/debt/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id), code_por');
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

    public function dashboard()
    {
		$data_activity = [
			'information' => 'MELIHAT HALAMAN UTAMA',
			'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
			'code_e'      => $this->session->userdata('code_e'),
			'name_e'      => $this->session->userdata('name_e'),
			'user_id'     => $this->session->userdata('id_u')
		];						
		$this->crud->insert('activity', $data_activity);

        $header = array("title" => "Halaman Utama");
        $footer = array("script" => ['dashboard.js']);
        $this->load->view('include/header', $header);
        $this->load->view('include/menubar');        
        $this->load->view('include/topbar');        
        $this->load->view('dashboard/dashboard');        
        $this->load->view('include/footer', $footer);
	}
}