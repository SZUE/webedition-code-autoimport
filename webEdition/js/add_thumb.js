'use strict';
/* global WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

var thumbData = WE().util.getDynamicVar(document, 'loadVarAdd_thumb', 'data-thumbData');

function add_thumbnails() {
	var sel = document.getElementById("Thumbnails"),
		thumbs = "";
	for (var i = 0; i < sel.options.length; i++) {
		if (sel.options[i].selected) {
			thumbs += (sel.options[i].value + ",");
		}
	}

	if (thumbs.length) {
		thumbs = "," + thumbs;
		window.opener.we_cmd("do_add_thumbnails", thumbs);
	}

	window.close();

}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "editThumbs":
			new (WE().util.jsWindow)(caller, url, "thumbnails", WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, true, true);
			break;
		default:
			window.parent.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function select_thumbnails(sel) {
	var thumbs = [];

	for (var i = 0; i < sel.options.length; i++) {
		if (sel.options[i].selected) {
			thumbs.push(sel.options[i].value);
		}
	}

	WE().layout.button.switch_button_state(document, "add", (thumbs.length ? "enabled" : "disabled"));

	window.showthumbs.location = WE().consts.dirs.WEBEDITION_DIR + "showThumb.php?u=" + Math.random() + "&t=" + thumbData.transaction + "&id=" + encodeURI(thumbs.join(","));

}