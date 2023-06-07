<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model 
{
    public function expense_code()
	{
		$data = $this->db->select('code')->from('expense')->order_by('id', 'DESC')->limit(1)->get()->row_array();
		$tahun = substr(date('Y'),2,2);
		$format = "EXP".$tahun.date('m').date('d');
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
}