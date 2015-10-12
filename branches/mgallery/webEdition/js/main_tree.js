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

function getLayout() {
	if (this.typ === "threedots") {
		return treeData.node_layouts["threedots"];
	}
	var layout_key = (this.typ === "group" && this.contenttype !== "text/weCollection" ? "group" : "item") +
					(this.selected ? "-selected" : "") +
					(this.disabled ? "-disabled" : "") +
					(this.checked ? "-checked" : "") +
					(this.open ? "-open" : "") +
					(this.typ == "item" && this.published == 0 ? "-notpublished" : "") +
					(this.typ == "item" && this.published == -1 ? "-changed" : "");

	return treeData.node_layouts[layout_key];
}

function openClose(id) {
	if (id == "") {
		return;
	}
	var eintragsIndex = indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;
	if (openstatus && treeData[eintragsIndex].loaded != 1) {
		we_cmd("loadFolder", top.treeData.table, treeData[eintragsIndex].id);
		toggleBusy(1);
	} else {
		we_cmd("closeFolder", top.treeData.table, treeData[eintragsIndex].id);
		drawTree();
	}
	if (openstatus == 1) {
		treeData[eintragsIndex].loaded = 1;
	}
}

function info(text) {
	t = TreeInfo.window.document.getElementById("infoField");
	s = TreeInfo.window.document.getElementById("search");
	if (text != " ") {
		s.style.display = "none";
		t.style.display = "block";
		t.innerHTML = text;
	} else {
		s.style.display = "block";
		t.innerHTML = text;
		t.style.display = "none";
	}
}

function doClick(id) {
	var node = frames.top.get(id);
	var ct = node.contenttype;
	var table = node.table;
	var id = node.we_id ? node.we_id : id;
	setScrollY();

	switch (table) {
		case WE().consts.tables.FILE_TABLE:
			if (frames.top.wasdblclick && ct !== "folder") {
				top.openBrowser(id);
				setTimeout("wasdblclick=false;", 400);
				break;
			}
		default:
			WE().layout.weEditorFrameController.openDocument(table, id, ct);
			break;
	}
}