#!/usr/local/bin/php5-56LATEST-CLI
<?php
$_SERVER['DOCUMENT_ROOT'] = '/kunden/343047_10825/sites/webedition.org/nightlybuilder';
define('NO_SESS', 1);
require('../conf/we_conf.inc.php');
require('../webEdition/we/include/we.inc.php');

$arguments = getopt("v:");
$version = intval($arguments['v']);

if($version){
	$ret = true;
	foreach(array('v6_versions', 'v6_changes', 'v6_changes_language') as $table){
		$ret &= $GLOBALS['DB_WE']->query('DELETE FROM ' . $table . ' WHERE version=' . $version);
	}

	echo intval($ret);
} else {
	echo 0;
}
