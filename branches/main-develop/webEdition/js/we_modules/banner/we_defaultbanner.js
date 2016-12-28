/* global top */

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

var loaded;
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_banner_selector":
			new (WE().util.jsWindow)(window, url, "we_bannerselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		default:
			top.content.we_cmd.apply(window, Array.prototype.slice.call(arguments));
	}
}

function we_save() {
	var acLoopCount = 0;
	var acStatus = WE().layout.weSuggest.checkRequired(window);

	while (acLoopCount < 20 && acStatus.running) {
		acLoopCount++;
		window.setTimeout(we_save, 100);
		return;
	}

	if (acStatus.valid) {
		document.we_form.submit();
	} else {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}