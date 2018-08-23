<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Project Statistics Model
 * ========================
 * @author Ryan Sandoval 
 * 
 * Gets Various statistics and info about a project
 */
class Project_stats extends MY_Model {

    /** @var int The project to query */
    public $project_id;

    /**
     * Sets the Project to query
     * @param $project_id
     * @return Project_stats
     */
    public function project($project_id)
    {
        $this->project = $project_id;
        return $this;
    }

    /**
     * Gets the total logs that a project has made
     * @param $project_id
     * @return int Total logs for project
     */
    public function total_logs($project_id = NULL)
    {
        if (!isset($project_id)) $project_id = $this->project_id;
        return $this->db
            ->where('project_id', $project_id)
            ->select('COUNT(`log_id`) as `total`')
            ->get('action_log')->row()->total;
    }

    /**
	 * Get all distict teams that have created a log for the
	 * specified project
	 * @param $project_id
     * @return CI_DB_Result
	 */
	public function get_active_teams($project_id = NULL)
	{
        if (!isset($project_id)) $project_id = $this->project_id;
		return $this->db
        ->select(' 
            DISTINCT(`action_log`.`team_id`) AS `team_id`, 
            COUNT(`action_log`.`log_id`) AS `num_logs`,
            `teams`.`team_name` as `team_name`,
            ')
		->join('teams', '`action_log`.`team_id` = `teams`.`team_id`')
		->where('project_id', $project_id)
        ->group_by('team_id')
        ->order_by('num_logs', 'DESC')
		->get('action_log');
    }

    /**
     * Gets all the stats
     * @param int $project_id 
     * @return array Contains all stats
     */
    public function get_all_stats($project_id = NULL)
    {
        if (!isset($project_id)) $project_id = $this->project_id;

        return array(
            'total_logs' => $this->total_logs($project_id),
            'active_teams' => $this->get_active_teams($project_id)
        );
    }
}

/* End of file Project_stats.php */
