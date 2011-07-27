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

	$db1 = new DB_WE();
	$db1->query("SELECT * FROM " . PREFS_TABLE);
	$db2 = new DB_WE();
	while ($db1->next_record()){
		$userprefs = $db1->Record;
		if ($userprefs['BackendCharset']=='' && $userprefs['Language']!='' && !is_numeric($userprefs['Language'])){
			if (strpos($userprefs['Language'],'UTF-8')===false){
				$q="UPDATE ".PREFS_TABLE." SET BackendCharset='ISO-8859-1' WHERE userID=".$userprefs['userID'];
			} else {
				$q="UPDATE ".PREFS_TABLE." SET BackendCharset='UTF-8', Language='".str_replace('_UTF-8','',$userprefs['Language'])."' WHERE userID=".$userprefs['userID'];                                                                                    
			}
			$db2->query($q);
		}
	}
	return true;  
 
