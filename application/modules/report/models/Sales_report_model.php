<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_report_model extends CI_Model 
{
    public function get_cashier()
    {
        return $this->db->select('employee.id, employee.code, employee.name')
                        ->from('employee')->join('pos', 'pos.cashier = employee.code')
                        ->where('pos.deleted', 0)->where('employee.deleted', 0)
                        ->group_by('employee.id')
                        ->get()->result();
    }

	// SALES INVOICE
	public function chart_sales_invoice($filter)
    {
        $result = [null];
        if($filter['view_type'] == "day")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%d-%m-%Y") AS time, sum(grandtotal) AS grandtotal');                          
        }
        if($filter['view_type'] == "month")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%m-%Y") AS time, sum(grandtotal) AS grandtotal');                          
        }        
        if($filter['view_type'] == "year")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%Y") AS time, sum(grandtotal) AS grandtotal');                      
        }
        $this->db->from('sales_invoice');
        if($filter['from_date'] != "")                 
        {
            $this->db->where('sales_invoice.date >=', $filter['from_date']);
        }
        if($filter['to_date'] != "")                 
        {
            $this->db->where('sales_invoice.date <=', $filter['to_date']);            
        }
        if($filter['payment'] != "")
        {
            $this->db->where('sales_invoice.payment', $filter['payment']);            
        }
        if($filter['customer_code'] != "")
        {
            $this->db->where('sales_invoice.customer_code', $filter['customer_code']);
        }
        if($filter['sales_code'] != "")
        {
            $this->db->where('sales_invoice.sales_code', $filter['sales_code']);
        }
        if($filter['payment_status'] != "")
        {
            $this->db->where('sales_invoice.payment_status', $filter['payment_status']);
        }
        $result = $this->db->where('sales_invoice.do_status', 1)->group_by('time')->order_by('time', 'ASC')->get()->result_array();
         
        return $result;
	}
	
	public function sales_invoice($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
    {
        $this->db->select('sales_invoice.*, customer.name AS name_c, sales.name AS name_s, ')
				 ->from('sales_invoice')
				 ->join('customer', 'customer.code = sales_invoice.customer_code')
                 ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
                 ->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1);
        if($from_date != "")                 
        {
            $this->db->where('sales_invoice.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('sales_invoice.date <=', $to_date);            
		}
		if($payment != "")
        {
            $this->db->where('sales_invoice.payment', $payment);
        } 
        if($customer_code != "")                 
        {
            $this->db->where('sales_invoice.customer_code', $customer_code);
		}       
		if($sales_code != "")
        {
            $this->db->where('sales_invoice.sales_code', $sales_code);
		}       
		if($payment_status != "")
        {
            $this->db->where('sales_invoice.payment_status', $payment_status);
        }
        return $this->db->group_by('sales_invoice.id')->order_by('sales_invoice.date', 'ASC')->get()->result_array();
    }

    public function detail_sales_invoice($sales_invoice_id)
    {
        return $this->db->select('product.code AS code_p, product.name AS name_p, warehouse.code AS code_w, warehouse.name AS name_w, sales_invoice_detail.qty, unit.code AS code_u, unit.name AS name_u, sales_invoice_detail.price, sales_invoice_detail.total')
                        ->from('sales_invoice_detail')
                        ->join('product', 'product.id = sales_invoice_detail.product_id')
                        ->join('warehouse', 'warehouse.id = sales_invoice_detail.warehouse_id')
                        ->join('unit', 'unit.id = sales_invoice_detail.unit_id')
                        ->where('sales_invoice_detail.sales_invoice_id', $sales_invoice_id)
                        ->group_by('sales_invoice_detail.id')->order_by('product.code', 'ASC')
                        ->get()->result_array();
	}

	public function print_sales_invoice_report($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
    {
        $this->db->select('sales_invoice.id, sales_invoice.date, sales_invoice.invoice, customer.name AS name_c, sales.name AS name_s, sales_invoice.grandtotal, sales_invoice.ppn');
        $this->db->from('sales_invoice');
		$this->db->join('customer', 'customer.code = sales_invoice.customer_code');
		$this->db->join('employee AS sales', 'sales.code = sales_invoice.sales_code');
        $this->db->where('sales_invoice.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('sales_invoice.date >=', date('Y-m-d', strtotime($from_date)));
        }
        if($to_date != "")
        {
            $this->db->where('sales_invoice.date <=', date('Y-m-d', strtotime($to_date)));
		}
		if($payment != "")
        {
            $this->db->where('sales_invoice.payment', $payment);
        }   
        if($customer_code != "")
        {
            $this->db->where('sales_invoice.customer_code', $customer_code);
		}
		if($sales_code != "")
        {
            $this->db->where('sales_invoice.sales_code', $sales_code);
		}
		if($payment_status != "")
        {
            $this->db->where('sales_invoice.payment_status', $payment_status);
        }
        return $this->db->group_by('sales_invoice.id')->get()->result_array();
    }

    public function print_sales_invoice_detail_report($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
    {
        $sales_invoices = $this->sales_report->sales_invoice($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status);
        $data = array();
        foreach($sales_invoices AS $sales_invoice)
        {
            $data[] = [
                'sales_invoice'		 => $sales_invoice,
                'detail_sales_invoice' => $this->sales_report->detail_sales_invoice($sales_invoice['id'])
            ];
        }                
        return $data;
    }

    public function print_sales_invoice_daily_report($from_date, $to_date, $payment, $customer_code, $sales_code, $payment_status)
    {
        $this->db->select('sales_invoice.date, COUNT(sales_invoice.id) AS count_sales_invoice, SUM(sales_invoice.grandtotal) AS total_sales_invoice, SUM(sales_invoice.account_payable) AS total_account_payable')
				 ->from('sales_invoice')
				 ->join('customer', 'customer.code = sales_invoice.customer_code')
                 ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
				 ->where('sales_invoice.deleted', 0);
				 
        if($from_date != "")                 
        {
            $this->db->where('sales_invoice.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('sales_invoice.date <=', $to_date);            
        }      
        if($payment != "")
        {
            $this->db->where('sales_invoice.payment', $payment);
        } 
        if($customer_code != "")                 
        {
            $this->db->where('sales_invoice.customer_code', $customer_code);
		}       
		if($sales_code != "")
        {
            $this->db->where('sales_invoice.sales_code', $sales_code);
		}       
		if($payment_status != "")
        {
            $this->db->where('sales_invoice.payment_status', $payment_status);
        }
        return $this->db->group_by('sales_invoice.date')->order_by('sales_invoice.date', 'ASC')->get()->result_array();
	}

    // PRODUCT SALES
    public function chart_product_sales($filter)
    {
        $result = [null];
        if($filter['view_type'] == "day")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%d-%m-%Y") AS time, round(sum(sales_invoice_detail.qty*sales_invoice_detail.unit_value), 2) AS total_qty, sum(sales_invoice_detail.total) AS grandtotal');                          
        }
        if($filter['view_type'] == "month")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%m-%Y") AS time, round(sum(sales_invoice_detail.qty*sales_invoice_detail.unit_value), 2) AS total_qty, sum(sales_invoice_detail.total) AS grandtotal');                          
        }        
        if($filter['view_type'] == "year")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%Y") AS time, round(sum(sales_invoice_detail.qty*sales_invoice_detail.unit_value), 2) AS total_qty, sum(sales_invoice_detail.total) AS grandtotal');                      
        }
        $this->db->from('sales_invoice_detail')->join('sales_invoice', 'sales_invoice.id = sales_invoice_detail.sales_invoice_id')
                 ->join('product', 'product.id = sales_invoice_detail.product_id')
                 ->join('department', 'department.code = product.department_code')
                 ->join('subdepartment','subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code');
        if($filter['search'] != "")
		{			
			$this->db->like('product.code', $filter['search']);
			$this->db->or_like('product.name', $filter['search']);
		}
        if($filter['from_date'] != "")                 
        {
            $this->db->where('sales_invoice.date >=', $filter['from_date']);
        }
        if($filter['to_date'] != "")                 
        {
            $this->db->where('sales_invoice.date <=', $filter['to_date']);            
        }        
        if($filter['department_code'] != "")
		{
			$this->db->where('department.code', $filter['department_code']);
		}
		if($filter['department_code'] != "" && $filter['subdepartment_code'] != "")
		{
			$this->db->where('department.code', $filter['department_code']);
			$this->db->where('subdepartment.code', $filter['subdepartment_code']);
		}
        if($filter['customer_code'] != "")
        {
            $this->db->where('sales_invoice.customer_code', $filter['customer_code']);
        }        
        if($filter['sales_code'] != "")
        {
            $this->db->where('sales_invoice.sales_code', $filter['sales_code']);
        }
        $result = $this->db->where('sales_invoice.do_status', 1)->group_by('time')->order_by('time', 'ASC')->get()->result_array();
        return $result;
    }

	public function get_product_sales_invoice_detail_report($product_code = null, $from_date = null, $to_date = null, $customer_code=null, $sales_code = null)
	{
		$this->db->select('sales_invoice_detail.product_id, sales_invoice_detail.product_code, sales_invoice_detail.qty, sales_invoice_detail.unit_id, sales_invoice_detail.unit_value, sales_invoice_detail.total')
						->from('sales_invoice_detail')->join('sales_invoice', 'sales_invoice.id = sales_invoice_detail.sales_invoice_id');
		if($product_code != null)
		{
			$this->db->where('sales_invoice_detail.product_code', $product_code);
		}
		if($from_date != null)
		{
			$this->db->where('sales_invoice.date >=', $from_date);
		}   
		if($to_date != null)
		{
			$this->db->where('sales_invoice.date <=', $to_date);
		}  
		if($customer_code != null)
		{
			$this->db->where('sales_invoice.customer_code', $customer_code);
		}  
        if($sales_code != null)
		{
			$this->db->where('sales_invoice.sales_code', $sales_code);
		}        
		return $this->db->where('sales_invoice.do_status', 1)->where('sales_invoice_detail.deleted', 0)->where('sales_invoice.deleted', 0)->group_by('sales_invoice_detail.id')->order_by('sales_invoice_detail.id', 'asc')->get();
    }
    
	public function get_product_sales_report($from_date = null, $to_date = null, $department_code, $subdepartment_code, $customer_code, $sales_code, $ppn, $search, $iLength = null, $iStart= null)
	{
		$product = [null];
		$sales_invoice_product = $this->sales_report->get_product_sales_invoice_detail_report(null, $from_date, $to_date, $customer_code, $sales_code)->result_array();
		foreach($sales_invoice_product AS $info2)
		{
			if(!in_array($info2['product_id'], $product))
			{
				$product[] = $info2['product_id'];
			}			
			continue;
		}
		$this->db->select('product.id AS id, product.barcode, product.code, product.name, product.ppn, unit.name AS unit,
						department.name AS name_d, subdepartment.name AS name_sub_d')                        
						->from('product')
						->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
						->join('unit', 'unit.id = product_unit.unit_id')
						->join('department', 'department.code = product.department_code')
						->join('subdepartment','subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code');
		if($search != "")
		{			
			$this->db->like('product.code', $search);
			$this->db->or_like('product.name', $search);
		}
		if($department_code != "")
		{
			$this->db->where('department.code', $department_code);
		}   
		if($department_code != "" && $subdepartment_code != "")
		{
			$this->db->where('department.code', $department_code);
			$this->db->where('subdepartment.code', $subdepartment_code);
		}
		if($ppn != "")
		{
			$this->db->where('product.ppn', $ppn);
		}
		if($iLength != null && $iStart != null)
		{
			if($iLength != '' && $iLength != '-1')
			{
				$this->db->limit($iLength, ($iStart)? $iStart : 0);        
			}
		}        
		return $this->db->where_in('product.id', $product)
						->group_by('product.id')->order_by('product.id', 'asc')
						->get();
    }

	// SALES RETURN
	public function sales_return($from_date, $to_date, $customer_code)
	{
		$this->db->select('sales_return.id AS id, sales_return.date, sales_return.code, employee.name AS name_e, customer.name AS name_c, 
						sales_return.method, sales_return.total_product, sales_return.total_qty,
						sales_return.total_return, sales_return.sales_id, purchase.invoice, sales_return.account_payable,
						sales_return.grandtotal')
						->from('sales_return')						
						->join('purchase', 'purchase.id = sales_return.sales_id', 'left')
						->join('employee', 'employee.code = sales_return.employee_code')
                        ->join('customer', 'customer.code = sales_return.customer_code')
                        ->where('sales_return.deleted', 0);
        if($from_date != "")                 
        {
            $this->db->where('sales_return.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('sales_return.date <=', $to_date);            
        }
        if($customer_code != "")                 
        {
            $this->db->where('sales_return.customer_code', $customer_code);
        }
        return $this->db->get()->result_array();
	}

	public function detail_sales_return($sales_return_id)
	{
		return $this->db->select('sales_return_detail.id AS id, product.code AS code_p, product.name AS name_p, 
						unit.code AS code_u, unit.name AS name_u, warehouse.name AS name_w, sales_return_detail.qty AS qty, 
						sales_return_detail.price, sales_return_detail.total AS total,
						sales_return_detail.information')
					  	->from('sales_return_detail')
					  	->join('product', 'product.code = sales_return_detail.product_code')
					  	->join('unit', 'unit.id = sales_return_detail.unit_id')
					  	->join('warehouse', 'warehouse.id = sales_return_detail.warehouse_id')					  						  	
					  	->where('sales_return_detail.sales_return_id', $sales_return_id)
						->where('sales_return_detail.deleted', 0)
						->group_by('sales_return_detail.id')
						->get()->result_array();
    }
    
    public function print_sales_return_report($from_date, $to_date, $customer_code)
    {
        $this->db->select('sales_return.id, sales_return.code, sales_return.date, customer.name AS name_c, sales_return.method, sales_return.total_return');
        $this->db->from('sales_return');
        $this->db->join('customer', 'customer.code = sales_return.customer_code');
        $this->db->where('sales_return.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('sales_return.date >=', date('Y-m-d', strtotime($from_date)));
        }
        if($to_date != "")
        {
            $this->db->where('sales_return.date <=', date('Y-m-d', strtotime($to_date)));
        }
        if($customer_code != "")
        {
            $this->db->where('sales_return.customer_code', $customer_code);
        }        
        return $this->db->group_by('sales_return.id')->get()->result_array();
    }

    public function print_sales_return_detail_report($from_date, $to_date, $customer_code)
    {
        $sales_returns = $this->sales_report->sales_return($from_date, $to_date, $customer_code);
        $data = array();
        foreach($sales_returns AS $sales_return)
        {
            $data[] = [
                'sales_return' => $sales_return,
                'detail_sales_return' => $this->sales_report->detail_sales_return($sales_return['id'])
            ];
        }                
        return $data;
    }

    public function print_sales_return_daily_report($from_date, $to_date, $customer_code)
    {
        $this->db->select('sales_return.date, COUNT(sales_return.id) AS count_sales_return, SUM(sales_return.total_return) AS total_sales_return')
                 ->from('sales_return')
                 ->join('employee', 'employee.code = sales_return.employee_code')
                 ->join('customer', 'customer.code = sales_return.customer_code')
                 ->where('sales_return.deleted', 0);
        if($from_date != "")                 
        {
            $this->db->where('sales_return.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('sales_return.date <=', $to_date);            
        }        
        if($customer_code != "")                 
        {
            $this->db->where('sales_return.customer_code', $customer_code);
        }
        return $this->db->group_by('sales_return.date')->order_by('sales_return.date', 'ASC')->get()->result_array();
	}
	
	// PRODUCT SALES RETURN
	public function get_product_sales_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $iLength = null, $iStart= null, $iOrder= null)
	{
		$column = array(null, 'code', 'name');
		$this->db->select('product.id AS id, product.barcode, product.code, product.name, unit.name AS unit,
						department.name AS name_d, subdepartment.name AS name_sub_d')                        
						->from('product')
						->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
						->join('unit', 'unit.id = product_unit.unit_id', 'inner')
						->join('department', 'department.code = product.department_code', 'inner')
						->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner')       
						->join('sales_return_detail', 'sales_return_detail.product_code = product.code')
						->join('sales_return', 'sales_return.id = sales_return_detail.sales_return_id');						
		if($search != "")
		{
			$this->db->like('product.barcode', $search);
			$this->db->or_like('product.code', $search);
			$this->db->or_like('product.name', $search);
		}
		if($department_code != "")
		{
			$this->db->where('department.code', $department_code);
		}   
		if($department_code != "" && $subdepartment_code != "")
		{
			$this->db->where('department.code', $department_code);
			$this->db->where('subdepartment.code', $subdepartment_code);
		} 
		if($from_date != null)
		{
			$this->db->where('sales_return.date >=', $from_date);
		}   
		if($to_date != null)
		{
			$this->db->where('sales_return.date <=', $to_date);
		}
		if($iLength != null && $iStart != null)
		{
			if($iLength != '' && $iLength != '-1')
			{
				$this->db->limit($iLength, ($iStart)? $iStart : 0);        
			}
		}
		if($iOrder != null)
		{                            
			$this->db->order_by($column[$iOrder['0']['column']], $iOrder['0']['dir']);
		}
		return $this->db->where('product.deleted', 0)
						->where('sales_return_detail.deleted', 0)
						->where('sales_return.deleted', 0)
						->where('sales_return.do_status', 1)
						->group_by('product.code')                        
						->get();
	}

	public function get_product_sales_return_detail_report($product_code, $from_date = null, $to_date = null)
	{
		$this->db->select('sales_return_detail.product_code, sales_return_detail.qty, sales_return_detail.unit_id, sales_return_detail.total')
						->from('sales_return_detail')
						->join('sales_return', 'sales_return.id = sales_return_detail.sales_return_id')
						->where('sales_return_detail.product_code', $product_code);
		if($from_date != null)
		{
			$this->db->where('sales_return.date >=', $from_date);
		}   
		if($to_date != null)
		{
			$this->db->where('sales_return.date <=', $to_date);
		}        
		return $this->db->where('sales_return_detail.deleted', 0)
						->where('sales_return.deleted', 0)
						->where('sales_return.do_status', 1)
						->group_by('sales_return_detail.id')
						->order_by('sales_return_detail.id', 'ASC')
						->get();
	}

	// INACTIVE CUSTOMER
	public function inactive_customer_id($from_date, $to_date)
	{
		$active_id = [''];
		$customer_sales = $this->db->select('customer.id')
								->from('customer')
								->join('sales_invoice', 'sales_invoice.customer_code = customer.code')
								->where('customer.deleted', 0)->where('sales_invoice.deleted', 0)
								->where('sales_invoice.date >=', date('Y-m-d', strtotime($from_date)))
								->where('sales_invoice.date <=', date('Y-m-d', strtotime($to_date)))
								->group_by('customer.id')
								->get()->result_array();		
		foreach($customer_sales AS $customer_sale)
		{
			$active_id[] = $customer_sale['id'];
		}
		$inactive_id = [];
		$inactive_customer_sales = $this->db->select('customer.id')
											->from('customer')->where('customer.id !=', 1)->where_not_in('customer.id', $active_id)
											->where('customer.deleted', 0)
											->group_by('customer.id')   
											->get()->result_array();        
		foreach($inactive_customer_sales AS $inactive_customer_sale)
		{
			$inactive_id[] = $inactive_customer_sale['id'];
		}
		return $inactive_id;
	}    
}