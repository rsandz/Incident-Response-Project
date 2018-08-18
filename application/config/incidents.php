<?php

/*
| ---------------------------------------------------------
|  					Incidents Configuration
| ---------------------------------------------------------
|
| This contains the configuration for the incidents and
| investigations library.
*/

/*
| ---------------------------------------------------------
|					Relevant Logs Algorithm
| ---------------------------------------------------------
|
| To find logs relevant to an incident, every log is given a score.
|
| The relevant action algorithm takes into account the amount
| of times an action occurs, the hours in the log and the date difference
| between the log and incident day (in Days)
|
| The Relevant Logs Algorithm is as follows:
| (hours) * (hours_mult) - (action_freq) * (action_mult) - |DATEDIFF(incident_date, log_date) * (date_mult)|
|
| Where:
| 	action_freq 	= 	# Times Action is used in logging)
|	incident_date 	= 	Date of incident
|	log_date 		= 	Date of the log
|	hours 			= 	Hours in the log
|	*_mult 			= 	Multiplier
|
| Note: Action refers to the action_id used in the log.
|		DATEDIFF is a MySQL Function
*/

/* 
| Multipliers for the above algorithm 
| ===================================
|	
| TO disable a component, set to 0;
*/
$config['multipliers'] = array(
	'action' => 0.2,
	'date'   => 0.5,
	'hours'  => 1
);

/*
| ---------------------------------------------------------
|					Email Contents
| ---------------------------------------------------------
|
| Stores the messages of the following emails:
|  - New Incident Notification
|  - Investigation Complete
| 
| Use templates to populate the email.
| {name} - Replaced with User name
| {link} - Replaced with Link to Incident Report Page
| {title} - Replaced with Incident Title
| {summary} - Replaced with Incident Summary
| {relevat_logs} - Replaced with Relevant Logs Table
*/

$config['new_incident_body'] = "
	<p>Hello {name}, </p>

	<p>
		An Incident has occured that may require your attention.
	</p>
	<p>
		The following is a short summary of the incident that has occured:
	<p>
		{title}
		{summary}
		<p>
			<h2>Relevant Logs:</h2>
			{relevant_logs}
		</p>
	</p>
	<hr>
	<p>
		For more Information, please visit this link: {link}
	</p>
	
	<p></p>
	<p>Kind Regards, <br>
	Incident Manager</p>
";

/*
| ---------------------------------------------------------
|					SMS Contents
| ---------------------------------------------------------
| Define the SMS message here
| Use templates to populate the Message.
| {name} - Replaced with User name
| {link} - Replaced with Link to Incident Report Page
*/

$config['new_incident_sms']  = '
Hi {name}.
A new Incident has occured that may require your attention.
See {link} for more info.
';