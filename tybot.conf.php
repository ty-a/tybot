<?php

	require_once("./tybot.class.php");
	
	$tybot = new tybot();

	$user = "";
	$pass = "";
	$wiki = "";
	
	$operator_email = "";
	
	$version = "1.0.0";
	$throttle = 2; //seconds 
	$useragent = "TyBot/" . $version . " " . $operator_email;
	
	$cookiefile = tempnam("/tmp", "CURLCOOKIE"); 
	$curloptions = array(
		CURLOPT_COOKIEFILE => $cookiefile,
		CURLOPT_COOKIEJAR => $cookiefile,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => $useragent,
		CURLOPT_POST => true
	);
