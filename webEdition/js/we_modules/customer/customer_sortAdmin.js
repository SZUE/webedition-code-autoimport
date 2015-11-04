/*
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

/* global top, WE */

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var url = frames.set + "?";
	if(typeof arguments[0] === "object" && arguments[0]["we_cmd[0]"] !== undefined){
		var args = {}, i = 0, tmp = arguments[0];
		url += Object.keys(tmp).map(function(key){args[key] = tmp[key]; args[i++] = tmp[key]; return key + "=" + encodeURIComponent(tmp[key]);}).join("&");
	} else {
		var args = Array.prototype.slice.call(arguments);
		for (var i = 0; i < args.length; i++) {
			url += "we_cmd[" + i + "]=" + encodeURIComponent(args[i]) + (i < (args.length - 1) ? "&" : "");
		}
	}

	switch (args[0]) {
		case "add_sort_field":
			if (args[1] === "") {
				top.we_showMessage(g_l.sortname_empty, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			document.we_form.sortindex.value = args[1];
		case "add_sort":
			document.we_form.cmd.value = args[0];
			submitForm();
			break;
		case "del_sort_field":
			document.we_form.fieldindex.value = args[2];
		case "del_sort":
			if (args[1] == settings.default_sort_view) {
				top.we_showMessage(g_l.default_soting_no_del, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			else {
				document.we_form.cmd.value = args[0];
				document.we_form.sortindex.value = args[1];
				submitForm();
			}
			break;
		case "save_sort":
		case "selectBranch":
			document.we_form.cmd.value = args[0];
			submitForm();
			break;
		default:
			top.content.we_cmd.apply(this, arguments);

	}
	setScrollTo();
}
