<?php
/**
 * webEdition CMS
 *
 * $Rev: 9577 $
 * $Author: mokraemer $
 * $Date: 2015-03-24 18:33:27 +0100 (Tue, 24 Mar 2015) $
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
//TODO: make read, save and process relations-data more concise

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();// FIXME: use perms
$collection = new we_collection();

if(we_base_request::_(we_base_request::BOOL, 'dosave')){
	$name = we_base_request::_(we_base_request::STRING, 'we_name');
	$collection->Filename = $collection->Text = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_Filename');
	$collection->ParentID = we_base_request::_(we_base_request::INT, 'we_' . $name . '_ParentID');
	$collection->ParentPath = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_ParentPath');
	$collection->remCT = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_remCT');

	$db = new DB_WE();
	$exists = f('SELECT 1 FROM ' . VFILE_TABLE . ' WHERE Text ="' . $collection->Text . '" AND ParentID=' . $collection->ParentID . ' LIMIT 1', '', $db);
	$saveSuccess = false;
	if(!$exists){
		$collection->remTable = we_base_request::_(we_base_request::STRING, 'we_' . $name . '_remTable');
		$collection->Path = ($collection->ParentID == 0 ? '' : $collection->ParentPath) . '/' . $collection->Filename;
		$collection->IsFolder = 0;
		$collection->Table = VFILE_TABLE;
		$collection->CreatorID = $_SESSION['user']['ID'];

		// FIXME: why does it not verify Filename/Text and existing Paths?!!
		$collection->ID = 0;
		$collection->fileExists = 0;

		$saveSuccess = $collection->we_save();

		$jsMessage = $ret ? 'collection successfully saved' : 'failed saving collection';
		if($saveSuccess){
			$jsMessage = 'collection successfully saved';
			$jsMessageType = we_message_reporting::WE_MESSAGE_NOTICE;
			$id = f('SELECT ID FROM ' . VFILE_TABLE . ' WHERE Text ="' . $collection->Text . '" AND ParentID=' . $collection->ParentID . ' LIMIT 1', 'ID', $db);

		} else {
			$jsMessage = 'failed saving collection';
			$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
		}
	} else {
		$jsMessage = 'collection allready exists';
		$jsMessageType = we_message_reporting::WE_MESSAGE_ERROR;
	}
}

echo we_html_tools::getHtmlTop('Neue Sammlung') .
		STYLESHEET .
		we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
		we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
		we_html_element::jsElement('
var name = "' . $collection->Name . '";
var _EditorFrame = {};
_EditorFrame.setEditorIsHot = function(){};
var pathOfDocumentChanged = false;

function we_submitForm(url){
	var f = self.document.we_form;
	f.action = url;
	f.method = "post";
	f.submit();
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?";
	var cmd = "' . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) . '";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "openDirselector":
			new jsWindow(url, "we_fileselector", -1, -1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ', true, true, true, true);
			break;
		case "openCatselector":
			new jsWindow(url, "we_catselector", -1, -1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ', true, true, true, true);
			break;
		case "close":
			window.close();
			break;
		case "save_notclose":
			if(document.we_form["we_" + name + "_Filename"].value){
				document.we_form["we_cmd[0]"].value = cmd;
				document.we_form["dosave"].value = 1;
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			} else {
				alert("no name set");
			}
			break;
		case "do_onSuccess":
			//opener.top.we_cmd("load", "' . VFILE_TABLE . '");
			opener.weEditorFrameController.openDocument("' . VFILE_TABLE . '", ' . ($id ? : 0) . ', "' . we_base_ContentTypes::COLLECTION . '");
			window.close();
			break;
		default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			opener.top.we_cmd.apply(this, args);
	}
}
' . (isset($jsMessage) ? we_message_reporting::getShowMessageCall($jsMessage, $jsMessageType) . ($saveSuccess ? 'we_cmd("do_onSuccess");' : '') : '')) .
'</head>';

$parts[] = array('headline' => g_l('weClass', '[path]'), 'html' => $collection->formPath(), 'space' => 0, 'noline' => 1);
$parts[] = array('headline' => 'Inhalt', 'html' => $collection->formContent(), 'space' => 0, 'noline' => 1);

$content = we_html_element::htmlHidden('dosave', 0) .
	we_html_element::htmlHidden('we_cmd[0]', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)) .
	we_html_element::htmlHidden('we_name', $collection->Name) .
	we_html_multiIconBox::getHTML(
		'weNewCollection', 500, $parts, 30, we_html_button::position_yes_no_cancel(
			we_html_button::create_button('save', 'javascript:we_cmd(\'save_notclose\');'), 
			'', 
			we_html_button::create_button('close', 'javascript:we_cmd(\'close\');')
		), -1, '', '', false, 'Neue Sammlung anlegen', '', '', 'scroll'
	);

echo we_html_element::htmlBody(
		array('class' => 'weDialogBody',
			'onload' => 'window.focus();'
		),
		we_html_element::htmlForm(array('method' => 'post'), $content)
	) . '</html>';
