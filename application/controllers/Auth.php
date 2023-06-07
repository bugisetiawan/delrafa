<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('Crud_model', 'crud');
		$this->load->model('Global_model', 'global');
		$this->load->model('Auth_model', 'auth');
    }
    
    public function index()
    {
		if($this->session->userdata('login') == 1)
		{
			redirect(base_url('dashboard'));
		}
		else
		{
			if($this->input->method() === 'post')
			{
				// Set Rule
				$this->form_validation->set_rules('name', 'Nama User', 'trim|required|xss_clean');
				$this->form_validation->set_rules('password', 'Kata Sandi', 'trim|required|xss_clean');
				// Set Message		
				$this->form_validation->set_message('numeric', 'Mohon maaf, <b>%s</b> ada kesalahan input');
				$this->form_validation->set_message('required', 'Mohon maaf, <b>%s</b> tidak boleh kosong');
				if($this->form_validation->run() == FALSE)
				{		
					$this->session->set_flashdata('error', ''.validation_errors());	
					redirect(base_url('login'));
				}
				else
				{
					$name		= strtoupper($this->input->post('name'));
					$password	= $this->input->post('password');
					$employee	= $this->auth->check_employee($name);
					if($employee->num_rows() > 0)
					{
						$data_employee = $employee->row_array();
						$user = $this->auth->check_user($data_employee['code_e']);
						if($user->num_rows() > 0)
						{
							$data_user = $user->row_array();
							if($data_user['active'] == 1)				
							{						
								if(password_verify($password, $data_user['password']))
								{
									if($data_user['id_u'] >=3)
									{
    									    
										if(date('H:i') >= $data_user['start_time'] && date('H:i') <= $data_user['end_time'])
										{
											$last_activity_date = date('Y-m-d',strtotime(date('Y-m-d') . "-7 days")).' 00:00:00';
											$this->crud->delete('activity', ['created <=' => $last_activity_date ]);
											$this->crud->update('user', ['last_login' => date('Y-m-d H:i:s')], ['id' => $data_user['id_u']]);
											$session = array(
												'code_e'	=> $data_employee['code_e'],
												'name_e' 	=> $data_employee['name_e'],
												'name_p'	=> $data_employee['name_p'],												
												'id_u'		=> $data_user['id_u'],
												'code_u'	=> $data_user['code_u'],
												'login'	    => 1,
												'company'   => $this->global->company()
											);
											$this->session->set_userdata($session);												
											$this->session->set_flashdata('success', 'Hallo '.$data_employee['name_e'].' Selamat Bekerja dan Beraktivitas!');
											redirect(base_url('dashboard'));
										}
										else
										{
											$this->session->set_flashdata('error', 'Maaf, Jam Akses telah berakhir');
											redirect(base_url('login'));
										}
									}
									else
									{
										$last_activity_date = date('Y-m-d',strtotime(date('Y-m-d') . "-7 days")).' 00:00:00';
										$this->crud->delete('activity', ['created <=' => $last_activity_date ]);
										$this->crud->update('user', ['last_login' => date('Y-m-d H:i:s')], ['id' => $data_user['id_u']]);
										$session = array(
											'code_e'	=> $data_employee['code_e'],
											'name_e' 	=> $data_employee['name_e'],
											'name_p'	=> $data_employee['name_p'],
											'id_u'		=> $data_user['id_u'],		
											'code_u'	=> $data_user['code_u'],									
											'login'	    => 1,
											'company'   => $this->global->company()
										);
										$this->session->set_userdata($session);
										$this->session->set_flashdata('success', 'Hallo '.$data_employee['name_e'].' semangat untuk hari ini...');
										redirect(base_url('dashboard'));
									}																															
								}
								else 
								{
									$this->session->set_flashdata('error', 'Maaf,  Harap periksa kembali Nama User/Password, terima kasih');
									redirect(base_url('login'));
								}
							}
							else
							{
								$this->session->set_flashdata('error', 'Maaf, Akun tidak aktif. Harap menghubungi admin, terima kasih');
								redirect(base_url('login'));
							}
						}	
						else
						{
							$this->session->set_flashdata('error', 'Mohon Maaf, Akun belum terdaftar');
							redirect(base_url('login'));
						}						
					}
					else
					{
						$this->session->set_flashdata('error', 'Mohon Maaf, Akun tidak ditemukan');
						redirect(base_url('login'));				
					}
				}

			}
			else
			{
				$query = $this->crud->get_where('setting', ['name' => 'company'])->row_array();
				$data = array(
					'company' => json_decode($query['information'])
				);				
				$this->load->view('auth/login', $data);
				// $this->load->view('auth/maintenance');	
			}							
		}		
	}	
		
	public function logout()
	{
		$this->session->sess_destroy();
        redirect(base_url('/'), 'refresh');
	}

	public function show_404()
	{
		$this->load->view('auth/show_404');			
	}

	public function show_access_denied()
	{
		$this->load->view('auth/show_access_denied');
	}

	public function maintenance()
	{
		$this->load->view('auth/maintenance');	
	}	
		
	public function verify_user_password()
	{
		if($this->input->is_ajax_request())
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post(); $res = 0; 
				$user_id = $this->session->userdata('id_u'); $module_url = $post['module_url']; $action = $post['action'];
				if($user_id <= 4)
				{
					$this->session->set_userdata('verifypassword', '1');
					$response   = [
						'status' => [
							'code'      => 200,
							'id_u'      => $this->session->userdata('id_u'),
							'code_e'    => $this->session->userdata('code_e'),
							'message'   => 'SUKSES! Verifikasi berhasil'
						]					
					];					
				}
				else
				{
					$data = $this->auth->verify_user_password($user_id, $module_url, $action);
					if($data[$action] == 1)
					{
						$this->session->set_userdata('verifypassword', '1');
						$response   = [
							'status' => [
								'code'      => 200,
								'id_u'      => $data['id_u'],
								'code_e'    => $data['code_e'],
								'message'   => 'SUKSES! Verifikasi berhasil'
							]					
						];
					}
					else
					{
						$response   = [
							'status'    => [
								'code'      => 400,
								'message'   => 'Mohon Maaf, verifikasi gagal'
							]
						];
					}
				}				
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 400,
						'message'   => 'Mohon Maaf, verifikasi gagal'
					]
				];				
			}
			header('Content-Type: application/json');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
	}

	public function verify_module_password()
	{
		if($this->input->is_ajax_request())
		{
			if($this->input->method() === 'post')
			{
				$post = $this->input->post(); $res = 0; 
				$module_url = $post['module_url']; $action = $post['action'];
				$bypass = $this->auth->bypass_module_password();
				foreach($bypass AS $info_bypass)
				{
					if(password_verify($post['verifypassword'], $info_bypass['password']))
					{
						$res++;
						$id_u = $info_bypass['id_u']; $code_e = $info_bypass['code_e'];						
						break;					
					}		
				}
				if($res==0)
				{
					$data_user = $this->auth->verify_module_password($module_url, $action);
					foreach($data_user AS $info_user)
					{
						if(password_verify($post['verifypassword'], $info_user['password']))
						{
							$res++;
							$id_u	= $info_user['id_u'];
							$code_e = $info_user['code_e'];
							break;
						}
					}
					if($res > 0)
					{
						$this->session->set_userdata('verifypassword', '1');
						$response   = [
							'status'    => [
								'code'      => 200,
								'id_u'      => $id_u,
								'code_e'    => $code_e,
								'message'   => 'SUKSES! Verifikasi berhasil',
							]					
						];
					}
					else
					{
						$response   = [
							'status'    => [
								'code'      => 401,
								'message'   => 'Mohon Maaf, verifikasi gagal',
							]				
						];
					}				
				}	
				else
				{
					$this->session->set_userdata('verifypassword', '1');
					$response   = [
						'status'    => [
							'code'      => 200,
							'id_u'      => $id_u,
							'code_e'    => $code_e,
							'message'   => 'SUKSES! Verifikasi berhasil',
						]					
					];
				}
			}
			else
			{
				$response   = [
					'status'    => [
						'code'      => 401,
						'message'   => '-',
					]				
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