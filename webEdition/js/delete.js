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
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	var sel = "";
	for (var i = 1; i <= top.treeData.len; i++) {
		if (top.treeData[i].checked == 1) {
			sel += (top.treeData[i].id + ",");
		}
	}
	if (!sel) {
		top.we_showMessage(WE().consts.g_l.main.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
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
	if (top.we_cmd) {
		top.we_cmd.apply(window, Array.prototype.slice.call(arguments));
	}
}

function init() {
	if (deleteData.wecmd0 != "delete_single_document") {
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