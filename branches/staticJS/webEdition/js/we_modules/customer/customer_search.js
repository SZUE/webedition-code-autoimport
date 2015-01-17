/**
 * webEdition CMS
 *
 * $Rev: 8972 $
 * $Author: mokraemer $
 * $Date: 2015-01-13 21:33:12 +0100 (Di, 13. Jan 2015) $
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
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}


function we_cmd() {
	var args = "";
	var url = frames.set + "?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	if (document.we_form.mode.value == "1"){
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
			for (var i = 0; i < arguments.length; i++) {
				args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
			}
			eval('top.content.we_cmd(' + args + ')');
	}
}