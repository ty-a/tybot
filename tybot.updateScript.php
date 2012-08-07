<?php

	require_once("./tybot.class.php");
	
	$tybot = new tybot();

	$user = ""; //user
	$pass = ""; //pass
	$wiki = "/api.php"; //Link to api.php

	$throttle = 0;
	$useragent = "TyBot Script Updater running as $user";
	
	$cookiefile = tempnam("/tmp", "CURLCOOKIE"); 
	$curloptions = array(
		CURLOPT_COOKIEFILE => $cookiefile,
		CURLOPT_COOKIEJAR => $cookiefile,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => $useragent,
		CURLOPT_POST => true
	);
	
	/* Parameters */
	$shortopts = 'f:';
	$shortopts .= 'e:';
	
	$options = getopt($shortopts);
	
	if(!isset($options['f'])) {
		die("Did not specify required -f parameter.\n");
	}
	
	$login = $tybot->login($user,$pass);
	
	if($login === false) {
		die("Login failed.\n");
	}
	
	$data = file_get_contents($options['f']);
	
	$token['edit'] = $tybot->get_edit_token();
	
	if(!isset($options['e'])) {
		$tybot->edit($options['f'], "<pre>\n" . $data, "Automated updating of script");
	} else {
		$tybot->edit($options['f'], "<source lang=" . $options['e'] . ">\n" . $data, "Automated updating of script");
	}