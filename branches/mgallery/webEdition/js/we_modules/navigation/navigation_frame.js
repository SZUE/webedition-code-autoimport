/* global top, WE */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 11050 $
 * $Author: mokraemer $
 * $Date: 2016-01-03 04:03:49 +0100 (So, 03. Jan 2016) $
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

function onSelectionClassChangeJS(value) {
	YAHOO.autocoml.modifySetById("yuiAcInputFolderPath", {
		table: "trunk_tblObjectFiles",
		rootDir: classPaths[value],
		mayBeEmpty: false
	}
	);
	document.we_form.elements.FolderID.value = classDirs[value];
	document.we_form.elements.FolderPath.value = classPaths[value];
	document.we_form.elements.FolderPath.disabled = !hasClassSubDirs[value];
	top.content.we_cmd('populateWorkspaces');
	top.content.mark();
}


function onSelectionTypeChangeJS(value) {
	if (document.we_form.elements.Selection.value === WE().consts.navigation.SELECTION_STATIC) {
		onFolderSelectionChangeJS(value);
	} else {
		if ((WE().consts.tables.OBJECT_FILES_TABLE !== "OBJECT_FILES_TABLE") && value === WE().consts.navigation.STYPE_CLASS) {
			document.we_form.elements.ClassID.selectedIndex = 0;
			onSelectionClassChangeJS(document.we_form.elements.ClassID.options[0].value);
		} else {
			YAHOO.autocoml.modifySetById("yuiAcInputFolderPath", {
				table: value === WE().consts.navigation.STYPE_DOCTYPE ? WE().consts.tables.FILE_TABLE : WE().consts.tables.CATEGORY_TABLE,
				rootDir: "",
				mayBeEmpty: value === WE().consts.navigation.STYPE_DOCTYPE
			}
			);
		}
		YAHOO.autocoml.setValidById("yuiAcInputFolderPath");
	}
}

function onFolderSelectionChangeJS(value) {
	var linktype = value === WE().consts.navigation.STYPE_DOCLINK ? WE().consts.navigation.STYPE_DOCLINK : (value === WE().consts.navigation.STYPE_CATLINK ? WE().consts.navigation.STYPE_CATLINK : (value === WE().consts.navigation.STYPE_OBJLINK ? WE().consts.navigation.STYPE_OBJLINK : WE().consts.navigation.STYPE_DOCLINK));
	YAHOO.autocoml.modifySetById("yuiAcInputLinkPath", {
		table: linktype === WE().consts.navigation.STYPE_DOCLINK ? WE().consts.tables.FILE_TABLE : (linktype === WE().consts.navigation.STYPE_OBJLINK ? WE().consts.tables.OBJECT_FILES_TABLE : (linktype === WE().consts.navigation.STYPE_CATLINK ? WE().consts.tables.CATEGORY_TABLE : "")),
		cTypes: linktype === WE().consts.navigation.STYPE_DOCLINK ? [WE().consts.contentTypes.FOLDER, WE().consts.contentTypes.XML, WE().consts.contentTypes.WEDOCUMENT, WE().consts.contentTypes.IMAGE, WE().consts.contentTypes.HTML, WE().consts.contentTypes.APPLICATION, WE().consts.contentTypes.FLASH, WE().consts.contentTypes.QUICKTIME].join(",") : (linktype === WE().consts.navigation.STYPE_OBJLINK ? [WE().consts.contentTypes.FOLDER, WE().consts.contentTypes.OBJECT_FILE].join(",") : "")
	}
	);
}

function openToEdit(tab, id, contentType) {
	if (id > 0) {
		WE().layout.weEditorFrameController.openDocument(tab, id, contentType);
	}
}

function removeAllCats() {
	top.content.mark();
	if (categories_edit.itemCount > 0) {
		while (categories_edit.itemCount > 0) {
			categories_edit.delItem(categories_edit.itemCount);
		}
	}
}

function addCat(paths) {
	top.content.mark();
	var path = paths.split(",");
	var found = false;
	var j = 0;
	for (var i = 0; i < path.length; i++) {
		if (path[i] !== "") {
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