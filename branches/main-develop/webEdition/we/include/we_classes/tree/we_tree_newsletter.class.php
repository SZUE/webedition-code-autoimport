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
class we_tree_newsletter extends we_tree_base{

	protected function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'newsletter/newsletter_tree.js');
	}

	public static function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,ParentID,Path,Text,IsFolder', $addWhere = '', $addOrderBy = ''){
		$db = new DB_WE();
		$table = NEWSLETTER_TABLE;
		$wsQuery = '';

		$items = $aWsQuery = $parentpaths = [];

		if(($ws = get_ws($table, true))){
			$wsPathArray = id_to_path($ws, $table, $db, true);
			foreach($wsPathArray as $path){
				$aWsQuery[] = ' Path LIKE "' . $path . '/%" OR ' . we_tool_treeDataSource::getQueryParents($path);
				while($path != "/" && $path != "\\" && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
			$wsQuery = $aWsQuery ? '(' . implode(' OR ', $aWsQuery) . ') AND ' : '';
		}

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = ['id' => 'prev_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $prevoffset . '-' . $offset . ')',
				'contenttype' => 'arrowup',
				'table' => NEWSLETTER_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'published' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $prevoffset
				];
		}

		$where = " WHERE $wsQuery ParentID=" . intval($ParentID) . ' ' . $addWhere;

		$db->query('SELECT ' . $db->escape($elem) . " FROM $table $where ORDER BY (text REGEXP '^[0-9]') DESC,abs(text),Text " . ($segment ? 'LIMIT ' . $offset . ',' . $segment : '' ));

		while($db->next_record(MYSQLI_ASSOC)){
			$typ = ['typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
				'disabled' => in_array($db->f('Path'), $parentpaths) ? 1 : 0,
				'text' => $db->f('Text'),
				'path' => $db->f('Path'),
				'published' => 1,
				'contentType' => ($db->f('IsFolder') == 1 ? 'folder' : 'we/newsletter'),
				];

			$fileds = array_change_key_case($db->Record, CASE_LOWER);

			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . $table . ' ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = ['id' => 'next_' . $ParentID,
				'parentid' => 0,
				'text' => 'display (' . $nextoffset . '-' . ($nextoffset + $segment) . ')',
				'contenttype' => 'arrowdown',
				'table' => NEWSLETTER_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $nextoffset
				];
		}

		return $items;
	}

}
