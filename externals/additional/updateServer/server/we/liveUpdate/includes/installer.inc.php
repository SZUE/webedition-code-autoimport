<?php
/**
 * $Id: installer.inc.php 13561 2017-03-13 13:40:03Z mokraemer $
 */
// 1st check if software is installed correct
// execute command


switch($_REQUEST['detail']){

	case 'confirmInstallation':

		// opens pop-up to confirm update.
		print installerUpdate::getConfirmInstallationResponse();

		break;

	case 'downloadInstaller':

		// as first step always load matching version of installer
		// this is simply one step

		print installerUpdate::getDownloadInstallerResponse();
		break;

	case 'downloadChanges':
		// start with downloading the files.
		// this is to check if current dl speed is too fast
		if(!isset($_SESSION['DOWNLOAD_KBYTES_PER_STEP'])){
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] = DOWNLOAD_KBYTES_PER_STEP;
		}
		if(isset($_REQUEST['decreaseSpeed']) && $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] > 100){
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] -= 100;
		}
		print installerUpdate::getDownloadChangesResponse();

		break;

	case 'updateDatabase':
		// this is to check if current query speed is too fast
		if(!isset($_SESSION['EXECUTE_QUERIES_PER_STEP'])){
			$_SESSION['EXECUTE_QUERIES_PER_STEP'] = EXECUTE_QUERIES_PER_STEP;
		}

		if(isset($_REQUEST['decreaseSpeed']) && $_SESSION['EXECUTE_QUERIES_PER_STEP'] > 1){
			$_SESSION['EXECUTE_QUERIES_PER_STEP'] --;
		}

		print installerUpdate::getUpdateDatabaseResponse();
		break;
	case 'copyFiles':
		// copy webEdition files at right position
		print installerUpdate::getCopyFilesResponse();
		break;

	case 'executePatches':
		// copy webEdition files at right position
		print installerUpdate::getExecutePatchesResponse();
		break;

	case 'finishInstallationPopUp':
		print installerUpdate::getFinishInstallationPopUpResponse();
		break;
}
