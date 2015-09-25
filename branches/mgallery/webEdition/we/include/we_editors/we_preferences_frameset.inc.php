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

include(WE_INCLUDES_PATH . 'we_editors/we_preferences_header.inc.php');
require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

we_html_tools::protect();
echo we_html_tools::getHtmlTop() .
 STYLESHEET . getPreferencesCSS();

$tabname = we_base_request::_(we_base_request::STRING, "tabname", we_base_request::_(we_base_request::STRING, 'we_cmd', "setting_ui", 1));

// Define needed JS
$_javascript = <<< END_OF_SCRIPT
var WE=opener.WE;
function we_cmd() {
	//var url = "/webEdition/we/include/we_editors/we_preferences.php?";

	switch (arguments[0]) {
END_OF_SCRIPT;
foreach($GLOBALS['tabs'] as $name => $perm){
	if(empty($perm) || permissionhandler::hasPerm($perm)){
		$_javascript.='case "' . $name . '":' . "\n";
	}
}
foreach($GLOBALS['tabs'] as $name => $perm){
	$_javascript.="try{content.document.getElementById('setting_" . $name . "').style.display = 'none';}catch(e){}";
}

$_javascript .= "
	try{
			content.document.getElementById('setting_' + arguments[0]).style.display = '';
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
 we_html_element::jsScript(JS_DIR . "keyListener.js") . "</head>";

include(WE_INCLUDES_PATH . 'we_editors/we_preferences_footer.inc.php');

$body = we_html_element::htmlBody(array('id' => 'weMainBody', 'onload' => 'setFrameSize()', 'onresize' => 'setFrameSize()')
		, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
			, we_html_element::htmlExIFrame('navi', getPreferencesHeader(), 'position:absolute;top:0px;height:' . getPreferencesTabsDefaultHeight() . 'px;left:0px;right:0px;overflow: hidden;') .
			we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . "we_editors/we_preferences.php?" . ($tabname ? "tabname=" . $tabname : ""), 'position:absolute;top:' . getPreferencesTabsDefaultHeight() . 'px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'border:0px;width:100%;height:100%;overflow: scroll;') .
			we_html_element::htmlExIFrame('we_preferences_footer', getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
	));

echo we_html_element::htmlBody(array(), $body) . getPreferencesJS() . getPreferencesFooterJS() . '</html>';
