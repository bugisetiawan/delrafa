<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General_model extends CI_Model 
{    
    public function __construct()
	{
		parent::__construct();
    }
    
    public function get_out_stock_product_id($filter)
    {
        $data = [null];
        $this->db->select('product.id AS id_p, sum(stock.qty) AS total_stock')
                 ->from('product')->join('stock', 'stock.product_code = product.code');
        if($filter['search_product'] != "")
        {
            $this->db->or_like('product.name', $filter['search_product']);			
        }
        if($filter['department_code'] != "")
        {
            $this->db->where('product.department_code', $filter['department_code']);
        }
        if($filter['department_code'] != "" && $filter['subdepartment_code'] != "")
        {
            $this->db->where('product.department_code', $filter['department_code']);
            $this->db->where('product.subdepartment_code', $filter['subdepartment_code']);
        }
        $stocks = $this->db->where('product.status', 1)->where('product.deleted', 0)->group_by('product.id')->get()->result_array();
        foreach($stocks AS $stock)
        {
            $product = $this->crud->get_where('product', ['id' => $stock['id_p']])->row_array();
            if($stock['total_stock'] <= $product['minimal'])
            {
                $data[]= $stock['id_p'];
            }
        }
        return $data;
    }

    public function get_more_stock_product_id()
    {
        $data = array(null);
        $stocks = $this->db->select('product.id AS id_p, sum(stock.qty) AS total_stock')
                            ->from('product')->join('stock', 'stock.product_code = product.code', 'left')
                            ->where('product.status', 1)->where('product.deleted', 0)
                            ->group_by('product.id')->get()->result_array();
        foreach($stocks AS $stock)
        {
            $product = $this->crud->get_where('product', ['id' => $stock['id_p']])->row_array();
            if($stock['total_stock'] >= $product['maximal'])
            {
                $data[]= $stock['id_p'];
            }
        }
        return $data;
    }

    public function low_selling_price()
    {
        $product = $this->db->select('product.code AS code_p, product.name AS name_p, product.buyprice,
                            product_unit.value, unit.name AS name_u,
                            sellprice.price_1, sellprice.price_2, sellprice.price_3, sellprice.price_4, sellprice.price_5')
                            ->from('product')
                            ->join('product_unit', 'product_unit.product_id = product.id')
                            ->join('unit', 'unit.id = product_unit.unit_id')
                            ->join('sellprice', 'sellprice.product_id = product.id AND sellprice.unit_id = product_unit.unit_id')
                            ->where(['product.deleted' => 0])
                            ->group_by('product_unit.id')
                            ->get()->result_array();
        $data = [];
        foreach($product AS $info)
        {
            if($info['price_5'] != 0)
            {
                if($info['price_5'] <= ($info['buyprice']*$info['value']))
                {
                    $data[] = array(
                        'code'    => $info['code_p'],
                        'name'    => $info['name_p'],
                        'name_u'  => $info['name_u'],
                        'buyprice' => ($info['buyprice']*$info['value']),
                        'price_1'  => $info['price_1'],
                        'price_2'  => $info['price_2'],
                        'price_3'  => $info['price_3'],
                        'price_4'  => $info['price_4'],
                        'price_5'  => $info['price_5'],
                    );                               
                }
            }            
        }
        return $data;
    }
    
    public function datatable($search, $department_code, $subdepartment_code, $iLength= null, $iStart= null, $iOrder = null)
    {
        $column = array(null, 'barcode', 'code', 'name', 'qty', 'unit', 'sellprice', 'name_d', 'name_sub_d');
        $this->db->select('product.id, product.barcode, product.code, product.name, product.ppn,
                unit.name AS unit, sum(stock.qty) AS qty,
                sellprice.price_1 AS sellprice_1, sellprice.price_2 AS sellprice_2, sellprice.price_3 AS sellprice_3, sellprice.price_4 AS sellprice_4, sellprice.price_5 AS sellprice_5,
                department.name AS name_d, subdepartment.name AS name_sub_d')
                ->from('product')
                ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                ->join('unit', 'unit.id = product_unit.unit_id')
                ->join('stock', 'stock.product_code = product.code', 'left')
                ->join('sellprice', 'sellprice.product_code = product.code AND sellprice.default = 1', 'left')
                ->join('department', 'department.code = product.department_code')
                ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code')
                ->group_by('product.id');
		if($search != "")
		{
            $this->db->like('product.name', $search);
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
        if($iOrder != null)
        {                            
            $this->db->order_by($column[$iOrder['0']['column']], $iOrder['0']['dir']);
        }
        $this->db->where('product.deleted', 0);
        return $this->db->get();
    }

    public function get_stock_primary($product_name)
    {
        return $this->db->select('stock.*')
                        ->from('stock')
                        ->join('product', 'product.id = stock.product_id')
                        ->where('product.name', $product_name)
                        ->group_by('stock.id')
                        ->get();
    }

    public function get_stock_secondary($product_name)
    {
        $db2 = $this->load->database('secondary', TRUE);
        return $db2->select('stock.*')
                        ->from('stock')
                        ->join('product', 'product.id = stock.product_id')
                        ->where('product.name', $product_name)
                        ->group_by('stock.id')
                        ->get();
    }

    public function detail_sellprice($product_code)
    {
        return $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p,
                            unit.id AS id_u, unit.name AS name_u,
                            sellprice.price_1, sellprice.price_2, sellprice.price_3, sellprice.price_4, sellprice.price_5')
                              ->from('product')
                              ->join('sellprice', 'sellprice.product_id = product.id')
                              ->join('unit', 'unit.id = sellprice.unit_id')
							  ->where('product.code', $product_code)
							  ->group_by('sellprice.id')
							  ->get()->result_array();
    }
}