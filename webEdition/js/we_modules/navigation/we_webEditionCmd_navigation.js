/* global WE */

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
we_cmd_modules.navigation = function (args, url) {
	switch (args[0]) {
		case "navigation_edit":
		case "navigation_edit_ifthere":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "module_navigation_new":
		case "module_navigation_new_group":
		case "exit_navigation":
		case "module_navigation_save":
		case "module_navigation_delete":
		case "module_navigation_reset_customer_filter":
			var wind = WE().util.jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				if (args[0] !== "empty_log") {
					wind.focus();
				}
			}
			return true;
		case "module_navigation_rules":
			WE().util.jsWindow.prototype.focus('edit_module');
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=ruleFrameset", "tool_navigation_rules", -1, -1, 680, 580, true, true, true, true);
			return true;
		case "module_navigation_edit_navi":
			new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=weNaviEditor&we_cmd[1]=" + args[1], "we_navieditor", -1, -1, 600, 350, true, false, true, true);
			return true;
		case "module_navigation_do_reset_customer_filter":
			we_repl(window.load, url, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_navigation_do_reset_customer_filter");
			return true;
	}
	return false;
};