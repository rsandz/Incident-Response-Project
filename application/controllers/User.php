<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
		$this->load->library('session');
		$this->load->helper('url');
	}

	public function index()
	{
		$this->check_login(); //Ensures that the user is logged in.

		$data['title']='Dashboard';
		$data['name'] = $this->session->name;

		$data['header'] = array(
			'text' => 'Hello '.$data['name'].', Welcome to your Dashboard',
			'colour' => 'is-info');

		$data['privileges'] = $this->session->privileges;

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head');
		$this->load->view('templates/navbar');
		$this->load->view('user/tabs', $data);

		//Loads table for previous entries
		$this->load->model('logging_model');
		$data['entries_table'] = $this->logging_model->get_entries_table(10)['table'];
		$this->load->view('logging/user-entries', $data); 

		$this->load->view('templates/footer');
	}

	/**
	 * Controller for login page
	 */
	public function loginUI() {
		$data['title'] = 'login';

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() === FALSE) {
			$this->load->view('templates/header', $data);
			$this->load->view('login/login');
			$this->load->view('templates/footer');
		} else {
			$result = $this->user_model->login_user();

			if ($result !== 'Loged_in') {
				$data['errors'] = $result;

				$this->load->view('templates/header', $data);
				$this->load->view('login/login', $data);
				$this->load->view('templates/footer');
			} else {
				redirect('home');
			}
		}
	}
	/**
	 * Logs user out.
	 */
	public function logout() {

		$this->session->sess_destroy();
		redirect('welcome');
	}

	public function check_login() 
	{
		if (!$this->session->logged_in)
		{
			redirect('login','refresh');
		} 
	}

}