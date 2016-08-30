/* global top, WE */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software, you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

function initMove(table) {
	top.treeData.setState(top.treeData.tree_states["select"]);
	if (top.treeData.table !== table) {
		top.treeData.table = table;
		we_cmd("load", table);
	} else {
		we_cmd("load", table);
		top.drawTree();
	}
}

function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);
	parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}

function we_submitForm(target, url) {
	var f = self.document.we_form;
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
		top.we_showMessage(WE().consts.g_l.main.nothing_to_move, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	sel = sel.substring(0, sel.length - 1);
	f.sel.value = sel;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}

function press_ok_move(type) {
	var sel = "";
	for (var i = 1; i <= top.treeData.len; i++) {
		if (top.treeData[i].checked == 1) {
			sel += (top.treeData[i].id + ",");
		}
	}
	if (!sel) {
		top.we_showMessage(WE().consts.g_l.main.nothing_to_move, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}

	// check if selected target exists
	var acStatus = '';
	acStatus = YAHOO.autocoml.checkACFields();
	acStatusType = typeof acStatus;
	if (acStatusType.toLowerCase() === 'object') {
		if (acStatus.running) {
			setTimeout(press_ok_move, 100, type);
			return;
		}
		if (!acStatus.valid) {
			top.we_showMessage(WE().consts.g_l.main.notValidFolder, WE().consts.message.WE_MESSAGE_ERROR, window);
			return;
		}
	}

	// close all documents before moving.


	// no open document can be moved
	// close all Editors with deleted documents
	var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();

	var _move_table = top.treeData.table;
	//var _move_ids = "," + sel;

	var _open_move_editors = [];

	for (frameId in _usedEditors) {
		if (_move_table == _usedEditors[frameId].getEditorEditorTable()) {
			_open_move_editors.push(_usedEditors[frameId]);
		}
	}
	if (_open_move_editors.length) {
		_openDocs_Str = "";

		for (i = 0; i < _open_move_editors.length; i++) {
			_openDocs_Str += "- " + _open_move_editors[i].getEditorDocumentPath() + "\n";

		}
		if (confirm(WE().util.sprintf(WE().consts.g_l.alert.move_exit_open_docs_question, type, type) + _openDocs_Str + "\n" + WE().consts.g_l.alert.move_exit_open_docs_continue)) {

			for (i = 0; i < _open_move_editors.length; i++) {
				_open_move_editors[i].setEditorIsHot(false);
				WE().layout.weEditorFrameController.closeDocument(_open_move_editors[i].getFrameId());

			}
			we_cmd('do_move', '', _move_table);
		}

	} else {

		if (confirm(WE().consts.g_l.alert.move)) {
			we_cmd('do_move', '', _move_table);
		}
	}
}
