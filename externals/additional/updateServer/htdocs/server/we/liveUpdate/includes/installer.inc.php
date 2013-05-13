<?php

// 1st check if software is installed correct
/*
if ( !(isset($_SESSION['clientInstalledTableId']) &&  $_SESSION['clientInstalledTableId']) ) {
	
	$_SESSION['clientInstalledTableId'] = license::getDomainId($_SESSION['clientDomain'], $_SESSION['clientUid']);
}
*/
// execute command
//if (isset($_SESSION['clientInstalledTableId'])) {
	
	
	switch ($_REQUEST['detail']) {
	
		case 'confirmInstallation':
			
			// opens pop-up to confirm update.
			print installer::getConfirmInstallationResponse();
			
		break;
		
		case 'downloadInstaller':
			
			// as first step always load matching version of installer
			// this is simply one step
			
			print installer::getDownloadInstallerResponse();
		break;
		
		case 'downloadChanges':
			// start with downloading the files.
			
			// this is to check if current dl speed is too fast
			if (!isset($_SESSION['DOWNLOAD_KBYTES_PER_STEP'])) {
				$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] = DOWNLOAD_KBYTES_PER_STEP;
	
			}
			if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] > 100) {
				$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] -= 100;
				
			}
			print installer::getDownloadChangesResponse();
			
		break;
		
		case 'updateDatabase':
			
			// this is to check if current query speed is too fast
			if (!isset($_SESSION['EXECUTE_QUERIES_PER_STEP'])) {
				$_SESSION['EXECUTE_QUERIES_PER_STEP'] = EXECUTE_QUERIES_PER_STEP;
			}
			
			if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['EXECUTE_QUERIES_PER_STEP'] > 1) {
				$_SESSION['EXECUTE_QUERIES_PER_STEP']--;
			}
			
			print installer::getUpdateDatabaseResponse();
		break;
		
		case 'prepareChanges':
			
			// this is to check if current preparation speed is too fast
			if (!isset($_SESSION['PREPARE_FILES_PER_STEP'])) {
				$_SESSION['PREPARE_FILES_PER_STEP'] = PREPARE_FILES_PER_STEP;
			}
			
			if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['PREPARE_FILES_PER_STEP'] > 40) {
				$_SESSION['PREPARE_FILES_PER_STEP'] -= 20;
			}
			
			// prepare changes - adjust tablenames and prefix
			print installer::getPrepareChangesResponse();
		break;
		
		case 'copyFiles':
			// copy webEdition files at right position
			print installer::getCopyFilesResponse();
		break;
		
		case 'executePatches':
			// copy webEdition files at right position
			print installer::getExecutePatchesResponse();
		break;
		
		case 'finishInstallationPopUp':
			print installer::getFinishInstallationPopUpResponse();
		break;
	}
/*	
} else {
	
}
*/


?>