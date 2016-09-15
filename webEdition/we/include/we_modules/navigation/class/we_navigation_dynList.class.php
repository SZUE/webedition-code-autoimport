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
abstract class we_navigation_dynList{

	public static function getDocuments($doctypeid, $dirid, $categories, $catlogic, $sort, $count, $field){
		$select = 'f.ID AS id,f.Text AS text,IFNULL(c.Dat,c.BDID) AS field';

		$fieldset = self::getDocData($select, $doctypeid, $dirid, $categories, $catlogic, $field, $sort, $count);

		return $fieldset;
	}

	private static function getDocData($select, $doctype, $dirID, $categories, $catlogic, $field, $order, $count = 100){
		$db = new DB_WE();
		$categories = is_array($categories) ? $categories : makeArrayFromCSV($categories);
		$cats = array();
		foreach($categories as $cat){
			$cat = is_numeric($cat) ? $cat : $db->escape(path_to_id($cat, CATEGORY_TABLE, $GLOBALS['DB_WE']));
			$cats[] = 'FIND_IN_SET(' . $cat . ',f.Category)'; //bug #6729
		}
		$sort = empty($order) ? array() : $order[0];

		$db->query('SELECT ' . $select . ' FROM ' . FILE_TABLE . ' f JOIN ' . LINK_TABLE . ' l ON (f.ID=l.DID AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '") JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID ' .
			($sort ? ' JOIN ' . LINK_TABLE . ' ls ON (ls.DID=f.ID AND ls.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '") JOIN ' . CONTENT_TABLE . ' cs ON (cs.ID=ls.CID)' : ''
			) . 'WHERE (f.IsFolder=0 AND f.Published>0) AND l.nHash=x\'' . md5($field) . '\' AND f.ParentID=' . $dirID .
			($doctype ? ' AND f.DocType=' . $db->escape($doctype) : '') .
			($cats ? (' AND (' . implode(" $catlogic ", $cats) . ')') : '') .
			($sort ? (' AND ls.nHash=x\'' . md5($sort['field']) . '\' ORDER BY IFNULL(cs.Dat,cs.BDID) ' . $sort['order']) : '') .
			'  LIMIT ' . $count);


		return $db->getAll();
	}

	public static function getObjects($classid, $dirid, $categories, $catlogic, $sort, $count, $field){
		$select = array('OF_ID', 'OF_Text');

		if($field){
			$select[] = $field;
		}

		$sort = is_array($sort) ? $sort : array();

		$order = array();
		foreach($sort as $sort){
			$order[] = $sort['field'] . ' ' . $sort['order'];
		}
		$categories = is_array($categories) ? $categories : makeArrayFromCSV($categories);
		$fieldset = self::getObjData($select, $classid, id_to_path($dirid, OBJECT_FILES_TABLE), $categories, $catlogic, array(), $order, 0, $count);
		$ids = array();

		foreach($fieldset as $data){
			$ids[] = array(
				'id' => $data['OF_ID'],
				'text' => $data['OF_Text'],
				'field' => $field && $data[$field] ? we_navigation_navigation::encodeSpecChars($data[$field]) : ''
			);
		}

		return $ids;
	}

	private static function getObjData(array $select, $classid, $dirpath = '/', array $categories = array(), $catlogic = 'AND', array $condition = array(), array $order = array(), $offset = 0, $count = 100){
		$db = new DB_WE();
		$categories = is_array($categories) ? $categories : makeArrayFromCSV($categories);
		$cats = array();
		foreach($categories as $cat){
			$cat = is_numeric($cat) ? $cat : $db->escape(path_to_id($cat, CATEGORY_TABLE));
			$cats[] = 'FIND_IN_SET(' . $cat . ',OF_Category)'; //bug #6729
		}

		$where = array();

		if($cats){
			$where[] = '(' . implode(" $catlogic ", $cats) . ')';
		}
		if($condition){
			$where[] = implode(' AND ', $condition);
		}
		if($dirpath != '/'){
			$where[] = 'OF_Path LIKE "' . $db->escape($dirpath) . '%"';
		}
		$where[] = 'OF_Published>0'; // Bug #4797
		$db->query('SELECT ' . implode(',', $select) . ' FROM ' . OBJECT_X_TABLE . intval($classid) . ' WHERE OF_ID!=0 ' .
			($where ? ('AND ' . implode(' AND ', $where)) : '') .
			($order ? (' ORDER BY ' . implode(',', $order)) : '') . ' LIMIT ' . $offset . ',' . $count);

		return $db->getAll();
	}

	public static function getCatgories($dirid, $count){
		$ids = array();
		$db = new DB_WE();
		$db->query('SELECT ID,Text,Title FROM ' . CATEGORY_TABLE . ' WHERE ParentID=' . intval($dirid) . ' LIMIT ' . $count);

		while($db->next_record()){
			$ids[] = array(
				'id' => $db->f('ID'),
				'text' => $db->f('Text'),
				'field' => we_navigation_navigation::encodeSpecChars($db->f('Title'))
			);
		}

		return $ids;
	}

	public static function getWorkspacesForObject($id){
		$obj = new we_objectFile();
		$obj->initByID($id, OBJECT_FILES_TABLE);

		$values = array_merge(makeArrayFromCSV($obj->Workspaces), makeArrayFromCSV($obj->ExtraWorkspaces));

		$all = makeArrayFromCSV($obj->getPossibleWorkspaces(false));
		$ret = array();
		$db = new DB_WE();
		foreach($values as $k => $id){
			if(!we_base_file::isWeFile($id, FILE_TABLE, $db) || !in_array($id, $all)){
				unset($values[$k]);
			} else {
				$ret[$id] = id_to_path($id, FILE_TABLE, $db);
			}
		}
		return $ret;
	}

	public static function getWorkspacesForClass($id){
		$obj = new we_object();
		$obj->initByID($id, OBJECT_TABLE);

		$values = makeArrayFromCSV($obj->Workspaces);
		$db = new DB_WE();
		$ret = array();
		foreach($values as $k => $id){
			if(!we_base_file::isWeFile($id, FILE_TABLE, $db)){
				unset($values[$k]);
			} else {
				$ret[$id] = id_to_path($id, FILE_TABLE, $db);
			}
		}
		return $ret;
	}

	/* 	function getDocumentsWithWorkspacePath($ws){
	  $ret = array();
	  $db = new DB_WE();
	  foreach($ws as $id => $path){
	  $ret[self::getFirstDynDocument($id, $db)] = $path;
	  }
	  return $ret;
	  } */

	public static function getFirstDynDocument($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		return (
			($id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($id) . ' AND IsFolder=0 AND IsDynamic=1 AND Published!=0', '', $db))? :
				f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path LIKE "' . $db->escape(id_to_path($id, FILE_TABLE, $db)) . '%" AND IsFolder=0 AND IsDynamic=1 AND Published!=0', '', $db)
			);
	}

	public static function getWorkspaceFlag($id){
		$clsid = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), '', new DB_WE());
		$cls = new we_object();
		$cls->initByID($clsid, OBJECT_TABLE);

		return $cls->WorkspaceFlag;
	}

}
