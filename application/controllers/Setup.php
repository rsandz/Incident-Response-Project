<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('inflector');
		
		$this->load->model('Logging_model');

		date_default_timezone_set('America/Edmonton');

		if (!$this->session->logged_in)
		{
			show_error('401 - Not Authorized. Please Log in.', 401);
		} 
	}

	public function index()
	{


		$insert_data = array
			(
			'name'       => 'User1',
			'email'      => 'User1@foo.com',
			'password'   => crypt('User1', 'ifft'),
			'privileges' => 'admin',
			);

		$this->Logging_model->log_item('users', $insert_data);
	}

}

/* End of file SETUP.php */
/* Location: ./application/controllers/SETUP.php */