<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        //Load Models
        $this->CI->load->model('statistics_model');
	}

	

}

/* End of file Statistics.php */
/* Location: ./application/libraries/Statistics.php */
