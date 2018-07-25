<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
	}

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */