<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	/** @var string Notifications in the form of an HTML string*/
	protected $notifications = '';

	public function __construct()
	{
		parent::__construct();

		//Loads Notifications if flashed
		$this->notifications = $this->session->notifications;
		$this->errors = $this->session->errors;
		
	}

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */