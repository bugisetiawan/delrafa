<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model 
{
    public function get_employee()
    {
        return $this->db->select('employee.code, employee.name')
                        ->from('employee')
                        ->join('expense', 'expense.employee_code = employee.code')
                        ->where('expense.deleted', 0)
                        ->where('employee.deleted', 0)
                        ->group_by('expense.employee_code')
                        ->get()->result();
    }

    public function get_cashier()
    {
        return $this->db->select('employee.id, employee.code, employee.name')
                        ->from('employee')->join('pos', 'pos.cashier = employee.code')
                        ->where('pos.deleted', 0)->where('employee.deleted', 0)
                        ->group_by('employee.id')
                        ->get()->result();
    }

    // PRODUCT PROFIT
    public function get_product_profit_report($search = null, $from_date = null,  $to_date = null, $department_code = null, $subdepartment_code = null, $iLength = null, $iStart= null)
    {
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, unit.name AS unit, product.hpp,
                         department.name AS name_d, subdepartment.name AS name_sub_d')                        
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
                         ->join('unit', 'unit.id = product_unit.unit_id', 'inner')
                         ->join('department', 'department.code = product.department_code', 'inner')
                         ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner');
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
        if($iLength != null && $iStart != null)
        {
            if($iLength != '' && $iLength != '-1')
            {
                $this->db->limit($iLength, ($iStart)? $iStart : 0);        
            }
        }        
        return $this->db->where('product.deleted', 0)->group_by('product.code')->get();        
    }

    public function get_product_profit_sales_invoice_detail_report($product_code, $from_date = null, $to_date = null)
    {
        $this->db->select('sales_invoice_detail.product_code, sales_invoice_detail.qty, sales_invoice_detail.unit_id, sales_invoice_detail.total, sales_invoice_detail.hpp')
                         ->from('sales_invoice_detail')->join('sales_invoice', 'sales_invoice.id = sales_invoice_detail.sales_invoice_id')
                         ->where('sales_invoice_detail.product_code', $product_code);
        if($from_date != null)
        {
            $this->db->where('sales_invoice.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('sales_invoice.date <=', $to_date);
        }        
        return $this->db->where('sales_invoice_detail.deleted', 0)->where('sales_invoice.deleted', 0)->group_by('sales_invoice_detail.id')->get();
    }

    public function get_product_profit_pos_detail_report($product_code, $from_date = null, $to_date = null)
    {
        $this->db->select('pos_detail.product_code, pos_detail.qty, pos_detail.unit_id, pos_detail.total, pos_detail.hpp')
                         ->from('pos_detail')->join('pos', 'pos.id = pos_detail.pos_id')
                         ->where('pos_detail.product_code', $product_code);
        if($from_date != null)
        {
            $this->db->where('pos.date >=', $from_date);
        }   
        if($to_date != null)
        {
            $this->db->where('pos.date <=', $to_date);
        }        
        return $this->db->where('pos_detail.deleted', 0)->where('pos.deleted', 0)->group_by('pos_detail.id')->get();
    }

    public function get_product_profit_sales_return_detail_report($product_code, $from_date = null, $to_date = null)
    {
        $this->db->select('sales_return_detail.product_code, sales_return_detail.qty, sales_return_detail.unit_id, sales_return_detail.total, sales_return_detail.hpp')
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
                        ->group_by('sales_return_detail.id')
                        ->order_by('sales_return_detail.id', 'ASC')
                        ->get();
    }

    // INVENTORY VALUE
    public function get_inventory_value_report($search, $department_code, $subdepartment_code, $ppn, $iLength = null, $iStart= null, $iOrder= null)
    {
        $column = array(null, 'code', 'name', 'qty');
        $this->db->select('product.id AS id, product.barcode, product.code, product.name, unit.name AS unit, sum(stock.qty) AS qty,
                         department.name AS name_d, subdepartment.name AS name_sub_d')
                         ->from('product')
                         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
                         ->join('unit', 'unit.id = product_unit.unit_id', 'inner')
                         ->join('stock', 'stock.product_code = product.code', 'left')
                         ->join('department', 'department.code = product.department_code', 'inner')
                         ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner');
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
        if($iOrder != null)
        {                            
            $this->db->order_by($column[$iOrder['0']['column']], $iOrder['0']['dir']);
        } 
        return $this->db->where('product.deleted', 0)
                        ->where('product.status', 1)
                        ->group_by('product.code')
                        ->get();
    }

    // PROFIT AND LOSS
    public function pos_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('sum(pos.grandtotal) AS total_pos')->from('pos')->where('pos.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('pos.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('pos.date <=', $to_date);
        }
        $data = $this->db->get()->row_array();
        return $data['total_pos'];

    }

    public function sales_invoice_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('sum(sales_invoice.grandtotal) AS total_sales_invoice')->from('sales_invoice')->where('sales_invoice.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('sales_invoice.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('sales_invoice.date <=', $to_date);
        }
        $data = $this->db->get()->row_array();
        return $data['total_sales_invoice'];
    }

    public function sales_return_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('sum(sales_return.total_return) AS total_sales_return')->from('sales_return')->where('sales_return.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('sales_return.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('sales_return.date <=', $to_date);
        }
        $data = $this->db->get()->row_array();
        return $data['total_sales_return'];
    }

    public function hpp_pos_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('pos_detail.product_id, pos_detail.product_code, pos_detail.qty, pos_detail.unit_id,  pos_detail.hpp')->from('pos_detail')
                 ->join('pos', 'pos.id = pos_detail.pos_id')
                 ->where('pos_detail.deleted', 0)->where('pos.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('pos.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('pos.date <=', $to_date);
        }        
        $data = $this->db->group_by('pos_detail.id')->get()->result_array();
        $total_hpp = 0;
        foreach($data AS $info)
        {
            $where_convert = array(
                'product_code' => $info['product_code'],
                'unit_id'      => $info['unit_id']
            );
            $convert = $this->crud->get_where('product_unit', $where_convert);
            if($convert->num_rows() == 1)
            {
                $convert   = $convert->row_array();
                $total_hpp = $total_hpp + ($info['hpp']*($info['qty']*$convert['value']));
            }
            else
            {
                $total_hpp = $total_hpp + $info['hpp']*($info['qty']*1);                    
            }
        }
        return $total_hpp;
    }

    public function hpp_sales_invoice_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('sales_invoice_detail.qty, sales_invoice_detail.hpp')->from('sales_invoice_detail')
                 ->join('sales_invoice', 'sales_invoice.id = sales_invoice_detail.sales_invoice_id')
                 ->where('sales_invoice_detail.deleted', 0)->where('sales_invoice.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('sales_invoice.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('sales_invoice.date <=', $to_date);
        }        
        $data = $this->db->group_by('sales_invoice_detail.id')->get()->result_array();
        $total_hpp = 0;
        foreach($data AS $info)
        {
            $total_hpp = $total_hpp + ($info['qty']*$info['hpp']);
        }
        return $total_hpp;
    }

    public function hpp_sales_return_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('sales_return_detail.product_id, sales_return_detail.product_code, sales_return_detail.qty, sales_return_detail.unit_id,  sales_return_detail.hpp')->from('sales_return_detail')
                 ->join('sales_return', 'sales_return.id = sales_return_detail.sales_return_id')
                 ->where('sales_return_detail.deleted', 0)->where('sales_return.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('sales_return.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('sales_return.date <=', $to_date);
        }        
        $data = $this->db->group_by('sales_return_detail.id')->get()->result_array();
        $total_hpp = 0;
        foreach($data AS $info)
        {
            $where_convert = array(
                'product_code' => $info['product_code'],
                'unit_id'      => $info['unit_id']
            );
            $convert = $this->crud->get_where('product_unit', $where_convert);
            if($convert->num_rows() == 1)
            {
                $convert   = $convert->row_array();
                $total_hpp = $total_hpp + ($info['hpp']*($info['qty']*$convert['value']));
            }
            else
            {
                $total_hpp = $total_hpp + $info['hpp']*($info['qty']*1);                    
            }
        }
        return $total_hpp;
    }

    public function expense_profit_and_loss($from_date, $to_date)
    {
        $this->db->select('sum(expense.amount) AS total_expense')->from('expense')->where('expense.deleted', 0);
        if($from_date != "")
        {
            $this->db->where('expense.date >=', $from_date);
        }
        if($to_date != "")
        {
            $this->db->where('expense.date <=', $to_date);
        }
        $data = $this->db->get()->row_array();
        return $data['total_expense'];
    }
        
}
