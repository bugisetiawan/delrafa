<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model 
{		

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
						cash_account.name AS cash_account_name,
						transfer_account.name AS transfer_account_name,
						cheque_account.name AS cheque_account_name,
						employee.name AS name_e')
						->from('payment_ledger AS pod')
						->join('employee', 'employee.code = pod.employee_code')
						->join('cash_ledger_account AS cash_account', 'cash_account.id = pod.cash_account_id', 'left')
						->join('cash_ledger_account AS transfer_account', 'transfer_account.id = pod.transfer_account_id', 'left')
						->join('cash_ledger_account AS cheque_account', 'cheque_account.id = pod.cheque_account_id', 'left')
						->where('pod.id', $pod_id)
						->group_by('pod.id')
						->get()->row_array();
	}

	public function get_detail_pod_detail($pod_id)
	{
		return $this->db->select('pod_detail.*,
						purchase_invoice.date, purchase_invoice.code, purchase_invoice.invoice,
						supplier.name AS name_s')						
						->from('payment_ledger_detail AS pod_detail')
						->join('purchase_invoice', 'purchase_invoice.id = pod_detail.transaction_id')
						->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
						->where('pod_detail.pl_id', $pod_id)
						->group_by('pod_detail.id')
						->get()->result_array();
	}

	public function get_detail_multi_pod($pod_code)
	{
		return $this->db->select('pod.*, purchase_invoice.date AS purchase_date, purchase_invoice.code AS code_pi,
						cash_account.name AS cash_account_name,
					    supplier.code AS code_s, supplier.name AS name_s, purchase_invoice.invoice AS invoice, purchase_invoice.grandtotal As grandtotal_pi,
						bank_transfer.code AS transfer_bank, transfer_account.number AS transfer_number_ba, transfer_account.name AS transfer_name_ba,
						bank_cheque.code AS cheque_bank, cheque_account.number AS cheque_number_ba, cheque_account.name AS cheque_name_ba,
						employee.name AS name_e')
						->from('payment_ledger AS pod')
						->join('purchase_invoice', 'purchase_invoice.id = pod.transaction_id')
						->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
						->join('employee', 'employee.code = pod.employee_code')
						->join('employee AS cash_account', 'cash_account.code = pod.cash_account_id', 'left')
						->join('bank_account AS transfer_account', 'transfer_account.id = pod.transfer_account_id', 'left')
						->join('bank AS bank_transfer', 'bank_transfer.id = transfer_account.bank_id','left')
						->join('bank_account AS cheque_account', 'cheque_account.id = pod.cheque_account_id', 'left')
						->join('bank AS bank_cheque', 'bank_cheque.id = cheque_account.bank_id', 'left')						
						->where('pod.code', $pod_code)
						->group_by('pod.id')
						->get()->result_array();
	}

	// PAYMENT OF RECEIVABLE
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
						cash_account.name AS cash_account_name,
						bank_account.name AS bank_account_name,
						cheque_account.name AS cheque_account_name,
						employee.name AS name_e')
						->from('payment_ledger AS por')
						->join('employee', 'employee.code = por.employee_code')
						->join('cash_ledger_account AS cash_account', 'cash_account.id = por.cash_account_id', 'left')
						->join('cash_ledger_account AS bank_account', 'bank_account.id = por.transfer_account_id', 'left')
						->join('cash_ledger_account AS cheque_account', 'cheque_account.id = por.cheque_account_id', 'left')
						->where('por.id', $por_id)
						->group_by('por.id')
						->get()->row_array();
	}

	public function get_detail_por_detail($por_id)
	{
		return $this->db->select('por_detail.*,
						sales_invoice.date, sales_invoice.invoice,
						customer.name AS name_s')						
						->from('payment_ledger_detail AS por_detail')
						->join('sales_invoice', 'sales_invoice.id = por_detail.transaction_id')
						->join('customer', 'customer.code = sales_invoice.customer_code')
						->where('por_detail.pl_id', $por_id)
						->group_by('por_detail.id')
						->get()->result_array();
	}
}