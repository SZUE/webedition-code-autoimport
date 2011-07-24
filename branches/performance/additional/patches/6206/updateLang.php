<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	return true;
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_conf.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_global.inc.php");
we_loadLanguageConfig();
/*if(!we_writeLanguageConfig($GLOBALS['weFrontendLanguages'],$GLOBALS['weDefaultFrontendLanguage'])){
	$GLOBALS['errorDetail']='Error at updating global language.';
	return false;
}*/

return true;
