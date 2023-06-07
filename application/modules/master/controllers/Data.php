<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends System_Controller 
{	
    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI =&$this;
        $this->load->model('Crud_model', 'crud');        
    } 
        
    // Datatable -> ID Code Name
    public function datatable($table)
    {
        $this->datatables->select('id, code, name');
        $this->datatables->from($table);        
        if($this->uri->segment(4) == "position")
        {
            $this->datatables->where('id >', 4);
        }
        $this->datatables->where('deleted', 0);
        $this->datatables->add_column('view', 
        '
            <a href="javascript:void(0);" class="kt-font-warning kt-link update" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Data">
                <i class="fa fa-edit"> Edit</i>
            </a>
            &nbsp;
            <a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Hapus Data">
                <i class="fa fa-times"> Hapus</i>
            </a>            
        ', 'id');
        header('Content-Type: application/json');
        echo $this->datatables->generate();
    }      

    // Datatable -> ID Name
    public function datatable2($table)
    {
        $this->datatables->select('id, name');
        $this->datatables->from($table);
        $this->datatables->where('deleted', 0);  
        $this->datatables->add_column('view', 
        '
            <a href="javascript:void(0);" class="kt-font-warning kt-link update" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Data">
                <i class="fa fa-edit"> Edit</i>
            </a>
            &nbsp;
            <a href="javascript:void(0);" class="kt-font-danger kt-link delete" id="" data-id=$1 data-container="body" data-toggle="kt-tooltip" data-placement="left" title="Edit Data">
                <i class="fa fa-times"> Hapus</i>
            </a>            
        ', 'id');
        header('Content-Type: application/json');
        echo $this->datatables->generate();
    }  

    // Get Data Detail
	function get_detail($table)
	{
        $id=$this->input->get('id');
        $data = $this->crud->get_by_id($table, $id)->row_array();        
        echo json_encode($data);
	}
}
