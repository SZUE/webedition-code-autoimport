<?php

/**
 * webEdition CMS configuration file
 * NOTE: this file is regenerated, so any extra contents will be overwritten. Change only already existent fields, if really needed.
 *
 */

//if used password protection to the webEdition directory, the username
//define('HTTP_USERNAME', base64_decode(''));

//if used password protection to the webEdition directory, the password
//define('HTTP_PASSWORD', base64_decode(''));

//Mode how to access the database: mysqli_connect, mysqli_pconnect, deprecated: connect, pconnect
define('DB_CONNECT', "mysqli_connect");

//connection charset to db
define('DB_SET_CHARSET', "utf8");

//Domain or IP address of the database server
define('DB_HOST', "mysql5.webedition.de");

//Name of database used by webEdition
define('DB_DATABASE', "");

//Username to access the database
define('DB_USER', base64_decode(''));

//Password to access the database
define('DB_PASSWORD', base64_decode(''));

//Prefix of tables in database for this webEdition.
define('TBL_PREFIX', "");

//Charset of tables in database for this webEdition.
define('DB_CHARSET', "utf8");

//Collation of tables in database for this webEdition.
define('DB_COLLATION', "utf8_general_ci");

//Original language of this version of webEdition, used for login-screen
define('WE_LANGUAGE', "Deutsch");

//Original backend charset of this version of webEdition, used for login-screen
define('WE_BACKENDCHARSET', "ISO-8859-1");
