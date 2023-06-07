<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_report extends System_Controller 
{	
    public function __construct()
  	{
      parent::__construct();
      $this->load->model('Inventory_report_model', 'inventory_report');
    }

    public function get_warehouse()
    {        
        $warehouse  = $this->crud->get_where('warehouse', ['deleted' => 0])->result_array();
        $option		= "<option value='0'>- SEMUA GUDANG -</option>";		
		foreach($warehouse as $data)
		{
			if($data['default']==1)
			{
				$option .= "<option value='".$data['id']."' selected>".$data['name']."</option>";
			}
			else
			{
				$option .= "<option value='".$data['id']."'>".$data['name']."</option>";
			}
		}		
		$result = array
		(
			'option'=>$option
		);
		echo json_encode($result);
    }

    // INVENTORY VALUE    
    public function total_inventory_value_report()
    {    
        header('Content-Type: application/json');
		$post         = $this->input->post();        
        $search       = $post['search'];
        $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
        $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
        $ppn          = (!isset($post['ppn'])) ?   null : $post['ppn'];
        $warehouse_id = $post['warehouse_id'];        
        $grandtotal = 0; $total_product = 0; $total_qty = 0;
        
        if($search != "" || $department_code !="")
        {
            $product      = $this->inventory_report->get_inventory_value_report($search, $department_code, $subdepartment_code, $ppn)->result_array();            
            foreach($product AS $info)
            {	
                if($warehouse_id != 0)
                {
                    $where = array(
                        'product_code' => $info['code'],
                        'warehouse_id' => $warehouse_id,
                        'deleted'	   => 0
                    );
                }	
                else
                {
                    $where = array(
                        'product_code' => $info['code'],					
                        'deleted'	   => 0
                    );				
                }		
                $data_stock = $this->crud->get_where('stock', $where);
                if($data_stock->num_rows() > 0)
                {
                    $stock = 0;
                    foreach($data_stock->result_array() AS $info_stock)
                    {
                        $stock = $stock + $info_stock['qty'];
                    }
                }
                else
                {
                    $stock = 0;
                }

                $grandtotal = $grandtotal + ($stock*$info['hpp']);
                $total_qty = $total_qty + $stock;            
                $total_product++;
            }

            $data = array(
                'grandtotal'       =>number_format($grandtotal,2,".",","), 
                'total_product'   => $total_product
            );
        }
        else
        {
            $product = $this->crud->get_where('product', ['status' => 1, 'deleted' => 0]);
            foreach($product->result_array() AS $info)
            {	
                if($warehouse_id != 0)
                {
                    $where = array(
                        'product_code' => $info['code'],
                        'warehouse_id' => $warehouse_id,
                        'deleted'	   => 0
                    );
                }	
                else
                {
                    $where = array(
                        'product_code' => $info['code'],					
                        'deleted'	   => 0
                    );				
                }		
                $data_stock = $this->crud->get_where('stock', $where);
                if($data_stock->num_rows() > 0)
                {
                    $stock = 0;
                    foreach($data_stock->result_array() AS $info_stock)
                    {
                        $stock = $stock + $info_stock['qty'];
                    }
                }
                else
                {
                    $stock = 0;
                }

                $grandtotal = $grandtotal + ($stock*$info['hpp']);
                $total_qty = $total_qty + $stock;            
                $total_product++;
            }

            $data = array(
                'grandtotal'      => number_format($grandtotal,2,".",","), 
                'total_product'   => $product->num_rows()
            );
        }
        header('Content-Type: application/json');
        echo json_encode($data);   
    }
    
    public function inventory_value()
    {
        if($this->system->check_access('report/inventory_value', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post         = $this->input->post();        
                $search       = $post['search'];
                $department_code    = (!isset($post['department_code'])) ?   null : $post['department_code'];
                $subdepartment_code = (!isset($post['subdepartment_code'])) ?   null : $post['subdepartment_code'];
                $ppn          = (!isset($post['ppn'])) ?   null : $post['ppn'];
                $warehouse_id = $post['warehouse_id'];
                $draw         = (!isset($post['draw'])) ?        0 : $post['draw'];
                $iLength      = (!isset($post['length'])) ?   null : $post['length'];
                $iStart       = (!isset($post['start'])) ?    null : $post['start'];
                $iOrder   	  = (!isset($post['order'])) ?    null : $post['order'];
                if($search != "" || $department_code !="")
                {
                    $total      = $this->inventory_report->get_inventory_value_report($search, $department_code, $subdepartment_code, $ppn)->num_rows();
                    $product    = $this->inventory_report->get_inventory_value_report($search, $department_code, $subdepartment_code, $ppn, $iLength, $iStart, $iOrder)->result_array();
                    $data 		= array();
                    foreach($product AS $info)
                    {	
                        if($warehouse_id != 0)
                        {
                            $where = array(
                                'product_code' => $info['code'],
                                'warehouse_id' => $warehouse_id,
                                'deleted'	   => 0
                            );
                        }	
                        else
                        {
                            $where = array(
                                'product_code' => $info['code'],					
                                'deleted'	   => 0
                            );				
                        }		
                        $data_stock = $this->crud->get_where('stock', $where);
                        if($data_stock->num_rows() > 0)
                        {
                            $stock = 0;
                            foreach($data_stock->result_array() AS $info_stock)
                            {
                                $stock = $stock + $info_stock['qty'];
                            }
                        }
                        else
                        {
                            $stock = 0;
                        }                    

                        $data[] = array(
                            'id'        => $info['id'],
                            'barcode'   => $info['barcode'],
                            'code' 		=> '<a class="kt-font-primary kt-link text-center" href="'.site_url('product/detail/'.$this->global->encrypt($info['code'])).'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>'.$info['code'].'</b></a>',
                            'name'      => $info['name'],
                            'department'    => $info['name_d'],
                            'subdepartment' => $info['name_sub_d'],
                            'stock'     => $stock,
                            'unit'      => $info['unit'],
                            'hpp'       => $info['hpp'], 
                            'total'     => $stock * $info['hpp']
                        );
                    }

                    $output = array(
                        'draw'            => $draw,
                        'recordsTotal'    => $total,
                        'recordsFiltered' => $total,
                        'data'            => $data
                    );                    
                }
                else
                {
                    $draw   = (!isset($post['draw']))   ? 0 : $post['draw'];
                    $output = array(
                        'draw'            => $draw,
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => []
                    );
                }
                header('Content-Type: application/json');
                echo json_encode($output);
            }
            else
            {
                $header = array("title" => "Nilai Persediaan Produk");
                $footer = array("script" => ['report/inventory/inventory_value_report.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('inventory/inventory_value_report');
                $this->load->view('include/footer', $footer);
            }            
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }  
    
    // MUTATION
    public function mutation()
    {      
        if($this->system->check_access('report/mutation', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post        = $this->input->post();
                $from_date   = $post['from_date'];
                $to_date     = $post['to_date'];
                $from_warehouse_id = $post['from_warehouse_id'];
                $to_warehouse_id = $post['to_warehouse_id'];
                $search_product  = $post['search_product'];
                $this->datatables->select('mutation.id, mutation.code AS search_code, mutation.code AS code, mutation.date AS date, mutation.total_product, checker.name AS checker, operator.name AS operator')
                                 ->from('mutation')
                                 ->join('mutation_detail', 'mutation_detail.mutation_id = mutation.id')
                                 ->join('product', 'product.id = mutation_detail.product_id')
                                 ->join('employee AS checker', 'checker.code = mutation.checker')
                                 ->join('employee AS operator', 'operator.code = mutation.operator')                        
                                 ->where('mutation.deleted', 0);
                if($from_date != "")
                {
                    $this->datatables->where('mutation.date >=', format_date($from_date));
                }
                if($to_date != "")
                {
                    $this->datatables->where('mutation.date <=', format_date($to_date));
                }
                if($from_warehouse_id != "")
                {
                    $this->datatables->where('mutation_detail.from_warehouse_id', $from_warehouse_id);
                }
                if($to_warehouse_id != "")
                {
                    $this->datatables->where('mutation_detail.to_warehouse_id', $to_warehouse_id);
                }
                if($search_product != "")
                {
                    $this->datatables->like('product.name', $search_product);
                }
                $this->datatables->add_column('code',
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('mutation/detail/$1').'" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
                ', 'encrypt_custom(id), code');
                $this->datatables->group_by('mutation.id');
                header('Content-Type: application/json');
                echo $this->datatables->generate(); 
            }
            else
            {
                $header = array("title" => "Mutasi");
                $footer = array("script" => ['report/inventory/mutation_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('inventory/mutation_report');
                $this->load->view('include/footer', $footer);
            }        
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    // PRODUCT USAGE
    public function user_product_usage()
    {
        if($this->input->is_ajax_request())
		{            
            $data = $this->db->select('user.id AS id, user.code AS code, user.name AS name')
                             ->from('employee AS user')
                             ->join('product_usage AS pug', 'pug.employee_code = user.code')
                             ->group_by('user.id')
                             ->get()->result();
			if($data)
			{
				$response   =   [
					'status'    => [
						'code'      => 200,
						'message'   => 'Data Ditemukan',
					],
					'response'  => $data,
				];
			}
			else
			{
				$response   =   [
					'status'    => [
						'code'      => 401,
						'message'   => 'Data Tidak Ditemukan',
					],
					'response'  => '',
				];
			}
			echo json_encode($response);
		}
		else
		{
			$this->load->view('auth/show_404');
		}
    }

    public function total_product_usage()
    {
        if($this->input->is_ajax_request())
        {
            $post        = $this->input->post();
            $from_date   = $post['from_date'];
            $to_date     = $post['to_date'];
            $user_code   = $post['user_code'];            
            $this->db->select('sum(grandtotal) AS grandtotal')
                    ->from('product_usage AS pug')
                    ->join('employee AS user', 'user.code = pug.employee_code');
            if($from_date != "")
            {
                $this->db->where('pug.date >=', format_date($from_date));
            }
            if($to_date != "")
            {
                $this->db->where('pug.date <=', format_date($to_date));
            }
            if($user_code != "")
            {
                $this->db->where('pug.employee_code', $user_code);
            }
            $data = $this->db->where('pug.deleted', 0)->where('pug.do_status', 1)->get()->row_array();
            $response = [
                'grandtotal' => ($data['grandtotal'] != null) ? number_format($data['grandtotal'], 2, '.', ',') : 0
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        else
        {
            $this->load->view('auth/show_404');
        } 
    }

    public function product_usage()
    {      
        if($this->system->check_access('report/product_usage', 'A'))
        {
            if($this->input->is_ajax_request())
            {
                $post        = $this->input->post();
                $from_date   = $post['from_date'];
                $to_date     = $post['to_date'];
                $user_code   = $post['user_code'];
                $this->datatables->select('pug.id, pug.code, pug.date, pug.information, pug.grandtotal, user.name AS name_u,
                                 pug.code AS search_code')
                                 ->from('product_usage AS pug')
                                 ->join('employee AS user', 'user.code = pug.employee_code');
                if($from_date != "")
                {
                    $this->datatables->where('pug.date >=', format_date($from_date));
                }
                if($to_date != "")
                {
                    $this->datatables->where('pug.date <=', format_date($to_date));
                }
                if($user_code != "")
                {
                    $this->datatables->where('pug.employee_code', $user_code);
                }
                $this->datatables->add_column('code',
                '
                    <a class="kt-font-primary kt-link text-center" href="'.site_url('product_usage/detail/$1').'" target="_blank" data-container="body" data-toggle="kt-tooltip" data-placement="left" data-skin="dark" title="Klik untuk detail"><b>$2</b></a>
                ', 'encrypt_custom(id), code');
                $this->datatables->where('pug.deleted', 0)
                                 ->where('pug.do_status', 1)
                                 ->group_by('pug.id');
                header('Content-Type: application/json');
                echo $this->datatables->generate(); 
            }
            else
            {
                $header = array("title" => "Pemakaian");
                $footer = array("script" => ['report/inventory/product_usage_report.js']);
                $this->load->view('include/header', $header);        
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('inventory/product_usage/product_usage_report');
                $this->load->view('include/footer', $footer);
            }        
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }
    }

    public function print_product_usage_detail_report()
    {
        if($this->input->method() === 'post')
		{			                                                
			$data_activity = [
				'information' => 'MENCETAK LAPORAN DETAIL PEMAKAIAN',
				'method'      => 6, // 1:READ, 2:DETAIL, 3:CREATE, 4:UPDATE, 5:DELETE, 6:PRINTOUT
				'code_e'      => $this->session->userdata('code_e'),
				'name_e'      => $this->session->userdata('name_e'),
				'user_id'     => $this->session->userdata('id_u')
			];						
            $this->crud->insert('activity', $data_activity);			

            $post = $this->input->post();
			$from_date      = (!isset($post['from_date']))     ? null : date('Y-m-d', strtotime($post['from_date']));
			$to_date        = (!isset($post['to_date']))       ? null : date('Y-m-d', strtotime($post['to_date']));
            $user_code     = (!isset($post['user_code'])) ? null : $post['user_code'];            

			$filter = [
                'from_date' => $from_date,
                'to_date'   => $to_date,
                'user_code'  => ($user_code == '') ? 'SEMUA USER' : $user_code
            ];

			$data = [
                'title'      => 'Laporan Detail Pemakaian',
                'perusahaan' => $this->global->company(),
                'filter'     => $filter,
				'data'	     => $this->inventory_report->print_product_usage_detail_report($from_date, $to_date, $user_code)
            ];
            
			$mpdf = new \Mpdf\Mpdf([
				'format' => [210, 297],
				'orientation' => 'P',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 25,
				'margin_bottom' => 10,
				'margin_header' => 5,
				'margin_footer' => 5,
				// 'setAutoBottomMargin' => 'stretch'
			]);
			$mpdf->SetHTMLHeader('
				<div style="font-weight: bold; font-size:16px;">
					<u>LAPORAN DETAIL PEMAKAIAN</u>
				</div>
				<table style="width:100%; font-size:14px;">
                    <tbody>
                        <tr>
                            <td>Periode Transaksi: '.$post['from_date'].' s.d '.$post['to_date'].'</td>                            
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid black;">User: '.$filter['user_code'].'</td>
                        </tr>
					</tbody>
				</table>
			');
			$mpdf->SetHTMLFooter('
				<table width="100%">
					<tr>
						<td><small>Waktu Cetak: '.date('d-m-Y H:i:s').' | User: '.$this->session->userdata('name_e').'</small></td>
						<td align="center">{PAGENO}/{nbpg}</td>
					</tr>
				</table>'
			);
			$data = $this->load->view('inventory/product_usage/print_product_usage_detail_report', $data, true);
			$mpdf->WriteHTML($data);
			$mpdf->Output();
		}
		else
		{
	
			$this->load->view('auth/show_404');
		}
    }
}