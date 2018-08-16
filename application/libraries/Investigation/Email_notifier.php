<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Investigate_base.php');
use Carbon\Carbon;


defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Notifier Library
 * ======================
 * 
 * Sends emails about incidents
 */
class Email_notifier extends Investigate_base
{
    protected $incident_id;

    /** @var array Emails last sent to */
    protected $sent_to = array();

    /** @var string Last message sent to users. */
    protected $message  = '';

    /** 
     * Constructs this class.
     * Loads the necessary resources
     */
    public function __construct()
    {
        parent::__construct();
        $this->CI->load->model('Settings/admin_model', 'admin_settings');

        //Load Library and Configs
		$this->CI->load->library('email');
		$this->CI->load->config('email');
    }

        /**
         * Sets the incident to send an email about
         * @param int $incident_id
         * @return Email_notifier Method Chaining
         */
    public function incident($incident_id)
    {
        if (is_int($incident_id))
        {
            $this->incident_id = $incident_id;
        }
        return $this;
    }

    /**
	 * Notifies admins on new incidents if they have chosen to receive
	 * notifications on new incidents
	 * @return boolean              TRUE on success
	 */
	public function new_incident_notify()
	{
        if (empty($this->incident_id))
        {
            show_error('Invalid Incident ID. Cannot send email');
        }

        //Sets incident id in investigator so we can get relevant logs table
        $this->CI->investigator->incident($this->incident_id);

		$users_send_to = $this->CI->admin_settings->get_notify_new_incidents();
		$incident_summary = $this->incident_info($this->incident_id);
		$incident_title = $this->incident_title($this->incident_id);

		foreach ($users_send_to as $user)
		{
            $sent_to[] = $user->email;
			//Generate the email
			$this->CI->email->from($this->CI->config->item('smtp_user'), 'Incident Manager');
			$this->CI->email->to($user->email);
			
			$this->CI->email->subject('New Incident');
			
			//Message formatting
			$message = $this->CI->config->item('new_incident_body');
			$message = str_replace('{name}', $user->name, $message);
			$message = str_replace('{link}', site_url('Incidents/report/'.$this->incident_id), $message);
			$message = str_replace('{summary}', $incident_summary, $message);
			$message = str_replace('{title}', $incident_title, $message);
			$message = str_replace('{relevant_logs}', $this->CI->investigator->relevant_logs(), $message);

			$this->CI->email->message($message);
			
            //Send it out!
            log_message('info', 'Sending Incident Notification to '.$user->email);
			$this->CI->email->send();
        }

        $this->reset();

        $this->sent_to = $sent_to;
        $this->message = $message;

		return TRUE;
    }
    
    /** 
     * Reset the sotred data
     * @return void
     */
    public function reset()
    {
        $to_reset = array(
            'incident_id' => NULL,
            'sent_to' => array(),
            'message' => ''

		);

		foreach ($to_reset as $item => $default)
		{
			$this->{$item} = $default;
		}
    }
}

/* End of file Email_notifier extends Investigate_base.php */
