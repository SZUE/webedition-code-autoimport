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

$name = array();

$menuItems = we_tool_lookup::getAllTools(true, false);
$tool = we_base_request::_(we_base_request::STRING, 'tool');

// temporarily show search-tab (on first position)
foreach($menuItems as $menuItem){
	if($menuItem["name"] === 'weSearch'){
		$text = g_l('searchtool', '[weSearch]');
		if(permissionhandler::hasPerm($menuItem['startpermission'])){
			$we_tabs->addTab(new we_tab($text, ($tool == $menuItem["name"]), "openTool('" . $menuItem["name"] . "');", array("id" => $menuItem["name"])));
		}
	}
}

foreach($menuItems as $menuItem){
	$text = $menuItem["text"];
	if($menuItem["name"] === 'toolfactory'){
		if(permissionhandler::hasPerm($menuItem['startpermission'])){
			$we_tabs->addTab(new we_tab($text, ($tool == $menuItem["name"]), "openTool('" . $menuItem["name"] . "');", array("id" => $menuItem["name"])));
		}
	}
}

foreach($menuItems as $menuItem){
	switch($menuItem["name"]){
		case "weSearch":
			$text = g_l('searchtool', '[weSearch]');
			break;
		case 'toolfactory':
			$text = $menuItem["text"];
			break;
		default:
			$text = $menuItem["text"];
			if(permissionhandler::hasPerm($menuItem['startpermission'])){
				$we_tabs->addTab(new we_tab($text, ($tool == $menuItem["name"]), "openTool('" . $menuItem["name"] . "');", array("id" => $menuItem["name"])));
			}
	}
}

echo we_tabs::getHeader();
?>
<div id="main" ><?php echo $we_tabs->getHTML(); ?></div>
<script><!--
	var current = "<?php echo $tool; ?>";
	function openTool(tool) {
		if (top.content.hot === 1) {
			if (confirm("<?php echo g_l('alert', '[discard_changed_data]') ?>")) {
				top.content.hot = 0;
				current = tool;
				top.content.location.replace(WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/tools_content.php?tool=" + tool);
			} else {
				top.navi.weTabs.setActiveTab(current);
			}
		} else {
			top.content.hot = 0;
			current = tool;
			top.content.location.replace(WE().consts.dirs.WE_INCLUDES_DIR + "we_tools/tools_content.php?tool=" + tool);
		}
	}

	//-->
</script>
