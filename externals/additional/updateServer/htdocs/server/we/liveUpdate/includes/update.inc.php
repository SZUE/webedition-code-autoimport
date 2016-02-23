<?php
// execute command

switch($_REQUEST['detail']){

	case 'lookForUpdate':
		/*
		 * at least one Update exists, if its not a beta which needs to be updated and beta-switch is off
		 */

		update::updateLogStart();
		if(($maxVersionNumber = update::checkForUpdate())){
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
			foreach($versionLngs as $version => $lngArray){
				for($i = 0; $i < sizeof($lngArray); $i++){
					if(!isset($lngVersions[$lngArray[$i]])){
						$lngVersions[$lngArray[$i]] = updateUtil::number2version($version);
					}
				}
			}

			$updateServerTemplateData['availableVersions'] = $lngVersions;
			$updateServerTemplateData['xyzVersions'] = $versionLngs;
			$verlog = array(
				'version' => $updateServerTemplateData['maxVersionNumber'],
				'svnrevision' => $_SESSION['SubVersions'][$updateServerTemplateData['maxVersionNumber']],
				'type' => $_SESSION['AlphaBetaVersions'][$updateServerTemplateData['maxVersionNumber']]['type'],
				'versionBranch' => $_SESSION['AlphaBetaVersions'][$updateServerTemplateData['maxVersionNumber']]['branch'],
			);
			if(isset($_SESSION['clientSubVersion']) && $_SESSION['clientSubVersion'] != '0000' && $_SESSION['clientSubVersion'] != ''){
				$_SESSION['clientSubVersionDB'] = update::getSubVersion($_SESSION['clientVersionNumber']);
				$verlog['svnrevisionDB'] = $_SESSION['clientSubVersionDB'];
			} else {
				$verlog['svnrevisionDB'] == '';
			}

			update::updateLogAvail($verlog);
			//error_log($maxVersionNumber);
			//error_log(print_r($_SESSION,true));
			//error_log(print_r($possibleVersions,true));
			// is the update possible
			if(sizeof($possibleVersions) < 1){
				print update::getNoUpdateForLanguagesResponse();
				break;
			}
			if(isset($_SESSION['clientSubVersionDB']) && isset($_SESSION['clientSubVersion']) && $_SESSION['clientSubVersion'] < $_SESSION['clientSubVersionDB']){
				$SubVersions = update::getSubVersions();
				$_SESSION['SubVersions'] = $SubVersions;
				$updateServerTemplateData['maxVersionNumber'] = update::getMaxVersionNumber();
				print update::getUpdateAvailableAfterRepeatResponse();
				break;
			}
			print update::getUpdateAvailableResponse();
			break;
		}

		// no new version available -> print correct template
		$SubVersions = update::getSubVersions();
		$_SESSION['SubVersions'] = $SubVersions;
		$updateServerTemplateData['maxVersionNumber'] = update::getMaxVersionNumber();
		update::updateLogAvail($updateServerTemplateData['maxVersionNumber']);

		print update::getNoUpdateAvailableResponse();

		break;

	case 'confirmRepeatUpdate':
	case 'confirmUpdate':

		$_SESSION['clientTargetVersionNumber'] = $_REQUEST['clientTargetVersionNumber'];
		$_SESSION['clientTargetVersion'] = updateUtil::number2version($_REQUEST['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionName'] = update::getVersionName($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetSubVersionNumber'] = update::getSubVersion($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionType'] = update::getVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionOnlyType'] = update::getOnlyVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionOnlyTypeVersion'] = update::getOnlyVersionTypeVersion($_SESSION['clientTargetVersionNumber']);

		$_SESSION['clientTargetFormattedVersionString'] = update::getFormattedVersionString($_SESSION['clientTargetVersionNumber'], true, false);

		$Request = unserialize(base64_decode($_REQUEST['reqArray']));

		if(!empty($Request['clientPhpVersion'])){
			$_SESSION['clientPhpVersion'] = $Request['clientPhpVersion'];
		}
		if(!empty($Request['clientPcreVersion'])){
			$_SESSION['clientPcreVersion'] = $Request['clientPcreVersion'];
		}
		if(!empty($Request['clientPhpExtensions'])){
			$tmp = unserialize(base64_decode($Request['clientPhpExtensions']));
			$_SESSION['clientPhpExtensions'] = ($tmp === false ? $Request['clientPhpExtensions'] : implode(',', $tmp));
		}
		if(!empty($Request['clientMySQLVersion'])){
			$_SESSION['clientMySQLVersion'] = $Request['clientMySQLVersion'];
		}

		if($_SESSION['clientTargetVersionNumber'] == $_SESSION['clientVersionNumber']){
			/*
			 * Repeat the update
			 */
			print update::getConfirmRepeatUpdateResponse();
			break;
		}
		/*
		 * Normal update
		 */
		print update::getConfirmUpdateResponse();
		break;

	case 'startRepeatUpdate':
	case 'startUpdate':

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
		// delete tmp dir and write new version number
		print update::getFinishInstallationResponse();
		break;

	default:
		print notification::getCommandNotKnownResponse();

		break;
}