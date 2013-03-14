<?php

// execute command
switch ($_REQUEST['detail']) {

	// get form with all possible languages
	case 'languagesForm':
		print languages::getLanguagesFormResponse();
		break;


	// save chosen languages in session
	case 'registerLanguages':
		$defaultLanguage = $clientRequestVars['le_defaultLanguage'];
		$extraLanguages = array();

		if(isset($clientRequestVars['le_extraLanguages'])) {
			$extraLanguages = $clientRequestVars['le_extraLanguages'];

		}

		print languages::getRegisterLanguagesResponse($defaultLanguage, $extraLanguages);
		break;


	// get form with all versions
	case 'versionForm':

		print license::getVersionFormResponse();
		break;


	// save the version in session
	case 'registerVersion':
		print license::getRegisterVersionResponse($clientRequestVars['le_version']);
		break;


	// show the form for the serial number
	case 'serialForm':
error_log("serialForm");
		unset($_SESSION['clientChanges']);
		print register::getRegisterFormResponse();
		break;


	// skip the serial information
	case 'skipSerial':
error_log("skipSerial");
		print register::getDontRegisterResponse();
		break;

	// check the serial and save into session if the serial is correct
	case 'checkSerial':
error_log("checkSerial");
		$clientSerialFormatted = license::formatSerial($clientRequestVars['clientSerial']);
		$serialState = license::checkSerialState($clientSerialFormatted);

		if(isset($_SESSION['clientSerial'])) {
			if($clientSerialFormatted != $_SESSION['clientSerial']) {
				unset($_SESSION['clientDesiredModules']);
				unset($_SESSION['existingModules']);

			}

		}
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
		break;


	// get form with all possible modules
	case 'modulesForm':

		print modules::getModulesFormResponse();
		break;


	// save chosen modules in session
	case 'registerModules':

		$modules = array();
		if(isset($clientRequestVars['le_modules'])) {
			$modules = $clientRequestVars['le_modules'];

		}

		print modules::getRegisterModulesResponse($modules);
		break;


	// get form with all snippets
	case 'snippetsForm':
		print snippets::getSnippetsFormResponse();
		break;

}

?>