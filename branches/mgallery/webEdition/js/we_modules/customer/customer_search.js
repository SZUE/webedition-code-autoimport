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

function doUnload() {
	jsWindowCloseAll();s
}


function we_cmd() {
	var url = frames.set + "?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	if (document.we_form.mode.value == "1") {
		transferDateFields();
	}
	switch (arguments[0]) {
		case "selectBranch":
			document.we_form.cmd.value = arguments[0];
			submitForm();
			break;
		case "add_search":
			document.we_form.count.value++;
			submitForm();
			break;
		case "del_search":
			if (document.we_form.count.value > 0)
				document.we_form.count.value--;
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
			var args = [];
			for (i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);
	}
}