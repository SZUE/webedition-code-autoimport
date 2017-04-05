<?php
/**
 * $Id: checkTemporarilyShutDown.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
/**
 * temporarly shut down - preparations for wE4
 *
 */

// use this template to print a notification when update is temporarily unavailable
return;
if ( !isset($_SESSION['testUpdate']) ) {
	print notificationBase::getMaintenanceResponse();
	exit;
}

