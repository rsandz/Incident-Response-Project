<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->helper('url');
		$this->load->library('session');

		$logged_in = $this->session->logged_in;

		$data['title']='Step Project';
		

		if ($logged_in) {

			$data['name'] = $this->session->name;
			$data['privileges'] = $this->session->privileges;

			$this->load->view('templates/header', $data);
			$this->load->view('dashboard');

			//Loads table for previous entries
			$this->load->model('logging_model');
			$data['entries_table'] = $this->logging_model->get_entries_table();
			$this->load->view('logging/entries', $data); 

			$this->load->view('templates/footer');

		} else {
			//Not logged in
			$this->load->view('templates/header', $data);
			$this->load->view('visitor');
			$this->load->view('templates/footer');
		}
	}

}
;