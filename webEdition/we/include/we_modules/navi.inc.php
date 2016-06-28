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

foreach($mods as $menuItem){
	if((!empty($menuItem['inModuleMenu'])) || (!empty($menuItem['inModuleWindow']))){
		if(we_base_moduleInfo::isActive($menuItem["name"])){ //	MODULE INSTALLED
			if(we_users_util::canEditModule($menuItem["name"])){
				$we_tabs->addTab(new we_tab(
						($menuItem['icon'] ? '<i class="fa fa-lg ' . $menuItem['icon'] . '"></i> ' : '') .
						$menuItem["text"]
						, ( $mod == $menuItem["name"]), "openModule('" . $menuItem["name"] . "');", array("id" => $menuItem["name"])));
			}
		}
	}
}
?>
<div id="main" ><?php echo $we_tabs->getHTML(); ?> </div>
