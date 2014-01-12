<?php
/*************************************************************************
*    This file is part of TyBot (in PHP).
*
*    TyBot (in PHP) is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    TyBot (in PHP) is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with TyBot (in PHP).  If not, see <http://www.gnu.org/licenses/>.
*/
require_once ("../core/tybot.class.php");

require_once ("../core/tybot.conf.php");

$useragent = "TyBot Double Redirect Resolver";
$cookiefile = tempnam("/tmp", "CURLCOOKIE");
$curloptions = array(
    CURLOPT_COOKIEFILE => $cookiefile,
    CURLOPT_COOKIEJAR => $cookiefile,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => $useragent,
    CURLOPT_POST => true
);
$tybot = new tybot();

/** Login */
$r = $tybot->login($user, $pass);

if ($r == true)
  {
    print ("Login successful.\n");
  }
else
  {
    print ("An error has occured. Please confirm that your login details are correct and try again.\n");
    die(0);
  }

/** Get edit token */
$token = $tybot->get_token();

/** Get list of double redirects */
$r = $tybot->query_page("DoubleRedirects");

/** Resolve redirects */
foreach($r["query"]["querypage"]["results"] as $first)
  {
    $r = $tybot->get_page_content($first["title"]);
    if ($r == "")
      {
        print ("Cannot get page contents, skipping.\n");
        continue;
      }

    $page = str_replace("#REDIRECT [[", "", $r);
    $page = str_replace("]]", "", $page);
    $r = $tybot->get_page_content($page);
    if ($r == "")
      {
        print ("Cannot get page contents, skipping.\n");
        continue;
      }

    $r = $tybot->edit($first["title"], $r, "Resolving double redirect", 1);
    if ($r == false)
      {
        print ("Could not resolve redlink.\n");
      }
    else
      {
        print ("Redlink resolved!\n");
      }
  }
