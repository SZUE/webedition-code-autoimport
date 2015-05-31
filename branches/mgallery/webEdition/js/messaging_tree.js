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

var hot = 0;
var multi_select = 0;
var startloc = 0;
var count = 0;
var folder = 0;
var mode = "show_folder_content";

deleteMode = false;
entries_selected = [];
del_parents = [];
open_folder = -1;
viewclass = "message";
mode = "show_folder_content";

function check(img) {
	var i;
	var tarr = img.split("_");
	var id = tarr[1];
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].id != id) {
			continue;
		}
		if (treeData[i].checked) {
			if (document.images && document.images[img]) {
				document.images[img].src = tree_img_dir + "check0.gif";
			}
			treeData[i].checked = false;
			unSelectMessage(img, "elem", "");
			break;
		} else {
			if (document.images && document.images[img]) {
				document.images[img].src = tree_img_dir + "check1.gif";

			}
			treeData[i].checked = true;
			doSelectMessage(img, "elem", "");
			break;
		}
	}
}
if (!document.images) {
	drawEintraege();

}


function r_tree_open(id) {
	ind = indexOfEntry(id);
	if (ind != -1) {
		treeData[ind].open = 1;
		if (treeData[ind].parentid >= 1) {
			r_tree_open(treeData[ind].parentid);
		}
	}
}

function update_messaging() {
	if (!deleteMode && (mode == "show_folder_content")) {
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
	if (fid == open_folder) {
		return 1;
	}
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id == fid) {
			treeData[ai].icon = treeData[ai].iconbasename + "_open.gif";
			if (++s == 2) {
				break;
			}
		}
		if (treeData[ai].id == open_folder) {
			treeData[ai].icon = treeData[ai].iconbasename + ".gif";
			if (++s == 2) {
				break;
			}
		}
	}
	open_folder = fid;
	drawEintraege();
}

function get_mentry_index(id) {
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id) {
			return ai;
		}
		ai++;
	}
	return -1;
}

function set_frames(vc) {
	if (vc == "message" || vc == "todo") {
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
				top.opener.top.we_cmd("exit_modules");
			}
			break;
		case "show_folder_content":
			ind = get_mentry_index(arguments[1]);
			if (ind > -1) {
				update_icon(arguments[1]);
				if (top.content.viewclass != treeData[ind].viewclass) {
					set_frames(treeData[ind].viewclass);
				}
				top.content.viewclass = treeData[ind].viewclass;
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
			var args = [];
			for (var i = 0; i < arguments.length; i++)
			{
				args.push(arguments[i]);
			}
			top.opener.top.we_cmd.apply(this, args);
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
	while (ai <= nf.len) {
		ret += zweigEintrag;
		if (nf[ai].typ == "leaf_Folder") {
			ret += '&nbsp;&nbsp;<img src="' + tree_img_dir +
							(ai == nf.len ?
											'kreuzungend.gif' :
											'kreuzung.gif'
											) +
							'" class="treeKreuz">';

			if (nf[ai].id != -1) {
				ret += '<a id="_' + nf[ai].id + '" href="javascript://" onclick="doClick(' + nf[ai].id + ');return true;">';
			}
			if (deleteMode) {
				if (nf[ai].id != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].id + "\");";
					ret += '<a href="' + trg + '"><img src="' + tree_img_dir + (nf[ai].checked ? "check1.gif" : "check0.gif") + '" alt="' + g_l.tree_select_statustext + '" id="img_' + nf[ai].id + '"></a>';

				}
			} else {
				ret += '<a id="_' + nf[ai].id + "\" href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ");return true;\" BORDER=0>" +
								"<img src=\"" + tree_icon_dir + nf[ai].icon + "\" alt=\"" + g_l.tree_edit_statustext + "\">" +
								"</a>";
				trg = "doClick(" + nf[ai].id + ");return true;";
			}
			ret += "&nbsp;<a id=\"_" + nf[ai].id + "\" href=\"javascript://\" onclick=\"" + trg + "\"><font color=\"black\">" + (parseInt(nf[ai].published) ? " <b>" : "") + translate(nf[ai].text) + (parseInt(nf[ai].published) ? " </b>" : "") + "</font></A>&nbsp;&nbsp;<br/>";
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai == nf.len) ? "end" : "";
			var zusatz2 = "";
			if (nf[ai].open === 0) {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].id + "',1)\"><img src=\"" + tree_img_dir + "auf" + zusatz + ".gif\" class=\"treeKreuz\" alt=\"" + g_l.tree_open_statustext + "\"></A>";
			} else {
				ret += "&nbsp;&nbsp;<a href=\"javascript:top.content.openClose('" + nf[ai].id + "',0)\"><img src=\"" + tree_img_dir + "zu" + zusatz + ".gif\" class=\"treeKreuz\"alt=\"" + g_l.tree_close_statustext + "\"></A>";
				zusatz2 = "open";
			}
			if (deleteMode) {
				if (nf[ai].id != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].id + "\");";
					ret += "<a href=\"" + trg + "\"><img src=\"" + tree_img_dir +
									(nf[ai].checked ?
													"check1.gif" :
													"check0.gif"
													) +
									"\" alt=\"" + tree_select_statustext + "\" id='img_" + nf[ai].id + "'></a>";
				}
			} else {
				trg = "doClick(" + nf[ai].id + ");return true;";
			}

			ret += "<a id='_" + nf[ai].id + "' href=\"javascript://\" onclick=\"" + trg + "\">" +
							"<img src=\"" + tree_icon_dir + nf[ai].icon + "\" alt=\"" + g_l.tree_edit_statustext + "\">" +
							"</a>" +
							"<a id=\"_" + nf[ai].id + "\" href=\"javascript://\" onclick=\"" + trg + "\">" +
							"&nbsp;" + translate(nf[ai].text) + "</a>" +
							"&nbsp;&nbsp;<br/>";
			if (nf[ai].open) {
				if (ai == nf.len) {
					newAst += "<span class=\"treeKreuz\"></span>";
				} else {
					newAst += "<img src=\"" + tree_img_dir + "strich2.gif\" class=\"treeKreuz\">";
				}
				ret += zeichne(nf[ai].id, newAst);
			}
		}
		ai++;
	}
	return ret;
}

function updateEntry(id, pid, text, pub, redraw) {
	var ai = 1;
	for (ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id != id) {
			continue;
		}
		if ((treeData[ai].typ == "parent_Folder") || (treeData[ai].typ == "leaf_Folder")) {
			if (pid != -1) {
				treeData[ai].parentid = pid;
			}
			treeData[ai].text = text;
			if (pub != -1) {
				treeData[ai].published = pub;
			}
			break;
		}
	}
	if (redraw == 1) {
		drawEintraege();
	}
}

function deleteEntry(id) {
	var ind = 0;
	for (var ai = 1; ai <= treeData.len; ai++) {
		if (treeData[ai].id != id) {
			continue;
		}
		if ((treeData[ai].typ == "parent_Folder") || (treeData[ai].typ == "leaf_Folder")) {
			ind = ai;
			break;
		}
	}
	updateTreeAfterDel(ind);
}

function openClose(id, status) {
	var eintragsIndex = indexOfEntry(id);
	treeData[eintragsIndex].open = status;
	drawEintraege();
}

function indexOfEntry(id) {
	for (var ai = 1; ai <= treeData.len; ai++) {
		if ((treeData[ai].typ == "root") || (treeData[ai].typ == "parent_Folder")) {
			if (treeData[ai].id == id) {
				return ai;
			}
		}
	}
	return -1;
}

function search(eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= treeData.len; ai++) {
		if ((treeData[ai].typ == "parent_Folder") || (treeData[ai].typ == "leaf_Folder")) {
			if (treeData[ai].parentid == eintrag) {
				nf.add(treeData[ai]);
			}
		}
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

function update_Node(id) {
	var i;
	var off = -1;
	for (i = 1; i < treeData.len; i++) {
		if (treeData[i].id == id) {
			off = i;
			break;
		}
	}
}

function get_index(id) {
	var i;
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].id == id) {
			return i;
		}
	}
	return -1;
}

function folder_added(parent_id) {
	var ind = get_index(parent_id);
	if (ind > -1) {
		if (treeData[ind].typ == "leaf_Folder") {
			treeData[ind].typ = "parent_Folder";
			treeData[ind].open = 0;
			treeData[ind].leaf_count = 1;
		}
		else {
			treeData[ind].leaf_count++;
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
		treeData[ind].leaf_count--;
		if (treeData[ind].leaf_count <= 0) {
			treeData[ind].typ = "leaf_Folder";
		}
	}
}

function delete_menu_entries(ids) {
	var i, done = 0;
	var t = treeData;
	var cont = new container();
	del_parents = [];
	for (i = 1; i <= t.len; i++) {
		if (array_search(t[i].id, ids) == -1) {
			cont.add(t[i]);
		} else {
			del_parents = del_parents.concat([String(t[i].parentid)]);
		}
	}
	treeData = cont;
}

function rootEntry(id, text, rootstat) {
	this.id = id;
	this.text = text;
	this.loaded = true;
	this.typ = 'root';
	this.rootstat = rootstat;
	return this;
}

function dirEntry(icon, id, parentid, text, open, contentType, table, leaf_count, iconbasename, viewclass) {
	this.icon = icon;
	this.iconbasename = iconbasename;
	this.id = id;
	this.parentid = parentid;
	this.text = text;
	this.typ = "parent_Folder";
	this.open = (open ? 1 : 0);
	this.contentType = contentType;
	this.leaf_count = leaf_count;
	this.table = table;
	this.loaded = (open ? 1 : 0);
	this.checked = false;
	this.viewclass = viewclass;
	return this;
}

function urlEntry(icon, id, parentid, text, contentType, table, iconbasename, viewclass) {
	this.icon = icon;
	this.iconbasename = iconbasename;
	this.id = id;
	this.parentid = parentid;
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

function doClick(id) {
	top.content.we_cmd(top.content.mode, id);
}
var treeData = new container();
