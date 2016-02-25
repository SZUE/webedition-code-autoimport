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
/**
 * Class we_error_handler
 *
 * Provides a error handler for webEdition.
 */
//essential includes, use these to allow it to be called without we "running"
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_global.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');


/* * ***********************************************************************
 * VARIABLES
 * *********************************************************************** */

if(!defined('E_SQL')){
	define('E_SQL', -1);
}

$GLOBALS['we']['errorhandler'] = array(
	'notice' => defined('WE_ERROR_NOTICES') ? (WE_ERROR_NOTICES == 1 ? true : false) : false,
	'deprecated' => defined('WE_ERROR_DEPRECATED') ? (WE_ERROR_DEPRECATED == 1 ? true : false) : false,
	'warning' => defined('WE_ERROR_WARNINGS') ? (WE_ERROR_WARNINGS == 1 ? true : false) : false,
	'error' => true,
	'sql' => true,
	'display' => false,
	'log' => defined('WE_ERROR_LOG') ? (WE_ERROR_LOG == 1 ? true : false) : true,
	'send' => (defined('WE_ERROR_MAIL') && defined('WE_ERROR_MAIL_ADDRESS')) ? (WE_ERROR_MAIL == 1 ? true : false) : false,
	'shutdown' => 'we',
);

function we_error_setHandleAll(){
	$GLOBALS['we']['errorhandler'] = array(
		'notice' => true,
		'deprecated' => true,
		'warning' => true,
		'error' => true,
		'sql' => true,
		'display' => false,
		'log' => true,
		'send' => (defined('WE_ERROR_MAIL') && defined('WE_ERROR_MAIL_ADDRESS')) ? (WE_ERROR_MAIL == 1 ? true : false) : false,
		'shutdown' => 'we',
	);
}

function we_error_handler($in_webEdition = true){
	static $called = false;
	if($called){
		return;
	}
	$called = true;
	// Get way of how to show errors
	if($in_webEdition){
		$GLOBALS['we']['errorhandler']['display'] = false;
		ini_set('display_errors', false);
		if(!defined('WE_ERROR_LOG')){
			define('WE_ERROR_LOG', 1);
		}
		if(!defined('WE_ERROR_HANDLER')){
			define('WE_ERROR_HANDLER', 1);
		}
		//we want all errors inside WE
		//we_error_setHandleAll();
	} else {
		$GLOBALS['we']['errorhandler']['display'] = defined('WE_ERROR_SHOW') ? (WE_ERROR_SHOW == 1 ? true : false) : true;
	}

	if((defined('WE_ERROR_HANDLER') && WE_ERROR_HANDLER)){
		$_error_level = 0 +
				($GLOBALS['we']['errorhandler']['deprecated'] ? E_DEPRECATED | E_USER_DEPRECATED | E_STRICT : 0) +
				($GLOBALS['we']['errorhandler']['notice'] ? E_NOTICE | E_USER_NOTICE | E_STRICT : 0) +
				($GLOBALS['we']['errorhandler']['warning'] ? E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING : 0) +
				($GLOBALS['we']['errorhandler']['error'] ? E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR : 0);
		error_reporting($_error_level);
		ini_set('error_reporting', $_error_level);

		set_error_handler('error_handler', $_error_level);
		register_shutdown_function('shutdown_handler');
		set_exception_handler('we_exception_handler');
	} else {
		//disable strict & deprecated errors

		$cur_error = error_reporting();
		if(($cur_error & (E_DEPRECATED | E_STRICT) ) > 0){
			$new_error = $cur_error & ~(E_DEPRECATED | E_STRICT);
			error_reporting($new_error);
		}
	}
}

//Note: Errors can only have ONE type - in case of changed typenames, rename DB's enum
function translate_error_type($type){
	switch($type){
		case E_ERROR:
			return 'Error';
		case E_WARNING:
			return 'Warning';
		case E_PARSE:
			return 'Parse error';
		case E_NOTICE:
			return 'Notice';
		case E_CORE_ERROR:
			return 'Core error';
		case E_CORE_WARNING:
			return 'Core warning';
		case E_COMPILE_ERROR:
			return 'Compile error';
		case E_COMPILE_WARNING:
			return 'Compile warning';
		case E_USER_ERROR:
			return 'User error';
		case E_USER_WARNING:
			return 'User warning';
		case E_USER_NOTICE:
			return 'User notice';
		case E_DEPRECATED:
			return 'Deprecated notice';
		case E_STRICT:
			return 'Strict Error';
		case E_USER_DEPRECATED:
			return 'User deprecated notice';
		case E_SQL:
			return 'SQL Error';
		default:
			return 'unknown Error';
	}
}

function getBacktrace(array $skip = array()){
	$_detailedError = $_caller = $_file = $_line = '';

	$_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$cnt = 0;
	$found = false;
	//error handler called directly caused by an error
	foreach($_backtrace as $no => $arr){
		if($arr['function'] === 't_e'){
			unset($_backtrace[$no - 1]);
			$found = true;

			break;
		}
	}
	if(!$found){
		$pos = array_search('error_handler', $skip);
		unset($skip[$pos]);
	}

	foreach($_backtrace as $no => $arr){
		//NOTE: error_handler holds line no & filename of the callee if not called by t_e
		if(in_array($arr['function'], $skip)){
			continue;
		} else if($cnt == 0){ //this is the caller
			$_caller = $arr['function'];
			$_file = (isset($arr['file']) ? str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $arr['file']) : '');
			$_line = (isset($arr['line']) ? $arr['line'] : '');
		}
		$_detailedError .='#' . ($cnt++) . ' ' . $arr['function'] . ' called at [' . (isset($arr['file']) ? str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $arr['file']) : '') . ':' . (isset($arr['line']) ? $arr['line'] : '') . "]\n";
	}
	return array($_detailedError, $_caller, $_file, $_line);
}

/**
 * This function checks the syntax of an email address.
 *
 * @param          string                                  $email
 * *
 * @return         bool
 */
function display_error_message($type, $message, $file, $line, $skipBT = false){
	$detailedError = $_caller = '-';
	if($skipBT === false){
		list($detailedError, $_caller, $file, $line) = getBacktrace(($type == E_SQL ? array('error_showDevice', 'trigger_error', 'error_handler', 'getBacktrace', 'display_error_message') : array('error_showDevice', 'error_handler', 'getBacktrace', 'display_error_message')));
	} else if(is_string($skipBT)){
		$detailedError = $skipBT;
	}

	// Build the error table
	echo '<br /><table bgcolor="#FFFFFF" cellpadding="4" style="text-align:center;border: 1px solid #265da6;width:95%"><colgroup><col sytle="width:10%"/><col style="width:90%" /></colgroup>
		<tr bgcolor="#f7f7f7" style="vertical-align:top">
			<td colspan="2" style="border-bottom: 1px solid #265da6;">An error occurred while executing this script.</td>
		</tr>
	<tr style="vertical-align:top">
		<td style="white-space:nowrap;border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;text-weight:bold;">Error type:</td>
		<td style="border-bottom: 1px solid #265da6;"><i>' . translate_error_type($type) . '</i></td>
	</tr>
	<tr style="vertical-align:top">
			<td style="white-space:nowrap;border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;text-weight:bold;">Error message:</td>
			<td style="border-bottom: 1px solid #265da6;"><i><pre>' . str_replace($_SERVER['DOCUMENT_ROOT'], "", $message) . '</pre></i></td>
	</tr>
	<tr style="vertical-align:top">
			<td style="white-space:nowrap;border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;text-weight:bold;">Script name:</td>
			<td style="border-bottom: 1px solid #265da6;"><i>' . str_replace($_SERVER['DOCUMENT_ROOT'], "", $file) . '</i></td>
	</tr>
	<tr style="vertical-align:top">
			<td style="white-space:nowrap;border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;text-weight:bold;">Line number:</td>
			<td style="border-bottom: 1px solid #265da6;"><i>' . $line . '</i></td>
	</tr>
	<tr style="vertical-align:top">
			<td style="white-space:nowrap;border-right: 1px solid #265da6;text-weight:bold;">Backtrace</td>
			<td>' . str_replace(array("\r", "\n"), '', nl2br($detailedError)) . '</td>
	</tr>
	</table><br />';
}

function we_NiceArray($var, $unindent = ''){
	return preg_replace(array('|Array\n\(|', '|\n\)$|', '|\n(    )' . ($unindent ?  '{' . $unindent . '}':'+' ) . '|','|\n(    )|'), array('', '', "\n","\n\t"), $var);
}

function getVariableMax($var){
	static $max = 65500; //max lenght of text-col in mysql - this is enough debug-data, leave some space...
	switch($var){
		case 'Request':
			$ret = (isset($_REQUEST) ? we_NiceArray(print_r(array_diff_key($_REQUEST, array('user' => '', 'username' => '', 'pass' => '', 'password' => '', 's' => '', 'WE_LOGIN_password', 'WE_LOGIN_username', 'Password', 'Password2')), true), 1) : ' - ');
			break;
		case 'Session':
			if(!isset($_SESSION)){
				$ret = ' - ';
				break;
			}
			$ret = '';
			//FIXME: clone will be reduced to unsetting weS+webuser if all vars have moved
			if(isset($_SESSION['webuser']) && isset($_SESSION['webuser']['ID']) && $_SESSION['webuser']['registered']){
				$ret.= 'webUser: ' .
						we_NiceArray(print_r(array('ID' => $_SESSION['webuser']['ID'], 'Username' => $_SESSION['webuser']['Username'] . '(' . $_SESSION['webuser']['Forename'] . ' ' . $_SESSION['webuser']['Surname'] . ')'), true)).
						"----------------------------------------\n";
			}
			if(isset($_SESSION['user']) && isset($_SESSION['user']['ID'])){
				$ret.= 'webEdition-User: ' .
						we_NiceArray(print_r(array('ID' => $_SESSION['user']['ID'], 'Username' => $_SESSION['user']['Username']), true)).
						"----------------------------------------\n";
			}

			if(isset($_SESSION['weS'])){
				$ret.=  "Internal data:\n" .
						we_NiceArray(print_r(array_diff_key($_SESSION['weS'], array('versions' => '', 'prefs' => '', 'we_data' => '', 'perms' => '', 'webuser' => '')), true), 1).
						"----------------------------------------\n";
			}

			if(isset($_SESSION['perms'])){
				$ret.="Effective Permissions:\n" . we_NiceArray(print_r(array_filter($_SESSION['perms']), true)) .
						"\n------------------------------------\n";
			}
			$ret.= we_NiceArray(print_r(array_diff_key($_SESSION, array('prefs' => '', 'perms' => '', 'webuser' => '', 'weS' => '')), true), 1);

			break;
		case 'Global':
			if(!isset($GLOBALS)){
				$ret = ' - ';
				break;
			}
			$ignore = array('GLOBALS', '_GET', '_POST', '_REQUEST', '_COOKIE', '_FILES', '_SERVER', '_SESSION',
				'we', 'DB_WE', 'we_doc', 'WE_MAIN_DOC', 'loader', 'WE_MAIN_DOC_REF');
			$clone = array();
			foreach($GLOBALS as $key => $val){
				if(!in_array($key, $ignore)){
					$clone[] = $val;
				}
			}
			$ret.= print_r($clone, true);

			break;
		case 'Server':
			$ret = (isset($_SERVER) ? we_NiceArray(print_r($_SERVER, true)) : ' - ');
			break;
		default:
			$ret = '';
	}

	if(strlen($ret) > $max){
		$ret = substr($ret, 0, $max) . "\n[...]";
	}
	return $var . '="' . escape_sql_query($ret) . '"';
}

function log_error_message($type, $message, $file, $_line, $skipBT = false){
	static $max = 500;
	if(--$max < 0){
		//don't log more messages per request
		return;
	}
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_db_tools.inc.php');
	$_detailedError = $_caller = '-';
	if($skipBT === false){
		list($_detailedError, $_caller, $file, $_line) = getBacktrace(($type == E_SQL ? array('error_showDevice', 'trigger_error', 'error_handler', 'getBacktrace', 'log_error_message') : array('error_showDevice', 'error_handler', 'getBacktrace', 'log_error_message')));
	} else if(is_string($skipBT)){
		$_detailedError = $skipBT;
	}

	// Error type
	$_type = translate_error_type($type);

	// Error message
	$_text = str_replace($_SERVER['DOCUMENT_ROOT'], 'SECURITY_REPL_DOC_ROOT', $message);

	// Script name
	$_file = str_replace($_SERVER['DOCUMENT_ROOT'], 'SECURITY_REPL_DOC_ROOT', $file);

	// Log the error
	if(defined('DB_HOST') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_DATABASE')){
		$logVars = array('Request', 'Session', 'Server');
		$tbl = defined('ERROR_LOG_TABLE') ? ERROR_LOG_TABLE : TBL_PREFIX . 'tblErrorLog';
		$_query = 'INSERT INTO ' . $tbl . ' SET Type="' . escape_sql_query($_type) . '",
			`Function`="' . escape_sql_query($_caller) . '",
			File="' . escape_sql_query($_file) . '",
			Line=' . intval($_line) . ',
			Text="' . escape_sql_query($_text) . '",
			Backtrace="' . escape_sql_query($_detailedError) . '"';
		if(isset($GLOBALS['DB_WE'])){
			$db = new DB_WE();
			if(!$db->query($_query)){
				mail_error_message($type, 'Cannot log error! Query failed: ' . $message, $file, $_line, $skipBT);
			} else {
				$id = $db->getInsertId();
				foreach($logVars as $var){
					$db->query('UPDATE ' . $tbl . ' SET ' . getVariableMax($var) . ' WHERE ID=' . $id);
				}
			}
		} else {
			$hasI = function_exists('mysqli_connect') ? 'i' : '';
			$connect = 'mysql' . $hasI . '_connect';
			$select = 'mysql' . $hasI . '_select_db';
			$err = 'mysql' . $hasI . '_error';
			$query = 'mysql' . $hasI . '_query';
			$insert = 'mysql' . $hasI . '_insert_id';
			$close = 'mysql' . $hasI . '_close';

			$link = $connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Cannot log error! Could not connect: ' . $err());
			$select($link, DB_DATABASE) or die('Cannot log error! Could not select database.');
			if($query($link, $_query) === FALSE){
				mail_error_message($type, 'Cannot log error! Query failed: ' . $message, $file, $_line, $skipBT);
				//die('Cannot log error! Query failed: ' . mysql_error());
			} else {
				$id = $insert($link);
				foreach($logVars as $var){
					$query($link, 'UPDATE ' . $tbl . ' SET ' . getVariableMax($var) . ' WHERE ID=' . $id);
				}
			}
			$close($link);
		}
	} else {
		mail_error_message($type, 'Cannot log error! Database connection not known: ' . $message, $file, $line, $skipBT);
		//die('Cannot log error! Database connection not known.');
	}
	return (isset($id)) ? $id : false;
}

function mail_error_message($type, $message, $file, $line, $skipBT = false, $insertID = false){
	static $max = 15;
	if(--$max < 0){
		//don't mail more than this
		return;
	}
	$detailedError = $_caller = '-';
	if($skipBT === false){
		list($detailedError, $_caller, $file, $line) = getBacktrace(($type == E_SQL ? array('error_showDevice', 'trigger_error', 'error_handler', 'getBacktrace', 'mail_error_message') : array('error_showDevice', 'error_handler', 'getBacktrace', 'mail_error_message')));
	} else if(is_array($skipBT)){
		list($detailedError, $_caller, $file, $line) = $skipBT;
	}

	$ttype = translate_error_type($type);

	// Build the error table
	$_detailedError = "An error occurred while executing a script in webEdition.\n\n\n" .
			($insertID && function_exists('getServerUrl') ?
					getServerUrl() . WEBEDITION_DIR . 'errorlog.php?function=pos&ID=' . $insertID . "\n\n" : '') .
// Domain
			'webEdition address: ' . $_SERVER['SERVER_NAME'] . ",\n\n" .
			'URI: ' . $_SERVER['REQUEST_URI'] . "\n" .
			// Error type
			'Error type: ' . $ttype . "\n" .
			// Error message
			'Error message: ' . str_replace($_SERVER['DOCUMENT_ROOT'], 'SECURITY_REPL_DOC_ROOT', $message) . "\n" .
			// Script name
			'Script name: ' . str_replace($_SERVER['DOCUMENT_ROOT'], 'SECURITY_REPL_DOC_ROOT', $file) . "\n" .
			// Line
			'Line number: ' . $line . "\n" .
			'Caller: ' . $_caller . "\n" .
			'Backtrace: ' . $detailedError;

	// Log the error
	if(defined('WE_ERROR_MAIL_ADDRESS')){
		if(!mail(WE_ERROR_MAIL_ADDRESS, $ttype . ': ' . $_SERVER['SERVER_NAME'] . '(webEdition)', $_detailedError)){
			if(in_array($type, array('E_ERROR', 'E_CORE_ERROR', 'E_COMPILE_ERROR', 'E_USER_ERROR'))){
				echo 'Cannot log error! Could not send e-mail: <pre>' . $_detailedError . '</pre>';
			}
		}
	} else {
		if(in_array($type, array('E_ERROR', 'E_CORE_ERROR', 'E_COMPILE_ERROR', 'E_USER_ERROR'))){
			echo 'Cannot log error! Could not send e-mail due to no known recipient: <pre>' . $_detailedError . '</pre>';
		}
	}
}

function error_showDevice($type, $message, $file, $line, $skip = false){
	// Display error?
	if(isset($GLOBALS['we']['errorhandler']) && $GLOBALS['we']['errorhandler']['display']){
		display_error_message($type, $message, $file, $line, $skip);
	}

	// Log error?
	if(!isset($GLOBALS['we']['errorhandler']) || $GLOBALS['we']['errorhandler']['log']){
		$insertID = log_error_message($type, $message, $file, $line, $skip);
	}

	// Mail error?
	if(isset($GLOBALS['we']['errorhandler']) && !empty($GLOBALS['we']['errorhandler']['send'])){
		mail_error_message($type, $message, $file, $line, $skip, isset($insertID) ? $insertID : false);
	}
}

function error_handler($type, $message, $file, $line, $context){
	// Don't respond to the error if it was suppressed with a '@'
	if(error_reporting() == 0){
		return;
	}
	if(strpos($message, 'MYSQL-ERROR') === 0){
		$type = E_SQL;
		if(!$GLOBALS['we']['errorhandler']['sql']){
			//sql-handling disabled
			return true;
		}
	}

	switch($type){
		case E_NOTICE:
		case E_USER_NOTICE:
			if($GLOBALS['we']['errorhandler']['notice']){
				error_showDevice($type, $message, $file, $line);
			}
			break;

		case E_SQL:
		case E_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_USER_WARNING:
			if($GLOBALS['we']['errorhandler']['warning']){
				error_showDevice($type, $message, $file, $line);
			}

			break;

		case E_ERROR:
		case E_PARSE:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
			if($GLOBALS['we']['errorhandler']['error']){
				error_showDevice($type, $message, $file, $line, true);
			}

			// Stop execution
			return false;
		case E_DEPRECATED:
		case E_USER_DEPRECATED:
			if($GLOBALS['we']['errorhandler']['deprecated']){
				error_showDevice($type, $message, $file, $line);
			}
			break;
		default:
	}
	//Error handled
	return true;
}

function shutdown_handler(){
	if(isset($GLOBALS['we']['errorhandler']) && $GLOBALS['we']['errorhandler']['shutdown'] != 'we'){
		return;
	}
	$error = error_get_last();
	if(is_array($error)){
		switch($error['type']){
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				error_handler($error['type'], $error['message'] . "\n" . print_r($error, true), $error['file'], $error['line'], null);
			default:
		}
	}
}

function we_exception_handler($exception){
	$type = E_ERROR;
	$message = $exception->getMessage();
	$file = $exception->getFile();
	$line = $exception->getLine();
	$bt = $exception->getTraceAsString();

	error_showDevice($type, $message, $file, $line, $bt);
}
