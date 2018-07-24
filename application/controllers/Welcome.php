<?php
/**
 * Welcome Controller
 * ==================
 * @author Ryan Sandoval, May 2018
 *
 * This controller handles the content displayed when a visitor (User that is not logged in) visits the site.
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Welcome extends MY_Controller {

	/**
	 * Controller for the landing page
	 */
	public function index()
	{
		$this->load->helper('url');

		if ($this->authentication->check_login(FALSE)) {

			redirect('Dashboard','refresh');

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