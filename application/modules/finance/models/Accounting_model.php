<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting_model extends CI_Model 
{
    public function get_account($search)
	{		
		return $this->db->select('id, code, name')->from('coa_account')
					->like('name', $search)->or_like('code', $search)
		            ->order_by('code', 'ASC')->get();
	}

	public function journal_code()
	{
		$data = $this->db->select('code')->from('journal')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "JRNL".$tahun.date('m').date('d'); //jrnl22090901
		if($data)
		{
			$sub_tanggal = substr($data['code'], 7, 2);
			$no 		 = substr($data['code'], -2,2);
			if($sub_tanggal == date('d'))
			{
				$no++;
				$code = $format.sprintf("%02s", $no);
			}
			else
			{
				$no = 1;
				$code = $format.sprintf("%02s", $no);
			}
		}
		else
		{
			$no = 1;
			$code = $format.sprintf("%02s", $no);
		}					 						 
		return $code;
	}

	public function get_detail_journal($journal_id)
	{
		return $this->db->select('*')->from('journal')->where('id', $journal_id)->get()->row_array();
	}

	public function get_detail_journal_detail($journal_id)
	{
		return $this->db->select('coa_account.code AS code_coa, coa_account.name AS name_coa, journal_detail.debit, journal_detail.credit')
						->from('journal_detail')
						->join('coa_account', 'coa_account.code = journal_detail.coa_account_code')
						->where('journal_detail.journal_id', $journal_id)
						->group_by('journal_detail.id')
						->get()->result_array();
	}
}