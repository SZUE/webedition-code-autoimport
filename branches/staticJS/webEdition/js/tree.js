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

function getTreeLayout() {
	return this.tree_layouts[this.state];
}

function setTreeState() {
	this.state = arguments[0];

	if (this.state == this.tree_states["edit"]) {
		for (i = 1; i <= this.len; i++) {
			if (this[i].checked == 1) {
				this[i].checked = 0;
			}
		}

	}

}

function applyLayout() {
	if (arguments[0]) {
		eval("if(" + treeData.treeFrame + ".document.getElementById(\"lab_" + this.id + "\"))" + treeData.treeFrame + ".document.getElementById(\"lab_" + this.id + "\").className =\"" + arguments[0] + "\";");
	} else {
		eval("if(" + treeData.treeFrame + ".document.getElementById(\"lab_" + this.id + "\"))" + treeData.treeFrame + ".document.getElementById(\"lab_" + this.id + "\").className =\"" + this.getlayout() + "\";");
	}
}

function rootEntry(id, text, rootstat, offset) {
	this.id = id;
	this.text = text;
	this.open = 1;
	this.loaded = 1;
	this.typ = "root";
	this.offset = offset;
	this.rootstat = rootstat;
	this.showsegment = showSegment;
	this.clear = clearItems;

	return this;
}

function node(attribs) {

	for (aname in attribs) {
		var val = "" + attribs[aname];
		this[aname] = val;
	}

	this.getlayout = getLayout;
	this.applylayout = applyLayout;
	this.showsegment = showSegment;
	this.clear = clearItems;
	return this;
}

function selectNode() {
	if (arguments[0]) {
		var ind;
		if (treeData.selection != "" && treeData.selection_table == treeData.table) {
			ind = indexOfEntry(treeData.selection);
			if (ind != -1) {
				var oldnode = get(treeData.selection);
				oldnode.selected = 0;
				oldnode.applylayout();
			}
		}
		ind = indexOfEntry(arguments[0]);
		if (ind != -1) {
			var newnode = get(arguments[0]);
			newnode.selected = 1;
			newnode.applylayout();
		}
		treeData.selection = arguments[0];
		treeData.selection_table = treeData.table;
	}
}

function unselectNode() {
	if (treeData.selection != "" && treeData.table == treeData.selection_table) {
		var ind = indexOfEntry(treeData.selection);
		if (ind != -1) {
			var node = get(treeData.selection);
			node.selected = 0;
			if (node.applylayout)
				node.applylayout();
		}
		treeData.selection = "";
	}
}

function deleteEntry(id) {
	var ai = 1;
	var ind = 0;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id) {
			ind = ai;
			break;
		}
		ai++;
	}
	if (ind != 0) {
		ai = ind;
		while (ai <= treeData.len - 1) {
			treeData[ai] = treeData[ai + 1];
			ai++;
		}
		treeData.len[treeData.len] = null;
		treeData.len--;
		drawTree();
	}
}

function makeFoldersOpenString() {
	var op = "";
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].typ == "group" && treeData[i].open == 1) {
			op += treeData[i].id + ",";
		}
	}
	op = op.substring(0, op.length - 1);
	return op;
}

function clearTree() {
	treeData.clear();
}

function parentChecked(start) {
	var obj = top.treeData;
	for (var i = 1; i <= obj.len; i++) {
		if (obj[i].id == start) {
			if (obj[i].checked == 1) {
				return true;
			}

			if (obj[i].parentid != 0) {
				parentChecked(obj[i].parentid);
			}
		}
	}

	return false;
}

function setCheckNode(imgName) {
	if (document.images[imgName]) {
		document.images[imgName].src = "/webEdition/images/tree/check0.gif";
	}
}

function setUnCheckNode(imgName) {
	if (document.images[imgName]) {
		document.images[imgName].src = "/webEdition/images/tree/check1.gif";
	}
}