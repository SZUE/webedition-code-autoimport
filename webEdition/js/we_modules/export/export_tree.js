/* global container, WE,treeData,drawTree, top */

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

function initTree() {
	window.treeData = new container();
	treeData.SelectedItems = {};
	treeData.SelectedItems[WE().consts.tables.FILE_TABLE] = [];
	treeData.SelectedItems[WE().consts.tables.TEMPLATES_TABLE] = [];
	treeData.SelectedItems[WE().consts.tables.OBJECT_FILES_TABLE] = [];
	treeData.SelectedItems[WE().consts.tables.OBJECT_TABLE] = [];

	treeData.openFolders = {};
	treeData.openFolders[WE().consts.tables.FILE_TABLE] = "";
	treeData.openFolders[WE().consts.tables.TEMPLATES_TABLE] = "";
	treeData.openFolders[WE().consts.tables.OBJECT_FILES_TABLE] = "";
	treeData.openFolders[WE().consts.tables.OBJECT_TABLE] = "";
}

function startTree() {
	var win = top.content ? top.content : top;
	win.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=load&cmd=load&tab=" + treeData.table + "&pid=0&openFolders=" + treeData.openFolders[treeData.table];
}

container.prototype.openClose = function (id) {
	if (id === "") {
		return;
	}

	var eintragsIndex = this.indexOfEntry(id),
		openstatus = (this[eintragsIndex].open ? 0 : 1),
		win = (top.content ? top.content : top);

	this[eintragsIndex].open = openstatus;
	if (openstatus && !this[eintragsIndex].loaded) {
		win.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=load&tab=" + treeData.table + "&cmd=load&pid=" + id;
		treeData.openFolders[treeData.table] += "," + id;
	} else {
		var arr = treeData.openFolders[treeData.table].split(",");
		treeData.openFolders[treeData.table] = "";
		for (var t = 0; t < arr.length; t++) {
			if (arr[t] !== "" && arr[t] != id) {
				treeData.openFolders[treeData.table] += "," + arr[t];
			}
		}
		drawTree();
	}
	if (openstatus) {
		this[eintragsIndex].loaded = true;
	}
};

container.prototype.checkNode = function (imgName) {
	var tmp;
	var object_name = imgName.substring(4, imgName.length);
	for (var i = 1; i <= this.len; i++) {
		if (this[i].id == object_name) {
//			populate(this[i].id, this.table);
			if (this[i].checked == 1) {
				if (document.getElementsByName(imgName)) {
					tmp = document.getElementsByName(imgName)[0];
					tmp.classList.remove('fa-check-square-o');
					tmp.classList.add('fa-square-o');
				}
				this[i].checked = 0;
				var pos = treeData.SelectedItems[treeData.table].indexOf(this[i].id);
				if (pos > -1) {
					treeData.SelectedItems[treeData.table].splice(pos, 1);
				}

				this[i].applylayout();
				break;
			} else {
				if (document.getElementsByName(imgName)) {
					tmp = document.getElementsByName(imgName)[0];
					tmp.classList.remove('fa-square-o');
					tmp.classList.add('fa-check-square-o');
				}
				this[i].checked = 1;
				treeData.SelectedItems[treeData.table].push(this[i].id);
				this[i].applylayout();
				break;
			}
		}

	}
	if (top.content) {
		if (top.content.hot !== undefined) {
			top.content.hot = true;
		}
	}
};

function info(text) {
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
				continue;
			}
			this[j] = object;
			break;
		}
		break;
	}
};

function setHead(tab) {
	treeData.table = tab;
	document.we_form.table.value = tab;
	window.setTimeout(startTree, 100);
}

function drawTree() {
	document.getElementById("treetable").innerHTML = '<div class="treetable ' + treeData.getLayout() + '">' +
		treeData.draw(treeData.startloc, "") +
		"</div>";
}