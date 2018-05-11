<?php
class User extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
		$this->load->library('session');
		$this->load->helper('url');
	}

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
	public function logout() {

		$this->session->sess_destroy();
		redirect('home');
	}

}