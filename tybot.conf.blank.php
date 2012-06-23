<?php

	require_once("./tybot.class.php"); // Imports the tybot class
	
	$tybot = new tybot(); // Makes the tybot object

	$user = ""; // Bot's user account
	$pass = ""; // Bot's password
	$wiki = "; // The link to the wiki's api.php
	$operator_email = ""; // Your email address used in the useragent
	
	$version = "1.1.0"; // Version number
	$throttle = 2; // in seconds
	$useragent = "TyBot/" . $version . " " . $operator_email;
	
	$cookiefile = tempnam("/tmp", "CURLCOOKIE"); 
	$curloptions = array(
		CURLOPT_COOKIEFILE => $cookiefile,
		CURLOPT_COOKIEJAR => $cookiefile,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => $useragent,
		CURLOPT_POST => true
	);
	
	/**
	*
	* In this section you write the code to use the functions provided to make the bot function
	*
	*/