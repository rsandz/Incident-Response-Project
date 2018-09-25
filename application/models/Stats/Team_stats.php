<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Team Statistics Model
 * ========================
 * @author Ryan Sandoval 
 * 
 * Gets Various statistics and info about a Team
 */
class Team_stats extends MY_Model {

    /** @var int The Team to query */
    public $team_id;

    /**
     * Sets the Team to query
     * @param $team_id
     * @return Team_stats
     */
    public function team($team_id)
    {
        $this->project = $team_id;
        return $this;
    }

    /**
     * Gets the total logs that a Team has made
     * @param $taem_id
     * @return int Total logs for team
     */
    public function total_logs($team_id = NULL)
    {
        if (!isset($team_id)) $team_id = $this->team_id;
        return $this->db
            ->where('team_id', $team_id)
            ->select('COUNT(`log_id`) as `total`')
            ->get('action_log')->row()->total;
    }
    /**
     * Gets all the stats
     * @param int $team_id 
     * @return array Contains all stats
     */
    public function get_all_stats($team_id = NULL)
    {
        if (!isset($team_id)) $team_id = $this->team_id;

        return array(
            'total_logs' => $this->total_logs($team_id),
        );
    }
}

/* End of file Team_stats.php */
