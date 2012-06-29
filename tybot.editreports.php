<?php

	require_once("./tybot.class.php");
	
	$tybot = new tybot();

	$user = "TyBot";
	$pass = "";
	$wiki = "";
	$operator_email = "";
	
	$version = "1.5.0";
	$throttle = 0;
	$useragent = "TyBot/" . $version . " " . $operator_email;
	
	$cookiefile = tempnam("/tmp", "CURLCOOKIE"); 
	$curloptions = array(
		CURLOPT_COOKIEFILE => $cookiefile,
		CURLOPT_COOKIEJAR => $cookiefile,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => $useragent,
		CURLOPT_POST => true
	);
	
	/* BEGIN DIS STUFF */

	$namespace_edits = array(
		0 => 0, // main
		1 => 0, // talk
		2 => 0, // user
		3 => 0, // user talk
		4 => 0, // project 
		5 => 0, // project talk
		6 => 0, // file
		7 => 0, // file talk
		8 => 0, // MediaWiki
		9 => 0, // MediaWiki talk
		10 => 0, // Template
		11 => 0, // Template talk
		12 => 0, // Help
		13 => 0, //Help talk
		14 => 0, // Category
		15 => 0, // Category talk
		100 => 0, // Update
		101 => 0, // Update talk
		110 => 0, // Forum
		111 => 0, // Forum talk
		112 => 0, // Exchange
		113 => 0, // Exchange talk
		114 => 0, // Charm
		115 => 0, // Charm talk
		116 => 0, // Calculator 
		117 => 0, // Calculator talk
		118 => 0, // Map
		119 => 0, // Map talk
		120 => 0,  // Beta
		121 => 0,  // Beta talk
		1100 => 0, // RelatedVideos
		1200 => 0, // Message Wall
		1201 => 0, // Thread
		1202 => 0 // Message Wall Greeting
	);
	
	$minor_edits = 0;
	$new_pages = 0;
	$no_summary = 0;
	$top = 0;
	$target = $tybot->get_page_content("User:TyBot/requests");
	
	$last_report = $tybot->get_user_contribs("TyBot",2,1,false);
	
	if(strpos($last_report[0]["title"], $target) !== false ) {
		die("Their report was last\n");
	}
	
	$result = $tybot->login($user,$pass);
	
	if ($result = false) {
		die("Login failed");
	}
	
	$token["edit"] = $tybot->get_edit_token();
	
	print "Getting user contribs\n";
	$contribs = $tybot->get_user_contribs($target);
	
	print "Analyzing contribs\n";
	foreach($contribs as $y) {
		if (isset($y["minor"])) {
			$minor_edits += 1;
		}
		
		if (isset($y["new"])) {
			$new_pages += 1;
		}
		
		if($y["comment"] = '') {
			$no_summary += 1;
		}
		
		if(isset($y["top"])) {
			$top += 1;
		}
		
		$namespace_edits[$y["ns"]] += 1;
	}
	
	print "Getting log events\n";
	$logevents = $tybot->get_log_events($target);
	$logtypes = array(
		'upload' => 0,
		'delete' => 0,
		'block' => 0,
		'patrol' => 0,
		'rights' => 0,
		'move' => 0,
		'newusers' => 0,
		'useravatar' => 0,
		'chatban' => 0,
		'protect' => 0,
		'abusefilter' => 0,
		'import' => 0,
		'merge' => 0,
		'wikialabs' => 0,
		'wikifeatures' => 0,
		'pr_rep_log' => 0
	);
	
	print "Processing log events\n";
	foreach($logevents as $y) {
		$logtypes[$y["type"]] += 1;
	}
	
	$editcount = count($contribs);
	$logcount = count($logevents);
	print "Edit count: " . $editcount . "\n";
	print "Minor count: " . $minor_edits . "\n";
	print "New pages: " . $new_pages . "\n";
	print "Top: " . $top . "\n";
	
	$max = array_keys($logtypes, max($logtypes));
	
	$max = $max[0];
	
	/* Make the log events pie chart */
	$counter = 2;
$pie = "
{{Pie
|size=250
|legend = yes
|tot= $logcount
|$logtypes[$max]
|l1 = $max: $logtypes[$max]";

unset($logtypes[$max]);

foreach($logtypes as $y => $value) {
	$pie .= "
|$logtypes[$y]
|l" . $counter . " = $y: $value";

	$counter += 1;

}

$pie .= "
}}";
/* End log events pie chart */
	
	$content = 
"
== [[User:$target|$target]]'s Edit Report ==

=== Edits by namespace === 
{{Pie
|size=250
|legend=yes
|tot=$editcount
|$namespace_edits[0]
|$namespace_edits[1]
|$namespace_edits[2]
|$namespace_edits[3]
|$namespace_edits[4]
|$namespace_edits[5]
|$namespace_edits[6]
|$namespace_edits[7]
|$namespace_edits[8]
|$namespace_edits[9]
|$namespace_edits[10]
|$namespace_edits[11]
|$namespace_edits[12]
|$namespace_edits[13]
|$namespace_edits[14]
|$namespace_edits[15]
|$namespace_edits[100]
|$namespace_edits[101]
|$namespace_edits[110]
|$namespace_edits[111]
|$namespace_edits[112]
|$namespace_edits[113]
|$namespace_edits[114]
|$namespace_edits[115]
|$namespace_edits[116]
|$namespace_edits[117]
|$namespace_edits[118]
|$namespace_edits[119]
|$namespace_edits[120]
|$namespace_edits[121]
|l1=Main: $namespace_edits[0]
|l2=Talk: $namespace_edits[1]
|l3=User: $namespace_edits[2]
|l4=User talk: $namespace_edits[3]
|l5=Project: $namespace_edits[4]
|l6=Project talk: $namespace_edits[5]
|l7=File: $namespace_edits[6]
|l8=File talk: $namespace_edits[7]
|l9=MediaWiki: $namespace_edits[8]
|l10=MediaWiki talk: $namespace_edits[9]
|l11=Template: $namespace_edits[10]
|l12=Template talk: $namespace_edits[11]
|l13=Help: $namespace_edits[12]
|l14=Help talk: $namespace_edits[13]
|l15=Category: $namespace_edits[14]
|l16=Category talk: $namespace_edits[15]
|l17=Update: $namespace_edits[100]
|l18=Update talk: $namespace_edits[101]
|l19=Forum: $namespace_edits[110]
|l20=Forum talk: $namespace_edits[111]
|l21=Exchange: $namespace_edits[112]
|l22=Exchange talk: $namespace_edits[113]
|l23=Charm: $namespace_edits[114]
|l24=Charm talk: $namespace_edits[115]
|l25=Calculator: $namespace_edits[116]
|l26=Calculator talk: $namespace_edits[117]
|l27=Map: $namespace_edits[118]
|l28=Map talk: $namespace_edits[119]
|l29=Beta: $namespace_edits[120]
|l30=Beta talk: $namespace_edits[121]
}}

=== Log events ===
$pie

=== Stats ===
* Edit count: $editcount
* Minor edits: $minor_edits
* Pages created: $new_pages
* Pages with current revision: $top
* Total log entries: $logcount
* Most used log type: $max

";
	
$tybot->edit("User:TyBot/editreports/$target", $content, "Creating edit report for $target");
