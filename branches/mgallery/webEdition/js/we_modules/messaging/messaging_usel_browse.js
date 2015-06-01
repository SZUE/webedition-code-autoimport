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
multi_select = 1;

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

	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].name == id) {
			if (treeData[i].checked) {
				if (document.images) {
					if (messaging_usel_main.document.images[img])
						messaging_usel_main.document.images[img].src = tree_img_dir + "check0.gif";
				}
				treeData[i].checked = false;
				unSelectMessage(entry, 'elem', messaging_usel_main);
				break;
			} else {
				if (document.images) {
					if (messaging_usel_main.document.images[img])
						messaging_usel_main.document.images[img].src = tree_img_dir + "check1.gif";
				}
				treeData[i].checked = true;
				doSelectMessage(entry, 'elem', messaging_usel_main);
				break;
			}
		}
	}
	if (!document.images) {
		drawEintraege();
	}
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	ret = "";
	while (ai <= nf.len) {
		ret += zweigEintrag;
		if (nf[ai].typ == 'user') {
			if (ai == nf.len) {
				ret += "<IMG SRC=\"" + tree_img_dir + "kreuzungend.gif\" class=\"treeKreuz\">";
			} else {
				ret += "<IMG SRC=\"" + tree_img_dir + "kreuzung.gif\" class=\"treeKreuz\">";
			}
			if (nf[ai].name != -1) {
				ret += "<a name='_" + nf[ai].name + "' href=\"javascript:doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" border=\"0\">";
			}
			ret += "<IMG SRC=\"" + tree_icon_dir + nf[ai].icon + "\" alt=\"" + g_l.tree_edit_statustext + "\"></a>" +
							"<a href=\"javascript:top.check('" + nf[ai].name + '&' + nf[ai].text + "')\"><img src=\"" + tree_img_dir + (nf[ai].checked ? "check1.gif" : "check0.gif") + "\" \" alt=\"\" name=\"img_" + nf[ai].name + "\" /></a>" +
							"&nbsp;<a name='_" + nf[ai].name + "' href=\"javascript:top.check('" + nf[ai].name + '&' + nf[ai].text + "')\"><span id=\"" + nf[ai].name + '&' + nf[ai].text + "\" class=\"u_tree_entry\">" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "") + "</span></A>&nbsp;&nbsp;<br/>"
		} else {
			var newAst = zweigEintrag;

			var zusatz = (ai == nf.len) ? "end" : "";
			var zusatz2 = "";
			if (nf[ai].open === 0) {
				ret += "<A href=\"javascript:top.openClose('" + nf[ai].name + "',1)\"><IMG SRC=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + g_l.tree_open_statustext + "\"></A>";
			} else {
				ret += "<A href=\"javascript:top.openClose('" + nf[ai].name + "',0)\"><IMG SRC=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + g_l.tree_close_statustext + "\"></A>";
				zusatz2 = "open";
			}
			ret += "<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" +
							"<IMG SRC=\"" + tree_icon_dir + "usergroup" + zusatz2 + ".gif\" alt=\"" + g_l.tree_edit_statustext + "\"></a>" +
							"<A name='_" + nf[ai].name + "' HREF=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" +
							"&nbsp;<b>" + nf[ai].text + "</b></a>" +
							"&nbsp;&nbsp;<br/>";
			if (nf[ai].open) {
				if (ai == nf.len) {
					newAst += "<span class=\"treeKreuz\"></span>";
				} else {
					newAst += "<img SRC=\"" + tree_img_dir + "strich2.gif\" class=\"treeKreuz\">";
				}
				ret += zeichne(nf[ai].name, newAst);
			}
		}
		ai++;
	}
	return ret;
}


function makeNewEntry(icon, id, pid, txt, open, ct, tab, pub) {

	if (table == tab) {
		if (treeData[indexOfEntry(pid)]) {
			if (ct === "folder") {
				treeData.addSort(new dirEntry(icon, id, pid, txt, open, ct, tab));
			} else {
				treeData.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
			}
			drawEintraege();
		}
	}
}


function updateEntry(id, pid, text, pub) {
	var ai = 1;
	while (ai <= treeData.len) {
		if ((treeData[ai].typ === 'folder') || (treeData[ai].typ === 'user')) {
			if (treeData[ai].name == id) {
				treeData[ai].parentid = pid;
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
		if ((treeData[ai].typ == 'folder') || (treeData[ai].typ == 'user'))
			if (treeData[ai].name == id) {
				ind = ai;
				break;
			}
		ai++;
	}
	if (ind !== 0) {
		ai = ind;
		while (ai <= treeData.len - 1) {
			treeData[ai] = treeData[ai + 1];
			ai++;
		}
		treeData.len[treeData.len] = null;
		treeData.len--;
		drawEintraege();
	}
}

function openClose(name, status) {
	var eintragsIndex = indexOfEntry(name);
	treeData[eintragsIndex].open = status;
	drawEintraege();
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= treeData.len) {
		if ((treeData[ai].typ == 'root') || (treeData[ai].typ == 'folder')) {
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
		if ((treeData[ai].typ == 'folder') || (treeData[ai].typ == 'user')) {
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
	this.clear = function (){
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

function addSort(object) {
	this.len++;
	for (var i = this.len; i > 0; i--) {
		if (i > 1 && this[i - 1].text.toLowerCase() > object.text.toLowerCase()) {
			this[i] = this[i - 1];
		} else {
			this[i] = object;
			break;
		}
	}
}

function rootEntry(name, text, rootstat) {
	this.name = name;
	this.text = text;
	this.loaded = true;
	this.typ = 'root';
	this.rootstat = rootstat;
	return this;
}

function dirEntry(icon, name, parentid, text, open, contentType, table) {
	this.icon = icon;
	this.name = name;
	this.parentid = parentid;
	this.text = text;
	this.typ = 'folder';
	this.open = (open ? 1 : 0);
	this.contentType = contentType;
	this.table = table;
	this.loaded = (open ? 1 : 0);
	this.checked = false;
	return this;
}

function urlEntry(icon, name, parentid, text, contentType, table, published, checked) {
	this.icon = icon;
	this.name = name;
	this.parentid = parentid;
	this.text = text;
	this.typ = 'user';
	this.contentType = contentType;
	this.table = table;
	this.published = published;
	this.checked = checked;
	return this;
}

function drawEintraege() {//FIXME: we don't have an existing document to write on, change this, as is changed in tree
	messaging_usel_main.window.document.body.innerHTML = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\"><NOBR>" +
					zeichne(top.startloc, "") +
					"</nobr></td></tr></table>";


	for (var k = 0; k < parent.entries_selected.length; k++) {
		parent.highlight_Elem(parent.entries_selected[k], parent.sel_color, parent.messaging_usel_main);
	}
}
