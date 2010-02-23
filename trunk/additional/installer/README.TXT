webEdition installer files
==========================

this directory contains all files required to build a new webEdition installer script. It can be run in two ways:

1. directly using a web server:
-------------------------------
point your browser to the URL http://YOURHOST/additional/installer/make.php
NOTE: you have to rename or remove the the .htaccess file located in /additional/
  
This method will create an OnlineInstaller.php script located in the directory /additional/installer/out/

2. from php:
------------
you can also use make.php from another PHP script using this piece of code (and by replacing the paths in this example with valid values):

include('path/to/make.php');
$le_OnlineInstaller = new le_OnlineInstaller_Make();
$le_OnlineInstaller->execute('path/to/base', 'path/to/out/', '6.0.0.9');

To avoid creating the OnlineInstaller.php file in /additional/installer/out/ you can comment out the two last lines of code in make.php

TO DEVELOPERS: Creating new versions:
-------------------------------------
When creating a new releases of the webEdition installer (meaning changing the version number), the version number has to be changed in these files:

- setup.php (script comment and variable $LU_Version, line 39)
- make.php (last line of code, app. line 514)

The changelog is now located at http://documentation.webedition.de
