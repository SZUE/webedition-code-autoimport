/* global WE, top, treeData, container, we_transaction, node */

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
'use strict';

var multi_select = false,
	mode = "show_folder_content",
	deleteMode = false,
	entries_selected = [],
	del_parents = [],
	open_folder = -1,
	viewclass = "message",
	mode = "show_folder_content";

function check(img) {
	var i, tmp;
	var tarr = img.split("_");
	var id = tarr[1];
	for (i = 1; i <= treeData.len; i++) {
		if (treeData[i].id != id) {
			continue;
		}
		if (treeData[i].checked) {
			treeData[i].checked = false;
			if (document.getElementsByName(img)) {
				tmp = document.getElementsByName(img)[0];
				tmp.classList.remove('fa-check-square-o');
				tmp.classList.add('fa-square-o');
			}

			unSelectMessage(img, "");
			break;
		}
		treeData[i].checked = true;
		if (document.getElementsByName(img)) {
			tmp = document.getElementsByName(img)[0];
			tmp.classList.add('fa-check-square-o');
			tmp.classList.remove('fa-square-o');
		}
		doSelectMessage(img, "");
		break;
	}
}


function r_tree_open(id) {
	var ind = treeData.indexOfEntry(id);
	if (ind != -1) {
		treeData[ind].open = 1;
		if (treeData[ind].parentid >= 1) {
			r_tree_open(treeData[ind].parentid);
		}
	}
}

function update_messaging() {
	var ent_str;
	if (!deleteMode && (mode == "show_folder_content")) {
		if (top.content.editor.edbody.entries_selected && top.content.editor.edbody.entries_selected.length > 0) {
			ent_str = "&entrsel=" + top.content.editor.edbody.entries_selected.join(",");
		} else {
			ent_str = "";
		}
		window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=update_msgs" + ent_str;
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
		top.content.iconbar.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&we_transaction=" + we_transaction + "&pnt=iconbar&viewclass=" + vc;
		top.content.editor.edheader.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&we_transaction=" + we_transaction + "&pnt=edheader&viewclass=" + vc;
		top.content.editor.edbody.messaging_fv_headers.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&we_transaction=" + we_transaction + "&pnt=msg_fv_headers&viewclass=" + vc;
	}
	viewclass = vc;
}

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	/*jshint validthis:true */
	var caller = (this && this.window === this ? this : window);
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args, WE().consts.dirs.WEBEDITION_DIR + "we_cmd.php?we_transaction=" + we_transaction + "&");

	if (hot && args[0] !== "messaging_start_view") {
		if (window.confirm(WE().consts.g_l.messaging.save_changed_folder)) {
			top.content.editor.document.edit_folder.submit();
		} else {
			top.content.usetHot();
		}
	}
	var ind;
	switch (args[0]) {
		case "messaging_exit":
			if (!hot) {
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
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=show_folder_content&id=" + args[1];
			break;
		case "edit_folder":
			update_icon(args[1]);
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=edit&fid=" + args[1];
			break;
		case "folder_new":
			break;
		case "messaging_new_message":
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=new_message&mode=new";
			break;
		case "messaging_new_todo":
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=new_todo";
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
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=new";
			break;
		case "messaging_delete_mode_on":
			deleteMode = true;
			drawTree();
			top.content.editor.edbody.location = WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_delete_folders.php?we_transaction=" + we_transaction;
			break;
		case "messaging_delete_folders":
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=delete_folders&folders=" + entries_selected.join(",");
			break;
		case "messaging_edit_folder":
			mode = "edit_folder";
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_folder&mode=edit&fid=" + open_folder;
			break;
		case "messaging_settings":
			window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=edit_settings&mode=new";
			break;
		case "messaging_copy":
			if (window.editor && window.editor.edbody && window.editor.edbody.entries_selected && window.editor.edbody.entries_selected.length > 0) {
				window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=copy_msg&entrsel=" + window.editor.edbody.entries_selected.join(",");
			}
			break;
		case "messaging_cut":
			if (window.editor && window.editor.edbody && window.editor.edbody.entries_selected && window.editor.edbody.entries_selected.length > 0) {
				window.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=cut_msg&entrsel=" + window.editor.edbody.entries_selected.join(",");
			}
			break;
		case "messaging_paste":
			top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&we_transaction=" + we_transaction + "&mcmd=paste_msg";
			break;
		default:
			top.we_cmd.apply(caller, Array.prototype.slice.call(arguments));
	}
}

function drawTree() {
	top.content.document.getElementById("treetable").innerHTML = treeData.draw(treeData.startloc, "");
}

container.prototype.drawGroup = function (nf, ai, zweigEintrag) {
	var newAst = zweigEintrag,
		trg;
	var ret = "<span onclick=\"top.content.treeData.openClose('" + nf[ai].id + "',1)\" class='treeKreuz fa-stack " + (ai == nf.len ? "kreuzungend" : "kreuzung") + "'><i class='fa fa-square fa-stack-1x we-color'></i><i class='fa fa-caret-" + (nf[ai].open ? "down" : "right") + " fa-stack-1x'></i></span>";
	if (deleteMode) {
		if (nf[ai].id != -1) {
			trg = "top.content.check('img_" + nf[ai].id + "');";
			ret += "<i onclick=\"" + trg + "\" class=\"fa fa-" + (nf[ai].checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + nf[ai].id + '"></i>';
		}
	} else {
		trg = "doClick(" + nf[ai].id + ");return true;";
	}

	ret += "<span id='_" + nf[ai].id + "' onclick=\"" + trg + "\">" +
		WE().util.getTreeIcon(nf[ai].contenttype, nf[ai].open) +
		"</span>" +
		"<span id=\"_" + nf[ai].id + "\" onclick=\"" + trg + "\">" +
		translate(nf[ai].text) + "</span>" +
		"<br/>";
	if (nf[ai].open) {
		newAst += '<span class="' + (ai === nf.len ? 'strich ' : '') + 'treeKreuz"></span>';
		ret += this.draw(nf[ai].id, newAst);
	}
	return ret;
};

container.prototype.drawItem = function (nf, ai) {
	var ret = '<span class="treeKreuz ' + (ai == nf.len ? "kreuzungend" : "kreuzung") + '"></span>',
		trg;

	if (deleteMode) {
		if (nf[ai].id != -1) {
			trg = "top.content.check('img_" + nf[ai].id + "');";
			ret += '<i onclick="' + trg + '" class="fa fa-' + (nf[ai].checked ? 'check-' : '') + 'square-o wecheckIcon" name="img_' + nf[ai].id + '"></i>';
		}
	} else {
		ret += '<span id="_' + nf[ai].id + "\" onclick=\"doClick(" + nf[ai].id + ");\">" +
			WE().util.getTreeIcon(nf[ai].contenttype, nf[ai].open) +
			"</span>";
		trg = "doClick(" + nf[ai].id + ");";
	}
	ret += "<span id=\"_" + nf[ai].id + "\" onclick=\"" + trg + "\" style=\"color:black\">" + (parseInt(nf[ai].published) ? " <b>" : "") + translate(nf[ai].text) + (parseInt(nf[ai].published) ? " </b>" : "") + "</span><br/>";
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
};

container.prototype.search = function (eintrag) {
	var nf = new container();
	for (var ai = 1; ai <= this.len; ai++) {
		if ((this[ai].typ === "group") || (this[ai].typ === "item")) {
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
		if (treeData[ind].typ === "item") {
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
	var i = 0;
	var t = treeData;
	var cont = new container();
	del_parents = [];
	for (i = 1; i <= t.len; i++) {
		if (ids.indexOf(t[i].id) === -1) {
			cont.add(t[i]);
		} else {
			del_parents = del_parents.concat([String(t[i].parentid)]);
		}
	}
	window.treeData = cont;
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