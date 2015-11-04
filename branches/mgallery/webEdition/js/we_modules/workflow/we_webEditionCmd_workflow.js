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
function we_cmd_workflow() {
	var args = arguments[0],
		url = arguments[1];

	switch (args[0]) {
		case "workflow_isIn":
		case "workflow_pass":
		case "workflow_decline":
			new (WE().util.jsWindow)(this, url, "choose_workflow", -1, -1, 420, 320, true, true, true, true);
			return true;
		case "workflow_finish":
			we_repl(self.load, url, args[0]);
			return true;
		case "workflow_edit":
		case "workflow_edit_ifthere":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "new_user":
		case "exit_workflow":
//case "reload_workflow":
		case "save_workflow":
		case "new_workflow":
		case "delete_workflow":
		case "empty_log":
			var wind = WE().util.jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				wind.focus();
			}
			return true;
	}
	return false;
}