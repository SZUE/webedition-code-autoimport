<?php
/**
 * $Id: upgrade.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
// execute command
switch($_REQUEST['detail']){

	case 'lookForUpgrade':
		updateUpdate::updateLogStart();
		// get all possible versions
		$possibleVersions = updateUpdate::getPossibleVersionsArray();
		$SubVersions = updateUpdate::getSubVersions();
		$AlphaBetaVersions = updateUpdate::getAlphaBetaVersions();
		$_SESSION['SubVersions'] = $SubVersions;
		$maxVersionNumber = updateUpdate::checkForUpdate();
		$verlog = array(
			'version' => $maxVersionNumber,
			'svnrevision' => $_SESSION['SubVersions'][$maxVersionNumber],
			'type' => $AlphaBetaVersions[$maxVersionNumber]['type'],
			'versionBranch' => $AlphaBetaVersions[$maxVersionNumber]['branch'],
		);
		if(isset($_SESSION['clientSubVersion']) && $_SESSION['clientSubVersion'] != '0000' && $_SESSION['clientSubVersion'] != ''){
			$_SESSION['clientSubVersionDB'] = updateUpdate::getSubVersion($_SESSION['clientVersionNumber']);
			$verlog['svnrevisionDB'] = $_SESSION['clientSubVersionDB'];
		} else {
			$verlog['svnrevisionDB'] == '';
		}

		updateUpdate::updateLogAvail($verlog);



		//error_log(print_r($possibleVersions,true));
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
		$updateServerTemplateData['possibleVersions'] = $possibleVersions;
		//error_log(print_r($lngVersions,true));
		if(!empty($possibleVersions)){
			print upgradeUpdate::getUpgradePossibleResponse();
		} else {

			print upgradeUpdate::getNoUpdateForLanguagesResponse();
		}
		break;


	case 'confirmUpgrade':
		// not needed here
		break;


	case 'startUpgrade':

		// save all needed stuff in session here!
		$_SESSION['update_cmd'] = $_REQUEST['update_cmd'];

		$_SESSION['clientTargetVersionNumber'] = $_REQUEST['clientTargetVersionNumber'];
		$_SESSION['clientTargetSubVersionNumber'] = updateUpdate::getSubVersion($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersionType'] = updateUpdate::getVersionType($_SESSION['clientTargetVersionNumber']);
		$_SESSION['clientTargetVersion'] = updateUtilUpdate::number2version($_REQUEST['clientTargetVersionNumber']);

		// start Update -> get the screen and start downloading the installer
		print installerUpdate::getInstallationScreenResponse();
		break;


	case 'getChanges':
		updateUpdate::updateLogTarget();
		// get all needed files for this update
		$_SESSION['clientChanges'] = upgradeUpdate::getChangesForUpdate();

		// all files are recognized, continue with next step
		print updateUpdate::getGetChangesResponse();
		break;


	case 'copyFiles':
		// copy webEdition files at correct places
		print upgradeUpdate::getCopyFilesResponse();
		break;


	case 'executePatches':
		// make nothing here
		print upgradeUpdate::getExecutePatchesResponse();
		break;


	case 'finishInstallation':
		// delete tmp dir and write new version number
		print upgradeUpdate::getFinishInstallationResponse();
		break;

	case 'finishUpgradePopUp':
		print upgradeUpdate::getFinishUpgradePopUpResponse();
		break;


	default:
		print notificationUpdate::getCommandNotKnownResponse();
		break;
}
