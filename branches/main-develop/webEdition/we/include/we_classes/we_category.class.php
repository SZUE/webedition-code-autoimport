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

/**
 * Class we_category
 *
 * Provides functions for handling webEdition category.
 */
class we_category extends we_base_model{
	var $ClassName = __CLASS__;
	var $ContentType = 'category';

	function __construct($id = 0){
		parent::__construct(CATEGORY_TABLE);
		if($id && is_int($id)){
			$this->load($id);
		}
	}

	function we_save(){
		parent::save();
		//FIXME:improve clean of nav Cache each time a category is saved!
		we_navigation_cache::clean(true);
	}

	function delete(){
		$ret = parent::delete();
		//FIXME:improve clean of nav Cache each time a category is deleted!
		we_navigation_cache::clean(true);
		return $ret;
	}

	static function getCatSQLTail($catCSV = '', $table = FILE_TABLE, $catOr = false, we_database_base $db = null, $fieldName = 'Category', $categoryids = ''){
		$db = $db ? : new DB_WE();
		$catCSV = trim($catCSV, ' ,');
		$pre = ' FIND_IN_SET("';
		$post = '",' . $table . '.' . $fieldName . ') ';

		$idarray = [];
		$folders = [];
		if($categoryids){
			$idarray2 = array_unique(array_map('trim', explode(',', trim($categoryids, ','))));
			$db->query('SELECT ID,1 AS IsFolder,Path FROM ' . CATEGORY_TABLE . ' WHERE ID IN(' . implode(',', $idarray2) . ')');
			while($db->next_record()){
				if($db->f('IsFolder')){
					//all folders need to be searched in deep
					$catCSV.=',' . $db->f('Path');
				} else {
					$idarray[] = $db->f('ID');
				}
			}
		}

		if($catCSV){
			$idarray1 = array_unique(array_map('trim', explode(',', trim($catCSV, ','))));
			foreach($idarray1 as $cat){
				$cat = '/' . trim($cat, '/ ');
				$isFolder = 0;
				$tmp = [];
				$db->query('SELECT ID, EXISTS (SELECT * FROM ' . CATEGORY_TABLE . ' cc WHERE c.ID=cc.ParentID) AS IsFolder FROM ' . CATEGORY_TABLE . ' c WHERE c.Path LIKE "' . $db->escape($cat) . '/%" OR c.Path="' . $db->escape($cat) . '"');
				while($db->next_record()){
					$tmp[] = $db->f('ID');
					$isFolder|=$db->f('IsFolder');
				}
				if($isFolder){
					$folders[] = $tmp;
				} else {
					$idarray = array_merge($idarray, $tmp);
				}
			}
		}
		if(empty($idarray) && empty($folders)){
			return '';
		}

		$where = [];
		if(!empty($idarray)){
			$where[] = $pre . implode($post . ($catOr ? ' OR ' : ' AND ') . $pre, array_unique($idarray)) . $post;
		}
		if(!empty($folders)){
			foreach($folders as &$cur){
				$where[] = '(' . $pre . implode($post . ' OR ' . $pre, $cur) . $post . ')';
			}
			unset($cur);
		}

		return ' AND (' . implode(($catOr ? ' OR ' : ' AND '), $where) . ' )';
	}

	public function registerMediaLinks(){
		if($this->Description){
			$this->MediaLinks = array_values(we_wysiwyg_editor::reparseInternalLinks($this->Description));
		}

		$this->unregisterMediaLinks();
		parent::registerMediaLinks();
	}

	static function we_getCatsFromDoc($doc, $tokken = ',', $showpath = false, we_database_base $db = null, $rootdir = '/', $catfield = '', $onlyindir = ''){
		return (isset($doc->Category) ?
				self::we_getCatsFromIDs($doc->Category, $tokken, $showpath, $db, $rootdir, $catfield, $onlyindir) :
				'');
	}

	static function we_getCatsFromIDs($catIDs, $tokken = ',', $showpath = false, we_database_base $db = null, $rootdir = '/', $catfield = '', $onlyindir = '', $asArray = false, $complete = false){
		return ($catIDs ?
				self::we_getCategories($catIDs, $tokken, $showpath, $db, $rootdir, $catfield, $onlyindir, $asArray, $complete) :
				($asArray ?
					[] :
					'')
			);
	}

	static function we_getCategories($catIDs, $tokken = ',', $showpath = false, we_database_base $db = null, $rootdir = '/', $catfield = '', $onlyindir = '', $asArray = false, $assoc = false, $complete = false, $includeDir = false, $order = ''){
		$db = ($db ? : new DB_WE());
		$cats = [];
		$whereIDs = trim($catIDs, ',') ? ' ID IN(' . trim($catIDs, ',') . ')' : 1;
		$whereDir = ' AND Path LIKE "' . $db->escape($onlyindir) . '/%"';
		$whereIncludeDir = $onlyindir && $includeDir !== false ? ' OR (Path = "' . $db->escape($onlyindir) . '")' : '';
		$orderBy = $order ? ' ORDER BY ' . $db->escape($order) : '';
		$field = $catfield ? : ($showpath ? 'Path' : 'Category');
		$showpath &=!$catfield;

		$db->query('SELECT ID,Path,Category,Title,Description, ParentID FROM ' . CATEGORY_TABLE . ' WHERE (' . $whereIDs . $whereDir . ')' . $whereIncludeDir . $orderBy);
		while($db->next_record()){
			$data = $db->getRecord();
			if(!$complete){
				$cat = ($field === 'Description' ? we_document::parseInternalLinks($data[$field], 0) : $data[$field]);
				if(($showpath || $catfield === 'Path') && strlen($rootdir)){
					if(substr($cat, 0, strlen($rootdir)) == $rootdir){
						$cat = substr($cat, strlen($rootdir));
					}
				}
				if($assoc){
					$cats[$data['ID']] = $cat;
				} else {
					$cats[] = $cat;
				}
			} else {//we return complete data allways as associative arrays
				$cats[$data['ID']] = array(
					'ID' => $data['ID'],
					'ParentID' => $data['ParentID'],
					'Path' => $data['Path'],
					'Category' => $data['Category'],
					'Title' => $data['Title'],
					'Description' => $data['Description'],
					'IsFolder' => 1//$data['IsFolder']
				);
			}
		}

		return ($complete || $asArray) ? $cats : implode($tokken, $cats);
	}

}
