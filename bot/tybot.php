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

$tybot = new tybot();
$version = "2.0.0";
$useragent = "TyBot/" . $version . " - " . $operator_email;
$cookiefile = tempnam("/tmp", "CURLCOOKIE");
$curloptions = array(
    CURLOPT_COOKIEFILE => $cookiefile,
    CURLOPT_COOKIEJAR => $cookiefile,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => $useragent,
    CURLOPT_POST => true
);
/* STUFF GOES UNDER HERE */
/* Get command line arguments and act on them */
$shortopts = 's:'; //optional - speed
$shortopts.= 'r:'; // reason
$shortopts.= 'p:'; // page
$shortopts.= 'q:'; // query
$shortopts.= 'c:'; //category
$shortopts.= 'f:'; //from
$shortopts.= 't:'; //to
$longopts = array(
    'task:'
);
$options = getopt($shortopts, $longopts);

// var_dump($options);

if (!isset($options['task']))
  {
    $options['task'] = 'help';
  }

if ($options['task'] != 'help')
  {
    $result = $tybot->login($user, $pass);
    if ($result === false)
      {
        die("Incorrect username or password.\n");
      }
    else
      {
        print "Now logged in.\n";
        $token['edit'] = $tybot->get_edit_token();
        $token['delete'] = $tybot->get_delete_token();
        $token['protect'] = $tybot->get_protect_token();
        $token['block'] = $tybot->get_block_token();
        $token['unblock'] = $tybot->get_unblock_token();
      }
  }

if (isset($options['s']))
  {
    $throttle = $options['s'];
  }

switch ($options['task'])
  {
case 'help':
    print "Thank you for using TyBot! You are currently using TyBot version $version!\n";
    print "To choose a task, use --task task where the 2nd task is the name of the task\nyou'd like to run. ";
    print "Legal options are redirect, delete, replace, and allwikis.\n";
    print "'redirect' fixes double redirects and accepts no parameters.\n";
    print "'delete' deletes pages. It requires user to have the delete right.\n";
    print 'Parameters for delete are -p "page", -c "category", and -r "reason".';
    print "\n";
    print "'replace' replaces text with other text. It requires the edit right.\n";
    print "Parameters for replace are -f 'from text', -t 'to text', -s 'editing speed in \nseconds default:2'.\n";
    print "'allwikis' saves a text file called 'wikis.txt' in the running directory with \nall Wikia wikis in it.\n";
    print "For assistance with this tool, please contact [[User:TyA]] at \nhttp://c.wikia.com/wiki/Message_Wall:TyA\n";
    break;

case 'redirect':
    $tybot->fix_double_redirects();
    break;

case 'delete':
    if (!isset($options['r']))
      {
        $options['r'] = "Automated deletion";
      }

    if (!isset($options['p']) && !isset($options['c']))
      {
        die("You did not specify the page or category to delete.\n");
      }

    if (isset($options['c']))
      {
        $pages = $tybot->get_category_members("Category:" . $options['c']);
        if ($pages === false)
          {
            die("Error getting pages from Category" . $options['c'] . "\n");
          }

        foreach($pages as $y)
          {
            $tybot->delete($y, $options['r']);
          }
      }
    elseif (isset($options['p']))
      {
        $tybot->delete($options['p'], $options['r']);
      }

    break;

case 'replace':
    if (!isset($options['t']))
      {
        die("You need to specify what you're replacing with using -t 'replacement'\n");
      }

    if (!isset($options['f']))
      {
        die("You need to specify what you want to replace using -f 'change this'\n");
      }

    if (!isset($options['c']) && !isset($options['p']))
      {
        die("-c or -p not specified.\n");
      }

    if (isset($options['c']))
      {
        $pages = $tybot->get_category_members("Category:" . $options['c']);
        if ($pages === false)
          {
            die("Failed to get category members in Category:" . $options['c'] . "\n");
          }

        foreach($pages as $y)
          {
            $tybot->find_and_replace($y, $options['f'], $options['t']);
          }
      }
    elseif (isset($options['p']))
      {
        $tybot->find_and_replace($options['p'], $options['f'], $options['t']);
      }

    break;

case 'allwikis':
    $tybot->get_all_wikia_wikis();
    break;

default:
    print "Invalid option. Use --task help for list of commands.\n";
    break;
  }
