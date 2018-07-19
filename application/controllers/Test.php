<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function test()
	{
		$this->load->model('settings/admin_model', 'admin_settings');
		$this->admin_settings->notify_investigated(10, 1);

	}
}

/* End of file Test.php */
/* Location: ./application/controllers/Test.php */

