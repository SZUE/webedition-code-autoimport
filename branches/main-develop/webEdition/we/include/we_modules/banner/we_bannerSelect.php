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

if(isset($_REQUEST['we_cmd'])){
	$_REQUEST['id'] = weRequest('int', 'we_cmd', 0, 1);
	$_REQUEST['JSIDName'] = we_cmd_dec(2);
	$_REQUEST['JSTextName'] = we_cmd_dec(3);
	$_REQUEST['JSCommand'] = we_cmd_dec(4);
}

$_SERVER["SCRIPT_NAME"] = WE_MODULES_DIR . "banner/we_bannerSelect.php";
$fs = new we_banner_selector(weRequest('int', "id", 0), weRequest('js', "JSIDName", ''), weRequest('js', "JSTextName", ''), weRequest('js', "JSCommand", ''), weRequest('raw', "order", ''));

$fs->printHTML(weRequest('int', "what", we_selector_file::FRAMESET));
