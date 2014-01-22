Heirarchy:
==========
The Tybot heirarchy is comprised of a single ui script which creates module (sub-class) instances.
There are four layers or levels of the Tybot software. Layer 1 is the user interface which the 
user will interact with through the console. Layer 2 is made up of a single page containing imports for 
every module that is to be used. It also includes several misc. variables such as Curl-lib setup info.
Layer 3 and Layer 4 are parallel to each other meaning they are both included in Layer 2. Layer 3 
consists of the wrapper class which contains all of the functions that pertain to interaction with
the MediaWiki software. This involves editing and deleting, querying pages and rights, and fetching
other misc. information to be processed by the Level 4 scripts. Layer 4 is made up of several classes that
extend Layer 3. Each script (edit, delete, block, move category, etc) will be contained within it's own class.
Each class must have a main method titled init. This method is the first and only thing called by Layer 1.
If the script is a simple "do this then this then this" script then everything will be contained within
the init method. This is just fine and dandy, but if your script is to have multiple functions they must 
also be class methods.

Diagram
=======

         Layer 1
		    |
	   	 Layer 2
	  ______|_______
	  |            |
   Layer 3      Layer 4


Layer 3 ex.
===========
class Edit extends Tybot {

    public function init($page) {

	    //Do stuff here

    }

}
