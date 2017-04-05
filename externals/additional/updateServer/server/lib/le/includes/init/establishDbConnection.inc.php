<?php
/**
 * $Id: establishDbConnection.inc.php 13540 2017-03-12 11:48:37Z mokraemer $
 */
/**
 * Establish connection to databases containing versioning and register
 * information
 */
// versioning database
$db_versioning_down = false;
$DB_WE = new DB_WE();
/*
$DB_Versioning = & DB::connect($DSN_versioning, $OPTIONS_versioning);
if(PEAR::isError($DB_Versioning)){
	$db_versioning_down = true;
} else {
	$DB_Versioning->setFetchMode(DB_FETCHMODE_ASSOC);
}
*/