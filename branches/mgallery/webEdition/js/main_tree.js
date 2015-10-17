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

node.prototype.getLayout = function () {
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
};

container.prototype.openClose = function (id) {
	if (id == "") {
		return;
	}
	var eintragsIndex = treeData.indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;
	if (openstatus && !treeData[eintragsIndex].loaded) {
		we_cmd("loadFolder", top.treeData.table, treeData[eintragsIndex].id);
	} else {
		we_cmd("closeFolder", top.treeData.table, treeData[eintragsIndex].id);
		drawTree();
	}
	if (openstatus) {
		treeData[eintragsIndex].loaded = true;
	}
};

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
	var node = frames.top.treeData.get(id);
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