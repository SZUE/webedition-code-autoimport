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
 * @package    we_core
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
/*
 * Sets some global variables which are needed
 * for other classes and scripts and defines
 * the __autoload() function
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php');
we_error_handler(!isset($GLOBALS['WE_TEMPLATE_INIT']));


// include configuration file of webEdition
require_once (WE_INCLUDES_PATH . 'conf/we_conf.inc.php');
require_once(WE_INCLUDES_PATH . 'we_classes/we_autoloader.class.php');

if(ini_set('include_path', WE_LIB_PATH . PATH_SEPARATOR . WE_APPS_PATH . PATH_SEPARATOR . ini_get('include_path')) === FALSE){
	t_e('unable to add webEdition to include path! Expect Problems!');
}

//FIXME: remove after end of support for PHP 5.3
if(!function_exists('we_stripslashes')){

	function we_stripslashes(&$arr){
		foreach($arr as $n => $v){
			if(is_array($v)){
				we_stripslashes($arr[$n]);
			} else {
				$arr[$n] = stripslashes($v);
			}
		}
	}

}

//FIXME: remove after end of support for PHP 5.3
if((get_magic_quotes_gpc() == 1)){
	if($_REQUEST){
		foreach($_REQUEST as $n => $v){
			if(is_array($v)){
				we_stripslashes($v);
				$_REQUEST[$n] = $v;
			} else {
				$_REQUEST[$n] = stripslashes($v);
			}
		}
	}
}

//make we_autoloader the first autoloader
$ret = spl_autoload_register('we_autoloader::autoload', false, true);
//FIXME: remove workaround php 5.2
if($ret != true){
	spl_autoload_register('we_autoloader::autoload', true);
}

if(!defined('DATETIME_INITIALIZED')){// to prevent additional initialization if set somewhere else, i.e in we_conf.inc.php, this also allows later to make that an settings-item
	if(!date_default_timezone_set(@date_default_timezone_get())){
		date_default_timezone_set('Europe/Berlin');
	}
	define('DATETIME_INITIALIZED', 1);
}
if(!isset($_SERVER['TMP'])){
	$_SERVER['TMP'] = WEBEDITION_PATH . 'we/zendcache';
}
