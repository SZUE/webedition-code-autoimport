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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$_SERVER["SCRIPT_NAME"] = WE_MODULES_DIR . "newsletter/we_newsletterDirSelector.php";
if(isset($_REQUEST["JSIDName"]) && strpos($_REQUEST["JSIDName"], 'WECMDENC_') !== false){
	$_REQUEST["JSIDName"] = base64_decode(urldecode(substr($_REQUEST["JSIDName"], 9)));
}
if(isset($_REQUEST["JSTextName"]) && strpos($_REQUEST["JSTextName"], 'WECMDENC_') !== false){
	$_REQUEST["JSTextName"] = base64_decode(urldecode(substr($_REQUEST["JSTextName"], 9)));
}
if(isset($_REQUEST["JSCommand"]) && strpos($_REQUEST["JSCommand"], 'WECMDENC_') !== false){
	$_REQUEST["JSCommand"] = base64_decode(urldecode(substr($_REQUEST["JSCommand"], 9)));
}

$fs = new we_newsletter_dirSelector(
	isset($id) ? $id : weRequest('int', "id", 0), weRequest('js', "JSIDName", ''), weRequest('js', "JSTextName", ''), weRequest('js', "JSCommand", ''), weRequest('raw', "order", ''), 0, weRequest('int', "we_editDirID", 0), weRequest('raw', "we_FolderText", ''), weRequest('int', "rootDirID", 0), weRequest('bool', "multiple")
);

$fs->printHTML(weRequest('int', "what", we_selector_file::FRAMESET));
