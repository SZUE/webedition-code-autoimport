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
class we_exim_XMLExport extends we_exim_XMLExIm{
	var $db;
	var $prepare = true;

	function __construct(){
		parent::__construct();
	}

	function setRefTable(&$rTable){
		$this->RefTable = $rTable;
	}

	function export($id, $ct, $fname, $table = "", $export_binary = true, $compression = ""){
		update_time_limit(0);
		$doc = we_exim_contentProvider::getInstance($ct, $id, $table);
		// add binary data separately to stay compatible with the new binary feature in v5.1
		if(isset($doc->ContentType) && (
			strpos($doc->ContentType, "image/") === 0 ||
			strpos($doc->ContentType, "application/") === 0 ||
			strpos($doc->ContentType, "video/") === 0)){
			//FIXME: this should be determined by document, not by type
			$doc->setElement("data", we_base_file::load($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $doc->Path));
		}

		$fh = fopen($fname, 'ab');
		if(!$fh){
			return -1;
		}

		$params = [];
		if(isset($doc->ID)){
			$params["ID"] = $doc->ID;
		}
		if(isset($doc->ContentType)){
			$params["ContentType"] = $doc->ContentType;
		} elseif(isset($doc->Table) && $doc->Table == DOC_TYPES_TABLE){
			$params["ContentType"] = "doctype";
		}

		$this->RefTable->setProp($params, "Examined", 1);

		$classname = $doc->ClassName;

		$attribute = (isset($doc->attribute_slots) ? $doc->attribute_slots : []);

		switch($classname){
			case 'we_backup_tableAdv':
				if((defined('OBJECT_X_TABLE') && strtolower(substr($doc->table, 0, 10)) == strtolower(stripTblPrefix(OBJECT_X_TABLE))) ||
					defined('CUSTOMER_TABLE')){
					$doc->getColumns();
				}
			//no break;
			default :
				we_exim_contentProvider::object2xml($doc, $fh, $attribute);
				break;
			case 'we_backup_binary':
			case 'weBinary'://FIXME remove
				if(!is_numeric($id)){
					$doc->Path = $doc->ID;
					$doc->ID = 0;
				}

				we_exim_contentProvider::binary2file($doc, $fh);
				break;
		}

		fwrite($fh, we_backup_util::backupMarker . "\n");

		if($classname === 'we_backup_tableItem' && $export_binary &&
			strtolower($doc->table) == strtolower(FILE_TABLE) &&
			($doc->ContentType == we_base_ContentTypes::IMAGE || stripos($doc->ContentType, "application/") !== false)){
			$bin = we_exim_contentProvider::getInstance("we_backup_binary", $doc->ID);
			$attribute = (isset($bin->attribute_slots) ? $bin->attribute_slots : []);
			we_exim_contentProvider::binary2file($bin, $fh);
		}

		fclose($fh);

		unset($doc);
	}

	function getSelectedItems($selection, $extype, $art, $type, $doctype, $classname, $categories, $dir, &$selDocs, &$selTempl, &$selObjs, &$selClasses){
		$this->db = new DB_WE();
		switch($selection){
			case "manual":
				if($extype == we_import_functions::TYPE_WE_XML){
					$selDocs = array_unique($this->getIDs($selDocs, FILE_TABLE, false));
					$selTempl = array_unique($this->getIDs($selTempl, TEMPLATES_TABLE, false));
					$selObjs = defined('OBJECT_FILES_TABLE') ? array_unique($this->getIDs($selObjs, OBJECT_FILES_TABLE, false)) : "";
					$selClasses = defined('OBJECT_FILES_TABLE') ? array_unique($this->getIDs($selClasses, OBJECT_TABLE, false)) : "";
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
				break;
			case 'doctype':
				$cat_sql = ($categories ? we_category::getCatSQLTail('', FILE_TABLE, true, $this->db, 'Category', $categories) : '');
				if($dir != 0){
					$workspace = id_to_path($dir, FILE_TABLE, $this->db);
					$ws_where = ' AND (' . FILE_TABLE . '.Path LIKE "' . $this->db->escape($workspace) . '/%" OR ' . FILE_TABLE . '.ID="' . $dir . '")';
				} else {
					$ws_where = '';
				}

				$this->db->query('SELECT distinct ID FROM ' . FILE_TABLE . ' WHERE 1 ' . $ws_where . '  AND ' . FILE_TABLE . '.IsFolder=0 AND ' . FILE_TABLE . '.DocType="' . $this->db->escape($doctype) . '"' . $cat_sql);
				$selDocs = $this->db->getAll(true);
				break;
			default:
				if(defined('OBJECT_FILES_TABLE')){
					$where = $this->queryForAllowed(OBJECT_FILES_TABLE);
					$cat_sql = ' ' . ($categories ? we_category::getCatSQLTail('', OBJECT_FILES_TABLE, true, $this->db, 'Category', $categories) : '');

					$this->db->query('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND TableID=' . intval($classname) . $cat_sql . $where);
					$selObjs = $this->db->getAll(true);
				}
		}

		foreach($selDocs as $v){
			$this->RefTable->add2(array(
				"ID" => $v,
				"ContentType" => f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($v), "", $this->db),
				"level" => 0
				)
			);
		}

		foreach($selTempl as $v){
			$this->RefTable->add2(array(
				"ID" => $v,
				"ContentType" => we_base_ContentTypes::TEMPLATE,
				"level" => 0
				)
			);
		}
		if(is_array($selObjs)){
			foreach($selObjs as $v){
				$this->RefTable->add2(array(
					"ID" => $v,
					"ContentType" => we_base_ContentTypes::OBJECT_FILE,
					"level" => 0
					)
				);
			}
		}
		if(is_array($selClasses)){
			foreach($selClasses as $v){
				$this->RefTable->add2(array(
					"ID" => $v,
					"ContentType" => "object",
					"level" => 0
				));
			}
		}
	}

	function queryForAllowed($table){
		$db = new DB_WE();
		$parentpaths = [];
		$wsQuery = '';
		if(($ws = get_ws($table))){
			$wsPathArray = id_to_path($ws, $table, $db, true);
			foreach($wsPathArray as $path){
				if($wsQuery != ''){
					$wsQuery .=' OR ';
				}
				$wsQuery .= ' Path LIKE "' . $db->escape($path) . '/%" OR ' . we_tool_treeDataSource::getQueryParents($path);
				while($path != "/" && $path){
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
			$ac = we_users_util::getAllowedClasses($db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				if($wsQuery != ''){
					$wsQuery .=' OR ';
				}
				$wsQuery .= ' Path LIKE "' . $db->escape($path) . '/%" OR Path="' . $db->escape($path) . '"';
			}
		}

		return we_users_util::makeOwnersSql() . ( $wsQuery ? ' OR (' . $wsQuery . ')' : '');
	}

	function getIDs($selIDs, $table, $with_dirs = false){
		if(empty($selIDs)){
			return [];
		}
		$tmp = [];
		$db = new DB_WE();
		$allow = $this->queryForAllowed($table);
		$db->query('SELECT ID FROM ' . $db->escape($table) . ' WHERE ID IN (' . implode(',', $selIDs) . ') AND IsFolder=1');
		$folders = $db->getAll(true);
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
		$db->query('SELECT ID FROM ' . $db->escape($table) . ' WHERE ID IN (' . implode(',', $tmp) . ') AND IsFolder=0');
		return $db->getAll(true);
	}

	function prepareExport(array $ids = []){
		//$this->RefTable = new RefTable();
		$preparer = new we_export_preparer($this->options, $this->RefTable);
		$preparer->prepareExport();
	}

	function exportInfoMap($info){
		$out = '<we:info>';
		foreach($info as $inf){
			$out.='<we:map';
			foreach($inf as $key => $value){
				$out.=' ' . $key . '="' . $value . '"';
			}
			$out.='></we:map>';
		}
		$out.='</we:info>' .
			we_backup_util::backupMarker . "\n";
		return $out;
	}

	public function loadPerserves(){
		parent::loadPerserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			$this->prepare = $_SESSION['weS']['ExImPrepare'];
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			$this->options = $_SESSION['weS']['ExImOptions'];
		}
	}

	public function savePerserves(){
		parent::savePerserves();
		$_SESSION['weS']['ExImPrepare'] = $this->prepare;
		$_SESSION['weS']['ExImOptions'] = $this->options;
	}

	public function unsetPerserves(){
		parent::unsetPerserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			unset($_SESSION['weS']['ExImPrepare']);
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			unset($_SESSION['weS']['ExImOptions']);
		}
	}

}
