/* global WE */

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
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

var allFields = [
	"FolderID", "DoctypeID", "ClassID", "WorkspaceID"
];
var resetFields = [
	"NavigationName", "NavigationID", "NavigationIDPath", "FolderID", "FolderIDPath", "DoctypeID", "ClassID", "ClassIDPath", "WorkspaceID"
];

function switchType(value) {
	// 1st hide all
	for (i = 0; i < allFields.length; i++) {
		if ((elem = document.getElementById("tr" + allFields[i]))) {
			elem.style.display = "none";
		}
	}

	// show needed
	if (dependencies[value]) {

		for (j = 0; j < dependencies[value].length; j++) {
			if ((elem = document.getElementById("tr" + dependencies[value][j]))) {
				elem.style.display = "";
			}
		}
	}
}

function clearNavigationForm() {
	for (i = 0; i < resetFields.length; i++) {
		if (document.we_form[resetFields[i]]) {
			document.we_form[resetFields[i]].value = "";
		}
	}

	document.we_form.ID.value = "0";
	weSelect.removeOptions("WorkspaceID");
	removeAllCats();
}

function removeAllCats() {

	if (categories_edit.itemCount > 0) {
		while (categories_edit.itemCount > 0) {
			categories_edit.delItem(categories_edit.itemCount);
		}
	}
	document.we_form.CategoriesCount.value = categories_edit.itemCount;
}

function addCat(paths, ids) {
	for (var i = 0; i < paths.length; i++) {
		if (paths[i] !== "") {
			categories_edit.addItem();
			//FIXME: ids will not be used, since this js function only has 3 parameters!
			categories_edit.setItem(0, (categories_edit.itemCount - 1), paths[i], ids[i]);
		}
	}
	categories_edit.showVariant(0);
	document.we_form.CategoriesCount.value = categories_edit.itemCount;
}


function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "switchType":
			switchType(args[1]);
			break;
		case "new_navigation_rule":
			clearNavigationForm();
			break;
		case "save_navigation_rule":
			var isValid = true;
			if (document.we_form.SelectionType.options[0].selected === true) {
				isValid = WE().layout.weSuggest.checkRequired(window, "yuiAcInputFolderIDPath").valid;
			} else if (document.we_form.SelectionType.options[1] !== undefined && document.we_form.SelectionType.options[1].selected === true) {
				isValid = WE().layout.weSuggest.checkRequired(window, "yuiAcInputClassIDPath").valid;
			}
			if (isValid && WE().layout.weSuggest.checkRequired(window, "yuiAcInputNavigationIDPath").valid) {
				weInput.setValue("cmd", "save_navigation_rule");
				document.we_form.submit();
			} else {
				top.we_showMessage(WE().consts.g_l.navigation.rule.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, this);
				return false;
			}
			break;
		case "delete_navigation_rule":
			if ((navId = document.we_form.navigationRules.value)) {
				document.we_form.NavigationName.value = "";
				weInput.setValue("cmd", "delete_navigation_rule");
				weInput.setValue("ID", navId);
				document.we_form.submit();
			}
			break;
		case "navigation_edit_rule":
			weInput.setValue("cmd", "navigation_edit_rule");
			weInput.setValue("ID", args[1]);
			document.we_form.submit();
			break;
		case "get_workspaces":
			weInput.setValue("cmd", "get_workspaces");
			document.we_form.submit();
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url, "we_selector_directory", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(window, url, "we_selector_category", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(window, url, "we_selector_file", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(window, url, args[0], WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
	}
}