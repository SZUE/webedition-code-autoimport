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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
var lastCFolder;
function removeAllCats() {
	if (categories_edit.itemCount > 0) {
		while (categories_edit.itemCount > 0) {
			categories_edit.delItem(categories_edit.itemCount);
		}
	}
}

function addCat(paths) {
	var path = paths.split(",");
	var found = false;
	var j = 0;
	for (var i = 0; i < path.length; i++) {
		if (path[i] != "") {
			found = false;
			for (j = 0; j < categories_edit.itemCount; j++) {
				if (categories_edit.form.elements[categories_edit.name + "_variant0_" + categories_edit.name + "_item" + j].value == path[i]) {
					found = true;
				}
			}
			if (!found) {
				categories_edit.addItem();
				categories_edit.setItem(0, (categories_edit.itemCount - 1), path[i]);
			}
		}
	}
	categories_edit.showVariant(0);
}

function toggleButton() {
	if (document.getElementById('CreateTemplate').checked) {
		WE().layout.button.enable(document,'select');
		if (acin = document.getElementById('yuiAcInputTemplate')) {
			document.getElementById('yuiAcInputTemplate').disabled = false;
			lastCFolder = acin.value;
			acin.readOnly = false;
		}
		return true;
	} else {
		WE().layout.button.disable(document,'select');
		if (acin = document.getElementById('yuiAcInputTemplate')) {
			document.getElementById('yuiAcInputTemplate').disabled = true;
			acin.readOnly = true;
			acin.value = lastCFolder;
		}
		return true;
	}
	return false;
}
function incTemp(val) {
	if (val) {
		document.getElementsByName("CreateMasterTemplate")[0].disabled = false;
		document.getElementsByName("CreateIncludedTemplate")[0].disabled = false;
		document.getElementById("label_CreateMasterTemplate").style.color = "black";
		document.getElementById("label_CreateIncludedTemplate").style.color = "black";
	} else {
		document.getElementsByName("CreateMasterTemplate")[0].checked = false;
		document.getElementsByName("CreateIncludedTemplate")[0].checked = false;
		document.getElementsByName("CreateMasterTemplate")[0].disabled = true;
		document.getElementsByName("CreateIncludedTemplate")[0].disabled = true;
		document.getElementById("label_CreateMasterTemplate").style.color = "grey";
		document.getElementById("label_CreateIncludedTemplate").style.color = "grey";
	}
}

function we_cmd() {
	var args = "";
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + escape(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	switch (arguments[0]) {
		case "we_selector_directory":
			new (WE().util.jsWindow)(top.window, url, "we_fileselector", -1, -1, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(top.window, url, "we_cateditor", -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			opener.we_cmd.apply(this, args);
	}
}
