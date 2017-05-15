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

// FIXME: move to some new class we_exim_preparer => no exporting in modul anymore
class we_export_preparer extends we_exim_ExIm{
	var $RefTable;
	var $options;
	var $PatternSearch;
	protected $db;

	function __construct(){
		parent::__construct();
		$this->PatternSearch = new we_exim_searchPatterns();
		$this->db = new DB_WE();
	}

	private function getDocumentIncludes($text, $level){
		$match = [];

		foreach($this->PatternSearch->doc_patterns["id"] as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $i => $include){
					if(stripos($match[0][$i], 'type="template"') === false){
						$this->addToDepArray($level, $include);
					}
				}
			}
		}

		foreach($this->PatternSearch->doc_patterns['path'] as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $path){
					$include = path_to_id($path, FILE_TABLE, $GLOBALS['DB_WE']);
					$this->addToDepArray($level, $include);
				}
			}
		}
	}

	private function getObjectIncludes($text, $level){
		$match = [];

		foreach($this->PatternSearch->obj_patterns["id"] as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $include){
					$this->addToDepArray($level, $include, we_base_ContentTypes::OBJECT_FILE, OBJECT_FILES_TABLE);
				}
			}
		}

		foreach($this->PatternSearch->class_patterns as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $include){
					$this->addToDepArray($level, $include, we_base_ContentTypes::OBJECT, OBJECT_TABLE);
				}
			}
		}
	}

	private function getExternalLinked($text, $level){
		$match = [];
		if(!is_array($text)){
			foreach($this->PatternSearch->ext_patterns as $pattern){
				if(preg_match_all($pattern, $text, $match)){
					foreach($match[2] as $external){
						$path = $this->isPathLocal($external);
						if($path && $path != '/'){
							$id = path_to_id($path, FILE_TABLE, $GLOBALS['DB_WE']);
							$this->addToDepArray($level, $id, (!empty($id) ? '' : 'weBinary'));
						}
					}
				}
			}
		}
	}

	private function getNavigation($text, $level){
		$navrules = $match = [];
		foreach($this->PatternSearch->navigation_patterns as $pattern){
			if(!preg_match_all($pattern, $text, $match)){
				continue;
			}
			foreach($match[2] as $value){
				if(!is_numeric($value)){
					continue;
				}
				$path = ($value ?
					f('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($value), '', $this->db) :
					'');

				$this->addToDepArray($level, $value, we_base_ContentTypes::NAVIGATION, NAVIGATION_TABLE);
				$navrules[] = $value;

				$this->db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $this->db->escape($path) . '/%"');
				while($this->db->next_record()){
					$this->addToDepArray($level, $this->db->f('ID'), we_base_ContentTypes::NAVIGATION, NAVIGATION_TABLE);
					$navrules[] = $this->db->f('ID');
				}
			}
		}
		if($navrules){
			$this->getNavigationRule($navrules, $level);
		}
	}

	private function getNavigationRule(array $naviid, $level){
		$this->db->query('SELECT ID FROM ' . NAVIGATION_RULE_TABLE . ' WHERE NavigationID IN (' . implode(',', $naviid) . ')');
		while($this->db->next_record()){
			$this->addToDepArray($level, $this->db->f('ID'), we_base_ContentTypes::NAVIGATIONRULE, NAVIGATION_RULE_TABLE);
		}
	}

	private function getThumbnail($text, $level){
		$match = $values = [];
		foreach($this->PatternSearch->thumbnail_patterns as $pattern){
			if(preg_match_all($pattern, $text, $match)){
				foreach($match[2] as $value){
					$values[] = $this->db->escape($value);
				}
			}
		}
		if($values){
			$this->db->query('SELECT ID FROM ' . THUMBNAILS_TABLE . ' WHERE Name IN ("' . (implode('","', $values)) . '")');
			while($this->db->next_record()){
				$this->addToDepArray($level, $this->db->f('ID'), 'weThumbnail', THUMBNAILS_TABLE);
			}
		}
	}

	private function getIncludesFromWysiwyg($text, $level){
		$match = [];

		if(is_array($text)){ // shop exception - handle array in the content
			foreach($text as $item1){
				if(!is_array($item1)){
					continue;
				}
				foreach($item1 as $item2){
					if(!is_array($item2)){
						continue;
					}
					foreach($item2 as $item3){
						if(is_array($item3) && in_array('bdid', array_keys($item3)) && !empty($item3['bdid'])){
							$this->addToDepArray($level, $item3['bdid']);
						}
					}
				}
			}
			return;
		}
		foreach($this->PatternSearch->wysiwyg_patterns as $patterns){
			foreach($patterns as $pattern){
				if(preg_match_all($pattern, $text, $match)){
					foreach($match[2] as $k => $include){
						$isobj = (strpos($match[1][$k], we_base_link::TYPE_OBJ_PREFIX) !== false);
						$this->addToDepArray($level, $include, ($isobj ? 'objectFile' : ''), ($isobj ? OBJECT_FILES_TABLE : ''));
					}
				}
			}
		}
	}

	private function isPathLocal($path){
		if(stripos($path, $_SERVER['SERVER_NAME']) !== false){
			$path = str_replace([getServerUrl(), getServerUrl(true)], '', $path);
			//try again with password
		}
		return (is_readable($_SERVER['DOCUMENT_ROOT'] . $path) ? $path : false);
	}

	private function addToDepArray($level, $id, $ct = "", $table = ""){
		if(!$ct){
			$table = $table ?: FILE_TABLE;
			$ct = f('SELECT ContentType FROM ' . escape_sql_query($table) . ' WHERE ID=' . intval($id), '', new DB_WE());
		}

		if($ct){
			$new = ['ID' => $id,
				'ContentType' => $ct,
				'level' => $level
			];
			if(!$this->RefTable->exists($new)){
				$this->RefTable->add2($new);
			}
		}
	}

	private function getDepFromArray($array){
		$ret = ["docs" => [], "objs" => []];

		if(!empty($array['id'])){
			$ret["docs"][] = $array['id'];
		}

		if(!empty($array['img_id'])){
			$ret["docs"][] = $array['img_id'];
		}

		if(!empty($array['obj_id'])){
			$ret["objs"][] = $array['obj_id'];
		} else {
			foreach($array as $key => $value){
				if($value && is_array($value)){
					$ret = array_merge_recursive($ret, $this->getDepFromArray($array[$key]));
				}
			}
		}
		return $ret;
	}

	private function getDependent(&$object, $level){
		if(isset($object->Table) && ($this->options['handle_document_includes'] || $this->options['handle_document_linked']) && isset($object->elements) && is_array($object->elements)){
			foreach($object->elements as $ek => $ev){

				if($this->options["handle_document_linked"]){

					if(strpos($ek, 'LinkID') !== false || strpos($ek, 'RollOverID') !== false || strpos($ek, 'longdescid') !== false){
						if(($val = $object->getElement($ek))){
							$this->addToDepArray($level, $val);
						}
					} else if(strpos($ek, 'ObjID') !== false){
						if(($val = $object->getElement($ek))){
							$this->addToDepArray($level, $val, 'objectFile');
						}
					}

					if(isset($ev["dat"])){
						$dat = we_unserialize($ev["dat"]);
						if(!is_array($dat) && $this->options["handle_document_linked"]){
							$this->getExternalLinked($ev["dat"], $level);
						}
					}
				}

				if($this->options["handle_document_includes"]){
					if(strpos($ek, 'intID') !== false){
						if(isset($ev['bdid'])){
							$this->addToDepArray($level, $ev['bdid']);
						}
					} else if(isset($ev["dat"])){
						$dat = we_unserialize($ev["dat"], [], true);
						if(is_array($dat)){
							$elarray = $this->getDepFromArray($dat);
							foreach($elarray as $elk => $elv){
								foreach($elv as $id){
									if($id){
										$this->addToDepArray($level, $id, ($elk === "docs" ? '' : "objectFile"));
									}
								}
							}
						} else {
							$this->getIncludesFromWysiwyg($ev["dat"], $level);
						}
					}
					if(!empty($ev["bdid"])){
						$this->addToDepArray($level, $ev['bdid']);
					}
				}
			}
		}

		if($object instanceof we_template){
			if($this->options["handle_def_templates"] && $object->MasterTemplateID){
				$this->addToDepArray($level, $object->MasterTemplateID, we_base_ContentTypes::TEMPLATE, TEMPLATES_TABLE);
			}
			$data = $object->getElement('data');
			if($this->options["handle_document_includes"]){
				$this->getDocumentIncludes($data, $level);
			}
			if($this->options["handle_object_includes"] && defined('OBJECT_FILES_TABLE')){
				$this->getObjectIncludes($data, $level);
			}
			if($this->options["handle_document_linked"]){
				$this->getExternalLinked($data, $level);
			}
			if($this->options['handle_navigation']){
				$this->getNavigation($data, $level);
			}
			if($this->options['handle_thumbnails']){
				$this->getThumbnail($data, $level);
			}

			$match = [];
			if($this->options['handle_def_templates']){
				foreach($this->PatternSearch->tmpl_patterns as $include_pattern){
					if(preg_match_all($include_pattern, $data, $match)){
						foreach($match[2] as $key => $value){
							$this->addToDepArray($level, $value, we_base_ContentTypes::TEMPLATE, TEMPLATES_TABLE);
						}
					}
				}
			}
		}

		if(!empty($object->TemplateID) && $this->options["handle_def_templates"]){
			$this->addToDepArray($level, $object->TemplateID, we_base_ContentTypes::TEMPLATE, TEMPLATES_TABLE);
		}

		if(isset($object->TableID) && $this->options["handle_def_classes"]){
			$this->addToDepArray($level, $object->TableID, we_base_ContentTypes::OBJECT, OBJECT_TABLE);
		}

		if(!empty($object->DocType) && $object->ClassName != "we_docTypes" && $this->options["handle_doctypes"]){
			$this->addToDepArray($level, $object->DocType, 'doctype', DOC_TYPES_TABLE);
		}

		if(!empty($object->Category) && $object->ClassName != "we_category" && $this->options["handle_categorys"]){
			$cats = makeArrayFromCSV($object->Category);
			foreach($cats as $cat){
				$this->addToDepArray($level, $cat, 'category', CATEGORY_TABLE);
			}
		}

		if($this->options['handle_object_embeds'] && defined('OBJECT_TABLE') && isset($object->ClassName) && $object->ClassName === 'we_objectFile'){
			foreach($object->elements as $key => $value){
				if(preg_match('|we_object_[0-9]+|', $key)){
					if(isset($value['dat'])){
						$this->addToDepArray($level, $value['dat'], we_base_ContentTypes::OBJECT_FILE, OBJECT_FILES_TABLE);
					}
				}
				if(preg_match('|we_object_[0-9]+_path|', $key)){
					if(isset($value['dat'])){
						$this->addToDepArray($level, path_to_id($value['dat'], OBJECT_FILES_TABLE, $GLOBALS['DB_WE']), we_base_ContentTypes::OBJECT_FILE, OBJECT_FILES_TABLE);
					}
				}
				switch((empty($value['type']) ? '' : $value['type'])){
					case 'img':
					case 'binary':
						$this->addToDepArray($level, isset($value['bdid']) ? $value['bdid'] : $value['dat']);
				}
			}
		}
	}

	private function makeExportList(){
		$searchCT = [we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::TEMPLATE, 'doctype', 'category', we_base_ContentTypes::OBJECT, we_base_ContentTypes::OBJECT_FILE,
			we_base_ContentTypes::IMAGE];

		$step = 0;
		while(($id = $this->RefTable->getNext())){
			$search = in_array($id->ContentType, $searchCT);
			if($search || $this->options['handle_owners']){
				$doc = we_exim_contentProvider::getInstance($id->ContentType, $id->ID);
			}

			if($search && $this->options['export_depth'] > $id->level){
				if(!isset($this->analyzed[$id->ContentType])){
					$this->analyzed[$id->ContentType] = [];
				}
				if(!in_array($id->ID, $this->analyzed[$id->ContentType])){
					$l = $id->level + 1;
					$this->getDependent($doc, $l);
					$this->analyzed[$id->ContentType][] = $id->ID;
				}
			}
			// collect owners info
			if($this->options["handle_owners"]){
				$uids = [];
				if(isset($doc->CreatorID) && !in_array($doc->CreatorID, $this->RefTable->Users)){
					$uids = [$doc->CreatorID];
				}
				if(isset($doc->Owners)){
					$uids = array_merge($uids, makeArrayFromCSV($doc->Owners));
				}
				if(!empty($uids)){
					$this->RefTable->addToUsers($uids);
				}
			}

			$step++;
			if(10 < $step){ //FIXME: removed BACKUP_STEPS, should be handled equal to backup
				break;
			}
		}

		if(isset($doc)){
			unset($doc);
		}
	}

	public function prepareExport(array $ids = []){
		//we_updater::fixInconsistentTables(); // FIXME: this call kills all elements!

		if($this->options['handle_def_templates'] ||
			$this->options['handle_doctypes'] ||
			$this->options['handle_categorys'] ||
			$this->options['handle_def_classes'] ||
			$this->options['handle_document_includes'] ||
			$this->options['handle_document_linked'] ||
			$this->options['handle_object_includes'] ||
			$this->options['handle_object_embeds'] ||
			$this->options['handle_class_defs'] ||
			$this->options['handle_owners'] ||
			$this->options['handle_navigation'] ||
			$this->options['handle_thumbnails']
		){
			$this->makeExportList();
		}

		// move objects to the end of the reftable because objects should be imported after classes
		if(defined('OBJECT_TABLE')){
			$this->RefTable->moveItemsToEnd('objectFile');
		}
	}

	public function loadPreserves(){
		parent::loadPreserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			$this->prepare = $_SESSION['weS']['ExImPrepare'];
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			$this->options = $_SESSION['weS']['ExImOptions'];
		}
	}

	public function savePreserves(){
		parent::savePreserves();
		$_SESSION['weS']['ExImPrepare'] = $this->prepare;
		$_SESSION['weS']['ExImOptions'] = $this->options;
	}

	public static function unsetPreserves(){
		parent::unsetPreserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			unset($_SESSION['weS']['ExImPrepare']);
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			unset($_SESSION['weS']['ExImOptions']);
		}
	}

}
