/* global WE, top, container,drawTree,treeData */

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
'use strict';

container.prototype.openClose = function (id) {
	var sort = "";
	if (id === "") {
		return;
	}
	var eintragsIndex = treeData.indexOfEntry(id);
	var openstatus = (treeData[eintragsIndex].open ? 0 : 1);

	treeData[eintragsIndex].open = openstatus;

	if (openstatus && !treeData[eintragsIndex].loaded) {
		top.content.cmd.location = top.getFrameset() + "&pnt=cmd&cmd=mainload&pid=" + id + (sort !== "" ? "&sort=" + sort : "");
	} else {
		drawTree();
	}
	if (openstatus) {
		treeData[eintragsIndex].loaded = true;
	}
};

function doClick(id, typ) {
	var cmd = "";
	var node;
	if (top.content.hot) {
		if (window.confirm(WE().consts.g_l.exports.save_changed_export)) {
			cmd = "save_export";
			top.content.we_cmd("save_export");
			return;
		}
	}
	top.content.unsetHot();
	cmd = "export_edit";
	node = treeData.get(id);
	top.content.editor.edbody.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edbody&cmd=" + cmd + "&cmdid=" + node.id + "&tabnr=" + top.content.activ_tab;
}


function startTree() {
	top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=cmd&cmd=mainload&pid=0";
	drawTree();
}