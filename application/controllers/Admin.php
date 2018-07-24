<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin Controller
 * ================
 * Written by: Ryan Sandoval, May 2018
 *
 * Handles Administrative functionality and routing.
 *
 * @Depreciated
 * 
 */
class Admin extends MY_Controller {

	/**
	 * Construcor Method fo Admin Controller
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		$this->authentication->check_admin();
	}
	/**
	 * Loads the main administration Dashboard
	 */
	public function index() 
	{
			$data['title'] = 'Admin Dashboard';
			
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/tabs');
			$this->load->view('admin/admin-dashboard');
			$this->load->view('templates/footer');
	}

}