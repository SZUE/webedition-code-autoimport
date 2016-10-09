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
list($jsFile, $oSelCls) = include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');
we_html_tools::protect();
$disableNew = true;
$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "');";
if(permissionhandler::hasPerm("NEW_WEBEDITIONSITE")){
	if(permissionhandler::hasPerm("NO_DOCTYPE")){
		$disableNew = false;
	} else {
		$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
		$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');

		if($id){
			$disableNew = false;
			$cmdNew = "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','" . $id . "')";
		} else {
			$disableNew = true;
		}
	}
} else {
	$disableNew = true;
}

$disableObjects = false;
if(defined('OBJECT_TABLE')){
	$allClasses = we_users_util::getAllowedClasses();
	if(empty($allClasses)){
		$disableObjects = true;
	}
}

$shortcuts = array_filter([
	'open_document' => (defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') ? g_l('button', '[open_document][value]') : ''),
	'new_document' => (defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') && !$disableNew ? g_l('button', '[new_document][value]') : ''),
	'new_template' => (defined('TEMPLATES_TABLE') && permissionhandler::hasPerm('NEW_TEMPLATE') ? g_l('button', '[new_template][value]') : ''),
	'new_directory' => (permissionhandler::hasPerm('NEW_DOC_FOLDER') ? g_l('button', '[new_directory][value]') : ''),
	'unpublished_pages' => (defined('FILE_TABLE') && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS') ? g_l('button', '[unpublished_pages][value]') : ''),
	'unpublished_objects' => (defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES') && !$disableObjects ? g_l('button', '[unpublished_objects][value]') : ''),
	'new_object' => (defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('NEW_OBJECTFILE') && !$disableObjects ? g_l('button', '[new_object][value]') : ''),
	'new_class' => (defined('OBJECT_TABLE') && permissionhandler::hasPerm('NEW_OBJECT') ? g_l('button', '[new_class][value]') : ''),
	'preferences' => (permissionhandler::hasPerm('EDIT_SETTINGS') ? g_l('button', '[preferences][value]') : ''),
	'btn_add_image' => (permissionhandler::hasPerm('NEW_GRAFIK') ? g_l('button', '[btn_add_image][alt]') : '')
	]);

$oSctPool = new we_html_select([
	"name" => "sct_pool",
	'class' => 'defaultfont',
	"onchange" => "addBtn(_fo['list11'],this.options[this.selectedIndex].text,this.options[this.selectedIndex].value,true);this.options[0].selected=true;"
	]
);
$oSctPool->insertOption(0, " ", "", true);
$iCurrOpt = 1;
foreach($shortcuts as $key => $value){
	$oSctPool->insertOption($iCurrOpt, $key, $value, true);
	$iCurrOpt++;
}

$oSctList11 = new we_html_select(["multiple" => "multiple",
	"name" => "list11",
	"size" => 10,
	"style" => "width:200px;",
	'class' => 'defaultfont',
	"onDblClick" => "moveSelectedOptions(this.form['list11'],this.form['list21'],false);"
	]);
$oSctList21 = new we_html_select(["multiple" => "multiple",
	"name" => "list21",
	"size" => 10,
	"style" => "width:200px;",
	'class' => 'defaultfont',
	"onDblClick" => "moveSelectedOptions(this.form['list21'],this.form['list11'],false);"
	]);

$oBtnDelete = we_html_button::create_button(we_html_button::DELETE, "javascript:removeOption(document.forms[0]['list11']);removeOption(document.forms[0]['list21']);", false, -1, -1, "", "", false, false);
$oShortcutsRem = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[sct_rem]'), we_html_tools::TYPE_INFO, 420);

$oPool = new we_html_table(["width" => 420, 'class' => 'default'], 3, 3);
$oPool->setCol(0, 0, null, $oSctList11->getHTML());
$oPool->setCol(0, 1, array('style' => 'text-align:center;vertical-align:middle;'), we_html_element::htmlA(array(
		"href" => "#",
		"onclick" => "moveOptionUp(document.forms[0]['list11']);moveOptionUp(document.forms[0]['list21']);return false;"
		), '<i class="fa fa-lg fa-caret-up"></i>') .
	we_html_element::htmlBr() . we_html_element::htmlBr() .
	we_html_element::htmlA(["href" => "#",
		"onclick" => "moveSelectedOptions(document.forms[0]['list11'],document.forms[0]['list21'],false);return false;"
		], '<i class="fa fa-lg fa-caret-right"></i>') .
	we_html_element::htmlBr() . we_html_element::htmlBr() .
	we_html_element::htmlA(["href" => "#",
		"onclick" => "moveSelectedOptions(document.forms[0]['list21'],document.forms[0]['list11'],false);return false;"
		], '<i class="fa fa-lg fa-caret-left"></i>') .
	we_html_element::htmlBr() . we_html_element::htmlBr() .
	we_html_element::htmlA(["href" => "#",
		"onclick" => "moveOptionDown(document.forms[0]['list11']);moveOptionDown(document.forms[0]['list21']);return false;"
		], '<i class="fa fa-lg fa-caret-down"></i>'
));
$oPool->setCol(0, 2, null, $oSctList21->getHTML());
$oPool->setCol(1, 0, ["colspan" => 3, 'style' => 'text-align:center;padding-top:5px;'], $oBtnDelete);

$content = $oShortcutsRem . we_html_element::htmlBr() . we_html_tools::htmlFormElementTable(
		$oSctPool->getHTML(), g_l('cockpit', '[select_buttons]'), "left", "defaultfont") . we_html_element::htmlBr() . $oPool->getHTML();

$parts = [
	["headline" => "", "html" => $content,
	],
	["headline" => "", "html" => $oSelCls->getHTML(),
	]
];

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();");
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getJS() . we_html_multiIconBox::getHTML("sctProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[shortcuts]'));

echo we_html_tools::getHtmlTop(g_l('cockpit', '[shortcuts]'), '', '', $jsFile .
	we_html_element::jsScript(JS_DIR . 'widgets/sct.js', '', ['id' => 'loadVarWidget', 'data-widget' => setDynamicVar([
			'aLang' => $shortcuts,
		])]
	), we_html_element::htmlBody(
		["class" => "weDialogBody", "onload" => "init();"
		], we_html_element::htmlForm("", $sTblWidget)));
