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
function addBlack() {
	var p = document.we_form.elements.blacklist_sel;
	var newRecipient = window.prompt(WE().consts.g_l.newsletter.add_email, "");

	if (newRecipient !== null) {
		if (newRecipient.length > 0) {
			if (newRecipient.length > 255) {
				top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
				return;
			}

			if (!inSelectBox(p, newRecipient)) {
				addElement(p, "#", newRecipient, true);
			} else {
				top.we_showMessage(WE().consts.g_l.newsletter.email_exists, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		} else {
			top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	}
}

function deleteBlack() {
	var p = document.we_form.elements.blacklist_sel;

	if (p.selectedIndex >= 0) {
		if (window.confirm(WE().consts.g_l.newsletter.email_delete)) {
			p.options[p.selectedIndex] = null;
		}
	}
}

function deleteallBlack() {
	var p = document.we_form.elements.blacklist_sel;

	if (window.confirm(WE().consts.g_l.newsletter.email_delete_all)) {
		p.options.length = 0;
	}
}

function editBlack() {
	var p = document.we_form.elements.blacklist_sel;
	var index = p.selectedIndex;

	if (index >= 0) {
		var editRecipient = window.prompt(WE().consts.g_l.newsletter.edit_email, p.options[index].text);

		if (editRecipient !== null) {
			if (editRecipient !== "") {
				if (editRecipient.length > 255) {
					top.we_showMessage(WE().consts.g_l.newsletter.email_max_len, WE().consts.message.WE_MESSAGE_ERROR, window);
					return;
				}
				p.options[index].text = editRecipient;
			} else {
				top.we_showMessage(WE().consts.g_l.newsletter.no_email, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		}
	}
}

function set_import(val) {
	document.we_form.sib.value = val;

	if (val == 1) {
		document.we_form.seb.value = 0;
	}

	populateVar(document.we_form.blacklist_sel, document.we_form.black_list);
	submitForm("black_list");
}

function set_export(val) {
	document.we_form.seb.value = val;

	if (val == 1) {
		document.we_form.sib.value = 0;
	}

	populateVar(document.we_form.blacklist_sel, document.we_form.black_list);
	submitForm("black_list");
}

function clearLog() {
	var f = window.document.we_form;
	f.action = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter";
	f.method = "post";
	f.submit();
}


function ask(start, group) {
	//WE().util.showConfirm(window,"","' . g_l('modules_newsletter', '[continue_camp]') . '");
	new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=qsend&start=" + start + "&grp=" + group, "send_question", WE().consts.size.dialog.smaller, WE().consts.size.dialog.tiny, true, true, true, false);
}

function no() {
	doSend(0, 0);
}
function cancel() {
	window.close();
}
