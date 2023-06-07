<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depreciation extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();		
    }
    	
	public function datatable()
    {
        header('Content-Type: application/json');
        $this->datatables->select('id, date, name, price, period, value, TIMESTAMPDIFF(month, depreciation.date, NOW()) AS datediff, created, modified');
        $this->datatables->from('depreciation');
        $this->datatables->where('depreciation.deleted', 0);
        $this->datatables->add_column('view', 
        '
            <a href="javascript:void(0);" class="kt-font-warning kt-link update" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Data">
                <i class="fa fa-edit"> Edit</i>
            </a>
            &nbsp;
            <a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                <i class="fa fa-times"> Hapus</i>
            </a>            
        ', 'id');        
        echo $this->datatables->generate();
    } 

    public function index()
    {
        if($this->system->check_access('depreciation', 'read'))
        {
			$header = array("title" => "Depresiasi");
			$footer = array("script" => ['master/depreciation.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('depreciation/depreciation');
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }
    
	public function add()
    {
        if($this->system->check_access('depreciation', 'create'))
        {
            $post   = $this->input->post();
			$this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');
			$this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');            
			$this->form_validation->set_rules('price', 'Harga Beli', 'trim|required|xss_clean');
			$this->form_validation->set_rules('period', 'Tenor Waktu', 'trim|required|xss_clean');			
            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');                  
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
                $period = $post['period'];
                $data = [
                    'date'   => date('Y-m-d', strtotime($post['date'])),
                    'name'   => $post['name'],
					'price'  => format_amount($post['price']),
					'period' => format_amount($post['period']),
                    'value'  => ceil(format_amount($post['price'])/format_amount($post['period'])),
                    'due_date' => date('Y-m-d',strtotime($post['date'] . "+$period month"))
                ];                
                if($this->crud->insert('depreciation', $data))
                {
                    $data_activity = array (
                        'information' => 'MENAMBAH DATA DEPRESIASI',
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
    
	function get_detail_depreciation()
	{
        if($this->input->is_ajax_request())
		{
            $id=$this->input->get('id');
            $data = $this->crud->get_by_id('depreciation', $id)->row_array();
            echo json_encode($data);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
    }
    
    public function update()
    {
        if($this->system->check_access('depreciation', 'update'))
        {            
            $post   = $this->input->post();
            $this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');
			$this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
			$this->form_validation->set_rules('price', 'Harga Beli', 'trim|required|xss_clean');
			$this->form_validation->set_rules('period', 'Tenor Waktu', 'trim|required|xss_clean');
            $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
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
                $period = $post['period'];
                $data = [
                    'date'   => date('Y-m-d', strtotime($post['date'])),
                    'name'   => $post['name'],
					'price'  => format_amount($post['price']),
					'period' => format_amount($post['period']),
                    'value'  => ceil(format_amount($post['price'])/format_amount($post['period'])),
                    'due_date' => date('Y-m-d',strtotime($post['date'] . "+$period month"))
                ];
                $id     = $post['depreciation_id'];
                $update = $this->crud->update_by_id('depreciation', $data, $id);
                if($update)
                {
                    $data_activity = array (
                        'information' => 'MEMPERBAHARUI DATA DEPRESIASI ( ID - '.$id.')',
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
		if($this->system->check_access('depreciation', 'delete'))
		{
			$id     = $this->input->get('id');
			$data = array(
				'deleted' => 1
			);
			$delete = $this->crud->update_by_id('depreciation', $data, $id);
			if($delete)
			{
                $data_activity = array (
                    'information' => 'MENGHAPUS DATA DEPRESIASI ( ID - '.$id.')',
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
}
