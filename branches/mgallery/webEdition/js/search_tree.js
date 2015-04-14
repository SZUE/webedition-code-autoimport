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

function showSegment() {
	top.reloadGroup(this.parentid, this.offset);
}

function makeNewEntry(icon, id, pid, txt, open, ct, tab, pub, order) {
	if (treeData[indexOfEntry(pid)] && treeData[indexOfEntry(pid)].loaded) {

		ct = (ct == "folder" ? "group" : "item");

		var attribs = {
			"id": id,
			"icon": icon,
			"text": txt,
			"parentid": pid,
			"open": open,
			"order": order,
			"tooltip": id,
			"typ": ct,
			"disabled": 0,
			"published": (pub == 0 ? 1 : 0),
			"depended": pub,
			"selected": 0,
		};

		treeData.addSort(new node(attribs));

		drawTree();
	}
}
function reloadGroup(pid) {
	var ai = 1;
	var it = get(pid);
	offset = arguments[1] ? arguments[1] : 0;
	if (it) {
		it.clear();
		startTree(pid, offset);
	}
}

function info(text) {
	t = frames.top.resize.left.treefooter.window.document.getElementById("infoField");
	if (text != " ") {
		t.style.display = "block";
		t.innerHTML = text;
	} else {
		t.innerHTML = text;
		t.style.display = "none";
	}
}

function openClose(id) {
	var sort = "";
	if (id == "") {
		return;
	}
	var eintragsIndex = indexOfEntry(id);
	var openstatus;

	openstatus = (treeData[eintragsIndex].open == 0 ? 1 : 0);

	treeData[eintragsIndex].open = openstatus;

	if (openstatus && treeData[eintragsIndex].loaded != 1) {
		if (sort != "") {
			frames.cmd.location = treeData.frameset + "?pnt=cmd&pid=" + id + "&sort=" + sort;
		} else {
			frames.cmd.location = treeData.frameset + "?pnt=cmd&pid=" + id;
		}
	} else {
		drawTree();
	}
	if (openstatus == 1) {
		treeData[eintragsIndex].loaded = 1;
	}
}

function updateEntry(id, text, pid, pub, order) {
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id) {
			treeData[ai].text = text;
			treeData[ai].parentid = pid;
			treeData[ai].order = order;
			treeData[ai].tooltip = id;
		}
		ai++;
	}
	drawTree();
}

function doClick(id, typ) {
	var node = top.content.get(id);
	top.content.resize.right.editor.edbody.we_cmd("tool_weSearch_edit", node.id);
}