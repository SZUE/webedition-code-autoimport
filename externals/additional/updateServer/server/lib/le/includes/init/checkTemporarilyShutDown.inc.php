<?php
/**
 * $Id$
 */
/**
 * temporarly shut down - preparations for wE4
 *
 */

// use this template to print a notification when update is temporarily unavailable
return;
if ( !isset($_SESSION['testUpdate']) ) {
	print notification::getMaintenanceResponse();
	exit;
}

