/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 13204 $
 * $Author: mokraemer $
 * $Date: 2017-01-01 20:30:03 +0100 (So, 01. Jan 2017) $
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
'use strict';
function we_submitForm(target, url) {
	var f = document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	var sel = "";
	for (var i = 1; i <= top.treeData.len; i++) {
		if (top.treeData[i].checked)
			sel += (top.treeData[i].name + ",");
	}
	if (!sel) {
		top.we_showMessage(WE().consts.g_l.main.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}
	sel = sel.substring(0, sel.length - 1);
	f.sel.value = sel;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function do_delete() {
	document.we_form.folders.value = top.content.entries_selected.join(",");
	document.we_form.submit();
}

function save() {
	document.edit_folder.submit();
}

var rcpt_sel = [];

function update_rcpts() {
	var rcpt_str = "";

	for (var i = 0; i < rcpt_sel.length; i++) {
		rcpt_str += rcpt_sel[i][2];
		if (i != rcpt_sel.length - 1) {
			rcpt_str += ", ";
		}
	}

	document.compose_form.mn_recipients.value = rcpt_str;
}

function do_send() {
	rcpt_s = encodeURI(document.compose_form.mn_recipients.value);
	document.compose_form.rcpts_string.value = rcpt_s;
	document.compose_form.submit();
}

function save_settings() {
	document.search_adv.submit();
}
