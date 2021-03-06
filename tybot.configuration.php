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

#########################
###   WIKI API PATH   ###
#########################
$wiki = "http://path.to/api.php";

#########################
###   WIKI USERNAME   ###
#########################
$user = "MyMediaWikiBot";

#########################
###   WIKI PASSWORD   ###
#########################
$pass = "MyMediaWikiPassword";

########################
###   CURL OPTIONS   ###
###   DO NOT TOUCH   ###
########################
$useragent = "tybot-4.0/bot";
$cookiefile = tempnam("/tmp", "CURLCOOKIE");
$curloptions = array(
    CURLOPT_COOKIEFILE => $cookiefile,
    CURLOPT_COOKIEJAR => $cookiefile,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => $useragent,
    CURLOPT_POST => true
);
