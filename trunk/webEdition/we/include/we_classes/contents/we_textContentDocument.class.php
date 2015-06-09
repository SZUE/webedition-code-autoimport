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
abstract class we_textContentDocument extends we_textDocument{
	/* Doc-Type of the document */
	public $DocType = '';

	/* these fields are never read from temporary tables */
	const primaryDBFiels = 'Path,Text,Filename,Extension,ParentID,Published,ModDate,CreatorID,ModifierID,Owners,RestrictOwners,WebUserID,Language';

	function __construct(){
		parent::__construct();

		$this->persistent_slots[] = 'DocType';
		$this->PublWhenSave = 0;
		$this->IsTextContentDoc = true;
		if(isWE()){
			if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
				array_push($this->persistent_slots, 'FromOk', 'ToOk', 'From', 'To');
			}
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_SCHEDULER);
		}
	}

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_SCHEDULER:
				return 'we_editors/we_editor_schedpro.inc.php';
			case we_base_constants::WE_EDITPAGE_VALIDATION:
				return 'we_templates/validateDocument.inc.php';
			default:
				return parent::editor();
		}
	}

	public function makeSameNew(){
		$Category = $this->Category;
		$ContentType = $this->ContentType;
		$DocType = $this->DocType;
		$IsSearchable = $this->IsSearchable;
		$Extension = $this->Extension;
		parent::makeSameNew();
		$this->DocType = $DocType;
		$this->changeDoctype();
		$this->Category = $Category;
		$this->ContentType = $ContentType;
		$this->IsSearchable = $IsSearchable;
		$this->Extension = $Extension;
	}

	public function insertAtIndex(array $only = null, array $fieldTypes = null){
		if(!($this->IsSearchable && $this->Published)){
			$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=0 AND ID=' . intval($this->ID));
			return true;
		}
		$text = '';

		if($only){
			foreach($only as $cur){
				$text .= ' ' . $this->getElement($cur);
			}
		} else {
			$this->resetElements();
			while((list($k, $v) = $this->nextElement('txt'))){
				$_dat = (isset($v['dat']) && is_string($v['dat']) && isset($v['dat']) ? $v['dat'] : '');
				if($k[0] === '$' || (isset($k[1]) && $k[1] === '$') || empty($_dat)){
					//skip elements whose names are variables or if element is empty
					continue;
				}

				if(isset($v['type']) && $v['type'] === 'txt' && !preg_match('|^a:\d:{|', $_dat)){
					$text .= ' ' . $_dat;
				}
			}
			//variants ar initialized, so nothing special to do
		}

		$maxDB = 65535; //min(1000000, $this->DB_WE->getMaxAllowedPacket() - 1024);
		return $this->DB_WE->query('REPLACE INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => intval($this->ID),
					'DID' => intval($this->ID),
					'Text' => substr(preg_replace(array('/(&#160;|&nbsp;)/', "/ *[\r\n]+/", '/  +/'), ' ', trim(strip_tags($text))), 0, $maxDB),
					'Workspace' => $this->ParentPath,
					'WorkspaceID' => intval($this->ParentID),
					'Category' => $this->Category,
					'Doctype' => $this->DocType,
					'Title' => $this->getElement('Title'),
					'Description' => $this->getElement('Description'),
					'Path' => $this->Path,
					'Language' => $this->Language
		)));
	}

	/* publish a document */

	function getMetas($code){
		$regs = array();
		$title = (preg_match('|< ?title[^>]*>(.*)< ?/ ?title[^>]*>|i', $code, $regs) ? $regs[1] : '');
		$tempname = we_base_file::saveTemp($code);
		$metas = get_meta_tags($tempname);
		unlink($tempname);
		$metas['title'] = $title;
		return $metas;
	}

	public function changeDoctype($dt = '', $force = false){
		if((!$this->ID) || $force){
			if($dt){
				$this->DocType = $dt;
			}
			if($this->DocType && ($rec = getHash('SELECT * FROM ' . DOC_TYPES_TABLE . ' WHERE ID =' . intval($this->DocType), new DB_WE()))){
				$this->Extension = $rec['Extension'];
				if($rec['ParentPath'] != ''){
					$this->ParentPath = $rec['ParentPath'];
					$this->ParentID = $rec['ParentID'];
				}
				if($this->ContentType == we_base_ContentTypes::WEDOCUMENT){
					// only switch template, when current template is not in Templates
					$_templates = explode(',', $rec['Templates']);
					if(!in_array($this->TemplateID, $_templates)){
						$this->setTemplateID($rec['TemplateID']);
					}
					$this->IsDynamic = $rec['IsDynamic'];
				}
				$this->IsSearchable = $rec['IsSearchable'];
				$this->Category = $rec['Category'];
				$this->Language = $rec['Language'];
				$_pathFirstPart = substr($this->ParentPath, -1) === '/' ? '' : '/';
				switch($rec['SubDir']){
					case self::SUB_DIR_YEAR:
						$this->ParentPath .= $_pathFirstPart . date('Y');
						break;
					case self::SUB_DIR_YEAR_MONTH:
						$this->ParentPath .= $_pathFirstPart . date('Y') . '/' . date('m');
						break;
					case self::SUB_DIR_YEAR_MONTH_DAY:
						$this->ParentPath .= $_pathFirstPart . date('Y') . '/' . date('m') . '/' . date('d');
						break;
				}
				$this->i_checkPathDiffAndCreate();
				$this->Text = $this->Filename . $this->Extension;

				// get Customerfilter of parent
				if(defined('CUSTOMER_TABLE') && isset($this->documentCustomerFilter)){
					$_tmpFolder = new we_folder();
					$_tmpFolder->initByID($this->ParentID, $this->Table);
					$this->documentCustomerFilter = $_tmpFolder->documentCustomerFilter;
					unset($_tmpFolder);
				}
			}
		}
	}

	function formDocType2($width = 300, $disable = false){
		if($disable){
			$name = ($this->DocType ? f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($this->DocType), 'DocType', $this->DB_WE) : g_l('weClass', '[nodoctype]'));
			return g_l('weClass', '[doctype]') . we_html_element::htmlBr() . $name;
		}
		$dtq = we_docTypes::getDoctypeQuery($this->DB_WE);
		return $this->formSelect2($width, 'dt.DocType', DOC_TYPES_TABLE, 'dt.ID', 'DocType dt LEFT JOIN tblFile dtf ON dt.ParentID=dtf.ID ' . $dtq['join'], g_l('weClass', '[doctype]'), $dtq['where'], 1, $this->DocType, false, (($this->DocType !== '') ?
					"if(confirm('" . g_l('weClass', '[doctype_changed_question]') . "')){we_cmd('doctype_changed');};" :
					"we_cmd('doctype_changed');") .
				"_EditorFrame.setEditorIsHot(true);", array(), 'left', "defaultfont", "", we_html_button::create_button("edit", "javascript:top.we_cmd('doctypes')", false, 0, 0, "", "", (!permissionhandler::hasPerm('EDIT_DOCTYPE'))), ((permissionhandler::hasPerm('NO_DOCTYPE') || ($this->ID && empty($this->DocType)) ) ) ? array('', g_l('weClass', '[nodoctype]')) : '');
	}

	function formDocTypeTempl(){
		return '
<table style="border-spacing: 0px;border-style:none" cellpadding="0">
	<tr><td class="defaultfont" align="left">' . $this->formDocType2(388, $this->Published) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 6) . '</td></tr>
	<tr><td>' . $this->formIsSearchable() . '</td></tr>
	<tr><td>' . $this->formInGlossar() . '</td></tr>
</table>';
	}

	public function we_new(){
		parent::we_new();
		$this->Filename = $this->i_getDefaultFilename();
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		switch($from){
			case we_class::LOAD_MAID_DB:
				parent::we_load($from);
				break;
			case we_class::LOAD_TEMP_DB:
				$sessDat = @unserialize(we_temporaryDocument::load($this->ID, $this->Table, $this->DB_WE));
				if($sessDat){
					$this->i_initSerializedDat($sessDat);
					$this->i_getPersistentSlotsFromDB(self::primaryDBFiels);
					$this->OldPath = $this->Path;
				} else {
					$this->we_load(we_class::LOAD_MAID_DB);
				}
				break;
			case we_class::LOAD_REVERT_DB: //we_temporaryDocument::revert gibst nicht mehr siehe #5789
				$this->we_load(we_class::LOAD_TEMP_DB);
				break;
			case we_class::LOAD_SCHEDULE_DB:
				if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
					$sessDat = f('SELECT SerializedData FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND ClassName="' . $this->DB_WE->escape($this->ClassName) . '" AND Was=' . we_schedpro::SCHEDULE_FROM, 'SerializedData', $this->DB_WE);
					if($sessDat &&
						$this->i_initSerializedDat(unserialize(substr_compare($sessDat, 'a:', 0, 2) == 0 ? $sessDat : gzuncompress($sessDat)))){
						$this->i_getPersistentSlotsFromDB(self::primaryDBFiels);
						$this->OldPath = $this->Path;

						break;
					}// take tmp db, when doc not in schedule db
				}
				$this->we_load(we_class::LOAD_TEMP_DB);

				break;
		}
		$this->OldPath = $this->Path;
		if(isWE()){//no need to load schedule data, if not in editmode
			$this->loadSchedule();
		}
		if($this->Category){ // Category-Fix!
			$this->Category = $this->i_fixCSVPrePost($this->Category);
		}
	}

	public function we_save($resave = 0, $skipHook = 0){
		$this->errMsg = '';
		$this->i_setText();
		if(!$skipHook){
			$hook = new weHook('preSave', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		if(!$this->ID && !we_root::we_save(0)){ // when no ID, then allways save before in main table
			return false;
		}
		if($resave == 0){
			$this->ModifierID = !isset($GLOBALS['we']['Scheduler_active']) && isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;
			$this->ModDate = time();
			$this->wasUpdate = true;
			we_history::insertIntoHistory($this);
			$this->resaveWeDocumentCustomerFilter();
		}

		/* version */
		$version = new we_versions_version();

		// allways store in temp-table
		$ret = $this->i_saveTmp(!$resave);
		$this->OldPath = $this->Path;

		if(($this->ContentType == we_base_ContentTypes::WEDOCUMENT && defined('VERSIONING_TEXT_WEBEDITION') && VERSIONING_TEXT_WEBEDITION) || ($this->ContentType == we_base_ContentTypes::HTML && defined('VERSIONING_TEXT_HTML') && VERSIONING_TEXT_HTML)){
			$version->save($this);
		}

		/* hook */
		if(!$skipHook){
			$hook = new weHook('save', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		return $ret;
	}

	public function we_publish($DoNotMark = false, $saveinMainDB = true, $skipHook = 0){
		if(!$skipHook){
			$hook = new weHook('prePublish', '', array($this));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		$this->oldCategory = f('SELECT Category FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->ID), '', $this->DB_WE);

		if($saveinMainDB && !we_root::we_save(1)){
			return false; // calls the root function, so the document will be saved in main-db but it will not be written!
		}

		$oldPublished = $this->Published;

		$this->Published = time();

		if(!$this->i_writeDocWhenPubl()){
			$this->Published = $oldPublished;
			return false;
		}

		if($DoNotMark == false){
			if(!$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->Table) . ' SET Published=' . intval($this->Published) . ' WHERE ID=' . intval($this->ID))){
				return false; // mark the document as published;
			}
		}

		//Bug #5505
//		if($_oldPublished == 0 || $this->isMoved() || $this->Category != $this->oldCategory || $oldDocType != $this->DocType){
		//FIXME: changes of customerFilter are missing here
		$this->rewriteNavigation();
		//	}
		if(isset($_SESSION['weS']['versions']['fromScheduler']) && $_SESSION['weS']['versions']['fromScheduler'] && (($this->ContentType == we_base_ContentTypes::WEDOCUMENT && defined('VERSIONING_TEXT_WEBEDITION') && VERSIONING_TEXT_WEBEDITION) || ($this->ContentType == we_base_ContentTypes::HTML && defined('VERSIONING_TEXT_HTML') && VERSIONING_TEXT_HTML))){
			$version = new we_versions_version();
			$version->save($this, 'published');
		}
		/* hook */
		if(!$skipHook){
			$hook = new weHook('publish', '', array($this, 'prePublishTime' => $oldPublished));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		if(we_temporaryDocument::isInTempDB($this->ID, $this->Table, $this->DB_WE)){
			we_temporaryDocument::delete($this->ID, $this->Table, $this->DB_WE);
		}
		return $this->insertAtIndex();
	}

	public function we_unpublish($skipHook = 0){
		if(!$this->ID || (file_exists($this->getRealPath(true)) && !we_base_file::deleteLocalFile($this->getRealPath(!$this->isMoved())))){
			return false;
		}
		if(!$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->Table) . ' SET Published=0 WHERE ID=' . intval($this->ID))){
			return false;
		}

		$this->Published = 0;
		$this->rewriteNavigation();

		/* version */
		if((VERSIONING_TEXT_WEBEDITION && $this->ContentType == we_base_ContentTypes::WEDOCUMENT ) || (VERSIONING_TEXT_HTML && $this->ContentType == we_base_ContentTypes::HTML)){
			$version = new we_versions_version();
			$version->save($this, 'unpublished');
		}
		/* hook */
		if(!$skipHook){
			$hook = new weHook('unpublish', '', array($this));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=0 AND ID=' . intval($this->ID));

		return true;
	}

	public function we_republish($rebuildMain = true){
		return ($this->Published ?
				$this->we_publish(true, $rebuildMain) :
				$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=0 AND ID=' . intval($this->ID))
			);
	}

	function we_resaveTemporaryTable(){
		$saveArr = array();
		$this->saveInSession($saveArr);
		if(($this->ModDate > $this->Published) && $this->Published){
			return (!we_temporaryDocument::isInTempDB($this->ID, $this->Table, $this->DB_WE) ?
					we_temporaryDocument::save($this->ID, $this->Table, $saveArr, $this->DB_WE) :
					we_temporaryDocument::resave($this->ID, $this->Table, $saveArr, $this->DB_WE)
				);
		}
		return true;
	}

	function ModifyPathInformation($parentID){
		$this->setParentID($parentID);
		$this->Path = $this->getPath();
		$this->wasUpdate = true;
		$this->i_savePersistentSlotsToDB('Filename,Extension,Text,Path,ParentID');
		$this->we_resaveTemporaryTable();
		$this->insertAtIndex();
		$this->modifyChildrenPath(); // only on folders, because on other classes this function is empty
	}

### private ####

	private function i_saveTmp($write = true){
		$saveArr = array();
		$this->saveInSession($saveArr);
		if(!we_temporaryDocument::save($this->ID, $this->Table, $saveArr, $this->DB_WE)){
			return false;
		}
		if(!$this->i_savePersistentSlotsToDB('Path,Text,Filename,Extension,ParentID,CreatorID,ModifierID,RestrictOwners,Owners,Published,ModDate,temp_template_id,temp_category,DocType,WebUserID')){
			return false;
		}
		return ($write ? $this->i_writeDocument() : true);
	}

	protected function i_writeMainDir($doc){
		return true; // do nothing!
	}

	private function i_writeDocWhenPubl(){
		if(!$this->ID){
			return false;
		}
		$realPath = $this->getRealPath();
		$parent = str_replace('\\', '/', dirname($realPath));
		$cf = array();
		while(!we_base_file::checkAndMakeFolder($parent, true)){
			$cf[] = $parent;
			$parent = str_replace('\\', '/', dirname($parent));
		}
		for($i = (count($cf) - 1); $i >= 0; $i--){
			we_base_file::createLocalFolder($cf[$i]);
		}
		$doc = $this->i_getDocumentToSave();
		return parent::i_writeMainDir($doc);
	}

	public function revert_published(){
		we_temporaryDocument::delete($this->ID, $this->Table, $this->DB_WE);
		$this->initByID($this->ID);
		$this->ModDate = $this->Published;
		$this->we_save();
		$this->we_publish();
		if(defined('WORKFLOW_TABLE') && $this->ContentType == we_base_ContentTypes::WEDOCUMENT){
			if(we_workflow_utility::inWorkflow($this->ID, $this->Table)){
				we_workflow_utility::removeDocFromWorkflow($this->ID, $this->Table, $_SESSION['user']['ID'], '');
			}
		}
	}

}
