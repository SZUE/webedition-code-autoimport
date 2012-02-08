<?php
/*
Attempt to update 
*/
// only execute on liveUpdate:
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	//return true; 
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/conf/we_conf.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
$db= new DB_WE();
$q="UPDATE ".FILE_TABLE." SET Category='' WHERE ContentType='image/*' AND Category=',we_imageDocument,'"; 
$db->query($q);
return true;
?>