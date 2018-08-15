<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
class Admin extends MY_Controller
{

	/**
	 * Construcor Method fo Admin Controller
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
	}
	/**
	 * Loads the main administration Dashboard
	 */
	public function index()
	{
		$data['title'] = 'Admin Dashboard';
		$data['content'] = $this->load->view('admin/admin-dashboard', $data, TRUE);
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer');
	}

	/**
	 * Controller for the site settings page
	 */
	public function site_settings() 
	{
		$data['title'] = 'Site Settings';
		$data['content'] = 'test';
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);
	}

}
