<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('Table_base.php');

class Team_model extends Table_base {

	/*
	---------------------
		Get Functions
	---------------------
	*/

	/**
	 * Gets the team by name
	 * @param  string $name Name
	 * @return object|boolean       The row object if successful. FALSE otherwise
	 */
	public function get_by_name($name)
	{
		$this->db->where('team_name', $name);
		if($result = $this->db->get('teams'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}

	}

	/**
	 * Gets the team by ID
	 * @param  integer $id The team's ID
	 * @return object|boolean     The row object is successful. FALSE Otherwise
	 */
	public function get_by_id($id)
	{
		$this->db->where('team_id', $id);
		if($result = $this->db->get('teams'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}		
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
		$this->apply_sort();
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
		$this->apply_sort();
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
		$this->apply_sort();
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

	/**
	 * Gets information about a team
	 * @param int $team_id The ID of the team to get info for
	 * @return array Array containing data
	 */
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
	 * Custom Sort function for Team Model
	 */
	public function apply_sort()
	{
		switch ($this->sort['sort_field'])
		{
			case 'log_num':
				$this->db
				->join('action_log', '`action_log`.`team_id` = `teams`.`team_id`', 'left')
				->order_by('COUNT(`log_id`)', $this->sort['sort_dir'])
				->group_by('team_id');
				break;
			default:
				parent::apply_sort();
				break;
		}
	}

	/*
	-----------------------
		Make Functions
	-----------------------
	*/

	/**
	 * Makes a team based on the inserted data
	 * @param  array $insert_data Associative array of the data
	 * @param boolean $validate Whether to validate before Inserting
	 * @return integer            The integer ID of the team just created
	 */
	public function make($insert_data, $validate = FALSE)
	{
		if ($validate && !$this->validate_insert_data($insert_data))
		{
			return FALSE;
		}
		$this->db->insert('teams', $insert_data);
		return $this->db->insert_id();
	}

	/**
	 * Validates the Insert Data for the team
	 * @param array	$insert_data
	 * @return boolean TRUE if valid
	 */
	public function valid_insert_data($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'team_name' => $insert_data['team_name'],
		);

		if ($this->data_exists('teams', $check_array))
		{
			return FALSE;
		}
		return TRUE;
	}

}

/* End of file Team_model.php */
/* Location: ./application/models/tables/Team_model.php */