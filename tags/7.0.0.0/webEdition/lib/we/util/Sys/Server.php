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
 * @see we_util_Sys
 */

/**
 * utility class for various web servers
 * * @deprecated since version 6.4.0

 * @category   we
 * @package none
 * @subpackage we_util_Sys
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Sys_Server extends we_util_Sys{

	/**
	 * tries to identify the web server and return its product name.
	 * The product name return to the caller is the name used for these classes.
	 * @deprecated since version 6.4.0
	 *
	 * @return string product name or false, if the server product is unknown.
	 */
	public static function product(){
		t_e('deprecated', __FUNCTION__);
		if(self::isApache()){
			return "Apache";
		}
		return (self::isIIS() ? "IIS" : false);
	}

	/**
	 * checks if this is an apache web server
	 *
	 * @param string $version optional parameter to check for a specific apache version.
	 * this method checks the part of the version string that comes after "Apache/"
	 * i.e. "Apache/1.3" in "Apache/1.3.29 (Unix) PHP/4.3.4"
	 * This depends on settings in httpd.conf ServerTokens, possible return values are:
	 * ServerTokens Full - Apache/1.3.29 (Unix) PHP/4.3.4
	 * ServerTokens Full - Apache/2.0.55 (Win32) DAV/2
	 * ServerTokens OS - Apache/2.0.55 (Win32)
	 * ServerTokens Minor - Apache/2.0
	 * ServerTokens Minimal - Apache/2.0.55
	 * ServerTokens Major - Apache/2
	 * ServerTokens Prod - Apache
	 * @deprecated since version 6.4.0
	 *
	 * @return bool true/false
	 */
	public static function isApache($version = ""){
		t_e('deprecated', __FUNCTION__);
		if(!function_exists("apache_get_version")){
			return false;
		}
		if(!$version){
			return true;
		}
		$apacheVersion = apache_get_version();
		if($apacheVersion === false){
			return false;
		}
		return (stristr($version, "Apache/" . $apacheVersion));
	}

	/**
	 * checks if this is a Microsoft IIS
	 * @deprecated since version 6.4.0
	 *
	 * @return bool true/false
	 */
	public static function isIIS(){
		t_e('deprecated', __FUNCTION__);
		return (defined('IIS_RUNNING') && IIS_RUNNING === true);
	}

	/**
	 * Retrieve Hostname for current request
	 * @deprecated since version 6.4.0
	 *
	 * @return string
	 */
	public static function getHost(){
		t_e('deprecated', __FUNCTION__);
		return $_SERVER['SERVER_NAME'];
	}

	/**
	 * Retrieve Protocol for current request
	 * @deprecated since version 6.4.0
	 *
	 * @return string
	 */
	public static function getProtocol(){
		t_e('deprecated', __FUNCTION__);
		return getServerProtocol();
	}

	/**
	 * Retrieve Port for current request
	 * @deprecated since version 6.4.0
	 *
	 * @return integer
	 */
	public static function getPort(){
		t_e('deprecated', __FUNCTION__);
		return $_SERVER['SERVER_PORT'];
	}

	/**
	 * Retrieve complete URI for host and appends an url if set
	 *
	 *  * @deprecated since version 6.4.0

	 * @param string $url  url to append. If empty a uri only with hostname is returned
	 * @return string
	 */
	public static function getHostUri($url = ''){
		t_e('deprecated', __FUNCTION__);
		$uri = getServerUrl();
		return $uri . ($url !== '' ?
				'/' . ltrim($url, '/') : '');
	}

	/**
	 * identify docroot, either via $_SERVER['DOCUMENT_ROOT'] or path reproduction
	 *
	 * @return string complete path of the servers docroot without a trailing slash
	 * @author Alexander Lindenstruth
	 */
	public static function getDocroot(){
		if(!empty($_SERVER['DOCUMENT' . '_ROOT'])){
			return $_SERVER['DOCUMENT' . '_ROOT'];
		}
		// mostly on Microsoft IIS servers (Windows) without DOCUMENT_ROOT:
		return realpath(__DIR__ . "/.." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR);
	}

}
