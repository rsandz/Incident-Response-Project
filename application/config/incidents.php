<?php

/*
| ---------------------------------------------------------
|  					Incidents Configuration
| ---------------------------------------------------------
|
| This contains the configuration for the incidents
| library.
|
*/

/*
| ---------------------------------------------------------
|					Email Contents
| ---------------------------------------------------------
|
| Stores the messages of the following emails:
|  - New Incident Notification
|  - Investigation Complete
*/

$config['new_incident_body'] = "
	<p>Hello {name}, </p>

	<p>
		An Incident has occured that may require your attention.
	</p>
	<p>
		The following is a short summary of the incident that has occured:
	<p>
	<hr>
		{summary}
	<hr>
	<p>
		For more Information, please visit this link: {link}
	</p>
	
	<p></p>
	<p>Kind Regards, <br>
	Incident Manager</p>
";

