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

var loaded = 0;
var hot = 0;
var multi_select = 0;
var startloc = 0;
var loaded_thr = 2;
var load_state = 0;
var menuDaten = new container();
var count = 0;
var folder = 0;
var mode = "show_folder_content";

deleteMode = false;
entries_selected = [];
del_parents = [];
open_folder = -1;
viewclass = "message";
mode = "show_folder_content";
// message folders
f1_img = new Image();
f3_img = new Image();
f5_img = new Image();
f1_o_img = new Image();
f3_o_img = new Image();
f5_o_img = new Image();
f1_img.src = tree_icon_dir + "msg_folder.gif";
f3_img.src = tree_icon_dir + "msg_in_folder.gif";
f5_img.src = tree_icon_dir + "msg_sent_folder.gif";
f1_o_img.src = tree_icon_dir + "msg_folder_open.gif";
f3_o_img.src = tree_icon_dir + "msg_in_folder_open.gif";
f5_o_img.src = tree_icon_dir + "msg_sent_folder_open.gif";
// todo folders
tf1_img = new Image();
tf3_img = new Image();
tf13_img = new Image();
tf11_img = new Image();
tf1_o_img = new Image();
tf3_o_img = new Image();
tf13_o_img = new Image();
tf11_o_img = new Image();
tf1_img.src = tree_icon_dir + "todo_folder.gif";
tf3_img.src = tree_icon_dir + "todo_in_folder.gif";
tf13_img.src = tree_icon_dir + "todo_done_folder.gif";
tf11_img.src = tree_icon_dir + "todo_reject_folder.gif";
tf1_o_img.src = tree_icon_dir + "todo_folder_open.gif";
tf3_o_img.src = tree_icon_dir + "todo_in_folder_open.gif";
tf13_o_img.src = tree_icon_dir + "todo_done_folder_open.gif";
tf11_o_img.src = tree_icon_dir + "todo_reject_folder_open.gif";

function check(img) {
	var i;
	var tarr = img.split("_");
	var id = tarr[1];
	for (i = 1; i <= menuDaten.laenge; i++) {
		if (menuDaten[i].name == id) {
			if (menuDaten[i].checked) {
				if (left.document.images) {
					if (left.document.images[img]) {
						left.document.images[img].src = tree_img_dir + "check0.gif";
					}
				}
				menuDaten[i].checked = false;
				unSelectMessage(img, "elem", "");
				break;
			}
			else {
				if (left.document.images) {
					if (left.document.images[img]) {
						left.document.images[img].src = tree_img_dir + "check1.gif";
					}
				}
				menuDaten[i].checked = true;
				doSelectMessage(img, "elem", "");
				break;
			}
		}
	}
	if (!left.document.images) {
		drawEintraege();
	}
}


function r_tree_open(id) {
	ind = indexOfEntry(id);
	if (ind != -1) {
		menuDaten[ind].offen = 1;
		if (menuDaten[ind].vorfahr >= 1) {
			r_tree_open(menuDaten[ind].vorfahr);
		}
	}
}

function update_messaging() {
	if (!deleteMode && (mode == "show_folder_content") && (load_state >= loaded_thr)) {
		if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
			ent_str = "&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
		} else {
			ent_str = "";
		}
		cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=update_msgs" + ent_str;
	}
}

function update_icon(fid) {
	var s = 0;
	var ai = 1;
	if (fid == open_folder) {
		return 1;
	}
	while (ai <= menuDaten.laenge) {
		if (menuDaten[ai].name == fid) {
			menuDaten[ai].icon = menuDaten[ai].iconbasename + "_open.gif";
			if (++s == 2) {
				break;
			}
		}
		if (menuDaten[ai].name == open_folder) {
			menuDaten[ai].icon = menuDaten[ai].iconbasename + ".gif";
			if (++s == 2) {
				break;
			}
		}
		ai++;
	}
	open_folder = fid;
	drawEintraege();
}

function get_mentry_index(name) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if (menuDaten[ai].name == name) {
			return ai;
		}
		ai++;
	}
	return -1;
}

function set_frames(vc) {
	if (vc == "message") {
		top.content.iconbar.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=iconbar&viewclass=" + vc;
		top.content.editor.edheader.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=edheader&viewclass=" + vc;
		top.content.editor.edbody.messaging_fv_headers.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=msg_fv_headers&viewclass=" + vc;
	}
	else if (vc == "todo") {
		top.content.iconbar.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=iconbar&viewclass=" + vc;
		top.content.editor.edheader.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=edheader&viewclass=" + vc;
		top.content.editor.edbody.messaging_fv_headers.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=msg_fv_headers&viewclass=" + vc;
	}
	viewclass = vc;
}

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = we_dir + "we_cmd.php?we_transaction=" + we_transaction + "&";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}

	if (hot == "1" && arguments[0] != "messaging_start_view") {
		if (confirm(save_changed_folder)) {
			top.content.editor.document.edit_folder.submit();
		} else {
			top.content.usetHot();
		}
	}
	switch (arguments[0]) {
		case "messaging_exit":
			if (hot != "1") {
				eval('top.opener.top.we_cmd("exit_modules")');
			}
			break;
		case "show_folder_content":
			ind = get_mentry_index(arguments[1]);
			if (ind > -1) {
				update_icon(arguments[1]);
				if (top.content.viewclass != menuDaten[ind].viewclass) {
					set_frames(menuDaten[ind].viewclass);
				}
				top.content.viewclass = menuDaten[ind].viewclass;
			}
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=show_folder_content&id=" + arguments[1];
			break;
		case "edit_folder":
			update_icon(arguments[1]);
			top.content.cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=edit&fid=" + arguments[1];
			break;
		case "folder_new":
			break;
		case "messaging_new_message":
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=new_message&mode=new";
			break;
		case "messaging_new_todo":
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=new_todo";
			break;
		case "messaging_start_view":
			deleteMode = false;
			mode = "show_folder_content";
			entries_selected = [];
			drawEintraege();
			top.content.editor.edbody.location = "about:blank";
			top.content.usetHot();
			break;
		case "messaging_new_folder":
			mode = "folder_new";
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=new";
			break;
		case "messaging_delete_mode_on":
			deleteMode = true;
			drawEintraege();
			top.content.editor.edbody.location = messaging_module_dir + "messaging_delete_folders.php?we_transaction=" + we_transaction;
			break;
		case "messaging_delete_folders":
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=delete_folders&folders=" + entries_selected.join(",");
			break;
		case "messaging_edit_folder":
			mode = "edit_folder";
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=edit&fid=" + open_folder;
			break;
		case "messaging_settings":
			cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_settings&mode=new";
			break;
		case "messaging_copy":
			if (editor && editor.edbody && editor.edbody.entries_selected && editor.edbody.entries_selected.length > 0) {
				cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=copy_msg&entrsel=" + editor.edbody.entries_selected.join(",");
			}
			break;
		case "messaging_cut":
			if (editor && editor.edbody && editor.edbody.entries_selected && editor.edbody.entries_selected.length > 0) {
				cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=cut_msg&entrsel=" + editor.edbody.entries_selected.join(",");
			}
			break;
		case "messaging_paste":
			top.content.cmd.location = we_frameset + "?pnt=cmd&we_transaction=" + we_transaction + "&mcmd=paste_msg";
			break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments[" + i + "]" + ((i < (arguments.length - 1)) ? "," : "");
			}
			eval("top.opener.top.we_cmd(" + args + ")");
	}
}

function setHot() {
	hot = 1;
}

function usetHot() {
	hot = 0;
}


function drawEintraege() {
	fr = top.content.tree.window.document.body; //IMI: set tree indstead of left
	fr.innerHTML = '<div id="treetable" class="tree"><nobr>' +
					zeichne(top.content.startloc, "") +
					"</nobr></div>" +
					"</body></html>";
	top.content.tree.window.loadFinished();
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	ret = "";
	while (ai <= nf.laenge) {
		ret += zweigEintrag;
		if (nf[ai].typ == "leaf_Folder") {
			ret += '&nbsp;&nbsp;<IMG SRC="' + tree_img_dir +
							(ai == nf.laenge ?
											'kreuzungend.gif' :
											'kreuzung.gif'
											) +
							'" class="treeKreuz">';

			if (nf[ai].name != -1) {
				ret += '<a name="_' + nf[ai].name + '" href="javascript://" onclick="doClick(' + nf[ai].name + ');return true;">';
			}
			if (deleteMode) {
				if (nf[ai].name != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].name + "\");";
					if (nf[ai].checked) {
						ret += '<a href="' + trg + '"><img src="' + tree_img_dir + "check1.gif\" alt=\"" + g_l.tree_select_statustext + "\" name=\"img_" + nf[ai].name + "\"></a>";
					} else {
						ret += "<a href=\"" + trg + "\"><img src=\"" + tree_img_dir + "check0.gif\" alt=\"" + g_l.tree_select_statustext + "\" name=\"img_" + nf[ai].name + "\"></a>";
					}
				}
			} else {
				ret += "<a name=\"_" + nf[ai].name + "\" href=\"javascript://\" onclick=\"doClick(" + nf[ai].name + ");return true;\" BORDER=0>" +
								"<IMG SRC=\"" + tree_icon_dir + nf[ai].icon + "\" alt=\"" + g_l.tree_edit_statustext + "\">" +
								"</a>";
				trg = "doClick(" + nf[ai].name + ");return true;";
			}
			ret += "&nbsp;<a name=\"_" + nf[ai].name + "\" href=\"javascript://\" onclick=\"" + trg + "\"><font color=\"black\">" + (parseInt(nf[ai].published) ? " <b>" : "") + translate(nf[ai].text) + (parseInt(nf[ai].published) ? " </b>" : "") + "</font></A>&nbsp;&nbsp;<br/>";
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai == nf.laenge) ? "end" : "";
			var zusatz2 = "";
			if (nf[ai].offen === 0) {
				ret += "&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',1)\"><IMG SRC=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + g_l.tree_open_statustext + "\"></A>";
			} else {
				ret += "&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',0)\"><IMG SRC=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\"alt=\"" + g_l.tree_close_statustext + "\"></A>";
				zusatz2 = "open";
			}
			if (deleteMode) {
				if (nf[ai].name != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].name + "\");";
					ret += "<a href=\"" + trg + "\"><img src=\"" + tree_img_dir +
									(nf[ai].checked ?
													"check1.gif" :
													"check0.gif"
													) +
									"\" alt=\"" + tree_select_statustext + "\" name=\'img_" + nf[ai].name + "\'></a>";
				}
			} else {
				trg = "doClick(" + nf[ai].name + ");return true;";
			}

			ret += "<a name=\'_" + nf[ai].name + "\' href=\"javascript://\" onclick=\"" + trg + "\" BORDER=0>" +
							"<IMG SRC=\"" + tree_icon_dir + nf[ai].icon + "\" alt=\"" + g_l.tree_edit_statustext + "\">" +
							"</a>" +
							"<A name=\"_" + nf[ai].name + "\" HREF=\"javascript://\" onclick=\"" + trg + "\">" +
							"&nbsp;" + translate(nf[ai].text) +
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

function updateEntry(id, pid, text, pub, redraw) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == "parent_Folder") || (menuDaten[ai].typ == "leaf_Folder")) {
			if (menuDaten[ai].name == id) {
				if (pid != -1) {
					menuDaten[ai].vorfahr = pid;
				}
				menuDaten[ai].text = text;
				if (pub != -1) {
					menuDaten[ai].published = pub;
				}
				break;
			}
		}
		ai++;
	}
	if (redraw == 1) {
		drawEintraege();
	}
}

function deleteEntry(id) {
	var ai = 1;
	var ind = 0;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == "parent_Folder") || (menuDaten[ai].typ == "leaf_Folder")) {
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
	drawEintraege();
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == "root") || (menuDaten[ai].typ == "parent_Folder")) {
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
		if ((menuDaten[ai].typ == "parent_Folder") || (menuDaten[ai].typ == "leaf_Folder")) {
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

function update_Node(id) {
	var i;
	var off = -1;
	for (i = 1; i < menuDaten.laenge; i++) {
		if (menuDaten[i].name == id) {
			off = i;
			break;
		}
	}
}

function get_index(id) {
	var i;
	for (i = 1; i <= menuDaten.laenge; i++) {
		if (menuDaten[i].name == id) {
			return i;
		}
	}
	return -1;
}

function folder_added(parent_id) {
	var ind = get_index(parent_id);
	if (ind > -1) {
		if (menuDaten[ind].typ == "leaf_Folder") {
			menuDaten[ind].typ = "parent_Folder";
			menuDaten[ind].offen = 0;
			menuDaten[ind].leaf_count = 1;
		}
		else {
			menuDaten[ind].leaf_count++;
		}
	}
}

function folders_removed() {
	var ind;
	var i;
	for (i = 0; i < del_parents.length; i++) {
		if ((ind = get_index(del_parents[i])) < 0) {
			continue;
		}
		menuDaten[ind].leaf_count--;
		if (menuDaten[ind].leaf_count <= 0) {
			menuDaten[ind].typ = "leaf_Folder";
		}
	}
}

function delete_menu_entries(ids) {
	var i, done = 0;
	var t = menuDaten;
	var cont = new container();
	del_parents = [];
	for (i = 1; i <= t.laenge; i++) {
		if (array_search(t[i].name, ids) == -1) {
			cont.add(t[i]);
		} else {
			del_parents = del_parents.concat(new Array(String(t[i].vorfahr)));
		}
	}
	menuDaten = cont;
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

function dirEntry(icon, name, vorfahr, text, offen, contentType, table, leaf_count, iconbasename, viewclass) {
	this.icon = icon;
	this.iconbasename = iconbasename;
	this.name = name;
	this.vorfahr = vorfahr;
	this.text = text;
	this.typ = "parent_Folder";
	this.offen = (offen ? 1 : 0);
	this.contentType = contentType;
	this.leaf_count = leaf_count;
	this.table = table;
	this.loaded = (offen ? 1 : 0);
	this.checked = false;
	this.viewclass = viewclass;
	return this;
}

function urlEntry(icon, name, vorfahr, text, contentType, table, iconbasename, viewclass) {
	this.icon = icon;
	this.iconbasename = iconbasename;
	this.name = name;
	this.vorfahr = vorfahr;
	this.text = text;
	this.typ = "leaf_Folder";
	this.checked = false;
	this.contentType = contentType;
	this.table = table;
	this.viewclass = viewclass;
	return this;
}

function msg_start() {
	loadData();
	drawEintraege();
}
