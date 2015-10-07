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
var hot = 0;

function setHot() {
	hot = "1";
}

function usetHot() {
	hot = "0";
}

function doUnload() {
	jsWindowCloseAll();
}

/**
 * Menu command controler
 */
function we_cmd() {
	var args=[];
	var url = top.WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	var i;
	for (i = 0; i < arguments.length; i++) {
					args.push(arguments[i]);
	url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	if (hot == 1 && args[0] !== "save_newsletter") {
		if (confirm(g_l.save_changed_newsletter)) {
			args[0] = "save_newsletter";
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "exit_newsletter":
			if (hot != 1) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;

		case "new_newsletter":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(function () {
					we_cmd("new_newsletter");
				}, 10);
			}
			break;

		case "new_newsletter_group":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(function () {
					we_cmd("new_newsletter_group");
				}, 10);
			}
			break;

		case "delete_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (!perms.DELETE_NEWSLETTER) {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (top.content.editor.edbody.loaded) {
				var delQuestion = top.content.editor.edbody.document.we_form.IsFolder.value == 1 ? g_l.delete_group_question : g_l.delete_question;
				if (!confirm(delQuestion)) {
					return;
				}
			} else {
				top.we_showMessage(g_l.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			topFrame.editor.edheader.location = frameSet + "?home=1&pnt=edheader";
			topFrame.editor.edfooter.location = frameSet + "?home=1&pnt=edfooter";
			top.content.editor.edbody.document.we_form.ncmd.value = args[0];
			top.content.editor.edbody.submitForm();

			break;

		case "save_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				if (!perms.EDIT_NEWSLETTER && !perms.NEW_NEWSLETTER) {
					top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
					return;
				}
				if (top.content.editor.edbody.loaded) {
					if (!top.content.editor.edbody.checkData()) {
						return;
					}
					top.content.editor.edbody.document.we_form.ncmd.value = args[0];
					top.content.editor.edbody.submitForm();

				} else {
					top.we_showMessage(g_l.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
				top.content.usetHot();
			}
			break;

		case "newsletter_edit":
			top.content.hot = 0;
			top.content.editor.edbody.document.we_form.ncmd.value = args[0];
			top.content.editor.edbody.document.we_form.nid.value = args[1];
			top.content.editor.edbody.submitForm();
			break;

		case "send_test":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("send_test");
			}
			break;

		case "empty_log":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				new jsWindow(frameSet + "?pnt=qlog", "log_question", -1, -1, 330, 230, true, false, true);
			}
			break;

		case "preview_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("popPreview");
			}
			break;

		case "send_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("popSend");
			}
			break;

		case "test_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("popSend", "1");
			}
			break;

		case "domain_check":
		case "show_log":
		case "print_lists":
		case "search_email":
		case "clear_log":
			if (top.content.editor.edbody.document.we_form.ncmd.value == "home") {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(g_l.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
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
			top.content.editor.location = frameSet + "?pnt=editor";
			break;

		default:
			top.opener.top.we_cmd.apply(this, args);

	}
}
