<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication Library
 * =============================================================
 * @author Ryan Sandoval
 * @package Authentication
 * @version 1.0
 *
 * This library is used to authenticate the user and to handle any
 * querries regarding whether the user is logged in or not.
 *
 * It also handles password recovery functionality, including:
 * 	- Email Sending
 * 	- Password Reseting and Hashing
 *
 * Please note, upon sending the email, the user's password would
 * have been set to a temporary password. 
 * 	i.e. If the user remembers their old password after asking 
 * 		for a password reset, it will no longer work.
 *
 * --------------------------------------------------------------
 *
 * Please Refer to the authentication configuration to alter the
 * more important functionality of this library
 * 	{@link ./application/config/authentication.php)
 */
class Authentication
{
	/**
	 * Code Igniter Instance
	 * @var object
	 */
	protected $CI;

	/**
	 * Array of errors that occurred during authentication
	 * i.e. - Incorrect Credentials
	 * 		- Not Logged in when accessing a page
	 * @var string
	 */
	private $errors = array();

	/**
	 * Whether to return a specific error in login failure
	 * @var boolean
	 */
	private $specific_errors;

	/**
	 * Array of Error messages set in the config file. The user
	 * will see these messages
	 * @var array
	 */
	private $messages;

	/**
	 * The session data set by logging in must contain these values
	 * @var array
	 */
	private $session_data_base = array(
		'email', 'first_name', 'last_name', 'name', 'user_id', 'privileges', 'logged_in'
	);

	/**
	 * The URL that the user was redirected from if
	 * any privelege check failed.
	 * i.e. Last page the user was at before being redirected
	 * 		due to not being logged in, not admin, etc.
	 * @var string
	 */
	public $redirected_url = '';

	/**
	 * Reason why the User was redirected
	 * @var string
	 */
	public $redirected_reason = '';


	/**
	 * Constructor for the Authentication Library
	 *
	 * Loads the CI instance and various other resources.
	 */
	public function __construct()
	{
        $this->CI =& get_instance();

        //Load the user authentication model
        $this->CI->load->model('Tables/user_model');

        //Load Configuration and Set the properties
        $this->CI->load->config('authentication', TRUE);

        $this->specific_errors = $this->CI->config->item('specific_errors', 'authentication');
        $this->messages = $this->CI->config->item('messages', 'authentication');
        
        //Take the salt defined in the global config (i.e from appconfig) first. If not set, use default in
        //authentication config.
        $this->salt = $this->CI->config->item('salt') ?: $this->CI->config('default_salt', 'authentication');

        //Save the redirected url from flash data
        $this->redirected_url = $this->CI->session->redirected_url;

        //Tell the user why they were redirected to login page
        if (isset($this->redirected_url))
        {
        	$this->error('reason_not_logged_in', TRUE);
        }
	}

	/**
	 * Use to login the user
	 * @param  string $email    The user Email
	 * @param  string $password The password that the user entered
	 * @return boolean           True if sucessful. False otherwise
	 */
	public function login_user($email, $password)
	{
		//Validate email first
		$user = $this->CI->user_model->get_by_email($email);

		if (empty($user))
		{
			//Invalid User
			$this->error('invalid_email');
			return FALSE;
		}

		//Validate the Password
		$stored_pass = $user->password;
		if ($stored_pass === crypt($password, $stored_pass))
		{
			//User is valid, so login
			$this->set_session_data($user);
			return TRUE;
		}
		else
		{
			//Incorect Password
			$this->error('invalid_pass');
			return FALSE;
		}
	}

	/**
	 * Logs the user out of the site
	 * @return boolean TRUE if successful
	 */
	public function logout_user()
	{
		//First clear the session data
		$this->unset_session_data();

		//Destroy Session
		$this->CI->session->sess_destroy();
		return TRUE;
	}

	/**
	 * Sets the session to include the user's data
	 * 
	 * @param object $user The user object taken stright from the database.
	 * @return void
	 */
	public function set_session_data($user)
	{
		$sess_data = array(
			'email'      => $user->email,
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
			'name'       => $user->name,
			'user_id'    => $user->user_id,
			'privileges' => $user->privileges,
			'logged_in'  => TRUE
		);
		$this->validate_sess_data($sess_data);
		$this->CI->session->set_userdata($sess_data);

		//Unset the redirected URL in session.
		$this->CI->session->unset_userdata('redirected_url');
	}

	/**
	 * Unsets the user data set by logging in
	 * @return boolean TRUE if successful
	 */
	public function unset_session_data()
	{
		$this->CI->session->unset_userdata($this->session_data_base);
		return TRUE;
	}

	/**
	 * Ensures that the session data adheres to the
	 * structure of the session data base.
	 * 
	 * @return boolean TRUE if the data is good
	 */
	public function validate_sess_data($sess_data)
	{
		//Must be the same length
		if (count($sess_data) !== count($this->session_data_base))
		{
			show_error('Session data does not adhere to standards. Incorrect Size.');
		}

		foreach($sess_data as $key => $val)
		{
			if(!in_array($key, $this->session_data_base))
			{
				show_error('Session data does not adhere to standards. Key not in base.');
			}
		}

		return TRUE;		
	}

	/**
	 * Will check if the user is logged in.
	 *
	 * @param boolean $mode Whether to redirect when not logged in, or simply output a string. 
	 *
	 * @return	True if logged in, False if not
	 */
	function check_login($redirect = TRUE)
	{
		if (isset($this->CI->session->logged_in))
		{
			return TRUE;
		}
		if ($redirect)
		{
			$this->CI->session->set_tempdata('redirected_url', current_url(), 300);
			redirect('login','refresh', 401);
		}
		return FALSE;
	}

	/**
	 * Will check if the user is an admin
	 *
	 * @param boolean $redirect Whether to redirect when not logged in, or simply output a string
	 *
	 * @return True if logged in, False if not
	 */
	function check_admin($redirect = FALSE)
	{
		$this->check_login(TRUE);

		if ($this->CI->session->user_id !== NULL && $this->CI->session->privileges == 'admin')
		{
			return TRUE;
		}
		if ($redirect)
		{
			redirect('Dashboard','refresh');
		}
		return FALSE;
	}

	/**
	 * Checks if the user has a required privilege.
	 * @param  string  $privilege The privilege that the user should have
	 * @param  boolean $redirect  Whether to redirect Home or not
	 * @return boolean            True if the user has that privilege. False otherwise
	 */
	function check_privileges($privilege, $redirect = FALSE)
	{
		if ($this->CI->session->privileges == $privilege)
		{
			return TRUE;
		}
		else
		{
			//Not authorized
			if ($redirect)
			{

				redirect('home','refresh'); 
			}
			return FALSE;
		}
	}

	/**
	 * Recover the user's account using provided email.
	 * @return boolean TRUE if Successful. False if not.
	 */
	public function recover($email)
	{
		$user = $this->CI->user_model->get_by_email($email);
		//Validate email
		if (empty($user))
		{
			$this->error('invalid_email');
			return FALSE;
		}

		//Set the password to a temporary password
		$this->CI->load->helper('string');

		$temp_pass = random_string();
		$insert_data = array('password' => crypt($temp_pass, $this->salt));
		$this->CI->user_model->update($user->user_id, $insert_data);

		//Generate the email
		//------------------
		
		//load Email Library and Config
		$this->CI->load->library('email');
		$this->CI->load->config('email');

		$this->CI->email->from($this->CI->config->item('smtp_user'), 'Password Manager');
		$this->CI->email->to($email);
		
		$this->CI->email->subject('Password Recovery Request');
		
		//Message formatting
		$message = $this->CI->config->item('recover_email_content', 'authentication');
		$message = str_replace('{name}', $user->name, $message);
		$message = str_replace('{link}', site_url("recover-form/{$user->user_id}/{$temp_pass}"), $message);

		$this->CI->email->message($message);

		//Send it out!
		$this->CI->email->send();

		return TRUE;
	}

	/**
	 * Reset the user's password
	 * @param int $user_id The user's ID
	 * @param string $pass The new password
	 * @return boolean TRUE if successful
	 */
	public function reset_pass($user_id, $pass)
	{
		$update_data = array('password' => crypt($pass, $this->salt));
		$this->CI->user_model->update($user_id, $update_data);
		return TRUE;
	}

	/**
	 * Validates the password given with password in the database
	 * @param  int $user_id   The User ID of the user
	 * @param  string $pass The password
	 * @return boolean            True if Validated. False Otherwise
	 */
	public function validate_pass($user_id, $pass)
	{
		$user = $this->CI->user_model->get_by_id($user_id, 'id');

		if ($user->password === crypt($pass, $user->password))
		{
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Adds an error to the error array
	 * @param string $error 			The error key that matched the error in the 
	 *                        			$this->message array
	 *                         			OR
	 *                          		An error String
	 * @param string $ignore_specific 	Set this to TRUE to display the error
	 *                                  instead of the general error even if
	 *                                  specific errors in config is FALSE
	 * @return void 
	 */
	public function error($error, $ignore_specific = FALSE)
	{
		$msg = $this->messages[$error];

		if (!$this->specific_errors && !$ignore_specific)
		{
			$msg = $this->messages['general_error'];
		}

		//Prevent Duplicates
		if (!in_array($msg, $this->errors))
		{
			$this->errors[] = $msg; //Add it in
		}
		return;
	}

	/**
	 * Gets all the errors during authentication
	 *
	 * Error can be specific or general base on the $specific_errors config	
	 * @return string The error
	 */
	public function get_errors()
	{
		$string = '';
		foreach ($this->errors as $error)
		{
			$string .= $error."<br>";
		}
		return $string;
	}
}

/* End of file Authentication.php */
/* Location: ./application/libraries/Authentication.php */
