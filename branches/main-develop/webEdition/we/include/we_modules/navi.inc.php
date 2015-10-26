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

//TODO: remove when implemented completely
$mods = we_base_moduleInfo::getAllModules();
we_base_moduleInfo::orderModuleArray($mods);
//END TODO
$mod = we_base_request::_(we_base_request::STRING, 'mod', '');

foreach($mods as $_menuItem){
	if((!empty($_menuItem["inModuleMenu"])) || (!empty($_menuItem["inModuleWindow"]))){
		if(we_base_moduleInfo::isActive($_menuItem["name"])){ //	MODULE INSTALLED
			if(we_users_util::canEditModule($_menuItem["name"])){
				$we_tabs->addTab(new we_tab($_menuItem["text"], ( $mod == $_menuItem["name"] ? we_tab::ACTIVE : we_tab::NORMAL), "openModule('" . $_menuItem["name"] . "');", array("id" => $_menuItem["name"])));
			}
		}
	}
}

$tab_header = we_tabs::getHeader();

echo $tab_header .
 we_html_element::jsElement('
	var current = "' . $mod . '";
	function openModule(module) {
		if(top.content.hot =="1") {
			if(confirm("' . g_l('alert', '[discard_changed_data]') . '")) {
				if(typeof "top.content.usetHot" == "function") {top.content.usetHot();}
				current = module;
				top.content.location.replace(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=" + module);
			} else {
				weTabs.setActiveTab(current);
			}
		} else {
			if(typeof "top.content.usetHot" == "function") {top.content.usetHot();}
			current = module;
			top.content.location.replace(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=" + module);

		}

	}');
?>
<div id="main" ><?php echo $we_tabs->getHTML(); ?> </div>
