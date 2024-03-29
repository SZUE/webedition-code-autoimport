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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect(); // FIXME: use perms
$collection = new we_collection();
$collection->we_new();

$name = we_base_request::_(we_base_request::STRING, 'we_name', '');
$fixedPID = we_base_request::_(we_base_request::INT, 'fixedpid', -1);
$fixedRemTable = we_base_request::_(we_base_request::STRING, 'fixedremtable', '');

$collection->ParentID = $fixedPID !== -1 ? $fixedPID : we_base_request::_(we_base_request::INT, 'we_' . $name . '_ParentID', 0);
$collection->ParentPath = id_to_path($collection->ParentID, $collection->Table);
$collection->remTable = stripTblPrefix(FILE_TABLE); // FIXME: make dynamic when implementing object collections
//$collection->remTable = $fixedRemTable ? : we_base_request::_(we_base_request::STRING, 'we_' . $name . '_remTable');

$caller = we_base_request::_(we_base_request::STRING, 'caller');

$id = 0;
$saveSuccess = false;
if(we_base_request::_(we_base_request::BOOL, 'dosave')){
	$collection->ContentType = we_base_ContentTypes::COLLECTION;
	$collection->IsFolder = 0;
	$collection->we_new();

	$collection->Filename = $collection->Text = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_Filename');
	$collection->Path = ($collection->ParentID == 0 ? '' : $collection->ParentPath) . '/' . $collection->Filename;
	$collection->remCT = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_remCT');
	$collection->remClass = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_remClass');
	$collection->IsDuplicates = we_base_request::_(we_base_request::INT, 'we_' . $name . 'IsDuplicates', 1);
	$collection->InsertRecursive = 1;
	$writeBack = array();

	if(($jsMessage = $collection->checkFieldsOnSave())){
		$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
	} else {
		$saveSuccess = $collection->we_save();
		if($saveSuccess){
			$jsMessage = $caller == 'selector' ? '' : sprintf(g_l('weEditor', '[text/weCollection][response_save_ok]'), $collection->Text);
			$jsMessageType = we_message_reporting::WE_MESSAGE_NOTICE;
			$id = f('SELECT ID FROM ' . VFILE_TABLE . ' WHERE Text ="' . $collection->Text . '" AND ParentID=' . $collection->ParentID . ' LIMIT 1');
			$writeBack = array(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1), we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
		} else {
			$jsMessage = 'unknown error ';
			$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
		}
	}
}

echo we_html_tools::getHtmlTop('Neue Sammlung'/* FIXME: missing title */, '', '', STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
	we_html_element::jsElement('
var name = "' . $collection->Name . '";
var _EditorFrame = {};
_EditorFrame.setEditorIsHot = function(){};
var pathOfDocumentChanged = false;

function we_submitForm(url){
	var f = self.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

var cmd = "' . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) . '";

	switch (args[0]) {
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url, "we_fileselector", -1, -1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ', true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url, "we_catselector", -1, -1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ', true, true, true, true);
			break;
		case "close":
			window.close();
			break;
		case "save_notclose":
			if(document.we_form["we_" + name + "_Filename"].value){
				document.we_form["we_cmd[0]"].value = cmd;
				document.we_form["dosave"].value = 1;
				we_submitForm("' . getScriptName() . '");
			} else {
				alert("no name set");
			}
			break;
		case "do_onSuccess":
			' . ($caller === 'selector' ? 'opener.top.reloadDir();
			opener.top.unselectAllFiles();
			opener.top.doClick(' . $id . ', 0);
			setTimeout(opener.top.selectFile, 200,' . $id . ');' :
			((isset($writeBack[0]) && $writeBack[0] && isset($writeBack[1])) ? 'opener.' . $writeBack[0] . ' = ' . $id . ';opener.' . $writeBack[1] . ' = "' . $collection->Path . '";' : '')) . '
			window.close();
			break;
		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
' . (!empty($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) : '') . ($saveSuccess ? 'we_cmd("do_onSuccess");' : '')));

$parts[] = array('headline' => g_l('weClass', '[path]'), 'html' => $collection->formPath($fixedPID !== -1, true), 'noline' => 1);
$parts[] = array('headline' => 'Inhalt', 'html' => $collection->formContent(true), 'noline' => 1);

$content = we_html_element::htmlHidden('dosave', 0) .
	we_html_element::htmlHiddens(array(
		'we_cmd[0]' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
		'we_cmd[1]' => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 1),
		'we_cmd[2]' => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 2),
		"fixedpid" => $fixedPID,
		"fixedremtable" => $fixedRemTable,
		'we_name' => $collection->Name,
		'caller' => $caller
	)) .
	we_html_multiIconBox::getHTML(
		'weNewCollection', $parts, 30, we_html_button::position_yes_no_cancel(
			we_html_button::create_button(we_html_button::SAVE, 'javascript:we_cmd(\'save_notclose\');'), '', we_html_button::create_button(we_html_button::CLOSE, 'javascript:we_cmd(\'close\');')
		), -1, '', '', false, g_l('buttons_global', '[new_collection][value]'));

echo we_html_element::htmlBody(
	array('class' => 'weDialogBody',
	'onload' => 'window.focus();'
	), we_html_element::htmlForm(array('method' => 'post'), $content)
) . '</html>';
