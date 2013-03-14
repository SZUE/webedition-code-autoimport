<?php

// Set the current root directory
define("ROOT_DIR", dirname(__FILE__));

// set the shared directory
define("SHARED_DIR", $_SERVER["DOCUMENT_ROOT"] . '/lib/le');

// set the online installation directory
define("LIVEUPDATE_SERVER_DIR", ROOT_DIR . '/snippets');


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
 * check if it is test update (not needed for snippets yet)
 */
//require_once(SHARED_DIR . '/includes/init/checkTestVersion.inc.php');


/**
 * check if it is a beta version (not needed for snippets yet)
 */
//require_once(SHARED_DIR . '/includes/init/checkBetaVersion.inc.php');


/**
 * check if all is temporarily shut down
 */
require_once(SHARED_DIR . '/includes/init/checkTemporarilyShutDown.inc.php');


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


		case 'snippet':
			require_once(LIVEUPDATE_SERVER_DIR . '/includes/snippet.inc.php');
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