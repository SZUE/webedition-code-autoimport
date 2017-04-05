<?php
/**
 * $Id: notification.inc.php 13564 2017-03-13 17:13:40Z mokraemer $
 */
// execute command
switch($_REQUEST['detail']){

	case 'lostSession':
		echo notificationBase::getLostSessionResponse();
		break;

	case 'databaseFailure':
		echo notificationBase::getDatabaseFailureResponse();
		break;

	default:
		echo notificationBase::getCommandNotKnownResponse();
		break;
}

