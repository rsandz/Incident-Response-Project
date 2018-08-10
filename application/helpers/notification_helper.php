<?php

if (!function_exists('set_notification'))
{
    /**
     * Flashes the notification msg given so it can be displayed on
     * the next page.
     * 
     * @param string $msg The notification
     * @return string HTML formatted notification
     */
    function set_notification($msg) 
    {
        $CI =& get_instance();
        $data['notification'] = $msg;
        $notifications = $CI->load->view('templates/notification', $data, TRUE);    
        $CI->session->set_flashdata('notifications', $notifications);
        return $notifications;
    }
}


/* End of file notification_helper.php */
/* Location: ./application/helpers/notification_helper.php */

