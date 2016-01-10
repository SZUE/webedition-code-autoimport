/* global container, treeData, WE */

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
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

//FIXME: compare & unite all _tree.js files

//FIXME: add drawEintrage

var hot = 0;
var i;
var count = 0;
var folder = 0;

entries_selected = [];
last_entry_selected = -1;
multi_select = true;

for (i = 0; i < opener.current_sel.length; i++) {
	if (opener.current_sel[i][0] != 'we_message') {
		continue;
	}
	entries_selected = entries_selected.concat([opener.current_sel[i][1] + '&' + opener.current_sel[i][2]]);
}

function setHot() {
	hot = 1;
}

function usetHot() {
	hot = 0;
}

function do_selupdate() {
	opener.delta_sel = entries_selected;
	opener.delta_sel_add('we_message', '&');
	window.close();
}

function check(entry) {
	var i;
	var tarr = entry.split('&');
	var id = tarr[0];
	var img = "img_" + id;
	var tmp;
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].id == id) {
			if (treeData[i].checked) {
				treeData[i].checked = false;
				if (document.getElementsByName(imgName)) {
					tmp = document.getElementsByName(imgName)[0];
					tmp.classList.remove('fa-check-square-o');
					tmp.classList.add('fa-square-o');
				}
				unSelectMessage(entry, "");
				break;
			}
			treeData[i].checked = true;
			if (document.getElementsByName(imgName)) {
				tmp = document.getElementsByName(imgName)[0];
				tmp.classList.add('fa-check-square-o');
				tmp.classList.remove('fa-square-o');
			}
			doSelectMessage(entry, "");
			break;
		}
	}
	if (!document.images) {
		drawTree();
	}
}

function draw(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ret = "";
	for (var ai = 1; ai <= nf.len; ai++) {
		ret += zweigEintrag;
		switch (nf[ai].typ) {
			case 'user':
				ret = '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>' +
								'<span ' +
								(nf[ai].id != -1 ?
												"name='_" + nf[ai].id + "' onclick=\"doClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "')\" border=\"0\"" :
												'') +
								'>' +
								WE().util.getTreeIcon(nf[ai].contentType) +
								"</span>" +
								"<span onclick=\"top.check('" + nf[ai].id + '&' + nf[ai].text + "')\"><i class=\"fa fa-" + (nf[ai].checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + nf[ai].id + '"></i>' +
								"&nbsp;<span id=\"" + nf[ai].id + '&' + nf[ai].text + "\" class=\"u_tree_entry\">" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "") +
								"</span></span><br/>";
				break;
			default:
				ret += '<a href="javascript:top.treeData.openClose(\'' + nf[ai].id + '\',1)"><span class="treeKreuz fa-stack ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open === 0 ? "plus" : "minus") + "-square-o fa-stack-1x'></i></span>" +
								"<span name='_" + nf[ai].id + "' onclick=\"doClick(" + nf[ai].id + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');\">" +
								WE().util.getTreeIcon('we/userGroup') +
								"&nbsp;<b>" + nf[ai].text + "</b></span>" +
								"<br/>" +
								(nf[ai].open ? draw(nf[ai].id, zweigEintrag + '<span class="' + (ai == nf.len ? "" : "strich ") + 'treeKreuz"></span>') : "");
		}
	}
	return ret;
}

function deleteEntry(id) {
	var ind = 0;
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id == id) {
			ind = ai;
			break;
		}
	}
	if (ind !== 0) {
		for (ai = ind; ai <= treeData.len - 1; ai++) {
			treeData[ai] = treeData[ai + 1];
		}
		treeData.len[treeData.len] = null;
		treeData.len--;
		drawTree();
	}
}


openClose = function (id, status) {
	var eintragsIndex = treeData.indexOfEntry(id);
	treeData[eintragsIndex].open = status;
	drawTree();
};

indexOfEntry = function (id) {
	for (var ai = 1; ai <= this.len; ai++) {
		if ((this[ai].typ === 'root') || (this[ai].typ === 'folder')) {
			if (this[ai].id == id) {
				return ai;
			}
		}
	}
	return -1;
};

function search(eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= treeData.len; ai++) {
		if ((treeData[ai].typ === 'folder') || (treeData[ai].typ === 'user')) {
			if (treeData[ai].parentid == eintrag) {
				nf.add(treeData[ai]);
			}
		}
	}
	return nf;
}

function drawTree() {//FIXME: we don't have an existing document to write on, change this, as is changed in tree
	window.document.body.innerHTML = "<table class=\"default\" style=\"width:100%\"><tr><td class=\"tree\">" +
					draw(treeData.startloc, "") +
					"</td></tr></table>";

	for (var k = 0; k < parent.entries_selected.length; k++) {
		parent.highlight_Elem(parent.entries_selected[k], parent.sel_color, parent);
	}
}
