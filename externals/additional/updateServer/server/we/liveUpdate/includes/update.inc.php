<?php
/**
 * $Id: update.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
// execute command

switch($_REQUEST['detail']){

	case 'lookForUpdate':
		/*
		 * at least one Update exists, if its not a beta which needs to be updated and beta-switch is off
		 */

		updateUpdate::updateLogStart();
		if(($maxVersionNumber = updateUpdate::checkForUpdate())){
			$updateServerTemplateData['maxVersionNumber'] = $maxVersionNumber;

			// get all possible versions
			$possibleVersions = updateUpdate::getPossibleVersionsArray();
			$SubVersions = updateUpdate::getSubVersions();
			$AlphaBetaVersions = updateUpdate::getAlphaBetaVersions();
			$_SESSION['SubVersions'] = $SubVersions;
			$_SESSION['AlphaBetaVersions'] = $AlphaBetaVersions;
			foreach($possibleVersions as $key => &$value){
				$value = updateUpdate::getFormattedVersionString($key, true, false);
			}
			$updateServerTemplateData['possibleVersions'] = $possibleVersions;

			// get max version foreach language
			$versionLngs = updateUpdate::getVersionsLanguageArray(true);

			$lngVersions = array();
			foreach($versionLngs as $version => $lngArray){
				for($i = 0; $i < count($lngArray); $i++){
					if(!isset($lngVersions[$lngArray[$i]])){
						$lngVersions[$lngArray[$i]] = updateUtilUpdate::number2version($version);
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
				$_SESSION['clientSubVersionDB'] = updateUpdate::getSubVersion($_SESSION['clientVersionNumber']);
				$verlog['svnrevisionDB'] = $_SESSION['clientSubVersionDB'];
			} else {
				$verlog['svnrevisionDB'] = '';
			}

			updateUpdate::updateLogAvail($verlog);

			// is the update possible
			if(empty($possibleVersions)){
				print updateUpdate::getNoUpdateForLanguagesResponse();
				break;
			}
			if(isset($_SESSION['clientSubVersionDB']) && isset($_SESSION['clientSubVersion']) && $_SESSION['clientSubVersion'] < $_SESSION['clientSubVersionDB']){
				$SubVersions = updateUpdate::getSubVersions();
				$_SESSION['SubVersions'] = $SubVersions;
				$updateServerTemplateData['maxVersionNumber'] = updateUpdate::getMaxVersionNumber();
				print updateUpdate::getUpdateAvailableAfterRepeatResponse();
				break;
			}
			print updateUpdate::getUpdateAvailableResponse();
			break;
		}

		// no new version available -> print correct template
		$SubVersions = updateUpdate::getSubVersions();
		$_SESSION['SubVersions'] = $SubVersions;
		$updateServerTemplateData['maxVersionNumber'] = updateUpdate::getMaxVersionNumber();
		updateUpdate::updateLogAvail($updateServerTemplateData['maxVersionNumber']);

		print updateUpdate::getNoUpdateAvailableResponse();

		break;

	case 'confirmRepeatUpdate':
	case 'confirmUpdate':

		$_SESSION['clientTargetVersionNumber'] = $_REQUEST['clientTargetVersionNumber'];
		$_SESSION['clientTargetVersion'] = updateUtilUpdate::number2version($_REQUEST['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionName'] = updateUpdate::getVersionName($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetSubVersionNumber'] = updateUpdate::getSubVersion($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionType'] = updateUpdate::getVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionOnlyType'] = updateUpdate::getOnlyVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionOnlyTypeVersion'] = updateUpdate::getOnlyVersionTypeVersion($_SESSION['clientTargetVersionNumber']);

		$_SESSION['clientTargetFormattedVersionString'] = updateUpdate::getFormattedVersionString($_SESSION['clientTargetVersionNumber'], true, false);

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
			print updateUpdate::getConfirmRepeatUpdateResponse();
			break;
		}
		/*
		 * Normal update
		 */
		print updateUpdate::getConfirmUpdateResponse();
		break;

	case 'startRepeatUpdate':
	case 'startUpdate':
		// save all needed stuff in session here!
		$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];

		// start Update -> get the screen and start downloading the installer
		print installerUpdate::getInstallationScreenResponse();
		break;
	case 'getChanges':
		updateUpdate::updateLogTarget();
		// get all needed files for this update
		$_SESSION['clientChanges'] = updateUpdate::getChangesForUpdate();

		// all files are recognized, continue with next step
		print updateUpdate::getGetChangesResponse();
		break;

	case 'finishInstallation':
		//error_log('testFI '.print_r($_SESSION,true)); Wird scheinbar nicht aufgerufen
		// delete tmp dir and write new version number
		print updateUpdate::getFinishInstallationResponse();
		break;

	default:
		print notificationUpdate::getCommandNotKnownResponse();

		break;
}