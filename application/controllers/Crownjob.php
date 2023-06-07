<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crownjob extends CI_Controller 
{	
    public function __construct()
  	{
		parent::__construct();
		$this->load->model('Crud_model', 'crud');
	}
}