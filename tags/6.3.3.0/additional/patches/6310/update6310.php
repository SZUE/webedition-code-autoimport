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
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
include_once(WE_INCLUDES_PATH."we_classes/base/weFile.class.php");

function up6310_updatePrefs(){
	$db = new DB_WE();
	$db->query('UPDATE '.PREFS_TABLE.' SET BackendCharset="ISO-8859-1" WHERE (Language NOT LIKE "%_UTF-8%" AND Language!="") AND BackendCharset=""');
	$db->query('UPDATE '.PREFS_TABLE.' SET BackendCharset="UTF-8",Language=REPLACE(Language,"_UTF-8","") WHERE (Language LIKE "%_UTF-8%") AND BackendCharset=""');
	$db->query('UPDATE '.PREFS_TABLE.' SET BackendCharset="UTF-8",Language="Deutsch" WHERE Language="" AND BackendCharset=""');
	$_SESSION["prefs"] = getHash("SELECT * FROM " . PREFS_TABLE . " WHERE userID=" . intval($_SESSION["prefs"]["userID"]), $db);
	return true;
}

function up6310_updateLang(){
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
function up6310_updateActiveModules(){
	$dir=$_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/';
	$file='we_active_integrated_modules.inc.php';
	if(file_exists($dir.$file) && !file_exists($dir.'conf/'.$file) ){
		return rename($dir.$file,$dir.'conf/'.$file);
	} else {
		return true;
	}
}
function up6310_updateConf(){
	$filename= $_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/conf/we_conf.inc.php';
	$conf=file_get_contents($filename);
	if (strpos($conf,'_UTF-8')!==false){
		$conf=str_replace('_UTF-8','',$conf);
		$settingvalue='UTF-8';
	} else {
		$settingvalue='ISO-8859-1';
	}

	if(strpos($conf,'define("WE_BACKENDCHARSET"')===false){
		$conf = weConfParser::changeSourceCode("define", $conf, "WE_BACKENDCHARSET", $settingvalue);
		$conf=str_replace('include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php")','',$conf);
		return file_put_contents($filename,$conf);
	} else {
		return true;
	}
}
function up6310_removeFiles(){
	$toRemove = array('Deutsch_UTF-8','Dutch_UTF-8','English_UTF-8','Finnish_UTF-8','French_UTF-8','Polish_UTF-8','Russian_UTF-8','Spanish_UTF-8');
	foreach ($toRemove as $datei){
		if(file_exists($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/we_language/'.$datei) ){
			we_util_File::rmdirr($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/we_language/'.$datei);
		}
	}
	if(file_exists($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/weTagWizard/we_tags/we_tag_redirectObjectSeoUrls.inc.php')){
		we_util_File::delete($_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/weTagWizard/we_tags/we_tag_redirectObjectSeoUrls.inc.php');
	}
}
up6310_updatePrefs();
up6310_updateLang();
up6310_updateActiveModules();
up6310_updateConf();
up6310_removeFiles();
return true;
