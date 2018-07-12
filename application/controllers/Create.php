<?php
/**
 * Create Controller
 * =================
 * Written by: Ryan Sandoval, May 2018
 *
 * This is the controller for the 'create' functionality in the app. I allows users to create actions, teams, projects, etc.
 * 	in the site using an iterface.
 * Much of the data validation is handled by Code Igniter's form validation
 * 	@see https://www.codeigniter.com/userguide3/libraries/form_validation.html
 *
 * Certain actions are locked to specific user prvilleges
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Create extends CI_Controller {
	/**
	 * Constructor class for Create
	 *
	 * Loads SOME of the necessary libraries, helpers and models.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->model('Logging_model');
		$this->load->model('search_model');
		$this->load->model('Form_get_model');

		date_default_timezone_set($this->config->item('timezone'));

		$this->authentication->check_login(TRUE);

	}

	/**
	 * Main Controller for the create pages. Contains the logic of the create funcitionality.
	 * 	Calls the necessary functions to load up forms and ensures priveleges are OK before displaying the form.
	 * 
	 * @param  string $type Type of data to create. (i.e. User, Action, Project)
	 */
	public function index($type) 
	{
		$data['type']       = $type;
		$data['title']      = 'Create '.$type;
		$data['header'] = array(
			'text'   => 'Create',
			'colour' => 'is-primary'
		);

		if ($data['type'] === 'action') 
		{
			$this->action_form($data);
		}
		elseif($data['type'] === 'action_type' && !check_privileges('user'))
		{
			$this->action_type_form($data);
		}
		elseif ($data['type'] === 'project' && !check_privileges('user')) 
		{
			$this->project_form($data);
		}
		elseif ($data['type'] === 'user' && !check_privileges('user')) 
		{
			$this->user_form($data);
		}
		elseif ($data['type'] === 'team' && !check_privileges('user'))
		{
			$this->team_form($data);
		}
		else
		{
			show_error('Not Authorized', 401);
		}
	}

	/**
	 * Controller for the action form
	 * @param  array $data Data from index method above
	 */
	public function action_form($data) 
	{
		$projects = $this->search_model->get_items('projects');

		foreach ($projects as $project) {
			$data['projects'][$project->project_id] = $project->project_name;
		}

		//Form Validation Rules
		$this->form_validation->set_rules('action_name', 'Action Name', 'trim|required');
		$this->form_validation->set_rules('action_type', 'Action type', 'trim|required');
		$this->form_validation->set_rules('action_desc', 'Action Description', 'trim');
		$this->form_validation->set_rules('project_id', 'Project ID', 'trim|required');

		if ($this->form_validation->run()) {
			//Logging action
			$this->Logging_model->log_action('create', 'action', $this->input->post('action_name', TRUE));			

			//Enter into Database
			$insert_data = array
				(
				'action_name' => $this->input->post('action_name', TRUE),
				'type_id'     => $this->input->post('action_type', TRUE),
				'action_desc' => $this->input->post('action_desc') == "" ? NULL : $this->input->post('action_desc', TRUE),
				'project_id'  => $this->input->post('project_id', TRUE),
				'is_active'   => 1,
				'is_global'   => $this->input->post('is_global', TRUE) == 1 ? 1 : 0,
				);

			$this->Logging_model->log_item('actions', $insert_data);
			//Success

			$data['title'] = 'Created '.$data['type'];
			$data['header'] = array(
				'text'   => 'Success',
				'colour' => 'is-success'
			);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/success', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		} else {
			
			$data['types'] = $this->search_model->get_items('action_types', array('is_active !=' => '0'));

			// Make the Form
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/action', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		}
	}

	/**
	 * Controller for the project form
	 * @param  array $data Data from the index method above
	 */
	public function project_form($data) 
	{
		//Form Validation Rules
		$this->form_validation->set_rules('project_name', 'Project Name', 'trim|required');
		$this->form_validation->set_rules('project_leader', 'Project Leader', 'trim');
		$this->form_validation->set_rules('project_desc', 'Project Description', 'trim');

		if ($this->form_validation->run() == TRUE) 
		{
			//Logging action
			$this->Logging_model->log_action('create', 'project', $this->input->post('project_name', TRUE));
			//Enter into Database
			$insert_data = array
				(
				'project_name'   => $this->input->post('project_name', TRUE),
				'project_desc'   => $this->input->post('project_desc', TRUE),
				'project_leader' => $this->input->post('project_leader', TRUE),
				);

			$this->Logging_model->log_item('projects', $insert_data);
			//Success

			$data['title'] = 'Created '.$data['type'];
			$data['header'] = array(
				'text'   => 'Success',
				'colour' => 'is-success'
			);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/success', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		} 
		else 
		{
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/project', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		}
			
	}

	/**
	 * Controller for the User form
	 * @param  array $data Data Array from index method
	 */
	public function user_form($data)
	{

		$insert_data = array
			(
			'name'       => $this->input->post('name', TRUE),
			'email'      => $this->input->post('email', TRUE),
			'password'   => crypt($this->input->post('password'), $this->config->item('salt')),
			'privileges' => 'user',
			);

		//Form Valiation
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password-confirm', 'Confirm password', 'required|matches[password]');

		if ($this->form_validation->run()) {
			//Logging action
			$this->Logging_model->log_action('create', 'user', $this->input->post('name', TRUE));

			$this->Logging_model->log_item('users', $insert_data);
			//Success

			$data['title'] = 'Created '.$data['type'];
			$data['header'] = array(
				'text'   => 'Success',
				'colour' => 'is-success'
			);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/success', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		} else {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/user', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		}

	}

	/**
	 * Controller for the team form
	 * @param  array $data Data Array from index method
	 */
	public function team_form($data) 
	{

		//Form Validation Rules
		$this->form_validation->set_rules('team_name', 'Team Name', 'trim|required');
		$this->form_validation->set_rules('team_leader', 'Team Leader', 'trim');
		$this->form_validation->set_rules('team_desc', 'Team Description', 'trim');

		if ($this->form_validation->run() == TRUE) 
		{
			//Logging action
			$this->Logging_model->log_action('create', 'team', $this->input->post('team_name', TRUE));
			//Enter into Database
			$insert_data = array
				(
				'team_name'   => $this->input->post('team_name', TRUE),
				'team_desc'   => $this->input->post('team_desc', TRUE),
				'team_leader' => $this->input->post('team_leader', TRUE),
				);

			$this->Logging_model->log_item('teams', $insert_data);
			//Success

			$data['title'] = 'Created '.$data['type'];
			$data['header'] = array(
				'text'   => 'Success',
				'colour' => 'is-success'
			);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/success', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		} 
		else 
		{
			//Get Team Leaders
			if(check_admin())
			{
				$data['team_leaders_select'] = $this->Form_get_model->team_leaders_select();
			}
			else
			{
				$data['team_leaders_select'] = $this->Form_get_model->team_leaders_select();
			}

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/team', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		}
	}

	/**
	 * Controller for the action type form
	 * @param  array $data Data from index method above
	 */
	public function action_type_form($data) 
	{
		//Form Validation Rules
		$this->form_validation->set_rules('action_type_name', 'Action Type Name', 'trim|required');
		$this->form_validation->set_rules('action_type_desc', 'Action Description', 'trim');

		if ($this->form_validation->run()) {
			//Logging action
			$this->Logging_model->log_action('create', 'action type', $this->input->post('action_type_name', TRUE));			

			//Enter into Database
			$insert_data = array
				(
					'type_name' => $this->input->post('action_type_name', TRUE),
					'type_desc' => $this->input->post('action_type_desc', TRUE),
					'is_active' => $this->input->post('is_active') ?: 0
				);

			$this->Logging_model->log_item('action_types', $insert_data);
			//Success

			$data['title'] = 'Created '.$data['type'];
			$data['header'] = array(
				'text'   => 'Success',
				'colour' => 'is-success'
			);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/success', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		} else {
			$data['types'] = $this->search_model->get_items('action_types', array('is_active !=' => '0'));

			// Make the Form
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar');
			$this->load->view('create/tabs');
			$this->load->view('create/action_type', $data);
			$this->load->view('create/errors', $data);
			$this->load->view('templates/footer');
		}
	}

}

/* End of file Create.php */
/* Location: ./application/controllers/Create.php */