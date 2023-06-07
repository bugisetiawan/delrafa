<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Expense_model', 'expense');
	}		     

    public function total()
    {
        if($this->input->is_ajax_request())
		{
            $expense = $this->db->select('expense.id, date, cost.name AS name_c, amount, expense.code, expense.information, employee.name AS name_e')
                                        ->from('expense')
                                        ->join('cost', 'cost.id = expense.cost_id')
                                        ->join('employee', 'employee.code = expense.employee_code')
                                        ->where('expense.deleted', 0)
                                        ->where('MONTH(expense.date)', date('m'))->get()->result_array();
            $grandtotal = 0;
            foreach($expense AS $info_expense)
            {
                $grandtotal = $grandtotal+$info_expense['amount'];
            }
            $result = array(
                'grandtotal'         => number_format($grandtotal, 2, ".", ",")
            );
            header('Content-Type: application/json');
            echo json_encode($result);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    public function index()
    {
        if($this->system->check_access('finance/expense', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $this->datatables->select('expense.id, date, cost.name AS name_c, amount, expense.code, expense.information, employee.name AS name_e');
                $this->datatables->from('expense');
                $this->datatables->join('cost', 'cost.id = expense.cost_id');
                $this->datatables->join('employee', 'employee.code = expense.employee_code');
                $this->datatables->where('expense.deleted', 0);
                $this->datatables->where('MONTH(expense.date)', date('m'));
                $this->datatables->add_column('view', 
                '            
                    <a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                        <i class="fa fa-times"> Hapus</i>
                    </a>            
                ', 'id');
                header('Content-Type: application/json');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Biaya");
                $footer = array("script" => ['finance/expense.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('expense/expense');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function get_cost()
    {
        $data       = $this->crud->get_where('cost', ['deleted' => '0'])->result();
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

	public function add()
    {
        if($this->system->check_access('finance/expense', 'create'))
        {
            $post   = $this->input->post();
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
                $expense_code = $this->expense->expense_code();
                $cost = $this->crud->get_where('cost', ['id' => $post['cost_id']])->row_array();
                $amount = format_amount($post['amount']);
                $data = [
                    'date'        => format_date($post['date']),
                    'cost_id'     => $post['cost_id'],
                    'code'        => $expense_code,
                    'amount'      => $amount,
                    'information' => $post['information'],
                    'employee_code' => $this->session->userdata('code_e')
                ];
                $expense_id = $this->crud->insert_id('expense', $data);
                if($expense_id != null)
                {
                    // GENERAL_LEDGER -> EXPENSE (D)
                    $where_last_balance = [
                        'coa_account_code' => $cost['code'],
                        'date <='          => format_date($post['date']),
                        'deleted'          => 0
                    ];
                    $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                    $balance = ($last_balance != null) ?  add_balance($last_balance['balance'], $amount) : add_balance(0, $amount);
                    $data = [
                        'coa_account_code' => $cost['code'],
                        'date'        => format_date($post['date']),										
                        'transaction_id' => $expense_id,
                        'invoice'     => $expense_code,
                        'information' => 'PENGELUARAN BIAYA',
                        'note'		  => 'PENGELUARAN_BIAYA_'.$expense_code,
                        'debit'      => $amount,
                        'balance'     => $balance
                    ];
                    if($this->crud->insert('general_ledger', $data))
                    {
                        $where_after_balance = [
                            'coa_account_code' => $cost['code'],
                            'date >'        => format_date($post['date']),
                            'deleted'       => 0
                        ];
                        $after_balance = $this->db->select('*')->from('general_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                        foreach($after_balance  AS $info)
                        {
                            $this->crud->update('general_ledger', ['balance' => add_balance($info['balance'], $amount)], ['id' => $info['id']]);
                        }                            
                    }
                    // GENERAL_LEDGER -> KAS & BANK (K)
                    $where_last_balance = [
                        'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                        'date <='          => format_date($post['date']),
                        'deleted'          => 0
                    ];
                    $last_balance = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
                    $balance = ($last_balance != null) ?  sub_balance($last_balance['balance'], $amount) : sub_balance(0, $amount);
                    $data = [
                        'coa_account_code' => ($post['from_cl_type'] == 1) ? "10101" : "10102",
                        'date'        => format_date($post['date']),										
                        'transaction_id' => $expense_id,
                        'invoice'     => $expense_code,
                        'information' => 'PENGELUARAN BIAYA',
                        'note'		  => 'PENGELUARAN_BIAYA_'.$expense_code,
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
                    // CASH LEDGER
                    $from_where_last_balance = [
                        'cl_type'    => $post['from_cl_type'],
                        'account_id' => $post['from_account_id'],
                        'date <='    => format_date($post['date']),                    
                        'deleted'    => 0
                    ];
                    $from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
                    $from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-format_amount($post['amount']) : 0-format_amount($post['amount']);
                    $data = [
                        'date'        => format_date($post['date']),
                        'cl_type'     => $post['from_cl_type'],
                        'account_id'  => $post['from_account_id'],
                        'information' => 'PENGELUARAN BIAYA',
                        'transaction_id' => $expense_id,
                        'invoice'     => $expense_code,
                        'note'        => 'PENGELUARAN_BIAYA_'.$expense_code,                    
                        'amount'      => format_amount($post['amount']),
                        'method'      => 2,
                        'balance'     => $from_balance
                    ];
                    $from_cl_id = $this->crud->insert_id('cash_ledger', $data);
                    if($from_cl_id)
                    {
                        $where_after_balance = [
                            'cl_type'       => $post['from_cl_type'],
                            'account_id'    => $post['from_account_id'],
                            'date >'        => format_date($post['date']),
                            'deleted'       => 0
                        ];                    
                        $after_balance = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
                        foreach($after_balance  AS $info)
                        {                        
                            $balance = $info['balance'] - format_amount($post['amount']);
                            $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                        }                            
                    }
                    $data_activity = [
                        'information' => 'MEMBUAT PENGELUARAN BIAYA',
                        'method'      => 3, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];
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
    
	function get_detail_expense()
	{
        if($this->input->is_ajax_request())
		{
            $expense_id =$this->input->get('id');
            $where_cash_ledger = [
                'transaction_type' => 7,
                'transaction_id' => $expense_id
            ];
            $data = [
                'expense' => $this->crud->get_by_id('expense', $expense_id)->row_array(),
                'cash_ledger' => $this->crud->get_where('cash_ledger', $where_cash_ledger)->row_array()   
            ];            
            echo json_encode($data);
		}
		else
		{
			$this->load->view('auth/show_404');
		}        
    }
    
    public function update()
    {
        if($this->system->check_access('finance/expense', 'update'))
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
                $expense_id = $post['expense_id'];
                $data = [
                    'date'      => date('Y-m-d', strtotime($post['date'])),
                    'cost_id'   => $post['cost_id'],
                    'amount'    => format_amount($post['amount']),
                    'invoice'   => $post['invoice'],
                    'information'   => $post['information'],
                ];                
                if($this->crud->update_by_id('expense', $data, $expense_id))
                {
                    $where_cash_ledger = [
                        'transaction_type'=> 7,
                        'transaction_id'  => $expense_id,
                    ];
                    $cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->result_array();
                    if($cash_ledger)
                    {
                        foreach($cash_ledger AS $info_cash_ledger)
                        {
                            $where_after_balance = [
                                'cl_type'    => $info_cash_ledger['cl_type'],
                                'account_id' => $info_cash_ledger['account_id'],
                                'date >='    => $info_cash_ledger['date'],                
                                'deleted'    => 0
                            ];
                            $data   = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
                            foreach($data AS $info)
                            {
                                if($info['date'] == $info_cash_ledger['date'] && $info['id'] < $info_cash_ledger['id'])
                                {
                                    continue;
                                }
                                else
                                {
                                    if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                                    {
                                        $balance = $info['balance'] - $info_cash_ledger['amount'];
                                    }
                                    else
                                    {
                                        $balance = $info['balance'] + $info_cash_ledger['amount'];
                                    }
                                    $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                                }
                            }
                            $this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
                        }
                    }
                    $from_where_last_balance = [
                        'cl_type'    => $post['from_cl_type'],
                        'account_id' => $post['from_account_id'],
                        'date <='    => format_date($post['date']),                    
                        'deleted'    => 0
                    ];
                    $from_last_balance = $this->db->select('*')->from('cash_ledger')->where($from_where_last_balance)->order_by('date', 'desc')->order_by('id', 'desc')->limit(1)->get()->row_array();
                    $from_balance = ($from_last_balance != null) ?  $from_last_balance['balance']-format_amount($post['amount']) : 0-format_amount($post['amount']);
                    $data = [
                        'cl_type'     => $post['from_cl_type'], //1:BIG CASH, 2:SMALL CASH, 3:BANK CASH
                        'account_id'  => $post['from_account_id'],
                        'transaction_type' => 7, //1:DEPOSIT, 2:CASH MUTATION, 3:PURCHASE, 4:PURCHASE RETURN, 5:SALES INVOICE, 6:SALES RETURN, 7: EXPENSE                        
                        'transaction_id' => $expense_id,
                        'invoice'     => $post['invoice'],
                        'information' => 'PENGELUARAN BIAYA',
                        'date'        => format_date($post['date']),                                            
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
                        $from_after_big_cash = $this->db->select('*')->from('cash_ledger')->where($from_where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
                        foreach($from_after_big_cash  AS $info)
                        {                        
                            $balance = $info['balance'] - format_amount($post['amount']);
                            $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                        }
                    }
                    $data_activity = [
                        'information' => 'MEMPERBARUI PENGELUARAN BIAYA',
                        'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
                        'code_e'      => $this->session->userdata('code_e'),
                        'name_e'      => $this->session->userdata('name_e'),
                        'user_id'     => $this->session->userdata('id_u')
                    ];						
                    $this->crud->insert('activity', $data_activity);
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
		if($this->system->check_access('finance/expense', 'delete'))
		{            
            $expense = $this->crud->get_where('expense', ['id' => $this->input->get('id')])->row_array();
			if($this->crud->delete_by_id('expense', $expense['id']))
			{
                // GENERAL LEDGER
                $where_general_ledger = [
                    'invoice'		=> $expense['code']
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
                // CASH LEDGER
                $where_cash_ledger = [
                    'transaction_id'  => $expense['id'],
                    'invoice'         => $expense['code'],
                ];
                $cash_ledger = $this->crud->get_where('cash_ledger', $where_cash_ledger)->result_array();
                foreach($cash_ledger AS $info_cash_ledger)
                {
                    $where_after_balance = [
                        'cl_type'    => $info_cash_ledger['cl_type'],
                        'account_id' => $info_cash_ledger['account_id'],
                        'date >='    => $info_cash_ledger['date'],                
                        'deleted'    => 0
                    ];
                    $data   = $this->db->select('*')->from('cash_ledger')->where($where_after_balance)->order_by('date', 'asc')->order_by('id', 'asc')->get()->result_array();
                    foreach($data AS $info)
                    {
                        if($info['date'] == $info_cash_ledger['date'] && $info['id'] < $info_cash_ledger['id'])
                        {
                            continue;
                        }
                        else
                        {
                            if($info_cash_ledger['method'] == 1) //1:DEBIT (IN), 2:CREDIT (OUT)
                            {
                                $balance = $info['balance'] - $info_cash_ledger['amount'];
                            }
                            else
                            {
                                $balance = $info['balance'] + $info_cash_ledger['amount'];
                            }
                            $this->crud->update('cash_ledger', ['balance' => $balance], ['id' => $info['id']]);
                        }
                    }
                    $this->crud->delete_by_id('cash_ledger', $info_cash_ledger['id']);
                }
                $data_activity = [
                    'information' => 'MENGHAPUS PENGELUARAN BIAYA',
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
}