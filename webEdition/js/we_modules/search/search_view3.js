/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 12728 $
 * $Author: mokraemer $
 * $Date: 2016-09-05 15:19:48 +0200 (Mo, 05. Sep 2016) $
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
var loaded = 0;
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(this, url, "we_docselector", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(this, url, "we_selector", -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(this, url, "we_selector", -1, -1, WE().consts.size.windowDirSelect.width, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url, "we_catselector", -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case "openweSearchDirselector":
			url = WE().consts.dirs.WEBEDITION_DIR + "apps/weSearch/we_weSearchDirSelect.php?";
			for (var i = 0; i < args.length; i++) {
				url += "we_cmd[]=" + encodeURI(args[i]);
				if (i < (args.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(this, url, "we_weSearch_dirselector", -1, -1, 600, 400, true, true, true);
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}