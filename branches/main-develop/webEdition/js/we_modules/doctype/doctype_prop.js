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
WE().util.loadConsts(document, "g_l.doctypeEdit");
var activeTab = 0;

function doUnload() {
        WE().util.jsWindow.prototype.closeAll(window);
}

function disableLangDefault(allnames, allvalues, deselect) {
	var arr = allvalues.split(",");

	for (var i = 0; i < arr.length; i++) {
		document.we_form.elements[allnames + '[' + arr[i] + ']'].disabled = false;
	}

	document.we_form.elements[allnames + '[' + deselect + ']'].disabled = true;
}

function askForSaveOrRefireCmd(args) {
	WE().util.showConfirm(window, "", WE().consts.g_l.doctypeEdit.save_changed_doctype, ["save_docType"], ["continueCommand", Array.prototype.slice.call(args)]);
}

var countSaveLoop = 0;
function we_save_docType(document, url) {
	var acStatus = WE().layout.weSuggest.checkRequired(window);

	if (countSaveLoop > 10) {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		countSaveLoop = 0;
	} else {
		if (acStatus.running) {
			countSaveLoop++;
			window.setTimeout(we_save_docType, 100, document);
		} else if (!acStatus.valid) {
			WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			countSaveLoop = 0;
		} else {
			countSaveLoop = 0;
			submitForm(url);
		}
	}
}


function submitForm(url) {
	var f = /*form ? window.document.forms[form] :*/ window.document.we_form;
	f.target = "edbody";
	f.action = url;
	f.method = "post";

	f.submit();
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var url2 = WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=doctype&pnt=edbody&");

	switch (args[0]) {
		case "continueCommand":
			top.content.unsetHot();
			we_cmd.apply(window, args[1]);
			break;
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
			url2 += "&we_cmd[1]=" + args[1].allIDs.join(",");
			/*falls through*/
		case "delete_dt_template":
		case "dt_delete_cat":
			top.content.setHot();
			we_save_docType(document, url2); // FIXME: bad function name since we just submit form to execute cmd other than save!
			break;
		case "save_docType":
			top.content.unsetHot();
			we_save_docType(document, url2);
			break;
		case "setHot":
			top.content.setHot();
			break;
		case "change_docType":
			if (top.content.hot) {
				askForSaveOrRefireCmd(args);
				break;
			}
			top.content.unsetHot();
			caller.location = url;
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}
