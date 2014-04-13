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

if(isset($_REQUEST['we_cmd'])){
	$id = $_REQUEST['we_cmd'][1];
	$JSIDName = we_cmd_dec(2);
	$JSTextName = we_cmd_dec(3);
	$JSCommand = we_cmd_dec(4);
}

we_html_tools::protect();
$_SERVER["SCRIPT_NAME"] = WE_MODULES_DIR . "voting/we_votingDirSelect.php";
$fs = new we_voting_dirSelector(isset($id) ? $id : weRequest('int', "id", 0), isset($JSIDName) ? $JSIDName : weRequest('string', "JSIDName", ''), isset($JSTextName) ? $JSTextName : weRequest('string', "JSTextName", ''), isset($JSCommand) ? $JSCommand : weRequest('raw', "JSCommand", ''), isset($order) ? $order : weRequest('raw', "order", ''), isset($we_editDirID) ? $we_editDirID : weRequest('int', "we_editDirID", 0), isset($we_FolderText) ? $we_FolderText : weRequest('raw', "we_FolderText", ''));

$fs->printHTML(weRequest('int', "what", we_selector_file::FRAMESET));
