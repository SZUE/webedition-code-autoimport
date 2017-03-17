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
var nlView = WE().util.getDynamicVar(document, 'loadVarNewsletter_property', 'data-nlView');

function set_state_edit_delete_recipient(control) {
	var p = document.forms[0].elements[control];
	var i = p.length;

	if (i === 0) {
		WE().layout.button.switch_button_state(document, "edit", "disabled");
		WE().layout.button.switch_button_state(document, "delete", "disabled");
		WE().layout.button.switch_button_state(document, "delete_all", "disabled");
		//edit_enabled = WE().layout.button.switch_button_state(document, "edit", "disabled");
		//delete_enabled = WE().layout.button.switch_button_state(document, "delete", "disabled");
		//delete_all_enabled = WE().layout.button.switch_button_state(document, "delete_all", "disabled");

	} else {
		WE().layout.button.switch_button_state(document, "edit", "enabled");
		WE().layout.button.switch_button_state(document, "delete", "enabled");
		WE().layout.button.switch_button_state(document, "delete_all", "enabled");
		//edit_enabled = WE().layout.button.switch_button_state(document, "edit", "enabled");
		//delete_enabled = WE().layout.button.switch_button_state(document, "delete", "enabled");
		//delete_all_enabled = WE().layout.button.switch_button_state(document, "delete_all", "enabled");
	}
}

function addElement(p, value, text, sel, optionClassName) {
	var i = p.length;

	p.options[i] = new Option(text, value);
	p.options[i].className = optionClassName;

	if (sel) {
		p.selectedIndex = i;
	}
}

function getGroupsNum() {
	return document.we_form.groups.value;
}

function populateVar(p, dest) {
	var arr = [];

	for (var i = 0; i < p.length; i++) {
		arr[i] = p.options[i].text;
	}

	dest.value = arr.join();
}

function populateMultipleVar(p, dest) {
	var arr = [],
		c = 0;

	for (var i = 0; i < p.length; i++) {
		if (p.options[i].selected) {
			c++;
			arr[c] = p.options[i].value;
		}
	}

	dest.value = arr.join();
}

function addEmail(group, email, html, salutation, title, firstname, lastname) {
	var dest = document.forms[0].elements["group" + group + "_Emails"];
	var str = dest.value;

	var arr = (str.length > 0 ? str.split("\n") : []);

	arr[arr.length] = email + "," + html + "," + salutation + "," + title + "," + firstname + "," + lastname;

	dest.value = arr.join("\n");

	top.content.hot = true;
}

function editEmail(group, id, email, html, salutation, title, firstname, lastname) {
	var dest = document.forms[0].elements["group" + group + "_Emails"];
	var str = dest.value;

	var arr = str.split("\n");

	arr[id] = email + "," + html + "," + salutation + "," + title + "," + firstname + "," + lastname;

	dest.value = arr.join("\n");

	top.content.hot = true;
}

function mysplice(arr, id) {
	var newarr = [];

	for (var i = 0; i < arr.lenght; i++) {
		if (i != id) {
			newarr[newarr.lenght] = arr[id];
		}
	}
	return newarr;
}

function delEmail(group, id) {
	var dest = document.forms[0].elements["group" + group + "_Emails"];
	var str = dest.value;
	var arr = str.split("\n");

	arr.splice(id, 1);
	dest.value = arr.join("\n");
	top.content.hot = true;
}

function delallEmails(group) {
	var dest = document.forms[0].elements["group" + group + "_Emails"];

	dest.value = "";
	top.content.hot = true;
}

function inSelectBox(p, val) {
	for (var i = 0; i < p.options.length; i++) {

		if (p.options[i].text == val) {
			return true;
		}
	}
	return false;
}

function markEMails() {
}

function switchRadio(a, b, x, c) {
	a.value = 1;
	a.checked = true;
	b.value = 0;
	b.checked = false;

	if (c) {
		c.value = 0;
		c.checked = false;
	}
}

function clickCheck(a) {
	if (a.checked) {
		a.value = 1;
	} else {
		a.value = 0;
	}
}

function popAndSubmit(wname, pnt, width, height) {
	var old = document.we_form.pnt.value;
	document.we_form.pnt.value = pnt;

	new (WE().util.jsWindow)(window, "about:blank", wname, width, height, true, true, true, true);
	submitForm(wname);
	document.we_form.pnt.value = old;
}

function doScrollTo() {
	if (window.parent.scrollToVal) {
		window.scrollTo(0, window.parent.scrollToVal);
		window.parent.scrollToVal = 0;
	}
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

/**
 * Newsletter command controler
 */
function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	if (args[0] != "switchPage") {
		window.setScrollTo();
	}

	switch (args[0]) {
		case "setFocus":
			setFocus();
			break;
		case "we_users_selector":
			new (WE().util.jsWindow)(caller, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(caller, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(caller, url, "we_docselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_newsletter_dirSelector":
			new (WE().util.jsWindow)(caller, url, "we_newsletter_dirselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "add_customer":
			document.we_form.ngroup.value = args[2];
			document.we_form.ncmd.value = args[0];
			document.we_form.ncustomer.value = args[1].allIDs.join(",");
			top.content.hot = true;
			submitForm();
			break;
		case "del_customer":
			document.we_form.ncmd.value = args[0];
			document.we_form.ncustomer.value = args[1];
			top.content.hot = true;
			submitForm();
			break;
		case "del_all_customers":
		case "del_all_files":
			top.content.hot = true;
			document.we_form.ncmd.value = args[0];
			document.we_form.ngroup.value = args[1];
			submitForm();
			break;
		case "add_file":
			document.we_form.ncmd.value = args[0];
			document.we_form.nfile.value = args[1].add_file;
			document.we_form.ngroup.value = args[2];
			top.content.hot = true;
			submitForm();
			break;
		case "del_file":
			document.we_form.ncmd.value = args[0];
			document.we_form.nfile.value = args[1];
			top.content.hot = true;
			submitForm();
			break;
		case "set_import":
		case "reset_import":
		case "set_export":
		case "reset_export":
			document.we_form.ncmd.value = args[0];
			document.we_form.ngroup.value = args[1];
			submitForm();
			break;
		case "addBlock":
		case "delBlock":
			document.we_form.ncmd.value = args[0];
			document.we_form.blockid.value = args[1];
			top.content.hot = true;
			submitForm();
			break;
		case "addGroup":
		case "delGroup":
			document.we_form.ncmd.value = args[0];
			document.we_form.ngroup.value = args[1];
			top.content.hot = true;
			submitForm();
			break;
		case "popPreview":
			if (document.we_form.ncmd.value === "home") {
				return;
			}
			if (top.content.hot) {
				top.we_showMessage(WE().consts.g_l.newsletter.must_save_preview, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			document.we_form.elements["we_cmd[0]"].value = "preview_newsletter";
			document.we_form.gview.value = window.parent.edfooter.document.we_form.gview.value;
			document.we_form.hm.value = window.parent.edfooter.document.we_form.hm.value;
			popAndSubmit("newsletter_preview", "preview", 800, 800);
			break;
		case "popSend":
			if (document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (top.content.hot) {
				top.we_showMessage(WE().consts.g_l.newsletter.must_save, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (document.we_form.IsFolder.value === 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			args[0] = "popSend_do";
			WE().util.showConfirm(window, "", (args[1] ? WE().consts.g_l.newsletter.send_test_question : WE().consts.g_l.newsletter.send_question), args);
			break;
		case "popSend_do":
			document.we_form.ncmd.value = "popSend";
			if (args[1]) {
				document.we_form.test.value = args[1];
			}
			submitForm();

			break;
		case "popSend_do_cont":
			if (args[2]) {
				WE().util.showConfirm(window, "", WE().consts.g_l.newsletter.no_subject, ["popSend_do_cont_yes", args[1], args[2], args[3]]);
				break;
			}
			/*falls through*/
		case "popSend_do_cont_yes":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=send&nid=" + args[1] + (args[3] ? '&test=1' : '');
			new (WE().util.jsWindow)(caller, url, "newsletter_send", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true, false);
			break;
		case "send_test":
			if (document.we_form.ncmd.value === "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (top.content.hot) {
				top.we_showMessage(WE().consts.g_l.newsletter.must_save, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			if (document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			WE().util.showConfirm(window, "", WE().util.sprintf(WE().consts.g_l.newsletter.test_email_question, 'TEST_EMAIL'/* $this->newsletter->Test */), ["send_test_do"]);
			//FIXME: check where we get the test-email adress, this has to be set in the stored data.
			break;
		case "send_test_do":
			document.we_form.ncmd.value = "send_test";
			document.we_form.gview.value = window.parent.edfooter.document.we_form.gview.value;
			document.we_form.hm.value = window.parent.edfooter.document.we_form.hm.value;
			submitForm();
			break;
		case "print_lists":
		case "domain_check":
		case "show_log":
			if (document.we_form.ncmd.value != "home") {
				popAndSubmit(args[0], args[0], 650, 650);
			}
			break;
		case "newsletter_settings":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=" + args[0], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "black_list":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=" + args[0], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "edit_file":
			if (args[1]) {
				new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=" + args[0] + "&art=" + args[1], args[0], WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			} else {
				new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=" + args[0], args[0], WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			}
			break;
		case "reload_table":
		case "copy_newsletter":
			top.content.hot = true;
			document.we_form.ncmd.value = args[0];
			submitForm();
			break;
		case "add_filter":
		case "del_filter":
		case "del_all_filters":
			top.content.hot = true;
			document.we_form.ncmd.value = args[0];
			document.we_form.ngroup.value = args[1];
			submitForm();
			break;
		case "switch_sendall":
			document.we_form.ncmd.value = args[0];
			top.content.hot = true;
			if (document.we_form["sendallcheck_" + args[1]].checked) {
				document.we_form["group" + args[1] + "_SendAll"].value = 1;
			} else {
				document.we_form["group" + args[1] + "_SendAll"].value = 0;
			}
			submitForm();
			break;
		case "save_settings":
			document.we_form.ncmd.value = args[0];
			submitForm("newsletter_settings");
			break;
		case "import_csv":
		case "export_csv":
			document.we_form.ncmd.value = args[0];
			submitForm();
			break;
		case "do_upload_csv":
			document.we_form.ncmd.value = args[0];
			submitForm("upload_csv");
			break;
		case "do_upload_black":
			document.we_form.ncmd.value = args[0];
			submitForm("upload_black");
			break;
		case "upload_csv":
		case "upload_black":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=" + args[0] + "&grp=" + args[1], args[0], WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, true, true, true);
			break;
		case "add_email":
			document.we_form.group = args[1];
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=eemail&grp=" + args[1], "edit_email", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, true, true, true);
			break;
		case "edit_email":
			var p = document.we_form["we_recipient" + args[1]];
			if (p.selectedIndex < 0) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			var dest = document.we_form["group" + args[1] + "_Emails"];

			var str = dest.value;
			var arr = str.split("\n");

			var str2 = arr[p.selectedIndex];
			var arr2 = str2.split(",");
			var eid = p.selectedIndex;
			var emailx = p.options[p.selectedIndex].text;
			var htmlmail = arr2[1];
			var salutation = arr2[2];
			var title = arr2[3];
			var firstname = arr2[4];
			var lastname = arr2[5];

			salutation = encodeURIComponent(salutation.replace("+", "[:plus:]"));
			title = encodeURIComponent(title.replace("+", "[:plus:]"));
			firstname = encodeURIComponent(firstname.replace("+", "[:plus:]"));
			lastname = encodeURIComponent(lastname.replace("+", "[:plus:]"));
			emailx = encodeURIComponent(emailx);
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=eemail&grp=" + args[1] + "&etyp=1&eid=" + eid + "&email=" + emailx + "&htmlmail=" + htmlmail + "&salutation=" + salutation + "&title=" + title + "&firstname=" + firstname + "&lastname=" + lastname, "edit_email", WE().consts.size.dialog.small, WE().consts.size.dialog.tiny, true, true, true, true);
			break;
		case "save_black":
		case "import_black":
		case "export_black":
			document.we_form.ncmd.value = args[0];
			populateVar(document.we_form.blacklist_sel, document.we_form.black_list);
			submitForm("black_list");
			break;
		case "search_email":
			if (document.we_form.ncmd.value === "home") {
				return;
			}
			var searchname = window.prompt(WE().consts.g_l.newsletter.search_text, "");

			if (searchname !== null) {
				searchEmail(searchname);
			}
			break;
		case "clear_log":
			new (WE().util.jsWindow)(caller, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=" + args[0], args[0], WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true, true);
			break;
		case "blocks_selectTemplateCallback":
			document.we_form.elements['block' + args[2] + '_use_def_template'].checked = false;
			we_cmd('setHot');
			break;
		case 'settings_close':
			caller.close();
			break;
		case 'editEmails_onload':
			caller.document.we_form.emailfield.select();
			caller.document.we_form.emailfield.focus();
			break;
		case 'editEmails_save':
			switch (args[1]) {
				case 2:
					caller.opener.setAndSave(caller.document.we_form.id.value, caller.document.we_form.emailfield.value, caller.document.we_form.htmlmail.value, caller.document.we_form.salutation.value, caller.document.we_form.title.value, caller.document.we_form.firstname.value, caller.document.we_form.lastname.value);
					caller.close();
					break;
				case 1:
					caller.opener.editIt(caller.document.we_form.group.value, caller.document.we_form.id.value, caller.document.we_form.emailfield.value, caller.document.we_form.htmlmail.value, caller.document.we_form.salutation.value, caller.document.we_form.title.value, caller.document.we_form.firstname.value, caller.document.we_form.lastname.value);
					caller.close();
					break;
				default:
					caller.opener.add(caller.document.we_form.group.value, caller.document.we_form.emailfield.value, caller.document.we_form.htmlmail.value, caller.document.we_form.salutation.value, caller.document.we_form.title.value, caller.document.we_form.firstname.value, caller.document.we_form.lastname.value);
					caller.close();
			}
			break;
		case "export_csv_window":
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=export_csv_mes&lnk=" + encodeURI(args[1]), "edit_email", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true, true);
			break;
		case "updateLog":
			for (var i = 0; i < args[1].log.length; i++) {
				updateText(args[1].log[i]);
			}
			top.send_body.setProgress(args[1].percent);
			if (args[1].text) {
				top.send_body.setProgressText("title", args[1].text);
			}
			if (args[1].percent === 100) {
				top.send_control.location = "about:blank";
			}
			break;
		case 'nextMails':
			document.we_form.ecs.value = args[2];
			top.send_control.document.we_form.ecs.value = args[2];
			if (args[1]) {//wait
				setTimeout(function (doc) {
					doc.we_form.submit();
				}, args[1], document);
			} else {
				document.we_form.submit();
			}
			break;

		default:
			// go to newsletter_top.js
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter");
	f.method = (method ? method : "post");
	f.submit();
}

function checkData() {
	if (document.we_form.Text.value === "") {
		top.we_showMessage(WE().consts.g_l.newsletter.empty_name, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	return true;
}

function add(group, newRecipient, htmlmail, salutation, title, firstname, lastname) {
	var //p = document.forms[0].elements["we_recipient" + group],
		optionClassName;

	if (newRecipient !== null) {
		if (newRecipient.length > 0) {
			if (newRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!inSelectBox(document.forms[0].elements["we_recipient" + group], newRecipient)) {
				optionClassName = (isValidEmail(newRecipient) ? "markValid" : "markNotValid");

				addElement(document.forms[0].elements["we_recipient" + group], "#", newRecipient, true, optionClassName);
				addEmail(group, newRecipient, htmlmail, salutation, title, firstname, lastname);
			} else {
				top.we_showMessage(WE().consts.g_l.newsletter.email_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		} else {
			top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
		//set_state_edit_delete_recipient("we_recipient"+group);
	}
}

function deleteit(group) {
	var p = document.forms[0].elements["we_recipient" + group];

	if (p.selectedIndex >= 0) {
		if (window.confirm(WE().consts.g_l.newsletter.email_delete)) {
			delEmail(group, p.selectedIndex);
			p.options[p.selectedIndex] = null;
		}
	} else {
		top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
	//set_state_edit_delete_recipient("we_recipient"+group);
}

function deleteall(group) {
	var p = document.forms[0].elements["we_recipient" + group];

	if (window.confirm(WE().consts.g_l.newsletter.email_delete_all)) {
		delallEmails(group);
		we_cmd("switchPage", 1);
	}
	//set_state_edit_delete_recipient("we_recipient"+group);
}

function editIt(group, index, editRecipient, htmlmail, salutation, title, firstname, lastname) {
	var p = document.forms[0].elements["we_recipient" + group],
		optionClassName;

	if (index >= 0 && editRecipient !== null) {
		if (editRecipient !== "") {
			if (editRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			optionClassName = (isValidEmail(editRecipient) ? "markValid" : "markNotValid");
			p.options[index].text = editRecipient;
			p.options[index].className = optionClassName;
			editEmail(group, index, editRecipient, htmlmail, salutation, title, firstname, lastname);
		} else {
			top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}

function searchEmail(searchname) {
	var f = document.we_form,
		c,
		hit = 0, j;

	if (document.we_form.page.value == 1) {
		for (var i = 0; i < f.groups.value; i++) {
			c = f.elements["we_recipient" + i];
			c.selectedIndex = -1;

			for (j = 0; j < c.length; j++) {
				if (c.options[j].text == searchname) {
					c.selectedIndex = j;
					hit++;
				}
			}
		}
		top.we_showMessage(WE().util.sprintf(WE().consts.g_l.newsletter.search_finished, hit), WE().consts.message.WE_MESSAGE_NOTICE, window);
	}
}

function isValidEmail(email) {
	email = email.toLowerCase();
	return nlView.checkMail ? WE().util.validate.email(email) : true;
}

function setHeaderTitle() {
	if (window.parent.edheader && window.parent.edheader.weTabs && window.parent.edheader.weTabs.setTitlePath) {
		var preObj = document.getElementById("yuiAcInputPathGroup");
		var postObj = document.getElementById("yuiAcInputPathName");

		window.parent.edheader.weTabs.setTitlePath((postObj ? postObj.value : ""), (preObj ? preObj.value : ""));
	} else {
		window.setTimeout(setHeaderTitle, 100);
	}

}

function weShowMailsByStatus(status, group) {
	var maillist = document.getElementById("we_recipient" + group).options;
	var i;
	switch (status) {
		case "0":
			for (i = 0; i < maillist.length; i++) {
				maillist[i].style.display = "";
			}
			break;
		case "1":
			for (i = 0; i < maillist.length; i++) {
				if (maillist[i].className == "markValid") {
					maillist[i].style.display = "none";
				}
			}
			break;
		default :
	}
}

function changeFieldValue(val, valueField) {
	top.content.hot = true;
	document.we_form.ncmd.value = val;
	document.we_form.ngroup.value = valueField;

	switch (val) {
		case "MemberSince":
		case "LastLogin":
		case "LastAccess":
			document.getElementById(valueField).value = "";
	}
	submitForm();
}

function setFocus() {
	if (top.content) {
		if (top.content.get_focus) {
			window.focus();
		} else {
			top.content.get_focus = 1;
		}
	}
}

function setScrollTo() {
	window.parent.scrollToVal = window.pageYOffset;
}

function editEmailFile(eid, email, htmlmail, salutation, title, firstname, lastname) {
	new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=eemail&eid=" + eid + "&etyp=2&email=" + email + "&htmlmail=" + htmlmail + "&salutation=" + salutation + "&title=" + title + "&firstname=" + firstname + "&lastname=" + lastname, "edit_email", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true, true);
}

function setAndSave(eid, email, htmlmail, salutation, title, firstname, lastname) {
	var fr = document.we_form;
	fr.nrid.value = eid;
	fr.email.value = email;
	fr.htmlmail.value = htmlmail;
	fr.salutation.value = salutation;
	fr.title.value = title;
	fr.firstname.value = firstname;
	fr.lastname.value = lastname;
	fr.ncmd.value = "save_email_file";
	submitForm("edit_file");
}

function listFile() {
	var fr = document.we_form;
	fr.nrid.value = "";
	fr.email.value = "";
	fr.htmlmail.value = "";
	fr.salutation.value = "";
	fr.title.value = "";
	fr.firstname.value = "";
	fr.lastname.value = "";
	fr.offset.value = 0;
	submitForm("edit_file");
}

function postSelectorSelect(wePssCmd) {
	switch (wePssCmd) {
		case "selectFile":
			listFile();
			break;
	}
}

function delEmailFile(eid, email) {
	var fr = document.we_form;
	if (window.confirm(WE().util.sprintf(WE().consts.g_l.newsletter.del_email_file, email))) {
		fr.nrid.value = eid;
		fr.ncmd.value = "delete_email_file";
		submitForm("edit_file");
	}
}

function getStatusContol() {
	return document.we_form[nlView.uid + "_Status"].value;
}

function updateText(text) {
	top.send_body.document.we_form.details.value = top.send_body.document.we_form.details.value + "\n" + text;
}

function checkTimeout() {
	return document.we_form.ecs.value;
}

function initControl() {
	if (top.send_control.init) {
		top.send_control.init();
	}
}
