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
class we_voting_tree extends weTree{

	function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'voting/voting_tree.js');
	}

	function getJSStartTree(){
		return '
function startTree(){
			frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
};
treeData.frames=frames;
				frames.cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=cmd&pid=0";
				drawTree();
			}';
	}

	static function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = "ID,ParentID,Path,Text,IsFolder,RestrictOwners,Owners,Active,ActiveTime,Valid", $addWhere = "", $addOrderBy = ""){
		$db = new DB_WE();
		$table = VOTING_TABLE;

		$items = array();

		$owners_sql = we_voting_voting::getOwnersSql();

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = array(
				"id" => "prev_" . $ParentID,
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
			);
		}

		$where = ' WHERE ParentID=' . intval($ParentID) . ' ' . $addWhere . $owners_sql;

		$db->query('SELECT ' . $db->escape($elem) . ' FROM ' . $db->escape($table) . $where . ' ORDER BY IsFolder DESC,(text REGEXP "^[0-9]") DESC,ABS(text),Text' . ($segment ? ' LIMIT ' . abs($offset) . "," . abs($segment) : '' ));
		$now = time();

		while($db->next_record()){
			$typ = array(
				'typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
				'contentType'=>($db->f('IsFolder') == 1 ? 'folder' : 'we/voting'),
			);

			if($db->f('IsFolder') == 0){
				$typ['published'] = ($db->f('Active') && ($db->f('ActiveTime') == 0 || ($now < $db->f('Valid')))) ? 1 : 0;
			}
			$fileds = array();

			foreach($db->Record as $k => $v){
				if(!is_numeric($k)){
					$fileds[strtolower($k)] = $v;
				}
			}

			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . $db->escape($table) . ' ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				"id" => "next_" . $ParentID,
				"parentid" => 0,
				"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
				"contenttype" => "arrowdown",
				"table" => VOTING_TABLE,
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
