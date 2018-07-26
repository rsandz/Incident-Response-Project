<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * THE ULTIMATE GET MODEL
 * ======================
 *
 * This model contains all the get methods that... don't have a home.
 *
 * This model is used for utility get methods that can be used in many 
 * controllers.
 * 
 */
class Get_model extends MY_Model {

	/**
	 * Gets the projects available to the user
	 * @param boolean $admin_mode If TRUE, will also get inactive projects
	 * @return array             Code Igniter db results that contains the projects
	 */
	public function get_projects($admin_mode = FALSE)
	{
		if (!$admin_mode)
		{
			//Only get active projects
			$this->db->where('is_active', 1);
		}

		//Get the projects
		return $this->db->join('users', 'users.user_id = projects.project_leader', 'left')
			->select(
				'project_name, project_id, 
				CONCAT(users.first_name, " ", users.last_name) as project_leader_name, project_desc')
			->get('projects')->result();
	}

	/**
	 * Gets all the action types that are active. If admin_mode is TRUE, 
	 * will also get inactive action types
	 * @param  boolean $admin_mode Gets inactive action types if TRUE
	 * @return array              Array of result objects.
	 */
	public function get_action_types($admin_mode = FALSE)
	{
		if (!$admin_mode)
		{
			$this->db->where('is_active', 1);
		}

		return $this->db->get('action_types')->result();
	}

	/**
	 * Get all users in the table. If provided with an ID, will get that 
	 * specific user
	 * @param mixed $user_id Either a single user ID, or array of user IDs
	 * @return object db result object for that user(s)
	 */
	public function get_users($user_id = NULL)
	{
		if (isset($user_id))
		{
			if (is_array($user_id))
			{
				//Use where_in
				$this->db->where_in('user_id', $user_id);
			}
			else
			{
				$this->db->where('user_id', $user_id);
			}
		}

		return $this->db
			->select('*, CONCAT(first_name, " ", last_name) as name')
			->get('users')
			->result();
	}

	/**
	 * Returns the teams that the user is in. If admin mode is TRUE,
	 * will return all the teams/
	 * @param  boolean $admin_mode Whether to reutrn only user teams or all the teams.
	 * @return array             Array of db result objects. Or Null if something went wrong.
	 */
	public function get_teams($admin_mode = FALSE)
	{
		return $this->get_user_teams($this->session->user_id, $admin_mode);
	}

	/**
	 * Get the teams that the provided user id is in
	 * @param boolean $admin_mode If true, will ignore the user_id and get all the teams
	 * @param  string $user_id User Id of the user in question. 
	 * @return array           Code Igniter db results array that contains the teams
	 *                         that the user is in.
	 */
	public function get_user_teams($user_id, $admin_mode = FALSE)
	{
		if (!$admin_mode)
		{
			//Get the teams that the user is in
			$query = $this->db
						->where('user_id', $user_id)
						->select('team_id')
						->get('user_teams')
						->result();
			$team_ids = array_map(function($x){return $x->team_id;}, $query);
			//Check if user is in no teams
			if (empty($team_ids))
			{
				return NULL;
			}
			$this->db->where_in('teams.team_id', $team_ids);
		}

		 return $this->db
					->join('users', 'users.user_id = teams.team_leader', 'left')
					->select(
						'teams.team_name, teams.team_id, 
						teams.team_desc, 
						CONCAT(users.first_name, " ", users.last_name) as team_leader_name')
					->get('teams')
					->result();
	}
	
	/**
	 * Gets the users in a team
	 * @param  string|array $team The team(s) name that will be querried for users
	 * @return array       	Code Igniter db results array that contains the users
	 *                      in the team. Data taken from users table.
	 */
	public function get_team_users($team_id)
	{
		if (is_array($team_id))
		{
			$user_ids = $this->db->where('team_id', $team_id)->get('user_teams');
			if (count($user_ids) > 0)
			{
				return $this->db->where_in('user_id', $user_ids)->get('users');
			}
			else
			{
				return NULL;
			}

		}
		else
		{
			$query = $this->db->where('team_id', $team_id)->get('user_teams')->result();
			$user_ids = array_map(function($x) {return $x->user_id;}, $query);
			if (count($user_ids) > 0)
			{
				return $this->get_users($user_ids);
			}
			else
			{
				return NULL;
			}
		}
	}

	/**
	 * Gets the users not in the twam
	 * @param  int $team_id The id of the team to test
	 * @return array          A CI db results array of objects containing the users not in the team.
	 */
	public function get_users_not_in_team($team_id)
	{
		$users_in_team = $this->get_team_users($team_id);
		if (!empty($users_in_team))
		{
			$exclude_ids = array_map(
				function ($user) {return $user->user_id;},
				$users_in_team);
			$this->db->where_not_in('user_id', $exclude_ids);
		}
		
		return $this->get_users();
	}

	public function get_team_info($team_id)
	{
		$query = $this->db->where('team_id', $team_id)->get('teams')->row(); 
		$data['team_name'] = $query->team_name; //Get team name
		$data['team_id'] = $query->team_id; //Get Team ID

		$data['team_members_raw'] = $this->get_team_users($team_id); //CI result array of obj

		//Get amount of members
		if (isset($data['team_members_raw']))
		{
			$data['num_members'] = count($data['team_members_raw']);
		}
		else
		{
			$data['num_members'] = 0; //team_members_raw is NULL, so no users
		}

		//Get team logs total
		$data['team_logs'] = $this->db->select('COUNT(*) as count')
									->where('team_id', $team_id)
									->get('action_log')
									->row()
									->count;

		//Get Team leader
		$query2 = $this->db
			->select('*, CONCAT(first_name, " ", last_name) as name')
			->where('user_id', $query->team_leader)
			->get('users')
			->row();
		if (isset($query2))
		{
			$data['team_leader_name'] = $query2->name;
		}
		else
		{
			$data['team_leader_name'] = NULL;
		}

		if (isset($data['team_members_raw']))
		{
			foreach($data['team_members_raw'] as $team_member)
			{
				$data['team_members'][$team_member->name] = $team_member->user_id;
			}
		}

		return $data;
	}


	/**
	 * Gets the user's name from id
	 * @param  int $user_id Id of the user
	 * @return string          User's name
	 */
	public function get_user_name($user_id)
	{
		return $this->get_users($user_id)[0]->name;
	}


	/**
	 * Gets the team name from the team id
	 * @param  int $team_id The ID of the team
	 * @return string          THe team Name
	 */
	public function get_team_name($team_id)
	{
		return $this->db
			->select('team_name')
			->where('team_id', $team_id)
			->get('teams')
			->row()
			->team_name;
	}

	/**
	 * Get project name from project ID
	 * @param  int $project_id The ID of the project
	 * @return string             THe project name
	 */
	public function get_project_name($project_id)
	{
		return $this->db
			->select('project_name')
			->where('project_id', $project_id)
			->get('projects')
			->row()
			->project_name;
	}

	/**
	 * Get the action type name from the action type ID
	 * @param  int $type_id The action ype ID
	 * @return string          The action type name
	 */
	public function get_type_name($type_id)
	{
		return $this->db
			->select('type_name')
			->where('type_id', $type_id)
			->get('action_types')
			->row()
			->type_name;
	}

	/**
	 * Will get all table rows and return a HTML table string. Used for the view tables @see (Search/view_tables)
	 * @param  string $table  The table name
	 * @param  offset $offset Offset for pagination
	 * @return string         HTML string for the table
	 */
	public function get_all_entries($table, $offset)
	{
		$per_page = $this->config->item('per_page');

		//Conditions and data formatting for certain tables

		//Set From in db
		$this->db->from($table);
		
		//Format the table
		$this->sql_commands_for_table($table); //Applies table filters as stated in view_tables.php

		// Get total results before pagination
		$this->total_rows = $this->db->count_all_results('', FALSE);

		//Limits and Offset
		$this->db->limit($per_page, $offset);

		//Get Table Data
		$query = $this->db->get();

		//Censoring Password Hashes - See configuration for disabling this
		if ($this->config->item('show_hashes') && $this->db->field_exists('password', $table)) 
		{
			foreach ($query->result() as &$row)
			{
				$row->password = '***********';
			}
		}

		return $query;
	}
}

/* End of file Get_model.php */
/* Location: ./application/models/Get_model.php */