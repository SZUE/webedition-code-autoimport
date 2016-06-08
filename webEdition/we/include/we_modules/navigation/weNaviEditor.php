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
we_html_tools::protect();

$_path = we_base_request::_(we_base_request::FILE, 'we_cmd', '', 1);
$_id = (!empty($_path)) ? path_to_id($_path, NAVIGATION_TABLE, $GLOBALS['DB_WE']) : 0;
$_navi = new we_navigation_navigation($_id);
$_wrkNavi = array();
$_db = new DB_WE();

if(permissionhandler::hasPerm('ADMINISTRATOR')){
	$_dirs = array(
		'0' => '/'
	);
	$_def = 0;
} else {
	$_dirs = array();
	$_def = null;
}

if($_id){
	$_def = $_navi->ParentID;
}

$_db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=1 ' . we_navigation_navigation::getWSQuery() . ' ORDER BY Path');
while($_db->next_record()){
	if($_def === null){
		$_def = $_db->f('ID');
	}
	$_dirs[$_db->f('ID')] = $_db->f('Path');
}

$_parts = array(
	array(
		'headline' => g_l('navigation', '[name]'),
		'html' => we_html_tools::htmlTextInput('Text', 24, $_navi->Text, '', 'style="width:440px;" onblur="setSaveState();" onkeyup="setSaveState();"'),
		'space' => we_html_multiIconBox::SPACE_SMALL,
		'noline' => 1
	),
	array(
		'headline' => g_l('navigation', '[group]'),
		'html' => we_html_tools::htmlSelect('ParentID', $_dirs, 1, $_navi->ParentID, false, array('style' => 'width:440px;', 'onchange' => 'queryEntries(this.value);')),
		'space' => we_html_multiIconBox::SPACE_SMALL,
		'noline' => 1
	),
	array(
		'headline' => '',
		'html' => '<div id="details" class="blockWrapper" style="width: 440px;height: 100px;"></div>',
		'space' => we_html_multiIconBox::SPACE_SMALL,
		'noline' => 1
	),
	array(
		'headline' => g_l('navigation', '[order]'),
		'html' => we_html_element::htmlHidden('Ordn', $_navi->Ordn) .
		we_html_tools::htmlTextInput('OrdnTxt', 8, ($_navi->Ordn + 1), '', 'onchange="document.we_form.Ordn.value=(document.we_form.OrdnTxt.value-1);"', 'text', 117) .
		we_html_tools::htmlSelect('OrdnSelect', array('begin' => g_l('navigation', '[begin]'), 'end' => g_l('navigation', '[end]')), 1, '', false, array('onchange' => 'changeOrder(this);'), 'value', 317),
		'space' => we_html_multiIconBox::SPACE_SMALL,
		'noline' => 1
	)
);

$buttonsBottom = '<div style="float:right">' .
	we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:save();', true, 100, 22, '', '', ($_id ? false : true), false), null, we_html_button::create_button(we_html_button::CLOSE, 'javascript:self.close();')) . '</div>';

$_body = we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => 'loaded=1;queryEntries(' . $_def . ')'
		), we_html_element::htmlForm(
			array(
			"name" => "we_form", "onsubmit" => "return false"
			), we_html_multiIconBox::getHTML('', $_parts, 30, $buttonsBottom, -1, '', '', false, g_l('navigation', '[add_navigation]'))));

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
	YAHOO_FILES .
	we_html_element::jsElement('var WE_NAVIID=' . intval($_id) . ';') .
	we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/weNaviEditor.js')
	, $_body);
