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
$we_transaction = $_REQUEST['we_cmd'][1] ? $_REQUEST['we_cmd'][1] : $we_transaction;
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : '');

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');


we_html_tools::protect();

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case "object_toggleExtraWorkspace":
		$oid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
		$wsid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3);
		$wsPath = id_to_path($wsid, FILE_TABLE, $DB_WE);
		$tableID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);
		$ofID = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE ObjectID='$oid' AND TableID=" . intval($tableID), "ID", $DB_WE);
		$foo = f("SELECT OF_ExtraWorkspacesSelected FROM " . OBJECT_X_TABLE . intval($tableID) . " WHERE ID='" . $oid . "'", "OF_ExtraWorkspacesSelected", $DB_WE);
		if(strstr($foo, "," . $wsid . ",")){
			$ews = str_replace("," . $wsid, ",", "", $foo);
			if($ews == ",")
				$ews = "";
			$check = 0;
		}
		else {
			$ews = ($foo ? $foo : ",") . $wsid . ",";
			$check = 1;
		}
		$DB_WE->query("UPDATE " . OBJECT_X_TABLE . intval($tableID) . " SET OF_ExtraWorkspacesSelected='" . $DB_WE->escape($ews) . "' WHERE ID=" . intval($oid));
		$DB_WE->query("UPDATE " . OBJECT_FILES_TABLE . " SET ExtraWorkspacesSelected='" . $DB_WE->escape($ews) . "' WHERE ID=" . intval($ofID));
		$of = new we_objectFile();
		$of->initByID($ofID, OBJECT_FILES_TABLE);
		$of->insertAtIndex();
		print we_html_element::jsElement('top.we_cmd("reload_editpage");');
		break;
	case "object_obj_search":
		$we_doc->Search = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);
		$we_doc->SearchField = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 3);
		$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_WORKSPACE;
		$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_WORKSPACE;
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
		echo we_html_element::jsElement('top.we_cmd("switch_edit_page",' . we_base_constants::WE_EDITPAGE_WORKSPACE . ',"' . $_REQUEST['we_cmd'][1] . '");');
		break;
}