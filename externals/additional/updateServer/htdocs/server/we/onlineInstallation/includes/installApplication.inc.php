<?php

$installer = new installApplication();


// execute command
switch ($_REQUEST['detail']) {

	// delete tables in this step and move folder
	case 'prepareApplicationInstallation':

		print $installer->getPrepareApplicationInstallationResponse();
		
		break;


	case 'determineApplicationFiles':

		// get files for that version
		$_SESSION["clientChanges"] = $installer->getApplicationFiles();

		print $installer->getApplicationFilesResponse();
		
		break;


	case 'downloadApplicationFiles':

		// this is to check if current dl speed is too fast
		if (!isset($_SESSION['DOWNLOAD_KBYTES_PER_STEP'])) {
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] = DOWNLOAD_KBYTES_PER_STEP;

		}
		if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] > 100) {
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] -= 100;
			
		}
		print $installer->getDownloadChangesResponse();
		
		break;


	case 'updateApplicationDatabase':

		// this is to check if current query speed is too fast
		if (!isset($_SESSION['EXECUTE_QUERIES_PER_STEP'])) {
			$_SESSION['EXECUTE_QUERIES_PER_STEP'] = EXECUTE_QUERIES_PER_STEP;

		}

		if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['EXECUTE_QUERIES_PER_STEP'] > 1) {
			$_SESSION['EXECUTE_QUERIES_PER_STEP']--;

		}

		print $installer->getUpdateApplicationDatabaseResponse();
		
		break;


	case 'prepareApplicationFiles':

		// this is to check if current preparation speed is too fast
		if (!isset($_SESSION['PREPARE_FILES_PER_STEP'])) {
			$_SESSION['PREPARE_FILES_PER_STEP'] = PREPARE_FILES_PER_STEP;

		}

		if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['PREPARE_FILES_PER_STEP'] > 40) {
			$_SESSION['PREPARE_FILES_PER_STEP'] -= 20;

		}

		// prepare changes - adjust tablenames and prefix
		print $installer->prepareApplicationFilesResponse();
		
		break;


	case 'copyApplicationFiles':

		print $installer->getCopyApplicationFilesResponse();
		
		break;


	case 'writeApplicationConfiguration':

		update::installLogStart();
		print $installer->getWriteApplicationConfigurationResponse();
		
		break;


	default:

		print notification::getCommandNotKnownResponse();
		
		break;

}

?>