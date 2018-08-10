# Incident Response Project


**Created by Ryan Sandoval and Supervised by Dr. Fehmi Jaafar. 2018**

## Table of Contents

- [Description](#description)
- [Framework and Libraries](#frameworks-and-libraries)
- [Setup](#setup)

Description
===========
This app is used to log actions to assist with the investigation, in the event that a security breech occurs.


Dependencies, Frameworks and Libraries
======================================

This application was created with the following frameworks:
1. Code igniter -> PHP Framework
2. Bulma -> CSS Framework
3. Composer -> Gets nice PHP Lib
If you plan to modify/work on this project, please consider reading the documentation for the above frameworks.

This application uses the following libraries:
**JavaScript**
1. **JQuery**
2. **Moment.js** - JavaScript Date and Time
3. **Select2** - Javascript Selection Input Library

**PHP**
1. **Carbon** - Date and Time Library
	- __Note:__ This library can only handle dates up to 2038 if PHP and the server is 32-bit.

**CSS**
Although you may make changes to the css file directly, we do not recommend doing so.

To increase readability and maintainability, this app uses SASS. To change the styling of the app:
1. Download and install SASS from (this page)[https://sass-lang.com/].
2. Make Edits at 'assets/css/sass'.
3. Use SASS to compile the scss files to their respective css counterparts.
Please note that we use the scss syntax and not the sass syntax.

Setup
=====
Before deploying, go to index.php (in root folder) and change the environment variable to `production` or if you are developing this app, change it to `development`.
Example: `define('ENVIRONMENT', 'production');`

A copy of the database has been provided in the setup folder. Import this into a database.
Edit the configuration files in:
1. application/config/config.php --> Code Igniter configuration. Read up on the code igniter documentation to learn more.
	- The following are the config values that you should take a look at.
		1. log_threshold -> Verbosity of logs at application/logs
		2. base_url -> Change to your website.
		3. date_default_timezone_set -> Change to your timezone.
1. `application/config/database.php` --> Database credentials and config
1. `application/config/email.php` --> Configuration for Emails.
1. `application/config/appconfig.php` --> Configuration for application

**Note: If the production environment is 'DEVELOPMENT' then edit the configs in the development folder.**


If there are no users in the database, acquire the SETUP.php in the setup folder. Open it in a text editor and edit the fields to match your desired admin credentials. Run the setup in your browser by going to:

> https://yourwebsiteurl.com/Setup

The setup file should automatically insert the admin account into the 'users' table in the database.

Go to the dashboard of the site and login with the credentials that you entered. For security reasons, delete `Setup.php` when done.

Password Recovery Mail
======================

You must set up SMTP on your server or use other servers like google.
1. Ensure that openssl is allowed.
   - In XAMPP, this means allowing extension=openssl
2. Configure `config/email`
