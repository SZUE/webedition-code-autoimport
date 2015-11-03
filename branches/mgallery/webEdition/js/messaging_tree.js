/* global WE */

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
			treeData[i].checked = false;
			if (document.getElementsByName(img)) {
				var tmp = document.getElementsByName(img)[0];
				tmp.classList.remove('fa-check-square-o');
				tmp.classList.add('fa-square-o');
			}

			unSelectMessage(img, "");
			break;
		}
		treeData[i].checked = true;
		if (document.getElementsByName(img)) {
			var tmp = document.getElementsByName(img)[0];
			tmp.classList.add('fa-check-square-o');
			tmp.classList.remove('fa-square-o');
		}
		doSelectMessage(img, "");
		break;
	}
}


function r_tree_open(id) {
	ind = treeData.indexOfEntry(id);
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
		cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=update_msgs" + ent_str;
	}
}

function update_icon(fid) {
	if (fid == open_folder) {
		return 1;
	}
	open_folder = fid;
	drawTree();
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
	var args = [];
	var url = WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_transaction=" + we_transaction + "&";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[]=" + encodeURI(arguments[i]) + (i < (arguments.length - 1) ? "&" : '');
		args.push(arguments[i]);
	}

	if (hot === 1 && args[0] != "messaging_start_view") {
		if (confirm(WE().consts.g_l.messaging.save_changed_folder)) {
			top.content.editor.document.edit_folder.submit();
		} else {
			top.content.usetHot();
		}
	}
	switch (args[0]) {
		case "messaging_exit":
			if (hot !== 1) {
				top.opener.top.we_cmd("exit_modules");
			}
			break;
		case "show_folder_content":
			ind = treeData.indexOfEntry(args[1]);
			if (ind > -1) {
				update_icon(args[1]);
				if (top.content.viewclass != treeData[ind].viewclass) {
					set_frames(treeData[ind].viewclass);
				}
				top.content.viewclass = treeData[ind].viewclass;
			}
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=show_folder_content&id=" + args[1];
			break;
		case "edit_folder":
			update_icon(args[1]);
			top.content.cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=edit&fid=" + args[1];
			break;
		case "folder_new":
			break;
		case "messaging_new_message":
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=new_message&mode=new";
			break;
		case "messaging_new_todo":
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=new_todo";
			break;
		case "messaging_start_view":
			deleteMode = false;
			mode = "show_folder_content";
			entries_selected = [];
			drawTree();
			top.content.editor.edbody.location = "about:blank";
			top.content.usetHot();
			break;
		case "messaging_new_folder":
			mode = "folder_new";
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=new";
			break;
		case "messaging_delete_mode_on":
			deleteMode = true;
			drawTree();
			top.content.editor.edbody.location = WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_delete_folders.php?we_transaction=" + we_transaction;
			break;
		case "messaging_delete_folders":
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=delete_folders&folders=" + entries_selected.join(",");
			break;
		case "messaging_edit_folder":
			mode = "edit_folder";
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=edit&fid=" + open_folder;
			break;
		case "messaging_settings":
			cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_settings&mode=new";
			break;
		case "messaging_copy":
			if (editor && editor.edbody && editor.edbody.entries_selected && editor.edbody.entries_selected.length > 0) {
				cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=copy_msg&entrsel=" + editor.edbody.entries_selected.join(",");
			}
			break;
		case "messaging_cut":
			if (editor && editor.edbody && editor.edbody.entries_selected && editor.edbody.entries_selected.length > 0) {
				cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=cut_msg&entrsel=" + editor.edbody.entries_selected.join(",");
			}
			break;
		case "messaging_paste":
			top.content.cmd.location = we_frameset + "&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=paste_msg";
			break;
		default:
			top.opener.top.we_cmd.apply(this, args);
	}
}

function drawTree() {
	top.content.document.getElementById("treetable").innerHTML = treeData.draw(treeData.startloc, "");
}

container.prototype.drawGroup = function (nf, ai, zweigEintrag) {
	var newAst = zweigEintrag;
	var ret = "<a href=\"javascript:top.content.treeData.openClose('" + nf[ai].id + "',1)\"><span class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-" + (nf[ai].open ? "minus" : "plus") + "-square-o fa-stack-1x'></i></span></a>";
	if (deleteMode) {
		if (nf[ai].id != -1) {
			trg = "javascript:top.content.check('img_" + nf[ai].id + "');";
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
		ret += this.draw(nf[ai].id, newAst);
	}
	return ret;
};

container.prototype.drawItem = function (nf, ai) {
	var ret = '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>';

	if (nf[ai].id != -1) {
		ret += '<a id="_' + nf[ai].id + '" href="javascript://" onclick="doClick(' + nf[ai].id + ');return true;">';
	}
	if (deleteMode) {
		if (nf[ai].id != -1) {
			trg = "javascript:top.content.check('img_" + nf[ai].id + "');";
			ret += '<a href="' + trg + '"><i class="fa fa-' + (nf[ai].checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + nf[ai].id + '"></i>';
		}
	} else {
		ret += '<a id="_' + nf[ai].id + "\" href=\"javascript://\" onclick=\"doClick(" + nf[ai].id + ");return true;\">" +
						WE().util.getTreeIcon(nf[ai].contenttype, nf[ai].open) +
						"</a>";
		trg = "doClick(" + nf[ai].id + ");return true;";
	}
	ret += "<a id=\"_" + nf[ai].id + "\" href=\"javascript://\" onclick=\"" + trg + "\" style=\"color:black\">" + (parseInt(nf[ai].published) ? " <b>" : "") + translate(nf[ai].text) + (parseInt(nf[ai].published) ? " </b>" : "") + "</a><br/>";
	return ret;
};

container.prototype.updateEntry = function (id, pid, text, pub, redraw) {
	var ai = 1;
	for (ai = 1; ai <= this.len; ai++) {
		if (this[ai].id != id) {
			continue;
		}
		if ((this[ai].typ == "group") || (this[ai].typ == "item")) {
			if (pid != -1) {
				this[ai].parentid = pid;
			}
			this[ai].text = text;
			if (pub != -1) {
				this[ai].published = pub;
			}
			break;
		}
	}
	if (redraw == 1) {
		drawTree();
	}
};

container.prototype.openClose = function (id, status) {
	var eintragsIndex = treeData.indexOfEntry(id);
	treeData[eintragsIndex].open = status;
	drawTree();
}

container.prototype.search = function (eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= this.len; ai++) {
		if ((this[ai].typ == "group") || (this[ai].typ == "item")) {
			if (this[ai].parentid == eintrag) {
				nf.add(this[ai]);
			}
		}
	}
	return nf;
};

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
		if (treeData[ind].typ == "item") {
			treeData[ind].typ = "group";
			treeData[ind].open = 0;
			treeData[ind].leaf_count = 1;
		} else {
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
			treeData[ind].typ = "item";
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

function msg_start() {
	loadData();
	drawTree();
}

function doClick(id) {
	top.content.we_cmd(top.content.mode, id);
}

function loadData() {
	treeData.clear();
	treeData.startloc = 0;
	treeData.add(node.prototype.rootEntry(0, "root", "root"));
}

function translate(inp) {
	if (inp.substring(0, 12).toLowerCase() === "messages - (") {
		return WE().consts.g_l.messaging.Mitteilungen + " - (" + inp.substring(12, inp.length);
	} else if (inp.substring(0, 8).toLowerCase() === "task - (" || inp.substring(0, 8).toLowerCase() === "todo - (") {
		return WE().consts.g_l.messaging.ToDo + " - (" + inp.substring(8, inp.length);
	} else if (inp.substring(0, 8).toLowerCase() === "done - (") {
		return WE().consts.g_l.messaging.Erledigt + " - (" + inp.substring(8, inp.length);
	} else if (inp.substring(0, 12).toLowerCase() === "rejected - (") {
		return WE().consts.g_l.messaging.Zurueckgewiesen + " - (" + inp.substring(12, inp.length);
	} else if (inp.substring(0, 8).toLowerCase() === "sent - (") {
		return WE().consts.g_l.messaging.Gesendet + " - (" + inp.substring(8, inp.length);
	}
	return inp;
}