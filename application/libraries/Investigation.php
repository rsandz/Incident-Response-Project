<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Investigation Library
 * =====================
 * @author Ryan Sandoval
 * @version 1.0
 * @package Investigation
 * @dependencies TODO
 *
 * This library contains the functionality that allows app
 * to perform automatic investigation and report creation.
 */
class Investigation
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();

        //Load the model
        $this->CI->load->model('investigation/investigation_model');
	}

	public function new_incident()
	{
		
	}

	public function recent_invesitgations()
	{

	}

}

/* End of file Investigation.php */
/* Location: ./application/libraries/Investigation.php */
