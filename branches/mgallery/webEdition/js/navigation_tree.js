/* global top */

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

container.prototype.openClose = function (id) {
	var sort = "";
	if (id === "")
		return;
	var eintragsIndex = treeData.indexOfEntry(id);
	var openstatus;

	openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;

	if (openstatus && !treeData[eintragsIndex].loaded) {
		frames.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=cmd&pid=" + id + (sort !== "" ? "&sort=" + sort : "");
	} else {
		drawTree();
	}
	if (openstatus) {
		treeData[eintragsIndex].loaded = true;
	}
};

node.prototype.showSegment = function () {
	top.reloadGroup(this.parentid, this.offset);
};

function reloadGroup(pid, offset) {
	var it = get(pid);
	if (it) {
		it.clear();
		startTree(pid, (offset ? offset : 0));
	}
}

function info(text) {
	t = frames.top.document.getElementById("infoField");
	if (text != " ") {
		t.style.display = "block";
		t.innerHTML = text;
	} else {
		t.innerHTML = text;
		t.style.display = "none";
	}
}

container.prototype.addSort = function (object) {
	this.len++;
	for (var i = this.len; i > 0; i--) {
		if (i > 1 && (this[i - 1].order > object.order)) {
			this[i] = this[i - 1];
			continue;
		}
		for (var j = i; j > 0; j--) {
			if (j > 1 && (this[j - 1].order == object.order) && (this[j - 1].text.toLowerCase() > object.text.toLowerCase() || (this[j - 1].typ > object.typ))) {
				this[j] = this[j - 1];
			} else {
				this[j] = object;
				break;
			}
		}
		break;
	}
};

function doClick(id, typ) {
	var node = frames.top.treeData.get(id);
	top.content.editor.edbody.we_cmd("module_navigation_edit", node.id);
}
