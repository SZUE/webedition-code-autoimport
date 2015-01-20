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

if(isset($_REQUEST['we_cmd'])){
	$_REQUEST['id'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
	$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
	$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
	$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
}

$_SERVER["SCRIPT_NAME"] = WE_MODULES_DIR . "banner/we_bannerSelect.php";
$fs = new we_banner_selector(we_base_request::_(we_base_request::INT, "id", 0), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::RAW, "order", ''));

$fs->printHTML(we_base_request::_(we_base_request::INT, "what", we_selector_file::FRAMESET));
