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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


/**
 * Class we_error_handler
 *
 * Provides a error handler for webEdition.
 */

include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/conf/we_conf.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/conf/we_conf_global.inc.php');

/*************************************************************************
 * VARIABLES
 *************************************************************************/

$_error_notice = false;
$_error_deprecated = false;
$_error_warning = false;
$_error_error = true;

$_display_error = true;
$_log_error = false;

$_send_error = false;
$_send_address = '';

if (!defined('E_SQL')) {
	define('E_SQL', -1);
}

/*************************************************************************
 * FUNCTIONS
 *************************************************************************/

function we_error_handler($in_webEdition = true) {
	global $_error_notice, $_error_deprecated, $_error_warning, $_error_error, $_display_error, $_log_error, $_send_error, $_send_address;

	// Get error types to be handled
	$_error_notice = defined('WE_ERROR_NOTICES') ? (WE_ERROR_NOTICES == 1 ? true : false) : false;
	$_error_deprecated = defined('WE_ERROR_DEPRECATED') ? (WE_ERROR_DEPRECATED == 1 ? true : false) : false;
	$_error_warning = defined('WE_ERROR_WARNINGS') ? (WE_ERROR_WARNINGS == 1 ? true : false) : false;
	$_error_error = defined('WE_ERROR_ERRORS') ? (WE_ERROR_ERRORS == 1 ? true : false) : true;

	// Get way of how to show errors
	if ($in_webEdition) {
		$_display_error = false;
		if (!defined('WE_ERROR_HANDLER_SET')){
			define('WE_ERROR_HANDLER_SET',1);
		}
	} else {
		$_display_error = defined('WE_ERROR_SHOW') ? (WE_ERROR_SHOW == 1 ? true : false) : true;
	}
	$_log_error = defined('WE_ERROR_LOG') ? (WE_ERROR_LOG == 1 ? true : false) : true;

	$_send_error = (defined('WE_ERROR_MAIL') && defined('WE_ERROR_MAIL_ADDRESS')) ? (WE_ERROR_MAIL == 1 ? true : false) : false;
	$_send_address = (defined('WE_ERROR_MAIL') && defined('WE_ERROR_MAIL_ADDRESS')) ? WE_ERROR_MAIL_ADDRESS : '';

	// Check PHP version
	if (version_compare(PHP_VERSION, '5.2.4') < 0) {
		display_error_message(E_ERROR, 'Unable to launch webEdition - PHP 5.2.4 or higher required!', '/webEdition/we/we_classes/base/we_error_handler.inc.php', 69);
		exit();
	}

	if (defined('WE_ERROR_HANDLER') && (WE_ERROR_HANDLER == 1)) {
		$_error_level = 0 +
			($_error_deprecated && defined('E_DEPRECATED') ? E_DEPRECATED|E_USER_DEPRECATED|E_STRICT : 0) +
			($_error_notice ? E_NOTICE|E_USER_NOTICE : 0) +
			($_error_warning ? E_WARNING|E_CORE_WARNING|E_COMPILE_WARNING|E_USER_WARNING : 0) +
			($_error_error ? E_ERROR|E_PARSE|E_CORE_ERROR|E_COMPILE_ERROR|E_USER_ERROR|E_RECOVERABLE_ERROR : 0);
		error_reporting($_error_level);
		ini_set('display_errors', $_display_error);
		set_error_handler('error_handler',$_error_level);
	} else {
		//disable strict & deprecated errors
		if (version_compare(PHP_VERSION, '5.3.0') >= 0){
			$cur_error = error_reporting();
			if (($cur_error & (E_DEPRECATED|E_STRICT) ) > 0 ) {
				$new_error = $cur_error & ~(E_DEPRECATED|E_STRICT);
				$old_error = error_reporting($new_error);
			}
		}
	}
}

//Note: Errors can only have ONE type - in case of changed typenames, rename DB's enum
function translate_error_type($type) {
	global $_error_notice, $_error_warning, $_error_error, $_display_error, $_log_error, $_send_error, $_send_address;
	if (!defined('E_STRICT')) {
		define('E_STRICT', 2048);
	}

	if (!defined('E_DEPRECATED')) {
		define('E_DEPRECATED', 8192);
	}

	if (!defined('E_USER_DEPRECATED')) {
		define('E_USER_DEPRECATED', 16384);
	}

	switch ($type) {
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

function getBacktrace($skip=0){
	++$skip;//don't count ourself
	$_detailedError=$_caller='';

	$_backtrace=debug_backtrace();

	foreach($_backtrace AS $no=>$arr){
		if($no<$skip){
			continue;
		}else if($no==$skip){ //this is the caller
			$_caller=$arr['function'];
			$_file=(isset($arr['file'])?str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $arr['file']):'');
			$_line=(isset($arr['line'])?$arr['line']:'');
			if($_caller=='error_handler'&&$_line==''){
				++$no;
				continue;
			}
		}
		$_detailedError .='#'.($no-$skip).' '.$arr['function'].' called at ['.(isset($arr['file'])?str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $arr['file']):'').':'.(isset($arr['line'])?$arr['line']:'')."]\n";
	}
	return array($_detailedError,$_caller,$_file,$_line);
}

/**
 * This function checks the syntax of an email address.
 *
 * @param          string                                  $email
 * *
 * @return         bool
 */

function display_error_message($type, $message, $file, $line) {
	global $_error_notice, $_error_deprecated, $_error_warning, $_error_error, $_display_error, $_log_error, $_send_error, $_send_address;
	if(strpos($message,'MYSQL-ERROR')===0){
		$type=E_SQL;
	}

	list($detailedError,$_caller,$file,$line)=getBacktrace(($type==E_SQL?3:1));

	// Build the error table
	$_detailedError  = '<br /><table align="center" bgcolor="#FFFFFF" cellpadding="4" cellspacing="0" style="border: 1px solid #265da6;" width="95%"><colgroup><col width="10%"/><col width="90%" /></colgroup>';
	$_detailedError .= '	<tr bgcolor="#f7f7f7" valign="top">';
	$_detailedError .= '		<td colspan="2" style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">An error occurred while executing this script.</font></td>';
	$_detailedError .= '	</tr>';

	// Error type
	$_detailedError .= '	<tr valign="top">';
	$_detailedError .= '		<td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Error type:</b></font></td>';
	$_detailedError .= '		<td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . translate_error_type($type) . '</i></font></td>';
	$_detailedError .= '	</tr>';

	// Error message
	$_detailedError .= '	<tr valign="top">';
	$_detailedError .= '		<td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Error message:</b></font></td>';
	$_detailedError .= '		<td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . str_replace($_SERVER["DOCUMENT_ROOT"], "", $message) . '</i></font></td>';
	$_detailedError .= '	</tr>';

	// Script name
	$_detailedError .= '	<tr valign="top">';
	$_detailedError .= '		<td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Script name:</b></font></td>';
	$_detailedError .= '		<td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . str_replace($_SERVER["DOCUMENT_ROOT"], "", $file) . '</i></font></td>';
	$_detailedError .= '	</tr>';

	// Line
	$_detailedError .= '	<tr valign="top">';
	$_detailedError .= '		<td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Line number:</b></font></td>';
	$_detailedError .= '		<td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . $line . '</i></font></td>';
	$_detailedError .= '	</tr>';

	// Backtrace
	$_detailedError .= '	<tr valign="top">';
	$_detailedError .= '		<td nowrap="nowrap" style="border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Backtrace</b></font></td>';
	$_detailedError .= '		<td ><font face="Verdana, Arial, Helvetica, sans-serif" size="2">';

	$detailedError=preg_replace("|[\r\n]|",'',nl2br($detailedError));
	$_detailedError .= $detailedError;
	$_detailedError .= ' 	</font></td>';
	$_detailedError .= '	</tr>';

	// Finalize table
	$_detailedError .= '</table><br />';

	// Display the error
	print $_detailedError;
}

function log_error_message($type, $message, $file, $_line) {
	global $_error_notice, $_error_deprecated, $_error_warning, $_error_error, $_display_error, $_log_error, $_send_error, $_send_address;

	if(strpos($message,'MYSQL-ERROR')===0){
		$type=E_SQL;
	}
	list($_detailedError,$_caller,$file,$_line)=getBacktrace(($type==E_SQL?3:1));

	// Error type
	$_type=translate_error_type($type);

	// Error message
	$_text = str_replace($_SERVER['DOCUMENT_ROOT'], '', $message);

	// Script name
	$_file = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);



	// Log the error
	if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_DATABASE')) {
		$_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
			or die('Cannot log error! Could not connect: ' . mysql_error());

		mysql_select_db(DB_DATABASE) or die('Cannot log error! Could not select database.');
		//make sure we have a table name!
		$tbl=defined(ERROR_LOG_TABLE)?ERROR_LOG_TABLE:TBL_PREFIX . 'tblErrorLog';
		$_query = 'INSERT INTO ' . $tbl . ' SET Type=\''.mysql_real_escape_string($_type).'\',
			`Function`=\''.mysql_real_escape_string($_caller).'\',
			File=\'' . mysql_real_escape_string($_file) . '\',
			Line=\'' . abs($_line) . '\',
			Text=\'' . mysql_real_escape_string($_text) . '\',
			Backtrace=\'' . mysql_real_escape_string($_detailedError) . '\';';

		mysql_query($_query);

		if (mysql_affected_rows() != 1) {
			die('Cannot log error! Query failed: ' . mysql_error());
		}
	} else {
		die('Cannot log error! Database connection not known.');
	}
}

function mail_error_message($type, $message, $file, $line) {
	global $_error_notice, $_error_deprecated, $_error_warning, $_error_error, $_display_error, $_log_error, $_send_error, $_send_address;
	if(strpos($message,'MYSQL-ERROR')===0){
		$type=E_SQL;
	}
	list($detailedError,$_caller,$file,$line)=getBacktrace(($type==E_SQL?3:1));

	// Build the error table
	$_detailedError  = "An error occurred while executing a script in webEdition.\n\n\n";

	// Domain
	if (defined('SERVER_NAME')) {
		$_detailedError .= 'webEdition address: ' . SERVER_NAME . ",\n\n";
	}

	// Error type
	$_detailedError .= 'Error type: ' . translate_error_type($type) . ",\n";

	// Error message
	$_detailedError .= 'Error message: ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $message) . ",\n";

	// Script name
	$_detailedError .= 'Script name: ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file) . ",\n";

	// Line
	$_detailedError .= 'Line number: ' . $line. ",\n";


	$_detailedError .=' Caller: '.$_caller. ",\n";
 	$_detailedError .=' Backtrace: '.$detailedError;

	// Log the error
	if (defined('WE_ERROR_MAIL_ADDRESS')) {
		if (!mail(WE_ERROR_MAIL_ADDRESS, '[webEdition] PHP Error', $_detailedError)) {
			die('Cannot log error! Could not send e-mail.');
		}
	} else {
		die('Cannot log error! Could not send e-mail due to no known recipient.');
	}
}

function error_handler($type, $message, $file, $line, $context) {
	global $_error_notice, $_error_deprecated, $_error_warning, $_error_error, $_display_error, $_log_error, $_send_error, $_send_address;

	// Don't respond to the error if it was suppressed with a '@'
	if (error_reporting() == 0) {
		return;
	}
	if (!defined('E_DEPRECATED')) {
		define('E_DEPRECATED', 8192);
	}

	if (!defined('E_USER_DEPRECATED')) {
		define('E_USER_DEPRECATED', 16384);
	}

	switch($type) {
		case E_NOTICE:
		case E_USER_NOTICE:
			if ($_error_notice) {
				// Display error?
				if ($_display_error) {
					display_error_message($type, $message, $file, $line);
				}

				// Log error?
				if ($_log_error) {
					log_error_message($type, $message, $file, $line);
				}

				// Mail error?
				if (isset($_send_error) && $_send_error) {
					mail_error_message($type, $message, $file, $line);
				}
			}
			break;

		case E_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_USER_WARNING:
			if ($_error_warning) {
				// Display error?
				if ($_display_error) {
					display_error_message($type, $message, $file, $line);
				}

				// Log error?
				if ($_log_error) {
					log_error_message($type, $message, $file, $line);
				}

				// Mail error?
				if (isset($_send_error) && $_send_error) {
					mail_error_message($type, $message, $file, $line);
				}
			}

			break;

		case E_ERROR:
		case E_PARSE:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
			if ($_error_error) {
				// Display error?
				if ($_display_error) {
					display_error_message($type, $message, $file, $line);
				}

				// Log error?
				if ($_log_error) {
					log_error_message($type, $message, $file, $line);
				}

				// Mail error?
				if (isset($_send_error) && $_send_error) {
					mail_error_message($type, $message, $file, $line);
				}
			}

			// Stop execution
			die();
			break;
		case E_DEPRECATED:
		case E_USER_DEPRECATED:
				if ($_error_deprecated) {
					// Display error?
					if ($_display_error) {
						display_error_message($type, $message, $file, $line);
					}

					// Log error?
					if ($_log_error) {
						log_error_message($type, $message, $file, $line);
					}

					// Mail error?
					if (isset($_send_error) && $_send_error) {
						mail_error_message($type, $message, $file, $line);
					}
				}
				break;
		default:
	}
	//Error handled
	return true;
}
