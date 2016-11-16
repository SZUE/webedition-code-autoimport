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
$we_tabs = new we_tabs();

$name = [];

$menuItems = we_tool_lookup::getAllTools(true, false);
$tool = we_base_request::_(we_base_request::STRING, 'tool');

foreach($menuItems as $menuItem){
	switch($menuItem['name']){
		case "weSearch":
			continue;
		case 'toolfactory':
			$text = $menuItem["text"];
			break;
	}
	if(permissionhandler::hasPerm($menuItem['startpermission'])){
		$we_tabs->addTab($menuItem["text"], ($tool == $menuItem['name']), "'" . $menuItem['name'] . "'", ["id" => $menuItem['name']]);
	}
}

echo we_tabs::CSS . we_html_element::jsElement(
				we_tabs::JS_LOAD . '
var current = "' . $tool . '";
function setTab(tool) {
	if (top.content.hot) {
		if (confirm("' . g_l('alert', '[discard_changed_data]') . '")) {
			top.content.hot = false;
			current = tool;
			top.content.location.replace(WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/tools_content.php?tool=" + tool);
		} else {
			top.navi.weTabs.setActiveTab(current);
		}
	} else {
		top.content.hot = false;
		current = tool;
		top.content.location.replace(WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/tools_content.php?tool=" + tool);
	}
}
');
?>
<div id="main" ><?= $we_tabs->getHTML(); ?></div>
