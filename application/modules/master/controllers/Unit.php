<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit extends System_Controller
{	
    public function __construct()
    {
        parent::__construct();
    }
    	
    private $table = 'unit';    
    
    public function index()
    {
        if($this->system->check_access('unit', 'A'))
		{
            $data_activity = [
                'information' => 'MELIHAT DAFTAR SATUAN',
                'method'      => 1, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                'code_e'      => $this->session->userdata('code_e'),
                'name_e'      => $this->session->userdata('name_e'),
                'user_id'     => $this->session->userdata('id_u')
            ];						
            $this->crud->insert('activity', $data_activity);

            $header = array("title" => "Satuan");
            $footer = array("script" => ['master/unit.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('unit/unit');        
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
        $code   = (isset($post['code'])) ? $post['code'] : $post['editCode'];        
        return ($this->crud->get_where('unit', ['code' => $code, 'deleted' => 0])->num_rows() == 1) ? FALSE : TRUE;
    }
    
	public function add()
    {
        if($this->system->check_access('unit', 'C'))
        {
            $post   = $this->input->post();
            $this->form_validation->set_rules('code', 'Kode', 'trim|required|callback_check_code|xss_clean');
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
                if($this->crud->insert('unit', $data))
                {       
                    $data_activity = [
                        'information' => 'MEMBUAT SATUAN BARU (CODE: '.$post['code'].' | NAMA: '.$post['name'].')',
                        'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];						
                    $this->crud->insert('activity', $data_activity);

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

    public function check_unit()
    {
        $id = $this->input->get('id');
        $check_unit = $this->db->select('unit_id')->where('unit_id', $id)->from('product_unit')->get();
        if($check_unit->num_rows() == 0 )
        {   
            $response = [
                'status'	=> [
                    'code'  	=> 200,
                    'message'   => 'Data diperbolehkan untuk di edit/hapus',
                ],
                'response'  => ''
            ];            
        }
        else
        {
            $response = [
                'status'	=> [
                    'code'  	=> 401,
                    'message'   => 'Gagal Memperbarui/Menghapus Data. Karena Sudah Terdapat di Produk',
                ],
                'response'  => ''
            ];            
        }             
        echo json_encode($response);   
    }

    public function update()
    {
		if($this->system->check_access('unit', 'U'))
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
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Memperbarui Data',
                    ],
                    'response'  => ''
                ];
			}
			else
			{
                $id     = $post['editId']; 
                $data   = [
                    'code' => $post['editCode'], 
                    'name' => $post['editName'] 
                ];				
				if($this->crud->update_by_id($this->table, $data, $id))
				{
                    $data_activity = [
                        'information' => 'MEMPERBARUI SATUAN (ID: '.$id.')',
                        'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];						
                    $this->crud->insert('activity', $data_activity);
					$response = [
						'status'	=> [
							'code'  	=> 200,
							'message'   => 'Berhasil Memperbarui Data',
						],
						'response'  => ''
					];				
				}
				else
				{
					$response   =   [
						'status'    => [
							'code'      => 401,
							'message'   => 'Gagal Memperbarui Data',
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
		if($this->system->check_access('unit', 'D'))
		{
			$id = $this->input->get('id');		
			if($this->crud->update_by_id('unit', ['deleted' => 1], $id))
			{
                $data_activity = [
                    'information' => 'MENGHAPUS SATUAN (ID: '.$id.')',
                    'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
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
        
    public function get_unit()
    {        
        $data = $this->crud->get_where('unit', ['deleted' => '0'])->result();
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
}