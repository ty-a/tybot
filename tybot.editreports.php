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
	
	$namespace_names = array(
		0 => 'Mainspace', // main
		1 => 'Talk', // talk
		2 => 'User', // user
		3 => 'User talk', // user talk
		4 => 'Project', // project 
		5 => 'Project talk', // project talk
		6 => 'File', // file
		7 => 'File talk', // file talk
		8 => 'MediaWiki', // MediaWiki
		9 => 'MediaWiki talk', // MediaWiki talk
		10 => 'Template', // Template
		11 => 'Template talk', // Template talk
		12 => 'Help', // Help
		13 => 'Help talk', //Help talk
		14 => 'Category', // Category
		15 => 'Category talk', // Category talk
		100 => 'Update', // Update
		101 => 'Update talk', // Update talk
		110 => 'Forum', // Forum
		111 => 'Forum talk', // Forum talk
		112 => 'Exchange', // Exchange
		113 => 'Exchange talk', // Exchange talk
		114 => 'Charm', // Charm
		115 => 'Charm talk' , // Charm talk
		116 => 'Calculator ', // Calculator 
		117 => 'Calculator talk', // Calculator talk
		118 => 'Map', // Map
		119 => 'Map talk', // Map talk
		120 => 'Beta',  // Beta
		121 => 'Beta talk',  // Beta talk
		1100 => 'RelatedVideos', // RelatedVideos
		1200 => 'Message Wall', // Message Wall
		1201 => 'Thread', // Thread
		1202 => 'Message Wall Greeting' // Message Wall Greeting
	);
	
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
	);
	
	$months = array(
		"01" => 'January',
		"02" => 'Febuary',
		"03" => 'March',
		"04" => 'April',
		"05" => 'May',
		"06" => 'June',
		"07" => 'July',
		"08" => 'August',
		"09" => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December'
	);
	
	$titles = array();
	$summaries = array();
	$minor_edits = 0;
	$new_pages = 0;
	$no_summary = 0;
	$top = 0;
	#$target = $tybot->get_page_content("User:TyBot/requests");
	$target = $argv[1];
	$target = ucfirst($target);
	
	$last_report = $tybot->get_user_contribs("TyBot",2,1,false);
	
	if(strpos($last_report[0]["title"], $target) !== false ) {
		#die("Their report was last\n");
	} 
	
	$result = $tybot->login($user,$pass);
	
	if ($result = false) {
		die("Login failed");
	}
	
	$token["edit"] = $tybot->get_edit_token();
	
	#print "Getting user contribs\n";
	$contribs = $tybot->get_user_contribs($target);
	
	#print "Analyzing contribs\n";
	foreach($contribs as $y) {
		if (isset($y["minor"])) {
			$minor_edits += 1;
		}
		
		if(empty($month[substr($y['timestamp'], 5, 2)])) {
			$month[substr($y['timestamp'], 5, 2)] = 1;
		} else {
			$month[substr($y['timestamp'], 5, 2)] += 1;
		}
		
		if(empty($day[substr($y['timestamp'], 8, 2)])) {
			$day[substr($y['timestamp'], 8, 2)] = 1;
		} else {
			$day[substr($y['timestamp'], 8, 2)] += 1;
		}
		
		if(empty($year[substr($y['timestamp'], 0, 2)])) {
			$year[substr($y['timestamp'], 0, 2)] = 1;
		} else {
			$year[substr($y['timestamp'], 0, 2)] += 1;
		}
		
		if (isset($y["new"])) {
			$new_pages += 1;
		}
		$length = mb_strlen($y["comment"]);
		if($length = 0) {
			$no_summary += 1;
		} else {
			if(!isset($summaries[$y["comment"]])) {
				$summaries[$y["comment"]] = 0;
			}
			
			$summaries[$y["comment"]] += 1;
		}
		
		if(isset($y["top"])) {
			$top += 1;
		}
		
		if(!isset($titles[$y["title"]])) {
			$titles[$y["title"]] = 0;
		}
		
		$titles[$y["title"]] += 1;
		$namespace_edits[$y["ns"]] += 1;
	}
	
	#print "Getting log events\n";
	$logevents = $tybot->get_log_events($target);
	
	#print "Processing log events\n";
	foreach($logevents as $y) {
		if(!isset($logtypes[$y["type"]])) {
			$logtypes[$y["type"]] = 0;
		}
		
		$logtypes[$y["type"]] += 1;
	}
	
	$editcount = count($contribs);
	$logcount = count($logevents);
	
	if (($contribs == false) && ($logevents == false)) {
		die("foo\n");
	}
	#print "Edit count: " . $editcount . "\n";
	#print "Minor count: " . $minor_edits . "\n";
	#print "New pages: " . $new_pages . "\n";
	#print "Top: " . $top . "\n";
	
	#print "Making pie charts\n";
	
	/* Get top 5 pages */
	for($counter=0;$counter<5;$counter+=1) {
		$max = array_keys($titles, max($titles));
		$most_edited[$counter] = $max[0];
		$times_edited[$counter] = $titles[$most_edited[$counter]];
		unset($titles[$most_edited[$counter]]);
	}
	/* END Get top 5 pages*/
	
	/* Get top 5 summaries */
	for($counter=0;$counter<5;$counter+=1) {
		$max = array_keys($summaries, max($summaries));
		$summary[$counter] = $max[0];
		$times_used[$counter] = $summaries[$summary[$counter]];
		unset($summaries[$summary[$counter]]);
	}
	/* END Get top 5 summaries */
	
	$namespace_pie = '';
	$pie = '';
	if(!$contribs == false) {
		/* Make edit by day/month/year pie chart */
		$max = array_keys($month, max($month));
		$max = $max[0];
		
		$counter = 2;
		$month_pie = "
=== Edits by month ===
{{Pie
|size = 250
|legend = yes
|tot = $editcount
|$month[$max]
|l1 = $months[$max]: $month[$max]";
		
		unset($month[$max]);
		
		foreach($month as $y => $value) {
			if($month[$y] === 0) {
				unset($month[$y]); 
			} else {
				$month_pie .= "
|$value
|l" . $counter . " = $months[$y]: $value";				
				$counter += 1;
			}
		}
		$month_pie .= "}}";
		/* END MONTH PIE */
		
		/* START DAY TABLE */
		
		$day_table = '
=== Edits by day ===
{|class="wikitable"
!Day
!Amount of edits';

		foreach($day as $y => $value) {
			$day_table .= "
|-
|$y
|$value";
		}
		
		$day_table .= "
|}";
		
		/* Make edit by namespace pie chart */
		$max = array_keys($namespace_edits, max($namespace_edits));
		$max = $max[0];
	
		$counter = 2;
		$namespace_pie = "
=== Edits by namespace === 

{{Pie
|size = 250
|legend = yes
|tot = $editcount	
|$namespace_edits[$max]
|l1 = $namespace_names[$max]: $namespace_edits[$max]";

		unset($namespace_edits[$max]);

		foreach($namespace_edits as $y => $value) {
			if($namespace_edits[$y] === 0) {
				unset($namespace_edits[$y]);
			} else {
				$namespace_pie.= "
|$namespace_edits[$y]
|l" . $counter . " = $namespace_names[$y]: $value";
		$counter += 1;
			}
		}
		$namespace_pie .= "}}";

		/* End namespace pie chart */
	}
	
	if(!$logevents == false) {
	
		$max = array_keys($logtypes, max($logtypes));
	
		$max = $max[0];
	
		/* Make the log events pie chart */
		$counter = 2;
		$pie = "
=== Log events ===

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
}
	$content = 
"
== [[User:$target|$target]]'s Edit Report ==
:''Last updated at ~~~~~''

$namespace_pie

$pie

$month_pie

$day_table

=== Stats ===
* Edit count: $editcount
* Minor edits: $minor_edits
* Pages created: $new_pages
* Pages with current revision: $top
* Total log entries: $logcount
* Most used log type: $max

=== Top 5 edited pages ===
# [[$most_edited[0]]] at $times_edited[0] edits
# [[$most_edited[1]]] at $times_edited[1] edits
# [[$most_edited[2]]] at $times_edited[2] edits
# [[$most_edited[3]]] at $times_edited[3] edits
# [[$most_edited[4]]] at $times_edited[4] edits

=== Top 5 edit summaries ===
# <nowiki>'</nowiki>''<nowiki>$summary[0]</nowiki>''<nowiki>'</nowiki> was used $times_used[0] times.
# <nowiki>'</nowiki>''<nowiki>$summary[1]</nowiki>''<nowiki>'</nowiki> was used $times_used[1] times.
# <nowiki>'</nowiki>''<nowiki>$summary[2]</nowiki>''<nowiki>'</nowiki> was used $times_used[2] times.
# <nowiki>'</nowiki>''<nowiki>$summary[3]</nowiki>''<nowiki>'</nowiki> was used $times_used[3] times.
# <nowiki>'</nowiki>''<nowiki>$summary[4]</nowiki>''<nowiki>'</nowiki> was used $times_used[4] times.

";
$tybot->edit("User:TyBot/editreports/$target", $content, "Creating edit report for $target");