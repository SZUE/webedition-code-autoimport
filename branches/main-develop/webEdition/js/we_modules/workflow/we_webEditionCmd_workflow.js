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
we_cmd_modules.workflow = function (args, url, caller) {
	switch (args[0]) {
		case "workflow_isIn":
		case "workflow_pass":
		case "workflow_decline":
			new (WE().util.jsWindow)(caller, url, "choose_workflow", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true, true);
			return true;
		case "workflow_finish":
			window.we_repl(window.load, url);
			return true;
		case "workflow_edit":
			new (WE().util.jsWindow)(caller, url, "edit_module", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			return true;
		case "new_user":
		case "exit_workflow":
//case "reload_workflow":
		case "save_workflow":
		case "new_workflow":
		case "delete_workflow":
		case "empty_log":
			WE().layout.pushCmdToModule(args);
			return true;
	}
	return false;
};