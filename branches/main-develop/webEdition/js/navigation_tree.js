/* global top, WE, treeData, container,drawTree,startTree,node*/

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

container.prototype.openClose = function (id) {
	var sort = "";
	if (id === "") {
		return;
	}
	var eintragsIndex = treeData.indexOfEntry(id);
	var openstatus;

	openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;

	if (openstatus && !treeData[eintragsIndex].loaded) {
		treeData.frames.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=cmd&pid=" + id + (sort !== "" ? "&sort=" + sort : "");
	} else {
		drawTree();
	}
	if (openstatus) {
		treeData[eintragsIndex].loaded = true;
	}
};

function reloadGroup(pid, offset) {
	var it = treeData.get(pid);
	if (it) {
		it.clear();
		startTree(pid, (offset ? offset : 0));
	}
}

node.prototype.showSegment = function () {
	reloadGroup(this.parentid, this.offset);
};

function info(text) {
	var i = treeData.frames.top.document.getElementById("infoField");
	var s = treeData.frames.top.document.getElementById("search");
	if (text != " ") {
		s.style.display = "none";
		i.style.display = "block";
		i.innerHTML = text;
	} else {
		i.innerHTML = text;
		i.style.display = "none";
		s.style.display = "block";
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
	var node = treeData.get(id);
	top.content.editor.edbody.we_cmd("module_navigation_edit", node.id);
}
