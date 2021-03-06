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

#########################################################
# All files needed for operation must be imported here. #
#########################################################

############################################
#####        MAIN CLASS IMPORT         #####
############################################
/*#*/ include("core/tybot.class.php"); /*#*/
############################################

###############################################
###           CONFIGURATION FILE            ###       
###############################################
/*#*/ include("tybot.configuration.php"); /*#*/
###############################################

###################
###   MODULES   ###
###################

#Double Redirects
include("core/modules/tybot.doubleredirects.php");

#Unused Images
include("core/modules/tybot.unusedimages.php");