<?php

// 1st check if software is installed correct


// execute command
switch ($_REQUEST['detail']) {

	case 'registerForm': // this is a new installation
		print register::getRegisterFormResponse();
	break;

	case 'register':

		// register a demo version here
		$clientSerialFormatted = license::formatSerial($clientRequestVars['clientSerial']);

		$serialState = license::checkSerialState($clientSerialFormatted);

		switch ($serialState) {

			case 'ok':
				print register::getRegisterResponse( $clientSerialFormatted );
			break;
			default:
				/*
				 * possible responses
				 * 'notEnoughVersions'
				 * 'noVersion5'
				 * 'serialNotExist'
				 * 'noStratoIp'
				 * 'noWpolskaIp'
				 */
				print register::getRegisterFormErrorResponse($serialState);
			break;
		}
		installationLog::insertRegistrationEntry($serialState);
	break;


	case 'repeatRegistrationForm': // user has to repeat his registration
		print register::getRepeatRegistrationFormResponse();
	break;

	case 'repeatRegistration':

		// register a demo version here
		$clientSerialFormatted = license::formatSerial($clientRequestVars['clientSerial']);
		$serialState = license::checkSerialState($clientSerialFormatted);

		switch ($serialState) {

			case 'ok':
				print register::getRepeatRegistrationResponse( $clientSerialFormatted );
			break;
			default:
				/*
				 * possible responses
				 * 'notEnoughVersions'
				 * 'noVersion5'
				 * 'serialNotExist'
				 * 'noStratoIp'
				 * 'noWpolskaIp'
				 */
				print register::getRepeatRegistrationFormErrorResponse($serialState);
			break;
		}
		installationLog::insertRegistrationEntry($serialState);
	break;
}

?>