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
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 1);

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

function inWorkflow($doc){
	if(!defined('WORKFLOW_TABLE') || !$doc->IsTextContentDoc){
		return false;
	}
	return ($doc->ID ? we_workflow_utility::inWorkflow($doc->ID, $doc->Table) : false);
}

function getControlElement($type, $name){
	if(isset($GLOBALS['we_doc']->controlElement) && is_array($GLOBALS['we_doc']->controlElement)){

		return (isset($GLOBALS['we_doc']->controlElement[$type][$name]) ?
				$GLOBALS['we_doc']->controlElement[$type][$name] :
				false);
	}
	return false;
}

switch($we_doc->userHasAccess()){
	case we_root::USER_HASACCESS : //	all is allowed, creator or owner
		break;

	case we_root::FILE_NOT_IN_USER_WORKSPACE : //	file is not in workspace of user
		we_editor_footer::fileInWorkspace();
		exit();

	case we_root::USER_NO_PERM : //	access is restricted and user has no permission
		we_editor_footer::fileIsRestricted($we_doc);
		exit;

	case we_root::FILE_LOCKED : //	file is locked by another user
		we_editor_footer::fileLocked($we_doc);
		exit;

	case we_root::USER_NO_SAVE : //	user has not the right to save the file.
		we_editor_footer::fileNoSave();
		exit;
}


//	preparations of needed vars
echo we_html_tools::getHtmlTop();

$showPubl = permissionhandler::hasPerm("PUBLISH") && $we_doc->userCanSave() && $we_doc->IsTextContentDoc;
$reloadPage = (bool) (($showPubl || $we_doc->ContentType == we_base_ContentTypes::TEMPLATE) && (!$we_doc->ID));
$haspermNew = false;

//	Check permissions for buttons
switch($we_doc->ContentType){
	case we_base_ContentTypes::HTML:
		$haspermNew = permissionhandler::hasPerm("NEW_HTML");
		break;
	case we_base_ContentTypes::WEDOCUMENT:
		$haspermNew = permissionhandler::hasPerm("NEW_WEBEDITIONSITE");
		break;
	case we_base_ContentTypes::OBJECT_FILE:
		$haspermNew = permissionhandler::hasPerm("NEW_OBJECTFILE");
		break;
}

$showGlossaryCheck = (isset($_SESSION['prefs']['force_glossary_check']) && $_SESSION['prefs']['force_glossary_check'] == 1 && (
	$we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType === we_base_ContentTypes::OBJECT_FILE
	) ? 1 : 0);

//	added for we:controlElement type="button" name="save" hide="true"
$_ctrlElem = getControlElement('button', 'save');

$canWeSave = $we_doc->userCanSave();

if($canWeSave &&
	(($_ctrlElem && $_ctrlElem['hide']) ||
	(defined('WORKFLOW_TABLE') && inWorkflow($we_doc) && (!we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"])))
	)){
	$canWeSave = false;
}

// publish for templates to save in version
$pass_publish = $canWeSave && ($showPubl || ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE && defined('VERSIONING_TEXT_WETMPL') && defined('VERSIONS_CREATE_TMPL') && VERSIONS_CREATE_TMPL && VERSIONING_TEXT_WETMPL)) ? " _EditorFrame.getEditorPublishWhenSave() " : "''";
$js_we_save_cmd = "we_cmd('save_document','','','',''," . $pass_publish . ",addCmd);";

$js = "
function generatedSaveDoc(){
	var addCmd = arguments[0] ? arguments[0] : '';
	if(weCanSave){
" . ($we_doc->isBinary() ?
		we_fileupload_ui_preview::getJsOnLeave($js_we_save_cmd) :
		$js_we_save_cmd
	) .
	($reloadPage ?
		'setTimeout(saveReload,1500);' :
		''
	) . '
	}
}';

if($we_doc->IsTextContentDoc && $haspermNew && //	$_js_permnew
	($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT)){ // not in SeeMode or in editmode
	$_ctrlElem = getControlElement('checkbox', 'makeSameDoc');
	$_js_permnew = ($_ctrlElem ? //	changes for we:controlElement
			'setTextDocument(true,' . ($_ctrlElem["checked"] ? "true" : "false") . ');' :
			'setTextDocument(false);');
} else {
	$_js_permnew = '';
}


echo STYLESHEET .
 we_html_element::jsElement('
	var we_transaction="' . $we_transaction . '";
	var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction(we_transaction);
	var g_l={
		"in_wf_warning":"' . (defined('WORKFLOW_TABLE') ? g_l('alert', '[' . stripTblPrefix($we_doc->Table) . '][in_wf_warning]') : '') . '",
	};
	var doc={
		ID:"' . $we_doc->ID . '",
		Path:"' . $we_doc->Path . '",
		Text:"' . $we_doc->Text . '",
		contentType:"' . $we_doc->ContentType . '",
		editFilename:"' . preg_replace('|/' . $we_doc->Filename . '.*$|', $we_doc->Filename . (isset($we_doc->Extension) ? $we_doc->Extension : ''), $we_doc->Path) . '",
		makeSameDocCheck: ' . intval($we_doc->IsTextContentDoc && $haspermNew && (!inWorkflow($we_doc))) . ',
		isTemplate:' . intval($we_doc->Table == TEMPLATES_TABLE) . ',
		isFolder:' . intval($we_doc->ContentType == we_base_ContentTypes::FOLDER) . ',
		classname:"' . ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table != TEMPLATES_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')) . '"
	};
	var weCanSave=' . ($canWeSave ? 'true' : 'false') . ';
	var _showGlossaryCheck = ' . $showGlossaryCheck . ';


top.toggleBusy(0);

function we_footerLoaded(){
if(doc.isTemplate){
			setTemplate();
			}' .
	$_js_permnew .
	'setPath();
}
' .
	$js) .
 we_html_element::jsScript(JS_DIR . 'we_editor_footer.js');
?>
</head>

<?php
//	Document is in workflow
if(inWorkflow($we_doc)){
	we_editor_footer::workflow($we_doc);
	exit();
}
?>

<body id="footerBody" onload="we_footerLoaded();"<?php echo $we_doc->getEditorBodyAttributes(we_root::EDITOR_FOOTER); ?>>
	<form name="we_form" action=""<?php if(!empty($we_doc->IsClassFolder)){ ?> onsubmit="sub();
				return false;"<?php } ?>>
					<?php
					echo we_html_element::htmlHidden('sel', $we_doc->ID);
					$_SESSION['weS']['seemForOpenDelSelector'] = array(
						'ID' => $we_doc->ID,
						'Table' => $we_doc->Table
					);

					if($we_doc->userCanSave()){

						switch($_SESSION['weS']['we_mode']){
							default:
							case we_base_constants::MODE_NORMAL: // open footer for NormalMode
								we_editor_footer::normalMode($we_doc, $we_transaction, $haspermNew, $showPubl);
								break;
							case we_base_constants::MODE_SEE: // open footer for SeeMode
								we_editor_footer::SEEMode($we_doc, $we_transaction, $haspermNew, $showPubl);
								break;
						}
					} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){

						$_noPermTable = new we_html_table(array("class" => 'default footertable'), 1, 2);

						$_noPermTable->setColContent(0, 0, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", 'style' => 'margin-right:10px;')));
						$_noPermTable->setColContent(0, 1, g_l('SEEM', '[no_permission_to_edit_document]'));


						echo $_noPermTable->getHtml();
					}
					?>
	</form>
</body>
</html>
