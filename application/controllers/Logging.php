<?php 
/**
 * Logging Controller
 * ==================
 * Written by: Ryan Sandoval, May 2018
 *
 * This controller handles the functionality regarding logging actions.It allows users to log actions using an interface
 * 	It also contains methods used by $.ajax() to request descriptions on certain action_types, actions and projects
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logging extends CI_controller {
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('logging_model');
		$this->load->model('search_model');
		$this->load->helper('form');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE

		$this->load->helper('user');
		check_login(TRUE);
	}

	/**
	 * Controller for the log form
	 */
	public function log() 
	{

		$data['title'] = 'Logging Form';

        $data['projects'] = $this->search_model->get_items('projects');
        $data['types'] = $this->search_model->get_items('action_types', array('is_active !=' => 0));
        $data['teams'] = $this->search_model->get_items('teams');

		$this->load->library('form_validation');
		$this->load->helper('url');

		$this->form_validation->set_rules('date', 'Date', 'required');
		$this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required'); 

		if ($this->form_validation->run() === FALSE) 
		{	
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

			$this->load->view('templates/header');
			$this->load->view('logging/success');
			}
			else
			{
				show_error('User is not logged in', 401);
			}

		}	
	}

}
