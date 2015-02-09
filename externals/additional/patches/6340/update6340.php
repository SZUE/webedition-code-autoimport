<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
//aber meiner Meinung nach nicht notwendig, patches werden nicht ausgeführt
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	//return true;
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

we_updater::updateGlossar();
return true;
