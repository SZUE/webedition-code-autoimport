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
we_cmd_modules.banner = function (args, url) {
	switch (args[0]) {
		case "edit_settings_banner":
			we_cmd("banner_default");
			break;
		case "banner_edit":
		case "banner_edit_ifthere":
			new (WE().util.jsWindow)(window, url, "edit_module", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			return true;
		case "banner_default":
			WE().util.jsWindow.prototype.focus('edit_module');
			new (WE().util.jsWindow)(window, url, "defaultbanner", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, false, true, true);
			return true;
		case "banner_code":
			WE().util.jsWindow.prototype.focus('edit_module');
			new (WE().util.jsWindow)(window, url, "bannercode", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true, false);
			return true;
		case "new_banner":
		case "new_bannergroup":
		case "save_banner":
		case "exit_banner":
		case "delete_banner":
			WE().layout.pushCmdToModule(args);
			return true;
	}
	return false;
};