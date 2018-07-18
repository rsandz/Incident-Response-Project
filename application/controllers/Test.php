<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function test()
	{
		$this->load->library('Log_Builder', NULL, 'lb');
		// $insert_data = array(
		// 	'action_id' => 1,
		// 	'user_id' => $this->session->user_id,
		// 	'log_date' => '2018-07-17',
		// 	'log_time' => '12:12:00',
		// 	'log_desc' => 'testing new daslib'
		// );
		// $this->logging->quick_log($insert_data);
		$this->lb
			->sys_action('Test sys')
			->user(10)
			->date('now')
			->hours(10)
			->desc('SYs Test2')
			->log();
		
	}
}

/* End of file Test.php */
/* Location: ./application/controllers/Test.php */

