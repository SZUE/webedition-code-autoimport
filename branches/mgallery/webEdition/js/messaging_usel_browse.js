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

var loaded = 0;
var hot = 0;
var i;
var menuDaten = new container();
var count = 0;
var folder = 0;

entries_selected = [];
last_entry_selected = -1;
multi_select = 1;

for (i = 0; i < opener.current_sel.length; i++) {
	if (opener.current_sel[i][0] != 'we_message') {
		continue;
	}
	entries_selected = entries_selected.concat(new Array(opener.current_sel[i][1] + '&' + opener.current_sel[i][2]));
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

	for (i = 1; i <= menuDaten.laenge; i++) {
		if (menuDaten[i].name == id) {
			if (menuDaten[i].checked) {
				if (document.images) {
					if (messaging_usel_main.document.images[img])
						messaging_usel_main.document.images[img].src = tree_img_dir + "check0.gif";
				}
				menuDaten[i].checked = false;
				unSelectMessage(entry, 'elem', messaging_usel_main);
				break;
			} else {
				if (document.images) {
					if (messaging_usel_main.document.images[img])
						messaging_usel_main.document.images[img].src = tree_img_dir + "check1.gif";
				}
				menuDaten[i].checked = true;
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
	while (ai <= nf.laenge) {
		fr.write(zweigEintrag);
		if (nf[ai].typ == 'user') {
			if (ai == nf.laenge) {
				fr.write("&nbsp;&nbsp;<IMG SRC=\"" + tree_img_dir + "kreuzungend.gif\" class=\"treeKreuz\">");
			} else {
				fr.write("&nbsp;&nbsp;<IMG SRC=\"" + tree_img_dir + "kreuzung.gif\" class=\"treeKreuz\">");
			}
			if (nf[ai].name != -1) {
				fr.write("<a name='_" + nf[ai].name + "' href=\"javascript:doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\" BORDER=\"0\">");
			}
			fr.write("<IMG SRC=\"" + tree_img_dir + nf[ai].icon + "\" alt=\"" + g_l.tree_edit_statustext + "\">");
			fr.write("</a>");

			if (nf[ai].checked) {
				checkpic = "check1.gif";
			} else {
				checkpic = "check0.gif";
			}

			fr.write("<a href=\"javascript:top.check('" + nf[ai].name + '&' + nf[ai].text + "')\"><img src=\"" + tree_img_dir + checkpic + "\" \" alt=\"\" name=\"img_" + nf[ai].name + "\" /></a>");
			fr.write("&nbsp;<a name='_" + nf[ai].name + "' href=\"javascript:top.check('" + nf[ai].name + '&' + nf[ai].text + "')\"><span id=\"" + nf[ai].name + '&' + nf[ai].text + "\" class=\"u_tree_entry\">" + (parseInt(nf[ai].published) ? " <b>" : "") + nf[ai].text + (parseInt(nf[ai].published) ? " </b>" : "") + "</span></A>&nbsp;&nbsp;<br/>\n");
		} else {
			var newAst = zweigEintrag;

			var zusatz = (ai == nf.laenge) ? "end" : "";
			var zusatz2 = "";
			if (nf[ai].offen === 0) {
				fr.write("&nbsp;&nbsp;<A href=\"javascript:top.openClose('" + nf[ai].name + "',1)\"><IMG SRC=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + g_l.tree_open_statustext + "\"></A>");
			} else {
				fr.write("&nbsp;&nbsp;<A href=\"javascript:top.openClose('" + nf[ai].name + "',0)\"><IMG SRC=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + g_l.tree_close_statustext + "\"></A>");
				zusatz2 = "open";
			}
			fr.write("<a name='_" + nf[ai].name + "' href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");
			fr.write("<IMG SRC=\"" + tree_icon_dir + "usergroup" + zusatz2 + ".gif\" alt=\"" + g_l.tree_edit_statustext + "\">");
			fr.write("</a>");
			fr.write("<A name='_" + nf[ai].name + "' HREF=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ",'" + nf[ai].contentType + "','" + nf[ai].table + "');return true;\">");
			fr.write("&nbsp;<b>" + nf[ai].text + "</b>");
			fr.write("</a>");
			fr.write("&nbsp;&nbsp;<br/>\n");
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


function makeNewEntry(icon, id, pid, txt, offen, ct, tab, pub) {

	if (table == tab) {
		if (menuDaten[indexOfEntry(pid)]) {
			if (ct == "folder") {
				menuDaten.addSort(new dirEntry(icon, id, pid, txt, offen, ct, tab));
			} else {
				menuDaten.addSort(new urlEntry(icon, id, pid, txt, ct, tab, pub));
			}
			drawEintraege();
		}
	}
}


function updateEntry(id, pid, text, pub) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ === 'folder') || (menuDaten[ai].typ === 'user')) {
			if (menuDaten[ai].name == id) {
				menuDaten[ai].vorfahr = pid;
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
		if ((menuDaten[ai].typ == 'folder') || (menuDaten[ai].typ == 'user'))
			if (menuDaten[ai].name == id) {
				ind = ai;
				break;
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
	drawEintraege();
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == 'root') || (menuDaten[ai].typ == 'folder')) {
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
	this.typ = 'root';
	this.rootstat = rootstat;
	return this;
}

function dirEntry(icon, name, vorfahr, text, offen, contentType, table) {
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
	return this;
}

function urlEntry(icon, name, vorfahr, text, contentType, table, published, checked) {
	this.icon = icon;
	this.name = name;
	this.vorfahr = vorfahr;
	this.text = text;
	this.typ = 'user';
	this.contentType = contentType;
	this.table = table;
	this.published = published;
	this.checked = checked;
	return this;
}
