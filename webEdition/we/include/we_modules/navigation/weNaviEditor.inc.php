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
$path = we_base_request::_(we_base_request::FILE, 'we_cmd', '', 1);
$id = (!empty($path)) ? path_to_id($path, NAVIGATION_TABLE, $GLOBALS['DB_WE']) : 0;
$navi = new we_navigation_navigation($id);
$db = new DB_WE();

if(permissionhandler::hasPerm('ADMINISTRATOR')){
	$dirs = array(
		'0' => '/'
	);
	$def = 0;
} else {
	$dirs = array();
	$def = null;
}

if($id){
	$def = $navi->ParentID;
}

$db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=1 ' . we_navigation_navigation::getWSQuery() . ' ORDER BY Path');
while($db->next_record()){
	if($def === null){
		$def = $db->f('ID');
	}
	$dirs[$db->f('ID')] = $db->f('Path');
}

$parts = array(
	array(
		'headline' => g_l('navigation', '[name]'),
		'html' => we_html_tools::htmlTextInput('Text', 24, $navi->Text, '', 'style="width:440px;" onblur="setSaveState();" onkeyup="setSaveState();"'),
		'space' => we_html_multiIconBox::SPACE_MED,
		'noline' => 1
	),
	array(
		'headline' => g_l('navigation', '[group]'),
		'html' => we_html_tools::htmlSelect('ParentID', $dirs, 1, $navi->ParentID, false, array('style' => 'width:440px;', 'onchange' => 'queryEntries(this.value);')),
		'space' => we_html_multiIconBox::SPACE_MED,
		'noline' => 1
	),
	array(
		'headline' => '',
		'html' => '<div id="details" class="blockWrapper" style="width: 440px;height: 100px;"></div>',
		'space' => we_html_multiIconBox::SPACE_MED,
		'noline' => 1
	),
	array(
		'headline' => g_l('navigation', '[order]'),
		'html' => we_html_element::htmlHidden('Ordn', $navi->Ordn) .
		we_html_tools::htmlTextInput('OrdnTxt', 8, ($navi->Ordn + 1), '', 'onchange="document.we_form.Ordn.value=(document.we_form.OrdnTxt.value-1);"', 'text', 117) .
		we_html_tools::htmlSelect('OrdnSelect', array('begin' => g_l('navigation', '[begin]'), 'end' => g_l('navigation', '[end]')), 1, '', false, array('onchange' => 'changeOrder(this);'), 'value', 317),
		'space' => we_html_multiIconBox::SPACE_MED,
		'noline' => 1
	)
);

$buttonsBottom = '<div style="float:right">' .
	we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:top.save();', true, 100, 22, '', '', ($id ? false : true), false), null, we_html_button::create_button(we_html_button::CLOSE, 'javascript:self.close();')) . '</div>';

$body = we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onload" => 'loaded=1;queryEntries(' . $def . ')'
		), we_html_element::htmlForm(
			array(
			"name" => "we_form", "onsubmit" => "return false"
			), we_html_multiIconBox::getHTML('', $parts, 30, $buttonsBottom, -1, '', '', false, g_l('navigation', '[add_navigation]'))));

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
	YAHOO_FILES .
	we_html_element::jsElement('var WE_NAVIID=' . intval($id) . ';') .
	we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/weNaviEditor.js')
	, $body);
