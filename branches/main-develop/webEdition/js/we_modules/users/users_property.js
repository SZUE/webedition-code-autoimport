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
var loaded = false,
	TAB_DATA = 0,
	TAB_PERMISSION = 1,
	TAB_WORKSPACES = 2,
	TAB_SETTINGS = 3,
	multiEditorreload = false;

function we_submitForm(target, url) {
	var f = window.document.we_form;

	var ok = true;

	if (f.input_pass) {
		if (f.oldtab.value === "0") {
			if (f.input_pass.value.length < 4 && f.input_pass.value.length !== 0) {
				WE().util.showMessage(WE().consts.g_l.navigation.users.password_alert, WE().consts.message.WE_MESSAGE_ERROR, window);
				return false;
			}
			if (f.input_pass.value !== "") {
				var clearPass = f.input_pass.value;
				f.input_pass.value = "";
				f[f.obj_name.value + "_clearpasswd"].value = clearPass;
			}
		}
	}

	if (ok) {
		if (!f.checkValidity()) {
			top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
		return true;
	}
	return false;
}

var activeTab = TAB_DATA;
function setTab(tab) {
	switch (tab) {
		case TAB_DATA:
			top.content.editor.edbody.switchPage(tab);
			break;
		case TAB_PERMISSION:
		case TAB_WORKSPACES:
		case TAB_SETTINGS:
			if (!top.content.editor.edbody.switchPage(tab)) {
				window.setTimeout(resetTabs, 50);
			}
			break;
	}
	activeTab = tab;
}

function resetTabs() {
	top.content.editor.edbody.document.we_form.tab.value = TAB_DATA;
	top.content.editor.edheader.weTabs.setActiveTab(TAB_DATA);
}

function switchPage(page) {
	document.we_form.tab.value = page;
	return we_submitForm(window.name, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=users&pnt=edbody");
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "we_users_selector":
			new (WE().util.jsWindow)(caller, url, "browse_users", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, false, true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(caller, url, "we_fileselector", WE().consts.size.dialog.big, WE().consts.size.dialog.small, true, true, true, true);
			break;
		case "select_seem_start":
			var myWind = WE().util.jsWindow.prototype.find('preferences');
			var myWindStr = "WE().util.jsWindow.prototype.find(\'preferences\')";
			top.opener.top.we_cmd("we_selector_document", myWind.document.forms[0].elements.seem_start_file.value, WE().consts.tables.FILE_TABLE, myWindStr + ".document.forms[0].elements.seem_start_file.value", myWindStr + ".document.forms[0].elements.seem_start_file_name.value", "", "", "", WE().consts.contentTypes.WEDOCUMENT, 1);
			break;
		case "we_navigation_dirSelector":
		case "we_newsletter_dirSelector":
			new (WE().util.jsWindow)(caller, url, "we_navigation_dirselector", WE().consts.size.dialog.small, WE().consts.size.dialog.smaller, true, true, true);
			break;
		case "showParentPerms":
			showParentPerms(args[1]);
			break;
		case "show_seem_chooser":
			show_seem_chooser(args[1]);
			break;
		case "rememberPreference":
			rememberPreference(args[1], args[2]);
			break;
		default:
			top.content.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function rememberPreference(pref, spec) {
	var frameId, _usedEditors;
	switch (pref) {
		case 'Language':
		case 'BackendCharset':
			if (top.frames[0]) {
				top.frames[0].location.reload();
			}

			if (parent.frames[0]) {
				parent.frames[0].location.reload();
			}

// Tabs Module User
			if (top.content.editor.edheader) {
				top.content.editor.edheader.location = top.content.editor.edheader.location + '?tab=' + top.content.editor.edheader.activeTab;
			}

// Editor Module User
			if (top.content.editor.edbody) {
				top.content.editor.edbody.location = top.content.editor.edbody.location + '?tab=' + spec + '&perm_branch=' + top.content.editor.edbody.opened_group;
			}

// Save Module User
			if (top.content.editor.edfooter) {
				top.content.editor.edfooter.location.reload();
			}
			if (top.opener.top.header) {
				top.opener.top.header.location.reload();
			}

// reload all frames of an editor
// reload current document => reload all open Editors on demand
			_usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
			for (frameId in _usedEditors) {

				if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);

				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			multiEditorreload = true;
			return;
		case 'reload':
			if (multiEditorreload) {
				return;
			}
			_usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();

			for (frameId in _usedEditors) {

				if ((_usedEditors[frameId].getEditorEditorTable() === WE().consts.tables.TEMPLATES_TABLE || _usedEditors[frameId].getEditorEditorTable() === WE().consts.tables.OBJECT_FILES_TABLE || _usedEditors[frameId].getEditorEditorTable() === WE().consts.tables.FILE_TABLE) &&
					_usedEditors[frameId].getEditorEditPageNr() === WE().consts.global.WE_EDITPAGE_CONTENT) {

					if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
						_usedEditors[frameId].setEditorReloadNeeded(true);
						_usedEditors[frameId].setEditorIsActive(true);
					} else {
						_usedEditors[frameId].setEditorReloadNeeded(true);
					}
				}
			}

			multiEditorreload = true;
			return;
		case 'weWidth':
			top.opener.top.resizeTo(spec[0], spec[1]);
			top.opener.top.moveTo((screen.width / 2) - (spec[0] / 2), (screen.height / 2) - (spec[1] / 2));
			return;
	}
}

function select_seem_start() {
	var myWindStr = "WE().util.jsWindow.prototype.find('edit_module').content.editor.edbody";

	if (document.getElementById('seem_start_type').value == 'object') {
		we_cmd('we_selector_document', document.forms[0].elements.seem_start_object.value, WE().consts.tables.OBJECT_FILES_TABLE, myWindStr + '.document.forms[0].elements.seem_start_object.value', myWindStr + '.document.forms[0].elements.seem_start_object_name.value', '', '', '', WE().consts.contentTypes.OBJECT_FILE, 'objectFile', WE().util.hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') ? 0 : 1);
	} else {
		we_cmd('we_selector_document', document.forms[0].elements.seem_start_document.value, WE().consts.tables.FILE_TABLE, myWindStr + '.document.forms[0].elements.seem_start_document.value', myWindStr + '.document.forms[0].elements.seem_start_document_name.value', '', '', '', WE().consts.contentTypes.WEDOCUMENT, 'objectFile', WE().util.hasPerm('CAN_SELECT_OTHER_USERS_FILES') ? 0 : 1);
	}
}

function show_seem_chooser(val) {
	switch (val) {
		case 'document':
			if (document.getElementById('seem_start_object')) {
				document.getElementById('seem_start_object').style.display = 'none';
			}
			document.getElementById('seem_start_document').style.display = 'block';
			document.getElementById('seem_start_weapp').style.display = 'none';
			break;
		case 'object':
			if (WE().consts.modules.active.indexOf("object") > 0) {
				document.getElementById('seem_start_document').style.display = 'none';
				document.getElementById('seem_start_weapp').style.display = 'none';
				document.getElementById('seem_start_object').style.display = 'block';
			}
			break;
		case 'weapp':
			document.getElementById('seem_start_document').style.display = 'none';
			document.getElementById('seem_start_object').style.display = 'none';
			document.getElementById('seem_start_weapp').style.display = 'block';
			break;
		default:
			document.getElementById('seem_start_document').style.display = 'none';
			document.getElementById('seem_start_weapp').style.display = 'none';
			if (document.getElementById('seem_start_object')) {
				document.getElementById('seem_start_object').style.display = 'none';
			}
			break;
	}
}

function showParentPerms(show) {
	var tmp = document.getElementsByClassName("showParentPerms");
	for (var k = 0; k < tmp.length; k++) {
		tmp[k].style.display = (show ? "inline" : "none");
	}
}

function delElement(elvalues, elem) {
	elvalues.value = elem;
	top.content.setHot();
}

function addElement(elvalues) {
	elvalues.value = "new";
	switchPage(2);
}

function rebuildCheckboxClicked() {
	toggleRebuildPerm(false);
}
