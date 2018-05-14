<?php

class Admin extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('form');
	}
	public function index() {
		if ($this->session->privileges !== 'admin'){
			$this->load->view('templates/header');
			$this->load->view('admin/not-admin');
			$this->load->view('templates/footer');
		} else {
			$this->load->view('templates/header');
			$this->load->view('admin/admin-dashboard');
			$this->load->view('templates/footer');

		}
	}

	public function create() {
		//Creates a new item in database.

	}

}