﻿#TyBot Release Notes

TyBot is a PHP bot framework for editing MediaWiki Wikis via the API.

## 2.0
* Major rewrite 
* All actions are to be done through command line options

## 1.6.0
* Added get_user_contribs function to get a user's contribs
* Added get_log_actions function to get log events
* Added a script to make edit reports
* Less verbose

## 1.5.0
* Added get_what_links_here function to return all pages that link to a page
* Changed get_category_members function to return all pages in categories larger than "max"

## 1.4.0
* Added get_all_ages function to get all pages

## 1.3.0
* Added fix_double_redirect function to fix double redirects (MW 1.18+)
* Added query_page function to make use of list=querypage (MW 1.18+)

## 1.2.0
* Added get_random_pages function to return random pages
* Added return for find_and_replace function based on success

## 1.1.0
* Added some error checking
* Added get_users_in_groups() to get the users in a specified usergroup
* Most functions will now return their success or lack thereof

## 1.0.0
* Initial release
