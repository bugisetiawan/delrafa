<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        // $this->form_validation->CI =&$this;
        $this->load->model('Department_model', 'department');
    }
            
    public function datatable_department()
    {
        if($this->input->is_ajax_request())
		{
            $this->datatables->select('department.id AS d_id, department.code AS d_code, department.name AS d_name, subdepartment.id AS s_id, subdepartment.code AS s_code, subdepartment.name AS s_name');
            $this->datatables->from('department');
            $this->datatables->join('subdepartment', 'subdepartment.department_code = department.code', 'left');
            $this->datatables->order_by('department.code', 'asc');
            $this->datatables->order_by('subdepartment.code', 'asc');
            $this->datatables->add_column('view','
                <div class="kt-section__content">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Katg.</button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0)" class="dropdown-item" id="update_department" data-code="$1">Edit</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item" id="delete_department" data-code="$1">Hapus</a>
                        </div>
                    </div>
                    <!-- /btn-group -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Subkatg.</button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0)" class="dropdown-item" id="update_subdepartment" data-id="$3">Edit</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" class="dropdown-item" id="delete_subdepartment" data-id="$3">Hapus</a>
                        </div>
                    </div>
                </div>
            ', 'd_code, s_code, s_id');
            header('Content-Type: application/json');
            echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}                
    }
    
    public function index()
    {
        if($this->system->check_access('department', 'A'))
		{
            $data_activity = [
				'information' => 'MELIHAT DAFTAR DEPARTEMEN DAN SUBDEPARTEMEN',
				'method'      => 1,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
            $this->crud->insert('activity', $data_activity);
            
            $header = array( "title" => "Kategori Produk");
            $footer = array("script" => ['master/department.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('department/department');        
            $this->load->view('include/footer', $footer);
		}
		else
		{
			$this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url('dashboard'));			
        }
	}

    public function detail_department()
    {
        if($this->input->is_ajax_request())
		{
            $department_code    = $this->input->get('department_code');
            $data               = $this->crud->get_by_code('department', $department_code)->row();
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

    public function detail_subdepartment()
    {
        if($this->input->is_ajax_request())
		{
            $department_code    = $this->input->get('department_code');
            $data               = $this->crud->get_by_code('department', $department_code)->row();
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
    
    public function add_process()
    {   
        if($this->system->check_access('department', 'C'))
        {
            $post   = $this->input->post();     
            $type   = $post['type'];
            if($type == 2)
            {
                $this->form_validation->set_rules('department_code', 'Departemen', 'trim|required|xss_clean');
            }        
            $this->form_validation->set_rules('type', 'Jenis', 'trim|required|xss_clean');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');

            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');

            if($this->form_validation->run() == FALSE)
            {
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Menambahkan Data',
                    ],
                    'response'  => ''
                ];
                echo json_encode($response);
                echo validation_errors();
            }
            else
            {            
                if($type == 1)
                {
                    $code_dept = $this->department->department_code();
                    $data_department    =   [
                        'code'  => $code_dept,
                        'name'  => $post['name']
                    ];
                    $insert_department  = $this->crud->insert('department', $data_department);
                    if($insert_department)
                    {
                        $data_activity = [
                            'information' => 'MEMBUAT DEPARTEMEN BARU (CODE '.$code_dept.')',
                            'method'	  => 3,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $response   =   [
                            'status'    => [
                                'code'      => 200,
                                'message'   => 'Berhasil Menambahkan Data',
                            ],
                            'response'  => ''
                        ];
                    }
                    else
                    {
                        $response   =   [
                            'status'    => [
                                'code'      => 401,
                                'message'   => 'Gagal Menambahkan Data',
                            ],
                            'response'  => ''
                        ];
                    }                    
                }
                else
                {
                    $code_subdept = $this->department->subdepartment_code($post['department_code']);
                    $data_sub    =   [
                        'code'              => $code_subdept,
                        'department_code'   => $post['department_code'],
                        'name'              => $post['name']
                    ];
                    $insert_sub  = $this->crud->insert('subdepartment', $data_sub);
                    if($insert_sub)
                    {
                        $data_activity = [
                            'information' => 'MEMBUAT SUBDEPARTEMEN BARU (DEPT '.$post['department_code'].' | CODE '.$code_subdept.')',
                            'method'	  => 3,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $response   =   [
                            'status'    => [
                                'code'      => 200,
                                'message'   => 'Berhasil Menambahkan Data',
                            ],
                            'response'  => ''
                        ];
                    }
                    else
                    {
                        $response   =   [
                            'status'    => [
                                'code'      => 401,
                                'message'   => 'Gagal Menambahkan Data',
                            ],
                            'response'  => ''
                        ];
                    }                    
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

    public function update_department_process()
    {
        if($this->system->check_access('department', 'U'))
        {
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');

            $post       = $this->input->post();
            $department_code = $post['code'];
            $cek        = $this->db
                        ->select('d.code')
                        ->from('department AS d')
                        ->join('product AS p', 'p.department_code = d.code')
                        ->where('p.department_code', $department_code)                    
                        ->where('p.deleted', 0)
                        ->get();
            if($cek->num_rows() > 0)       
            {
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Memperbaharui Kode. Karena Sudah Terdapat di Produk'
                    ],
                    'response'  => ''
                ];
                echo json_encode($response);
            }
            else
            {
                if($this->form_validation->run() == FALSE)
                {
                    echo validation_errors();
                }
                else
                {
                    $data       =   [
                        'name'      => $post['name'],
                    ];
                    $code       = $post['code'];
                    $update     = $this->crud->update_by_code('department', $data, $code);
                    if($update)
                    {
                        $data_activity = [
                            'information' => 'MEMPERBARUI DATA DEPARTEMEN (CODE '.$code.')',
                            'method'	  => 4,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $response   =   [
                            'status'    => [
                                'code'      => 200,
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

    public function update_subdepartment_process()
    {
		if($this->system->check_access('department', 'U'))
		{
			$post       = $this->input->post();
			$this->form_validation->set_rules('department_code', 'Departemen', 'trim|required|xss_clean');
			$this->form_validation->set_message('required', 'Maaf! <b>%s</b> Telah Digunakan');

			if($this->form_validation->run() == FALSE)
			{
				echo validation_errors();
			}
			else
			{
				if($post['department_code'] != $post['department_code_old'])
				{
                    $code_subdept = $this->department->subdepartment_code($post['department_code']);
					$data_insert = [
						'code'              => $code_subdept,
						'department_code'   => $post['department_code'],
						'name'              => $post['subdepartment_name']
					];
					$insert     = $this->db->insert('subdepartment', $data_insert);
					if($insert)
					{                        
                        $data_activity = [
                            'information' => 'MEMPERBARUI DATA SUBDEPARTEMEN (DEPT '.$post['department_code'].' | CODE '.$code_subdept.')',
                            'method'	  => 4,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
						$response   =   [
							'status'    => [
								'code'      => 200,
								'message'   => 'Berhasil Menambahkan Data',
							],
							'response'  => $this->db->delete('subdepartment', ['id'  => $post['subdepartment_id']])
						];
					}
					else
					{
						$response   =   [
							'status'    => [
								'code'      => 401,
								'message'   => 'Gagal Menambahkan Data',
							],
							'response'  => ''
						];
					}					
				}
				else
				{
					$data_update = [
						'name'      => $post['subdepartment_name'],
					];
					$this->db->where('id', $post['subdepartment_id']);
					$update = $this->db->update('subdepartment', $data_update);
					if($update)
					{
                        $data_activity = [
                            'information' => 'MEMPERBARUI DATA SUBDEPARTEMEN (ID SUBDEPT '.$post['subdepartment_id'].')',
                            'method'      => 4,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
						$response   =   [
							'status'    => [
								'code'      => 200,
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

    public function delete_department_process()
    {
		if($this->system->check_access('department', 'D'))
		{
			$post       = $this->input->post();
			$department_code    = $post['department_code'];
			$cek        = $this->db
						->select('d.code')
						->from('department AS d')
						->join('product AS p', 'p.department_code = d.code')
						->where('p.department_code', $department_code)
						->get();
			if($cek->num_rows() > 0)       
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Gagal Menghapus Data. Karena Sudah Terdapat di Produk'
					],
					'response'  => ''
				];				
			}
			else
			{				
				$delete     = $this->crud->delete_by_code('department', $department_code);
				if($delete)
				{
                    $data_activity = [
                        'information' => 'MENGHAPUS DATA DEPARTEMEN (CODE '.$department_code.')',
                        'method'      => 5,
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];
                    $this->crud->insert('activity', $data_activity);
					$response       = [
						'status'    => [
							'code'      => 200,
							'message'   => 'Berhasil Menghapus Data',
						],
						'response'  => '',
					];
				}
				else
				{
					$response       = [
						'status'    => [
							'code'      => 401,
							'message'   => 'Gagal Menghapus Data',
						],
						'response'  => '',
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

    public function check_code_department()
    {
        $department_code    = $this->input->get('department_code');
        $cek_depart         = $this->db->select('id, code')
                                        ->from('department')
                                        ->where('code', $department_code)
                                        ->get();
        if($cek_depart->num_rows() > 0)
        {
            $data_depart    = $cek_depart->row_array();
            $cek_product    = $this->db
                                ->select('id, code, department_code')
                                ->from('product')
								->where('department_code', $data_depart['code'])
								->where('deleted', 0)
                                ->get();
            if($cek_product->num_rows() > 0)
            {
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Memperbaharui/Menghapus Data. Karena Sudah Terdapat di Produk'
                    ],
                    'response'  => ''
                ];
            }
            else
            {
                $response   =   [
                    'status'    => [
                        'code'      => 200,
                        'message'   => 'Data Boleh Dihapus'
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
                    'message'   => 'Gagal Memperbaharui/Menghapus. Data Tidak Ditemukan'
                ],
                'response'  => ''
            ];
        }
        echo json_encode($response);
    }

    public function check_id_subdepartment()
    {
        $subdepartment_id     = $this->input->get('subdepartment_id');
        $cek_sub    = $this->db
                            ->select('id, department_code, code, name')
                            ->from('subdepartment')
                            ->where('id', $subdepartment_id)
                            ->get();
        if($cek_sub->num_rows() > 0)
        {
            $data_sub = $cek_sub->row_array();
            $cek_product = $this->db
                                ->select('p.id, p.code, p.department_code, p.subdepartment_code')
                                ->from('product AS p')
                                ->where('p.department_code', $data_sub['department_code'])
								->where('p.subdepartment_code', $data_sub['code'])
								->where('deleted', 0)
                                ->get();
            if($cek_product->num_rows() > 0)
            {
                $response   =   [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Gagal Memperbaharui/Menghapus Data. Karena Sudah Terdapat di Produk'
                    ],
                    'response'  => ''
                ];
            }
            else
            {
                $response   =   [
                    'status'    => [
                        'code'      => 200,
                        'message'   => 'Data Boleh Dihapus'
                    ],
                    'response'  => $data_sub
                ];
            }
        }
        else
        {
            $response   =   [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Gagal Memperbaharui/Menghapus. Data Tidak Ditemukan'
                ],
                'response'  => ''
            ];
        }
        echo json_encode($response);
    }
    
    public function delete_subdepartment_process()
    {
		if($this->system->check_access('department', 'D'))
		{
			$subdepartment_id   = $this->input->post('subdepartment_id');
			$delete     = $this->crud->delete_by_id('subdepartment', $subdepartment_id);
			if($delete)
			{
                $data_activity = [
                    'information' => 'MENGHAPUS DATA SUBDEPARTEMEN (ID SUBDEPT '.$subdepartment_id.')',
                    'method'	  => 5,
                    'code_e'      => $this->session->userdata('code_e'),
                    'name_e'      => $this->session->userdata('name_e'),
                    'user_id'     => $this->session->userdata('id_u')
                ];
                $this->crud->insert('activity', $data_activity);
				$response       = [
					'status'    => [
						'code'      => 200,
						'message'   => 'Berhasil Menghapus Data',
					],
					'response'  => '',
				];
			}
			else
			{
				$response       = [
					'status'    => [
						'code'      => 401,
						'message'   => 'Gagal Menghapus Data',
					],
					'response'  => '',
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
        
    public function get_department()
    {
        $data       = $this->crud->get('department')->result();
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

    public function get_sub()
    {
        $code       = $this->input->get('code_depart');
        $data       = $this->db->get_where('subdepartment', ['department_code'  =>  $code])->result();
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
