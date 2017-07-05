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
var activeTab = 0;
var countSaveLoop = 0;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_save_thumbnail(document, url) {
	var acStatus = WE().layout.weSuggest.checkRequired(window);

	if (countSaveLoop > 10) {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		countSaveLoop = 0;
	} else {
		if (acStatus.running) {
			countSaveLoop++;
			window.setTimeout(we_save_thumbnail, 100, document);
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
		case "setHot":
			top.content.setHot();
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}
