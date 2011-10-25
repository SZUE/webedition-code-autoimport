<?php
/*
Attempt to update 
*/
// only execute on liveUpdate:
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	return true;
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_conf.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/base/"."we_updater.inc.php");
$_updater = new we_updater();
if (!$_updater->updateLock()) {
	$GLOBALS['errorDetail']='Error at updating the tblLock table';
	return false;
}

unset($_updater);
return true;
?>