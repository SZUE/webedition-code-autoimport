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


require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

// include configuration
include_once(WE_APPS_PATH . $TOOLNAME . '/conf/meta.conf.php');

// get controller instance
$controller = Zend_Controller_Front::getInstance();

// set path for controller directory
$controller->setControllerDirectory('./controllers');

// turn on exceptions, if false implement errorAction
$controller->throwExceptions(true);

// disables automatic view rendering
$controller->setParam('noViewRenderer', true);

// set some app specific parameter
$controller->setParam('appDir', WE_APPS_DIR . $TOOLNAME); // dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME'])));
$controller->setParam('appPath', WE_APPS_PATH . $TOOLNAME);
$controller->setParam('appName', $TOOLNAME);

// run!
$controller->dispatch();
