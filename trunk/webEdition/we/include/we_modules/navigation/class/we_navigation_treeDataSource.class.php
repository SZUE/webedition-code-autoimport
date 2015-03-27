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
class we_navigation_treeDataSource extends we_tool_treeDataSource{

	function __construct($ds){
		parent::__construct($ds);
	}

	function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,ParentID,Path,Text,Icon,IsFolder,Ordn,Depended,Charset', $addWhere = '', $addOrderBy = ''){
		$db = new DB_WE();
		$table = $this->SourceName;

		$items = $_aWsQuery = $parentpaths = array();

		if(($ws = get_ws($table))){
			$wsPathArray = id_to_path($ws, $table, $db, false, true);
			foreach($wsPathArray as $path){
				$_aWsQuery[] = " Path LIKE '" . $db->escape($path) . "/%' OR " . we_navigation_treeDataSource::getQueryParents($path);
				while($path != "/" && $path != "\\" && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
		}

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = array(
				'icon' => 'arrowup.gif',
				'id' => 'prev_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $prevoffset . '-' . $offset . ')',
				'contenttype' => 'arrowup',
				'table' => $table,
				'typ' => 'threedots',
				'open' => 0,
				'published' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $prevoffset
			);
		}

		$where = ($_aWsQuery ? '(' . implode(' OR ', $_aWsQuery) . ') AND ' : '') . ' ParentID=' . intval($ParentID) . ' ' . $addWhere;

		$db->query('SELECT ' . $elem . ', abs(text) as Nr, (text REGEXP "^[0-9]") AS isNr FROM ' . $table . ' WHERE ' . $where . ' ORDER BY Ordn, isNr DESC,Nr,Text ' . ($segment ? 'LIMIT ' . $offset . ',' . $segment : ''));

		while($db->next_record()){

			$typ = array(
				'typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
				'order' => $db->f('Ordn'),
				'published' => $db->f('Depended') ? 0 : 1,
				'disabled' => in_array($db->f('Path'), $parentpaths) ? 1 : 0,
			);

			$fileds = array();

			foreach($db->Record as $k => $v){
				if(!is_numeric($k)){
					$fileds[strtolower($k)] = $v;
				}
			}

			if($db->f('IsFolder') == 0){
				$_charset = we_navigation_navigation::findCharset($db->f('ParentID'));
			} else {
				$_charset = $db->f('Charset');
			}
			$_textUncleaned = $db->f('Text');
			$_textUncleaned = strtr($_textUncleaned, array(
				"<br/>" => " ", "<br/>" => " ", "<br />" => " "
			));

			$_text = str_replace('&amp;', '&', strip_tags($_textUncleaned));
			$_path = str_replace('&amp;', '&', $db->f('Path'));

			if(!empty($_charset) && function_exists('mb_convert_encoding')){
				$typ['text'] = mb_convert_encoding($_text, 'HTML-ENTITIES', $_charset);
				$typ['path'] = mb_convert_encoding($_path, 'HTML-ENTITIES', $_charset);
			} else {
				$typ['text'] = $_text;
				$typ['path'] = $_path;
			}

			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) as total FROM ' . $table . ' WHERE ' . $where, 'total', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				'icon' => 'arrowdown.gif',
				'id' => 'next_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $nextoffset . '-' . ($nextoffset + $segment) . ')',
				'contenttype' => 'arrowdown',
				'table' => $table,
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
