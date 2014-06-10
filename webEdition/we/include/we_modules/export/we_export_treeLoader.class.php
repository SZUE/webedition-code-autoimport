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
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);
class we_export_treeLoader{

	function getItems($pid, $offset = 0, $segment = 500, $sort = ""){
		return self::getItemsFromDB($pid, $offset, $segment);
	}

	function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = "ID,ParentID,Path,Text,Icon,IsFolder", $addWhere = "", $addOrderBy = ""){
		$db = new DB_WE();
		$table = EXPORT_TABLE;

		$items = array();

		$prevoffset = $offset - $segment;
		$prevoffset = ($prevoffset < 0) ? 0 : $prevoffset;
		if($offset && $segment){
			$items[] = array(
				"icon" => "arrowup.gif",
				"id" => "prev_" . $ParentID,
				"parentid" => $ParentID,
				"text" => "display (" . $prevoffset . "-" . $offset . ")",
				"contenttype" => "arrowup",
				"table" => EXPORT_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"published" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $prevoffset
			);
		}

		$where = " WHERE ParentID=" . intval($ParentID) . " " . $addWhere;

		$db->query("SELECT $elem, abs(text) as Nr, (text REGEXP '^[0-9]') as isNr from $table $where ORDER BY isNr DESC,Nr,Text " . ($segment ? "LIMIT $offset,$segment;" : ";" ));

		while($db->next_record()){

			$typ = array(
				"typ" => ($db->f("IsFolder") == 1 ? "group" : "item"),
				"open" => 0,
				"disabled" => 0,
				"tooltip" => $db->f("ID"),
				"offset" => $offset,
			);
			$tt = "";
			//$ttrow=getHash("SELECT * FROM ".RAW_TABLE." WHERE ID='".$db->f("ID")."';",new DB_WE());
			$ttrow = $db->Record;

			$fileds = array();

			foreach($db->Record as $k => $v){
				if(!is_numeric($k)){
					$fileds[strtolower($k)] = $v;
				}
			}

			$fileds["text"] = trim($tt) ? $tt : $db->f("Text");
			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) as total FROM ' . $table . ' ' . $where, 'total', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				"icon" => "arrowdown.gif",
				"id" => "next_" . $ParentID,
				"parentid" => 0,
				"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
				"contenttype" => "arrowdown",
				"table" => EXPORT_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $nextoffset
			);
		}

		return $items;
	}

}
