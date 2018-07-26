<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_Model extends MY_Model {

    /** 
     * Initializes the model
     * Loads the necessary resources to run the analytics model
     */
    public function __construct()
    {
        parent::__construct();
    }

    /** 
     * Empties the Analytics Metrics Table. Use when updating the table.
     * @return void
     */
    public function empty_metrics_settings()
    {
        $this->db->truncate('analytics_metrics');
    }

    /**
     * Inserts the new metrics settings to use into the table
     * Analytics Metrics Table
     * 
     * Please note that the way the update works is by first 
     * truncating the settings table and then inserting the 
     * settings again. Thus, $new_settings should include
     * both the old and the new settings
     * 
     * @param array $new_settings The New settings to insert
     * @return void
     */
    public function update_metrics_settings($new_settings)
    {
        $this->empty_metrics_settings();
        foreach ($new_settings as $setting)
        {
            $insert_data = array(
                'metric_name'     => $setting['metric_name'],
                'metric_operator' => $setting['metric_operator'],
                'metric_value'    => $setting['metric_value']
            );
            $this->db->insert('analytics_metrics', $insert_data);
        }

    }

    /**   
     * Gets the current settings for the analytics
     * The settings are organized like such:
     * $settings =
     * [
     *     ['metric_name','metric_operator','metric_value'],
     *     ['metric_name','metric_operator','metric_value'],
     *      etc...
     * ]
     * 
     * @return array An array of an array of metrics settings
     *               or an emptry array if there are no settings
     */
    public function get_current_metrics()
    {
        $metrics = $this->db->get('analytics_metrics')->result();
        $settings_array = array();

        if (empty($metrics))
        {
            return array();
        }

        foreach($metrics as $metric)
        {
            $settings = array(
                'metric_name' => $metric->metric_name,
                'metric_operator' => $metric->metric_operator,
                'metric_value' => $metric->metric_value
            );
            $settings_array[] = $settings;
        }
        return $settings_array;
    }

}

/* End of file Analytics_Model.php */
