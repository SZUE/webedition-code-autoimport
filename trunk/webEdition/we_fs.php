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
	isset($id) ? $id : weRequest('int', 'id', 0), isset($table) ? $table : weRequest('table', 'table', FILE_TABLE), isset($JSIDName) ? $JSIDName : weRequest('raw', 'JSIDName', ''), isset($JSTextName) ? $JSTextName : weRequest('raw', 'JSTextName', ''), isset($JSCommand) ? $JSCommand : weRequest('raw', 'JSCommand', ''), isset($order) ? $order : weRequest('raw', 'order', ''), isset($rootDirID) ? $rootDirID : weRequest('int', 'rootDirID', 0), isset($multiple) ? $multiple : weRequest('bool', 'multiple'), isset($filter) ? $filter : weRequest('raw', 'filter', ''));

$fs->printHTML(weRequest('int', 'what', we_selector_file::FRAMESET));
