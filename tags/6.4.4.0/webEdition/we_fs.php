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
we_html_tools::protect(/* array('BROWSE_SERVER') */);

$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_fs.php';

$fs = new we_selector_multiple(
	isset($id) ? $id : we_base_request::_(we_base_request::INT, 'id', 0), isset($table) ? $table : we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE), isset($JSIDName) ? $JSIDName : we_base_request::_(we_base_request::RAW, 'JSIDName', ''), isset($JSTextName) ? $JSTextName : we_base_request::_(we_base_request::RAW, 'JSTextName', ''), isset($JSCommand) ? $JSCommand : we_base_request::_(we_base_request::RAW, 'JSCommand', ''), isset($order) ? $order : we_base_request::_(we_base_request::RAW, 'order', ''), isset($rootDirID) ? $rootDirID : we_base_request::_(we_base_request::INT, 'rootDirID', 0), isset($multiple) ? $multiple : we_base_request::_(we_base_request::BOOL, 'multiple'), isset($filter) ? $filter : we_base_request::_(we_base_request::RAW, 'filter', ''));

$fs->printHTML(we_base_request::_(we_base_request::INT, 'what', we_selector_file::FRAMESET));
