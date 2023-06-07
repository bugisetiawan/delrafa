<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends System_Controller {

    public function __construct()
  	{
		parent::__construct();
        $this->load->model('Customer_model', 'customer');
    }

    private $table = "customer";	

    public function index()
    {   
        if($this->system->check_access('customer', 'A'))
		{
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                $this->datatables->select('customer.id, customer.code, customer.name, customer.contact, customer.telephone, customer.phone, customer.pkp, customer.status, zone.code AS zone')
                                 ->from('customer')
                                 ->join('zone', 'zone.id = customer.zone_id', 'left')
                                 ->where('customer.deleted', 0);
                if($post['status'] != "")
                {
                    $this->datatables->where('customer.status', $post['status']);
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
                $data_activity = [
                    'information' => 'MELIHAT DAFTAR PELANGGAN',
                    'method'      => 1,
                    'code_e'      => $this->session->userdata('code_e'),
                    'name_e'      => $this->session->userdata('name_e'),
                    'user_id'     => $this->session->userdata('id_u')
                ];
                $this->crud->insert('activity', $data_activity);
                
                $header = array("title" => "Pelanggan");
                $footer = array("script" => ['master/customer/customer.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('customer/customer');        
                $this->load->view('include/footer', $footer);
            }            
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('dashboard'));			
        }                       	
  	}  
  
    // CREATE
    public function add()
    {
        if($this->system->check_access('customer', 'C'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('address', 'Alamat', 'trim|required|xss_clean');
                $this->form_validation->set_rules('province', 'Provinsi', 'trim|xss_clean');
                $this->form_validation->set_rules('city', 'Kota', 'trim|xss_clean');
                $this->form_validation->set_rules('phone', 'No. Ponsel', 'min_length[10]|max_length[13]|trim|numeric|xss_clean');
                $this->form_validation->set_rules('telephone', 'No. Telepon', 'trim|xss_clean');
                $this->form_validation->set_rules('contact', 'Kontak', 'trim|xss_clean');
                $this->form_validation->set_rules('email', 'Email', 'valid_email|valid_emails|trim|xss_clean');
                $this->form_validation->set_rules('credit', 'Plafon Kredit', 'trim|required|xss_clean');
                $this->form_validation->set_rules('dueday', 'Jatuh Tempo', 'numeric|trim|required|xss_clean');
                $this->form_validation->set_rules('price_class', 'Harga Jual', 'numeric|trim|required|xss_clean');
                $this->form_validation->set_rules('pkp', 'PKP', 'numeric|trim|xss_clean');
                $this->form_validation->set_rules('npwp', 'NPWP', 'numeric|trim|xss_clean');
                $this->form_validation->set_rules('zone', 'Zona Wilayah', 'numeric|trim|xss_clean');

                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('numeric', 'Maaf! <b>%s</b> Harus Berupa Angka');
                $this->form_validation->set_message('min_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('max_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('valid_email', 'Maaf! <b>%s</b> Tidak Valid');
                $this->form_validation->set_message('valids_email', 'Maaf! <b>%s</b> Tidak Valid');

                if($this->form_validation->run() == FALSE)
                {
                    $header = array( "title" => "Pelanggan Baru");
                    $footer = array("script" => ['master/customer/crud_customer.js']);
                    $this->load->view('include/header', $header);        
                    $this->load->view('include/menubar');        
                    $this->load->view('include/topbar');        
                    $this->load->view('customer/add_customer');        
                    $this->load->view('include/footer', $footer);
                }
                else
                {
                    $post       = $this->input->post();
                    $code       = $this->customer->customer_code();
                    $data_cust  = [
                        'code'          => $code,
                        'name'          => $post['name'],                                
                        'address'       => $post['address'],
                        'province_id'   => (($post['province'] == "")) ?  null : $post['province'],
                        'city_id'       => (($post['city'] == "")) ?  null : $post['city'],
                        'phone'         => (($post['phone'] == "")) ?  null : $post['phone'],
                        'telephone'     => (($post['telephone'] == "")) ?  null : $post['telephone'],                
                        'contact'       => (($post['contact'] == "")) ?  null : $post['contact'],
                        'email'         => (($post['email'] == "")) ?  null : $post['email'],
                        'credit'        => format_amount($post['credit']),
                        'dueday'        => $post['dueday'],              
                        'price_class'   => $post['price_class'],  
                        'pkp'           => (!isset($post['pkp'])) ?  0 : $post['pkp'],
                        'npwp'          => (!isset($post['pkp'])) ?  null : $post['npwp'],
                        'zone_id'       => (($post['zone'] == "")) ?  null : $post['zone']
                    ];
                    
                    if($this->crud->insert('customer', $data_cust))
                    {
                        $data_activity = [
                            'information' => 'MEMBUAT PELANGGAN BARU (CODE - '.$code.')',
                            'method'      => 3,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);                        
                        $this->session->set_flashdata('success', 'Data Pelanggan berhasil ditambahkan');
                        
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, Data Pelanggan gagal ditambahkan');
                    }            
                    redirect(base_url('customer'));
                }                
            }
            else
            {
                $header = array( "title" => "Pelanggan Baru");
                $footer = array("script" => ['master/customer/crud_customer.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('customer/add_customer');        
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url($this->table));
        }        
    }
     
    // DETAIL
    public function detail($code)
    {        
        if($this->system->check_access('customer', 'R'))
        {
            $code = $this->global->decrypt($code);
            $data_activity = [
				'information' => 'MELIHAT DETAIL PELANGGAN (CODE '.$code.')',
				'method'      => 2,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
            $this->crud->insert('activity', $data_activity);
            
            $header = array("title" => "Detail Pelanggan");            
            $data = array('customer' => $this->customer->detail_customer($code));
            $footer = array("script" => ['master/customer/detail_customer.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('customer/detail_customer', $data);        
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url($this->table));
        }        
    }
    
    public function update($code)
    {
        if($this->system->check_access('customer', 'U'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('address', 'Alamat', 'trim|required|xss_clean');
                $this->form_validation->set_rules('province', 'Provinsi', 'trim|xss_clean');
                $this->form_validation->set_rules('city', 'Kota', 'trim|xss_clean');
                $this->form_validation->set_rules('phone', 'No. Ponsel', 'min_length[10]|max_length[13]|trim|numeric|xss_clean');
                $this->form_validation->set_rules('telephone', 'No. Telepon', 'trim|xss_clean');
                $this->form_validation->set_rules('contact', 'Kontak', 'trim|xss_clean');
                $this->form_validation->set_rules('email', 'Email', 'valid_email|valid_emails|trim|xss_clean');
                $this->form_validation->set_rules('credit', 'Plafon Kredit', 'trim|required|xss_clean');
                $this->form_validation->set_rules('dueday', 'Jatuh Tempo', 'numeric|trim|required|xss_clean');
                $this->form_validation->set_rules('price_class', 'Harga Jual', 'numeric|trim|required|xss_clean');
                $this->form_validation->set_rules('pkp', 'PKP', 'numeric|trim|xss_clean');
                $this->form_validation->set_rules('npwp', 'NPWP', 'numeric|trim|xss_clean');
                $this->form_validation->set_rules('zone', 'Zona Wilayah', 'numeric|trim|xss_clean');
                $this->form_validation->set_rules('status', 'Alamat', 'trim|required|xss_clean');

                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('numeric', 'Maaf! <b>%s</b> Harus Berformat Angka');
                $this->form_validation->set_message('min_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('max_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('valid_email', 'Maaf! <b>%s</b> Tidak Valid');
                $this->form_validation->set_message('valids_email', 'Maaf! <b>%s</b> Tidak Valid');

                $code    = $this->input->post('code');
                if($this->form_validation->run() == FALSE)
                {
                    $header = array("title" => "Perbarui Pelanggan");            
                    $data = array('customer' => $this->customer->detail_customer($code));
                    $footer = array("script" => ['master/customer/crud_customer.js']);
                    $this->load->view('include/header', $header);        
                    $this->load->view('include/menubar');        
                    $this->load->view('include/topbar');        
                    $this->load->view('customer/update_customer', $data);        
                    $this->load->view('include/footer', $footer);             
                }
                else
                {
                    $post       = $this->input->post();
                    $data  = [
                        'name'          => $post['name'],                                
                        'address'       => $post['address'],
                        'province_id'   => (($post['province'] == "")) ?  null : $post['province'],
                        'city_id'       => (($post['city'] == "")) ?  null : $post['city'],
                        'phone'         => (($post['phone'] == "")) ?  null : $post['phone'],
                        'telephone'     => (($post['telephone'] == "")) ?  null : $post['telephone'],                
                        'contact'       => (($post['contact'] == "")) ?  null : $post['contact'],
                        'email'         => (($post['email'] == "")) ?  null : $post['email'],
                        'credit'        => format_amount($post['credit']),
                        'dueday'        => $post['dueday'],              
                        'price_class'   => $post['price_class'],  
                        'pkp'           => (!isset($post['pkp'])) ?  0 : $post['pkp'],
                        'npwp'          => (!isset($post['pkp'])) ?  null : $post['npwp'],
                        'zone_id'       => (($post['zone'] == "")) ?  null : $post['zone'],
                        'status'        => $post['status']
                    ];
                                        
                    if($this->crud->update_by_code($this->table, $data, $code))
                    {
                        $data_activity = [
                            'information' => 'MEMPERBARUI DATA PELANGGAN (CODE '.$code.')',
                            'method'      => 4,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $this->session->set_flashdata('success', 'Data Pelanggan berhasil diperbarui');                
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, Data Pelanggan gagal diperbarui');                
                    }
                }
                redirect(site_url('customer'));
            }
            else
            {
                $header = array("title" => "Perbarui Pelanggan");            
                $data = array('customer' => $this->customer->detail_customer($this->global->decrypt($code)));
                $footer = array("script" => ['master/customer/crud_customer.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');
                $this->load->view('customer/update_customer', $data);        
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(site_url('customer'));
        }        
    }    
        
    // DELETE
    public function delete()
    {    
        if($this->system->check_access('customer', 'D'))
        {
            $code     = $this->input->get('id');
			$data = array(
				'deleted' => 1
			);
			$delete = $this->crud->update_by_code($this->table, $data, $code);
            if($delete)
            {
                $data_activity = [
                    'information' => 'MENGHAPUS DATA PELANGGAN (CODE '.$code.')',
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
                $this->session->set_flashdata('success', 'Data berhasil dihapus');                
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
                $this->session->set_flashdata('error', 'Mohon maaf, Data gagal dihapus');                
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
}