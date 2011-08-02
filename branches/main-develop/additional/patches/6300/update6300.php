<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
//aber meiner Meinung nach nicht notwendig, patches werden nicht ausgeführt
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	return true;
}
//die Vorbilder includen mehr, aber wozu?
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

function updatePrefs(){
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
}
function updateLang(){
	we_loadLanguageConfig();
	/*if(!we_writeLanguageConfig($GLOBALS['weFrontendLanguages'],$GLOBALS['weDefaultFrontendLanguage'])){
	$GLOBALS['errorDetail']='Error at updating global language.';
	return false;
	}*/
	return true;
	
}
function updateActiveModules(){
	$dir=$_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/';
	$file='we_active_integrated_modulesA.inc.php';
	if(file_exists($dir.$file) && !file_exists($dir.'conf/'.$file) ){
		return rename($dir.$file,$dir.'conf/'.$file);
	} else {
		return true;	
	}
}
function updateConf(){
	$filename= $_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/conf/we_conf.inc.php';
	$conf=file_get_contents($filename);
	$conf=str_replace('_UTF-8','',$conf);
	$conf=str_replace('include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php")','',$conf);
	return file_put_contents($filename,$conf);
}
	
updatePrefs();
updateLang();
updateActiveModules();
updateConf();

return true;
