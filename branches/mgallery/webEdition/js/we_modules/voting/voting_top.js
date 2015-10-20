/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 10569 $
 * $Author: mokraemer $
 * $Date: 2015-10-12 19:56:23 +0200 (Mo, 12. Okt 2015) $
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

function setHot() {
	hot = 1;
}
function usetHot() {
	hot = 0;
}
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = [];
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		args.push(arguments[i]);

		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	if (hot === 1 && args[0] != "save_voting") {
		if (confirm(WE().consts.g_l.voting.save_changed_voting)) {
			args[0] = "save_voting";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_voting":
			if (hot !== 1) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;

		case "vote":
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.tabnr.value = 3;
			top.content.editor.edbody.document.we_form.votnr.value = args[1];
			top.content.editor.edbody.submitForm();
			break;
		case "resetscores":
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.tabnr.value = 3;
			top.content.editor.edbody.submitForm();
			break;
		case "new_voting":
		case "new_voting_group":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.cmdid.value = args[1];
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.document.we_form.vernr.value = 0;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(function () {
					we_cmd("new_voting");
				}, 10);
			}
			break;
		case "delete_voting":
			if (top.content.editor.edbody.document.we_form.cmd.value == "home")
				return;
			if (top.content.editor.edbody.document.we_form.newone.value == 1) {
				WE().util.showMessage(WE().consts.g_l.voting.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (!WE().util.hasPerm("DELETE_VOTING")) {
				WE().util.showMessage(WE().consts.g_l.voting.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (top.content.editor.edbody.loaded) {
				if (confirm(WE().consts.g_l.voting.delete_alert)) {
					top.content.editor.edbody.document.we_form.cmd.value = args[0];
					top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
					top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&home=1&pnt=edheader";
					top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&home=1&pnt=edfooter";
					top.content.editor.edbody.submitForm();
				}
			} else {
				WE().util.showMessage(WE().consts.g_l.voting.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			break;

		case "save_voting":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home")
				return;
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
				top.content.editor.edbody.document.we_form.owners_name.value = top.content.editor.edbody.owners_label.name;
				top.content.editor.edbody.document.we_form.owners_count.value = top.content.editor.edbody.owners_label.itemCount;
				if (top.content.editor.edbody.document.we_form.IsFolder.value !== 1) {
					top.content.editor.edbody.document.we_form.question_name.value = top.content.editor.edbody.question_edit.name;
					top.content.editor.edbody.document.we_form.answers_name.value = top.content.editor.edbody.answers_edit.name;
					top.content.editor.edbody.document.we_form.variant_count.value = top.content.editor.edbody.answers_edit.variantCount;
					top.content.editor.edbody.document.we_form.item_count.value = top.content.editor.edbody.answers_edit.itemCount;
					top.content.editor.edbody.document.we_form.iptable_name.value = top.content.editor.edbody.iptable_label.name;
					top.content.editor.edbody.document.we_form.iptable_count.value = top.content.editor.edbody.iptable_label.itemCount;
				}

				top.content.editor.edbody.submitForm();
				top.content.usetHot();
			} else {
				WE().util.showMessage(WE().consts.g_l.voting.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, window);
			}

			break;

		case "voting_edit":
			if (!WE().util.hasPerm("EDIT_VOTING")) {
				WE().util.showMessage(WE().consts.g_l.voting.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.cmdid.value = args[1];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edbody.submitForm();
			break;
		case "load":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=cmd&pid=" + args[1] + "&offset=" + args[2] + "&sort=" + args[3];
			break;
		case "home":
			top.content.editor.edbody.parent.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&pnt=editor";
			break;
		default:
			top.opener.top.we_cmd.apply(this, args);
	}
}