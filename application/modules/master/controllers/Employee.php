<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee_model', 'employee');
    }

    private $table = "employee";

    // READ
    public function datatable()
    {
        header('Content-Type: application/json');
        $this->datatables->select('employee.id, employee.code, employee.name, position.code AS position, employee.status');
        $this->datatables->from('employee');
        $this->datatables->join('position', 'position.id = employee.position_id');        
        $this->datatables->where('is_user !=', 1);
        $this->datatables->where('employee.deleted', 0);
        $this->datatables->add_column('code', 
        '
            <a class="kt-font-primary kt-link text-center" href="'.base_url('employee/detail/$1').'"><b>$2</b></a>
        ', 'encrypt_custom(code),code');
        echo $this->datatables->generate();
    }  

    public function index()
    { 
        if($this->system->check_access('employee', 'A'))
		{
            $data_activity = [
				'information' => 'MELIHAT DAFTAR PEGAWAI',
				'method'      => 1,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
            ];            
            $this->crud->insert('activity', $data_activity);
            
            $header = ['title' => "Pegawai"];
            $footer = array("script" => ['master/employee/employee.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar'); 
            $this->load->view('employee/employee');       
            $this->load->view('include/footer', $footer);
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
        if($this->system->check_access('employee', 'C'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('nik', 'Nomor Induk Kependudukan', 'min_length[16]|max_length[16]|trim|required|xss_clean');
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('gender', 'Jenis Kelamin', 'trim|required|xss_clean');
                $this->form_validation->set_rules('religion', 'Agama', 'trim|xss_clean');
                $this->form_validation->set_rules('education', 'Pendidikan Terakhir', 'trim|xss_clean');
                $this->form_validation->set_rules('address', 'Alamat', 'trim|xss_clean');
                $this->form_validation->set_rules('province', 'Provinsi', 'trim|xss_clean');
                $this->form_validation->set_rules('city', 'Kota', 'trim|xss_clean');
                $this->form_validation->set_rules('phone', 'No Handphone', 'min_length[10]|max_length[13]|trim|required|xss_clean');
                $this->form_validation->set_rules('telephone', 'No Telpon', 'max_length[13]|trim|xss_clean');
                $this->form_validation->set_rules('born', 'Tempat Lahir', 'trim|xss_clean');
                $this->form_validation->set_rules('birthday', 'Tanggal Lahir', 'trim|xss_clean');        
                $this->form_validation->set_rules('married', 'Status Perkawinan', 'trim|required|xss_clean');
                $this->form_validation->set_rules('npwp', 'NPWP', 'trim|xss_clean');
                $this->form_validation->set_rules('bpjs', 'BPJS Kesehatan', 'trim|xss_clean');
                $this->form_validation->set_rules('bpjsk', 'BPJS Ketenagakerjaan', 'trim|xss_clean');
                $this->form_validation->set_rules('position', 'Jabatan Pegawai', 'trim|required|xss_clean');
                $this->form_validation->set_rules('join', 'Tanggal Masuk', 'trim|required|xss_clean');
                $this->form_validation->set_rules('out', 'Tanggal Keluar', 'trim|xss_clean');

                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');        
                $this->form_validation->set_message('min_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('max_length', 'Maaf! <b>%s</b> Maksimal <b>%s</b> Karakter');
            
                if($this->form_validation->run() == FALSE)
                {
                    $header = ['title'         => 'Pegawai Baru'];
                    $footer = array("script" => ['master/employee/crud_employee.js']);
                    $this->load->view('include/header', $header);        
                    $this->load->view('include/menubar');        
                    $this->load->view('include/topbar');        
                    $this->load->view('employee/add_employee');        
                    $this->load->view('include/footer', $footer);
                }
                else
                {
                    $post = $this->input->post();
                    $code = $this->employee->custom_increment();
                    if(!empty($_FILES['photo']['name']))
                    {
                        $config['upload_path']   = "./assets/media/system/employee";
                        $config['allowed_types'] = "jpg|png|jpeg";
                        $config['max_size']      = "10240";
                        $config['remove_space']  = TRUE;
                        $config['file_name']     = $code; 
                        $config['overwrite']     = TRUE; 
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if($this->upload->do_upload('photo')) 
                        {
                            $this->load->library('image_lib');
                            $upload = array('upload_data' => $this->upload->data());
                            $resize=$this->upload->data();

                            $configer =  array(
                                'image_library'   => 'gd2',
                                'source_image'    => $resize['full_path'],
                                'create_thumb' 	  => FALSE,
                                'maintain_ratio'  => FALSE,
                                'width'           => 118,
                                'height'          => 472,
                                );
                            $this->image_lib->clear();
                            $this->image_lib->initialize($configer);
                                            
                            if($this->image_lib->resize())
                            {    
                                $photo = $this->upload->data('file_name');
                            }
                            else
                            {
                                $this->session->set_flashdata('error', 'Sorry, failed to resize photo');                        
                            }
                        } 
                        else
                        {
                            $this->session->set_flashdata('error', 'Sorry, failed to upload photo'.$this->upload->display_errors());                    
                        }
                    }
                    else
                    {  
                        $photo = null;
                    }  
                    $data = [                
                        'code'          => $code,
                        'photo'         => $photo,
                        'nik'           => $post['nik'],
                        'name'          => $post['name'],
                        'gender'        => $post['gender'],
                        'religion_id'   => $post['religion'],
                        'education_id'  => $post['education'],
                        'address'       => $post['address'],
                        'province_id'   => $post['province'],
                        'city_id'       => $post['city'],
                        'phone'         => $post['phone'],
                        'telephone'     => $post['telephone'],
                        'born_id'       => $post['born'],
                        'birthday'      => $post['birthday'],                
                        'married'       => $post['married'],
                        'npwp'          => $post['npwp'],
                        'bpjs'          => $post['bpjs'],
                        'bpjsk'         => $post['bpjsk'],
                        'position_id'   => $post['position'],
                        'join'          => $post['join'],
                        'out'           => $post['out'],
                        'status'        => $post['status'],
                    ];                                  
                    if($this->crud->insert('employee', $data))
                    {
                        $data_activity = [
                            'information' => 'MEMBUAT PEGAWAI BARU (CODE '.$code.')',
                            'method'      => 3,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $this->session->set_flashdata('success', 'Data Pegawai berhasil ditambahkan');                            
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, Data Pegawai gagal ditambahkan');                            
                    }  
                    redirect(site_url('employee'));    			           
                }                
            }
            else
            {
                $header = ['title' => 'Pegawai Baru'];
                $footer = array("script" => ['master/employee/crud_employee.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');
                $this->load->view('employee/add_employee');
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
        if($this->system->check_access('employee', 'R'))
        {
            $code = $this->global->decrypt($code);

            $data_activity = [
				'information' => 'MELIHAT DETAIL PEGAWAI (CODE '.$code.')',
				'method'      => 2,
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];
			$this->crud->insert('activity', $data_activity);

            $header = array("title" => 'Detail Pegawai');   
            $info = $this->crud->get_by_code($this->table, $code)->row_array(); 
            $province = $this->employee->get_province($info['province_id']);
            $city = $this->employee->get_city($info['province_id'], $info['city_id']);
            $religion = $this->employee->get_religion($info['religion_id']);
            $education = $this->employee->get_education($info['education_id']);   
            $born = $this->employee->get_born($info['born_id']);
            $position = $this->employee->get_position($info['position_id']);
            $data = array('info' => $info, 'province' => $province,'city' => $city, 'religion' => $religion, 'education' => $education, 'born' => $born, 'position' => $position);            
            $footer = array("script" => ['master/employee/detail_employee.js']);
            
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('employee/detail_employee', $data);        
            $this->load->view('include/footer', $footer);

        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url($this->table));
        }        
    }

    // UPDATE
    public function update($code)
    {
        if($this->system->check_access('employee', 'U'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('nik', 'Nomor Induk Kependudukan', 'min_length[16]|max_length[16]|trim|required|xss_clean');
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('gender', 'Jenis Kelamin', 'trim|required|xss_clean');
                $this->form_validation->set_rules('religion', 'Agama', 'trim|xss_clean');
                $this->form_validation->set_rules('education', 'Pendidikan Terakhir', 'trim|xss_clean');
                $this->form_validation->set_rules('address', 'Alamat', 'trim|xss_clean');
                $this->form_validation->set_rules('province', 'Provinsi', 'trim|xss_clean');
                $this->form_validation->set_rules('city', 'Kota', 'trim|xss_clean');
                $this->form_validation->set_rules('phone', 'No Handphone', 'min_length[10]|max_length[13]|trim|required|xss_clean');
                $this->form_validation->set_rules('telephone', 'No Telpon', 'max_length[13]|trim|xss_clean');
                $this->form_validation->set_rules('born', 'Tempat Lahir', 'trim|xss_clean');
                $this->form_validation->set_rules('birthday', 'Tanggal Lahir', 'trim|xss_clean');        
                $this->form_validation->set_rules('married', 'Status Perkawinan', 'trim|required|xss_clean');
                $this->form_validation->set_rules('npwp', 'NPWP', 'trim|xss_clean');
                $this->form_validation->set_rules('bpjs', 'BPJS Kesehatan', 'trim|xss_clean');
                $this->form_validation->set_rules('bpjsk', 'BPJS Ketenagakerjaan', 'trim|xss_clean');
                $this->form_validation->set_rules('position', 'Jabatan Pegawai', 'trim|required|xss_clean');
                $this->form_validation->set_rules('join', 'Tanggal Masuk', 'trim|required|xss_clean');
                $this->form_validation->set_rules('out', 'Tanggal Keluar', 'trim|xss_clean');

                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('is_unique', 'Maaf! <b>%s</b> Telah Digunakan');
                $this->form_validation->set_message('min_length', 'Maaf! <b>%s</b> Minimal <b>%s</b> Karakter');
                $this->form_validation->set_message('max_length', 'Maaf! <b>%s</b> Maksimal <b>%s</b> Karakter');

                $post    = $this->input->post(); 
                $code    = $this->input->post('code');
                if($this->form_validation->run() == FALSE)
                {
                    $header = array("title" => 'Perbarui Pegawai');
                    $info = $this->crud->get_by_code($this->table, $code)->row_array();
                    $data = array('info' => $info);
                    $footer = array("script" => ['master/employee/crud_employee.js']);
                    $this->load->view('include/header', $header);        
                    $this->load->view('include/menubar');        
                    $this->load->view('include/topbar');        
                    $this->load->view('employee/update_employee', $data);        
                    $this->load->view('include/footer', $footer);
                }
                else
                {
                    $config['upload_path']   = "./assets/media/system/employee/";
                    $config['allowed_types'] = "jpg|png|jpeg";
                    $config['max_size']      = "10240";
                    $config['remove_space']  = TRUE;
                    $config['file_name']     = $code; 
                    $config['overwrite']     = TRUE; 
                    $this->load->library('upload', $config);

                    if(!empty($_FILES['photo']['name']))
                    {
                        if ($this->upload->do_upload('photo')) 
                        {   
                            $this->load->library('image_lib');
                            $upload = array('upload_data' => $this->upload->data());
                            $resize=$this->upload->data();

                            $configer =  array(
                                'image_library'   => 'gd2',
                                'source_image'    => $resize['full_path'],
                                'create_thumb' 	  => FALSE,
                                'maintain_ratio'  => FALSE,
                                'width'           => 118,
                                'height'          => 472,
                                );
                            $this->image_lib->clear();
                            $this->image_lib->initialize($configer);
                                            
                            if($this->image_lib->resize())
                            {    
                                $photo = $this->upload->data('file_name');
                            }
                            else
                            {
                                $this->session->set_flashdata('error', 'Sorry, failed to resize photo');                        
                            }                            
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'Sorry, failed to upload photo');                    
                        }
                    }
                    else
                    {
                        $photo = $post['photo_old'];
                    }
                    $data = [                
                        'photo'      	=> $photo,
                        'nik'           => $post['nik'],
                        'name'          => $post['name'],
                        'gender'        => $post['gender'],
                        'religion_id'   => $post['religion'],
                        'education_id'  => $post['education'],
                        'address'       => $post['address'],
                        'province_id'   => $post['province'],
                        'city_id'       => $post['city'],
                        'phone'         => $post['phone'],
                        'telephone'     => $post['telephone'],
                        'born_id'       => $post['born'],
                        'birthday'      => $post['birthday'],                
                        'married'       => $post['married'],
                        'npwp'          => $post['npwp'],
                        'bpjs'          => $post['bpjs'],
                        'bpjsk'         => $post['bpjsk'],
                        'position_id'   => $post['position'],
                        'join'          => $post['join'],
                        'out'           => $post['out'],
                        'status'        => $post['status'],
                    ];
                    if($this->crud->update_by_code($this->table, $data, $code))
                    {
                        $data_activity = [
                            'information' => 'MEMPERBARUI DATA PEGAWAI (CODE '.$code.')',
                            'method'      => 4,
                            'code_e'      => $this->session->userdata('code_e'),
                            'name_e'      => $this->session->userdata('name_e'),
                            'user_id'     => $this->session->userdata('id_u')
                        ];
                        $this->crud->insert('activity', $data_activity);
                        $this->session->set_flashdata('success', 'Data berhasil diubah');                        
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, Data gagal diubah');                        
                    }
                    redirect(site_url('employee'));
                }                                                
            }
            else
            {
                $code = $this->global->decrypt($code);
                $header = array("title" => 'Perbarui Pegawai');
                $info = $this->crud->get_by_code($this->table, $code)->row_array();
                $data = array('info' => $info);
                $footer = array("script" => ['master/employee/crud_employee.js']);                                        
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('employee/update_employee', $data);        
                $this->load->view('include/footer', $footer);                
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
			redirect(base_url($this->table));
        }        
    }    
    
    // RESIGN
    public function resign()
    {
        if($this->system->check_access('employee', 'U'))
        {
            $employee_code     = $this->input->post('employee_code');
            $data = array(
                'out' => date('Y-m-d'),
				'status' => 0
			);			
            if($this->crud->update_by_code($this->table, $data, $employee_code))
            {
                $this->crud->delete('user', ['employee_code' => $employee_code]);                
                $data_activity = [
                    'information' => 'MERESIGNKAN Pegawai (CODE '.$employee_code.')',
                    'method'      => 4,
                    'code_e'      => $this->session->userdata('code_e'),
                    'name_e'      => $this->session->userdata('name_e'),
                    'user_id'     => $this->session->userdata('id_u')
                ];
                $this->crud->insert('activity', $data_activity);
                $response = [
                    'status'	=> [
                        'code'  	=> 200,
                        'message'   => 'Pegawai Berhasil Resign',
                    ],
                    'response'  => ''
                ];
                $this->session->set_flashdata('success', 'Pegawai Berhasil Resign');                
            }
            else
            {
                $response   = [
                    'status'    => [
                        'code'      => 401,
                        'message'   => 'Pegawai Gagal Resign',
                    ],
                    'response'  => '',
                ];                
            }            
        }
        else
        {
            $response   = [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
                ],
                'response'  => '',
            ];                     			
        }     
        echo json_encode($response);
    }

    // DELETE
    public function delete()
    {
        if($this->system->check_access('employee', 'D'))
        {
            $code     = $this->input->get('id');
            $data = array(
				'deleted' => 1
			);
			$delete = $this->crud->update_by_code($this->table, $data, $code);
            if($delete)
            {
                // $path	= FCPATH.'./assets/media/system/employee/'.$data['photo'];
                // if(file_exists($path))
                // {
                //     unlink($path);
                //     $response	= [
                //         'status' => [
                //             'code'	=> 200,
                //             'message' => 'Berhasil Menghapus Data',
                //         ],
                //         'response' => $this->crud->delete('employee', ['code' => $code])
                //     ];
                // }
                $data_activity = [
                    'information' => 'MENGHAPUS DATA Pegawai (CODE '.$code.')',
                    'method'	  => 5,
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
                $response   = [
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
            $response   = [
                'status'    => [
                    'code'      => 401,
                    'message'   => 'Mohon maaf, anda tidak memiliki akses. Terima kasih',
                ],
                'response'  => '',
            ];                     			
        }     
        echo json_encode($response);
    }
    
    public function get_employee()
    {
		if($this->input->is_ajax_request())
		{
			$where = array(
                'id > '   => 3,
                'is_user' => 0,
				'deleted' => 0
			);
			$data       = $this->crud->get_where('employee', $where)->result();
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
}
