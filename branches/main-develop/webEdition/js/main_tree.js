/* global treeData, node, container, drawTree*/

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
'use strict';

Node.prototype.getLayout = function () {
	if (this.typ === "threedots") {
		return treeData.node_layouts.threedots;
	}
	var layout_key = (this.typ === "group" && this.contenttype !== "text/weCollection" ? "group" : "item") +
					(this.selected ? "Selected" : "") +
					(this.disabled ? "Disabled" : "") +
					(this.checked ? "Checked" : "") +
					(this.open ? "Open" : "") +
					(this.typ === "item" && this.published === 0 ? "Notpublished" : "") +
					(this.typ === "item" && this.published === -1 ? "Changed" : "");

	return treeData.node_layouts[layout_key];
};

container.prototype.openClose = function (id) {
	if (id === "") {
		return;
	}
	var eintragsIndex = treeData.indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;
	if (openstatus && !treeData[eintragsIndex].loaded) {
		window.we_cmd("loadFolder", top.treeData.table, treeData[eintragsIndex].id);
	} else {
		window.we_cmd("closeFolder", top.treeData.table, treeData[eintragsIndex].id);
		drawTree();
	}
	if (openstatus) {
		treeData[eintragsIndex].loaded = true;
	}
};

function info(text) {
	var t = document.getElementById("infoField");
	var s = document.getElementById("search");
	if (text !== " ") {
		s.style.display = "none";
		t.innerHTML = text;
		t.style.display = "block";
	} else {
		s.style.display = "block";
		t.style.display = "none";
		t.innerHTML = text;
	}
}

function doClick(id) {
	var node = treeData.get(id);
	var ct = node.contenttype;
	var table = node.table;
	id = node.we_id ? node.we_id : id;
	window.setScrollY();

	switch (table) {
		case WE().consts.tables.FILE_TABLE:
			if (top.wasdblclick && ct !== WE().consts.contentTypes.FOLDER) {
				WE().layout.openBrowser(id);
				window.setTimeout(function () {
					top.wasdblclick = false;
				}, 400);
				break;
			}
			/* falls through */
		default:
			WE().layout.weEditorFrameController.openDocument(table, id, ct);
			break;
	}
}