webEdition installer files
==========================

this directory contains all files required to build a new webEdition installer script. It can be run in two ways:


1. directly using a web server:
--------------------------------------------------------------------------
point your browser to the URL http://YOURHOST/additional/installer/make.php
NOTE: you have to rename or remove the the .htaccess file located in /additional/
  
This method will create an OnlineInstaller.php script located in the directory /additional/installer/out/


2. from php:
--------------------------------------------------------------------------
you can also use make.php from another PHP script using this piece of code (and by replacing the paths in this example with valid values):

include('path/to/make.php');
$le_OnlineInstaller = new le_OnlineInstaller_Make();
$code = $le_OnlineInstaller->execute(null, null, '2.3.0.0');

To avoid creating http headers for sending the OnlineInstaller.php file to the browser you should comment out the two last lines of code in make.php
The null values in execute() causes make.php to return the contents of the OnlineInstaller.php script to the caller instead of writing it to a file.


TO DEVELOPERS: Creating new versions:
--------------------------------------------------------------------------
When creating a new release of the webEdition installer (meaning changing the version number), the version number is specified 
by setting the parameter of the method "execute()" as seen above. When you call this script from code, nothing has to be changed.
Please do not remove the placeholders ###VERSION### in setup.php since they will be automatically replaced by the make script.

When running make.php from your browser, don't forget to change the last line of code in make.php to update the version number: 
-> make.php (last line of code, app. line 514)


Please note:
--------------------------------------------------------------------------
The changelog is now located at http://documentation.webedition.org
