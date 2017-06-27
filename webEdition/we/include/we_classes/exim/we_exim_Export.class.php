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
class we_exim_Export extends we_exim_ExIm{
	var $db;
	var $prepare = true;

	protected $exportType;
	protected $exportProperties = [];
	protected $permittedContentTypes = [];

	const EXPORT_PRE_PROCESS = 'initExport';
	const EXPORT_PROCESS_NEXT = 'writeNextItem';
	const EXPORT_POST_PROCESS = 'finishExport';

	const SELECTIONTYPE_CLASSNAME = 'classname';
	const SELECTIONTYPE_DOCTYPE = 'doctype';
	const SELECTIONTYPE_DOCUMENT = 'document';
	const ENABLE_DOCUMENTS2CSV = true;

	function __construct(){
		parent::__construct();
		$this->db = new DB_WE;
	}

	public function getNextTask(){
		return !self::isInitialized() ? self::EXPORT_PRE_PROCESS :
			($this->RefTable->current !== $this->RefTable->getCount() ? self::EXPORT_PROCESS_NEXT : self::EXPORT_POST_PROCESS);
	}

	public function exportPreprocess(we_export_export $export){
		$this->initExporter($export);
		$this->prepareExport();
		$this->savePreserves();
	}

	public function exportPostprocess(){
		$this->fileComplete();
		self::unsetPreserves();
	}

	public function exportNext(){
		$all = $this->RefTable->getCount();
		$ref = $this->RefTable->getNext();
		$this->savePreserves();

		if(!$ref->ID || !$ref->ContentType || !$ref->Table){
			return ['success' => false];
		}

		$table = $this->db->escape($ref->Table);
		if($ref->ContentType === 'weBinary' || f('SELECT 1 FROM ' . $table . ' WHERE ID=' . intval($ref->ID), '', $this->db)){
			$current = $this->RefTable->current;
			$this->export($ref->ID, $ref->ContentType, $table);

			return ['success' => true, 'id' => $ref->ID, 'contentType' => $ref->ContentType, 'table' => $table, 'current' => $current, 'total' => $all];
		}

		return ['success' => false];
	}

	protected function initExporter(we_export_export $export){
		self::unsetPreserves();
		$finalDocs = [];
		$finalTempl = [];
		$finalObjs = [];
		$finalClasses = [];
		$handle_documents = 0;
		$handle_templates = 0;
		$handle_classes = 0;
		$handle_objects = 0;

		// FIXME: this may be obsolete when $xmlExIm->getSelectedItems() works correctly
		if($export->Selection === 'manual'){
			switch($export->ExportType){
				case we_exim_ExIm::TYPE_CSV: 
				case we_exim_ExIm::TYPE_XML:
					if($export->XMLTable === stripTblPrefix(FILE_TABLE)){
						$finalDocs = makeArrayFromCSV($export->selDocs);
						$handle_documents = 1;
					} else {
						$finalObjs = makeArrayFromCSV($export->selObjs);
						$handle_objects = 1;
					}
					$export->ExportDepth = 1;
					break;
				default:
					$finalDocs = makeArrayFromCSV($export->selDocs);
					$finalTempl = makeArrayFromCSV($export->selTempl);
					$finalObjs = makeArrayFromCSV($export->selObjs);
					$finalClasses = makeArrayFromCSV($export->selClasses);
					$handle_documents = 1;
					$handle_templates = 1;
					$handle_classes = 1;
					$handle_objects = 1;
			}
		}

		$this->getSelectedItems($export->Selection,
			$export->ExportType,
			$export->XMLTable,
			$export->SelectionType,
			$export->DocType,
			$export->ClassName,
			$export->Categorys,
			$export>Folder,
			$finalDocs,
			$finalTempl,
			$finalObjs,
			$finalClasses
		);

		$this->setOptions(['export_type' => $export->ExportType,
			'handle_def_templates' => $export->HandleDefTemplates,
			'handle_doctypes' => $export->HandleDoctypes,
			'handle_categorys' => $export->HandleCategorys,
			'handle_def_classes' => $export->HandleDefClasses,
			'handle_document_includes' => $export->HandleDocIncludes,
			'handle_document_linked' => $export->HandleDocLinked,
			'handle_object_includes' => $export->HandleObjIncludes,
			'handle_object_embeds' => $export->HandleObjEmbeds,
			'handle_class_defs' => $export->HandleDefClasses,
			'handle_owners' => $export->HandleOwners,
			'export_depth' => $export->ExportDepth,
			'handle_documents' => $handle_documents,
			'handle_templates' => $handle_templates,
			'handle_classes' => $handle_classes,
			'handle_objects' => $handle_objects,
			'handle_navigation' => $export->HandleNavigation,
			'handle_thumbnails' => $export->HandleThumbnails,
			'xml_cdata' => $export->XMLCdata,
			'xml_table' => $export->XMLTable,
			'csv_delimiter' => $export->CSVDelimiter,
			'csv_lineend' => $export->CSVLineend,
			'csv_enclose' => $export->CSVEnclose,
			'csv_fieldnames' => $export->CSVFieldnames
		]);

		$this->exportProperties = ['exportType' => $this->exportType,
			'fileName' => $export->Filename,
			'fileExtension' => $export->Extension,
			'file' => $export->ExportFilename
		];

		$this->RefTable->reset();
		$this->savePreserves();
	}

	public function prepareExport(array $ids = []){ // check access level of parent
		$this->prepare = false;
		$this->fileCreate();
		$this->RefTable->reset();

		if($this->options->handle_owners){
			we_base_file::save($this->exportProperties['file'], self::exportInfoMap($this->RefTable->Users), "ab");
		}
	}

	protected function fileCreate(){
		if(empty($this->exportProperties['file'])){
			return false;
		}

		if(file_exists($this->exportProperties['file'])){
			return unlink($this->exportProperties['file']);
		}

		return true;
	}

	protected function fileComplete(){
		// to be overridden
	}

	protected function setRefTable(&$rTable){
		$this->RefTable = $rTable;
	}

	protected function export($id, $ct, $table = ''){

		$export_binary = true; // FIXME: where is this set? <= is set by exporttype!
		//$compression = ''; // FIXME: where is this set and used?

		update_time_limit(0);
		$doc = we_exim_contentProvider::getInstance($ct, $id, $table);
		// add binary data separately to stay compatible with the new binary feature in v5.1
		if(isset($doc->ContentType) && (
				strpos($doc->ContentType, "image/") === 0 ||
				strpos($doc->ContentType, "application/") === 0 ||
				strpos($doc->ContentType, "video/") === 0)){
				//FIXME: this should be determined by document, not by type
			$doc->setElement('data', we_base_file::load($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $doc->Path));
		}

		$fh = fopen($this->exportProperties['file'], 'ab');
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
				$this->writeExportItem($doc, $fh, $attribute, false, true);
				break;
			case 'we_backup_binary':
			case 'weBinary'://FIXME remove
				if(!is_numeric($id)){
					$doc->Path = $doc->ID;
					$doc->ID = 0;
				}

				$this->writeExportItem($doc, $fh, [], true, true);
				break;
		}

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

	// FIXME: move this to some new class we_exim_contentProviderNonWE
	protected function document2nonWE($we_doc, $fh, $cdata = false){
		if($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT){
			$DB_WE = new DB_WE();

			$template_code = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c WHERE c.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND c.DID=' . intval($we_doc->TemplateID) . ' AND c.nHash=x\'' . md5("completeData") . '\'', '', $DB_WE);
			$tag_parser = new we_tag_tagParser($template_code);
			$tags = $tag_parser->getAllTags();
			$regs = $records = [];

			foreach($tags as $tag){
				if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){
					$tag_name = $regs[1];
					if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tag_name != "var")){
						$name = $regs[1];
						switch($tag_name){
							// tags with text content, links and hrefs
							case "input":
							case "textarea":
							case "href":
							case "link":
								$records[] = $name;
								break;
						}
					}
				}
			}

			$hrefs = [];
			$content = '';
			$tag_counter = 0;
			$usedFields = [];

			foreach($we_doc->elements as $k => $v){
				$tag_counter++;

				switch(isset($v['type']) ? $v['type'] : ''){
					case 'date': // is a date field
						$tag_name = self::correctTagname($k, 'date', $tag_counter);

						$value = $we_doc->elements[$k]['dat'] ?: ($we_doc->elements[$k]['bdid'] ?: 0); // FIXME: dates must not be stored in bdid!

						$content .= $this->formatOutput(abs($value), $tag_name, $this->exportType, 2, $cdata);
						$usedFields[] = $tag_name;

						// Remove tagname from array
						if(isset($records)){
							$records = $this->remove_from_check_array($records, $tag_name);
						}

						break;
					case 'txt':
						if(preg_match('|(.+)' . we_base_link::MAGIC_INFIX . '(.+)|', $k, $regs)){ // is a we:href field
							if(!in_array($regs[1], $hrefs)){
								$hrefs[] = $regs[1];

								if($we_doc->getElement($regs[1] . we_base_link::MAGIC_INT_LINK, 'dat', 0)){
									$intID = $we_doc->getElement($regs[1] . we_base_link::MAGIC_INT_LINK_ID, 'bdid');

									$tag_name = self::correctTagname($k, 'link', $tag_counter);
									$content .= $this->formatOutput(id_to_path($intID, FILE_TABLE, $DB_WE), $tag_name, $this->exportType, 2, $cdata);
									$usedFields[] = $tag_name;
									
									// Remove tagname from array
									if(isset($records)){
										$records = $this->remove_from_check_array($records, $tag_name);
									}
								} else {
									$tag_name = self::correctTagname($k, "link", $tag_counter);
									$content .= $this->formatOutput($we_doc->elements[$regs[1]]["dat"], $tag_name, $this->exportType, 2, $cdata);
									$usedFields[] = $tag_name;

									// Remove tagname from array
									if(isset($records)){
										$records = $this->remove_from_check_array($records, $tag_name);
									}
								}
							}
						} else if(substr($we_doc->elements[$k]["dat"], 0, 2) === "a:" && is_array(we_unserialize($we_doc->elements[$k]["dat"]))){ // is a we:link field
							$tag_name = self::correctTagname($k, "link", $tag_counter);
							$content .= $this->formatOutput(self::formatOutput($we_doc->getFieldByVal($we_doc->elements[$k]["dat"], "link"), '',"cdata"), $tag_name, $this->exportType, 2, $cdata);
							$usedFields[] = $tag_name;

							// Remove tagname from array
							if(isset($records)){
								$records = $this->remove_from_check_array($records, $tag_name);
							}
						} else { // is a normal text field
							$tag_name = self::correctTagname($k, 'text', $tag_counter);
							$content .= $this->formatOutput(we_document::parseInternalLinks($we_doc->elements[$k]['dat'], $we_doc->ParentID, ''), $tag_name, $this->exportType, 2, $cdata, $this->exportType === we_exim_ExIm::TYPE_XML);
							$usedFields[] = $tag_name;

							// Remove tagname from array
							if(isset($records)){
								$records = $this->remove_from_check_array($records, $tag_name);
							}
						}

						break;
				}
			}

			if(isset($records) && is_array($records)){
				foreach($records as $cur){
					$content .= $this->formatOutput('', $cur, $this->exportType, 2, $cdata, $this->exportType === we_exim_ExIm::TYPE_XML);
					$usedFields[] = $cur;
				}
			}

			$this->checkWriteFieldNames($usedFields, $fh);

			return fwrite($fh, $content);

		}

		return false;
	}

	// FIXME: move this to some new class we_exim_contentProviderNonWE
	protected function object2nonWE($we_obj, $fh, $cdata = false){
		$DB_WE = new DB_WE();
		$dv = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($we_obj->TableID), '', $DB_WE);
		$dv = we_unserialize($dv);
		if(!is_array($dv)){
			$dv = [];
		}

		$tableInfo_sorted = $we_obj->getSortedTableInfo($we_obj->TableID, true, $DB_WE);

		$fields = $regs = [];
		foreach($tableInfo_sorted as $cur){
			// bugfix 8141
			if(preg_match('/(.+?)_(.*)/', $cur['name'], $regs)){
				$fields[] = ['name' => $regs[2], "type" => $regs[1]];
			}
		}

		$usedFields = [];
		foreach($fields as $i => $field){
			switch($field['type']){
				case 'object':
				case 'img':
				case 'binary':
				case (in_array($this->exportType, [we_exim_ExIm::TYPE_CSV, we_exim_ExIm::TYPE_XML]) ? 'OF' : ''):
					continue;
				default:
					$realName = $field['type'] . '_' . $field['name'];
					$element = $we_obj->getElementByType($field['name'], $field["type"], (empty($dv[$realName]) ? [] : $dv[$realName]));

					$tag_name = self::correctTagname($field['name'], 'value', $i);
					$usedFields[] = $field['name'];
					$content .= $this->formatOutput(we_document::parseInternalLinks($element, 0, ''), $tag_name, we_exim_ExIm::TYPE_XML, 2, $cdata, (($field["type"] != "date") && ($field["type"] != "int") && ($field["type"] != "float")));
			}
		}

		$this->checkWriteFieldNames($usedFields, $fh);

		return fwrite($fh, $content);
	}
	
	protected function formatOutput($content){
		return $content; // to be overridden
	}

	protected function exportObjectFieldNames(){
		// to be overridden
	}

	protected function getSelectedItems($selection, $exportType, $xmlTable, $selectionType, $docType, $className, $categorys, $folder, &$finalDocs, &$finalTempl, &$finalObjs, &$finalClasses){
		$this->db = new DB_WE();
		switch($selection){
			case "manual":
				switch($exportType){
					case we_exim_ExIm::TYPE_CSV:
						/*
						$finalObjs = defined('OBJECT_FILES_TABLE') ? self::getIDs($finalObjs, OBJECT_FILES_TABLE) : "";
						break;
						 * 
						 */
					case we_exim_ExIm::TYPE_XML:
						switch($xmlTable){
							case stripTblPrefix(FILE_TABLE):
								$finalDocs = self::getIDs($finalDocs, FILE_TABLE);
								$finalObjs = [];
								break;
							default:
								$finalObjs = defined('OBJECT_FILES_TABLE') ? self::getIDs($finalObjs, OBJECT_FILES_TABLE) : "";
								$finalDocs = [];
								break;
						}
						break;
					case we_exim_ExIm::TYPE_WE:
					default:
						$finalDocs = array_unique(self::getIDs($finalDocs, FILE_TABLE, false));
						$finalTempl = array_unique(self::getIDs($finalTempl, TEMPLATES_TABLE, false));
						$finalObjs = defined('OBJECT_FILES_TABLE') ? array_unique(self::getIDs($finalObjs, OBJECT_FILES_TABLE, false)) : "";
						$finalClasses = defined('OBJECT_FILES_TABLE') ? array_unique(self::getIDs($finalClasses, OBJECT_TABLE, false)) : "";
				}
				break;
			case "auto":
			default:
				$doctype_where = '';

				switch($selectionType){
					case self::SELECTIONTYPE_CLASSNAME:
						if(defined('OBJECT_FILES_TABLE')){
							$where = we_exim_ExIm::queryForAllowed(OBJECT_FILES_TABLE);
							$cat_sql = ' ' . ($categorys ? ' AND ' . we_category::getCatSQLTail('', 'of', true, $this->db, 'Category', $categorys) : '');

							$this->db->query('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' of WHERE of.IsFolder=0 AND of.TableID=' . intval($className) . $cat_sql . $where);
							$finalObjs = $this->db->getAll(true);
						}
						break;
					case self::SELECTIONTYPE_DOCTYPE:
						$doctype_where = ' AND f.DocType="' . $this->db->escape($docType) . '"';
						/* fall through */
					case self::SELECTIONTYPE_DOCUMENT:
					case FILE_TABLE:
					default:
						$cat_sql = ($categorys ? ' AND ' . we_category::getCatSQLTail('', 'f', true, $this->db, 'Category', $categorys) : '');
						if(false && $folder != 0){
							$workspace = id_to_path($folder, FILE_TABLE, $this->db);
							$ws_where = ' AND (f.Path LIKE "' . $this->db->escape($workspace) . '/%" OR f.ID="' . $folder . '")';
						} else {
							$ws_where = '';
						}

						$this->db->query('SELECT distinct ID FROM ' . FILE_TABLE . ' f WHERE 1 ' . $ws_where . $doctype_where . $cat_sql);
						$finalDocs = $this->db->getAll(true);
						break;
				}
				
		}

		foreach($finalDocs as $v){
			$ct = f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($v), "", $this->db);

			if(empty($this->permittedContentTypes) || in_array($ct, $this->permittedContentTypes)){
				$this->RefTable->add2(["ID" => $v,
					"ContentType" => $ct,
					"level" => 0
					]
				);
			}
		}

		foreach($finalTempl as $v){
			if(empty($this->permittedContentTypes) || in_array(we_base_ContentTypes::TEMPLATE, $this->permittedContentTypes)){
				$this->RefTable->add2(["ID" => $v,
					"ContentType" => we_base_ContentTypes::TEMPLATE,
					"level" => 0
					]
				);
			}
		}

		if(is_array($finalObjs)){
			foreach($finalObjs as $v){
				if(empty($this->permittedContentTypes) || in_array(we_base_ContentTypes::OBJECT_FILE, $this->permittedContentTypes)){
					$this->RefTable->add2(["ID" => $v,
						"ContentType" => we_base_ContentTypes::OBJECT_FILE,
						"level" => 0
						]
					);
				}
			}
		}

		if(is_array($finalClasses)){
			if(empty($this->permittedContentTypes) || in_array(we_base_ContentTypes::OBJECT, $this->permittedContentTypes)){
				foreach($finalClasses as $v){
					$this->RefTable->add2(["ID" => $v,
						"ContentType" => we_base_ContentTypes::OBJECT,
						"level" => 0
					]);
				}
			}
		}
	}

	protected function remove_from_check_array($check_array){
		return $check_array; // to be overridden
	}

	protected function checkWriteFieldNames(){
		// to be overridden
	}

	protected static function getIDs($selIDs, $table, $with_dirs = false){
		$tmp = [];
		$db = new DB_WE();
		$allow = we_exim_ExIm::queryForAllowed($table);
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

	protected static function correctTagname($tagname, $alternative_name, $alternative_number = -1){
		if($tagname != ''){
			// Remove spaces + special characters
			$tagname = preg_replace(['/\40+/', '/[^a-zA-Z0-9_]+/'], ["_", ''], $tagname);
		}

		// Set alternative name if no name is now present present
		return ($tagname ? :
				(($alternative_number != -1) ? $alternative_name . $alternative_number : $alternative_name) );
	}

	protected static function exportInfoMap($info){
		$out = '<we:info>';
		foreach($info as $inf){
			$out .= '<we:map';
			foreach($inf as $key => $value){
				$out .= ' ' . $key . '="' . $value . '"';
			}
			$out .= '></we:map>';
		}
		$out .= '</we:info>' .
			we_backup_util::backupMarker . "\n";
		return $out;
	}

	protected static function isInitialized(){
		return !empty($_SESSION['weS']['ExImRefTable']);
	}

	// FIXME: make array instead of these single entries
	public function loadPreserves(){
		parent::loadPreserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			$this->prepare = $_SESSION['weS']['ExImPrepare'];
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			$this->options = $_SESSION['weS']['ExImOptions'];
		}
		if(isset($_SESSION['weS']['ExImExportType'])){
			$this->exportType = $_SESSION['weS']['ExImExportType'];
		}
		if(isset($_SESSION['weS']['ExImProperties'])){
			$this->exportProperties = $_SESSION['weS']['ExImProperties'];
		}
	}

	public function savePreserves(){
		parent::savePreserves();
		$_SESSION['weS']['ExImPrepare'] = $this->prepare;
		$_SESSION['weS']['ExImOptions'] = $this->options;
		$_SESSION['weS']['ExImExportType'] = $this->exportType;
		$_SESSION['weS']['ExImProperties'] = $this->exportProperties;

	}

	public static function unsetPreserves(){
		parent::unsetPreserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			unset($_SESSION['weS']['ExImPrepare']);
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			unset($_SESSION['weS']['ExImOptions']);
		}
		if(isset($_SESSION['weS']['ExImExportType'])){
			unset($_SESSION['weS']['ExImExportType']);
		}
		if(isset($_SESSION['weS']['ExImProperties'])){
			unset($_SESSION['weS']['ExImProperties']);
		}
	}

}
