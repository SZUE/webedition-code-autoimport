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
/* a class for handling directories */
class we_folder extends we_root{
	/* Flag which is set, when the file is a folder  */

	var $IsFolder = 1;
	var $IsClassFolder = 0;
	var $IsNotEditable = 0;
	var $WorkspacePath = '';
	var $WorkspaceID = '';
	var $Language = '';
	var $GreenOnly = 0;
	var $searchclassFolder;
	var $searchclassFolder_class;
	protected $urlMap;

	/**
	 * @var we_customer_documentFilter
	 */
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !!!!

	/* Constructor */

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'SearchStart', 'SearchField', 'Search', 'Order', 'GreenOnly', 'IsClassFolder', 'IsNotEditable', 'WorkspacePath', 'WorkspaceID', 'Language', 'TriggerID', 'searchclassFolder', 'searchclassFolder_class', 'urlMap');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO);
		}
		$this->Table = FILE_TABLE;
		$this->ContentType = 'folder';
		$this->Icon = we_base_ContentTypes::FOLDER_ICON;
	}

	public function we_new(){
		parent::we_new();
		$this->adjustEditPageNr();
	}

	function getPath(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){
			return we_root::getPath();
		} else {
			$ParentPath = $this->getParentPath();
			$ParentPath .= ($ParentPath != '/') ? '/' : '';
			return $ParentPath . $this->Text;
		}
	}

	function we_initSessDat($sessDat){
		we_root::we_initSessDat($sessDat);

		if($this->Table == FILE_TABLE || $this->Table == OBJECT_FILES_TABLE){
			if(!$this->Language){
				$this->initLanguageFromParent();
			}
			if(we_base_request::_(we_base_request::BOOL, 'we_edit_weDocumentCustomerFilter')){
				$this->documentCustomerFilter = we_customer_documentFilter::getCustomerFilterFromRequest($this->ID, $this->ContentType, $this->Table);
			} else if(isset($sessDat[3])){ // init webUser from session
				$this->documentCustomerFilter = unserialize($sessDat[3]);
			}
		}
		$this->adjustEditPageNr();

		if(isset($this->searchclassFolder_class) && !is_object($this->searchclassFolder_class)){
			$this->searchclassFolder_class = unserialize($this->searchclassFolder_class);
		}
		if(is_object($this->searchclassFolder_class)){
			$this->searchclassFolder = $this->searchclassFolder_class;
		} else {
			$this->searchclassFolder = new we_search_search();
			$this->searchclassFolder_class = serialize($this->searchclassFolder);
		}
		$this->searchclassFolder->initSearchData();
	}

	/**
	 * adjust EditPageNrs for CUSTOMERFILTER AND DOCLIST
	 */
	function adjustEditPageNr(){
		if(isWE()){
			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){

				if($this->Table == FILE_TABLE || $this->Table == OBJECT_FILES_TABLE){
					array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_WEBUSER);
				}
			}

			if($this->Table == FILE_TABLE){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_DOCLIST;
			}
		}
	}

	function initLanguageFromParent(){

		$ParentID = $this->ParentID;
		$i = 0;
		while($this->Language == ''){
			if($ParentID == 0 || $i > 20){
				we_loadLanguageConfig();
				$this->Language = $GLOBALS['weDefaultFrontendLanguage'];
				if($this->Language == ''){
					$this->Language = 'de_DE';
				}
			} else {
				$Query = 'SELECT Language, ParentID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID = ' . intval($ParentID);
				$this->DB_WE->query($Query);

				while($this->DB_WE->next_record()){
					$ParentID = $this->DB_WE->f('ParentID');
					$this->Language = $this->DB_WE->f('Language');
				}
			}
			$i++;
		}
	}

	function initByPath($path, $tblName = FILE_TABLE, $IsClassFolder = 0, $IsNotEditable = 0){
		if(substr($path, -1) == '/'){
			$path = substr($path, 0, strlen($path) - 1);
		}
		$id = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $this->DB_WE->escape($path) . '" AND IsFolder=1', '', $this->DB_WE);
		if($id != ''){
			$this->initByID($id, $tblName);
			if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
				$this->ClassName = 'we_class_folder';
			}
		} else {
			## Folder does not exist, so we have to create it (if user has permissons to create folders)

			$spl = explode('/', $path);
			$folderName = array_pop($spl);
			$p = array();
			$anz = count($spl);
			$last_pid = 0;
			for($i = 0; $i < $anz; $i++){
				array_push($p, array_shift($spl));
				$pa = implode('/', $p);
				if($pa){
					$pid = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $this->DB_WE->escape($pa) . '"', 'ID', $this->DB_WE);
					if(!$pid){
						if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
							$folder = new we_class_folder();
						} else {
							$folder = new we_folder();
						}
						$folder->we_new();
						$folder->Table = $tblName;
						$folder->ParentID = $last_pid;
						$folder->Text = $p[$i];
						$folder->Filename = $p[$i];
						$folder->IsClassFolder = $IsClassFolder;
						$folder->IsNotEditable = $IsClassFolder;
						$folder->Path = $pa;
						$folder->save();
						$last_pid = $folder->ID;
					} else {
						$last_pid = $pid;
					}
				}
			}
			$this->we_new();
			$this->Icon = $IsClassFolder ? we_base_ContentTypes::CLASS_FOLDER_ICON : we_base_ContentTypes::FOLDER_ICON;
			$this->Table = $tblName;
			$this->IsClassFolder = $IsClassFolder;
			$this->ParentID = $last_pid;
			$this->Text = $folderName;
			$this->Filename = $folderName;
			$this->Path = $path;
			$this->IsNotEditable = $IsNotEditable;
			$this->save();
		}
		return true;
	}

	function i_canSaveDirinDir(){
		if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
			if($this->Icon == '' && $this->ParentID == 0){
				return false;
			} else {
				if($this->ParentID != 0){
					$this->Icon = we_base_ContentTypes::FOLDER_ICON;
					$this->IsClassFolder = 0;
				}
			}

			if($this->ParentID != 0){
				$this->DB_WE->query('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsNotEditable=1');
				while($this->DB_WE->next_record()){
					if($this->DB_WE->f('ID') == $this->ParentID){
						return false;
					}
				}
			}
		}
		return true;
	}

	function i_sameAsParent(){
		if($this->ID){
			$db = new DB_WE();
			$pid = $this->ParentID;
			while($pid){
				if($this->ID == $pid){
					return true;
				}
				$pid = f('SELECT ParentID FROM ' . $db->escape($this->Table) . '  WHERE ID=' . intval($pid), 'ParentID', $db);
			}
		}
		return false;
	}

	/* saves the folder */

	public function we_save($resave = 0, $skipHook = 0){
		$this->i_setText();
		$objFolder = (defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE);
		if($objFolder){
			$this->ClassName = 'we_class_folder';
		}
		$update = $this->isMoved();
		if($update && !$objFolder){
			if(file_exists($this->OldPath) && file_exists($this->Path)){
				t_e('Both paths exists!', $this->OldPath, $this->Path);
				return false;
			}
			//leave old dir for parent save
			$tmp = $this->Path;
			$this->Path = $this->OldPath;
			if(!parent::we_save($resave)){
				return false;
			}
			//set back path, since we want to move the dir
			$this->Path = $tmp;
			if(!$this->writeFolder()){
				return false;
			}
		}

		if(!$update || $objFolder){
			if(!parent::we_save($resave) || !$this->writeFolder()){
				return false;
			}
		}
		$this->OldPath = $this->Path;
		if(defined('OBJECT_TABLE') && $this->Table == OBJECT_TABLE){
			$f = new we_class_folder();
			$f->initByPath($this->Path, OBJECT_FILES_TABLE, 0, 1);
		}
		$this->resaveWeDocumentCustomerFilter();

		if($resave == 0 && $update){
			//FIXME:improve!
			we_navigation_cache::clean(true);
		}
		if(LANGLINK_SUPPORT && ($langid = we_base_request::_(we_base_request::STRING, 'we_' . $this->Name . '_LanguageDocID'))){
			$this->setLanguageLink($langid, 'tblFile', true, ($this instanceof we_class_folder));
		} else {
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, true); //if language changed, we
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
		return true;
	}

	function changeLanguageRecursive(){

		$DB_WE = new DB_WE();
		$DB_WE2 = new DB_WE();
		$DB_WE3 = new DB_WE();

		$language = $this->Language;

		// Adapt tblLangLink-entries of documents and objects to the new language (all published and unpublished)

		if(!$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","objectFile")')){
			return false;
		}
		while($DB_WE->next_record()){
			if($DB_WE->Record['Language'] != $language){
				$documentTable = ($DB_WE->escape($this->Table) == FILE_TABLE) ? 'tblFile' : 'tblObjectFile';
				$existLangLinks = false;
				$deleteLangLinks = false;
				if($DB_WE2->query('SELECT LDID, Locale FROM ' . LANGLINK_TABLE . ' WHERE DID = ' . intval($DB_WE->Record['ID']) . ' AND DocumentTable = "' . $DB_WE2->escape($documentTable) . '"')){
					$ldidArray = array();
					while($DB_WE2->next_record()){
						$existLangLinks = true;
						$ldidArray[] = $DB_WE2->Record['LDID'];
						if($DB_WE2->Record['Locale'] == $language){
							$deleteLangLinks = true;
						}
					}
					if($existLangLinks){
						if($deleteLangLinks){
							$didCondition = 'DID = ' . intval($DB_WE->Record['ID']);
							foreach($ldidArray as $ldid){
								$didCondition .= ' OR DID = ' . intval($ldid);
							}
							$DB_WE3->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE (' . $didCondition . ')  AND DocumentTable = "' . $DB_WE3->escape($documentTable) . '"');
						} else {
							$DB_WE3->query('UPDATE ' . LANGLINK_TABLE . ' SET DLOCALE = "' . $DB_WE3->escape($language) . '" WHERE DID = ' . intval($DB_WE->Record['ID']));
							$DB_WE3->query('UPDATE ' . LANGLINK_TABLE . ' SET LOCALE = "' . $DB_WE3->escape($language) . '" WHERE LDID = ' . intval($DB_WE->Record['ID']));
						}
					}
				}
			}
		}

		// Adapt tblLangLink-entries of folders to the new language
		if(!$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType = "folder"')){
			return false;
		}
		while($DB_WE->next_record()){
			$documentTable = 'tblFile';
			$DB_WE2->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID = ' . intval($DB_WE->Record['ID']) . ' AND DocumentTable = "' . $DB_WE2->escape($documentTable) . '" AND IsFolder > 0 AND Locale = "' . $DB_WE2->escape($language) . '"');
			$DB_WE2->query('UPDATE ' . LANGLINK_TABLE . ' SET DLOCALE = "' . $DB_WE2->escape($language) . '" WHERE DID = ' . intval($DB_WE->Record['ID']) . ' AND DocumentTable = "' . $DB_WE2->escape($documentTable) . '" AND IsFolder > 0');
		}

		// Change language of published documents, objects
		if(!$DB_WE->query('UPDATE ' . $DB_WE->escape($this->Table) . ' SET Language = "' . $DB_WE->escape($this->Language) . '" WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ((Published = 0 AND ContentType = "folder") OR (Published > 0 AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","objectFile")))')){
			return false;
		}

		// Change Language of unpublished documents
		if(!$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","objectFile")')){
			return false;
		}
		while($DB_WE->next_record()){
			$DocumentObject = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID = ' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1', 'DocumentObject', $DB_WE2);
			if($DocumentObject != ''){
				$DocumentObject = unserialize($DocumentObject);
				$DocumentObject[0]['Language'] = $this->Language;
				$DocumentObject = serialize($DocumentObject);
				$DocumentObject = str_replace("'", "\'", $DocumentObject);

				if(!$DB_WE2->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocumentObject="' . $DB_WE->escape($DocumentObject) . '" WHERE DocumentID=' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1')){
					return false;
				}
			}
		}

		// Sprache auch bei den einzelnen Objekten aendern
		if($this->Table == OBJECT_FILES_TABLE){
			// Klasse feststellen
			$ClassPathArray = explode('/', $this->Path);
			$ClassPath = '/' . $ClassPathArray[1];
			$q = 'SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path = "' . $DB_WE->escape($ClassPath) . '"';
			$cid = $pid = f($q, 'ID', $DB_WE);
			$_obxTable = OBJECT_X_TABLE . $cid;

			if(!$DB_WE->query('UPDATE ' . $DB_WE->escape($_obxTable) . ' SET OF_Language = "' . $DB_WE->escape($this->Language) . '" WHERE OF_Path LIKE "' . $DB_WE->escape($this->Path) . '/%" ')){
				return false;
			}
		}

		return true;
	}

	function changeTriggerIDRecursive(){

		$DB_WE = new DB_WE();
		$DB_WE2 = new DB_WE();

		$language = $this->TriggerID;

		// Change TriggerID of published documents first
		if(!$DB_WE->query('UPDATE ' . $DB_WE->escape($this->Table) . ' SET TriggerID = ' . intval($this->TriggerID) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ((Published = 0 AND ContentType = "folder") OR (Published > 0 AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","objectFile")))')){
			return false;
		}
		// Change Language of unpublished documents

		if(!$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","objectFile")')){
			return false;
		}
		while($DB_WE->next_record()){
			$DocumentObject = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($DB_WE->f('ID')) . ' AND DocTable="' . stripTblPrefix($this->Table) . '" AND Active=1', '', $DB_WE2);
			if($DocumentObject != ''){
				$DocumentObject = unserialize($DocumentObject);
				$DocumentObject[0]['TriggerID'] = $this->TriggerID;
				$DocumentObject = str_replace("'", "\'", serialize($DocumentObject));

				if(!$DB_WE2->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocumentObject="' . $DB_WE->escape($DocumentObject) . '" WHERE DocumentID=' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1')){
					return false;
				}
			}
		}

		// TriggerID auch bei den einzelnen Objekten aendern
		if($this->Table == OBJECT_FILES_TABLE){
			// Klasse feststellen
			list(, $ClassPath) = explode('/', $this->Path);
			$cid = $pid = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path = "/' . $DB_WE->escape($ClassPath) . '"', 'ID', $DB_WE);
			$_obxTable = OBJECT_X_TABLE . $cid;

			if(!$DB_WE->query('UPDATE ' . $DB_WE->escape($_obxTable) . ' SET OF_TriggerID = ' . intval($this->TriggerID) . ' WHERE OF_Path LIKE "' . $DB_WE->escape($this->Path) . '/%" ')){
				return false;
			}
		}

		return true;
	}

	protected function i_setText(){
		$this->Text = ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? $this->Filename : $this->Text;
	}

	function i_filenameDouble(){
		return f('SELECT 1 FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE Path="' . $this->DB_WE->escape($this->Path) . '" AND ID != ' . intval($this->ID), '', $this->DB_WE);
	}

	function i_filenameEmpty(){
		$fn = ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? $this->Filename : $this->Text;
		return ($fn == '') ? true : false;
	}

	/* returns 0 because it is a directory */

	function getfilesize(){
		return 0;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_templates/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_templates/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_modules/customer/editor_weDocumentCustomerFilter.inc.php';
			case we_base_constants::WE_EDITPAGE_DOCLIST:
				return 'we_doclist/we_editor_doclist.inc.php';
			default:
				$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PROPERTIES;
				return 'we_templates/we_editor_properties.inc.php';
		}
	}

	function formPath(){
		$ws = get_ws($this->Table);
		if(intval($this->ParentID) == 0 && $ws){
			$wsa = makeArrayFromCSV($ws);
			$this->ParentID = $wsa[0];
			$this->ParentPath = id_to_path($this->ParentID, $this->Table, $this->DB_WE);
		}

		$userCanChange = permissionhandler::hasPerm('CHANGE_DOC_FOLDER_PATH') || ($this->CreatorID == $_SESSION['user']['ID']) || (!$this->ID);
		if($this->ID != 0 && $this->ParentID == 0 && $this->ParentPath == '/' && defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
			$userCanChange = false;
		}
		return (!$userCanChange ? '<table border="0" cellpadding="0" cellspacing="0"><tr><td><span class="defaultfont">' . $this->Path . '</span></td></tr>' :
				'<table border="0" cellpadding="0" cellspacing="0">
	<tr><td class="defaultfont">' . $this->formInputField('', ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? 'Filename' : 'Text', g_l('weClass', '[filename]'), 50, 388, 255, 'onchange=_EditorFrame.setEditorIsHot(true);pathOfDocumentChanged();') . '</td><td></td><td></td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 10) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3" class="defaultfont">' . $this->formDirChooser(388) . '</td></tr>' .
				(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE ?
					'	<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
		<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
		<tr><td colspan="3" class="defaultfont">' . $this->formTriggerDocument() . '</td></tr>
			<tr><td colspan="3">
		<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[grant_tid_expl]') . ($this->ID ? '' : g_l('weClass', '[availableAfterSave]')), we_html_tools::TYPE_INFO, 388, false) . '</td><td>' .
					we_html_button::create_button('ok', 'javascript:if(_EditorFrame.getEditorIsHot()) { ' . we_message_reporting::getShowMessageCall(g_l('weClass', "[saveFirstMessage]"), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeTriggerIDRecursive','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', ($this->ID ? false : true)) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table></td></tr>' :
					'') .
				($this->Table == FILE_TABLE && $this->ID && permissionhandler::hasPerm('ADMINISTRATOR') ? '
	<tr><td>' . we_html_tools::getPixel(20, 10) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td class="defaultfont">' . $this->formInputField('', 'urlMap', g_l('weClass', '[urlMap]'), 50, 388, 255, 'onchange=_EditorFrame.setEditorIsHot(true); ') . '</td><td></td><td></td></tr>
' : '')) .
			'</table>';
	}

	function formLanguage(){
		we_loadLanguageConfig();

		$value = ($this->Language ? $this->Language : $GLOBALS['weDefaultFrontendLanguage']);

		$inputName = 'we_' . $this->Name . '_Language';

		$_languages = getWeFrontendLanguagesForBackend();
		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			$isobject = (defined('OBJECT_FILES_TABLE') && ($this->Table == OBJECT_FILES_TABLE) ? 1 : 0);
			foreach($_languages as $langkey => $lang){
				$LDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblFile" AND IsObject=' . intval($isobject) . ' AND DID=' . intval($this->ID) . ' AND Locale="' . $this->DB_WE->escape($langkey) . '"', 'LDID', $this->DB_WE);
				if(!$LDID){
					$LDID = 0;
				}
				$divname = 'we_' . $this->Name . '_LanguageDocDiv[' . $langkey . ']';
				$htmlzw.= '<div id="' . $divname . '" ' . ($this->Language == $langkey ? ' style="display:none" ' : '') . '>' . $this->formLanguageDocument($lang, $langkey, $LDID) . '</div>';
				$langkeys[] = $langkey;
			}

			return
				'<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
				<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);", 'onchange' => "dieWerte='" . implode(',', $langkeys) . "';showhideLangLink('we_" . $this->Name . "_LanguageDocDiv',dieWerte,this.options[this.selectedIndex].value);_EditorFrame.setEditorIsHot(true);"), "value", 508) . '</td></tr>
				<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
				<tr><td class="defaultfont" align="left">' . g_l('weClass', '[languageLinksDir]') . '</td></tr>
			</table>' . we_html_element::htmlBr() . $htmlzw;
		} else {

			return '<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);", 'onchange' => "_EditorFrame.setEditorIsHot(true);"), "value", 388) . '</td></tr>
			</table>';
		}
	}

	function formChangeOwners(){
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('modules_users', "[grant_owners_expl]") . $_disabledNote, we_html_tools::TYPE_INFO, 388, false) . '</td><td>' .
			we_html_button::create_button('ok', 'javascript:if(_EditorFrame.getEditorIsHot()) { ' . we_message_reporting::getShowMessageCall(g_l('weClass', '[saveFirstMessage]'), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('users_changeR','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	function formChangeLanguage(){
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[grant_language_expl]") . $_disabledNote, we_html_tools::TYPE_INFO, 388, false) . '</td><td>' .
			we_html_button::create_button("ok", "javascript:if(_EditorFrame.getEditorIsHot()) { " . we_message_reporting::getShowMessageCall(g_l('weClass', "[saveFirstMessage]"), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeLanguageRecursive','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$parents = array(0, $this->ID);
		we_getParentIDs(FILE_TABLE, $this->ID, $parents);
		$ParentsCSV = makeCSVFromArray($parents, true);
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));
		$wecmdenc1 =we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("var parents = '" . $ParentsCSV . "';if(parents.indexOf(',' WE_PLUS currentID WE_PLUS ',') > -1){" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folder_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . "}else{opener.top.we_cmd('copyFolder', currentID," . $this->ID . ",1,'" . $this->Table . "');}");
		$but = we_html_button::create_button("select", ($this->ID ?
					"javascript:we_cmd('openDirselector', document.forms['we_form'].elements['" . $idname . "'].value, '" . $this->Table . "', '" . $wecmdenc1 . "', '', '" . $wecmdenc3 . "')" :
					"javascript:" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folders_no_id]'), we_message_reporting::WE_MESSAGE_ERROR))
				, true, 100, 22, "", "", !empty($_disabledNote));

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[copy_owners_expl]") . $_disabledNote, we_html_tools::TYPE_INFO, 388, false) . '</td><td>' .
			$this->htmlHidden($idname, $this->CopyID) . $but . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	################ internal functions ######

	function writeFolder($pub = 0){
		if($this->Path == $this->OldPath || !$this->OldPath){
			return $this->saveToServer();
		} else {
			if(!$this->moveAtServer()){
				return false;
			}
			$this->modifyIndexPath();
			$this->modifyLinks();
			$this->modifyChildrenPath();
		}
		$this->OldPath = $this->Path;
		return true;
	}

	function modifyIndexPath(){
		$this->DB_WE->query('UPDATE ' . INDEX_TABLE . ' SET Workspace="' . $this->DB_WE->escape($this->Path . substr($this->DB_WE->f('Workspace'), strlen($this->OldPath))) . '" WHERE Workspace LIKE "' . $this->DB_WE->escape($this->OldPath) . '%"');
	}

	function modifyLinks(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->Table) . ' SET Path=CONCAT("' . $this->DB_WE->escape($this->Path) . '",SUBSTRING(Path,' . (strlen($this->OldPath) + 1) . ')) WHERE Path LIKE "' . $this->DB_WE->escape($this->OldPath) . '/%" OR Path="' . $this->DB_WE->escape($this->OldPath) . '"');
		}
	}

	function modifyChildrenPath(){
		@ignore_user_abort(true);
		$DB_WE = new DB_WE();
		// Update Paths also in Doctype Table
		$DB_WE->query('UPDATE ' . DOC_TYPES_TABLE . ' SET ParentPath="' . $DB_WE->escape($this->Path) . '" WHERE ParentID=' . intval($this->ID));
		$DB_WE->query('SELECT ID,ClassName FROM ' . $DB_WE->escape($this->Table) . ' WHERE ParentID=' . intval($this->ID));
		while($DB_WE->next_record()){
			update_time_limit(30);
			$we_doc = $DB_WE->f('ClassName');
			if($we_doc){
				$we_doc = new $we_doc();
				$we_doc->initByID($DB_WE->f('ID'), $this->Table, we_class::LOAD_TEMP_DB); // BUG4397 - added LOAD_TEMP_DB to parameters
				$we_doc->ModifyPathInformation($this->ID);
			} else {
				t_e('No class set at entry ', $DB_WE->f('ID'), $this->Table);
			}
		}
		@ignore_user_abort(false);
	}

	/* for internal use */

	private function moveAtServer(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){

			// renames the folder on the local machine in the root-dir
			$path = $this->getRealPath();
			$oldPath = $this->getRealPath(true);
			if(!file_exists($path) && !file_exists($oldPath)){
				t_e('old path doesn\'t exist', $oldPath);
				return false;
			}
			if($this->Table != TEMPLATES_TABLE){
				// renames the folder on the local machine in the root-dir+site-dir
				$sitepath = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($this->Path, 1);
				$siteoldPath = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($this->OldPath, 1);
				if(file_exists($sitepath) && file_exists($siteoldPath)){
					t_e('old and new dir exists!', $sitepath, $siteoldPath);
					return false;
				}
				if(!file_exists($sitepath) && !file_exists($siteoldPath)){
					t_e('old directory doesn\'t exist!', $oldPath);
					return false;
				}
			}

			if(!file_exists($path) && file_exists($oldPath)){
				if(!rename($oldPath, $path)){
					return false;
				}
			}

			if($this->Table != TEMPLATES_TABLE){
				//we are responsible for site dir!
				if(!file_exists($sitepath) && file_exists($siteoldPath)){
					if(!rename($siteoldPath, $sitepath)){
						//move back other dir!
						rename($path, $oldPath);
						return false;
					}
				}
			}
		}
		return true;
	}

	/* for internal use */

	private function saveToServer(){
		$isTemplFolder = false;
		switch($this->Table){
			case TEMPLATES_TABLE:
				$isTemplFolder = true;
			case FILE_TABLE:
				$path = $this->getPath();

				// creates the folder on the local machine in the root-dir
				if(!we_util_File::createLocalFolder(($isTemplFolder ? TEMPLATES_PATH : $_SERVER['DOCUMENT_ROOT']), $path)){
					return false;
				}
				if(!$isTemplFolder && $this->urlMap){
					we_base_file::makeSymbolicLink(WEBEDITION_PATH, $_SERVER['DOCUMENT_ROOT'] . $path . rtrim(WEBEDITION_DIR, '/'));
					//make sure we have a symbolic dir
					we_base_file::checkAndMakeFolder($_SERVER['DOCUMENT_ROOT'] . WE_THUMBNAIL_DIRECTORY);
					if(WE_THUMBNAIL_DIRECTORY){
						we_base_file::makeSymbolicLink($_SERVER['DOCUMENT_ROOT'] . WE_THUMBNAIL_DIRECTORY, $_SERVER['DOCUMENT_ROOT'] . $path . WE_THUMBNAIL_DIRECTORY);
					}
				}

				// creates the folder on the local machine in the root-dir+site-dir
				if(!$isTemplFolder && !we_util_File::createLocalFolder($_SERVER['DOCUMENT_ROOT'] . SITE_DIR, $path)){
					return false;
				}
			default:
				return true;
		}
	}

	/**
	 * Beseitigt #Bug 3705: sorgt daf�r, das auch leere Dokumentenordner bei einem REbuild angelegt werden
	 */
	function we_rewrite(){
		if(parent::we_rewrite()){
			return ($this->Table == FILE_TABLE ? $this->we_save(1) : true);
		}
		return false;
	}

	/**
	 * @desc	the function modifies document EditPageNrs set
	 */
	function checkTabs(){

	}

	protected function updateRemoteLang($db, $id, $lang, $type){
		$oldLang = f('SELECT Language FROM ' . $db->escape($this->Table) . ' WHERE ID=' . intval($id), 'Language', $db);
		if($oldLang == $lang){
			return;
		}
		//update Lang of doc
		$db->query('UPDATE ' . $db->escape($this->Table) . ' SET Language="' . $db->escape($lang) . '" WHERE ID=' . intval($id));
		//update LangLink:
		$db->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $db->escape($lang) . '" WHERE DID=' . intval($id) . ' AND DocumentTable="' . $db->escape($type) . '"');
		//drop invalid entries => is this safe???
		$db->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $db->escape($type) . '" AND DLocale!="' . $db->escape($lang) . '"');
	}

	public static function getUrlReplacements(we_database_base $db, $onlyUrl = false){
		//TODO: cache this!
		static $ret = -1;
		if($ret == -1){
			$ret = array('full' => array(), 'url' => array());
			$db->query('SELECT Path,urlMap FROM ' . FILE_TABLE . ' WHERE urlMap!=""');
			while($db->next_record(MYSQL_NUM)){
				$host = trim(preg_replace('-(http://|https://)-', '', $db->f(1)), '/');
				$ret['full']['\1' . ($_SERVER['SERVER_NAME'] == $host ? '' : '//' . $host) . '\4'] = '-((href\s*=|src\s*=|action\s*=|location\s*=|url)\s*["\'\(])(' . preg_quote($db->f(0), '-') . ')(/[^"\'\)]*["\'\)])-';
				$ret['url'][($_SERVER['SERVER_NAME'] == $host ? '' : '//' . $host) . '\1'] = '-^' . preg_quote($db->f(0), '-') . '(/.*)-';
			}
		}
		return $ret[$onlyUrl ? 'url' : 'full'];
	}

}
