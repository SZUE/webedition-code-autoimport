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
we_cmd_modules.export = function (args, url) {
	switch (args[0]) {
		case "export_edit":
		case "export_edit_ifthere":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "new_export":
		case "new_export_group":
		case "save_export":
		case "delete_export":
		case "exit_export":
		case "start_export":
			var wind = WE().util.jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				wind.focus();
			}
			return true;
		case "unlock"://FIXME:???
			we_repl(window.load, url, args[0]);
			return true;
	}
	return false;
};