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
	
	$dir=$_SERVER["DOCUMENT_ROOT"].'/webEdition/we/include/';
	$file='we_active_integrated_modulesA.inc.php';
	if(file_exists($dir.$file) && !file_exists($dir.'conf/'.$file) ){
		return rename($dir.$file,$dir.'conf/'.$file);
	} else {
		return true;	
	}


return true;
