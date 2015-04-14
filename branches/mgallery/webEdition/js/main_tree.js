/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 9713 $
 * $Author: mokraemer $
 * $Date: 2015-04-10 01:33:24 +0200 (Fr, 10. Apr 2015) $
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
	if (this.typ === "threedots"){
		return treeData.node_layouts["threedots"];
	}
	var layout_key = (this.typ === "group" && this.contenttype !== "text/weCollection" ? "group" : "item") +
					(this.selected == 1 ? "-selected" : "") +
					(this.disabled == 1 ? "-disabled" : "") +
					(this.checked == 1 ? "-checked" : "") +
					(this.open == 1 ? "-open" : "") +
					(this.typ == "item" && this.published == 0 ? "-notpublished" : "") +
					(this.typ == "item" && this.published == -1 ? "-changed" : "");

	return treeData.node_layouts[layout_key];
}
function openClose(id) {
	if (id == "") {
		return;
	}
	var eintragsIndex = indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open == 0 ? 1 : 0);
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

function makeNewEntry(icon, id, pid, txt, open, ct, tab) {
	if (treeData.table == tab) {
		if (treeData[indexOfEntry(pid)]) {
			if (treeData[indexOfEntry(pid)].loaded) {

				var attribs = {
					"id": id,
					"icon": icon,
					"text": txt,
					"parentid": pid,
					"open": open,
					"typ": (ct == "folder" ? "group" : "item"),
					"table": tab,
					"tooltip": id,
					"contenttype": ct,
					"disabled": 0,
					"selected": 0
				};
				if (attribs["typ"] == "item") {
					attribs["published"] = 0;
				}

				treeData.addSort(new node(attribs));

				drawTree();
			}
		}
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
function updateEntry(id, text, pid, tab) {
	if ((treeData.table == tab) && (treeData[indexOfEntry(pid)])) {
		var ai = 1;
		while (ai <= treeData.len) {
			if (treeData[ai].id == id) {
				if (text) {
					treeData[ai].text = text;
				}
				if (pid) {
					treeData[ai].parentid = pid;
				}
				if (tab) {
					treeData[ai].table = tab;
				}
			}
			ai++;
		}
		drawTree();
	}
}

function doClick(id) {
	var node = frames.top.get(id);
	var ct = node.contenttype;
	var table = node.table;
	var id = node.we_id ? node.we_id : id;
	setScrollY();
	if (frames.top.wasdblclick && ct != "folder") {
		switch (table) {
			case top.table.FILE_TABLE:
				top.openBrowser(id);
				setTimeout("wasdblclick=false;", 400);
				break;
			default:
				top.weEditorFrameController.openDocument(table, id, ct);
				break;
		}
	}
}