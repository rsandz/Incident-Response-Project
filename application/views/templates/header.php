<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	
	<!-- Style Sheets -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.css"/>
	<?php echo css_tag('css/custom-styles.css')?>


	<!--- JQUERY -->
	<script
	  src="https://code.jquery.com/jquery-3.3.1.min.js"
	  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
	  crossorigin="anonymous"></script>

	<script id='site-link' data="<?php echo site_url()?>"></script>	
	<script id='ajax-link' data="<?php echo site_url('Ajax')?>"></script>	

	<!--[if lt IE 9]>
  	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<![endif]-->

	<title><?php echo $title ?: 'Incident Response Project'; ?></title>


</head>
<body>
