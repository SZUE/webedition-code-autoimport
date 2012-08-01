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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
we_html_tools::htmlTop();


$we_tabs = new we_tabs();

$name = array();

//	First sort the array after the text.
function order_available_modules($a, $b){
	return (strcmp($a["text"], $b["text"]));
}

uasort($_we_available_modules, "order_available_modules");


foreach($_we_available_modules as $_menuItem){
	if((isset($_menuItem["inModuleMenu"]) && $_menuItem["inModuleMenu"]) || (isset($_menuItem["inModuleWindow"]) && $_menuItem["inModuleWindow"])){
		if(in_array($_menuItem["name"], $GLOBALS['_we_active_integrated_modules'])){ //	MODULE INSTALLED
			if(we_userCanEditModule($_menuItem["name"])){
				$we_tabs->addTab(new we_tab("#", $_menuItem["text"], ( isset($_REQUEST["mod"]) && $_REQUEST["mod"] == $_menuItem["name"] ? "TAB_ACTIVE" : "TAB_NORMAL"), "openModule('" . $_menuItem["name"] . "');", array("id" => $_menuItem["name"])));
			}
		}
	}
}

$we_tabs->onResize('navi');
$tab_header = $we_tabs->getHeader('_modules', 1);
$tab_js = $we_tabs->getJS();

print $tab_header .
	we_html_element::jsElement('
	var current = "' . $_REQUEST["mod"] . '";
	function openModule(module) {
		if(top.content.hot =="1") {
			if(confirm("' . g_l('alert', '[discard_changed_data]') . '")) {
				if(typeof "top.content.usetHot" == "function") {top.content.usetHot();}
				current = module;
				top.content.location.replace("show.php?mod=" + module);
			} else {
				setActiveTab(current);
			}
		} else {
			if(typeof "top.content.usetHot" == "function") {top.content.usetHot();}
			current = module;
			top.content.location.replace("show.php?mod=" + module);

		}

	}' . $tab_js);
?>
</head>
<body style="margin:0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;" link="black" alink="#1559b0" vlink="black" onLoad="setFrameSize()" onResize="setFrameSize()">
	<div id="main" ><?php echo $we_tabs->getHTML(); ?> </div>
</body>
</html>