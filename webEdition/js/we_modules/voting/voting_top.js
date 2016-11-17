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

WE().util.loadConsts(document, "g_l.voting");

var get_focus = 1;
var activ_tab = 1;
var hot = false;
var scrollToVal = 0;

function setHot() {
	hot = true;
}
function usetHot() {
	hot = false;
}
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	if (hot && args[0] != "save_voting") {
		if (confirm(WE().consts.g_l.voting.save_changed_voting)) {
			args[0] = "save_voting";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_voting":
			if (!hot) {
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
				top.content.editor.edbody.document.we_form.cmdid.value = (args[1] ? args[1] : 0);
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.document.we_form.vernr.value = 0;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "new_voting");
			}
			break;
		case "delete_voting":
			if (top.content.editor.edbody.document.we_form.cmd.value == "home") {
				return;
			}
			if (top.content.editor.edbody.document.we_form.newone.value == 1) {
				WE().util.showMessage(WE().consts.g_l.voting.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (!WE().util.hasPerm("DELETE_VOTING")) {
				WE().util.showMessage(WE().consts.g_l.voting.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (!top.content.editor.edbody.loaded) {
				WE().util.showMessage(WE().consts.g_l.voting.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}

			WE().util.showConfirm(window, "", WE().consts.g_l.voting.delete_alert, ["delete_voting_do"]);
			break;
		case "delete_voting_do":
			top.content.editor.edbody.document.we_form.cmd.value = "delete_voting";
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&home=1&pnt=edheader";
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=voting&home=1&pnt=edfooter";
			top.content.editor.edbody.submitForm();
			break;
		case "save_voting":
			if (top.content.editor.edbody.document.we_form.cmd.value === "home") {
				return;
			}
			if (!top.content.editor.edbody.loaded) {
				WE().util.showMessage(WE().consts.g_l.voting.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			top.content.editor.edbody.document.we_form.cmd.value = args[0];
			top.content.editor.edbody.document.we_form.tabnr.value = top.content.activ_tab;
			if (top.content.editor.edbody.document.we_form.IsFolder.value !== '1') {
				top.content.editor.edbody.document.we_form.owners_name.value = top.content.editor.edbody.owners_label.name;
				top.content.editor.edbody.document.we_form.owners_count.value = top.content.editor.edbody.owners_label.itemCount;
				top.content.editor.edbody.document.we_form.question_name.value = top.content.editor.edbody.question_edit.name;
				top.content.editor.edbody.document.we_form.answers_name.value = top.content.editor.edbody.answers_edit.name;
				top.content.editor.edbody.document.we_form.variant_count.value = top.content.editor.edbody.answers_edit.variantCount;
				top.content.editor.edbody.document.we_form.item_count.value = top.content.editor.edbody.answers_edit.itemCount;
				top.content.editor.edbody.document.we_form.iptable_name.value = top.content.editor.edbody.iptable_label.name;
				top.content.editor.edbody.document.we_form.iptable_count.value = top.content.editor.edbody.iptable_label.itemCount;
			}

			top.content.editor.edbody.submitForm();
			top.content.usetHot();
			break;

		case "voting_edit":
			if (!WE().util.hasPerm("EDIT_VOTING")) {
				WE().util.showMessage(WE().consts.g_l.voting.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
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
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}