<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Login Controller
 * ===============
 * @author Ryan Sandoval, May 2018
 * @version 1.1
 *
 * This controller handles the login process and password recovery 
 * 
 */
class Login extends MY_Controller {

	/**
	 * Constructor for User Controller
	 * Loads the necessary resources
	 */
	public function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');
	}

	/**
	 * Controller for login page
	 */
	public function login() {
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
				$redirect_url = $this->authentication->redirected_url ?: site_url('Dashboard');
				redirect($redirect_url);
			} 
		} 

		//Get any errors
		$data['errors'] = $this->authentication->get_errors();
		//Formats the errors. View returns a string
		$data['errors'] = $this->load->view('templates/errors', $data, TRUE);

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
				$data['success_msg'] = 'The Password Recovery Email has been sent. Please see your email for further instructions';
				$data['success_back_url'] = site_url('login');
				$this->load->view('templates/header', $data);
				$this->load->view('templates/success', $data);
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
			$data['success_msg'] = 'Your Password has been changed';
			$data['success_back_url'] = site_url('login');

			$this->authentication->reset_pass($user_id, $this->input->post('password'));
			$this->load->view('templates/header', $data);
			$this->load->view('templates/success', $data);
		}
		else
		{
			//Fill out form, or form had errors
			$data['temp_pass'] = $temp_pass;
			$data['user_id'] = $user_id;
		
			$data['title'] = 'Reset Password';
			$data['errors'] = $this->load->view('templates/errors', $data, TRUE);
		
			$this->load->view('templates/header', $data);
			$this->load->view('user/reset_form', $data);
		}
		
	}
}