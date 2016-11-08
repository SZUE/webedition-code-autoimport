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
var hot = false;
WE().util.loadConsts(document, "g_l.newsletter");

function setHot() {
	hot = true;
}

function usetHot() {
	hot = false;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

/**
 * Menu command controler
 */
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	if (hot && args[0] !== "save_newsletter") {
		if (confirm(WE().consts.g_l.newsletter.save_changed_newsletter)) {
			args[0] = "save_newsletter";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_newsletter":
			if (!hot) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;

		case "new_newsletter":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "new_newsletter");
			}
			break;

		case "new_newsletter_group":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(we_cmd, 10, "new_newsletter_group");
			}
			break;

		case "delete_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			if (!WE().util.hasPerm("DELETE_NEWSLETTER")) {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (!top.content.editor.edbody.loaded) {
				top.we_showMessage(WE().consts.g_l.newsletter.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			WE().util.showConfirm(window, "", (top.content.editor.edbody.document.we_form.IsFolder.value == 1 ? WE().consts.g_l.newsletter.delete_group_question : WE().consts.g_l.newsletter.delete_question), [
				"delete_newsletter_do"]);
			break;
		case "delete_newsletter_do":
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&home=1&pnt=edheader";
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&home=1&pnt=edfooter";
			top.content.editor.edbody.document.we_form.ncmd.value = "delete_newsletter";
			top.content.editor.edbody.submitForm();

			break;

		case "save_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (!WE().util.hasPerm("EDIT_NEWSLETTER") && !WE().util.hasPerm("NEW_NEWSLETTER")) {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, this);
				return;
			}
			if (top.content.editor.edbody.loaded) {
				if (!top.content.editor.edbody.checkData()) {
					return;
				}
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();

			} else {
				top.we_showMessage(WE().consts.g_l.newsletter.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, this);
			}
			top.content.usetHot();
			break;
		case "save_newsletter_question":
			self.focus();
			top.content.get_focus = 0;
			WE().util.showConfirm(window, "", WE().consts.g_l.newsletter.ask_to_preserve, ["save_newsletter_question_yes"]);
			break;
		case "save_newsletter_question_yes":
			document.we_form.ask.value = 0;
			we_cmd('save_newsletter');
			break;
		case "newsletter_edit":
			top.content.hot = false;
			top.content.editor.edbody.document.we_form.ncmd.value = args[0];
			top.content.editor.edbody.document.we_form.nid.value = args[1];
			top.content.editor.edbody.submitForm();
			break;

		case "send_test":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				top.content.editor.edbody.we_cmd("send_test");
			}
			break;

		case "empty_log":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				new (WE().util.jsWindow)(this, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=qlog", "log_question", -1, -1, 330, 230, true, false, true);
			}
			break;

		case "preview_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				top.content.editor.edbody.we_cmd("popPreview");
			}
			break;

		case "send_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				top.content.editor.edbody.we_cmd("popSend");
			}
			break;

		case "test_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				top.content.editor.edbody.we_cmd("popSend", "1");
			}
			break;

		case "domain_check":
		case "show_log":
		case "print_lists":
		case "search_email":
		case "clear_log":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				top.content.editor.edbody.we_cmd(args[0]);
			}
			break;

		case "newsletter_settings":
		case "black_list":
		case "edit_file":
			top.content.editor.edbody.we_cmd(args[0]);
			break;

		case "home":
			top.content.editor.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=editor";
			break;

		default:
			top.we_cmd.apply(this, Array.prototype.slice.call(arguments));

	}
}
