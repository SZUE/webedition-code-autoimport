/* global top, WE */

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

var hot = false;
WE().util.loadConsts(document, "g_l.banner");

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function setHot() {
	hot = true;
}

function usetHot() {
	hot = false;
}


function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	if (hot && args[0] != "save_banner") {
		if (confirm(WE().consts.g_l.banner.view.save_changed_banner)) {
			args[0] = "save_banner";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_banner":
			if (!hot) {
				top.opener.top.we_cmd('exit_modules');
			}
			break;
		case "new_banner":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "new_banner");
			}
			break;
		case "new_bannergroup":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "new_bannergroup");
			}
			break;
		case "delete_banner":
			if (!WE().util.hasPerm("DELETE_BANNER")) {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (!top.content.editor.edbody.loaded && top.content.editor.edbody.we_is_home === undefined) {
				WE().util.showMessage(WE().consts.g_l.banner.view.nothing_to_delete, WE().consts.message.WE_MESSAGE_WARNING, this);
				return;
			}
			WE().util.showConfirm(window, "", WE().consts.g_l.banner.view.delete_question, ["delete_banner_do"]);
			break;
		case "delete_banner_do":
			top.content.editor.edbody.document.we_form.ncmd.value = "delete_banner";
			top.content.editor.edbody.submitForm();

			break;
		case "save_banner":
			if (WE().util.hasPerm("EDIT_BANNER") || WE().util.hasPerm("NEW_BANNER")) {
				if (top.content.editor.edbody.loaded && top.content.editor.edbody.we_is_home === undefined) {
					if (!top.content.editor.edbody.checkData()) {
						return;
					}
				} else {
					top.we_showMessage(WE().consts.g_l.banner.view.nothing_to_save, WE().consts.message.WE_MESSAGE_WARNING, this);
					return;
				}

				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			top.content.usetHot();
			break;
		case "banner_edit":
			top.content.editor.edbody.document.we_form.ncmd.value = args[0];
			top.content.editor.edbody.document.we_form.bid.value = args[1];
			top.content.editor.edbody.submitForm();
			break;
		case "banner_load":
			top.content.editor.edheader.location  =WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=banner&pnt=edheader&page=' + args[1] + '&txt=' + args[2] + '&isFolder=' + args[3];
			top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=banner&pnt=edfooter';
			break;
		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));

	}
}