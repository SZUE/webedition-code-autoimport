<?php

// error logging
ini_set("log_errors",0);
//ini_set("log_errors",1);
//ini_set("error_reporting",E_ALL); 
//ini_set("error_log",$_SERVER["DOCUMENT_ROOT"]."/server/we/php_errors.log");
/*
error_log(print_r($_REQUEST,1));
if(isset($_REQUEST["reqArray"]) && !empty($_REQUEST["reqArray"])) {
	error_log(print_r(unserialize(base64_decode($_REQUEST["reqArray"])),1));
}
*/

// Set the current root directory
define("ROOT_DIR", dirname(__FILE__));

// set the shared directory
define("SHARED_DIR", $_SERVER["DOCUMENT_ROOT"] . '/server/lib/le');

// set the online installation directory
define("LIVEUPDATE_SERVER_DIR", ROOT_DIR . '/onlineInstallation');


// include all needed shared files
require_once(SHARED_DIR . '/includes/includes.inc.php');


/**
 * Prepare global needed Variables for the LiveUpdate
 */
require_once(SHARED_DIR . '/includes/init/handleSession.inc.php');


/**
 * Initialize the language (setting SHARED_LANGUGAE)
 */
require_once(SHARED_DIR . '/includes/init/initLanguage.inc.php');


// include languages
require_once(SHARED_LANGUAGE_DIR . '/' . SHARED_LANGUAGE . '.lang.php');

// include all needed online installation files
require_once(LIVEUPDATE_SERVER_DIR . '/includes/includes.inc.php');


/**
 * Establish connection to databases containing versioning and register
 * information
 */
require_once(SHARED_DIR . '/includes/init/establishDbConnection.inc.php');


/**
 * Prepare global needed Variables for the LiveUpdate
 */
require_once(SHARED_DIR . '/includes/init/prepareVariables.inc.php');


/**
 * check if it is test update
 */
require_once(SHARED_DIR . '/includes/init/checkTestVersion.inc.php');


/**
 * check if it is a beta version
 */
require_once(SHARED_DIR . '/includes/init/checkBetaVersion.inc.php');


/**
 * check if all is temporarily shut down
 */
require_once(SHARED_DIR . '/includes/init/checkTemporarilyShutDown.inc.php');


/**
 * check online installer version
 * it has to be at least 2.0.0.0 or newer 
 */
require_once(SHARED_DIR . '/includes/init/checkInstallerVersion.inc.php');


/**
 * Start handling the incoming commands
 */

$_SESSION['clientContent'] = true;

if (isset($_REQUEST['update_cmd'])) {

	// checkdatabases first
	if ($db_register_down || $db_versioning_down) {
		$_REQUEST['update_cmd'] = 'notification';
		$_REQUEST['detail'] = 'databaseFailure';
	}

	switch ($_REQUEST['update_cmd']) {


		case 'notification':
			require_once(SHARED_DIR . '/includes/notification.inc.php');
			break;


		case 'checkConnection':
			require_once(SHARED_DIR . '/includes/connection.inc.php');
			break;


		case 'feature':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/feature.inc.php');
			break;


		case "downloadInstaller":
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/downloadInstaller.inc.php');
			break;


		case "installApplication":
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/installApplication.inc.php');
			break;


		case 'community':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/community.inc.php');
			break;


		default:
			print notification::getNotAvailableAtTheMomentResponse();
			break;

	}


// check databases
} else {
	if ($db_register_down || $db_versioning_down) {
		include(SHARED_TEMPLATE_DIR . '/connection/serverDatabaseDown.inc.php');

	} else {
		include(SHARED_TEMPLATE_DIR . '/connection/serverUpAndRunning.inc.php');

	}

}

/*
if(isset($_REQUEST)) {
	error_log("---> REQUEST");
	error_log(print_r($_REQUEST, true));

}
*/
/*
if(isset($_SESSION)) {
	error_log("---> SESSION");
	error_log(print_r($_SESSION, true));

}
*/


?>
