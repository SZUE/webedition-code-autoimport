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

var count = 0;
var folder = 0;

function drawEintraege() {
	fr = top.content.tree.window.document.body;
	fr.innerHTML = '<div id="treetable" class="tree"><nobr>' +
					"<tr><td class=\"tree\"><nobr><a href=javascript:// onclick=\"doYearClick(" + top.yearshop + ");return true;\" title=\"" + treeYearClick + "\" >" + treeYear + ": <strong>" + top.yearshop + " </strong></a> <br/>" +
					zeichne(0, "") +
					"</nobr></div>" +
					"</body></html>";
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	ret = "";
	while (ai <= nf.len) {
		ret += zweigEintrag;
		if (nf[ai].typ === 'shop') {
			ret += "&nbsp;&nbsp;<IMG SRC=\"" + tree_img_dir +
							(ai === nf.len ?
											"kreuzungend.gif" :
											"kreuzung.gif"
											) +
							"\" class=\"treeKreuz\"/>";
			if (perm_EDIT_SHOP_ORDER) { // make  in tree clickable
				if (nf[ai].name !== -1) {
					ret += "<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">";
				}
			}
			ret += "<img src=\"" + tree_img_dir + "icons/" + nf[ai].icon + "\" title=\"" + g_l.tree_edit_statustext + "\">" +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" :
											"") +
							"&nbsp;" +
							(perm_EDIT_SHOP_ORDER ? // make orders in tree clickable
											"<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
											"") +
							//changed for #6786
							"<span style='" + nf[ai].st + "'>" + nf[ai].text + "</span>" +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" : ""
											) +
							"&nbsp;&nbsp;<br/>";
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai === nf.len) ? "end" : "";
			var zusatz2 = "";

			if (nf[ai].open === 0) {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\"><img src=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\" title=\"" + g_l.tree_open_statustext + "\"></a>";
			} else {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\"><img src=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\" title=\"" + g_l.tree_close_statustext + "\"></a>";
				zusatz2 = "open";
			}
			ret += (perm_EDIT_SHOP_ORDER ?
							"<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
							"") +
							"<img src=\"" + tree_img_dir + "icons/folder" + zusatz2 + ".gif\" title=\"" + g_l.tree_edit_statustext + "\">" +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" +
											// make the month in tree clickable
											"<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" :
											"") +
							"&nbsp;" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "") +
							(perm_EDIT_SHOP_ORDER ?
											"</a>" : "") +
							"&nbsp;&nbsp;<br/>";
			if (nf[ai].open) {
				newAst = newAst + "<img src=\"" + tree_img_dir + (ai === nf.len ? "leer.gif" : "strich2.gif") + "\" class=\"treeKreuz\">";
				ret += zeichne(nf[ai].name, newAst);
			}
		}
		ai++;
	}
	return ret;
}

function makeNewEntry(icon, id, pid, txt, open, ct, tab, pub) {
	if (table === tab && treeData[indexOfEntry(pid)]) {
		if (ct === "folder") {
			treeData.addSort(new dirEntry(icon, id, pid, txt, open, ct, tab));
		} else {
			treeData.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
		}
		drawEintraege();
	}
}


function updateEntry(id, text, pub) {
	var ai = 1;
	while (ai <= treeData.len) {
		if ((treeData[ai].typ === 'folder') || (treeData[ai].typ === 'shop')) {
			if (treeData[ai].name == id) {
				treeData[ai].text = text;
				treeData[ai].published = pub;
			}
		}
		ai++;
	}
	drawEintraege();
}

function deleteEntry(id) {
	var ai = 1;
	var ind = 0;
	while (ai <= treeData.len) {
		if ((treeData[ai].typ === 'folder') || (treeData[ai].typ === 'shop')) {
			if (treeData[ai].name == id) {
				ind = ai;
				break;
			}
		}
		ai++;
	}
	updateTreeAfterDel(ind);
}

function openClose(name, status) {
	var eintragsIndex = indexOfEntry(name);
	treeData[eintragsIndex].open = status;
	drawEintraege();
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= treeData.len) {
		if ((treeData[ai].typ === 'root') || (treeData[ai].typ === 'folder')) {
			if (treeData[ai].name == name) {
				return ai;
			}
		}
		ai++;
	}
	return -1;
}

function search(eintrag) {
	var nf = new container();
	var ai = 1;
	while (ai <= treeData.len) {
		if ((treeData[ai].typ === 'folder') || (treeData[ai].typ === 'shop')) {
			if (treeData[ai].parentid == eintrag) {
				nf.add(treeData[ai]);
			}
		}
		ai++;
	}
	return nf;
}

function container() {
	this.len = 0;
	this.clear = function () {
		this.len = 0;
	};
	this.add = add;
	this.addSort = addSort;
	return this;
}

function add(object) {
	this.len++;
	this[this.len] = object;
}


function rootEntry(name, text, rootstat) {
	this.name = name;
	this.text = text;
	this.loaded = true;
	this.typ = 'root';
	this.rootstat = rootstat;
	return this;
}

//changed for #6786
function urlEntry(icon, name, parentid, text, contentType, table, published, style) {
	this.icon = icon;
	this.name = name;
	this.parentid = parentid;
	this.text = text;
	this.typ = 'shop';
	this.checked = false;
	this.contentType = contentType;
	this.table = table;
	this.published = published;
	this.st = style;
	return this;
}

function start() {
	loadData();
	drawEintraege();
}

function doClick(id, ct, table) {
	top.content.editor.location = '/webEdition/we/include/we_modules/shop/edit_shop_frameset.php?pnt=editor&bid=' + id;
}
function doFolderClick(id, ct, table) {
	top.content.editor.location = '/webEdition/we/include/we_modules/shop/edit_shop_frameset.php?pnt=editor&mid=' + id;
}
function doYearClick(yearView) {
	top.content.editor.location = '/webEdition/we/include/we_modules/shop/edit_shop_frameset.php?pnt=editor&ViewYear=' + yearView;
}
var treeData = new container();
