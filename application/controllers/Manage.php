<?php

use function GuzzleHttp\json_encode;
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Manage Controller
 * =================
 * @author Ryan Sandoval
 *
 * This controls the functionality related to End-User Data management.
 * For Admin Data Management, see the modfy controller.
 */
class Manage extends MY_Controller {

	/**
	 * Constructor method for Manage Controller
	 *
	 * Loads all the necessary libraries, models, etc.
	 * Also handles if user is logged in or not.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->load->library('table');
		$this->load->library('log_builder', NULL, 'lb');
		$this->load->helper('form');
		$this->load->helper('sort');
		
		$this->load->model('get_model');
		$this->load->model('Searching/search_model');
		$this->load->model('modify_model');

		$this->authentication->check_login();
	}

	/**
	 * Multifunctional Team Management Method
	 * --------------------------------------
	 * 
	 * Displays a team selection page so that the user can manage the teams if no $team parameter is
	 * not present in the URI
	 *
	 * If the $team parameter is defined in the URI, then it takes the user to the management screen
	 * 
	 * @param  string $team The team id to manage
	 */
	public function manage_teams($team_id = NULL)
	{
		if (!isset($team_id)) //Then we are selecting the team first
		{
			$data['title'] = 'Manage Teams';

			//Get data for team selection
			$data['teams'] = $this->get_model->get_user_teams(
				$this->session->user_id, 
				$this->authentication->check_admin()
			);
			if(!empty($data['teams']))
			{
				$data['team_modify_links'] = array_map(function($x)
					{
						return anchor("Manage/teams/{$x->team_id}", "Manage", 'class="button is-info"');
					},
					$data['teams']);
			}
			$data['content'] = $this->load->view('manage/manage_teams/teams-selection', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
		}
		else
		{
			//Display Team management
			$data = $this->get_model->get_team_info($team_id);

			$data['title'] = 'Manage Teams';
			$data['content'] = $this->load->view('manage/manage_teams/modify-team', $data, TRUE);
			$data['notifications'] = $this->session->notifications;

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
		}
	}

	public function add_users($team_id)
	{
		$data['title'] = 'Manage Teams';
		$data['team_id'] = $team_id;

		//Set form Validation
		$this->form_validation->set_rules('users[]', 'Users', 'required');

		if ($this->form_validation->run())
		{
			$user_id_list = $this->input->post('users[]', TRUE);
			//Add user to database
			foreach ($user_id_list as $user_id)
			{
				$query = $this->modify_model->add_to_team($team_id, $user_id);
				if (!$query) //Query failed if query was false
				{
					log_message('error', "Failed to add User Id: $user_id to Team: Id: $team_id in manage teams");
					show_error('Failed to add a user to a team.');
				}

				//Put into list of user names - Used for logging
				$user_name_list[]  = $this->get_model->get_user_name($user_id);
			}

			//Get Team name
			$team_name = $this->get_model->get_team_name($team_id);
			//Log the action
			$this->lb
				->sys_action('Added Users to Team')
				->date('now')
				->desc('Added `'.implode(', ', $user_name_list)."` to Team `{$team_name}`.")
				->log();
			
			$data['notification'] = 'Sucessfully added user(s) to team';
			$notifications = $this->load->view('templates/notification', $data, TRUE);
			$this->session->set_flashdata('notifications', $notifications);
			redirect(site_url("Manage/teams/{$team_id}"),'refresh');
		}
		else
		{
			//User needs to fill out form or no user was selected

			//Get users not in team
			$data['users'] = $this->get_model->get_users_not_in_team($team_id);
			$data['team_id'] = $team_id;

			$data['title'] = 'Manage Teams';
			$data['notifications'] = $this->session->notifications;
			$data['content'] = $this->load->view('manage/manage_teams/add-user', $data, TRUE);
			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
		}
		
	}

	public function remove_users($team_id)
	{
		if (empty($this->input->post('users[]', TRUE)))
		{
			$data['notification'] = 'No change in Database.';
			$notifications = $this->load->view('templates/notification', $data, TRUE);
			$this->session->set_flashdata('notifications', $notifications);
			redirect('Manage/teams/'.$team_id,'refresh');
		}
		foreach ($this->input->post('users[]', TRUE) as $user_id)
		{
			$this->modify_model->remove_from_team($team_id, $user_id);
			//Put into list of user names - Used for logging
			$user_name_list[]  = $this->get_model->get_user_name($user_id);
		}

		//Get Team name
		$team_name = $this->get_model->get_team_name($team_id);
		//Create the log
		$this->lb
			->sys_action('Removed Users from Team')
			->date('now')
			->desc('Removed Users `'.implode(', ', $user_name_list)."` from Team `{$team_name}`.")
			->log();

		$data['title'] = 'Manage Teams';
		
		$data['notification'] = 'Successfully removed selected Users.';
		$notifications = $this->load->view('templates/notification', $data, TRUE);
		$this->session->set_flashdata('notifications', $notifications);
		
		redirect('Manage/teams/'.$team_id,'refresh');
	}

	//-------------------------------------------------

	/*
		Management for Projects
	*/

	public function manage_projects($project_id = NULL)
	{
		$this->load->model('Tables/project_model');
		$this->load->model('Stats/project_stats');
		$this->load->model('Tables/user_model');

		$data['title'] = 'Manage Projects';

		if (isset($project_id))
		{
			//Manage the project
			$data['project'] = $this->project_model->get_by_id($project_id);
			$data['project_leader'] = $this->user_model->get_by_id($data['project']->project_leader_id);
			
			//Generate Active teams Table
			$active_teams = $this->project_stats->get_active_teams($project_id);
			$table_data = array();
			foreach ($active_teams->result() as $team)
			{
				$team_data['Team Name'] = $team->team_name;
				$team_data['Number of Logs'] = $team->num_logs;
				$team_data['link'] = anchor(site_url('Manage/teams/'.$team->team_id), 'View', 'class="button is-info"');
				$table_data[] = $team_data;
			}
			$heading = array('Team Name', 'Number of Logs', '');
			$data['active_teams_table'] = $this->table->my_generate($table_data, $heading);

			$data['content'] = $this->load->view('manage/projects/view', $data, TRUE);
		}
		else
		{
			//Select Project to manage
			$selection['title'] = 'Select a Project to Manage';
			$selection['empty_data_msg'] = $this->load->view('selection/empty_box_projects', NULL, TRUE);

			//Sorting
			$selection['sort_options'] = array(
				'log_num' => 'Log Number',
				'project_name' => 'Project Name',
				'project_leader' => 'Project Leader'
			);
			$selection['current_sort'] = get_sort('project', 'log_num');
			$selection['sort_identifier'] = 'project';

			$projects = $this->project_model
				->sort(get_sort('project'))
				->get(NULL, NULL, $this->session->user_id);

			//Make selection Items
			foreach ($projects as $project)
			{
				$selection_item['title'] = $project->project_name;
				$selection_item['body'] = "
				<ul>
					<li>Project Leader: {$project->project_leader_name}</li>
					<li>Projec Description: {$project->project_desc}</li>
					<li>Total Logs: {$this->project_stats->total_logs($project->project_id)} </li>
				</ul>
				";
				$selection_item['link'] = site_url('Manage/projects/'.$project->project_id);
				$selection_item['link_name'] = 'Manage';
				$selection['selection_items'][] = $selection_item;
			}
			
			$data['content'] = $this->load->view('selection/selection_main', $selection, TRUE);

		}

		$data['notifications'] = $this->notifications;
		$data['errors'] = $this->errors;

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);

	}

	// ----------------------------------------------------------------

	/*
		Management for Users
	*/

	public function manage_users($user_id = NULL)
	{
		$this->load->model('Tables/user_model');
		$this->load->model('Stats/user_stats');

		$data['title'] = 'Manage Users';

		if (isset($user_id))
		{
			//View the user
			$data['user'] = $this->user_model->get_by_id($user_id);

			//Make Stats
			$data['stats'] = $this->user_stats->get_all_stats($user_id);

			$last_log = $this->search_model->search_for_logs($data['stats']['last_log']->log_id);
			$data['stats']['last_log'] = $this->table->my_generate($last_log);
			$data['stats']['action_ranking'] = $this->table->my_generate($data['stats']['action_ranking']);
			$data['content'] = $this->load->view('Manage/users/view', $data, TRUE);
		}
		else
		{
			//Select the user
			$selection['title'] = 'Select a User to Manage';
			$selection['empty_data_msg'] = $this->load->view('selection/empty_box_users', NULL, TRUE);

			//Sorting
			$selection['sort_options'] = array(
				'log_num' => 'Log Amount',
				'name' => 'User Name',
			);
			$selection['sort_identifier'] = 'user';
			$selection['current_sort'] = get_sort('user', 'name');

			$users = $this->user_model
				->sort(get_sort('user'))
				->get();
			//Make selection Items
			foreach ($users as $user)
			{
				$selection_item['title'] = $user->name;
				$selection_item['body'] = "
					Amount of Logs: {$this->user_stats->total_logs($user->user_id)}
				";
				$selection_item['link'] = site_url('Manage/users/'.$user->user_id);
				$selection_item['link_name'] = 'Manage';
				$selection['selection_items'][] = $selection_item;
			}
			
			$data['content'] = $this->load->view('selection/selection_main', $selection, TRUE); 
		}

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);
	} 
}

/* End of file Manage.php */
/* Location: ./application/controllers/Manage.php */