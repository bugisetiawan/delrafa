<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('encrypt_custom'))
{
	/**
	 * - Fungsi untuk mengencrypt
	 * 
	 * @param 	string 	$string
	 * @return 	string 	
	*/
	function encrypt_custom($string)
	{
		$output = false; $encrypt_method = "AES-256-CBC"; $secret_key = 'BUGI SETIAWAN'; $secret_iv = 'Setiawan Bugi'; $key = hash('sha256', $secret_key);		
		$iv = substr(hash('sha256', $secret_iv), 0, 16); $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv); $output = base64_encode($output);
		return $output;
	}
}

if(!function_exists('decrypt_custom'))
{
	/**
	 * - Fungsi untuk mendecrypt	 
	 * 
	 * @param 	string 	$string
	 * @return 	string 	
	*/
	function decrypt_custom($string)
	{
		$output = false; $encrypt_method = "AES-256-CBC"; $secret_key = 'BUGI SETIAWAN'; $secret_iv = 'Setiawan Bugi'; $key = hash('sha256', $secret_key);		
		$iv = substr(hash('sha256', $secret_iv), 0, 16); $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		return $output;
	}
}

if(!function_exists('format_date'))
{
	/**
	 * - Fungsi untuk mengubah format tanggal menjadi sesuai dengan struktur database
	 * 
	 * @param 	string 	$date
	 * @return 	string 	
	*/
	function format_date($date)
	{
		return date('Y-m-d', strtotime($date));
	}
}

if(!function_exists('format_amount'))
{
	/**
	 * - Fungsi untuk mengubah "," menjadi ""
	 * 
	 * @param 	string 	$ammount
	 * @return 	string 	
	*/
	function format_amount($amount)
	{
		return str_replace(",","", $amount);
	}
}

if(!function_exists('add_balance'))
{
	/**
	 * - Fungsi untuk mengencrypt
	 * 
	 * @param 	string 	$string
	 * @return 	string 	
	*/
	function add_balance($number1, $number2)
	{
		return $number1+$number2;
	}
}

if(!function_exists('sub_balance'))
{
	/**
	 * - Fungsi untuk mengencrypt
	 * 
	 * @param 	string 	$string
	 * @return 	string 	
	*/
	function sub_balance($number1, $number2)
	{
		return $number1-$number2;
	}
}