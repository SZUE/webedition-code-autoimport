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

function doUnload() {
	jsWindow.prototype.closeAll();
}

function we_cmd() {
	var url = frames.set + "?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {

		case "add_sort_field":
			if (arguments[1] === "") {
				top.we_showMessage(g_l.sortname_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			document.we_form.sortindex.value = arguments[1];
		case "add_sort":
			document.we_form.cmd.value = arguments[0];
			submitForm();
			break;
		case "del_sort_field":
			document.we_form.fieldindex.value = arguments[2];
		case "del_sort":
			if (arguments[1] == settings.default_sort_view) {
				top.we_showMessage(g_l.default_soting_no_del, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			else {
				document.we_form.cmd.value = arguments[0];
				document.we_form.sortindex.value = arguments[1];
				submitForm();
			}
			break;
		case "save_sort":
		case "selectBranch":
			document.we_form.cmd.value = arguments[0];
			submitForm();
			break;
		default:
			var args = [];
			for (i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);

	}
	setScrollTo();
}
