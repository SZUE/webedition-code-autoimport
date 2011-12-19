<?php
/**
 * webEdition CMS
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * webEdition configuration script
 *
 * optional URL parameters:
 * - debug		turn on debug messages
 * - debugoff	turn off debug messages
 * - phpinfo	show phpinfo() instead of the setup screen
 * example:		http://yourdomain/setup.php?debug
 * 				http://yourdomain/setup.php?phpinfo
 *
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

// some utility features:
ini_set("display_errors", "Off");
ini_set("error_reporting", 0);

if(isset($_REQUEST["phpinfo"])) {
	phpinfo();
	exit();
}
if(version_compare(PHP_VERSION,"5.2.4","<")) {
	die('<html><head><title>webEdition setup</title></head><body><div style="font-family:sans-serif, sans; font-size:10pt; border:1px solid red; text-align:center; padding:10px; margin:100px;"><b>PHP Version mismatch</b><br /><br />The PHP Version currently used  on this server is too old to run webEdition.<br />webEdition needs at least PHP Version 5.2.4 or newer, please ask your administrator to update your PHP installation!</div></body></html>');
}

// first some includes:
if(
	!is_readable('./webEdition/we/include/we_version.php') ||
	!is_readable('./webEdition/we/include/conf/we_conf.inc.php') ||
	!is_dir('./webEdition') ||
	!is_dir('./webEdition/lib/we')) {
	die("No webEdition installation found. This script has to be placed in your DOCUMENT_ROOT besides your webEdition folder!");
}
include_once './webEdition/we/include/we_version.php';
include_once './webEdition/lib/we/core/autoload.php';
@session_start();
if(isset($_REQUEST["debug"]) && !isset($_SESSION["debug"])) $_SESSION["debug"] = true;
if(isset($_REQUEST["debugoff"]) && isset($_SESSION["debug"])) unset($_SESSION["debug"]);

//remove slashes if magic_quotes_gpc is on
if(get_magic_quotes_gpc()) {
	foreach($_REQUEST as $k=>$v) {
		if(!is_array($v))$_REQUEST[$k] = stripslashes($v);
	}
}

// html code for additional html header tags:
$header = "";
// boolean for error state (for disabling the next button if any errors occured)
$errors = false;

$steps = array(
	array(
		"id" => "1",
		"title" => "webEdition setup",
		"name" => "welcome"
	),
	array(
		"id" => "2",
		"title" => "System requirements",
		"name" => "requirements"
	),
	array(
		"id" => "3",
		"title" => "Filesystem checks",
		"name" => "filesystem"
	),
	array(
		"id" => "4",
		"title" => "Database settings",
		"name" => "database"
	),
	array(
		"id" => "5",
		"title" => "Check database settings",
		"name" => "databasecheck"
	),
	array(
		"id" => "6",
		"title" => "Language selection",
		"name" => "language"
	),
	array(
		"id" => "7",
		"title" => "Summary",
		"name" => "summary"
	),
	array(
		"id" => "8",
		"title" => "Installation",
		"name" => "installation"
	),
	array(
		"id" => "9",
		"title" => "Setup complete.",
		"name" => "finish"
	),
	array(
		"id" => "10",
		"title" => "Cleanup complete.",
		"name" => "cleanup"
	),
);

// identify current step:
if(isset($_REQUEST["step"]) && !empty($_REQUEST["step"]) && intval($_REQUEST) >= "1" && intval($_REQUEST) <= sizeof($steps)) {
	$currentStep = $steps[intval($_REQUEST["step"]) - 1];
} else {
	$currentStep = $steps[0];
}

//functions for checking system
function ini_get_bool($val) {
	$bool = ini_get($val);
	if($val == "1") {
		return true;
	}
	if($val == "0") {
		return false;
	}
	switch (strtolower($bool)) {
		case '1':
		case 'on':
		case 'yes':
		case 'true':
			return true;
		default:
			return false;
	}
	return false;
}



// function for executing steps:
function step_welcome() {
	$output = '<b>Welcome to webEdition 6!</b><br />
We recommend to use the latest OnlineInstaller instead of this <br/>tarball setup, because the OnlineInstaller has much more features<br/> and is much faster.<br/>

The OnlineInstaller is available at <a href="http://download.webedition.org/releases/" target="_blank">download.webedition.org/releases/</a>
or at <a href="https://sourceforge.net/projects/webedition/files/webEdition%20OnlineInstaller/" target="_blank">sourceforge.net/projects/webedition/files/webEdition%20OnlineInstaller/</a><br/><br/><br/>
This webEdition tarball setup script will guide you through the initial configuration steps:
<ul>
		<li>System requirements and recommendations</li>
		<li>Filesystem checks (write permissions etc.)</li>
		<li>Database configuration and checks</li>
		<li>Language and character set</li>
		<li>Database installation and webEdition configuration</li>
</ul>
<b>Important:</b> Please remember to delete this script afterwards in order to prevent system damage by misuse!
<br /><br />
	';
	// session and cookie test:
	$sessionid = session_id();
	if(!$sessionid) {
		$_SESSION["we_test"] = @session_id();
		$_COOKIE["we_test"] = @session_id();
	} else {
		$_SESSION["we_test"] = "";
		$_COOKIE["we_test"] = "";
	}
	return $output;
}

function step_requirements() {
	global $errors;

	$sdkDbOK = true;
	$phpExtensionsDetectable = true;

	$phpextensions = get_loaded_extensions();
	foreach ($phpextensions as &$extens){
		$extens= strtolower($extens);
	}
	$phpextensionsMissing = array();
	$phpextensionsMin = array('ctype','date','dom','filter','gd','iconv','libxml','mbstring','mysql','pcre','Reflection','session','SimpleXML','SPL','standard','tokenizer','xml','zlib');

	if (count($phpextensions)> 3) {
		foreach ($phpextensionsMin as $exten){
			if(!in_array(strtolower($exten),$phpextensions,true) ){$phpextensionsMissing[]=$exten;}
		}
		if ( in_array(strtolower('PDO'),$phpextensions) && in_array(strtolower('pdo_mysql'),$phpextensions) ){//später ODER mysqli
			$phpextensionsSDK_DB = 'PDO &amp; PDO_mysql';
		} else {
			$phpextensionsSDK_DB= '';
			$sdkDbOK = false;
		}
	} else {
		$phpExtensionsDetectable = false;
	}

	$output = "Checking if all system requirements are met. Some additional tests are performed as they are needed for webEdition to be fully functional but are not essential to run webEdition.<br /><br /><b>Basic Requirements:</b><ul style=\"list-style-position:outside;\">";
	$errors = false;
	if(version_compare(PHP_VERSION,"5.2.4","<")) {
		$output.=tpl_error("PHP Version 5.2.4 or newer required!");
		$errors = true;
	} else {
		$output.=tpl_ok("Your PHP Version is up to date (Version ".PHP_VERSION.")");
	}
	if(!empty($phpextensionsMissing)){
		$output.=tpl_error("Required PHP extensions are not available, missing: ".implode(', ', $phpextensionsMissing));
		$errors = true;
	}
	if(!is_callable("mysql_query")) {
		$output.=tpl_error("PHP MySQL Support is required for running webEdition! MySQL servers at version 5.0 or newer are supported.");
		$errors = true;
	} else {
		$mysqlVersion = mysql_get_client_info();
		if(version_compare($mysqlVersion,"5.0","<")) {
			$output.=tpl_error("MySQL Version 5.0 or newer required!");
			$errors = true;
		} else {
			$output.=tpl_ok("PHP MySQL support available (Client API Version ".$mysqlVersion." found)");
		}
	}

	$output .= "</ul><b>Additional requirements:</b><ul style=\"list-style-position:outside;\">";
	if(ini_get_bool('safe_mode')) {
		$output.=tpl_warning("PHP Safe Mode is active.<br />webEdition may run with activated <a href=\"http://www.php.net/manual/en/features.safe-mode.php\" target=\"_blank\">PHP Safe Mode</a>, yet we do not recommend it since it is DEPRECATED since PHP version 5.3. We also cannot guarantee that all features of webEdition will work properly.");
	}
	if(ini_get_bool('register_globals')) {
		$output.=tpl_warning("register_globals is active!<br />This may cause <b>severe security problems</b>, is declared DEPRECATED since PHP version 5.3 and we strongly recommend to disable this \"feature\". See <a href=\"http://www.php.net/manual/en/security.globals.php\" target=\"_blank\">php.net/manual</a> for more information.");
	}
	if(in_array('suhosin',get_loaded_extensions()) ) {
		$output.=tpl_warning("Suhosin is active! The application <b>might</b> work with activated <a href=\"http://www.hardened-php.net/\" target=\"_blank\">Suhosin</a>, but yet we do not recommend it, since Suhosin can lead to problems due it's many configuration options.");
	}


	if(!is_callable("curl_getinfo")) {
		$output.=tpl_warning("curl support is not available.<br />You need at least curl or allow_url_fopen activated for using webEdition liveUpdate, the First Steps Wizard or the application installer.");
	} else {
		$curlVersion = curl_version();
		$output.=tpl_ok("curl support is available (Version ".$curlVersion["version"]." found)");
	}
	if(!ini_get_bool("allow_url_fopen")) {
		$output.=tpl_warning("allow_url_fopen deactivated.<br />You need at least curl or allow_url_fopen activated for using webEdition liveUpdate.");
	} else {
		$output.=tpl_ok("allow_url_fopen activated.");
	}

	if(!is_callable("mb_convert_encoding")) {
		$output.=tpl_warning("PHP multibyte functions not available");
	} else {
		$output.=tpl_ok("PHP multibyte functions available");
	}
	if(!is_callable("gd_info")) {
		$output.=tpl_warning("gdlib functions not available");
	} else {
		$output.=tpl_ok("gdlib functions available (Version ".GD_VERSION." found)");
	}
	if(!is_callable("exif_imagetype")) {
		$output.=tpl_warning("exif extension not available: EXIF-Metadata for images are not available");
	}
	if(!$sdkDbOK) {
		$output.=tpl_warning("SDK Operations and WE-APPS with database access are not available");
	}
	if(!$phpExtensionsDetectable) {
		$output.=tpl_warning("Not all requirements could be checked (Suhosin?). Please check the system requirements at http://documentation.webedition.org/wiki/de/webedition/system-requirements/start");
	}

	if(defined("PCRE_VERSION") && substr(PCRE_VERSION,0,1)<7){
		$output.=tpl_warning("Your PCRE extension is outdated: ".PCRE_VERSION." detected. This can lead to problems, particularly in future webEdition versions.");
	}
	if(!defined("PCRE_VERSION") ){
		$output.=tpl_warning("Your PCRE extension version can not be determined. Versions before 7.0 can lead to problems, particularly in future webEdition versions.");
	}



	$output .= "</ul>";
	if($errors === true) {
		$output .= tpl_errorbox("Some of the essential system requirements are not met. Please check the informations given above and update yor system!<br /><a href=\"?phpinfo\" target=\"_blank\">Click here</a> to check your system's PHP configuration.");
	}
	// session and cookie test:
	$output .= "</ul><b>Session / cookie test:</b><ul style=\"list-style-position:outside;\">";
	if(isset($_SESSION["we_test"]) && $_SESSION["we_test"] == @session_id()) {
		$output.=tpl_error("Session test failed. Maybe restarting your browser may help.");
	} else {
		$output.=tpl_ok("Session test");
	}
	if(isset($_COOKIE["we_test"]) && $_COOKIE["we_test"] == @session_id()) {
		$output.=tpl_error("Cookie test failed. Maybe cookies are disabled in your browser.");
	} else {
		$output.=tpl_ok("Cookie test");
	}
	$output .= "</ul>";
	return $output;
}

function step_filesystem() {
	global $errors;
	$output = "Some Directories and files have to be writable by the webserver for running webEdition:<ul>";
	if(!is_writable('./')) {
		$output .= tpl_error("DOCUMENT ROOT is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./ (DOCUMENT_ROOT)");
	}
	if(!is_writable('./webEdition/')) {
		$output .= tpl_error("The directory webEdition/ is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./webEdition");
	}

        // check if directory exists
        if (!is_dir('./webEdition/site')) {
            mkdir('./webEdition/site');
        }
	if(!is_writable('./webEdition/site')) {
		$output .= tpl_error("The directory webEdition/site could not be created or is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./webEdition/site");
	}

        // check if directory exists
        if (!is_dir('./webEdition/we/templates')) {
            mkdir('./webEdition/we/templates');
        }
	if(!is_writable('./webEdition/we/templates')) {
		$output .= tpl_error("The directory webEdition/we/templates could not be created or is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./webEdition/we/templates");
	}

	if(!is_writable('./webEdition/we/include/conf')) {
		$output .= tpl_error("The webEdition configuration directory webEdition/we/include/conf is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./webEdition/we/include/conf");
	}
	if(!is_writable('./webEdition/we/include/conf/we_conf.inc.php')) {
		$output .= tpl_error("The webEdition configuration file webEdition/we/include/conf/we_conf.inc.php is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./webEdition/we/include/conf/we_conf.inc.php");
	}

	// check if directory exists
        if (!is_dir('./webEdition/we/tmp')) {
            mkdir('./webEdition/we/tmp');
        }
	if(!is_writable('./webEdition/we/tmp')) {
		$output .= tpl_error("The webEdition temporary directory webEdition/we/tmp could not be created or is not writable!");
		$errors = true;
	} else {
		$output .= tpl_ok("./webEdition/we/tmp");
	}
       if (!is_dir('./webEdition/liveUpdate/tmp')) {
           mkdir('./webEdition/liveUpdate/tmp');
       }

	if(!is_writable('./webEdition/liveUpdate/tmp')) {
		$output .= tpl_warning("The webEdition liveUpdate temporary directory webEdition/liveUpdate/tmp could not be created or is not writable! You will not be able to use this feature.");
	} else {
		$output .= tpl_ok("./webEdition/liveUpdate/tmp");
	}

	$output .= "</ul>";
	if($errors === true) {
		$output .= tpl_errorbox("There were some errors regarding file access privileges. Please fix these issues (i.e. via ftp) and try again.<br/> We do not recommend to try to make these files and directories writeble on an individual base because there are additional files and directories which ahve to be writeble and which are not testet here.<br/> You should use your ftp programm to set the rights to 755 to the /webEdition and all included directories and files recursively (good ftp programms allow to set the rights in a singel step)");
	} else {
		$output .= "All these directories seem to be writable by the webserver.<br /><br />";
	}
	$output .= "Sometimes there may occur problems while using webEdition regarding file access permissions, even if the directories seem to be writable to this script. If that happens you should verify all access privileges and file owner informations of the critical webEdition directories. This can be done with ftp applications like <a href=\"http://www.filezilla-project.org\" target=\"_blank\">FileZilla</a>.";
	return $output;
}

function step_database() {
	global $header;
	$output = "Please enter all informations required to connect to the database server:<br /><br />";
	// database host name
	$input_host = new we_ui_controls_TextField();
	$input_host->setName('db_host');
	if(isset($_SESSION["db_host"]) && !empty($_SESSION["db_host"])) {
		$input_host->setValue($_SESSION["db_host"]);
	} else {
		$input_host->setValue('localhost');
	}
	$input_host->setWidth(200);
	$input_host->setHeight(26);

	// database name:
	$input_database = new we_ui_controls_TextField();
	$input_database->setName('db_database');
	if(isset($_SESSION["db_database"]) && !empty($_SESSION["db_database"])) {
		$input_database->setValue($_SESSION["db_database"]);
	} else {
		$input_database->setValue('webedition');
	}
	$input_database->setWidth(200);
	$input_database->setHeight(26);

	// table prefix:
	$input_tableprefix = new we_ui_controls_TextField();
	$input_tableprefix->setName('db_tableprefix');
	if(isset($_SESSION["db_tableprefix"]) && !empty($_SESSION["db_tableprefix"])) {
		$input_tableprefix->setValue($_SESSION["db_tableprefix"]);
	} else {
		$input_tableprefix->setValue('');
	}
	$input_tableprefix->setWidth(200);
	$input_tableprefix->setHeight(26);

	// database username:
	$input_username = new we_ui_controls_TextField();
	$input_username->setName('db_username');
	if(isset($_SESSION["db_username"]) && !empty($_SESSION["db_username"])) {
		$input_username->setValue($_SESSION["db_username"]);
	} else {
		$input_username->setValue('');
	}
	$input_username->setWidth(200);
	$input_username->setHeight(26);

	// database user password:
	$input_password = new we_ui_controls_TextField();
	$input_password->setName('db_password');
	if(isset($_SESSION["db_password"])) {
		$input_password->setValue($_SESSION["db_password"]);
	} else {
		$input_password->setValue('');
	}
	$input_password->setWidth(200);
	$input_password->setClass("small");
	$input_password->setType("password");
	$input_password->setHeight(26);;

	foreach($input_host->getJSFiles() as $jsFile) {
		$header .= '<script src="'.$jsFile.'" language="JavaScript" type="text/javascript"></script>';
	}
	foreach($input_host->getCSSFiles() as $cssFile) {
		$header .= '<link href="'.$cssFile["path"].'" media = "'.$cssFile["media"].'" rel="styleSheet" type="text/css" />';
	}
	$output .= '<table class="small">';
	$output .= '<tr><td style="width:80px;">Server: </td><td>'.$input_host->getHTML().'</td></tr>';
	$output .= '<tr><td style="width:80px;">Database: </td><td>'.$input_database->getHTML().'</td></tr>';
	$output .= '<tr><td style="width:80px;">Table prefix: </td><td>'.$input_tableprefix->getHTML().'</td></tr>';
	$output .= '<tr><td style="width:80px;">Username: </td><td>'.$input_username->getHTML().'</td></tr>';
	$output .= '<tr><td style="width:80px;">Password: </td><td>'.$input_password->getHTML().'</td></tr>';
	$output .= '</table>';
	return $output;
}

function step_databasecheck() {
	global $errors;
	$output = "Some checks are being performed to verify that the database server is fully operational:<ul>";
	if((!isset($_SESSION["db_host"]) || empty($_SESSION["db_host"])) && (!isset($_REQUEST["db_host"]) || empty($_REQUEST["db_host"]))) {
		$output .= tpl_error("Please enter the host name of your MySQL database server.");
		$errors = true;
	} else if(isset($_REQUEST["db_host"])) {
		//$_SESSION["db_host"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["db_host"]))));
		$_SESSION["db_host"] = $_REQUEST["db_host"];
	}
	if((!isset($_SESSION["db_database"]) || empty($_SESSION["db_database"])) && (!isset($_REQUEST["db_database"]) || empty($_REQUEST["db_database"]))) {
		$output .= tpl_error("Please enter the database name to be used by webEdition. This database does not need to exist yet, if the specified database user has the permission to create databases.");
		$errors = true;
	} else if(isset($_REQUEST["db_database"])) {
		//$_SESSION["db_database"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["db_database"]))));
		$_SESSION["db_database"] = $_REQUEST["db_database"];
	}
	/*
	if(isset($_REQUEST["db_tableprefix"])) {
		$_SESSION["db_tableprefix"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["db_tableprefix"]))));
	} else if(!isset($_SESSION["db_tableprefix"])) {
		$_SESSION["db_tableprefix"] = '';
	}
	*/
	if(isset($_REQUEST["db_tableprefix"]) && preg_match('/^[a-z0-9_-]{0,}$/i', $_REQUEST["db_tableprefix"])) {
		$_SESSION["db_tableprefix"] = $_REQUEST["db_tableprefix"];
 	} else if(!isset($_SESSION["db_tableprefix"])) {
 		$_SESSION["db_tableprefix"] = '';
 	}

	if((!isset($_SESSION["db_username"]) || empty($_SESSION["db_username"])) && (!isset($_REQUEST["db_username"]) || empty($_REQUEST["db_username"]))) {
		$output .= tpl_error("Please enter the username for accessing your MySQL database server.");
		$errors = true;
	} else if(isset($_REQUEST["db_username"])) {
		//$_SESSION["db_username"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["db_username"]))));
		$_SESSION["db_username"] = $_REQUEST["db_username"];
	}
	if(!isset($_SESSION["db_password"]) && !isset($_REQUEST["db_password"])) {
		$output .= tpl_error("Please enter the password for accessing your MySQL database server.");
		$errors = true;
	} else if(isset($_REQUEST["db_password"])) {
		//$_SESSION["db_password"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["db_password"]))));
		$_SESSION["db_password"] = $_REQUEST["db_password"];
		if(empty($_REQUEST["db_password"])) {
			$output .= tpl_warning("No password entered. Are you sure?");
		}
	}
	if($errors) {
		$output .= tpl_errorbox("Please enter the missing informations.");
		return $output.'</ul>';
	}

	// check connection to db server using the entered data
	$conn = @mysql_connect($_SESSION["db_host"],$_SESSION["db_username"],$_SESSION["db_password"]);
	if(!$conn) {
		$output .= tpl_error("Could not connect to MySQL database server.");
		$errors = true;
		return $output.'</ul>';
	} else {
		$output .= tpl_ok("Connection test succeeded");
	}

	// check if selected database already exists:
	$op_createdb = false;
	//$result = @mysql_list_dbs($conn);
	$result = @mysql_query(sprintf('use `%s`', $_SESSION['db_database']), $conn);
	//$dblist = mysql_fetch_array($result);
	//$output .= print_r($dblist,true);
	//if(!in_array($_SESSION["db_database"],$dblist)) {
	if(!$result) {
		$output .= tpl_info("The database \"".$_SESSION["db_database"]."\" does not exist yet. Will try to create it.");
		$op_createdb = true;
	} else {
		$output .= tpl_ok("The database \"".$_SESSION["db_database"]."\" exists already");
		$op_createdb = false;
	}

	// try to create db:
	if($op_createdb === true) {
		if(!@mysql_query(sprintf('use `%s`', $_SESSION['db_database']), $conn)) {
			$output .= tpl_error("Could not create the database. Message from the server: ".mysql_error($conn));
			$errors = true;
			return $output.'</ul>';
		}
	}
	$result = @mysql_query(sprintf('use `%s`', $_SESSION['db_database']),$conn);

	// check if there is already a webEdition installation present:

	$result = @mysql_query("select ID from ".$_SESSION["db_tableprefix"]."tblUser",$conn);
	if(!$result) {
		$output .= tpl_ok("The selected database obviously does not conain any previous webEdition installations using this table prefix");
	} else {
		$data = @mysql_num_rows($result);
		if(!empty($data)) {
			$output .= tpl_warning("There is obviously a previous webEdition installation in the selected database. <b>All data will be lost if you continue this installation!</b> Please backup your data or use an alternate table prefix.");
		} else {
			$output .= tpl_ok("The selected database obviously does not conain any previous webEdition installations using this table prefix");
		}
	}
	if ( (float) mysql_get_server_info($conn) < 5.0) {
		$output .= 	tpl_warning(sprintf("The database server reports the version %s, webEdition requires at least the  MySQL-Server version 5.0. webEdition may work with the used version, but this can not be guarented for new webEdition versions (i.e. after updates). For webEdition version 7,  MySQL version 5 will definitely be required.<br/><span style=\"color:red;font-weight:bold\">In addition: In addition: The installed MySQL version is outdated. There are no security updates available for this version, which may put the security of the whole system at risk!</span><br/<br/>",mysql_get_server_info($conn)));
	}

	// check for required database access permissions (select, insert, alter, update, drop)
	$output .= "</ul>Performing some permission tests for important database operations:<ul>";
	if(!@mysql_query("CREATE TABLE  `we_installer_test` (`id` VARCHAR( 100 ) NOT NULL) ENGINE = MyISAM;",$conn)) {
		$output .= tpl_error("CREATE TABLE failed: ".mysql_error($conn));
		$errors = true;
	} else {
		$output .= tpl_ok("CREATE TABLE succeeded");
	}
	if(!@mysql_query("INSERT INTO `we_installer_test` VALUES('eins');",$conn)) {
		$output .= tpl_error("INSERT failed: ".mysql_error($conn));
		$errors = true;
	} else {
		$output .= tpl_ok("INSERT succeeded");
	}
	if(!@mysql_query("UPDATE `we_installer_test` SET `id` = 'zwei' WHERE `id` != 'zwei';",$conn)) {
		$output .= tpl_error("UPDATE failed: ".mysql_error($conn));
		$errors = true;
	} else {
		$output .= tpl_ok("UPDATE succeeded");
	}
	if(!@mysql_query("DROP TABLE `we_installer_test`;",$conn)) {
		$output .= tpl_error("DROP TABLE failed: ".mysql_error($conn));
		$errors = true;
	} else {
		$output .= tpl_ok("DROP TABLE succeeded");
	}



	$output .= "</ul>";
	if($errors === false) {
	 	$output .= "All seems to be ok, all requirements are met.";
	} else {
		$output .= tpl_errorbox("There were some problems with the MySQL database server, please check the informations given above and fix these issues to continue the webEdition installation.");
	}

	//$output .= "<br /><br /><br /><br /><br /><br />";
	return $output;
}

function step_language() {
	global $errors;
	$output = "Please select a language to be used by webEdition. You can change this at any time using the webEdition preferences dialog window. There you can alos change to an ISO backend charset";
	if(!is_dir('./webEdition/we/include/we_language/')) {
		$output .= tpl_errorbox('There is a problem with your webEdition installation, could not find the language directory. Please verify that the installation archive has been completely unpacked into this directory.');
		$errors = true;
		return $output;
	}
	$langdirs = scandir('./webEdition/we/include/we_language/');
	//$output .= print_r($langdirs,true);
	foreach($langdirs as $lang) {
		if(substr($lang,0,1) != "." && strtoupper($lang) != "CVS" && strtoupper($lang) != "SVN") {
			if(is_readable('./webEdition/we/include/we_language/'.$lang.'/translation.inc.php')) {
				include_once('./webEdition/we/include/we_language/'.$lang.'/translation.inc.php');
			}
		}
	}
	asort($_language["translation"]);
	$defaultLanguage = "English";
	$defaultLanguageTranslation = "English";
	$isoLanguages = false;
	if(!isset($_SESSION["we_language_translation"])) {
		$currentLanguage = $defaultLanguageTranslation;
	} else {
		$currentLanguage = $_SESSION["we_language_translation"];
	}
	$output .= '<input type="hidden" name="we_language_translation" value="'.$currentLanguage.'" />';
	$output .= '<div style="display:block; margin:10px; text-align:center;"><select name="we_language" onchange="document.getElementsByName(\'we_language_translation\')[0].value = this[this.selectedIndex].text;">';
	foreach($_language["translation"] as $k => $v) {
		if(!isset($_SESSION["we_language"]) && $k == $defaultLanguage) {
			$selected = 'selected="selected" ';
		} else if(isset($_SESSION["we_language"]) && $_SESSION["we_language"] == $k) {
			$selected = 'selected="selected" ';
		} else {
			$selected = "";
		}
		// check if this an iso encoded language (needed for displaying an additional information box):
		//if(!strpos($v,"UTF-8")) {
			//$isoLanguages = true;
			//$v .= " (ISO 8859-1)";
		//}
		$output .= '<option '.$selected.'name="'.$v.'" value="'.$k.'">'.$v.'</option>';
	}
	$output .= '</select></div>';
	// additional information box for iso encoded languages:
	if($isoLanguages === true) {
		$output .= "<b>Important:</b> We strongly recommend using UTF-8 for new projects. webEdition still contains a couple of ISO-8859-1 (ISO Latin-1) encoded translations for backwards compatibility, but all new translations are and will be UTF-8 encoded. In addition, for the upcoming Version 7, we do not guarantee full support for ISO languages, so you might need to convert your site to UTF-8. <br /><br />";
	}
	$output .= "If your language is missing in this list, feel free to contribute a new translation to the webEdition community. You can find more informations about contributing code and translations on the <a href=\"http://www.webedition.org\" target=\"_blank\">webEdition website</a>.";

	$conn = @mysql_connect($_SESSION["db_host"],$_SESSION["db_username"],$_SESSION["db_password"]);
	$result = @mysql_query(sprintf('use `%s`', $_SESSION['db_database']),$conn);
	$result = @mysql_query("SHOW COLLATION WHERE Compiled = 'Yes' ",$conn);
	if(!isset($_SESSION["we_db_collation"])) {
		$currentcharset = 'utf8_general_ci';
	} else {
		$currentcharset = $_SESSION["we_db_collation"];
	}

	$output .= "<br/>&nbsp;<br/>Please select the default encoding and the corresponding database collation (defining the standard sorting within database calls). We recommend utf8_general_ci";
	$output .= '<input type="hidden" name="we_db_collation" value="'.$currentcharset.'" />';
	$output .= '<div style="display:block; margin:10px; text-align:center;">';

	$output .= '<select name="we_db_char" onchange="document.getElementsByName(\'we_db_collation\')[0].value = this[this.selectedIndex].text;">';
	$cset ='';
	while ($row = mysql_fetch_assoc($result)) {
		if ($cset != $row['Charset']){
			if ($cset != ''){$output .= '</optgroup>';}
			$output .= '<optgroup label="'.$row['Charset'].'">';
			$cset = $row['Charset'];
		}
		$output .= '<option ';
		if ($row['Collation']== $currentcharset){$output .= 'selected="selected"';  }
		$output .= ' >'.$row['Collation'].'</option>';
	}
				//sort($charsets);
	$output .= '</select></div>';

	return $output;
}

function step_summary() {
	global $errors;
	//print_r($_SESSION);
	$output = "";
	if((!isset($_SESSION["we_language"]) || empty($_SESSION["we_language"])) && (!isset($_REQUEST["we_language"]) || empty($_REQUEST["we_language"]))) {
		$output .= tpl_errorbox("Please select a valid language to be used by webEdition.");
		$errors = true;
	} else if(isset($_REQUEST["we_language"])) {
		$_SESSION["we_language"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["we_language"]))));
		$_SESSION["we_language_translation"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["we_language_translation"]))));
	}
	if((!isset($_SESSION["we_db_collation"]) || empty($_SESSION["we_db_collation"])) && (!isset($_REQUEST["we_db_collation"]) || empty($_REQUEST["we_db_collation"]))) {
		$output .= tpl_errorbox("Please select a valid database collation to be  used by webEdition.");
		$errors = true;
	} else if(isset($_REQUEST["we_db_collation"])) {
		$_SESSION["we_db_collation"] = str_replace("/*","",str_replace('"','',str_replace("'","",trim($_REQUEST["we_db_collation"]))));
	}
	$dbcharsetparts = explode('_',$_SESSION["we_db_collation"]);
	$_SESSION["we_db_charset"] = $dbcharsetparts[0];
	if ($_SESSION["we_db_charset"] =="utf8") {
		$_SESSION["we_charset"] = "UTF-8";
	} else {
		$_SESSION["we_charset"] = "ISO-8859-1";
	}

	// webEdition settings:
	$output .= '<fieldset><legend>webEdition:</legend><table class="small" style="width:100%; table-layout:fixed;">';
	$output .= '<tr><td style="width:160px;">Language*:</td><td>'.(isset($_SESSION["we_language_translation"]) ? htmlentities($_SESSION["we_language_translation"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>webEdition Code*:</td><td>'.(isset($_SESSION["we_language"]) ? htmlentities($_SESSION["we_language"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Default Charset*:</td><td>'.(isset($_SESSION["we_charset"]) ? htmlentities($_SESSION["we_charset"]) : ' - ').'</td></tr>';

	$output .= '</table></fieldset><br />';

	// database settings:
	$output .= '<fieldset><legend>Database server:</legend><table class="small" style="width:100%; table-layout:fixed;">';
	$output .= '<tr><td style="width:160px;">Server name:</td><td>'.(isset($_SESSION["db_host"]) ? htmlentities($_SESSION["db_host"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Database name:</td><td>'.(isset($_SESSION["db_database"]) ? htmlentities($_SESSION["db_database"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Table prefix:</td><td>'.(isset($_SESSION["db_tableprefix"]) ? htmlentities($_SESSION["db_tableprefix"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Username:</td><td>'.(isset($_SESSION["db_username"]) ? htmlentities($_SESSION["db_username"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Password:</td><td>'.(isset($_SESSION["db_password"]) ? htmlentities($_SESSION["db_password"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Database charset:</td><td>'.(isset($_SESSION["we_db_charset"]) ? htmlentities($_SESSION["we_db_charset"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>Database collation:</td><td>'.(isset($_SESSION["we_db_collation"]) ? htmlentities($_SESSION["we_db_collation"]) : ' - ').'</td></tr>';
	$output .= '<tr><td>DB connection charset*:</td><td>'.(isset($_SESSION["we_db_charset"]) ? htmlentities($_SESSION["we_db_charset"]) : ' - ').'</td></tr>';
	$output .= '</table></fieldset>';
	if(
		!isset($_SESSION["db_host"]) ||
		empty($_SESSION["db_host"]) ||
		!isset($_SESSION["db_database"]) ||
		empty($_SESSION["db_database"]) ||
		!isset($_SESSION["db_tableprefix"]) ||
		!isset($_SESSION["db_username"]) ||
		empty($_SESSION["db_username"]) ||
		!isset($_SESSION["db_password"]) ||
		!isset($_SESSION["we_language_translation"]) ||
		empty($_SESSION["we_language_translation"]) ||
		!isset($_SESSION["we_language"]) ||
		empty($_SESSION["we_language"])
		) {
		$errors = true;
	}
	$output.="*These values can be changed easily with the configuration dialog.";
	return $output;
}

function step_installation() {
	global $errors;
	$output = "<b>Installation of database tables:</b><br /><br />";
	// read and parse database dump:
	if(!is_readable("./database.sql") && !is_readable("./additional/sqldumps/dump/complete.sql")) {
		$output .= tpl_error("Could not read database dump file.");
		$errors = true;
		return $output;
	}
	if(is_readable("./database.sql")) {
		$dbdata = file_get_contents("./database.sql");
	} else {
		$dbdata = file_get_contents("./additional/sqldumps/dump/complete.sql");
	}
	// $dbdata = str_replace("`","",$dbdata);
	$dbqueries = explode("/* query separator */",$dbdata);
	echo sizeof($dbqueries).' queries found.';
	$conn = @mysql_connect($_SESSION["db_host"],$_SESSION["db_username"],$_SESSION["db_password"]);
	if(!$conn) {
		$output .= tpl_error("Could not connect to database server. Message from server: ".mysql_error());
		$errors = true;
		return $output;
	} else {
		$output .= tpl_ok("connected to database server on \"".$_SESSION["db_host"]."\"");
	}
	// select database:
	if(!@mysql_query(sprintf('use `%s`', $_SESSION['db_database']),$conn)) {
		$output .= tpl_error("Error using specified database. Message from server: ".mysql_error());
		$errors = true;
		return $output;
	} else {
		$output .= tpl_ok("Using specified database \"".$_SESSION["db_database"]."\"");
	}
	// drop all existing tables beginning with $prefix$tbl:
	$res = @mysql_query('show tables where Tables_in_'.$_SESSION["db_database"].' LIKE "'.$_SESSION["db_tableprefix"].'tbl%"',$conn);
	while($table = @mysql_fetch_array($res)) {
		@mysql_query("drop table ".$table[0],$conn);
		echo $table[0]." dropped.<br />";
	}
	// insert table prefix and install all tables from sql dump:
	$queryErrors = false;

	$charset_collation = "";
	if (isset($_SESSION["we_db_charset"]) && $_SESSION["we_db_charset"] != "" && isset($_SESSION["we_db_collation"]) && $_SESSION["we_db_collation"] != "") {
		$Charset = $_SESSION["we_db_charset"];
		$Collation = $_SESSION["we_db_collation"];
		$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;
		$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation. " ENGINE=MyISAM ";
	} else {
		$charset_collation = "ENGINE=MyISAM";
	}
	@mysql_query(" SET NAMES '" . $_SESSION["we_db_charset"] . "' ",$conn );
	foreach($dbqueries as $dbquery) {
		if(isset($_SESSION["db_tableprefix"]) && !empty($_SESSION["db_tableprefix"])) {
			$dbquery=str_replace('###TBLPREFIX###', $_SESSION["db_tableprefix"], $dbquery);
		}else{
			$dbquery=str_replace('###TBLPREFIX###', '', $dbquery);
		}
		$dbquery=str_replace('###INSTALLONLY###', '', $dbquery);

		$dbquery = str_replace("ENGINE=MyISAM",$charset_collation,$dbquery);
		if(strpos($dbquery,'###UPDATEONLY###')!==false){
			$dbquery='';
		}else if(strpos($dbquery,'###UPDATEDROPCOL')!==false){
			$dbquery='';
		}
		if(!empty($dbquery)) {
			if(!@mysql_query($dbquery,$conn)) {
				if(mysql_errno() != "1065") {
					$output .= tpl_warning("error executing query. Message from server: ".mysql_error());
					print("<pre>".$dbquery."</pre><hr />");
					$queryErrors = true;
				}
				//$output .= tpl_warning("error executing query.");
			} else {
				//print("<pre>".mysql_info($conn)."</pre><hr />");
			}
		}
	}
        if ($queryErrors === true) {
		$output .= tpl_error("There were some errors while executing the database queries.");
                $errors = true;
	} else {
		$output .= tpl_ok("Executed all queries successfully to the selected database.");
	}
	//print("<pre>".$dbdata."</pre>");
	$output .= "<br /><b>Writing webEdition configuration:</b><br /><br />";

	//$output .= "<li><i>under construction ...</i></li>";
	// set the language of the default user
	if(!@mysql_query('UPDATE '.$_SESSION["db_tableprefix"].'tblPrefs set Language = "'.mysql_real_escape_string($_SESSION["we_language"]).'" where userID="1"',$conn)) {
		$output .= tpl_warning("Could not change the default user's language settings. Message from server: ".mysql_error());
		print("<pre>".$dbquery."</pre><hr />");
		$queryErrors = true;
		//$output .= tpl_warning("error executing query.");
	} else {
		$output .= tpl_ok("Changed the default user's language to ".$_SESSION["we_language"]);
	}
	@mysql_close($conn);

	//move .default files to their "new" location.
	if(is_writable('./webEdition/we/include/conf/')){
		$dir='./webEdition/we/include/conf/';
		$files = scandir($dir);
		foreach($files as $file){
			if(substr($file,-8)=='.default'){
				$new=$dir.substr($file,0,-8);
				rename($dir.$file, $new);
			}
		}
	}
	// write database connection data to we_conf.inc.php
	if(!is_writable('./webEdition/we/include/conf/we_conf.inc.php') || !is_writable('./webEdition/we/include/conf/we_conf_global.inc.php')) {
		tpl_error("Could not open webEdition configuration files for writing.");
		$errors = true;
	} else {
		$we_config = file_get_contents('./webEdition/we/include/conf/we_conf.inc.php.default');
		$we_config_global = file_get_contents('./webEdition/we/include/conf/we_conf_global.inc.php.default');
		$we_active_modules = file_get_contents('./webEdition/we/include/conf/we_active_integrated_modules.inc.php.default');
		//$we_config = str_replace('define("WE_LANGUAGE","English_UTF-8");','define("WE_LANGUAGE","'.$_SESSION["we_language"].'");',$we_config);
		//$we_config = preg_replace('/(define\("WE_LANGUAGE",")(\s*)+("\);)/i','$1'.$_SESSION["we_language"].'$3',$we_config);
		//str_replace('define("TBL_PREFIX","");','define("TBL_PREFIX","'.$_SESSION["db_tableprefix"].'"',$we_config);

		//$we_config = preg_replace('/(define\("DB_HOST",")(\w*)("\);)/i','$1'.$_SESSION["db_host"].'$3',$we_config);
		//$we_config = preg_replace('/(define\("DB_DATABASE",")(\w*)("\);)/i','$1'.$_SESSION["db_database"].'$3',$we_config);
		//$we_config = preg_replace('/(define\("DB_USER",")(\w*)("\);)/i','$1'.$_SESSION["db_username"].'$3',$we_config);
		//$we_config = preg_replace('/(define\("DB_PASSWORD",")(\w*)("\);)/i','$1'.$_SESSION["db_password"].'$3',$we_config);
		//$we_config = preg_replace('/(define\("TBL_PREFIX",")(\w*)("\);)/i','$1'.$_SESSION["db_tableprefix"].'$3',$we_config);
		//$we_config = preg_replace('/(define\("WE_LANGUAGE",")(\w*)(\055?)(\w*)("\);)/i','$1'.$_SESSION["we_language"].'$5',$we_config);

		$we_config = preg_replace('/(define\("DB_CHARSET",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["we_db_charset"]).'${3}',$we_config);
		$we_config = preg_replace('/(define\("DB_COLLATION",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["we_db_collation"]).'${3}',$we_config);

		$we_config_global = preg_replace('/(define\("DB_SET_CHARSET",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["we_db_charset"]).'${3}',$we_config_global);

		//$we_config_global = preg_replace('/(define\("DEFAULT_CHARSET",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["we_charset"]).'${3}',$we_config_global); Das klappt irgendwie nicht, ersatz:
		$we_config_global = str_replace('define("DEFAULT_CHARSET","UTF-8")','define("DEFAULT_CHARSET","'.str_replace('"', '\\"', $_SESSION["we_charset"]).'")',$we_config_global);

		$we_config = preg_replace('/(define\("DB_HOST",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["db_host"]).'${3}',$we_config);
		//$we_config = preg_replace('/(define\(\'DB_HOST\',\')(\w*)(\'\);)/i','${1}'.str_replace('"','\\\'',$_SESSION["db_host"]).'${3}',$we_config);
		$we_config = preg_replace('/(define\("DB_DATABASE",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["db_database"]).'${3}',$we_config);
		//$we_config = preg_replace('/(define\(\'DB_DATABASE\',\')(\w*)(\'\);)/i','${1}'.str_replace('"', '\\\'', $_SESSION["db_database"]).'${3}',$we_config);
		$we_config = preg_replace('/(define\("DB_USER",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["db_username"]).'${3}',$we_config);
		//$we_config = preg_replace('/(define\(\'DB_USER\',\')(\w*)(\'\);)/i','${1}'.str_replace('"', '\\\'', $_SESSION["db_username"]).'${3}',$we_config);
		$we_config = preg_replace('/(define\("DB_PASSWORD",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["db_password"]).'${3}',$we_config);
		//$we_config = preg_replace('/(define\(\'DB_PASSWORD\',\')(\w*)(\'\);)/i','${1}'.str_replace('"', '\\\'', $_SESSION["db_password"]).'${3}',$we_config);
		$we_config = preg_replace('/(define\("TBL_PREFIX",")(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["db_tableprefix"]).'${3}',$we_config);
		//$we_config = preg_replace('/(define\(\'TBL_PREFIX\',\')(\w*)(\'\);)/i','${1}'.str_replace('"', '\\\'', $_SESSION["db_tableprefix"]).'${3}',$we_config);
		
		
		$we_config = preg_replace('/(define\("WE_LANGUAGE",")(\w*)(\055?)(\w*)("\);)/i','${1}'.str_replace('"', '\\"', $_SESSION["we_language"]).'${5}',$we_config);

		$output .= tpl_ok("Changed the system's default language to ".$_SESSION["we_language"]);
		$output .= tpl_ok("Saved database configuration.");
		if(! (file_put_contents('./webEdition/we/include/conf/we_conf.inc.php',$we_config) && file_put_contents('./webEdition/we/include/conf/we_conf_global.inc.php',$we_config_global) && file_put_contents('./webEdition/we/include/conf/we_active_integrated_modules.inc.php',$we_active_modules) )) {
			$output .= tpl_error("Could not write webEdition configuration files.");
			$errors = true;
		} else {
			$output .= tpl_ok("webEdition configuration files written.");
		}
		//error_log($we_config);
		/*
		define("DB_HOST","localhost");
		define("DB_DATABASE","wedev_svn");
		define("DB_USER","root");
		define("DB_PASSWORD","root");
		define("TBL_PREFIX","");
		define("DB_CHARSET","");
		define("WE_LANGUAGE","English_UTF-8");
		*/
	}
	return $output;
}

function step_finish() {
	$output = "The webEdition installation is now finished. It is located in the subdirectory \"/webEdition/\", you can enter webEdition by <a href=\"/webEdition/\" target=\"_blank\">clicking here</a>.
	If you want more informations about how to use webEdition, visit our website or join the webEdition community.<br /><br />
	";
	$output .= "<b>Important:</b><br /><br />";
	$output .= "Please don't forget to remove this setup script in order to prevent damage to your website by misuse. The next and final step of this installation script will take care of that.<br /><br />";
	$output .= "The first thing you should do is to change the default password and username to less obvious ones, by default it is:
	<p style=\"margin-left:20px;\"><b>Username:</b> admin<br /><b>Password:</b> admin</p>
	You can do that using the webEdition user management module (located at the top of the \"Modules\" menu).";
	//return "<br />Live long and prosper!<br /><br /><br /><br /><br /><br />";
	return $output;
}

function step_cleanup() {
	$filesToDelete = array(
		".".DIRECTORY_SEPARATOR."README.txt",
		".".DIRECTORY_SEPARATOR."BUILD	",
		".".DIRECTORY_SEPARATOR."BUILDDATE",
		".".DIRECTORY_SEPARATOR."INSTALL.txt",
		".".DIRECTORY_SEPARATOR."LICENSE.txt",
		".".DIRECTORY_SEPARATOR."database.sql",
		".".DIRECTORY_SEPARATOR."setup.php"
	);
	$error = false;
	foreach($filesToDelete as $fileToDelete) {
		if(is_file($fileToDelete)) {
			@unlink($fileToDelete);
			if(is_readable($fileToDelete) && $fileToDelete != ".".DIRECTORY_SEPARATOR."setup.php") {
				$error = true;
			}
		}
	}
	//if(is_readable("./setup.php")) $error = true;
	$output = "The webEdition installation is now finished. It is located in the subdirectory \"/webEdition/\", you can enter webEdition by <a href=\"/webEdition/\">clicking here</a>.
	If you want more informations about how to use webEdition, visit our website or join the webEdition community.<br /><br />
	";
	if($error === true) {
		$output .= tpl_errorbox("At least one of the setup files could not be deleted (maybe insufficient access permissions?), please do that manually!");
	} else {
		$output .= tpl_infobox("All setup files have been deleted successfully to avoid system damage by misuse.");
	}
	$output .= "<br /><b>Important:</b><br /><br />";
	$output .= "The first thing you should do is to change the default password and username to less obvious ones, by default it is:
	<p style=\"margin-left:20px;\"><b>Username:</b> admin<br /><b>Password:</b> admin</p>
	You can do that using the webEdition user management module (located at the top of the \"Extras\" menu).";
	//return "<br />Live long and prosper!<br /><br /><br /><br /><br /><br />";
	return $output;

}

// html template functions:

// error message box:
function tpl_errorbox($text = "") {
	return '<div style="display:block; padding:3px; padding-left:24px; margin:3px 0px 3px 0px; border:1px solid red; background: url(./webEdition/images/icons/invalid.gif) 3px center no-repeat;" />'.$text.'</div>';
}

// info message box:
function tpl_infobox($text = "") {
	return '<div style="display:block; padding:3px; padding-left:24px; margin:3px 0px 3px 0px; border:1px solid green; background: url(./webEdition/images/icons/valid.gif) 3px center no-repeat;" />'.$text.'</div>';
}

// informational message:
function tpl_info($text = "") {
	return '<p>INFO: '.$text.'</p>';
}

// error message:
function tpl_error($text = "") {
	return '<li><font color="red">ERROR: </font>'.$text.'</li>';
}

// succes message:
function tpl_ok($text = "") {
	return '<li>'.$text.' - <font color="green">OK</font></li>';
}

// warning message:
function tpl_warning($text = "") {
	return '<li><font color="orange">WARNING:</font> '.$text.'</li>';
}

// title text
if(isset($currentStep["title"])) {
	$stepTitle = '<big><b>'.$currentStep["id"].'. '.$currentStep["title"].'</b></big><br /><br />';
} else {
	$stepTitle = '';
}

// step navigation (2 buttons):
function tpl_navigation($step = "1") {
	global $header, $currentStep, $steps, $errors;
	$nextID = $step + 1;
	$prevID = $step - 1;
	// next button
	$buttonNext = new we_ui_controls_Button();
	$buttonNext->setWidth(120);
	$buttonNext->setTextPosition('right');
	/*
	if($step == sizeof($steps)) {
		$buttonNext->setHref('./webEdition/');
		$buttonNext->setTarget('_blank');
		$buttonNext->setText('start webEdition');
		$buttonNext->setTitle('start webEdition in a new window');
	} else {
		*/
		$buttonNext->setTitle('next step');
		if($step == (sizeof($steps)-1)) {
			$buttonNext->setText('cleanup');
		} else {
			$buttonNext->setText('next');
		}

		$buttonNext->setTarget('_self');
		$buttonNext->setType('submit');
		if($step >= sizeof($steps) || $errors === true) {
			$buttonNext->setDisabled(true);
		} else {
			$buttonNext->setHref('?step='.$nextID);
		}
	//}
	// back button
	$buttonPrev = new we_ui_controls_Button();
	$buttonPrev->setTitle('previous step');
	$buttonPrev->setText('back');
	$buttonPrev->setType('href');
	$buttonPrev->setTarget('_self');
	if($step == "1" || $step >= (sizeof($steps)-1)) {
		$buttonPrev->setDisabled(true);
	} else {
		$buttonPrev->setHref('?step='.$prevID);
	}
	$buttonPrev->setWidth(120);
	$buttonPrev->setTextPosition('left');

	foreach($buttonNext->getJSFiles() as $jsFile) {
		$header .= '<script src="'.$jsFile.'" language="JavaScript" type="text/javascript"></script>';
	}
	foreach($buttonNext->getCSSFiles() as $cssFile) {
		$header .= '<link href="'.$cssFile["path"].'" media = "'.$cssFile["media"].'" rel="styleSheet" type="text/css" />';
	}

	$output = '<div style="display:block; margin:10px 0px 10px 0px;"><div style="float:left;">'.$buttonPrev->getHTML().'</div>';
	$output .= '<div style="float:right;">'.$buttonNext->getHTML().'</div></div>';
	return $output;
}

// buffer
ob_start();
if(is_callable("step_".$currentStep["name"])) {
	$output = call_user_func("step_".$currentStep["name"]);
} else {
	$output = '<br /><i>under construction...</i><br /><br /><br /><br /><br /><br />';
}
$navigation = tpl_navigation($currentStep["id"]);
$bufferedOutput = ob_get_contents();
ob_end_clean();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
	<title>webEdition &bull; initial configuration</title>
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta http-equiv="imagetoolbar" content="no">
	<meta name="generator" content="webEdition Version <?php echo WE_VERSION; ?>">
	<style type="text/css">
	body, div, div.small, table, td, font, li, form {
		font-size:8pt;
	}
	table {font-weight:normal;}
	fieldset {
		border:1px solid #888;
	}
	legend {
		font-weight:bold;
	}
	div.debug {
		position:absolute;
		margin:0px auto;
		background:transparent;
		width:100%;
		height:110px;
		overflow:auto;
		z-index:99;
		font-size:9pt;
		font-weight:normal;
		border-bottom:1px solid #333;
	}
	a {
		color:#000000;
	}
	a:visited {
		color:#000000;
	}
	</style>
	<link href="/webEdition/css/global.php?WE_LANGUAGE=English&amp;WE_BACKENDCHARSET=UTF-8" rel="styleSheet" type="text/css" />
	<?php echo $header; ?>
</head>
<body bgcolor="#386AAB" class="header" onLoad="" style="margin:0px">
<div class="debug"<?php if(isset($_SESSION["debug"])) {echo ' style="display:block;"';} else {echo ' style="display:none;"';} ?>>
<?php echo $bufferedOutput; ?>
</div>
<table width="100%" style="width: 100%; height: 100%;">
	<tr>
		<td align="center" valign="middle">
			<form action="/setup.php?step=<?php echo ($currentStep["id"] + 1) ?>" method="post">
			<input name="step" value="<?php echo $currentStep["id"] + 1 ?>" type="hidden" />
			<table cellpadding="0" cellspacing="0" border="0" style="width:818px;">
				<tr style="height:10px;">
					<td style="width:260px;background-color:#386AAB;"></td>
					<td rowspan="2" style="width:430px;">
						<table border="0" cellpadding="0" cellspacing="0" style="background-image:url(/webEdition/images/info/info.jpg);background-repeat: no-repeat;background-color:#EBEBEB;">
							<tr>
								<td colspan="3" width="432" height="110"><img src="/webEdition/images/pixel.gif" width="432" height="110" border="0"></td>
							</tr>
							<tr>
								<td width="432" colspan="3"><img src="/webEdition/images/pixel.gif" width="432" height="15" border="0"></td>
							</tr>
							<tr>
								<td width="15"><img src="/webEdition/images/pixel.gif" width="15" height="1" border="0"></td>
								<td width="402">
								<?php
								echo $stepTitle;
								echo $output;
								echo $navigation;
								?>
								</td>
								<td width="15"><img src="/webEdition/images/pixel.gif" width="15" height="1" border="0"></td>
							</tr>
							<tr>
								<td width="432" colspan="3"><img src="/webEdition/images/pixel.gif" width="432" height="10" border="0"></td>
							</tr>
							<tr>
								<td width="15"><img src="/webEdition/images/pixel.gif" width="15" height="1" border="0"></td>
								<td width="402" class="small">Version: <?php echo WE_VERSION ?></td>
								<td width="15"><img src="/webEdition/images/pixel.gif" width="15" height="1" border="0"></td>
							</tr>
							<tr>
								<td width="432" colspan="3"><img src="/webEdition/images/pixel.gif" width="432" height="10" border="0"></td>
							</tr>
							<tr>
								<td width="432" colspan="3">
									<img src="/webEdition/images/pixel.gif" width="432" height="10" border="0">
								</td>
							</tr>
						</table>
					</td>
					<td valign="top" style="width:260px;background-image:url(/webEdition/images/login/right.jpg);background-repeat:repeat-y;">
						<img src="/webEdition/images/login/top_r.jpg" width="260" height="10"/>
					</td>
				</tr>
				<tr>
					<td  valign="bottom" style="width:260px;height:296px;background-color:#386AAB;">
						<img src="/webEdition/images/pixel.gif" width="260" height="296" />
					</td>
					<td valign="bottom" style="width:260px;height:296px;background-image:url(/webEdition/images/login/right.jpg);background-repeat:repeat-y;">
						<img src="/webEdition/images/login/bottom_r.jpg" width="260" height="296" />
					</td>
				</tr>
				<tr style="height:10px;">
					<td style="width:260px;"><img src="/webEdition/images/pixel.gif" width="260" height="10" /></td>
					<td style="background-image:url(/webEdition/images/login/bottom.jpg);height:10px;"><img src="/webEdition/images/login/bottom_l.jpg" width="184" height="10" /></td>
					<td style="width:260px;"><img src="/webEdition/images/login/bottom_r2.jpg" width="260" height="10" /></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
</body>
</html>
