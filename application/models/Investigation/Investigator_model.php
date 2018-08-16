<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use Carbon\Carbon;

class Investigator_model extends MY_Model {

    /** 
     * Uses an Algorithm to rank logs based on relevancy
     * See the Incidents configuration for more details on the algorithm
     * and how to change it.
     * @param string $incident_date Date of the incident
     * @param int $Limit Amount of Logs to get
     * @param string $direction What direction to sort logs. DESC or ASC
     * @return CI_DB_result
     */
    public function score_logs_relevancy($incident_date, $limit = NULL, $direction = 'DESC')
    {
        $this->load->config('incidents');

        $limit = $limit ? "LIMIT {$limit}" : NULL;
        $date = $incident_date->format('Y-m-d');
        $mult = $this->config->item('multipliers');

        //Scores Actions based on frequency
        $this->db->query("
            CREATE TEMPORARY TABLE `action_scores`
            SELECT `action_id`, COUNT(*) as `amount`
            FROM action_log
            GROUP BY `action_id`
            ORDER BY `amount` DESC;
        ");

        //Scores the logs
        $result = $this->db->query("
            Select 
                `log_id`, 
                ( 
                    CAST(`hours` as signed)  * {$mult['hours']}
                    - `amount` * {$mult['action']} 
                    - ABS(DATEDIFF('{$date}', `log_date`)) * {$mult['date']}
                ) as Score
            FROM `action_log`
            LEFT JOIN `action_scores` ON `action_scores`.`action_id` = `action_log`.`action_id`
            WHERE `log_date` <= '{$date}'
            ORDER BY `Score` {$direction}
            {$limit};
        ");

        //Cleanup
        $this->db->query("
            DROP TEMPORARY TABLE `action_scores`;
        ");
        return $result;
    }

}

/* End of file Investigator_model.php */
