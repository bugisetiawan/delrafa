<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_report_model extends CI_Model 
{

    public function sales_invoice($view_type, $from_date, $to_date)
    {
        $result = [null];
        if($view_type == "day")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%d-%m-%Y") AS time, sum(grandtotal) AS total_sales')
                          ->from('sales_invoice')
                          ->where('sales_invoice.date >=', $from_date)
                          ->where('sales_invoice.date <=', $to_date)
                          ->where('sales_invoice.do_status', 1)
                          ->group_by('time')
                          ->order_by('time', 'ASC');
            $result =$this->db->get()->result_array();
        }
        elseif($view_type == "month")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%m-%Y") AS time, sum(grandtotal) AS total_sales')
                          ->from('sales_invoice')
                          ->where('sales_invoice.date >=', $from_date)
                          ->where('sales_invoice.date <=', $to_date)
                          ->where('sales_invoice.do_status', 1)
                          ->group_by('time')
                          ->order_by('time', 'ASC');
            $result = $this->db->get()->result_array();
        }        
        elseif($view_type == "year")
        {
            $this->db->select('DATE_FORMAT(sales_invoice.date, "%Y") AS time, sum(grandtotal) AS total_sales')
                          ->from('sales_invoice')
                          ->where('date >=', $from_date)
                          ->where('date <=', $to_date)
                          ->where('sales_invoice.do_status', 1)
                          ->group_by('time')
                          ->order_by('time', 'ASC');
            $result =$this->db->get()->result_array();
        }
         
        return $result;
    }

}
