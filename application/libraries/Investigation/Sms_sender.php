<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once('Investigate_base.php');
use Twilio\Rest\Client;

/**
 * SMS Sender Library
 * ==================
 * @author Ryan Sandoval
 * 
 * Library for Sending SMS
 * Only works with [SMS gateway] right now.
 * 
 * In the future when implmenting other SMS gateways,
 * turn this into a Driver (See CI documentation)
 */
class Sms_sender extends Investigate_base
{
    /** @var Client Twilio Client */
    protected $client;

    protected $incident_id;

    /** @var array Array of result objects that holds users to send sms to */
    protected $send_to;

    public function __construct()
    {
        parent::__construct();

        //Load Configs
        $this->CI->load->config('sms');
        $this->CI->load->config('incidents');

        $twilio = $this->CI->config->item('twilio_api');

        //Start Twilio Client
        $sid = $twilio['sid'];
        $token = $twilio['token'];
        $this->phone_num = $twilio['twilio_number'];

        //Start Twilio Client
        $this->client = new Client($sid, $token);

        //Get the users to send it to
        $this->CI->load->model('Settings/admin_model', 'admin_settings');
        $this->send_to = $this->CI->admin_settings->get_notify_incident_sms();
    }

    /**
     * Sends a message via the Twilio Client
     * @param string $num Phone Number of recipient
     * @param string $msg The message to send
     */
    public function send_message($num, $msg)
    {
        $message = $this->client
                    ->messages
                    ->create(
                        $num, // to
                        array("from" => $this->phone_num, "body" => $msg)
                    );

        echo($message->sid);
    }
    
    /**
     * Sets the incident to send an email about
     * @param int $incident_id
     * @return Sms_sender Method Chaining
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
     * Notifies users by SMS on new incidents
     * @return void
     */
    public function notify_incident()
    {
        $incident_link = site_url('Incidents/report/'.$this->incident_id);
        
        foreach($this->send_to as $user)
        {
            log_message('info', "Sending message to {$user->name} @ {$user->phone_num}");

            //Get message from logs
            $message = $this->CI->config->item('new_incident_sms');
            
            //Replace Placeholders in template
            $message = str_replace('{name}', $user->name, $message);
            $message = str_replace('{link}', site_url('Incidents/report/'.$this->incident_id), $message);
            
            $this->send_message($user->phone_num, $message);
        }
    }

    /** 
     * Reset the sotred data
     * @return void
     */
    public function reset()
    {
        $to_reset = array(
            'incident_id' => NULL,
            'sent_to' => $this->admin_settings->get_notify_incident_sms(),

		);

		foreach ($to_reset as $item => $default)
		{
			$this->{$item} = $default;
		}
    }

}

/* End of file Sms_sender.php */
/* Location: ./application/libraries/Investigation/Sms_sender.php */
