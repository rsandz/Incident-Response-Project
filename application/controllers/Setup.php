<?php
/**
 * SETUP
 * =====
 * Written by: Ryan Sandoval, May 2018
 *
 *	This will automatically insert an admin accout into the database without logging it and needing to pass checks.
 *	Use for when the app is first being used.
 *
 *  # Before you access this controller:
 * 		Add "$route['setup'] = 'Setup/index';" to config/routes.php
 *
 * 	# TO EDIT ADMIN CREDENTIALS
 * 		- Edit the fields in the controller properties.
 * 		
 *  # Access this controller by going to:
 * 		`https://yourwebsiteurl.com/Setup`
 * 		
 * 	### IMPORTANT ###
 * 	Once you are done with the setup, DELETE the field in the routing that routes to this setup cotroller.
 * 	Or better yet, REMOVE Setup.php from the controller folder.
 *
 * If you don't do this, it becomes a security issue.
 *
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup extends CI_Controller {

	//EDIT THESE FIELDS//////////////////////////////////

	public $email = 'EMAIL@Foo.com';
	public $password = 'ADMIN';
	public $name = 'ADMIN';

	/////////////////////////////////////////////////////

	/**
	 * Constructor
	 * Loads the necessary Libraries to run the setup.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Logging_model');
	}

	public function index()
	{

		$insert_data = array
			(
			'name'       => $name,
			'email'      => $email,
			'password'   => crypt('$password', 'ADMIN'),
			'privileges' => 'admin',
			);

		$this->Logging_model->log_item('users', $insert_data);
	}

}

/* End of file SETUP.php */
/* Location: ./application/controllers/SETUP.php */