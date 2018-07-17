<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 * ====================
 * @author Ryan Sandoval
 * @version 1.0
 * 
 * Controller for the Dashboard pages.
 * The dashboard pages shows the user an overview of basic information
 * about the site and their account
 * For example:
 * 	- Previous entries table
 * 	- User Information
 */
class Dashboard extends CI_Controller {

	/**
	 * Constructor for the Dashboard Controller
	 * Loads Necessary Resources
	 */
	public function __construct()
	{
		parent::__construct();
		
		//User Private - Must be logged in
		$this->authentication->check_login();
	}

	/**
	 * The main/first page that the user will see. Contains the previous entries.
	 */
	public function index()
	{
		$data['title']='Dashboard';
		$data['header'] = array(
			'text' => 'Hello '.$this->session->name.', Welcome to your Dashboard',
			'colour' => 'is-info');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('user/tabs', $data);

		//Loads table for previous entries
		$this->load->model('statistics_model');
		$data['entries_table'] = $this->statistics_model->get_my_entries_table();
		$this->load->view('logging/user-entries', $data); 

		$this->load->view('templates/footer');
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */