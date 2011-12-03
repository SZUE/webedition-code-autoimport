<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
//aber meiner Meinung nach nicht notwendig, patches werden nicht ausgeführt
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	//return true;
}
//die Vorbilder includen mehr, aber wozu?
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_inc_min.inc.php");//nicht we.inc.php da genau das nicht funktioniert wegen der noch nicht verschobenen we_active_integrated_modules
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/base/weConfParser.class.php");
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');

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
	if (is_array($GLOBALS['weFrontendLanguages'])){
		$FLkeys = array_keys($GLOBALS['weFrontendLanguages']);
		if (!is_numeric($FLkeys[0]) ){
			we_writeLanguageConfig($GLOBALS['weDefaultFrontendLanguage'],$FLkeys);
		}
	} else {
		
	}
	/*if(!we_writeLanguageConfig($GLOBALS['weFrontendLanguages'],$GLOBALS['weDefaultFrontendLanguage'])){
	$GLOBALS['errorDetail']='Error at updating global language.';
	return false;
	}*/
	return true;
	
}
function updateActiveModules(){
	$dir=$_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/';
	$file='we_active_integrated_modules.inc.php';
	if(file_exists($dir.$file) && !file_exists($dir.'conf/'.$file) ){
		return rename($dir.$file,$dir.'conf/'.$file);
	} else {
		return true;	
	}
}
function updateConf(){
	$filename= $_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/conf/we_conf.inc.php';
	$conf=file_get_contents($filename);
	if (strpos($conf,'_UTF-8')!==false){
		$conf=str_replace('_UTF-8','',$conf);
		$settingvalue='UTF-8';
	} else {
		$settingvalue='ISO-8859-1';
	}
	
	$conf = weConfParser::changeSourceCode("define", $conf, "WE_BACKENDCHARSET", $settingvalue);
	$conf=str_replace('include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php")','',$conf);
	
	return file_put_contents($filename,$conf);
}
function removeFiles(){	
	$toRemove = array('Deutsch_UTF-8','Dutch_UTF-8','English_UTF-8','Finnish_UTF-8','French_UTF-8','Polish_UTF-8','Russian_UTF-8','Spanish_UTF-8');
	foreach ($toRemove as $datei){
		if(file_exists($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/we_language/'.$datei) ){
			we_util_File::rmdirr($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/we_language/'.$datei);
		}
	}
}
updatePrefs();t_e('Update Prefs OK');
updateLang();t_e('Update Lang OK');
updateActiveModules();t_e('Update modules OK');
updateConf();t_e('Update Conf OK');
removeFiles();t_e('Update Files OK');
return true;
