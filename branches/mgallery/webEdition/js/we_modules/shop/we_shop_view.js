/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

var get_focus = 1;
var activ_tab = 1;
var hot = 0;
var scrollToVal = 0;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}


function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "new_raw":
			if (top.content.editor.edbody.loaded) {
				top.content.hot = 1;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.cmdid.value = args[1];
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(function () {
					we_cmd("new_raw");
				}, 10);
			}
			break;
		case "delete_raw":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home")
				return;
			if (WE().util.hasPerm("DELETE_RAW")) {
				if (top.content.editor.edbody.loaded) {
					if (confirm(WE().consts.g_l.shop.delete_alert)) {
						top.content.editor.edbody.document.we_form.cmd.value = args[0];
						top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
						top.content.editor.edbody.submitForm();
					}
				} else {
					top.we_showMessage(WE().consts.g_l.shop.view.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
				}

			} else {
				top.we_showMessage(WE().consts.g_l.shop.view.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);

			}
			break;
		case "save_raw":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				return;
			}

			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;

				top.content.editor.edbody.submitForm();
			} else {
				top.we_showMessage(WE().consts.g_l.shop.view.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, this);

			}
			break;
		case "edit_raw":
			top.content.hot = 0;
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.cmdid.value = args[1];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.submitForm();
			break;
		case "load":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=cmd&pid=" + args[1] + "&offset=" + args[2] + "&sort=" + args[3];
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}