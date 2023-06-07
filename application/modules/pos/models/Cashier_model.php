<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashier_model extends CI_Model 
{	
    public function get_product($search)
	{		
		return $this->db->select('product.id AS id_p, product.barcode AS barcode_p, product.code AS code_p, product.name AS name_p, sellprice.price_1 AS price_1')
					->from('product')
					->join('sellprice', 'sellprice.product_code = product.code AND sellprice.default = 1')
					->like('product.barcode', $search)->where('product.status', 1)->where('product.deleted', 0)
					->or_like('product.code', $search)->where('product.status', 1)->where('product.deleted', 0)
					->or_like('product.name', $search)->where('product.status', 1)->where('product.deleted', 0)
					->get();
    }
	
	public function check_promotion($product_code)
	{
		$promotion = $this->db->select('id, discount')
						->from('promotion_product AS pp')						
						->where('start_date <=', date('Y-m-d'))
						->where('start_time <=', date('H:i:s'))
						->where('end_date >=', date('Y-m-d'))
						->where('end_time >=', date('H:i:s'))
						->group_by('pp.id')->get()->row_array();
		if(isset($promotion))
		{
			$product = $this->db->select('product_code')->from('promotion_product_detail AS ppd')
							->where('ppd.promotion_product_id', $promotion['id'])
							->where('product_code', $product_code)->get()->row_array();
			return isset($product) ? $promotion : null;
		}
		else
		{
			return null;
		}		
				
	}

    public function scan_product($customer_code, $product_code, $type)
    {
        $product = $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, product.photo')
                        ->from('product')
                        ->where('product.deleted', 0)
                        ->where('product.status', 1)
                        ->where($type, $product_code)
                        ->get()->row_array();
        if($product !=null)
        {
            if($customer_code == "CUST-00000")
            {
                $price_class ="price_1";			
            }
            else
            {
                $class = $this->crud->get_where('customer', ['code' => $customer_code])->row_array();
                $price_class = "price_".$class['price_class'];
            }
            $price = $this->db->select($price_class)->from('sellprice')->where('default', 1)->where('deleted', 0)
							->where(['product_id' => $product['id_p'], 'product_code' => $product['code_p']])->get()->row_array();
			
			$promotion = $this->cashier->check_promotion($product['code_p']);
			if($promotion != null)
			{
				$data = [
					'id_p'   => $product['id_p'],
					'code_p' => $product['code_p'],
					'name_p' => $product['name_p'],
					'photo'  => $product['photo'],
					'price'  => $price[$price_class]-($price[$price_class]*$promotion['discount']/100)
				];                
			}
			else
			{
				$data = [
					'id_p'   => $product['id_p'],
					'code_p' => $product['code_p'],
					'name_p' => $product['name_p'],
					'photo'  => $product['photo'],
					'price'  => $price[$price_class]
				];
			}            
            return $data;
        }
        else
        {
            return null;
        }        
    }

    public function get_unit($where)
    {	
		return $this->db->select('unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, value, default')
						->from('product_unit')
						->join('unit','unit.id=product_unit.unit_id')
						->where($where)
						->get();
	}
	
	public function get_sellprice($customer_code, $product_code, $unit_id)
	{
		if($customer_code == "CUST-00000")
		{
			$price_class ="price_1";			
		}
		else
		{
			$class = $this->crud->get_where('customer', ['code' => $customer_code])->row_array();
			$price_class = "price_".$class['price_class'];
		}
		$price = $this->db->select($price_class)->from('sellprice')->where('deleted', 0)
						->where(['product_code' => $product_code, 'unit_id' => $unit_id])->get()->row_array();		
		return $price[$price_class];
	}
	
    public function invoice()
	{		
		$tanggal = date('d'); $code_bulan = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
		for($i=1; $i<=12; $i++)
		{
			if(date('m') == $i)
			{
				$bulan = $code_bulan[$i-1];
				break;
			}
			else
			{
				continue;
			}
		}
		$tahun = substr(date('Y'),2,2);
		$code = $tahun.$bulan.$tanggal;		
		$data = $this->db->select('invoice')->from('pos')->limit('1')->order_by('id', 'DESC')->get()->row_array();
		if($data)
		{
			$sub_tanggal 	= substr($data['invoice'], 3, 2);
			$no_urut 	= substr($data['invoice'], -5,5);
			if($sub_tanggal == $tanggal)
			{
				$no_urut++;
				$invoice = $code.sprintf("%05s", $no_urut);
			}
			else
			{
				$no_urut =1;
				$invoice = $code.sprintf("%05s", $no_urut);
			}	
		}
		else
		{
			$no_urut =1;
			$invoice = $code.sprintf("%05s", $no_urut);
		}
		return $invoice;
	}

	public function detail_pos($pos_id)
	{
		return $this->db->select('pos.id AS id_p, pos.invoice, pos.date, pos.time, cashier.code AS code_cashier, cashier.name AS name_cashier,
                        customer.code AS code_cust, customer.name AS name_cust, pos.total_product, pos.total_qty, pos.grandtotal, pos.pay, pos.payment, card_id')
						->from('pos')
						->join('employee AS cashier', 'cashier.code = pos.cashier')
                        ->join('customer', 'customer.code = pos.customer_code')
                        ->where('pos.id', $pos_id)
                        ->get()->row_array();
	}

	public function detail_pos_detail($pos_id)
	{
		return $this->db->select('product.id AS id_p, product.code AS code_p, product.name AS name_p, pos_detail.qty AS qty, 
						unit.id AS id_u, unit.code AS code_u, unit.name AS name_u, pos_detail.price, pos_detail.discount_p, pos_detail.total')
						->from('pos_detail')
						->join('product', 'product.id = pos_detail.product_id')
						->join('unit', 'unit.id = pos_detail.unit_id')
						->where('pos_detail.pos_id', $pos_id)
						->group_by('pos_detail.id')->order_by('pos_detail.id', 'ASC')
						->get()->result_array();
	}

	public function detail_collect($collect_id)
	{
		return $this->db->select('collect.date, collect.time, collector.name AS collector, cashier.name AS cashier, collect.total')
						->from('collect')
						->join('employee AS collector', 'collector.code = collect.collector')
						->join('employee AS cashier', 'cashier.code = collect.cashier')
						->where('collect.id', $collect_id)
						->group_by('collect.id')
						->get()->row_array();
	}

	public function detail_cashier($cashier_id)
	{
		return $this->db->select('cashier.*, employee.name AS name_c')
						->from('cashier')
						->join('employee', 'employee.code = cashier.cashier')						
						->where('cashier.id', $cashier_id)
						->group_by('cashier.id')
						->get()->row_array();
	}
	
	public function summary_dp($date, $cashier, $open_time, $close_time)
	{
		$where_dp = [
			'date' 			=> $date, 
			'employee_code' => $cashier,
			'payment'       => 2,
			'created >=' 	=> $date.' '.$open_time, 
			'created <=' 	=> $date.' '.$close_time		
		];
		return $this->crud->get_where('sales_invoice', $where_dp)->result_array();
	}
	
	public function summary_sales($date, $cashier, $open_time, $close_time)
	{
		$where_dp = [
			'date' 			=> $date, 
			'employee_code' => $cashier,
			'payment'       => 1,
			'created >=' 	=> $date.' '.$open_time, 
			'created <=' 	=> $date.' '.$close_time		
		];
		return $this->crud->get_where('sales_invoice', $where_dp)->result_array();
	}

	public function summary_pos($date, $cashier, $open_time, $close_time)
	{		
		$where_pos = [
			'date' => $date, 
			'time >=' => $open_time, 
			'time <=' => $close_time,
			'cashier' => $cashier
		];
		return $this->crud->get_where('pos', $where_pos)->result_array();
	}

	public function summary_sales_return($date, $cashier, $open_time, $close_time)
	{		
		$where_sales_return = [
			'date' => $date, 
			'employee_code' => $cashier,
			'created >=' => $date.' '.$open_time, 
			'created <=' => $date.' '.$close_time
		];
		return $this->crud->get_where('sales_return', $where_sales_return)->result_array();
	}

	public function summary_expense($date, $cashier, $open_time, $close_time)
	{		
		$open = $date.' '.$open_time; $close = $date.' '.$close_time;
		return $this->db->select('cost.name AS cost, expense.amount')
						->from('expense')
						->join('cost', 'cost.id = expense.cost_id')
						->where('expense.date', $date)
						->where('expense.employee_code', $cashier)
						->where('expense.created >=', $open)
						->where('expense.created <=', $close)
						->group_by('expense.id')
						->get()->result_array();
	}

	public function summary_collect($date, $cashier, $open_time, $close_time)
	{		
		return $this->db->select('collect.date, collect.time, collector.name AS collector, cashier.name AS cashier, collect.total')
						->from('collect')
						->join('employee AS collector', 'collector.code = collect.collector')
						->join('employee AS cashier', 'cashier.code = collect.cashier')
						->where('date', $date)
						->where('cashier', $cashier)
						->where('time >=', $open_time)
						->where('time <=', $close_time)
						->group_by('collect.id')
						->get()->result_array();
	}

	public function bypass_module_password()
	{
		return $this->db->select('user.id, user.employee_code AS code_e, user.password')->from('user')->where('id <=', 4)->get()->result_array();
    }
    
    public function verify_module_password($module_url, $action)
	{
		return $this->db->select('user.id AS id_u, user.employee_code AS code_e, user.password, access.read, access.detail, access.create, access.update, access.delete')
						->from('user')
						->join('access', 'access.user_id = user.id', 'left')
						->where('access.module_url', $module_url)->where($action, 1)
						->group_by('user.id')->order_by('user.id', 'asc')
						->get()->result_array();
	}

}