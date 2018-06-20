<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Help Controller
 * ===============
 * @author Ryan Sandoval, June 2018
 */
class Help extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('user');

		check_login(TRUE);
	}

	public function index()
	{
		
	}

	public function markups() 
	{
		$data['title'] = 'Help';
		$data['header']['text'] = "Help";
		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('help/markups');
		$this->load->view('templates/footer');
	}

}

/* End of file Help.php */
/* Location: ./application/controllers/Help.php */