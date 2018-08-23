<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('Table_base.php');

class Project_model extends Table_base {

	/** 
	 * If TRUE, will get all results, regardless if user_lock is set
	 * @var boolean Whether current user is admin 
	 */
	protected $admin_mode;
	
	/** @var boolean Whether to lock projects to get to current user */
	protected $user_to_lock;

	/**
	 * Constructor for this class
	 * Loads the necessary resources
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Use to lock project searces that contain this user (as manager)
	 * For this to effect, admin_mode must be false
	 * @param int $user_id User ID. Set to false to disable locking
	 * @return Project_model Method chaining
	 */
	public function user_to_lock($user_id)
	{
		$this->user_to_lock($user_id);
		return $this;
	}
	
	/**
	 * Use to unlock project searches to include active items.
	 * @param boolean $mode
	 * @return Project_model Method chaining
	 */
	public function admin_mode($mode)
	{
		$this->admin_mode = $mode;
		return $this;
	}

	/**
	* Applies restrictions related to admin mode and user locking
	* @param boolean $admin_mode If TRUE, enables get of inactive projects
	* @param mixed $user_to_lock $admin_mode must be FALSE to effect. Locks the user in get
	* @return void
	*/
   protected function apply_restrictions($admin_mode = NULL, $user_to_lock = NULL)
   {
	   if (!isset($admin_mode)) $admin_mode = $this->admin_mode;
	   if (!isset($user_to_lock)) $user_to_lock = $this->user_to_lock;

	   if (!$admin_mode) $this->db->where('is_active', 1);
	   if (!$admin_mode && $user_to_lock != FALSE) 
	   {
		   $this->db->where('project_leader', $user_to_lock);
	   }
   }
   
   /**
	* Custom Sort function for project model
    */
   protected function apply_sort()
   {
		switch ($this->sort['sort_field'])
		{
			case 'log_num':
				$this->db
				->join('action_log', '`action_log`.`project_id` = `projects`.`project_id`', 'left')
				->order_by('COUNT(`log_id`)', $this->sort['sort_dir'])
				->group_by('project_id');
				break;
			default:
				parent::apply_sort();
				break;
		}
   }

	/*
	---------------------
		Get Methods
	---------------------
	*/

	/**
	 * Gets Projects with ids. Otherwise, gets all Projects
	 * @param mixed $project_id Either a single project ID, or array of project IDs
	 * @param boolean $admin_mode If TRUE, will get all projects. Set to NULL to use default
	 * @param mixed $user_to_lock Locks search to this projects with this user
	 * 							  For this to effect, admin_mode must be false
	 * @return object db result object for that project(s)
	 */
	public function get($project_id = NULL, $admin_mode = NULL, $user_to_lock = NULL)
	{
		//Apply restrictions
		$this->apply_restrictions($admin_mode, $user_to_lock);
		//Apply Sort
		$this->apply_sort();

		//Filter
		if (isset($project_id))
		{
			if (is_array($project_id))
			{
				//Use where_in
				$this->db->where_in('project_id', $project_id);
			}
			else
			{
				$this->db->where('project_id', $project_id);
			}
		}

		return $this->db
			->join('users', 'users.user_id = projects.project_leader', 'left')
			->select(
				'`project_name`, `projects`.`project_id`, 
				CONCAT(`users`.`first_name`, " ", `users`.`last_name`) as `project_leader_name`, `project_desc`,
				`project_leader` as `project_leader_id`, IF(`is_active`, "TRUE", "FALSE") as is_active')
			->get('projects')->result();
			
	}

	/**
	 * Gets the project by name
	 * @param  string $name Name
	 * @param boolean $admin_mode If TRUE, will also get inactive projects
	 * @param mixed $user_to_lock Locks search to this projects with this user
	 * * 							  For this to effect, admin_mode must be false
	 * @return object|boolean       The row object if successful. FALSE otherwise
	 */
	public function get_by_name($name, $admin_mode = TRUE, $user_to_lock = FALSE)
	{
		$this->apply_restrictions($admin_mode, $user_to_lock);
		//Apply Sort
		$this->apply_sort();

		$this->db
			->where('project_name', $name)
			->join('users', 'users.user_id = projects.project_leader', 'left')
			->select(
				'`project_name`, `projects`.`project_id`, 
				CONCAT(`users`.`first_name`, " ", `users`.`last_name`) as `project_leader_name`, `project_desc`,
				`project_leader` as `project_leader_id`, IF(`is_active`, "TRUE", "FALSE") as is_active');
		if($result = $this->db->get('projects'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}

	}

	/**
	 * Gets the project by ID
	 * @param  integer $id The project's ID
	 * @param boolean $admin_mode If TRUE, will also get inactive projects
	 * @param mixed $user_to_lock Locks search to this projects with this user
	 * * 							  For this to effect, admin_mode must be false
	 * @return object|boolean     The row object is successful. FALSE Otherwise
	 */
	public function get_by_id($id, $admin_mode = TRUE, $user_to_lock = FALSE)
	{
		$this->apply_restrictions($admin_mode, $user_to_lock);
		//Apply Sort
		$this->apply_sort();

		$this->db
			->where('project_id', $id)
			->join('users', '`users`.`user_id` = `projects`.`project_leader`', 'left')
			->select(
				'`project_name`, `projects`.`project_id`, 
				CONCAT(`users`.`first_name`, " ", `users`.`last_name`) as `project_leader_name`, `project_desc`,
				`project_leader` as `project_leader_id`, IF(`is_active`, "TRUE", "FALSE") as is_active');
		if($result = $this->db->get('projects'))
		{
			return $result->row();
		}
		else
		{
			return FALSE;
		}		
	}
	
	/*
	--------------------
		Make Methods
	--------------------
	*/

	/**
	 * Makes a project based on the inserted data
	 * @param array $insert_data Associative array of the data
	 * @param boolean $validate Whether to validate before Inserting
	 * @return integer            The integer ID of the project just created
	 */
	public function make($insert_data, $validate = FALSE)
	{
		if ($validate && !$this->validate_insert_data($insert_data)) return FALSE;
		$this->db->insert('projects', $insert_data);

		return $this->db->insert_id();
	}
	
	/**
	 * Validates Project insert data
	 * Same Name must not already exsist
	 * @param $insert_data
	 * @return boolean If valid (TRUE) or not (FALSE)
	 */
	public function valid_insert_data($insert_data)
	{
		//Check if action already exists
		$check_array = array(
			'project_name' => $insert_data['project_name'],
		);

		if ($this->data_exists('projects', $check_array))
		{
			return FALSE;
		}
		return TRUE;
	}

}

/* End of file Project_model.php */
/* Location: ./application/models/tables/Project_model.php */