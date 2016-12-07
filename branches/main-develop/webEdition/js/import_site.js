/* global WE, top, self*/

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

WE().util.loadConsts(document, "g_l.import");

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "submit_form":
			top.siteimportcontent.document.we_form.submit();
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(window, url, "we_docselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;

		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url, "we_dirselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "browse_server":
			new (WE().util.jsWindow)(window, url, "browse_server", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, false, true);
			break;
		case "siteImportCreateWePageSettings":
			new (WE().util.jsWindow)(window, url, "siteImportCreateWePageSettings", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, false, true);
			break;
		case "displayTable":
			displayTable();
			break;
		default:
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

function hideTable() {
	document.getElementById("specifyParam").style.display = "none";
}

function displayTable() {
	if (document.we_form.templateID.value > 0) {
		document.getElementById("specifyParam").style.display = "block";
		var iframeObj = document.getElementById("iloadframe");
		iframeObj.src = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=updateSiteImportTable&tid=" + document.we_form.templateID.value;
	}
}
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}


function createTypeChanged(s) {
	var val = s.options[s.selectedIndex].value;
	document.getElementById("ctauto").style.display = (val === "auto") ? "block" : "none";
	document.getElementById("ctspecify").style.display = (val === "specify") ? "block" : "none";
}

function dateFormatChanged(s) {
	var val = s.options[s.selectedIndex].value;
	document.getElementById("ownValueInput").style.display = (val === "own") ? "block" : "none";
	document.getElementById("ownValueInputHelp").style.display = (val === "own") ? "block" : "none";
}

function showDateHelp() {
	// this is a real alert, dont use showMessage yet
	top.we_showMessage(WE().consts.g_l.import.format_timestamp, top.WE().consts.message.WE_MESSAGE_INFO, window);
}

function checkForm() {
	var f = document.forms[0];
	var i, search, result, index, key;
	var createType = f.createType.options[f.createType.selectedIndex].value;
	if (createType === "specify") {
		// check if template is selected
		if (f.templateID.value == "0" || f.templateID.value === "") {
			top.we_showMessage(WE().consts.g_l.import.pleaseSelectTemplateAlert, top.WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}
		// check value of fields
		var fields = [];
		var inputElements = f.getElementsByTagName("input");
		for (i = 0; i < inputElements.length; i++) {
			if (inputElements[i].name.indexOf("fields[") === 0) {
				search = /^fields\[([^\]]+)\]\[([^\]]+)\]$/;
				result = search.exec(inputElements[i].name);
				index = parseInt(result[1]);
				key = result[2];
				if (fields[index] === null) {
					fields[index] = {};
				}
				fields[index][key] = inputElements[i].value;
			}
		}
		var textareaElements = f.getElementsByTagName("textarea");
		for (i = 0; i < textareaElements.length; i++) {
			if (textareaElements[i].name.indexOf("fields[") === 0) {
				search = /^fields\[([^\]]+)\]\[([^\]]+)\]$/;
				result = search.exec(textareaElements[i].name);
				index = parseInt(result[1]);
				key = result[2];
				if (fields[index] === null) {
					fields[index] = {};
				}
				fields[index][key] = textareaElements[i].value;
			}
		}
		var filled = 0;
		for (i = 0; i < fields.length; i++) {
			if (fields[i].pre.length > 0 && fields[i].post.length > 0) {
				filled = 1;
				break;
			}
		}
		if (filled === 0) {
			top.we_showMessage(WE().consts.g_l.import.startEndMarkAlert, top.WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}
		if (document.getElementById("ownValueInput").style.display !== "none") {
			if (f.dateformatField.value.length === 0) {
				top.we_showMessage(WE().consts.g_l.import.errorEmptyDateFormat, top.WE().consts.message.WE_MESSAGE_ERROR, window);
				return false;
			}
		}
	} else {
		if (f.templateName.value.length === 0) {
			top.we_showMessage(WE().consts.g_l.import.nameOfTemplateAlert, top.WE().consts.message.WE_MESSAGE_ERROR, window);
			f.templateName.focus();
			f.templateName.select();
			return false;
		}
		var reg = /[^a-z0-9\._+\-]/gi;
		if (reg.test(f.templateName.value)) {
			top.we_showMessage(WE().consts.g_l.import.we_filename_notValid, top.WE().consts.message.WE_MESSAGE_ERROR, window);
			f.templateName.focus();
			f.templateName.select();
			return false;
		}
	}
	return true;
}

function back() {
	top.location.href = WE().consts.dirs.WEBEDITION_DIR + 'we_cmd.php?we_cmd[0]=import&we_cmd[1]=siteImport';
}

function next() {
	var testvalue = 0;
	if (!top.siteimportcontent.document.we_form.from.value || top.siteimportcontent.document.we_form.from.value == '/') {
		testvalue += 1;
	}
	if (top.siteimportcontent.document.we_form.to.value === "0" || !top.siteimportcontent.document.we_form.to.value) {
		testvalue += 2;
	}
	switch (testvalue) {
		case 0:
			top.siteimportcontent.document.we_form.submit();
			break;
		case 1:
			WE().util.showConfirm(window, "", WE().consts.g_l.import.root_dir_1, [
				"submit_form"]);
			break;
		case 2:
			WE().util.showConfirm(window, "", WE().consts.g_l.import.root_dir_2, [
				"submit_form"]);
			break;
		case 3:
			WE().util.showConfirm(window, "", WE().consts.g_l.import.root_dir_3, [
				"submit_form"]);
			break;
		default:
	}
}