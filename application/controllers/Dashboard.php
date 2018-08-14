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
class Dashboard extends MY_Controller {

	/**
	 * Constructor for the Dashboard Controller
	 * Loads Necessary Resources
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Searching/search_model');
		$this->load->model('get_model');
		$this->load->library('table');
		
		//User Private - Must be logged in
		$this->authentication->check_login();
	}

	/**
	 * The main/first page that the user will see. Contains the previous entries.
	 */
	public function index()
	{
		$data['title']='Dashboard';
		$data['name'] = $this->session->name;

		//Loads table for previous entries
		$this->search_model->pagination($this->config->item('per_page'));
		$previous_logs = $this->search_model->search();

		$data['entries_table'] = $this->table->my_generate($previous_logs);
		
		$user_info = $this->get_model->get_user_info();
		$data['user_logs_today'] = $user_info->logs_today;
		$data['user_hours_today'] = $user_info->hours_today;

		$data['content'] = $this->load->view('user/dashboard-top', $data, TRUE);
		$data['content'] .= $this->load->view('logging/user-entries', $data, TRUE); 

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */