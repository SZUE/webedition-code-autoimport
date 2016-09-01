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
/* * ***************************************************************************
 * INCLUDES
 * *************************************************************************** */

require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');


$tabname = we_base_request::_(we_base_request::STRING, "tabname", "setting_ui");


// generate the tabs

$we_tabs = new we_tabs();

foreach($GLOBALS['tabs'] as $name => $list){
	list($icon, $perm) = $list;
	if(empty($perm) || permissionhandler::hasPerm($perm)){
		$we_tabs->addTab(($icon ? '<i class="fa fa-lg ' . $icon . '"></i> ' : '') . g_l('prefs', '[tab][' . $name . ']'), ($tabname === 'setting_' . $name), "top.we_cmd('" . $name . "');");
	}
}

function getPreferencesHeader(){
	return '<div id="main" >' . $GLOBALS['we_tabs']->getHTML() . '</div>';
}

we_html_tools::protect();

$tabname = we_base_request::_(we_base_request::STRING, "tabname", we_base_request::_(we_base_request::STRING, 'we_cmd', "setting_ui", 1));

// Define needed JS
$javascript = <<< EOS
var WE=opener.WE;
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
EOS;
foreach(array_keys($GLOBALS['tabs']) as $name){
	if(empty($perm) || permissionhandler::hasPerm($perm)){
		$javascript.='case "' . $name . '":
';
	}
}
$javascript.='default:
';
foreach(array_keys($GLOBALS['tabs']) as $name){
	$javascript.="try{
		content.document.getElementById('setting_" . $name . "').style.display = 'none';
	}catch(e){}
";
}

$javascript .= "
		try{
				content.document.getElementById('setting_' + args[0]).style.display = '';
		}catch(e){}
	break;
	}
}
self.focus();

function closeOnEscape() {
	return true;

}

function saveOnKeyBoard() {
	this.we_save();
	return true;

}";

function getPreferencesFooterJS(){
	$tmp = '';
	foreach(array_keys($GLOBALS['tabs']) as $key){
		$tmp.="document.getElementById('content').contentDocument.getElementById('setting_" . $key . "').style.display = 'none';";
	}
	$javascript = <<< EOS
var countSaveTrys = 0;
function we_save() {
		$tmp
	// update setting for message_reporting
	WE().session.messageSettings = document.getElementById('content').contentDocument.getElementById("message_reporting").value;

	if(WE().layout.weEditorFrameController.getActiveDocumentReference().quickstart){
		var oCockpit=WE().layout.weEditorFrameController.getActiveDocumentReference();
		var _fo=document.getElementById('content').contentDocument.forms[0];
		var oSctCols=_fo.elements['newconf[cockpit_amount_columns]'];
		var iCols=oSctCols.options[oSctCols.selectedIndex].value;
		if(iCols!=oCockpit._iLayoutCols){
			oCockpit.modifyLayoutCols(iCols);
		}
	}

	document.getElementById('content').contentDocument.getElementById('setting_save').style.display = '';
	document.getElementById('content').contentDocument.we_form.save_settings.value = 1;

	document.getElementById('content').contentDocument.we_form.submit();
 }

EOS;
	return we_html_element::jsElement($javascript);
}

function getPreferencesFooter(){
	$okbut = we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save();');
	$cancelbut = we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.close()');

	return we_html_element::htmlDiv(['class' => 'weDialogButtonsBody', 'style' => 'height:100%;'], we_html_button::position_yes_no_cancel($okbut, '', $cancelbut, 10, '', '', 0));
}

echo we_html_tools::getHtmlTop('', '', '', we_tabs::getHeader() .
	we_html_element::jsElement($javascript));

$body = we_html_element::htmlBody(['id' => 'weMainBody', 'onload' => 'weTabs.setFrameSize()', 'onresize' => 'weTabs.setFrameSize()']
, we_html_element::htmlDiv(['style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;']
, we_html_element::htmlExIFrame('navi', getPreferencesHeader(), 'right:0px;') .
			we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . "we_editors/we_preferences.php?" . ($tabname ? "tabname=" . $tabname : ""), 'position:absolute;top:22px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'border:0px;width:100%;height:100%;overflow: scroll;') .
			we_html_element::htmlExIFrame('we_preferences_footer', getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
	));

echo we_html_element::htmlBody([], $body) . getPreferencesFooterJS() . '</html>';
