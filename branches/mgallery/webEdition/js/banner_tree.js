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

var loaded = 0;
var hot = 0;
var hloaded = 0;
function setHot() {
	hot = 1;
}

function usetHot() {
	hot = 0;
}

var menuDaten = new container();
var count = 0;
var folder = 0;
function drawEintraege() {
	fr = top.content.tree.window.document.body;
	fr.innerHTML = '<div id="treetable" class="tree"><nobr>' +
					zeichne(top.content.startloc, "") +
					"</nobr></div>" +
					"</body></html>";
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	ret = "";
	while (ai <= nf.len) {
		ret += zweigEintrag;
		if (nf[ai].typ == 'file') {
			ret += "&nbsp;&nbsp;<img src=\"" + tree_img_dir +
							(ai == nf.len ?
											"kreuzungend.gif" :
											"kreuzung.gif"
											) +
							"\" class=\"treeKreuz\">";
			if (nf[ai].name != -1) {
				ret += "<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">";
			}
			ret += "<img src=\"" + tree_img_dir + "icons/" + nf[ai].icon + "\"/>" +
							"</a>" +
							"&nbsp;<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" + (parseInt(nf[ai].published) ? "" : "") + nf[ai].text + (parseInt(nf[ai].published) ? "" : "") + "</a>&nbsp;&nbsp;<br/>";
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai == nf.len ? "end" : "");
			var zusatz2 = "";
			if (nf[ai].offen === 0) {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',1)\"><img src=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\"></a>";
			} else {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].name + "',0)\"><img src=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\"></a>";
				zusatz2 = "open";
			}
			ret += "<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" +
							"<img src=\"" + tree_img_dir + "icons/" + nf[ai].icon.replace(/\.gif/, "") + zusatz2 + ".gif\">" +
							"</a>" +
							"<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">" +
							"&nbsp;<b>" + nf[ai].text + "</b>" +
							"</a>" +
							"&nbsp;&nbsp;<br/>";
			if (nf[ai].offen) {
				newAst += "<img src=\"" + tree_img_dir +
								(ai == nf.len ?
												"leer.gif" :
												"strich2.gif"
												) + "\" class=\"treeKreuz\">";

				ret += zeichne(nf[ai].name, newAst);
			}
		}
		ai++;
	}
	return ret;
}


function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub) {
	if (ct === "folder") {
		menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab, pub));
	} else {
		menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
	}
	drawEintraege();
}

function updateEntry(id, pid, text, pub) {
	var ai = 1;
	while (ai <= menuDaten.len) {
		if (menuDaten[ai].name == id) {
			menuDaten[ai].vorfahr = pid;
			menuDaten[ai].text = text;
			menuDaten[ai].published = 1;
		}
		ai++;
	}
	drawEintraege();
}

function deleteEntry(id, type) {
	var ai = 1;
	var ind = 0;
	while (ai <= menuDaten.len) {
		if ((menuDaten[ai].typ == type) && menuDaten[ai].name == id) {
			ind = ai;
			break;

		}
		ai++;
	}
	updateTreeAfterDel(ind);
}

function openClose(name, status) {
	var eintragsIndex = indexOfEntry(name);
	menuDaten[eintragsIndex].offen = status;
	drawEintraege();
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= menuDaten.len) {
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
	while (ai <= menuDaten.len) {
		if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'file')) {
			if (menuDaten[ai].vorfahr == eintrag) {
				nf.add(menuDaten[ai]);
			}
		}
		ai++;
	}
	return nf;
}

function container() {
	this.len = 0;
	this.clear = containerClear;
	this.add = add;
	this.addSort = addSort;
	return this;
}

function add(object) {
	this.len++;
	this[this.len] = object;
}

function containerClear() {
	this.len = 0;
}

function rootEntry(name, text, rootstat) {
	this.name = name;
	this.text = text;
	this.loaded = true;
	this.typ = 'root';
	this.rootstat = rootstat;
	return this;
}

function start() {
	loadData();
	drawEintraege();
}