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

function PopulateVar(p, dest) {
	var arr = [];

	for (i = 0; i < p.length; i++) {
		arr[i] = p.options[i].text;
	}

	dest.value = arr.join();
}

function PopulateMultipleVar(p, dest) {
	var arr = [];
	c = 0;

	for (i = 0; i < p.length; i++) {
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

	top.content.hot = 1;
}

function editEmail(group, id, email, html, salutation, title, firstname, lastname) {
	var dest = document.forms[0].elements["group" + group + "_Emails"];
	var str = dest.value;

	var arr = str.split("\n");

	arr[id] = email + "," + html + "," + salutation + "," + title + "," + firstname + "," + lastname;

	dest.value = arr.join("\n");

	top.content.hot = 1;
}

function mysplice(arr, id) {
	var newarr = [];

	for (i = 0; i < arr.lenght; i++) {
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
	top.content.hot = 1;
}

function delallEmails(group) {
	var dest = document.forms[0].elements["group" + group + "_Emails"];

	dest.value = "";
	top.content.hot = 1;
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

function getNumOfDocs() {
	return 0;
}

function switchRadio(a, b) {
	a.value = 1;
	a.checked = true;
	b.value = 0;
	b.checked = false;

	if (arguments[3]) {
		c = arguments[3];
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
	old = document.we_form.pnt.value;
	document.we_form.pnt.value = pnt;

	new (WE().util.jsWindow)(window, "about:blank", wname, -1, -1, width, height, true, true, true, true);
	submitForm(wname);
	document.we_form.pnt.value = old;
}

function doScrollTo() {
	if (parent.scrollToVal) {
		window.scrollTo(0, parent.scrollToVal);
		parent.scrollToVal = 0;
	}
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

/**
 * Newsletter command controler
 */
function we_cmd() {
	var args = "";
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	var i;
	for (i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	if (arguments[0] != "switchPage") {
		self.setScrollTo();
	}

	switch (arguments[0]) {
		case "we_users_selector":
			new (WE().util.jsWindow)(window, url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;

		case "browse_server":
			new (WE().util.jsWindow)(window, url, "browse_server", -1, -1, 840, 400, true, false, true);
			break;

		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(window, url, "we_docselector", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;

		case "we_selector_file":
			new (WE().util.jsWindow)(window, url, "we_selector", -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;

		case "openNewsletterDirselector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=we_newsletter_dirSelector&";
			for (i = 1; i < arguments.length; i++) {
				url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
				if (i < (arguments.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(window, url, "we_newsletter_dirselector", -1, -1, 600, 400, true, true, true);
			break;

		case "add_customer":
			document.we_form.ngroup.value = arguments[2];
			//no break;
		case "del_customer":
			document.we_form.ncmd.value = arguments[0];
			document.we_form.ncustomer.value = arguments[1];
			top.content.hot = 1;
			submitForm();
			break;

		case "del_all_customers":
		case "del_all_files":
			top.content.hot = 1;
			document.we_form.ncmd.value = arguments[0];
			document.we_form.ngroup.value = arguments[1];
			submitForm();
			break;

		case "add_file":
			document.we_form.ngroup.value = arguments[2];
		case "del_file":
			document.we_form.ncmd.value = arguments[0];
			document.we_form.nfile.value = arguments[1];
			top.content.hot = 1;
			submitForm();
			break;

		case "switchPage":
			document.we_form.ncmd.value = arguments[0];
			document.we_form.page.value = arguments[1];
			submitForm();
			break;

		case "set_import":
		case "reset_import":
		case "set_export":
		case "reset_export":
			document.we_form.ncmd.value = arguments[0];
			document.we_form.ngroup.value = arguments[1];
			submitForm();
			break;

		case "addBlock":
		case "delBlock":
			document.we_form.ncmd.value = arguments[0];
			document.we_form.blockid.value = arguments[1];
			top.content.hot = 1;
			submitForm();
			break;

		case "addGroup":
		case "delGroup":
			document.we_form.ncmd.value = arguments[0];
			document.we_form.ngroup.value = arguments[1];
			top.content.hot = 1;
			submitForm();
			break;

		case "popPreview":
			if (document.we_form.ncmd.value == "home")
				return;
			if (top.content.hot !== 0) {
				top.we_showMessage(WE().consts.g_l.newsletter.must_save_preview, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				document.we_form.elements["we_cmd[0]"].value = "preview_newsletter";
				document.we_form.gview.value = parent.edfooter.document.we_form.gview.value;
				document.we_form.hm.value = parent.edfooter.document.we_form.hm.value;
				popAndSubmit("newsletter_preview", "preview", 800, 800);
			}
			break;

		case "popSend":
			if (document.we_form.ncmd.value == "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.hot !== 0) {
				top.we_showMessage(WE().consts.g_l.newsletter.must_save, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (document.we_form.IsFolder.value === 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {

				message_text = (arguments[1] ? WE().consts.g_l.newsletter.send_test_question : WE().consts.g_l.newsletter.send_question);

				if (confirm(message_text)) {
					document.we_form.ncmd.value = arguments[0];
					if (arguments[1])
						document.we_form.test.value = arguments[1];
					submitForm();
				}
			}
			break;

		case "send_test":
			if (document.we_form.ncmd.value == "home") {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (top.content.hot !== 0) {
				top.we_showMessage(WE().consts.g_l.newsletter.must_save, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else if (document.we_form.IsFolder.value == 1) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_newsletter_selected, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				if (confirm(WE().consts.g_l.newsletter.test_email_question)) {
					document.we_form.ncmd.value = arguments[0];
					document.we_form.gview.value = parent.edfooter.document.we_form.gview.value;
					document.we_form.hm.value = parent.edfooter.document.we_form.hm.value;
					submitForm();
				}
			}
			break;
		case "print_lists":
		case "domain_check":
		case "show_log":
			if (document.we_form.ncmd.value != "home")
				popAndSubmit(arguments[0], arguments[0], 650, 650);
			break;
		case "newsletter_settings":
			new (WE().util.jsWindow)(window, modFrameSet + "&pnt=" + arguments[0], arguments[0], -1, -1, 600, 750, true, true, true, true);
			break;

		case "black_list":
			new (WE().util.jsWindow)(window, modFrameSet + "&pnt=" + arguments[0], arguments[0], -1, -1, 560, 460, true, true, true, true);
			break;

		case "edit_file":
			if (arguments[1]) {
				new (WE().util.jsWindow)(window, modFrameSet + "&pnt=" + arguments[0] + "&art=" + arguments[1], arguments[0], -1, -1, 950, 640, true, true, true, true);
			} else {
				new (WE().util.jsWindow)(window, modFrameSet + "&pnt=" + arguments[0], arguments[0], -1, -1, 950, 640, true, true, true, true);
			}
			break;

		case "reload_table":
		case "copy_newsletter":
			top.content.hot = 1;
			document.we_form.ncmd.value = arguments[0];
			submitForm();
			break;

		case "add_filter":
		case "del_filter":
		case "del_all_filters":
			top.content.hot = 1;
			document.we_form.ncmd.value = arguments[0];
			document.we_form.ngroup.value = arguments[1];
			submitForm();
			break;

		case "switch_sendall":
			document.we_form.ncmd.value = arguments[0];
			top.content.hot = 1;
			eval("if(document.we_form.sendallcheck_" + arguments[1] + ".checked) document.we_form.group" + arguments[1] + "_SendAll.value=1; else document.we_form.group" + arguments[1] + "_SendAll.value=0;");
			submitForm();
			break;

		case "save_settings":
			document.we_form.ncmd.value = arguments[0];
			submitForm("newsletter_settings");
			break;

		case "import_csv":
		case "export_csv":
			document.we_form.ncmd.value = arguments[0];
			submitForm();
			break;

		case "do_upload_csv":
			document.we_form.ncmd.value = arguments[0];
			submitForm("upload_csv");
			break;

		case "do_upload_black":
			document.we_form.ncmd.value = arguments[0];
			submitForm("upload_black");
			break;

		case "upload_csv":
		case "upload_black":
			new (WE().util.jsWindow)(window, modFrameSet + "&pnt=" + arguments[0] + "&grp=" + arguments[1], arguments[0], -1, -1, 450, 270, true, true, true, true);
			break;

		case "add_email":
			var email = document.we_form.group = arguments[1];
			new (WE().util.jsWindow)(window, modFrameSet + "&pnt=eemail&grp=" + arguments[1], "edit_email", -1, -1, 450, 270, true, true, true, true);
			break;

		case "edit_email":
			eval("var p=document.we_form.we_recipient" + arguments[1] + ";");

			if (p.selectedIndex < 0) {
				top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			eval("var dest=document.we_form.group" + arguments[1] + "_Emails;");

			var str = dest.value;

			var arr = str.split("\n");

			var str2 = arr[p.selectedIndex];
			var arr2 = str2.split(",");
			var eid = p.selectedIndex;
			var email = p.options[p.selectedIndex].text;
			var htmlmail = arr2[1];
			var salutation = arr2[2];
			var title = arr2[3];
			var firstname = arr2[4];
			var lastname = arr2[5];

			salutation = encodeURIComponent(salutation.replace("+", "[:plus:]"));
			title = encodeURIComponent(title.replace("+", "[:plus:]"));
			firstname = encodeURIComponent(firstname.replace("+", "[:plus:]"));
			lastname = encodeURIComponent(lastname.replace("+", "[:plus:]"));
			email = encodeURIComponent(email);
			new (WE().util.jsWindow)(window, modFrameSet + "&pnt=eemail&grp=" + arguments[1] + "&etyp=1&eid=" + eid + "&email=" + email + "&htmlmail=" + htmlmail + "&salutation=" + salutation + "&title=" + title + "&firstname=" + firstname + "&lastname=" + lastname, "edit_email", -1, -1, 450, 270, true, true, true, true);
			break;

		case "save_black":
		case "import_black":
		case "export_black":
			document.we_form.ncmd.value = arguments[0];
			PopulateVar(document.we_form.blacklist_sel, document.we_form.black_list);
			submitForm("black_list");
			break;
		case "search_email":
			if (document.we_form.ncmd.value === "home") {
				return;
			}
			var searchname = prompt(WE().consts.g_l.newsletter.search_text, "");

			if (searchname !== null) {
				searchEmail(searchname);
			}

			break;
		case "clear_log":
			new (WE().util.jsWindow)(window, modFrameSet + "&pnt=" + arguments[0], arguments[0], -1, -1, 450, 300, true, true, true, true);
			break;

		default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);

	}
}

function submitForm() {
	if (self.weWysiwygSetHiddenText) {
		weWysiwygSetHiddenText();
	}

	var f = self.document.we_form;

	f.target = (arguments[0] ? arguments[0] : "edbody");
	f.action = (arguments[1] ? arguments[1] : modFrameSet);
	f.method = (arguments[2] ? arguments[2] : "post");

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
	var p = document.forms[0].elements["we_recipient" + group];

	if (newRecipient !== null) {
		if (newRecipient.length > 0) {
			if (newRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!inSelectBox(document.forms[0].elements["we_recipient" + group], newRecipient)) {
				if (isValidEmail(newRecipient))
					optionClassName = "markValid";
				else
					optionClassName = "markNotValid";
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
		if (confirm(WE().consts.g_l.newsletter.email_delete)) {
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

	if (confirm(WE().consts.g_l.newsletter.email_delete_all)) {
		delallEmails(group);
		we_cmd("switchPage", 1);
	}
	//set_state_edit_delete_recipient("we_recipient"+group);
}

function editIt(group, index, editRecipient, htmlmail, salutation, title, firstname, lastname) {
	var p = document.forms[0].elements["we_recipient" + group];

	if (index >= 0 && editRecipient !== null) {
		if (editRecipient !== "") {
			if (editRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}
			if (isValidEmail(editRecipient))
				optionClassName = "markValid";
			else
				optionClassName = "markNotValid";
			p.options[index].text = editRecipient;
			p.options[index].className = optionClassName;
			editEmail(group, index, editRecipient, htmlmail, salutation, title, firstname, lastname);
		} else {
			top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}

function searchEmail(searchname) {
	var f = document.we_form;
	var c;
	var hit = 0;

	if (document.we_form.page.value == 1) {
		for (i = 0; i < f.groups.value; i++) {
			c = f.elements["we_recipient" + i];
			c.selectedIndex = -1;

			for (j = 0; j < c.length; j++) {
				if (c.options[j].text == searchname) {
					c.selectedIndex = j;
					hit++;
				}
			}
		}
		msg = WE().util.sprintf(WE().consts.g_l.newsletter.search_finished, hit);
		top.we_showMessage(msg, WE().consts.message.WE_MESSAGE_NOTICE, window);
	}
}

function isValidEmail(email) {
	email = email.toLowerCase();
	return checkMail ? we.validate.email(email) : true;
}

function setHeaderTitle() {
	if (parent.edheader && parent.edheader.weTabs.setTitlePath) {
		var preObj = document.getElementById("yuiAcInputPathGroup");
		var postObj = document.getElementById("yuiAcInputPathName");

		parent.edheader.weTabs.setTitlePath((postObj ? postObj.value : ""), (preObj ? preObj.value : ""));
	} else {
		setTimeout(setHeaderTitle, 100);
	}

}

function weShowMailsByStatus(status, group) {
	var maillist = document.getElementById("we_recipient" + group).options;
	switch (status) {
		case "0":
			for (var i = 0; i < maillist.length; i++) {
				maillist[i].style.display = "";
			}
			break;
		case "1":
			for (var i = 0; i < maillist.length; i++) {
				if (maillist[i].className == "markValid") {
					maillist[i].style.display = "none";
				}
			}
			break;
		default :
			//alert(status);
	}
}

function calendarSetup(group, x) {
	for (i = 0; i <= x; i++) {
		if (document.getElementById("date_picker_from_" + group + "_" + i + "") != null) {
			Calendar.setup({inputField: "filter_fieldvalue_" + group + "_" + i + "", ifFormat: "%d.%m.%Y", button: "date_picker_from_" + group + "_" + i + "", align: "Tl", singleClick: true});
		}
	}
}

function changeFieldValue(val, valueField) {
	top.content.hot = 1;
	document.we_form.ncmd.value = arguments[0];
	document.we_form.ngroup.value = arguments[1];

	if (val == "MemberSince" || val == "LastLogin" || val == "LastAccess") {
		document.getElementById(valueField).value = "";
	}
	submitForm();
}

function setFocus() {
	if (top.content) {
		if (top.content.get_focus) {
			self.focus();
		} else {
			top.content.get_focus = 1;
		}
	}
}

function setScrollTo() {
	parent.scrollToVal = pageYOffset;
}
