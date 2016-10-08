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
class we_voting_tree extends we_tree_base{

	protected function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'voting/voting_tree.js');
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		$db = new DB_WE();

		$items = [];

		$owners_sql = we_voting_voting::getOwnersSql();

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = ["id" => "prev_" . $ParentID,
				"parentid" => $ParentID,
				"text" => "display (" . $prevoffset . "-" . $offset . ")",
				"contenttype" => "arrowup",
				"table" => VOTING_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"published" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $prevoffset
				];
		}

		$where = ' WHERE ParentID=' . intval($ParentID) . ' ' .  $owners_sql;

		$db->query('SELECT ID,ParentID,Path,Text,IsFolder,RestrictOwners,Owners,Active,ActiveTime,Valid FROM ' . VOTING_TABLE . $where . ' ORDER BY IsFolder DESC,(text REGEXP "^[0-9]") DESC,ABS(text),Text' . ($segment ? ' LIMIT ' . abs($offset) . "," . abs($segment) : '' ));
		$now = time();

		while($db->next_record(MYSQLI_ASSOC)){
			$typ = ['typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
				'contenttype' => ($db->f('IsFolder') == 1 ? 'folder' : 'we/voting'),
			];

			if($db->f('IsFolder') == 0){
				$typ['published'] = ($db->f('Active') && ($db->f('ActiveTime') == 0 || ($now < $db->f('Valid')))) ? 1 : 0;
			}
			$fileds = array_change_key_case($db->Record, CASE_LOWER);

			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . VOTING_TABLE . ' ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = ["id" => "next_" . $ParentID,
				"parentid" => 0,
				"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
				"contenttype" => "arrowdown",
				"table" => VOTING_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $nextoffset
				];
		}

		return $items;
	}

}
