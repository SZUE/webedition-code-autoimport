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
class rpcSelectorGetFilesOfDirCmd extends we_rpc_cmd{

	function execute(){

		$resp = new we_rpc_response();

		$queryClass = new we_selector_query();
		$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);

		// if a value is already inserted in a selector, we get an i, not a parentID
		if(($id = we_base_request::_(we_base_request::INT, 'id', 0))){

			// detect belonging parentid
			$queryClass = new we_selector_query();
			if(($res = $queryClass->getItemById($id, $table))){
				$id = $res[0]["ParentID"];
			} else {
				$id = 0;
			}
		} else {
			$id = we_base_request::_(we_base_request::INT, 'parentId', 0);
		}

		if(($types = we_base_request::_(we_base_request::STRING, "types"))){
			$types = explode(",", $types);
		} else {
			$types = [];
		}
		$queryClass->queryFolderContents($id, $table, $types, null);
		$entries = $queryClass->getResult();

		$first = true;

		$data = "_files = {};";
		// 1st step, select this folder if folders are selectable
		if(in_array('folder', $types)){
			$data .= '_files["id_' . $id . '"] = {"type":"folder","text":".","id":"' . $id . '"};';
		}
		// one folder, or up to root
		// parent Folder for navigation up
		if($parentFolder = $queryClass->getItemById($id, $table)){
			$data .= '_files["id_' . $parentFolder[0]["ParentID"] . '"] = {"type":"folder","text":"..","id":"' . $parentFolder[0]["ParentID"] . '"};';
		} else {
			$data .= '_files["id_0"] = {"type":"folder","text":"..","id":"0"};';
		}


		foreach($entries as $entry){
			$data .= '_files["id_' . $entry["ID"] . '"] = {"type":"' . (isset($entry["ContentType"]) ? $entry["ContentType"] : "") . '","text":"' . $entry["Text"] . '","id":"' . $entry["ID"] . '"};';
		}
		$resp->addData("data", $data);
		return $resp;
	}

}
