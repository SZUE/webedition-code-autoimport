<?php
// 1st check if software is installed correct
// not needed any more
/*

if ( !(isset($_SESSION['clientInstalledTableId']) &&  $_SESSION['clientInstalledTableId']) ) {
	
	$_SESSION['clientInstalledTableId'] = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid']);
}

if( $_SESSION['clientInstalledTableId'] ) {
*/	
	// execute command
	
	switch ($_REQUEST['detail']) {
		
		case 'lookForUpdate':
			// no need for license checks in webEdition OSS any more:
			//if (license::areInstalledModulesLicensed($_SESSION['clientInstalledTableId']) ) {
				
				/*
				 * at least one Update exists, if its not a beta which needs to be updated and beta-switch is off
				 */

				update::updateLogStart();
				if ($maxVersionNumber = update::checkForUpdate()) {
					
					$updateServerTemplateData['maxVersionNumber'] = $maxVersionNumber;
					
					// get all possible versions
					$possibleVersions = update::getPossibleVersionsArray();
					$SubVersions = update::getSubVersions();
					$AlphaBetaVersions = update::getAlphaBetaVersions();
					$_SESSION['SubVersions'] = $SubVersions;
					$_SESSION['AlphaBetaVersions'] = $AlphaBetaVersions;
					foreach($possibleVersions as $key => &$value){
						$value = update::getFormattedVersionString($key, true, false);
					}
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
					$updateServerTemplateData['xyzVersions'] = $versionLngs;
					$verlog = array();
					$verlog['version'] = $updateServerTemplateData['maxVersionNumber'];
					$verlog['svnrevision'] = $_SESSION['SubVersions'][$updateServerTemplateData['maxVersionNumber']];
					$verlog['type'] = $_SESSION['AlphaBetaVersions'][$updateServerTemplateData['maxVersionNumber']]['type'];
					$verlog['versionBranch'] = $_SESSION['AlphaBetaVersions'][$updateServerTemplateData['maxVersionNumber']]['branch'];
					if (isset($_SESSION['clientSubVersion']) &&  $_SESSION['clientSubVersion'] !='0000' && $_SESSION['clientSubVersion'] !='' ){
						$_SESSION['clientSubVersionDB'] = update::getSubVersion($_SESSION['clientVersionNumber']); 
						$verlog['svnrevisionDB'] = $_SESSION['clientSubVersionDB'];
					} else {$verlog['svnrevisionDB']=='';}
					
					update::updateLogAvail($verlog);
					//error_log($maxVersionNumber);
					//error_log(print_r($_SESSION,true));
					//error_log(print_r($possibleVersions,true));
					// is the update possible
					if (sizeof($possibleVersions) < 1) {
						
						print update::getNoUpdateForLanguagesResponse();
						
					} else {
						if (isset($_SESSION['clientSubVersionDB']) && isset($_SESSION['clientSubVersion']) && $_SESSION['clientSubVersion'] < $_SESSION['clientSubVersionDB']){
							$SubVersions = update::getSubVersions();
							$_SESSION['SubVersions'] = $SubVersions;
							$updateServerTemplateData['maxVersionNumber'] = update::getMaxVersionNumber();
							print update::getUpdateAvailableAfterRepeatResponse();
						} else {
							print update::getUpdateAvailableResponse();
						}
					}
					
				} else {
					
					// no new version available -> print correct template
					$SubVersions = update::getSubVersions();
					$_SESSION['SubVersions'] = $SubVersions;
					$updateServerTemplateData['maxVersionNumber'] = update::getMaxVersionNumber();
					update::updateLogAvail($updateServerTemplateData['maxVersionNumber']);
					
					print update::getNoUpdateAvailableResponse();
				}
			/*
			} else {
				
				print register::getRepeatRegistrationFormResponse();
			}
			*/
		break;
		
		case 'confirmRepeatUpdate':
		case 'confirmUpdate':
			
			$_SESSION['clientTargetVersionNumber'] = $_REQUEST['clientTargetVersionNumber'];
			$_SESSION['clientTargetVersion'] = updateUtil::number2version($_REQUEST['clientTargetVersionNumber']);
			$_SESSION['clientTargetSubVersionNumber'] = update::getSubVersion($_SESSION['clientTargetVersionNumber']);
			$_SESSION['clientTargetVersionType'] = update::getVersionType($_SESSION['clientTargetVersionNumber']);
			$_SESSION['clientTargetVersionOnlyType'] = update::getOnlyVersionType($_SESSION['clientTargetVersionNumber']);
			$_SESSION['clientTargetVersionOnlyTypeVersion'] = update::getOnlyVersionTypeVersion($_SESSION['clientTargetVersionNumber']);

			$Request = unserialize(base64_decode($_REQUEST['reqArray']));
			
			if (isset($Request['clientPhpVersion']) && $Request['clientPhpVersion']!=''){
				$_SESSION['clientPhpVersion'] = $Request['clientPhpVersion'];
			}
			if (isset($Request['clientPcreVersion']) && $Request['clientPcreVersion']!=''){
				$_SESSION['clientPcreVersion'] = $Request['clientPcreVersion'];
			}
			if (isset($Request['clientPhpExtensions']) && $Request['clientPhpExtensions']!=''){
				$tmp=@unserialize(@base64_decode($Request['clientPhpExtensions']));
 				if($tmp===false){
 					$_SESSION['clientPhpExtensions'] = $Request['clientPhpExtensions'];
 				} else {
					$_SESSION['clientPhpExtensions'] = implode(',',$tmp);
				}
				
			}
			if (isset($Request['clientMySQLVersion']) && $Request['clientMySQLVersion']!=''){
				$_SESSION['clientMySQLVersion'] = $Request['clientMySQLVersion'];
			}
			
			if ($_SESSION['clientTargetVersionNumber'] == $_SESSION['clientVersionNumber']) {
				/*
				 * Repeat the update
				 */
				print update::getConfirmRepeatUpdateResponse();
			} else {
				/*
				 * Normal update
				 */
				print update::getConfirmUpdateResponse();
			}
			
		break;
		
		case 'startRepeatUpdate':
		case 'startUpdate':
			
			//installationLog::insertUpdateEntry();
			
			// save all needed stuff in session here!
			$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];
			
			// start Update -> get the screen and start downloading the installer
			print installer::getInstallationScreenResponse();
			
		break;
		
		case 'getChanges':
			
			update::updateLogTarget();
			// get all needed files for this update
			$_SESSION['clientChanges'] = update::getChangesForUpdate();
			
			// all files are recognized, continue with next step
			print update::getGetChangesResponse();
		break;
		
		case 'finishInstallation':
			//error_log('testFI '.print_r($_SESSION,true)); Wird scheinbar nicht aufgerufen 
			//installationLog::insertUpdateEntry();
			
			// delete tmp dir and write new version number
			print update::getFinishInstallationResponse();
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