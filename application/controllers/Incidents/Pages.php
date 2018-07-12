<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This is the controller for the pages in the incidents functinalilty.
 * It will be able to handle:
 * 	- New Incident creation
 * 	- Historical Incidents
 * 	- Running investigations
 */
class Pages extends CI_Controller {

	/**
	 * Constructor for this controller.
	 * Loads the Neccessary resources/libraries
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('statistics_model');
		$this->load->model('search_model');
		$this->load->helper(array(
			'form', 'user_helper'
		));

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
		
		check_login();
	}

	public function index()
	{
		$data = array(
			'title' => 'Incidents Overview',
			'header' => array(
				'text' => 'Incidents Overiew'
			),
		);
		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('templates/footer');
	}

}

/* End of file Pages.php */
/* Location: ./application/controllers/Incidents/Pages.php */