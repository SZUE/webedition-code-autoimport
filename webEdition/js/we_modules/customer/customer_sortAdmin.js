/**
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

/* global top, WE */
'use strict';

var settings = WE().util.getDynamicVar(document, 'loadVarCustomer_sortAdmin', 'data-settings');

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));

	switch (args[0]) {
		case "add_sort_field":
			if (args[1] === "") {
				top.we_showMessage(WE().consts.g_l.customer.sortAdmin.sortname_empty, WE().consts.message.WE_MESSAGE_ERROR, window);
				break;
			}
			document.we_form.sortindex.value = args[1];
			/* falls through */
		case "add_sort":
			document.we_form.cmd.value = args[0];
			submitForm();
			break;
		case "del_sort_field":
			document.we_form.fieldindex.value = args[2];
			/* falls through */
		case "del_sort":
			if (args[1] === settings.default_sort_view) {
				top.we_showMessage(WE().consts.g_l.customer.sortAdmin.default_soting_no_del, WE().consts.message.WE_MESSAGE_ERROR, window);
			} else {
				document.we_form.cmd.value = args[0];
				document.we_form.sortindex.value = args[1];
				submitForm();
			}
			break;
		case "setSorts":
			var selected = opener.top.content.document.we_form_treeheader.sort.selectedIndex;
			window.opener.top.content.document.we_form_treeheader.sort.options.length = 0;
			for (var i = 0; i < args[1].length; i++) {
				window.opener.top.content.addSorting(args[1][i]);
			}

			if (selected < opener.top.content.document.we_form_treeheader.sort.options.length) {
				window.opener.top.content.document.we_form_treeheader.sort.selectedIndex = selected;
			} else {
				window.opener.top.content.document.we_form_treeheader.sort.selectedIndex = opener.top.content.document.we_form_treeheader.sort.options.length - 1;
			}

			window.opener.top.content.applySort();
			window.close();
			break;
		case "save_sort":
		case "selectBranch":
			document.we_form.cmd.value = args[0];
			submitForm();
			break;
		default:
			top.content.we_cmd.apply(window, Array.prototype.slice.call(arguments));

	}
	setScrollTo();
}


function doScrollTo() {
	if (opener.top.content.scrollToVal) {
		window.scrollTo(0, opener.top.content.scrollToVal);
		window.opener.top.content.scrollToVal = 0;
	}
}

function setScrollTo() {
	window.opener.top.content.scrollToVal = window.pageYOffset;
}

function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = target ? target : "sort_admin";
	f.action = action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer";
	f.method = method ? method : "post";

	f.submit();
}