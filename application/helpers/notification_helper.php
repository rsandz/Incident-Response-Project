<?php
/*
| Notification Helper
| ===================
| Helper for diaplying notifications and errors.
*/

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

if (!function_exists('set_error'))
{
    /**
     * Flashes the Error Message so it can be displated on 
     * the next page
     * 
     * @param string $msg The error string
     * @param string HTML formatted error
     */
    function set_error($msg)
    {
        $CI =& get_instance();
        $data['errors'] = $msg;
        $errors = $CI->load->view('templates/errors', $data, TRUE);
        $CI->session->set_flashdata('errors', $errors);
        return $errors;
    }
}


/* End of file notification_helper.php */
/* Location: ./application/helpers/notification_helper.php */