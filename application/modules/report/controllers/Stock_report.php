<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_report extends System_Controller 
{	
    public function __construct()
  	{
      parent::__construct();
      $this->load->model('Stock_report_model', 'stock_report');
    }

    public function get_warehouse()
    {        
        $warehouse  = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
        $option		= "<option value='0'>- SEMUA GUDANG -</option>";		
		foreach($warehouse as $data)
		{
			if($data['default']==1)
			{
				$option .= "<option value='".$data['id']."' selected>".$data['name']."</option>";
			}
			else
			{
				$option .= "<option value='".$data['id']."'>".$data['name']."</option>";
			}
		}		
		$result = array
		(
			'option'=>$option
		);
		echo json_encode($result);
    }
    
    // STOCK CARD
	public function stock_card()
	{
		if($this->system->check_access('stock/card', 'read'))
        {
			if($this->input->is_ajax_request())
			{
                $post = $this->input->post();
				header('Content-Type: application/json');
				$this->datatables->select('stock_card.id As id_sc, stock_card.transaction_id, stock_card.invoice, product.code AS code_p, product.name AS name_p, stock_card.qty, stock_card.information, stock_card.type, 
								stock_card.method, stock_card.stock, stock_card.created, warehouse.code AS code_w, warehouse.name AS name_w,
								stock_card.invoice AS search_invoice, product.code AS search_code_p');
				$this->datatables->from('stock_card');
                $this->datatables->join('product', 'product.id = stock_card.product_id');
                $this->datatables->join('department', 'department.code = product.department_code');
				$this->datatables->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code');
				$this->datatables->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1');
                $this->datatables->join('unit', 'unit.id = product_unit.unit_id');
				$this->datatables->join('warehouse', 'warehouse.id = stock_card.warehouse_id');
                $this->datatables->where('stock_card.deleted', 0);
                if($post['from_date'] != "")
                {
                    $this->datatables->where('DATE(stock_card.created) >=', format_date($post['from_date']));
                }
                if($post['to_date'] != "")
                {
                    $this->datatables->where('DATE(stock_card.created) <=', format_date($post['to_date']));
                }
                if($post['department_code'] != "")
                {
                    $this->db->where('department.code', $post['department_code']);
                }
                if($post['department_code'] != "" && $post['subdepartment_code'] != "")
                {
                    $this->db->where('department.code', $post['department_code']);
                    $this->db->where('subdepartment.code', $post['subdepartment_code']);
                }
                if($post['transaction_type'] != "")
                {
                    $this->datatables->where('stock_card.type', $post['transaction_type']);
                }  
                if($post['warehouse_id'] != 0)
                {
                    $this->datatables->where('stock_card.warehouse_id', $post['warehouse_id']);
                }                                 
				$this->datatables->group_by('stock_card.id');
				$this->datatables->add_column('transaction_id', 
				'
					$1		                
				', 'encrypt_custom(transaction_id)');
				$this->datatables->add_column('code_p', 
				'
					<a class="text-primary kt-link text-center" href="'.site_url('product/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
				', 'encrypt_custom(code_p), code_p');
				echo $this->datatables->generate();
			}
			else
			{
				$header = array("title" => "Daftar kartu Stok"); 
				$footer = array("script" => ['report/stock/stock_card_report.js']);
				$this->load->view('include/header', $header);        
				$this->load->view('include/menubar');        
				$this->load->view('include/topbar');        
				$this->load->view('stock/stock_card_report');
				$this->load->view('include/footer', $footer);
			}			
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }		
	}

    // STOCK OPNAME DAN ADJUSMENT
    public function stock_opname()
    {  
        if($this->system->check_access('report/stock/opname', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $post           = $this->input->post();
                $from_date      = $post['from_date'];
                $to_date        = $post['to_date'];        
                $warehouse_id   = $post['warehouse_id'];
                $status         = $post['status'];
            
                header('Content-Type: application/json');
                $this->datatables->select('stock_opname.id, stock_opname.date, stock_opname.code AS search_code, stock_opname.code, checker.name AS checker, operator.name AS operator, stock_opname.total_product, warehouse.name AS warehouse, stock_opname.status');
                $this->datatables->from('stock_opname');
                $this->datatables->join('employee AS checker', 'checker.code = stock_opname.checker');
                $this->datatables->join('employee AS operator', 'operator.code = stock_opname.operator');
                $this->datatables->join('warehouse', 'warehouse.id = stock_opname.warehouse_id');
                if($from_date != "")
                {
                    $this->datatables->where('stock_opname.date >=', format_date($from_date));
                }
                if($to_date != "")
                {
                    $this->datatables->where('stock_opname.date <=', format_date($to_date));
                }
                if($warehouse_id != 0)
                {
                    $this->datatables->where('stock_opname.warehouse_id', $warehouse_id);
                } 
                if($status != 0)
                {
                    $this->datatables->where('stock_opname.status', $status);
                }        
                $this->datatables->group_by('stock_opname.id');
                $this->datatables->add_column('code', 
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('opname/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(id),code');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Stok Opname dan Adjusment Stok");
                $footer = array("script" => ['report/stock/stock_opname_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('stock/stock_opname_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }              
    }

    // REPACKING 
    public function repacking()
    {     
        if($this->system->check_access('report/stock/repacking', 'read'))
        {
            if($this->input->is_ajax_request())
            {
                $post           = $this->input->post();
                $from_date      = $post['from_date'];
                $to_date        = $post['to_date'];        
                // $repacker       = $post['repacker'];
                // $employee_code  = $post['employee_code'];               
                header('Content-Type: application/json');
                $this->datatables->select('repacking.id, repacking.date, repacking.code AS search_code, repacking.code, repacking.repacker, repacking.employee_code AS operator');
                $this->datatables->from('repacking');
                $this->datatables->join('employee AS repacker', 'repacker.code = repacking.repacker', 'left');
                $this->datatables->join('employee AS operator', 'operator.code = repacking.employee_code');
                if($from_date != "")
                {
                    $this->datatables->where('repacking.date >=', format_date($from_date));
                }
                if($to_date != "")
                {
                    $this->datatables->where('repacking.date <=', format_date($to_date));
                }
                // if($employee_code != 0)
                // {
                //     $this->datatables->where('repacking.employee_code', $employee_code);
                // }
                $this->datatables->group_by('repacking.id');
                $this->datatables->add_column('code', 
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('repacking/detail/$1').'"><b>$2</b></a>
                ', 'encrypt_custom(id),code');
                echo $this->datatables->generate();
            }
            else
            {
                $header = array("title" => "Repacking Produk");
                $footer = array("script" => ['report/stock/repacking_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('stock/repacking_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }           
    }

        
}