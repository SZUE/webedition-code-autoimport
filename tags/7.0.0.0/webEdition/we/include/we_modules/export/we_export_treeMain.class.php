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
class we_export_treeMain extends weTree{

	protected function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'export/export_treeMain.js');
	}

	function getJSStartTree(){
		return '
function startTree(){
	treeData.frames={
		top:' . $this->topFrame . ',
		cmd:' . $this->cmdFrame . ',
		tree:' . $this->treeFrame . '
	};
	treeData.frames.cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=cmd&cmd=mainload&pid=0";
	drawTree();
			}';
	}

	public static function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,ParentID,Path,Text,IsFolder', $addWhere = '', $addOrderBy = ''){
		$db = new DB_WE();
		$table = EXPORT_TABLE;

		$items = array();

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = array(
				'id' => 'prev_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $prevoffset . '-' . $offset . ')',
				'contenttype' => 'arrowup',
				'table' => EXPORT_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'published' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $prevoffset
			);
		}

		$where = ' WHERE ParentID=' . intval($ParentID) . ' ' . $addWhere;

		$db->query('SELECT ' . $elem . ' FROM ' . $table . $where . ' ORDER BY IsFolder DESC,(text REGEXP "^[0-9]") DESC,abs(text),Text' . ($segment ? " LIMIT $offset,$segment" : '' ));

		while($db->next_record()){
			$typ = array(
				'typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'contentType'=>($db->f('IsFolder') == 1 ? 'folder' : 'we/export'),
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
			);
			$tt = '';

			$fileds = array();

			foreach($db->Record as $k => $v){
				if(!is_numeric($k)){
					$fileds[strtolower($k)] = $v;
				}
			}

			$fileds['text'] = trim($tt) ? $tt : $db->f('Text');
			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . $table . ' ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				'id' => 'next_' . $ParentID,
				'parentid' => 0,
				'text' => 'display (' . $nextoffset . '-' . ($nextoffset + $segment) . ')',
				'contenttype' => 'arrowdown',
				'table' => EXPORT_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $nextoffset
			);
		}

		return $items;
	}

}
