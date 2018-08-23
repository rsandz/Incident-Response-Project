<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Statistics Model
 * ========================
 * @author Ryan Sandoval 
 * 
 * Gets Various statistics and info about a user
 */
class User_stats extends MY_Model {

    /** @var int The user to query */
    public $user_id;

    /**
     * Sets the user to query
     * @param $user_id
     * @return Project_stats
     */
    public function user($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * Gets the total logs that a project has made
     * @param $project_id
     * @return int Total logs for project
     */
    public function total_logs($user_id = NULL)
    {
        if (!isset($user_id)) $user_id = $this->user_id;
        return $this->db
            ->where('user_id', $user_id)
            ->select('COUNT(`log_id`) as `total`')
            ->get('action_log')->row()->total;
    }

    /**
     * Gets the last log of the user
     * @param int $user_id 
     * @return obj Result object from row()
     */
    public function last_log($user_id = NULL)
    {
        if (!isset($user_id)) $user_id = $this->user_id;
        return $this->db
        ->where('user_id', $user_id)
        ->order_by('created_on')
        ->limit(1)
        ->get('action_log')->row();
    }

    /**
     * Ranks the frequency of actions used by this user
     * @param int $user_id
     * @param string $dir The direction of sort
     * @param int $limit Amount of logs to get
     * @return obj Result object that contains action_name and frequency
     */
    public function action_ranking($user_id = NULL, $dir = 'desc', $limit = 10)
    {
        if (!isset($user_id)) $user_id = $this->user_id;
        return $this->db
        ->where('user_id', $user_id)
        ->join('actions', '`actions`.`action_id` = `action_log`.`action_id`')
        ->select('`actions`.`action_name` as `Action Name`, COUNT(`log_id`) as `Frequency`')
        ->group_by('action_name')
        ->order_by('COUNT(`log_id`)', $dir)
        ->limit($limit)
        ->get('action_log');
    }



    /**
     * Gets all the stats
     * @param int $user_id 
     * @return array Contains all stats
     */
    public function get_all_stats($user_id = NULL)
    {
        if (!isset($user_id)) $user_id = $this->user_id;

        return array(
            'total_logs' => $this->total_logs($user_id),
            'last_log' => $this->last_log($user_id),
            'action_ranking' => $this->action_ranking($user_id)
        );
    }
}

/* End of file User_stats.php */
