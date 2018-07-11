<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function test()
	{
		$this->load->model('statistics_model');
		$query = array(
			'users' => 1
		);
		echo json_encode($this->statistics_model->get_log_frequency('daily', $query));
	}
}

/* End of file Test.php */
/* Location: ./application/controllers/Test.php */

