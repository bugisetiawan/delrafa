<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model 
{	
    public function detail_transaction($pos_id)
    {
        return $this->db->select('pos.*, 
                        employee.code AS code_e, employee.name AS name_e, 
                        customer.code AS code_c, customer.name AS name_c')
                        ->from('pos')                        
                        ->join('employee', 'employee.code = pos.cashier')
                        ->join('customer', 'customer.code = pos.customer_code')
                        ->where('pos.id', $pos_id)
                        ->get()->row_array();
    }

    public function detail_transaction_detail($pos_id)
    {
        return $this->db->select('pos_detail.*, 
                        product.id AS id_p, product.code AS code_p, product.name AS name_p,
                        unit.id AS id_u, unit.code AS code_u, unit.name AS name_u,
                        warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w')
                        ->from('pos_detail')                        
                        ->join('product', 'product.code = pos_detail.product_code')
                        ->join('unit', 'unit.id = pos_detail.unit_id')
                        ->join('warehouse', 'warehouse.id = pos_detail.warehouse_id')
                        ->where('pos_detail.deleted', 0)->where('pos_detail.pos_id', $pos_id)
                        ->group_by('pos_detail.id')->order_by('pos_detail.id', 'ASC')
                        ->get()->result_array();
    }
}