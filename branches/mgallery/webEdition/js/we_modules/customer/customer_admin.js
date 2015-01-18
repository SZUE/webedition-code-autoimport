/*
 * webEdition CMS
 *
 * $Rev: 9019 $
 * $Author: mokraemer $
 * $Date: 2015-01-16 23:04:21 +0100 (Fr, 16. Jan 2015) $
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

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
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
	var args = "";
	var url = frameUrl + "?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {
		case "open_add_field":
			var branch = document.we_form.branch.value;
			url = frameUrl + "?pnt=field_editor&art=add&branch=" + branch;
			new jsWindow(url, "field_editor", -1, -1, 380, 250, true, false, true);
			break;
		case "open_edit_field":
			var field = document.we_form.fields_select.value;
			var branch = document.we_form.branch.value;
			if (field == "") {
				top.we_showMessage(g_l.no_field, WE_MESSAGE_ERROR, window);
			} else {
				url = frameUrl + "?pnt=field_editor&art=edit&field=" + field + "&branch=" + branch;
				new jsWindow(url, "field_editor", -1, -1, 380, 250, true, false, true);
			}
			break;
		case "delete_field":
			var field = document.we_form.fields_select.value;
			if (field == "") {
				top.we_showMessage(g_l.no_field, WE_MESSAGE_ERROR, window);
			} else {
				if (confirm(g_l.del_fild_question)) {
					document.we_form.cmd.value = arguments[0];
					submitForm();
				}
			}
			break;
		case "reset_edit_order":
			var field = document.we_form.fields_select.value;
			var branch = document.we_form.branch.value;
			if (confirm(g_l.reset_edit_order_question)) {
				document.we_form.cmd.value = arguments[0];
				submitForm();
			}
			break;
		case "move_field_up":
			var field = document.we_form.fields_select.value;
			var branch = document.we_form.branch.value;
			if (field == "") {
				top.we_showMessage(g_l.no_field, WE_MESSAGE_ERROR, window);
			} else {
				document.we_form.cmd.value = arguments[0];
				submitForm();
			}
			break;
		case "move_field_down":
			var field = document.we_form.fields_select.value;
			var branch = document.we_form.branch.value;
			if (field == "") {
				top.we_showMessage(g_l.no_field, WE_MESSAGE_ERROR, window);
			} else {
				document.we_form.cmd.value = arguments[0];
				submitForm();
			}
			break;
		case "open_edit_branch":
			var branch = document.we_form.branch_select.options[document.we_form.branch_select.selectedIndex].text;
			if (branch == "") {
				top.we_showMessage(g_l.no_branch, WE_MESSAGE_ERROR, window);
			} else if (branch == g_l.other) {
				top.we_showMessage(g_l.branch_no_edit, WE_MESSAGE_ERROR, window);
			} else {
				url = frameUrl + "?pnt=branch_editor&art=edit&&branch=" + branch;
				new jsWindow(url, "field_editor", -1, -1, 380, 250, true, false, true);
			}
			break;
		case "save_branch":
		case "save_field":
			var field_name = document.we_form.name.value;
			if (field_name == "" || field_name.match(/[^a-zA-Z0-9\_]/) != null) {
				top.we_showMessage(g_l.we_fieldname_notValid, WE_MESSAGE_ERROR, window);
			} else {
				document.we_form.cmd.value = arguments[0];
				submitForm("field_editor");
			}
			break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
			}
			eval('top.content.we_cmd(' + args + ')');
	}
}