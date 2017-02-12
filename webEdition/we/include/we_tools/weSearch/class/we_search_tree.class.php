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
class we_search_tree extends we_tree_base{
	static $treeItems = [];

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'search_tree.js');
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		$db = new DB_WE();
		$openFolders = [];

		if(isset($_SESSION['weS']['weSearch']['modelidForTree'])){
			$id = $_SESSION['weS']['weSearch']['modelidForTree'];
			$pid = f('SELECT ParentID FROM `' . SEARCH_TABLE . '` WHERE ID=' . intval($id), '', $db);
			$openFolders[] = $pid;
			while($pid > 0){
				$pid = f('SELECT ParentID FROM `' . SEARCH_TABLE . '` WHERE ID=' . intval($pid), '', $db);
				$openFolders[] = $pid;
			}
		}

		$wsQuery = '';
		$prevoffset = max(0, $offset - $segment);
		if($offset && $segment){
			self::$treeItems[] = ['id' => 'prev_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $prevoffset . '-' . $offset . ')',
				'contenttype' => 'arrowup',
				'table' => SEARCH_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'published' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $prevoffset
			];
		}

		$where = $wsQuery . ' ParentID=' . intval($ParentID) . ' ';

		$db->query('SELECT ID,ParentID,Path,Text,IsFolder FROM `' . SEARCH_TABLE . '` WHERE ' . $where . ' ORDER BY (Text REGEXP "^[0-9]") DESC,abs(Text),Text' . ($segment ? ' LIMIT ' . abs($offset) . ',' . abs($segment) : ''));

		while($db->next_record(MYSQL_ASSOC)){
			switch($db->f('Path')){
				case '/_PREDEF_/object':
				case '/_PREDEF_/object/unpublished':
					if(!defined('OBJECT_FILES_TABLE') || !defined('OBJECT_TABLE') || !we_base_permission::hasPerm('CAN_SEE_OBJECTFILES')){
						continue;
					}
				case '/_PREDEF_/document':
				case '/_PREDEF_/document/unpublished':
				case '/_PREDEF_/document/static':
				case '/_PREDEF_/document/dynamic':
					if(!we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
						continue;
					}
				default:
					if(strpos($db->f('Path'), '/_VERSION_') === 0 && !we_base_permission::hasPerm('SEE_VERSIONS')){
						continue;
					}

					$isOpen = (in_array($db->f('ID'), $openFolders) ? 1 : 0);

					$typ = [
						'typ' => ($db->f('IsFolder') ? 'group' : 'item'),
						'contenttype' => ($db->f('IsFolder') ? we_base_ContentTypes::FOLDER : 'we/search'),
						'open' => $isOpen,
						'disabled' => 0,
						'tooltip' => $db->f('ID'),
						'offset' => $offset,
						'order' => $db->f('Ordn'),
						'published' => 1,
						'disabled' => 0,
						'text' => oldHtmlspecialchars(we_search_model::getLangText($db->f('Path'), $db->f('Text'))),
					];
					$fields = array_change_key_case($db->Record, CASE_LOWER);


					self::$treeItems[] = array_merge($fields, $typ);

					if($db->f('IsFolder') && $isOpen){
						static::getItems($db->f('ID'), 0, $segment);
					}
			}
		}

		$total = f('SELECT COUNT(1) FROM `' . SEARCH_TABLE . '` WHERE ' . $where, '', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			self::$treeItems[] = ['id' => 'next_' . $ParentID,
				'parentid' => $ParentID,
				'text' => 'display (' . $nextoffset . '-' . ($nextoffset + $segment) . ')',
				'contenttype' => 'arrowdown',
				'table' => SEARCH_TABLE,
				'typ' => 'threedots',
				'open' => 0,
				'disabled' => 0,
				'tooltip' => '',
				'offset' => $nextoffset
			];
		}

		return self::$treeItems;
	}

}
