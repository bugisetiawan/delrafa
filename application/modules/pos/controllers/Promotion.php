<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promotion extends System_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->form_validation->CI =&$this;			    
		$this->load->model('Promotion_model','promotion');
	}		

	private function format_date($date)
	{
		$explode = explode('-',$date);
		$array = array($explode[2],$explode[1],$explode[0]);
		$implode = implode('-',$array);
		return $implode;
	}

	private function format_amount($amount)
    {
        return str_replace(",","", $amount);
    }
    
    public function datatable_promotion()
    {
        function encrypt_promo($string)
        {
            $output = false; $encrypt_method = "AES-256-CBC"; $secret_key = 'BUGI SETIAWAN'; $secret_iv = 'Setiawan Bugi'; $key = hash('sha256', $secret_key); $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv); $output = base64_encode($output);
            return $output;
        }
        header('Content-Type: application/json');
        $this->datatables->select('id, name, start_date, start_time, end_date, end_time, discount, total_product');
		$this->datatables->from('promotion_product');		
		$this->datatables->where('promotion_product.deleted', 0);
        $this->datatables->add_column('name', 
        '
            <a class="kt-font-primary kt-link text-center" href="'.site_url('pos/promotion/detail/$1').'"><b>$2</b></a>
        ', 'encrypt_promo(id), name');
        echo $this->datatables->generate();
    }

    public function index()
    {
        if($this->system->check_access('pos/promotion', 'read'))
        {
            $header = array("title" => "Datar Promosi POS");
            $footer = array("script" => ['pos/promotion/promotion.js']); 
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('promotion/promotion');
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function add()
    {
        if($this->system->check_access('pos/promotion', 'create'))
        {
            if($this->input->method() === 'post')
            {
                $post = $this->input->post();
                if($post['total_product'] == "" || $post['total_product'] == 0)
                {
                    $header = array("title" => "Pemesanan Pembelian Baru");                
                    $footer = array("script" => ['pos/promotion/crud_promotion.js']);
                    $data   = [
                        'products' => $this->promotion->get_products($post['department_code'], $post['subdepartment_code'])
                    ];
                    $this->load->view('include/header', $header);
                    $this->load->view('include/menubar');
                    $this->load->view('include/topbar');
                    $this->load->view('promotion/add_promotion', $data);
                    $this->load->view('include/footer', $footer);
                }
                else
                {
                    $data_promotion_product = [
                        'name' => $post['name'],
                        'type' => $post['type'],
                        'start_date' => date('Y-m-d', strtotime($post['start_date'])),
                        'start_time' => $post['start_time'],
                        'end_date'   => date('Y-m-d', strtotime($post['end_date'])),
                        'end_time'   => $post['end_time'],
                        'discount'   => $post['discount'],
                        'total_product' => $post['total_product']
                    ];
                    $pp_id = $this->crud->insert_id('promotion_product', $data_promotion_product);
                    if($pp_id != null)
                    {
                        foreach($post['product'] AS $product_code)
                        {
                            $detail_pp = [
                                'promotion_product_id' => $pp_id,
                                'product_id'   => $this->crud->get_product_id($product_code),
                                'product_code' => $product_code
                            ];
                            if($this->crud->insert('promotion_product_detail', $detail_pp))
                            {
                                $this->session->set_flashdata('success', 'Data Promosi berhasil ditambahkan');                            
                            }
                            else
                            {
                                $this->session->set_flashdata('error', 'Data Promosi gagal ditambahkan');
                                break;                            
                            }
                        }                    
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Data Promosi gagal ditambahkan');
                    }
                    redirect(site_url('pos/promotion'));

                }
            }
            else
            {
                $header = array("title" => "Promo Baru");
                $footer = array("script" => ['pos/promotion/crud_promotion.js']);
                $this->load->view('include/header', $header);
                $this->load->view('include/menubar');
                $this->load->view('include/topbar');
                $this->load->view('promotion/add_promotion');
                $this->load->view('include/footer', $footer);
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }

    public function detail($promotion_id)
    {
        if($this->system->check_access('pos/promotion', 'detail'))
        {
            $header = array("title" => "Detail Promo");
            $footer = array("script" => ['pos/promotion/detail_promotion.js']);
            $promotion = $this->crud->get_where('promotion_product', ['id' => $this->global->decrypt($promotion_id)])->row_array();
            $product = $this->db->select('product.code, product.name')
                                ->from('product')
                                ->join('promotion_product_detail AS ppd','ppd.product_code = product.code')
                                ->where('ppd.promotion_product_id', $promotion['id'])
                                ->group_by('product.code')
                                ->get()->result_array();
            $data = array(
                'promotion' => $promotion,
                'products'   => $product
            );
            $this->load->view('include/header', $header);
            $this->load->view('include/menubar');
            $this->load->view('include/topbar');
            $this->load->view('promotion/detail_promotion', $data);
            $this->load->view('include/footer', $footer);
        }
        else
        {
            $this->session->set_flashdata('error', 'Mohon maaf, anda tidak memiliki akses. Terima kasih');
            redirect(site_url('dashboard'));
        }        
    }


}