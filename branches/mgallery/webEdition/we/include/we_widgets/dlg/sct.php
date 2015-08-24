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
$_disableNew = true;
$_cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "');";
if(permissionhandler::hasPerm("NEW_WEBEDITIONSITE")){
	if(permissionhandler::hasPerm("NO_DOCTYPE")){
		$_disableNew = false;
	} else {
		$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
		$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');

		if($id){
			$_disableNew = false;
			$_cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $id . "')";
		} else {
			$_disableNew = true;
		}
	}
} else {
	$_disableNew = true;
}

$_disableObjects = false;
if(defined('OBJECT_TABLE')){
	$allClasses = we_users_util::getAllowedClasses();
	if(empty($allClasses)){
		$_disableObjects = true;
	}
}

$shortcuts = array();

if(defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$shortcuts['open_document'] = g_l('button', '[open_document][value]');
}
if(defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') && !$_disableNew){
	$shortcuts['new_document'] = g_l('button', '[new_document][value]');
}
if(defined('TEMPLATES_TABLE') && permissionhandler::hasPerm('NEW_TEMPLATE')){
	$shortcuts['new_template'] = g_l('button', '[new_template][value]');
}
if(permissionhandler::hasPerm('NEW_DOC_FOLDER')){
	$shortcuts['new_directory'] = g_l('button', '[new_directory][value]');
}
if(defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
	$shortcuts['unpublished_pages'] = g_l('button', '[unpublished_pages][value]');
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES') && !$_disableObjects){
	$shortcuts['unpublished_objects'] = g_l('button', '[unpublished_objects][value]');
}
if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('NEW_OBJECTFILE') && !$_disableObjects){
	$shortcuts['new_object'] = g_l('button', '[new_object][value]');
}
if(defined('OBJECT_TABLE') && permissionhandler::hasPerm('NEW_OBJECT')){
	$shortcuts['new_class'] = g_l('button', '[new_class][value]');
}
if(permissionhandler::hasPerm('EDIT_SETTINGS')){
	$shortcuts['preferences'] = g_l('button', '[preferences][value]');
}
if(permissionhandler::hasPerm('NEW_GRAFIK')){
	$shortcuts['btn_add_image'] = g_l('button', '[btn_add_image][alt]');
}

$jsLang = array();
foreach($shortcuts as $k => $v){
	$jsLang [] = "'" . $k . "':'" . $v . "'";
}

$oSctPool = new we_html_select(
	array(
	"name" => "sct_pool",
	"size" => 1,
	"class" => "defaultfont",
	"onchange" => "addBtn(_fo['list11'],this.options[this.selectedIndex].text,this.options[this.selectedIndex].value,true);this.options[0].selected=true;"
	)
);
$oSctPool->insertOption(0, " ", "", true);
$iCurrOpt = 1;
foreach($shortcuts as $key => $value){
	$oSctPool->insertOption($iCurrOpt, $key, $value, true);
	$iCurrOpt++;
}

$oSctList11 = new we_html_select(
	array(
	"multiple" => "multiple",
	"name" => "list11",
	"size" => 10,
	"style" => "width:200px;",
	"class" => "defaultfont",
	"onDblClick" => "moveSelectedOptions(this.form['list11'],this.form['list21'],false);"
	));
$oSctList21 = new we_html_select(
	array(
	"multiple" => "multiple",
	"name" => "list21",
	"size" => 10,
	"style" => "width:200px;",
	"class" => "defaultfont",
	"onDblClick" => "moveSelectedOptions(this.form['list21'],this.form['list11'],false);"
	));

$oBtnDelete = we_html_button::create_button(we_html_button::DELETE, "javascript:removeOption(document.forms[0]['list11']);removeOption(document.forms[0]['list21']);", false, -1, -1, "", "", false, false);
$oShortcutsRem = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[sct_rem]'), we_html_tools::TYPE_INFO, 420);

$oPool = new we_html_table(array("width" => 420, 'class' => 'default'), 3, 3);
$oPool->setCol(0, 0, null, $oSctList11->getHTML());
$oPool->setCol(0, 1, array('style' => 'text-align:center;vertical-align:middle;'), we_html_element::htmlA(
		array(
		"href" => "#",
		"onclick" => "moveOptionUp(document.forms[0]['list11']);moveOptionUp(document.forms[0]['list21']);return false;"
		), '<i class="fa fa-lg fa-caret-up"></i>') .
	we_html_element::htmlBr() . we_html_element::htmlBr() .
	we_html_element::htmlA(array(
		"href" => "#",
		"onclick" => "moveSelectedOptions(document.forms[0]['list11'],document.forms[0]['list21'],false);return false;"
		), '<i class="fa fa-lg fa-caret-right"></i>') .
	we_html_element::htmlBr() . we_html_element::htmlBr() .
	we_html_element::htmlA(array(
		"href" => "#",
		"onclick" => "moveSelectedOptions(document.forms[0]['list21'],document.forms[0]['list11'],false);return false;"
		), '<i class="fa fa-lg fa-caret-left"></i>') .
	we_html_element::htmlBr() . we_html_element::htmlBr() .
	we_html_element::htmlA(array(
		"href" => "#",
		"onclick" => "moveOptionDown(document.forms[0]['list11']);moveOptionDown(document.forms[0]['list21']);return false;"
		), '<i class="fa fa-lg fa-caret-down"></i>'
));
$oPool->setCol(0, 2, null, $oSctList21->getHTML());
$oPool->setCol(1, 0, array("colspan" => 3, 'style' => 'text-align:center;padding-top:5px;'), $oBtnDelete);

$content = $oShortcutsRem . we_html_element::htmlBr() . we_html_tools::htmlFormElementTable(
		$oSctPool->getHTML(), g_l('cockpit', '[select_buttons]'), "left", "defaultfont") . we_html_element::htmlBr() . $oPool->getHTML();

$parts = array(
	array(
		"headline" => "", "html" => $content, "space" => 0
	),
	array(
		"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getJS() . we_html_multiIconBox::getHTML("sctProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[shortcuts]'));

echo we_html_tools::getHtmlTop(g_l('cockpit', '[shortcuts]'), '', '', STYLESHEET .
	we_html_element::jsElement($jsPrefs . "
_aLang={" . implode(',', $jsLang) . "};
var g_l={
all_selected:'" . g_l('cockpit', '[all_selected]') . "',
	prefs_saved_successfully: '" . we_message_reporting::prepareMsgForJS(g_l('cockpit', '[prefs_saved_successfully]')) . "'
};
") .
	we_html_element::jsScript(JS_DIR . 'widgets/sct.js'), we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => "init();"
		), we_html_element::htmlForm("", $sTblWidget)));
