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

var customer = WE().util.getDynamicVar(document, 'loadVarCustomer_property', 'data-customer');

var loaded = 0;

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
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
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "switchPage":
			document.we_form.cmd.value = args[0];
			document.we_form.branch.value = args[1];
			submitForm();
			break;
		case "submitForm":
			submitForm();
			break;
		case "setNewBranchName":
			opener.document.we_form.branch.value = args[1];
			submitForm();
			if (window.opener.document.we_form && window.opener.document.we_form.branch) {
				window.opener.document.we_form.branch.value = args[2];
				window.opener.refreshForm();
			}
			caller.close();
			break;
		case 'refreshForm':
			refreshForm();
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function resetLogins(id) {
	WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=ResetFailedCustomerLogins&cns=customer", "custid=" + id, function (weResponse) {
		if (weResponse) {
			if (weResponse.DataArray.data === "true") {
				document.getElementById("FailedCustomerLogins").innerText = weResponse.DataArray.value;
			}
			top.we_showMessage(WE().consts.g_l.customer.view.reset_failed_login_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
		}
	});
}

function refreshForm() {
	if (document.we_form.cmd.value !== "home") {
		we_cmd("switchPage", top.content.activ_tab);
		top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer&pnt=edheader&text=" + encodeURI(customer.username);
	}
}

function submitForm(target, action, method, form) {
	var f = form ? window.document.forms[form] : window.document.we_form;
	f.target = target ? target : "edbody";
	f.action = action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=customer";
	f.method = method ? method : "post";

	f.submit();
}