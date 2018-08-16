<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Misc Stats Model
 * ================
 * @author Ryan Sandoval
 * 
 * Use for Storing and Retreiving Miscelleneous 
 * Statistics from the database.
 * Misc statistics do not fit into other tables and
 * cannot be calculated from existing tables.
 */
class Misc_stats_model extends MY_Model {

    /** @var int ID of last row updated */
    public $update_id;

    /**
     * Updates the specified statistic to a certain value
     * @param string $stat_name
     * @param string $stat_value
     * @return void
     */
    public function update_stat($stat_name, $stat_value)
    {
        if (!$this->data_exists('misc_statistics', array('stat_name' => $stat_name)))
        {
            $this->create_stat($stat_name, $stat_value);
            return;
        }
        $this->db
            ->where('stat_name', $stat_name)
            ->set('stat_value', $stat_value)
            ->update('misc_statistics');
        $this->update_id = $this->db->insert_id();
        return;
    }

    /**
     * Creates the specified statistic
     * @param string $stat_name
     * @param string $starting_value Optional Starting Value
     * @return void
     */
    public function create_stat($stat_name, $starting_value = NULL)
    {
        $insert_data = array(
            'stat_name' => $stat_name,
            'stat_value' => $starting_value
        );
        $this->db->insert('misc_statistics', $insert_data);
        return;
    }

    /** 
     * Gets the statistic and its value by name
     * @param string $stat_name 
     * @return string Statistics Value
     */
    public function get_stat_by_name($stat_name)
    {
        return $this->db
            ->where('stat_name', $stat_name)
            ->get('misc_statistics')
            ->row()
            ->stat_value;
    }

} 

/* End of file Misc_stats_model.php */
