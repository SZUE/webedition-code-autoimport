<?php
/**
 * $Id$
 */
// execute command
switch($_REQUEST['detail']){

	case 'lostSession':
		print notificationBase::getLostSessionResponse();
		break;

	case 'databaseFailure':
		print notificationBase::getDatabaseFailureResponse();
		break;

	default:
		print notificationBase::getCommandNotKnownResponse();
		break;
}

