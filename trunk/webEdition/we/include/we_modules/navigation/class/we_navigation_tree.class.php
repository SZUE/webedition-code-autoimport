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
class we_navigation_tree extends we_tree_base{

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'navigation_tree.js');
	}

	function getJSStartTree(){
		return parent::getJSStartTree() . 'drawTree.selection_table="' . NAVIGATION_TABLE . '";';
	}

	static function getItems($ParentID = 0, $offset = 0, $segment = 500){
		$db = new DB_WE();

		$items = $aWsQuery = $parentpaths = array();

		if(($ws = get_ws(NAVIGATION_TABLE))){
			$wsPathArray = id_to_path($ws, NAVIGATION_TABLE, $db, true);
			foreach($wsPathArray as $path){
				$aWsQuery[] = ' Path LIKE "' . $db->escape($path) . '/%" OR ' . we_tool_treeDataSource::getQueryParents($path);
				while($path != "/" && $path != "\\" && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
		}

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = array(
				'id' => 'prev_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $prevoffset . '-' . $offset . ')',
				'contenttype' => 'arrowup',
				'table' => NAVIGATION_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'published' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $prevoffset,
			);
		}

		$where = ($aWsQuery ? '(' . implode(' OR ', $aWsQuery) . ') AND ' : '') . ' ParentID=' . intval($ParentID) . ' ';

		$db->query('SELECT ID,ParentID,Path,Text,IsFolder,Ordn,Depended,Charset,abs(text) as Nr, (text REGEXP "^[0-9]") AS isNr FROM ' . NAVIGATION_TABLE . ' WHERE ' . $where . ' ORDER BY Ordn, isNr DESC,Nr,Text ' . ($segment ? 'LIMIT ' . $offset . ',' . $segment : ''));

		while($db->next_record(MYSQL_ASSOC)){
			$typ = array(
				'typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'disabled' => 0,
				'tooltip' => intval($db->f('ID')),
				'offset' => $offset,
				'order' => intval($db->f('Ordn')),
				'published' => $db->f('Depended') ? 0 : 1,
				'disabled' => in_array($db->f('Path'), $parentpaths) ? 1 : 0,
				'contentType' => ($db->f('IsFolder') == 1 ? 'folder' : 'we/navigation'),
			);

			$fileds = array();

			foreach($db->Record as $k => $v){
				$fileds[strtolower($k)] = $v;
			}

			$charset = ($db->f('IsFolder') == 0 ?
					we_navigation_navigation::findCharset($db->f('ParentID')) :
					$db->f('Charset'));

			$text = strip_tags(strtr($db->f('Text'), array(
				'&amp;' => '&', "<br/>" => " ", "<br />" => " "
			)));
			$path = str_replace('&amp;', '&', $db->f('Path'));

			if(!empty($charset) && function_exists('mb_convert_encoding')){
				$typ['text'] = mb_convert_encoding($text, 'HTML-ENTITIES', $charset);
				$typ['path'] = mb_convert_encoding($path, 'HTML-ENTITIES', $charset);
			} else {
				$typ['text'] = $text;
				$typ['path'] = $path;
			}

			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) FROM ' . NAVIGATION_TABLE . ' WHERE ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				'id' => 'next_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $nextoffset . '-' . ($nextoffset + $segment) . ')',
				'contenttype' => 'arrowdown',
				'table' => NAVIGATION_TABLE,
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
