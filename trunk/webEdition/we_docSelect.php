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

$_SERVER['SCRIPT_NAME'] = WEBEDITION_DIR . 'we_docSelect.php';
$table = isset($table) ? $table : ( isset($_REQUEST["table"]) ? $_REQUEST["table"] : FILE_TABLE);

$fs = new we_selector_document(isset($id) ? $id : ( isset($_REQUEST["id"]) ? $_REQUEST["id"] : '' ), $table, isset($JSIDName) ? $JSIDName : ( isset($_REQUEST["JSIDName"]) ? $_REQUEST["JSIDName"] : ""), isset($JSTextName) ? $JSTextName : ( isset($_REQUEST["JSTextName"]) ? $_REQUEST["JSTextName"] : "" ), isset($JSCommand) ? $JSCommand : ( isset($_REQUEST["JSCommand"]) ? $_REQUEST["JSCommand"] : "" ), isset($order) ? $order : ( isset($_REQUEST["order"]) ? $_REQUEST["order"] : ""), 0, isset($we_editDirID) ? $we_editDirID : ( isset($_REQUEST["we_editDirID"]) ? $_REQUEST["we_editDirID"] : "" ), isset($we_FolderText) ? $we_FolderText : ( isset($_REQUEST["we_FolderText"]) ? $_REQUEST["we_FolderText"] : "" ), isset($filter) ? $filter : ( isset($_REQUEST["filter"]) ? $_REQUEST["filter"] : "" ), isset($rootDirID) ? $rootDirID : ( isset($_REQUEST["rootDirID"]) ? $_REQUEST["rootDirID"] : "" ), isset($open_doc) || isset($_REQUEST["open_doc"]) ? ($table == FILE_TABLE ? permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") : ($table == OBJECT_FILES_TABLE ? permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") : false)) : false, isset($multiple) ? $multiple : ( isset($_REQUEST["multiple"]) ? $_REQUEST["multiple"] : "" ), isset($canSelectDir) ? $canSelectDir : ( isset($_REQUEST["canSelectDir"]) ? $_REQUEST["canSelectDir"] : "" ));

$fs->printHTML(isset($_REQUEST["what"]) ? $_REQUEST["what"] : we_selector_file::FRAMESET);
