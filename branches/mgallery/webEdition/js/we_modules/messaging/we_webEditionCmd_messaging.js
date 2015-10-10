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
function we_cmd_messaging(args, url) {
	switch (args[0]) {
		case "messaging_start":
		case "messaging_edit_ifthere":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "messaging_new_message":
		case "messaging_new_todo":
		case "messaging_start_view":
		case "messaging_new_folder":
		case "messaging_delete_mode_on":
		case "messaging_delete_folders":
		case "messaging_edit_folder":
		case "messaging_exit":
		case "messaging_new_account":
		case "messaging_edit_account":
		case "messaging_copy":
		case "messaging_cut":
		case "messaging_paste":
		case "messaging_settings":
			var wind = jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				wind.focus();
			}
			return true;
	}
	return false;
}