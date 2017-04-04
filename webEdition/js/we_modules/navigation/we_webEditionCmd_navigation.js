/* global WE, we_cmd_modules */

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
'use strict';
we_cmd_modules.navigation = function (args, url, caller) {
	switch (args[0]) {
		case "navigation_edit":
			new (WE().util.jsWindow)(caller, url, "edit_module", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			return true;
		case "module_navigation_new":
		case "module_navigation_new_group":
		case "exit_navigation":
		case "module_navigation_save":
		case "module_navigation_delete":
		case "module_navigation_reset_customer_filter":
		case "module_navigation_rules":
			WE().layout.pushCmdToModule(args);
			return true;
		case "module_navigation_edit_navi":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=weNaviEditor&we_cmd[1]=" + args[1], "we_navieditor", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true, true);
			return true;
	}
	return false;
};