<?php 
/**
 * User Controller
 * ===============
 * Written by: Ryan Sandoval, May 2018
 *
 * This controller handles user-specific functionality such as displaying the dashboard and user specific statistics.
 * It also handles the login process.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

	public function recover_password()
	{
		$data = array(
			'title' => 'Password Reset',
			);

		//Load Form Validatin Libraries ann models
		$this->load->library('form_validation');
		$this->load->helper('form');

		$this->form_validation->set_rules('email','Email','required|valid_email|callback_in_database');

		if ($this->form_validation->run() == TRUE) {
			//Get email from post
			$email = $this->input->post('email');

			$user_data = $this->user_model->recovery_data($email);

			//load Email Library
			$this->load->library('email');
			
			$this->email->from($this->config->item('recovery_email'), $this->config->item('recovery_email_name'));
			$this->email->to($email);
			
			$this->email->subject('Password Recovery Requesy');
			$this->email->message(
				'Hello '.$user_data['name'].', \n'
				.'You have requested to change your password. If this was not you, please ignore this email \n'
				.'Please click on the following link to reset your password: \n'
				.site_url($email.'/'.$user_data['email_code'])
				.'\n\nKind Regards,'
				.'\n'.$this->config->item('recovery_email_name')
			);
			$this->email->send(FALSE);
			echo $this->email->print_debugger();
			

		} else {
			$data['show_form_errors'] = TRUE;

			$this->load->view('templates/header', $data);
			$this->load->view('login/recover', $data);
			$this->load->view('templates/error', $data);
		}

		
	}

	public function in_database($email) {
		$in_database = $this->user_model->email_in_database($email);

		if ($in_database)
		{
			return True;
		}
		else
		{
			$this->form_validation->set_message('in_database', 'Email could not be found in database');
			return FALSE;
		}
	}


}