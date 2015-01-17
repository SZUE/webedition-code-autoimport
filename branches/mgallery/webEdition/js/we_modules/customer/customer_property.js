/*
 * webEdition CMS
 *
 * $Rev: 9019 $
 * $Author: mokraemer $
 * $Date: 2015-01-16 23:04:21 +0100 (Fr, 16. Jan 2015) $
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

function setMultiSelectData(name, max) {
	var tmp = "";
	for (var i = 0; i < max; ++i) {
		var val = document.getElementsByName(name + "_multi_" + i)[0];
		if (val.checked) {
			tmp += val.value + ",";
		}
	}
	tmp = tmp.substr(0, tmp.length - 1);
	document.getElementsByName(name)[0].value = tmp;
	top.content.setHot();
}

function formatDate(date, format) {
	var daynum = date.getDate();
	var day = daynum.toString();
	if (format.search("d") != -1) {
		if (daynum < 10) {
			day = "0" + day;
		}
	}

	format = format.replace("d", day).replace("j", day);

	var monthnum = date.getMonth() + 1;
	var month = monthnum.toString();
	if (format.search("m") != -1) {
		if (monthnum < 10) {
			month = "0" + month;
		}
	}

	format = format.replace("m", month).replace("n", month);

	format = format.replace("Y", date.getFullYear());
	var yearnum = date.getYear();
	var year = yearnum.toString();
	format = format.replace("y", year.substr(2, 2));

	var hournum = date.getHours();
	var hour = hournum.toString();
	if (format.search("H") != -1) {
		if (hournum < 10) {
			hour = "0" + hour;
		}
	}

	format = format.replace("H", hour).replace("G", hour);

	var minnum = date.getMinutes();
	var min = minnum.toString();
	if (minnum < 10) {
		min = "0" + min;
	}

	format = format.replace("i", min);


	/*var secnum=date.getSeconds();
	 var sec=secnum.toString();
	 if(secnum<10) sec="0"+sec;*/

	var sec = "00";
	format = format.replace("s", sec).replace(/\\/g, "");
	return format;
}

function we_cmd() {
	var args = "";
	var url = dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {
		case "browse_users":
			new jsWindow(url, "browse_users", -1, -1, 500, 300, true, false, true);
			break;
		case "openImgselector":
		case "openDocselector":
			new jsWindow(url, "we_fileselector", -1, -1, size.docSelect.width, size.docSelect.height, true, true, true, true);
			break;
		case "switchPage":
			document.we_form.cmd.value = arguments[0];
			document.we_form.branch.value = arguments[1];
			submitForm();
			break;
		case "show_search":
			keyword = top.content.we_form_treefooter.keyword.value;
			new jsWindow(dirs.WE_CUSTOMER_MODULE_DIR + "edit_customer_frameset.php?pnt=search&search=1&keyword=" + keyword, "search", -1, -1, 650, 600, true, true, true, false);
			break;
		case "show_customer_settings":
			new jsWindow(dirs.WE_CUSTOMER_MODULE_DIR + "edit_customer_frameset.php?pnt=settings", "customer_settings", -1, -1, 570, 270, true, true, true, false);
			break;
		case "export_customer":
			new jsWindow(dirs.WE_CUSTOMER_MODULE_DIR + "edit_customer_frameset.php?pnt=export", "export_customer", -1, -1, 640, 600, true, true, true, false);
			break;
		case "import_customer":
			new jsWindow(dirs.WE_CUSTOMER_MODULE_DIR + "edit_customer_frameset.php?pnt=import", "import_customer", -1, -1, 640, 600, true, true, true, false);
			break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments[" + i + "]" + ((i < (arguments.length - 1)) ? "," : "");
			}
			eval("top.content.we_cmd(" + args + ")");
	}
}
