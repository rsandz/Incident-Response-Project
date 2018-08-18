<?php
/*
| -------------------------------------------------
|       SMS Configuration
| -------------------------------------------------
| Configuration for SMS functionality of the app.
| The SMS gateway that this app uses is Twilio.
| See https://www.twilio.com/
*/

/* 
| -------------------------------------------------
|   Twilio API Configurations
| -------------------------------------------------
| Twilio API access keys
| Sid and Token can be found in your twilio dashboard
| The twilio_num is your phone number for Twilio
*/

$config['twilio_api'] = array(
    'sid' => '',
    'token' => '',
    'twilio_number' => ''
);