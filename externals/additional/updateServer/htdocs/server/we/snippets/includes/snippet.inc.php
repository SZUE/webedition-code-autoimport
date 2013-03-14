<?php

$snippet = new downloadSnippet();

// execute command
switch ($_REQUEST['detail']) {


	case 'overview':

		if(!isset($GLOBALS['clientRequestVars']['ImportType'])) {
			// import type not set
			print notification::getNoImportTypeSetResponse();
			
		} else {
			$_SESSION['clientImportType'] = $GLOBALS['clientRequestVars']['ImportType'];
			// get files for application specific online installer
			print $snippet->getGetOverviewResponse();
		
		}

		break;


	case 'registerImport':

		// get files for application specific online installer
		$import = isset($GLOBALS['clientRequestVars']['import']) ? $GLOBALS['clientRequestVars']['import'] : '';
		print $snippet->getGetRegisterImportResponse($import);

		break;


	case 'determineFiles':
		// get files for application specific online installer
		$_SESSION["clientChanges"] = $snippet->getFiles();
		print $snippet->getGetFilesResponse();

		break;


	case "downloadFiles":
		// this is to check if current dl speed is too fast
		if (!isset($_SESSION['DOWNLOAD_KBYTES_PER_STEP'])) {
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] = DOWNLOAD_KBYTES_PER_STEP;

		}
		if (isset($_REQUEST['decreaseSpeed']) && $_SESSION['DOWNLOAD_KBYTES_PER_STEP'] > 100) {
			$_SESSION['DOWNLOAD_KBYTES_PER_STEP'] -= 100;
			
		}
		print $snippet->getDownloadChangesResponse();

		break;


	default:

		print notification::getCommandNotKnownResponse();

		break;

}

?>