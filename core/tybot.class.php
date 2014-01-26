<?php
##########################################################################
#    This file is a part of Tybot in PHP
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
##########################################################################

#############################################
# Wrapper to interface with the MediaWiki API
#############################################
class Tybot {

    ##############################
    ###   START CORE ACTIONS   ###
    ##############################

    ######################################
    # Post - creates and http post request
    #
    # Returns - unserialized post result
    #
    # Arguments - array[$data]
    ######################################
    public function post($data) {
    
        global $curloptions,
               $wiki;

        $ch = curl_init();

        curl_setopt_array($ch, $curloptions);

        curl_setopt($ch, CURLOPT_URL, $wiki);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($ch);
        $result = unserialize($result);

        return $result;
    }
    
    ############################
    ###   END CORE ACTIONS   ###
    ############################
    
    #########################
    ###   START QUERIES   ###
    #########################
      
    ###############################################################
    # Category Members - Get the titles of every page in a category
    #
    # Returns - An array of page titles or false
    #
    # Arguments - string[$cat] int[$limit]
    ###############################################################
    public function categoryMembers($cat, $limit="max") {
    	
        $cmcontinue = '';
    	
        while (true) {
        	
            if ($cmcontinue === '') {
            	
                $data = array(
                    "action" => "query",
                    "list" => "categorymembers",
                    "cmtitle" => $cat,
                    "cmlimit" => $limit,
                    "cmprop" => "title",
                    "format" => "php"
                );
                
            } else {
            	
                $data = array(
                    "action" => "query",
                    "list" => "categorymembers",
                    "cmtitle" => $cat,
                    "cmlimit" => $limit,
                    "cmprop" => "title",
                    "cmcontinue" => $cmcontinue,
                    "format" => "php"
                );
                
            }

            $result = $this->post($data);

            if (!empty($result["error"])) {
            	
                print("ERROR: " . $result["error"]["code"] . "\n");
                
                return false;
                
            }

            foreach($result["query"]["categorymembers"] as $t) {
            	
                $pages[] = $t["title"];
                
            }

            if (empty($result["query-continue"])) {
            	
                return $pages;
                
            } else {

                $cmcontinue = $result["query-continue"]["categorymembers"]["cmcontinue"];
                
            }
            
        }
        
    }
    
    #################################################
    # Page Content - Gets the text contents of a page
    #
    # Returns - The page content or false
    #
    # Arguments - string[$title]
    #################################################
    public function pageContent($title) {

        $data = array(
	        "action" => "query",
            "format" => "php",
            "prop" => "revisions",
            "rvprop" => "content",
            "titles" => $title
        );

        $result = $this->post($data);

        foreach($result["query"]["pages"] as $r) {

            if (isset($r["revisions"][0]["*"])) {

                return $r["revisions"][0]["*"];

            } else {

                return "";

            }

        }
	    
    }
    
    ####################################################################
    # Query Special Page - Get results from a special page (maintenence)
    #
    # Returns - Array of page contents or false
    #
    # Arguments - string[$page] int[$amount]
    ####################################################################
    public function querySpecialPage($page, $limit="") {
    	
        $data = array(
            "action" => "query",
            "list" => "querypage",
            "qppage" => $page,
            "qplimit" => $limit,
            "format" => "php"
        );
        
        $result = $this->post($data);

        if (empty($result["error"])) {
        	
            return $result;
            
        } else {
        	
            print("ERROR: " . $result["error"]["code"] . "\n");
            
            return false;
            
        }
    	
    }
    
    ############################################################
    # Group - Get's a list of all members of a user rights group
    #
    # Returns - array of users
    #
    # Arguments - string[$group] int[amount]
    ############################################################
    public function group($group, $amount="max") {
        
        $data = array(
            "action" => "query",
            "list" => "allusers",
            "augroup" => $group,
            "aulimit" => $amount,
            "format" => "php",
        );
        
        $result = $this->post($data);

        if (empty($result["error"])) {
        	
            foreach($result["query"]["allusers"] as $u) {
            	
                $users[] = $u["name"];
                
            }

            return $users;
            
        } else {
        	
            print("ERROR: " . $result["error"]["code"] . "\n");
            
            return false;
            
        }
    	
    }
    
    ########################################
    # Rights - Get the user rights of a user
    #
    # Returns - array of rights a user holds
    #
    # Arguments - string[$user]
    ########################################
    public function rights($user) {
        
        $data = array(
            "action" => "query",
            "list" => "users",
            "ususers" => $user,
            "usprop" => "groups",
            "format" => "php"
        );
        
        $result = $this->post($data);

        if (!empty($result["query"]["users"][0]["groups"])) {
            
                
            foreach($result["query"]["users"][0]["groups"] as $r) {
                
                $rights[] = $r;
                
            }
            
        } else {
            
            $rights = "none";
            
        }

        return $rights;

    }

    #######################################
    # Token - Grabs the edit token
    #
    # Returns - The recieved token or false
    #
    # Arguments - none
    #######################################
    public function token() {
    
        $data = array(
            "action" => "query",
            "prop" => "info|revisions",
            "intoken" => "edit",
            "titles" => "Main Page",
            "format" => "php"
        );
        
        $result = $this->post($data);
    
        if (empty($result["error"])) {

            foreach($result["query"]["pages"] as $p) {
        
                $token = $p["edittoken"];
            
            }

            return $token;

        } else {

            return false;

        }
    
    }

    #######################
    ###   END QUERIES   ###
    #######################
    
    #########################
    ###   START ACTIONS   ###
    #########################
    
    ##############################################
    # Block - Block a user from the wiki
    #
    # Returns - True or false
    #
    # Arguments - string[$target] string[$summary]
    #             string[$expiry]
    ##############################################
    public function block($target, $summary="", $expiry="indefinite") {

        global $token;

        $data = array(
            "action" => "block",
            "user" => "target",
            "reason" => $summary,
            "expiry" => $expiry,
            "format" => "php",
            "token" => $token
        );

        $result = $this->post($data);

        if (empty($result["error"])) {

            return true;

        } else {

            print("ERROR: " . $result["error"]["code"] . "\n");

            return false;

        }

    }

    ############################################
    # Delete - deletes a page from the wiki
    #
    # Returns - true or false 
    #
    # Arguments - string[$page] string[$summary]
    ############################################
    public function delete($page, $summary="") {
    	
        global $token;

        $data = array(
            "action" => "delete",
            "title" => $page,
            "reason" => $summary,
            "format" => "php",
            "token" => $token
        );
        
        $result = $this->post($data);

        if (empty($result["error"])) {
        	
            return true;
            
        } else {
        	
            print("ERROR: " . $result["error"]["code"] . "\n");
            
            return false;
            
        }
    	
    }
    
    ###########################################
    # Edit - Edits a page
    #
    # Returns - true or false
    #
    # Arguments - string[$page] string[$text] 
    #             string[$summary] int[$bot] 
    #             int[$throttle]
    ###########################################
    public function edit($page, $text, $summary="", $bot=1, $throttle=1) {
    	
        global $token;

        $data = array(
            "action" => "edit",
            "title" => $page,
            "summary" => $summary,
            "text" => $text,
            "bot" => $bot,
            "token" => $token,
            "format" => "php"
        );
        
        print("Throttling...");
        
        sleep($throttle);
        
        $result = $this->post($data);

        if (empty($result["error"])) {
                
            return true;
            
        } else {
                
            print("ERROR: " . $result["error"]["code"] . "\n");
            
            return false;
            
        }
        
    }
    
    #########################################
    # Login - Creates a login request
    #
    # Returns - True or False 
    #
    # Arguments - string[$user] string[$pass]
    #########################################
    public function login($user, $pass) {
        
        global $wiki;
        
        $data = array(
            "action" => "login",
            "lgname" => $user,
            "lgpass" => $pass,
            "format" => "php",
        );

        $result = $this->post($data);
        
        $data = array(
            "action" => "login",
            "lgname" => $user,
            "lgpassword" => $pass,
            "lgtoken" => $result["login"]["token"],
            "format" => "php"
        );
        
        $result = $this->post($data);
        
        if ($result["login"]["result"] == "Success") {
            
            print("Now logged in!\n");

            return true;
            
        } else {
        
            die("Failed to login.\n");
            
        }
            
    }

    ################################################
    # Protect - Protects a page from editing/moving
    #
    # Returns - True or false
    #
    # Arguments - string[$page] string[$movelevel]
    #			  string[$editlevel] string[$expiry]
    #             string[$summary]
    ################################################
    public function protect($page, $movelevel="", $editlevel="", $expiry="", $summary="") {

        global $token;

        $data = array(
            "action" => "protect",
            "title" => $page,
            "protections" => "edit=$editlevel|move=$movelevel",
            "expiry" => $expiry,
            "format" => "php",
            "token" => $token
        );

        $result = $this->post($data);

        if (empty($result["error"])) {

            return true;

        } else {

            print("ERROR: " . $result["error"]["code"] . "\n");

            return false;

        }

    }

    ##############################################
    # Unblock - Unblocks a user from a wiki
    #
    # Returns - True or False
    #
    # Arguments - string[$target] string[$summary]
    ##############################################
    public function unblock($target, $summary="") {

        global $token;

        $data = array(
            "action" => "unblock",
            "user" => $target,
            "reason" => $summary,
            "format" => "php",
            "token" => $this->$token
        );

        $result = $this->post($data);

        if (empty($result["error"])) {

            return true;

        } else {

            print("ERROR: " . $result["error"]["code"] . "\n");

            return false;

        }

    }

    ############################################
    # Undelete - Undeletes a page
    #
    # Returns - True or False
    #
    # Arguments - string[$page] string[$summary]
    ############################################
    public function undelete($page, $summary="") {

        global $token;

        $data = array(
            "action" => "undelete",
            "title" => $page,
            "reason" => $summary,
            "format" => "php",
            "token" => $token
        );

        $result = $this->post($data);
	
        if (empty($result["error"])) {

            return true;

        } else {

            print("ERROR: " . $result["error"]["code"] . "\n");

            return false;

        }

    }

    #######################
    ###   END ACTIONS   ###
    #######################
    
}
