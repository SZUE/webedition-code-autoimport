<?php
/*
 * database connection
 */
//Mode how to access the database: mysqli_connect, mysqli_pconnect, deprecated: connect, pconnect
define('DB_CONNECT', "mysqli_connect");

//connection charset to db
define('DB_SET_CHARSET', "utf8");

//Domain or IP address of the database server
define('DB_HOST', "mysql5.webedition.de");

//Name of database used by webEdition
define('DB_DATABASE', "db343047_3");

//Username to access the database
define('DB_USER', base64_decode('ZGIzNDMwNDdfMw=='));

//Password to access the database
define('DB_PASSWORD', base64_decode('V2RjQXFjZDJhcQ=='));

//Prefix of tables in database for this webEdition.
define('TBL_PREFIX', "");

//Charset of tables in database for this webEdition.
define('DB_CHARSET', "utf8");

//Collation of tables in database for this webEdition.
define('DB_COLLATION', "utf8_general_ci");

// database for version information
$DSN_versioning = array(
	'phptype' => 'mysql',
	'hostspec' => 'mysql5.webedition.info',
	'database' => 'db343047_3', // webedition_versioning - versioning
);

$OPTIONS_versioning = array(
);


/*
 * Path to pear, only uses DB atm
 */
define('PATH_TO_PEAR', $_SERVER['DOCUMENT_ROOT'] . '/server/lib/PEAR');

// add pear directory to the include-path if not already done.
// add comments if this is already done
//ini_set('include_path', ini_get('include_path') . ";" . PATH_TO_PEAR);
ini_set('include_path', PATH_TO_PEAR . ":."); // - server

ini_set('magic_quotes_gpc', 'Off');
ini_set('session.use_cookies', 'Off');

// Since version 5 there are some modules free of charge an included in webEdition per default
$GLOBALS['MODULES_FREE_OF_CHARGE_INCLUDED'] = array(
	'users',
	'banner',
	'editor',
	'schedpro',
	'schedule',
	'spellchecker',
	'export',
	'voting',
	'glossary',
);
