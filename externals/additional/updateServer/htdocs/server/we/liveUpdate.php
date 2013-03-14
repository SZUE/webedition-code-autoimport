<?php

// error logging
//ini_set("log_errors",1);
ini_set("log_errors",0);
//ini_set("error_reporting",E_ALL); 
ini_set("error_log",$_SERVER["DOCUMENT_ROOT"]."/php_errors.log.php");

// Set the current root directory
define("ROOT_DIR", dirname(__FILE__));

// set the shared directory
define("SHARED_DIR", $_SERVER["DOCUMENT_ROOT"] . '/server/lib/le');

// set the shared directory
define("LIVEUPDATE_SERVER_DIR", ROOT_DIR . '/liveUpdate');


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

// include all needed live update files
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
 * display important announcements:
 */
/*
if ( !isset($_SESSION['testUpdate']) ) {
	print notificationBase::getAnnouncementResponse();
}
*/
//require_once(SHARED_DIR . '/includes/init/displayAnnouncement.inc.php');

/**
 * Start handling the incoming commands
 */
if (isset($_REQUEST['update_cmd'])) {
	// show global announcement on every liveUpdate page using $GLOBALS['lang']['notification']['importantAnnouncement']
	//$_showCommands = array("lookForUpgrade", "lookForUpdate", "confirmLanguages", "selectLanguages");
	$_showCommands = array();
	if(isset($_REQUEST['detail']) && in_array($_REQUEST['detail'],$_showCommands)) {
		$_SESSION["displayAnnouncement"] = true;
	} else {
		$_SESSION["displayAnnouncement"] = false;
	}
	
	// checkdatabases first
	//if ($db_register_down || $db_versioning_down) {
	if ($db_versioning_down) {
		$_REQUEST['update_cmd'] = 'notification';
		$_REQUEST['detail'] = 'databaseFailure';

	}
	//error_log($_REQUEST['update_cmd']." > ".$_REQUEST['detail']);
	// handling request
	switch ($_REQUEST['update_cmd']) {


		case 'notification':
			require_once(SHARED_DIR . '/includes/notification.inc.php');
			break;


		case 'checkConnection':
			require_once(SHARED_DIR . '/includes/connection.inc.php');
			break;


		case 'register':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/register.inc.php');
			break;


		case 'installer':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/installer.inc.php');
			break;


		case 'update':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/update.inc.php');
			break;


		case 'upgrade':
			/*
			if(isset($_SESSION["clientWE_LIGHT"]) && $_SESSION["clientWE_LIGHT"]) {
				require_once(LIVEUPDATE_SERVER_DIR . '/includes/upgrade.inc.php');
			} else {
				print notification::getNotAvailableAtTheMomentResponse();
			}
			*/
			//print notification::getNotAvailableAtTheMomentResponse();
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/upgrade.inc.php');
			break;


		case 'modules':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/modules.inc.php');
			break;

		case 'languages':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/languages.inc.php');
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