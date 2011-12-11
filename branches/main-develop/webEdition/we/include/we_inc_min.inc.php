<?php
/**
 * webEdition CMS
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

//	This is the new absolute minimum include for any we-file, reduces memory consumption for special usages about 20 MB.

// exit if script called directly
if (isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']),'',$_SERVER['SCRIPT_NAME'])=='/we_inc_min.inc.php') {
	exit();
}

// remove trailing slash
if (isset($_SERVER['DOCUMENT_ROOT'])){
	$_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
}

// Set PHP flags
@$_memlimit = intval(ini_get('memory_limit'));
if ($_memlimit < 32) {
	@ini_set('memory_limit', '32M');
}
@ini_set('allow_url_fopen', '1');
@ini_set('file_uploads', '1');
@ini_set('session.use_trans_sid', '0');

//start autoloader!
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.php');

// Activate the webEdition error handler
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php');
if (!defined('WE_ERROR_HANDLER_SET')){
	we_error_handler();
}

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_global.inc.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');
