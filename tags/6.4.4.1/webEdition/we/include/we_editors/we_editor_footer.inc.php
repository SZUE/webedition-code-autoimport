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
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd',  we_base_request::_(we_base_request::TRANSACTION,'we_transaction'), 1);

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
	case "objectFile":
		$haspermNew = permissionhandler::hasPerm("NEW_OBJECTFILE");
		break;
}

//	########################	required javascript functions
//	########################	function we_save_document	######################################
// ---> Glossary Check
//
// load Glossary Settings

$showGlossaryCheck = (isset($_SESSION['prefs']['force_glossary_check']) && $_SESSION['prefs']['force_glossary_check'] == 1 && (
	$we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType === "objectFile"
	) ? 1 : 0);

$js = 'var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("' . $we_transaction . '");' . "
var _showGlossaryCheck = $showGlossaryCheck;
var countSaveLoop = 0;
function saveReload(){
	self.location='" . we_class::url(WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=load_edit_footer') . "';
}

function we_save_document(){
	try{
		var contentEditor = top.weEditorFrameController.getVisibleEditorFrame();
		if (contentEditor && contentEditor.fields_are_valid && !contentEditor.fields_are_valid()) {
			return;

		}
	}
	catch(e) {
		// Nothing
	}

	if (_EditorFrame.getEditorPublishWhenSave() && _showGlossaryCheck) {
		we_cmd('glossary_check', '', '" . $we_transaction . "');
	} else {
		acStatus = '';
		invalidAcFields = false;
		try{
			if(parent && parent.frames[1] && parent.frames[1].YAHOO && parent.frames[1].YAHOO.autocoml) {
				 acStatus = parent.frames[1].YAHOO.autocoml.checkACFields();
			}
		}
		catch(e) {
			// Nothing
		}
		acStatusType = typeof acStatus;
		if(parent && parent.weAutoCompetionFields && parent.weAutoCompetionFields.length>0) {
			for(i=0; i<parent.weAutoCompetionFields.length; i++) {
				if(parent.weAutoCompetionFields[i] && parent.weAutoCompetionFields[i].id && !parent.weAutoCompetionFields[i].valid) invalidAcFields = true;
			}
		}
		if (countSaveLoop > 10) {
			" . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . ";
			countSaveLoop = 0;
		}else if(acStatusType.toLowerCase() == 'object' && acStatus.running) {
			countSaveLoop++;
			setTimeout('we_save_document()',100);
		}else if(invalidAcFields) {
			" . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . ";
			countSaveLoop=0;
		} else {
			countSaveLoop=0;
";
if($we_doc->userCanSave()){
	$js.= "var addCmd = arguments[0] ? arguments[0] : '';";

	// publish for templates to save in version
	$pass_publish = $showPubl ? " _EditorFrame.getEditorPublishWhenSave() " : "''";
	if($we_doc->ContentType == we_base_ContentTypes::TEMPLATE && defined('VERSIONING_TEXT_WETMPL') && defined('VERSIONS_CREATE_TMPL') && VERSIONS_CREATE_TMPL && VERSIONING_TEXT_WETMPL){
		$pass_publish = " _EditorFrame.getEditorPublishWhenSave() ";
	}

	$js_we_save_cmd = "we_cmd('save_document','','','',''," . $pass_publish . ",addCmd);";
	$js.= $we_doc->isBinary() ? we_fileupload_binaryDocument::getJsOnLeave($js_we_save_cmd) : $js_we_save_cmd;
	//$js.= $js_we_save_cmd;
	$js.= ($reloadPage ? "setTimeout('saveReload()',1500);" : '');
}

$js.= '
			_showGlossaryCheck = ' . $showGlossaryCheck . ';
		}
	}
}';

//	########################	function for workflow	###########################################
if(defined('WORKFLOW_TABLE')){

	$js.= "
function put_in_workflow() {

	if( _EditorFrame.getEditorIsHot() ) {
		if(confirm('" . g_l('alert', '[' . stripTblPrefix($we_doc->Table) . '][in_wf_warning]') . "')) {
			we_cmd('save_document','','','','',0,0,1);
		}
	}else {
		top.we_cmd('workflow_isIn','" . $we_transaction . "'," . ( ($we_doc->IsTextContentDoc && $haspermNew && (!inWorkflow($we_doc))) ? "( _EditorFrame.getEditorMakeSameDoc() ? 1 : 0 )" : "0" ) . ");
	}
}

function pass_workflow() {
	we_cmd('workflow_pass','" . $we_transaction . "');
}

function workflow_finish() {
	we_cmd('workflow_finish','" . $we_transaction . "');
}

function decline_workflow() {
	we_cmd('workflow_decline','" . $we_transaction . "');
}";
}
//	########################	function variable cansave	###########################################
$js.= 'var weCanSave=' . ($we_doc->userCanSave() ? 'true' : 'false') . ';';

//	added for we:controlElement type="button" name="save" hide="true"
$_ctrlElem = getControlElement('button', 'save');

if($_ctrlElem && $_ctrlElem['hide']){
	$js.= 'weCanSave=false;'; //	we:controlElement
}


if(defined('WORKFLOW_TABLE') && inWorkflow($we_doc)){
	if(!we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"])){
		$js.= 'weCanSave=false;';
	}
}


//	########################	toggleBusy call	#########################################################
$js.= 'top.toggleBusy(0);';

//	########################	function we_cmd	#########################################################

$js.= "
	function we_cmd() {
	var url = '" . WEBEDITION_DIR . "we_cmd.php?';
	for(var i = 0; i < arguments.length; i++) {
		url += \"we_cmd[\"+i+\"]=\"+encodeURI(arguments[i]);
		if(i < (arguments.length - 1)){
			url += \"&\";
		}
	}
		switch(arguments[0]) {
";
if($we_doc->Table == TEMPLATES_TABLE){ //	Its a template
	$js.= '
		case "save_document":	// its a folder
	' . ( $we_doc->ContentType == we_base_ContentTypes::FOLDER ?
			"
			top.we_cmd(\"save_document\",'" . $we_transaction . "',0,1,'','',arguments[6] ? arguments[6] : '',arguments[7] ? arguments[7] : '');" : "
			top.we_cmd(\"save_document\",'" . $we_transaction . "',0,0,'',arguments[5] ? arguments[5] : '',arguments[6] ? arguments[6] : '',arguments[7] ? arguments[7] : '');
" ) . '
			return;
		';
} else { //	Its not a template
	$js.= '
			case "glossary_check":
				new jsWindow(url,"glossary_check",-1,-1,730,400,true,false,true);
				return;
			case "save_document":
				top.we_cmd("save_document","' . $we_transaction . '",0,1,' . ( ($we_doc->IsTextContentDoc && $haspermNew && (!inWorkflow($we_doc))) ? '( _EditorFrame.getEditorMakeSameDoc() ? 1 : 0 )' : '0' ) . ',arguments[5] ? arguments[5] : "",arguments[6] ? arguments[6] : "",arguments[7] ? arguments[7] : "");
				return;
' .
		(isset($we_doc->IsClassFolder) ? '
			case "object_obj_search":
				top.we_cmd("object_obj_search","' . $we_transaction . '",document.we_form.obj_search.value,document.we_form.obj_searchField[document.we_form.obj_searchField.selectedIndex].value);
				return;
' : '');
}

$js.= "}
		var args = '';
		for(var i = 0; i < arguments.length; i++) {
			args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
		}
		eval('top.we_cmd('+args+')');
	}
";

$js.= '
	function we_submitForm(target, url){
		var f = self.document.we_form;
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
	}

function we_footerLoaded(){
';

if($we_doc->ContentType == we_base_ContentTypes::TEMPLATE){ // a template
	$js.= '
		if( _EditorFrame.getEditorAutoRebuild() ) {
			self.document.we_form.autoRebuild.checked = true;
		} else {
			self.document.we_form.autoRebuild.checked = false;
		}
		if( _EditorFrame.getEditorMakeNewDoc() ) {
			self.document.we_form.makeNewDoc.checked = true;
		} else {
			self.document.we_form.makeNewDoc.checked = false;
		}';
}

if($we_doc->IsTextContentDoc && $haspermNew){
	if($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT){ // not in SeeMode or in editmode
		$_ctrlElem = getControlElement('checkbox', 'makeSameDoc');
		if(!$_ctrlElem){ //	changes for we:controlElement
			$js.= ($we_doc->ID ? '
			if(self.document.we_form && self.document.we_form.makeSameDoc){
				self.document.we_form.makeSameDoc.checked = false;
			}
			' : '
			if( _EditorFrame.getEditorMakeSameDoc() ) {
				if(self.document.we_form && self.document.we_form.makeSameDoc){
					self.document.we_form.makeSameDoc.checked = true;
				}
			} else {
				if(self.document.we_form && self.document.we_form.makeSameDoc){
					self.document.we_form.makeSameDoc.checked = false;
				}
			}
			');
		} else { //	$_ctrlElement determines values
			$js .= '
			if(self.document.we_form && self.document.we_form.makeSameDoc){
				self.document.we_form.makeSameDoc.checked = ' . ($_ctrlElem["checked"] ? "true" : "false") . ';
				_EditorFrame.setEditorMakeSameDoc(' . $_ctrlElem["checked"] ? "true" : "false" . ');
			}';
		}
	}
}

$js.='try{
			_EditorFrame.getDocumentReference().frames[0].we_setPath("' . $we_doc->Path . '","' . $we_doc->Text . '", "' . $we_doc->ID . '");
			}catch(e){}
}';


//	########################	print javascript src	#########################################################
echo STYLESHEET .
 we_html_element::jsScript(JS_DIR . "windows.js") .
 we_html_element::jsElement($js);
?>
</head>

<?php
//	Document is in workflow
if(inWorkflow($we_doc)){
	we_editor_footer::workflow($we_doc);
	exit();
}
?>

<body style="background-color:#f0f0f0; background-image: url('<?php echo EDIT_IMAGE_DIR ?>editfooterback.gif');background-repeat:repeat;margin:10px 0px 10px 0px" onload="we_footerLoaded();">
	<form name="we_form" action=""<?php if(isset($we_doc->IsClassFolder) && $we_doc->IsClassFolder){ ?> onsubmit="sub();
				return false;"<?php } ?>>
		<input type="hidden" name="sel" value="<?php echo $we_doc->ID; ?>" />
		<?php
		$_SESSION['weS']['seemForOpenDelSelector']['ID'] = $we_doc->ID;
		$_SESSION['weS']['seemForOpenDelSelector']['Table'] = $we_doc->Table;

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
		} else {

			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){

				$_noPermTable = new we_html_table(array("cellpadding" => 0,
					"cellspacing" => 0,
					"border" => 0), 1, 4);

				$_noPermTable->setColContent(0, 0, we_html_tools::getPixel(20, 2));
				$_noPermTable->setColContent(0, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
				$_noPermTable->setColContent(0, 2, we_html_tools::getPixel(10, 2));
				$_noPermTable->setColContent(0, 3, g_l('SEEM', '[no_permission_to_edit_document]'));


				echo $_noPermTable->getHtml();
			}
		}
		?>
	</form>
</body>
</html>
