<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model 
{		
    public function get_transaction($transaction_type, $account_code, $search)
    {
        if($transaction_type == 1) //PURCHASE INVOICE
        {
            return $this->db->select('id, date, code, invoice, grandtotal, account_payable')
                     ->from('purchase_invoice')
                     ->like('purchase_invoice.code', $search)
					 ->where('purchase_invoice.supplier_code', $account_code)
					 ->where('purchase_invoice.deleted', 0)->where('purchase_invoice.payment_status', 2)->where('purchase_invoice.account_payable >', 0)
                     ->or_like('purchase_invoice.invoice', $search)
					 ->where('purchase_invoice.supplier_code', $account_code)
					 ->where('purchase_invoice.deleted', 0)->where('purchase_invoice.payment_status', 2)->where('purchase_invoice.account_payable >', 0)
                     ->get();
        }
        elseif($transaction_type == 2) //SALES INVOICE
        {
			return $this->db->select('id, date, invoice, grandtotal, account_payable')
                     ->from('sales_invoice')
                     ->like('sales_invoice.invoice', $search)
					 ->where('sales_invoice.customer_code', $account_code)
					 ->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1)->where('sales_invoice.payment_status', 2)->where('sales_invoice.account_payable >', 0)
                     ->get();
        }
    }

	// PAYMENT OF DEBT
	public function pod_code()
	{
		$data = $this->db->select('code')->from('payment_ledger')->where('transaction_type', 1)->where('deleted', 0)->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "POD".date('d').date('m').$tahun; //POD121121001
		if($data)
		{
			$sub_tanggal = substr($data['code'], 3, 2);
			$no 		 = substr($data['code'], -3,3);
			if($sub_tanggal == date('d'))
			{
				$no++;
				$code = $format.sprintf("%03s", $no);
			}
			else
			{
				$no = 1;
				$code = $format.sprintf("%03s", $no);
			}
		}	
		else
		{
			$no = 1;
			$code = $format.sprintf("%03s", $no);
		}					 						 
		return $code;
	}	

	public function get_detail_pod($pod_id)
	{
		return $this->db->select('pod.*,
						employee.code AS code_e, employee.name AS name_e')		
						->from('payment_ledger AS pod')
						->join('employee', 'employee.code = pod.employee_code')
						->where('pod.id', $pod_id)
						->group_by('pod.id')
						->get()->row_array();
	}

	public function get_detail_pod_transaction($pod_id)
	{
		$pod_transactions = $this->db->select('supplier.name AS supplier, purchase_invoice.id AS id_pi, purchase_invoice.code AS code_pi, purchase_invoice.date, purchase_invoice.invoice, pod_transaction.disc_rp, pod_transaction.amount')
									 ->from('payment_ledger_transaction AS pod_transaction')
									 ->join('purchase_invoice', 'purchase_invoice.id = pod_transaction.transaction_id')
									 ->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
									 ->where('pod_transaction.pl_id', $pod_id)
									 ->group_by('pod_transaction.id')
									 ->order_by('pod_transaction.id', 'ASC')
									 ->get()->result_array();
		$data = [];
		foreach($pod_transactions AS $pod_transaction)
		{
			$data[] = [
				'supplier' => $pod_transaction['supplier'],
				'code_pi' => '<a class="kt-font-primary kt-link text-center" href="'.site_url('purchase/invoice/detail/'.encrypt_custom($pod_transaction['id_pi'])).'"><b>'.$pod_transaction['code_pi'].'</b></a>',
				'date' => date('d-m-Y', strtotime($pod_transaction['date'])),
				'invoice' => $pod_transaction['invoice'],
				'disc_rp' => number_format($pod_transaction['disc_rp'], 2, '.', ','),
				'amount' => number_format($pod_transaction['amount'], 2, '.', ',')
			];
		}
		return $data;
	}

	public function get_detail_pod_detail($pod_id)
	{
		$pod_details = $this->crud->get_where('payment_ledger_detail', ['pl_id' => $pod_id])->result_array();
		$data = [];
		foreach($pod_details AS $pod_detail)
		{			
			switch ($pod_detail['cheque_status']) {
				case 1:
					  $cheque_status = "<span class='kt-font-bold text-success'><i class='fa fa-check'></i></span>";
				  break;
				case 2:
					$cheque_status = "<span class='kt-font-bold text-warning'><i class='fa fa-exclamation-circle'></i></span>";
				  break;
				case 3:
					$cheque_status = "<span class='kt-font-bold text-danger'><i class='fa fa-times'></i></span>";
				  break;
				default:			
					$cheque_status = "-";
			}
			if(in_array("1",json_decode($pod_detail['method'])))
			{
				$method = 'TUNAI';
				$account_id = $this->crud->get_where('cash_ledger_account', ['type' => 1, 'id' => $pod_detail['account_id']])->row_array();
				$cheque_number = "-"; $cheque_open_date = "-"; $cheque_close_date = "-";
			}
			elseif(in_array("2",json_decode($pod_detail['method'])))
			{
				$method = 'TRANSFER';
				$account_id = $this->crud->get_where('cash_ledger_account', ['type' => 2, 'id' => $pod_detail['account_id']])->row_array();
				$cheque_number = "-"; $cheque_open_date = "-"; $cheque_close_date = "-";
			}
			elseif(in_array("3",json_decode($pod_detail['method'])))
			{
				$method = 'CEK/GIRO';
				$account_id = $this->crud->get_where('cash_ledger_account', ['type' => 2, 'id' => $pod_detail['account_id']])->row_array();				
				$cheque_number = $pod_detail['cheque_number'].' '.$cheque_status; $cheque_open_date = date('d-m-Y', strtotime($pod_detail['cheque_open_date'])); $cheque_close_date = date('d-m-Y', strtotime($pod_detail['cheque_close_date']));
			}
			elseif(in_array("4",json_decode($pod_detail['method'])))
			{
				$method = 'DEPOSIT';
				$account_id = $this->crud->get_where('cash_ledger_account', ['type' => 3, 'id' => $pod_detail['account_id']])->row_array();				
				$cheque_number = "-"; $cheque_open_date = "-"; $cheque_close_date = "-";
			}
			$data[] = [
				'id'	=> $pod_detail['id'],
				'method' => $method,
				'account_id' => $account_id['name'],
				'cheque_number' => $cheque_number,
				'cheque_open_date' => $cheque_open_date,
				'cheque_close_date' => $cheque_close_date,
				'cheque_acquittance_date' => $pod_detail['cheque_acquittance_date'],
				'cheque_status' => $pod_detail['cheque_status'],
				'amount' => number_format($pod_detail['amount'], 2, '.', ',')
			];
		}
		return $data;
	}

	// PAYMENT_OF_RECEIVABLE
	public function por_code()
	{
		$data = $this->db->select('code')->from('payment_ledger')->where('transaction_type', 2)->where('deleted', 0)->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "POR".date('d').date('m').$tahun; //POR121121001
		if($data)
		{
			$sub_tanggal = substr($data['code'], 3, 2);
			$no 		 = substr($data['code'], -3,3);
			if($sub_tanggal == date('d'))
			{
				$no++;
				$code = $format.sprintf("%03s", $no);
			}
			else
			{
				$no = 1;
				$code = $format.sprintf("%03s", $no);
			}
		}	
		else
		{
			$no = 1;
			$code = $format.sprintf("%03s", $no);
		}					 						 
		return $code;
	}
	
	public function get_detail_por($por_id)
	{
		return $this->db->select('por.*, 
					   	employee.code AS code_e, employee.name AS name_e')
						->from('payment_ledger AS por')
						->join('employee', 'employee.code = por.employee_code')
						->where('por.id', $por_id)
						->group_by('por.id')
						->get()->row_array();
	}

	public function get_detail_por_transaction($por_id)
	{
		$por_transactions = $this->db->select('customer.name AS customer, sales_invoice.id AS id_si, sales_invoice.date, sales_invoice.invoice AS code_si, por_transaction.disc_rp, por_transaction.amount')
									 ->from('payment_ledger_transaction AS por_transaction')
									 ->join('sales_invoice', 'sales_invoice.id = por_transaction.transaction_id')
									 ->join('customer', 'customer.code = sales_invoice.customer_code')
									 ->where('por_transaction.pl_id', $por_id)
									 ->group_by('por_transaction.id')
									 ->order_by('por_transaction.id', 'ASC')
									 ->get()->result_array();
		$data = [];
		foreach($por_transactions AS $por_transaction)
		{
			$data[] = [
				'customer' => $por_transaction['customer'],
				'code_si' => '<a class="kt-font-primary kt-link text-center" href="'.site_url('sales/invoice/detail/'.encrypt_custom($por_transaction['id_si'])).'"><b>'.$por_transaction['code_si'].'</b></a>',
				'date' => date('d-m-Y', strtotime($por_transaction['date'])),
				'disc_rp' => number_format($por_transaction['disc_rp'], 2, '.', ','),
				'amount' => number_format($por_transaction['amount'], 2, '.', ',')
			];
		}
		return $data;
	}
	
	public function get_detail_por_detail($por_id)
	{
		$por_details = $this->crud->get_where('payment_ledger_detail', ['pl_id' => $por_id])->result_array();
		$data = [];
		foreach($por_details AS $por_detail)
		{			
			switch ($por_detail['cheque_status']){
				case 1:
					  $cheque_status = "<span class='kt-font-bold text-success' data-container='body' data-toggle='kt-tooltip' data-placement='right' data-skin='dark' title='Lunas'><i class='fa fa-check'></i></span>";
				  break;
				case 2:
					$cheque_status = "<span class='kt-font-bold text-warning' data-container='body' data-toggle='kt-tooltip' data-placement='right' data-skin='dark' title='Belum Dikonfirmasi'><i class='fa fa-exclamation-circle'></i></span>";
				  break;
				case 3:
					$cheque_status = "<span class='kt-font-bold text-danger' data-container='body' data-toggle='kt-tooltip' data-placement='right' data-skin='dark' title='Ditolak'><i class='fa fa-times'></i></span>";
				  break;
				default:			
					$cheque_status = "-";
			}
			if(in_array("1",json_decode($por_detail['method'])))
			{
				$method = 'TUNAI';
				$account_id = $this->crud->get_where_select('name', 'cash_ledger_account', ['type' => 1, 'id' => $por_detail['account_id']])->row_array();
				$cheque_number = "-"; $cheque_open_date = "-"; $cheque_close_date = "-";
			}
			if(in_array("2",json_decode($por_detail['method'])))
			{
				$method = 'TRANSFER';
				$account_id = $this->crud->get_where_select('name', 'cash_ledger_account', ['type' => 2, 'id' => $por_detail['account_id']])->row_array();
				$cheque_number = "-"; $cheque_open_date = "-"; $cheque_close_date = "-";
			}
			if(in_array("3",json_decode($por_detail['method'])))
			{
				$method = 'CEK/GIRO';
				$account_id = $this->crud->get_where_select('name', 'cash_ledger_account', ['type' => 2, 'id' => $por_detail['account_id']])->row_array();				
				$cheque_number = $por_detail['cheque_number'].' '.$cheque_status; $cheque_open_date = date('d-m-Y', strtotime($por_detail['cheque_open_date'])); $cheque_close_date = date('d-m-Y', strtotime($por_detail['cheque_close_date']));
			}
			if(in_array("4",json_decode($por_detail['method'])))
			{
				$method = 'DEPOSIT';
				$account_id = $this->crud->get_where_select('name', 'customer', ['id' => $por_detail['account_id']])->row_array();
				$cheque_number = $por_detail['cheque_number'].' '.$cheque_status; $cheque_open_date = date('d-m-Y', strtotime($por_detail['cheque_open_date'])); $cheque_close_date = date('d-m-Y', strtotime($por_detail['cheque_close_date']));
			}

			$data[] = [
				'id'	=> $por_detail['id'],
				'method' => $method,
				'account_id' => $account_id['name'],
				'cheque_number' => $cheque_number,
				'cheque_open_date' => $cheque_open_date,
				'cheque_close_date' => $cheque_close_date,
				'cheque_acquittance_date' => $por_detail['cheque_acquittance_date'],
				'cheque_status' => $por_detail['cheque_status'],
				'amount' => number_format($por_detail['amount'], 2, '.', ',')
			];
		}
		return $data;
	}
}