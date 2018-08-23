<?php

if (!function_exists('set_sort'))
{
    /**
     * Sets the sort values of a certain idetifier
     * @param string $identifier
     */
    function set_sort($identifier, $sort_field, $sort_dir = 'desc')
    {
        $CI =& get_instance();
        $session_identifier = 'sort_'.$identifier;
        $sort_data = array(
            'sort_field' => $sort_field,
            'sort_dir' => $sort_dir
        );
        $CI->session->set_userdata($session_identifier, $sort_data);
    }
}

if (!function_exists('get_sort'))
{
    /**
     * Gets the sort values of a certain identifier
     * @param string $identifier
     * @return array Contains sort_field and sort_dir
     */
    function get_sort($identifier, $default_field = NULL)
    {
        $CI =& get_instance();

        $session_identifier = 'sort_'.$identifier;
        $sort = $CI->session->{$session_identifier};

        //If sort is not set, set it to default
        if (empty($sort))
        {
            set_sort($identifier, $default_field);
            $sort = $CI->session->{$session_identifier};
        }

        return $sort;
    }
}