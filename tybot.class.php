<?php
class tybot {

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
			print "Now logged in.\n";
			return true;
		
		} else {
			print "Failed!";
		}
		
		}
	
	public function post_to_wiki($dataToPost) {
	
		global $curloptions,$wiki,$useragent;

		$ch = curl_init();
		curl_setopt_array($ch, $curloptions);
		curl_setopt($ch, CURLOPT_URL, $wiki);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataToPost);
		$result = curl_exec($ch);
		$result = unserialize($result);
		var_dump($result);
		
		return $result;
	}
	
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
		
	public function get_undelete_token() {
		$dataToPost = array(
			"action" => "query",
			"list" => "deletedrevs",
			"drprop" => "token",
			"format" => "php"
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		foreach ($result["query"]["deletedrevs"] as $y) {
			$token["undelete"] = $y["token"];
		}
		
		return $token["undelete"]; 
		
	}
	
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
		
		$result = $this->post_to_wiki($dataToPost);
		echo "Sleeping for $throttle seconds.";
		sleep($throttle);
		
		
	}
	
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
		
		}
		
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
		}
		
	public function userrights($targetUser,$add='',$remove='',$summary='') {
		
		$userrightstoken = get_userrights_token($targetUser);
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
		
	
		}
		
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
		
		
		}
		
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
	}
	
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
	
	}
	
	public function get_category_members($category) {
		
		$dataToPost = array(
			'action' => 'query',
			'list' => 'categorymembers',
			'cmtitle' => $category,
			'cmlimit' => 'max',
			'cmprop' => 'title',
			'format' => 'php'
		);
		
		$result = $this->post_to_wiki($dataToPost);
		
		var_dump($result);
		
		foreach($result["query"]["categorymembers"] as $y) {
			$pages[] = $y["title"];
		}
		
		return $pages;
	
	}
	
	public function find_and_replace($page,$find,$replace) {
		$content = $this->get_page_content($page);
		$content = str_replace($find,$replace,$content);
		
		$this->edit($page,$content,"Replacing " . $find . " with " . $replace ,"1");
		
	}
	
}