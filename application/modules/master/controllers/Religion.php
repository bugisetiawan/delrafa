<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Religion extends System_Controller
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;	
        $this->load->model('Crud_model', 'crud');
    }    

    private $table = 'religion';

    public function index()
    {
        $header = array("title" => "Agama");
        $footer = array("script" => ['master/religion.js']);
        $this->load->view('include/header', $header);        
        $this->load->view('include/menubar');        
        $this->load->view('include/topbar');        
        $this->load->view('religion/religion');        
        $this->load->view('include/footer', $footer);
    }
      	
	public function add()
    {
        if($this->system->check_access('create'))
        {
            $post   = $this->input->post();        
            $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');

            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
            $this->form_validation->set_message('is_unique', 'Maaf! <b>%s</b> Telah Digunakan');

            if($this->form_validation->run() == FALSE)
            {
                echo validation_errors();
            }
            else
            {
                $data = [                
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
        if($this->system->check_access('update'))
        {
            $post = $this->input->post();
            $this->form_validation->set_rules('editId', 'ID', 'trim|required|xss_clean');        
            $this->form_validation->set_rules('editName', 'Nama', 'trim|required|xss_clean');

            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');

            if($this->form_validation->run() == FALSE)
            {
                echo validation_errors();
            }
            else
            {
                $data	= ['name' => $post['editName'] ];
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
        if($this->system->check_access('delete'))
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
    
    public function get_religion()
    {
        $data       = $this->crud->get('religion')->result();
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
