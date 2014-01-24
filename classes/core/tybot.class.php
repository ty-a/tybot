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
    ###   CURL-LIB VARIABLES   ###
    ##############################
    public $curloptions = array(
        CURLOPT_COOKIEFILE = tempnam("/tmp", "CURLCOOKIE"),
        CURLOPT_COOKIEFILE = tempnam("/tmp", "CURLCOOKIE"),
        CURLOPT_RETURNTRANSFER = true,
        CURLOPT_USERAGENT = "tybot-4.0",
        CURLOPT_POST = true
    );

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
    
        global $wiki;
        
        #Initialize Curl
        $ch = curl_init();
        
        #Define options
        curl_setopt_array($ch, $this->$curloptions);
        
        curl_setopt($ch, CURLOPT_URL, $wiki);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        #Perform request
        $result = curl_exec($ch);
        
        #Unserialize request
        $result = unserialize($result);

        return $result;
               
    }
    
    ############################
    ###   END CORE ACTIONS   ###
    ############################
    
    #########################
    ###   START QUERIES   ###
    #########################
      
    #######################################################
    # Cat_memb - Get the titles of every page in a category
    #
    # Returns - array of page titles
    #
    # Arguments - string[$cat] int[$limit]
    #######################################################
    public function cat_memb($cat, $limit="max") {
    	
        $cmcontinue = '';
    	
        #Initial operation loop
        while (true) {
        	
            #Check if this is first query
            if ($cmcontinue === '') {
            	
                #Set up data for API query
                $data = array(
                    "action" => "query",
                    "list" => "categorymembers",
                    "cmtitle" => $cat,
                    "cmlimit" => $limit,
                    "cmprop" => "title",
                    "format" => "php"
                );
                
            } else {
            	
                #Set up data for API query
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

            #Send request and grab result
            $result = $this->post($data);

            #Check for query errors
            if (!empty($result["error"])) {
            	
                print("ERROR: " . $result["error"]["code"] . "\n");
                
                return false;
                
            }

            #Iterate through result and add title to array
            #Iterate sounds sounds much more intelligent
            foreach($result["query"]["categorymembers"] as $t) {
            	
                $pages[] = $t["title"];
                
            }

            #Check if there are more results
            if (empty($result["query-continue"])) {
            	
                return $pages;
                
            } else {
            	
            	#Continue loop
                $cmcontinue = $result["query-continue"]["categorymembers"]["cmcontinue"];
                
            }
            
        }
        
    }
    
    ####################################
    # Content - get contents of a page
    #
    # Returns - the text content of page
    #
    # Arguments - string[$title]
    ####################################
    public function content($title) {

        #Set data for api query
        $data = array(
	    "action" => "query",
            "format" => "php",
            "prop" => "revisions",
            "rvprop" => "content",
            "titles" => $title
        );

        #Post request and grab result
        $result = $this->post($data);

        #Iterate through result and grab content
        #If there is no content present it will
        #return an empty string
        foreach($result["query"]["pages"] as $r) {

            if (isset($r["revisions"][0]["*"])) {

                return $r["revisions"][0]["*"];

            } else {

                return "";

            }

        }
	    
    }
    
    #############################################################
    # Get_special - Get results from a special page (maintenence)
    #
    # Returns - an array of titles
    #
    # Arguments - string[$page] int[$amount]
    #############################################################
    public function get_special($page, $limit="") {
    	
        #Set up data for api query
        $dataToPost = array(
            "action" => "query",
            "list" => "querypage",
            "qppage" => $page,
            "qplimit" => $limit,
            "format" => "php"
        );
        
        #Send request and grab result
        $result = $this->post($data);

        #Check if our result was empty
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
    	
        #Set up data for api query
        $data = array(
            "action" => "query",
            "list" => "allusers",
            "augroup" => $group,
            "aulimit" => $amount,
            "format" => "php",
        );
        
        #Send request and grab result
        $result = $this->post($data);

        #Check if result is empty
        if (!empty($result)) {

            if (empty($result["error"])) {
        	
                foreach($result["query"]["allusers"] as $u) {
            	
                    $users[] = $u["name"];
                
                }

                return $users;
            
            } else {
        	
                print("ERROR: " . $result["error"]["code"] . "\n");
            
                return false;
            
            }

        } else {

            print("ERROR: No result recieved!\n");

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
        
        #Set up data for api query
        $data = array(
            "action" => "query",
            "list" => "users",
            "ususers" => $user,
            "usprop" => "groups",
            "format" => "php"
        );
        
        #Post request and grab result
        $result = $this->post($data);
        
        #Iterate through results only if there are any
        if (!empty($result)) {

            if (!empty($result["query"]["users"][0]["groups"])) {
            
                foreach($result["query"]["users"][0]["groups"] as $r) {
                
                    $rights[] = $r;
                
                }
            
            } else {
            
                $rights = "none";
            
            }

            return $rights;
        
        } else {

            print("ERROR: No result recieved!\n");

            return false;

        }

    }

    ####################################################
    # Special Page - Get the contents from special pages
    #
    # Returns - Array or false
    #
    # Arguments - srtring[$type] int[$limit]
    ####################################################
    public function special_page($type, $limit="") {

        #Set up data for query
        $data = array(
            "action" => "query",
            "list" => "querypage",
            "qptype" => $type,
            "qplimit" => $limit,
            "type" => "php"
        );
        
        #Send query and grab the result
        $result = $this->post($data);

        if (!empty($result)) {

            if (empty($result["error"])) {

                return $result;

            } else {

                print("ERROR: " . $result["error"]["code"] . "\n");

                return false;

            }

        } else {

            print("ERROR: No result recieved!\n");

            return false;

        }

    }

    ###############################
    # Token - grabs the edit token
    #
    # Returns - the recieved token
    #
    # Arguments - none
    ###############################
    public function token() {
    
        #Set data for api query
        $data = array(
            "action" => "query",
            "prop" => "info|revisions",
            "intoken" => "edit",
            "titles" => "Main Page",
            "format" => "php"
        );
        
        #Post request and grab result
        $result = $this->post($data);
        
        #Iterate through result and grab token
        if (!empty($result)) {

            foreach($result["query"]["pages"] as $p) {
        
                $token = $p["edittoken"];
            
            }

            return $token;

        } else {

            print("ERROR: No result recieved!\n");

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

        #Set up data for post
        $data = array(
            "action" => "block",
            "user" => "target",
            "reason" => $summary,
            "expiry" => $expiry,
            "format" => "php",
            "token" => $token
        );

        #Send request and grab result
        $result = $this->post($data);

        #Check for errors
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
    # Returns - boolean 
    #
    # Arguments - string[$page] string[$summary]
    ############################################
    public function delete($page, $summary="") {
    	
        global $token;
    	
        #Set up data to post to api
        $dataToPost = array(
            "action" => "delete",
            "title" => $page,
            "reason" => $summary,
            "format" => "php",
            "token" => $token
        );
        
        #Send request and grab result
        $result = $this->post($data);

        #Check result and return a boolean
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
    # Returns - true or error
    #
    # Arguments - string[$page] string[$text] 
    #             string[$summary] string[type] 
    #             int[$bot]
    ###########################################
    public function edit($page, $text, $summary="", $bot=1, $type="") {
    	
        global $token, $throttle;
    	
        #set up data for api post
        $data = array(
            "action" => "edit",
            "title" => $page,
            "summary" => $summary,
            $type . "text" => $text,
            "bot" => $bot,
            "token" => $token,
            "format" => "php"
        );
        
        #Pause program (throttle)
        print("Throttling...");
        
        sleep($throttle);
        
        #Send request and grab result
        $result = $this->post($data);

        #Check to see if edit was successful
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
        
        #Set data to get login token
        $data = array(
            "action" => "login",
            "lgname" => $user,
            "lgpass" => $pass,
            "format" => "php",
        );
        
        #Call post function to get and return data
        $result = $this->post($data, $wiki);
        
        #Set data for login with token
        $data = array(
            "action" => "login",
            "lgname" => $user,
            "lgpassword" => $pass,
            "lgtoken" => $result["login"]["token"],
            "format" => "php"
        );
        
        #Attempt to login to the wiki
        $result = $this->post($data, $wiki);
        
        #Check to see if we are logged in and return a boolean
        if ($result["login"]["result"] == "Success") {

            return true;
            
        } else {
        
            return false;
            
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

        #Set up data for api post
        $data = array(
            "action" => "protect",
            "title" => $page,
            "protections" => "edit=$editlevel|move=$movelevel",
            "expiry" => $expiry,
            "format" => "php",
            "token" => $token
        );

        #Send request and grab result
        $result = $this->post($data);

        #Check for errors
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

        #Set up data for api post
        $data = array(
            "action" => "unblock",
            "user" => $target,
            "reason" => $summary,
            "format" => "php",
            "token" => $token
        );

        #Send request and grab result
        $result = $this->post($data);

        #Check for errors
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

        #Set up data for api post
        $data = array(
            "action" => "undelete",
            "title" => $page,
            "reason" => $summary,
            "format" => "php",
            "token" => $token
        );

        #Send request and grab result
        $result = $this->post($data);
	
        #Check for errors
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