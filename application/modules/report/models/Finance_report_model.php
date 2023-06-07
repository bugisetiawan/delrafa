<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_report_model extends CI_Model 
{
    // SALES RECIVABLE
    public function sales_receivable($filter, $customer_code)
    {
        $this->db->select('sales_invoice.*, customer.name AS name_c, sales.name AS name_s')
				 ->from('sales_invoice')
				 ->join('customer', 'customer.code = sales_invoice.customer_code')
                 ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
                 ->where('sales_invoice.do_status', 1)
				 ->where('sales_invoice.deleted', 0)
				 ->where('sales_invoice.payment_status !=', 1)
                 ->where('sales_invoice.deleted', 0);
        if($filter['from_date'] != "")                 
        {
            $this->db->where('sales_invoice.date >=', $filter['from_date']);
        }
        if($filter['to_date'] != "")                 
        {
            $this->db->where('sales_invoice.date <=', $filter['to_date']);            
		}		
        if($customer_code != "")                 
        {
            $this->db->where('sales_invoice.customer_code', $customer_code);
		}       
		if($filter['sales_code'] != "")
        {
            $this->db->where('sales_invoice.sales_code', $filter['sales_code']);
		}
        return $this->db->group_by('sales_invoice.id')->order_by('sales_invoice.date', 'ASC')->order_by('sales_invoice.id', 'ASC')->get()->result_array();
    }

    public function sales_receivable_payment($sales_invoice_id)
    {
        return $this->db->select('sum(por_transaction.amount) AS payment')
			            ->from('payment_ledger AS por')
                        ->join('payment_ledger_transaction AS por_transaction', 'por_transaction.pl_id = por.id')
                        ->where('por.transaction_type', 2)
                        ->where('por_transaction.transaction_id', $sales_invoice_id)
                        ->where('por.deleted', 0)
                        ->group_by('por_transaction.transaction_id')
                        ->get()->row_array();
    }

    public function print_sales_receivable_report($filter)
    {
        if($filter['customer_code'] != "")
        {
            $customers = $this->crud->get_where('customer', ['code' => $filter['customer_code']])->result_array();
        }
        else
        {
            $customers = $this->crud->get_where('customer', ['deleted' => 0])->result_array();
        }
        $result = [];
        foreach($customers AS $customer)
        {
            $sales_receivables = $this->finance_report->sales_receivable($filter, $customer['code']);
            if(count($sales_receivables) > 0)
            {
                $data = [];
                foreach($sales_receivables AS $sales_receivable)
                {
                    $data[] =[
                        'sales_receivable' => $sales_receivable,
                        'sales_receivable_payment' => $this->finance_report->sales_receivable_payment($sales_receivable['id'])
                    ];                
                }

                $result[] = [
                    'customer' => $customer,
                    'data'      => $data
                ];
            }     
            else
            {
                continue;
            }       
        }
        return $result;
    }

    public function print_customer_global_sales_receivable_report($filter)
    {
        $this->db->select('customer.id AS id_c, customer.code AS code_c, customer.name AS name_c, sum(grandtotal) AS total_grandtotal, sum(down_payment) AS total_down_payment, sum(account_payable) AS total_account_payable, sum(cheque_payable) AS total_cheque_payable')
                 ->from('sales_invoice')
                 ->join('customer', 'customer.code = sales_invoice.customer_code')
                 ->where('sales_invoice.payment_status !=', 1)->where('sales_invoice.do_status', 1);
        if($filter['customer_code'] != "")
        {
            $this->db->where('sales_invoice.customer_code', $filter['customer_code']);
        }
        return $this->db->group_by('customer.id')->get()->result_array();
    }

    // CASH LEDGER TRANSACTION
    public function cash_ledger_transaction($filter)
    {
        $this->db->select('cl.id AS id_cl, cla.name AS name, date, invoice, information, note, amount, method, balance');
        $this->db->from('cash_ledger AS cl');
        $this->db->where('cl.cl_type', $filter['cl_type']);
        if(in_array($filter['cl_type'], [1, 2]))
        {
            $this->db->join('cash_ledger_account AS cla', 'cla.id = cl.account_id');
        }
        elseif($filter['cl_type'] == 3)
        {
            $this->db->join('supplier AS cla', 'cla.id = cl.account_id');
        }
        elseif($filter['cl_type'] == 4)
        {
            $this->db->join('customer AS cla', 'cla.id = cl.account_id');
        }        
        $this->db->where('cl.deleted', 0);
        if($filter['from_date'] != "")
        {
            $this->db->where('cl.date >=', format_date($filter['from_date']));
        }
        if($filter['to_date'] != "")
        {
            $this->db->where('cl.date <=', format_date($filter['to_date']));
        }
        if($filter['account_id'] != "")
        {
            $this->db->where('cl.account_id', $filter['account_id']);
        }
        return $this->db->group_by('cl.id')->order_by('cl.date', 'ASC')->order_by('cl.id','ASC')->get()->result_array();
    }
    // PRODUCT PROFIT
    public function get_product_profit_sales_invoice_detail_report($product_code=null, $from_date=null, $to_date=null)
    {
        $this->db->select('sales_invoice_detail.product_id, sales_invoice_detail.product_code, sales_invoice_detail.qty, sales_invoice_detail.unit_id, sales_invoice_detail.unit_value, sales_invoice_detail.total, sales_invoice_detail.hpp')
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
        return $this->db->where('sales_invoice.deleted', 0)->where('sales_invoice_detail.deleted', 0)->where('sales_invoice.do_status', 1)->group_by('sales_invoice_detail.id')->get()->result_array();
    }    
    
    public function get_product_profit_sales_return_detail_report($product_code = null, $from_date = null, $to_date = null)
    {
        $this->db->select('sales_return_detail.product_id, sales_return_detail.product_code, sales_return_detail.qty, sales_return_detail.unit_id, sales_return_detail.unit_value, sales_return_detail.total, sales_return_detail.hpp')
                         ->from('sales_return_detail')
                         ->join('sales_return', 'sales_return.id = sales_return_detail.sales_return_id');
        if($product_code != null)
        {
            $this->db->where('sales_return_detail.product_code', $product_code);
        }                                             
        if($from_date != null)
        {
            $this->db->where('sales_return.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('sales_return.date <=', $to_date);
        }        
        return $this->db->where('sales_return_detail.deleted', 0)->where('sales_return.deleted', 0)->where('sales_return.do_status', 1)->group_by('sales_return_detail.id')->get()->result_array();
    }

    public function get_product_profit_report($search = null, $from_date = null,  $to_date = null, $department_code = null, $subdepartment_code = null, $iLength = null, $iStart= null)
    {      
        $product_id = [''];
        $sales_invoices = $this->finance_report->get_product_profit_sales_invoice_detail_report(null, $from_date, $to_date);
        foreach($sales_invoices AS $sales_invoice)
        {
            if(!in_array($sales_invoice['product_id'], $product_id))
            {
                $product_id[] = $sales_invoice['product_id'];
            }
        }
        $sales_returns = $this->finance_report->get_product_profit_sales_return_detail_report(null, $from_date, $to_date);
        foreach($sales_returns AS $sales_return)
        {
            if(!in_array($sales_return['product_id'], $product_id))
            {
                $product_id[] = $sales_return['product_id'];
            }            
        }
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, unit.name AS unit, product.hpp,
                         department.name AS name_d, subdepartment.name AS name_sub_d')                        
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                         ->join('unit', 'unit.id = product_unit.unit_id')
                         ->join('department', 'department.code = product.department_code')
                         ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code')
                         ->where('product.deleted', 0)->where_in('product.id', $product_id);
        if($search != "")
        {
			$this->db->like('product.code', $search);
            $this->db->or_like('product.name', $search) ->where('product.deleted', 0)->where_in('product.id', $product_id);
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
        if($iLength != null && $iStart != null)
        {
            if($iLength != '' && $iLength != '-1')
            {
                $this->db->limit($iLength, ($iStart)? $iStart : 0);        
            }
        }        
        return $this->db->group_by('product.id')->get();
    }

    // SALES PROFIT
    public function get_sales_invoice_customer_report($from_date, $to_date, $customer_code, $sales_code)
    {
        $this->db->select('sum(sales_invoice.grandtotal) AS grandtotal, sum(sales_invoice.total_hpp) AS total_hpp')
                 ->from('sales_invoice')
                 ->join('employee', 'employee.code = sales_invoice.sales_code')
                 ->join('customer', 'customer.code = sales_invoice.customer_code')
                 ->where('sales_invoice.deleted', 0)->where('sales_invoice.do_status', 1);
        if($from_date != "")
        {
            $this->db->where('sales_invoice.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('sales_invoice.date <=', $to_date);
        }        
        if($customer_code != "")
        {
            $this->db->where('sales_invoice.customer_code', $customer_code);
        }
        if($sales_code != "")
        {
            $this->db->where('sales_invoice.sales_code', $sales_code);
        }
        $data = $this->db->get()->row_array();
        $result = [
            'grandtotal' => $data['grandtotal'],
            'total_hpp' => $data['total_hpp'],
        ];
        return $result;        
    }

    public function get_sales_return_customer_report($from_date, $to_date, $customer_code, $sales_code)
    {
        $this->db->select('sum(sales_return.total_return) AS grandtotal, sum(sales_return.total_hpp) AS total_hpp')
                 ->from('sales_return')
                 ->join('customer', 'customer.code = sales_return.customer_code')
                 ->where('sales_return.deleted', 0)->where('sales_return.do_status', 1);
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
        if($sales_code != "")
        {
            $this->db->where('sales_return.employee_code', $sales_code);
        }
        $data = $this->db->get()->row_array();
        $result = [
            'grandtotal' => $data['grandtotal'],
            'total_hpp' => $data['total_hpp']
        ];
        return $result;
    }  
    
    public function sales_invoice($filter)
    {
        $this->db->select('sales_invoice.*, customer.name AS name_c, sales.name AS name_s')
				 ->from('sales_invoice')
				 ->join('customer', 'customer.code = sales_invoice.customer_code')
                 ->join('employee AS sales', 'sales.code = sales_invoice.sales_code')
                 ->where('sales_invoice.deleted', 0);
        if($filter['from_date'] != "")                 
        {
            $this->db->where('sales_invoice.date >=', $filter['from_date']);
        }
        if($filter['to_date'] != "")                 
        {
            $this->db->where('sales_invoice.date <=', $filter['to_date']);            
		}		
        if($filter['customer_code'] != "")                 
        {
            $this->db->where('sales_invoice.customer_code', $filter['customer_code']);
		}       
		if($filter['sales_code'] != "")
        {
            $this->db->where('sales_invoice.sales_code', $filter['sales_code']);
		}
        return $this->db->group_by('sales_invoice.id')->order_by('sales_invoice.date', 'ASC')->get()->result_array();
    }

    public function detail_sales_invoice($sales_invoice_id)
    {
        return $this->db->select('product.code AS code_p, product.name AS name_p, 
                        warehouse.code AS code_w, warehouse.name AS name_w, 
                        unit.code AS code_u, unit.name AS name_u,
                        sales_invoice_detail.qty, sales_invoice_detail.unit_value, sales_invoice_detail.price, sales_invoice_detail.hpp, sales_invoice_detail.total')
                        ->from('sales_invoice_detail')
                        ->join('product', 'product.id = sales_invoice_detail.product_id')
                        ->join('warehouse', 'warehouse.id = sales_invoice_detail.warehouse_id')
                        ->join('unit', 'unit.id = sales_invoice_detail.unit_id')
                        ->where('sales_invoice_detail.sales_invoice_id', $sales_invoice_id)
                        ->group_by('sales_invoice_detail.id')->order_by('product.code', 'ASC')
                        ->get()->result_array();
    }
    
    public function print_sales_profit_detail_report($filter)
    {
        $sales_invoices = $this->finance_report->sales_invoice($filter);
        $data = [];
        foreach($sales_invoices AS $sales_invoice)
        {
            $data[] = [
                'sales_invoice'		   => $sales_invoice,
                'detail_sales_invoice' => $this->finance_report->detail_sales_invoice($sales_invoice['id'])
            ];
        }                
        return $data;
    }

    // PROFIT AND LOSS
    public function total_sales_invoice_profit_and_loss($from_date, $to_date)
    {
        $where_general_ledger = [
            'coa_account_code' => "40101",
            'date >=' => $from_date,
            'date <=' => $to_date
        ];
        $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
        $grandtotal =0;
        foreach($general_ledger AS $info)
        {
            $grandtotal = $grandtotal+$info['credit'];
        }
        return $grandtotal;        
    }    

    public function total_sales_return_profit_and_loss($from_date, $to_date)
    {        
        $where_general_ledger = [
            'coa_account_code' => "40103",
            'date >=' => $from_date,
            'date <=' => $to_date
        ];
        $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
        $grandtotal =0;
        foreach($general_ledger AS $info)
        {
            $grandtotal = $grandtotal+$info['debit'];
        }
        return $grandtotal;
    }

    public function total_other_income_profit_and_loss($from_date, $to_date)
    {
        $where_general_ledger = [
            'coa_account_code' => "60101",
            'date >=' => $from_date,
            'date <=' => $to_date
        ];
        $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
        $grandtotal =0;
        foreach($general_ledger AS $info)
        {
            $grandtotal = $grandtotal+$info['credit'];
        }
        return $grandtotal;
    }

    public function total_hpp_profit_and_loss($from_date, $to_date)
    {        
        $where_general_ledger = [
            'coa_account_code' => "50001",
            'date >=' => $from_date,
            'date <=' => $to_date
        ];
        $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
        $grandtotal =0;
        foreach($general_ledger AS $info)
        {
            $grandtotal = $grandtotal+$info['debit']-$info['credit'];
        }
        return $grandtotal;        
    }        

    public function list_of_expense_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('coa_account.name AS name_c, sum(general_ledger.debit-general_ledger.credit) AS total_expense')
                         ->from('general_ledger')
                         ->join('coa_account', 'coa_account.code = general_ledger.coa_account_code')
                         ->where('coa_account.coa_category_code', "5")->where('coa_account.coa_subcategory_code', "01");
        if($from_date != "")
        {
            $this->db->where('general_ledger.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('general_ledger.date <=', $to_date);
        }
        return $this->db->group_by('coa_account.code')->order_by('coa_account.code', 'ASC')->get()->result_array();
    }

    public function total_stock_opname_profit_and_loss($from_date, $to_date)
    {
        $where_general_ledger = [
            'coa_account_code' => "70101",
            'date >=' => $from_date,
            'date <=' => $to_date
        ];
        $general_ledger = $this->crud->get_where('general_ledger', $where_general_ledger)->result_array();
        $grandtotal =0;
        foreach($general_ledger AS $info)
        {
            if($info['debit'] != 0 && $info['credit'] == 0)
            {
                $grandtotal = $grandtotal+$info['debit'];
            }
            elseif($info['debit'] == 0 && $info['credit'] != 0)
            {
                $grandtotal = $grandtotal-$info['credit'];
            }
        }
        return $grandtotal;
    }

    // BALANCE SHEET
    public function get_last_balance_coa($coa_account_code, $date)
    {
        $where_last_balance = [            
            'coa_account_code' => $coa_account_code,
            'date <='    => format_date($date)            
        ];
        $data = $this->db->select('*')->from('general_ledger')->where($where_last_balance)->order_by('date', 'DESC')->order_by('id', 'DESC')->limit(1)->get()->row_array();
        return $data['balance'];
    }
}