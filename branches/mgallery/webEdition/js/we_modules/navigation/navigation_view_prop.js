/* global WE, top, YAHOO, data */

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

var loaded = false;
var table = WE().consts.tables.FILE_TABLE;

function setFieldValue(fieldNameTo, fieldFrom) {
	if (document.we_form.SelectionType.value === "doctype" && (fieldNameTo === "TitleField" || fieldNameTo === "SorrtField")) {
		document.we_form[fieldNameTo].value = fieldFrom.value;
		weInputRemoveClass(fieldFrom, "weMarkInputError");
	} else if (weNavTitleField[fieldFrom.value] !== undefined) {
		document.we_form[fieldNameTo].value = weNavTitleField[fieldFrom.value];
		weInputRemoveClass(fieldFrom, "weMarkInputError");
	} else if (fieldFrom.value === "") {
		document.we_form[fieldNameTo].value = '';
		weInputRemoveClass(fieldFrom, "weMarkInputError");
	} else {
		weInputAppendClass(fieldFrom, "weMarkInputError");
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
		elem.style.display = (visible == true ? "block" : "none");
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
	var args = [];
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		args.push(arguments[i]);
		url += "we_cmd[]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (args[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(window, url, "we_docselector", -1, -1, WE().consts.size.docSelect.width, WE().consts.size.docSelect.height, true, true, true, true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(window, url, "we_selector", -1, -1, WE().consts.size.windowSelect.width, WE().consts.size.windowSelect.height, true, true, true, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(window, url, "we_selector", -1, -1, WE().consts.size.windowDirSelect.width, WE().consts.size.windowDirSelect.height, true, true, true, true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(window, url, "we_catselector", -1, -1, WE().consts.size.catSelect.width, WE().consts.size.catSelect.height, true, true, true, true);
			break;
		case "openNavigationDirselector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?";
			args[0] = "we_navigation_dirSelector";
			for (var i = 0; i < args.length; i++) {
				url += "we_cmd[]=" + encodeURI(args[i]);
				if (i < (args.length - 1)) {
					url += "&";
				}
			}
			new (WE().util.jsWindow)(window, url, "we_navigation_dirselector", -1, -1, 600, 400, true, true, true);
			break;
		case "openFieldSelector":
			url = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=fields&cmd=" + args[1] + "&type=" + args[2] + "&selection=" + args[3] + "&multi=" + args[4];
			new (WE().util.jsWindow)(window, url, "we_navigation_field_selector", -1, -1, 380, 350, true, true, true);
			break;
		case "copyNaviFolder":
			folderPath = document.we_form.CopyFolderPath.value;
			folderID = document.we_form.CopyFolderID.value;
			setTimeout(function () {
				copyNaviFolder(folderPath, folderID);
			}, 100);
			break;
		case "rebuildNavi":
			//new (WE().util.jsWindow)(window, WE().consts.dirs.WE_INCLUDES_DIR+"we_cmd.php?we_cmd[0]=rebuild&step=2&type=rebuild_navigation&responseText=\',\'resave\',-1,-1,600,130,0,true);
			break;
		default:
			top.content.we_cmd.apply(this, args);

	}
}


var copyNaviFolderUrl = WE().consts.dirs.WEBEDITION_DIR + "rpc/rpc.php";
function copyNaviFolder(folderPath, folderID) {
	var parentPos = selfNaviPath.indexOf(folderPath);
	if (parentPos === -1 || selfNaviPath.indexOf(folderPath) > 0) {
		cnfUrl = copyNaviFolderUrl + "?protocol=text&cmd=CopyNavigationFolder&cns=navigation&we_cmd[0]=" + selfNaviPath + "&we_cmd[1]=" + selfNaviId + "&we_cmd[2]=" + folderPath + "&we_cmd[3]=" + folderID;
		YAHOO.util.Connect.asyncRequest("GET", cnfUrl, copyNaviFolderAjaxCallback);
	} else {
		WE().util.showMessage(WE().consts.g_l.main.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
	}
}

var copyNaviFolderAjaxCallback = {
	success: function (o) {
		if (o.responseText !== "") {
			WE().util.showMessage(WE().consts.g_l.main.folder_copy_success, WE().consts.message.WE_MESSAGE_NOTICE, window);
			//FIXME: add code for Tree reload!
			top.content.cmd.location.reload();
		} else {
			WE().util.showMessage(WE().consts.g_l.main.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		}
	},
	failure: function (o) {
		WE().util.showMessage(WE().consts.g_l.main.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
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
	top.content.mark();
	document.we_form.TitleField.value = field;
	document.we_form.__TitleField.value = document.we_form.SelectionType.value === "doctype" ? field : field.substring(field.indexOf("_") + 1, field.length);
	weInputRemoveClass(document.we_form.__TitleField, "weMarkInputError");
}

function putSortField(field) {
	top.content.mark();
	document.we_form.SortField.value = field;
	document.we_form.__SortField.value = document.we_form.SelectionType.value === "doctype" ? field : field.substring(field.indexOf("_") + 1, field.length);
	weInputRemoveClass(document.we_form.__SortField, "weMarkInputError");
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
		case WE().consts.navigation.STYPE_CLASS:
			setVisible("objLinkWorkspaceClass", true);
			break;
		case WE().consts.navigation.STYPE_OBJLINK:
			setVisible("objLinkWorkspace", true);
			break;
	}
}

function setStaticSelection(value) {
	switch (value) {
		case WE().consts.navigation.STYPE_CATEGORY:
			setVisible("dynUrl", true);
			setVisible("dynamic_LinkSelectionDiv", true);
			dynamic_setLinkSelection(WE().consts.navigation.LSELECTION_INTERN);
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
			setLinkSelection(WE().consts.navigation.LSELECTION_INTERN);
			break;
		case WE().consts.navigation.STYPE_URLLINK:
			setVisible("dynUrl", false);
			setVisible("staticSelect", false);
			setVisible("staticUrl", true);
			setVisible("LinkSelectionDiv", false);
			setLinkSelection(WE().consts.navigation.LSELECTION_EXTERN);
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
	document.we_form.FolderUrl.value = "http://";
	document.we_form.FolderWsID.value = -1;
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

function setPresentation(type) {
	top.content.mark();
	var st = document.we_form.SelectionType;
	st.options.length = 0;
	switch (type) {
		case WE().consts.navigation.SELECTION_DYNAMIC:
			st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.documents, WE().consts.navigation.STYPE_DOCTYPE);
			if (WE().consts.tables.OBJECT_TABLE !== "OBJECT_TABLE") {
				st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.objects, WE().consts.navigation.STYPE_CLASS);
			}
			st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.categories, WE().consts.navigation.STYPE_CATEGORY);
			setVisible("doctype", true);
			setVisible("classname", false);
			setVisible("docFolder", true);
			setVisible("objFolder", false);
			setVisible("catFolder", false);
			setStaticSelection("document");
			break;
		default:
			st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.docLink, WE().consts.navigation.STYPE_DOCLINK);
			st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.urlLink, WE().consts.navigation.STYPE_URLLINK);
			if (WE().consts.tables.OBJECT_TABLE !== "OBJECT_TABLE") {
				st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.objLink, WE().consts.navigation.STYPE_OBJLINK);
			}
			st.options[st.options.length] = new Option(WE().consts.g_l.navigation.view.catLink, WE().consts.navigation.STYPE_CATLINK);
			setVisible("classname", true);
			setVisible("doctype", false);
			setVisible("docFolder", false);
			setVisible("objFolder", true);
			setVisible("catFolder", true);
			setVisible("docLink", true);
			setStaticSelection("docLink");
	}
	clearFields();
}


function submitForm() {
	var f = self.document.we_form;
	populateVars();
	f.target = (arguments[0] ? arguments[0] : "edbody");
	f.action = (arguments[1] ? arguments[1] : data.frameset);
	f.method = (arguments[2] ? arguments[2] : "post");
	f.submit();
}

function clearFields() {
	top.content.mark();
	var st = document.we_form.SelectionType;
	if (st.selectedIndex > -1) {
		removeAllCats();
		WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "enabled");
		WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_SortField", "enabled");
		if (st.options[st.selectedIndex].value === WE().consts.navigation.STYPE_CLASS && document.we_form.ClassID.options.length < 1) {
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "disabled");
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_XFolder", "disabled");
			document.getElementById("yuiAcInputFolderPath").disabled = true;
		} else {
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_XFolder", "enabled");
			document.getElementById("yuiAcInputFolderPath").disabled = false;
		}
		switch (st.options[st.selectedIndex].value) {
			case WE().consts.navigation.STYPE_DOCTYPE:
				setVisible("docFolder", true);
				setVisible("objFolder", false);
				setVisible("catFolder", false);
				if (top.content.editor.edbody.document.we_form.DocTypeID.options[top.content.editor.edbody.document.we_form.DocTypeID.selectedIndex].value === 0) {
					WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "disabled");
					WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_SortField", "disabled");
				}
				break;
			case WE().consts.navigation.STYPE_CLASS:
				setVisible("docFolder", false);
				setVisible("objFolder", true);
				setVisible("catFolder", false);
		}
		if (!data.IsFolder) {
			document.we_form.LinkID.value = "";
			document.we_form.LinkPath.value = "";
		}

		document.we_form.FolderID.value = 0;
		document.we_form.FolderPath.value = "/";
		document.we_form.TitleField.value = "";
		document.we_form.__TitleField.value = "";
		document.we_form.SortField.value = "";
		document.we_form.__SortField.value = "";
		weInputRemoveClass(document.we_form.__TitleField, "weMarkInputError");
		weInputRemoveClass(document.we_form.__SortField, "weMarkInputError");
		document.we_form.dynamic_Parameter.value = "";
		if (document.we_form.IsFolder.value == 0) {
			document.we_form.Parameter.value = "";
			document.we_form.Url.value = "http://";
		}

		if (st.options[st.selectedIndex].value == WE().consts.navigation.STYPE_CATEGORY) {
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
	if (data.IsFolder) {
		return;
	}
	var st = document.we_form.SelectionType;
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
