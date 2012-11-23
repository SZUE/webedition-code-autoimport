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
 * @package    webEdition_model
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Class we_category
 *
 * Provides functions for handling webEdition category.
 */
class we_category extends weModelBase{

	var $ClassName = __CLASS__;
	var $ContentType = "category";

	function __construct(){
		parent::__construct(CATEGORY_TABLE);
	}

	function we_save(){
		if(isset($this->Catfields) && is_array($this->Catfields)){
			$this->Catfields = serialize($this->Catfields);
		}

		weModelBase::save();
	}

	static function getCatSQLTail($catCSV = '', $table = FILE_TABLE, $catOr = false, $db = '', $fieldName = 'Category', $getParentCats = true, $categoryids = ''){
		$db = $db ? $db : new DB_WE();
		$catCSV = trim($catCSV, ' ,');
		$idarray = array();
		if($categoryids){
			$idarray2 = array_map('trim', explode(',', trim($categoryids, ',')));
			sort($idarray2);
			$idarray2 = array_unique($idarray2);
			$db->query('SELECT ID,IsFolder,Path FROM ' . CATEGORY_TABLE . ' WHERE ID IN(' . implode(',', $idarray2) . ')');
			while($db->next_record()) {
				if($db->f('IsFolder')){
					//all folders need to be searched in deep
					$catCSV.=',' . $db->f('Folder');
				} else{
					$idarray[] = $db->f('ID');
				}
			}
		}

		if($catCSV){
			$idarray1 = array_map('trim', explode(',', trim($catCSV, ',')));
			sort($idarray1);
			$idarray1 = array_unique($idarray1);
			foreach($idarray1 as $cat){
				$cat = '/' . trim($cat, '/ ');

				$db->query('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Path LIKE "' . $db->escape($cat) . '/%" OR Path="' . $db->escape($cat) . '"');
				while($db->next_record()) {
					$idarray[] = $db->f('ID');
				}
			}
		}
		if(empty($idarray)){
			return '';
		}
		sort($idarray);
		$idarray = array_unique($idarray);

		$pre = ' FIND_IN_SET("';
		$post = '",' . $table . '.' . $fieldName . ') ';

		return (empty($idarray) ?
				' AND ' . $table . '.' . $fieldName . ' = "-1" ' :
				' AND (' . $pre . implode($post . ($catOr ? 'OR' : 'AND') . $pre, $idarray) . $post . ' )');
	}

}
