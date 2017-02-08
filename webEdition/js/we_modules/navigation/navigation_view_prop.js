/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
'use strict';

var loaded = false;
var table = WE().consts.tables.FILE_TABLE;
var props = WE().util.getDynamicVar(document, 'loadVarViewProp', 'data-prop');


function setFieldValue(fieldNameTo, fieldFrom) {
	if (document.we_form.DynamicSelection.value === "doctype" && (fieldNameTo === "TitleField" || fieldNameTo === "SorrtField")) {
		document.we_form[fieldNameTo].value = fieldFrom.value;
		fieldFrom.classList.remove('weMarkInputError');
	} else if (props.weNavTitleField[fieldFrom.value] !== undefined) {
		document.we_form[fieldNameTo].value = props.weNavTitleField[fieldFrom.value];
		fieldFrom.classList.remove('weMarkInputError');
	} else if (fieldFrom.value === "") {
		document.we_form[fieldNameTo].value = '';
		fieldFrom.classList.remove("weMarkInputError");
	} else {
		fieldFrom.classList.add("weMarkInputError");
	}
}

function toggle(id) {
	var elem = document.getElementById(id);
	if (elem) {
		elem.style.display = (elem.style.display == "none" ? "block" : "none");
	}
}

function setVisible(id, visible) {
	var elem = document.getElementById(id);
	if (elem) {
		elem.style.display = (visible === true ? "block" : "none");
	}
}

function populateVars() {
	if (window.categories_edit !== undefined && document.we_form.CategoriesCount !== undefined) {
		document.we_form.CategoriesCount.value = categories_edit.itemCount;
	}
	if (window.sort_edit !== undefined && document.we_form.SortCount !== undefined) {
		document.we_form.SortCount.value = sort_edit.itemCount;
	}
	if (window.specificCustomersEdit !== undefined && document.we_form.specificCustomersEditCount !== undefined) {
		document.we_form.specificCustomersEditCount.value = specificCustomersEdit.itemCount;
	}
	if (window.blackListEdit !== undefined && document.we_form.blackListEditCount !== undefined) {
		document.we_form.blackListEditCount.value = blackListEdit.itemCount;
	}
	if (window.whiteListEdit !== undefined && document.we_form.whiteListEditCount !== undefined) {
		document.we_form.whiteListEditCount.value = whiteListEdit.itemCount;
	}
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args),
		folderPath, folderID;

	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(caller, url, "we_docselector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.medium, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_selector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(caller, url, "we_catselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "we_navigation_dirSelector":
			new (WE().util.jsWindow)(caller, url, "we_navigation_dirselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "openFieldSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=fields&cmd=" + args[1] + "&type=" + args[2] + "&selection=" + args[3] + "&multi=" + args[4];
			new (WE().util.jsWindow)(caller, url, "we_navigation_field_selector", WE().consts.size.dialog.smaller, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "copyNaviFolder":
			folderPath = document.we_form.CopyFolderPath.value;
			folderID = document.we_form.CopyFolderID.value;
			window.setTimeout(copyNaviFolder, 100, folderPath, folderID);
			break;
		case "rebuildNavi":
			//new (WE().util.jsWindow)(caller, WE().consts.dirs.WE_INCLUDES_DIR+"we_cmd.php?we_cmd[0]=rebuild&step=2&type=rebuild_navigation&responseText=\',\'resave\',WE().consts.size.dialog.small,WE().consts.size.dialog.tiny,0,true);
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));

	}
}

function copyNaviFolder(folderPath, folderID) {
	var parentPos = props.selfNaviPath.indexOf(folderPath);
	if (parentPos === -1 || props.selfNaviPath.indexOf(folderPath) > 0) {
		var cnfUrl = WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=CopyNavigationFolder&cns=navigation&we_cmd[0]=" + props.selfNaviPath + "&we_cmd[1]=" + props.selfNaviId + "&we_cmd[2]=" + folderPath + "&we_cmd[3]=" + folderID;

		WE().util.rpc(cnfUrl, null, function (weResponse) {
			var folders = weResponse.DataArray.folders;
			if (folders !== "") {
				WE().util.showMessage(WE().consts.g_l.main.folder_copy_success, WE().consts.message.WE_MESSAGE_NOTICE, window);
				//FIXME: add code for Tree reload!
				top.content.cmd.location.reload();
			} else {
				WE().util.showMessage(WE().consts.g_l.alert.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			}
		}).fail(function (jqxhr, textStatus, error) {
			WE().util.showMessage(WE().consts.g_l.alert.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		});
	} else {
		WE().util.showMessage(WE().consts.g_l.alert.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}


function closeAllSelection() {
	setVisible(WE().consts.navigation.SELECTION_DYNAMIC, false);
	setVisible(WE().consts.navigation.SELECTION_STATIC, false);
}

function closeAllType() {
	setVisible("doctype", false);
	if (WE().consts.tables.OBJECT_TABLE !== "OBJECT_TABLE") {
		setVisible("classname", false);
	}
}

function closeAllStats() {
	setVisible("docLink", false);
	if (WE().consts.tables.OBJECT_TABLE !== "OBJECT_TABLE") {
		setVisible("objLink", false);
		setVisible("objLinkWorkspace", false);
	}
	setVisible("catLink", false);
	document.we_form.LinkID.value = "";
	document.we_form.LinkPath.value = "";
}

function putTitleField(field) {
	we_cmd("setHot");
	document.we_form.TitleField.value = field;
	document.we_form.__TitleField.value = document.we_form.DynamicSelection.value === "doctype" ? field : field.substring(field.indexOf("_") + 1, field.length);
	document.we_form.__TitleField.classList.remove('weMarkInputError');
}

function putSortField(field) {
	we_cmd("setHot");
	document.we_form.SortField.value = field;
	document.we_form.__SortField.value = document.we_form.DynamicSelection.value === "doctype" ? field : field.substring(field.indexOf("_") + 1, field.length);
	document.we_form.__SortField.classList.remove('weMarkInputError');
}

function setFocus() {
	if (document.we_form.Text !== undefined && top.content.activ_tab == 1) {
		document.we_form.Text.focus();
	}
}

function setWorkspaces(value) {
	setVisible("objLinkWorkspaceClass", false);
	setVisible("objLinkWorkspace", false);
	switch (value) {
		case WE().consts.navigation.DYN_CLASS:
			setVisible("objLinkWorkspaceClass", true);
			break;
		case WE().consts.navigation.STYPE_OBJLINK:
			setVisible("objLinkWorkspace", true);
			break;
	}
}

function setStaticSelection(value) {
	switch (value) {
		case WE().consts.navigation.DYN_CATEGORY:
			setVisible("dynUrl", true);
			setVisible("dynamic_LinkSelectionDiv", true);
			setLinkSelection("dynamic_", WE().consts.navigation.LSELECTION_INTERN);
			break;
		case WE().consts.navigation.STYPE_CATLINK:
			setVisible("dynUrl", false);
			setVisible("staticSelect", true);
			setVisible("staticUrl", true);
			setVisible("docLink", false);
			setVisible("objLink", false);
			setVisible("catLink", false);
			setVisible(value, true);
			setVisible("LinkSelectionDiv", true);
			setLinkSelection("", WE().consts.navigation.LSELECTION_INTERN);
			break;
		case WE().consts.navigation.STYPE_URLLINK:
			setVisible("dynUrl", false);
			setVisible("staticSelect", false);
			setVisible("staticUrl", true);
			setVisible("LinkSelectionDiv", false);
			setLinkSelection("", WE().consts.navigation.LSELECTION_EXTERN);
			break;
		case WE().consts.navigation.STYPE_DOCLINK:
		case WE().consts.navigation.STYPE_OBJLINK:
			setVisible("dynUrl", false);
			setVisible("staticSelect", true);
			setVisible("staticUrl", false);
			setVisible("docLink", false);
			setVisible("objLink", false);
			setVisible("catLink", false);
			setVisible(value, true);
			break;
		default:
			//does anything match here???
			setVisible("dynUrl", false);
			setVisible("staticSelect", true);
			setVisible("staticUrl", false);

	}
}

function setFolderSelection(value) {
	document.we_form.LinkID.value = "";
	document.we_form.LinkPath.value = "";
	document.we_form.Url.value = "http://";
	document.we_form.WorkspaceID.value = -1;
	switch (value) {
		case WE().consts.navigation.STYPE_URLLINK:
			setVisible("folderSelectionDiv", false);
			setVisible("docFolderLink", false);
			setVisible("objFolderLink", false);
			setVisible("objLinkFolderWorkspace", false);
			setVisible("folderUrlDiv", true);
			break;
		case WE().consts.navigation.STYPE_DOCLINK:
			setVisible("folderSelectionDiv", true);
			setVisible("docFolderLink", true);
			setVisible("objFolderLink", false);
			setVisible("objLinkFolderWorkspace", false);
			setVisible("folderUrlDiv", false);
			break;
		default:
			setVisible("folderSelectionDiv", true);
			setVisible("docFolderLink", false);
			setVisible("objFolderLink", true);
			setVisible("objLinkFolderWorkspace", true);
			setVisible("folderUrlDiv", false);
	}
}

function submitForm(target, action, method) {
	var f = window.document.we_form;
	populateVars();
	f.target = (target ? target : "edbody");
	f.action = (action ? action : WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation");
	f.method = (method ? method : "post");
	f.submit();
}

function clearFields() {
	we_cmd("setHot");
	var st = document.we_form.DynamicSelection;
	if (st.selectedIndex > -1) {
		removeAllCats();
		WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "enabled");
		WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_SortField", "enabled");
		if (st.options[st.selectedIndex].value === WE().consts.navigation.DYN_CLASS && document.we_form.ClassID.options.length < 1) {
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "disabled");
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_XFolder", "disabled");
			document.getElementById("yuiAcInputFolderPath").disabled = true;
		} else {
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_XFolder", "enabled");
			document.getElementById("yuiAcInputFolderPath").disabled = false;
		}
		switch (st.options[st.selectedIndex].value) {
			case WE().consts.navigation.DYN_DOCTYPE:
				setVisible("docFolder", true);
				setVisible("objFolder", false);
				setVisible("catFolder", false);
				if (top.content.editor.edbody.document.we_form.DocTypeID.options[top.content.editor.edbody.document.we_form.DocTypeID.selectedIndex].value === 0) {
					WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "disabled");
					WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_SortField", "disabled");
				}
				break;
			case WE().consts.navigation.DYN_CLASS:
				setVisible("docFolder", false);
				setVisible("objFolder", true);
				setVisible("catFolder", false);
		}
		if (!props.IsFolder) {
			document.we_form.LinkID.value = "";
			document.we_form.LinkPath.value = "";
		}

		document.we_form.FolderID.value = 0;
		document.we_form.FolderPath.value = "/";
		document.we_form.TitleField.value = "";
		document.we_form.__TitleField.value = "";
		document.we_form.SortField.value = "";
		document.we_form.__SortField.value = "";
		document.we_form.__TitleField.classList.remove('weMarkInputError');
		document.we_form.__SortField.classList.remove('weMarkInputError');
		document.we_form.dynamic_Parameter.value = "";
		if (document.we_form.IsFolder.value === 0) {
			document.we_form.Parameter.value = "";
			document.we_form.Url.value = "http://";
		}

		if (st.options[st.selectedIndex].value == WE().consts.navigation.DYN_CATEGORY) {
			setVisible("docFolder", false);
			setVisible("objFolder", false);
			setVisible("catFolder", true);
			setVisible("fieldChooser", false);
			setVisible("catSort", false);
		} else {
			setVisible("fieldChooser", true);
			setVisible("catSort", true);
		}
	}
}

function setCustomerFilter(sel) {
	if (props.IsFolder) {
		return;
	}
	if (sel.options[sel.selectedIndex].value == "dynamic") {
		try {//FIXME
			document.we_form.elements._wecf_useDocumentFilter.checked = false;
			document.we_form.elements.wecf_useDocumentFilter.value = 0;
			document.we_form.elements._wecf_useDocumentFilter.disabled = true;
			document.getElementById("label__wecf_useDocumentFilter").style.color = "grey";
		} catch (e) {
		}
		document.getElementById("MainFilterDiv").style.display = "block";
	} else {
		try {//FIXME
			document.we_form.elements._wecf_useDocumentFilter.disabled = false;
			document.getElementById("label__wecf_useDocumentFilter").style.color = "";
		} catch (e) {
		}
	}
}
