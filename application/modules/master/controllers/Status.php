<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;	
        $this->load->model('Crud_model', 'crud');        
    }
    
    private $table = 'status';

    public function index()
    {
        $header = array(
            "title" => "Status"
        );                          
        $this->load->view('include/header', $header);        
        $this->load->view('include/menubar');        
        $this->load->view('include/topbar');        
        $this->load->view('status/status');        
        $this->load->view('include/footer');
    }
  
    public function check_code($code)
    {
        $post   = $this->input->post();        
        if(isset($post['code']))
        {
            $code = $post['code'];            
            $type = "add";
        }
        else
        {
            $code = $post['editCode'];            
            $type = "update";
        }
        $data = array(
            'code' => $code,
            'deleted' => 0
        );
        $check  = $this->crud->get_where($this->table, $data);        
        if($check->num_rows() == 1)
        {                          
            return FALSE;            
        }
        else
        {
            return TRUE;
        }
    }

	public function add()
    {
        $post   = $this->input->post();
        $this->form_validation->set_rules('code', 'Kode', 'trim|required|callback_check_code|xss_clean');
        $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');

        $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
        $this->form_validation->set_message('is_unique', 'Maaf! <b>%s</b> Telah Digunakan');
        $this->form_validation->set_message('check_code', 'Maaf! Kode Telah Digunakan');

        if($this->form_validation->run() == FALSE)
        {
            echo validation_errors();
        }
        else
        {
            $data = [
                'code'  => $post['code'],
				'name'	=> $post['name'],
            ];
            $insert	= $this->crud->insert($this->table, $data);
            if($insert)
            {
                $response = [
                    'status' => [
                        'code'      => 200,
                        'message'   => 'Berhasil Menambahkan Data',
                    ],
                    'response'  => ''
                ];
                echo json_encode($response);
            }
            else
            {
                $response = [
                    'status' => [
                        'code'      => 401,
                        'message'   => 'Gagal Menambahkan Data',
                    ],
                    'response'  => ''
                ];
                echo json_encode($response);
            }
        }
    }	    
        
    public function update()
    {
        $post = $this->input->post();
        $this->form_validation->set_rules('editId', 'ID', 'trim|required|xss_clean');
        $this->form_validation->set_rules('editCode', 'Kode', 'trim|required|callback_check_code|xss_clean');
        $this->form_validation->set_rules('editName', 'Nama', 'trim|required|xss_clean');

        $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
        $this->form_validation->set_message('check_code', 'Maaf! Kode Telah Digunakan');

        if($this->form_validation->run() == FALSE)
        {
            echo validation_errors();
        }
        else
        {
            $data	= ['code' => $post['editCode'], 'name' => $post['editName'] ];
            $id     = $post['editId'];
            $update = $this->crud->update_by_id($this->table, $data, $id);
            if($update)
            {
                $response = [
                    'status'	=> [
						'code'  	=> 200,
						'message'   => 'Berhasil Mengubah Data',
                    ],
					'response'  => ''
                ];
                echo json_encode($response);
            }
            else
            {
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Mengubah Data',
                    ],
                    'response'  => ''
                ];
                echo json_encode($response);
            }
        }
    }
	
    public function delete()
    {        
        $id     = $this->input->get('id');
        $data = array(
            'deleted' => 1
        );
        $delete = $this->crud->update_by_id($this->table, $data, $id);
        if($delete)
        {
            $response = [
                'status'	=> [
                    'code'  	=> 200,
                    'message'   => 'Berhasil Menghapus Data',
                ],
                'response'  => ''
            ];
            echo json_encode($response);
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
            echo json_encode($response);
        }
	}    
}
