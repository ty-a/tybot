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

$cookiefile = tempnam("/tmp", "CURLCOOKIE");
$curloptions = array(
    CURLOPT_COOKIEFILE => $cookiefile,
    CURLOPT_COOKIEJAR => $cookiefile,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => $useragent,
    CURLOPT_POST => true
);
$tybot = new tybot();
/** Get command line arguments */
$shortops = "o:";
$shortops.= "n:";
$longops = array(
    "help"
);
$ops = getopt($shortops, $longops);
/** Process command line ops */

if (!isset($ops["o"]))
  {
    print ("Please set the -o flag and try again.");
    die(0);
  }

if (!isset($ops["n"]))
  {
    print ("Please set the -n flag and try again.\n");
    die(0);
  }

if (isset($ops["help"]))
  {
    print ("Category rename:\n");
    print ("automatically renames a category and all of it's members\n");
    print ("to use: set the -o flag to the old category name\n");
    print ("and the -n flag to the name of the new category.\n");
    print ("Note: Please include the category prefix i.e. Category:Foo\n");
    die(0);
  }

if (!isset($ops["help"]) && !isset($ops["o"]) && !isset($ops["n"]))
  {
    print ("This script requires parameters. Please type \"--help\" for more info.");
    die(0);
  }

/** Login to the wiki */
$r = $tybot->login($user, $pass);

if ($r == false)
  {
    print ("Login failed, please check the config file to make sure that\n");
    print ("the details you have provided are correct. Sorry.\n");
    die(0);
  }

if ($r == true)
  {
    print ("Login was successful.\n");
  }

/** Get edit token */
$token = $tybot->get_token();
/** Get pages in category */
$pages = $tybot->get_category_members($ops["o"]);

if ($pages == false)
  {
    print ("Invalid or empty category: " . $ops["o"] . "\n");
  }

foreach($pages as $page)
  {
    $cont = $tybot->get_page_content($page);
    if ($cont == "")
      {
        print ("Error: Page doesn't exist!\n");
        continue;
      }

    $r = replace_category($page, $cont, $ops["o"], $ops["n"]);
    if ($r == true)
      {
        print ("Successfully updated: " . $page . "\n");
      }
  }

/** Move category */
$r = $tybot->get_page_content($ops["o"]);
$r = $tybot->edit($ops["n"], $r, "Renaming category", 1);

if ($r == true)
  {
    print ("Successfully transfered category contents.\n");
  }
else
  {
    print ("Failed to transfer category contents.\n");
  }

$r = $tybot->delete($ops["o"], "Renaming categories");

if ($r == true)
  {
    print ("Category deletion complete.\n");
  }
else
  {
    print ("Category deletion failed.\n");
  }

/** Find and replace category in page */

function replace_category($page, $cont, $old, $new)
  {
    $tybot = new tybot();
    $find = "[[$old";
    $replace = "[[$new";
    $content = str_replace($find, $replace, $cont);
    $r = $tybot->edit($page, $content, "Renaming category", 1);
    return $r;
  }
