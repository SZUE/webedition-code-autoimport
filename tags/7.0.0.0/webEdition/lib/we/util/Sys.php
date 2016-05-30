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
	  * @deprecated since version 6.4.0
	 */
	protected static function _versionCompare($version = "", $reference = "", $operator = null){
t_e('deprecated',__FUNCTION__);
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
