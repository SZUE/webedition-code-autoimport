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

$_SERVER['SCRIPT_NAME'] = WE_INCLUDES_DIR . 'we_modules/navigation/we_navigationDirSelect.php';
$fs = new we_navigation_dirSelector(weRequest('int', 'id', weRequest('int', 'we_cmd', 0, 1)), weRequest('string', 'JSIDName', weRequest('string', 'we_cmd', '', 2)), weRequest('raw', 'JSTextName', weRequest('raw', 'we_cmd', '', 3)), weRequest('raw', 'JSCommand', weRequest('raw', 'we_cmd', '', 4)), weRequest('raw', 'order', ''), weRequest('int', 'we_editDirID', 0), weRequest('raw', 'we_FolderText', ''));

$fs->printHTML(weRequest('int', 'what', we_selector_file::FRAMESET));
