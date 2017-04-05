<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*
 * map needed variables for the program here, for example map version number
 */

$LU_Variables = [
	'clientVersion' => WE_VERSION,
	'clientSubVersion' => WE_SVNREV,
	'clientVersionName' => (defined('WE_VERSION_NAME')) ? WE_VERSION_NAME : '',
	'clientVersionSupp' => (defined('WE_VERSION_SUPP')) ? WE_VERSION_SUPP : '',
	'clientVersionSuppVersion' => (defined('WE_VERSION_SUPP_VERSION')) ? WE_VERSION_SUPP_VERSION : '',
	'clientHotfixNr' => (defined('WE_VERSION_HOTFIX_NR')) ? WE_VERSION_HOTFIX_NR : 0,
	'clientVersionBranch' => (defined('WE_VERSION_BRANCH')) ? WE_VERSION_BRANCH : '',
	'clientPhpVersion' => PHP_VERSION,
	'clientPhpExtensions' => implode(',', get_loaded_extensions()),
	'clientPcreVersion' => (defined('PCRE_VERSION')) ? PCRE_VERSION : '',
	'clientMySQLVersion' => we_database_base::getMysqlVer(false),
	'clientDBcharset' => we_database_base::getCharset(),
	'clientDBcollation' => we_database_base::getCollation(),
	'clientServerSoftware' => $_SERVER['SERVER_SOFTWARE'],
	'clientSyslng' => WE_LANGUAGE,
	'clientLng' => $GLOBALS['WE_LANGUAGE'] . ($GLOBALS['WE_BACKENDCHARSET'] === 'UTF-8' ? '_UTF-8' : ''),
	'clientExtension' => DEFAULT_DYNAMIC_EXT,
	'clientDomain' => urlencode($_SERVER['SERVER_NAME']),
	'clientInstalledModules' => we_base_moduleInfo::getUserEnabledModules(),
	'clientInstalledLanguages' => liveUpdateFunctions::getInstalledLanguages(),
	'clientInstalledAppMeta' => we_tool_lookup::getAllTools(true, false, true),
	'clientUpdateUrl' => getServerUrl() . $_SERVER['SCRIPT_NAME'],
	'clientContent' => false,
	'clientEncoding' => 'none',
	'clientSessionName' => session_name(),
	'clientSessionID' => session_id(),
	'testUpdate' => empty($_SESSION['weS']['testUpdate']) ? 0 : $_SESSION['weS']['testUpdate'],
];

// These request variables listed here are NOT submitted to the server - fill it
// to keep requests small
$LU_IgnoreRequestParameters = [
	'we_mode',
	'cookie',
	'treewidth_main',
	session_name(),
	'we' . session_id()
 ];
