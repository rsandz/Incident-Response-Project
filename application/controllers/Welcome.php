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

			$this->load->view('templates/header', $data);
			$this->load->view('dashboard');
		} else {
			//Not logged in
			$this->load->view('templates/header', $data);
			$this->load->view('visitor');
		}
	}
}
