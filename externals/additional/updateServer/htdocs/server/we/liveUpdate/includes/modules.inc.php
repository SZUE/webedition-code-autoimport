<?php

// 1st check if software is installed correct
/*
if ( !(isset($_SESSION['clientInstalledTableId']) &&  $_SESSION['clientInstalledTableId']) ) {
	
	$_SESSION['clientInstalledTableId'] = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid']);
}

if( $_SESSION['clientInstalledTableId'] ) {
	*/
	// execute command
	switch ($_REQUEST['detail']) {
		
		case 'selectModules':
			
			/*
			if (license::areInstalledModulesLicensed($_SESSION['clientInstalledTableId']) ) {
				
				print modules::getSelectModulesResponse();
				
			} else {
				
				print register::getRepeatRegistrationFormResponse();
			}
			*/
			print modules::getSelectModulesResponse();
			
		break;
		
		case 'confirmModules':
			
			// check if modules are selected
			$desiredModules = array();
			foreach ($clientRequestVars as $key => $value) {
				if (strpos($key, 'module_') === 0) { // prefix needed for checkbox function (safari)
					$desiredModules[] = substr($key, 7);
				}
			}
			
			if ( sizeof($desiredModules) ) {
				
				// check if owns eough licenses // check if module combination is allowed
				/*
				if ( license::hasLicensesForDesiredModules($desiredModules) && modules::isDesiredModuleCombinationAllowed($desiredModules) ) {
					
					$_SESSION['clientDesiredModules'] = $desiredModules;
					
					print modules::getConfirmModulesResponse();
				} else {
					// desired modules is not saved in session yet
					print modules::getReselectModulesResponse($desiredModules);
				}
				*/
				$_SESSION['clientDesiredModules'] = $desiredModules;
				print modules::getConfirmModulesResponse();
				
			} else {
				
				print modules::getNoModulesSelectedResponse();
			}
		break;
		
		case 'startUpdate':
			
			if ( license::hasLicensesForDesiredModules($_SESSION['clientDesiredModules']) ) {
				
				// save new modules in domain
				license::insertNewModules($_SESSION['clientDesiredModules']);
				
				$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];
				
				//installationLog::insertModulesEntry();
				
				// start Update -> get the screen and start downloading the installer
				print installer::getInstallationScreenResponse();
				
			} else {
				print modules::getReselectModulesResponse();
			}
			
			// save all needed stuff in session here!
			
			break;
		
		case 'getChanges':
			
			// get all needed files for this update
			$_SESSION['clientChanges'] = modules::getChangesForUpdate();
			
			// all files are recognized, continue with next step
			print modules::getGetChangesResponse();
			
		break;
		
		case 'finishInstallation':
			// delete tmp dir and write new version number
			
			//installationLog::insertModulesEntry();
			
			print modules::getFinishInstallationResponse();
		break;
		
		default:
			print notification::getCommandNotKnownResponse();
		break;
		
	}/*
	
} else { // this installation is not registered -> show reregistration formular
	
	print register::getRepeatRegistrationFormResponse();
}
*/
?>