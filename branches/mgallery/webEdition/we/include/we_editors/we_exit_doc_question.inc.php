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

echo we_html_tools::getHtmlTop(g_l('global', '[question]'));


// we_cmd[0] => exit_doc_question
// we_cmd[1] => multiEditFrameId
// we_cmd[2] => content-type of the document
// we_cmd[3] => nextCommand -> as JS-String


$editorFrameId = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
if(!preg_match('/^multiEditFrame_[0-9]+$/', $editorFrameId)){
	exit('cmd[1] is not valid at we_exit_doc_question!');
}

$nextCmd = we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3); // close_all, logout, open_document, new_document(seeMode) etc.

$isOpenDocCmd = preg_match('/^top\.weEditorFrameController\.openDocument\("[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*,\s*"[^"]*"\s*\)\s*;\s*$/', $nextCmd);
$isDoLogoutCmd = preg_match('/^top\.we_cmd\("dologout"\)\s*;\s*$/', $nextCmd);
$isCloseAllCmd = preg_match('/^top\.we_cmd\("close_all_documents"\)\s*;\s*$/', $nextCmd);
$isCloseAllButActiveDocumentCmd = preg_match('/^top\.we_cmd\("close_all_but_active_document"\s*,\s*"[^"]*"\s*\)\s*;\s*$/', $nextCmd);

$nextCmdOk = ($nextCmd === "") || $isOpenDocCmd || $isDoLogoutCmd || $isCloseAllCmd || $isCloseAllButActiveDocumentCmd;


if(!$nextCmdOk){
	exit('cmd[3] (nextCmd) is not valid at we_exit_doc_question!' . $nextCmd);
}

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2)){
	case we_base_ContentTypes::TEMPLATE:
		$_documentTable = TEMPLATES_TABLE;
		break;
	case we_base_ContentTypes::OBJECT:
		if(defined('OBJECT_TABLE')){
			$_documentTable = OBJECT_TABLE;
		}
		break;
	case we_base_ContentTypes::OBJECT_FILE:
		if(defined('OBJECT_FILES_TABLE')){
			$_documentTable = OBJECT_FILES_TABLE;
		}
		break;
	case "folder":
	case we_base_ContentTypes::WEDOCUMENT:
	case we_base_ContentTypes::HTML:
	case we_base_ContentTypes::CSS:
	case we_base_ContentTypes::JS:
	case we_base_ContentTypes::IMAGE:
	case we_base_ContentTypes::APPLICATION:
	default:
		$_documentTable = FILE_TABLE;
		break;
}


echo we_html_element::jsElement("
	var _nextCmd = null;
	var _EditorFrame = WE().layout.weEditorFrameController.getEditorFrame('" . $editorFrameId . "');
	self.focus();

	// functions for keyBoard Listener
	function applyOnEnter() {
		pressed_yes();

	}

	// functions for keyBoard Listener
	function closeOnEscape() {
		pressed_cancel();

	}

	function pressed_yes() {
		_EditorFrame.getDocumentReference().frames.editFooter.we_save_document('" . str_replace("'", "\\'", "WE().layout.weEditorFrameController.closeDocument('" . $editorFrameId . "');" . ($nextCmd ? "top.setTimeout(function(){" . $nextCmd . "}, 1000);" : "" )) . "');
		window_closed();
		self.close();
	}

	function pressed_no() {
		_EditorFrame.setEditorIsHot(false);
		WE().layout.weEditorFrameController.closeDocument('" . $editorFrameId . "');
		" . ($nextCmd ? "opener.top.setTimeout(function(){" . $nextCmd . "}, 1000);" : "" ) . "
		window_closed();
		self.close();

	}

	function pressed_cancel() {
		window_closed();
		self.close();

	}

	function window_closed() {
		_EditorFrame.EditorExitDocQuestionDialog = false;

	}
");

// $yesCmd: $REQUEST['we_cmd'][6] => next-EditCommand, JS-Function Call !! after save document.


echo STYLESHEET;
?>
</head>

<body onunload="window_closed();" class="weEditorBody" onload="self.focus();" onblur="self.focus();">
	<?php echo we_html_tools::htmlYesNoCancelDialog(g_l('alert', '[' . stripTblPrefix($_documentTable) . '][exit_doc_question]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', true, true, true, "pressed_yes();", "pressed_no();", "pressed_cancel();"); ?>
</body>

</html>