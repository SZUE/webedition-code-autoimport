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
echo we_html_tools::getHtmlTop();

$we_tabs = new we_tabs();

$name = array();

$_menuItems = we_tool_lookup::getAllTools(true, true);
$tool = we_base_request::_(we_base_request::STRING, 'tool');

foreach($_menuItems as $_menuItem){
	$text = $_menuItem["text"];
	if($_menuItem["name"] === 'toolfactory'){
		if(permissionhandler::hasPerm($_menuItem['startpermission'])){
			$we_tabs->addTab(new we_tab("#", $text, ($tool == $_menuItem["name"] ? we_tab::ACTIVE : we_tab::NORMAL), "openTool('" . $_menuItem["name"] . "');", array("id" => $_menuItem["name"])));
		}
	}
}

foreach($_menuItems as $_menuItem){
	switch($_menuItem["name"]){
		case "weSearch":
			$text = g_l('searchtool', '[weSearch]');
			break;
		case "navigation":
			$text = g_l('navigation', '[navigation]');
			break;
		case 'toolfactory':
			$text = $_menuItem["text"];
			break;
		default:
			$text = $_menuItem["text"];
			if(permissionhandler::hasPerm($_menuItem['startpermission'])){
				$we_tabs->addTab(new we_tab("#", $text, ($tool == $_menuItem["name"] ? we_tab::ACTIVE : we_tab::NORMAL), "openTool('" . $_menuItem["name"] . "');", array("id" => $_menuItem["name"])));
			}
	}
}
switch($tool){
	case "weSearch":
		$we_tabs->heightPlus = -30;
		break;
}

$we_tabs->onResize('navi');
$tab_header = $we_tabs->getHeader('_tools', 1);

echo $tab_header;
?>
<script type="text/javascript"><!--
	var current = "<?php echo $tool; ?>";
	function openTool(tool) {
		if (top.content.hot == "1") {
			if (confirm("<?php echo g_l('alert', '[discard_changed_data]') ?>")) {
				top.content.hot = "0";
				current = tool;
				top.content.location.replace('tools_content.php?tool=' + tool);
			} else {
				top.navi.setActiveTab(current);
			}
		} else {
			top.content.hot = "0";
			current = tool;
			top.content.location.replace('tools_content.php?tool=' + tool);

		}

	}
	//-->
</script>
</head>
<body style="background: #C8D8EC url(<?php echo IMAGE_DIR; ?>backgrounds/header.gif);margin: 0px 0px 0px 0px;" link="black" alink="#1559b0" vlink="black" onload="setFrameSize()" onresize="setFrameSize()">
	<div id="main" ><?php echo $we_tabs->getHTML(); ?></div>
</body>
</html>
