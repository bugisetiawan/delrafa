<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Accounting_model', 'accounting');
    }
    
    // GENERAL FUNCTION
    public function get_coa_account()
    {
		if($this->input->is_ajax_request())
		{
			$data = $this->crud->get_where('coa_account', ['is_active' => 1, 'deleted' => '0'])->result();
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
    
    // GENERAL LEDGER
    public function general_ledger()
    {
        if($this->system->check_access('general_ledger', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $post = $this->input->post();
                if($post['coa_account_code'] != null)
                {
                    $this->datatables->select('gl.id, gl.date, coa_account.code, coa_account.name, gl.note, gl.debit, gl.credit, gl.balance');
                    $this->datatables->from('general_ledger AS gl');
                    $this->datatables->join('coa_account', 'coa_account.code = gl.coa_account_code');
                    $this->datatables->where('gl.deleted', 0);
                    $this->datatables->where('gl.coa_account_code', $post['coa_account_code']);
                    $this->datatables->group_by('gl.id');
                    $this->datatables->order_by('gl.date', 'DESC');
                    $this->datatables->order_by('gl.id','DESC');
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
                $header = array("title" => "Buku Besar");
                $footer = array("script" => ['finance/accounting/general_ledger/general_ledger.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('accounting/general_ledger/general_ledger');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    // COA ACCOUNT
    public function coa_account()
    {
        if($this->system->check_access('coa_account', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                header('Content-Type: application/json');
                $this->datatables->select('coa_account.id AS id_coa_account,
                                coa_category.name AS name_coa_category,
                                coa_subcategory.name AS name_coa_subcategory,
                                coa_account.code AS code_coa_account, coa_account.name AS name_coa_account');
                $this->datatables->from('coa_account');
                $this->datatables->join('coa_category', 'coa_category.code = coa_account.coa_category_code');
                $this->datatables->join('coa_subcategory', 'coa_subcategory.code = coa_account.coa_subcategory_code');
                $this->datatables->group_by('coa_account.id');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Daftar COA");
                $footer = array("script" => ['finance/accounting/coa/coa_account.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('accounting/coa/coa_account');
                $this->load->view('include/footer', $footer);			
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        } 
    }

    // JOURNAL
    public function journal()
    {
        if($this->system->check_access('journal', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                header('Content-Type: application/json');
                $this->datatables->select('journal.id, journal.date, journal.code, journal.total_debit, journal.total_credit, journal.information, employee.code AS code_e, employee.name AS name_e');
                $this->datatables->from('journal');
                $this->datatables->join('employee', 'employee.code = journal.employee_code');
                $this->datatables->where('journal.deleted', 0);
                $this->datatables->add_column('code', 
				'
					<a class="kt-font-primary kt-link text-center" href="'.site_url('journal/detail/$1').'"><b>$2</b></a>
				', 'encrypt_custom(id), code');                
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Jurnal Umum");
                $footer = array("script" => ['finance/accounting/journal/journal.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('journal/journal');
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
			$data   = $this->accounting->get_account($search);
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

    public function create_journal()
    {
        if($this->system->check_access('journal', 'create'))
        {
            if($this->input->method() === 'post')
            {                    
                $post = $this->input->post();
                $code = $this->accounting->journal_code();
                // JOURNAL
                $journal = [
                    'code' => $code,
                    'date' => format_date($post['date']),
                    'information'   => $post['information'],
                    'total_debit'   => format_amount($post['total_debit']),
                    'total_credit'  => format_amount($post['total_credit']),
                    'employee_code' => $this->session->userdata('code_e')
                ];
                $journal_id = $this->crud->insert_id('journal', $journal);
                if($journal_id != null)
                {
                    foreach($post['account'] AS $info_account)
                    {
                        $coa_category = substr($info_account['coa_account_code'], 0, 1);
                        $debit = format_amount($info_account['debit']); $credit = format_amount($info_account['credit']);
                        // JOURNAL DETAIL
                        $journal_detail = [
                            'journal_id' => $journal_id,
                            'coa_account_code' => $info_account['coa_account_code'],
                            'debit'            => ($debit != "") ? $debit : 0,
                            'credit'           => ($credit != "") ? $credit : 0,
						];
                        $this->crud->insert('journal_detail', $journal_detail);
                        // GENERAL LEDGER
						$where_last_balance = [
							'coa_account_code' => $info_account['coa_account_code'],
							'date <='        => format_date($post['date']),
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
                            'date'              => format_date($post['date']),
							'coa_account_code'  => $info_account['coa_account_code'],
							'transaction_id'    => $journal_id,
							'invoice'     		=> $code,
							'information' 		=> 'JURNAL UMUM',
							'note'		  		=> 'JURNAL_UMUM_'.$code,
                            'debit'            => ($debit != "") ? $debit : 0,
                            'credit'           => ($credit != "") ? $credit : 0,
							'balance'     		=> $balance
						];									
						$this->crud->insert('general_ledger', $general_ledger);
						$where_after_balance = [
                            'coa_account_code' => $info_account['coa_account_code'],
                            'date >'           => format_date($post['date']),
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
                    $this->session->set_flashdata('success', 'SUKSES! Jurnal berhasil disimpan');
                    redirect(site_url('journal'));
                }
                else
                {
                    $this->session->set_flashdata('error', 'GAGAL! Mohon maaf, Jurnal tidak berhasil tersimpan');
                    redirect(site_url('journal'));
                }                
            } 
            else
            {
                $header = array("title" => "Jurnal Umum Baru");
                $footer = array("script" => ['finance/accounting/journal/create_journal.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('journal/create_journal');
                $this->load->view('include/footer', $footer);			
            }
            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function detail_journal($journal_id)
    {
        if($this->system->check_access('journal', 'detail'))
        {
            $header = array("title" => "Detail Jurnal Umum");
            $data   = [
                'journal' => $this->accounting->get_detail_journal(decrypt_custom($journal_id)),
                'journal_detail' => $this->accounting->get_detail_journal_detail(decrypt_custom($journal_id))
            ];
            $footer = array("script" => ['finance/accounting/journal/detail_journal.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('journal/detail_journal', $data);
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function delete_journal()
    {
        if($this->input->is_ajax_request())
		{
			$this->session->unset_userdata('verifypassword');
			$post    = $this->input->post();
			$journal = $this->accounting->get_detail_journal(decrypt_custom($post['journal_id']));
            $journal_detail = $this->accounting->get_detail_journal_detail($journal['id']);
            // GENERAL LEDGER
            $where_general_ledger = [
                'invoice'		=> $journal['code']
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
            // DELETE JOURNAL_DETAIL
            $this->crud->delete('journal_detail', ['journal_id' => $journal['id']]);
            // DELETE JOURNAL
            $this->crud->delete('journal', ['id' => $journal['id']]);
			$data_activity = [
				'information' => 'MENGHAPUS JURNAL UMUM',
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
			$this->session->set_flashdata('success', 'BERHASIL! Jurnal Terhapus');
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    // TRANSACTION'S JOURNAL
    public function detail_transaction_journal()
    {        
        if($this->input->is_ajax_request())
        {
            $post = $this->input->post();
        }
        else
        {
            $this->load->view('auth/show_404');
        }
    }
}