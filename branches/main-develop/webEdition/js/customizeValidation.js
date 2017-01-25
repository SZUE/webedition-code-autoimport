/* global top, WE */

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

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "customValidationService":
			if (window.we_submitForm(url)) {
				we_cmd("reload_editpage");
			}
			break;
		case 'reload_hot_editpage':
		case "reload_editpage":
			if (WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_cmd) {
				WE().layout.weEditorFrameController.getActiveDocumentReference().frames[1].we_cmd("reload_editpage");
			}
			window.focus();
			break;
		case "close":
			window.close();
			break;
		default :
			top.opener.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
			break;
	}
}

function we_submitForm(url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}
	f.action = url;
	f.method = "post";

	f.submit();
	return true;
}
