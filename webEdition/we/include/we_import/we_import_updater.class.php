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
	private $processedPatterns = [];

	public function __construct(){
		parent::__construct();
		$this->Patterns = new we_exim_searchPatterns();
	}

	public function updateObject(/* we_document */ &$object){ //FIXME: imported types are not of type we_document
		if($this->debug){
			t_e('Updating object', $object->ID, (isset($object->Path) ? $object->Path : ''), (isset($object->Table) ? $object->Table : ''));
		}
		$this->processedPatterns = [];

		switch(isset($object->ClassName) ? $object->ClassName : ''){
			case 'we_textDocument'://JS, CSS
				$this->updateText($object);
				break;
			case "we_template":
				$this->updateTemplate($object);
				break;
			case "we_docTypes":
				$this->updateDocType($object);
				break;
			case 'we_object':
				// update class for embedded object
				if(is_array($object->SerializedArray) && preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+|', implode(',', array_keys($object->SerializedArray)))){
					$this->updateObjectModuleData($object);
				}
				break;
		}

		if($this->debug){
			t_e('Updating TemplateID property');
		}
		if(!empty($object->TemplateID)){
			$ref = $this->RefTable->getRef(
				['OldID' => $object->TemplateID,
					'ContentType' => we_base_ContentTypes::TEMPLATE
				]
			);
			if($ref){
				$object->TemplateID = $ref->ID;
			} else if(!empty($object->TemplatePath)){
				$ref = $this->RefTable->getRef(
					["ID" => $object->ID,
						"Table" => $object->Table
					]
				);
				if($ref && isset($ref->OldTemplatePath)){
					$tpath = we_base_file::clearPath(preg_replace('|^.+' . ltrim(TEMPLATES_DIR, '/') . '|i', '', $ref->OldTemplatePath));
					if(($id = path_to_id($tpath, TEMPLATES_TABLE, $GLOBALS['DB_WE']))){
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
				["OldID" => $object->DocType,
					"ContentType" => "doctype"
				]
			);
			if($ref){
				$object->DocType = $ref->ID;
			} else if(!empty($object->OldDocTypeName)){
				$object->DocType = intval(f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.DocType="' . $GLOBALS['DB_WE']->escape($object->OldDocTypeName) . '"'));
			}
		}

		if($this->debug){
			t_e('Updating Category property');
		}
		if(isset($object->Category) && $object->ClassName != "we_category"){
			$cats = makeArrayFromCSV($object->Category);
			$newcats = [];
			foreach($cats as $cat){
				$ref = $this->RefTable->getRef(
					["OldID" => $cat,
						"ContentType" => "category"
					]
				);
				if($ref){
					$newcats[] = $ref->ID;
				}
			}
			if($newcats){
				$object->Category = implode(',', $newcats);
			}
		}


		//update binary elements
		if(isset($object->elements)){
			$this->updateElements($object);
		}

		if(!empty($object->ContentType) && ($object->ContentType === we_base_ContentTypes::NAVIGATION || $object->ContentType === we_base_ContentTypes::NAVIGATIONRULE)){
			$this->updateNavigation($object);
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
		if(isset($object->ClassName) && $object->ClassName === "we_objectFile"){
			$regs = [];
			$del_elements = [];
			$new_elements = [];
			$new_defs = [];
			$del_defs = [];
		}

		foreach($object->elements as $k => &$element){
			if($this->debug){
				t_e("Updating object element ", $k);
			}

			if(strpos($k, 'intID') !== false || strpos($k, 'LinkID') !== false || strpos($k, 'RollOverID') !== false){
				if(isset($element['dat'])){
					$ref = $this->RefTable->getRef(
						['OldID' => $element['dat'],
							'Table' => FILE_TABLE
						]
					);
					$element['dat'] = ($ref ? $ref->ID : 0);
				}
			}

			if(isset($element["bdid"])){
				$ref = $this->RefTable->getRef(
					['OldID' => $element['bdid'],
						'Table' => FILE_TABLE
					]
				);

				$element['bdid'] = ($ref ? $ref->ID : 0);
			}

			switch(isset($object->ClassName) ? $object->ClassName : ''){
				case "we_objectFile":
					if(preg_match('|we_object_([0-9])+_path|', $k, $regs)){
						$ref = $this->RefTable->getRef(
							['OldID' => $regs[1],
								'Table' => OBJECT_TABLE
							]
						);
						if($ref){
							//$classid = $ref->ID;
							$objid = $object->elements['we_object_' . $regs[1]]['dat'];
							$objpath = $object->elements['we_object_' . $regs[1] . '_path']['dat'];
							$objref = $this->RefTable->getRef(
								['OldID' => $objid,
									'Table' => OBJECT_FILES_TABLE
								]
							);
							if($objref){
								$objid = $objref->ID;
								$objpath = $objref->Path;
							} else {
								$objid = path_to_id($objpath, OBJECT_FILES_TABLE, $GLOBALS['DB_WE']);
							}
							if($objid){
								$del_elements[] = $regs[1];
								$del_elements[] = 'we_object_' . $regs[1];
								$del_elements[] = 'we_object_' . $regs[1] . '_path';
								$new_elements[$ref->ID] = ['type' => 'object', 'len' => 22];
								$new_elements['we_object_' . $ref->ID] = ['type' => 'object', 'len' => 22, 'dat' => $objid];
								$new_elements['we_object_' . $ref->ID . '_path'] = ['type' => 'object', 'len' => 22, 'dat' => $objpath];

								if(isset($object->DefArray[we_object::QUERY_PREFIX . $regs[1]])){
									$del_defs[] = we_object::QUERY_PREFIX . $regs[1];
									$new_defs[we_object::QUERY_PREFIX . $ref->ID] = $object->DefArray[we_object::QUERY_PREFIX . $regs[1]];
								}
							}
						}
					}

					switch($element['type']){
						case 'img':
						case 'binary'://FIXME: do we still have this field?
							$objref = $this->RefTable->getRef(
								['OldID' => isset($element['bdid']) ? $element['bdid'] : $element['dat'],
									'Table' => FILE_TABLE
								]
							);

							if($objref){
								$element['bdid'] = $objref->ID;
							}
							break;
					}
					break;

				case 'we_object':
					if(preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+([a-zA-Z]*[0-9]*)|', $k, $regs)){
						if(count($regs) > 2 && isset($object->elements[we_object::QUERY_PREFIX . $regs[1] . $regs[2]])){
							$ref = $this->RefTable->getRef(
								['OldID' => $regs[1],
									'Table' => OBJECT_TABLE
								]
							);
							if($ref){
								$object->elements[we_object::QUERY_PREFIX . $ref->ID . $regs[2]] = array_merge_recursive($object->elements[we_object::QUERY_PREFIX . $regs[1] . $regs[2]]);
								unset($object->elements[we_object::QUERY_PREFIX . $regs[1] . $regs[2]]);
							}
						}
					}
					break;
			}
		}
		unset($element);

		// update object for embedded object
		if(isset($new_elements) && !empty($new_elements)){
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
					t_e('Updating webEdition and html documents for external links');
				}
				$source = $object->getElement('data');
				$this->updateSource($this->Patterns->ext_patterns, $source, 'Path', FILE_TABLE);
				$object->setElement("data", $source);
			}
		}

		// update elements serialized data
		if(!$object->isBinary()){
			if($this->debug){
				t_e('Updating serialized data in elements');
			}
			foreach($object->elements as $ek => $ev){
				if($this->debug){
					debug2($ev);
				}
				if(isset($ev["dat"])){
					$dat = we_unserialize($ev['dat'], '');
					if(is_array($dat)){
						$this->updateArray($dat);
						$object->elements[$ek]['dat'] = we_serialize($dat);
					} elseif(isset($object->ContentType)){
						switch($object->ContentType){
							case we_base_ContentTypes::WEDOCUMENT:
							case we_base_ContentTypes::HTML:
								$source = $ev['dat'];
								$this->updateSource($this->Patterns->wysiwyg_patterns['doc'], $source, 'ID', FILE_TABLE);
								$this->updateSource($this->Patterns->wysiwyg_patterns['obj'], $source, 'ID', OBJECT_FILES_TABLE);
								$object->elements[$ek]['dat'] = $source;
						}
					}
				}
			}
		}
	}

	private function updateText(we_textDocument &$object){
		if($this->debug){
			t_e("Updating text-doc source...\n");
		}

		$source = $object->getElement("data");
		$this->updateSource(array("/(#WE:)(\d+)(#)/se"), $source, 'ID', FILE_TABLE);
		$object->setElement("data", $source);
	}

	private function updateTemplate(we_template &$object){
		if($this->debug){
			t_e("Updating template source...\n");
		}
		if(!empty($object->MasterTemplateID)){
			$ref = $this->RefTable->getRef(
				['OldID' => $object->MasterTemplateID,
					'ContentType' => we_base_ContentTypes::TEMPLATE
					]
			);
			if($ref){
				$object->MasterTemplateID = $ref->ID;
				$object->_updateCompleteCode(true);
			}
		}
		$source = $object->getElement("data");

		$this->updateSource($this->Patterns->doc_patterns['id'], $source, 'ID', FILE_TABLE);
		$this->updateSource($this->Patterns->doc_patterns['path'], $source, 'Path', FILE_TABLE);
		if(defined('OBJECT_TABLE')){
			$this->updateSource($this->Patterns->obj_patterns['id'], $source, 'ID', OBJECT_FILES_TABLE);
			$this->updateSource($this->Patterns->doc_patterns['path'], $source, 'Path', OBJECT_FILES_TABLE);
			$this->updateSource($this->Patterns->class_patterns, $source, 'ID', OBJECT_TABLE);
		}

		$this->updateSource($this->Patterns->navigation_patterns, $source, 'ID', NAVIGATION_TABLE);

		$this->updateSource($this->Patterns->tmpl_patterns, $source, 'ID', TEMPLATES_TABLE);

		// must be at the end
		$this->updateSource($this->Patterns->special_patterns, $source, 'ID', FILE_TABLE);

		$object->setElement('data', $source);
	}

	private function updateObjectModuleData(we_object &$object){
		if($this->debug){
			t_e("Updating object module data...\n");
		}
		$new = $del = $regs = [];
		foreach($object->SerializedArray as $elkey => $elvalue){
			if(preg_match('|' . we_object::QUERY_PREFIX . '([0-9])+|', $elkey, $regs)){
				if(count($regs) > 1){
					$ref = $this->RefTable->getRef(
						['OldID' => $regs[1],
							'Table' => OBJECT_TABLE
							]
					);
					if($ref){
						$new[we_object::QUERY_PREFIX . $ref->ID] = array_merge_recursive($elvalue);
					}
					$del[] = $elkey;
				}
			}
		}
		foreach($del as $d){
			unset($object->SerializedArray[$d]);
		}
		$object->SerializedArray = array_merge($object->SerializedArray, $new);
		$object->DefaultValues = we_serialize($object->SerializedArray, SERIALIZE_JSON);
	}

	private function updateDocType(we_docTypes &$object){
		if($this->debug){
			t_e("Updating doctype object...\n");
		}
		// quick fix for fsw
		if(!empty($object->ParentPath)){
			$new_id = path_to_id($object->ParentPath, FILE_TABLE, $GLOBALS['DB_WE']);
			if($new_id){
				$object->ParentID = $new_id;
			} else {
				$object->ParentID = 0;
				$object->ParentPath = '/';
			}
		}

		if(!empty($object->Templates)){
			$tids = makeArrayFromCSV($object->Templates);
			$new_tids = [];
			foreach($tids as $tid){
				$ref = $this->RefTable->getRef(
					['OldID' => $tid,
						'Table' => TEMPLATES_TABLE
						]
				);
				if($ref){
					$new_tids[] = $ref->ID;
				}
			}
			$object->Templates = implode(',', $new_tids);
		}
	}

	private function updateNavigation(&$object){
		switch($object->ContentType){
			case we_base_ContentTypes::NAVIGATION:
				if($this->debug){
					t_e("Updating navigation...\n");
				}
				if($object->IsFolder){
					$this->updateField($object, 'LinkID', FILE_TABLE);
				}
				switch(isset($object->Selection) ? $object->Selection : ''){
					case we_navigation_navigation::SELECTION_DYNAMIC:
						switch($object->DynamicSelection){
							case we_navigation_navigation::DYN_DOCTYPE:
								$this->updateField($object, 'DocTypeID', DOC_TYPES_TABLE);
								$this->updateField($object, 'FolderID', FILE_TABLE);
								break;

							case we_navigation_navigation::DYN_CLASS:
								if(defined('OBJECT_TABLE')){
									$this->updateField($object, 'ClassID', OBJECT_TABLE);
									$this->updateField($object, 'FolderID', OBJECT_FILES_TABLE);
									$this->updateField($object, 'WorkspaceID', OBJECT_FILES_TABLE);
								}
								break;

							case we_navigation_navigation::DYN_CATEGORY:
								$this->updateField($object, 'FolderID', CATEGORY_TABLE);
								if($object->LinkSelection === we_navigation_navigation::LSELECTION_INTERN){
									$this->updateField($object, 'UrlID', FILE_TABLE);
								}
								break;
						}
						break;
				}
				switch($object->SelectionType){
					case we_navigation_navigation::STYPE_DOCLINK:
						$this->updateField($object, 'LinkID', FILE_TABLE);
						break;
					case we_navigation_navigation::STYPE_OBJLINK:
						$this->updateField($object, 'LinkID', OBJECT_FILES_TABLE);
						break;
					case we_navigation_navigation::STYPE_CATLINK:
						$this->updateField($object, 'LinkID', CATEGORY_TABLE);
						if($object->LinkSelection === we_navigation_navigation::LSELECTION_INTERN){
							$this->updateField($object, 'UrlID', FILE_TABLE);
						}
						break;
				}
				break;

			case we_base_ContentTypes::NAVIGATIONRULE:
				$this->updateField($object, 'NavigationID', NAVIGATION_TABLE);
				$this->updateField($object, 'DoctypeID', DOC_TYPES_TABLE);

				if($object->SelectionType === 'classname'){
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

		$cats = (isset($object->Categories) ?
			(is_array($object->Categories) ? $object->Categories : makeArrayFromCSV($object->Categories)) :
			[]);

		$new_cats = [];
		foreach($cats as $cat){
			$ref = $this->RefTable->getRef(
				['OldID' => isset($object->$cat) ? $object->$cat : 0,
					'Table' => CATEGORY_TABLE
					]
			);
			$new_cats[] = ($ref ? $ref->ID : $cat);
		}
	}

	private function updateField(&$object, $field, $table){

		$ref = $this->RefTable->getRef(
			['OldID' => isset($object->$field) ? $object->$field : 0,
				'Table' => $table
				]
		);

		if($ref){
			$object->$field = $ref->ID;
		}
	}

	private function updateArray(array &$array){
		foreach($array as $key => $value){
			// the condition is passed for key=0 ??!!??
			if(is_array($value)){
				$this->updateArray($array[$key]);
			} else if(($key === "id") || ($key === "img_id") || ($key === "obj_id")){
				$ref = $this->RefTable->getRef(
					["OldID" => $value,
						"Table" => (($key === "obj_id" && defined('OBJECT_FILES_TABLE')) ? OBJECT_FILES_TABLE : FILE_TABLE)
						]
				);
				if($ref){
					$array[$key] = $ref->ID;
				}
			}
		}
	}

	private function updateSource(array $patterns, &$source, $field = "ID", $table = FILE_TABLE){
		if(is_array($source)){ // shop exception - handle array in the content
			foreach($source as $k1 => $item1){
				if(!is_array($item1)){
					continue;
				}
				foreach($item1 as $k2 => $item2){
					if(!is_array($item2)){
						continue;
					}
					foreach($item2 as $k3 => $item3){
						if(in_array('bdid', array_keys($item3)) && $item3['bdid']){
							$ref = $this->RefTable->getRef(
								['OldID' => $item3['bdid'],
									'Table' => $table
									]
							);
							if($ref){
								$source[$k1][$k2][$k3]['bdid'] = $ref->ID;
							}
						}
					}
				}
			}
			return;
		}
		$match = [];
		foreach($patterns as $pattern){
			if(!preg_match_all($pattern, $source, $match)){
				continue;
			}
			foreach($match[2] as $k => $include){
				if(!is_numeric($include) || in_array($match[0][$k], $this->processedPatterns)){
					continue;
				}
				if($include == 0 && $table == NAVIGATION_TABLE){
					$new_id = path_to_id($this->options['navigation_path'], NAVIGATION_TABLE, $GLOBALS['DB_WE']);
					$source = str_replace($match[1][$k] . $match[2][$k] . $match[3][$k], $match[1][$k] . $new_id . $match[3][$k], $source);
				} else {
					$ref = $this->RefTable->getRef(
						['Old' . $field => $include,
							'Table' => $table
							]
					);
					if(isset($match[3][$k])){
						$this->processedPatterns[] = $match[0][$k];
						if($ref){
							$repl = $match[1][$k] . $ref->$field . $match[3][$k];
							$source = str_replace($match[1][$k] . $match[2][$k] . $match[3][$k], $repl, $source);
							$this->processedPatterns[] = '<' . $repl . '>';
						} else {
							//t_e('ref not found', $field, $include, $table, $match[1][$k] . $match[2][$k] . $match[3][$k], $this->processedPatterns, $this->RefTable);
						}
					}
				}
			}
		}
	}

}
