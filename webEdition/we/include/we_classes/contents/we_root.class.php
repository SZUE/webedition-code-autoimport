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
/* the parent class for tree-objects */
abstract class we_root extends we_class{
	const USER_HASACCESS = 1;
	const FILE_LOCKED = -3;
	const USER_NO_PERM = -2;
	const USER_NO_SAVE = -4;
	const FILE_NOT_IN_USER_WORKSPACE = -1;
	const EDITOR_HEADER = 1;
	const EDITOR_FOOTER = 2;

	var $IsClassFolder = 0;
	/* ParentID of the object (ID of the Parent-Folder of the Object) */
	var $ParentID = 0;

	/* Parent Path of the object (Path of the Parent-Folder of the Object) */
	var $ParentPath = '/';

	/* The Text that will be shown in the tree-menue */
	var $Text = '';

	/* Filename of the file */
	var $Filename = '';

	/* Path of the File  */
	var $Path = '';

	/* sha1 hash of the file */
	var $Filehash = '';

	/* OldPath of the File => used internal  */
	var $OldPath = '';

	/* Creation Date as UnixTimestamp  */
	var $CreationDate = 0;

	/* Modification Date as UnixTimestamp  */
	var $ModDate = 0;

	/* Rebuild Date as UnixTimestamp  */
	var $RebuildDate = 0;

	/* Flag which is set, when the file is a folder  */
	var $IsFolder = 0;

	/* ContentType of the Object  */
	public $ContentType = '';

	/* array which holds the content of the Object */
	var $elements = [];
	private $wasMoved = false;

	/* Number of the EditPage when editor() is called */
	public $EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
	var $CopyID;
	var $EditPageNrs = [];
	var $Owners = '';
	var $OwnersReadOnly = '';
	var $WebUserID = '';

	/* ID of the Autor who created the document */
	var $CreatorID = 0;

	/* ID of the user who last modify the document */
	var $ModifierID = 0;
	var $RestrictOwners = 0;
	protected $LockUser = 0;
	protected $MediaLinks = [];
	protected $LangLinks = [];

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->CreationDate = time();
		$this->ModDate = time();
		array_push($this->persistent_slots, 'OwnersReadOnly', 'ParentID', 'ParentPath', 'Text', 'Filename', 'Path', 'Filehash', 'OldPath', 'CreationDate', 'ModDate', 'RebuildDate', 'IsFolder', 'ContentType', 'elements', 'EditPageNr', 'CopyID', 'Owners', 'CreatorID', 'ModifierID', 'RestrictOwners', 'WebUserID', 'LockUser', 'LangLinks');
	}

	public function makeSameNew(array $keep = []){
		$keep = array_merge($keep, array('ParentID', 'ParentPath', 'EditPageNr',));
		$tempDoc = $this->ClassName;
		$tempDoc = new $tempDoc();
		$tempDoc->we_new();
		foreach($tempDoc->persistent_slots as $name){
			if(!in_array($name, $keep)){
				$this->{$name} = isset($tempDoc->{$name}) ? $tempDoc->{$name} : '';
			}
		}
		//t_e($this->ClassName, get_class($this), $tempDoc->persistent_slots, );
		$this->InWebEdition = true;
	}

	function equals($obj){
		foreach($this->persistent_slots as $cur){
			switch($cur){
				case 'Name':
				case 'elements':
				case 'EditPageNr':
				case 'wasUpdate':
					continue;
				default:
					if($this->{$cur} != $obj->{$cur}){
						return false;
					}
			}
		}
		foreach($this->elements as $key => $val){
			if($val['dat'] != $obj->elements[$key]['dat'] || $val['bdid'] != $obj->elements[$key]['bdid']){
				return false;
			}
		}
		return true;
	}

	function setParentID($newID){
		$this->ParentID = $newID;
		$this->ParentPath = $this->getParentPath();
	}

	function ModifyPathInformation($parentID){
		$this->setParentID($parentID);
		$this->Path = $this->getPath();
		$this->wasUpdate = true;
		$this->we_save(); //i_savePersistentSlotsToDB("Filename,Extension,Text,Path,ParentID");
		$this->modifyChildrenPath(); // only on folders, because on other classes this function is empty
	}

	function modifyChildrenPath(){
		// do nothing, only in Folder-Classes this Function schould have code!
	}

	function checkIfPathOk(){
		### check if Path has changed
		$Path = $this->getPath();
		if($Path != $this->Path){

			### check if Path exists in db
			if(f('SELECT Path FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE Path="' . $this->DB_WE->escape($Path) . '"', '', $this->DB_WE)){
				$GLOBALS['we_responseText'] = sprintf(g_l('weClass', '[response_path_exists]'), $Path);
				return false;
			}
			$this->Path = $Path;
		}
		return true;
	}

	/**
	 * @desc	the function modifies document EditPageNrs set
	 */
	function checkTabs(){
		//to be overriden
	}

	//FIXME: make this __sleep
	function saveInSession(&$save, $toFile = false){
		//NOTE: this is used for temporary documents! so be carefull when changing
		$save = array(
			[],
			$this->elements
		);
		foreach($this->persistent_slots as $slot){
			switch($slot){
				case 'elements'://elements are saved in slot 1
					break;
				//if saved to scheduler/temporary table we don't need these fields, they are only relevant in editor - or reread on load from current state table
				//FIXME: use primaryDBFiels or better
				case 'EditPageNr':
				case 'schedArr':
				case 'Path':
				case 'Text':
				case 'Filename':
				case 'Extension':
				case 'ParentID':
				case 'Published':
				case 'ModDate':
				case 'CreatorID':
				case 'ModifierID':
				case 'Owners':
				case 'RestrictOwners':
				case 'WebUserID':
				case 'Language':
				case 'temp_template_id':
				case 'DocType':
				case 'TemplateID':
				case 'OwnersReadOnly':
				case 'temp_category':
				case 'urlMap':
				case 'viewType':
				case 'IsProtected':
				case 'Filehash':
				case 'LockUser':
				case 'wasUpdate':
				case 'InWebEdition':
				case 'CreationDate':
				case 'RebuildDate':
				case 'CopyID':
				case 'hidePages':
				case 'controlElement':
				case 'usedElementNames':
				case 'editorSaves':
					if($toFile){
						break;
					}


				default:
					$bb = isset($this->{$slot}) ? $this->{$slot} : '';
//note these are objects: for searchclass, searchclassFolder
					$save[0][$slot] = $bb;
			}
		}
		// save weDocumentCustomerFilter in Session
		//NOTE: this is an object!
		//if we save to file, we load the new content from db instead
		if(!$toFile && !empty($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$save[3] = $this->documentCustomerFilter;
		}
	}

	function applyWeDocumentCustomerFilterFromFolder(){
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$tmpFolder = new we_folder();
			$tmpFolder->initByID($this->ParentID, $this->Table);
			$this->documentCustomerFilter = $tmpFolder->documentCustomerFilter;

			if($this->IsFolder && $this->ID != 0){
				$this->ApplyWeDocumentCustomerFiltersToChilds = true;
			}
			unset($tmpFolder);
		}
	}

	/* init the object with data from the database */

	function copyDoc($id){
		// overwrite
	}

######### Form functions for generating the html of the input fields ##########

	/* creates a text-input field for entering Data that will be stored at the $elements Array */

	/* creates the filename input-field */

	function formFilename($text = ''){
		return $this->formTextInput('', 'Filename', $text ? : g_l('weClass', '[filename]'), 24, 255);
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = 0, $rootDirID = 0, $table = '', $Pathname = 'ParentPath', $IDName = 'ParentID', $cmd = '', $label = true, $disabled = false){
		$yuiSuggest = &weSuggest::getInstance();
		$label = ($label === true ? g_l('weClass', '[dir]') : $label);

		if(!$table){
			$table = $this->Table;
		}
		$textname = 'we_' . $this->Name . '_' . $Pathname;
		$idname = 'we_' . $this->Name . '_' . $IDName;
		$path = $this->$Pathname;
		$myid = $this->$IDName;
		if($disabled){
			return we_html_tools::htmlFormElementTable(array(
					"text" => we_html_element::htmlHidden($idname, $myid, $idname) .
					we_html_element::htmlHidden($textname, $path, $textname) .
					we_html_element::htmlInput(array('name' => 'disabled', 'value' => $path, 'type' => 'text', 'width' => intval($width - 6), 'disabled' => 1)),
					'style' => 'vertical-align:top;height:10px;'), g_l('weClass', '[dir]')
			);
		}

		if($Pathname === 'ParentPath'){
			$parentPathChanged = 'if(opener.pathOfDocumentChanged) { opener.pathOfDocumentChanged(); }';
			$parentPathChangedBlur = 'if(pathOfDocumentChanged) { pathOfDocumentChanged(); }';
		} else {
			$parentPathChanged = $parentPathChangedBlur = '';
		}

		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . $table . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value") . "','" . we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);" . $parentPathChanged . str_replace('\\', '', $cmd)) . "','','" . $rootDirID . "')");

		$yuiSuggest->setAcId('Path', id_to_path(array($rootDirID), $table));
		$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER . ',' . we_base_ContentTypes::CLASS_FOLDER);
		$yuiSuggest->setInput($textname, $path, array('onblur' => $parentPathChangedBlur));
		$yuiSuggest->setLabel($label ? : '');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(intval($width));
		$yuiSuggest->setSelectButton($button);
		return $yuiSuggest->getHTML();
	}

	function htmlTextInput_formDirChooser($attribs = [], $addAttribs = []){
		$attribs = array(
			'class' => 'wetextinput',
			'size' => 30,
			'value' => '',
		);

		foreach($addAttribs as $key => $value){
			if(isset($attribs[$key])){
				$attribs[$key] .= $value;
			} else {
				$attribs[$key] = $value;
			}
		}

		foreach($attribs as $key => $value){
			$attribs[$key] = $value;
		}

		$attribs['type'] = 'text';

		return getHtmlTag('input', $attribs);
	}

	function formCreator($canChange){
		if(!$this->CreatorID){
			$this->CreatorID = 0;
		}
		$creator = $this->CreatorID ? id_to_path($this->CreatorID, USER_TABLE, $this->DB_WE) : g_l('weClass', '[nobody]');
		if(!$canChange){
			return $creator;
		}

		$textname = 'wetmp_' . $this->Name . '_CreatorID';
		$idname = 'we_' . $this->Name . '_CreatorID';

		$inputFeld = we_html_tools::htmlTextInput($textname, 24, $creator, '', ' readonly', '');
		$idfield = we_html_element::htmlHidden($idname, $this->CreatorID);
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_users_selector','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value") . "','user'," . $cmd1 . ",'" . we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);") . "')");

		return we_html_tools::htmlFormElementTable($inputFeld, g_l('weClass', '[maincreator]'), 'left', 'defaultfont', $idfield, $button);
	}

	function formRestrictOwners($canChange){
		if($canChange){
			$n = 'we_' . $this->Name . '_RestrictOwners';
			$v = $this->RestrictOwners ? true : false;
			return we_html_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass', '[limitedAccess]'), false, 'defaultfont', "setScrollTo();WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('reload_editpage');");
		}
		return '<table class="default"><tr><td><i class="fa fa-' . ($this->RestrictOwners ? 'check-' : '') . 'square-o wecheckIcon disabled"></i></td><td class="defaultfont">&nbsp;' . g_l('weClass', '[limitedAccess]') . '</td></tr></table>';
	}

	function formOwners($canChange = true){
		$owners = makeArrayFromCSV($this->Owners);
		$ownersReadOnly = we_unserialize($this->OwnersReadOnly);

		$content = '<table class="default" style="width:370px;margin:2px 0px;">
<colgroup><col style="width:20px;"/><col style="width:351px;"/><col style="width:100px;"/><col style="width:26px;"/></colgroup>';
		if($owners){
			$this->DB_WE->query('SELECT ID,Path,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType FROM ' . USER_TABLE . ' WHERE ID IN(' . implode(',', $owners) . ')');
			while($this->DB_WE->next_record(MYSQL_ASSOC)){
				$owner = $this->DB_WE->f('ID');
				$content .= '<tr><td class="userIcon" data-contenttype="' . $this->DB_WE->f('ContentType') . '"></td><td class="defaultfont">' . $this->DB_WE->f('Path') . '</td><td>' .
					we_html_forms::checkboxWithHidden(isset($ownersReadOnly[$owner]) ? $ownersReadOnly[$owner] : '', 'we_owners_read_only[' . $owner . ']', g_l('weClass', '[readOnly]'), false, 'defaultfont', 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);', !$canChange) .
					'</td><td>' . ($canChange ? we_html_button::create_button(we_html_button::TRASH, "javascript:setScrollTo();WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('users_del_owner','" . $owner . "');") : '') . '</td></tr>';
			}
		} else {
			$content .= '<tr><td class="userIcon" data-contenttype="we/user"></td><td class="defaultfont">' . g_l('weClass', '[onlyOwner]') . '</td><td></td><td></td></tr>';
		}
		$content .= '</table>';

		$textname = 'OwnerNameTmp';
		$idname = 'OwnerIDTmp';
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('users_del_all_owners','')", true, 0, 0, "", "", $this->Owners ? false : true);
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc5 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);opener.setScrollTo();fillIDs();opener.we_cmd('users_add_owner',top.allIDs);");
		$addbut = $canChange ?
			we_html_element::htmlHiddens(array($idname => '', $textname => '')) . we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_users_selector','document.we_form.elements[\'" . $idname . "\'].value','" . $wecmdenc2 . "','',document.we_form.elements['" . $idname . "'].value,'" . $wecmdenc5 . "','','',1);") : "";

		$content = '<table class="default" style="width:500px;">
<tr><td><div class="multichooser">' . $content . '</div></td></tr>
' . ($canChange ? '<tr><td style="text-align:right;padding-top:2px;">' . $delallbut . $addbut . '</td></tr>' : "") . '</table>' . we_html_element::jsElement('WE().util.setIconOfDocClass(document,\'userIcon\');');

		return we_html_tools::htmlFormElementTable($content, g_l('weClass', '[otherowners]'), 'left', 'defaultfont');
	}

	function formCreatorOwners(){
		$canChange = ((!$this->ID) || we_users_util::isUserInUsers($_SESSION['user']['ID'], $GLOBALS['we_doc']->CreatorID));

		return '<table class="default">
<tr><td class="defaultfont" style="padding-bottom:2px;">' . $this->formCreator($canChange && permissionhandler::hasPerm('CHANGE_DOCUMENT_OWNER')) . '</td></tr>
<tr><td>' . $this->formRestrictOwners($canChange && permissionhandler::hasPerm('CHANGE_DOCUMENT_PERMISSION')) . '</td></tr>' .
			($this->RestrictOwners ?
				'<tr><td style="padding-top:2px;">' . $this->formOwners($canChange && permissionhandler::hasPerm('CHANGE_DOCUMENT_PERMISSION')) . '</td></tr>' : '') .
			'</table>';
	}

	function del_all_owners(){
		$this->Owners = '';
	}

	function add_owner($id){
		$ids = array_filter(is_array($id) ? $id : explode(',', $id));
		$this->Owners = implode(',', array_unique(array_filter(array_merge(explode(',', $this->Owners), $ids)), SORT_NUMERIC));
	}

	function del_owner($id){
		$owners = array_filter(explode(',', $this->Owners));
		if(($pos = array_search($id, $owners, false)) === false){
			return;
		}

		unset($owners[$pos]);
		$this->Owners = implode(',', $owners);
	}

	/**
	 * @return bool
	 * @desc	checks if a document is restricted to several users and if
	  the user is one of the restricted users
	 */
	function userHasPerms(){
		if(permissionhandler::hasPerm('ADMINISTRATOR') || !$this->RestrictOwners || we_users_util::isOwner($this->Owners) || we_users_util::isOwner($this->CreatorID)){
			return true;
		}
		return false;
	}

	function userIsCreator(){
		return (permissionhandler::hasPerm('ADMINISTRATOR') || we_users_util::isOwner($this->CreatorID));
	}

	function userCanSave($ctConditionOk = false){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$ctConditionOk){ // check table condition in respective subclasses: eg in we_collection we check SAVE_COLLECTION
			if(defined('OBJECT_TABLE') && ($this->Table == OBJECT_FILES_TABLE)){
				if(!(permissionhandler::hasPerm(['NEW_OBJECTFILE_FOLDER', 'NEW_OBJECTFILE']))){
					return false;
				}
			} else {
				if(!permissionhandler::hasPerm('SAVE_DOCUMENT_TEMPLATE')){
					return false;
				}
			}
		}
		if(!$this->RestrictOwners){
			return true;
		}
		if(!$this->userHasPerms()){
			return false;
		}
		$ownersReadOnly = we_unserialize($this->OwnersReadOnly);
		$readers = [];
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1){
				$readers[] = $key;
			}
		}
		return !we_users_util::isUserInUsers($_SESSION['user']['ID'], $readers);
	}

	public function formPath($disablePath = false, $notSetHot = false, $extra = ''){
		$disable = ( ($this->ContentType == we_base_ContentTypes::HTML || $this->ContentType == we_base_ContentTypes::WEDOCUMENT) && $this->Published);
		if($this->ContentType === we_base_ContentTypes::HTACCESS){
			$vals = we_base_ContentTypes::inst()->getExtension($this->ContentType, true);
			$this->Filename = $this->Filename ? : current($vals);
			$filenameinput = $this->formSelectFromArray('', 'Filename', array_combine($vals, $vals), g_l('weClass', '[filename]'));
		} else {
			$filenameinput = $this->formInputField('', 'Filename', g_l('weClass', '[filename]'), 30, 0, 255, 'onchange="' . ($notSetHot ? '' : 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true); ') . 'if(self.pathOfDocumentChanged){pathOfDocumentChanged();}"');
		}
		return $disable ? ($this->Path) : '
<table class="default">
	<tr>
		<td style="padding-bottom:4px;">' . $filenameinput . '</td>
		<td></td>
		<td>' . $this->formExtension2() . '</td>
	</tr>
	<tr><td colspan="3">' . $this->formDirChooser(0, 0, '', 'ParentPath', 'ParentID', '', g_l('weClass', '[dir]'), $disablePath) . '</td></tr>' .
			$extra . '
</table>';
	}

	protected function formExtension2(){
		return '';
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$but = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', " . $cmd1 . ",'" . $this->Table . "','" . we_base_request::encCmd($cmd1) . "','','" . we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('copyDocument', currentID);") . "','','0','" . $this->ContentType . "',1);");

		return we_html_element::htmlHidden($idname, $this->CopyID) . $but;
	}

	# return html code for button and field to select user
	# ATTENTION !: You have to have we_cmd function in your file and browse_user section

	#
	function formUserChooser($old_userID = -1, $width = '', $in_textname = '', $in_idname = ''){
		$textname = $in_textname ? : 'we_' . $this->Name . '_UserName';
		$idname = $in_idname ? : 'we_' . $this->Name . '_UserID';

		$username = '';
		$userid = $old_userID;
		if(intval($userid) > 0){
			$username = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), 'username', $this->DB_WE);
		}

		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		return we_html_tools::htmlFormElementTable(self::htmlTextInput($textname, 30, $username, '', ' readonly', 'text', $width, 0), 'User', 'left', 'defaultfont', we_html_element::htmlHidden($idname, $userid), we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_users_selector','document.we_form.elements['" . $idname . "'].value','" . $wecmdenc2 . "','user')"));
	}

	//FIXME: this should be a general selector
	protected function formDocChooser($width, $name, $type = 'txt', $selector = weSuggest::DocSelector, $table = FILE_TABLE){
		$yuiSuggest = &weSuggest::getInstance();
		$textname = $this->Name . '_' . $name;
		$idname = 'we_' . $this->Name . '_' . $type . '[' . $name . '#bdid]';
		$myid = $this->getElement($name, 'bdid');
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), '', $this->DB_WE);
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);");

		$yuiSuggest->setAcId('TriggerID');
		$yuiSuggest->setContentType(we_base_ContentTypes::IMAGE /* $selector == weSuggest::DocSelector ? 'folder,' . we_base_ContentTypes::WEDOCUMENT : '' */);
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel(g_l('weClass', '[' . $name . ']'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector($selector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_image'," . $cmd1 . ",'" . $table . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::IMAGE . "',1)"));
		$yuiSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputTriggerID');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);", true, 27, 22));
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
		return $yuiSuggest->getHTML();
	}

	function formTriggerDocument($isclass = false){
		$yuiSuggest = &weSuggest::getInstance();
		$table = FILE_TABLE;
		$textname = 'we_' . $this->Name . '_TriggerName';
		if($isclass){
			$idname = 'we_' . $this->Name . '_DefaultTriggerID';
			$myid = $this->DefaultTriggerID ? : '';
		} else {
			$idname = 'we_' . $this->Name . '_TriggerID';
			$myid = $this->TriggerID ? : '';
		}
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), '', $this->DB_WE);
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);");

		$yuiSuggest->setAcId('TriggerID');
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT);
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel(g_l('modules_object', '[seourltrigger]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . $table . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::WEDOCUMENT . "',1)"));
		$yuiSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputTriggerID');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);", true, 27, 22));
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
		return $yuiSuggest->getHTML();
	}

	protected function formInputLangLink($headline, $langkey, $LDID = 0, $path = ''){
		$yuiSuggest = & weSuggest::getInstance();
		$textname = 'we_' . $this->Name . '_LanguageDocName[' . $langkey . ']';
		$idname = 'we_' . $this->Name . '_LanguageDocID[' . $langkey . ']';
		$ackeyshort = 'LanguageDoc' . str_replace('_', '', $langkey);
		$myid = $LDID ? : '';
		$table = $this->IsFolder ? FILE_TABLE : $this->Table;
		$path = $path ? : ($LDID ? f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), '', $this->DB_WE) : '');

		$rootDirID = $table === OBJECT_FILES_TABLE ? $this->rootDirID : 0;
		if($rootDirID && !$path){
			$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($rootDirID), '', $this->DB_WE);
		}

		$yuiSuggest->setAcId($ackeyshort, $path);
		if($table == FILE_TABLE){
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT);
			$ctype = we_base_ContentTypes::WEDOCUMENT;
			$etype = FILE_TABLE;
		} else {
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::OBJECT_FILE);
			$ctype = we_base_ContentTypes::OBJECT_FILE;
			$etype = OBJECT_FILES_TABLE;
		}
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd('opener.WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);');

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . $table . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','" . $ctype . "',1,0,0,'" . $langkey . "')");
		$trashButton = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='-1';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInput" . $ackeyshort . "');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);", true, 27, 22);
		$openbutton = we_html_button::create_button(we_html_button::EDIT, "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . $ctype . "','" . $etype . "'); }");
		if(!empty($this->DocType) && permissionhandler::hasPerm("NEW_WEBEDITIONSITE")){
			$db = new DB_WE();
			$LDcoType = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblDocTypes" AND DID=' . $this->DocType . ' AND Locale="' . $db->escape($langkey) . '"', '', $db);
			if($LDcoType){
				$createbutton = we_html_button::create_button('fa:add_doc,fa-plus,fa-lg fa-file-text-o', "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $LDcoType . "');");
				$yuiSuggest->setCreateButton($createbutton);
			}
		}
		$yuiSuggest->setInput($textname, $path, '', true);
		//$yuiSuggest->setInput($textname);
		$yuiSuggest->setLabel($headline);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(0);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setTrashButton($trashButton);
		$yuiSuggest->setOpenButton($openbutton);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
		//$yuiSuggest->setIsDropFromTree(true);//deactivated
		return $yuiSuggest->getHTML();
	}

	function formLangLinks($withHeadline = true){
		$defLang = self::getDefaultLanguage();
		$value = ($this->Language ? : $defLang);
		$inputName = 'we_' . $this->Name . '_Language';
		$languages = getWeFrontendLanguagesForBackend();
		$headline = ($withHeadline ? '<tr><td class="defaultfont">' . g_l('weClass', '[language]') . '</td></tr>' : '');

		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			foreach($languages as $langkey => $lang){
				$divname = 'we_' . $this->Name . '_LanguageDocDiv[' . $langkey . ']';
				$LDID = !empty($this->LangLinks[$langkey]['id']) && $this->LangLinks[$langkey]['id'] !== -1 ? $this->LangLinks[$langkey]['id'] : 0;
				$path = $LDID ? $this->LangLinks[$langkey]['path'] : '';

				$htmlzw.= '<div id="' . $divname . '" ' . ($this->Language == $langkey ? ' style="display:none" ' : '') . '>' . $this->formInputLangLink($lang, $langkey, $LDID, $path) . '</div>';
				$langkeys[] = $langkey;
			}
			return '
<table class="default" style="margin-top:2px;">' .
				$headline . '
	<tr><td style="padding-bottom:2px;">' . $this->htmlSelect($inputName, $languages, 1, $value, false, array("onblur" => "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);", 'onchange' => "dieWerte='" . implode(',', $langkeys) . "';showhideLangLink('we_" . $this->Name . "_LanguageDocDiv',dieWerte,this.options[this.selectedIndex].value);WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);"), "value") . '</td></tr>
	<tr><td class="defaultfont" style="text-align:left">' . g_l('weClass', '[languageLinks]') . '</td></tr>
</table>
<br/>' . $htmlzw; //.we_html_tools::htmlFormElementTable($htmlzw,g_l('weClass','[languageLinksDefaults]'),"left",	"defaultfont");	dieWerte=\''.implode(',',$langkeys).'\'; disableLangDefault(\'we_'.$this->Name.'_LangDocType\',dieWerte,this.options[this.selectedIndex].value);"
		}
		return '
<table class="default" style="margin-top:2px;">' .
			$headline . '
	<tr><td>' . $this->htmlSelect($inputName, $languages, 1, $value, false, array("onblur" => "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);", 'onchange' => "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);"), "value") . '</td></tr>
</table>';
	}

	#################### Function for getting and setting the $elements Array #########################################################################

	/* returns true if the element with the name $name is set */

	function issetElement($name){
		return isset($this->elements[$name]);
	}

	/* set the Data for an element */

	function setElement($name, $data, $type = 'txt', $key = 'dat', $autobr = false){
		$this->elements[$name][$key] = $data;
		$this->elements[$name]['type'] = $type;
		switch($key){
			case 'bdid':
				if(isset($this->elements[$name]['dat'])){//remove dat if bdid is set
					unset($this->elements[$name]['dat']);
				}
				break;
			case 'dat':
				if(isset($this->elements[$name]['bdid'])){//remove bdid if dat is set
					unset($this->elements[$name]['bdid']);
				}
				break;
		}
		if($autobr){
			$this->elements[$name]['autobr'] = $autobr;
		}
	}

	function delElement($name){
		unset($this->elements[$name]);
	}

	/* get the data from an element */

	function getElement($name, $key = 'dat', $default = '', $defaultOnEmpty = false){//FIXME should we bother bdid?
		switch($key){
			case 'dat':
				//check bdid first
				return (!empty($this->elements[$name]['bdid']) ?
						$this->elements[$name]['bdid'] :
						(isset($this->elements[$name]['dat']) && (!$defaultOnEmpty || $this->elements[$name]['dat']) ?
							$this->elements[$name]['dat'] :
							$default));
			default:
				return (isset($this->elements[$name][$key]) ? $this->elements[$name][$key] : $default);
		}
	}

	/* reset the array-pointer (for use with nextElement()) */

	function resetElements(){
		if(is_array($this->elements)){
			reset($this->elements);
		}
	}

	/* returns the next element or false if the array-pointer is at the end of the array */

	function nextElement($type = 'txt'){
		if(is_array($this->elements)){
			while($arr = each($this->elements)){
				if(!$type || (isset($arr['value']['type']) && $arr['value']['type'] == $type)){
					return $arr;
				}
			}
		}
		return false;
	}

	##### Functions for generating JavaScrit to update the document tree

	/* returns the JavaScript-Code which modifies the tree-menue */

	function getUpdateTreeScript($select = true){
		return $this->getMoveTreeEntryScript($select);
	}

	function getMoveTreeEntryScript($select = true){
		$Tree = new we_tree_main('webEdition.php', 'top', 'top', 'top.load');
		return $Tree->getJSUpdateTreeScript($this, $select);
	}

	/** returns the Path dynamically (use it, when the class-variable Path is not set)  */
	public function getPath(){
		return rtrim($this->getParentPath(), '/') . '/' . ( isset($this->Filename) ? $this->Filename : '' ) . ( isset($this->Extension) ? $this->Extension : '' );
	}

	/** returns the Path dynamically (use it, when the class-variable Text is not set)  */
	function getText(){
		return $this->Text;
	}

	public function getEditorBodyAttributes($editor = 0){
		return '';
	}

	/** get the Path of the Parent-Object */
	function getParentPath(){
		return (!$this->ParentID) ? '/' : f('SELECT Path FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->ParentID), '', $this->DB_WE);
	}

	function constructPath(){
		if($this->ID){
			$pid = $this->ParentID;
			$p = '/' . $this->Text;
			$z = 0;
			while($pid && $z < 50){
				$hash = getHash('SELECT ParentID,Text FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($pid), $this->DB_WE);
				$pid = $hash['ParentID'];
				$text = $hash['Text'];
				$p = '/' . $text . $p;
				$z++;
			}
			if($z >= 50){
				return false;
			}
			return $p;
		}
		return false;
	}

	/* get the Real-Path of the Object (Server-Path)
	 * @$fileaccesss bool if true, a path valid for domain replacement is given
	 */

	public function getRealPath($old = false){
		return (($this->Table == FILE_TABLE) ? $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . '..' : TEMPLATES_PATH) .
			($old ? $this->OldPath : $this->getPath());
	}

	/* get the Site-Path of the Object */

	public function getSitePath($old = false){
		return $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr(($old ? $this->OldPath : $this->getPath()), 1);
	}

	/* get the HTTP-Path of the Object */

	function getHttpPath(){
		$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE'], true);
		$http = $this->getPath();
		if($urlReplace){
			$cnt = 0;
			$http = preg_replace($urlReplace, array_keys($urlReplace), $http, -1, $cnt);
			return ($cnt ? 'http:' : getServerUrl()) . $http;
		}

		return $http;
	}

	protected static function getDefaultLanguage(){
// get interface language of user
		list($userLanguage) = explode('_', isset($_SESSION['prefs']['Language']) ? $_SESSION['prefs']['Language'] : '');

// trying to get locale string out of interface language
		$key = array_search($userLanguage, getWELangs());

		$defLang = $GLOBALS['weDefaultFrontendLanguage'];

// if default language is not equal with frontend language
		if(substr($defLang, 0, strlen($key)) !== $key){
// get first language that fits
			foreach(array_keys(getWeFrontendLanguagesForBackend()) as $k){
				$parts = explode('_', $k);
				if($parts[0] === $key){
					$defLang = $k;
				}
			}
		}
		return $defLang;
	}

	function editor(){

	}

	protected function getParentIDFromParentPath(){
		return 0;
	}

	/* function makeHrefByID($id){
	  return f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $this->DB_WE);
	  } */

	function save($resave = 0, $skipHook = 0){
		return $this->we_save($resave, $skipHook);
	}

# public ##################

	public function we_new(){
		parent::we_new();
		$this->CreatorID = isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;
		$this->ParentPath = $this->getParentPath();
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);

		$this->i_getContentData();
		$this->i_getLangLinks();
		$this->OldPath = $this->Path;
	}

	public function we_save($resave = false, $skipHook = false){
		//$this->i_setText;
		if($this->PublWhenSave){
			$this->Published = time();
		}
		if(!$resave){
			$this->ModDate = time();
			$this->ModifierID = empty($GLOBALS['we']['Scheduler_active']) && !empty($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;
		}
		$this->RebuildDate = time();
		if(!parent::we_save($resave)){
			return false;
		}
		$this->update_filehash();
		$a = $this->i_saveContentDataInDB();
		if(!$resave && !$this->IsClassFolder){
			we_history::insertIntoHistory($this);
		}
		return $a;
	}

	/**
	 * resave weDocumentCustomerFilter
	 *
	 */
	function resaveWeDocumentCustomerFilter(){
		if(!empty($this->documentCustomerFilter)){
			we_customer_documentFilter::saveForModel($this);
		}
	}

	protected function i_getDefaultFilename(){
		return f('SELECT MAX(ID) FROM ' . $this->DB_WE->escape($this->Table), '', $this->DB_WE) + 1;
	}

	public function we_initSessDat($sessDat){//FIXME: use __wakeup
		parent::we_initSessDat($sessDat);
		if(!empty($sessDat)){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
				}
			}
			if(isset($sessDat[1])){
				$this->elements = $sessDat[1];
			}
		}
		$this->i_setElementsFromHTTP();
	}

	//FIXME: make this __wakeup - used also to read data from temporary table
	protected function i_initSerializedDat($sessDat){
		if(!is_array($sessDat)){
			$this->Name = md5(uniqid(__FUNCTION__, true));
			return false;
		}
		foreach($this->persistent_slots as $cur){
			if(isset($sessDat[0][$cur])){
				$this->{$cur} = $sessDat[0][$cur];
			}
		}
		if(isset($sessDat[1])){
			$this->elements = $sessDat[1];
		}
		/* 		if(isset($sessDat[2])){
		  $this->NavigationItems = $sessDat[2];
		  } else {
		  $this->i_loadNavigationItems();
		  } */
		$this->Name = md5(uniqid(__FUNCTION__, true));
		return true;
	}

# private ###################

	protected function i_setText(){
		$this->Text = $this->Filename;
	}

	protected function i_convertElemFromRequest($type, &$v, $k){
		switch($type){
			case 'float':
				$v = floatval(str_replace(',', '.', $v));
				break;
			case 'int':
				$v = intval($v);
				break;
			case 'text':
				if($this->DefArray[$type . '_' . $k]['dhtmledit'] === 'on'){
					$v = we_base_util::rmPhp($v);
					break;
				}
			case 'input':
				if($this->DefArray[$type . '_' . $k]['forbidphp'] === 'on'){
					$v = we_base_util::rmPhp($v);
				}
				if($this->DefArray[$type . '_' . $k]['forbidhtml'] === 'on'){
					$v = removeHTML($v);
				}
				break;
			case 'internal'://pseudo-element for i_setElementsFromHTTP
				break;
			default:
				$v = removeHTML(we_base_util::rmPhp($v));
				break;
		}
	}

	protected function i_set_PersistentSlot($name, $value){
		if(in_array($name, $this->persistent_slots)){
			$this->$name = $value;
		}
	}

	protected function i_setElementsFromHTTP(){
		// do not set REQUEST VARS into the document
		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
			case 'switch_edit_page':
				if(we_base_request::_(we_base_request::STRING, 'we_cmd', false, 3)){
					return true;
				}
				break;
			case 'save_document':
				if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 7) === 'save_document'){
					return true;
				}
				break;
		}

		if($_REQUEST){
			$this->setRequestData();
		}
		$this->Text = $this->getText();
		$this->ParentPath = $this->getParentPath();
		$this->Path = $this->getPath();
	}

	private function setRequestData(){
		$regs = $dates = [];
		foreach($_REQUEST as $n => $v){
			if(preg_match('/^we_' . preg_quote($this->Name) . '_([^\[]+)$/', $n, $regs)){
				if(is_array($v)){
					$type = $regs[1];
					foreach($v as $name => $v2){
						$v2 = we_base_util::cleanNewLine($v2);
						switch($type){
							case 'LanguageDocName':
							case 'LanguageDocID':
								$this->LangLinks[$name][$type === 'LanguageDocName' ? 'path' : 'id'] = $v2;
								break;
							case 'date':
								preg_match('|(.*)_(.*)|', $name, $regs);
								list(, $name, $what) = $regs;
								$dates[$name][$what] = $v2;
								break;
							case 'category'://from we:category
								$this->setElement($name, (is_array($v2) ? implode(',', $v2) : $v2));
								break;
							default:
								if(preg_match('/(.+)#(.+)/', $name, $regs)){
									$this->setElement($regs[1], $v2, $type, $regs[2]);
								} else {
									//FIXME: check if we can apply the correct type
									$this->i_convertElemFromRequest('internal', $v2, $name);
									$this->setElement($name, $v2, $type);
								}
								break;
						}
					}
				} else {
					$this->i_set_PersistentSlot($regs[1], $v);
				}
			} else if($n === 'we_owners_read_only'){
				$this->OwnersReadOnly = we_serialize($v, SERIALIZE_JSON);
			}
		}
		$year = date('Y');
		foreach($dates as $k => $v){
			if(!array_sum($dates[$k])){
				$this->setElement($k, 0, 'date', 'bdid');
			} else {
				$this->setElement($k, mktime(empty($dates[$k]['hour']) ? 0 : $dates[$k]['hour'], empty($dates[$k]['minute']) ? 0 : $dates[$k]['minute'], 0, empty($dates[$k]['month']) ? 1 : $dates[$k]['month'], empty($dates[$k]['day']) ? 1 : $dates[$k]['day'], empty($dates[$k]['year']) ? $year : $dates[$k]['year']), 'date', 'bdid');
			}
		}
	}

	protected function i_isElement($Name){
		return true; // overwrite
	}

	protected function i_getContentData(){
		//FIXME:if we need dHash/nHash we have to get it hex, since tblTemporaryDoc will have problems with bin data
		$this->DB_WE->query('SELECT ID,BDID,Dat,CID,Type,Name,DocumentTable FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON c.ID=l.CID WHERE l.DID=' . intval($this->ID) . ' AND l.DocumentTable="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '"');
		$filter = array('Name', 'DID', 'Ord');
		while($this->DB_WE->next_record(MYSQLI_ASSOC)){
			$Name = $this->DB_WE->f('Name');

			switch($this->DB_WE->f('Type')){
				case 'formfield': // garbage fix!
					$this->elements[$Name] = we_unserialize($this->DB_WE->f('Dat'));
					break;
				default:
					if($this->i_isElement($Name)){
						foreach($this->DB_WE->Record as $k => $v){
							if(!in_array($k, $filter)){
								$this->elements[$Name][strtolower($k)] = $v;
							}
						}
						$this->elements[$Name]['table'] = CONTENT_TABLE;
					}
			}
		}
	}

	protected function i_getLangLinks(){
		$langkeys = array_keys(getWeFrontendLanguagesForBackend());
		if(LANGLINK_SUPPORT){
			$isObject = (defined('OBJECT_FILES_TABLE') ? $this->Table == OBJECT_FILES_TABLE || $this->Table == OBJECT_TABLE : false);
			$documentTable = $isObject && !$this->IsFolder ? stripTblPrefix(OBJECT_FILES_TABLE) : stripTblPrefix(FILE_TABLE);
			$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $documentTable . '" AND IsObject=' . intval($isObject) . ' AND DID=' . intval($this->ID) . ' AND Locale IN("' . implode('","', $langkeys) . '")');
			$tmpIDs = $this->DB_WE->getAllFirst(false);

			$tmpPaths = id_to_path($tmpIDs, $this->Table, $this->DB_WE, true);
			foreach($langkeys as $langkey){
				$this->LangLinks[$langkey] = empty($tmpIDs[$langkey]) || empty($tmpPaths[$tmpIDs[$langkey]]) ?
					array('id' => 0, 'path' => '') :
					array('id' => $tmpIDs[$langkey], 'path' => $tmpPaths[$tmpIDs[$langkey]]);
			}
			return;
		}
		foreach($langkeys as $langkey){
			$this->LangLinks[$langkey] = array('id' => 0, 'path' => '');
		}
	}

	private function getLinkReplaceArray(){
		$this->DB_WE->query('SELECT CONCAT_WS("_",Type,Name) AS Name,CID FROM ' . LINK_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix($this->Table) . '"');
		return $this->DB_WE->getAllFirst(false);
	}

	function i_saveContentDataInDB(){
		if(!is_array($this->elements)){
			return we_base_delete::deleteContentFromDB($this->ID, $this->Table, $this->DB_WE);
		}
		//don't stress index:
		$replace = $this->getLinkReplaceArray();
		foreach($this->elements as $k => $v){
			if(!$this->i_isElement($k) ||
				//ignore fields which result in empty entry
				(empty($v['dat']) && empty($v['bdid']) && empty($v['ffname'])) ||
				//don't set "vars" type
				(isset($v['type']) && $v['type'] == 'vars') ||
				//ignore binary data
				($k === 'data' && $this->isBinary())
			){
				continue;
			}
			$dat = isset($v['dat']) ? $v['dat'] : '';
			$bdid = isset($v['bdid']) ? $v['bdid'] : 0;

			$v['type'] = (empty($v['ffname']) ? (empty($v['type']) ? 'txt' : $v['type']) : 'formfield');

			switch($v['type']){
				case 'formfield':
					//remove empty fields on serialize
					$dat = we_serialize(array_filter($v), SERIALIZE_JSON);
					break;
				case 'date':
					$dat = sprintf('%016d', $dat);
					break;
			}

			if($bdid || $dat){
				$dat = ($bdid ? sql_function('NULL') : (is_array($dat) ? we_serialize($dat) : $dat));
				$data = array(
					'Dat' => $dat,
					'BDID' => intval($bdid),
					'dHash' => sql_function('x\'' . ($bdid ? '00' : md5($dat)) . '\''),
				);

				$key = $v['type'] . '_' . $k;
				if(isset($replace[$key])){
					$cid = $replace[$key];
					$data['ID'] = $cid;
					unset($replace[$key]);
				} else {
					$cid = 0;
				}
				$this->DB_WE->query('REPLACE INTO ' . CONTENT_TABLE . ' SET ' . we_database_base::arraySetter($data));
				$cid = $cid ? : $this->DB_WE->getInsertId();
				$this->elements[$k]['id'] = $cid; // update Object itself
				if(!$cid || !$this->DB_WE->query('REPLACE INTO ' . LINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
							'DID' => $this->ID,
							'CID' => $cid,
							'Name' => $k,
							'Type' => $v['type'],
							'nHash' => sql_function('x\'' . md5($k) . '\''),
							'DocumentTable' => stripTblPrefix($this->Table)
						))
					)){
					//this should never happen
					return false;
				}
			}
		}

		if($replace){
			$replace = implode(',', $replace);

			$this->DB_WE->query('DELETE l,c FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON c.ID=l.CID WHERE l.DID=' . $this->ID . ' AND l.DocumentTable="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '" AND l.CID IN(' . $replace . ')');
		}
		return true;
	}

	protected function i_getPersistentSlotsFromDB($felder = '*'){
		parent::i_getPersistentSlotsFromDB($felder);
		$this->ParentPath = $this->getParentPath();
	}

	protected function i_areVariantNamesValid(){
		return true;
	}

	protected function i_canSaveDirinDir(){
		return true;
	}

	protected function i_sameAsParent(){
		return false;
	}

	protected function i_filenameEmpty(){
		return ($this->Filename === '');
	}

	protected function i_pathNotValid(){
		if(strpos($this->ParentPath, '..') !== false || ($this->ParentPath && $this->ParentPath{0} != '/')){
			return true;
		}
		if(($ws = get_ws($this->Table, true))){ //	doc has workspaces
			if(!(we_users_util::in_workspace($this->ParentID, $ws, $this->Table, $GLOBALS['DB_WE']))){
				return true;
			}
		}
		return false;
	}

	protected function i_filenameNotValid(){
		return we_base_file::we_filenameNotValid($this->Filename, $this->getElement('Charset') != 'UTF-8');
	}

	protected function i_filenameNotAllowed(){
		if($this->Table == FILE_TABLE && $this->ParentID == 0 && strtolower($this->Filename . (isset($this->Extension) ? $this->Extension : '')) === 'webedition'){
			return true;
		}
		if(substr(strtolower($this->Filename . (isset($this->Extension) ? $this->Extension : '')), -1) === '.'){
			return true;
		}
		return false;
	}

	protected function i_fileExtensionNotValid(){
		if(isset($this->Extension)){
			$ext = ltrim($this->Extension, '.');

			return !(preg_match('/^[a-zA-Z0-9]+$/iD', $ext) || !$ext);
		}
		return false;
	}

	protected function i_filenameDouble(){
		return f('SELECT 1 FROM `' . $this->DB_WE->escape($this->Table) . '` WHERE ParentID=' . intval($this->ParentID) . ' AND Filename="' . $this->DB_WE->escape($this->Filename) . '" AND ID!=' . intval($this->ID), '', $this->DB_WE);
	}

	protected function i_urlDouble(){
		return false;
	}

	protected function i_triggerIdNotValdid(){
		return false;
	}

	protected function i_triggerDocNotDynamic(){
		return false;
	}

	### check if ParentPath is diffrent as ParentID, so we need to look what ParentID it is.
	### If it donesn't exists we have to create the folders (for auto Date-Folder Names)

	function i_checkPathDiffAndCreate(){
		if($this->getParentPath() != $this->ParentPath && $this->ParentPath != '' && $this->ParentPath != '/'){
			if(!$this->IsTextContentDoc || !$this->DocType){
				return false;
			}
			$doctype = new we_docTypes();
			$doctype->initByID($this->DocType, DOC_TYPES_TABLE);
			if(!$doctype->SubDir){
				return false;
			}
			$pathFirstPart = substr($this->getParentPath(), -1) === '/' ? '' : '/';
			$tail = '';
			switch($doctype->SubDir){
				case self::SUB_DIR_YEAR:
					$tail = $pathFirstPart . date('Y');
					break;
				case self::SUB_DIR_YEAR_MONTH:
					$tail = $pathFirstPart . date('Y') . '/' . date('m');
					break;
				case self::SUB_DIR_YEAR_MONTH_DAY:
					$tail = $pathFirstPart . date('Y') . '/' . date('m') . '/' . date('d');
					break;
			}
			if($this->getParentPath() . $tail != $this->ParentPath){
				return false;
			}

			$this->ParentID = $this->getParentIDFromParentPath();
			$this->Path = $this->getPath();
		}
		return ($this->ParentID != -1);
	}

	function i_correctDoublePath(){
		if($this->Filename){
			if(f('SELECT ID  FROM  ' . $this->DB_WE->escape($this->Table) . '  WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($this->Filename . (isset($this->Extension) ? $this->Extension : '')) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)){
				$z = 0;
				$footext = $this->Filename . '_' . $z . (isset($this->Extension) ? $this->Extension : '');
				while(f('SELECT ID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($footext) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)){
					$z++;
					$footext = $this->Filename . '_' . $z . (isset($this->Extension) ? $this->Extension : '');
				}
				$this->Text = $footext;
				$this->Filename = $this->Filename . '_' . $z;
				$this->Path = $this->getParentPath() . (($this->getParentPath() != '/') ? '/' : '') . $this->Text;
			}
		} else {
			if(f('SELECT ID  FROM  ' . $this->DB_WE->escape($this->Table) . '  WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($this->Text) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)){
				$z = 0;
				$footext = $this->Text . '_' . $z;
				while(f('SELECT ID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($footext) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)){
					$z++;
					$footext = $this->Text . '_' . $z;
				}
				$this->Text = $footext;
				$this->Path = $this->getParentPath() . (($this->getParentPath() != '/') ? '/' : '') . $this->Text;
			}
		}
	}

	protected function i_check_requiredFields(){
		return ''; // overwrite
	}

	protected function i_scheduleToBeforeNow(){
		return false; // overwrite
	}

	function i_publInScheduleTable(){
		return false; // overwrite
	}

	protected function i_hasDoubbleFieldNames(){
		return false;
	}

	function we_resaveTemporaryTable(){
		return true;
	}

	function we_resaveMainTable(){
		$this->wasUpdate = true;
		return we_root::we_save(true, true);
	}

	public function we_rewrite(){
		return true;
	}

	protected function update_filehash(){

	}

	function parseTextareaFields(){

	}

	public function correctFields(){

	}

	function registerMediaLinks($temp = false){
		$this->MediaLinks = array_filter($this->MediaLinks, function($v){
			return $v && is_numeric($v);
		});

		// filter MediaLinks by media contenttype
		$verifiedIDs = [];
		if(!empty($this->MediaLinks)){
			$whereType = 'AND ContentType IN ("' . we_base_ContentTypes::APPLICATION . '","' . we_base_ContentTypes::FLASH . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::VIDEO . '")';
			$this->DB_WE->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', array_unique(array_values($this->MediaLinks))) . ') ' . $whereType);
			while($this->DB_WE->next_record()){
				$verifiedIDs[] = $this->DB_WE->f('ID');
			}
		}
		$this->MediaLinks = array_intersect($this->MediaLinks, $verifiedIDs);

		if(empty($this->MediaLinks)){
			return true;
		}

		$sets = [];
		foreach($this->MediaLinks as $k => $remObj){
			$sets[] = we_database_base::arraySetter(array(
					'ID' => $this->ID,
					'DocumentTable' => stripTblPrefix($this->Table),
					'type' => 'media',
					'remObj' => $remObj,
					'remTable' => stripTblPrefix(FILE_TABLE),
					'element' => !is_numeric($k) ? $k : '',
					'position' => 0,
					'isTemp' => $temp ? 1 : 0
					), ',', true);
		}
		if($sets){
			return $this->DB_WE->query('REPLACE INTO ' . FILELINK_TABLE . ' VALUES ' . implode(',', $sets));
		}

		return true;
	}

	function unregisterMediaLinks($delPublished = true, $delTemp = true){
		if($delPublished){
			$this->DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix($this->Table) . '" AND isTemp=0 AND type="media"');
		}
		if($delTemp){
			$this->DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix($this->Table) . '" AND isTemp=1 AND type="media"');
		}
	}

	/**
	 * @return	int
	 * @desc	checks if the user can modify a document, or only read the doc (only preview tab).
	  returns	 1	if doc is not restricted any rules
	  -1	if doc is not in workspace of user
	  -2	if doc is restricted and user has nor rights
	  -3	if doc is locked by another user
	  -4	if user has not the right to save a file.
	 */
	function userHasAccess(){
		$uid = $this->isLockedByUser();
		if($uid > 0 && $uid != $_SESSION['user']['ID'] && $GLOBALS['we_doc']->ID){ // file is locked
			$this->LockUser = $uid;
			$this->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
			return self::FILE_LOCKED;
		}
		if($this->LockUser != 0 && $this->LockUser != $_SESSION['user']['ID'] && $uid == 0){
			$this->we_load(self::LOAD_TEMP_DB);
		}

		if(!$this->userHasPerms()){ //	File is restricted !
			return self::USER_NO_PERM;
		}

		if(!$this->userCanSave()){ //	user has no right to save.
			return self::USER_NO_SAVE;
		}

		if($this instanceof we_object){
			if($this->RestrictUsers && !(we_users_util::isOwner($this->CreatorID) || we_users_util::isOwner($this->Users))){ //	user is creator of doc - all is allowed.
				return self::USER_NO_PERM;
			}
		} elseif(we_users_util::isOwner($this->CreatorID) || we_users_util::isOwner($this->Owners)){ //	user is creator/owner of doc - all is allowed.
			$this->lockDocument();
			$this->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
			return self::USER_HASACCESS;
		}

		//	access to doc is not restricted, check workspaces of user
		if($GLOBALS['we_doc']->ID){
			if(($ws = get_ws($GLOBALS['we_doc']->Table, true))){ //	doc has workspaces
				if(!(we_users_util::in_workspace($GLOBALS['we_doc']->ID, $ws, $GLOBALS['we_doc']->Table, $GLOBALS['DB_WE']))){
					return self::FILE_NOT_IN_USER_WORKSPACE;
				}
			}
		}
		$this->lockDocument();
		$this->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
		return self::USER_HASACCESS;
	}

	/**
	 * @return int
	 * @desc	checks if a file is locked by another user. returns that userID
	  or 0 when file is not locked
	 */
	function isLockedByUser(){
		//select only own ID if not in same session
		return intval(f('SELECT UserID FROM ' . LOCK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND tbl="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '" AND sessionID!="' . session_id() . '" AND lockTime>NOW()', '', $this->DB_WE));
	}

	function lockDocument(){
		if($_SESSION['user']['ID'] && $this->ID){ // only if user->id != 0
			//if lock is used by other user and time is up, update table
			$this->DB_WE->query('INSERT INTO ' . LOCK_TABLE . ' SET ID=' . intval($this->ID) . ',UserID=' . intval($_SESSION['user']['ID']) . ',tbl="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '",sessionID="' . session_id() . '",lockTime=NOW()+INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' SECOND
				ON DUPLICATE KEY UPDATE UserID=' . intval($_SESSION['user']['ID']) . ',sessionID="' . session_id() . '",lockTime= NOW() + INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' SECOND');
			$this->LockUser = intval($_SESSION['user']['ID']);
		}
	}

	/**
	 * Gets the navigation folders for the current document
	 *
	 * @return Array
	 */
	protected function getNavigationFoldersForDoc(){
		if($this->Table !== FILE_TABLE){
			return [];
		}
		$category = property_exists($this, 'Category') ? array_map('escape_sql_query', array_unique(array_filter(array_merge(explode(',', $this->Category), explode(',', $this->oldCategory))))) : '';
		$queries = array('(((Selection="' . we_navigation_navigation::SELECTION_STATIC . '" AND SelectionType="' . we_navigation_navigation::STYPE_DOCLINK . '") OR (IsFolder=1 AND SelectionType="' . we_navigation_navigation::STYPE_DOCLINK . '")) AND LinkID=' . intval($this->ID) . ')',
		);
		if(isset($this->DocType)){
			//FIXME: query should use ID, not parentID
			$queries[] = '((Selection="' . we_navigation_navigation::SELECTION_DYNAMIC . '") AND (DocTypeID="' . $this->DB_WE->escape($this->DocType) . '" OR FolderID=' . intval($this->ParentID) . '))';
		}
		if($category){
			//FIXME: query should use ID, not parentID
			$queries[] = '((Selection="' . we_navigation_navigation::SELECTION_DYNAMIC . '" AND SelectionType="' . we_navigation_navigation::STYPE_DOCTYPE . '") AND (FIND_IN_SET("' . implode('",Categories) OR FIND_IN_SET("', $category) . '",Categories)))';
		}
		return $this->DB_WE->getAllq('SELECT DISTINCT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ' . implode(' OR ', $queries), true);
	}

	public function insertAtIndex(array $only = null, array $fieldTypes = null){

	}

	/**
	 * Rewrites the navigation cache files
	 *
	 */
	function rewriteNavigation(){
		// rewrite filter
		if(defined('CUSTOMER_TABLE') && !empty($this->documentCustomerFilter)){
			we_navigation_customerFilter::updateByFilter($this->documentCustomerFilter, $this->ID, $this->Table);
		}

		$folders = $this->getNavigationFoldersForDoc();
		foreach($folders as $f){
			we_navigation_cache::delNavigationTree($f);
		}
	}

	public function revert_published(){

	}

	public function isBinary(){
		return false;
	}

	protected function isMoved(){
		return ($this->wasMoved = ($this->OldPath && ($this->Path != $this->OldPath)));
	}

	public function wasMoved(){
		return $this->wasMoved;
	}

	public function showLockedWarning($userID){
		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_tools::htmlDialogLayout('<p class="defaultfont">' . sprintf(g_l('alert', '[temporaere_no_access_text]'), $this->Text, f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID))) . '</p>', g_l('alert', '[temporaere_no_access]')) .
//	For SEEM-Mode
				($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
					'<span style="text-decoration:none" onclick="WE().layout.weNavigationHistory.navigateReload()" >' . g_l('SEEM', '[try_doc_again]') . '</span>' : '')
		));
		exit();
	}

//FIXME: make abstract
	public function getPropertyPage(){

	}

	public function checkFieldsOnSave(){
		if($this->i_pathNotValid()){
			return sprintf(g_l('weClass', '[notValidFolder]'), $this->Path);
		}
		if($this->i_filenameEmpty()){
			return g_l('weEditor', '[' . $this->ContentType . '][filename_empty]');
		}
		if(!$this->i_canSaveDirinDir()){
			return g_l('weEditor', '[pfolder_notsave]');
		}
		if($this->i_sameAsParent()){
			return g_l('weEditor', '[folder_save_nok_parent_same]');
		}
		if($this->i_fileExtensionNotValid()){
			return sprintf(g_l('weEditor', '[' . $this->ContentType . '][we_filename_notValid]'), $this->Path);
		}
		if($this->i_filenameNotValid()){
			return sprintf(g_l('weEditor', '[' . $this->ContentType . '][we_filename_notValid]'), $this->Path);
		}
		if($this->i_descriptionMissing()){
			return sprintf(g_l('weEditor', '[' . $this->ContentType . '][we_description_missing]'), $this->Path);
		}
		if($this->i_filenameNotAllowed()){
			return sprintf(g_l('weEditor', '[' . $this->ContentType . '][we_filename_notAllowed]'), $this->Path);
		}
		if($this->i_filenameDouble()){
			return sprintf(g_l('weEditor', '[' . $this->ContentType . '][response_path_exists]'), $this->Path);
		}
		if($this->i_urlDouble()){
			return sprintf(g_l('weEditor', '[' . $this->ContentType . '][we_objecturl_exists]'), $this->Url);
		}
		if($this->i_triggerIdNotValdid()){
			return g_l('weEditor', '[triggerIdNotValid]');
		}
		if($this->i_triggerDocNotDynamic()){
			return g_l('weEditor', '[triggerDocNotDynamic]');
		}
		if(!$this->i_checkPathDiffAndCreate()){
			return sprintf(g_l('weClass', '[notValidFolder]'), $this->Path);
		}
		if(($n = $this->i_check_requiredFields())){
			return sprintf(g_l('weEditor', '[required_field_alert]'), $n);
		}
		if($this->i_scheduleToBeforeNow()){
			return g_l('modules_schedule', '[toBeforeNow]');
		}
		if(($n = $this->i_hasDoubbleFieldNames())){
			return sprintf(g_l('weEditor', '[doubble_field_alert]'), $n);
		}
		if(!$this->i_areVariantNamesValid()){
			return g_l('weEditor', '[variantNameInvalid]');
		}
		return false;
	}

}
