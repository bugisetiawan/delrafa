<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model 
{            
    public function datatable($search, $department_code, $subdepartment_code, $product_type, $ppn, $status, $input_order, $iLength= null, $iStart= null, $iOrder = null)
    {
        $column = array(null, 'barcode', 'code', 'name', 'qty', 'unit', 'sellprice');
        $this->db->select('product.id, product.barcode, product.code, product.name, product.ppn,
                 unit.name AS unit, sellprice.price_1 AS sellprice')
		         ->from('product')
		         ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                 ->join('unit', 'unit.id = product_unit.unit_id')                 
		         ->join('sellprice', 'sellprice.product_code = product.code AND sellprice.default = 1', 'left');
		if($search != "")
		{
            $this->db->like('product.barcode', $search)->where('product.deleted', 0);
            $this->db->or_like('product.code', $search)->where('product.deleted', 0);
            $this->db->or_like('product.name', $search);			
        }
        if($department_code != "")
        {
            $this->db->where('product.department_code', $department_code);
        }
        if($department_code != "" && $subdepartment_code != "")
        {
            $this->db->where('product.department_code', $department_code);
            $this->db->where('product.subdepartment_code', $subdepartment_code);
        }
        if($product_type != "")
		{
			$this->db->where('product.type', $product_type);
        }        
		if($ppn != "")
		{
			$this->db->where('product.ppn', $ppn);
        }
        if($status != "")
		{
			$this->db->where('product.status', $status);
        }
        if($input_order != "")
		{
			$this->db->order_by('product.created', $input_order);
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
        return $this->db->where('product.deleted', 0)->group_by('product.id')->get();
    }

    public function detail_stock($product_code)
    {
        $warehouse = $this->db->select('warehouse.name AS warehouse, stock.product_code, qty, product_location.location')
                              ->from('stock')							  							  
                              ->join('warehouse', 'warehouse.id = stock.warehouse_id')
                              ->join('product_location', 'product_location.product_code = stock.product_code AND product_location.warehouse_id = stock.warehouse_id', 'left')
                              ->where('stock.deleted', 0)
                              ->where('warehouse.deleted', 0)
							  ->where('stock.product_code', $product_code)							  
							  ->group_by('stock.warehouse_id')
							  ->get()->result_array();

		$product_unit = $this->db->select('unit.name AS unit, value')
								  ->from('product_unit')
								  ->join('unit', 'unit.id = product_unit.unit_id')
								  ->where('product_code', $product_code)
								  ->where('product_unit.deleted', 0)
								  ->order_by('product_unit.id', 'ASC')
								  ->get()->result_array();
        $data_stock = [];
		foreach($warehouse AS $info)
		{
            $stock = array();
            $total_stock = 0;
			foreach($product_unit AS $info2)
			{
                $total_stock = $total_stock+$info['qty'];
                $stock[] = array(					
					'value' => number_format($info['qty'] / $info2['value'],2,",","."),
					'unit'  => $info2['unit']
				);				
            }

            $data_stock[] = array(
                'warehouse' => $info['warehouse'],
                'location'  => $info['location'],
                'total_stock' => $total_stock,
                'stock'     => $stock                
            );
		}
		return $data_stock;
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
    
    public function last_buyprice($code)
    {
        $data_product = $this->db->select('product.buyprice, supplier.code AS code_s, supplier.name AS name_s')
                                 ->from('product')
                                 ->join('supplier', 'supplier.code = product.supplier_code', 'left')
                                 ->where('product.code', $code)->group_by('product.id')
                                 ->get()->row_array();
        if($data_product != null)
        {
            $result = array(
                'supplier_code' => $data_product['code_s'],
                'supplier'      => $data_product['name_s'],
                'price'         => $data_product['buyprice']                               
            );
        }   
        else
        {
            $result = array(
                'supplier_code' => "-",
                'supplier' => "-",
                'price' => 0
            );
        }                             
        return $result;                                                
    }
    
    public function lower_buyprice()
    {        
        // NEW
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

        // OLD
        // $product = $this->db->select('product.code, product.barcode, product.name')->from('product')->where(['deleted' => 0])->get()->result_array();
        // $data = array();
        // foreach($product AS $info)
        // {
        //     $buyprice  = $this->product->last_buyprice($info['code']);
        //     $product_units = $this->db->select('unit.name AS name_u, product_unit.unit_id, product_unit.value, product_unit.weight, product_unit.value, product_unit.default')
        //                              ->from('product_unit')->join('unit', 'unit.id = product_unit.unit_id')
        //                              ->where('unit.deleted', 0)->where('product_unit.deleted', 0)->where('product_unit.product_code', $info['code'])
        //                              ->group_by('product_unit.id')->get()->result_array();
        //     foreach($product_units AS $product_unit)
        //     {
        //         $sellprice = $this->crud->get_where('sellprice', ['product_code' => $info['code'], 'unit_id' => $product_unit['unit_id'], 'deleted' => 0])->row_array();
        //         if($sellprice['price_5'] <= ($buyprice['price']*$product_unit['value']))
        //         {
        //             $data[] = array(
        //                 'code'    => $info['code'],
        //                 'barcode' => $info['barcode'],
        //                 'name'    => $info['name'],
        //                 'name_u'  => $product_unit['name_u']
        //             );                               
        //         }
        //     }            
        // }
        // return $data;
    }

    public function get_product_id($code)
    {
        $data = $this->db->select('id')->from('product')->where('code', $code)->get()->row_array();
        return $data['id'];
    }    
    
    public function generate_code($code_depart, $code_sub)
	{		
		$search = $code_depart.$code_sub;
        $query          = $this->db->select('code')->from('product')->like('code', $search, 'after')
									->limit(1)->order_by('product.id', 'desc')->get();
        $data   = $query->row_array();
        $sub    = (int) substr($data['code'], 1, 11)+1;
        if($data['code'] == null)
        {
            $max  = $code_depart.$code_sub.sprintf("%06s", $sub);
        }
        else
        {
            $max    = sprintf("%012s", $sub);
        }
        return $max;
    }
    
    public function detail_product($code)
    {
        return $this->db->select('product.id AS id_p, product.code AS code_p, department.name AS department, subdepartment.name AS subdepartment,
                        department.code AS code_d, subdepartment.code AS code_sd,
                        sellprice.price_1 AS sellprice, barcode, product.name AS name_p, productid, description, minimal, maximal, product.type, 
                        unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, photo, ppn, status, weight, commission_sales')
						->from('product')
						->join('department', 'department.code = product.department_code')
						->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code')
						->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                        ->join('unit', 'unit.id = product_unit.unit_id')
                        ->join('sellprice', 'sellprice.product_id = product.id AND sellprice.unit_id = product_unit.unit_id', 'left')
                        ->where('product.code', $code)                        
                        ->get()->row_array();
    }

    public function detail_product_bundle($product_code)
    {
        $product_id = $this->crud->get_product_id($product_code);
        return $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, product_bundle.qty')
                        ->from('product_bundle')
                        ->join('product', 'product.id = product_bundle.product_id')
                        ->join('unit', 'unit.id = product_bundle.unit_id')
                        ->where('product_bundle.master_product_id', $product_id)
                        ->group_by('product_bundle.id')
                        ->get()->result_array();
    }

    public function get_unit_product($code)
    {  
        $unit = $this->db->select('unit_id')
                         ->from('sellprice')
                         ->where('product_code', $code)
                         ->where('deleted', 0)
                         ->get()->result_array();
        $data = array('');
        foreach($unit as $info)
        {
            $data[] = $info['unit_id'];
        }       
        return $this->db->select('unit.id AS id_u, unit.name AS name_u')
                        ->from('product_unit')
                        ->join('unit', 'unit.id = product_unit.unit_id')                        
                        ->where_not_in('unit.id', $data)
                        ->where('product_unit.product_code', $code)
                        ->where('product_unit.deleted', 0)
                        ->order_by('product_unit.id', 'ASC')
                        ->get()->result_array();
    }

    public function get_product_unit_value($product_code, $unit_id)
    {
        return $this->db->select('value')->from('product_unit')
                        ->where('deleted', 0)->where('product_code', $product_code)->where('unit_id', $unit_id)
                        ->get()->row_array();
    }

    public function multi_price($code)
    {
        return $this->db->select('sellprice.id AS id_sellprice, unit.id AS id_u, unit.name AS name_u, product_unit.value,
                        price_1, price_2, price_3, price_4, price_5')
                        ->from('sellprice')
                        ->join('unit', 'unit.id = sellprice.unit_id')
                        ->join('product_unit', 'product_unit.product_code = sellprice.product_code AND product_unit.unit_id = sellprice.unit_id')
                        ->where('sellprice.product_code', $code)
                        ->where('sellprice.deleted', 0) 
                        ->group_by('sellprice.id')
                        ->order_by('id_sellprice', 'ASC')                       
                        ->get()->result_array();
    }

    public function get_detail_sellprice($id)
    {
        return $this->db->select('sellprice.product_code, unit.id AS id_u, unit.name AS name_u, product_unit.value,
                        sellprice.price_1, sellprice.price_2, sellprice.price_3, sellprice.price_4, sellprice.price_5')
                        ->from('sellprice')
                        ->join('unit', 'unit.id = sellprice.unit_id')
                        ->join('product_unit', 'product_unit.product_code = sellprice.product_code AND product_unit.unit_id = sellprice.unit_id')
                        ->where('sellprice.deleted', 0)
                        ->where('unit.deleted', 0)
                        ->where('product_unit.deleted', 0)
                        ->where('sellprice.id', $id)
                        ->get()->row_array();
    }

    public function get_unit_option_product($code)
    {
        $unit = $this->db->select('unit_id')
                         ->from('product_unit')
                         ->where('product_code', $code)
                         ->where('deleted', 0)
                         ->get()->result_array();
        $data = array('');
        foreach($unit as $info)
        {
            $data[] = $info['unit_id'];
        }        
        return $this->db->select('unit.id AS id_u, unit.name AS name_u')
                        ->from('unit')                        
                        ->where_not_in('unit.id', $data)
                        ->where('unit.deleted', 0)
                        ->get()->result_array();
    }

    public function multi_unit($code)
    {
        return $this->db->select('product_unit.id AS id_mu, unit.id AS id_u, unit.name AS name_u, product_unit.value')
                        ->from('product_unit')
                        ->join('unit', 'unit.id = product_unit.unit_id')
                        ->where('product_code', $code)
                        ->where('product_unit.deleted', 0)
                        ->where('product_unit.default', 0)
                        ->get()->result_array();
    }

    public function get_detail_multi_unit($id)
    {
        return $this->db->select('product_unit.id AS id_mu, unit.id AS id_u, unit.name AS name_u, value, weight')
                        ->from('product_unit')
                        ->join('unit', 'unit.id = product_unit.unit_id')
                        ->where('product_unit.deleted', 0)
                        ->where('product_unit.id', $id)
                        ->get()->row_array();
    }    

    public function get_supplier($code)
    {
        return $this->db->select('purchase.supplier_code AS code_s')
                        ->from('purchase')
                        ->join('purchase_detail', 'purchase_detail.purchase_id = purchase.id')
                        ->where('purchase.deleted', 0)
                        ->where('purchase_detail.deleted', 0)
                        ->where('purchase_detail.product_code', $code)
                        ->group_by('purchase.supplier_code')
                        ->get()->result_array();
    }

    public function get_product_warehouse($code)
    {
        $warehouse = $this->db->select('warehouse_id')
                         ->from('product_location')
                         ->where('product_code', $code)
                         ->where('deleted', 0)
                         ->get()->result_array();
        $data = array('');
        foreach($warehouse as $info)
        {
            $data[] = $info['warehouse_id'];
        }       
        return $this->db->select('warehouse.id AS id_w, warehouse.name AS name_w')
                        ->from('stock')
                        ->join('warehouse', 'warehouse.id = stock.warehouse_id')
                        ->where('stock.product_code', $code)
                        ->where_not_in('warehouse.id', $data)
                        ->where('stock.deleted', 0)
                        ->group_by('warehouse.id')
                        ->order_by('warehouse.id', 'ASC')
                        ->get()->result_array();        
    }

    public function product_location($code)
    {
        return $this->db->select('product_location.id AS id_pl, warehouse.name AS name_w, location')
                        ->from('product_location')
                        ->join('warehouse', 'warehouse.id = product_location.warehouse_id')
                        ->where('product_location.product_code', $code)
                        ->where('product_location.deleted', 0)
                        ->get()->result_array();
    }

    public function get_detail_product_location($id)
    {
        return $this->db->select('product_location.id AS id_pl, warehouse.name AS name_w, location')
                        ->from('product_location')
                        ->join('warehouse', 'warehouse.id = product_location.warehouse_id')
                        ->where('product_location.id', $id)
                        ->get()->row_array();
    }

    public function get_warehouse_stock_card($code)
    {
        return $this->db->select('warehouse.id AS id_w, warehouse.code AS code_w, warehouse.name AS name_w, qty')
                        ->from('stock')
                        ->join('warehouse', 'warehouse.id = stock.warehouse_id')
                        ->where('stock.product_code', $code)
                        ->where('stock.deleted', 0)                        
                        ->group_by('warehouse.id')
                        ->order_by('warehouse.id', 'ASC')
                        ->get()->result_array();
    }
    public function stock_card($code)
    {
        return $this->db->select('stock_card.*, user.employee_code AS code_e')
                        ->from('stock_card')
                        ->join('user', 'user.id = stock_card.user_id')
                        ->where('product_code', $code)                        
                        ->order_by('stock_card.id', 'DESC')
                        ->get()->result_array();
    }

    public function hpp($code)
    {
        $data = $this->db->select('hpp')->from('product')->where('code', $code)->where('deleted', 0)->get()->row_array();
        return $data['hpp'];
    }

    public function get_stock_product($filter)
    {                
        $this->db->select('product.code AS code_p, product.name AS name_p, unit.code AS code_u, unit.name AS name_u, department.name AS name_d, subdepartment.name AS name_sd')
                 ->from('product')
                 ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1')
                 ->join('unit', 'unit.id = product_unit.unit_id')
                 ->join('department', 'department.code = product.department_code')
                 ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code')
                 ->where('product.status', 1)->where('product.deleted', 0);
        if($filter['search'] != "")
        {            
            $this->db->like('product.name', $filter['search']);
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
        $data = $this->db->group_by('product.id')->get()->result_array();
        $result = [];
        foreach($data AS $info)
        {
            if($filter['warehouse_id'] != 0)
    		{
    			$where = array(
    				'product_code' => $info['code_p'],
    				'warehouse_id' => $filter['warehouse_id'],
    				'deleted'	   => 0
    			);
    		}	
    		else
    		{
    			$where = array(
    				'product_code' => $info['code_p'],					
    				'deleted'	   => 0
    			);				
    		}		
    		$data_stock = $this->crud->get_where('stock', $where);
    		$stock = 0;
    		if($data_stock->num_rows() > 0)
			{
				foreach($data_stock->result_array() AS $info_stock)
				{
					$stock = $stock + $info_stock['qty'];
				}
			}
			if($stock >= $filter['min'] && $stock <= $filter['max'])
			{
			    $result[] = [
			        'name_p' => $info['name_p'],
			        'code_u' => $info['code_u'],
			        'name_u' => $info['name_u'],
			        'name_d' => $info['name_d'],
			        'name_sd' => $info['name_sd'],
			        'qty'  => $stock
		        ];
			}
        }
        return $result;
    }

    public function get_price_list($filter, $coloumn)
    {
        $this->db->select('product.code')
                        ->from('product')
		                ->join('product_unit', 'product_unit.product_code = product.code AND product_unit.default = 1', 'inner')
                        ->join('unit', 'unit.id = product_unit.unit_id', 'inner')                        
                        ->join('sellprice', 'sellprice.product_code = product.code AND sellprice.default = 1', 'left')
		                ->join('department', 'department.code = product.department_code')
		                ->join('subdepartment', 'subdepartment.department_code = product.department_code AND subdepartment.code = product.subdepartment_code', 'inner')
                        ->where('product.status', 1)->where('product.deleted', 0);
        if($filter['search'] != "")
        {            
            $this->db->like('product.name', $filter['search']);
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
        $product = $this->db->group_by('product.id')->get()->result_array();
        
        $data_export = [];
        foreach($product AS $info_p)
        {
            $product_unit_sellprice = $this->db->select('product.name, round(sum(stock.qty),2) AS qty, product_unit.value, unit.name AS name_u,  product.hpp, product.buyprice, product_unit.value,                
                                        sellprice.price_1 AS price_1, sellprice.price_2 AS price_2, sellprice.price_3 AS price_3, sellprice.price_4 AS price_4, sellprice.price_5 AS price_5 ')
                                        ->from('product')
                                        ->join('stock', 'stock.product_code = product.code', 'left')
                                        ->join('product_unit', 'product_unit.product_code = product.code')
                                        ->join('unit', 'unit.id = product_unit.unit_id')
                                        ->join('sellprice', 'sellprice.product_code = product.code AND sellprice.unit_id = product_unit.unit_id', 'left')
                                        ->where('product.deleted', 0)											 
                                        ->where('product_unit.deleted', 0)
                                        ->where('unit.deleted', 0)
                                        ->where('product.code', $info_p['code'])
                                        ->group_by('product_unit.id')
                                        ->order_by('product.code', 'asc')
                                        ->order_by('product_unit.value', 'asc')
                                        ->get()->result_array();
                                        
            foreach($product_unit_sellprice AS $info_pus)
            {
                $data_export[] = [
                    'name' => $info_pus['name'],
                    'qty' => $info_pus['qty']/$info_pus['value'],
                    'name_u' => $info_pus['name_u'],
                    'price_1' => $info_pus['price_1'],
                    'price_2' => $info_pus['price_2'],
                    'price_3' => $info_pus['price_3'],
                    'price_4' => $info_pus['price_4'],
                    'price_5' => $info_pus['price_5'],
                    'buyprice' => $info_pus['buyprice']*$info_pus['value'],
                    'hpp'     => $info_pus['hpp']*$info_pus['value']
                ];
            }					
        }
        return $data_export;
    }

    public function get_product_weight($code = null, $unit_id = null)
    {
        if($unit_id != null)
        {
            $data = $this->db->select('weight')
                          ->from('product_unit')
                          ->where('product_unit.deleted', 0)
                          ->where('product_code', $code)
                          ->where('unit_id', $unit_id)
                          ->get()->row_array();
        }        
        else
        {
            $data = $this->db->select('weight')
                          ->from('product_unit')
                          ->where('product_unit.deleted', 0)
                          ->where('product_code', $code)
                          ->where('default', 1)
                          ->get()->row_array();
        }
        return $data['weight'];
    }

    public function list_of_purchase_invoice($product_code)
    {
        return $this->db->select('supplier.id, supplier.code, supplier.name, pid.price')
                        ->from('supplier')
                        ->join('purchase_invoice AS pi', 'pi.supplier_code = supplier.code')
                        ->join('purchase_invoice_detail AS pid', 'pid.purchase_invoice_id = pi.id')
                        ->where('pid.product_code', $product_code)
                        ->order_by('pi.date', 'DESC')
                        ->group_by('supplier.id')
                        ->get()->result_array();
    }
    
    public function list_of_sales_invoice($product_code)
    {
        return $this->db->select('customer.id, customer.code, customer.name, sid.price')
                        ->from('customer')
                        ->join('sales_invoice AS si', 'si.customer_code = customer.code')
                        ->join('sales_invoice_detail AS sid', 'sid.sales_invoice_id = si.id')
                        ->where('si.do_status', 1)
                        ->where('sid.product_code', $product_code)
                        ->order_by('si.date', 'DESC')
                        ->group_by('customer.id')
                        ->get()->result_array();
    }
}