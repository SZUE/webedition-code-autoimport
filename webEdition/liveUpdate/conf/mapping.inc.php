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
 * @category   webEdition
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */ 

/*
 * map needed variables for the program here, for example map version number
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_version.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$LU_Variables = array(

	// always needed variables
	'clientVersion' => WE_VERSION,
	'clientSubVersion' => WE_SVNREV,
	'clientVersionSupp' => (defined("WE_VERSION_SUPP")) ? WE_VERSION_SUPP : '',
	'clientVersionSuppVersion' => (defined("WE_VERSION_SUPP_VERSION")) ? WE_VERSION_SUPP_VERSION : '',
	'clientVersionBranch' => (defined("WE_VERSION_BRANCH")) ? WE_VERSION_BRANCH : '',
	'clientPhpVersion' => phpversion(),
	'clientPhpExtensions' => implode(',',get_loaded_extensions()),
	'clientPcreVersion' => (defined("PCRE_VERSION")) ? PCRE_VERSION:'',
	'clientMySQLVersion' => getMysqlVer(false),
	'clientDBcharset' => (defined("DB_CHARSET") && DB_CHARSET != "") ? DB_CHARSET : '', 
	'clientDBcollation' => (defined("DB_COLLATION") && DB_COLLATION != "") ? DB_COLLATION : '',
	'clientServerSoftware' => $_SERVER["SERVER_SOFTWARE"],
	'clientUid' => (defined('UID') ? UID : false),
	'clientSyslng' => WE_LANGUAGE,
	'clientLng' => $GLOBALS['WE_LANGUAGE'],
	'clientExtension' => '.php',
	'clientDomain' => urlencode($_SERVER['SERVER_NAME']),
	'clientInstalledModules' => array_merge($_we_installed_modules, $_pro_modules),
	'clientInstalledLanguages' => liveUpdateFunctions::getInstalledLanguages(),
	'clientInstalledAppMeta' => weToolLookup::getAllTools(true, false, true),
	'clientInstalledAppTOC' => we_app_Common::readAppTOCasString(),
	'clientUpdateUrl' => liveUpdateHttp::getServerProtocol() . SERVER_NAME . (defined("HTTP_PORT") ? ":" . HTTP_PORT : "" ) . $_SERVER["PHP_SELF"],
	'clientContent' => false,
	'clientEncoding' => 'none'
);


// These request variables listed here are NOT submitted to the server - fill it
// to keep requests small
$LU_IgnoreRequestParameters = array(
	'we_mode',
	'cookie',
	'treewidth_main',
	session_name(),
	'we'.session_id()
);
?>