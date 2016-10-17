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

	public static function getDocuments($doctypeid, $dirid, array $categories, $catlogic, $sort, $count, $field){
		$select = 'f.ID AS id,f.Text AS text,IFNULL(c.Dat,c.BDID) AS field';

		return self::getDocData($select, $doctypeid, $dirid, $categories, $catlogic, $field, $sort, $count);
	}

	private static function getDocData($select, $doctype, $dirID, array $categories, $catlogic, $field, $order, $count = 100){
		$db = new DB_WE();
		$cats = [];
		foreach(array_filter($categories) as $cat){
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

	public static function getObjects($classid, $dirid, array $categories, $catlogic, array $sort, $count, $field){
		$select = 'of.ID,of.Text' . ($field ? ',obx.' . $field : '');
		$order = [];
		foreach($sort as $sort){
			$order[] = $sort['field'] . ' ' . $sort['order'];
		}
		$fieldset = self::getObjData($select, $classid, id_to_path($dirid, OBJECT_FILES_TABLE), $categories, $catlogic, $order, $count);
		$ids = [];

		foreach($fieldset as $data){
			$ids[] = ['id' => $data['ID'],
				'text' => $data['Text'],
				'field' => $field && $data[$field] ? we_navigation_navigation::encodeSpecChars($data[$field]) : ''
			];
		}

		return $ids;
	}

	private static function getObjData($select, $classid, $dirpath, array $categories, $catlogic, array $order, $count){
		$db = new DB_WE();
		$cats = [];
		foreach(array_filter($categories) as $cat){
			$cats[] = 'FIND_IN_SET(' . $cat . ',of.Category)'; //bug #6729
		}

		$where = [];

		if($cats){
			$where[] = '(' . implode($catlogic, $cats) . ')';
		}

		if($dirpath != '/'){
			$where[] = 'of.Path LIKE "' . $db->escape($dirpath) . '%"';
		}
		$where[] = 'of.Published>0'; // Bug #4797
		$db->query('SELECT ' . $select . ' FROM ' . OBJECT_X_TABLE . intval($classid) . ' obx JOIN ' . OBJECT_FILES_TABLE . ' of ON obx.OF_ID=of.ID WHERE of.ID!=0 ' .
			($where ? ('AND ' . implode(' AND ', $where)) : '') .
			($order ? (' ORDER BY ' . implode(',', $order)) : '') . ' LIMIT ' . $count);

		return $db->getAll();
	}

	public static function getCatgories($dirid, $count){
		$ids = [];
		$db = new DB_WE();
		$db->query('SELECT ID,Text,Title FROM ' . CATEGORY_TABLE . ' WHERE ParentID=' . intval($dirid) . ' LIMIT ' . $count);

		while($db->next_record(MYSQL_ASSOC)){
			$ids[] = [
				'id' => $db->f('ID'),
				'text' => $db->f('Text'),
				'field' => we_navigation_navigation::encodeSpecChars($db->f('Title'))
			];
		}

		return $ids;
	}

	public static function getWorkspacesForObject($id){
		$db = new DB_WE();

		list($obWsp, $clWsp) = getHash('SELECT of.Workspaces,o.Workspaces FROM ' . OBJECT_FILES_TABLE . ' of JOIN ' . OBJECT_TABLE . ' o ON of.TableID=o.ID WHERE of.ID=' . intval($id), $db, MYSQLI_NUM);

		$ret = id_to_path($obWsp, FILE_TABLE, $db, true);

		$all = we_objectFile::getPossibleWorkspaces($clWsp, $db);
		return array_intersect_key($ret, $all);
	}

	public static function getWorkspacesForClass($id){
		$values = f('SELECT Workspaces FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($id));
		return id_to_path($values, FILE_TABLE, $GLOBALS['DB_WE'], true);
	}

	/* 	function getDocumentsWithWorkspacePath($ws){
	  $ret = [];
	  $db = new DB_WE();
	  foreach($ws as $id => $path){
	  $ret[self::getFirstDynDocument($id, $db)] = $path;
	  }
	  return $ret;
	  } */

	public static function getFirstDynDocument($id, we_database_base $db = null){
		$db = $db ?: new DB_WE();
		return (
			($id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($id) . ' AND IsFolder=0 AND IsDynamic=1 AND Published!=0', '', $db)) ?:
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
