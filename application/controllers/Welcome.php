<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Welcome extends CI_Controller {

	/**
	 * Controller for the landing page
	 */
	public function index()
	{
		$this->load->helper('url');

		$logged_in = $this->session->logged_in;

		$data['title']='Step Project';
		

		if ($logged_in) {

			$data['name'] = $this->session->name;

			$data['header'] = array(
				'text' => 'Hello '.$data['name'].', Welcome to your Dashboard',
				'colour' => 'is-info');

			$data['privileges'] = $this->session->privileges;

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head');
			$this->load->view('templates/navbar');

			//Loads table for previous entries
			$this->load->model('logging_model');
			$data['entries_table'] = $this->logging_model->get_entries_table(10)['table'];
			$this->load->view('logging/user-entries', $data); 

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