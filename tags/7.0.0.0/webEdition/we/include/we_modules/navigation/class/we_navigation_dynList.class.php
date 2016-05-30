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

	public static function getDocuments($doctypeid, $dirid, $categories, $catlogic, &$sort, $count, $field){
		$_select = array(
			FILE_TABLE . '.ID',
			FILE_TABLE . '.Text',
			LINK_TABLE . '.Name as FieldName',
			CONTENT_TABLE . '.Dat as FieldData'
		);

		$_fieldset = self::getDocData($_select, $doctypeid, id_to_path($dirid), $categories, $catlogic, array(), array(), 0);
		$_docs = $_txt = $_fields = $_ids = array();

		foreach($_fieldset as $data){
			if(!isset($_docs[$data['ID']])){
				$_docs[$data['ID']] = array();
			}
			$_docs[$data['ID']][$data['FieldName']] = $data['FieldData'];

			$_txt[$data['ID']] = $data['Text'];

			if($data['FieldName'] == $field){
				$_fields[$data['ID']] = $data['FieldData'];
			} elseif(!isset($_fields[$data['ID']])){
				$_fields[$data['ID']] = $data['Text'];
			}
		}

		unset($_fieldset);

		$_arr = array();
		$sort = is_array($sort) ? $sort : array();

		foreach($sort as $_k => $_sort){
			$_arr[$_k] = array();
			foreach($_docs as $_id => $_doc){
				$_arr[$_k]['id_' . $_id] = (in_array($_sort['field'], array_keys($_doc)) ?
						$_doc[$_sort['field']] :
						$_fields[$_id]);
			}
			if($_sort['order'] === 'DESC'){
				natcasesort($_arr[$_k]);
				$_arr[$_k] = array_reverse($_arr[$_k]);
			} else {
				natcasesort($_arr[$_k]);
			}
		}

		if($_arr){
			$_ids_tmp = array_keys($_arr[0]);

			$_ids = array();

			for($_i = 0; $_i < $count; $_i++){
				if(isset($_ids_tmp[$_i])){
					$_id = str_replace('id_', '', $_ids_tmp[$_i]);
					$_ids[$_i] = array(
						'id' => str_replace('id_', '', $_id),
						'text' => $_txt[$_id],
						'field' => we_navigation_navigation::encodeSpecChars(isset($_fields[$_id]) ? $_fields[$_id] : '')
					);
				} else {
					break;
				}
			}
		} else {
			$_counter = 0;
			foreach($_docs as $_id => $_doc){
				if($_counter < $count){
					$_ids[] = array(
						'id' => $_id,
						'field' => we_navigation_navigation::encodeSpecChars(isset($_fields[$_id]) ? $_fields[$_id] : ''),
						'text' => $_txt[$_id]
					);
					$_counter++;
				} else {
					break;
				}
			}
		}

		return $_ids;
	}

	private static function getDocData(array $select, $doctype, $dirpath = '/', $categories = array(), $catlogic = 'AND', $condition = array(), $order = array(), $offset = 0, $count = 100){

		$_db = new DB_WE();
		$categories = is_array($categories) ? $categories : makeArrayFromCSV($categories);
		$_cats = array();
		foreach($categories as $cat){
			$cat = is_numeric($cat) ? $cat : $_db->escape(path_to_id($cat, CATEGORY_TABLE, $GLOBALS['DB_WE']));
			$_cats[] = 'FIND_IN_SET(' . $cat . ',Category)'; //bug #6729
		}

		$dirpath = we_base_file::clearPath($dirpath . '/');

		$_db->query('SELECT ' . implode(',', $select) . ' FROM ' . FILE_TABLE . ' JOIN ' . LINK_TABLE . ' ON ' . FILE_TABLE . '.ID=' . LINK_TABLE . '.DID JOIN ' . CONTENT_TABLE . ' ON ' . LINK_TABLE . '.CID=' . CONTENT_TABLE . '.ID WHERE (' . FILE_TABLE . '.IsFolder=0 AND ' . FILE_TABLE . '.Published>0) ' .
			'AND ' . LINK_TABLE . '.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" ' .
			($doctype ? ' AND ' . FILE_TABLE . '.DocType=' . $_db->escape($doctype) : '') .
			($_cats ? (' AND (' . implode(" $catlogic ", $_cats) . ')') : '') .
			($dirpath != '/' ? (' AND Path LIKE "' . $_db->escape($dirpath) . '%"') : '') . ' ' .
			($condition ? (' AND ' . implode(' AND ', $condition)) : '') . ' ' .
			($order ? (' ORDER BY ' . $order) : '') .
			'  LIMIT ' . $offset . ',' . $count);


		return $_db->getAll();
	}

	public static function getObjects($classid, $dirid, $categories, $catlogic, &$sort, $count, $field){
		$select = array('OF_ID', 'OF_Text');

		if($field){
			$select[] = $field;
		}

		$sort = is_array($sort) ? $sort : array();

		$_order = array();
		foreach($sort as $_sort){
			$_order[] = $_sort['field'] . ' ' . $_sort['order'];
		}
		$categories = is_array($categories) ? $categories : makeArrayFromCSV($categories);
		$_fieldset = self::getObjData($select, $classid, id_to_path($dirid, OBJECT_FILES_TABLE), $categories, $catlogic, array(), $_order, 0, $count);
		$_ids = array();

		foreach($_fieldset as $data){
			$_ids[] = array(
				'id' => $data['OF_ID'],
				'text' => $data['OF_Text'],
				'field' => $field && $data[$field] ? we_navigation_navigation::encodeSpecChars($data[$field]) : ''
			);
		}

		return $_ids;
	}

	private static function getObjData(array $select, $classid, $dirpath = '/', array $categories = array(), $catlogic = 'AND', array $condition = array(), array $order = array(), $offset = 0, $count = 100){
		$_db = new DB_WE();
		$categories = is_array($categories) ? $categories : makeArrayFromCSV($categories);
		$_cats = array();
		foreach($categories as $cat){
			$cat = is_numeric($cat) ? $cat : $_db->escape(path_to_id($cat, CATEGORY_TABLE));
			$_cats[] = 'FIND_IN_SET(' . $cat . ',OF_Category)'; //bug #6729
		}

		$_where = array();

		if($_cats){
			$_where[] = '(' . implode(" $catlogic ", $_cats) . ')';
		}
		if($condition){
			$_where[] = implode(' AND ', $condition);
		}
		if($dirpath != '/'){
			$_where[] = 'OF_Path LIKE "' . $_db->escape($dirpath) . '%"';
		}
		$_where[] = 'OF_Published>0'; // Bug #4797
		$_db->query('SELECT ' . implode(',', $select) . ' FROM ' . OBJECT_X_TABLE . intval($classid) . ' WHERE OF_ID!=0 ' .
			($_where ? ('AND ' . implode(' AND ', $_where)) : '') .
			($order ? (' ORDER BY ' . implode(',', $order)) : '') . ' LIMIT ' . $offset . ',' . $count);

		return $_db->getAll();
	}

	public static function getCatgories($dirid, $count){
		$_ids = array();
		$db = new DB_WE();
		$db->query('SELECT ID,Text,Title FROM ' . CATEGORY_TABLE . ' WHERE ParentID=' . intval($dirid) . ' AND IsFolder=0  LIMIT ' . $count);

		while($db->next_record()){
			$_ids[] = array(
				'id' => $db->f('ID'),
				'text' => $db->f('Text'),
				'field' => we_navigation_navigation::encodeSpecChars($db->f('Title'))
			);
		}

		return $_ids;
	}

	public static function getWorkspacesForObject($id){
		$_obj = new we_objectFile();
		$_obj->initByID($id, OBJECT_FILES_TABLE);

		$_values = array_merge(makeArrayFromCSV($_obj->Workspaces), makeArrayFromCSV($_obj->ExtraWorkspaces));

		$_all = makeArrayFromCSV($_obj->getPossibleWorkspaces(false));
		$_ret = array();
		$db = new DB_WE();
		foreach($_values as $_k => $_id){
			if(!we_base_file::isWeFile($_id, FILE_TABLE, $db) || !in_array($_id, $_all)){
				unset($_values[$_k]);
			} else {
				$_ret[$_id] = id_to_path($_id, FILE_TABLE, $db);
			}
		}
		return $_ret;
	}

	public static function getWorkspacesForClass($id){
		$_obj = new we_object();
		$_obj->initByID($id, OBJECT_TABLE);

		$_values = makeArrayFromCSV($_obj->Workspaces);
		$db = new DB_WE();
		$_ret = array();
		foreach($_values as $_k => $_id){
			if(!we_base_file::isWeFile($_id, FILE_TABLE, $db)){
				unset($_values[$_k]);
			} else {
				$_ret[$_id] = id_to_path($_id, FILE_TABLE, $db);
			}
		}
		return $_ret;
	}

	/* 	function getDocumentsWithWorkspacePath($ws){
	  $_ret = array();
	  $db = new DB_WE();
	  foreach($ws as $_id => $_path){
	  $_ret[self::getFirstDynDocument($_id, $db)] = $_path;
	  }
	  return $_ret;
	  } */

	public static function getFirstDynDocument($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		return (
			($_id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($id) . ' AND IsFolder=0 AND IsDynamic=1 AND Published!=0;', '', $db))? :
				f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path LIKE "' . $db->escape(id_to_path($id, FILE_TABLE, $db)) . '%" AND IsFolder=0 AND IsDynamic=1 AND Published!=0;', '', $db)
			);
	}

	public static function getWorkspaceFlag($id){
		$_clsid = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), '', new DB_WE());
		$_cls = new we_object();
		$_cls->initByID($_clsid, OBJECT_TABLE);

		return $_cls->WorkspaceFlag;
	}

}
