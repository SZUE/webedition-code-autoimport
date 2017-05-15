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

// FIXME: rename to we_exim_ExIm
class we_exim_ExIm{
	var $destination = [];
	var $RefTable;
	var $chunk_count;
	var $chunk_number;
	var $analyzed = [];
	var $level = 0;
	//var $recover_mode=0; // 0 -	save all to selected folder; 1 - save with given path

	const TYPE_CSV = 'CSV';
	const TYPE_XML = 'XML';
	const TYPE_WE = 'WE';
	const TYPE_LOCAL_FILES = 'FileImport';
	const TYPE_SITE = 'siteImport';

	var $options = [
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
		'rebuild' => 1,
		'xml_cdata' => 1,
		'xml_table' => 'tblFile',
		'csv_delimiter' => 'colon',
		'csv_lineend' => 'windows',
		'csv_enclose' => 'doublequote',
		'csv_fieldnames' => 1
	];
	var $xmlBrowser;

	function __construct($file = ''){
		$this->RefTable = new we_exim_refTable();
		if($file){
			$this->loadPreserves($file);
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
		$options = [
			"handle_content" => 1,
			"handle_table" => 1,
			"handle_tableitems" => 1,
			"handle_binarys" => 1,
		];

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

	public static function getTableForCT($we_ContentType, $table = ''){
		switch($we_ContentType){
			case 'doctype':
				return DOC_TYPES_TABLE;
			case 'category':
				return CATEGORY_TABLE;
			case we_base_ContentTypes::OBJECT:
				return (defined('OBJECT_TABLE')) ? OBJECT_TABLE : null;
			case we_base_ContentTypes::TEMPLATE:
				return TEMPLATES_TABLE;
			case we_base_ContentTypes::OBJECT_FILE:
				return (defined('OBJECT_FILES_TABLE')) ? OBJECT_FILES_TABLE : null;
			case 'we_backup_binary':
			case 'weBinary'://FIMXE remove
				return null;
			case we_base_ContentTypes::NAVIGATION:
				return NAVIGATION_TABLE;
			case we_base_ContentTypes::NAVIGATIONRULE:
				return NAVIGATION_RULE_TABLE;
			case 'weThumbnail':
				return THUMBNAILS_TABLE;
			case we_base_ContentTypes::FOLDER:
				if(!empty($table)){
					return $table;
				}
			//intentionally no break
			default:
				return FILE_TABLE;
		}
	}

	public function loadPreserves(){
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

	public function savePreserves(){
		$_SESSION['weS']['ExImRefTable'] = $this->RefTable;
		$_SESSION['weS']['ExImRefUsers'] = $this->RefTable->Users;
		$_SESSION['weS']['ExImCurrentRef'] = $this->RefTable->current;
	}

	public static function unsetPreserves(){
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

	/* function resetContenID(&$object){
	  if(isset($object->elements) && is_array($object->elements)){
	  foreach($object->elements as $ek => $ev){
	  $object->elements[$ek]["id"] = 0;
	  }
	  }
	  } */

	//FIXME given parameter is not used in the call stack!
	function prepareExport(array $ids = []){
		$this->RefTable = new we_exim_refTable();
		$preparer = new we_export_preparer($this->options, $this->RefTable);
		$preparer->prepareExport($ids);
	}

	static function getHeader($encoding = '', $type = '', $skipWE = false){
		return '<?xml version="1.0" encoding="' . ($encoding ?: $GLOBALS['WE_BACKENDCHARSET']) . '" standalone="yes"?>' . "\n" .
			($skipWE ? '' : we_backup_util::weXmlExImHead . ' version="' . WE_VERSION . '" type="' . $type . '" xmlns:we="we-namespace">' . "\n");
	}

	static function getFooter(){
		return we_backup_util::weXmlExImFooter;
	}

	public static function queryForAllowed($table){
		$db = new DB_WE();
		$wsQuery = [];
		if(($ws = get_ws($table, true))){
			$wsPathArray = id_to_path($ws, $table, $db, true);
			foreach($wsPathArray as $path){
				$wsQuery[] = 'Path LIKE "' . $db->escape($path) . '/%"';
				$wsQuery[] = we_tool_treeDataSource::getQueryParents($path);
			}
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!we_base_permission::hasPerm("ADMINISTRATOR"))){
			$ac = we_users_util::getAllowedClasses($db);
			$paths = id_to_path($ac, OBJECT_TABLE);
			foreach($paths as $path){
				$wsQuery[] = 'Path LIKE "' . $db->escape($path) . '/%"';
				$wsQuery[] = 'Path="' . $db->escape($path) . '"';
			}
		}

		return ' AND (1 ' . we_users_util::makeOwnersSql() . ( $wsQuery ? ' OR (' . implode(' OR ', $wsQuery) . ')' : '') . ')';
	}

	function isBinary(){
		return false;
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
