/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 10631 $
 * $Author: mokraemer $
 * $Date: 2015-10-20 08:22:44 +0200 (Di, 20. Okt 2015) $
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
		if (elem = document.getElementById("tr" + allFields[i])) {
			elem.style.display = "none";
		}
	}

	// show needed
	if (dependencies[value]) {

		for (j = 0; j < dependencies[value].length; j++) {
			if (elem = document.getElementById("tr" + dependencies[value][j])) {
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

	document.we_form["ID"].value = "0";
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

	var path = paths.split(",");
	var id = ids.split(",");
	for (var i = 0; i < path.length; i++) {
		if (path[i] != "") {
			categories_edit.addItem();
			categories_edit.setItem(0, (categories_edit.itemCount - 1), path[i], id[i]);
		}
	}
	categories_edit.showVariant(0);
	document.we_form.CategoriesCount.value = categories_edit.itemCount;
}


function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);
	var arguments = args;

	switch (args[0]) {
		case "switchType":
			switchType(args[1]);
			break;
		case "new_navigation_rule":
			clearNavigationForm();
			break;
		case "save_navigation_rule":
			var isValid = 1;
			if (document.we_form.SelectionType.options[0].selected == true) {
				isValid = YAHOO.autocoml.isValidById("yuiAcInputFolderIDPath");
			} else if (document.we_form.SelectionType.options[1] !== undefined && document.we_form.SelectionType.options[1].selected == true) {
				isValid = YAHOO.autocoml.isValidById("yuiAcInputClassIDPath");
			}
			if (isValid && YAHOO.autocoml.isValidById("yuiAcInputNavigationIDPath")) {
				weInput.setValue("cmd", "save_navigation_rule");
				document.we_form.submit();
			} else {
				top.we_showMessage(WE().consts.g_l.navigation.rule.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, this);
				return false;
			}
			break;
		case "delete_navigation_rule":
			if (navId = document.we_form["navigationRules"].value) {
				document.we_form["NavigationName"].value = "";
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
			new (WE().util.jsWindow)(this, url, args[0], -1, -1, WE().consts.size.windowDirSelect.width, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(this, url, args[0], -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(this, url, args[0], -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(this, url, args[0], -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;
	}
}