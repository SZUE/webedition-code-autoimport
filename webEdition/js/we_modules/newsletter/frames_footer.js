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
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "empty_log":
			break;
		default:
			window.parent.edbody.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function addGroup(text, val) {
	if (document.we_form.gview) {
		document.we_form.gview[document.we_form.gview.length] = new Option(text, val);
	}
}

function delGroup(val) {
	document.we_form.gview[val] = null;
}

function populateGroups() {
	if (!top.content.editor.edbody.getGroupsNum || !top.content.editor.edbody.loaded) {
		window.setTimeout(populateGroups, 100);
		return;
	}
	var num = top.content.editor.edbody.getGroupsNum();

	if (!num) {
		num = 1;
	} else {
		num++;
	}

	addGroup(WE().util.sprintf(WE().consts.g_l.newsletter.all_list, 0), 0);

	for (var i = 1; i < num; i++) {
		addGroup(WE().util.sprintf(WE().consts.g_l.newsletter.mailing_list, i), i);
	}
}

function we_save() {
	top.content.we_cmd("save_newsletter");
}

function afterLoad() {
	if (window.document.we_form.htmlmail_check !== undefined) {
		if (top.opener.top.nlHTMLMail) {
			window.document.we_form.htmlmail_check.checked = true;
			document.we_form.hm.value = 1;
		} else {
			window.document.we_form.htmlmail_check.checked = false;
			document.we_form.hm.value = 0;
		}
		populateGroups();
	}
}