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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* * ***************************************************************************
 * INCLUDES
 * *************************************************************************** */

include(WE_INCLUDES_PATH. 'we_editors/we_preferences_header.inc.php');

we_html_tools::protect();
we_html_tools::htmlTop();
print STYLESHEET.getPreferencesCSS();

$tabname = isset($_REQUEST["tabname"]) ? $_REQUEST["tabname"] : (isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : "setting_ui");

// Define needed JS
$_javascript = <<< END_OF_SCRIPT

function we_cmd() {
	var url = "/webEdition/we/include/we_editors/we_preferences.php?";

	switch (arguments[0]) {
		case "ui":
		case "editor":
		case "message_reporting":
END_OF_SCRIPT;

if(we_hasPerm("ADMINISTRATOR") || we_hasPerm("NEW_TEMPLATE")){
	$_javascript .= "
		case \"cache\":";
}

if(we_hasPerm("EDIT_SETTINGS_DEF_EXT")){
	$_javascript .= "
		case \"extensions\":";
}

if(we_hasPerm("EDIT_SETTINGS_DEF_EXT")){
	$_javascript .= "
		case \"recipients\":";
}

if(we_hasPerm("ADMINISTRATOR")){
	$_javascript .= "
		case \"proxy\":
		case \"advanced\":
		case \"system\":
		case \"seolinks\":
		case \"error_handling\":
		case \"backup\":
		case \"validation\":
		case \"language\":
		case \"countries\":
		case \"active_integrated_modules\":
		case \"versions\":
		case \"email\":";
}

if(we_hasPerm("FORMMAIL")){
	$_javascript .= "
		case \"recipients\":";
}

//if (we_hasPerm("ADMINISTRATOR") && defined("OBJECT_TABLE")) {
//	$_javascript .=	"
//		case \"modules\":";
//}

$_javascript .= <<< END_OF_SCRIPT
			we_preferences.document.getElementById('setting_ui').style.display = 'none';
			we_preferences.document.getElementById('setting_extensions').style.display = 'none';
			we_preferences.document.getElementById('setting_editor').style.display = 'none';
			we_preferences.document.getElementById('setting_recipients').style.display = 'none';
			we_preferences.document.getElementById('setting_proxy').style.display = 'none';
			we_preferences.document.getElementById('setting_advanced').style.display = 'none';
			we_preferences.document.getElementById('setting_system').style.display = 'none';
			we_preferences.document.getElementById('setting_seolinks').style.display = 'none';
			we_preferences.document.getElementById('setting_error_handling').style.display = 'none';
			//we_preferences.document.getElementById('setting_modules').style.display = 'none';
			we_preferences.document.getElementById('setting_backup').style.display = 'none';
			we_preferences.document.getElementById('setting_validation').style.display = 'none';
			we_preferences.document.getElementById('setting_language').style.display = 'none';
			we_preferences.document.getElementById('setting_countries').style.display = 'none';
			we_preferences.document.getElementById('setting_message_reporting').style.display = 'none';
			we_preferences.document.getElementById('setting_active_integrated_modules').style.display = 'none';
			we_preferences.document.getElementById('setting_email').style.display = 'none';
			we_preferences.document.getElementById('setting_versions').style.display = 'none';

			we_preferences.document.getElementById('setting_' + arguments[0]).style.display = '';

			break;
END_OF_SCRIPT;
/*				$menu = str_replace("\n", '"+"', addslashes($menu->getHTML(false)));
		return $location . 'document.getElementById("nav").parentNode.innerHTML="' . $menu . '";';
*/

$_javascript .= "
		case \"show_tabs\":
		//naviDiv.document.location = '" . WEBEDITION_DIR . "we/include/we_editors/we_preferences_header.php" . ($tabname != "" ? "?tabname=" . $tabname : "") . "';

			break;
	}
}
self.focus();

function closeOnEscape() {
	return true;

}

function saveOnKeyBoard() {
	window.frames[2].we_save();
	return true;

}";


print we_html_element::jsElement($_javascript) .
	we_html_element::jsScript(JS_DIR . "keyListener.js") . "</head>";

/*$frameset = new we_html_frameset(array("rows" => "38,*,40", "framespacing" => "0", "border" => "0", "frameborder" => "no"), 0);
$frameset->addFrame(array("src" => WEBEDITION_DIR . "html/white.html", "name" => "we_preferences_header", "scrolling" => "no", "noresize" => "noresize"));
$frameset->addFrame(array("src" => WEBEDITION_DIR . "we/include/we_editors/we_preferences.php?setting=ui" . ($tabname != "" ? "&tabname=" . $tabname : ""), "name" => "we_preferences", "scrolling" => "auto", "noresize" => "noresize"));
$frameset->addFrame(array("src" => WEBEDITION_DIR . "we/include/we_editors/we_preferences_footer.php", "name" => "we_preferences_footer", "scrolling" => "no", "noresize" => "noresize"));
*/
include(WE_INCLUDES_PATH. 'we_editors/we_preferences_footer.inc.php');

		$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;','onload'=>'setFrameSize()', 'onresize' => 'setFrameSize()')
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlExIFrame('navi', getPreferencesHeader(), 'position:absolute;top:0px;height:'.getPreferencesTabsDefaultHeight().'px;left:0px;right:0px;overflow: hidden;') .
					we_html_element::htmlIFrame('content', WEBEDITION_DIR . "we/include/we_editors/we_preferences.php?setting=ui" . ($tabname != "" ? "&tabname=" . $tabname : ""), 'position:absolute;top:'.getPreferencesTabsDefaultHeight().'px;bottom:40px;left:0px;right:0px;overflow: hidden;') .
					we_html_element::htmlExIFrame('we_preferences_footer',getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
				));

print we_html_element::htmlBody(array(),$body).getPreferencesJS().  getPreferencesFooterJS() . '</html>';

