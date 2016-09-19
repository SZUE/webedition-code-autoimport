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
	if (id === "") {
		return;
	}

	var eintragsIndex = this.indexOfEntry(id);
	var status;

	openstatus = (this[eintragsIndex].open ? 0 : 1);

	this[eintragsIndex].open = openstatus;
	if (openstatus && !this[eintragsIndex].loaded) {
		treeData.frames.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=load&tab=" + treeData.frames.top.table + "&cmd=load&pid=" + id;
		treeData.frames.top.openFolders[treeData.frames.top.table] += "," + id;
	} else {
		var arr = treeData.frames.top.openFolders[treeData.frames.top.table].split(",");
		treeData.frames.top.openFolders[treeData.frames.top.table] = "";
		for (var t = 0; t < arr.length; t++) {
			if (arr[t] !== "" && arr[t] != id) {
				treeData.frames.top.openFolders[treeData.frames.top.table] += "," + arr[t];
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
	for (i = 1; i <= this.len; i++) {
		if (this[i].id == object_name) {
//			treeData.frames.tree.populate(this[i].id, this.table);
			if (this[i].checked == 1) {
				if (treeData.frames.tree.document.getElementsByName(imgName)) {
					tmp = treeData.frames.tree.document.getElementsByName(imgName)[0];
					tmp.classList.remove('fa-check-square-o');
					tmp.classList.add('fa-square-o');
				}
				this[i].checked = 0;
				var pos = top.content.editor.edbody.SelectedItems[treeData.frames.top.table].indexOf(this[i].id);
				if (pos > -1) {
					top.content.editor.edbody.SelectedItems[treeData.frames.top.table].splice(pos, 1);
				}

				this[i].applylayout();
				break;
			} else {
				if (treeData.frames.tree.document.getElementsByName(imgName)) {
					tmp = treeData.frames.tree.document.getElementsByName(imgName)[0];
					tmp.classList.remove('fa-square-o');
					tmp.classList.add('fa-check-square-o');
				}
				this[i].checked = 1;
				top.content.editor.edbody.SelectedItems[treeData.frames.top.table].push(this[i].id);
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
	treeData.frames.top.table = tab;
	treeData.frames.top.document.we_form.table.value = tab;
	setTimeout(treeData.frames.top.startTree, 100);
}


function drawTree() {
	treeData.frames.tree.document.getElementById("treetable").innerHTML = '<div class="treetable ' + treeData.getLayout() + '">' +
					treeData.draw(treeData.startloc, "") +
					"</div>";
}