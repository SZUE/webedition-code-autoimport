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

function drawEintraege() {
	fr = top.content.tree.window.document.body;//IMI: set tree indstead of left
	fr.innerHTML = '<div id="treetable"><nobr>' +
					zeichne(top.content.startloc, "") +
					"</nobr></div>" +
					"</body></html>";
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	ret = "";
	while (ai <= nf.laenge) {
		ret += zweigEintrag;
		if (nf[ai].typ == "user") {
			ret += "&nbsp;&nbsp;<IMG SRC=\"" + tree_img_dir +
							(ai == nf.laenge ?
											"kreuzungend.gif" :
											"kreuzung.gif") +
							"\" class=\"treeKreuz\">" +
							(nf[ai].name != -1 ?
											"<a name=\'_" + nf[ai].name + "\' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",\'" + nf[ai].contentType + "\',\'" + nf[ai].table + "\');return true;\">" :
											"") +
							"<img src=\"" + tree_icon_dir + nf[ai].icon + "\" alt=\"' . g_l('tree', '[edit_statustext]') . '\">" +
							"</a>" +
							"&nbsp;<a name=\'_" + nf[ai].name + "\' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",\'" + nf[ai].contentType + "\',\'" + nf[ai].table + "\');return true;\"><font color=\"" + ((nf[ai].contentType == "alias") ? "#006DB8" : (parseInt(nf[ai].denied) ? "red" : "black")) + "\">" + (parseInt(nf[ai].published) ? "<b>" : "") + "<label title=\'" + nf[ai].name + "\'>" + nf[ai].text + "</label>" + (parseInt(nf[ai].published) ? "</b>" : "") + "</font></A>&nbsp;&nbsp;<br/>";
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai == nf.laenge) ? "end" : "";

			if (nf[ai].offen == 0) {
				ret += "&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',1)\"><IMG SRC=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + tree_open_statustext + "\"></A>";
				var zusatz2 = "";
			} else {
				ret += "&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',0)\"><IMG SRC=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + tree_close_statustext + "\"></A>";
				var zusatz2 = "open";
			}
			ret += "<a name=\'_" + nf[ai].name + "\' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",\'" + nf[ai].contentType + "\',\'" + nf[ai].table + "\');return true;\">" +
							"<IMG SRC=\"" + tree_icon_dir + "usergroup" + zusatz2 + ".gif\" alt=\"" + tree_edit_statustext + "\">" +
							"</a>" +
							"<A name=\'_" + nf[ai].name + "\' HREF=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",\'" + nf[ai].contentType + "\',\'" + nf[ai].table + "\');return true;\">" +
							"&nbsp;<b><label title=\'" + nf[ai].name + "\'>" + nf[ai].text + "</label></b>" +
							"</a>" +
							"&nbsp;&nbsp;<br/>";
			if (nf[ai].offen) {
				if (ai == nf.laenge) {
					newAst = newAst + "<IMG SRC=\"" + tree_img_dir + "leer.gif\" class=\"treeKreuz\">";
				} else {
					newAst = newAst + "<IMG SRC=\"" + tree_img_dir + "strich2.gif\" class=\"treeKreuz\">";
				}
				ret += zeichne(nf[ai].name, newAst);
			}
		}
		ai++;
	}
	return ret;
}

function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub, denied) {
	if (table == tab) {
		if (ct == "folder") {
			menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab));
		} else {
			menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub, denied));
		}
		drawEintraege();
	}
}

function updateEntry(id, pid, text, pub, denied) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == "user")) {
			if (menuDaten[ai].name == id) {
				menuDaten[ai].vorfahr = pid;
				menuDaten[ai].text = text;
				menuDaten[ai].published = pub;
				menuDaten[ai].denied = denied;
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
		if ((menuDaten[ai].typ == "folder") || (menuDaten[ai].typ == "user")) {
			if (menuDaten[ai].name == id) {
				ind = ai;
				break;
			}
		}
		ai++;
	}
	if (ind != 0) {
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
	drawEintraege();
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == "root") || (menuDaten[ai].typ == "folder")) {
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
		if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'user')) {
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
	this.typ = "root";
	this.rootstat = rootstat;
	return this;
}

function dirEntry(icon, name, vorfahr, text, offen, contentType, table) {
	this.icon = icon;
	this.name = name;
	this.vorfahr = vorfahr;
	this.text = text;
	this.typ = "folder";
	this.offen = (offen ? 1 : 0);
	this.contentType = contentType;
	this.table = table;
	this.loaded = (offen ? 1 : 0);
	this.checked = false;
	return this;
}

function urlEntry(icon, name, vorfahr, text, contentType, table, published, denied) {
	this.icon = icon;
	this.name = name;
	this.vorfahr = vorfahr;
	this.text = text;
	this.typ = "user";
	this.checked = false;
	this.contentType = contentType;
	this.table = table;
	this.published = published;
	this.denied = denied;
	return this;
}



function start() {
	loadData();
	drawEintraege();
}

var startloc = 0;

self.focus();

var menuDaten = new container();
var count = 0;
var folder = 0;
