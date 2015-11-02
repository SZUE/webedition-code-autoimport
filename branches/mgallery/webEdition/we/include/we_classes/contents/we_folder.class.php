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
	var $WorkspacePath = '';
	var $WorkspaceID = '';
	var $Language = '';
	var $GreenOnly = 0;
	var $searchclassFolder;
	var $searchclassFolder_class;
	//folders are always published
	public $Published = PHP_INT_MAX;
	protected $urlMap;

	/**
	 * @var we_customer_documentFilter
	 */
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !

	/* Constructor */

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'SearchStart', 'SearchField', 'Search', 'Order', 'GreenOnly', 'IsClassFolder', 'WorkspacePath', 'WorkspaceID', 'Language', 'TriggerID', 'searchclassFolder', 'searchclassFolder_class', 'urlMap');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO);
		}
		$this->Table = FILE_TABLE;
		$this->ContentType = we_base_ContentTypes::FOLDER;
	}

	public function we_new($table = '', $parentID = 0, $name = ''){
		if($table){
			$this->Table = $table;
			$this->ParentID = $parentID;
			$this->Filename = $name;
			$this->Text = $name;
			$this->Path = $this->getPath();
			$this->Published = time();
		}
		parent::we_new();
		$this->adjustEditPageNr();
	}

	function getPath(){
		switch($this->Table){
			case FILE_TABLE:
			case TEMPLATES_TABLE:
				return parent::getPath();
			default:
				return rtrim($this->getParentPath(), '/') . '/' . $this->Text;
		}
	}

	public function we_initSessDat($sessDat){
		we_root::we_initSessDat($sessDat);

		if($this->Table == FILE_TABLE || $this->Table == OBJECT_FILES_TABLE){
			if(!$this->Language){
				$this->initLanguageFromParent();
			}
			if(we_base_request::_(we_base_request::BOOL, 'we_edit_weDocumentCustomerFilter')){
				$this->documentCustomerFilter = we_customer_documentFilter::getCustomerFilterFromRequest($this->ID, $this->ContentType, $this->Table);
			} else if(isset($sessDat[3])){ // init webUser from session - only for old temporary documents
				$this->documentCustomerFilter = we_unserialize($sessDat[3]);
			}
		}
		$this->adjustEditPageNr();

		if(isset($this->searchclassFolder_class) && !is_object($this->searchclassFolder_class)){
			$this->searchclassFolder_class = we_unserialize($this->searchclassFolder_class);
		}
		if(is_object($this->searchclassFolder_class)){
			$this->searchclassFolder = $this->searchclassFolder_class;
		} else {
			$this->searchclassFolder = new we_search_search();
			$this->searchclassFolder_class = we_serialize($this->searchclassFolder);
		}
		$this->searchclassFolder->initSearchData();
	}

	/**
	 * adjust EditPageNrs for CUSTOMERFILTER AND DOCLIST
	 */
	function adjustEditPageNr(){
		if(!isWE()){
			return;
		}
		if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
			if($this->Table == FILE_TABLE || $this->Table == OBJECT_FILES_TABLE){
				array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_WEBUSER);
			}
		}

		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){
			$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_DOCLIST;
		}
	}

	private function initLanguageFromParent(){
		$ParentID = $this->ParentID;
		$i = 0;
		while(!$this->Language){
			if($ParentID == 0 || $i > 20){
				$this->Language = $GLOBALS['weDefaultFrontendLanguage'];
				if(!$this->Language){
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

	public function initByPath($path, $tblName = FILE_TABLE){
		$path = rtrim($path, '/');
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
			while($spl){
				array_push($p, array_shift($spl));
				$pa = implode('/', $p);
				if($pa){
					if(($pid = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $this->DB_WE->escape($pa) . '"', '', $this->DB_WE))){
						$last_pid = $pid;
					} else {
						$folder = (defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE ?
										new we_class_folder() : new self());

						$folder->we_new($tblName, $last_pid, end($p));
						$folder->IsClassFolder = $last_pid == 0;
						$folder->save();
						$last_pid = $folder->ID;
					}
				}
			}
			$this->we_new($tblName, $last_pid, $folderName);
			$this->IsClassFolder = $last_pid == 0;
			$this->save();
		}
		return true;
	}

	protected function i_sameAsParent(){
		if($this->ID){
			$db = new DB_WE();
			$pid = $this->ParentID;
			while($pid){
				if($this->ID == $pid){
					return true;
				}
				$pid = f('SELECT ParentID FROM ' . $db->escape($this->Table) . '  WHERE ID=' . intval($pid), '', $db);
			}
		}
		return false;
	}

	/* saves the folder */

	public function we_save($resave = false, $skipHook = false){
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
		}
		if(!$this->writeFolder()){
			return false;
		}

		if(!$update || $objFolder){
			if(!parent::we_save($resave) || !$this->writeFolder()){
				return false;
			}
		}
		$this->OldPath = $this->Path;
		if(defined('OBJECT_TABLE') && $this->Table == OBJECT_TABLE){
			$f = new we_class_folder();
			$f->initByPath($this->Path, OBJECT_FILES_TABLE, true);
		}
		$this->resaveWeDocumentCustomerFilter();

		if(!$resave && $update){
			//FIXME:improve!
			we_navigation_cache::clean(true);
		}

		if(LANGLINK_SUPPORT && in_array($this->Table, array(FILE_TABLE, OBJECT_FILES_TABLE))){
			$this->setLanguageLink($this->LangLinks, 'tblFile', true, ($this instanceof we_class_folder));
		} else {
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, true); //if language changed, we
		}
		/* hook */
		if(!$skipHook){
			$hook = new weHook('save', '', array($this, 'resave' => $resave));
			//check if doc should be saved
			if($hook->executeHook() === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		return true;
	}

	function changeLanguageRecursive(){
		$DB_WE = new DB_WE();

		$language = $this->Language;
		$documentTable = ($this->Table == FILE_TABLE) ? 'tblFile' : 'tblObjectFile';

		// Adapt tblLangLink-entries of documents and objects to the new language (all published and unpublished)

		$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '") AND Language!="' . $DB_WE->escape($language) . '"');
		$docIds = $DB_WE->getAll(true);
		foreach($docIds as $id){
			$deleteLangLinks = f('SELECT 1 FROM ' . LANGLINK_TABLE . ' WHERE DID=' . $id . ' AND DocumentTable="' . $DB_WE->escape($documentTable) . '" AND Locale="' . $DB_WE->escape($language) . '"LIMIT 1', '', $DB_WE);
			$DB_WE->query('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DID=' . $id . ' AND DocumentTable="' . $DB_WE->escape($documentTable) . '"');
			$ldidArray = $DB_WE->getAll(true);

			if($ldidArray){
				if($deleteLangLinks){
					$ldidArray[] = $id;
					$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID IN(' . implode(',', $ldidArray) . ')  AND DocumentTable="' . $DB_WE->escape($documentTable) . '"');
				} else {
					$DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $DB_WE->escape($language) . '" WHERE DID=' . $id);
					$DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET Locale="' . $DB_WE->escape($language) . '" WHERE LDID=' . $id);
				}
			}
		}

		// Adapt tblLangLink-entries of folders to the new language
		$ids = implode(',', $DB_WE->getAllq('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType="folder"', true));
		if($ids){
			$DB_WE->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID IN(' . $ids . ') AND DocumentTable="' . $DB_WE->escape($documentTable) . '" AND IsFolder=1 AND Locale="' . $DB_WE->escape($language) . '"');
			$DB_WE->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $DB_WE->escape($language) . '" WHERE DID IN(' . $ids . ') AND DocumentTable="' . $DB_WE->escape($documentTable) . '" AND IsFolder=1');
		}

		// Change language of published documents, objects
		$DB_WE->query('UPDATE ' . $DB_WE->escape($this->Table) . ' SET Language="' . $DB_WE->escape($this->Language) . '" WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ((Published=0 AND ContentType="folder") OR (Published!=0 AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '")))');


		// Sprache auch bei den einzelnen Objekten aendern
		if($this->Table == OBJECT_FILES_TABLE){
			// Klasse feststellen
			list(, $ClassPath) = explode('/', $this->Path);
			$cid = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path="/' . $DB_WE->escape($ClassPath) . '"', '', $DB_WE);
			$DB_WE->query('UPDATE ' . $DB_WE->escape(OBJECT_X_TABLE . $cid) . ' SET OF_Language="' . $DB_WE->escape($this->Language) . '" WHERE OF_Path LIKE "' . $DB_WE->escape($this->Path) . '/%" ');
		}

		return true;
	}

	function changeTriggerIDRecursive(){
		$DB_WE = new DB_WE();
		$DB_WE2 = new DB_WE();

		// Change TriggerID of published documents first
		$DB_WE->query('UPDATE ' . $DB_WE->escape($this->Table) . ' SET TriggerID = ' . intval($this->TriggerID) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ((Published=0 AND ContentType="folder") OR (Published!=0 AND ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '")))');

		// Change Language of unpublished documents

		$DB_WE->query('SELECT a.ID,b.DocumentObject FROM ' . $DB_WE->escape($this->Table) . ' a JOIN ' . TEMPORARY_DOC_TABLE . ' t ON a.ID=t.DocumentID WHERE a.Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND a.ContentType IN ("' . we_base_ContentTypes::WEDOCUMENT . '","' . we_base_ContentTypes::HTML . '","' . we_base_ContentTypes::OBJECT_FILE . '") AND t.DocTable="' . stripTblPrefix($this->Table) . '" AND t.Active=1');
		while($DB_WE->next_record()){
			$DocumentObject = we_unserialize($DB_WE->f('DocumentObject'));
			$DocumentObject[0]['TriggerID'] = $this->TriggerID;

			if(!$DB_WE2->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET ' .
							we_database_base::arraySetter(array(
								'DocumentObject' => ($DocumentObject ? we_serialize($DocumentObject) : ''),
							)) .
							' WHERE DocumentID=' . intval($DB_WE->f('ID')) . ' AND DocTable="' . stripTblPrefix($this->Table) . '" AND Active=1')){
				return false;
			}
		}

		// TriggerID auch bei den einzelnen Objekten aendern
		if($this->Table == OBJECT_FILES_TABLE){
			// Klasse feststellen
			list(, $ClassPath) = explode('/', $this->Path);
			$cid = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path="/' . $DB_WE->escape($ClassPath) . '"', '', $DB_WE);

			$DB_WE->query('UPDATE ' . $DB_WE->escape(OBJECT_X_TABLE . $cid) . ' SET OF_TriggerID=' . intval($this->TriggerID) . ' WHERE OF_Path LIKE "' . $DB_WE->escape($this->Path) . '/%" ');
		}

		return true;
	}

	protected function i_setText(){
		$this->Text = ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? $this->Filename : $this->Text;
	}

	protected function i_filenameDouble(){
		return f('SELECT 1 FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE Path="' . $this->DB_WE->escape($this->Path) . '" AND ID!=' . intval($this->ID) . ' LIMIT 1', '', $this->DB_WE);
	}

	protected function i_filenameEmpty(){
		$fn = ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? $this->Filename : $this->Text;
		return ($fn === '');
	}

	/* returns 0 because it is a directory */

	function getfilesize(){
		return 0;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			default:
				$_SESSION['weS']['EditPageNr'] = $this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_editors/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
			case we_base_constants::WE_EDITPAGE_DOCLIST:
				return 'we_editors/we_editor_doclist.inc.php';
		}
	}

	function formPath(){
		$ws = get_ws($this->Table, true);
		if(intval($this->ParentID) == 0 && $ws){
			$this->ParentID = $ws[0];
			$this->ParentPath = id_to_path($this->ParentID, $this->Table, $this->DB_WE);
		}

		$userCanChange = permissionhandler::hasPerm('CHANGE_DOC_FOLDER_PATH') || ($this->CreatorID == $_SESSION['user']['ID']) || (!$this->ID);
		if($this->ID != 0 && $this->ParentID == 0 && $this->ParentPath === '/' && defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
			$userCanChange = false;
		}
		return (!$userCanChange ? '<table class="default"><tr><td><span class="defaultfont">' . $this->Path . '</span></td></tr>' :
						'<table class="default">
<colgroup><col style="width:20px;"/><col style="width:20px;"/><col style="width:100px;"/></colgroup>
	<tr><td class="defaultfont" style="padding-bottom:10px;">' . $this->formInputField('', ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? 'Filename' : 'Text', g_l('weClass', '[foldername]'), 50, 0, 255, 'onchange=_EditorFrame.setEditorIsHot(true);pathOfDocumentChanged();') . '</td><td></td><td></td></tr>
	<tr><td colspan="3" class="defaultfont">' . $this->formDirChooser(0) . '</td></tr>' .
						(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE ? '
	<tr><td colspan="3" class="defaultfont" style="padding-top:4px;">' . $this->formTriggerDocument() . '</td></tr>
	<tr><td colspan="3">
		<table class="default"><tr><td style="padding-bottom:2px;">' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[grant_tid_expl]') . ($this->ID ? '' : g_l('weClass', '[availableAfterSave]')), we_html_tools::TYPE_INFO, 0, false) . '</td><td>' .
								we_html_button::create_button(we_html_button::OK, 'javascript:if(_EditorFrame.getEditorIsHot()) { ' . we_message_reporting::getShowMessageCall(g_l('weClass', '[saveFirstMessage]'), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeTriggerIDRecursive','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', ($this->ID ? false : true)) . '</td></tr>
					</table></td></tr>' :
								'') .
						($this->Table == FILE_TABLE && $this->ID && permissionhandler::hasPerm('ADMINISTRATOR') ? '
	<tr><td class="defaultfont" style="padding-top:10px;">' . $this->formInputField('', 'urlMap', g_l('weClass', '[urlMap]'), 50, 0, 255, 'onchange=_EditorFrame.setEditorIsHot(true); ') . '</td><td></td><td></td></tr>
' : '')) .
				'</table>';
	}

	function formChangeOwners(){
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		return '<table class="default"><tr><td style="padding-bottom:2px;">' . we_html_tools::htmlAlertAttentionBox(g_l('modules_users', '[grant_owners_expl]') . $_disabledNote, we_html_tools::TYPE_INFO, 390, false) . '</td><td>' .
				we_html_button::create_button(we_html_button::OK, 'javascript:if(_EditorFrame.getEditorIsHot()) { ' . we_message_reporting::getShowMessageCall(g_l('weClass', '[saveFirstMessage]'), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('users_changeR','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					</table>';
	}

	function formChangeLanguage(){
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		return '<table class="default"><tr><td style="padding-bottom:2px;">' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[grant_language_expl]') . $_disabledNote, we_html_tools::TYPE_INFO, 390, false) . '</td><td>' .
				we_html_button::create_button(we_html_button::OK, "javascript:if(_EditorFrame.getEditorIsHot()) { " . we_message_reporting::getShowMessageCall(g_l('weClass', '[saveFirstMessage]'), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeLanguageRecursive','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					</table>';
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$parents = array(0, $this->ID);
		we_getParentIDs(FILE_TABLE, $this->ID, $parents);
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		//FIXME: give JS an array!
		$wecmdenc3 = we_base_request::encCmd("var parents=[" . implode(',', $parents) . "];if(parents.indexOf(currentID) > -1){
			WE().util.showMessage(WE().consts.g_l.main.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
}else{
	opener.top.we_cmd('copyFolder', currentID," . $this->ID . ",1,'" . $this->Table . "');
}");
		$but = we_html_button::create_button(we_html_button::SELECT, ($this->ID ?
								"javascript:we_cmd('we_selector_directory', " . $cmd1 . ", '" . $this->Table . "', '" . we_base_request::encCmd($cmd1) . "', '', '" . $wecmdenc3 . "')" :
								"javascript:" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folders_no_id]'), we_message_reporting::WE_MESSAGE_ERROR))
						, true, 100, 22, "", "", !empty($_disabledNote));

		return '<table class="default"><tr><td style="padding-bottom:2px;">' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[copy_owners_expl]') . $_disabledNote, we_html_tools::TYPE_INFO, 0, false) . '</td><td>' .
				we_html_element::htmlHidden($idname, $this->CopyID) . $but . '</td></tr>
					</table>';
	}

	################ internal functions ######

	function writeFolder($pub = 0){
		if($this->Path == $this->OldPath || !$this->OldPath){
			return $this->saveToServer();
		}
		if(!$this->moveAtServer()){
			return false;
		}
		$this->modifyIndexPath();
		$this->modifyLinks();
		$this->modifyChildrenPath();

		$this->OldPath = $this->Path;
		return true;
	}

	function modifyIndexPath(){
		//FIXME: tablescan!
		$this->DB_WE->query('UPDATE ' . INDEX_TABLE . ' SET Workspace="' . $this->DB_WE->escape($this->Path . substr($this->DB_WE->f('Workspace'), strlen($this->OldPath))) . '" WHERE Workspace LIKE "' . $this->DB_WE->escape($this->OldPath) . '%"');
		$this->DB_WE->query('UPDATE ' . INDEX_TABLE . ' SET Path=CONCAT("' . $this->DB_WE->escape($this->Path) . '",SUBSTRING(Path,' . (strlen($this->OldPath) + 1) . ')) WHERE Path LIKE "' . $this->DB_WE->escape($this->OldPath) . '%"');
	}

	function modifyLinks(){
		switch($this->Table){
			case FILE_TABLE:
			case TEMPLATES_TABLE:
			case OBJECT_FILES_TABLE:
				$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->Table) . ' SET Path=CONCAT("' . $this->DB_WE->escape($this->Path) . '",SUBSTRING(Path,' . (strlen($this->OldPath) + 1) . ')) WHERE Path LIKE "' . $this->DB_WE->escape($this->OldPath) . '/%" OR Path="' . $this->DB_WE->escape($this->OldPath) . '"');
		}
	}

	function modifyChildrenPath(){
		@ignore_user_abort(true);
		$DB_WE = new DB_WE();
		// Update Paths also in Doctype Table
		//TODO: remove ParentPath
		if($this->Table == FILE_TABLE){
			$DB_WE->query('UPDATE ' . DOC_TYPES_TABLE . ' SET ParentPath="' . $DB_WE->escape($this->Path) . '" WHERE ParentID=' . intval($this->ID));
		}
		//FIMXE: is this really correct? this will only get the first, but not the second level files
		$DB_WE->query('SELECT ID,ClassName FROM ' . $DB_WE->escape($this->Table) . ' WHERE ParentID=' . intval($this->ID));
		while($DB_WE->next_record()){
			update_time_limit(30);
			$we_doc = $DB_WE->f('ClassName');
			if($we_doc){
				$we_doc = new $we_doc();
				$we_doc->initByID($DB_WE->f('ID'), $this->Table, we_class::LOAD_TEMP_DB);
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
				if(!we_base_file::createLocalFolder(($isTemplFolder ? TEMPLATES_PATH : $_SERVER['DOCUMENT_ROOT']), $path)){
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
				if(!$isTemplFolder && !we_base_file::createLocalFolder($_SERVER['DOCUMENT_ROOT'] . SITE_DIR, $path)){
					return false;
				}
			default:
				return true;
		}
	}

	/**
	 * Beseitigt #Bug 3705: sorgt daf�r, das auch leere Dokumentenordner bei einem REbuild angelegt werden
	 */
	public function we_rewrite(){
		return (parent::we_rewrite() ? ($this->Table == FILE_TABLE ? $this->we_save(true) : true) : false);
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

	public static function getUrlReplacements(we_database_base $db, $onlyUrl = false, $hostMatch = false){
		static $ret = -1;
		if($ret == -1){
			$ret = array('full' => array(), 'url' => array(), 'full_host' => array(), 'url_host' => array(),);
			$db->query('SELECT Path,urlMap FROM ' . FILE_TABLE . ' WHERE urlMap!="" ORDER BY Path DESC');
			$lastRules = array();
			while($db->next_record(MYSQL_NUM)){
				$host = trim(str_replace(array('https://', 'http://'), '', $db->f(1)), '/');
				$rep1 = '-((href\s*=|src\s*=|action\s*=|location\s*=|content\s*=|url)\s*["\'\(])(' . preg_quote($db->f(0), '-') . ')(/[^"\'\)]*["\'\)])-';
				$rep2 = '-^' . preg_quote($db->f(0), '-') . '(/.*)-';
				if($_SERVER['SERVER_NAME'] == $host){
					//this must be at the end, since duplicate replacements may need to match the original string
					$lastRules['full']['${1}${4}'] = $rep1;
					$lastRules['url']['${1}'] = $rep2;
				} else {
					$ret['full']['${1}//' . $host . '${4}'] = $rep1;
					$ret['url']['//' . $host . '${1}'] = $rep2;
				}
				$ret['full_host']['${1}' . '//' . $host . '${4}'] = $rep1;
				$ret['url_host']['//' . $host . '${1}'] = $rep2;
			}
			if($lastRules){
				$ret['full'] = array_merge($ret['full'], $lastRules['full']);
				$ret['url'] = array_merge($ret['url'], $lastRules['url']);
			}
		}
		return $ret[($onlyUrl ? 'url' : 'full') . ($hostMatch ? '_host' : '')];
	}

	public static function getUrlFromID($id){
		if(!$id){
			return '';
		}
		$replace = self::getUrlReplacements($GLOBALS['DB_WE'], true, true);
		$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id));
		return $replace ?
				preg_replace($replace, array_keys($replace), $path) :
				$path;
	}

	public function getPropertyPage(){
		$parts = array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140)
		);

		if($this->Table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE)){
			if(permissionhandler::hasPerm('ADMINISTRATOR')){
				$parts[] = array("icon" => "lang.gif", "headline" => g_l('weClass', '[language]'), "html" => $this->formLangLinks(), "noline" => 1, 'space' => 140);
				$parts[] = array("headline" => g_l('weClass', '[grant_language]'), "html" => $this->formChangeLanguage(), 'space' => 140, "forceRightHeadline" => true);
			} else if($this->Table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE)){
				$parts[] = array("icon" => "lang.gif", "headline" => g_l('weClass', '[language]'), "html" => $this->formLangLinks(), "space" => 140);
			}
		}

		if($this->Table == FILE_TABLE && permissionhandler::hasPerm('CAN_COPY_FOLDERS') ||
				(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE && permissionhandler::hasPerm('CAN_COPY_OBJECTS'))){
			$parts[] = array('icon' => 'copy.gif', 'headline' => g_l('weClass', '[copyFolder]'), "html" => $this->formCopyDocument(), 'space' => 140);
		}

		if($this->Table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE)){
			$parts[] = array("icon" => "user.gif", "headline" => g_l('weClass', '[owners]'), "html" => $this->formCreatorOwners() . "<br/>", "noline" => 1, 'space' => 140);
			if(permissionhandler::hasPerm("ADMINISTRATOR")){
				$parts[] = array("headline" => g_l('modules_users', '[grant_owners]'), "html" => $this->formChangeOwners(), "space" => 140, "forceRightHeadline" => 1);
			}
		}

		echo we_html_multiIconBox::getJS() .
		we_html_multiIconBox::getHTML('PropertyPage', $parts);
	}

}
