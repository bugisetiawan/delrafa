<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Province extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;	
        $this->load->model('Crud_model', 'crud');
    }

    public function get_province()
    {
        $data       = $this->crud->get('province')->result();
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
