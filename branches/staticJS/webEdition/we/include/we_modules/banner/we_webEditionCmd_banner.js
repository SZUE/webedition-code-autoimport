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
function we_cmd_banner(args,url) {
	switch (args[0]) {
		case "banner_edit":
		case "banner_edit_ifthere":
			new jsWindow(url, "edit_module", -1, -1, 970, 760, true, true, true, true);
			return true;
		case "banner_default":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo)
						break;
				}
				if (typeof (wind) != "undefined") {
					wind.focus();
				}
			}
			new jsWindow(url, "defaultbanner", -1, -1, 500, 220, true, false, true, true);
			return true;
		case "banner_code":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo) {
						break;
					}
				}
				wind.focus();
			}
			new jsWindow(url, "bannercode", -1, -1, 500, 420, true, true, true, false);
			return true;
		case "new_banner":
		case "new_bannergroup":
		case "save_banner":
		case "exit_banner":
		case "delete_banner":
			var fo = false;
			if (jsWindow_count) {
				for (var k = jsWindow_count - 1; k > -1; k--) {
					eval("if(jsWindow" + k + "Object.ref=='edit_module'){ jsWindow" + k + "Object.wind.content.we_cmd('" + args[0] + "');fo=true;wind=jsWindow" + k + "Object.wind}");
					if (fo) {
						break;
					}
				}
				if (wind && args[0] != "empty_log") {
					wind.focus();
				}
			}
			return true;
	}
	return false;
}
