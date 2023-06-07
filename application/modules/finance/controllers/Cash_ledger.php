<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_ledger extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Cash_ledger_model', 'cash_ledger');
    }		     

    // GENERAL -> CL_TYPE-> 1:CASH, 2:BANK, 3:SUPPLIER'S DEPOSIT, 4:CUSTOMER'S DEPOSIT
    public function get_cash_ledger_account($cl_type = null, $account_id = null)
    {
        if($this->input->is_ajax_request())
		{
            if(in_array($cl_type, [1, 2]))
            {
                $this->db->select('cla.id, cla.code, cla.name')
                         ->from('cash_ledger_account AS cla');
                if($cl_type != null || $cl_type != "")
                {
                    $this->db->where('cla.type', $cl_type);
                }
                $data = $this->db->group_by('cla.id')->get()->result();
            }
            if($cl_type == 3)
            {
                $this->db->select('supplier.id, supplier.code, supplier.name')
                         ->from('supplier')
                         ->join('cash_ledger AS cl', 'cl.account_id = supplier.id');
                if($account_id != null)
                {
                    $this->db->where('cl.account_id', $account_id);                    
                }
                $data = $this->db->group_by('supplier.id')->get()->result();
            }
            if($cl_type == 4)
            {
                $this->db->select('customer.id, customer.code, customer.name')
                         ->from('customer')
                         ->join('cash_ledger AS cl', 'cl.account_id = customer.id');
                if($account_id != null)
                {
                    $this->db->where('cl.account_id', $account_id);                    
                }
                $data = $this->db->group_by('customer.id')->get()->result();
            }
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

    public function get_last_balance()
    {
        if($this->input->is_ajax_request())
		{
            $post = $this->input->post();            
            $last_balance = $this->cash_ledger->get_last_balance($post['cl_type'], $post['account_id'], date('Y-m-d'));
            echo json_encode(isset($last_balance['balance']) ? $last_balance['balance'] : 0);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }    
    
    //CASH
    public function cash_account()
    {
        if($this->system->check_access('cash_ledger/cash/account', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('cla.id, cla.code, cla.name');
                $this->datatables->from('cash_ledger_account AS cla');
                $this->datatables->where('cla.type', 1);
                $this->datatables->where('deleted', 0);
                $this->datatables->add_column('action', 
                '
                    <a href="javascript:void(0);" class="kt-font-danger kt-link delete" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                        <i class="fa fa-times"> Hapus</i>
                    </a>
                ', 'id');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Daftar Akun Kas");
                $footer = array("script" => ['finance/cash_ledger/cash/cash_account.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('cash_ledger/cash/cash_account');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function check_cash_code($code)
    {
        $post   = $this->input->post();  
        $code   = (isset($post['code'])) ? $post['code'] : $post['editCode'];        
        return ($this->crud->get_where('cash_ledger_account', ['code' => $code, 'type' => 1, 'deleted' => 0])->num_rows() > 0) ? FALSE : TRUE;
    }

    public function create_cash_account()
    {
        if($this->input->is_ajax_request())
		{
            if($this->system->check_access('cash_ledger/cash/account','create'))
            {
                $post   = $this->input->post(); 
                $this->form_validation->set_rules('code', 'Kode', 'callback_check_cash_code|trim|required|xss_clean');
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('check_cash_code', 'Maaf! Kode Telah Digunakan');
                if($this->form_validation->run() == FALSE)
                {
                    echo validation_errors();
                }
                else
                {                    
                    $data_cash_account = [
                        'type'  => 1,
                        'code'  => $post['code'],
                        'name'	=> $post['name'],
                    ];
                    if($this->crud->insert('cash_ledger_account', $data_cash_account))
                    {
                        $response = [
                            'status' => [
                                'code'      => 200,
                                'message'   => 'Berhasil Menambahkan Data',
                            ],
                            'response'  => ""
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
		else
		{
			$this->load->view('auth/show_404');
		}        
    }

    public function check_cash_account()
    {
        $id = $this->input->get('id');
        $where_transaction = [
            'cl_type' => 1,
            'account_id' => $id
        ];
        $total_transaction = $this->crud->get_where('cash_ledger', $where_transaction)->num_rows();
        if($total_transaction == 0)
        {   
            $response = [
                'status'	=> [
                    'code'  	=> 200,
                    'message'   => 'Data diperbolehkan untuk di hapus',
                ],
                'response'  => ''
            ];            
        }
        else
        {
            $response = [
                'status'	=> [
                    'code'  	=> 400,
                    'message'   => 'Gagal Memperbarui/Menghapus Data. Karena Sudah Terdapat Transaksi',
                ],
                'response'  => ''
            ];            
        }             
        echo json_encode($response);   
    }  

    public function delete_cash_account()
    {        
		if($this->system->check_access('cash_ledger/cash/account', 'delete'))
		{
			$id = $this->input->get('id');		
			if($this->crud->update_by_id('cash_ledger_account', ['deleted' => 1], $id))
			{
                $data_activity = [
                    'information' => 'MENGHAPUS AKUN KAS (ID: '.$id.')',
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

    public function cash()
    {
        if($this->system->check_access('cash_ledger/cash', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                if($post['account_id'] != null || $post['account_id'] != "")
                {
                    $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance')
                                     ->from('cash_ledger AS cl')
                                     ->join('cash_ledger_account AS cla', 'cla.id = cl.account_id')
                                     ->where('cl.cl_type', 1)
                                     ->where('cl.deleted', 0)
                                     ->where('cl.account_id', $post['account_id'])
                                     ->where('DATE(cl.date) >=', date('Y-m-d',strtotime(format_date(date('Y-m-d')) . "-7 days")))
					                 ->where('DATE(cl.date) <=', date('Y-m-d'))
                                     ->group_by('cl.id')
                                     ->order_by('cl.date', 'DESC')
                                     ->order_by('cl.id','DESC');
                    $this->datatables->add_column('transaction_id', 
                    '$1', 'encrypt_custom(transaction_id)');
                    header('Content-Type: application/json');
                    echo $this->datatables->generate();
                }
                else
                {
                    $draw   = (!isset($post['draw']))   ? 0 : $post['draw'];
                    $output = array(
                        'draw'            => $draw,
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => []
                    );
                    header('Content-Type: application/json');        
                    echo json_encode($output); 
                }                
            }
            else
            {
                $header = array("title" => "Buku Kas");
                $data = [
                    'last_balance_cash' => $this->cash_ledger->last_balance_cash()
                ];
                $footer = array("script" => ['finance/cash_ledger/cash/cash.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('cash_ledger/cash/cash', $data);
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    // BANK ACCOUNT
    public function bank_account()
    {
        if($this->system->check_access('cash_ledger/bank/account', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('cla.id, cla.code, cla.name');
                $this->datatables->from('cash_ledger_account AS cla');
                $this->datatables->where('cla.type', 2);
                $this->datatables->where('deleted', 0);
                $this->datatables->add_column('action', 
                '
                    <a href="javascript:void(0);" class="kt-font-danger kt-link delete" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                        <i class="fa fa-times"> Hapus</i>
                    </a>
                ', 'id');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Daftar Akun Bank");
                $footer = array("script" => ['finance/cash_ledger/bank/bank_account.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('cash_ledger/bank/bank_account');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }  

    public function check_bank_code($code)
    {
        $post   = $this->input->post();  
        $code   = (isset($post['code'])) ? $post['code'] : $post['editCode'];        
        return ($this->crud->get_where('cash_ledger_account', ['code' => $code, 'type' => 2, 'deleted' => 0])->num_rows() > 0) ? FALSE : TRUE;
    }

    public function create_bank_account()
    {
        if($this->input->is_ajax_request())
		{
            if($this->system->check_access('cash_ledger/bank/account','create'))
            {
                $post   = $this->input->post(); 
                $this->form_validation->set_rules('code', 'Kode', 'callback_check_bank_code|trim|required|xss_clean');
                $this->form_validation->set_rules('name', 'Nama', 'trim|required|xss_clean');
                $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');
                $this->form_validation->set_message('check_bank_code', 'Maaf! Kode Telah Digunakan');
                if($this->form_validation->run() == FALSE)
                {
                    echo validation_errors();
                }
                else
                {                    
                    $data_cash_account = [
                        'type'  => 2,
                        'code'  => $post['code'],
                        'name'	=> $post['name'],
                    ];
                    if($this->crud->insert('cash_ledger_account', $data_cash_account))
                    {
                        $response = [
                            'status' => [
                                'code'      => 200,
                                'message'   => 'Berhasil Menambahkan Data',
                            ],
                            'response'  => ""
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
		else
		{
			$this->load->view('auth/show_404');
		}        
    }

    public function check_bank_account()
    {
        $id = $this->input->get('id');
        $where_transaction = [
            'cl_type' => 2,
            'account_id' => $id
        ];
        $total_transaction = $this->crud->get_where('cash_ledger', $where_transaction)->num_rows();
        if($total_transaction == 0)
        {   
            $response = [
                'status'	=> [
                    'code'  	=> 200,
                    'message'   => 'Data diperbolehkan untuk di hapus',
                ],
                'response'  => ''
            ];            
        }
        else
        {
            $response = [
                'status'	=> [
                    'code'  	=> 400,
                    'message'   => 'Gagal Memperbarui/Menghapus Data. Karena Sudah Terdapat Transaksi',
                ],
                'response'  => ''
            ];            
        }             
        echo json_encode($response);   
    }

    public function delete_bank_account()
    {        
		if($this->system->check_access('cash_ledger/cash/account', 'delete'))
		{
			$id = $this->input->get('id');		
			if($this->crud->update_by_id('cash_ledger_account', ['deleted' => 1], $id))
			{
                $data_activity = [
                    'information' => 'MENGHAPUS AKUN BANK (ID: '.$id.')',
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

    // BANK TRANSACTION
    public function bank()
    {
        if($this->system->check_access('cash_ledger/bank', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                if($post['account_id'] != null || $post['account_id'] != "")
                {
                    $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                    $this->datatables->from('cash_ledger AS cl');                
                    $this->datatables->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');
                    $this->datatables->where('cl.cl_type', 2);
                    $this->datatables->where('cl.deleted', 0);
                    $this->datatables->where('cl.account_id', $post['account_id']);
                    $this->datatables->group_by('cl.id');
                    $this->datatables->order_by('cl.date', 'DESC');
                    $this->datatables->order_by('cl.id','DESC');
                    header('Content-Type: application/json');
                    echo $this->datatables->generate();
                }
                else
                {
                    $draw   = (!isset($post['draw']))   ? 0 : $post['draw'];
                    $output = array(
                        'draw'            => $draw,
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => []
                    );
                    header('Content-Type: application/json');
                    echo json_encode($output);
                }
            }
            else
            {
                $header = array("title" => "Buku Bank");
                $data = [
                    'last_balance_bank' => $this->cash_ledger->last_balance_bank()
                ];
                $footer = array("script" => ['finance/cash_ledger/bank/bank.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('cash_ledger/bank/bank', $data);
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }   

    // SUPPLIER DEPOSIT CL_TYPE:3
    public function supplier_deposit()
    {
        if($this->system->check_access('cash_ledger/supplier_deposit', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('cl.id AS id_cl, cl_type, supplier.name AS name_s, date, invoice, information, note, amount, method, balance');
                $this->datatables->from('cash_ledger AS cl');
                $this->datatables->join('supplier', 'supplier.id = cl.account_id');
                $this->datatables->where('cl.cl_type', 3);
                $this->datatables->where('cl.deleted', 0);
                $this->datatables->group_by('cl.id');
                $this->datatables->order_by('cl.date', 'desc');
                $this->datatables->order_by('cl.id','desc');
                $this->datatables->add_column('action', 
                '                                        
                    <a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                        <i class="fa fa-trash"></i>
                    </a>
                ', 'id_cl');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Deposit Supplier");
                $footer = array("script" => ['finance/cash_ledger/supplier_deposit.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('cash_ledger/deposit/supplier_deposit');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    // CUSTOMER DEPOSIT CL_TYPE:4
    public function customer_deposit()
    {
        if($this->system->check_access('cash_ledger/customer_deposit', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('cl.id AS id_cl, cl_type, customer.name AS name_c, date, invoice, information, note, amount, method, balance');
                $this->datatables->from('cash_ledger AS cl');
                $this->datatables->join('customer', 'customer.id = cl.account_id');
                $this->datatables->where('cl.cl_type', 4);
                $this->datatables->where('cl.deleted', 0);
                $this->datatables->group_by('cl.id');
                $this->datatables->order_by('cl.date', 'desc');
                $this->datatables->order_by('cl.id','desc');
                $this->datatables->add_column('action', 
                '                                        
                    <a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                        <i class="fa fa-trash"></i>
                    </a>
                ', 'id_cl');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Deposit Pelanggan");
                $footer = array("script" => ['finance/cash_ledger/customer_deposit.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('cash_ledger/deposit/customer_deposit');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }         

    // TRANSACTION DEPOSIT -> METHOD 1:IN, 2:OUT
    public function deposit()
    {
        if($this->input->is_ajax_request())
		{
            if($this->system->check_access('menu', 'read'))
            {
                $post   = $this->input->post();
                $this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');
                $this->form_validation->set_rules('to_account_id', 'Akun', 'trim|required|xss_clean');
                $this->form_validation->set_rules('amount', 'Jumlah Nominal', 'trim|required|xss_clean');
                $this->form_validation->set_rules('note', 'Catatan', 'trim|xss_clean');
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
                    $code = $this->cash_ledger->deposit_code($post['deposit_type']);
                    $amount = format_amount($post['amount']);
                    switch($post['deposit_type']){
                        case 1: // SUPPLIER'S DEPOSIT
                            $supplier = $this->crud->get_where('supplier', ['id' => $post['to_account_id']])->row_array();
                            // CASH LEDGER                            
                            // TO ACCOUNT
                            $last_balance = $this->cash_ledger->get_last_balance($post['to_cl_type'], $post['to_account_id'], $post['date']);                            
                            if($post['method'] == 1)
                            {
                                $information ="PEMBAYARAN UANG MUKA PEMBELIAN";
                                $note ="PEMBAYARAN_UANG_MUKA_PEMBELIAN_".$supplier['name'];
                                $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                            }
                            elseif($post['method'] == 2)
                            {
                                $information ="PENGEMBALIAN UANG MUKA PEMBELIAN";
                                $note ="PENGEMBALIAN_UANG_MUKA_PEMBELIAN_".$supplier['name'];
                                $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                            }                    
                            $data = [
                                'date'        => format_date($post['date']),
                                'cl_type'     => $post['to_cl_type'],
                                'account_id'  => $post['to_account_id'],
                                'information' => $information,
                                'invoice'     => $code,
                                'note'        => $note,
                                'amount'      => $amount,
                                'method'      => $post['method'],
                                'balance'     => $balance
                            ];
                            $to_cl_id = $this->crud->insert_id('cash_ledger', $data);
                            if($to_cl_id != null)
                            {
                                $to_where_after_balance = [
                                    'date >'        => format_date($post['date']),
                                    'cl_type'       => $post['to_cl_type'],
                                    'account_id'    => $post['to_account_id'],
                                    'deleted'       => 0
                                ];                    
                                $to_after_balance = $this->db->select('*')->from('cash_ledger')->where($to_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                                foreach($to_after_balance  AS $info)
                                {
                                    if($post['method'] == 1)
                                    {
                                        $this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                    }
                                    elseif($post['method'] == 2)
                                    {
                                        $this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                    }                                    
                                }    
                                // FROM ACCOUNT
                                $from_last_balance = $this->cash_ledger->get_last_balance($post['from_cl_type'], $post['from_account_id'], $post['date']);
                                if($post['method'] == 1)
                                {
                                    $from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $amount) : sub_balance(0, $amount);
                                }
                                elseif($post['method'] == 2)
                                {
                                    $from_balance = ($from_last_balance != null) ?  add_balance($from_last_balance['balance'], $amount) : add_balance(0, $amount);
                                }                    
                                $data = [
                                    'date'        => format_date($post['date']),
                                    'cl_type'     => $post['from_cl_type'],
                                    'account_id'  => $post['from_account_id'],
                                    'information' => $information,
                                    'invoice'     => $code,
                                    'note'        => $note,
                                    'amount'      => $amount,
                                    'method'      => ($post['method'] == 1) ? 2 : 1,
                                    'balance'     => $from_balance
                                ];
                                $from_cl_id = $this->crud->insert_id('cash_ledger', $data);
                                if($from_cl_id != null)
                                {
                                    $from_where_after_balance = [
                                        'date >'        => format_date($post['date']),
                                        'cl_type'       => $post['from_cl_type'],
                                        'account_id'    => $post['from_account_id'],
                                        'deleted'       => 0
                                    ];                    
                                    $from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->get()->result_array();
                                    foreach($from_after_balance  AS $info)
                                    {                        
                                        if($post['method'] == 1)
                                        {
                                            $this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], format_amount($post['amount']))], ['id' => $info['id']]);
                                        }
                                        if($post['method'] == 2)
                                        {
                                            $this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], format_amount($post['amount']))], ['id' => $info['id']]);
                                        }                                        
                                    }
                                }
                                $this->crud->update('cash_ledger', ['transaction_id' => $from_cl_id, 'to_cl_id' => $to_cl_id], ['id' => $from_cl_id]);
                                $this->crud->update('cash_ledger', ['transaction_id' => $to_cl_id, 'from_cl_id' => $from_cl_id], ['id' => $to_cl_id]);
                                // GENERAL_LEDGER -> SUPPLIER'S DEPOSIT
                                $where_last_balance = [
                                    'coa_account_code' => "10401",
                                    'date <='        => format_date($post['date']),
                                    'deleted'        => 0
                                ];
                                $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                                if($post['method'] == 1) //DEBIT
                                {
                                    $information ="PEMBAYARAN UANG MUKA PEMBELIAN";
                                    $note ="PEMBAYARAN_UANG_MUKA_PEMBELIAN_".$supplier['name'];
                                    $method = 'debit';
                                    $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                                }
                                elseif($post['method'] == 2) //CREDIT
                                {
                                    $information ="PENGEMBALIAN UANG MUKA PEMBELIAN";
                                    $note ="PENGEMBALIAN_UANG_MUKA_PEMBELIAN_".$supplier['name'];
                                    $method = 'credit';                                    
                                    $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                                }                    
                                $data = [
                                    'coa_account_code' => "10401",
                                    'date'        => format_date($post['date']),										
                                    'transaction_id' => $to_cl_id,
                                    'invoice'     => $code,
                                    'information' => $information,
                                    'note'		  => $note,
                                    $method       => $amount,
                                    'balance'     => $balance
                                ];									
                                if($this->crud->insert('general_ledger', $data))
                                {
                                    $where_after_balance = [
                                        'coa_account_code' => "10401",
                                        'date >'        => format_date($post['date']),
                                        'deleted'       => 0
                                    ];
                                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                                    foreach($after_balance  AS $info)
                                    {
                                        if($post['method'] == 1)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }
                                        elseif($post['method'] == 2)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }                                        
                                    }
                                }
                                // GENERAL_LEDGER -> KAS & BANK (K)
                                $where_last_balance = [
                                    'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                                    'date <='        => format_date($post['date']),
                                    'deleted'        => 0
                                ];
                                $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                                if($post['method'] == 1) //DEBIT
                                {
                                    $information ="PEMBAYARAN UANG MUKA PEMBELIAN";
                                    $note ="PEMBAYARAN_UANG_MUKA_PEMBELIAN_".$supplier['name'];
                                    $method = 'credit';
                                    $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                                }
                                elseif($post['method'] == 2) //CREDIT
                                {
                                    $information ="PENGEMBALIAN UANG MUKA PEMBELIAN";
                                    $note ="PENGEMBALIAN_UANG_MUKA_PEMBELIAN_".$supplier['name'];
                                    $method = 'debit';
                                    $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                                }                    
                                $data = [
                                    'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                                    'date'        => format_date($post['date']),										
                                    'transaction_id' => $from_cl_id,
                                    'invoice'     => $code,
                                    'information' => $information,
                                    'note'		  => $note,
                                    $method       => $amount,
                                    'balance'     => $balance
                                ];									
                                if($this->crud->insert('general_ledger', $data))
                                {
                                    $where_after_balance = [
                                        'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                                        'date >'        => format_date($post['date']),
                                        'deleted'       => 0
                                    ];
                                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                                    foreach($after_balance  AS $info)
                                    {
                                        if($post['method'] == 1)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }
                                        elseif($post['method'] == 2)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }                                        
                                    }
                                }
                                $data_activity = [
                                    'information' => $information,
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
                            break;
                        case 2: // CUSTOMER'S DEPOSIT                                                  
                            $customer = $this->crud->get_where('customer', ['id' => $post['to_account_id']])->row_array();
                            // CASH LEDGER                            
                            // TO ACCOUNT
                            $last_balance = $this->cash_ledger->get_last_balance($post['to_cl_type'], $post['to_account_id'], $post['date']);                            
                            if($post['method'] == 1)
                            {
                                $information = "PENERIMAAN UANG MUKA PENJUALAN";
                                $note = "PENERIMAAN_UANG_MUKA_PENJUALAN_".$customer['name'];
                                $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                            }
                            elseif($post['method'] == 2)
                            {
                                $information = "PENGEMBALIAN UANG MUKA PENJUALAN";
                                $note = "PENGEMBALIAN_UANG_MUKA_PENJUALAN_".$customer['name'];
                                $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                            }                    
                            $data = [
                                'date'        => format_date($post['date']),
                                'cl_type'     => $post['to_cl_type'],
                                'account_id'  => $post['to_account_id'],
                                'information' => $information,
                                'invoice'     => $code,
                                'note'        => $note,
                                'amount'      => $amount,
                                'method'      => $post['method'],
                                'balance'     => $balance
                            ];
                            $to_cl_id = $this->crud->insert_id('cash_ledger', $data);
                            if($to_cl_id != null)
                            {
                                $to_where_after_balance = [
                                    'date >'        => format_date($post['date']),
                                    'cl_type'       => $post['to_cl_type'],
                                    'account_id'    => $post['to_account_id'],
                                    'deleted'       => 0
                                ];                    
                                $to_after_balance = $this->db->select('*')->from('cash_ledger')->where($to_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                                foreach($to_after_balance  AS $info)
                                {
                                    if($post['method'] == 1)
                                    {
                                        $this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                    }
                                    elseif($post['method'] == 2)
                                    {
                                        $this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                    }                                    
                                }    
                                // FROM ACCOUNT
                                $from_last_balance = $this->cash_ledger->get_last_balance($post['from_cl_type'], $post['from_account_id'], $post['date']);
                                if($post['method'] == 1)
                                {
                                    $from_balance = ($from_last_balance != null) ?  add_balance($from_last_balance['balance'], $amount) : add_balance(0, $amount);
                                }
                                elseif($post['method'] == 2)
                                {
                                    $from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $amount) : sub_balance(0, $amount);
                                }                    
                                $data = [
                                    'date'        => format_date($post['date']),
                                    'cl_type'     => $post['from_cl_type'],
                                    'account_id'  => $post['from_account_id'],
                                    'information' => $information,
                                    'invoice'     => $code,
                                    'note'        => $note,
                                    'amount'      => $amount,
                                    'method'      => ($post['method'] == 1) ? 1 : 2,
                                    'balance'     => $from_balance
                                ];
                                $from_cl_id = $this->crud->insert_id('cash_ledger', $data);
                                if($from_cl_id != null)
                                {
                                    $from_where_after_balance = [
                                        'date >'        => format_date($post['date']),
                                        'cl_type'       => $post['from_cl_type'],
                                        'account_id'    => $post['from_account_id'],
                                        'deleted'       => 0
                                    ];                    
                                    $from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->get()->result_array();
                                    foreach($from_after_balance  AS $info)
                                    {                        
                                        if($post['method'] == 1)
                                        {
                                            $this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], format_amount($post['amount']))], ['id' => $info['id']]);
                                        }
                                        if($post['method'] == 2)
                                        {
                                            $this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], format_amount($post['amount']))], ['id' => $info['id']]);
                                        }                                        
                                    }
                                }
                                $this->crud->update('cash_ledger', ['transaction_id' => $from_cl_id, 'to_cl_id' => $to_cl_id], ['id' => $from_cl_id]);
                                $this->crud->update('cash_ledger', ['transaction_id' => $to_cl_id, 'from_cl_id' => $from_cl_id], ['id' => $to_cl_id]);
                                // GENERAL_LEDGER -> KAS & BANK
                                $where_last_balance = [
                                    'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                                    'date <='        => format_date($post['date']),
                                    'deleted'        => 0
                                ];
                                $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                                if($post['method'] == 1) //DEBIT
                                {
                                    $information ="PENERIMAAN UANG MUKA PENJUALAN";
                                    $note ="PENERIMAAN_UANG_MUKA_PENJUALAN_".$customer['name'];
                                    $method = 'debit';
                                    $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                                }
                                elseif($post['method'] == 2) //CREDIT
                                {
                                    $information ="PENGEMBALIAN UANG MUKA PENJUALAN";
                                    $note ="PENGEMBALIAN_UANG_MUKA_PENJUALAN_".$customer['name'];
                                    $method = 'credit';
                                    $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                                }                    
                                $data = [
                                    'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                                    'date'        => format_date($post['date']),										
                                    'transaction_id' => $from_cl_id,
                                    'invoice'     => $code,
                                    'information' => $information,
                                    'note'		  => $note,
                                    $method       => $amount,
                                    'balance'     => $balance
                                ];									
                                if($this->crud->insert('general_ledger', $data))
                                {
                                    $where_after_balance = [
                                        'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                                        'date >'        => format_date($post['date']),
                                        'deleted'       => 0
                                    ];
                                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                                    foreach($after_balance  AS $info)
                                    {
                                        if($post['method'] == 1)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }
                                        elseif($post['method'] == 2)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }                                        
                                    }
                                }
                                // GENERAL_LEDGER -> UANG MUKA PENJUALAN
                                $where_last_balance = [
                                    'coa_account_code' => "20201",
                                    'date <='        => format_date($post['date']),
                                    'deleted'        => 0
                                ];
                                $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                                if($post['method'] == 1) //CREDIT
                                {
                                    $information ="PENERIMAAN UANG MUKA PENJUALAN";
                                    $note ="PENERIMAAN_UANG_MUKA_PENJUALAN_".$customer['name'];
                                    $method = 'credit';
                                    $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                                }
                                elseif($post['method'] == 2) //DEBIT
                                {
                                    $information = "PENGEMBALIAN UANG MUKA PENJUALAN";
                                    $note ="PENGEMBALIAN_UANG_MUKA_PENJUALAN_".$customer['name'];
                                    $method = 'debit';                                    
                                    $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                                }                    
                                $data = [
                                    'coa_account_code' => "20201",
                                    'date'        => format_date($post['date']),										
                                    'transaction_id' => $to_cl_id,
                                    'invoice'     => $code,
                                    'information' => $information,
                                    'note'		  => $note,
                                    $method       => $amount,
                                    'balance'     => $balance
                                ];									
                                if($this->crud->insert('general_ledger', $data))
                                {
                                    $where_after_balance = [
                                        'coa_account_code' => "20201",
                                        'date >'        => format_date($post['date']),
                                        'deleted'       => 0
                                    ];
                                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                                    foreach($after_balance  AS $info)
                                    {
                                        if($post['method'] == 1)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }
                                        elseif($post['method'] == 2)
                                        {
                                            $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                                        }                                        
                                    }
                                }                            
                                $data_activity = [
                                    'information' => $information,
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
                            break;                    
                        default:
                            $response = [
                                'status' => [
                                    'code'      => 401,
                                    'message'   => "Mohon Maaf, transaksi gagal",
                                ],
                                'response'  => ''
                            ];
                            break;
                    }
                }
            }
            else
            {
                $response = [
                    'status' => [
                        'code'      => 401,
                        'message'   => "Mohon Maaf, anda tidak memilik Akses. Terima Kasih",
                    ],
                    'response'  => ''
                ];
            }
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    // CAST LEDGER IN/OUT
    public function cash_ledger_in_out()
    {
        if($this->system->check_access('cash_ledger/in_out', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                $this->datatables->from('cash_ledger AS cl');                
                $this->datatables->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');                
                $this->datatables->where('cl.information', "KAS&BANK MASUK/KELUAR");
                $this->datatables->where('cl.deleted', 0);                
                $this->datatables->group_by('cl.id');
                $this->datatables->order_by('cl.date', 'DESC');
                $this->datatables->order_by('cl.id','DESC');
                $this->datatables->add_column('transaction_id', 
                '$1', 'encrypt_custom(transaction_id)');
                header('Content-Type: application/json');
                echo $this->datatables->generate();                                        
            }
            else
            {
                $header = array("title" => "Daftar Kas&Bank Masuk/Keluar");
                $footer = array("script" => ['finance/cash_ledger/in_out/cash_ledger_in_out.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('cash_ledger/in_out/cash_ledger_in_out');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function get_account()
    {
        if($this->input->is_ajax_request())
		{
			$search = urldecode($this->uri->segment(4));				
			$data   = $this->cash_ledger->get_account($search);
			$response = [];
			if($data->num_rows() > 0){
				foreach($data->result_array() as $info)
				{
					$response[] = array(
                        'id'   => $info['id'],
						'code' => $info['code'],
						'name' => $info['name'],
					);
				}            
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    public function create_cash_ledger_in_out()
    {
        if($this->system->check_access('cash_ledger/in_out', 'create'))
        {
            if($this->input->method() === 'post')
            {
                $post = $this->input->post();
                $code = $this->cash_ledger->cash_ledger_in_out_code($post['method']);
                $date = format_date($post['date']);
                $grandtotal = format_amount($post['grandtotal']);
                // CASH LEDGER
                $where_last_balance = [
                    'cl_type'    => $post['cl_type'],
                    'account_id' => $post['account_id'],
                    'date <='    => $date,                    
                    'deleted'    => 0
                ];
                $last_balance = $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
                if($post['method'] == 1)
                {
                    $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $grandtotal) : add_balance(0, $grandtotal);
                }
                elseif($post['method'] == 2)
                {                
                    $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
                }
                $data = [
                    'date'        => $date,
                    'cl_type'     => $post['cl_type'],
                    'account_id'  => $post['account_id'],
                    'information' => 'KAS&BANK MASUK/KELUAR',
                    'invoice'     => $code,
                    'note'        => ($post['information'] != "") ? $post['information'] : "-",
                    'amount'      => $grandtotal,
                    'method'      => $post['method'],
                    'balance'     => $balance
                ];
                $cl_id = $this->crud->insert_id('cash_ledger', $data);
                if($cl_id != null)
                {
                    $this->crud->update('cash_ledger', ['transaction_id' => $cl_id], ['id' => $cl_id]);
                    $where_after_balance = [
                        'cl_type'       => $post['cl_type'],
                        'account_id'    => $post['account_id'],
                        'date >'        => $date,
                        'deleted'       => 0
                    ];                    
                    $after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                    foreach($after_balance  AS $info)
                    {   
                        if($post['method'] == 1)                     
                        {
                            $balance = add_balance($info['balance'], $grandtotal);
                        }
                        elseif($post['method'] == 2)
                        {                        
                            $balance = sub_balance($info['balance'], $grandtotal);
                        }                    
                        $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                    }
                    // GENERAL_LEDGER -> KAS & BANK (K)
                    $where_last_balance = [
                        'coa_account_code' => ($post['cl_type'] == 1) ? "10101" : "10102",
                        'date <='        => $date,                    
                        'deleted'        => 0
                    ];
                    $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                    if($post['method'] == 1)
                    {
                        $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $grandtotal) : add_balance(0, $grandtotal);
                        $method = "debit";
                    }
                    elseif($post['method'] == 2)
                    {
                        $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $grandtotal) : sub_balance(0, $grandtotal);
                        $method = "credit";
                    }                    
                    $data = [
                        'coa_account_code' => ($post['cl_type'] == 1) ? "10101" : "10102",
                        'date'        => $date,										
                        'transaction_id' => $cl_id,
                        'invoice'     => $code,
                        'information' => 'KAS&BANK MASUK/KELUAR',
                        'note'		  => 'KAS&BANK_MASUK/KELUAR_'.$code,
                        $method       => $grandtotal,
                        'balance'     => $balance
                    ];									
                    if($this->crud->insert('general_ledger', $data))
                    {
                        $where_after_balance = [
                            'coa_account_code' => ($post['cl_type'] == 1) ? "10101" : "10102",
                            'date >'        => $date,
                            'deleted'       => 0
                        ];
                        $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                        foreach($after_balance  AS $info)
                        {
                            if($post['method'] == 1)
                            {
                                $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
                            }
                            elseif($post['method'] == 2)
                            {
                                $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $grandtotal)], ['id' => $info['id']]);
                            }                            
                        }
                    }
                    // CASH LEDGER IN/OUT
                    foreach($post['account'] AS $info_account)
                    {
                        $cash_ledger_transaction = [
                            'cl_id' => $cl_id,
                            'coa_account_code' => $info_account['coa_account_code'],
                            'amount'           => format_amount($info_account['amount']),
                        ];
                        $this->crud->insert('cash_ledger_in_out', $cash_ledger_transaction);
                        // GENERAL LEDGER -> CASH LEDGER IN/OUT
                        $coa_category = substr($info_account['coa_account_code'], 0, 1);
                        if($post['method'] == 1)
                        {
                            $debit = 0; $credit = format_amount($info_account['amount']);
                        }
                        elseif($post['method'] == 2)
                        {
                            $debit = format_amount($info_account['amount']); $credit = 0;
                        }
                        $where_last_balance = [
                            'coa_account_code' => $info_account['coa_account_code'],
                            'date <='        => $date,
                            'deleted'        => 0
                        ];
                        $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                        if(in_array($coa_category, [1, 5, 7]))
                        {
                            if($debit != 0 && $credit == 0)
                            {
                                $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $debit) : add_balance(0, $debit);
                            }
                            elseif($debit == 0 && $credit != 0)
                            {
                                $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $credit) : sub_balance(0, $credit);
                            }
                        }
                        elseif(in_array($coa_category, [2, 3, 4, 6]))
                        {
                            if($debit != 0 && $credit == 0)
                            {
                                $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $debit) : sub_balance(0, $debit);
                            }
                            elseif($debit == 0 && $credit != 0)
                            {
                                $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $credit) : add_balance(0, $credit);
                            }
                        }						
                        $general_ledger = [
                            'date'              => $date,
                            'coa_account_code'  => $info_account['coa_account_code'],
                            'transaction_id'    => $cl_id,
                            'invoice'     		=> $code,
                            'information' 		=> 'KAS&BANK MASUK/KELUAR',
                            'note'		  		=> 'KAS&BANK_MASUK/KELUAR_'.$code,
                            'debit'            => ($debit != "") ? $debit : 0,
                            'credit'           => ($credit != "") ? $credit : 0,
                            'balance'     		=> $balance
                        ];									
                        $this->crud->insert('general_ledger', $general_ledger);
                        $where_after_balance = [
                            'coa_account_code' => $info_account['coa_account_code'],
                            'date >'           => $date,
                            'deleted'          => 0
                        ];
                        $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                        foreach($after_balance  AS $info_after_balance)
                        {
                            if(in_array($coa_category, [1, 5, 7]))
                            {
                                if($debit != 0 && $credit == 0)
                                {
                                    $balance = add_balance($info_after_balance['balance'], $debit);
                                }
                                elseif($debit == 0 && $credit != 0)
                                {
                                    $balance = sub_balance($info_after_balance['balance'], $credit);
                                }
                            }
                            elseif(in_array($coa_category, [2, 3, 4, 6]))
                            {
                                if($debit != 0 && $credit == 0)
                                {
                                    $balance = sub_balance($info_after_balance['balance'], $debit);
                                }
                                elseif($debit == 0 && $credit != 0)
                                {
                                    $balance = add_balance($info_after_balance['balance'], $credit);
                                }
                            }
                            $this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
                        }
                    }
                    $this->session->set_flashdata('success', 'SUKSES! KAS&BANK Masuk/Keluar berhasil disimpan');
                    redirect(site_url('cash_ledger/cash'));
                }
                else
                {
                    $this->session->set_flashdata('error', 'GAGAL! KAS&BANK Masuk/Keluar gagal disimpan');
                    redirect(site_url('cash_ledger/cash'));
                }
            }
            else
            {
                $header = array("title" => "Kas&Bank Masuk/Keluar Baru");
                $footer = array("script" => ['finance/cash_ledger/create_cash_ledger_in_out.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('cash_ledger/create_cash_ledger_in_out');
                $this->load->view('include/footer', $footer);			
            }        
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function detail_cash_ledger_in_out($cl_id)
    {
        if($this->system->check_access('cash_ledger/in_out', 'detail'))
        {
            $cash_ledger = $this->cash_ledger->get_detail_cash_ledger(decrypt_custom($cl_id));
            $header = array("title" => "Detail Kas&Bank Masuk/Keluar");
            $data = [
                'cash_ledger' => $cash_ledger,
                'cash_ledger_in_out' => $this->cash_ledger->get_detail_cash_ledger_in_out($cash_ledger['id'])
            ];
            $footer = array("script" => ['finance/cash_ledger/detail_cash_ledger_in_out.js']);
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('cash_ledger/detail_cash_ledger_in_out', $data);
            $this->load->view('include/footer', $footer);             
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function delete_cash_ledger_in_out()
    {
        if($this->system->check_access('cash_ledger/in_out', 'delete'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                $cash_ledger = $this->crud->get_where('cash_ledger', ['id' => $post['cash_ledger_id']])->row_array();
                $cash_ledger_in_out = $this->crud->get_where('cash_ledger_in_out', ['cl_id' => $cash_ledger['id']])->result_array();
                // CASH LEDGER
                $where_after_balance = [
                    'cl_type'    => $cash_ledger['cl_type'],
                    'account_id' => $cash_ledger['account_id'],
                    'date >='    => $cash_ledger['date'],
                    'deleted'    => 0
                ];
                $after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                foreach($after_balance AS $info_after_balance)
                {
                    if($info_after_balance['date'] == $cash_ledger['date'] && $info_after_balance['id'] < $cash_ledger['id'])
                    {
                        continue;
                    }
                    else
                    {
                        if($cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                        {
                            $balance = $info_after_balance['balance']-$cash_ledger['amount'];
                        }
                        else
                        {
                            $balance = $info_after_balance['balance']+$cash_ledger['amount'];
                        }
                        $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
                    }
                }
                $this->crud->delete_by_id('cash_ledger', $cash_ledger['id']);
                $this->crud->delete('cash_ledger_in_out', ['cl_id' => $cash_ledger['id']]);
				$general_ledger = $this->crud->get_where('general_ledger', ['invoice' => $cash_ledger['invoice']]);
				if($general_ledger->num_rows() > 0)
				{
					foreach($general_ledger->result_array() AS $info_general_ledger)
					{
						$where_after_balance = [
							'coa_account_code'=> $info_general_ledger['coa_account_code'],
							'date >='    => $info_general_ledger['date'],
							'deleted'    => 0
						];
						$after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_balance AS $info_after_balance)
						{
							if($info_after_balance['date'] == $info_general_ledger['date'] && $info_after_balance['id'] < $info_general_ledger['id'])
							{
								continue;
							}
							else
							{
								$coa_category = substr($info_general_ledger['coa_account_code'], 0, 1);
								if(in_array($coa_category, [1, 5, 7]))
                                {
                                    if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
                                    {
                                        $balance = $info_after_balance['balance']-$info_general_ledger['debit'];
                                    }
                                    elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
                                    {
                                        $balance = $info_after_balance['balance']+$info_general_ledger['credit'];
                                    }
                                }
                                elseif(in_array($coa_category, [2, 3, 4, 6]))
                                {
                                    if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
                                    {
                                        $balance = $info_after_balance['balance']+$info_general_ledger['debit'];
                                    }
                                    elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
                                    {
                                        $balance = $info_after_balance['balance']-$info_general_ledger['credit'];
                                    }
                                }
								$this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
							}
						}
						$this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
					}
                }    
                $data_activity = [
                    'information' => 'MENGHAPUS KAS MASUK/KERLUAR',
                    'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                    'code_e'      => $this->session->userdata('code_e'),
                    'name_e'      => $this->session->userdata('name_e'),
                    'user_id'     => $this->session->userdata('id_u')
                ];						
                $this->crud->insert('activity', $data_activity);
    
                $response   =   [
                    'status'    => [
                        'code'      => 200,
                        'message'   => 'Berhasil',
                    ],
                    'response'  => ''
                ];
                $this->session->set_flashdata('success', 'BERHASIL! Kas Masuk/Keluar Terhapus');
                echo json_encode($response);            				
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

    // CASH LEDGER MUTATION    
    public function cash_ledger_mutation()
    {
        if($this->system->check_access('cash_ledger/mutation', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('cl.id AS id, cl.cl_type, cla.code AS cla_code, cl.date, cl.transaction_id, cl.invoice, cl.information, cl.note, cl.amount, cl.method, cl.balance');
                $this->datatables->from('cash_ledger AS cl');                
                $this->datatables->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');                
                $this->datatables->where('cl.information', "KAS&BANK MUTASI");
                $this->datatables->where('cl.deleted', 0);                
                $this->datatables->group_by('cl.id');
                $this->datatables->order_by('cl.date', 'DESC');
                $this->datatables->order_by('cl.id','DESC');
                $this->datatables->add_column('transaction_id', 
                '$1', 'encrypt_custom(transaction_id)');
                header('Content-Type: application/json');
                echo $this->datatables->generate();                                        
            }
            else
            {
                $header = array("title" => "Daftar Kas&Bank Mutasi");
                $footer = array("script" => ['finance/cash_ledger/mutation/cash_ledger_mutation.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('cash_ledger/mutation/cash_ledger_mutation');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function create_cash_ledger_mutation()
    {
        if($this->system->check_access('cash_ledger/mutation', 'create'))
        {
            if($this->input->method() === 'post')
            {
                $post = $this->input->post();
                $code = $this->cash_ledger->cash_ledger_mutation_code();
                $amount = format_amount($post['amount']);
                $to_account  = $this->crud->get_where('cash_ledger_account', ['type' => $post['to_cl_type'], 'id' => $post['to_account_id']])->row_array();
                $from_account  = $this->crud->get_where('cash_ledger_account', ['type' => $post['from_cl_type'], 'id' => $post['from_account_id']])->row_array();                                        
                // CASH LEDGER-> FROM ACCOUNT
                $from_last_balance = $this->cash_ledger->get_last_balance($post['from_cl_type'], $post['from_account_id'], $post['date']);
                $from_balance = ($from_last_balance != null) ?  sub_balance($from_last_balance['balance'], $amount) : sub_balance(0, $amount);
                $data = [
                    'date'        => format_date($post['date']),
                    'cl_type'     => $post['from_cl_type'],
                    'account_id'  => $post['from_account_id'],
                    'information' => 'KAS&BANK MUTASI',
                    'invoice'     => $code,
                    'note'        => 'MUTASI_KE_'.$to_account['name'],
                    'amount'      => $amount,
                    'method'      => 2,
                    'balance'     => $from_balance
                ];
                $from_cl_id = $this->crud->insert_id('cash_ledger', $data);                    
                $from_where_after_balance = [
                    'date >'        => format_date($post['date']),
                    'cl_type'       => $post['from_cl_type'],
                    'account_id'    => $post['from_account_id'],
                    'deleted'       => 0
                ];                    
                $from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->get()->result_array();
                foreach($from_after_balance  AS $info)
                {                                                        
                    $this->crud->update('cash_ledger', ['balance' => sub_balance($info['balance'], format_amount($post['amount']))], ['id' => $info['id']]);
                }
                // GENERAL_LEDGER -> FROM ACCOUNT
                $where_last_balance = [
                    'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                    'date <='        => format_date($post['date']),
                    'deleted'        => 0
                ];
                $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : add_balance(0, $amount);                                                
                $data = [
                    'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                    'date'        => format_date($post['date']),										
                    'transaction_id' => $from_cl_id,
                    'invoice'     => $code,
                    'information' => 'KAS&BANK MUTASI',
                    'note'        => 'MUTASI_KE_'.$to_account['name'],
                    'credit'      => $amount,
                    'balance'     => $balance
                ];									
                if($this->crud->insert('general_ledger', $data))
                {
                    $where_after_balance = [
                        'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                        'date >'        => format_date($post['date']),
                        'deleted'       => 0
                    ];
                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                    foreach($after_balance  AS $info)
                    {                                
                        $this->crud->update('general_ledger', ['balance' => sub_balance($info['balance'], $amount)], ['id' => $info['id']]);
                    }
                }
                // CASH LEDGER -> TO ACCOUNT                    
                $to_last_balance = $this->cash_ledger->get_last_balance($post['to_cl_type'], $post['to_account_id'], $post['date']);
                $to_balance = ($to_last_balance != null) ?  add_balance($to_last_balance['balance'], $amount) : add_balance(0, $amount);
                $data = [
                    'date'        => format_date($post['date']),
                    'cl_type'     => $post['to_cl_type'],
                    'account_id'  => $post['to_account_id'],
                    'information' => 'KAS&BANK MUTASI',
                    'invoice'     => $code,
                    'note'        => 'MUTASI_DARI_'.$from_account['name'],
                    'amount'      => $amount,
                    'method'      => 1,
                    'balance'     => $to_balance
                ];
                $to_cl_id = $this->crud->insert_id('cash_ledger', $data);
                $to_where_after_balance = [
                    'date >'        => format_date($post['date']),
                    'cl_type'       => $post['to_cl_type'],
                    'account_id'    => $post['to_account_id'],
                    'deleted'       => 0
                ];                    
                $to_after_balance = $this->db->select('*')->from('cash_ledger')->where($to_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                foreach($to_after_balance  AS $info)
                {
                    $this->crud->update('cash_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                }
                // GENERAL_LEDGER -> TO ACCOUNT
                $where_last_balance = [
                    'coa_account_code' => ($post['to_cl_type'] == 1) ? "10101" : "10102",
                    'date <='        => format_date($post['date']),
                    'deleted'        => 0
                ];
                $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : add_balance(0, $amount);                                                
                $data = [
                    'coa_account_code' => ($post['to_cl_type'] == 1) ? "10101" : "10102",
                    'date'        => format_date($post['date']),										
                    'transaction_id' => $to_cl_id,
                    'invoice'     => $code,
                    'information' => 'KAS&BANK MUTASI',
                    'note'        => 'MUTASI_DARI_'.$from_account['name'],
                    'debit'       => $amount,
                    'balance'     => $balance
                ];									
                if($this->crud->insert('general_ledger', $data))
                {
                    $where_after_balance = [
                        'coa_account_code' => ($post['to_cl_type'] == 1) ? "10101" : "10102",
                        'date >'        => format_date($post['date']),
                        'deleted'       => 0
                    ];
                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                    foreach($after_balance  AS $info)
                    {                                
                        $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                    }
                }
                $this->crud->update('cash_ledger', ['transaction_id' => $from_cl_id, 'to_cl_id' => $to_cl_id], ['id' => $from_cl_id]);
                $this->crud->update('cash_ledger', ['transaction_id' => $to_cl_id, 'from_cl_id' => $from_cl_id], ['id' => $to_cl_id]);
                $this->session->set_flashdata('success', 'BERHASIL! Transaksi Kas&Bank Mutasi berhasil tersimpan');
                redirect(site_url('cash_ledger/mutation'));
            }
            else
            {
                $header = array("title" => "Kas&Bank Mutasi Baru");
                $footer = array("script" => ['finance/cash_ledger/create_cash_ledger_mutation.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('cash_ledger/create_cash_ledger_mutation');
                $this->load->view('include/footer', $footer);                
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }    

    public function detail_cash_ledger_mutation($cl_id)
    {
        if($this->system->check_access('cash_ledger/mutation', 'detail'))
        {
            $cash_ledger = $this->cash_ledger->get_detail_cash_ledger_mutation(decrypt_custom($cl_id));
            $header = array("title" => "Detail Kas&Bank Mutasi");
            $data = [
                'cash_ledger' => $cash_ledger,
            ];
            $footer = array("script" => ['finance/cash_ledger/detail_cash_ledger_mutation.js']);
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('cash_ledger/detail_cash_ledger_mutation', $data);
            $this->load->view('include/footer', $footer);             
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function delete_cash_ledger_mutation()
    {
		if($this->input->is_ajax_request())
		{
			$id     = $this->input->post('id');
            $detail = $this->crud->get_where('cash_ledger', ['id' => $id])->row_array();
            // GENERAL LEDGER
            $where_general_ledger = [
                'invoice'		=> $detail['invoice']
            ];
            $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger);
            if($general_ledger->num_rows() > 0)
            {
                foreach($general_ledger->result_array() AS $info_general_ledger)
                {
                    $where_after_balance = [
                        'coa_account_code'=> $info_general_ledger['coa_account_code'],
                        'date >='    => $info_general_ledger['date'],
                        'deleted'    => 0
                    ];
                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                    foreach($after_balance AS $info_after_balance)
                    {
                        if($info_after_balance['date'] == $info_general_ledger['date'] && $info_after_balance['id'] < $info_general_ledger['id'])
                        {
                            continue;
                        }
                        else
                        {
                            $coa_category = substr($info_general_ledger['coa_account_code'], 0, 1);
                            if(in_array($coa_category, [1]))
                            {
                                if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
                                {
                                    $balance = $info_after_balance['balance']-$info_general_ledger['debit'];
                                }
                                elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
                                {
                                    $balance = $info_after_balance['balance']+$info_general_ledger['credit'];
                                }
                            }
                            elseif(in_array($coa_category, [2]))
                            {
                                if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
                                {
                                    $balance = $info_after_balance['balance']+$info_general_ledger['debit'];
                                }
                                elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
                                {
                                    $balance = $info_after_balance['balance']-$info_general_ledger['credit'];
                                }
                            }
                            $this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
                        }
                    }
                    $this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
                }
            }
            // CASH LEDGER
            $where_after_balance = [
                'cl_type'    => $detail['cl_type'],
                'account_id' => $detail['account_id'],
                'date >='    => $detail['date'],                
                'deleted'    => 0
            ];
            $data = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
            foreach($data AS $info)
            {
                if($info['date'] == $detail['date'] && $info['id'] < $detail['id'])
                {
                    continue;
                }
                else
                {
                    if($detail['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                    {
                        $balance = $info['balance']-$detail['amount'];
                    }
                    else
                    {
                        $balance = $info['balance']+$detail['amount'];
                    }
                    $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                }
            }
            $this->crud->delete_by_id('cash_ledger', $id);
            if($detail['from_cl_id'] != null || $detail['to_cl_id'] != null)
            {
                $other_cl_id  = $detail['from_cl_id'] != null ? $detail['from_cl_id'] : $detail['to_cl_id'];
                $other_detail = $this->crud->get_where('cash_ledger', ['id' => $other_cl_id])->row_array();
                $other_where_after_balance = [
                    'cl_type'    => $other_detail['cl_type'],
                    'account_id' => $other_detail['account_id'],
                    'date >='    => $other_detail['date'],
                    'deleted'    => 0
                ];
                $data   = $this->db->select('*')->from('cash_ledger')->where($other_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                foreach($data AS $info)
                {
                    if($info['date'] == $other_detail['date'] && $info['id'] < $other_detail['id'])
                    {
                        continue;
                    }
                    else
                    {
                        if($other_detail['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                        {
                            $balance = $info['balance']-$other_detail['amount'];
                        }
                        else
                        {
                            $balance = $info['balance']+$other_detail['amount'];
                        }
                        $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                    }
                }
                $this->crud->delete_by_id('cash_ledger', $other_cl_id);
            }

            $data_activity = [
                'information' => 'MENGHAPUS KAS&BANK Mutasi',
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
            $this->session->set_flashdata('success', 'BERHASIL! Kas&Bank Mutasi Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    } 

    public function add()
    {
        if($this->input->is_ajax_request())
		{
            $post   = $this->input->post();
            $this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', 'Jumlah Nominal', 'trim|required|xss_clean');
            $this->form_validation->set_rules('transaction_type', 'Jenis Penambahan', 'trim|required|xss_clean');
            $this->form_validation->set_rules('note', 'Catatan', 'trim|xss_clean');
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
                // TO ACCOUNT
                $last_balance = $this->cash_ledger->get_last_balance($post['to_cl_type'], $post['to_account_id'], $post['date']);
                $balance      = ($last_balance != null) ?  $last_balance['balance']+format_amount($post['amount']) : format_amount($post['amount']);
                $information  = $post['transaction_type'] == 1 ? "DEPOSIT" : "MUTASI";
                $data = [
                    'cl_type'     => $post['to_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
                    'transaction_type' => $post['transaction_type'],
                    'account_id'  => $post['to_account_id'],
                    'date'        => format_date($post['date']),
                    'invoice'     => "-",
                    'information' => $information,
                    'note'        => $post['note'],
                    'amount'      => format_amount($post['amount']),
                    'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
                    'balance'     => $balance
                ];
                $to_cl_id = $this->crud->insert_id('cash_ledger', $data);
                if($to_cl_id)
                {
                    $to_where_after_balance = [
                        'cl_type'       => $post['to_cl_type'],
                        'account_id'    => $post['to_account_id'],
                        'date >'        => format_date($post['date']),
                        'deleted'       => 0
                    ];                    
                    $to_after_balance = $this->db->select('*')->from('cash_ledger')->where($to_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                    foreach($to_after_balance  AS $info)
                    {                        
                        $balance = $info['balance']+format_amount($post['amount']);                        
                        $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                    }

                    // FROM ACCOUNT
                    if($post['transaction_type'] == 2)
                    {                        
                        $from_last_balance = $this->cash_ledger->get_last_balance($post['from_cl_type'], $post['from_account_id'], $post['date']);
                        $from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-format_amount($post['amount']) : 0-format_amount($post['amount']);
                        $data = [
                            'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
                            'transaction_type' => 2,
                            'account_id'  => $post['from_account_id'],
                            'date'        => format_date($post['date']),
                            'invoice'     => "-",
                            'information' => $information,
                            'note'        => $post['note'],
                            'amount'      => format_amount($post['amount']),
                            'method'      => 2, // 1:DEBIT (IN), 2:KREDIT (OUT)
                            'balance'     => $from_balance
                        ];
                        $from_cl_id = $this->crud->insert_id('cash_ledger', $data);
                        if($from_cl_id)
                        {
                            $from_where_after_balance = [
                                'cl_type'       => $post['from_cl_type'],
                                'account_id'    => $post['from_account_id'],
                                'date >'        => format_date($post['date']),
                                'deleted'       => 0
                            ];
                            $from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                            foreach($from_after_balance  AS $info)
                            {                        
                                $balance = $info['balance']-format_amount($post['amount']);
                                $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                            }                            
                        }
                        $this->crud->update('cash_ledger', ['to_cl_id' => $to_cl_id], ['id' => $from_cl_id]);
                        $this->crud->update('cash_ledger', ['from_cl_id' => $from_cl_id], ['id' => $to_cl_id]);
                    }

                    $data_activity = [
                        'information' => $information,
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
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}             
        
    }    

    // public function add_deposit()
    // {
    //     if($this->input->is_ajax_request())
	// 	{
    //         $post   = $this->input->post();
    //         $this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('to_account_id', 'Pelanggan', 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('amount', 'Jumlah Nominal', 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('note', 'Catatan', 'trim|xss_clean');
    //         $this->form_validation->set_message('required', 'Maaf! <b>%s</b> Tidak Boleh Kosong');                  
    //         if($this->form_validation->run() == FALSE)
    //         {
    //             $response = [
    //                 'status' => [
    //                     'code'      => 401,
    //                     'message'   => validation_errors(),
    //                 ],
    //                 'response'  => ''
    //             ];
    //         }
    //         else
    //         {
    //             $to_account   = ($post['deposit_type'] == 1) ? $this->crud->get_where('supplier', ['code' => $post['to_account_id']])->row_array() : $this->crud->get_where('customer', ['code' => $post['to_account_id']])->row_array();                
    //             $from_account = ($post['from_cl_type'] != 3) ? $this->crud->get_where('employee', ['code' => $post['from_account_id']])->row_array() : $this->crud->get_where('bank_account', ['id' => $post['from_account_id']])->row_array();
    //             switch ($post['from_cl_type']) {
    //                 case 1:
    //                     $note = "KAS BESAR | ".$from_account['name'];
    //                     break;
    //                 case 2:
    //                     $note = "KAS KECIL | ".$from_account['name'];
    //                     break;
    //                 case 3:
    //                     $note = "KAS BANK | ".$from_account['number'].' '.$from_account['name'];
    //                     break;
    //                 default:
    //                     $note = "-";
    //             }
    //             // TO ACCOUNT
    //             $last_balance = $this->cash_ledger->get_last_balance($post['to_cl_type'], $post['to_account_id'], $post['date']);
    //             $balance = ($last_balance != null) ?  $last_balance['balance']+format_amount($post['amount']) : format_amount($post['amount']);
    //             $data = [
    //                 'cl_type'     => $post['to_cl_type'],
    //                 'transaction_type' => 1,
    //                 'account_id'  => $post['to_account_id'],
    //                 'date'        => format_date($post['date']),
    //                 'invoice'     => "-",
    //                 'information' => 'DEPOSIT',
    //                 'note'        => $note,
    //                 'amount'      => format_amount($post['amount']),
    //                 'method'      => 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
    //                 'balance'     => $balance
    //             ];
    //             $to_cl_id = $this->crud->insert_id('cash_ledger', $data);
    //             if($to_cl_id)
    //             {
    //                 $to_where_after_balance = [
    //                     'cl_type'       => $post['to_cl_type'],
    //                     'date >'        => format_date($post['date']),
    //                     'account_id'    => $post['to_account_id'],
    //                     'deleted'       => 0
    //                 ];                    
    //                 $to_after_balance = $this->db->select('*')->from('cash_ledger')->where($to_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
    //                 foreach($to_after_balance  AS $info)
    //                 {                        
    //                     $balance = $info['balance']+format_amount($post['amount']);                        
    //                     $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
    //                 }    
    //                 // FROM ACCOUNT
    //                 $from_last_balance = $this->cash_ledger->get_last_balance($post['from_cl_type'], $post['from_account_id'], $post['date']);
    //                 $information = ($post['deposit_type'] == 1) ? "DEPOSIT SUPPLIER" : "DEPOSIT PELANGGAN";                              
    //                 if($post['deposit_type'] == 1)
    //                 {
    //                     $from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-format_amount($post['amount']) : format_amount($post['amount']);
    //                 }
    //                 else
    //                 {
    //                     $from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']+format_amount($post['amount']) : format_amount($post['amount']);
    //                 }                    
    //                 $data = [
    //                     'cl_type'     => $post['from_cl_type'],
    //                     'transaction_type' => 1,
    //                     'account_id'  => $post['from_account_id'],
    //                     'date'        => date('Y-m-d', strtotime($post['date'])),
    //                     'invoice'     => "-",
    //                     'information' => $information,
    //                     'note'        => $to_account['name'],
    //                     'amount'      => format_amount($post['amount']),
    //                     'method'      => ($post['deposit_type'] == 1) ? 2: 1, // 1:DEBIT (IN), 2:KREDIT (OUT)
    //                     'balance'     => $from_balance
    //                 ];
    //                 $from_cl_id = $this->crud->insert_id('cash_ledger', $data);
    //                 if($from_cl_id)
    //                 {
    //                     $from_where_after_balance = [
    //                         'cl_type'       => $post['from_cl_type'],
    //                         'account_id'    => $post['from_account_id'],
    //                         'date >'        => date('Y-m-d', strtotime($post['date'])),
    //                         'deleted'       => 0
    //                     ];                    
    //                     $from_after_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
    //                     if($post['deposit_type'] == 1)
    //                     {
    //                         foreach($from_after_balance  AS $info)
    //                         {                        
    //                             $balance = $info['balance']-format_amount($post['amount']);
    //                             $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
    //                         }
    //                     }
    //                     else
    //                     {
    //                         foreach($from_after_balance  AS $info)
    //                         {                        
    //                             $balance = $info['balance']+format_amount($post['amount']);
    //                             $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
    //                         }
    //                     }                                                 
    //                 }
    //                 $this->crud->update('cash_ledger', ['to_cl_id' => $to_cl_id], ['id' => $from_cl_id]);
    //                 $this->crud->update('cash_ledger', ['from_cl_id' => $from_cl_id], ['id' => $to_cl_id]);
    //                 $data_activity = [
    //                     'information' => $information,
    //                     'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
    //                     'code_e'      => $this->session->userdata('code_e'),
    //                     'name_e'      => $this->session->userdata('name_e'),
    //                     'user_id'     => $this->session->userdata('id_u')
    //                 ];						
    //                 $this->crud->insert('activity', $data_activity);
    //                 $response = [
    //                     'status' => [
    //                         'code'      => 200,
    //                         'message'   => 'Berhasil Menambahkan Data',
    //                     ],
    //                     'response'  => ''
    //                 ];                    
    //             }
    //             else
    //             {
    //                 $response = [
    //                     'status' => [
    //                         'code'      => 401,
    //                         'message'   => 'Gagal Menambahkan Data',
    //                     ],
    //                     'response'  => ''
    //                 ];                    
    //             }
	// 		}
	// 		echo json_encode($response);
	// 	}
	// 	else
	// 	{
	// 		$this->load->view('auth/show_404');
	// 	}
    // }

    public function delete()
    {
		if($this->input->is_ajax_request())
		{
			$id     = $this->input->get('id');
            $detail = $this->crud->get_where('cash_ledger', ['id' => $id])->row_array();
            // GENERAL LEDGER
            $where_general_ledger = [
                'invoice'		=> $detail['invoice']
            ];
            $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger);
            if($general_ledger->num_rows() > 0)
            {
                foreach($general_ledger->result_array() AS $info_general_ledger)
                {
                    $where_after_balance = [
                        'coa_account_code'=> $info_general_ledger['coa_account_code'],
                        'date >='    => $info_general_ledger['date'],
                        'deleted'    => 0
                    ];
                    $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                    foreach($after_balance AS $info_after_balance)
                    {
                        if($info_after_balance['date'] == $info_general_ledger['date'] && $info_after_balance['id'] < $info_general_ledger['id'])
                        {
                            continue;
                        }
                        else
                        {
                            $coa_category = substr($info_general_ledger['coa_account_code'], 0, 1);
                            if(in_array($coa_category, [1]))
                            {
                                if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
                                {
                                    $balance = $info_after_balance['balance']-$info_general_ledger['debit'];
                                }
                                elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
                                {
                                    $balance = $info_after_balance['balance']+$info_general_ledger['credit'];
                                }
                            }
                            elseif(in_array($coa_category, [2]))
                            {
                                if($info_general_ledger['debit'] != 0 && $info_general_ledger['credit'] == 0)
                                {
                                    $balance = $info_after_balance['balance']+$info_general_ledger['debit'];
                                }
                                elseif($info_general_ledger['debit'] == 0 && $info_general_ledger['credit'] != 0)
                                {
                                    $balance = $info_after_balance['balance']-$info_general_ledger['credit'];
                                }
                            }
                            $this->crud->update('general_ledger', ['balance' => $balance], ['id' => $info_after_balance['id']]);
                        }
                    }
                    $this->crud->delete_by_id('general_ledger', $info_general_ledger['id']);
                }
            }
            // CASH LEDGER
            $where_after_balance = [
                'cl_type'    => $detail['cl_type'],
                'account_id' => $detail['account_id'],
                'date >='    => $detail['date'],                
                'deleted'    => 0
            ];
            $data = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
            foreach($data AS $info)
            {
                if($info['date'] == $detail['date'] && $info['id'] < $detail['id'])
                {
                    continue;
                }
                else
                {
                    if($detail['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                    {
                        $balance = $info['balance']-$detail['amount'];
                    }
                    else
                    {
                        $balance = $info['balance']+$detail['amount'];
                    }
                    $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                }
            }
            $this->crud->delete_by_id('cash_ledger', $id);
            if($detail['from_cl_id'] != null || $detail['to_cl_id'] != null)
            {
                $other_cl_id  = $detail['from_cl_id'] != null ? $detail['from_cl_id'] : $detail['to_cl_id'];
                $other_detail = $this->crud->get_where('cash_ledger', ['id' => $other_cl_id])->row_array();
                $other_where_after_balance = [
                    'cl_type'    => $other_detail['cl_type'],
                    'account_id' => $other_detail['account_id'],
                    'date >='    => $other_detail['date'],
                    'deleted'    => 0
                ];
                $data   = $this->db->select('*')->from('cash_ledger')->where($other_where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                foreach($data AS $info)
                {
                    if($info['date'] == $other_detail['date'] && $info['id'] < $other_detail['id'])
                    {
                        continue;
                    }
                    else
                    {
                        if($other_detail['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                        {
                            $balance = $info['balance']-$other_detail['amount'];
                        }
                        else
                        {
                            $balance = $info['balance']+$other_detail['amount'];
                        }
                        $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                    }
                }
                $this->crud->delete_by_id('cash_ledger', $other_cl_id);
            }

            $data_activity = [
                'information' => 'MENGHAPUS DATA BUKU KAS',
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
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }    

    // OTHER FUNCTION
    public function last_balance()
    {
        if($this->input->is_ajax_request())
		{
            $last_balance = $this->db->select('*')->from('bank_cash')->where('deleted', 0)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
            $result = array(
                'last_balance' => number_format($last_balance['balance'],'2','.',',')
            );
            echo json_encode($result); 
		}
		else
		{
			$this->load->view('auth/show_404');
		}               
    }

    public function update()
    {
        if($this->system->check_access('expense', 'update'))
        {            
            $post   = $this->input->post();
            $this->form_validation->set_rules('expense_id', 'ID Expense', 'trim|required|xss_clean');
            $this->form_validation->set_rules('date', 'Tanggal', 'trim|required|xss_clean');
            $this->form_validation->set_rules('cost_id', 'Biaya', 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', 'Besar Biaya', 'trim|required|xss_clean');
            $this->form_validation->set_rules('invoice', 'Nomor Bukti', 'trim|xss_clean');
            $this->form_validation->set_rules('information', 'Keterangan', 'trim|xss_clean');
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
                $data = [
                    'date'      => date('Y-m-d', strtotime($post['date'])),
                    'cost_id'   => $post['cost_id'],
                    'amount'    => format_amount($post['amount']),
                    'invoice'   => $post['invoice'],
                    'information'   => $post['information'],
                ];
                $id     = $post['expense_id'];
                $update = $this->crud->update_by_id('expense', $data, $id);
                if($update)
                {
                    $data_activity = array (
                        'information' => 'MEMPERBAHARUI DATA POSTING BIAYA ( ID - '.$id.')',
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
}