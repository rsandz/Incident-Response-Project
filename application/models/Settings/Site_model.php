<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Site Model
 * ==========
 * Model for Site (Global) settings.
 */
class Site_model extends MY_Model {

    /**
     * Sets the site notification that users see on the dashboard.
     * @param string $notification The new Notification
     * @return void
     */
    public function set_site_notification($notification = NULL)
    {
        if (!$this->data_exists('site_settings', array('setting_name' => 'site_notification')))
        {
            //Make a new setting if not exists
            $this->create_setting('site_notification', $notification);
        }

        $this->db
            ->where('setting_name', 'site_notification')
            ->set('setting_value', $notification)
            ->update('site_settings');
        return;
    }

    /**
     * Gets the Site's Global notifications. 
     * @return string The Notification String
     */
    public function get_site_notification()
    {
        if (!$this->data_exists('site_settings', array('setting_name' => 'site_notification')))
        {
            $this->set_site_notification(NULL);
        }

        return $this->db
            ->where('setting_name', 'site_notification')
            ->get('site_settings')
            ->row()
            ->setting_value;
    }

    /**
     * Creates a site setting
     * @param string $name Name of the Setting
     * @param string $value Starting Value of the Setting
     * @return void
     */
    public function create_setting($name, $value = NULL)
    {
        $insert_data = array(
            'setting_name'  => $name,
            'setting_value' => $value
        );
        $this->db
            ->insert('site_settings', $insert_data);
        return;
    }

    /**
     * Get all the current site settings
     * @return array Array containing all the site settings and their respective values
     */
    public function get_all_settings()
    {
        return array(
            'site_notification' => $this->get_site_notification()
        );
    }

}

/* End of file Site_model.php */
/* Location: ./application/models/settings/site_mode.php */
