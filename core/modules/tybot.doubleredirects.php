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
class DoubleRedirect extends Tybot {

    ################
    # Initialization
    ################
    public function init() {
    
        #Notify of start
        print("Starting Double Redirect Resolver\n");
        
        #Get a list of double redirects
        $result = $this->querySpecialPage("DoubleRedirects");
        
        #Resolve redirects one by one
        foreach($result["query"]["querypage"]["results"] as $first) {
        
            #Get page content
            $result = $this->pageContent($first["title"]);
            
            #Check if page exists
            if ($result == "") {
            
                #Print error and skip
                print ("Cannot get page contents, skipping.\n");
                continue;
                
            }

            #find and replace redirect text
            $page = str_replace("#REDIRECT [[", "", $result);
            $page = str_replace("]]", "", $page);
            
            #Get the page contents of the broken redirect
            $result = $this->pageContent($page);
            
            #Check if page is blank
            if ($result == "") {
            
                #Print error and skip
                print ("Cannot get page contents, skipping.\n");
                continue;
                
            }

            #Resolve redirect
            $result = $this->edit($first["title"], $result, "Resolving double redirect", 1);
            
            #Check for errors
            if ($result == false) {
            
                #Print error
                print ("Could not resolve redlink.\n");
                
            } else {
            
                #Print success
                print ("Redlink resolved!\n");
                
            }
            
        }
        
    }
    
}
