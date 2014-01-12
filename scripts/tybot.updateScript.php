<?php

/*************************************************************************
*    This file is part of TyBot (in PHP).
*
*    TyBot (in PHP) is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    TyBot (in PHP) is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with TyBot (in PHP).  If not, see <http://www.gnu.org/licenses/>.
*/

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
	$shortopts .= 'l:';
	
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
	
	if(isset($options['l'])) {
		$location = $options['l'];
	} else {
		$location = $options['f'];
	}
	
	if(!isset($options['e'])) {
		$tybot->edit($location, "<pre>\n" . $data, "Automated updating of script");
	} else {
		$tybot->edit($location, "<source lang=" . $options['e'] . ">\n" . $data, "Automated updating of script");
	}
