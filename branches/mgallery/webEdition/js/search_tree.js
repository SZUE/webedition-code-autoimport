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

node.prototype.showSegment = function () {
	top.reloadGroup(this.parentid, this.offset);
};

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
	t = frames.top.document.getElementById("infoField");
	if (text != " ") {
		t.style.display = "block";
		t.innerHTML = text;
	} else {
		t.innerHTML = text;
		t.style.display = "none";
	}
}

container.prototype.openClose = function(id) {
	if (id == "") {
		return;
	}
	var sort = "";
	var eintragsIndex = treeData.indexOfEntry(id);
	var openstatus;

	openstatus = (treeData[eintragsIndex].open ? 0 : 1);
	treeData[eintragsIndex].open = openstatus;

	if (openstatus && treeData[eintragsIndex].loaded != 1) {
		frames.cmd.location = treeData.frameset + "?pnt=cmd&pid=" + id + (sort ? "&sort=" + sort : "");
	} else {
		drawTree();
	}
	if (openstatus == 1) {
		treeData[eintragsIndex].loaded = 1;
	}
}

function doClick(id, typ) {
	var node = top.content.treeData.get(id);
	top.content.editor.edbody.we_cmd("tool_weSearch_edit", node.id);
}