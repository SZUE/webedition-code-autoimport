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
$_SERVER['SCRIPT_NAME'] = WE_USERS_MODULE_DIR . 'we_usersSelect.php';

$fs = new we_users_selector(weRequest('int', "id", 0), weRequest('table', 'table', USER_TABLE), weRequest('string', "JSIDName", ""), weRequest('string', "JSTextName", ""), weRequest('raw', "JSCommand", ""), weRequest('raw', "order", ""), weRequest('int', "rootDirID", 0), weRequest('raw', "filter", ""), weRequest('bool', "multiple"));

$fs->printHTML(weRequest('int', "what", we_selector_file::FRAMESET));
