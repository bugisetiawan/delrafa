<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends System_Controller 
{	
    public function __construct()
  	{
	  parent::__construct();
	  $this->load->model('master/Product_model', 'product');
      $this->load->model('transaction/Sales_model', 'sales');
      $this->load->model('Transaction_model', 'transaction');
    }

    public function datatable()
    {
        header('Content-Type: application/json');
        $this->datatables->select('pos.id, pos.invoice, pos.date, pos.time, employee.name AS name_e, pos.total_product, pos.grandtotal');
        $this->datatables->from('pos');		
        $this->datatables->join('pos_detail', 'pos_detail.pos_id = pos.id');
		$this->datatables->join('employee', 'employee.code = pos.cashier');
		$this->datatables->where('pos.deleted', 0);
        $this->datatables->where('pos_detail.deleted', 0);
        $this->datatables->where('DATE(pos.created)', date('Y-m-d'));
        $this->datatables->group_by('pos.id');	        
        $this->datatables->add_column('invoice', 
        '
            <a class="kt-font-primary kt-link text-center" href="'.site_url('pos/transaction/detail/$1').'"><b>$2</b></a>
        ', 'encrypt_custom(id),invoice');
        echo $this->datatables->generate();
    }

    public function index()
    {
        if($this->system->check_access('pos', 'read'))
        {
            $header = array("title" => "Point of Sale (POS)");
            $footer = array("script" => ['pos/transaction/transaction.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('transaction/transaction');        
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }              

    public function datatable_detail_transaction($pos_id)
    {     
        if($this->input->is_ajax_request())
		{
            header('Content-Type: application/json');
            $this->datatables->select('pos_detail.id AS id, product.code AS code_p, product.name AS name_p, pos_detail.qty, 
            unit.name AS name_u, pos_detail.price AS sellprice, pos_detail.total');
            $this->datatables->from('pos_detail');
            $this->datatables->join('product', 'product.code = pos_detail.product_code');
            $this->datatables->join('unit', 'unit.id = pos_detail.unit_id');
            $this->datatables->where('pos_detail.deleted', 0);
            $this->datatables->where('pos_detail.pos_id', $pos_id);
            $this->datatables->group_by('pos_detail.id');
            echo $this->datatables->generate();
		}
		else
		{
			$this->load->view('auth/show_404');
		}           
    }

    public function detail_transaction($pos_id)
    {
        if($this->system->check_access('pos', 'detail'))
        {
            $header = array("title" => "Detail POS");        
            $data = array(
                'pos' => $this->transaction->detail_transaction(decrypt_custom($pos_id))
            );
            $footer = array("script" => ['pos/transaction/detail_transaction.js']);
            $this->load->view('include/header', $header);        
            $this->load->view('include/menubar');        
            $this->load->view('include/topbar');        
            $this->load->view('transaction/detail_transaction', $data);
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('pos/transaction'));
        }
    }

    public function update_transaction($pos_id)
    {
        if($this->input->method() === 'post')
        {
            $post = $this->input->post();
			$pos 		= $this->transaction->detail_transaction(decrypt_custom($post['pos_id']));
			$pos_detail = $this->transaction->detail_transaction_detail($pos['id']);

			$data_pos=array(				
				'total_product'	=> format_amount($post['total_product']),
				'total_qty'		=> format_amount($post['total_qty']),
				'grandtotal'	=> format_amount($post['grandtotal']),
			);
			$this->crud->update('pos',$data_pos, ['id' => $pos['id']]);
			
			// LIST OLD PRODUCT, AND NEW PRODUCT
			$old_pos_detail_id = []; $new_pos_detail_id = [];
			foreach($pos_detail AS $info_old_product)
			{
				$old_pos_detail_id[] = $info_old_product['id'];
			}
			foreach($post['product'] AS $info_new_product)
			{
				$new_pos_detail_id[] = isset($info_new_product['pos_detail_id']) ? $info_new_product['pos_detail_id'] : null;
			}

			// CHECK AND DELETE OLD PRODUCT WHERE NOT LISTED IN NEW LIST PRODUCT						
			foreach($pos_detail AS $info_old_product)
			{
				if(in_array($info_old_product['id'], $new_pos_detail_id))
				{
					continue;
				}
				else
				{
					// ADD STOCK
					$where_stock = [
						'product_code'	=> $info_old_product['product_code'],
						'warehouse_id'	=> $info_old_product['warehouse_id']
					];
					$stock = $this->crud->get_where('stock', $where_stock)->row_array();
					$update_stock = [
						'qty' => $stock['qty'] + ($info_old_product['qty']*$info_old_product['unit_value'])
					];
					$this->crud->update('stock', $update_stock, $where_stock);

					// UPDATE AND DELETE STOCK CARD
					$where_stock_card = [
						'transaction_id' => $pos['id'],
						'product_code'	 => $info_old_product['product_code'],
						'information'    => 'POS',
						'type'			 => 3,
						'method'		 => 2,
						'warehouse_id'	 => $info_old_product['warehouse_id']
					];								
					$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
					$where_after_stock_card = [
						'id >'          => $stock_card['id'],
						'product_code'	=> $info_old_product['product_code'],
						'warehouse_id'	=> $info_old_product['warehouse_id'],
						'deleted'		=> 0
					];
					$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.id', 'ASC')->get()->result_array();
					foreach($after_stock_cards AS $info_stock_card)
					{
						$update_stock_card = [
							'stock' => $info_stock_card['stock'] + ($info_old_product['qty']*$info_old_product['unit_value'])
						];
						$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
					}
					$this->crud->delete('stock_card', $where_stock_card);																																

					// DELETE SALES INVOICE DETAIL ID
					$where_pos_detail = [
						'id'	=> $info_old_product['id']
					];
					$this->crud->delete('pos_detail', $where_pos_detail);
				}
			}

			$total_hpp = 0;
			foreach($post['product'] AS $info)
			{
				// SKIP THE FIRST PRODUCT, BECAUSE IS TEMPLATE
				if($info['product_code'] == ""  && $info['qty'] == "" && $info['price'] == "" && $info['total'] == "")
				{																	
					continue;
				}
				else
				{			
					$ppn = (!isset($post['ppn'])) ?  0 : $post['ppn'];						
					if(isset($info['pos_detail_id'])) // IF OLD PRODUCT
					{
						$i = array_search($info['pos_detail_id'], array_column($pos_detail, 'id'));
						if($info['qty'] == $pos_detail[$i]['qty'] && $info['unit_id'] == $pos_detail[$i]['unit_id'] && $info['warehouse_id'] == $pos_detail[$i]['warehouse_id'])
						{
							$total_hpp = $total_hpp + ($pos_detail[$i]['qty']*$pos_detail[$i]['unit_value'])*$pos_detail[$i]['hpp'];
							$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);
							// UPDATE SALES INVOICE DETAIL
							$data_pos_detail=array(
								'price'			=> $price,
								'ppn'			=> $ppn,
								'total'			=> $total
							);
							$this->crud->update('pos_detail', $data_pos_detail, ['id' => $info['pos_detail_id']]);
							$res = 1;
						}
						else
						{
							$product_id = $this->crud->get_product_id($info['product_code']);
							$qty = format_amount($info['qty']); $price = format_amount($info['price']); $total = format_amount($info['total']);

							// ADD THE STOCK WITH OLD DATA
							$where_stock = [
								'product_code'	=> $pos_detail[$i]['product_code'],
								'warehouse_id'	=> $pos_detail[$i]['warehouse_id']
							];
							$stock = $this->crud->get_where('stock', $where_stock)->row_array();
							$update_stock = [
								'qty' => $stock['qty'] + ($pos_detail[$i]['qty']*$pos_detail[$i]['unit_value'])
							];
							$this->crud->update('stock', $update_stock, $where_stock);

							// UPDATE STOCK CARD WITH OLD DATA
							$where_stock_card = [
								'transaction_id' => $pos['id'],
								'product_code'	 => $pos_detail[$i]['product_code'],
								'information'    => 'POS',
								'type'			 => 3,
								'method'		 => 2,
								'warehouse_id'	 => $pos_detail[$i]['warehouse_id']
							];								
							$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
							$where_after_stock_card = [
								'id >'          => $stock_card['id'],
								'product_code'	=> $pos_detail[$i]['product_code'],
								'warehouse_id'	=> $pos_detail[$i]['warehouse_id'],
								'deleted'		=> 0
							];
							$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.id', 'ASC')->get()->result_array();
							foreach($after_stock_cards AS $info_stock_card)
							{
								$update_stock_card = [
									'stock' => $info_stock_card['stock'] + ($pos_detail[$i]['qty']*$pos_detail[$i]['unit_value'])
								];
								$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
							}
							$this->crud->delete('stock_card', ['id' => $stock_card['id']]);

							// REDUCE THE STOCK WITH NEW DATA		
							$convert = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();								
							$qty_convert = isset($convert) ? $qty * $convert['value'] : $qty;
							$where_stock = [
								'product_code'	=> $info['product_code'],
								'warehouse_id'	=> $info['warehouse_id']
							];
							$stock = $this->crud->get_where('stock', $where_stock)->row_array();
							if($stock == null)
							{
								$data_stock = array(                                
									'product_id'    => $product_id,
									'product_code'  => $info['product_code'],                                                        
									'qty'           => 0 - $qty,
									'warehouse_id'  => $info['warehouse_id']
								);
								$this->crud->insert('stock', $data_stock);
							}
							else
							{
								$update_stock = [
									'qty' => $stock['qty'] - $qty_convert
								];
								$this->crud->update('stock', $update_stock, $where_stock);
							}										

							// ADD STOCK CARD WITH NEW DATA
							$data_stock_card = array(
								'transaction_id'  => $pos['id'],
								'invoice'         => $pos['invoice'],
								'product_id'      => $product_id,
								'product_code'    => $info['product_code'],
								'qty'             => $qty_convert,
								'information'     => 'POS',
								'type'            => 3, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Adjusment Stock, 9:Mutation
								'method'          => 2, // 1:In, 2:Out
								'stock'           => $stock['qty']-$qty_convert,
								'warehouse_id'    => $info['warehouse_id'],
								'user_id'         => $this->session->userdata('id_u')
							);									
							$this->crud->insert('stock_card',$data_stock_card);

							$total_hpp = $total_hpp + ($pos_detail[$i]['hpp']*$qty*$convert['value']);										

							// UPDATE POS DETAIL
							$data_pos_detail=array(
								'qty'			=> $qty,
								'unit_id'		=> $info['unit_id'],
								'unit_value'    => ($convert['value'] != null) ? $convert['value'] : 1,
								'price'			=> $price,
								'warehouse_id'	=> $info['warehouse_id'],
								'ppn'			=> $ppn,
								'total'			=> $total
							);
							$this->crud->update('pos_detail', $data_pos_detail, ['id' => $info['pos_detail_id']]);
							$res = 1;
						}											
					}
					else // IF NEW PRODUCT
					{
						$product_id = $this->crud->get_product_id($info['product_code']);
						$hpp = $this->product->hpp($info['product_code']);
						$convert     = $this->crud->get_where('product_unit', ['product_code' => $info['product_code'], 'unit_id' => $info['unit_id'], 'deleted' => 0])->row_array();
						$data_pos_detail = array(
							'pos_id'		=> $pos['id'],
							'invoice'		=> $pos['invoice'],
							'product_id'	=> $product_id,
							'product_code'	=> $info['product_code'],
							'qty'			=> format_amount($info['qty']),
							'unit_id'		=> $info['unit_id'],
							'unit_value'	=> ($convert['value'] != null) ? $convert['value'] : 1,
							'warehouse_id'  => $info['warehouse_id'],
							'price'			=> format_amount($info['price']),
							'discount_p'	=> (isset($info['discount_p']))? format_amount($info['discount_p']) : 0,
							'total'			=> format_amount($info['total']),
							'hpp'			=> $hpp,
							'ppn'			=> $ppn
						);
						if($this->crud->insert('pos_detail', $data_pos_detail))
						{
							$check_stock = $this->crud->get_where('stock', ['product_code' => $info['product_code'], 'warehouse_id' => $info['warehouse_id']]); $stock = $check_stock->row_array();							
							$qty 		 = $stock['qty'] - (format_amount($info['qty'])*$convert['value']);
							$total_hpp   = $total_hpp + ($hpp*(format_amount($info['qty'])*$convert['value']));
							if($check_stock->num_rows() == 1)
							{														
								$where_stock = array(
									'product_code'  => $info['product_code'],
									'warehouse_id'  => $info['warehouse_id']
								);       							
								$stock = array(                                
									'product_id'    => $product_id,
									'qty'           => $qty,
								);
								$update_stock = $this->crud->update('stock', $stock, $where_stock);
							}
							else
							{
								$stock = array(                                
									'product_id'    => $product_id,
									'product_code'  => $info['product_code'],                                                        
									'qty'           => $qty,
									'warehouse_id'  => $info['warehouse_id']
								);
								$update_stock = $this->crud->insert('stock', $stock);
							}                            
							if($update_stock)
							{
								$data_stock_card = array(
									'transaction_id'  => $pos['id'],
									'invoice'         => $pos['invoice'],
									'product_id'      => $product_id,
									'product_code'    => $info['product_code'],
									'qty'             => format_amount($info['qty'])*$convert['value'],
									'information'     => 'POS',
									'type'            => 3, // 1:Purchase, 2:Purchase Return, 3:POS, 4:Sales, 5:Sales Return, 6:Production, 7:Repacking, 8:Stock Opname, 9:Mutation
									'method'          => 2, // 1:In, 2:Out
									'stock'           => $qty,
									'warehouse_id'    => $info['warehouse_id'],
									'user_id'         => $this->session->userdata('id_u')
								);
								$stock_card= $this->crud->insert('stock_card',$data_stock_card);
								if($stock_card)
								{
									$res = 1;
									continue;
								}
								else
								{								
									break;
								}
							}
							else
							{							
								break;
							}
						}
						else
						{
							break;
						}															
					}
				}
			}
			
			// UPDATE TOTAL HPP POS
			$this->crud->update('pos', ['total_hpp' => $total_hpp], ['id' => $pos['id']]);

			$data_activity = [
				'information' => 'MEMPERBARUI PENJUALAN',
				'method'      => 4, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);			

			if($res == 1)
			{
				$this->session->set_flashdata('success', 'Transaksi Penjualan berhasil diperbarui');
			}
			else
			{
				$this->session->set_flashdata('error', 'Transaksi Penjualan gagal diperbarui');
			}												
			redirect(site_url('pos/transaction/detail/'.encrypt_custom($pos['id'])));

        }
        else
        {
            if($this->session->userdata('verifypassword') == 1)
            {
                $this->session->unset_userdata('verifypassword');
                $pos = $this->transaction->detail_transaction(decrypt_custom($pos_id));
                $pos_detail = $this->transaction->detail_transaction_detail($pos['id']);
                $header = array("title" => "Perbarui Penjualan (POS)");
                $data = array(
                    'pos'        => $pos,
                    'pos_detail' => $pos_detail
                );
                $footer = array("script" => ['pos/transaction/update_transaction.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');        
                $this->load->view('include/topbar');        
                $this->load->view('transaction/update_transaction', $data);
                $this->load->view('include/footer', $footer);                                            
            }
            else
            {
                $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
                redirect(urldecode($this->agent->referrer()));
            }            
        }                
    }

    public function delete_transaction()
    {
        if($this->input->is_ajax_request())
		{
            $this->session->unset_userdata('verifypassword');
            $post = $this->input->post();
			$pos  = $this->transaction->detail_transaction($post['pos_id']);
			$pos_detail = $this->transaction->detail_transaction_detail($pos['id']);

			foreach($pos_detail AS $info_pos_detail)
			{					
				// ADD STOCK
				$where_stock = [
					'product_code'	=> $info_pos_detail['product_code'],
					'warehouse_id'	=> $info_pos_detail['warehouse_id']
				];
				$stock = $this->crud->get_where('stock', $where_stock)->row_array();
				$update_stock = [
					'qty' => $stock['qty'] + ($info_pos_detail['qty']*$info_pos_detail['unit_value'])
				];
				$this->crud->update('stock', $update_stock, $where_stock);

				// UPDATE AND DELETE STOCK CARD
				$where_stock_card = [
					'transaction_id' => $pos['id'],
					'product_code'	 => $info_pos_detail['product_code'],
					'information'    => 'POS',
					'type'			 => 3,
					'method'		 => 2,
					'warehouse_id'	 => $info_pos_detail['warehouse_id']
				];								
				$stock_card = $this->crud->get_where('stock_card', $where_stock_card)->row_array();
				$where_after_stock_card = [
					'id >'          => $stock_card['id'],
					'product_code'	=> $info_pos_detail['product_code'],
					'warehouse_id'	=> $info_pos_detail['warehouse_id'],
					'deleted'		=> 0
				];
				$after_stock_cards = $this->db->select('id, stock')->from('stock_card')->where($where_after_stock_card)->order_by('stock_card.id', 'ASC')->get()->result_array();
				foreach($after_stock_cards AS $info_stock_card)
				{
					$update_stock_card = [
						'stock' => $info_stock_card['stock'] + ($info_pos_detail['qty']*$info_pos_detail['unit_value'])
					];
					$this->crud->update('stock_card', $update_stock_card, ['id' => $info_stock_card['id']]);
				}
				$this->crud->delete('stock_card', ['id' => $stock_card['id']]);

				// DELETE SALES INVOICE DETAIL ID
				$where_pos_detail = [
					'id'	=> $info_pos_detail['id']
				];
				$this->crud->delete('pos_detail', $where_pos_detail);
			}

			// DELETE SALES INVOICE
            $this->crud->delete('pos', ['id' => $pos['id']]);
            
			$data_activity = [
				'information' => 'MENGHAPUS PENJUALAN (POS)',
				'method'      => 5, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
			$this->crud->insert('activity', $data_activity);

			$response   =   [
				'status'    => [
					'code'      => 200,
					'message'   => 'Berhasil',
				],
				'response'  => ''
			];
            $this->session->set_flashdata('success', 'BERHASIL! Penjualan (POS) Terhapus');
            echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }
}
