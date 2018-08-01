# Incident Response Project


**Created by Ryan Sandoval and Supervised by Dr. Fehmi Jaafar. 2018**

## Table of Contents

- [Description](#description)
- [Framework and Libraries](#frameworks-and-libraries)
- [Setup](#setup)

Description
===========
This app is used to log actions to assist with the investigation, in the event that a security breech occurs.


Frameworks and Libraries
========================

This application was created with the following frameworks:
1. Code igniter -> PHP Framework
2. Bulma -> CSS Framework
3. Composer -> Gets nice PHP Lib
If you plan to modify/work on this project, please consider reading the documentation for the above frameworks.

This application usses the following libraries:
**JavaScript**
1. **JQuery**
2. **Moment.js** - JavaScript Date and Time

**PHP**
1. **Carbon** - Date and Time Library
	- __Note:__ This library can only handle dates up to 2038 if PHP and the server is 32-bit.

Setup
=====
Before deploying, go to index.php (in root folder) and change the environment variable to production

A copy of the database has been provided in the setup folder. Import this into a database.
Edit the configuration files in:
1. application/config/config.php --> Code Igniter configuration. Read up on the code igniter documentation to learn more.
	- The following are the config values that you should take a look at.
		1. log_threshold -> Verbosity of logs at application/logs
		2. base_url -> Change to your website.
2. application/config/appconfig.php --> Configuration for application
3. application/config/database.php --> Database credentials and config


If there are no users in the database, aquire the SETUP.php in the setup folder. Open it and edit the fields to match your desired admin credentials. Run the setup in your borwser by going to:

> https://yourwebsiteurl.com/Setup

The setup file should automatically insert the admin account into the 'users' table in the database.

Password Recovery Mail
======================

You must set up SMTP on your server or use other servers like google.
1. Ensure that openssl is allowed.
   - In XAMPP, this means allowing extension=openssl
2. Configure config/email
