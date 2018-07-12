<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Help Controller
 * ===============
 * @author Ryan Sandoval, June 2018
 *
 * This is the controller for the help functionality of the app. 
 * This contains help articles for using the app.
 */
class Help extends CI_Controller {

	/**
	 * Constructor for the help controller
	 *
	 * Loads all the necessary resources
	 * Also ensures that the user is logged in.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');

		$this->authentication->check_login(TRUE);
	}

	/**
	 * The index page for the help cotroller
	 */
	public function index()
	{
		
	}

	/**
	 * Help page explaining markups to the user.
	 */
	public function markups() 
	{
		$data['title'] = 'Help';
		$data['header']['text'] = "Help";
		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('help/markups');
		$this->load->view('templates/footer');
	}

}

/* End of file Help.php */
/* Location: ./application/controllers/Help.php */