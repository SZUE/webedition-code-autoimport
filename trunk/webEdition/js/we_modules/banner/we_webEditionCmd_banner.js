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
function we_cmd_banner(args, url) {
	switch (args[0]) {
		case "edit_settings_banner":
			we_cmd("banner_default");
			break;
		case "banner_edit":
		case "banner_edit_ifthere":
			new (WE().util.jsWindow)(this, url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "banner_default":
			WE().util.jsWindow.prototype.focus('edit_module');
			new (WE().util.jsWindow)(this, url, "defaultbanner", -1, -1, 500, 220, true, false, true, true);
			return true;
		case "banner_code":
			WE().util.jsWindow.prototype.focus('edit_module');
			new (WE().util.jsWindow)(this, url, "bannercode", -1, -1, 500, 420, true, true, true, false);
			return true;
		case "new_banner":
		case "new_bannergroup":
		case "save_banner":
		case "exit_banner":
		case "delete_banner":
			var wind = WE().util.jsWindow.prototype.find('edit_module');
			if (wind) {
				wind.content.we_cmd(args[0]);
				if (args[0] !== "empty_log") {
					wind.focus();
				}
			}
			return true;
	}
	return false;
}
