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

	public static function getDocuments($doctypeid, $dirid, array $categories, $catlogic, &$sort, $count, $field){
		$select = 'f.ID,f.Text,l.Name as FieldName,c.Dat as FieldData';

		$fieldset = self::getDocData($select, $doctypeid, id_to_path($dirid), $categories, $catlogic);
		$docs = $txt = $fields = $ids = [];

		foreach($fieldset as $data){
			if(!isset($docs[$data['ID']])){
				$docs[$data['ID']] = [];
			}
			$docs[$data['ID']][$data['FieldName']] = $data['FieldData'];

			$txt[$data['ID']] = $data['Text'];

			if($data['FieldName'] == $field){
				$fields[$data['ID']] = $data['FieldData'];
			} elseif(!isset($fields[$data['ID']])){
				$fields[$data['ID']] = $data['Text'];
			}
		}

		unset($fieldset);

		$arr = [];
		$sort = is_array($sort) ? $sort : [];

		foreach($sort as $k => $sort){
			$arr[$k] = [];
			foreach($docs as $id => $doc){
				$arr[$k]['id_' . $id] = (in_array($sort['field'], array_keys($doc)) ?
						$doc[$sort['field']] :
						$fields[$id]);
			}
			if($sort['order'] === 'DESC'){
				natcasesort($arr[$k]);
				$arr[$k] = array_reverse($arr[$k]);
			} else {
				natcasesort($arr[$k]);
			}
		}

		if($arr){
			$ids_tmp = array_keys($arr[0]);

			$ids = [];

			for($i = 0; $i < $count; $i++){
				if(isset($ids_tmp[$i])){
					$id = str_replace('id_', '', $ids_tmp[$i]);
					$ids[$i] = array(
						'id' => str_replace('id_', '', $id),
						'text' => $txt[$id],
						'field' => we_navigation_navigation::encodeSpecChars(isset($fields[$id]) ? $fields[$id] : '')
					);
				} else {
					break;
				}
			}
		} else {
			$counter = 0;
			foreach($docs as $id => $doc){
				if($counter < $count){
					$ids[] = array(
						'id' => $id,
						'field' => we_navigation_navigation::encodeSpecChars(isset($fields[$id]) ? $fields[$id] : ''),
						'text' => $txt[$id]
					);
					$counter++;
				} else {
					break;
				}
			}
		}

		return $ids;
	}

	private static function getDocData(array $select, $doctype, $dirpath, array $categories, $catlogic){
		$db = new DB_WE();
		$cats = [];
		foreach($categories as $cat){
			$cats[] = 'FIND_IN_SET(' . $cat . ',f.Category)'; //bug #6729
		}

		$dirpath = we_base_file::clearPath($dirpath . '/');

		$db->query('SELECT ' . $select . ' FROM ' . FILE_TABLE . ' f JOIN ' . LINK_TABLE . ' l ON f.ID=l.DID JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE (f.IsFolder=0 AND f.Published>0) ' .
			'AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" ' .
			($doctype ? ' AND f.DocType=' . $db->escape($doctype) : '') .
			($cats ? (' AND (' . implode($catlogic, $cats) . ')') : '') .
			($dirpath != '/' ? (' AND f.Path LIKE "' . $db->escape($dirpath) . '%"') : '') .
			' LIMIT 100');


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
			$ids[] = array(
				'id' => $data['ID'],
				'text' => $data['Text'],
				'field' => $field && $data[$field] ? we_navigation_navigation::encodeSpecChars($data[$field]) : ''
			);
		}

		return $ids;
	}

	private static function getObjData($select, $classid, $dirpath, array $categories, $catlogic, array $order, $count){
		$db = new DB_WE();
		$cats = [];
		foreach($categories as $cat){
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
		$ret = [];
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
		$ret = [];
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
	  $ret = [];
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
