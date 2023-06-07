<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends System_Controller {

    public function __construct()
  	{
		parent::__construct();
        $this->form_validation->CI =&$this;	
        $this->load->model('Crud_model', 'crud');
        $this->load->model('Global_model', 'global');        
    }    
    
	private $title = "Profil Perusahaan";	

    public function index()
    {
    	$header = array(
        	"title" => $this->title
		);
		$data = array(
			'profile' => $this->global->company()
		);                                                   
		$this->load->view('include/header', $header);        
		$this->load->view('include/menubar');        
		$this->load->view('include/topbar');        
		$this->load->view('profile', $data);        
		$this->load->view('include/footer');
	  }
	  
	  public function save()
	  {
			$this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
			$this->form_validation->set_rules('address', 'Alamat', 'trim|required|xss_clean');
			$this->form_validation->set_rules('phone', 'No. Handphone', 'numeric|trim|required|xss_clean');
			$this->form_validation->set_rules('telephone', 'No.Telepon', 'numeric|trim|required|xss_clean');

			$this->form_validation->set_message('numeric', 'Maaf! <b>%s</b> Harus Berupa Angka');
			$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');			
			if($this->form_validation->run() == FALSE)
			{
				$header = array(
					"title" => $this->title
				);
				$data = array(
					'profile' => $this->global->company()
				);                                                   
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('profile', $data);        
				$this->load->view('include/footer');            
			}
			else
			{
				$post       = $this->input->post();				
				$data = [					
					'name'      => $post['name'],
					'address'   => $post['address'],
					'phone'   	=> $post['phone'],
					'telephone' => $post['telephone']					
				];
				if($this->crud->update('setting', ['information' => json_encode($data)], ['name' => 'company']))
				{
					$this->session->unset_userdata('company');
					$this->session->set_userdata('company', $this->global->company());
					$this->session->set_flashdata('success', 'Data berhasil disimpan');
					redirect(base_url('setting/profile'));
				}
				else
				{
					$this->session->set_flashdata('error', 'Mohon maaf, Data gagal disimpan');
					redirect(base_url('setting/profile'));
				}            
			}
	  }
}