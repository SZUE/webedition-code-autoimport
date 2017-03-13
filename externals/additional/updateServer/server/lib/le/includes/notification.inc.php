<?php
/**
 * $Id$
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

