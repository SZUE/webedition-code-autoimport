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
class we_export_preparer extends we_exim_XMLExIm{
	var $RefTable;
	var $options;
	var $PatternSearch;

	function __construct(){
		$this->RefTable = new we_exim_refTable();
		$this->PatternSearch = new we_exim_searchPatterns();
	}

	function getDocumentIncludes($text, $level){

		$trenner = "[\040|\n|\t|\r]*";
		$match = array();

		foreach($this->PatternSearch->doc_patterns["id"] as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $_i => $include){
					if(stripos($match[0][$_i], 'type="template"') === false){
						$this->addToDepArray($level, $include);
					}
				}
			}
		}

		foreach($this->PatternSearch->doc_patterns["path"] as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $path){
					$include = path_to_id($path);
					$this->addToDepArray($level, $include);
				}
			}
		}
	}

	function getObjectIncludes($text, $level){

		$match = array();

		foreach($this->PatternSearch->obj_patterns["id"] as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $include){
					$this->addToDepArray($level, $include, 'objectFile');
				}
			}
		}

		foreach($this->PatternSearch->class_patterns as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $include){
					$this->addToDepArray($level, $include, 'object', OBJECT_TABLE);
				}
			}
		}
	}

	function getExternalLinked($text, $level){

		$match = array();
		if(!is_array($text)){
			foreach($this->PatternSearch->ext_patterns as $pattern){
				if(preg_match_all($pattern, $text, $match)){
					foreach($match[2] as $external){
						$path = $this->isPathLocal($external);
						if($path && $path != '/'){
							$id = path_to_id($path);
							if(isset($id) && $id){
								$this->addToDepArray($level, $id);
							} else {
								$this->addToDepArray($level, $path, 'weBinary');
							}
						}
					}
				}
			}
		}
	}

	function getNavigation(&$text, $level){

		$match = array();
		foreach($this->PatternSearch->navigation_patterns as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				$_db = new DB_WE();
				foreach($match[2] as $key => $value){
					if(is_numeric($value)){
						$_path = '';
						if($value){
							$_path = f('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($value), 'Path', $_db);
						}

						$this->addToDepArray($level, $value, 'weNavigation', NAVIGATION_TABLE);
						$this->getNavigationRule($value, $level);

						$_db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $_db->escape($_path) . '/%"');
						while($_db->next_record()){
							$this->addToDepArray($level, $_db->f('ID'), 'weNavigation', NAVIGATION_TABLE);
							$this->getNavigationRule($_db->f('ID'), $level);
						}
					}
				}
			}
		}
	}

	function getNavigationRule($naviid, $level){
		$_db = new DB_WE();
		$_db->query('SELECT ID FROM ' . NAVIGATION_RULE_TABLE . ' WHERE NavigationID=' . intval($naviid));
		while($_db->next_record()){
			$this->addToDepArray($level, $_db->f('ID'), 'weNavigationRule', NAVIGATION_RULE_TABLE);
		}
	}

	function getThumbnail(&$text, $level){

		$match = array();
		foreach($this->PatternSearch->thumbnail_patterns as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				$_db = new DB_WE();
				foreach($match[2] as $key => $value){

					$_id = f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' WHERE Name="' . $_db->escape($value) . '"', 'ID', $_db);

					if($_id){
						$this->addToDepArray($level, $_id, 'weThumbnail', THUMBNAILS_TABLE);
					}
				}
			}
		}
	}

	function getIncludesFromWysiwyg($text, $level){

		$match = array();

		if(is_array($text)){ // shop exception - handle array in the content
			foreach($text as $_item1){
				if(is_array($_item1)){
					foreach($_item1 as $_item2){
						if(is_array($_item2)){
							foreach($_item2 as $_item3){
								if(is_array($_item3)){
									if(in_array('bdid', array_keys($_item3))){
										if(!empty($_item3['bdid'])){
											$this->addToDepArray($level, $_item3['bdid']);
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			foreach($this->PatternSearch->wysiwyg_patterns as $patterns){
				foreach($patterns as $pattern){
					if(preg_match_all($pattern, $text, $match)){
						foreach($match[2] as $k => $include){
							if(strpos($match[1][$k], we_base_link::TYPE_OBJ_PREFIX) !== false){
								$this->addToDepArray($level, $include, 'objectFile');
							} else {
								$this->addToDepArray($level, $include);
							}
						}
					}
				}
			}
		}
	}

	function isPathLocal($path){
		if(stripos($path, $_SERVER['SERVER_NAME']) !== false){
			$path = str_replace(getServerUrl(), '', $path);
			//try again with password
			$path = str_replace(getServerUrl(true), '', $path);
		}
		return (is_readable($_SERVER['DOCUMENT_ROOT'] . $path) ? $path : false);
	}

	function addToDepArray($level, $id, $ct = "", $table = ""){
		if(!$ct){
			if(!$table){
				$table = FILE_TABLE;
			}
			$ct = f('SELECT ContentType FROM ' . escape_sql_query($table) . ' WHERE ID=' . intval($id), 'ContentType', new DB_WE());
		}

		if($ct){
			$new = array(
				'ID' => $id,
				'ContentType' => $ct,
				'level' => $level
			);
			if(!$this->RefTable->exists($new)){
				$this->RefTable->add2($new);
			}
		}
	}

	function getDepFromArray($array){

		$ret = array("docs" => array(), "objs" => array());

		if(isset($array['id']) && $array['id']){
			$ret["docs"][] = $array['id'];
		}

		if(isset($array['img_id']) && $array['id']){
			$ret["docs"][] = $array['img_id'];
		}

		if(isset($array['obj_id']) && $array['obj_id']){
			$ret["objs"][] = $array['obj_id'];
		} else {
			foreach($array as $key => $value){
				if($value){
					if(is_array($value)){
						$ret = array_merge_recursive($ret, $this->getDepFromArray($array[$key]));
					}
				}
			}
		}
		return $ret;
	}

	function getDependent(&$object, $level){
		$trenner = "[\040|\n|\t|\r]*";
		if(isset($object->Table) && ($this->options['handle_document_includes'] || $this->options['handle_document_linked']) && isset($object->elements) && is_array($object->elements)){
			foreach($object->elements as $ek => $ev){

				if($this->options["handle_document_linked"]){

					if(strpos($ek, 'LinkID') !== false || strpos($ek, 'RollOverID') !== false || strpos($ek, 'longdescid') !== false){
						if(isset($ev['dat'])){
							$this->addToDepArray($level, $ev['dat']);
						}
					} else if(strpos($ek, 'ObjID') !== false){
						if(isset($ev['dat'])){
							$this->addToDepArray($level, $ev['dat'], 'objectFile');
						}
					}

					if(isset($ev["dat"])){
						$dat = @unserialize($ev["dat"]);
						if(!is_array($dat) && $this->options["handle_document_linked"]){
							$this->getExternalLinked($ev["dat"], $level);
						}
					}
				}

				if($this->options["handle_document_includes"]){
					if(strpos($ek, 'intID') !== false){
						if(isset($ev['dat'])){
							$this->addToDepArray($level, $ev['dat']);
						}
					} else if(isset($ev["dat"])){
						$dat = @unserialize($ev["dat"]);
						if(is_array($dat)){
							$elarray = $this->getDepFromArray($dat);
							foreach($elarray as $elk => $elv){
								foreach($elv as $id){
									if(!empty($id)){
										if($elk === "docs"){
											$this->addToDepArray($level, $id);
										} else {
											$this->addToDepArray($level, $id, "objectFile");
										}
									}
								}
							}
						} else {
							$this->getIncludesFromWysiwyg($ev["dat"], $level);
						}
					}
					if(isset($ev["bdid"]) && $ev["bdid"]){
						$this->addToDepArray($level, $ev['bdid']);
					}
				}
			}
		}

		if($object instanceof we_template){
			if($this->options["handle_def_templates"] && $object->MasterTemplateID){
				$this->addToDepArray($level, $object->MasterTemplateID, we_base_ContentTypes::TEMPLATE);
			}
			$_data = $object->getElement("data");
			if($this->options["handle_document_includes"]){
				$this->getDocumentIncludes($_data, $level);
			}
			if($this->options["handle_object_includes"] && defined('OBJECT_FILES_TABLE')){
				$this->getObjectIncludes($_data, $level);
			}
			if($this->options["handle_document_linked"]){
				$this->getExternalLinked($_data, $level);
			}
			if($this->options['handle_navigation']){
				$this->getNavigation($_data, $level);
			}
			if($this->options['handle_thumbnails']){
				$this->getThumbnail($_data, $level);
			}

			$match = array();
			if($this->options['handle_def_templates']){
				foreach($this->PatternSearch->tmpl_patterns as $_include_pattern){
					if(preg_match_all($_include_pattern, $_data, $match)){
						foreach($match[2] as $key => $value){
							$this->addToDepArray($level, $value, we_base_ContentTypes::TEMPLATE);
						}
					}
				}
			}
		}

		if(isset($object->TemplateID) && $object->TemplateID && $this->options["handle_def_templates"]){
			$this->addToDepArray($level, $object->TemplateID, we_base_ContentTypes::TEMPLATE);
		}

		if(isset($object->TableID) && $this->options["handle_def_classes"]){
			$this->addToDepArray($level, $object->TableID, "object");
		}

		if(isset($object->DocType) && $object->DocType && $object->ClassName != "we_docTypes" && $this->options["handle_doctypes"]){
			$this->addToDepArray($level, $object->DocType, "doctype");
		}

		if(isset($object->Category) && $object->Category && $object->ClassName != "we_category" && $this->options["handle_categorys"]){
			$cats = makeArrayFromCSV($object->Category);
			foreach($cats as $cat){
				$this->addToDepArray($level, $cat, "category");
			}
		}

		if(defined('OBJECT_TABLE') && isset($object->ClassName) && $object->ClassName === 'we_objectFile' && $this->options['handle_object_embeds']){
			foreach($object->elements as $key => $value){
				if(preg_match('|we_object_[0-9]+|', $key)){
					if(isset($value['dat'])){
						$this->addToDepArray($level, $value['dat'], 'objectFile');
					}
				}
				if(preg_match('|we_object_[0-9]+_path|', $key)){
					if(isset($value['dat'])){
						$this->addToDepArray($level, path_to_id($value['dat'], OBJECT_FILES_TABLE), 'objectFile');
					}
				}

				if(isset($value['type']) && ($value['type'] === 'img' || $value['type'] === 'binary')){
					$this->addToDepArray($level, isset($value['bdid']) ? $value['dat'] : $value['dat']);
				}
			}
		}
	}

	function makeExportList(){

		$serachCT = array(we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::TEMPLATE, 'doctype', 'category', 'object', 'objectFile', we_base_ContentTypes::IMAGE);

		$_step = 0;
		$id = $this->RefTable->getNext();
		while($id){
			$serach = in_array($id->ContentType, $serachCT);
			if($serach || $this->options["handle_owners"]){
				$doc = we_exim_contentProvider::getInstance($id->ContentType, $id->ID);
			}

			if($serach && $this->options["export_depth"] > $id->level){
				if(!isset($this->analyzed[$id->ContentType])){
					$this->analyzed[$id->ContentType] = array();
				}
				if(!in_array($id->ID, $this->analyzed[$id->ContentType])){
					$l = $id->level + 1;
					$this->getDependent($doc, $l);
					$this->analyzed[$id->ContentType][] = $id->ID;
				}
			}
			// collect owners info
			if($this->options["handle_owners"]){
				$uids = array();
				if(isset($doc->CreatorID) && !in_array($doc->CreatorID, $this->RefTable->Users)){
					$uids = array($doc->CreatorID);
				}
				if(isset($doc->Owners)){
					$uids = array_merge($uids, makeArrayFromCSV($doc->Owners));
				}
				if(!empty($uids)){
					$this->RefTable->addToUsers($uids);
				}
			}

			$id = $this->RefTable->getNext();
			$_step++;
			if(10 < $_step){ //FIXME: removed BACKUP_STEPS, should be handled equal to backup
				break;
			}
		}

		if(isset($doc)){
			unset($doc);
		}
	}

	function addToRefTable($ids){
		foreach($ids as $id){
			if(!$this->RefTable->exists($id)){
				$this->RefTable->add2($id);
			}
		}
	}

	function prepareExport(){
		we_updater::fixInconsistentTables();

		if($this->options['handle_def_templates'] || $this->options['handle_doctypes'] ||
			$this->options['handle_categorys'] || $this->options['handle_def_classes'] ||
			$this->options['handle_document_includes'] || $this->options['handle_document_linked'] ||
			$this->options['handle_object_includes'] || $this->options['handle_object_embeds'] ||
			$this->options['handle_class_defs'] || $this->options['handle_owners'] || $this->options['handle_navigation'] || $this->options['handle_thumbnails']
		){

			$this->makeExportList();
		}

		// move objects to the end of the reftable because objects should be imported after classes
		if(defined('OBJECT_TABLE')){
			$this->RefTable->moveItemsToEnd('objectFile');
		}
	}

	function loadPerserves(){
		parent::loadPerserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			$this->prepare = $_SESSION['weS']['ExImPrepare'];
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			$this->options = $_SESSION['weS']['ExImOptions'];
		}
	}

	function savePerserves(){
		parent::savePerserves();
		$_SESSION['weS']['ExImPrepare'] = $this->prepare;
		$_SESSION['weS']['ExImOptions'] = $this->options;
	}

	function unsetPerserves(){
		parent::unsetPerserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			unset($_SESSION['weS']['ExImPrepare']);
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			unset($_SESSION['weS']['ExImOptions']);
		}
	}

}
