<?php

/**
 * temporarly shut down - preparations for wE4
 *
 */

// use this template to print a notification when update is temporarily unavailable
/*
if ( !isset($_SESSION['testUpdate']) ) {
	print notification::getMaintenanceResponse();
	exit;
}
*/

// use this template to print a notification with a link to the sourceforge download page:
/*
print notification::getHighloadSourceforgeResponse();
exit;
*/
// or without a link to sourceforge:
/*
print notification::getHighloadResponse();
exit;
*/
?>