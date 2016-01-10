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
		$we_tabs->addTab(new we_tab(($icon ? '<i class="fa fa-lg ' . $icon . '"></i> ' : '') . g_l('prefs', '[tab][' . $name . ']'), ($tabname === 'setting_' . $name ? we_tab::ACTIVE : we_tab::NORMAL), "top.we_cmd('" . $name . "');"));
	}
}

function getPreferencesHeader(){
	return '<div id="main" >' . $GLOBALS['we_tabs']->getHTML() . '</div>';
}

we_html_tools::protect();
echo we_html_tools::getHtmlTop() .
 STYLESHEET . we_tabs::getHeader();

$tabname = we_base_request::_(we_base_request::STRING, "tabname", we_base_request::_(we_base_request::STRING, 'we_cmd', "setting_ui", 1));

// Define needed JS
$_javascript = <<< END_OF_SCRIPT
var WE=opener.WE;
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
END_OF_SCRIPT;
foreach(array_keys($GLOBALS['tabs']) as $name){
	if(empty($perm) || permissionhandler::hasPerm($perm)){
		$_javascript.='case "' . $name . '":';
	}
}
foreach(array_keys($GLOBALS['tabs']) as $name){
	$_javascript.="try{content.document.getElementById('setting_" . $name . "').style.display = 'none';}catch(e){}";
}

$_javascript .= "
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


echo we_html_element::jsElement($_javascript) .
 "</head>";

include(WE_INCLUDES_PATH . 'we_editors/we_preferences_footer.inc.php');

$body = we_html_element::htmlBody(array('id' => 'weMainBody', 'onload' => 'weTabs.setFrameSize()', 'onresize' => 'weTabs.setFrameSize()')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
						, we_html_element::htmlExIFrame('navi', getPreferencesHeader(), 'right:0px;') .
						we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . "we_editors/we_preferences.php?" . ($tabname ? "tabname=" . $tabname : ""), 'position:absolute;top:22px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'border:0px;width:100%;height:100%;overflow: scroll;') .
						we_html_element::htmlExIFrame('we_preferences_footer', getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
		));

echo we_html_element::htmlBody(array(), $body) . getPreferencesFooterJS() . '</html>';
