<?php

	require_once("./tybot.class.php");
	
	$tybot = new tybot();

	$user = "user";
	$pass = "pass";
	$wiki = "http://wiki/api.php";
	$operator_email = "email";
	
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

	$namespace_edits = array();
	
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
		"02" => 'February',
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
	$reverts = 0;
	#$target = $tybot->get_page_content("User:$user/requests"); //based on request page
	$target = $argv[1]; //commandline
	$target = ucfirst($target);
	
	
	/* Commenting out because unused, but has potiential to be used.
	$last_report = $tybot->get_user_contribs("TyBot",2,1,false);
	
	if(strpos($last_report[0]["title"], $target) !== false ) {
		die("Their report was last\n");
	} */
	
	$result = $tybot->login($user,$pass);
	
	if ($result = false) {
		die("Login failed");
	}
	
	$token["edit"] = $tybot->get_edit_token();
	
	# get usergroups
	$usergroups = $tybot->get_usergroups($target);

	if(is_array($usergroups)) {
		$groups = '';
		foreach($usergroups as $y) {
			$groups .= $y . ", ";
		}
	} else {
		$groups = "None";
	}
	
	$groups = substr($groups, 0, -2);
	
	#get namespaces
	$namespaces = $tybot->get_namespaces();
	
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
				$summaries[$y["comment"]] = 1;
			} else {
				$summaries[$y["comment"]] += 1;
			}
		}
		
		if(isset($y["top"])) {
			$top += 1;
		}
		
		if(!isset($titles[$y["title"]])) {
			$titles[$y["title"]] = 1;
		} else {
			$titles[$y["title"]] += 1;
		}		
		
		if(isset($namespace_edits[$y["ns"]])) {
			$namespace_edits[$y["ns"]] += 1;
		} else {
			$namespace_edits[$y["ns"]] = 1;
		}
		
		if((strpos($y["comment"], "Undid revision ") !== false) || (strpos($y["comment"], "Reverted edits by ") !== false)) {
			$reverts += 1;
		}
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
{|class="wikitable sortable"
!Day
!Number of edits';

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
|l1 = $namespaces[$max]: $namespace_edits[$max]";

		unset($namespace_edits[$max]);

		foreach($namespace_edits as $y => $value) {
			if($namespace_edits[$y] === 0) {
				unset($namespace_edits[$y]);
			} else {
				$namespace_pie.= "
|$namespace_edits[$y]
|l" . $counter . " = $namespaces[$y]: $value";
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
* Usergroups: $groups
* Number of reverts: $reverts

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
$tybot->edit("User:$user/editreports/$target", $content, "Creating edit report for $target");