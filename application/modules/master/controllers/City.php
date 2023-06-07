<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;	
        $this->load->model('Crud_model', 'crud');
    }

    public function get_city()
    {
        $province_id    = $this->input->get('province_id');
        $data       = $this->db->get_where('city', ['province_id' => $province_id])->result();
        if($data)
        {
            $response   = [
                'status'    => [
                    'code'      => 200,
                    'message'   => 'Data Ditemukan',
                ],
                'response'  => $data
            ];
            echo json_encode($response);
        }
        else
        {
            $response   = [
                'status'    => [
                    'code'      => 404,
                    'message'   => 'Data Tidak Ditemukan',
                ],
                'response'  => ''
            ];
            echo json_encode($response);
        }
    }

    public function get_all_city()
    {        
        $data       = $this->db->get('city')->result();
        if($data)
        {
            $response   = [
                'status'    => [
                    'code'      => 200,
                    'message'   => 'Data Ditemukan',
                ],
                'response'  => $data
            ];
            echo json_encode($response);
        }
        else
        {
            $response   = [
                'status'    => [
                    'code'      => 404,
                    'message'   => 'Data Tidak Ditemukan',
                ],
                'response'  => ''
            ];
            echo json_encode($response);
        }
    }
}
