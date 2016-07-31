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
class we_tool_treeDataSource{
	var $SourceType;
	var $SourceName;

	function __construct($ds){

		$dsd = explode(':', $ds);
		if(isset($dsd[0]) && isset($dsd[1])){
			$this->SourceType = $dsd[0];
			$this->SourceName = $dsd[1];
		}
	}

	function getItems($pid, $offset = 0, $segment = 500, $sort = ''){
		switch($this->SourceType){
			case 'table' :
				return $this->getItemsFromDB($pid, $offset, $segment);
			case 'file':
				return $this->getItemsFromFile($pid, $offset, $segment);
			case 'custom':
				return $this->getCustomItems($pid, $offset, $segment);
		}
	}

	public static function getQueryParents($path){
		$out = [];
		$db = $GLOBALS['DB_WE'];
		while($path != '/' && $path != '\\' && $path){
			$out[] = '"' . $db->escape($path) . '"';
			$path = dirname($path);
		}
		return ($out ? 'Path IN(' . implode(',', $out) . ')' : '');
	}

	function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,ParentID,Path,Text,IsFolder', $addWhere = '', $addOrderBy = ''){

		$db = new DB_WE();
		$table = $this->SourceName;

		$items = [];

		$wsQuery = '';
		$aWsQuery = [];
		$parentpaths = [];

		if(($ws = get_ws($table, true))){
			$wsPathArray = id_to_path($ws, $table, $db, true);
			foreach($wsPathArray as $path){
				$aWsQuery[] = ' Path LIKE "' . $db->escape($path) . '/%" OR ' . self::getQueryParents($path);
				while($path != '/' && $path != "\\" && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
			$wsQuery = !empty($aWsQuery) ? '(' . implode(' OR ', $aWsQuery) . ') AND ' : '';
		}

		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			$items[] = array(
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

		$where = ' WHERE ' . $wsQuery . ' ParentID=' . intval($ParentID) . ' ' . $addWhere;

		$db->query('SELECT ' . $elem . ' FROM ' . $db->escape($table) . $where . ' ORDER BY (text REGEXP "^[0-9]") DESC,abs(text),Text ' . ($segment ? 'LIMIT ' . abs($offset) . ',' . abs($segment) : '' ));

		while($db->next_record()){
			$typ = array(
				'typ' => ($db->f('IsFolder') == 1 ? 'group' : 'item'),
				'open' => 0,
				'disabled' => 0,
				'tooltip' => $db->f('ID'),
				'offset' => $offset,
				'order' => $db->f('Ordn'),
				'published' => 1,
				'disabled' => 0,
				'contentType' => ($db->f('IsFolder') == 1 ? 'folder' : 'item'),
			);

			$fileds = [];

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

	function getItemsFromFile($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,ParentID,Path,Text,IsFolder', $addWhere = '', $addOrderBy = ''){

		return array(
			array(
				'id' => 1,
				'parentid' => 0,
				'text' => 'Test 1',
				'contenttype' => 'item',
				'table' => $this->SourceName,
				'typ' => 'item',
				'open' => 0,
				'published' => 1,
				'disabled' => 0,
				'tooltip' => ''
		));
	}

	function getCustomItems($ParentID = 0, $offset = 0, $segment = 500, $elem = 'ID,ParentID,Path,Text,IsFolder', $addWhere = '', $addOrderBy = ''){
		return array(
			array(
				'id' => 1,
				'parentid' => 0,
				'text' => 'Custom Group',
				'contenttype' => 'item',
				'table' => '',
				'typ' => 'group',
				'open' => 0,
				'published' => 1,
				'disabled' => 0,
				'tooltip' => ''
			),
			array(
				'icon' => '',
				'id' => 2,
				'parentid' => 1,
				'text' => 'Custom Item',
				'contenttype' => 'item',
				'table' => '',
				'typ' => 'item',
				'open' => 0,
				'published' => 1,
				'disabled' => 0,
				'tooltip' => ''
			)
		);
	}

}
