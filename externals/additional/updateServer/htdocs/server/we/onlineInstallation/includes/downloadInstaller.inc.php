<?php

$downloadInstaller = new installerDownload();

// execute command
switch ($_REQUEST['detail']) {


	case 'determineInstallerFiles':

		// get files for application specific online installer
		$_SESSION["clientChanges"] = $downloadInstaller->getInstallerFiles();
		print $downloadInstaller->getGetInstallerFilesResponse();

		break;


	case "downloadInstallerFiles":
		// this is to check if current dl speed is too fast
		if (!isset($_SESSION['DOWNLOAD_KBYTES_PER_STEP'])) {
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] = DOWNLOAD_KBYTES_PER_STEP;

		}
		if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] > 100) {
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] -= 100;
			
		}
		print $downloadInstaller->getDownloadChangesResponse();

		break;


	case "prepareInstallerFiles":

		// this is to check if current preparation speed is too fast
		if (!isset($_SESSION['PREPARE_FILES_PER_STEP'])) {
			$_SESSION['PREPARE_FILES_PER_STEP'] = PREPARE_FILES_PER_STEP;

		}

		if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['PREPARE_FILES_PER_STEP'] > 40) {
			$_SESSION['PREPARE_FILES_PER_STEP'] -= 20;

		}

		// prepare changes - adjust tablenames and prefix
		print $downloadInstaller->getPrepareInstallerFilesResponse();

		break;


	case "copyInstallerFiles":

		print $downloadInstaller->getCopyFilesResponse();

		break;


	default:

		print notification::getCommandNotKnownResponse();

		break;

}

?>