/* global top, WE,transferDateFields */

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
'use strict';

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}


function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));

	if (document.we_form.mode.value == "1") {
		transferDateFields();
	}
	switch (args[0]) {
		case "selectBranch":
			document.we_form.cmd.value = args[0];
			submitForm();
			break;
		case "add_search":
			document.we_form.count.value++;
			submitForm();
			break;
		case "del_search":
			if (document.we_form.count.value > 0) {
				document.we_form.count.value--;
			}
			submitForm();
			break;
		case "search":
			document.we_form.search.value = 1;
			submitForm();
			break;
		case "switchToAdvance":
			document.we_form.mode.value = "1";
			submitForm();
			break;
		case "switchToSimple":
			document.we_form.mode.value = "0";
			submitForm();
			break;
		default:
			top.content.we_cmd.apply(window, Array.prototype.slice.call(arguments));
	}
}


function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = target ? target : "search";
	f.action = action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer";
	f.method = method ? method : "post";

	f.submit();
}