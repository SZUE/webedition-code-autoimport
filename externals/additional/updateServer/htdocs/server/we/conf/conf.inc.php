<?php
/*
 * database connection
 */

// database for version information
$DSN_versioning = array(
	'phptype' => 'mysql',
	'hostspec' => 'mysql5.webedition.info',
	'database' => 'db343047_3', // webedition_versioning - versioning
);

$OPTIONS_versioning = array(
);


// database for registration information
$DSN_customer = array(
	'phptype' => 'mysql',
	'hostspec' => 'mysql5.webedition.info',
	'database' => 'licensing', // webedition_licensing - licensing
);

$OPTIONS_customer = array(
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
