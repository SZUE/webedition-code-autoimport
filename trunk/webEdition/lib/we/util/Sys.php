<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Class to get informations about the system environment
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Sys{

	/**
	 * protected method for comparing two specified version numbers with each other
	 *
	 * @param string $version version number compared with the reference version number
	 * @param string $reference reference version number to compare with
	 * @param string operator
	 * 			possible values for $rel: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
	 * @return if no operator is used: -1 (if older), 0 (if equal) or 1 (if newer)
	 * @return bool with operator
	 * @link http://php.net/manual/en/function.version-compare.php documentation of version_compare()
	 * @example we_util_Sys::_versionCompare("1.0", "1,1"); // will return -1
	 * @example we_util_Sys::_versionCompare("1.1", "1,1"); // will return 0
	 * @example we_util_Sys::_versionCompare("1.1", "1,0"); // will return 1
	 * @example we_util_Sys::_versionCompare("1.0", "1,1", "<"); // will return (bool)true
	 * @example we_util_Sys::_versionCompare("1.0", "1,1", ">"); // will return (bool)false
	 */
	protected static function _versionCompare($version = "", $reference = "", $operator = null){
		/*
		 * will soon replace the code of following methods:
		 * - we_util_Sys_Webedition::versionCompare()
		 * - we_util_Sys_Webedition::toolVersionCompare()
		 * - we_util_Sys_Php::versionCompare()
		 * - we_util_Sys_Db_Mysql::versionCompare()
		 * they'll call this method here insead of implementing the functionality themselves.
		 */
		return (!$version || !$reference ?
				false :
				version_compare($version, $reference, $operator)
			);
	}

}

// example usage of sys classes:
/*
we_util_Sys_Server::product(); // gibt den Namen des Webserver-Produktes zurueck
we_util_Sys_Server::isApache();
we_util_Sys_Server::isApache("2");
we_util_Sys_Server::isIIS(); // checks if IIS_RUNNING is defined and (bool)true

we_util_Sys_Server_Apache::version(); // Version des Webservers, uses apache_get_version()
we_util_Sys_Server_Apache::module(); // vgl.: we_util_Sys_Php::extension(), uses apache_get_modules()

we_util_Sys_Server_IIS::function(); // IIS class not implemented (yet)

if(we_util_Sys_Webedition::version("customer")) {}
if(we_util_Sys_Webedition::module("customer")) {}
if(we_util_Sys_Webedition::moduleLicense("customer")) {}
if(we_util_Sys_Webedition::tool("customer")) {}
if(we_util_Sys_Webedition::toolLicense("customer")) {}

if(we_util_Sys_Php::version("customer")) {}
if(we_util_Sys_Php::ini("customer")) {}
if(we_util_Sys_Php::extension("customer")) {}

*/