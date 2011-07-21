<?php
/**
 * webEdition Installer
 *
 * @category   WE
 * @package    WE_installer
 * @version    ###VERSION###

*
 * VERSION HISTORY:
 * 
 * 2.5.0.0
 * 2010-02-23
 * - Various bugsfixes regarding all parts and steps of the online installation process

 * - Implemented some updates needed for installing webEdition 6 and pageLogger 1.6
 * - Implemented a slightly different mechanism for executing sql queries

 * - Included webEdition 5, 6 and pageLogger into one single installer

 * - Various updates needed for changes in the server application
 
 * - Facelifting of the main screen and the installers
 
 * - Introduced new version numbers for the online installer, starting at 2.0.0.0
 
 * The old one is treated as "1.0.0.0"
 * 
 * 2.0.0.1
 * - updated layout
 * - renamed to "webEdition installer"
 * 
 * 2.5.0.0
 * - the webEdition installer is now a subproject of webEdition
 * - changelog moved to http://documentation.webedition.org
 */
 	
 	$LU_Version = "###VERSION###";
	
	//ini_set("display_errors", "Off");
	//ini_set("error_reporting", 0);
	
	if (isset($_REQUEST["phpinfo"])) {
		echo '<body style="margin:0px; padding:0px;"><div style="background:transparent url(./OnlineInstaller/img/leLayout/bgcontent.gif);">';
		phpinfo();
		echo '</div></body>';
		exit;

	}

	session_cache_limiter('leOnlineInstaller');
	session_start();
	require("./OnlineInstaller/includes/constants.inc.php");
	require(LE_ONLINE_INSTALLER_PATH . "/includes/library.inc.php");

	// workaround for servers where HTTP_HOST is always set to www.domain.tld irrespective of the browser's http-request url
	// redirect to HTTP_HOST if HTTP_HOST != SCRIPT_URI-Domain (if SCRIPT_URI is set)
	if (!empty($_SERVER["SCRIPT_URI"])) {
		$referer = parse_url($_SERVER["SCRIPT_URI"]);
		if ($_SERVER["HTTP_HOST"] != $referer["host"]) {
			$protocol = "http" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] =='on' ? "s" : "") . "://";
			header("location: ".$protocol.$_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER["QUERY_STRING"]);
		}
	}
	
	// initialise OnlineInstaller
	$OnlineInstaller = new leOnlineInstaller();
	$_REQUEST["le_installer_version"] = $LU_Version;
	/**
	 * Online Installation Applications
	 *
	 */

	if(file_exists(LE_INSTALLER_PATH . "/OnlineInstaller.log.php") && is_file(LE_INSTALLER_PATH . "/OnlineInstaller.log.php")) {
		require_once(LE_INSTALLER_PATH . "/OnlineInstaller.log.php");
		// Setting the default application
		if(isset($app)) {
			define("LE_DEFAULT_APPLICATION", $app);
			
		} else {
			define("LE_DEFAULT_APPLICATION", "webEdition");
		}
		
	}
	
	// Installer for Update Server Beta:
	$leApplicationList = array(
		'webEdition' => array( // webEdition 6 (Open Source)
			'Name' => $lang["Application"]["webEdition"]["name"],
			'Description' => $lang["Application"]["webEdition"]["description"],
			'Longdescription' => $lang["Application"]["webEdition"]["longdescription"],
			'Link' => $lang["Application"]["webEdition"]["link"],
			'UpdateServer' => 'update.webedition.org',
			//'UpdateServer' => 'update.alex.hq.living-e.zz', // local mirror
			'UpdateScript' => '/server/we/onlineInstallation.p' . 'hp',
			'testUpdate' => false,
		),
		'webEditionBeta' => array( // webEdition 6 (Open Source)
			'Name' => $lang["Application"]["webEditionBeta"]["name"],
			'Description' => $lang["Application"]["webEditionBeta"]["description"],
			'Longdescription' => $lang["Application"]["webEditionBeta"]["longdescription"],
			'Link' => $lang["Application"]["webEditionBeta"]["link"],
			'UpdateServer' => 'update.webedition.org',
			//'UpdateServer' => 'update.alex.hq.living-e.zz', // local mirror
			'UpdateScript' => '/server/we/onlineInstallation.p' . 'hp',
			'testUpdate' => true,
		)  /*,
		'webEdition5' => array( // webEdition 5
			'Name' => $lang["Application"]["webEdition5"]["name"],
			'Description' => $lang["Application"]["webEdition5"]["description"],
			'Longdescription' => $lang["Application"]["webEdition5"]["longdescription"],
			'Link' => $lang["Application"]["webEdition5"]["link"],
			'UpdateServer' => 'update.webedition.org',
			//'UpdateServer' => 'update.alex.hq.living-e.zz', // local mirror
			'UpdateScript' => '/we5/onlineInstallation.p' . 'hp',
		), 
		'pageLogger' => array( // pageLogger 1.6 (Open Source)
			'Name' => $lang["Application"]["pageLogger"]["name"],
			'Description' => $lang["Application"]["pageLogger"]["description"],
			'Longdescription' => $lang["Application"]["pageLogger"]["longdescription"],
			'Link' => $lang["Application"]["pageLogger"]["link"],
			'UpdateServer' => 'update.webedition.org',
			//'UpdateServer' => 'update.alex.hq.living-e.zz', // local mirror
			'UpdateScript' => '/server/pl/onlineInstallation.p' . 'hp',
			'testUpdate' => false,
		),*/
	);
	
	// choose the application which have to be installed
	if(!isset($_SESSION['leApplication'])) {
		if(sizeof($GLOBALS['leApplicationList']) >= 1) {
			$temp = array_keys($leApplicationList);
			$_SESSION['leApplication'] =  $temp[0];

		} elseif(defined("LE_DEFAULT_APPLICATION")) {
			$_SESSION['leApplication'] =  LE_DEFAULT_APPLICATION;

		}

	}

	if(isset($_REQUEST['changeApplication']) && array_key_exists($_REQUEST['changeApplication'], $leApplicationList)) {
		$_SESSION['leApplication'] = $_REQUEST['changeApplication'];

	}

	// Beta-Version an- abwählen
	$_SESSION['testUpdate'] = $leApplicationList[$_SESSION['leApplication']]['testUpdate'];
	
	
	/**
	 * End Online Installation Applications
	 *
	 */


	$OnlineInstaller->initialize();


	if (version_compare(phpversion(), '5.0') < 0) {
		eval('
			function clone($object) {
				return $object;
			}
		');
	}

	function _getServerProtocol() {

		$_prot = "http";

		if(isset($_SERVER["HTTPS"]) && strtoupper($_SERVER["HTTPS"]) == "ON"){
			$_prot = "https";
		}
		return $_prot . "://";
	}



	$_tmp = pathinfo(__FILE__);
	if ( !isset($_tmp["extension"]) || $_tmp["extension"] =='') {
		$_tmp["extension"] = 'php';
	}
	$LU_Variables = array(

		// always needed variables
		'clientIsOnlineInstaller' => true,
		'clientPhpVersion' => phpversion(),
		'clientPhpExtensions' => implode(',',get_loaded_extensions()),
		'clientPcreVersion' => (defined("PCRE_VERSION")) ? PCRE_VERSION:'',
		'clientServerSoftware' => $_SERVER["SERVER_SOFTWARE"],
		'clientHttpHost' => $_SERVER['HTTP_HOST'],
		'clientLeWizard' => isset($_REQUEST["leWizard"]) ? $_REQUEST["leWizard"] : "",
		'clientLeStep' => isset($_REQUEST["leStep"]) ? $_REQUEST["leStep"] : "",
		'clientSyslng' => $_SESSION["leInstallerLanguage"],
		'clientLng' => $_SESSION["leInstallerLanguage"],
		'clientExtension' => ".{$_tmp["extension"]}",
		'clientDomain' => urlencode($_SERVER['HTTP_HOST']),
		'clientInstalledModules' => array(),
		'clientInstalledLanguages' => array($_SESSION["leInstallerLanguage"]),
		'clientUpdateUrl' => str_replace("\\", "/", _getServerProtocol() . $_SERVER['HTTP_HOST']) . $_SERVER['SCRIPT_NAME'], // REQUEST_URI is not always available so we use PHP_SELF 
		'clientContent' => (isset($_SESSION["le_install_demo"]) ? $_SESSION["le_install_demo"] : true),
		'clientEncoding' => (isset($_SESSION["le_Encoding"]) ? $_SESSION["le_Encoding"] : "none"),
		'clientSessionName' => session_name(),
		'clientSessionID' => session_id()
	);
	unset($_tmp);

	// These request variables listed here are NOT submitted to the server - fill it
	// to keep requests small
	$LU_IgnoreRequestParameters = array(
		'we_mode',
		'le_testCookie',
		'treewidth_main',
		session_name(),
		'we'.session_id(),
	);

	$LU_ParameterNames = array(
		'liveUpdateSession',
		'leWizard',
		'leStep',
		'update_cmd',
		'detail',
		'position',
		'part',
		'clientLng',
		'clientTargetVersionNumber',
		'decreaseSpeed'
	);

	if (isset($_REQUEST["leWizard"])) {
		if(isset($_REQUEST['debug'])) {
			ini_set("display_errors", "On");
			ini_set("error_reporting", E_ALL);
			
		}
		print $OnlineInstaller->executeStep();

	} else {
		include(LE_ONLINE_INSTALLER_PATH . "/includes/template.inc.php");

	}


?>