/**
 * webEdition CMS
 *
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function openClose(id) {
	var sort = "";
	if (id === "") {
		return;
	}
	var eintragsIndex = indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open === 0 ? 1 : 0);

	treeData[eintragsIndex].open = openstatus;

	if (openstatus && treeData[eintragsIndex].loaded != 1) {
		frames.cmd.location = treeData.frameset + "?pnt=cmd&pid=" + id + (sort !== "" ? "&sort=" + sort : "");
	} else {
		drawTree();
	}
	if (openstatus == 1) {
		treeData[eintragsIndex].loaded = 1;
	}
}

function updateEntry(id, text, pid, pub) {
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id) {
			treeData[ai].text = text;
			treeData[ai].parentid = pid;
			treeData[ai].published = pub;
		}
		ai++;
	}
	drawTree();
}

function doClick(id, typ) {
	var cmd = "";
	if (top.content.hot == "1") {
		if (confirm(g_l.save_changed_voting)) {
			cmd = "save_voting";
			top.content.we_cmd("save_voting");
		} else {
			top.content.usetHot();
			cmd = "voting_edit";
			var node = frames.top.get(id);
			frames.top.editor.edbody.location = treeData.frameset + "?pnt=edbody&cmd=" + cmd + "&cmdid=" + node.id + "&tabnr=" + frames.top.activ_tab;
		}
	} else {
		cmd = "voting_edit";
		var node = frames.top.get(id);
		frames.top.editor.edbody.location = treeData.frameset + "?pnt=edbody&cmd=" + cmd + "&cmdid=" + node.id + "&tabnr=" + frames.top.activ_tab;
	}
}

function info(text) {
}

function showSegment() {
	parentnode = frames.top.get(this.parentid);
	parentnode.clear();
	frames.cmd.location = treeData.frameset + "?pnt=cmd&pid=" + this.parentid + "&offset=" + this.offset;
	drawTree();
}

function makeNewEntry(icon, id, pid, txt, open, ct, tab, pub) {
	if (treeData[indexOfEntry(pid)] && treeData[indexOfEntry(pid)].loaded) {
		ct = (ct == "folder" ? "group" : "item");

		var attribs = {
			"id": id,
			"icon": icon,
			"text": txt,
			"parentid": pid,
			"open": open,
			"tooltip": id,
			"typ": ct,
			"disabled": 0,
			"published": (ct == "item" ? pub : 1),
			"selected": 0
		};

		treeData.addSort(new node(attribs));

		drawTree();
	}
}