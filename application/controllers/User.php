<?php 
/**
 * User Controller
 * ===============
 * @author Ryan Sandoval, May 2018
 *
 * This controller handles user-specific functionality such as displaying the dashboard and user specific statistics.
 * It also handles the login process.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');
	}

	public function index()
	{
		$this->authentication->check_login(TRUE); //Ensures that the user is logged in.

		$data['title']='Dashboard';
		$data['header'] = array(
			'text' => 'Hello '.$this->session->name.', Welcome to your Dashboard',
			'colour' => 'is-info');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head');
		$this->load->view('templates/navbar');
		$this->load->view('user/tabs', $data);

		//Loads table for previous entries
		$this->load->model('statistics_model');
		$data['entries_table'] = $this->statistics_model->get_my_entries_table();
		$this->load->view('logging/user-entries', $data); 

		$this->load->view('templates/footer');
	}

	/**
	 * Controller for login page
	 */
	public function loginUI() {
		$data['title'] = 'login';

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run()) 
		{
			//Validation good
			$email = $this->input->post('email', TRUE);
			$password = $this->input->post('password', TRUE);
			$result = $this->authentication->login_user($email, $password);

			if ($result) 
			{
				redirect('home');
				
			} 
			else 
			{
				//Login Failed
				$data['errors'] = $this->authentication->get_error();
			}
			
		} 
			//Validation or Login has Failed
			$this->load->view('templates/header', $data);
			$this->load->view('user/login/login', $data);
			$this->load->view('templates/footer', $data);
	}
	/**
	 * Logs user out.
	 */
	public function logout() {

		$this->authentication->logout_user();
		redirect('Welcome','refresh');
	}


	public function recover_password()
	{
		$data = array(
			'title' => 'Password Reset',
			);

		//Validation Rules
		$this->form_validation->set_rules('email','Email','required|valid_email');

		if ($this->form_validation->run()) {
			//Get email from post
			$email = $this->input->post('email');

			//Recover Account
			$result = $this->authentication->recover($email);

			if ($result !== FALSE)
			{
				$this->load->view('templates/header', $data);
				$this->load->view('user/login/sent', $data);
				return;
			}

			//Something went wrong
			$data['errors'] = $this->authentication->get_error();
		} 
			$this->load->view('templates/header', $data);
			$this->load->view('user/login/recover', $data);
	}


	public function recover_form($user_id, $temp_pass)
	{
		//Validate Pass
		if (!$this->authentication->validate_pass($user_id, $temp_pass))
		{
			show_error('Invalid Link', 401);
		}

		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

		if ($this->form_validation->run())
		{
			//Validation is good
			$data['title'] = 'Reset Successful';

			$this->authentication->reset_pass($user_id, $this->input->post('password'));
			$this->load->view('templates/header', $data);
			$this->load->view('user/reset/success', $data);
		}
		else
		{
			//Fill out form, or form had errors
			$data['temp_pass'] = $temp_pass;
			$data['user_id'] = $user_id;
		
			$data['title'] = 'Reset Password';
		
			$this->load->view('templates/header', $data);
			$this->load->view('user/reset/reset_form', $data);
			$this->load->view('templates/errors', $data);
		}
		
	}

	/**
	 * Controller for the page that displays the User's information
	 */
	public function my_info() 
	{
		$this->authentication->check_login(TRUE);
		
		$this->load->helper('form');
		$this->load->model('search_model');

		$data['user_teams'] = $this->search_model->get_user_teams($this->session->user_id, FALSE);
		$data['title'] = 'My Info';
		$data['header']['text'] = "My Info";

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('user/tabs', $data);
		$this->load->view('user/myinfo', $data);

		$this->load->view('templates/footer');
	}
}