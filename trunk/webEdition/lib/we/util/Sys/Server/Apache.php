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
 * @subpackage we_util_Sys
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * utility class for apache web server
 *
 * @category   we
 * @package none
 * @subpackage we_util_Sys
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Sys_Server_Apache extends we_util_Sys_Server{

	/**
	 * checks if a specified module is loaded
	 *
	 * @param string $module name of an apache module
	 * @return bool true/false
	 */
	public static function module($module = ""){
		if(!$module || !function_exists("apache_get_modules")){
			return false;
		}
		return in_array($module, apache_get_modules());
	}

	/**
	 * returns the apache version string, if it is available.
	 * This depends on settings in httpd.conf ServerTokens, possible return values are:
	 * ServerTokens Full - Apache/1.3.29 (Unix) PHP/4.3.4
	 * ServerTokens Full - Apache/2.0.55 (Win32) DAV/2
	 * ServerTokens OS - Apache/2.0.55 (Win32)
	 * ServerTokens Minor - Apache/2.0
	 * ServerTokens Minimal - Apache/2.0.55
	 * ServerTokens Major - Apache/2
	 * ServerTokens Prod - Apache
	 *
	 * @return string apache version or (bool)false, if there was an error reading the version string.
	 */
	public static function version(){
		return (function_exists("apache_get_version") ? apache_get_version() : false);
	}

	/**
	 * compares specified apache version with the currently installed apache version
	 * @param int $reference target version to be compared to current apache version
	 * @param string $operator
	 * @see we_util_Sys::_versionCompare()
	 * @example we_util_Sys_Webedition::versionCompare("5501");
	 * @example we_util_Sys_Webedition::versionCompare("5501", "<");
	 */
	public static function versionCompare($reference = "", $operator = ""){
		return parent::_versionCompare($reference, self::version(), $operator);
	}

}
