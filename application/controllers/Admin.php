<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin Controller
 * ================
 * Written by: Ryan Sandoval, May 2018
 *
 * Handles Administrative things.
 *
 * @Depreciated
 * 
 */
class Admin extends CI_Controller {

	/**
	 * Construcor Method fo Admin Controller
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('form');
		
		$this->load->model('logging_model');

		if ($this->session->privileges !== 'admin')
		{
			redirect('home','refresh');
			show_error('401 - Not Authorized', 401);
		} 
	}
	/**
	 * Loads the main administration Dashboard
	 */
	public function index() 
	{
			$data['title'] = 'Admin Dashboard';
			$data['header']['text'] = "Admin Dashboard";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/tabs');
			$this->load->view('admin/admin-dashboard');
			$this->load->view('templates/footer');
	}

}