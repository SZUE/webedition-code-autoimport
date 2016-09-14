/*
 * webEdition CMS
 *
 * $Rev: 12762 $
 * $Author: mokraemer $
 * $Date: 2016-09-13 13:38:06 +0200 (Di, 13. Sep 2016) $
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

/* global WE, top */
var moduleData = WE().util.getDynamicVar(document, 'loadVarShowMod', 'data-moduleData');

var weTabs = new (WE().layout.we_tabs)(document, window);

var makeNewEntryCheck = 0;
var publishWhenSave = 0;
var weModuleWindow = true;

function we_cmd() {
	top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}

var current = moduleData.mod;
function openModule(module) {
	if (top.content.hot) {
		if (confirm(WE().consts.g_l.alert.discard_changed_data)) {
			if (typeof "top.content.usetHot" == "function") {
				top.content.usetHot();
			}
			current = module;
			top.content.location.replace(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=" + module);
		} else {
			weTabs.setActiveTab(current);
		}
	} else {
		if (typeof "top.content.usetHot" == "function") {
			top.content.usetHot();
		}
		current = module;
		top.content.location.replace(WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=" + module);
	}
}