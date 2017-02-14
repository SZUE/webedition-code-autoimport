/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
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
WE().util.loadConsts(document, "g_l.doctypeEdit");
var doctype = WE().util.getDynamicVar(document, 'loadVarDoctypeEdit', 'data-doctype');

var countSaveLoop = 0;
function we_save_docType(doc, url) {
	var acStatus = WE().layout.weSuggest.checkRequired(window);

	if (countSaveLoop > 10) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		countSaveLoop = 0;
	} else {
		if (acStatus.running) {
			countSaveLoop++;
			window.setTimeout(we_save_docType, 100, doc, url);
		} else if (!acStatus.valid) {
			top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			countSaveLoop = 0;
		} else {
			countSaveLoop = 0;
			we_submitForm(doc, url);
		}
	}
}

function we_submitForm(target, url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function doUnload() {
	/*WE().util.jsWindow.prototype.closeAll(window);
	 opener.top.dc_win_open = false;*/
}

function disableLangDefault(allnames, allvalues, deselect) {
	var arr = allvalues.split(",");
	var w, e;

	for (var v in arr) {
		w = allnames + '[' + arr[v] + ']';
		e = document.getElementById(w);
		e.disabled = false;
	}
	w = allnames + '[' + deselect + ']';
	e = document.getElementById(w);
	e.disabled = true;
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "add_dt_template":
		case "dt_add_cat":
			url += "&we_cmd[1]=" + args[1].allIDs.join(",");
			we_save_docType(caller, url);
			break;

		case "delete_dt_template":
		case "dt_delete_cat":
		case "save_docType":
			we_save_docType(caller, url);
			break;
		case "newDocType":
			var name = window.prompt(WE().consts.g_l.doctypeEdit.newDocTypeName, "");
			if (name !== null) {
				if ((name.indexOf("<") !== -1) || (name.indexOf(">") !== -1)) {
					top.we_showMessage(WE().consts.g_l.main.name_nok, WE().consts.message.WE_MESSAGE_ERROR, window);
					return;
				}
				if (name.indexOf("'") !== -1 || name.indexOf('"') !== -1 || name.indexOf(',') !== -1) {
					top.we_showMessage(WE().consts.g_l.doctypeEdit.doctype_hochkomma, WE().consts.message.WE_MESSAGE_ERROR, window);
				} else if (name === "") {
					top.we_showMessage(WE().consts.g_l.doctypeEdit.doctype_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
				} else if (WE().util.in_array(name, doctype.docTypeNames)) {
					top.we_showMessage(WE().consts.g_l.doctypeEdit.doctype_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
				} else {
					/*						if (top.opener.top.header) {
					 top.opener.top.header.location.reload();
					 }*/
					window.location = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=newDocType&we_cmd[1]=" + encodeURIComponent(name);
				}
			}
			break;
		case "change_docType":
		case "deleteDocType":
		case "deleteDocTypeok":
			caller.location = url;
			break;
		case "confirmDeleteDocType":
			WE().util.showConfirm(window, "", args[1].msg, args[1].yes);
			break;
		default:
			window.opener.top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}
