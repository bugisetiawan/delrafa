<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends System_Controller{

    public function __construct()
  	{
        parent::__construct();
        $this->load->model('Supplier_model', 'supplier');	
    }

	private $table = "supplier";
	
    public function datatable()
    {        
        header('Content-Type: application/json');
        $this->datatables->select('supplier.id, supplier.code, supplier.name, city.name AS city, supplier.contact, supplier.phone, supplier.ppn AS pkp');
        $this->datatables->from('supplier');
        $this->datatables->join('city', 'city.id = supplier.city_id', 'left');
        $this->datatables->group_by('supplier.id');
        $this->datatables->where('deleted', 0);
        $this->datatables->add_column('code', 
        '
            <a class="kt-font-primary kt-link text-center" href="'.site_url('supplier/detail/$1').'"><b>$2</b></a>
        ', 'encrypt_custom(code),code');
        echo $this->datatables->generate();
	} 
	
    public function index()
    {    
        if($this->system->check_access('supplier', 'A'))
		{
            $data_activity = [
				'information' => 'MELIHAT DAFTAR SUPPLIER',
				'method'      => 1,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
            $this->crud->insert('activity', $data_activity);
            
            $header = array("title" => "Supplier");
            $footer = array("script" => ['master/supplier/supplier.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('supplier/supplier');        
            $this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('dashboard'));			
        }    	
    }  
        
    public function add()
    {
        if($this->system->check_access('supplier', 'C'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('address', 'Alamat', 'trim|xss_clean');
                $this->form_validation->set_rules('province', 'Provinsi', 'trim|xss_clean');
                $this->form_validation->set_rules('city', 'Kota', 'trim|xss_clean');
                $this->form_validation->set_rules('phone', 'No. Ponsel', 'min_length[10]|max_length[13]|trim|numeric|xss_clean');
                $this->form_validation->set_rules('telephone', 'No. Telepon', 'trim|xss_clean');        
                $this->form_validation->set_rules('contact', 'Kontak', 'trim|xss_clean');
                $this->form_validation->set_rules('email', 'Email', 'is_unique[supplier.email]|valid_email|valid_emails|trim|xss_clean');        
                $this->form_validation->set_rules('dueday', 'Jatuh Tempo', 'numeric|trim|xss_clean');

                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('is_unique', 'Maaf! <b>%s</b> Telah Terdaftarw');
                $this->form_validation->set_message('numeric', 'Maaf! <b>%s</b> Harus Berformat Angka');
                $this->form_validation->set_message('min_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('max_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('valid_email', 'Maaf! <b>%s</b> Tidak Valid');
                $this->form_validation->set_message('valids_email', 'Maaf! <b>%s</b> Tidak Valid');

                if($this->form_validation->run() == FALSE)
                {
                    $header = array( "title" => 'Supplier Baru');    
                    $footer = array("script" => ['master/supplier/crud_supplier.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');        
                    $this->load->view('include/topbar');        
                    $this->load->view('supplier/add_supplier');        
                    $this->load->view('include/footer', $footer);            
                }
                else
                {
                    
                    $post       = $this->input->post();            
                    $code       = $this->supplier->supplier_code();
                    // $credit     = str_replace('.','',substr($post['credit'],4));
                    $ppn = (!isset($post['ppn'])) ?  0 : $post['ppn'];
                    $dueday = ($post['dueday'] == "") ?  0 : $post['dueday'];
                    $data  = [
                        'code'			=> $code,
                        'name'          => $post['name'],
                        'address'       => $post['address'],
                        'province_id'	=> $post['province'],
                        'city_id'       => $post['city'],
                        'phone'         => $post['phone'],
                        'telephone'     => $post['telephone'],                
                        'contact'       => $post['contact'],
                        'email'         => $post['email'],                
                        'dueday'        => $dueday,
                        'ppn'        	=> $ppn
                    ];
                    
                    if($this->crud->insert($this->table, $data))
                    {
                        $data_activity = [
                            'information' => 'MEMBUAT SUPPLIER BARU (CODE '.$code.')',
                            'method'      => 3,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $this->session->set_flashdata('success', 'Data Supplier berhasil ditambahkan');                
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, Data Supplier gagal ditambahkan');                
                    }                                
                    redirect(site_url('supplier'));
                }                
            }
            else
            {
                $header = array("title" => "Supllier Baru");
                $footer = array("script" => ['master/supplier/crud_supplier.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('supplier/add_supplier');        
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url($this->table));
        }        
    }

    public function detail($code)
    {     
        if($this->system->check_access('supplier', 'R'))
        {
            $code = $this->global->decrypt($code);
            $data_activity = [
				'information' => 'MELIHAT DETAIL SUPPLIER (CODE '.$code.')',
				'method'      => 2,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
            $this->crud->insert('activity', $data_activity);
            
            $header = array("title" => "Detail Supplier");
            $footer = array("script" => ['master/supplier/detail_supplier.js']);
            $info = $this->crud->get_by_code($this->table, $code)->row_array();        
            $province = $this->supplier->get_province($info['province_id']);
            $city = $this->supplier->get_city($info['province_id'], $info['city_id']);    
            $data = array('info' => $info, 'province' => $province, 'city' => $city);            
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');        
            $this->load->view('supplier/detail_supplier', $data);        
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
        if($this->system->check_access('supplier', 'U'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('address', 'Alamat', 'trim|xss_clean');
                $this->form_validation->set_rules('province', 'Provinsi', 'trim|xss_clean');
                $this->form_validation->set_rules('city', 'Kota', 'trim|xss_clean');
                $this->form_validation->set_rules('phone', 'No. Ponsel', 'min_length[10]|max_length[13]|trim|numeric|xss_clean');
                $this->form_validation->set_rules('telephone', 'No. Telepon', 'trim|xss_clean');        
                $this->form_validation->set_rules('contact', 'Kontak', 'trim|xss_clean');
                $this->form_validation->set_rules('email', 'Email', 'valid_email|valid_emails|trim|xss_clean');    
                $this->form_validation->set_rules('dueday', 'Jatuh Tempo', 'numeric|trim|xss_clean');

                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('numeric', 'Maaf! <b>%s</b> Harus Berformat Angka');
                $this->form_validation->set_message('min_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('max_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('valid_email', 'Maaf! <b>%s</b> Tidak Valid');
                $this->form_validation->set_message('valids_email', 'Maaf! <b>%s</b> Tidak Valid');

                $code    = $this->input->post('code');
                if($this->form_validation->run() == false)
                {
                    $header = array("title" => 'Perbarui Supplier');     
                    $info = $this->crud->get_by_code($this->table, $code)->row_array();
                    $data = array('info' => $info);                                                   
                    $footer = array("script" => ['master/supplier/crud_supplier.js']);
                    $this->load->view('include/header', $header);        
                    $this->load->view('include/menubar');        
                    $this->load->view('include/topbar');        
                    $this->load->view('supplier/update_supplier', $data);        
                    $this->load->view('include/footer', $footer);
                }
                else
                {
                    $post   = $this->input->post();        			
                    $ppn    = (!isset($post['ppn'])) ?  0 : $post['ppn'];
                    $dueday = ($post['dueday'] == "") ?  0 : $post['dueday'];
                    $data  = [                
                        'name'          => $post['name'],
                        'address'       => $post['address'],
                        'province_id'   => $post['province'],
                        'city_id'       => $post['city'],
                        'phone'         => $post['phone'],
                        'telephone'     => $post['telephone'],                
                        'contact'       => $post['contact'],
                        'email'         => $post['email'],                
                        'dueday'        => $dueday,
                        'ppn'        	=> $ppn
                    ];
                                        
                    if($this->crud->update_by_code($this->table, $data, $code))
                    {                                                
                        $data_activity = [
                            'information' => 'MEMPERBARUI DATA SUPPLIER (CODE '.$code.')',
                            'method'      => 4,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $this->session->set_flashdata('success', 'Data berhasil diperbarui');                
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, Data gagal diperbarui');                
                    }                    
                    redirect(base_url('supplier'));
                }                
            }
            else
            {
                $code = $this->global->decrypt($code);
                $header = array( "title" => "Perbarui Supplier");
                $info = $this->crud->get_by_code($this->table, $code)->row_array();
                $data = array('info' => $info);
                $footer = array("script" => ['master/supplier/crud_supplier.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('supplier/update_supplier', $data);
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('supplier'));
        }
	}
	    
    public function delete()
    {
        if($this->system->check_access('supplier', 'D'))
        {
            if($this->input->is_ajax_request())
            {
                $supplier = $this->crud->get_where_select('id, code, name', 'supplier', ['id' => $this->input->get('id')])->row_array();
                $purchase_invoice = $this->crud->get_where_select('id', 'purchase_invoice', ['code' => $supplier['code']])->num_rows();
                // Check Purchase Invoice
                if($purchase_invoice == 0)
                {
                    $code = $this->input->get('id');        
                    $data = array(
                        'deleted' => 1
                    );
                    if($this->crud->update_by_code($this->table, $data, $code))
                    {
                        $data_activity = [
                            'information' => 'MENGHAPUS DATA SUPPLIER (CODE '.$code.')',
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
                            'message'   => 'Gagal Menghapus Data',
                        ],
                        'response'  => ''
                    ];
                    $this->session->set_flashdata('error', 'Mohon maaf, Data gagal dihapus. Sudah terdapat data yang terkait');

                }   
                echo json_encode($response);   
            }
            else
            {
                $this->load->view('auth/show_404');
            }                       
        }                   
	}    
}