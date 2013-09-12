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
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');

// Absolute Server Path to the webEdition base directory
$GLOBALS['__WE_BASE_PATH__'] = WEBEDITION_PATH;

// Absolute Server Path to the lib directory
$GLOBALS['__WE_LIB_PATH__'] = WE_LIB_PATH;

// Absolute Server Path to the apps directory
$GLOBALS['__WE_APP_PATH__'] = WE_APPS_PATH;

// Absolute Server Path to the apps directory
$GLOBALS['__WE_CMS_PATH__'] = WEBEDITION_PATH . 'cms';

// Absolute URL to the webEdition base directory (eg. "/webEdition")
$GLOBALS['__WE_BASE_URL__'] = WEBEDITION_DIR;

// Absolute URL to the lib directory (eg. "/webEdition/lib")
$GLOBALS['__WE_LIB_URL__'] = LIB_DIR;

// Absolute URL to the apps directory (eg. "/webEdition/apps")
$GLOBALS['__WE_APP_URL__'] = WE_APPS_DIR;

// Absolute URL to the apps directory (eg. "/webEdition/apps")
$GLOBALS['__WE_CMS_URL__'] = WEBEDITION_DIR . 'cms';

// add __WE_LIB_PATH__ and __WE_APP_PATH__ to the include_path
if(ini_set('include_path', WE_LIB_PATH . PATH_SEPARATOR . WE_APPS_PATH . PATH_SEPARATOR . ini_get('include_path')) === FALSE){
	t_e('unable to add webEdition to include path! Expect Problems!');
}

require_once(WE_INCLUDES_PATH . 'we_classes/we_autoloader.class.php');

//make we_autoloader the first autoloader
$ret = spl_autoload_register('we_autoloader::autoload', false, true);
//workaround php 5.2
if($ret != true){
	spl_autoload_register('we_autoloader::autoload', true);
}

// include Zend_Autoloader  #3815
require_once('Zend/Loader/Autoloader.php');

$loader = Zend_Loader_Autoloader::getInstance(); #3815
$loader->setFallbackAutoloader(true); #3815
$loader->suppressNotFoundWarnings(true);

spl_autoload_register('we_autoloader::finalLoad', true);

// include configuration file of webEdition
require_once (WE_INCLUDES_PATH . 'conf/we_conf.inc.php');

if(!defined("DATETIME_INITIALIZED")){// to prevent additional initialization if set somewhere else, i.e in we_conf.inc.php, this also allows later to make that an settings-item
	if(!date_default_timezone_set(@date_default_timezone_get())){
		date_default_timezone_set('Europe/Berlin');
	}
	define("DATETIME_INITIALIZED", "1");
}
if(!isset($_SERVER['TMP'])){
	$_SERVER['TMP'] = WEBEDITION_PATH . 'we/zendcache';
}
