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

$cmd = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$name = we_base_request::_(we_base_request::STRING, 'we_name', '');
$fixedPID = we_base_request::_(we_base_request::INT, 'fixedpid', -1);
$fixedRemTable = we_base_request::_(we_base_request::STRING, 'fixedremtable', '');

$collection->ParentID = $fixedPID !== -1 ? $fixedPID : we_base_request::_(we_base_request::INT, 'we_' . $name . '_ParentID', 0);
$collection->ParentPath = id_to_path($collection->ParentID, $collection->Table);
$collection->remTable = stripTblPrefix(FILE_TABLE); // FIXME: make dynamic when implementing object collections
//$collection->remTable = $fixedRemTable ? : we_base_request::_(we_base_request::STRING, 'we_' . $name . '_remTable');

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
	$writeBack = [];

	if(($jsMessage = $collection->checkFieldsOnSave())){
		$jsMessageType = we_base_util::WE_MESSAGE_ERROR;
	} else {
		if(($saveSuccess = $collection->we_save())){
			$jsMessage = sprintf(g_l('weEditor', '[text/weCollection][response_save_ok]'), $collection->Text);
			$jsMessageType = we_base_util::WE_MESSAGE_NOTICE;
			$id = f('SELECT ID FROM ' . VFILE_TABLE . ' WHERE Text ="' . $collection->Text . '" AND ParentID=' . $collection->ParentID . ' LIMIT 1');
			$jsCommandOnSuccess = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1);
		} else {
			$jsMessage = 'unknown error ';
			$jsMessageType = we_base_util::WE_MESSAGE_ERROR;
		}
	}
}

$jsDynamicVars = ['name' => $collection->Name,
	'cmd' =>  $cmd,
	'scriptName' => $_SERVER['SCRIPT_NAME'],
	'cmdOnSuccess' => $jsCommandOnSuccess,
	'data' => ['id' => $id, 'text' => $collection->Path]

];

$jsCmd = new we_base_jsCmd();
if($jsMessage){
	$jsCmd->addMsg($jsMessage, $jsMessageType);
}
if($saveSuccess){
	$jsCmd->addCmd('do_onSuccess');
}

echo we_html_tools::getHtmlTop(g_l('buttons_global','[new_collection][value]'), '', '', we_html_element::jsScript(JS_DIR . 'collection.js') .
		we_html_element::jsScript(JS_DIR . '/dialogs/we_dialog_newCollection.js', '', ['id' => 'loadVarWe_dialog_newCollection', 'data-dialog' => setDynamicVar($jsDynamicVars)]) .
		$jsCmd->getCmds()
	);

$parts[] = ['headline' => g_l('weClass', '[path]'), 'html' => $collection->formPath($fixedPID !== -1, true), 'noline' => 1];
$parts[] = ['headline' => 'Inhalt', 'html' => $collection->formContent(true), 'noline' => 1];

$content = we_html_element::htmlHiddens([
		'dosave' => 0,
		'we_cmd[0]' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
		'we_cmd[1]' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1),
		'we_cmd[2]' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2),
		"fixedpid" => $fixedPID,
		"fixedremtable" => $fixedRemTable,
		'we_name' => $collection->Name,
		'caller' => $caller
	]) .
	we_html_multiIconBox::getHTML(
		'weNewCollection', $parts, 30, we_html_button::position_yes_no_cancel(
			we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_notclose');"), '', we_html_button::create_button(we_html_button::CLOSE, "javascript:we_cmd('close');")
		), -1, '', '', false, g_l('buttons_global', '[new_collection][value]'));

echo we_html_element::htmlBody(
	['class' => 'weDialogBody',
	'onload' => 'window.focus();'
 ], we_html_element::htmlForm(['method' => 'post'], $content)
) . '</html>';
