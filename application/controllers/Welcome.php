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

		if ($logged_in) {

			redirect('Home','refresh');

		} else {
			//Not logged in
			$data['title']='Step Project';
			$this->load->view('templates/header', $data);
			$this->load->view('visitor');
			$this->load->view('templates/footer');
		}	
	}

}
;