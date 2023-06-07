<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;
	}
	
	private $table = 'warehouse';
    
    public function index()
    {
        if($this->system->check_access('warehouse', 'A'))
		{
            $header = array("title" => "Gudang");          
            $footer = array("script" => ['master/warehouse.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('warehouse/warehouse');
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
        if($this->system->check_access('warehouse', 'C'))
        {
            $post   = $this->input->post();
            $this->form_validation->set_rules('code', 'Kode', 'trim|required|is_unique[warehouse.code]|xss_clean');
            $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');

            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
            $this->form_validation->set_message('is_unique', 'Maaf! <b>%s</b> Telah Digunakan');
            $this->form_validation->set_message('check_code', 'Maaf! Kode Telah Digunakan');

            if($this->form_validation->run() == FALSE)
            {
                $response = [
                    'status' => [
                        'code'      => 401,
                        'message'   => validation_errors(),
                    ],
                    'response'  => ''
                ];                
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
                    $data_activity = array (
                        'information' => 'MENAMBAH DATA GUDANG',
                        'method'	  => 3,
                        'user_id' 	  => $this->session->userdata('id_u')
                    );
                    $this->crud->insert('activity',$data_activity);
                    $response = [
                        'status' => [
                            'code'      => 200,
                            'message'   => 'Berhasil Menambahkan Data',
                        ],
                        'response'  => ''
                    ];                    
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
        }        
        echo json_encode($response);
    }    	  

    public function update()
    {
        if($this->system->check_access('warehouse', 'U'))
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
                    $data_activity = array (
                        'information' => 'MEMPERBAHARUI DATA GUDANG ( ID - '.$id.')',
                        'method'	  => 4,
                        'user_id' 	  => $this->session->userdata('id_u')
                    );
                    $this->crud->insert('activity',$data_activity);
                    $response = [
                        'status'	=> [
                            'code'  	=> 200,
                            'message'   => 'Berhasil Memperbaharui Data',
                        ],
                        'response'  => ''
                    ];                    
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
        }        
        echo json_encode($response);
    }
    	
    public function delete()
    {        
		if($this->system->check_access('warehouse', 'D'))
		{
			$id     = $this->input->get('id');
			$data = array(
				'deleted' => 1
			);
			$delete = $this->crud->update_by_id($this->table, $data, $id);
			if($delete)
			{
                $data_activity = array (
                    'information' => 'MENGHAPUS DATA GUDANG ( ID - '.$id.')',
                    'method'	  => 5,
                    'user_id' 	  => $this->session->userdata('id_u')
                );
                $this->crud->insert('activity',$data_activity);
				$response = [
					'status'	=> [
						'code'  	=> 200,
						'message'   => 'Berhasil Menghapus Data',
					],
					'response'  => ''
				];				
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

    public function get_warehouse()
    {
        if($this->input->is_ajax_request())
		{
            $data       = $this->crud->get_where($this->table, ['deleted' => '0'])->result();
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
		else
		{
			$this->load->view('auth/show_404');
		}        
    }
    
    public function default()
    {   
        if($this->system->check_access('warehouse', 'C'))
        {
            $where = array('default' => 1);     
            $data = array('default' => 0);
            $clear_default = $this->crud->update($this->table, $data, $where);
            if($clear_default)
            {
                $new_data = array(
                    'default' => 1
                );
                $id     = $this->input->post('warehouse_id');        
                $default = $this->crud->update_by_id($this->table, $new_data, $id);
                if($default)
                {
                    $data_activity = array (
                        'information' => 'MEMPERBAHARUI DATA GUDANG DEFAULT ( ID - '.$id.')',
                        'method'	  => 4,
                        'user_id' 	  => $this->session->userdata('id_u')
                    );
                    $this->crud->insert('activity',$data_activity);
                    $response = [
                        'status'	=> [
                            'code'  	=> 200,
                            'message'   => 'Berhasil Memperbaharui Data Gudang Default',
                        ],
                        'response'  => ''
                    ];                    
                }
                else
                {
                    $response   =   [
                        'status'    => [
                            'code'      => 401,
                            'message'   => 'Gagal Memperbaharui Data Gudang Default',
                        ],
                        'response'  => ''
                    ];                    
                }            
            }
            else
            {
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Memperbaharui Data Gudang Default',
                    ],
                    'response'  => ''
                ];                
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