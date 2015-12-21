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
$fs = new we_navigation_dirSelector(we_base_request::_(we_base_request::INT, 'id', we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)), we_base_request::_(we_base_request::CMD, 'JSIDName', we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2)), we_base_request::_(we_base_request::CMD, 'JSTextName', we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3)), we_base_request::_(we_base_request::CMD, 'JSCommand', we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4)), we_base_request::_(we_base_request::RAW, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::RAW, 'we_FolderText', ''));

$fs->printHTML(we_base_request::_(we_base_request::INT, 'what', we_selector_file::FRAMESET));
