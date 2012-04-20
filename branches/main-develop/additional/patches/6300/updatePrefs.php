<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
//aber meiner Meinung nach nicht notwendig, patches werden nicht ausgeführt
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	return true;
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

	$db = new DB_WE();
	$db->query('UPDATE '.PREFS_TABLE.' SET BackendCharset="ISO-8859-1" WHERE Language NOT LIKE "%_UTF-8%"');
	$db->query('UPDATE '.PREFS_TABLE.' SET BackendCharset="UTF-8",Language=REPLACE(Language,"_UTF-8","") WHERE Language LIKE "%_UTF-8%"');

	return true;
