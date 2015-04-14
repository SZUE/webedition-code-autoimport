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

$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_imgSelect.php?';

$fs = new we_selector_image(we_base_request::_(we_base_request::INT, 'id', 0), ($table = we_base_request::_(we_base_request::TABLE, 'table', (defined('FILE_TABLE') ? FILE_TABLE : 'FF'))), isset($JSIDName) ? $JSIDName : '', isset($JSTextName) ? $JSTextName : '', isset($JSCommand) ? $JSCommand : '', we_base_request::_(we_base_request::RAW, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::RAW, 'we_FolderText', ''), we_base_request::_(we_base_request::INT, 'rootDirID', 0), we_base_request::_(we_base_request::BOOL, 'open_doc') ? ($table == (defined('FILE_TABLE') ? FILE_TABLE : 'FF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') : ($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') : false)) : false, we_base_request::_(we_base_request::BOOL, 'multiple'), we_base_request::_(we_base_request::BOOL, 'canSelectDir'), we_base_request::_(we_base_request::INT, 'startID'));

$fs->printHTML(we_base_request::_(we_base_request::INT, 'what', we_selector_file::FRAMESET));
