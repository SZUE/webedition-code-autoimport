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
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'newsletter/newsletter_tree.js', 'initTree();');
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		$db = new DB_WE();
		$wsQuery = '';

		$items = $aWsQuery = $parentpaths = [];

		if(($ws = get_ws(NEWSLETTER_TABLE, true))){
			$wsPathArray = id_to_path($ws, NEWSLETTER_TABLE, $db, true);
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


		$db->query('SELECT ID,ParentID,Path,Text,IsFolder FROM ' . NEWSLETTER_TABLE . '  WHERE ' . $wsQuery . ' ParentID=' . intval($ParentID) . ' ORDER BY (text REGEXP "^[0-9]") DESC,abs(text),Text ' . ($segment ? 'LIMIT ' . $offset . ',' . $segment : '' ));

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
				'contenttype' => ($db->f('IsFolder') == 1 ? we_base_ContentTypes::FOLDER : 'we/newsletter'),
			];

			$fileds = array_change_key_case($db->Record, CASE_LOWER);

			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . NEWSLETTER_TABLE . '  WHERE ' . $wsQuery . ' ParentID=' . intval($ParentID), '', $db);
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
