<?php 
/**
 * Logging Controller
 * ==================
 * @author Ryan Sandoval, May 2018
 *
 * This controller handles the functionality regarding logging actions.It allows users to log actions using an interface
 * 	It also contains methods used by $.ajax() to request descriptions on certain action_types, actions and projects
 *
 * Note that the page will only work for browsers with js enabled. Due to data relations constraining the user input
 * 	(i.e. A certain action can only be selected with a certain action type), fields need to be updated
 * 	as per user selection. As such, if there is no JS enabled, this form wil not work.
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logging extends MY_controller {
	/**
	 * Constructs the logging class.
	 *
	 * Loads all the necessary libraries, helpers and other resources.
	 * Also sets the default timezone. To configure the time zone, see config/appconfig and 
	 * 	refer to the PHP documentation for timezone values..
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('log_builder', NULL, 'lb');
		$this->load->model('get_model');
		$this->load->helper('form');

		$this->authentication->check_login(TRUE); //Redirect if not logged in.
	}

	/**
	 * Controller for the log form
	 *
	 * Displays the logging form or validates the submitted form.
	 * If Validation is successful, then the data is passed onto the logging model
	 * 	to be inserted into the database conffigured at config/database.
	 *
	 * For information on how form validation is handled, please see the code ignitor documentation
	 * @link 
	 */
	public function log() 
	{
		$this->load->library('form_validation');
		$this->load->helper('url');

		$data['title'] = 'Logging Form';
        $data['projects'] = $this->get_model->get_projects();
        $data['types'] = $this->get_model->get_action_types(); // Only displays active action types
        $data['teams'] = $this->get_model->get_teams();

		//Validation Rules
		$this->form_validation->set_rules('date', 'Date', 'required');
		$this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required'); 

		if ($this->form_validation->run() === FALSE) 
		{	
			//Get Errors and content
			$data['errors'] = $this->load->view('templates/errors', $data, TRUE);
			$data['notifications'] = $this->notifications;
			$data['content'] = $this->load->view('logging/logging-form', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
			
		}
		else 
		{
			$result = $this->lb
				->action($this->input->post('action', TRUE))
				->desc($this->input->post('desc', TRUE))
				->date($this->input->post('date', TRUE))
				->time($this->input->post('time', TRUE))
				->team($this->input->post('team', TRUE))
				->project($this->input->post('project', TRUE))
				->hours($this->input->post('hours', TRUE))
				->user($this->session->user_id)
				->log();

			if (!$result) //Unsuccessful log
			{
				set_notification('Your activity was not successfully logged.');
			}
			else
			{
				set_notification('Your Activity has been inserted into the log table');
			}
			redirect(current_url());
		}
	}
}
