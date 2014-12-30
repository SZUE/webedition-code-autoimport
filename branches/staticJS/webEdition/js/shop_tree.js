/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 8784 $
 * $Author: mokraemer $
 * $Date: 2014-12-18 13:57:57 +0100 (Do, 18. Dez 2014) $
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

var menuDaten = new container();
var count = 0;
var folder = 0;

function drawEintraege() {
	fr = top.content.tree.window.document.body;
	fr.innerHTML = '<table border=0 cellpadding=0 cellspacing=0 width="100%"><tr><td class="tree"><nobr>' +
					"<tr><td class=\"tree\"><nobr><a href=javascript:// onclick=\"doYearClick(" + top.yearshop + ");return true;\" title=\"" + treeYearClick + "\" >" + treeYear + ": <strong>" + top.yearshop + " </strong></a> <br/>"
	zeichne(0, "") +
					"</nobr></td></tr></table>" +
					"</body></html>";
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	ret = "";
	while (ai <= nf.laenge) {
		ret += zweigEintrag;
		if (nf[ai].typ === 'shop') {
			if (ai === nf.laenge) {
				ret += "&nbsp;&nbsp;<IMG SRC=\"" + tree_img_dir + "kreuzungend.gif\" WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
			} else {
				ret += "&nbsp;&nbsp;<IMG SRC=\"" + tree_img_dir + "kreuzung.gif\" WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
			}
			if (perm_EDIT_SHOP_ORDER) { // make  in tree clickable
				if (nf[ai].name !== -1) {
					ret += "<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>";
				}
			}
			ret += "<IMG SRC=\"" + tree_img_dir + "icons/" + nf[ai].icon + "\" title=\"" + tree_edit_statustext + "\">";
			if (perm_EDIT_SHOP_ORDER) {
				ret += "</a>";
			}
			ret += "&nbsp;";
			if (perm_EDIT_SHOP_ORDER) { // make orders in tree clickable
				ret += "<a href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">";

			}
			//changed for #6786
			ret += "<span style='" + nf[ai].st + "'>" + nf[ai].text + "</span>";
			if (perm_EDIT_SHOP_ORDER) {
				ret += "</a>";
			}
			ret += "&nbsp;&nbsp;<br/>";
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai === nf.laenge) ? "end" : "";

			if (nf[ai].offen === 0) {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\" border=0><img src=\"" + tree_img_dir + "auf" + zusatz + ".gif\" WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"" + tree_open_statustext + "\"></a>";
				var zusatz2 = "";
			} else {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\" border=0><img src=\"" + tree_img_dir + "zu" + zusatz + ".gif\" WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 title=\"" + tree_close_statustext + "\"></a>";
				var zusatz2 = "open";
			}
			if (perm_EDIT_SHOP_ORDER) {
				ret += "<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=0>";
			}
			ret += "<img src=\"" + tree_img_dir + "icons/folder" + zusatz2 + ".gif\" title=\"" + tree_edit_statustext + "\">";
			if (perm_EDIT_SHOP_ORDER) {
				ret += "</a>";

			}
			if (perm_EDIT_SHOP_ORDER) {
				// make the month in tree clickable
				ret += "<a href=\"javascript://\" onclick=\"doFolderClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">";
			}
			ret += "&nbsp;" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "");
			if (perm_EDIT_SHOP_ORDER) {
				ret += "</a>";
			}
			ret += "&nbsp;&nbsp;<br/>";
			if (nf[ai].offen) {
				newAst = newAst + "<img src=\"" + tree_img_dir + (ai === nf.laenge ? "leer.gif" : "strich2.gif") + "\" width=19 height=18 align=absmiddle border=0>";
				ret += zeichne(nf[ai].name, newAst);
			}
		}
		ai++;
	}
	return ret;
}

function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub) {
	if (table === tab && menuDaten[indexOfEntry(pid)]) {
		if (ct === "folder") {
			menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab));
		} else {
			menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
		}
		drawEintraege();
	}
}


function updateEntry(id, text, pub) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'shop')) {
			if (menuDaten[ai].name == id) {
				menuDaten[ai].text = text;
				menuDaten[ai].published = pub;
			}
		}
		ai++;
	}
	drawEintraege();
}

function deleteEntry(id) {
	var ai = 1;
	var ind = 0;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'shop')) {
			if (menuDaten[ai].name == id) {
				ind = ai;
				break;
			}
		}
		ai++;
	}
	if (ind !== 0) {
		ai = ind;
		while (ai <= menuDaten.laenge - 1) {
			menuDaten[ai] = menuDaten[ai + 1];
			ai++;
		}
		menuDaten.laenge[menuDaten.laenge] = null;
		menuDaten.laenge--;
		drawEintraege();
	}
}

function openClose(name, status) {
	var eintragsIndex = indexOfEntry(name);
	menuDaten[eintragsIndex].offen = status;
	if (status) {
		if (!menuDaten[eintragsIndex].loaded) {
			drawEintraege();
		} else {
			drawEintraege();
		}
	} else {
		drawEintraege();
	}
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ === 'root') || (menuDaten[ai].typ === 'folder')) {
			if (menuDaten[ai].name == name) {
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
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'shop')) {
			if (menuDaten[ai].vorfahr == eintrag) {
				nf.add(menuDaten[ai]);
			}
		}
		ai++;
	}
	return nf;
}

function container() {
	this.laenge = 0;
	this.clear = containerClear;
	this.add = add;
	this.addSort = addSort;
	return this;
}

function add(object) {
	this.laenge++;
	this[this.laenge] = object;
}

function containerClear() {
	this.laenge = 0;
}

function addSort(object) {
	this.laenge++;
	for (var i = this.laenge; i > 0; i--) {
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

function dirEntry(icon, name, vorfahr, text, offen, contentType, table, published) {
	this.icon = icon;
	this.name = name;
	this.vorfahr = vorfahr;
	this.text = text;
	this.typ = 'folder';
	this.offen = (offen ? 1 : 0);
	this.contentType = contentType;
	this.table = table;
	this.loaded = (offen ? 1 : 0);
	this.checked = false;
	this.published = published;
	return this;
}

//changed for #6786
function urlEntry(icon, name, vorfahr, text, contentType, table, published, style) {
	this.icon = icon;
	this.name = name;
	this.vorfahr = vorfahr;
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
self.focus();
