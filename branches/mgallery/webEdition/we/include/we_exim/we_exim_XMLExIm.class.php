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
class we_exim_XMLExIm{
	var $destination = array();
	var $RefTable;
	var $chunk_count;
	var $chunk_number;
	var $analyzed = array();
	var $level = 0;
	//var $recover_mode=0; // 0 -	save all to selected folder; 1 - save with given path

	var $options = array(
		'handle_paths' => 0,
		'handle_def_templates' => 0,
		'handle_doctypes' => 0,
		'handle_categorys' => 0,
		'handle_def_classes' => 0,
		'handle_binarys' => 0,
		'update_mode' => 0,
		'handle_document_includes' => 0,
		'handle_document_linked' => 0,
		'handle_object_includes' => 0,
		'handle_object_embeds' => 0,
		'handle_class_defs' => 0,
		'export_depth' => 1,
		'handle_documents' => 0,
		'handle_templates' => 0,
		'handle_classes' => 0,
		'handle_objects' => 0,
		'handle_content' => 0,
		'handle_table' => 0,
		'handle_tableitems' => 0,
		'handle_binarys' => 0,
		'handle_doc_paths' => 0,
		'handle_templ_paths' => 0,
		'document_path' => '',
		'template_path' => '',
		'handle_collision' => '',
		'restore_doc_path' => 1,
		'restore_tpl_path' => 1,
		'handle_owners' => 0,
		'owners_overwrite' => 0,
		'owners_overwrite_id' => 0,
		'handle_navigation' => 0,
		'navigation_path' => 0,
		'handle_thumbnails' => 0,
		'change_encoding' => 0,
		'xml_encoding' => '',
		'target_encoding' => '',
		'rebuild' => 1
	);
	var $xmlBrowser;

	function __construct($file = ''){
		$this->RefTable = new we_exim_refTable();
		if($file){
			$this->loadPerserves($file);
		}

		$this->destination[strtolower(FILE_TABLE)] = 0;
		$this->destination[strtolower(TEMPLATES_TABLE)] = 0;
		$this->destination[strtolower(DOC_TYPES_TABLE)] = 0;
		if(defined('OBJECT_TABLE')){
			$this->destination[strtolower(OBJECT_TABLE)] = 0;
		}
		if(defined('OBJECT_FILES_TABLE')){
			$this->destination[strtolower(OBJECT_FILES_TABLE)] = 0;
		}
	}

	function setOptions($options){
		foreach($options as $k => $v){
			if(isset($this->options[$k])){
				$this->options[$k] = $v;
			}
		}
	}

	function setBackupProfile(){
		$options = array(
			"handle_content" => 1,
			"handle_table" => 1,
			"handle_tableitems" => 1,
			"handle_binarys" => 1,
		);

		$this->setOptions($options);
	}

	function getTable($ClassName){
		switch($ClassName){
			case 'we_template':
				return TEMPLATES_TABLE;
			case 'we_docTypes':
				return DOC_TYPES_TABLE;
			case 'we_category':
				return CATEGORY_TABLE;
			case 'we_navigation_navigation':
			case 'weNavigation':
				return NAVIGATION_TABLE;
			case 'we_navigation_rule':
			case 'weNavigationRule':
				return NAVIGATION_RULE_TABLE;
			case 'we_thumbnailEx':
			case 'we_thumbnail':
				return THUMBNAILS_TABLE;
			case 'we_backup_binary':
			case 'weBinary'://FIMXE remove
				return '';
			case 'we_object':
				return OBJECT_TABLE;
			case 'we_objectFile':
				return OBJECT_FILES_TABLE;

			default:
				return FILE_TABLE;
		}
	}

	function getTableForCT($we_ContentType, $table = ''){
		switch($we_ContentType){
			case "doctype":
				return DOC_TYPES_TABLE;
			case "category":
				return CATEGORY_TABLE;
			case we_base_ContentTypes::OBJECT:
				return (defined('OBJECT_TABLE')) ? OBJECT_TABLE : null;
			case we_base_ContentTypes::TEMPLATE:
				return TEMPLATES_TABLE;
			case we_base_ContentTypes::OBJECT_FILE:
				return (defined('OBJECT_FILES_TABLE')) ? OBJECT_FILES_TABLE : null;
			case "we_backup_binary":
			case 'weBinary'://FIMXE remove
				return null;
			case "weNavigation":
				return NAVIGATION_TABLE;
			case "weNavigationRule":
				return NAVIGATION_RULE_TABLE;
			case "weThumbnail":
				return THUMBNAILS_TABLE;
			case "folder":
				if(!empty($table)){
					return $table;
				}
			//intentionally no break
			default:
				return FILE_TABLE;
		}
	}

	public function loadPerserves(){
		if(isset($_SESSION['weS']['ExImRefTable'])){
			$this->RefTable = $_SESSION['weS']['ExImRefTable'];
		}
		if(isset($_SESSION['weS']['ExImRefUsers'])){
			$this->RefTable->Users = $_SESSION['weS']['ExImRefUsers'];
		}
		if(isset($_SESSION['weS']['ExImCurrentRef'])){
			$this->RefTable->current = $_SESSION['weS']['ExImCurrentRef'];
		}
	}

	public function savePerserves(){
		$_SESSION['weS']['ExImRefTable'] = $this->RefTable;
		$_SESSION['weS']['ExImRefUsers'] = $this->RefTable->Users;
		$_SESSION['weS']['ExImCurrentRef'] = $this->RefTable->current;
	}

	public function unsetPerserves(){
		if(isset($_SESSION['weS']['ExImRefTable'])){
			unset($_SESSION['weS']['ExImRefTable']);
		}
		if(isset($_SESSION['weS']['ExImRefUsers'])){
			unset($_SESSION['weS']['ExImRefUsers']);
		}
		if(isset($_SESSION['weS']['ExImCurrentRef'])){
			unset($_SESSION['weS']['ExImCurrentRef']);
		}
	}

	function resetContenID(&$object){//FIXME unused?
		if(isset($object->elements) && is_array($object->elements)){
			foreach($object->elements as $ek => $ev){
				$object->elements[$ek]["id"] = 0;
			}
		}
	}

	function prepareExport($ids){

		$this->RefTable = new we_exim_refTable();
		$_preparer = new we_export_preparer($this->options, $this->RefTable);
		$_preparer->prepareExport($ids);
	}

	static function getHeader($encoding = '', $type = ''){
		return '<?xml version="1.0" encoding="' . ($encoding ? : $GLOBALS['WE_BACKENDCHARSET']) . '" standalone="yes"?>' . "\n" .
			we_backup_backup::weXmlExImHead . ' version="' . WE_VERSION . '" type="' . $type . '" xmlns:we="we-namespace">' . "\n";
	}

	static function getFooter(){
		return we_backup_backup::weXmlExImFooter;
	}

	function getIDs($selIDs, $table, $with_dirs = false){
		$tmp = array();
		$db = new DB_WE();
		$allow = $this->queryForAllowed($table);
		if($selIDs){
			$db->query('SELECT ID FROM ' . $table . ' WHERE ID IN (' . implode(',', $selIDs) . ') AND IsFolder=1');
			$folders = $db->getAll(true);
		}
		foreach($selIDs as $v){
			if($v){
				if(in_array($v, $folders)){
					if($with_dirs){
						$tmp[] = $v;
					}
				} else {
					$tmp[] = $v;
				}
			}
		}
		if($folders){
			we_readChilds($folders, $tmp, $table, false, $allow);
		}
		if($with_dirs){
			return $tmp;
		}
		$db->query('SELECT ID FROM ' . $db->escape($table) . ' WHERE IsFolder=0 AND ID IN(' . ($tmp ? implode(',', $tmp) : '0') . ')');
		return $db->getAll(true);
	}

	function getQueryParents($path){
		$out = array();
		while($path != '/' && $path){
			$out[] = 'Path="' . $path . '"';
			$path = dirname($path);
		}
		return $out ? implode(' OR ', $out) : '';
	}

	function queryForAllowed($table){
		$db = new DB_WE();
		$parentpaths = $wsQuery = array();
		if(($ws = get_ws($table))){
			$wsPathArray = id_to_path($ws, $table, $db, false, true);
			foreach($wsPathArray as $path){
				$wsQuery[] = ' Path LIKE "' . $db->escape($path) . '/%" OR ' . we_exim_XMLExIm::getQueryParents($path);
				while($path != '/' && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
			$ac = we_users_util::getAllowedClasses($db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wsQuery[] = ' Path LIKE "' . $db->escape($path) . '/%" OR Path="' . $db->escape($path) . '"';
			}
		}

		return ' AND (1 ' . we_users_util::makeOwnersSql() . ( $wsQuery ? ' OR (' . implode(' OR ', $wsQuery) . ')' : '') . ')';
	}

	function getSelectedItems($selection, $extype, $art, $type, $doctype, $classname, $categories, $dir, &$selDocs, &$selTempl, &$selObjs, &$selClasses){
		$db = new DB_WE();
		if($selection === 'manual'){
			if($extype == we_import_functions::TYPE_WE_XML){
				$selDocs = $this->getIDs($selDocs, FILE_TABLE, false);
				$selTempl = $this->getIDs($selTempl, TEMPLATES_TABLE, false);
				$selObjs = defined('OBJECT_FILES_TABLE') ? $this->getIDs($selObjs, OBJECT_FILES_TABLE, false) : '';
				$selClasses = defined('OBJECT_FILES_TABLE') ? $this->getIDs($selClasses, OBJECT_TABLE, false) : '';
			} else {
				switch($art){
					case 'docs':
						$selDocs = $this->getIDs($selDocs, FILE_TABLE);
						break;
					case 'objects':
						$selObjs = defined('OBJECT_FILES_TABLE') ? $this->getIDs($selObjs, OBJECT_FILES_TABLE) : "";
						break;
				}
			}
			return;
		}
		switch($type){
			case 'doctype':
				$cat_sql = ($categories ? we_category::getCatSQLTail('', FILE_TABLE, true, $db, 'Category', $categories) : '');
				if($dir != 0){
					$workspace = id_to_path($dir, FILE_TABLE, $db);
					$ws_where = ' AND (' . FILE_TABLE . '.Path LIKE "' . $db->escape($workspace) . '/%" OR ' . FILE_TABLE . '.ID="' . $dir . '") ';
				} else {
					$ws_where = '';
				}

				$db->query('SELECT DISTINCT ID FROM ' . FILE_TABLE . ' f WHERE 1 ' . $ws_where . '  AND f.IsFolder=0 AND f.DocType="' . $db->escape($doctype) . '"' . $cat_sql);
				$selDocs = $db->getAll(true);
				return;
			default:
				if(defined('OBJECT_FILES_TABLE')){
					$cat_sql = ' ' . ($categories ? we_category::getCatSQLTail('', OBJECT_FILES_TABLE, true, $db, 'Category', $categories) : '');
					$where = $this->queryForAllowed(OBJECT_FILES_TABLE);

					$db->query('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND TableID=' . intval($classname) . $cat_sql . $where);
					$selObjs = $db->getAll(true);
				}
		}
	}

	function isBinary(){

	}

	function saveObject(&$object){
		if(!is_object($object)){
			return true;
		}
		$ret = true;
		// save binary data first to stay compatible with the new binary feature in v5.1
		if(method_exists($object, 'savebinarydata')){
			$object->savebinarydata();
		}

		if($object->ClassName === 'we_docTypes'){
			$ret = $object->we_save_exim();
		} else {
			$GLOBALS['we_doc'] = $object;
			if(method_exists($object, 'we_save')){
				if(!$object->we_save()){
					return false;
				}
			}

			if(method_exists($object, 'we_publish')){
				if(!$object->we_publish()){
					return false;
				}
			}

			if(method_exists($object, 'savebinarydata')){
				$object->setElement('data', '');
			}
		}
		return $ret;
	}

//FIXME: splitFile,exportChunk missing - called in Backup class
}
