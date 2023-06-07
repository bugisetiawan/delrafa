<?php defined('BASEPATH') OR exit('No direct script access allowed');

class System_Controller extends CI_Controller 
{
		
	public function __construct()
	{        
        parent::__construct();  
        $this->form_validation->CI =&$this;      
        if($this->session->userdata('login')!=1)
        {
            redirect(base_url('login'), 'refresh');
        }        
        else
        {
            $this->load->model('Crud_model', 'crud');
            $this->load->model('Global_model', 'global');            
        }
    }    
}