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

we_html_tools::protect();
require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

$tabname = we_base_request::_(we_base_request::STRING, "tabname", we_base_request::_(we_base_request::STRING, 'we_cmd', "setting_ui", 1));

// generate the tabs
$we_tabs = new we_tabs();
$validTabs = [];

foreach($GLOBALS['tabs'] as $name => $list){
	list($icon, $perm) = $list;
	if(empty($perm) || permissionhandler::hasPerm($perm)){
		$we_tabs->addTab(($icon ? '<i class="fa fa-lg ' . $icon . '"></i> ' : '') . g_l('prefs', '[tab][' . $name . ']'), ($tabname === 'setting_' . $name), $name);
		$validTabs[] = $name;
	}
}

function getPreferencesFooter(){
	$okbut = we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save();');
	$cancelbut = we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.close()');

	return we_html_element::htmlDiv(['class' => 'weDialogButtonsBody', 'style' => 'height:100%;'], we_html_button::position_yes_no_cancel($okbut, '', $cancelbut, 10, '', '', 0));
}

echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(we_tabs::CSS) .
	we_html_element::jsScript(JS_DIR . 'preferences_frameset.js', 'self.focus();', ['id' => 'loadVarPreferences_frameset', 'data-prefData' => setDynamicVar([
			'tabs' => array_keys($GLOBALS['tabs']),
			'validTabs' => $validTabs,
	])]), we_html_element::htmlBody(['id' => 'weMainBody', 'onload' => 'weTabs.setFrameSize()', 'onresize' => 'weTabs.setFrameSize()']
		, we_html_element::htmlDiv(['style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;']
			, we_html_element::htmlExIFrame('navi', '<div id="main" >' . $GLOBALS['we_tabs']->getHTML() . '</div>', 'right:0px;') .
			we_html_element::htmlIFrame('content', WE_INCLUDES_DIR . "we_editors/we_preferences.php?" . ($tabname ? "tabname=" . $tabname : ""), 'position:absolute;top:22px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'border:0px;width:100%;height:100%;overflow: scroll;') .
			we_html_element::htmlExIFrame('we_preferences_footer', getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
)));
