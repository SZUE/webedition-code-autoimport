<?php

// execute command
switch ($_REQUEST['detail']) {
	
	case 'lostSession':
		print notification::getLostSessionResponse();
	break;
	
	case 'databaseFailure':
		print notification::getDatabaseFailureResponse();
	break;
	
	default:
		print notification::getCommandNotKnownResponse();
	break;
}

?>