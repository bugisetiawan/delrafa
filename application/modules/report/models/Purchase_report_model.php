<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_report_model extends CI_Model 
{   
    // PURCHASE INVOICE
    public function total_purchase_invoice($filter)
    {
        $this->db->select('purchase_invoice.grandtotal, purchase_invoice.account_payable, purchase_invoice.cheque_payable')
                 ->from('purchase_invoice')
                 ->join('purchase_invoice_detail AS pid', 'pid.purchase_invoice_id = purchase_invoice.id')
                 ->join('product', 'product.id = pid.product_id')
                 ->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
        if($filter['from_date'] != "")
        {
            $this->db->where('purchase_invoice.date >=', $filter['from_date']);
        }
        if($filter['to_date'] != "")
        {
            $this->db->where('purchase_invoice.date <=', $filter['to_date']);
        }
        if($filter['payment'] != "")
        {
            $this->db->where('purchase_invoice.payment', $payment);
        }                
        if($filter['supplier_code'] != "")
        {
            $this->db->where('purchase_invoice.supplier_code', $supplier_code);
        }
        if($filter['ppn'] != "")
        {
            $this->db->where('purchase_invoice.ppn', $ppn);
        }            
        if($filter['payment_status'] != "")
        {
            if($filter['payment_status'] != 3)
            {
                $this->db->where('purchase_invoice.payment_status', $filter['payment_status']);
            }                    
            else
            {
                $this->db->where('purchase_invoice.payment_status !=', 1);
                $this->db->where('purchase_invoice.due_date <', date('Y-m-d'));
            }
        }
        if($filter['search_product'] != "")
        {
            $this->db->like('product.name', $filter['search_product']);
        }                
        $purchase_invoice = $this->db->where('purchase_invoice.deleted', 0)->group_by('purchase_invoice.id')->get()->result_array();
        $grandtotal=0; $account_payable=0;
        foreach($purchase_invoice AS $info_purchase_invoice)
        {
            $grandtotal=$grandtotal+$info_purchase_invoice['grandtotal'];
            $account_payable=$account_payable+$info_purchase_invoice['account_payable']+$info_purchase_invoice['cheque_payable'];
        }
        $result = [
            'grandtotal' => $grandtotal,
            'account_payable' => $account_payable
        ];
        return $result;
    }

    public function chart_purchase_invoice($filter)
    {
        $result = [null];
        if($filter['view_type'] == "day")
        {
            $this->db->select('DATE_FORMAT(purchase_invoice.date, "%d-%m-%Y") AS time, grandtotal');
        }
        if($filter['view_type'] == "month")
        {
            $this->db->select('DATE_FORMAT(purchase_invoice.date, "%m-%Y") AS time, grandtotal');
        }        
        if($filter['view_type'] == "year")
        {
            $this->db->select('DATE_FORMAT(purchase_invoice.date, "%Y") AS time, grandtotal');
        }
        $this->db->from('purchase_invoice');
        $this->db->join('purchase_invoice_detail AS pid', 'pid.purchase_invoice_id = purchase_invoice.id');
        $this->db->join('product', 'product.id = pid.product_id');
        if($filter['from_date'] != "")
        {
            $this->db->where('purchase_invoice.date >=', $filter['from_date']);
        }
        if($filter['to_date'] != "")
        {
            $this->db->where('purchase_invoice.date <=', $filter['to_date']);
        }
        if($filter['payment'] != "")
        {
            $this->db->where('purchase_invoice.payment', $filter['payment']);
        }
        if($filter['supplier_code'] != "")
        {
            $this->db->where('purchase_invoice.supplier_code', $filter['supplier_code']);
        }
        if($filter['ppn'] != "")
        {
            $this->db->where('purchase_invoice.ppn', $filter['ppn']);
        }
        if($filter['payment_status'] != "")
        {
            if($filter['payment_status'] != 3)
            {
                $this->db->where('purchase_invoice.payment_status', $filter['payment_status']);
            }                    
            else
            {
                $this->db->where('purchase_invoice.payment_status !=', 1);
                $this->db->where('purchase_invoice.due_date <', date('Y-m-d'));
            }
        } 
        if($filter['search_product'] != "")
        {
            $this->db->like('product.name', $filter['search_product']);
        }
        $purchase_invoice = $this->db->group_by('time')->group_by('purchase_invoice.id')->order_by('time', 'ASC')->get()->result_array();
        return $purchase_invoice;
    }

    public function purchase_invoice($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
    {
        $this->db->select('purchase_invoice.*, employee.name AS name_e, supplier.name AS name_s')
                 ->from('purchase_invoice')
                 ->join('employee', 'employee.code = purchase_invoice.employee_code')
                 ->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
                 ->where('purchase_invoice.deleted', 0);
        if($from_date != "")                 
        {
            $this->db->where('purchase_invoice.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('purchase_invoice.date <=', $to_date);            
        }
        if($payment != "")
        {
            $this->db->where('purchase_invoice.payment', $payment);            
        }
        if($supplier_code != "")                 
        {
            $this->db->where('purchase_invoice.supplier_code', $supplier_code);
        }
        if($ppn != "")                 
        {
            $this->db->where('purchase_invoice.ppn', $ppn);
        }
        if($payment_status != "")                 
        {
            $this->db->where('purchase_invoice.payment_status', $payment_status);
        }
        return $this->db->group_by('purchase_invoice.id')->order_by('purchase_invoice.date', 'ASC')->get()->result_array();
    }

    public function detail_purchase_invoice($purchase_id)
    {
        return $this->db->select('product.code AS code_p, product.name AS name_p, warehouse.code AS code_w, warehouse.name AS name_w, purchase_invoice_detail.qty, unit.code AS code_u, unit.name AS name_u, purchase_invoice_detail.price, purchase_invoice_detail.total')
                        ->from('purchase_invoice_detail')
                        ->join('product', 'product.id = purchase_invoice_detail.product_id')
                        ->join('warehouse', 'warehouse.id = purchase_invoice_detail.warehouse_id')
                        ->join('unit', 'unit.id = purchase_invoice_detail.unit_id')
                        ->where('purchase_invoice_detail.purchase_invoice_id', $purchase_id)
                        ->group_by('purchase_invoice_detail.id')->order_by('product.code', 'asc')
                        ->get()->result_array();
    }

    public function print_purchase_invoice_report($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
    {
        $this->db->select('purchase_invoice.id AS id, purchase_invoice.date, purchase_invoice.code AS code, purchase_invoice.invoice, , purchase_invoice.payment, purchase_invoice.due_date, purchase_invoice.grandtotal, purchase_invoice.account_payable, supplier.name AS supplier, purchase_invoice.ppn, purchase_invoice.payment_status');
        $this->db->from('purchase_invoice');
        $this->db->join('supplier', 'supplier.code = purchase_invoice.supplier_code');
        $this->db->where('purchase_invoice.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('purchase_invoice.date >=', date('Y-m-d', strtotime($from_date)));
        }
        if($to_date != "")
        {
            $this->db->where('purchase_invoice.date <=', date('Y-m-d', strtotime($to_date)));
        }
        if($payment != "")
        {
            $this->db->where('purchase_invoice.payment', $payment);
        }                
        if($supplier_code != "")
        {
            $this->db->where('purchase_invoice.supplier_code', $supplier_code);
        }
        if($ppn != "")
        {
            $this->db->where('purchase_invoice.ppn', $ppn);
        }
        if($payment_status != "")
        {
            $this->db->where('purchase_invoice.payment_status', $payment_status);
        }
        return $this->db->group_by('purchase_invoice.id')->get()->result_array();
    }

    public function print_purchase_invoice_detail_report($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
    {
        $purchases = $this->purchase_report->purchase_invoice($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status);
        $data = array();
        foreach($purchases AS $purchase)
        {
            $data[] = [
                'purchase' => $purchase,
                'detail_purchase' => $this->purchase_report->detail_purchase_invoice($purchase['id'])
            ];
        }                
        return $data;
    }

    public function print_purchase_invoice_daily_report($from_date, $to_date, $payment, $supplier_code, $ppn, $payment_status)
    {
        $this->db->select('purchase_invoice.date, COUNT(purchase_invoice.id) AS count_purchase_invoice, SUM(purchase_invoice.grandtotal) AS total_purchase_invoice, SUM(purchase_invoice.account_payable) AS total_account_payable')
                 ->from('purchase_invoice')
                 ->join('employee', 'employee.code = purchase_invoice.employee_code')
                 ->join('supplier', 'supplier.code = purchase_invoice.supplier_code')
                 ->where('purchase_invoice.deleted', 0);
        if($from_date != "")                 
        {
            $this->db->where('purchase_invoice.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('purchase_invoice.date <=', $to_date);            
        }
        if($payment != "")
        {
            $this->db->where('purchase_invoice.payment', $payment);            
        }
        if($supplier_code != "")                 
        {
            $this->db->where('purchase_invoice.supplier_code', $supplier_code);
        }
        if($ppn != "")                 
        {
            $this->db->where('purchase_invoice.ppn', $ppn);
        }
        if($payment_status != "")                 
        {
            $this->db->where('purchase_invoice.payment_status', $payment_status);
        }
        return $this->db->group_by('purchase_invoice.date')->order_by('purchase_invoice.date', 'ASC')->get()->result_array();
    }
    
    // PRODUCT PURCHASE
    public function get_product_purchase_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn, $iLength = null, $iStart= null, $iOrder= null)
    {
        $column = array(null, 'code', 'name');
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, product.ppn, unit.name AS unit,
                         department.name AS name_d, subdepartment.name AS name_sub_d')                        
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                         ->join('unit', 'unit.id = product_unit.unit_id')
                         ->join('department', 'department.code = product.department_code')
                         ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code')
                         ->join('purchase_invoice_detail', 'purchase_invoice_detail.product_code = product.code')
                         ->join('purchase_invoice', 'purchase_invoice.id = purchase_invoice_detail.purchase_invoice_id');
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
        if($from_date != null)
        {
            $this->db->where('purchase_invoice.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('purchase_invoice.date <=', $to_date);
        }
        if($supplier_code != "")
        {
            $this->db->where('purchase_invoice.supplier_code', $supplier_code);
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
        return $this->db->where('product.deleted', 0)->where('purchase_invoice_detail.deleted', 0)->where('purchase_invoice.deleted', 0)
                        ->group_by('product.id')->get();
    }

    public function get_product_purchase_detail_report($product_code, $from_date = null, $to_date = null, $supplier_code = null)
    {
        $this->db->select('purchase_invoice_detail.product_code, purchase_invoice_detail.qty, purchase_invoice_detail.unit_id, purchase_invoice_detail.unit_value, purchase_invoice_detail.total')
                         ->from('purchase_invoice_detail')
                         ->join('purchase_invoice', 'purchase_invoice.id = purchase_invoice_detail.purchase_invoice_id')
                         ->where('purchase_invoice_detail.product_code', $product_code);
        if($from_date != null)
        {
            $this->db->where('purchase_invoice.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('purchase_invoice.date <=', $to_date);
        }  
        if($supplier_code != "")
        {
            $this->db->where('purchase_invoice.supplier_code', $supplier_code);
        }
        return $this->db->where('purchase_invoice_detail.deleted', 0)
                        ->where('purchase_invoice.deleted', 0)
                        ->group_by('purchase_invoice_detail.id')
                        ->order_by('purchase_invoice_detail.id', 'ASC')
                        ->get();
	}
    
    // PURCHASE RETURN
    public function purchase_return($from_date, $to_date, $supplier_code)
	{
		$this->db->select('purchase_return.id AS id, purchase_return.date, purchase_return.code, employee.name AS name_e, supplier.name AS name_s, 
						purchase_return.method, purchase_return.total_product, purchase_return.total_qty,
						purchase_return.total_return, purchase_return.purchase_id, purchase.invoice, purchase_return.account_payable,
						purchase_return.grandtotal')
						->from('purchase_return')						
						->join('purchase', 'purchase.id = purchase_return.purchase_id', 'left')
						->join('employee', 'employee.code = purchase_return.employee_code')
                        ->join('supplier', 'supplier.code = purchase_return.supplier_code')
                        ->where('purchase_return.deleted', 0);
        if($from_date != "")                 
        {
            $this->db->where('purchase_return.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('purchase_return.date <=', $to_date);            
        }
        if($supplier_code != "")                 
        {
            $this->db->where('purchase_return.supplier_code', $supplier_code);
        }
        return $this->db->get()->result_array();
	}

	public function detail_purchase_return($purchase_return_id)
	{
		return $this->db->select('purchase_return_detail.id AS id, product.code AS code_p, product.name AS name_p, 
						unit.code AS code_u, unit.name AS name_u, warehouse.name AS name_w, purchase_return_detail.qty AS qty, 
						purchase_return_detail.price, purchase_return_detail.total AS total,
						purchase_return_detail.information')
					  	->from('purchase_return_detail')
					  	->join('product', 'product.code = purchase_return_detail.product_code')
					  	->join('unit', 'unit.id = purchase_return_detail.unit_id')
					  	->join('warehouse', 'warehouse.id = purchase_return_detail.warehouse_id')					  						  	
					  	->where('purchase_return_detail.purchase_return_id', $purchase_return_id)
						->where('purchase_return_detail.deleted', 0)
						->group_by('purchase_return_detail.id')
						->get()->result_array();
    }
    
    public function print_purchase_return_report($from_date, $to_date, $supplier_code)
    {
        $this->db->select('purchase_return.id, purchase_return.code, purchase_return.date, supplier.name AS name_s, purchase_return.method, purchase_return.total_return');
        $this->db->from('purchase_return');
        $this->db->join('supplier', 'supplier.code = purchase_return.supplier_code');
        $this->db->where('purchase_return.do_status', 1);
        $this->db->where('purchase_return.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('purchase_return.date >=', date('Y-m-d', strtotime($from_date)));
        }
        if($to_date != "")
        {
            $this->db->where('purchase_return.date <=', date('Y-m-d', strtotime($to_date)));
        }
        if($supplier_code != "")
        {
            $this->db->where('purchase_return.supplier_code', $supplier_code);
        }        
        return $this->db->group_by('purchase_return.id')->get()->result_array();
    }

    public function print_purchase_return_detail_report($from_date, $to_date, $supplier_code)
    {
        $purchase_returns = $this->purchase_report->purchase_return($from_date, $to_date, $supplier_code);
        $data = array();
        foreach($purchase_returns AS $purchase_return)
        {
            $data[] = [
                'purchase_return' => $purchase_return,
                'detail_purchase_return' => $this->purchase_report->detail_purchase_return($purchase_return['id'])
            ];
        }                
        return $data;
    }

    public function print_purchase_return_daily_report($from_date, $to_date, $supplier_code)
    {
        $this->db->select('purchase_return.date, COUNT(purchase_return.id) AS count_purchase_return, SUM(purchase_return.total_return) AS total_purchase_return')
                 ->from('purchase_return')
                 ->join('employee', 'employee.code = purchase_return.employee_code')
                 ->join('supplier', 'supplier.code = purchase_return.supplier_code')
                 ->where('purchase_return.deleted', 0);
        if($from_date != "")                 
        {
            $this->db->where('purchase_return.date >=', $from_date);
        }
        if($to_date != "")                 
        {
            $this->db->where('purchase_return.date <=', $to_date);            
        }        
        if($supplier_code != "")                 
        {
            $this->db->where('purchase_return.supplier_code', $supplier_code);
        }
        return $this->db->group_by('purchase_return.date')->order_by('purchase_return.date', 'ASC')->get()->result_array();
    }

	// PRODUCT PURCHASE RETURN
    public function get_product_purchase_return_report($search, $from_date, $to_date, $department_code, $subdepartment_code, $supplier_code, $ppn, $iLength = null, $iStart= null, $iOrder= null)
    {
        $column = array(null, 'code', 'name');
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, product.ppn, unit.name AS unit,
                         department.name AS name_d, subdepartment.name AS name_sub_d')                        
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
                         ->join('unit', 'unit.id = product_unit.unit_id', 'inner')
                         ->join('department', 'department.code = product.department_code', 'inner')
                         ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner')       
                         ->join('purchase_return_detail', 'purchase_return_detail.product_code = product.code')
                         ->join('purchase_return', 'purchase_return.id = purchase_return_detail.purchase_return_id');
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
        if($from_date != null)
        {
            $this->db->where('purchase_return.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('purchase_return.date <=', $to_date);
        }
        if($supplier_code != "")
        {
            $this->db->where('purchase_return.supplier_code', $supplier_code);
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
        return $this->db->where('product.deleted', 0)->where('purchase_return_detail.deleted', 0)->where('purchase_return.deleted', 0)
                        ->group_by('product.id')->get();
    }

    public function get_product_purchase_return_detail_report($product_code, $from_date = null, $to_date = null, $supplier_code = null)
    {
        $this->db->select('purchase_return_detail.product_code, purchase_return_detail.qty, purchase_return_detail.unit_id, purchase_return.unit_value, purchase_return_detail.total')
                         ->from('purchase_return_detail')
                         ->join('purchase_return', 'purchase_return.id = purchase_return_detail.purchase_return_id')
                         ->where('purchase_return_detail.product_code', $product_code);
        if($from_date != null)
        {
            $this->db->where('purchase_return.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('purchase_return.date <=', $to_date);
        }
        if($supplier_code != "")
        {
            $this->db->where('purchase_return.supplier_code', $supplier_code);
        }
        return $this->db->where('purchase_return_detail.deleted', 0)->where('purchase_return.deleted', 0)
                        ->group_by('purchase_return_detail.id')->order_by('purchase_return_detail.id', 'ASC')
                        ->get();
    }
}