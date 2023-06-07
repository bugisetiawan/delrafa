<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zone extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;	
        $this->load->model('Crud_model', 'crud');        
    }
    
	private $table = 'zone';
	
    public function index()
    {
        if($this->system->check_access('zone', 'A'))
		{
            $header = array("title" => "Zona");                                                          
            $footer = array("script" => ['master/zone.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('zone/zone');
            $this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('dashboard'));
        }        
    }

    public function check_code($code)
    {
        $post   = $this->input->post();        
        if(isset($post['code']))
        {
            $code = $post['code'];
        }
        else
        {
            $code = $post['editCode'];
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
        if($this->system->check_access('zone', 'C'))
        {
            $post   = $this->input->post();
            $this->form_validation->set_rules('code', 'Kode', 'trim|required|callback_check_code|xss_clean');
            $this->form_validation->set_rules('name', 'Keterangan', 'trim|required|xss_clean');

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
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
                ],
                'response'  => ''
            ];            
            echo json_encode($response);
        }        
    }	   
        
    public function update()
    {
        if($this->system->check_access('zone', 'U'))
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
                            'message'   => 'Berhasil Memperbaharui Data',
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
                            'message'   => 'Gagal Memperbaharui Data',
                        ],
                        'response'  => ''
                    ];
                    echo json_encode($response);
                }
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
            echo json_encode($response);
        }        
    }
	
    public function delete()
    {  
        if($this->system->check_access('zone', 'D'))
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
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
                ],
                'response'  => ''
            ];            
            echo json_encode($response);            
        }               
	} 
		
	public function get_zone()
	{
		$data       = $this->crud->get('zone')->result();
		if($data)
		{
			$response   = [
				'status'    => [
					'code'      => 200,
					'message'   => 'Data Ditemukan',
				],
				'response'  => $data
			];
			echo json_encode($response);
		}
		else
		{
			$response   = [
				'status'    => [
					'code'      => 404,
					'message'   => 'Data Tidak Ditemukan',
				],
				'response'  => ''
			];
			echo json_encode($response);
		}
	}
}