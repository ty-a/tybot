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
class UnusedImages extends Tybot {

    ################
    # Initialization
    ################
    public function init() {
    
        #Notify of start
        print("Starting Unused Images Deletion\n");
        
        #Get a list of unused images
        $result = $this->querySpecialPage("Unusedimages");
        
        #Delete images one by one
        foreach($result["query"]["querypage"]["results"] as $image) {
        
            #Print message
            print("Attempting to delete File:$image\n");
            
            #Delete image
            $result = $this->deletePage($image, "Unused image - bot");
            
            if (empty($result["error"])) {
            
                print("File:$image deleted!\n");
                
            } else {
            
                print("Failed to delete File:$image\n");
                
            }
            
        }
        
    }
    
}