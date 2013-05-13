<?php

// 1st check if software is installed correct
/*
if ( !(isset($_SESSION['clientInstalledTableId']) &&  $_SESSION['clientInstalledTableId']) ) {
	$_SESSION['clientInstalledTableId'] = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid'], false);
	
}

if( $_SESSION['clientInstalledTableId'] ) {
*/
	// execute command
	switch ($_REQUEST['detail']) {
		
		case 'lookForUpgrade':
			update::updateLogStart();
			//if (license::areInstalledModulesLicensed($_SESSION['clientInstalledTableId']) ) {
				
				// get all possible versions
				$possibleVersions = update::getPossibleVersionsArray();
				$SubVersions = update::getSubVersions();
				$AlphaBetaVersions = update::getAlphaBetaVersions();
				$_SESSION['SubVersions'] = $SubVersions;
				$maxVersionNumber = update::checkForUpdate();
				$verlog = array();
				$verlog['version'] = $maxVersionNumber;
				$verlog['svnrevision'] = $_SESSION['SubVersions'][$maxVersionNumber];
				$verlog['type'] = $AlphaBetaVersions[$maxVersionNumber]['type'];
				$verlog['versionBranch'] = $AlphaBetaVersions[$maxVersionNumber]['branch'];
				if (isset($_SESSION['clientSubVersion']) &&  $_SESSION['clientSubVersion'] !='0000' && $_SESSION['clientSubVersion'] !='' ){
					$_SESSION['clientSubVersionDB'] = update::getSubVersion($_SESSION['clientVersionNumber']); 
					$verlog['svnrevisionDB'] = $_SESSION['clientSubVersionDB'];
				} else {$verlog['svnrevisionDB']=='';}
				
				update::updateLogAvail($verlog);
				
				
				
				//error_log(print_r($possibleVersions,true));
				$updateServerTemplateData['possibleVersions'] = $possibleVersions;
				
				// get max version foreach language
				$versionLngs = update::getVersionsLanguageArray(true);
				
				$lngVersions = array();
				foreach ($versionLngs as $version => $lngArray) {
					
					for ($i=0;$i<sizeof($lngArray);$i++) {
						
						if (!isset($lngVersions[$lngArray[$i]])) {
							
							$lngVersions[$lngArray[$i]] = updateUtil::number2version($version);
						}
					}
				}
				
				$updateServerTemplateData['availableVersions'] = $lngVersions;
				$updateServerTemplateData['possibleVersions'] = $possibleVersions;
				//error_log(print_r($lngVersions,true));
				if (sizeof($possibleVersions)) {
					/*
					// has user enough licenses left?
					if(license::hasEnoughLicensesForUpgrade()) {
						
						print upgrade::getUpgradePossibleResponse();
						
					} else {
						
						print upgrade::getNotEnoughLicensesForUpgradeResponse();
					}
					*/
					print upgrade::getUpgradePossibleResponse();
					
				} else {
					
					print upgrade::getNoUpdateForLanguagesResponse();
				}
			/*
			} else {
				
				print upgrade::getRegisterBeforeUpgradeResponse();
			}
			*/
			break;
		
			
		case 'confirmUpgrade':
			// not needed here
			break;
			
			
		case 'startUpgrade':
			
			// save all needed stuff in session here!
			$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];
			
			$_SESSION['clientTargetVersionNumber'] = $_REQUEST['clientTargetVersionNumber'];
			$_SESSION['clientTargetSubVersionNumber'] = update::getSubVersion($_SESSION['clientTargetVersionNumber']);
			$_SESSION['clientTargetVersionType'] = update::getVersionType($_SESSION['clientTargetVersionNumber']);
			$_SESSION['clientTargetVersion'] = updateUtil::number2version($_REQUEST['clientTargetVersionNumber']);
			
			//installationLog::insertUpgradeEntry();
			
			//license::insertUpgradeInformation($_SESSION['clientInstalledTableId']);
			
			// start Update -> get the screen and start downloading the installer
			print installer::getInstallationScreenResponse();
			break;
		
			
		case 'getChanges':
			update::updateLogTarget();
			// get all needed files for this update
			$_SESSION['clientChanges'] = upgrade::getChangesForUpdate();
			
			// all files are recognized, continue with next step
			print update::getGetChangesResponse();
			break;
		
			
		case 'copyFiles':
			// copy webEdition files at correct places
			print upgrade::getCopyFilesResponse();
			break;
		
		
		case 'executePatches':
			// make nothing here
			print upgrade::getExecutePatchesResponse();
			break;
		
			
		case 'finishInstallation':
			// delete tmp dir and write new version number
			//installationLog::insertUpgradeEntry();
			print upgrade::getFinishInstallationResponse();
			break;
		
		case 'finishUpgradePopUp':
			print upgrade::getFinishUpgradePopUpResponse();
		break;
		
			
		default:
			print notification::getCommandNotKnownResponse();
			break;
		
	}
/*
} else { // this installation is not registered -> show reregistration formular
	
	print upgrade::getRegisterBeforeUpgradeResponse();
}
*/
?>