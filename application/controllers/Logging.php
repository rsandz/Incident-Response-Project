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

class Logging extends CI_controller {
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
		$this->load->model('logging_model');
		$this->load->model('search_model');
		$this->load->helper('form');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE

		$this->load->helper('user');
		check_login(TRUE); //Redirect if not logged in.
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

        $data['projects'] = $this->search_model->get_items('projects');
        $data['types'] = $this->search_model->get_items('action_types', array('is_active !=' => 0)); // Only displays active action types
        $data['teams'] = $this->search_model->get_items('teams');

		//Validation Rules
		$this->form_validation->set_rules('date', 'Date', 'required');
		$this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required'); 

		if ($this->form_validation->run() === FALSE) 
		{	
			//Show Sucess page
			$data['header'] = array(
				'text' => 'Logging form',
				'colour' => 'is-success');

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('logging/logging-form', $data);
		}
		else 
		{
			if ($this->session->user_id !== NULL) 
			{
				//Show the logging form
				$data['title'] = 'Success';
				$insert_data = array(
					'action_id'  => $this->input->post('action', TRUE),
					'log_desc'   => $this->input->post('desc', TRUE),
					'log_date'   => $this->input->post('date', TRUE),
					'log_time'   => $this->input->post('time', TRUE),
					'team_id'    => $this->input->post('team', TRUE),
					'project_id' => $this->input->post('project', TRUE),
					'hours'      => $this->input->post('hours', TRUE),
					'user_id'    => $this->session->user_id,
					);

				$this->logging_model->log_action('form', $insert_data);

				$data['header'] = array(
					'text' => 'Log Sucessfuly entered into database!',
					'colour' => 'is-success'
				);
				$this->load->view('templates/header', $data);
				$this->load->view('templates/hero-head', $data);
				$this->load->view('templates/navbar', $data);
				$this->load->view('logging/success');
			}
			else
			{
				// User was not login, so die.
				show_error('User is not logged in', 401);
			}

		}	
	}

}
