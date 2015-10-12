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

var multi_select = false;
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
			if (document.getElementsByName(imgName)) {
				var tmp = document.getElementsByName(imgName)[0];
				tmp.classList.remove('fa-check-square-o');
				tmp.classList.add('fa-square-o');
			}

			unSelectMessage(img, "elem", "");
			break;
		}
		treeData[i].checked = true;
		if (document.getElementsByName(imgName)) {
			var tmp = document.getElementsByName(imgName)[0];
			tmp.classList.add('fa-check-square-o');
			tmp.classList.remove('fa-square-o');
		}
		doSelectMessage(img, "elem", "");
		break;
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

function set_frames(vc) {
	if (vc == "message" || vc == "todo") {
		top.content.iconbar.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=iconbar&viewclass=" + vc;
		top.content.editor.edheader.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=edheader&viewclass=" + vc;
		top.content.editor.edbody.messaging_fv_headers.location = we_frameset + "?we_transaction=" + we_transaction + "&pnt=msg_fv_headers&viewclass=" + vc;
	}
	viewclass = vc;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_transaction=" + we_transaction + "&";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]) +
						(i < (arguments.length - 1) ? "&" : '');
	}

	if (hot == "1" && arguments[0] != "messaging_start_view") {
		if (confirm(g_l.save_changed_folder)) {
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
			ind = indexOfEntry(arguments[1]);
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

function drawEintraege() {
	fr = top.content.tree;
	fr.innerHTML = '<div id="treetable"><nobr>' +
					zeichne(top.content.startloc, "") +
					"</nobr></div>" +
					"</body></html>";
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	ret = "";
	for (var ai = 1; ai <= nf.len; ai++) {
		ret += zweigEintrag;
		if (nf[ai].typ == "leaf_Folder") {
			ret += '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>';

			if (nf[ai].id != -1) {
				ret += '<a id="_' + nf[ai].id + '" href="javascript://" onclick="doClick(' + nf[ai].id + ');return true;">';
			}
			if (deleteMode) {
				if (nf[ai].id != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].id + "\");";
					ret += '<a href="' + trg + '"><i class="fa fa-' + (nf[ai].checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + nf[ai].id + '"></i>';
				}
			} else {
				ret += '<a id="_' + nf[ai].id + "\" href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ");return true;\">" +
								WE().util.getTreeIcon(nf[ai].contenttype, nf[ai].open) +
								"</a>";
				trg = "doClick(" + nf[ai].id + ");return true;";
			}
			ret += "<a id=\"_" + nf[ai].id + "\" href=\"javascript://\" onclick=\"" + trg + "\" style=\"color:black\">" + (parseInt(nf[ai].published) ? " <b>" : "") + translate(nf[ai].text) + (parseInt(nf[ai].published) ? " </b>" : "") + "</a><br/>";
		} else {
			var newAst = zweigEintrag;
			ret += "<a href=\"javascript:top.content.openClose('" + nf[ai].id + "',1)\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open ? "minus" : "plus") + "-square-o fa-stack-1x'></i></span></a>";
			if (deleteMode) {
				if (nf[ai].id != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].id + "\");";
					ret += "<a href=\"" + trg + "\"><i class=\"fa fa-" + (nf[ai].checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + nf[ai].id + '"></i></a>';
				}
			} else {
				trg = "doClick(" + nf[ai].id + ");return true;";
			}

			ret += "<a id='_" + nf[ai].id + "' href=\"javascript://\" onclick=\"" + trg + "\">" +
							WE().util.getTreeIcon(nf[ai].contenttype, nf[ai].open) +
							"</a>" +
							"<a id=\"_" + nf[ai].id + "\" href=\"javascript://\" onclick=\"" + trg + "\">" +
							"" + translate(nf[ai].text) + "</a>" +
							"<br/>";
			if (nf[ai].open) {
				if (ai == nf.len) {
					newAst += "<span class=\"treeKreuz\"></span>";
				} else {
					newAst += '<span class="strich treeKreuz "></span>';
				}
				ret += zeichne(nf[ai].id, newAst);
			}
		}
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
		if (!WE().util.in_array(t[i].id, ids)) {
			cont.add(t[i]);
		} else {
			del_parents = del_parents.concat([String(t[i].parentid)]);
		}
	}
	treeData = cont;
}

function rootEntry(id, text, rootstat) {
	return new node({
		id: id,
		text: text,
		loaded: true,
		typ: 'root',
		rootstat: rootstat,
	});
}

function msg_start() {
	loadData();
	drawEintraege();
}

function doClick(id) {
	top.content.we_cmd(top.content.mode, id);
}

function loadData() {
	treeData.clear();
	startloc = 0;
	treeData.add(self.rootEntry("0", "root", "root"));
}

function translate(inp) {
	if (inp.substring(0, 12).toLowerCase() == "messages - (") {
		return g_l.Mitteilungen + " - (" + inp.substring(12, inp.length);
	} else if (inp.substring(0, 8).toLowerCase() == "task - (" || inp.substring(0, 8).toLowerCase() == "todo - (") {
		return g_l.ToDo + " - (" + inp.substring(8, inp.length);
	} else if (inp.substring(0, 8).toLowerCase() == "done - (") {
		return g_l.Erledigt + " - (" + inp.substring(8, inp.length);
	} else if (inp.substring(0, 12).toLowerCase() == "rejected - (") {
		return g_l.Zurueckgewiesen + " - (" + inp.substring(12, inp.length);
	} else if (inp.substring(0, 8).toLowerCase() == "sent - (") {
		return g_l.Gesendet + " - (" + inp.substring(8, inp.length);
	}
	return inp;
}