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

/* global WE, top */

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function selectBranch() {
	var f = document.we_form;
	var txt = f.branch;
	var sel = f.branch_select.options[f.branch_select.selectedIndex].text;
	f.cmd.value = "switchBranch";
	txt.value = sel;
	submitForm();
}

function saveField() {
	document.we_form.cmd.value = "save_field";
	submitForm();
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&");

	var branch, field;
	switch (args[0]) {
		case "open_add_field":
			branch = document.we_form.branch.value;
			url += "&pnt=field_editor&art=add&branch=" + branch;
			new (WE().util.jsWindow)(this, url, "field_editor", -1, -1, 380, 250, true, false, true);
			break;
		case "open_edit_field":
			field = document.we_form.fields_select.value;
			branch = document.we_form.branch.value;
			if (field === "") {
				top.we_showMessage(WE().consts.g_l.customer.admin.no_field, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				url += "&pnt=field_editor&art=edit&field=" + field + "&branch=" + branch;
				new (WE().util.jsWindow)(this, url, "field_editor", -1, -1, 380, 250, true, false, true);
			}
			break;
		case "delete_field":
			field = document.we_form.fields_select.value;
			if (field === "") {
				top.we_showMessage(WE().consts.g_l.customer.admin.no_field, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			if (confirm(WE().consts.g_l.customer.admin.del_fild_question)) {
				document.we_form.cmd.value = args[0];
				submitForm();
			}
			break;
		case "move_field_up":
		case "move_field_down":
			field = document.we_form.fields_select.value;
			branch = document.we_form.branch.value;
			if (field === "") {
				top.we_showMessage(WE().consts.g_l.customer.admin.no_field, WE().consts.message.WE_MESSAGE_ERROR, this);
				break;
			}
			document.we_form.cmd.value = args[0];
			submitForm();

			break;
		case "open_edit_branch":
			branch = document.we_form.branch_select.options[document.we_form.branch_select.selectedIndex].text;
			if (branch === "") {
				top.we_showMessage(WE().consts.g_l.customer.admin.no_branch, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else if (branch === WE().consts.g_l.customer.admin.other) {
				top.we_showMessage(WE().consts.g_l.customer.admin.branch_no_edit, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				url += "&pnt=branch_editor&art=edit&&branch=" + branch;
				new (WE().util.jsWindow)(this, url, "field_editor", -1, -1, 380, 250, true, false, true);
			}
			break;
		case "save_branch":
		case "save_field":
			var field_name = document.we_form.name.value;
			if (field_name === "" || field_name.match(/[^a-zA-Z0-9\_]/) !== null) {
				top.we_showMessage(WE().consts.g_l.customer.admin.we_fieldname_notValid, WE().consts.message.WE_MESSAGE_ERROR, this);
			} else {
				document.we_form.cmd.value = args[0];
				submitForm("field_editor");
			}
			break;
		default:
			top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function setStatusEncryption(type) {
	var encSelect = document.getElementsByName('field_encrypt')[0];
	switch (type) {
		case 'input':
		case 'textarea':
			encSelect.disabled = false;
			break;
		default:
			encSelect.disabled = true;
	}

}

function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = target ? target : "customer_admin";
	f.action = action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer";
	f.method = method ? method : "post";

	f.submit();
}

function setFieldsButtons(pos, max) {
	document.getElementById("editFieldButton").disabled = false;
	document.getElementById("deleteFieldButton").disabled = false;
	document.getElementById("moveFieldUpButton").disabled = (pos === 0);
	document.getElementById("moveFieldDownButton").disabled = (pos === (max - 1));
}