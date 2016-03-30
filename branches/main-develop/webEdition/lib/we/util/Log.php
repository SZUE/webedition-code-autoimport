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
 * static logging class for logging messages
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_util_Log{
	const ENABLE_LOGGING = true;

	/**
	 * @var Zend_Log object for syslog.php
	 */
	protected static $_syslog = null;

	/**
	 * @var Zend_Log object for other logfiles than syslog.php
	 */
	protected static $_logfile = null;

	/**
	 * logs messages with debuglevel via Zend_Log to webEdition logfile "syslog.php"
	 * @param string $message message to write to log file
	 * @param $filename optional parameter to write the message to another file than "syslog.php"
	 * @param int $errorlevel priority code defined in Zend_Log:
	 * 			EMERG   = 0;  // Emergency: system is unusable
	 * 			ALERT   = 1;  // Alert: action must be taken immediately
	 * 			CRIT    = 2;  // Critical: critical conditions
	 * 			ERR     = 3;  // Error: error conditions
	 * 			WARN    = 4;  // Warning: warning conditions
	 * 			NOTICE  = 5;  // Notice: normal but significant condition
	 * 			INFO    = 6;  // Informational: informational messages
	 * 			DEBUG   = 7;  // Debug: debug messages
	 * @return bool false if logging to file fails (mostly because of insufficient file access rights)
	 */
	public static function log($message = "", $errorlevel = 7, $filename = "syslog"){
		t_e($message);
	}

	/**
	 * static function to log messages with errorlevel to the system's syslog.
	 * @param string $message message to write to log file
	 * @param int $errorlevel priority code defined in Zend_Log:
	 * 			LOG_EMERG   = 0;  // Emergency: system is unusable
	 * 			LOG_ALERT   = 1;  // Alert: action must be taken immediately
	 * 			LOG_CRIT    = 2;  // Critical: critical conditions
	 * 			LOG_ERR     = 3;  // Error: error conditions
	 * 			LOG_WARNING = 4;  // Warning: warning conditions
	 * 			LOG_NOTICE  = 5;  // Notice: normal but significant condition
	 * 			LOG_INFO    = 6;  // Informational: informational messages
	 * 			LOG_DEBUG   = 7;  // Debug: debug messages
	 * @return bool status ofs yslog()
	 * @uses syslog http://de.php.net/manual/de/function.syslog.php
	 */
	public static function syslog($message = "", $errorlevel = 7){
		t_e($message);
	}

	/**
	 * logs messages to php errorlog
	 * @param mixed $message message to write to errorlog
	 * 			$message can be a string as well as an array or an object
	 */
	public static function errorLog($message = ""){
		t_e($message);
	}

	/**
	 * logs current memory usage to syslog
	 * @param string $message optional text message for description
	 */
	public static function memusage($message = ""){
		t_e($message);
	}

	/**
	 * checks if the used log file already exists.
	 * creates the missinglog file in webEdition/log/ with php exit statement at the beginning and .php suffix
	 * if it does not exist already.
	 */
	public static function checkCreateLog($filename = ""){

	}

	/**
	 * checks if either the system wide constant ENABLE_LOGGING or the class constant SELF::ENABLE_LOGGING is set to (bool)true
	 * @return bool true/false
	 */
	public static function isActive(){
		return true;
	}

}
