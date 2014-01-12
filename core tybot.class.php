<?php
/*************************************************************************
* TyBot (in PHP) - A MediaWiki wrapper for use on wikis. 
*    Copyright (C) 2012  TyA <tya.wiki@gmail.com>
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/** 
 * class tybot
 *
 * creates tybot object from which you can use to perform various functions
 *
*/
class tybot {

	/** 
	* function login()
	*
	* Logs into the wiki via the API
	*
	* @param string $user the bot's username
	* @param string $pass the bot's password
	* @return true/false depending if login was successful
	*/
	public function login($user,$pass) {
		global $wiki;
		
		$dataToPost = array(
			"action" => "login",
			"lgname" => $user,
			"lgpass" => $pass,
			"format" => "php",
		);
		
		$result = $this->post_to_wiki($dataToPost,$wiki);
		
		$dataToPost = array(
			"action" => "login",
			"lgname" => $user,
			"lgpassword" => $pass,
			"lgtoken" => $result["login"]["token"],
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost,$wiki);
		
		if ($result["login"]["result"] == "Success") {
			#print "Now logged in.\n";
			return true;
		
		} else {
			#print "Failed!";
			return false;
		}
		
		}
	
	/**
	* function post_to_wiki()
	*
	* POSTs data to the wiki
	*
	* @param array $dataToPost the info to post
	* @return the info from the POST
	*/
	public function post_to_wiki($dataToPost) {
	
		global $curloptions,$wiki,$useragent;

		$ch = curl_init();
		curl_setopt_array($ch, $curloptions);
		curl_setopt($ch, CURLOPT_URL, $wiki);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToPost);
		$result = curl_exec($ch);
		$result = unserialize($result);
		#var_dump($result);
		
		return $result;
	}
	
	/**
	* function get_edit_token()
	*
	* @param none
	* @return the edit token
	*/
	public function get_edit_token() {
	
		$dataToPost = array(
			"action" => "query",
			"prop" => "info|revisions",
			"intoken" => "edit",
			"titles" => "Main Page",
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		foreach ($result["query"]["pages"] as $y) {
			$token["edit"] = $y["edittoken"];
			}
		
		return $token["edit"];
		
		}
	/**
	* function get_delete_token()
	* 
	* Gets the delete token
	*
	* @param none
	* @return the delete token
	*/
	public function get_delete_token() {
	
		$dataToPost = array(
			"action" => "query",
			"prop" => "info",
			"intoken" => "delete",
			"titles" => "Main Page",
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		foreach ($result["query"]["pages"] as $y) {
			$token["delete"] = $y["deletetoken"];
			}
		
		return $token["delete"];
		
		}
		
	/**
	* function get_undelete_token()
	*
	* Gets the undelete token
	*
	* @param none
	* @return the undelete token
	*/
	public function get_undelete_token() {
		$dataToPost = array(
			"action" => "query",
			"list" => "deletedrevs",
			"drprop" => "token",
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
		
			foreach ($result["query"]["deletedrevs"] as $y) {
				$token["undelete"] = $y["token"];
			}
			
			return $token["undelete"]; 
		} else {
			
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
		
	}
	
	/**
	* function get_userrights_token
	*
	* Gets the userrights token
	*
	* @param string $targetUser the user whose rights are being changed
	* @return the userrights token
	*/
	public function get_userrights_token($targetUser) {
		$dataToPost = array(
			"action" => "query",
			"list" => "users",
			"ususers" => $targetUser,
			"ustoken" => "userrights",
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost);
		foreach ($result["query"]["users"] as $y) {
			$token["userrights"] = $y["userrightstoken"];
		}
		
		return $token["userrights"];
		
	}
	
	/**
	* function get_protect_token()
	*
	* Gets the protect token
	*
	* @param none
	* @return the protect token
	*/
	public function get_protect_token() {
		$dataToPost = array(
			"action" => "query",
			"prop" => "info",
			"intoken" => "protect",
			"titles" => "Main Page",
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		foreach ($result["query"]["pages"] as $y) {
			$token["protect"] = $y["protecttoken"];
		}
		
		return $token["protect"];
	
	}
	
	/**
	* function get_block_token()
	*
	* Gets the block token
	*
	* @param none
	* @return the block token
	*/
	public function get_block_token() {
		$dataToPost = array(
			'action' => 'query',
			'prop' => 'info',
			'intoken' => 'block',
			'titles' => 'User:Foo',
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		foreach ($result["query"]["pages"] as $y) {
			$token["block"] = $y["blocktoken"];
		}
		
		return $token["block"];
	}
	
	/**
	* function get_unblock_token()
	*
	* Gets the unblock token
	*
	* @param none
	* @return the unblock token
	*/
	public function get_unblock_token() {
		$dataToPost = array(
			'action' => 'query',
			'prop' => 'info',
			'intoken' => 'unblock',
			'titles' => 'User:Foo',
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		foreach ($result["query"]["pages"] as $y) {
			$token["unblock"] = $y["unblocktoken"];
		}
		
		return $token["unblock"];
	}
	
	/**
	* function get_page_content()
	*
	* Gets the content of the specified page
	*
	* @param string $page the page to get the content of
	* @return the page content
	*/
	public function get_page_content($page) {
	
		$dataToPost = array(
			"action" => "query",
			"format" => "php",
			"prop" => "revisions",
			"rvprop" => "content",
			"titles" => $page
			);
			
		$result = $this->post_to_wiki($dataToPost);
			
		foreach($result['query']['pages'] as $y) {
			if(isset($y['revisions'][0]['*'])) {
				return $y['revisions'][0]['*'];
			} else {
				return "";
			}
		}
	}
	
	/**
	* function edit()
	*
	* Edits pages
	*
	* @param string $page the page to edit
	* @param string $text the content to be saved
	* @param string $summary the edit summary
	* @param int $bot if marked as bot
	* @return boolean based on success
	*/
	public function edit($page,$text,$summary='',$bot=1) {
		global $token,$throttle;
		
		$dataToPost = array(
			"action" => "edit",
			"title" => $page,
			"summary" => $summary,
			"text" => $text,
			"bot" => $bot,
			"token" => $token["edit"],
			"format" => "php"
		);
		
		echo "Sleeping for $throttle seconds.\n";
		sleep($throttle);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
			return true;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}

	}
	
	/**
	* function delete()
	*
	* Deletes pages
	*
	* @param string $page the page to be deleted
	* @param string $summary deletion reason
	* @return boolean based on success
	*/
	public function delete($page,$summary='') {
		global $token;
		
		$dataToPost = array(
			"action" => "delete",
			"title" => $page,
			"reason" => $summary,
			"format" => "php",
			"token" => $token["delete"]
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
			return true;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
		
	}
	
	/**
	* function undelete()
	*
	* Undeletes pages
	*
	* @param string $page the page to be undeleted
	* @param string $summary the restore summary
	* @return boolean based on success
	*/
	public function undelete($page,$summary='') {
		global $token;
		$dataToPost = array(
			'action' => 'undelete',
			'title' => $page,
			'reason' => $summary,
			'format' => 'php',
			'token' => $token["undelete"]
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
			return true;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
		
	}
	
	/**
	* function userrights()
	*
	* Modifies userrights
	*
	* @param string $targetUser user whose rights are being changed
	* @param string $add rights to add
	* @param string $remove rights to remove
	* @param string $summary summary for change
	* @return boolean based on success
	*/
	public function userrights($targetUser,$add='',$remove='',$summary='') {
		
		$userrightstoken = $this->get_userrights_token($targetUser);
		$dataToPost = array(
			'action' => 'userrights',
			'user' => $targetUser,
			'format' => 'php',
			'token' => $userrightstoken,
			'add' => $add,
			'remove' => $remove,
			'reason' => $summary
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
			return true;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
	
	}
	
	/**
	* function protect
	*
	* Changes page's protection levels
	*
	* @param string $page the target page
	* @param string $movelevel the protection level on move
	* @param string $editlevel the protection level on edit
	* @param string $expiry the the protection expires
	* @param string $summary the summary
	* @return boolean based on success
	*/
	public function protect($page,$movelevel='',$editlevel='',$expiry='',$summary='') {
		global $token;
		
		$dataToPost = array(
			'action' => 'protect',
			'title' => $page,
			'protections' => 'edit=' . $editlevel . '|move=' . $movelevel,
			'expiry' => $expiry,
			'reason' => $summary,
			'token' => $token['protect'],
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
		
			return true;

		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
	}
	
	/**
	* function block()
	*
	* Blocks users
	*
	* @param string $target user to block
	* @param string $summary the block reason
	* @param string $expiry when block expires
	* @return boolean based on success
	*/
	public function block($target,$summary='',$expiry="infinite") {
		global $token;
		
		$dataToPost = array(
			'action' => 'block',
			'user' => $target,
			'reason' =>$summary,
			'expiry' => $expiry,
			'token' => $token["block"],
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		if(empty($result["error"])) {
			return true;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
	}
	
	/**
	* function unblock()
	* 
	* Unblocks users
	*
	* @param string $target the user to unblock
	* @param string $summary the unblock reason
	* @return boolean based on success
	*/
	public function unblock($target,$summary='') {
		global $token;
		
		$dataToPost = array(
			'action' => 'unblock',
			'user' => $target,
			'reason' => $summary,
			'token' => $token["unblock"],
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
			return true;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
	}
	
	/** 
	* function get_category_members
	*
	* Gets the titles of all the pages in a category 
	*
	* @param string $category the category to get the pages from
	* @param $limit amount of pages to return (default: "max")
	* @return array of page titles (false on error)
	*/
	public function get_category_members($category,$limit="max") {
		$cmcontinue = '';
		while(true) {
			if ($cmcontinue === '') {
				$dataToPost = array(
					'action' => 'query',
					'list' => 'categorymembers',
					'cmtitle' => $category,
					'cmlimit' => $limit,
					'cmprop' => 'title',
					'format' => 'php'
				);
			} else {
				$dataToPost = array(
					'action' => 'query',
					'list' => 'categorymembers',
					'cmtitle' => $category,
					'cmlimit' => $limit,
					'cmprop' => 'title',
					'cmcontinue' => $cmcontinue,
					'format' => 'php'
				);
			}
		
			$result = $this->post_to_wiki($dataToPost);
			#var_dump($result);
	
			if(!empty($result["error"])) {
				print "ERROR: " . $result["error"]["code"] . "\n";
				return false;
			}
			
			foreach($result["query"]["categorymembers"] as $y) {
				$pages[] = $y["title"];
			}
		
			if(empty($result["query-continue"])) {
				return $pages;
			} else {
				$cmcontinue = $result["query-continue"]["categorymembers"]["cmcontinue"];
			}
		}
	}
	
	/**
	* function find_and_replace()
	*
	* Finds and replaces content in pages
	*
	* @param string $page the page to check on
	* @param string $find what is being changed
	* @param string $replace what is being added
	* @return boolean based on success
	*/
	public function find_and_replace($page,$find,$replace) {
		$content = $this->get_page_content($page);
		$content = str_replace($find,$replace,$content);
		
		$result = $this->edit($page,$content,"Replacing " . $find . " with " . $replace ,"1");
		
		if ($result = false) {
			print "Error editing page";
			return false;
		} else {
			return true;
		}
		
	}
	
	/**
	* function get_users_in_group()
	*
	* Returns all the users in a specified usergroup
	*
	* @param string $group The usergroup to get members of
	* @param int $amount The amount of results
	* @return array The users in the group (false on error)
	*/
	public function get_users_in_group($group,$amount="max") {
		$dataToPost = array(
			'action' => 'query',
			'list' => 'allusers',
			'augroup' => $group,
			'aulimit' => $amount,
			'format' => 'php',
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {
		
			foreach($result["query"]["allusers"] as $y) {
				$users[] = $y["name"];
			}
		
			return $users;
			
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
			
		}
	}
	
	/**
	* function get_random_pages()
	*
	* @param string $limit how many pages to return (Default: 10)
	* @param string $namespace numerical ID of namespace to get pages from (Default: 0)
	* @return array of pages (print warnings)
	*/
	public function get_random_pages($limit = 10,$namespace = 0) {
	
		$dataToPost = array(
			'action' => 'query',
			'list' => 'random',
			'rnlimit' => $limit,
			'rnnamespace' => $namespace,
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		if(empty($result["warnings"])) {
		
			foreach($result["query"]["random"] as $y) {
				$pages[] = $y["title"];
			}
			
		} else {
			
			print $result["warnings"]["random"];
			foreach($result["query"]["random"] as $y) {
				$pages[] = $y["title"];
			}
		}
		
		return $pages;
	}
	
	/**
	* function query_page()
	*
	* Function to get info from from the querypage list
	*
	* @param string $type The type of info to return 
	* Values: Ancientpages, BrokenRedirects, Deadendpages, 
	*     Disambiguations, DoubleRedirects, Listredirects, 
	*     Lonelypages, Longpages, Mostcategories, Mostimages, 
	*     Mostlinkedcategories, Mostlinkedtemplates, Mostlinked, 
	*     Mostrevisions, Fewestrevisions, Shortpages, 
	*     Uncategorizedcategories, Uncategorizedpages, 
	*     Uncategorizedimages, Uncategorizedtemplates, 
	*     Unusedcategories, Unusedimages, Wantedcategories, 
	*     Wantedfiles, Wantedpages, Wantedtemplates, 
	*     Unwatchedpages, Unusedtemplates, Withoutinterwiki
	* @param $limit How many results to return (Default: "max")
	* @return array result (false if fails)
	*/
	public function query_page($type,$limit="max") {

		$dataToPost = array(
			'action' => 'query',
			'list' => 'querypage',
			'qppage' => $type,
			'qplimit' => $limit,
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(empty($result["error"])) {

			return $result;
		} else {
			print "ERROR: " . $result["error"]["code"] . "\n";
			return false;
		}
		
	}
	
	/**
	* function fix_double_redirects()
	*
	* Fixes double redirects
	*
	* @param none
	* @return none
	*/
	public function fix_double_redirects() { #@TODO Make it keep categories
		$result = $this->query_page("DoubleRedirects");
		
		foreach($result["query"]["querypage"]["results"] as $y) {
			#$content = "#REDIRECT [[" . $y["databaseResult"]["tc"] . "]]"; // Only compatible with 1.20
			$content = $this->get_page_content($y["title"]); 
			print $content . "\n";
			
			
			$start = strpos($content, "[[") + 2; 
			$end = strpos($content, "]]"); 
			$length = $end - $start; 
			$extra_info = substr($content, $end + 2);
			
			$OriginalTarget = substr($content, $start, $length); 
			print "Page of 2nd redirect: " . $OriginalTarget . "\n";

			$content = $this->get_page_content($OriginalTarget); 
			
			$start = strpos($content, "[[") + 2; 
			$end = strpos($content, "]]"); 
			$length = $end - $start; 
			
			if (stripos($content, "#REDIRECT") !== FALSE) {
				$target = substr($content, $start, $length); 
				
				print "Final target: " . $target . "\n";
			
				$content = "#REDIRECT [[" . $target . "]]";			
				print "Redirect not fixed \n";
				$this->edit($y["title"], $content . $extra_info, "Fixing double redirect");
			} else {
				print "Redirect already fixed\n";
			}
		}
	}
	
	/**
	* function get_all_pages()
	*
	* Returns all the pages
	*
	* @param string $redirects show redirects, only redirects, or no redirects (default no redirects)
	*     legal values: all, redirects, nonredirects
	* @param $namespace the numerical namespace number (default 0)
	* @param $limit number of results per query. (default max)
	* @return array of all pages (false on failure)
	*/
	public function get_all_pages($redirects = "nonredirects",$namespace = 0,$limit="max") {
	
		$apfrom = '';
		$pages = array();
	
		while (true) {
	
			$dataToPost = array(
				'action' => 'query',
				'list' => 'allpages',
				'apfrom' => $apfrom,
				'apfilterredir' => $redirects,
				'apnamespace' => $namespace,
				'aplimit' => $limit,
				'format' => 'php'
			);
		
			$result = $this->post_to_wiki($dataToPost);
		
			if(!empty($result["error"])) {
				return false;
			
			}
		
			foreach($result["query"]["allpages"] as $y) {
				$pages[] = $y["title"];
			}
		
			if(empty($result["query-continue"]["allpages"])) {
				return $pages;
			} else {
				$apfrom = $result["query-continue"]["allpages"]["apfrom"];
			}
		}
	}
	
	/**
	* function get_what_links_here()
	*
	* @param $page The page to get what links to
	* @param $limit # of pages to return (default "max")
	* @param $redirects Whether or not to show redirects 
	*     Values: all (default), redirects, nonredirects
	* @return array of pages (false on failure)
	*/
	public function get_what_links_here($page,$limit="max",$redirects="all") {
		$blcontinue = '';
		while(true) {
			if ($blcontinue === '') {
				$dataToPost = array(
					'action' => 'query',
					'list' => 'backlinks',
					'bltitle' => $page,
					'bllimit' => $limit,
					'blfilterredir' => $redirects,
					'format' => 'php'
				);
				
			} else {
				$dataToPost = array(
					'action' => 'query',
					'list' => 'backlinks',
					'bltitle' => $page,
					'bllimit' => $limit,
					'blcontinue' => $blcontinue,
					'blfilterredir' => $redirects,
					'format' => 'php'
				);
			}
		
			$result = $this->post_to_wiki($dataToPost);
			
			if(!empty($result["error"])) {
				print "ERROR: " . $result["error"]["code"] . "\n";
				return false;
			}
			
			foreach($result["query"]["backlinks"] as $y) {
				$pages[] = $y["title"];
			}
			
			if(empty($result["query-continue"])) {
				return $pages;
			} else {
				$blcontinue = $result["query-continue"]["backlinks"]["blcontinue"];
			}
		}
	}
	
	/**
	* function get_user_contribs()
	*
	* return an array with all the user's contribs
	* @param $user the user to get params for
	* @return array with contribs or false on error
	*/
	public function get_user_contribs($user,$namespace='',$limit='max',$all=true) {
		$ucstart = '';
		$contribs = array();
		while(true) {
			
			$dataToPost = array(
				'action' => 'query',
				'list' => 'usercontribs',
				'ucstart' => $ucstart,
				'ucuser' => $user,
				'uclimit' => $limit,
				'ucprop' => 'title|timestamp|comment|flags',
				'ucdir' => 'older',
				'ucnamespace' => $namespace,
				'format' => 'php'
			);
					
			$result = $this->post_to_wiki($dataToPost);
			#var_dump($result);
			
			if(!empty($result["error"])) {
				print "ERROR: " . $result["error"]["code"] . "\n";
				return false;
			}
			
			foreach($result["query"]["usercontribs"] as $y) {
					$contribs[] = $y;
			}
			if($all === true) {
				if(!empty($result["query-continue"])) {
					$ucstart = $result["query-continue"]["usercontribs"]["ucstart"];
				} else {
					return $contribs;
				}
			} else {
				return $contribs;
			}
		}
	}
	
	/** 
	* function get_log_events
	*
	* @param $user whose logs to get
	* @return array with contribs or false on error
	*/
	public function get_log_events($user) {
		$lestart = '';
		while(true) {
			$dataToPost = array(
				'action' => 'query',
				'list' => 'logevents',
				'leuser' => $user,
				'lelimit' => 'max',
				'ucprop' => 'title|timestamp|comment|flags',
				'lestart' => $lestart,
				'format' => 'php'
			);
			
			$result = $this->post_to_wiki($dataToPost);
			#var_dump($result);
			
			if(!empty($result["error"])) {
				print "ERROR: " . $result["error"]["code"] . "\n";
				return false;
			}
			
			foreach($result["query"]["logevents"] as $y) {
				$logevents[] = $y;
			}
			
			if(!empty($result["query-continue"])) {
				$lestart = $result["query-continue"]["logevents"]["lestart"];
			} else {
				return $logevents;
			}
		}
	}
	
	public function get_all_wikia_wikis() {

		if(!isset($from)) {
			$from = 0;
			$to = 1000;
		}
		
		$fh = fopen("wikis.txt", 'w+');
		while(true) {
			$dataToPost = array(
				'action' => 'query',
				'format' => 'php',
				'list' => 'wkdomains',
				'wkfrom' => $from,
				'wkto' => $to,
				'wklimit' => 'max'
			);
			
			$result = $this->post_to_wiki($dataToPost);
			
			var_dump($result);
			
			foreach($result["query"]["wkdomains"] as $y) {
				$wikis[] = "http://" . $y["domain"] . "/api.php";
				$id = $y["id"];
				fwrite($fh, $y['domain'] . "\n");
			}
			
			if(empty($result["query"]["wkdomains"])) {
				return $wikis;
			}
			
			$from = $id + 1;
			$to = $from + 1000;
		}
		
		fclose($fh);
	}
	
	public function check_alog($user) {
	
		global $curloptions,$useragent;
		
		$alog_page = "http://services.runescape.com/m=adventurers-log/rssfeed?searchName=" . $user;
		
		$result = file_get_contents($alog_page);
		#var_dump($result);
		
		return $result;
	}
	
	public function get_usergroups($user) {
		
		$dataToPost = array(
			'action' => 'query',
			'list' => 'users',
			'ususers' => $user,
			'usprop' => 'groups',
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		if(!empty($result['query']['users'][0]['groups'])) {
			foreach($result['query']['users'][0]['groups'] as $y) {
				$rights[] = $y;
			}
		} else {
			$rights = 'none';
			
		}
		return $rights;

	}
	
	public function get_namespaces() {

		$dataToPost = array(
			'action' => 'query',
			'meta' => 'siteinfo',
			'siprop' => 'namespaces',
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		#var_dump($result);
		
		foreach($result['query']['namespaces'] as $y) {
			$namespaces[$y['id']] = $y['canonical'];
		}
		
		$namespaces[0] = 'Main';
		unset($namespaces[-2]);
		unset($namespaces[-1]);
		return $namespaces;

	}
}