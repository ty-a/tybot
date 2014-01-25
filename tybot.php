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

##################
# Include includes
##################
include("tybot.includes.php");

###########################
# Print the welcome message
###########################
$welcome =  "--------------------------------\n";
$welcome .= "   Welcome to Tybot in PHP   \n";    
$welcome .= "Please type \"help\" for more info\n";
$welcome .= "--------------------------------\n";

print($welcome);

#########################
# Call the input function
#########################
input();

###################
# Gather user input
###################
function input() {

    #Prompt user
    $input = readline("> ");

    #Pass input to switch function
    process($input);

}

##############################
# Start modules based on input
##############################
function process($input) {

    switch ($input) {

        default:
            print("The command you entered is invalid.\n");

    }

}
