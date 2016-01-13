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
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');
we_html_tools::protect();

list($sType, $iDate, $iAmountEntries, $sDisplayOpt, $sUsers) = explode(';', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));

$jsCode = "
var _sUsers='" . $sUsers . "';
	var g_l={
	no_type_selected: '" . we_message_reporting::prepareMsgForJS(g_l('cockpit', '[no_type_selected]')) . "'
};
";

$textname = 'UserNameTmp';
$idname = 'UserIDTmp';
$users = array_filter(explode(',', trim($sUsers, ',')));

$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$wecmdenc2 = we_base_request::encCmd("WE().layout.weEditorFrameController.getActiveDocumentReference()._propsDlg['" . $cmd0 . "'].document.forms[0].elements.UserNameTmp.value");
$wecmdenc5 = we_base_request::encCmd("WE().layout.weEditorFrameController.getActiveDocumentReference()._propsDlg['" . $cmd0 . "'].addUserToField();");

$content = '<table class="default" style="width:300px;margin-bottom:2px;">
<colgroup><col style="width:20px;"/><col style="width:254px;"/><col style="width:26px;"/></colgroup>';

if(permissionhandler::hasPerm('EDIT_MFD_USER') && $users){
	$db = new DB_WE();
	$db->query('SELECT ID,Path,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType FROM ' . USER_TABLE . ' WHERE ID IN (' . implode(',', $users) . ')');
	while($db->next_record(MYSQL_ASSOC)){
		$content .= '<tr><td class="mfdUIcon" data-contenttype="' . $db->f('ContentType') . '"></td><td class="defaultfont">' . $db->f("Path") . '</td><td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:delUser('" . $db->f('ID') . "');") . '</td></tr>';
	}
} else {
	$content .= '<tr><td class="mfdUIcon" data-contenttype="we/userGroup"></td><td class="defaultfont">' . (permissionhandler::hasPerm('EDIT_MFD_USER') ? g_l('cockpit', '[all_users]') : $_SESSION['user']['Username']) . '</td><td></td><td></td></tr>';
}
$content .= '</table>';

$sUsrContent = '<table class="default" style="width:300px"><tr><td>' . we_html_element::htmlDiv(array("class" => "multichooser"), $content) .
	we_html_element::htmlHiddens(array(
		"UserNameTmp" => "",
		"UserIDTmp" => ""
	)) .
	'</td></tr>' .
	(permissionhandler::hasPerm('EDIT_MFD_USER') ? '<tr><td style="text-align:right;padding-top:1em;">' .
		we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:delUser(-1)", true, -1, -1, "", "", ($users ? false : true)) .
		we_html_button::create_button(we_html_button::ADD, "javascript:opener.getUser('we_users_selector','WE().layout.weEditorFrameController.getActiveDocumentReference()._propsDlg[\"" . $cmd0 . "\"].document.forms[0].elements.UserIDTmp.value','" . $wecmdenc2 . "','','','" . $wecmdenc5 . "','','',1);") .
		'</td></tr>' : '') .
	'</table>';

$oShowUser = we_html_tools::htmlFormElementTable($sUsrContent, g_l('cockpit', '[following_users]'), "left", "defaultfont");

// Typ block
while(strlen($sType) < 4){
	$sType .= '0';
}
if($sType === '0000'){
	$sType = '1111';
}

$oChbxDocs = (permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') ?
		we_html_forms::checkbox(1, $sType{0}, "chbx_type", g_l('cockpit', '[documents]'), true, "defaultfont", "", !(defined('FILE_TABLE') && permissionhandler::hasPerm("CAN_SEE_DOCUMENTS")), '', 0, 0) :
		'<input type="hidden" name="chbx_type" value="0"/>');
$oChbxTmpl = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE ?
		we_html_forms::checkbox(1, $sType{1}, "chbx_type", g_l('cockpit', '[templates]'), true, "defaultfont", "", !(defined('TEMPLATES_TABLE') && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')), "", 0, 0) :
		'<input type="hidden" name="chbx_type" value="0"/>'); //FIXME: this is needed for getBinary!
$oChbxObjs = (permissionhandler::hasPerm('CAN_SEE_OBJECTFILES') ?
		we_html_forms::checkbox(1, $sType{2}, "chbx_type", g_l('cockpit', '[objects]'), true, "defaultfont", "", !(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')), "", 0, 0) :
		'<input type="hidden" name="chbx_type" value="0"/>');
$oChbxCls = (permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE ?
		we_html_forms::checkbox(1, $sType{3}, "chbx_type", g_l('cockpit', '[classes]'), true, "defaultfont", "", !(defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTS')), "", 0, 0) :
		'<input type="hidden" name="chbx_type" value="0"/>');

$oDbTableType = new we_html_table(array('class' => 'default'), 1, 3);
$oDbTableType->setCol(0, 0, array('style' => 'padding-right:10px;'), $oChbxDocs . $oChbxTmpl);
$oDbTableType->setCol(0, 2, null, $oChbxObjs . $oChbxCls);

$oSctDate = new we_html_select(array("name" => "sct_date", "size" => 1, "class" => "defaultfont", "onchange" => ""));
$aLangDate = array(
	g_l('cockpit', '[all]'),
	g_l('cockpit', '[today]'),
	g_l('cockpit', '[last_week]'),
	g_l('cockpit', '[last_month]'),
	g_l('cockpit', '[last_year]')
);
foreach($aLangDate as $k => $v){
	$oSctDate->insertOption($k, $k, $v);
}
$oSctDate->selectOption($iDate);

$oChbxShowMfdBy = we_html_forms::checkbox(0, $sDisplayOpt{0}, "chbx_display_opt", g_l('cockpit', '[modified_by]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxShowDate = we_html_forms::checkbox(0, $sDisplayOpt{1}, "chbx_display_opt", g_l('cockpit', '[date_last_modification]'), true, "defaultfont", "", false, "", 0, 0);
$oSctNumEntries = new we_html_select(array("name" => "sct_amount_entries", "size" => 1, "class" => "defaultfont"));
$oSctNumEntries->insertOption(0, 0, g_l('cockpit', '[all]'));
for($iCurrEntry = 1; $iCurrEntry <= 50; $iCurrEntry++){
	$oSctNumEntries->insertOption($iCurrEntry, $iCurrEntry, $iCurrEntry);
	if($iCurrEntry >= 10){
		$iCurrEntry += ($iCurrEntry == 25) ? 24 : 4;
	}
}
$oSctNumEntries->selectOption($iAmountEntries);

$oSelMaxEntries = new we_html_table(array("height" => "100%", 'class' => 'default'), 1, 3);
$oSelMaxEntries->setCol(0, 0, array("class" => "defaultfont", 'style' => 'vertical-align:middle;padding-right:5px;'), g_l('cockpit', '[max_amount_entries]'));
$oSelMaxEntries->setCol(0, 2, array('style' => 'vertical-align:middle;'), $oSctNumEntries->getHTML());

$show = $oSelMaxEntries->getHTML() . $oChbxShowMfdBy . $oChbxShowDate . we_html_element::htmlBr() . $oShowUser;

$parts = array(
	array(
		"headline" => g_l('cockpit', '[type]'),
		"html" => $oDbTableType->getHTML(),
		'space' => 80
	),
	array(
		"headline" => g_l('cockpit', '[date]'),
		"html" => $oSctDate->getHTML(),
		'space' => 80
	),
	array(
		"headline" => g_l('cockpit', '[display]'),
		"html" => $show,
		'space' => 80
	),
	array(
		"headline" => "",
		"html" => $oSelCls->getHTML(),
	)
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML('mfdProps', $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[last_modified]'), "", 390);

echo we_html_tools::getHtmlTop(g_l('cockpit', '[last_modified]'), '', '', STYLESHEET .
	$jsFile .
	we_html_element::jsElement($jsPrefs . $jsCode) .
	we_html_element::jsScript(JS_DIR . 'widgets/mfd.js'), we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => "init();WE().util.setIconOfDocClass(document,'mfdUIcon');"
		), we_html_element::htmlForm("", $sTblWidget)));
