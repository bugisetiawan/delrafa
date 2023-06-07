<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Import_export extends System_Controller 
{	
    public function __construct()
  	{		
		parent::__construct();
		if($this->session->userdata('id_u') != 1)
        {
            redirect(site_url('dashboard', 'refresh'));
		}
		$this->load->model('master/Product_model', 'product');
		$this->load->model('master/Supplier_model', 'supplier');
		$this->load->model('master/Customer_model', 'customer');
	}

	// Module / Feauture
	public function import_module()
	{		
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();	
			$filename = 'ImportModule-'.$this->session->userdata('code_e');			
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			if(isset($post['form_type']) && $post['form_type'] == 0)
			{
				$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
				{
					$config['upload_path']   = "./assets/upload/product/";
					$config['allowed_types'] = "xlsx";
					$config['max_size']      = "10240";
					$config['remove_space']  = TRUE;
					$config['overwrite'] = true;
					$config['file_name'] = $filename;
					$this->load->library('upload', $config);
					if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
					{ 
						$upload = array(
							'result' => 'success',
							'error' => ''
						);
					}
					else
					{
						$upload = array(
							'result' => 'failed',								
							'error' => $this->upload->display_errors()
						);
					}
					if($upload['result'] == "success")
					{				
						$spreadsheet = $reader->load('assets/upload/product/'.$filename.'.xlsx');
						$sheetData = $spreadsheet->getActiveSheet()->toArray();
						$header = array("title" => "Import Module");
						$data = array('sheet' => $sheetData);
						$footer = array("script" => ['master/product/import_product_sellprice.js']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_module', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$header = array("title" => "Import Module");				
						$data = array('error' => $upload['error']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_module', $data);
						$this->load->view('include/footer');							
					}
				}
				else
				{
					$header = array("title" => "Import Module");				
					$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe <b>EXCELL</b>! Terima Kasih');
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('importexport/import_module', $data);
					$this->load->view('include/footer');
				}					
			}
			else
			{				
				$spreadsheet = $reader->load('assets/upload/product/'.$filename.'.xlsx');
				$sheetData = $spreadsheet->getActiveSheet()->toArray();
				$this->db->truncate('module');
				for($i = 1;$i < count($sheetData);$i++)
				{
					$res = 1;
					$data_module = array(
						'name' => $sheetData[$i][0],
						'url' => $sheetData[$i][1],
						'category' => $sheetData[$i][2],
						'method' => $sheetData[$i][3],
						'active' => $sheetData[$i][4],							
					);
					$this->crud->insert('module', $data_module);
				}
				$this->session->set_flashdata('success', 'Import Module selesai');				
				redirect(site_url('dashboard'));					
			}				
		}
		else
		{
			$header = array("title" => "Import Module");				
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('importexport/import_module');
			$this->load->view('include/footer');
		}
		
	}

	// Import Custom
	public function import_custom()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();	
			$filename = 'ImportCustom-'.$this->session->userdata('id_u');			
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			if(isset($post['form_type']) && $post['form_type'] == 0)
			{
				$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
				{
					$config['upload_path']   = "./assets/upload/importexport/";
					$config['allowed_types'] = 'xls|xlsx';
					$config['max_size']      = "10240";
					$config['remove_space']  = TRUE;
					$config['overwrite'] 	 = TRUE;
					$config['file_name']     = $filename;
					$this->load->library('upload', $config);
					if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
					{ 
						$upload = array(
							'result' => 'success',
							'error' => ''
						);
					}
					else
					{
						$upload = array(
							'result' => 'failed',								
							'error' => $this->upload->display_errors()
						);
					}
					if($upload['result'] == "success")
					{				
						$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
						$sheetData = $spreadsheet->getActiveSheet()->toArray();
						$header = array("title" => "Import Custom");
						$data   = array('sheet' => $sheetData);
						$footer = array("script" => ['importexport/import_export.js']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_custom', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$header = array("title" => "Import Custom");
						$data = array('error' => $upload['error']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_custom', $data);
						$this->load->view('include/footer');							
					}
				}
				else
				{
					$header = array("title" => "Import Custom");
					$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe yang sesuai, Terima Kasih');
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('importexport/import_custom');
					$this->load->view('include/footer');
				}					
			}
			else
			{
				$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
				$sheetData = $spreadsheet->getActiveSheet()->toArray();				
				$inventory_value = 0;
				for($i=1;$i<count($sheetData);$i++)
				{
					// echo json_encode($sheetData[$i][0]); die;
					if($sheetData[$i][0] == NULL && $sheetData[$i][1] == NULL )
					{
						continue;
					}
					elseif($sheetData[$i][0] == "NAMA BARANG" && $sheetData[$i][1] == NULL)
					{						
						continue;
					}
					elseif($sheetData[$i][0] == NULL && $sheetData[$i][1] != NULL)
					{
						$warehouse = $sheetData[$i][1];
						continue;
					}
					elseif($sheetData[$i][0] != NULL && $sheetData[$i][0] != "NAMA BARANG")
					{
						$data_export[] = array(
							'name' => $sheetData[$i][0],
							'qty' => $sheetData[$i][2],
							'hpp' => $sheetData[$i][3],
							'warehouse' => $warehouse
						);
						continue;
					}					
				}
				$spreadsheet = new Spreadsheet();
				// Set document properties
				$spreadsheet->getProperties()->setCreator('TRUST System')
				->setLastModifiedBy('TRUST System')
				->setTitle('Export Pengolahan Persediaan Produk')
				->setSubject('Export Pengolahan Persediaan Produk')
				->setDescription('Export Pengolahan Persediaan Produk');
				// Add some data
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A1', 'NAMA')
				->setCellValue('B1', 'QTY')
				->setCellValue('C1', 'HPP')
				->setCellValue('D1', 'GUDANG')
				;
				// Miscellaneous glyphs, UTF-8
				$i=2; 
				foreach($data_export as $info_export) 
				{
					$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$i, $info_export['name'])
					->setCellValue('B'.$i, $info_export['qty'])
					->setCellValue('C'.$i, $info_export['hpp'])
					->setCellValue('D'.$i, $info_export['warehouse']);
					$i++;
				}

				// Rename worksheet
				$filename = 'TRUST System - Pengolahan Produk '.date('d-m-Y').'.xlsx';
				$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$spreadsheet->setActiveSheetIndex(0);
				
				// Redirect output to a client’s web browser (Xlsx)
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');

				// If you're serving to IE over SSL, then the following may be needed
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
				header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header('Pragma: public'); // HTTP/1.0
				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->save('php://output');
				exit;
			}				
		}
		else
		{
			$header = array("title" => "Import Custom");				
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('importexport/import_custom');
			$this->load->view('include/footer');			
		}
	}

	public function import_to_olah_persediaan()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();	
			$filename = 'ImportCustom-'.$this->session->userdata('id_u');			
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			if(isset($post['form_type']) && $post['form_type'] == 0)
			{
				$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
				{
					$config['upload_path']   = "./assets/upload/importexport/";
					$config['allowed_types'] = 'xls|xlsx';
					$config['max_size']      = "10240";
					$config['remove_space']  = TRUE;
					$config['overwrite'] 	 = TRUE;
					$config['file_name']     = $filename;
					$this->load->library('upload', $config);
					if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
					{ 
						$upload = array(
							'result' => 'success',
							'error' => ''
						);
					}
					else
					{
						$upload = array(
							'result' => 'failed',								
							'error' => $this->upload->display_errors()
						);
					}
					if($upload['result'] == "success")
					{				
						$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
						$sheetData = $spreadsheet->getActiveSheet()->toArray();
						$header = array("title" => "Import Custom");
						$data   = array('sheet' => $sheetData);
						$footer = array("script" => ['importexport/import_export.js']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_custom', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$header = array("title" => "Import Custom");
						$data = array('error' => $upload['error']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_custom', $data);
						$this->load->view('include/footer');							
					}
				}
				else
				{
					$header = array("title" => "Import Custom");
					$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe yang sesuai, Terima Kasih');
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('importexport/import_custom');
					$this->load->view('include/footer');
				}					
			}
			else
			{
				$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
				$sheetData = $spreadsheet->getActiveSheet()->toArray();				
				$inventory_value = 0;
				for($i=1;$i<count($sheetData);$i++)
				{
					// echo json_encode($sheetData[$i][0]); die;
					if($sheetData[$i][0] == NULL && $sheetData[$i][1] == NULL )
					{
						continue;
					}
					elseif($sheetData[$i][0] == "NAMA BARANG" && $sheetData[$i][1] == NULL)
					{						
						continue;
					}
					elseif($sheetData[$i][0] == NULL && $sheetData[$i][1] != NULL)
					{
						$warehouse = $sheetData[$i][1];
						continue;
					}
					elseif($sheetData[$i][0] != NULL && $sheetData[$i][0] != "NAMA BARANG")
					{
						$data_export[] = array(
							'name' => $sheetData[$i][0],
							'qty' => $sheetData[$i][2],
							'hpp' => $sheetData[$i][3],
							'warehouse' => $warehouse
						);
						continue;
					}					
				}
				$spreadsheet = new Spreadsheet();
				// Set document properties
				$spreadsheet->getProperties()->setCreator('TRUST System')
				->setLastModifiedBy('TRUST System')
				->setTitle('Export Pengolahan Persediaan Produk')
				->setSubject('Export Pengolahan Persediaan Produk')
				->setDescription('Export Pengolahan Persediaan Produk');
				// Add some data
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A1', 'NAMA')
				->setCellValue('B1', 'QTY')
				->setCellValue('C1', 'HPP')
				->setCellValue('D1', 'GUDANG')
				;
				// Miscellaneous glyphs, UTF-8
				$i=2; 
				foreach($data_export as $info_export) 
				{
					$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$i, $info_export['name'])
					->setCellValue('B'.$i, $info_export['qty'])
					->setCellValue('C'.$i, $info_export['hpp'])
					->setCellValue('D'.$i, $info_export['warehouse']);
					$i++;
				}

				// Rename worksheet
				$filename = 'TRUST System - Pengolahan Produk '.date('d-m-Y').'.xlsx';
				$spreadsheet->getActiveSheet()->setTitle('Report Excel '.date('d-m-Y H'));
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$spreadsheet->setActiveSheetIndex(0);
				
				ob_end_clean();
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
				header('Cache-Control: max-age=0');
				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->save('php://output');
				exit;
			}				
		}
		else
		{
			$header = array("title" => "Import Custom");				
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('importexport/import_custom');
			$this->load->view('include/footer');			
		}
	}
	
	// Master Product
	public function import_product()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();	
			$filename = 'ImportProduct-'.$this->session->userdata('id_u');			
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			if(isset($post['form_type']) && $post['form_type'] == 0)
			{
				$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
				{
					$config['upload_path']   = "./assets/upload/importexport/";
					$config['allowed_types'] = "xlsx";
					$config['max_size']      = "10240";
					$config['remove_space']  = TRUE;
					$config['overwrite'] 	 = TRUE;
					$config['file_name']     = $filename;
					$this->load->library('upload', $config);
					if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
					{ 
						$upload = array(
							'result' => 'success',
							'error' => ''
						);
					}
					else
					{
						$upload = array(
							'result' => 'failed',								
							'error' => $this->upload->display_errors()
						);
					}
					if($upload['result'] == "success")
					{				
						$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
						$sheetData = $spreadsheet->getActiveSheet()->toArray();
						$header = array("title" => "Import Produk");
						$data   = array('sheet' => $sheetData);
						$footer = array("script" => ['importexport/import_export.js']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_product', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$header = array("title" => "Import Produk");
						$data = array('error' => $upload['error']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_product', $data);
						$this->load->view('include/footer');							
					}
				}
				else
				{
					$header = array("title" => "Import Produk");
					$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe yang sesuai, Terima Kasih');
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('importexport/import_product');
					$this->load->view('include/footer');
				}					
			}
			else
			{			
				$this->db->trans_start();	
				$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
				$sheetData = $spreadsheet->getActiveSheet()->toArray();
				$found = 0;
				for($i=1;$i<count($sheetData);$i++)
				{
					$res = 0;
					$product = $this->db->select('name')->from('product')->where('name', $sheetData[$i][0])->get()->row_array();					
					if($product == null)
					{
						// PRODUCT
						$dept = $this->db->select('code')->from('department')->where('name', $sheetData[$i][1])->get()->row_array();
						$subdept = $this->db->select('code')->from('subdepartment')->where('name', $sheetData[$i][2])->get()->row_array();
						$post['department_code'] = $dept['code'];
						$post['subdepartment_code'] = $subdept['code'];
						$post['name'] = $sheetData[$i][0];
						$post['minimal'] = $sheetData[$i][4];
						$post['maximal'] = $sheetData[$i][5];
						$code = $this->product->generate_code($post['department_code'], $post['subdepartment_code']);
						$data_product =[
							'code'               => $code,
							'department_code'    => $post['department_code'],
							'subdepartment_code' => $post['subdepartment_code'],
							'type'               => 1,
							'ppn'                => 1,						
							'name'               => $post['name'],						
							'minimal'         	 => $post['minimal'],
							'maximal'         	 => $post['maximal'],
							'commission_sales' 	 => 0,
							'status'             => 1
						];
						$product_id = $this->crud->insert_id('product', $data_product);
						if($product_id != null)
						{
							// BASE UNIT
							$unit = $this->db->select('id')->from('unit')->where('code', $sheetData[$i][3])->get()->row_array();
							$post['unit_id'] = $unit['id'];
							$post['weight']  = $sheetData[$i][6];
							$data_unit = array(
								'product_id' 	=> $product_id,
								'product_code'	=> $code,
								'unit_id' 		=> $post['unit_id'],
								'value'			=> 1,
								'weight' 		=> $post['weight'],
								'default'		=> 1
							);			
							$this->crud->insert('product_unit', $data_unit);
							// MULTI UNIT 1
							$multi_unit_1 = $this->db->select('id')->from('unit')->where('code', $sheetData[$i][8])->get()->row_array();
							if(isset($multi_unit_1))
							{
								$multi_value_1 = $sheetData[$i][9];
								$data_multi_unit_1 = [
									'product_id' 	=> $product_id,
									'product_code' 	=> $code,
									'unit_id'  		=> $multi_unit_1['id'],
									'value'			=> $multi_value_1,
									'weight'		=> $multi_value_1*$post['weight'],
									'default'		=> 0
								];
								$this->crud->insert('product_unit', $data_multi_unit_1);
							}												
							// MULTI UNIT 2
							$multi_unit_2 		= $this->db->select('id')->from('unit')->where('code', $sheetData[$i][10])->get()->row_array();
							if(isset($multi_unit_2))
							{
								$multi_value_2 		= $sheetData[$i][11];
								$data_multi_unit_2 = [
									'product_id' 	=> $product_id,
									'product_code' 	=> $code,
									'unit_id'  		=> $multi_unit_2['id'],
									'value'			=> $multi_value_2,
									'weight'		=> $multi_value_2*$post['weight'],
									'default'		=> 0
								];
								$this->crud->insert('product_unit', $data_multi_unit_2);
							}						
							$res = 1;
						}					
						else
						{
							break;
						}							
					}
					else
					{
						// $found++;
					}									
				}	
				// echo json_encode($found); die;
				$this->db->trans_complete();													
				if($res == 1 && $this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'Sukses, Import Produk berhasil');
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Mohon maaf, Import Produk gagal');
				}
				redirect(site_url('dashboard'));					
			}				
		}
		else
		{
			$header = array("title" => "Import Produk");				
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('importexport/import_product');
			$this->load->view('include/footer');			
		}
	}

	// Master Supplier/Customer
	public function import_supplier_customer()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();	
			$filename = 'ImportSupplierCustomer-'.$this->session->userdata('id_u');			
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			if(isset($post['form_type']) && $post['form_type'] == 0)
			{
				$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
				{
					$config['upload_path']   = "./assets/upload/importexport/";
					$config['allowed_types'] = "xlsx";
					$config['max_size']      = "10240";
					$config['remove_space']  = TRUE;
					$config['overwrite'] 	 = TRUE;
					$config['file_name']     = $filename;
					$this->load->library('upload', $config);
					if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
					{ 
						$upload = array(
							'result' => 'success',
							'error' => ''
						);
					}
					else
					{
						$upload = array(
							'result' => 'failed',								
							'error' => $this->upload->display_errors()
						);
					}
					if($upload['result'] == "success")
					{				
						$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
						$sheetData = $spreadsheet->getActiveSheet()->toArray();
						$header = array("title" => "Import Supplier/Customer");
						$data   = array('sheet' => $sheetData);
						$footer = array("script" => ['importexport/import_export.js']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_supplier', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$header = array("title" => "Import Supplier/Customer");
						$data = array('error' => $upload['error']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_supplier', $data);
						$this->load->view('include/footer');							
					}
				}
				else
				{
					$header = array("title" => "Import Supplier/Customer");
					$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe yang sesuai, Terima Kasih');
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('importexport/import_supplier');
					$this->load->view('include/footer');
				}					
			}
			else
			{			
				$this->db->trans_start();	
				$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
				$sheetData = $spreadsheet->getActiveSheet()->toArray();
				for($i=1;$i<count($sheetData);$i++)
				{
					$res = 0;
					// $code = $this->supplier->supplier_code();					
					// $post['name'] = $sheetData[$i][1];
					// $post['address'] = $sheetData[$i][2];
					// $post['phone'] = $sheetData[$i][3];
					// $post['telephone'] = $sheetData[$i][4];
					// $post['contact'] = $sheetData[$i][5];
					// $post['email'] = $sheetData[$i][6];
					// $post['due_day'] = $sheetData[$i][8];
					// $ppn  = 1;
					// $post['npwp'] = $sheetData[$i][9];


					// $data_supplier  = [
                    //     'code'			=> $code,
                    //     'name'          => $post['name'],
                    //     'address'       => $post['address'],
					// 	'phone'         => $post['phone'],						
					// 	'telephone'     => $post['telephone'],                                        
					// 	'contact'       => $post['contact'],
                    //     'email'         => $post['email'],
                    //     'dueday'        => $post['due_day'],
					// 	'ppn'        	=> $ppn,
					// 	'npwp'			=> $post['npwp']
                    // ];
					// $supplier_id = $this->crud->insert_id('supplier', $data_supplier);
					// if($supplier_id != null)
					// {
					// 	$res=1;
					// 	continue;
					// }					
					// else
					// {
					// 	break;
					// }

					$code = $this->customer->customer_code();					
					$post['name'] = $sheetData[$i][1];
					$post['price_class'] = $sheetData[$i][2];
					$post['address'] = $sheetData[$i][3];
					$post['phone'] = $sheetData[$i][4];
					$post['telephone'] = $sheetData[$i][5];
					$post['contact'] = $sheetData[$i][6];
					$post['email'] = $sheetData[$i][7];
					$post['credit'] = $sheetData[$i][8];
					$post['due_day'] = $sheetData[$i][9];
					$pkp  = 1;
					$post['npwp'] = $sheetData[$i][10];
					$post['nik'] = $sheetData[$i][11];
					$data_customer  = [
                        'code'			=> $code,
                        'name'          => $post['name'],
                        'address'       => $post['address'],
						'phone'         => $post['phone'],						
						'telephone'     => $post['telephone'],                                        
						'contact'       => $post['contact'],
						'email'         => $post['email'],
						'credit'        => format_amount($post['credit']),
                        'dueday'        => $post['due_day'],
						'pkp'        	=> $pkp,
						'npwp'			=> $post['npwp'],
						'nik'			=> $post['nik']
                    ];
					$supplier_id = $this->crud->insert_id('customer', $data_customer);
					if($supplier_id != null)
					{
						$res=1;
						continue;
					}					
					else
					{
						break;
					}
				}	
				$this->db->trans_complete();													
				if($res == 1 && $this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'Sukses, Import Supplier/Customer berhasil');
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Mohon maaf, Import Supplier/Customer gagal');
				}
				redirect(site_url('dashboard'));					
			}				
		}
		else
		{
			$header = array("title" => "Import Supplier/Customer");				
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('importexport/import_supplier');
			$this->load->view('include/footer');			
		}
	}

	// Stock Product in each warehouse
	public function import_stock_product()
	{
		if($this->input->method() === 'post')
		{
			$post = $this->input->post();	
			$filename = 'ImportStockProduct-'.$this->session->userdata('id_u');			
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			if(isset($post['form_type']) && $post['form_type'] == 0)
			{
				$file_mimes = array('application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				if(isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) 
				{
					$config['upload_path']   = "./assets/upload/importexport/";
					$config['allowed_types'] = "xlsx";
					$config['max_size']      = "10240";
					$config['remove_space']  = TRUE;
					$config['overwrite'] 	 = TRUE;
					$config['file_name']     = $filename;
					$this->load->library('upload', $config);
					if($this->upload->do_upload('import_file')) // Lakukan upload dan Cek jika proses upload berhasil
					{ 
						$upload = array(
							'result' => 'success',
							'error' => ''
						);
					}
					else
					{
						$upload = array(
							'result' => 'failed',								
							'error' => $this->upload->display_errors()
						);
					}
					if($upload['result'] == "success")
					{				
						$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
						$sheetData = $spreadsheet->getActiveSheet()->toArray();
						$header = array("title" => "Import Stok Produk");
						$data   = array('sheet' => $sheetData);
						$footer = array("script" => ['importexport/import_export.js']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_stock_product', $data);
						$this->load->view('include/footer', $footer);
					}
					else
					{
						$header = array("title" => "Import Stok Produk");
						$data = array('error' => $upload['error']);
						$this->load->view('include/header', $header);        
						$this->load->view('include/menubar');        
						$this->load->view('include/topbar');        
						$this->load->view('importexport/import_stock_product', $data);
						$this->load->view('include/footer');							
					}
				}
				else
				{
					$header = array("title" => "Import Stok Produk");
					$data = array('error' => 'Mohon Maaf, file yang di upload harus bertipe yang sesuai, Terima Kasih');
					$this->load->view('include/header', $header);        
					$this->load->view('include/menubar');        
					$this->load->view('include/topbar');        
					$this->load->view('importexport/import_stock_product');
					$this->load->view('include/footer');
				}					
			}
			else
			{			
				$this->db->trans_start();	
				$spreadsheet = $reader->load('assets/upload/importexport/'.$filename.'.xlsx');
				$sheetData = $spreadsheet->getActiveSheet()->toArray();
				$not_found = []; $total_not_found = 0;
				for($i=1;$i<count($sheetData);$i++)
				{
					$res = 0;
					$info['product_code'] = $sheetData[$i][0];
					$qty_convert = floatval(format_amount($sheetData[$i][2]));
					$warehouse = $this->db->select('id')->from('warehouse')->where('name', $sheetData[$i][5])->get()->row_array();
					$info['warehouse_id'] = $warehouse['id'];
					$info['hpp'] = floatval(format_amount($sheetData[$i][3]));
					$product_id = $this->crud->get_product_id($info['product_code']);					
					// if($info['product_code'] == 0)
					// {
					// 	$not_found[] = $sheetData[$i][1];
					// }
					if($info['product_code'] != 0 && $qty_convert > 0)
					{
						// Update HPP 
						if($qty_convert != 0)
						{
							$this->crud->update('product', ['buyprice' => $info['hpp']*1.11, 'hpp' => $info['hpp']], ['id' => $product_id]);
						}						

						// STOCK
						$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]);
						if($check_stock->num_rows() == 1)
						{
							$data_stock = $check_stock->row_array();
							$where_stock = array(
								'product_code'  => $info['product_code'],
								'warehouse_id'  => $info['warehouse_id']
							);
							$stock = array(                                
								'product_id' => $product_id,
								'qty'        => $data_stock['qty']+$qty_convert,
							);
							$update_stock = $this->crud->update('stock', $stock, $where_stock);
						}
						else
						{
							$stock = array(                                
								'product_id'    => $product_id,
								'product_code'  => $info['product_code'],                                                        
								'qty'           => $qty_convert,
								'warehouse_id'  => $info['warehouse_id']
							);
							$update_stock = $this->crud->insert('stock', $stock);
						}
						// STOCK CARD
						$where_last_stock_card = [
							'date <='      => date('Y-m-d'),
							'product_id'   => $product_id,																						
							'warehouse_id' => $info['warehouse_id'],
							'deleted'      => 0											
						];
						$last_stock_card = $this->db->select('stock')->from('stock_card')->where($where_last_stock_card)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$data_stock_card = array(
							'type'            => null, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
							'information'     => 'STOK AWAL',
							'note'			  => 'STOK AWAL'.date('dmY'),
							'date'            => date('Y-m-d'),
							'transaction_id'  => null,
							'invoice'         => null,
							'product_id'      => $product_id,
							'product_code'    => $info['product_code'],
							'qty'             => $qty_convert,																						
							'method'          => 1, // 1:In, 2:Out
							'stock'           => $last_stock_card['stock']+$qty_convert,
							'warehouse_id'    => $info['warehouse_id'],
							'employee_code'   => $this->session->userdata('code_e')
						);
						$this->crud->insert('stock_card',$data_stock_card);
						$where_after_stock_card = [
							'date >'       => date('Y-m-d'),
							'product_id'   => $product_id,				
							'warehouse_id' => $info['warehouse_id'],
							'deleted'      => 0
						];                    
						$after_stock_card = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_stock_card  AS $info_after_stock_card)
						{
							$this->crud->update('stock_card', ['stock' => $info_after_stock_card['stock']+$qty_convert], ['id' => $info_after_stock_card['id']]);
						}
						// STOCK MOVEMENT
						$where_last_stock_movement = [
							'product_id'   => $product_id,
							'date <='      => date('Y-m-d'),
							'deleted'      => 0
						];
						$last_stock_movement = $this->db->select('stock')->from('stock_movement')->where($where_last_stock_movement)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
						$data_stock_movement = [
							'type'            => null, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
							'information'     => 'STOK AWAL',
							'note'			  => 'STOK AWAL'.date('dmY'),
							'date'            => date('Y-m-d'),
							'transaction_id'  => null,
							'invoice'         => null,
							'product_id'      => $product_id,
							'product_code'    => $info['product_code'],
							'qty'             => $qty_convert,
							'method'          => 1, // 1:In, 2:Out
							'stock'           => $last_stock_movement['stock']+$qty_convert,
							'employee_code'   => $this->session->userdata('code_e')
						];
						$stock_movement_id = $this->crud->insert_id('stock_movement', $data_stock_movement);
						$where_after_stock_movement = [
							'product_id'   => $product_id,
							'date >'       => date('Y-m-d'),
							'deleted'      => 0
						];                    
						$after_stock_movement = $this->db->select('*')->from('stock_movement')->where($where_after_stock_movement)->order_by('date', 'ASC')->order_by('id', 'ASC')->get()->result_array();
						foreach($after_stock_movement  AS $info_after_stock_movement)
						{
							$this->crud->update('stock_movement', ['stock' => $info_after_stock_movement['stock']+$qty_convert], ['id' => $info_after_stock_movement['id']]);
						}						
					}
					$res = 1;
				}
				// echo json_encode($not_found);die;
				$this->db->trans_complete();
				if($res == 1 && $this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('success', 'Sukses, Import Produk berhasil');
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('error', 'Mohon maaf, Import Produk gagal');
				}
				redirect(site_url('product'));					
			}				
		}
		else
		{
			$header = array("title" => "Import Stok Produk");				
			$this->load->view('include/header', $header);        
			$this->load->view('include/menubar');        
			$this->load->view('include/topbar');        
			$this->load->view('importexport/import_stock_product');
			$this->load->view('include/footer');			
		}		
	}
}