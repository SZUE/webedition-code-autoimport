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
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';
var loaded = 0;
function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(caller, url, "we_docselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "setTab":
			top.content.activ_tab = args[1];
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function submitForm(target, action, method) {
	var f = document.we_form;
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=weSearch");
	f.method = (method ? method : "post");
	f.submit();
}

function setTab(tab) {
	switch (tab) {
		default: // just toggle content to show
			parent.edbody.document.we_form.pnt.value = "edbody";
			parent.edbody.document.we_form.tabnr.value = tab;
			parent.edbody.submitForm();
			break;
	}
	window.focus();
	top.content.activ_tab = tab;
}