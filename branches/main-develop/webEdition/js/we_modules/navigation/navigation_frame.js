/* global top, WE, YAHOO */

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

var categories_edit;

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
		return;
	}
	switch (value) {
		case WE().consts.navigation.DYN_CLASS:
			if ((WE().consts.tables.OBJECT_FILES_TABLE !== "OBJECT_FILES_TABLE")) {
				document.we_form.elements.ClassID.selectedIndex = 0;
				onSelectionClassChangeJS(document.we_form.elements.ClassID.options[0].value);
			}
			break;
		default:
			YAHOO.autocoml.modifySetById("yuiAcInputFolderPath", {
				table: value === WE().consts.navigation.DYN_DOCTYPE ? WE().consts.tables.FILE_TABLE : WE().consts.tables.CATEGORY_TABLE,
				rootDir: "",
				mayBeEmpty: value === WE().consts.navigation.DYN_DOCTYPE
			}
			);
	}
	YAHOO.autocoml.setValidById("yuiAcInputFolderPath");
}

function onFolderSelectionChangeJS(value) {
	var linktype = '';
	switch (value) {
		case WE().consts.navigation.STYPE_DOCLINK:
		case WE().consts.navigation.STYPE_OBJLINK:
		case WE().consts.navigation.STYPE_CATLINK:
			linktype = value;
			break;
		default:
			linktype = WE().consts.navigation.STYPE_DOCLINK;
	}

	var set = {};
	switch (linktype) {
		case WE().consts.navigation.STYPE_DOCLINK:
			set = {
				table: WE().consts.tables.FILE_TABLE,
				cTypes: [
					WE().consts.contentTypes.FOLDER, WE().consts.contentTypes.XML,
					WE().consts.contentTypes.WEDOCUMENT, WE().consts.contentTypes.IMAGE,
					WE().consts.contentTypes.HTML, WE().consts.contentTypes.APPLICATION,
					WE().consts.contentTypes.FLASH
				].join(",")
			};
			break;
		case WE().consts.navigation.STYPE_OBJLINK:
			set = {
				table: WE().consts.tables.OBJECT_FILES_TABLE,
				cTypes: [WE().consts.contentTypes.FOLDER,
					WE().consts.contentTypes.OBJECT_FILE].join(",")
			};
			break;
		case WE().consts.navigation.STYPE_CATLINK:
			set = {
				table: WE().consts.tables.CATEGORY_TABLE,
				cTypes: ''
			};
			break;
	}


	YAHOO.autocoml.modifySetById("yuiAcInputLinkPath", set);
}

function fieldChooserBut(cmd) {
	var st = document.we_form.SelectionType.options[document.we_form.SelectionType.selectedIndex].value;
	var s = (st === WE().consts.navigation.DYN_DOCTYPE ? document.we_form.DocTypeID.options[document.we_form.DocTypeID.selectedIndex].value : document.we_form.ClassID.options[document.we_form.ClassID.selectedIndex].value);
	we_cmd('openFieldSelector', cmd, st, s, 0);
}

function categoriesEdit(size, elements, delBut) {
	categories_edit = new (WE().util.multi_edit)("categories", window, 0, delBut, size, false);
	categories_edit.addVariant();
	document.we_form.CategoriesControl.value = categories_edit.name;
	for (var i = 0; i < elements.length; i++) {
		categories_edit.addItem();
		categories_edit.setItem(0, (categories_edit.itemCount - 1), elements[i]);
	}
	categories_edit.showVariant(0);
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
	var found = false;
	var j = 0;
	for (var i = 0; i < paths.length; i++) {
		if (paths[i] !== "") {
			found = false;
			for (j = 0; j < categories_edit.itemCount; j++) {
				if (categories_edit.form.elements[categories_edit.name + "_variant0_" + categories_edit.name + "_item" + j].value == paths[i]) {
					found = true;
				}
			}
			if (!found) {
				categories_edit.addItem();
				categories_edit.setItem(0, (categories_edit.itemCount - 1), paths[i]);
			}
		}
	}
	categories_edit.showVariant(0);
}

function setLinkSelection(prefix, value) {
	setVisible(prefix + "intern", (value === WE().consts.navigation.LSELECTION_INTERN));
	setVisible(prefix + "extern", (value !== WE().consts.navigation.LSELECTION_INTERN));
}

function setFields(cmd) {
	var list = document.we_form.fields.options;

	var fields = [];
	for (i = 0; i < list.length; i++) {
		if (list[i].selected) {
			fields.push(list[i].value);
		}
	}
	opener[cmd](fields.join(","));
	window.close();
}

function selectItem() {
	if (document.we_form.fields.selectedIndex > -1) {
		WE().layout.button.switch_button_state(document, "save", "enabled");
	}
}

function mark() {
	var elem = document.getElementById("mark");
	elem.style.display = "inline";
}

function unmark() {
	var elem = document.getElementById("mark");
	elem.style.display = "none";
}

function initNavHeader() {
	weTabs.setFrameSize();
	document.getElementById('tab_' + top.content.activ_tab).className = 'tabActive';
}