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

$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_dirSelect.php';

$fs = new we_selector_directory(
	weRequest('int', "id", 0), weRequest('table', "table", FILE_TABLE), weRequest('js', "JSIDName", ''), weRequest('js', "JSTextName", ''), weRequest('js', "JSCommand", ''), weRequest('raw', "order", ''), 0, weRequest('int', "we_editDirID", 0), weRequest('raw', "we_FolderText", ''), weRequest('int', "rootDirID", 0), weRequest('bool', "multiple"),	isset($extInstanceId) ? $extInstanceId : '');

$fs->printHTML(weRequest('int', "what", we_selector_file::FRAMESET));
