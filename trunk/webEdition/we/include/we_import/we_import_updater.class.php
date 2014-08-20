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
class we_import_updater extends we_exim_XMLExIm{
	var $RefTable;
	var $UpdateItemsCount = 1;
	var $Patterns;
	var $debug = false;

	public function __construct(){
		parent::__construct();
	}

	public function updateObject(&$object){

		if($this->debug){
			t_e("Updating object", $object->ID, (isset($object->Path) ? $object->Path : ''), (isset($object->Table) ? $object->Table : ''));
		}

		$this->Patterns = new we_exim_searchPatterns();

		if(isset($object->MasterTemplateID) && $object->MasterTemplateID){
			$ref = $this->RefTable->getRef(
				array(
					'OldID' => $object->MasterTemplateID,
					'ContentType' => we_base_ContentTypes::TEMPLATE
				)
			);
			if($ref){
				$object->MasterTemplateID = $ref->ID;
				$object->_updateCompleteCode(true);
			}
		}

		if(isset($object->ClassName) && $object->ClassName == "we_template"){
			$this->updateTemplate($object);
		}

		if($this->debug){
			t_e("Updating TemplateID property");
		}
		if(isset($object->TemplateID) && $object->TemplateID){
			$ref = $this->RefTable->getRef(
				array(
					"OldID" => $object->TemplateID,
					"ContentType" => we_base_ContentTypes::TEMPLATE
				)
			);
			if($ref){
				$object->TemplateID = $ref->ID;
			} else if(isset($object->TemplatePath) && $object->TemplatePath){
				$ref = $this->RefTable->getRef(
					array(
						"ID" => $object->ID,
						"Table" => $object->Table
					)
				);
				if($ref && isset($ref->OldTemplatePath)){
					$tpath = we_base_file::clearPath(preg_replace('|^.+' . ltrim(TEMPLATES_DIR, '/') . '|i', '', $ref->OldTemplatePath));
					$id = path_to_id($tpath, TEMPLATES_TABLE);
					if($id){
						$object->TemplateID = $id;
					}
				}
			}
		}

		if($this->debug){
			t_e("Updating DocType property");
		}
		if(isset($object->DocType) && $object->ClassName != "we_docTypes"){
			$ref = $this->RefTable->getRef(
				array(
					"OldID" => $object->DocType,
					"ContentType" => "doctype"
				)
			);
			if($ref){
				$object->DocType = $ref->ID;
			} else if(isset($object->OldDocTypeName) && $object->OldDocTypeName){
				$object->DocType = intval(f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $GLOBALS['DB_WE']->escape($object->OldDocTypeName) . '"'));
			}
		}

		if($this->debug){
			t_e("Updating Category property");
		}
		if(isset($object->Category) && $object->ClassName != "we_category"){
			$cats = makeArrayFromCSV($object->Category);
			$newcats = array();
			foreach($cats as $cat){
				$ref = $this->RefTable->getRef(
					array(
						"OldID" => $cat,
						"ContentType" => "category"
					)
				);
				if($ref){
					$newcats[] = $ref->ID;
				}
			}
			if(!empty($newcats)){
				$object->Category = makeCSVFromArray($newcats);
			}
		}

		// update class for embedded object
		if(isset($object->ClassName) && ($object->ClassName == 'we_object') && preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+|', implode(',', array_keys($object->SerializedArray)))){
			$this->updateObjectModuleData($object);
		}

		//update binary elements
		if(isset($object->elements)){
			$this->updateElements($object);
		}

		if(isset($object->ContentType) && ($object->ContentType == 'weNavigation' || $object->ContentType == 'weNavigationRule')){
			$this->updateNavigation($object);
		}

		if($object->ClassName == "we_docTypes"){
			$this->updateDocType($object);
		}

		if($this->debug){
			t_e("Saving object...");
		}

		we_exim_XMLExIm::saveObject($object);

		if($this->debug){
			t_e("Object saved");
		}
	}

	private function updateElements(&$object){
		if(!isset($object->elements)){
			return;
		}

		if($this->debug){
			t_e("Updating elements");
		}
		if(isset($object->ClassName) && $object->ClassName == "we_objectFile"){
			$regs = array();
			$del_elements = array();
			$new_elements = array();
			$new_defs = array();
			$del_defs = array();
		}

		foreach($object->elements as $k => &$element){
			if($this->debug){
				t_e("Updating object element ", $k);
			}

			if(strpos($k, 'intID') !== false || strpos($k, 'LinkID') !== false || strpos($k, 'RollOverID') !== false){
				if(isset($element['dat'])){
					$ref = $this->RefTable->getRef(
						array(
							'OldID' => $element['dat'],
							'Table' => FILE_TABLE
						)
					);
					$element['dat'] = ($ref ? $ref->ID : 0);
				}
			}

			if(isset($element["bdid"])){
				$ref = $this->RefTable->getRef(
					array(
						'OldID' => $element['bdid'],
						'Table' => FILE_TABLE
					)
				);

				$element['bdid'] = ($ref ? $ref->ID : 0);
			}

			if(isset($object->ClassName) && ($object->ClassName == "we_objectFile")){

				if(preg_match('|we_object_([0-9])+_path|', $k, $regs)){
					$ref = $this->RefTable->getRef(
						array(
							'OldID' => $regs[1],
							'Table' => OBJECT_TABLE
						)
					);
					if($ref){
						$classid = $ref->ID;
						$objid = $object->elements['we_object_' . $regs[1]]['dat'];
						$objpath = $object->elements['we_object_' . $regs[1] . '_path']['dat'];
						$objref = $this->RefTable->getRef(
							array(
								'OldID' => $objid,
								'Table' => OBJECT_FILES_TABLE
							)
						);
						if($objref){
							$objid = $objref->ID;
							$objpath = $objref->Path;
						} else {
							$objid = path_to_id($objpath, OBJECT_FILES_TABLE);
						}
						if($objid){
							$del_elements[] = $regs[1];
							$del_elements[] = 'we_object_' . $regs[1];
							$del_elements[] = 'we_object_' . $regs[1] . '_path';
							$new_elements[$ref->ID] = array('type' => 'object', 'len' => 22);
							$new_elements['we_object_' . $ref->ID] = array('type' => 'object', 'len' => 22, 'dat' => $objid);
							$new_elements['we_object_' . $ref->ID . '_path'] = array('type' => 'object', 'len' => 22, 'dat' => $objpath);

							if(isset($object->DefArray[we_object::QUERY_PREFIX . $regs[1]])){
								$del_defs[] = we_object::QUERY_PREFIX . $regs[1];
								$new_defs[we_object::QUERY_PREFIX . $ref->ID] = $object->DefArray[we_object::QUERY_PREFIX . $regs[1]];
							}
						}
					}
				}

				if($element['type'] == 'img' || $element['type'] == 'binary'){
					$objref = $this->RefTable->getRef(
						array(
							'OldID' => $element['dat'],
							'Table' => FILE_TABLE
						)
					);

					if($objref){
						$element['dat'] = $objref->ID;
					}
				}
			}

			if(isset($object->ClassName) && ($object->ClassName == "we_object") && preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+([a-zA-Z]*[0-9]*)|', $k, $regs)){
				if(count($regs) > 2 && isset($object->elements[we_object::QUERY_PREFIX . $regs[1] . $regs[2]])){
					$ref = $this->RefTable->getRef(
						array(
							'OldID' => $regs[1],
							'Table' => OBJECT_TABLE
						)
					);
					if($ref){
						$object->elements[we_object::QUERY_PREFIX . $ref->ID . $regs[2]] = array_merge_recursive($object->elements[we_object::QUERY_PREFIX . $regs[1] . $regs[2]]);
						unset($object->elements[we_object::QUERY_PREFIX . $regs[1] . $regs[2]]);
					}
				}
			}
		}
		unset($element);

		// update object for embedded object
		if(isset($new_elements) && count($new_elements)){
			foreach($del_elements as $delid){
				unset($object->elements[$delid]);
			}
			foreach($del_defs as $delid){
				unset($object->DefArray[$delid]);
			}

			foreach($new_elements as $ek => $ev){
				$object->elements[$ek] = $ev;
			}
			foreach($new_defs as $ek => $ev){
				$object->DefArray[$ek] = $ev;
			}
		}

		if(isset($object->ContentType) && ($object->ContentType == we_base_ContentTypes::WEDOCUMENT || $object->ContentType == we_base_ContentTypes::HTML)){
			if(isset($object->elements["data"])){
				if($this->debug){
					debug("Updating webEdition and html documents for external links\n");
				}
				$source = $object->getElement("data");
				$this->updateSource($this->Patterns->ext_patterns, $source, "Path");
				$object->setElement("data", $source);
			}
		}

		// update elements serialized data
		if($object->isBinary() != 1){
			if($this->debug){
				debug("Updating serialized data in elements\n");
			}
			foreach($object->elements as $ek => $ev){
				if($this->debug){
					debug2($ev);
				}
				if(isset($ev["dat"])){
					$dat = @unserialize($ev["dat"]);
					if(is_array($dat)){
						$this->updateArray($dat);
						$object->elements[$ek]["dat"] = serialize($dat);
					} else {
						if(isset($object->ContentType) && ($object->ContentType == we_base_ContentTypes::WEDOCUMENT || $object->ContentType == we_base_ContentTypes::HTML)){
							$source = $ev["dat"];
							$this->updateSource($this->Patterns->wysiwyg_patterns['doc'], $source);
							$this->updateSource($this->Patterns->wysiwyg_patterns['obj'], $source);
							$object->elements[$ek]["dat"] = $source;
						}
					}
				}
			}
		}
	}

	private function updateTemplate(&$object){
		if(!(isset($object->ClassName) && $object->ClassName == "we_template")){
			return;
		}

		if($this->debug){
			debug("Updating template source...\n");
		}

		$source = $object->getElement("data");

		$this->updateSource($this->Patterns->doc_patterns["id"], $source, 'ID');
		$this->updateSource($this->Patterns->doc_patterns["path"], $source, 'Path');
		if(defined('OBJECT_TABLE')){
			$this->updateSource($this->Patterns->obj_patterns["id"], $source, 'ID', OBJECT_FILES_TABLE);
			$this->updateSource($this->Patterns->doc_patterns["path"], $source, 'Path', OBJECT_FILES_TABLE);
			$this->updateSource($this->Patterns->class_patterns, $source, 'ID', OBJECT_TABLE);
		}

		$this->updateSource($this->Patterns->navigation_patterns, $source, 'ID', NAVIGATION_TABLE);

		$this->updateSource($this->Patterns->tmpl_patterns, $source, 'ID', TEMPLATES_TABLE);

		// must be at the end
		$this->updateSource($this->Patterns->special_patterns, $source, 'ID', FILE_TABLE);

		$object->setElement("data", $source);
	}

	private function updateObjectModuleData(&$object){

		if(isset($object->ClassName) && ($object->ClassName == "we_object") && preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+|', implode(',', array_keys($object->SerializedArray)))){
			if($this->debug){
				debug("Updating object module data...\n");
			}
			$new = array();
			$del = array();
			foreach($object->SerializedArray as $elkey => $elvalue){
				if(preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+|', $elkey, $regs)){
					if(count($regs) > 1){
						$ref = $this->RefTable->getRef(
							array(
								'OldID' => $regs[1],
								'Table' => OBJECT_TABLE
							)
						);
						if($ref){
							$new[we_object::QUERY_PREFIX . $ref->ID] = array_merge_recursive($object->SerializedArray[$elkey]);
						}
						$del[] = $elkey;
					}
				}
			}
			foreach($del as $d){
				unset($object->SerializedArray[$d]);
			}
			$object->SerializedArray = array_merge($object->SerializedArray, $new);
			$object->DefaultValues = serialize($object->SerializedArray);
		}
	}

	private function updateDocType(&$object){

		if($object->ClassName == "we_docTypes"){
			if($this->debug){
				debug("Updating doctype object...\n");
			}
			// quick fix for fsw
			if(isset($object->ParentPath) && $object->ParentPath){
				$_new_id = path_to_id($object->ParentPath);
				if($_new_id){
					$object->ParentID = $_new_id;
				} else {
					$object->ParentID = 0;
					$object->ParentPath = '/';
				}
			}

			if(isset($object->Templates) && strlen($object->Templates) > 0){

				$_tids = makeArrayFromCSV($object->Templates);
				$_new_tids = array();
				foreach($_tids as $_tid){
					$_ref = $this->RefTable->getRef(
						array(
							'OldID' => $_tid,
							'Table' => TEMPLATES_TABLE
						)
					);
					if($_ref){
						$_new_tids[] = $_ref->ID;
					}
				}
				$object->Templates = makeCSVFromArray($_new_tids);
			}
		}
	}

	private function updateNavigation(&$object){
		if(isset($object->ContentType) && $object->ContentType == 'weNavigation'){
			if($this->debug){
				debug("Updating navigation...\n");
			}
			if($object->IsFolder){
				$this->updateField($object, 'LinkID', FILE_TABLE);
			}
			if(isset($object->Selection) && $object->Selection == 'dynamic'){

				switch($object->SelectionType){

					case 'doctype':
						$this->updateField($object, 'DocTypeID', DOC_TYPES_TABLE);
						$this->updateField($object, 'FolderID', FILE_TABLE);
						break;

					case 'classname':
						if(defined('OBJECT_TABLE')){
							$this->updateField($object, 'ClassID', OBJECT_TABLE);
							$this->updateField($object, 'FolderID', OBJECT_FILES_TABLE);
							$this->updateField($object, 'WorkspaceID', OBJECT_FILES_TABLE);
						}
						break;

					case 'category':
						$this->updateField($object, 'FolderID', CATEGORY_TABLE);
						if($object->LinkSelection == 'intern'){
							$this->updateField($object, 'UrlID', FILE_TABLE);
						}
						break;
				}
			}

			if(isset($object->Selection) && $object->Selection == 'static'){



				switch($object->SelectionType){

					case 'docLink' :
						$this->updateField($object, 'LinkID', FILE_TABLE);
						break;
					case 'objLink' :
						$this->updateField($object, 'LinkID', OBJECT_FILES_TABLE);
						break;
					case 'catLink' :
						$this->updateField($object, 'LinkID', CATEGORY_TABLE);
						if($object->LinkSelection == 'intern'){
							$this->updateField($object, 'UrlID', FILE_TABLE);
						}
						break;
				}
			}
		}

		if(isset($object->ContentType) && $object->ContentType == 'weNavigationRule'){

			$this->updateField($object, 'NavigationID', NAVIGATION_TABLE);
			$this->updateField($object, 'DoctypeID', DOC_TYPES_TABLE);

			if($object->SelectionType == 'classname'){
				if(defined('OBJECT_TABLE')){
					$this->updateField($object, 'FolderID', OBJECT_FILES_TABLE);
				}
			} else {
				$this->updateField($object, 'FolderID', FILE_TABLE);
			}

			if(defined('OBJECT_TABLE')){
				$this->updateField($object, 'ClassID', OBJECT_TABLE);
				$this->updateField($object, 'WorkspaceID', OBJECT_FILES_TABLE);
			}
		}

		if(isset($object->ContentType) && ($object->ContentType == 'weNavigation' || $object->ContentType == 'weNavigationRule')){
			if(isset($object->Categories) && is_array($object->Categories)){
				$_cats = $object->Categories;
			} else if(isset($object->Categories)){
				$_cats = makeArrayFromCSV($object->Categories);
			} else {
				$_cats = array();
			}
			$_new_cats = array();
			foreach($_cats as $_cat){
				$_ref = $this->RefTable->getRef(
					array(
						'OldID' => isset($object->$_cat) ? $object->$_cat : 0,
						'Table' => CATEGORY_TABLE
					)
				);
				if($_ref){
					$_new_cats[] = $_ref->ID;
				} else {
					$_new_cats[] = $_cat;
				}
			}
		}
	}

	private function updateField(&$object, $field, $table){

		$_ref = $this->RefTable->getRef(
			array(
				'OldID' => isset($object->$field) ? $object->$field : 0,
				'Table' => $table
			)
		);

		if($_ref){
			$object->$field = $_ref->ID;
		}
	}

	private function updateArray(&$array){
		foreach($array as $key => $value){
			// the condition is passed for key=0 ??!!??
			if(is_array($value)){
				$this->updateArray($array[$key]);
			} else if(($key == "id") || ($key == "img_id") || ($key == "obj_id")){
				$ref = $this->RefTable->getRef(
					array(
						"OldID" => $value,
						"Table" => (($key == "obj_id" && defined('OBJECT_FILES_TABLE')) ? OBJECT_FILES_TABLE : FILE_TABLE)
					)
				);
				if($ref){
					$array[$key] = $ref->ID;
				}
			}
		}
	}

	private function updateSource($patterns, &$source, $field = "ID", $table = FILE_TABLE){
		if(is_array($source)){ // shop exception - handle array in the content
			foreach($source as $_k1 => $_item1){
				if(!is_array($_item1)){
					continue;
				}
				foreach($_item1 as $_k2 => $_item2){
					if(!is_array($_item2)){
						continue;
					}
					foreach($_item2 as $_k3 => $_item3){
						if(in_array('bdid', array_keys($_item3)) && $_item3['bdid']){
							$ref = $this->RefTable->getRef(
								array(
									'OldID' => $_item3['bdid'],
									'Table' => $table
								)
							);
							if($ref){
								$source[$_k1][$_k2][$_k3]['bdid'] = $ref->ID;
							}
						}
					}
				}
			}
		} else {
			$match = array();
			foreach($patterns as $pattern){
				if(!preg_match_all($pattern, $source, $match)){
					continue;
				}
				foreach($match[2] as $k => $include){
					if(!is_numeric($include)){
						continue;
					}
					if($include == 0 && $table == NAVIGATION_TABLE){
						$_new_id = path_to_id($this->options['navigation_path'], NAVIGATION_TABLE);
						$source = str_replace($match[1][$k] . $match[2][$k] . $match[3][$k], $match[1][$k] . $_new_id . $match[3][$k], $source);
					} else {
						$ref = $this->RefTable->getRef(
							array(
								"Old" . $field => $include,
								"Table" => $table
							)
						);
						if($ref && isset($match[3][$k])){
							$source = str_replace($match[1][$k] . $match[2][$k] . $match[3][$k], $match[1][$k] . $ref->$field . $match[3][$k], $source);
						}
					}
				}
			}
		}
	}

}
