<?php
/**
 * $Id$
 */
// execute command
switch($_REQUEST['detail']){

	case 'showLanguages':
	case 'selectLanguages':
		print languagesUpdate::getSelectLanguagesResponse();
		break;

	case 'confirmLanguages':

		$desiredLanguages = array();
		foreach($clientRequestVars as $key => $value){
			if(strpos($key, 'lng_') === 0){ // prefix needed for checkbox function (safari)
				$desiredLanguages[] = substr($key, 4);
			}
		}

		if(!empty($desiredLanguages)){

			$_SESSION['clientDesiredLanguages'] = $desiredLanguages;
			print languagesUpdate::getConfirmLanguagesResponse();
		} else {

			print languagesUpdate::getNoLanguageSelectedResponse();
		}
		break;

	case 'startUpdate':

		$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];
		// start Update -> get the screen and start downloading the installer
		print installerUpdate::getInstallationScreenResponse();

		break;

	case 'getChanges':

		// get all needed files for this update
		$_SESSION['clientChanges'] = languagesUpdate::getChangesForUpdate();

		// all files are recognized, continue with next step
		print languagesUpdate::getGetChangesResponse();

		break;

	case 'finishInstallation':
		// delete tmp dir and write new version number
		print languagesUpdate::getFinishInstallationResponse();
		break;

	default:
		print notificationUpdate::getCommandNotKnownResponse();
		break;
}
