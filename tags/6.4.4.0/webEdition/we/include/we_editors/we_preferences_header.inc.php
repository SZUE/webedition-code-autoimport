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
$tabname = we_base_request::_(we_base_request::STRING,"tabname","setting_ui");
require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');


// generate the tabs

$we_tabs = new we_tabs();

foreach($GLOBALS['tabs'] as $name => $perm){
	if(empty($perm) || permissionhandler::hasPerm($perm)){
		$we_tabs->addTab(new we_tab("#", g_l('prefs', '[tab][' . $name . ']'), ($tabname === 'setting_' . $name ? we_tab::ACTIVE : we_tab::NORMAL), "top.we_cmd('" . $name . "');"));
	}
}

$we_tabs->onResize('naviDiv');
$tab_head = $we_tabs->getHeader('', 1);

function getPreferencesTabsDefaultHeight(){
	return 22;
}

function getPreferencesJS(){
	return '';
}

function getPreferencesCSS(){
	return $GLOBALS['we_tabs']->getHeader('', 1);
}

function getPreferencesHeader(){
	return '<div id="main" >' . $GLOBALS['we_tabs']->getHTML() . '</div>';
}
