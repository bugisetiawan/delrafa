<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
		$this->load->model('User_model', 'user');
    }        

    public function index()
    {
        if($this->system->check_access('setting/user', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('user.id AS id_u, user.code AS code_u, employee.code AS code_e, employee.name AS name_e, user.active');
                $this->datatables->from('user');
                $this->datatables->join('employee', 'employee.code = user.employee_code');
                if($this->session->userdata('id_u') <=3)
                {
                    $this->datatables->where('user.id >', $this->session->userdata('id_u'));
                }
                else
                {
                    $this->datatables->where('user.id >', 3);
                }                
                $this->datatables->where('user.deleted', 0);        
                $this->datatables->add_column('code_u', 
                '       
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('setting/user/detail/$1').'"
                    data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
                ', "encrypt_custom(id_u), code_u");
                header('Content-Type: application/json');
                echo $this->datatables->generate();                
            }
            else
            {
                $header = array("title" => "Daftar User");                                                          
                $footer = array("script" => ['setting/user/user.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');        
                $this->load->view('user/user');        
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
	}
	
	public function get_employee()
	{
        if($this->input->is_ajax_request())
		{
            $data       = $this->user->get_employee();
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

    public function check_user($type)
    {
        if($this->input->is_ajax_request())
		{
            if($type == "code")
            {
                $where = array(
                    'code' => strtoupper($this->input->post('code')),
                );
                $data = $this->crud->get_where('user', $where);
            }
            elseif($type == "name")
            {
                $where = array(
                    'name' => strtoupper($this->input->post('name')),
                    'is_user' => 1,
                );
                $data = $this->crud->get_where('employee', $where);
            }			
            if($data->num_rows() > 0)
            {
                $response = array(
                    'result' => 1
                );
            }
            else
            {
                $response = array(
                    'result' => 0
                );
    
            }
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    public function create()
    {
        if($this->system->check_access('setting/user', 'create'))
        {
            if($this->input->method() === 'post')
            {
                $post = $this->input->post();
                // echo json_encode($post); die;
                $this->form_validation->set_rules('code', 'Kode', 'trim|required|xss_clean');
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                if($this->form_validation->run() == FALSE)
                {            
                    $header = array(
                        "title" => "User Baru"
                    );
                    $data = array(
                        'master'    => $this->user->category_master(),
                        'purchase'  => $this->user->category_purchase(),
                        'sales'     => $this->user->category_sales(),
                        'inventory' => $this->user->category_inventory(),
                        'finance'   => $this->user->category_finance(),
                        'finance'   => $this->user->category_finance(),
                        'report'    => $this->user->category_report(),
                        'other'     => $this->user->category_other()
                    );
                    $this->load->view('include/header', $header);        
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');        
                    $this->load->view('user/add_user', $data);        
                    $this->load->view('include/footer');
                }
                else
                {
                    $employee_code = $this->user->user_code();
                    $data_employee  = [
                        'code' => $employee_code,
                        'name' => strtoupper($post['name']),
                        'phone' => "000000000000",
                        'position_id' => 4,
                        'status' => 1,
                        'is_user' => 1
                    ]; 
                    $employee_id = $this->crud->insert_id('employee', $data_employee);

                    $data_user  = [                
                        'employee_code' => $employee_code,
                        'code'          => strtoupper($post['code']),
                        'password'      => password_hash($post['password'], PASSWORD_BCRYPT),
                        'start_time'    => $post['start_time'],
                        'end_time'      => $post['end_time'],
                        'active'       	=> 1,
                    ]; 
                    $user_id = $this->crud->insert_id('user', $data_user);
                    if($user_id)
                    {
                        foreach($post['module-id'] AS $info_module_id)
                        {               
                            $module = $this->crud->get_where('module', ['id' => $info_module_id])->row_array();
                            $master_method = ["A", "C", "R", "U", "D"];
                            $user_method = [];
                            foreach($master_method AS $info_master_method)
                            {
                                if(isset($post[$info_master_method.'-'.$info_module_id]) && $post[$info_master_method.'-'.$info_module_id] == 1)
                                {
                                    $user_method[] = $info_master_method;                                    
                                }
                            }
                            $data_access = array(
                                    'user_id'   => $user_id,
                                    'module_url'=> $module['url'],
                                    'method'    => json_encode($user_method)
                                );
                            $this->crud->insert('access', $data_access); 
                        }
                        $this->session->set_flashdata('success', 'User berhasil ditambahkan');                        
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Mohon maaf, User gagal ditambahkan');                        
                    }
                    redirect(site_url('setting'));
                }
            }
            else
            {
                $header = array("title" => "User Baru");
                $data = array(
                    'master'    => $this->user->category_master(),
                    'purchase'  => $this->user->category_purchase(),
                    'sales'     => $this->user->category_sales(),
                    'inventory' => $this->user->category_inventory(),
                    'finance'   => $this->user->category_finance(),
                    'accounting'=> $this->user->category_accounting(),
                    'report'    => $this->user->category_report(),
                    'other'     => $this->user->category_other()
                );
                $footer = array("script" => ['setting/user/create_user.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');        
                $this->load->view('user/create_user', $data);        
                $this->load->view('include/footer', $footer);                
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }    	

    public function detail($user_id)
    {
        if($this->system->check_access('setting/user', 'detail'))
        {
            $user_id = decrypt_custom($user_id);
            $header = array(
                "title" => "Detail User"
            );                                 
            $data = array(
                'user' => $this->user->get_detail($user_id),            
                'master' => $this->user->category_master($user_id),
                'purchase' => $this->user->category_purchase($user_id),
                'sales' => $this->user->category_sales($user_id),
                'inventory'   => $this->user->category_inventory($user_id),
                'finance'     => $this->user->category_finance($user_id),
                'accounting'  => $this->user->category_accounting($user_id),
                'report'      => $this->user->category_report($user_id),
                'other'       => $this->user->category_other($user_id)
            );             
            $footer = array("script" => ['setting/user/detail_user.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');        
            $this->load->view('user/detail_user', $data);        
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }
        
    public function update($user_id)
    {
        if($this->system->check_access('setting/user', 'update'))
        {
            if($this->input->method() === 'post')
            {
                $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean');        
                $post    = $this->input->post();  
                $user_id = decrypt_custom($post['id_u']);
                if($this->form_validation->run() == FALSE)
                {
                    $user_id = decrypt_custom($user_id);
                    $header = array(
                        "title" => "Perbarui User"
                    );                                 
                    $data = array(
                        'user' => $this->user->get_detail($user_id),            
                        'master' => $this->user->category_master($user_id),
                        'purchase' => $this->user->category_purchase($user_id),
                        'sales' => $this->user->category_sales($user_id),
                        'inventory'   => $this->user->category_inventory($user_id),
                        'finance'     => $this->user->category_finance($user_id),
                        'accounting'  => $this->user->category_accounting($user_id),
                        'report'      => $this->user->category_report($user_id),
                        'other'       => $this->user->category_other($user_id)
                    );
                    $footer = array("script" => ['setting/user/crud_user.js']);
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('user/update_user', $data);
                    $this->load->view('include/footer', $footer);
                }
                else
                {   
                    $this->crud->delete('access', ['user_id' => $user_id]);
                    foreach($post['module-id'] AS $info_module_id)
                    {                
                        $module = $this->crud->get_where('module', ['id' => $info_module_id])->row_array();
                        $master_method = ["A", "C", "R", "U", "D"];
                        $user_method = [];
                        foreach($master_method AS $info_master_method)
                        {
                            if(isset($post[$info_master_method.'-'.$info_module_id]) && $post[$info_master_method.'-'.$info_module_id] == 1)
                            {
                                $user_method[] = $info_master_method;                                    
                            }
                        }
                        $data_access = array(
                            'user_id'   => $user_id,
                            'module_url'=> $module['url'],
                            'method'    => json_encode($user_method)
                        );
                        $this->crud->insert('access', $data_access);                         
                    }      
                    $this->crud->update_by_id('user', ['start_time' => $post['start_time'], 'end_time' => $post['end_time']], $user_id);
                    if($post['password'] !="")
                    {
                        $data_user  = [ 'password'      => password_hash($post['password'], PASSWORD_BCRYPT)]; 
                        $this->crud->update_by_id('user', $data_user, $user_id);
                    }
                    $this->session->set_flashdata('success', 'User berhasil diperbarui');
                    redirect(site_url('setting'));
                } 
            }
            else
            {
                $user_id = decrypt_custom($user_id);
                $header = ["title" => "Perbarui User"];
                $data = array(
                    'user' => $this->user->get_detail($user_id),            
                    'master' => $this->user->category_master($user_id),
                    'purchase' => $this->user->category_purchase($user_id),
                    'sales' => $this->user->category_sales($user_id),
                    'inventory'   => $this->user->category_inventory($user_id),
                    'finance'     => $this->user->category_finance($user_id),
                    'accounting'  => $this->user->category_accounting($user_id),
                    'report'      => $this->user->category_report($user_id),
                    'other'       => $this->user->category_other($user_id)
                );
                $footer = array("script" => ['setting/user/crud_user.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');        
                $this->load->view('user/update_user', $data);
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }                             
    }
    
    public function delete()
    {        
        $id     = decrypt_custom($this->input->get('id'));
        $delete_access = $this->crud->delete('access', ['user_id' => $id]);
        if($delete_access)
        {           
            $delete =$this->crud->delete('user', ['id' => $id]);
            if($delete)
            {
                $response = [
                    'status'	=> [
                        'code'  	=> 200,
                        'message'   => 'Berhasil Menghapus Data',
                    ],
                    'response'  => ''
                ];
                $this->session->set_flashdata('success', 'Data berhasil dihapus');
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
                $this->session->set_flashdata('error', 'Mohon maaf, Data gagal dihapus');
                echo json_encode($response);
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
            $this->session->set_flashdata('error', 'Mohon maaf, Data gagal dihapus');
            echo json_encode($response);
        }       
    }    
    
    public function change_password($user_id)
    {
        $user_id = decrypt_custom($user_id);
        if($this->input->method() === 'post')
		{
            $post = $this->input->post();
            if($post['password'] !="")
            {
                $data_user  = [ 'password'      => password_hash($post['password'], PASSWORD_BCRYPT)]; 
                if($this->crud->update_by_id('user', $data_user, $user_id))
                {
                    $this->session->set_flashdata('success', 'Data berhasil diperbarui');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Data gagal diperbarui');                    
                }
            }
            redirect(site_url('user/change_password/'.$this->global->encrypt($user_id)));
		}
		else
		{            
            $header = array( "title" => "Ganti Password");                                 
            $data = array(
                'user'          => $this->user->get_detail($user_id)
            );
            $footer = array("script" => ['setting/user/crud_user.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');        
            $this->load->view('user/change_password', $data);        
            $this->load->view('include/footer', $footer); 			
		}              
    }

    public function login_as($user_id)
    {
        if($this->session->userdata('id_u') == 1 || $this->session->userdata('name_p') == 'LOGIN AS')
        {                   
            if($this->input->method() === 'post')
            {
                if($this->session->userdata('id_u') == 1)
                {
                    $data_user     = $this->crud->get_where('user', ['id' => $user_id])->row_array();
                    $data_employee = $this->crud->get_where('employee', ['code' => $data_user['employee_code']])->row_array();
                    $session = array(			
                        'code_e'	=> $data_employee['code'],
                        'name_e' 	=> $data_employee['name'],										
                        'name_p'	=> 'LOGIN AS',
                        'id_u'		=> $data_user['id'],
                        'login'	    => 1,
                        'perusahaan' => $this->global->company()
                    );
                    $this->session->set_userdata($session);
                    $this->session->set_flashdata('success', 'Hallo '.$data_employee['name'].' semangat untuk hari ini...');
                    redirect(site_url('dashboard'), 'refresh');
                }
                elseif($this->session->userdata('name_p') == 'LOGIN AS')
                {
                    $data_user     = $this->crud->get_where('user', ['id' => $user_id])->row_array();
                    $data_employee = $this->crud->get_where('employee', ['code' => $data_user['employee_code']])->row_array();
                    $session = array(			
                        'code_e'	=> $data_employee['code'],
                        'name_e' 	=> $data_employee['name'],										
                        'name_p'	=> 'WEB DEVELOPER',
                        'id_u'		=> $data_user['id'],
                        'login'	    => 1,
                        'perusahaan' => $this->global->company()
                    );
                    $this->session->set_userdata($session);
                    $this->session->set_flashdata('success', 'Hallo '.$data_employee['name'].' semangat untuk hari ini...');
                    redirect(site_url('dashboard'), 'refresh');
                }
            }
            else
            {
                $this->load->view('auth/show_404');
            }                 
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }    
    }
}