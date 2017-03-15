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
'use strict';

var get_focus = 1;
var hot = false;
WE().util.loadConsts(document, "g_l.newsletter");
var newsletter = WE().util.getDynamicVar(document, 'loadVarNewsletter', 'data-newsletter');

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
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	if (hot && args[0] !== "save_newsletter") {
		if (window.confirm(WE().consts.g_l.newsletter.save_changed_newsletter)) {
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
		case 'loadHeadFooter':
			top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edheader&page=" + args[1].page + '&txt=' + encodeURI(args[1].txt) + '&group=' + (args[1].folder ? 1 : 0);
			top.content.editor.edfooter.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=edfooter&group=" + (args[1].folder ? 1 : 0);
			break;
		case "new_newsletter":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				window.setTimeout(we_cmd, 10, "new_newsletter");
			}
			break;

		case "new_newsletter_group":
			if (top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.ncmd.value = args[0];
				top.content.editor.edbody.submitForm();
			} else {
				window.setTimeout(we_cmd, 10, "new_newsletter_group");
			}
			break;

		case "delete_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (!WE().util.hasPerm("DELETE_NEWSLETTER")) {
				top.we_showMessage(WE().consts.g_l.main.no_perms, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (!top.content.editor.edbody.loaded) {
				top.we_showMessage(WE().consts.g_l.newsletter.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
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
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (!WE().util.hasPerm("EDIT_NEWSLETTER") && !WE().util.hasPerm("NEW_NEWSLETTER")) {
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
				top.we_showMessage(WE().consts.g_l.newsletter.nothing_to_save, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
			top.content.usetHot();
			break;
		case "save_newsletter_question":
			window.focus();
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
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("send_test");
			}
			break;

		case "empty_log":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=qlog", "log_question", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, false, true);
			}
			break;

		case "preview_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("popPreview");
			}
			break;

		case "send_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				top.content.editor.edbody.we_cmd("popSend");
			}
			break;

		case "test_newsletter":
			if (top.content.editor.edbody.document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
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
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.editor.edbody.document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
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
		case "switchPage":
			// modified for use as selector callback: args[1] is reserved for selector results
			top.content.editor.edbody.document.we_form.ncmd.value = args[0];
			top.content.editor.edbody.document.we_form.page.value = args[2];
			if (args[3] === 'setHot') {
				top.content.hot = true;
			}
			top.content.editor.edbody.submitForm();
			break;
		case "syncNewsletterTitle":
			top.content.editor.edheader.weTabs.setTitlePath(top.content.editor.edbody.document.we_form.elements.Text.value);
			break;
		case "groups_changed":
			top.content.editor.edfooter.document.we_form.gview.length = 0;
			top.content.editor.edfooter.populateGroups();
			break;
		case "processAfterUpload":
			switch (args[1].cmd) {
				case 'do_upload_csv':
					caller.opener.document.we_form["csv_file" + args[1].group].value = args[1].name;
					caller.opener.we_cmd("import_csv");
					break;
				default:
					caller.opener.document.we_form.csv_file.value = args[1].name;
					caller.opener.document.we_form.sib.value = 0;
					caller.opener.we_cmd("import_black");
			}
			caller.close();
			break;
		case "setHot":
			// do nothing
			break;
		case "unsetHot":
			top.content.hot = false;
			break;
		case "ask_camp":
			window.focus();
			WE().util.showConfirm(window, "", WE().consts.g_l.newsletter.continue_camp, ['continue_camp_yesNo', newsletter.start, newsletter.group], ['continue_camp_yesNo', 0, 0]);
			break;
		case "cancel_camp":
			doSend(0, 0);
			break;
		case "continue_camp_yesNo":
			doSend(args[1], args[2]);
			break;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function doSend(start, group) {
	window.focus();
	top.send_cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=send_cmd&nid=" + newsletter.nid + "&test=" + newsletter.test + "&blockcache=" + newsletter.blockcache + "&emailcache=" + newsletter.emailcache + "&ecount=" + newsletter.ecount + "&gcount=" + newsletter.gcount + "&start=" + start + "&egc=" + group;
}


function addBlack() {
	var p = document.we_form.elements.blacklist_sel;
	var newRecipient = window.prompt(WE().consts.g_l.newsletter.add_email, "");

	if (newRecipient !== null) {
		if (newRecipient.length > 0) {
			if (newRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!inSelectBox(p, newRecipient)) {
				addElement(p, "#", newRecipient, true);
			} else {
				top.we_showMessage(WE().consts.g_l.newsletter.email_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		} else {
			top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}

function deleteBlack() {
	var p = document.we_form.elements.blacklist_sel;

	if (p.selectedIndex >= 0) {
		if (window.confirm(WE().consts.g_l.newsletter.email_delete)) {
			p.options[p.selectedIndex] = null;
		}
	}
}

function deleteallBlack() {
	var p = document.we_form.elements.blacklist_sel;

	if (window.confirm(WE().consts.g_l.newsletter.email_delete_all)) {
		p.options.length = 0;
	}
}

function editBlack() {
	var p = document.we_form.elements.blacklist_sel;
	var index = p.selectedIndex;

	if (index >= 0) {
		var editRecipient = window.prompt(WE().consts.g_l.newsletter.edit_email, p.options[index].text);

		if (editRecipient !== null) {
			if (editRecipient !== "") {
				if (editRecipient.length > 255) {
					top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
					return;
				}
				p.options[index].text = editRecipient;
			} else {
				top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		}
	}
}

function set_import(val) {
	document.we_form.sib.value = val;

	if (val == 1) {
		document.we_form.seb.value = 0;
	}

	populateVar(document.we_form.blacklist_sel, document.we_form.black_list);
	submitForm("black_list");
}

function set_export(val) {
	document.we_form.seb.value = val;

	if (val == 1) {
		document.we_form.sib.value = 0;
	}

	populateVar(document.we_form.blacklist_sel, document.we_form.black_list);
	submitForm("black_list");
}

function clearLog() {
	var f = window.document.we_form;
	f.action = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter";
	f.method = "post";
	f.submit();
}
