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
var deleteData = WE().util.getDynamicVar(document, 'loadVarDelete', 'data-deleteData');


function we_submitForm(target, url) {
	var f = window.document.we_form;
	if (!f.checkValidity()) {
		WE().util.showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	var sel = "";
	for (var i = 1; i <= top.treeData.len; i++) {
		if (top.treeData[i].checked == 1) {
			sel += (top.treeData[i].id + ",");
		}
	}
	if (!sel) {
		WE().util.showMessage(WE().consts.g_l.main.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}

	sel = sel.substring(0, sel.length - 1);

	f.sel.value = sel;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);
	switch (args[0]) {
		case "delInfo":
			new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_cmd[0]=delInfo", "we_delinfo", WE().consts.size.dialog.small, WE().consts.size.dialog.small, true, true, true);
			break;
		case 'deleteTreeEntries':
			deleteTreeEntries(args[1]);
			break;
		case "closeDeletedDocuments":
			closeDeletedDocuments(args[1], args[2]);
			break;
		default:
			if (top.we_cmd) {
				top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
			}
	}
}

function confirmDel(found) {
	if (found) {
		WE().util.showConfirm(window, "", WE().consts.g_l.alert.found_in_workflow, [deleteData.wecmd0, "", deleteData.table, 1]);
	} else {
		we_cmd(deleteData.wecmd0, "", deleteData.table, 1);
	}
}

function deleteTreeEntries(dontMoveClassFolders) {
	var obj = top.treeData;
	var cont = new top.container();
	for (var i = 1; i <= obj.len; i++) {
		if (!obj[i].checked || (dontMoveClassFolders && obj[i].parentid == 0)) {
			if (obj[i].parentid != 0) {
				if (!top.treeData.parentChecked(obj[i].parentid)) {
					cont.add(obj[i]);
				}
			} else {
				cont.add(obj[i]);
			}
		}
	}
	top.treeData = cont;
	top.drawTree();
}

//FIXME: change args to array
function closeDeletedDocuments(_delete_Ids, _delete_objects) {
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
	for (var frameId in _usedEditors) {

		if (deleteData.table === _usedEditors[frameId].getEditorEditorTable() &&
			(_delete_Ids.indexOf("," + _usedEditors[frameId].getEditorDocumentId() + ",") !== -1) ||
			(_usedEditors[frameId].getEditorEditorTable() === WE().consts.tables.OBJECT_FILES_TABLE &&
				(_delete_objects.indexOf("," + _usedEditors[frameId].getEditorDocumentId() + ",") !== -1)
				)
			) {
			_usedEditors[frameId].setEditorIsHot(false);
			WE().layout.weEditorFrameController.closeDocument(frameId);
		}
	}
}

function init() {
	if (deleteData.wecmd0 !== "delete_single_document") {
		switch (deleteData.table) {
			case WE().consts.tables.FILE_TABLE:
				if (WE().util.hasPerm("DELETE_DOC_FOLDER") && WE().util.hasPerm("DELETE_DOCUMENT")) {
					top.treeData.setState(top.treeData.tree_states.select);
				} else if (WE().util.hasPerm("DELETE_DOCUMENT")) {
					top.treeData.setState(top.treeData.tree_states.selectitem);
				}
				break;
			case WE().consts.tables.TEMPLATES_TABLE:
				if (WE().util.hasPerm("DELETE_TEMP_FOLDER") && WE().util.hasPerm("DELETE_TEMPLATE")) {
					top.treeData.setState(top.treeData.tree_states.select);
				} else if (WE().util.hasPerm("DELETE_TEMPLATE")) {
					top.treeData.setState(top.treeData.tree_states.selectitem);
				}
				break;
			case WE().consts.tables.OBJECT_FILES_TABLE:
				if (WE().util.hasPerm("DELETE_OBJECTFILE")) {
					top.treeData.setState(top.treeData.tree_states.select);
				}
				break;
			case WE().consts.tables.VFILE_TABLE:
				// FIXME: implement prefs for collections
				//if(permissionhandler::hasPerm("DELETE_DOC_FOLDER") && permissionhandler::hasPerm("DELETE_DOCUMENT")){
				top.treeData.setState(top.treeData.tree_states.select);
				/*
				 } elseif(permissionhandler::hasPerm("DELETE_DOCUMENT")){
				 echo 'top.treeData.setState(top.treeData.tree_states["selectitem"]);';
				 }
				 *
				 */
				break;
			default:
				top.treeData.setState(top.treeData.tree_states.selectitem);
		}

	}

	if (top.treeData.table != deleteData.table) {
		top.treeData.table = deleteData.table;
		we_cmd("load", deleteData.table);
	} else {
		top.drawTree();
	}
}