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

	/* Icon which is shown at the tree-menue  */
	public $Icon = '';

	/* array which holds the content of the Object */
	var $elements = array();
	private $wasMoved = false;

	/* Number of the EditPage when editor() is called */
	public $EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
	var $CopyID;
	var $EditPageNrs = array();
	var $Owners = '';
	var $OwnersReadOnly = '';
	var $WebUserID = '';

	/* ID of the Autor who created the document */
	var $CreatorID = 0;

	/* ID of the user who last modify the document */
	var $ModifierID = 0;
	var $RestrictOwners = 0;
	protected $LockUser = 0;
	protected $LangLinks = array();

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->CreationDate = time();
		$this->ModDate = time();
		array_push($this->persistent_slots, 'OwnersReadOnly', 'ParentID', 'ParentPath', 'Text', 'Filename', 'Path', 'Filehash', 'OldPath', 'CreationDate', 'ModDate', 'RebuildDate', 'IsFolder', 'ContentType', 'Icon', 'elements', 'EditPageNr', 'CopyID', 'Owners', 'CreatorID', 'ModifierID', 'RestrictOwners', 'WebUserID', 'LockUser', 'LangLinks');
	}

	public function makeSameNew(){
		$ParentID = $this->ParentID;
		$ParentPath = $this->ParentPath;
		$EditPageNr = $this->EditPageNr;
		$tempDoc = $this->ClassName;
		$tempDoc = new $tempDoc();
		$tempDoc->we_new();
		foreach($tempDoc->persistent_slots as $name){
			$this->{$name} = isset($tempDoc->{$name}) ? $tempDoc->{$name} : '';
		}
		$this->InWebEdition = true;
		$this->ParentID = $ParentID;
		$this->ParentPath = $ParentPath;
		$this->EditPageNr = $EditPageNr;
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
		// do nothing, only in Folder-Classes this Function schould have code!!
	}

	function checkIfPathOk(){
		### check if Path has changed
		$Path = $this->getPath();
		if($Path != $this->Path){

			### check if Path exists in db
			if(f('SELECT Path FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE Path="' . $this->DB_WE->escape($Path) . '"', 'Path', $this->DB_WE)){
				$GLOBALS['we_responseText'] = sprintf(g_l('weClass', '[response_path_exists]'), $Path);
				return false;
			}
			$this->Path = $Path;
		}
		return true;
	}

	//FIXME: make this __sleep
	function saveInSession(&$save){
		$save = array(
			array(),
			$this->elements
		);
		foreach($this->persistent_slots as $slot){
			$bb = isset($this->{$slot}) ? $this->{$slot} : '';
			if(!is_object($bb)){
				$save[0][$slot] = $bb;
			} else {//FIXME: will this ever be restored???
				$save[0][$slot . '_class'] = serialize($bb);
			}
		}
		// save weDocumentCustomerFilter in Session
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$save[3] = serialize($this->documentCustomerFilter);
		}
	}

	function applyWeDocumentCustomerFilterFromFolder(){
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$_tmpFolder = new we_folder();
			$_tmpFolder->initByID($this->ParentID, $this->Table);
			$this->documentCustomerFilter = $_tmpFolder->documentCustomerFilter;

			if($this->IsFolder && $this->ID != 0){
				$this->ApplyWeDocumentCustomerFiltersToChilds = true;
			}
			unset($_tmpFolder);
		}
	}

	/* init the object with data from the database */

	function copyDoc(/* $id */){
		// overwrite
	}

######### Form functions for generating the html of the input fields ##########

	/* creates a text-input field for entering Data that will be stored at the $elements Array */

	/* creates the filename input-field */

	function formFilename($text = ''){
		return $this->formTextInput('', 'Filename', $text ? : g_l('weClass', '[filename]'), 24, 255);
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = 0, $rootDirID = 0, $table = '', $Pathname = 'ParentPath', $IDName = 'ParentID', $cmd = '', $showTitle = true){
		$yuiSuggest = &weSuggest::getInstance();

		if(!$table){
			$table = $this->Table;
		}
		$textname = 'we_' . $this->Name . '_' . $Pathname;
		$idname = 'we_' . $this->Name . '_' . $IDName;
		$path = $this->$Pathname;
		$myid = $this->$IDName;

		if($Pathname === 'ParentPath'){
			$_parentPathChanged = 'if(opener.pathOfDocumentChanged) { opener.pathOfDocumentChanged(); }';
			$_parentPathChangedBlur = 'if(pathOfDocumentChanged) { pathOfDocumentChanged(); }';
		} else {
			$_parentPathChanged = $_parentPathChangedBlur = '';
		}

		if($width){
			$_attribs['style'] = 'width: ' . $width . 'px';
		} else {
			$width = 0;
		}
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);" . $_parentPathChanged . str_replace('\\', '', $cmd));
		$button = we_html_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "')");

		$yuiSuggest->setAcId('Path', id_to_path(array($rootDirID), $table));
		$yuiSuggest->setContentType('folder,class_folder');
		$yuiSuggest->setInput($textname, $path, array('onblur' => $_parentPathChangedBlur));
		$yuiSuggest->setLabel(g_l('weClass', '[dir]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);
		return $yuiSuggest->getHTML();
	}

	function htmlTextInput_formDirChooser($attribs = array(), $addAttribs = array()){
		$_attribs = array(
			'class' => 'wetextinput',
			'size' => 30,
			'value' => '',
		);

		foreach($addAttribs as $key => $value){
			if(isset($_attribs[$key])){
				$_attribs[$key] .= $value;
			} else {
				$_attribs[$key] = $value;
			}
		}

		foreach($attribs as $key => $value){
			$_attribs[$key] = $value;
		}

		$_attribs['type'] = 'text';

		return getHtmlTag('input', $_attribs);
	}

	function formCreator($canChange, $width = 388){
		if(!$this->CreatorID){
			$this->CreatorID = 0;
		}
		$creator = $this->CreatorID ? id_to_path($this->CreatorID, USER_TABLE, $this->DB_WE) : g_l('weClass', '[nobody]');
		if(!$canChange){
			return $creator;
		}

		$textname = 'wetmp_' . $this->Name . '_CreatorID';
		$idname = 'we_' . $this->Name . '_CreatorID';

		$inputFeld = $this->htmlTextInput($textname, 24, $creator, '', ' readonly', '', $width);
		$idfield = $this->htmlHidden($idname, $this->CreatorID);
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$wecmdenc5 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);");
		$button = we_html_button::create_button('edit', "javascript:we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','user',document.forms[0].elements['" . $idname . "'].value,'" . $wecmdenc5 . "')");

		return we_html_tools::htmlFormElementTable($inputFeld, g_l('weClass', '[maincreator]'), 'left', 'defaultfont', $idfield, we_html_tools::getPixel(20, 4), $button);
	}

	function formRestrictOwners($canChange){
		if($canChange){
			$n = 'we_' . $this->Name . '_RestrictOwners';
			$v = $this->RestrictOwners ? true : false;
			return we_html_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass', '[limitedAccess]'), false, 'defaultfont', "setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');");
		}
		return '<table style="border-spacing: 0px;border-style:none;" cellpadding="0"><tr><td><img src="' . TREE_IMAGE_DIR . ($this->RestrictOwners ? 'check1_disabled.gif' : 'check0_disabled.gif') . '" /></td><td class="defaultfont">&nbsp;' . g_l('weClass', '[limitedAccess]') . '</td></tr></table>';
	}

	function formOwners($canChange = true){
		$owners = makeArrayFromCSV($this->Owners);
		$ownersReadOnly = $this->OwnersReadOnly ? unserialize($this->OwnersReadOnly) : array();

		$content = '<table style="border-spacing: 0px;border-style:none;width:370px;" cellpadding="0">' .
			'<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(351, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr>';
		if($owners){
			foreach($owners as $owner){
				$foo = getHash('SELECT ID,Path,Icon from ' . USER_TABLE . ' WHERE ID=' . intval($owner), $this->DB_WE);
				$icon = isset($foo['Icon']) ? TREE_ICON_DIR . $foo['Icon'] : TREE_ICON_DIR . 'user.gif';
				$_path = isset($foo['Path']) ? $foo['Path'] : '';
				$content .= '<tr><td><img src="' . $icon . '" width="16" height="18" /></td><td class="defaultfont">' . $_path . '</td><td>' .
					we_html_forms::checkboxWithHidden(isset($ownersReadOnly[$owner]) ? $ownersReadOnly[$owner] : '', 'we_owners_read_only[' . $owner . ']', g_l('weClass', '[readOnly]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);', !$canChange) .
					'</td><td>' . ($canChange ? we_html_button::create_button('image:btn_function_trash', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('users_del_owner','" . $owner . "');") : '') . '</td></tr>';
			}
		} else {
			$content .= '<tr><td><img src="' . TREE_ICON_DIR . "user.gif" . '" width="16" height="18" /></td><td class="defaultfont">' . g_l('weClass', '[onlyOwner]') . '</td><td></td><td></td></tr>';
		}
		$content .= '<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(351, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr></table>';

		$textname = 'OwnerNameTmp';
		$idname = 'OwnerIDTmp';
		$delallbut = we_html_button::create_button('delete_all', "javascript:we_cmd('users_del_all_owners','')", true, 0, 0, "", "", $this->Owners ? false : true);
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$wecmdenc5 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);opener.setScrollTo();fillIDs();opener.we_cmd('users_add_owner',top.allIDs);");
		$addbut = $canChange ?
			$this->htmlHidden($idname, '') . $this->htmlHidden($textname, '') . we_html_button::create_button('add', "javascript:we_cmd('browse_users','document.forms[\'we_form\'].elements[\'" . $idname . "\'].value','" . $wecmdenc2 . "','',document.forms['we_form'].elements['" . $idname . "'].value,'" . $wecmdenc5 . "','','',1);") : "";

		$content = '<table style="border-spacing: 0px;border-style:none;width:500px;" cellpadding="0">
<tr><td><div class="multichooser">' . $content . '</div></td></tr>
' . ($canChange ? '<tr><td align="right">' . we_html_tools::getPixel(2, 8) . '<br/>' . we_html_button::create_button_table(array($delallbut, $addbut)) . '</td></tr>' : "") . '</table>';

		return we_html_tools::htmlFormElementTable($content, g_l('weClass', '[otherowners]'), 'left', 'defaultfont');
	}

	function formCreatorOwners(){
		$canChange = ((!$this->ID) || we_users_util::isUserInUsers($_SESSION['user']['ID'], $GLOBALS['we_doc']->CreatorID));

		return '<table style="border-spacing: 0px;border-style:none;" cellpadding="0">
<tr><td class="defaultfont">' . $this->formCreator($canChange && permissionhandler::hasPerm('CHANGE_DOCUMENT_OWNER'), 388) . '</td></tr>
<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
<tr><td>' . $this->formRestrictOwners($canChange && permissionhandler::hasPerm('CHANGE_DOCUMENT_PERMISSION')) . '</td></tr>' .
			($this->RestrictOwners ?
				'<tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>
<tr><td>' . $this->formOwners($canChange && permissionhandler::hasPerm('CHANGE_DOCUMENT_PERMISSION')) . '</td></tr>' : '') .
			'</table>';
	}

	function del_all_owners(){
		$this->Owners = '';
	}

	function add_owner($id){
		$owners = makeArrayFromCSV($this->Owners);
		$ids = is_array($id) ? $id : explode(',', $id);
		foreach($ids as $id){
			if($id && (!in_array($id, $owners))){
				$owners[] = $id;
			}
		}
		$this->Owners = makeCSVFromArray($owners, true);
	}

	function del_owner($id){
		$owners = makeArrayFromCSV($this->Owners);
		if(in_array($id, $owners)){
			$pos = array_search($id, $owners);
			if($pos !== false || $pos == '0'){
				unset($owners[$pos]);
			}
		}
		$this->Owners = makeCSVFromArray($owners, true);
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

	function userCanSave(){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(defined('OBJECT_TABLE') && ($this->Table == OBJECT_FILES_TABLE)){
			if(!(permissionhandler::hasPerm('NEW_OBJECTFILE_FOLDER') || permissionhandler::hasPerm('NEW_OBJECTFILE'))){
				return false;
			}
		} else {
			if(!permissionhandler::hasPerm('SAVE_DOCUMENT_TEMPLATE')){
				return false;
			}
		}
		if(!$this->RestrictOwners){
			return true;
		}
		if(!$this->userHasPerms()){
			return false;
		}
		$ownersReadOnly = $this->OwnersReadOnly ? unserialize($this->OwnersReadOnly) : array();
		$readers = array();
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1){
				$readers[] = $key;
			}
		}
		return !we_users_util::isUserInUsers($_SESSION['user']['ID'], $readers);
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('copyDocument', currentID);");
		$but = we_html_button::create_button("select", "javascript:we_cmd('openDocselector', document.forms[0].elements['" . $idname . "'].value,'" . $this->Table . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','','0','" . $this->ContentType . "',1);");

		return $this->htmlHidden($idname, $this->CopyID) . $but;
	}

	# return html code for button and field to select user
	# ATTENTION !!: You have to have we_cmd function in your file and browse_user section

	#
	function formUserChooser($old_userID = -1, $width = '', $in_textname = '', $in_idname = ''){
		$textname = $in_textname ? : 'we_' . $this->Name . '_UserName';
		$idname = $in_idname ? : 'we_' . $this->Name . '_UserID';

		$username = '';
		$userid = $old_userID;
		if(intval($userid) > 0){
			$username = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), 'username', $this->DB_WE);
		}

		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		return we_root::htmlFormElementTable(we_root::htmlTextInput($textname, 30, $username, '', ' readonly', 'text', $width, 0), 'User', 'left', 'defaultfont', we_root::htmlHidden($idname, $userid), we_html_tools::getPixel(20, 4), we_html_button::create_button('select', "javascript:we_cmd('browse_users','document.forms['we_form'].elements['" . $idname . "'].value','" . $wecmdenc2 . "','user')"));
	}

	//FIXME: this should be a general selector
	protected function formDocChooser($width, $name, $type = 'txt', $selector = weSuggest::DocSelector, $table = FILE_TABLE){
		$yuiSuggest = &weSuggest::getInstance();
		$textname = $this->Name . '_' . $name;
		$idname = 'we_' . $this->Name . '_' . $type . '[' . $name . '#bdid]';
		$myid = $this->getElement($name, 'bdid');
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), '', $this->DB_WE);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
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
		$yuiSuggest->setSelectButton(we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::IMAGE . "',1)"));
		$yuiSuggest->setTrashButton(we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputTriggerID');_EditorFrame.setEditorIsHot(true);", true, 27, 22));
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
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
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
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
		$yuiSuggest->setSelectButton(we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','text/webedition',1)"));
		$yuiSuggest->setTrashButton(we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputTriggerID');_EditorFrame.setEditorIsHot(true);", true, 27, 22));
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
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
			$yuiSuggest->setContentType('folder,objectFile');
			$ctype = 'objectFile';
			$etype = OBJECT_FILES_TABLE;
		}
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd('opener._EditorFrame.setEditorIsHot(true);');

		$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','" . $ctype . "',1)");
		$trashButton = we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $idname . "'].value='-1';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInput" . $ackeyshort . "');_EditorFrame.setEditorIsHot(true);", true, 27, 22);
		$openbutton = we_html_button::create_button("image:edit_edit", "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . $ctype . "','" . $etype . "'); }");
		if(isset($this->DocType) && $this->DocType && permissionhandler::hasPerm("NEW_WEBEDITIONSITE")){
			$db = new DB_WE();
			$LDcoType = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblDocTypes" AND DID=' . $this->DocType . ' AND Locale="' . $db->escape($langkey) . '"', '', $db);
			if($LDcoType){
				$createbutton = we_html_button::create_button("image:add_doc", "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $LDcoType . "');");
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
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setTrashButton($trashButton);
		$yuiSuggest->setOpenButton($openbutton);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
		return $yuiSuggest->getHTML();
	}

	function formLangLinks($withHeadline = true){
		we_loadLanguageConfig();
		$_defLang = self::getDefaultLanguage();
		$value = ($this->Language ? : $_defLang);
		$inputName = 'we_' . $this->Name . '_Language';
		$_languages = getWeFrontendLanguagesForBackend();
		$_headline = ($withHeadline ? '<tr><td class="defaultfont">' . g_l('weClass', '[language]') . '</td></tr>' : '');

		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			foreach($_languages as $langkey => $lang){
				$divname = 'we_' . $this->Name . '_LanguageDocDiv[' . $langkey . ']';
				$LDID = isset($this->LangLinks[$langkey]['id']) && $this->LangLinks[$langkey]['id'] && $this->LangLinks[$langkey]['id'] !== -1 ? $this->LangLinks[$langkey]['id'] : 0;
				$path = $LDID ? $this->LangLinks[$langkey]['ipath'] : '';

				$htmlzw.= '<div id="' . $divname . '" ' . ($this->Language == $langkey ? ' style="display:none" ' : '') . '>' . $this->formInputLangLink($lang, $langkey, $LDID, $path) . '</div>';
				$langkeys[] = $langkey;
			}
			return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	' . $_headline . '
	<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);", 'onchange' => "dieWerte='" . implode(',', $langkeys) . "';showhideLangLink('we_" . $this->Name . "_LanguageDocDiv',dieWerte,this.options[this.selectedIndex].value);_EditorFrame.setEditorIsHot(true);"), "value", 508) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
	<tr><td class="defaultfont" align="left">' . g_l('weClass', '[languageLinks]') . '</td></tr>
</table>
<br/>' . $htmlzw; //.we_html_tools::htmlFormElementTable($htmlzw,g_l('weClass','[languageLinksDefaults]'),"left",	"defaultfont");	dieWerte=\''.implode(',',$langkeys).'\'; disableLangDefault(\'we_'.$this->Name.'_LangDocType\',dieWerte,this.options[this.selectedIndex].value);"
		} else {
			return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	' . $_headline . '
	<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);", 'onchange' => "_EditorFrame.setEditorIsHot(true);"), "value", 508) . '</td></tr>
</table>';
		}
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
				return (isset($this->elements[$name]['bdid']) && $this->elements[$name]['bdid'] ?
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
		$Tree = new weMainTree('webEdition.php', 'top', 'self.Tree', 'top.load');
		return $Tree->getJSUpdateTreeScript($this, $select);
	}

	/** returns the Path dynamically (use it, when the class-variable Path is not set)  */
	function getPath(){
		return rtrim($this->getParentPath(), '/') . '/' . ( isset($this->Filename) ? $this->Filename : '' ) . ( isset($this->Extension) ? $this->Extension : '' );
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

	/* get the Real-Path of the Object (Server-Path) */

	public function getRealPath($old = false){
		return (($this->Table == FILE_TABLE) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . ($old ? $this->OldPath : $this->getPath());
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
		list($_userLanguage) = explode('_', isset($_SESSION['prefs']['Language']) ? $_SESSION['prefs']['Language'] : '');

// trying to get locale string out of interface language
		$_key = array_search($_userLanguage, getWELangs());

		$_defLang = $GLOBALS['weDefaultFrontendLanguage'];

// if default language is not equal with frontend language
		if(substr($_defLang, 0, strlen($_key)) !== $_key){
// get first language that fits
			foreach(getWeFrontendLanguagesForBackend() as $_k => $_v){
				$_parts = explode('_', $_k);
				if($_parts[0] === $_key){
					$_defLang = $_k;
				}
			}
		}
		return $_defLang;
	}

	function editor(){

	}

	function getParentIDFromParentPath(){
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
		if(isset($this->ContentType) && $this->ContentType){
			$this->Icon = we_base_ContentTypes::inst()->getIcon($this->ContentType);
		}
		$this->ParentPath = $this->getParentPath();
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);

		$this->i_getContentData();
		$this->i_getLangLinks();
		$this->OldPath = $this->Path;
	}

	public function we_save($resave = false){
		//$this->i_setText;
		if($this->PublWhenSave){
			$this->Published = time();
		}
		if(!$resave){
			$this->ModDate = time();
			$this->ModifierID = !isset($GLOBALS['we']['Scheduler_active']) && isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;
		}
		$this->RebuildDate = time();
		if(!parent::we_save($resave)){
			return false;
		}
		$this->update_filehash();
		$a = $this->i_saveContentDataInDB();
		if(!$resave && !($this instanceof we_class_folder)){
			we_history::insertIntoHistory($this);
		}
		return $a;
	}

	/**
	 * resave weDocumentCustomerFilter
	 *
	 */
	function resaveWeDocumentCustomerFilter(){
		if(isset($this->documentCustomerFilter) && $this->documentCustomerFilter){
			we_customer_documentFilter::saveForModel($this);
		}
	}

	protected function i_getDefaultFilename(){
		return f('SELECT MAX(ID) FROM ' . $this->DB_WE->escape($this->Table), '', $this->DB_WE) + 1;
	}

	public function we_initSessDat($sessDat){//FIXME: use __wakeup
		parent::we_initSessDat($sessDat);
		if(is_array($sessDat)){
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

	//FIXME: make this __wakeup
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
				if(we_base_request::_(we_base_request::STRINGC, 'we_cmd', '', 7) === 'save_document'){
					return true;
				}
				break;
		}

		$regs = array();
		if($_REQUEST){
			$dates = array();
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
					$this->OwnersReadOnly = serialize($v);
				}
			}
			foreach($dates as $k => $v){
				$this->setElement($k, mktime($dates[$k]['hour'], $dates[$k]['minute'], 0, $dates[$k]['month'], $dates[$k]['day'], $dates[$k]['year']), 'date');
			}
		}
		$this->ParentPath = $this->getParentPath();
		$this->Path = $this->getPath();
	}

	protected function i_isElement(/* $Name */){
		return true; // overwrite
	}

	protected function i_getContentData(){
		$this->DB_WE->query('SELECT * FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON c.ID=l.CID WHERE l.DID=' . intval($this->ID) . ' AND l.DocumentTable="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '"');
		$filter = array('Name', 'DID', 'Ord');
		while($this->DB_WE->next_record()){
			$Name = $this->DB_WE->f('Name');
			$type = $this->DB_WE->f('Type');

			if($type === 'formfield'){ // Artjom garbage fix!
				$this->elements[$Name] = unserialize($this->DB_WE->f('Dat'));
			} elseif($this->i_isElement($Name)){
				foreach($this->DB_WE->Record as $k => $v){
					if(!in_array($k, $filter) && !is_numeric($k)){
						$this->elements[$Name][strtolower($k)] = $v;
					}
				}
				$this->elements[$Name]['table'] = CONTENT_TABLE;
			}
		}
	}

	protected function i_getLangLinks($isFolder = false, $isObject = false){
		we_loadLanguageConfig();
		$_languages = getWeFrontendLanguagesForBackend();

		$langkeys = array_keys($_languages);
		if(LANGLINK_SUPPORT){
			$documentTable = $isObject && !$isFolder ? stripTblPrefix(OBJECT_FILES_TABLE) : stripTblPrefix(FILE_TABLE);
			$this->DB_WE->query('SELECT Locale,LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="' . $documentTable . '" AND IsObject=' . intval($isObject) . ' AND DID=' . intval($this->ID) . ' AND Locale IN ("' . implode('","', $langkeys) . '")');
			$tmpIDs = $this->DB_WE->getAllFirst(false);

			$tmpPaths = id_to_path($tmpIDs, $this->Table, null, false, true);
			foreach($langkeys as $langkey){
				$this->LangLinks[$langkey] = isset($tmpIDs[$langkey]) ? array('id' => $tmpIDs[$langkey], 'path' => $tmpPaths[$tmpIDs[$langkey]]) :
					array('id' => 0, 'path' => '');
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
			if($this->i_isElement($k)){
				if((!isset($v['type']) || $v['type'] != 'vars') && (( isset($v['dat']) && $v['dat'] != '' ) || (isset($v['bdid']) && $v['bdid']) || (isset($v['ffname']) && $v['ffname']))){

					$tableInfo = $this->DB_WE->metadata(CONTENT_TABLE);
					$data = array();
					foreach($tableInfo as $t){
						$fieldName = $t['name'];
						$val = isset($v[strtolower($fieldName)]) ? $v[strtolower($fieldName)] : '';
						if($k === 'data' && $this->isBinary()){
							break;
						}
						if($fieldName === 'Dat' && (isset($v['ffname']) && $v['ffname'])){
							$v['type'] = 'formfield';
							$val = serialize($v);
							// Artjom garbage fix
						}

						if(!isset($v['type']) || !$v['type']){
							$v['type'] = 'txt';
						}
						if($v['type'] === 'date'){
							$val = sprintf('%016d', $val);
						}
						if($fieldName != 'ID'){
							$data[$fieldName] = is_array($val) ? serialize($val) : $val;
						}
					}
					if($data){
						$data = we_database_base::arraySetter($data);
						$key = $v['type'] . '_' . $k;
						if(isset($replace[$key])){
							$cid = $replace[$key];
							$data.=',ID=' . $cid;
							unset($replace[$key]);
						} else {
							$cid = 0;
						}
						$this->DB_WE->query('REPLACE INTO ' . CONTENT_TABLE . ' SET ' . $data);
						$cid = $cid ? : $this->DB_WE->getInsertId();
						$this->elements[$k]['id'] = $cid; // update Object itself
						if(!$cid || !$this->DB_WE->query('REPLACE INTO ' . LINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
									'DID' => $this->ID,
									'CID' => $cid,
									'Name' => $k,
									'Type' => $v["type"],
									'DocumentTable' => stripTblPrefix($this->Table)
								))
							)){
							//this should never happen
							return false;
						}
					}
				}
			}
		}

		if(($replace = implode(',', $replace))){
			$this->DB_WE->query('DELETE FROM ' . LINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '" AND CID IN(' . $replace . ')');
			$this->DB_WE->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (' . $replace . ')');
		}
		return true;
	}

	protected function i_getPersistentSlotsFromDB($felder = '*'){
		parent::i_getPersistentSlotsFromDB($felder);
		$this->ParentPath = $this->getParentPath();
	}

	function i_areVariantNamesValid(){
		return true;
	}

	function i_canSaveDirinDir(){
		return true;
	}

	function i_sameAsParent(){
		return false;
	}

	function i_filenameEmpty(){
		return ($this->Filename === '');
	}

	function i_pathNotValid(){
		return strpos($this->ParentPath, '..') !== false || ($this->ParentPath && $this->ParentPath{0} != '/');
	}

	function i_filenameNotValid(){
		return we_base_file::we_filenameNotValid($this->Filename, $this->getElement('Charset') != 'UTF-8');
	}

	function i_filenameNotAllowed(){
		if($this->Table == FILE_TABLE && $this->ParentID == 0 && strtolower($this->Filename . (isset($this->Extension) ? $this->Extension : '')) === 'webedition'){
			return true;
		}
		if(substr(strtolower($this->Filename . (isset($this->Extension) ? $this->Extension : '')), -1) === '.'){
			return true;
		}
		return false;
	}

	function i_fileExtensionNotValid(){
		if(isset($this->Extension)){
			$ext = (substr($this->Extension, 0, 1) === '.' ?
					substr($this->Extension, 1) :
					$this->Extension);

			return !(preg_match('/^[a-zA-Z0-9]+$/iD', $ext) || !$ext);
		}
		return false;
	}

	function i_filenameDouble(){
		return f('SELECT 1 FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Filename="' . $this->DB_WE->escape($this->Filename) . '" AND ID!=' . intval($this->ID), '', $this->DB_WE);
	}

	function i_urlDouble(){
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
			if(empty($doctype->SubDir)){
				return false;
			}
			$_pathFirstPart = substr($this->getParentPath(), -1) === '/' ? '' : '/';
			$tail = '';
			switch($doctype->SubDir){
				case self::SUB_DIR_YEAR:
					$tail = $_pathFirstPart . date('Y');
					break;
				case self::SUB_DIR_YEAR_MONTH:
					$tail = $_pathFirstPart . date('Y') . '/' . date('m');
					break;
				case self::SUB_DIR_YEAR_MONTH_DAY:
					$tail = $_pathFirstPart . date('Y') . '/' . date('m') . '/' . date('d');
					break;
			}
			if($this->getParentPath() . $tail != $this->ParentPath){
				return false;
			}


			$this->ParentID = $this->getParentIDFromParentPath();
			$this->Path = $this->getPath();
		}
		if($this->ParentID == -1){
			return false;
		}
		return true;
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

	function i_check_requiredFields(){
		return ''; // overwrite
	}

	function i_scheduleToBeforeNow(){
		return false; // overwrite
	}

	function i_publInScheduleTable(){
		return false; // overwrite
	}

	function i_hasDoubbleFieldNames(){
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

	public function we_republish(){
		return true;
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
		} elseif($this->LockUser != 0 && $this->LockUser != $_SESSION['user']['ID'] && $uid == 0){
			$this->we_load(self::LOAD_TEMP_DB);
		}

		if(!$this->userHasPerms()){ //	File is restricted !!!!!
			return self::USER_NO_PERM;
		}

		if(!$this->userCanSave()){ //	user has no right to save.
			return self::USER_NO_SAVE;
		}

		if($this instanceof we_object){
			if($this->RestrictUsers && !(we_users_util::isOwner($this->CreatorID) || we_users_util::isOwner($this->Users))){ //	user is creator of doc - all is allowed.
				return self::USER_NO_PERM;
			}
		} else {
			if(we_users_util::isOwner($this->CreatorID) || we_users_util::isOwner($this->Owners)){ //	user is creator/owner of doc - all is allowed.
				$this->lockDocument();
				$this->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
				return self::USER_HASACCESS;
			}
		}

		if($this->userHasPerms()){ //	access to doc is not restricted, check workspaces of user
			if($GLOBALS['we_doc']->ID){ //	userModule installed
				if(($ws = get_ws($GLOBALS['we_doc']->Table, false, true))){ //	doc has workspaces
					if(!(in_workspace($GLOBALS['we_doc']->ID, $ws, $GLOBALS['we_doc']->Table, $GLOBALS['DB_WE']))){
						return self::FILE_NOT_IN_USER_WORKSPACE;
					}
				}
			}
			$this->lockDocument();
			$this->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
			return self::USER_HASACCESS;
		}
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
			return array();
		}
		$category = property_exists($this, 'Category') ? array_map('escape_sql_query', array_unique(array_filter(array_merge(explode(',', $this->Category), explode(',', $this->oldCategory))))) : '';
		$queries = array('(((Selection="' . we_navigation_navigation::SELECTION_STATIC . '" AND SelectionType="' . we_navigation_navigation::STPYE_DOCLINK . '") OR (IsFolder=1 AND FolderSelection="' . we_navigation_navigation::STPYE_DOCLINK . '")) AND LinkID=' . intval($this->ID) . ')',
		);
		if(isset($this->DocType)){
			//FIXME: query should use ID, not parentID
			$queries[] = '((Selection="' . we_navigation_navigation::SELECTION_DYNAMIC . '") AND (DocTypeID="' . $this->DB_WE->escape($this->DocType) . '" OR FolderID=' . intval($this->ParentID) . '))';
		}
		if($category){
			//FIXME: query should use ID, not parentID
			$queries[] = '((Selection="' . we_navigation_navigation::SELECTION_DYNAMIC . '" AND SelectionType="' . we_navigation_navigation::STPYE_DOCTYPE . '") AND (FIND_IN_SET("' . implode('",Categories) OR FIND_IN_SET("', $category) . '",Categories)))';
		}
		$this->DB_WE->query('SELECT DISTINCT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ' . implode(' OR ', $queries));
		return $this->DB_WE->getAll(true);
	}

	public function insertAtIndex(array $only = null, array $fieldTypes = null){

	}

	/**
	 * Rewrites the navigation cache files
	 *
	 */
	function rewriteNavigation(){
		// rewrite filter
		if(defined('CUSTOMER_TABLE') && isset($this->documentCustomerFilter) && $this->documentCustomerFilter != false){
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
		$this->wasMoved = ($this->OldPath && ($this->Path != $this->OldPath));
		return $this->wasMoved;
	}

	public function wasMoved(){
		return $this->wasMoved;
	}

	public function showLockedWarning($userID){
		echo we_html_tools::getHtmlTop() . STYLESHEET .
		we_html_element::jsElement('top.toggleBusy(0);') .
		'</head>' . we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_tools::htmlDialogLayout('<p class="defaultfont">' . sprintf(g_l('alert', '[temporaere_no_access_text]'), $this->Text, f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID))) . '</p>', g_l('alert', '[temporaere_no_access]')) .
//	For SEEM-Mode
			($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
				'<a href="javascript://" style="text-decoration:none" onclick="top.weNavigationHistory.navigateReload()" >' . g_l('SEEM', '[try_doc_again]') . '</a>' : '')
		) .
		'</html>';
		exit();
	}

}
