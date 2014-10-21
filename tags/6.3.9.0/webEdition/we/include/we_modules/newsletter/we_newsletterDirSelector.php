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

$_SERVER["SCRIPT_NAME"] = WE_MODULES_DIR . "newsletter/we_newsletterDirSelector.php";

$fs = new we_newsletter_dirSelector(
	isset($id) ? $id : we_base_request::_(we_base_request::INT, "id", 0), 
	isset($JSIDName) ? $JSIDName : we_base_request::_(we_base_request::CMD, "JSIDName", ''), 
	isset($JSTextName) ? $JSTextName : we_base_request::_(we_base_request::CMD, "JSTextName", ''), 
	isset($JSCommand) ? $JSCommand : we_base_request::_(we_base_request::CMD, "JSCommand", ''), 
	we_base_request::_(we_base_request::RAW, "order", ''), 
	0, 
	we_base_request::_(we_base_request::RAW, "we_editDirID", 0), 
	we_base_request::_(we_base_request::RAW, "we_FolderText", ''), 
	isset($rootDirID) ? $rootDirID : we_base_request::_(we_base_request::RAW, "rootDirID", 0), 
	isset($multiple) ? $multiple : we_base_request::_(we_base_request::RAW, "multiple")
);

$fs->printHTML(we_base_request::_(we_base_request::INT, "what", we_selector_file::FRAMESET));
