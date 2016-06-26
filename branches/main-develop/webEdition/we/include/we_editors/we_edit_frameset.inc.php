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
we_html_tools::protect();

/**
 * Searches for the first Page in the editor, which the user is allowed to see.
 * If he can see the given Nr, then that page will be shown.
 *
 * @see     getFirstValidEditPageNr
 *
 * @param   doc         string
 * @param   EditPageNr  string

 * @return   string
 */
function getFirstValidEditPageNr($doc, $EditPageNr){
	if($doc->isValidEditPage($EditPageNr) && permissionhandler::isUserAllowedForAction('switch_edit_page', $EditPageNr)){
		return $EditPageNr;
	}
	//	bugfix for new tag: we:hidePages
	foreach(array_keys($doc->EditPageNrs) as $key){
		//  the command in this case is swith_edit_page, because in this funtion
		//  the editor tries to select a certain edit_page
		//  in some cases it must switch it
		if(permissionhandler::isUserAllowedForAction('switch_edit_page', $doc->EditPageNrs[$key])){
			return $doc->EditPageNrs[$key];
		}
	}
	return -1;
}

function getTabs($classname, $predefined = 0){
	$ret = $predefined;
	$documentClasses = array('we_webEditionDocument', 'we_htmlDocument', 'we_flashDocument', 'we_imageDocument', 'we_otherDocument', 'we_textDocument', 'we_objectFile');
	// Check which tab the user can see
	if(in_array($classname, $documentClasses)){
		$ret = getFirstValidEditPageNr($GLOBALS['we_doc'], $predefined);
	}
	return $ret;
}

$we_Table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 1);
$we_ID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
$we_ContentType = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3) ? : ($we_ID ? f('SELECT ContentType FROM ' . $GLOBALS['DB_WE']->escape($we_Table) . ' WHERE ID=' . $we_ID) : '');

if(isset($_SESSION['weS']['we_data'][$we_transaction])){
	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
}

// init document
$we_doc = we_document::initDoc($we_ContentType, (isset($we_dt) ? $we_dt : ''), $we_ID, $we_Table);
if(!$we_doc->fileExists){
	include(WE_INCLUDES_PATH . 'weInfoPages/weNoResource.inc.php');
	exit();
}

switch($we_Table){
	case TEMPLATES_TABLE:
		$needPerm = 'CAN_SEE_TEMPLATES';
		break;
	case FILE_TABLE:
		$needPerm = 'CAN_SEE_DOCUMENTS';
		break;
	case VFILE_TABLE:
		$needPerm = 'CAN_SEE_COLLECTIONS';
		break;
	default:
		$needPerm = '';
}
if($needPerm && !permissionhandler::hasPerm($needPerm)){
	include(WE_INCLUDES_PATH . 'weInfoPages/weNoPerms.inc.php');
	exit();
}

$we_doc->InWebEdition = true;
//$we_doc->i_loadNavigationItems();
//	check template for hidePages
$we_doc->setDocumentControlElements();

//	in SEEM-Mode the first page is the preview page.
//	when editing an image-document we go to edit page
if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
	if(we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include') && $we_doc->userHasAccess() == we_root::USER_HASACCESS){ //	Open seem_edit_include pages in edit-mode
		$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_CONTENT;
		$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
	} elseif($we_doc instanceof we_imageDocument){
		$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_CONTENT;
		$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
	} else {
		$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PREVIEW;
		$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_PREVIEW;
	}
}

//  This code was over the comment: init document !!!!!!! (line 82?)
if(!$we_ID){
	$_SESSION['weS']['EditPageNr'] = getTabs('we_webEditionDocument', we_base_constants::WE_EDITPAGE_PROPERTIES);
}

if(($tid = we_base_request::_(we_base_request::INT, 'we_cmd', false, 10)) !== false && ($we_Table == FILE_TABLE) && ($we_ContentType === we_base_ContentTypes::WEDOCUMENT)){
	$we_doc->setTemplateID($tid);
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, 1);
}

//predefine ParentPath
if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'new_document' && ($pid = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 5)) && $we_doc->ParentID == 0){
	if($we_doc->ContentType == we_base_ContentTypes::FOLDER){
		$we_doc->setParentID($pid);
	}
}


if(($doct = we_base_request::_(we_base_request::INT, 'we_cmd', false, 8)) !== false && ($we_Table === FILE_TABLE) && ($we_ContentType === we_base_ContentTypes::WEDOCUMENT)){
	$we_doc->changeDoctype($doct);
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, 1);
} else if($doct !== false && (defined('OBJECT_FILES_TABLE') && $we_Table == OBJECT_FILES_TABLE) && ($we_ContentType === we_base_ContentTypes::OBJECT_FILE)){
	$we_doc->TableID = $doct;
	$we_doc->setRootDirID(true);
	$we_doc->restoreDefaults();
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, we_base_constants::WE_EDITPAGE_CONTENT);
}


if($we_doc->ID){
	if(($ws = get_ws($we_Table, true))){
		if(!(we_users_util::in_workspace($we_doc->ID, $ws, $we_Table, $DB_WE))){
			switch($we_Table){
				case TEMPLATES_TABLE: //	different workspace. for template
					$we_message = g_l('alert', '[' . ($we_ContentType === we_base_ContentTypes::FOLDER) ? 'folder' : $we_Table . '][not_im_ws]');
					include(WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
					exit();
				case FILE_TABLE: //	only preview mode allowed for docs
					//	MUST change to Preview-Mode
					$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PREVIEW;
					break;
			}
		}
	}
	$access = $we_doc->userHasAccess();
	if(($access !== we_root::USER_HASACCESS && $access !== we_root::FILE_LOCKED)){ //   user has no access to object/document - bugfix #2481
		if($we_ContentType != we_base_ContentTypes::OBJECT){
			$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PREVIEW;
		} else {
			include(WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
			exit();
		}
	}
}


if(isset($we_sess_folderID) && is_array($we_sess_folderID) && (!$we_doc->ID) && $we_sess_folderID[$we_doc->Table]){
	$we_doc->setParentID($we_sess_folderID[$we_doc->Table]);
}

if($we_doc->ID == 0){
	$we_doc->EditPageNr = getTabs($we_doc->ClassName, we_base_constants::WE_EDITPAGE_PROPERTIES);
} else if(isset($_SESSION['weS']['EditPageNr'])){
	if(defined('SHOP_TABLE')){
		$we_doc->checkTabs();
	}

	$we_doc->EditPageNr = (in_array($_SESSION['weS']['EditPageNr'], $we_doc->EditPageNrs) ?
			getTabs($we_doc->ClassName, $_SESSION['weS']['EditPageNr']) :
			//	Here we must get the first valid EDIT_PAGE
			getFirstValidEditPageNr($we_doc, we_base_constants::WE_EDITPAGE_CONTENT));
}

if($we_doc->EditPageNr === -1){ //	there is no view available for this document
	//	show errorMessage - no view for this document (we:hidePages)
	echo we_html_tools::getHtmlTop('', '', '', STYLESHEET, we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_tools::htmlDialogLayout(we_html_tools::htmlAlertAttentionBox(g_l('alert', '[no_views][description]'), we_html_tools::TYPE_ALERT, 500, true), g_l('alert', '[no_views][headline]'))
		)
	);
	exit;
}

if(!isset($we_doc->IsClassFolder) || !$we_doc->IsClassFolder){
	//update already offline users

	$userID = $we_doc->isLockedByUser(); //	Check if file is locked.
	$GLOBALS['DB_WE']->query('UPDATE ' . USER_TABLE . ' SET Ping=NULL WHERE Ping<(NOW()- INTERVAL ' . (we_base_constants::PING_TIME + we_base_constants::PING_TOLERANZ) . ' SECOND)');

	$filelocked = ($userID != 0 && $userID != $_SESSION['user']['ID']);

	if(!$filelocked){ // file can be edited
		//	#####	Lock the new file
		//	before lock - check if user can edit the file.
		if($we_doc->userHasAccess() == we_root::USER_HASACCESS){ //	only when user has access to file
			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL || $we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW){
				$we_doc->lockDocument();
			}
		}
	}

	if($we_doc->ContentType === we_base_ContentTypes::OBJECT_FILE && (!$we_doc->canMakeNew())){ // at this time only in objectFiles
		$we_message = g_l('alert', '[no_new][objectFile]');
		include(WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
		exit;
	}
}

// objects need to know the last webEdition Path, because of Workspaces
if($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT){
	$_SESSION['weS']['last_webEdition_document'] = array(
		'Path' => $we_doc->Path
	);
}

// get default code
if(!$we_doc->getElement('data')){
	$we_doc->setElement('data', ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE && ($cmd10 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 10)) ?
			base64_decode($cmd10) :
			we_base_ContentTypes::inst()->getDefaultCode($we_doc->ContentType))
	);
}
echo we_html_tools::getHtmlTop('', '', 'frameset') .
 STYLESHEET .
 we_html_element::jsElement((isset($parastr) && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === "edit_document_with_parameters" ?
		'var parameters = "' . $parastr . '";' :
		'') .
	'var SEEM_edit_include=' . intval(we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')) . ';
var USERACCESS=' . intval($we_doc->userHasAccess() == we_root::USER_HASACCESS) . ';'
) .
 we_html_element::jsScript(JS_DIR . 'we_edit_frameset.js', "edit_framesetStart('" . oldHtmlspecialchars($we_doc->Text) . "','" . $we_doc->Path . "','" . $we_doc->Table . "'," . $we_doc->ID . ",'" . $we_transaction . "','" . $we_doc->ContentType . "','" . (empty($parastr) ? '' : $parastr ) . "');");

function setOnload(){
	// Don't do this with Templates and only in Preview Mode
	// in Edit-Mode all must be reloaded !!!
	// To remove this functionality - just use the second condition as well.
	return ($GLOBALS['we_doc']->ContentType != we_base_ContentTypes::TEMPLATE/* && $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW */ ?
			'setOnload();' :
			'');
}

if(!$we_doc->ID && $we_doc instanceof we_binaryDocument){
	$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
}
?>
</head><?php
$we_doc->saveInSession($_SESSION['weS']['we_data'][$GLOBALS['we_transaction']]);
$fid = we_base_request::_(we_base_request::STRING, "frameId");
switch($_SESSION['weS']['we_mode']){
	case we_base_constants::MODE_SEE:
		$showContentEditor = true;
		$headerSize = 1;
		break;
	case we_base_constants::MODE_NORMAL:
	default:
		$showContentEditor = ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT && substr($we_doc->ContentType, 0, 5) === 'text/' && $we_doc->ContentType != we_base_ContentTypes::WEDOCUMENT);
		$headerSize = 39;
}
?>
<body onload="_EditorFrame.initEditorFrameData({'EditorIsLoading': false});" onunload="doUnload();" class="editFrameset">
	<?php
//FIXME: if we want to remove these iframes, e.g. EditorFrameController.js enumerate the frames, make sure to get all
	echo we_html_element::htmlIFrame('editHeader', we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_edit_header"), 'height:' . $headerSize . 'px;', '', '', false, 'editHeader') .
	($showContentEditor ?
		we_html_element::htmlIFrame('editor_' . $fid, 'about:blank', 'display:none;top:' . $headerSize . 'px;', '', setOnload(), true, 'mainEditor') .
		we_html_element::htmlIFrame('contenteditor_' . $fid, we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_editor") . (isset($parastr) ? '&' . $parastr : '') . '&we_complete_request=1', 'top:' . $headerSize . 'px;', '', '', true, 'contenteditor') :
		we_html_element::htmlIFrame('editor_' . $fid, we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_editor") . (isset($parastr) ? '&' . $parastr : '') . '&we_complete_request=1', 'top:' . $headerSize . 'px;', '', setOnload(), true, 'mainEditor') .
		we_html_element::htmlIFrame('contenteditor_' . $fid, 'about:blank', 'display:none;top:' . $headerSize . 'px;', '', '', true, 'contenteditor')
	) .
	we_html_element::htmlIFrame('editFooter', we_class::url(WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=load_edit_footer'), '', '', '', false, 'editorButtonFrame');
	?>
</body>
</html>
