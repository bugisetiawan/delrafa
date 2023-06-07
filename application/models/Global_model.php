<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Global_model extends CI_Model 
{	

	public function company()
	{		
		$query = $this->crud->get_where('setting', ['name' => 'company'])->row_array();
		return json_decode($query['information']);
	}

	public function encrypt($string)
	{
		$output = false; $encrypt_method = "AES-256-CBC"; $secret_key = 'BUGI SETIAWAN'; $secret_iv = 'Setiawan Bugi'; $key = hash('sha256', $secret_key);		
		$iv = substr(hash('sha256', $secret_iv), 0, 16); $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv); $output = base64_encode($output);
		return $output;
	}

	public function decrypt($string)
	{
		$output = false; $encrypt_method = "AES-256-CBC"; $secret_key = 'BUGI SETIAWAN'; $secret_iv = 'Setiawan Bugi'; $key = hash('sha256', $secret_key);		
		$iv = substr(hash('sha256', $secret_iv), 0, 16); $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		return $output;
	}

	public function penyebut($nilai) 
	{
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	public function terbilang($nilai) 
	{
		if($nilai<0) {
			$hasil = "minus ". trim($this->penyebut($nilai));
		} else {
			$hasil = trim($this->penyebut($nilai));
		}     		
		return $hasil;
	}

    public function get_city($province_id)
    {
        $this->db->select('*');
        $this->db->from('city');
        $this->db->where('province_id', $province_id);
        return $this->db->get();
	}						
	
	public function get_purchase($product_code)
	{
		return $this->db->select('purchase.id, purchase.date, purchase_detail.product_code, purchase_detail.qty, purchase_detail.unit_id, purchase_detail.price')
						->from('purchase_detail')
						->join('purchase', 'purchase.id = purchase_detail.purchase_id')
						->where('purchase_detail.product_code', $product_code)
						->order_by('purchase.date','asc')
						->get();
	}

	public function get_purchase_all($where)
	{
		return $this->db->select('purchase.id, purchase.date, purchase_detail.product_code, purchase_detail.qty, purchase_detail.unit_id, purchase_detail.price')
						->from('purchase_detail')
						->join('purchase', 'purchase.id = purchase_detail.purchase_id')
						->where($where)
						->order_by('purchase.date','desc')
						->get()->result_array();
	}

	public function get_sales_return_all($where)
	{
		return $this->db->select('sales_return.id, sales_return.date, sales_return_detail.product_code, sales_return_detail.qty, sales_return_detail.unit_id, sales_return_detail.price')
						->from('sales_return_detail')
						->join('sales_return', 'sales_return.id = sales_return_detail.sales_return_id')
						->where($where)
						->order_by('sales_return.date','desc')
						->get()->result_array();
	}

	public function get_purchase_return_all($where)
	{
		return $this->db->select('purchase_return.id, purchase_return.date, purchase_return_detail.product_code, purchase_return_detail.qty, purchase_return_detail.unit_id, purchase_return_detail.price')
						->from('purchase_return_detail')
						->join('purchase_return', 'purchase_return.id = purchase_return_detail.purchase_return_id')
						->where($where)
						->order_by('purchase_return.date','desc')
						->get()->result_array();
	}

	public function get_pos_all($where)
	{
		return $this->db->select('pos.id, pos.date, pos_detail.product_code, pos_detail.qty, pos_detail.unit_id, pos_detail.price')
						->from('pos_detail')
						->join('pos', 'pos.id = pos_detail.pos_id')
						->where($where)
						->order_by('pos.date','desc')
						->get()->result_array();
	}
}
