<?php
/*
// 1st check if software is installed correct
if ( !(isset($_SESSION['clientInstalledTableId']) &&  $_SESSION['clientInstalledTableId']) ) {
	
	$_SESSION['clientInstalledTableId'] = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid']);
}

if( $_SESSION['clientInstalledTableId'] ) {
	*/
	// execute command
	switch ($_REQUEST['detail']) {
		
		case 'showLanguages':
		case 'selectLanguages':
			/*
			if (license::areInstalledModulesLicensed($_SESSION['clientInstalledTableId']) ) {
				
				print languages::getSelectLanguagesResponse();
				
			} else {
				print register::getRepeatRegistrationFormResponse();
			}
			*/
			print languages::getSelectLanguagesResponse();
		break;
		
		case 'confirmLanguages':
			
			$desiredLanguages = array();
			foreach ($clientRequestVars as $key => $value) {
				if (strpos($key, 'lng_') === 0) { // prefix needed for checkbox function (safari)
					$desiredLanguages[] = substr($key, 4);
				}
			}
			
			if (sizeof($desiredLanguages)) {
				
				$_SESSION['clientDesiredLanguages'] = $desiredLanguages;
				print languages::getConfirmLanguagesResponse();
				
			} else {
				
				print languages::getNoLanguageSelectedResponse();
			}
		break;
		
		case 'startUpdate':
			
			//installationLog::insertLanguagesEntry();
			
			$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];
			// start Update -> get the screen and start downloading the installer
			print installer::getInstallationScreenResponse();

		break;
		
		case 'getChanges':
			
			// get all needed files for this update
			$_SESSION['clientChanges'] = languages::getChangesForUpdate();
			
			// all files are recognized, continue with next step
			print languages::getGetChangesResponse();
			
		break;
		
		case 'finishInstallation':
			// delete tmp dir and write new version number
			//installationLog::insertLanguagesEntry();
			print languages::getFinishInstallationResponse();
		break;
		
		default:
			print notification::getCommandNotKnownResponse();
		break;
		
	}
	/*
} else { // this installation is not registered -> show reregistration formular
	
	print register::getRepeatRegistrationFormResponse();
}
*/
?>