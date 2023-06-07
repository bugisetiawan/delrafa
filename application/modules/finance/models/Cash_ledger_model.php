<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_ledger_model extends CI_Model 
{	    
    // GENERAL FUNCTION
    public function get_last_balance($cl_type, $account_id, $date)
    {
        $where_last_balance = [
            'cl_type'    => $cl_type,
            'account_id' => $account_id,
            'date <='    => format_date($date),
            'deleted'    => 0
        ];
        return $this->db->select('*')->from('cash_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
    }	

    // CASH
    public function last_balance_cash()
    {
        $data =[];
        $cash_account = $this->crud->get_where('cash_ledger_account', ['type' => 1, 'deleted' => 0])->result_array();
        foreach($cash_account AS $info_cash_account)
        {
            $last_balance = $this->cash_ledger->get_last_balance(1, $info_cash_account['id'], date('d-m-Y'));
            $data[] =[
                'code' => $info_cash_account['code'],
                'name' => $info_cash_account['name'],
                'balance' => $last_balance['balance']
            ];
        }
        return $data;                    
    }

    public function cash_account_code()
    {
        $code_category = "1"; $code_subcategory = "01"; $search = $code_category.$code_subcategory;
        $data = $this->db->select('code')->from('coa_account')->like('code', $search, 'after')
                                    ->limit(1)->order_by('coa_account.id', 'DESC')->get()->row_array();
        $sub = (int) substr($data['code'], 3, 2)+1;
        $max = $code_category.$code_subcategory.sprintf("%02s", $sub);
        return $max;
    }

    // BANK
    public function last_balance_bank()
    {
        $data =[];
        $cash_account = $this->crud->get_where('cash_ledger_account', ['type' => 2, 'deleted' => 0])->result_array();
        foreach($cash_account AS $info_cash_account)
        {
            $last_balance = $this->cash_ledger->get_last_balance(2, $info_cash_account['id'], date('d-m-Y'));
            $data[] =[
                'code' => $info_cash_account['code'],
                'name' => $info_cash_account['name'],
                'balance' => $last_balance['balance']
            ];
        }
        return $data;                    
    }

    public function bank_account_code()
    {
        $code_category = "1"; $code_subcategory = "02"; $search = $code_category.$code_subcategory;
        $data = $this->db->select('code')->from('coa_account')->like('code', $search, 'after')
                                    ->limit(1)->order_by('coa_account.id', 'DESC')->get()->row_array();
        $sub = (int) substr($data['code'], 3, 2)+1;
        $max = $code_category.$code_subcategory.sprintf("%02s", $sub);
        return $max;
    }      
    
    // CASH LEDGER TRANSACTION IN OUT
    public function get_account($search)
	{		
        $where_not_in = ['10101', '10102'];
        return $this->db->select('id, code, name')->from('coa_account')
                    ->where_not_in('code', $where_not_in)
                    ->like('name', $search)
                    ->or_like('code', $search)
		            ->order_by('code', 'ASC')->get();
    }
    
	public function cash_ledger_in_out_code($method)
	{         
		$data = $this->db->select('invoice')->from('cash_ledger')->where('information', 'KAS&BANK MASUK/KELUAR')->order_by('id', 'DESC')->limit(1)->get()->row_array();
        $tahun = substr(date('Y'),2,2);
        if($method == 1)
        {
            $format = "CLI".$tahun.date('m').date('d');
        }
        else if($method == 2)
        {
            $format = "CLO".$tahun.date('m').date('d');            
        }		
		if($data)
		{
			$sub_tanggal = substr($data['invoice'], 7, 2);
			$no 		 = substr($data['invoice'], -2,2);
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
    
    public function get_detail_cash_ledger($cl_id)
    {
        $cash_ledger = $this->crud->get_where('cash_ledger', ['id' => $cl_id])->row_array();
        switch ($cash_ledger['cl_type']) {
        case 1:
            $account = $this->crud->get_where('cash_ledger_account', ['type' => 1, 'id' => $cash_ledger['account_id']])->row_array();
            break;
        case 2:
            $account = $this->crud->get_where('cash_ledger_account', ['type' => 2, 'id' => $cash_ledger['account_id']])->row_array();
            break;
        case 3:
            $account = $this->crud->get_where('supplier', ['code' => $cash_ledger['account_id']])->row_array();
            break;
        case 4:
            $account = $this->crud->get_where('customer', ['code' => $cash_ledger['account_id']])->row_array();
            break;
        default:
            $account = "-";
        }

        $result = [
            'id'    => $cash_ledger['id'],
            'date'  => $cash_ledger['date'],
            'cl_type' => $cash_ledger['cl_type'],
            'account' => $account['name'],
            'invoice' => $cash_ledger['invoice'],
            'note'    => $cash_ledger['note'],
            'amount'  => $cash_ledger['amount'],
            'method' => $cash_ledger['method']
        ];
        return $result;
    }

    public function get_detail_cash_ledger_in_out($cl_id)
    {
        return $this->db->select('coa.code As code_coa, coa.name AS name_coa, clio.amount AS amount')
                        ->from('cash_ledger_in_out AS clio')
                        ->join('coa_account AS coa', 'coa.code = clio.  coa_account_code')
                        ->where('clio.cl_id', $cl_id)
                        ->group_by('clio.id')
                        ->get()->result_array();        
    }

    // CASH LEDGER MUTATION
    public function cash_ledger_mutation_code()
	{         
		$data = $this->db->select('invoice')->from('cash_ledger')->where('information', 'KAS&BANK MUTASI')->order_by('id', 'DESC')->limit(1)->get()->row_array();
        $tahun = substr(date('Y'),2,2);        
        $format = "CLM".$tahun.date('m').date('d');
		if($data)
		{
			$sub_tanggal = substr($data['invoice'], 7, 2);
			$no 		 = substr($data['invoice'], -2,2);
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

    public function get_detail_cash_ledger_mutation($cl_id)
    {
        $cash_ledger = $this->crud->get_where('cash_ledger', ['id' => $cl_id])->row_array();
        if($cash_ledger['from_cl_id'] == null)
        {
            $from_cl = $cash_ledger;
            $to_cl = $this->crud->get_where('cash_ledger', ['id' => $cash_ledger['to_cl_id']])->row_array();
        }
        else
        {
            $from_cl = $this->crud->get_where('cash_ledger', ['id' => $cash_ledger['from_cl_id']])->row_array();
            $to_cl = $cash_ledger;            
        }
        $from_account = $this->crud->get_where('cash_ledger_account', ['type' => $from_cl['cl_type'], 'id' => $from_cl['account_id']])->row_array();
        $to_account = $this->crud->get_where('cash_ledger_account', ['type' => $to_cl['cl_type'], 'id' => $to_cl['account_id']])->row_array();
        $data = [
            'from_cl' => $from_cl,
            'from_account' => $from_account,
            'to_cl' => $to_cl,
            'to_account' => $to_account,
        ];
        return $data;        
    }

    // DEPOSIT CODE
    public function deposit_code($deposit_type)
	{         		
        $tahun = substr(date('Y'),2,2);
        if($deposit_type == 1)
        {
            $data = $this->db->select('invoice')->from('cash_ledger')->where('information', 'PENERIMAAN UANG MUKA PEMBELIAN')->order_by('id', 'DESC')->limit(1)->get()->row_array();
            $format = "SDP".$tahun.date('d').date('m');
        }
        else if($deposit_type == 2)
        {
            $data = $this->db->select('invoice')->from('cash_ledger')->where('information', 'PENERIMAAN UANG MUKA PENJUALAN')->order_by('id', 'DESC')->limit(1)->get()->row_array();
            $format = "CDP".$tahun.date('d').date('m'); //cdp           
        }		
		if($data)
		{
			$sub_tanggal = substr($data['invoice'], 3, 2);
			$no 		 = substr($data['invoice'], -2,2);
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