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

defined('BASEPATH') or exit('No direct script access allowed');

class Create extends MY_Controller
{
	/**
	 * Constructor class for Create
	 *
	 * Loads SOME of the necessary libraries, helpers and models.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('log_builder', null, 'lb');
		$this->load->helper('form');
		$this->load->model('get_model');
		$this->load->model('Form_get_model');

		$this->authentication->check_login(true);

	}

	/**
	 * Main Controller for the create pages. Contains the logic of the create funcitionality.
	 * 	Calls the necessary functions to load up forms and ensures priveleges are OK before displaying the form.
	 * 
	 * @param  string $type Type of data to create. (i.e. User, Action, Project)
	 */
	public function index($type)
	{
		$data['type'] = $type;
		$data['title'] = 'Create ' . humanize($type);

		if ($data['type'] === 'action') {
			$this->action_form($data);
		} elseif ($data['type'] === 'action_type' && !$this->authentication->check_privileges('user')) {
			$this->action_type_form($data);
		} elseif ($data['type'] === 'project' && !$this->authentication->check_privileges('user')) {
			$this->project_form($data);
		} elseif ($data['type'] === 'user' && !$this->authentication->check_privileges('user')) {
			$this->user_form($data);
		} elseif ($data['type'] === 'team' && !$this->authentication->check_privileges('user')) {
			$this->team_form($data);
		} else {
			show_error('Not Authorized', 401);
		}
	}

	/**
	 * Controller for the action form
	 * @param  array $data Data from index method above
	 */
	public function action_form($data)
	{
		$projects = $this->get_model->get_projects();

		foreach ($projects as $project) {
			$data['projects'][$project->project_id] = $project->project_name;
		}

		//Form Validation Rules
		$this->form_validation->set_rules('action_name', 'Action Name', 'trim|required');
		$this->form_validation->set_rules('action_type', 'Action type', 'trim|required');
		$this->form_validation->set_rules('action_desc', 'Action Description', 'trim');
		$this->form_validation->set_rules('project_id', 'Project ID', 'trim|required');

		if ($this->form_validation->run()) {

			//Enter into Database
			$insert_data = array(
				'action_name' => $this->input->post('action_name', true),
				'type_id' => $this->input->post('action_type', true),
				'action_desc' => $this->input->post('action_desc') == "" ? null : $this->input->post('action_desc', true),
				'project_id' => $this->input->post('project_id', true),
				'is_active' => 1,
				'is_global' => $this->input->post('is_global', true) == 1 ? 1 : 0,
			);

			$this->load->model('Tables/action_model');
			$this->action_model->make($insert_data);

			//Create Log
			$this->lb
				->sys_action('Created Action')
				->date('now')
				->desc("Action `{$insert_data['action_name']}` was inserted into the Action Table.")
				->log();
			
			//Success Notification
			$data['notification'] = "Your Action `{$insert_data['action_name']}` has been created.";
			$data['notifications'] = $this->load->view('templates/notification', $data, TRUE);

			//Reset Validation
			$this->form_validation->reset_validation();
			$_POST = array();
		}

		$data['types'] = $this->get_model->get_action_types();
		
		$data['errors'] = $this->load->view('templates/errors', $data, TRUE);
		$data['content'] = $this->load->view('create/action', $data, TRUE);

		// Make the Form
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar');
		$this->load->view('templates/content-wrapper');
		$this->load->view('templates/footer');
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

		if ($this->form_validation->run() == true) {
			//Enter into Database
			$insert_data = array(
				'project_name' => $this->input->post('project_name', true),
				'project_desc' => $this->input->post('project_desc', true),
				'project_leader' => $this->input->post('project_leader', true),
			);

			$this->load->model('Tables/project_model');
			$this->project_model->make($insert_data);

			//Create the log
			$this->lb
				->sys_action('Created Project')
				->date('now')
				->desc("Project `{$insert_data['project_name']}` was inserted into the Project Table.")
				->log();
			
			//Create Success Data for success page
			$data['notification'] = "Project `{$insert_data['project_name']} has been created`";
			$data['notifications'] = $this->load->view('templates/notification', $data, TRUE);

			//Reset Validation
			$this->form_validation->reset_validation();
			$_POST = array();
		}
		//Get Users for project leaders
		$users = $this->get_model->get_users();
		foreach($users as $user)
		{
			$data['project_leaders'][$user->user_id] = $user->name;
		}

		//Get errors
		$data['errors'] = $this->load->view('templates/errors', $data, TRUE);
		$data['content'] = $this->load->view('create/project', $data, TRUE);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);

	}

	/**
	 * Controller for the User form
	 * @param  array $data Data Array from index method
	 */
	public function user_form($data)
	{
		//Form Valiation
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password-confirm', 'Confirm password', 'required|matches[password]');

		if ($this->form_validation->run()) {

			$insert_data = array(
				'first_name' => $this->input->post('first_name', true),
				'last_name' => $this->input->post('last_name', true),
				'email' => $this->input->post('email', true),
				'password' => crypt($this->input->post('password'), $this->config->item('salt')),
				'privileges' => 'user',
			);

			$full_name = $insert_data['first_name'] . " " . $insert_data['last_name'];

			//Put into Database
			$this->load->model('Tables/user_model');
			$this->user_model->make($insert_data);

			//Create the log
			$this->lb
				->sys_action('Created User')
				->date('now')
				->desc("User `{$full_name}` was inserted into the User table.")
				->log();

			//Success data for the success page
			$data['notification'] = "User `{$full_name}` has been created.";
			$data['notifications'] = $this->load->view('templates/notification', $data, TRUE);

			//Reset Validation
			$this->form_validation->reset_validation();
			$_POST = array();
		}
			//Get errors
			$data['errors'] = $this->load->view('templates/errors', $data, true);
			$data['content'] = $this->load->view('create/user', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar');
			$this->load->view('templates/content-wrapper');
			$this->load->view('templates/footer');
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

		if ($this->form_validation->run() == true) {
			//Enter into Database
			$insert_data = array(
				'team_name' => $this->input->post('team_name', true),
				'team_desc' => $this->input->post('team_desc', true),
				'team_leader' => $this->input->post('team_leader', true),
			);

			$this->load->model('Tables/team_model');
			$this->team_model->make($insert_data);

			//Create the log
			$this->lb
				->sys_action('Created Team')
				->date('now')
				->desc("Team `{$insert_data['team_name']}` was inserted into the Team table.")
				->log();

			//Success Data for the success page
			$data['notification'] = "Team `{$insert_data['team_name']}` has been created.";
			$data['notifications'] = $this->load->view('templates/notification', $data, TRUE);

			//Reset Validation
			$this->form_validation->reset_validation();
			$_POST = array();
		}
			//Get Team Leaders
			$data['team_leaders_select'] = $this->Form_get_model->team_leaders_select($this->authentication->check_admin());

			//Get errors
			$data['errors'] = $this->load->view('templates/errors', $data, TRUE);
			$data['content'] = $this->load->view('create/team', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
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

			//Enter into Database
			$insert_data = array(
				'type_name' => $this->input->post('action_type_name', true),
				'type_desc' => $this->input->post('action_type_desc', true),
				'is_active' => $this->input->post('is_active') ? : 0
			);

			$this->load->model('Tables/action_type_model');
			$this->action_type_model->make($insert_data);

			//Create the log
			$this->lb
				->sys_action('Created Action Type')
				->date('now')
				->desc("Type `{$insert_data['type_name']}` was inserted into the Action Types table.")
				->log();

			$data['notification'] = "Action Type `{$insert_data['type_name']}` has been created.";
			$data['notifications'] = $this->load->view('templates/notification', $data, TRUE);
		
			//Reset Validation
			$this->form_validation->reset_validation();
			$_POST = array();
		}
		//Get errors
		$data['errors'] = $this->load->view('templates/errors', $data, TRUE);
		$data['content'] = $this->load->view('create/action_type', $data, TRUE);

		// Make the Form
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);
	}

}

/* End of file Create.php */
/* Location: ./application/controllers/Create.php */
