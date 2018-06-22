<?php

if (!function_exists('check_login'))
{
	/**
	 * Will check if the user is logged in.
	 *
	 * @param boolean $mode Whether to redirect when not logged in, or simply output a string. 
	 *
	 * @return	True if logged in, False if not
	 */
	function check_login($redirect = TRUE)
	{
		$CI =& get_instance();
		if (isset($CI->session->logged_in))
		{
			return TRUE;
		}
		if ($redirect)
		{
			redirect('login','refresh', 401);
		}
		return FALSE;
	}
}


if (!function_exists('check_admin'))
{
	/**
	 * Will check if the user is an admin
	 *
	 * @param boolean $redirect Whether to redirect when not logged in, or simply output a string
	 *
	 * @return True if logged in, False if not
	 */
	function check_admin($redirect = FALSE)
	{
		$CI =& get_instance();
		if ($CI->session->user_id !== NULL && $CI->session->privilege == 'admin')
		{
			return TRUE;
		}
		if ($redirect)
		{
			redirect('login','refresh');
		}
		return FALSE;
	}
}

if (!function_exists('check_privileges'))
{
	/**
	 * Checks if the user has a required privilege.
	 * @param  string  $privilege The privilege that the user should have
	 * @param  boolean $redirect  Whether to redirect Home or not
	 * @return boolean            True if the user has that privilege. False otherwise
	 */
	function check_privileges($privilege, $redirect = FALSE)
	{
		$CI =& get_instance();
		if ($CI->session->privilege == $privilege)
		{
			return TRUE;
		}
		else
		{
			//Not authorized
			log_message('info', 'Insufficient Privileges for User '.$this->session->user_id.' to access '.current_url());
			if ($redirect)
			{
				redirect('home','refresh'); 
			}
			return FALSE;
		}
	}
}