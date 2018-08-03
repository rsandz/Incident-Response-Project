<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Investigator_model extends MY_Model {

    /** 
     * Gets the amount of logs per action
     * return CI_DB_result
     */
    public function score_logs_relevancy($limit = 5, $direction = 'DESC')
    {
        $this->db->query("
            CREATE TEMPORARY TABLE `action_scores`
            SELECT `action_id`, -COUNT(*) as `amount`
            FROM action_log
            GROUP BY `action_id`
            ORDER BY `amount` DESC;
        ");
        
        $result = $this->db->query("
            Select `log_id`, (`amount` + CAST(`hours` as signed)) as Score
            FROM `action_log`
            LEFT JOIN `action_scores` ON `action_scores`.`action_id` = `action_log`.`action_id`
            ORDER BY `Score` {$direction}
            LIMIT {$limit};
        ");

        //Cleanup
        $this->db->query("
            DROP TEMPORARY TABLE `action_scores`;
        ");

        return $result;
    }

}

/* End of file Investigator_model.php */
