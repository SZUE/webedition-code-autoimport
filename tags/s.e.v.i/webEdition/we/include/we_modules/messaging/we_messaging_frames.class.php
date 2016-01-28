<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//TODO: make weMessagingIconbar.class with weMsgIcobnbar.class and weTodoIconbar.class

class we_messaging_frames extends we_modules_frame{
	var $db;
	var $View;
	var $frameset;
	public $transaction;
	public $weTransaction;
	protected $messaging;
	public $viewclass;
	public $module = "messaging";
	protected $hasIconbar = true;
	protected $useMainTree = false;
	protected $treeDefaultWidth = 204;

	const TYPE_MESSAGE = 1;
	const TYPE_TODO = 2;

	public function __construct($viewclass, $reqTransaction, &$weTransaction){
		parent::__construct(WE_MESSAGING_MODULE_DIR . "edit_messaging_frameset.php");

		$this->transaction = $reqTransaction;
		$this->weTransaction = &$weTransaction;
		$this->viewclass = $viewclass;
		$this->View = new we_messaging_view(WE_MESSAGING_MODULE_DIR . "edit_messaging_frameset.php", "top.content", $this->transaction, $this->weTransaction);
	}

	function getHTML($what){
		switch($what){
			default:
				return parent::getHTML($what);
			case "msg_fv_headers":
				return $this->getHTMLFvHeaders();
			case 'iconbar':
				return $this->getHTMLIconbar();
		}
		exit();
	}

	function getJSCmdCode(){
		return $this->View->getJSTop_tmp();
	}

	function getJSTreeCode(){ //TODO: move to new class weUsersTree (extends weModulesTree)
		//TODO: title nach View->getJSTop()
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		$jsOut = '
var loaded = 0;
var hot = 0;
var multi_select = 0;
var startloc=0;
loaded_thr = 2;
load_state = 0;
loaded = false;
deleteMode = false;
entries_selected = new Array();
del_parents = new Array();
open_folder = -1;
viewclass ="message";
mode = "show_folder_content";

parent.document.title = "' . $title . '";
we_transaction = "' . $this->transaction . '";

check0_img = new Image();
check1_img = new Image();
check0_img.src = "' . TREE_IMAGE_DIR . 'check0.gif";
check1_img.src = "' . TREE_IMAGE_DIR . 'check1.gif";

// message folders
f1_img = new Image();
f3_img = new Image();
f5_img = new Image();

f1_o_img = new Image();
f3_o_img = new Image();
f5_o_img = new Image();

f1_img.src = "' . TREE_ICON_DIR . 'msg_folder.gif";
f3_img.src = "' . TREE_ICON_DIR . 'msg_in_folder.gif";
f5_img.src = "' . TREE_ICON_DIR . 'msg_sent_folder.gif";

f1_o_img.src = "' . TREE_ICON_DIR . 'msg_folder_open.gif";
f3_o_img.src = "' . TREE_ICON_DIR . 'msg_in_folder_open.gif";
f5_o_img.src = "' . TREE_ICON_DIR . 'msg_sent_folder_open.gif";

// todo folders
tf1_img = new Image();
tf3_img = new Image();
tf13_img = new Image();
tf11_img = new Image();

tf1_o_img = new Image();
tf3_o_img = new Image();
tf13_o_img = new Image();
tf11_o_img = new Image();

tf1_img.src = "' . TREE_ICON_DIR . 'todo_folder.gif";
tf3_img.src = "' . TREE_ICON_DIR . 'todo_in_folder.gif";
tf13_img.src = "' . TREE_ICON_DIR . 'todo_done_folder.gif";
tf11_img.src = "' . TREE_ICON_DIR . 'todo_reject_folder.gif";

tf1_o_img.src = "' . TREE_ICON_DIR . 'todo_folder_open.gif";
tf3_o_img.src = "' . TREE_ICON_DIR . 'todo_in_folder_open.gif";
tf13_o_img.src = "' . TREE_ICON_DIR . 'todo_done_folder_open.gif";
tf11_o_img.src = "' . TREE_ICON_DIR . 'todo_reject_folder_open.gif";

function check(img) {
	var i;
	var tarr = img.split("_");
	var id = tarr[1];
	for (i = 1; i <= menuDaten.laenge; i++) {
		if (menuDaten[i].name == id) {
			if (menuDaten[i].checked) {
				if (left.document.images) {
					if (left.document.images[img]) {
						left.document.images[img].src = check0_img.src;
					}
				}
				menuDaten[i].checked = false;
				unSelectMessage(img, "elem", "", 1);
				break;
			}
			else {
				if (left.document.images) {
					if (left.document.images[img]) {
						left.document.images[img].src = check1_img.src;
					}
				}
				menuDaten[i].checked = true;
				doSelectMessage(img, "elem", "", 1);
				break;
			}
		}
	}
	if (!left.document.images) {
		drawEintraege();
	}
}

function cb_incstate() {
	load_state++;
	if (!loaded && load_state >= loaded_thr) {
		loaded = true;
		loadData();
		';

		if(($param = we_base_request::_(we_base_request::INT, 'msg_param'))){
			switch($param){
				case self::TYPE_TODO:
					$f = $this->messaging->get_inbox_folder('we_todo');
					break;
				case self::TYPE_MESSAGE:
					$f = $this->messaging->get_inbox_folder('we_message');
					break;
			}
			$jsOut .= '
			r_tree_open(' . $f['ID'] . ');
			';
		}
		if(isset($f)){
			$jsOut .= '
		we_cmd("show_folder_content", ' . $f['ID'] . ');
			';
		} else {
			$jsOut .= '
		drawEintraege();
			';
		}

		$jsOut .= '
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
		}
		else {
			ent_str = "";
		}
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=update_msgs" + ent_str;
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
	if (menuDaten[ai].name == name)
		return ai;
	ai++;
}
return -1;
}

function set_frames(vc) {
if (vc == "message") {
	top.content.iconbar.location = "' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=iconbar&viewclass=" + vc;
	//top.content.iconbar.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_iconbar.php?we_transaction=' . $this->transaction . '";
	top.content.editor.edheader.location = "' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=edheader&viewclass=" + vc;
	top.content.editor.edbody.messaging_fv_headers.location="' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=msg_fv_headers&viewclass=" + vc;
}
else if (vc == "todo") {
	top.content.iconbar.location = "' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=iconbar&viewclass=" + vc;
	//top.content.iconbar.location = "' . WE_MESSAGING_MODULE_DIR . 'todo_iconbar.php?we_transaction=' . $this->transaction . '";
	top.content.editor.edheader.location = "' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=edheader&viewclass=" + vc;
	top.content.editor.edbody.messaging_fv_headers.location="' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=msg_fv_headers&viewclass=" + vc;
}
viewclass= vc;
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
var url = "' . WEBEDITION_DIR . 'we_cmd.php?we_transaction=' . $this->transaction . '&";
for(var i = 0; i < arguments.length; i++) {
	url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
	if(i < (arguments.length - 1)) {
		url += "&";
	}
}

if(hot == "1" && arguments[0] != "messaging_start_view") {
	if(confirm("' . g_l('modules_messaging', '[save_changed_folder]') . '")) {
		top.content.editor.document.edit_folder.submit();
	} else {
		top.content.usetHot();
	}
}
switch (arguments[0]) {
	case "messaging_exit":
		if(hot != "1") {
			eval(\'top.opener.top.we_cmd("exit_modules")\');
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
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=show_folder_content&id=" + arguments[1];
		break;
	case "edit_folder":
		update_icon(arguments[1]);
		top.content.cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=edit_folder&mode=edit&fid=" + arguments[1];
		break;
	case "folder_new":
		break;
	case "messaging_new_message":
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=new_message&mode=new";
		break;
	case "messaging_new_todo":
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=new_todo";
		break;
	case "messaging_start_view":
		deleteMode = false;
		mode = "show_folder_content";
		entries_selected = new Array();
		drawEintraege();
		top.content.editor.edbody.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_work.php?we_transaction=' . $this->transaction . '";
		top.content.usetHot();
		break;
	case "messaging_new_folder":
		mode = "folder_new";
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=edit_folder&mode=new";
		break;
	case "messaging_delete_mode_on":
		deleteMode = true;
		drawEintraege();
		top.content.editor.edbody.location = "' . WE_MESSAGING_MODULE_DIR . 'messaging_delete_folders.php?we_transaction=' . $this->transaction . '";
		break;
	case "messaging_delete_folders":
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=delete_folders&folders=" + entries_selected.join(",");
		break;
	case "messaging_edit_folder":
		mode = "edit_folder";
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=edit_folder&mode=edit&fid=" + open_folder;
		break;
	case "messaging_settings":
		cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=edit_settings&mode=new";
		break;
	case "messaging_copy":
		if (editor && editor.edbody && editor.edbody.entries_selected && editor.edbody.entries_selected.length > 0) {
			cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=copy_msg&entrsel=" + editor.edbody.entries_selected.join(",");
		}
		break;
	case "messaging_cut":
		if (editor && editor.edbody && editor.edbody.entries_selected && editor.edbody.entries_selected.length > 0) {
			cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=cut_msg&entrsel=" + editor.edbody.entries_selected.join(",");
		}
		break;
	case "messaging_paste":
		top.content.cmd.location = "' . $this->frameset . '?pnt=cmd&we_transaction=' . $this->transaction . '&mcmd=paste_msg";
		break;
	default:
		for(var i = 0; i < arguments.length; i++) {
			args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
		}
		eval("top.opener.top.we_cmd("+args+")");
	}
}

function setHot() {
	hot=1;
}

function usetHot() {
	hot=0;
}

var menuDaten = new container();
var count = 0;
var folder=0;
var table="' . MESSAGES_TABLE . '";
var mode = "show_folder_content";

function drawEintraege() {
	fr = top.content.tree.window.document;//IMI: set tree indstead of left
	fr.open();
	fr.writeln("<html><head>");
	fr.writeln("' . str_replace(array('script', '"'), array('scr"+"ipt', '\''), we_html_tools::getJSErrorHandler()) . '");
	fr.writeln("<script type=\"text/javascript\"><!--");
	fr.writeln("var clickCount=0;");
	fr.writeln("var wasdblclick=0;");
	fr.writeln("var tout=null;");
	fr.writeln("function doClick(id) {");
	fr.writeln("top.content.we_cmd(top.content.mode,id);");
	fr.writeln("}");

	fr.writeln("top.content.loaded=1;//-->");
	fr.writeln("</" + "script>");
	fr.writeln(\'' . STYLESHEET_SCRIPT . '\');
	fr.writeln("</head>");
	fr.writeln("<body bgcolor=\"#F3F7FF\" link=\"#000000\" alink=\"#000000\" vlink=\"#000000\" leftmargin=5 topmargin=5 marginheight=5 marginwidth=5 >");
	fr.writeln("<table border=0 cellpadding=0 cellspacing=0 width=\"100%\"><tr><td class=\"tree\"><nobr>");

	zeichne(top.content.startloc, "");

	fr.writeln("</nobr></td></tr></table>");
	fr.writeln("</body></html>");
	fr.close();
}

function zeichne(startEntry, zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	while (ai <= nf.laenge) {
		fr.write(zweigEintrag);
		if (nf[ai].typ == "leaf_Folder") {
			if (ai == nf.laenge){
				fr.write("&nbsp;&nbsp;<IMG SRC=' . TREE_IMAGE_DIR . 'kreuzungend.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
			} else {
				fr.write("&nbsp;&nbsp;<IMG SRC=' . TREE_IMAGE_DIR . 'kreuzung.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
			}
			if (nf[ai].name != -1) {
				fr.write("<a name=\"_"+nf[ai].name+"\" href=\"javascript://\" onclick=\"doClick("+nf[ai].name+");return true;\" BORDER=0>");
			}
			if (deleteMode) {
				if(nf[ai].name != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].name + "\");"
					if(nf[ai].checked) {
						fr.write("<a href=\"" + trg + "\"><img src=\"' . TREE_IMAGE_DIR . 'check1.gif\"WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\"' . g_l('tree', '[select_statustext]') . '\" name=\"img_"+nf[ai].name+"\"></a>");
					}
					else {
						fr.write("<a href=\"" + trg + "\"><img src=\"' . TREE_IMAGE_DIR . 'check0.gif\"WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\"' . g_l('tree', '[select_statustext]') . '\" name=\"img_"+nf[ai].name+"\"></a>");
					}
				}
			} else {
				fr.write("<a name=\"_"+nf[ai].name+"\" href=\"javascript://\" onclick=\"doClick("+nf[ai].name+");return true;\" BORDER=0>");
				fr.write("<IMG SRC=' . TREE_ICON_DIR . '"+nf[ai].icon+" WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\"' . g_l('tree', '[edit_statustext]') . '\">");
				fr.write("</a>");
				trg = "doClick("+nf[ai].name+");return true;"
			}
			fr.write("&nbsp;<a name=\"_"+nf[ai].name+"\" href=\"javascript://\" onclick=\"" + trg + "\"><font color=\"black\">"+(parseInt(nf[ai].published) ? " <b>" : "")+ translate(nf[ai].text) +(parseInt(nf[ai].published) ? " </b>" : "")+ "</font></A>&nbsp;&nbsp;<br/>\n");
		} else {
			var newAst = zweigEintrag;
			var zusatz = (ai == nf.laenge) ? "end" : "";
			if (nf[ai].offen == 0) {
				fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',1)\" BORDER=0><IMG SRC=' . TREE_IMAGE_DIR . 'auf"+zusatz+".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\'' . g_l('tree', '[open_statustext]') . '\'></A>");
				var zusatz2 = "";
			} else {
				fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',0)\" BORDER=0><IMG SRC=' . TREE_IMAGE_DIR . 'zu"+zusatz+".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\'' . g_l('tree', '[close_statustext]') . '\'></A>");
				var zusatz2 = "open";
			}
			if(deleteMode) {
				if(nf[ai].name != -1) {
					trg = "javascript:top.content.check(\"img_" + nf[ai].name + "\");";
					if(nf[ai].checked) {
						fr.write("<a href=\"" + trg + "\"><img src=\'' . TREE_IMAGE_DIR . 'check1.gif\' WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\'' . g_l('tree', '[select_statustext]') . '\' name=\'img_"+nf[ai].name+"\'></a>");
					} else {
						fr.write("<a href=\"" + trg + "\"><img src=\'' . TREE_IMAGE_DIR . 'check0.gif\' WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\'' . g_l('tree', '[select_statustext]') . '\' name=\'img_"+nf[ai].name+"\'></a>");
					}
				}
			} else {
				trg = "doClick("+nf[ai].name+");return true;"
			}

			fr.write("<a name=\'_"+nf[ai].name+"\' href=\"javascript://\" onclick=\"" + trg + "\" BORDER=0>");
			fr.write("<IMG SRC=' . TREE_ICON_DIR . '" + nf[ai].icon + " WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 Alt=\'' . g_l('tree', '[edit_statustext]') . '\'>");
			fr.write("</a>");

			fr.write("<A name=\"_"+nf[ai].name+"\" HREF=\"javascript://\" onclick=\"" + trg + "\">");
			fr.write("&nbsp;" + translate(nf[ai].text));
			fr.write("</a>");
			fr.write("&nbsp;&nbsp;<br/>\n");
			if (nf[ai].offen) {
				if(ai == nf.laenge) {
					newAst = newAst + "<IMG SRC=' . TREE_IMAGE_DIR . 'leer.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
				} else {
					newAst = newAst + "<IMG SRC=' . TREE_IMAGE_DIR . 'strich2.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
				}
				zeichne(nf[ai].name,newAst);
			}
		}
		ai++;
	}
}

function translate(inp){
	if(inp.substring(0,12).toLowerCase() == "messages - ("){
		return "' . g_l('modules_messaging', '[Mitteilungen]') . ' - ("+inp.substring(12,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "task - ("){
		return "' . g_l('modules_messaging', '[ToDo]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "todo - ("){
		return "' . g_l('modules_messaging', '[ToDo]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "done - ("){
		return "' . g_l('modules_messaging', '[Erledigt]') . ' - ("+inp.substring(8,inp.length);
	}else if(inp.substring(0,12).toLowerCase() == "rejected - ("){
		return "' . g_l('modules_messaging', '[Zurueckgewiesen]') . ' - ("+inp.substring(12,inp.length);
	}else if(inp.substring(0,8).toLowerCase() == "sent - ("){
		return "' . g_l('modules_messaging', '[Gesendet]') . ' - ("+inp.substring(8,inp.length);
	}else{
		return inp;
	}

}

function updateEntry(id,pid,text,pub,redraw) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ=="parent_Folder") || (menuDaten[ai].typ=="leaf_Folder"))
			if (menuDaten[ai].name==id) {
				if (pid != -1) {
					menuDaten[ai].vorfahr=pid;
				}
			menuDaten[ai].text=text;
			if (pub != -1) {
				menuDaten[ai].published=pub;
			}
			break;
		}
		ai++;
	}
	if (redraw == 1) {
		drawEintraege();
	}
}

function deleteEntry(id) {
	var ai = 1;
	var ind=0;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ=="parent_Folder") || (menuDaten[ai].typ=="leaf_Folder"))
			if (menuDaten[ai].name==id) {
				ind=ai;
				break;
			}
		ai++;
	}
	if(ind!=0) {
		ai = ind;
		while (ai <= menuDaten.laenge-1) {
			menuDaten[ai]=menuDaten[ai+1];
			ai++;
		}
		menuDaten.laenge[menuDaten.laenge]=null;
		menuDaten.laenge--;
		drawEintraege();
	}
}

function openClose(name,status) {
	var eintragsIndex = indexOfEntry(name);
	menuDaten[eintragsIndex].offen = status;
	if(status) {
		if(!menuDaten[eintragsIndex].loaded) {
			drawEintraege();
		}
		else {
			drawEintraege();
		}
	}
	else {
		drawEintraege();
	}
}

function indexOfEntry(name) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == "root") || (menuDaten[ai].typ == "parent_Folder"))
			if (menuDaten[ai].name == name)
				return ai;
		ai++;
	}
	return -1;
}

function search(eintrag) {
	var nf = new container();
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ == "parent_Folder") || (menuDaten[ai].typ == "leaf_Folder"))
			if (menuDaten[ai].vorfahr == eintrag)
				nf.add(menuDaten[ai]);
		ai++;
	}
	return nf;
}

function container() {
	this.laenge = 0;
	this.clear=containerClear;
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
	del_parents = new Array();
	for (i = 1; i <= t.laenge; i++) {
		if (array_search(t[i].name, ids) == -1) {
			cont.add(t[i]);
		}
		else {
			del_parents = del_parents.concat(new Array(String(t[i].vorfahr)));
		}
	}
	menuDaten = cont;
}

function containerClear() {
	this.laenge =0;
}

function addSort(object) {
	this.laenge++;
	for(var i=this.laenge; i > 0; i--) {
		if(i > 1 && this[i-1].text.toLowerCase() > object.text.toLowerCase()) {
			this[i] = this[i-1];
		}
		else {
			this[i] = object;
			break;
		}
	}
}

function rootEntry(name,text,rootstat) {
	this.name = name;
	this.text = text;
	this.loaded=true;
	this.typ = "root";
	this.rootstat = rootstat;
	return this;
}

function dirEntry(icon,name,vorfahr,text,offen,contentType,table,leaf_count,iconbasename,viewclass) {
	this.icon=icon;
	this.iconbasename=iconbasename;
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

function urlEntry(icon,name,vorfahr,text,contentType,table,iconbasename,viewclass) {
	this.icon=icon;
	this.iconbasename=iconbasename;
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

function loadData() {
	menuDaten.clear();
		';

		$entries = array();
		$jsOut .= '
	startloc=0;
	menuDaten.add(new self.rootEntry("0","root","root"));
		';

		foreach($this->messaging->available_folders as $folder){
			switch($folder['obj_type']){
				case we_messaging_proto::FOLDER_INBOX:
					$iconbasename = $folder['ClassName'] === 'we_todo' ? 'todo_in_folder' : 'msg_in_folder';
					$folder['Name'] = g_l('modules_messaging', $folder['ClassName'] === 'we_todo' ? '[ToDo]' : '[Mitteilungen]');
					break;
				case we_messaging_proto::FOLDER_SENT:
					$iconbasename = 'msg_sent_folder';
					$folder['Name'] = g_l('modules_messaging', '[Gesendet]');
					break;
				case we_messaging_proto::FOLDER_DONE:
					$iconbasename = 'todo_done_folder';
					$folder['Name'] = g_l('modules_messaging', '[Erledigt]');
					break;
				case we_messaging_proto::FOLDER_REJECT:
					$iconbasename = 'todo_reject_folder';
					$folder['Name'] = g_l('modules_messaging', '[Zurueckgewiesen]');
					break;
				default:
					$iconbasename = $folder['ClassName'] === 'we_todo' ? 'todo_folder' : 'msg_folder';
					break;
			}
			if(($sf_cnt = $this->messaging->get_subfolder_count($folder['ID'])) >= 0){


				$jsOut .= '
	menuDaten.add(new dirEntry("' . $iconbasename . '.gif",' . $folder['ID'] . ',' . $folder['ParentID'] . ',"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID']) . ')",false,"parent_Folder","' . MESSAGES_TABLE . '", ' . $sf_cnt . ', "' . $iconbasename . '", "' . $folder['view_class'] . '"));
				';
			} else {
				$jsOut .= '
	menuDaten.add(new urlEntry("' . $iconbasename . '.gif",' . $folder['ID'] . ',' . $folder['ParentID'] . ',"' . $folder['Name'] . ' - (' . $this->messaging->get_message_count($folder['ID']) . ')","leaf_Folder","' . MESSAGES_TABLE . '", "' . $iconbasename . '", "' . $folder['view_class'] . '"));
				';
			}
		}
		$jsOut .= '
}

function msg_start() {
	loadData();
	drawEintraege();
}
		';
		return we_html_element::jsElement($jsOut);
	}

	function getHTMLFrameset(){
		$this->transaction = &$this->weTransaction;

		$this->messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);

		if(!$this->messaging->check_folders()){
			if(!we_messaging_messaging::createFolders($_SESSION["user"]["ID"])){
				$extraHead .= we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[cant_create_folders]'), we_message_reporting::WE_MESSAGE_ERROR));
			}
		}

		$this->messaging->init($_SESSION['weS']['we_data'][$this->transaction]);
		$this->messaging->add_msgobj('we_message', 0);
		$this->messaging->add_msgobj('we_todo', 0);
		$this->messaging->add_msgobj('we_msg_email', 0);
		$this->messaging->saveInSession($_SESSION['weS']['we_data'][$this->transaction]);

		//TODO: move to a better place: jsTop()
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		$extraHead = $this->getJSCmdCode() .
			we_html_element::jsScript(JS_DIR . 'messaging_std.js') .
			we_html_element::jsScript(JS_DIR . 'messaging_hl.js') .
			$this->getJSTreeCode() .
			we_html_element::jsElement($this->getJSStart());

		return parent::getHTMLFrameset($extraHead, '&we_transaction=' . $this->transaction);
	}

	private function getHTMLIconbar(){
		$iconbar = new we_messaging_iconbar($this);
		return $iconbar->getHTML();
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), ''), $this->View->processCommands());
	}

	function getHTMLSearch(){

	}

	protected function getHTMLEditor(){
		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&we_transaction=' . $this->transaction, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 35px; overflow: hidden;', 'width: 100%; overflow: hidden') .
				we_html_element::htmlIFrame('edbody', $this->frameset . '?pnt=edbody&we_transaction=' . $this->transaction, 'position: absolute; top: 35px; bottom: 0px; left: 0px; right: 0px; overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;')
		);

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorHeader(){
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

		$extraHead = we_html_element::jsElement('
			function doSearch() {
				top.content.cmd.location = "' . $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=cmd&mcmd=search_messages&searchterm=" + document.we_messaging_search.messaging_search_keyword.value;
			}

			function launchAdvanced() {
				new jsWindow("' . WE_MESSAGING_MODULE_DIR . 'messaging_search_advanced.php?we_transaction=' . $this->transaction . '","messaging_search_advanced",-1,-1,300,240,true,false,true,false);
			}

			function clearSearch() {
				document.we_messaging_search.messaging_search_keyword.value = "";
				doSearch();
			}
		') . we_html_element::jsScript(JS_DIR . 'windows.js');

		$searchlabel = $this->viewclass === 'todo' ? '[search_todos]' : '[search_messages]';
		$hidden = we_html_tools::hidden('we_transaction', $this->transaction);
		$table = new we_html_table(array('style' => 'margin: 4px 0px 0px 7px;', 'border' => 0), 1, 2);

		$table->setCol(0, 0, array('class' => 'defaultfont'), g_l('modules_messaging', $searchlabel) .
			we_html_tools::getPixel(10, 1) .
			we_html_tools::htmlTextInput('messaging_search_keyword', 15, we_base_request::_(we_base_request::RAW, 'messaging_search_keyword', ''), 15) .
			we_html_tools::getPixel(10, 1)
		);

		$buttons = we_html_button::create_button_table(array(
				we_html_button::create_button("search", "javascript:doSearch();"),
				we_html_button::create_button("advanced", "javascript:launchAdvanced()", true),
				we_html_button::create_button("reset_search", "javascript:clearSearch();")), 10);

		$table->setCol(0, 1, array('class' => 'defaultfont'), $buttons);
		$form = we_html_element::htmlForm(
				array('name' => 'we_messaging_search', 'action' => $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=edheader&viewclass=' . $this->viewclass, 'onSubmit' => 'return doSearch()'), $hidden . $table->getHtml()
		);

		return $this->getHTMLDocument(we_html_element::htmlBody($attribs = array('background' => IMAGE_DIR . 'msg_white_bg.gif'), we_html_element::htmlNobr($form)), $extraHead);
	}

	protected function getHTMLEditorBody(){

		$frameset = new we_html_frameset(array("framespacing" => 0, "border" => 0, "frameborder" => "no"));
		$frameset->setAttributes(array("rows" => "26,1,*", 'framespacing' => 0, 'border' => 0, 'frameborder' => 'NO'));
		$frameset->addFrame(array("src" => $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=msg_fv_headers', 'name' => 'messaging_fv_headers', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array("src" => HTML_DIR . 'msg_white_fr.html', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array("src" => WE_MESSAGING_MODULE_DIR . 'messaging_mfv.php', "name" => "msg_mfv", "noresize" => null, "scrolling" => "no"));

		$noframeset = new we_html_baseElement("noframes");

		return $this->getHTMLDocument($frameset->getHtml() . $noframeset->getHTML());
	}

	function getHTMLFvHeaders(){

		$this->transaction = $this->transaction != 'no_request' ? $this->transaction : $this->weTransaction;
		$this->transaction = (preg_match('|^([a-f0-9]){32}$|i', $this->transaction) ? $this->transaction : 0);

		$extraHead = we_html_element::jsElement('
			function doSort(sortitem) {
				entrstr = "";
				top.content.cmd.location = "' . $this->frameset . '?pnt=cmd&mcmd=show_folder_content&sort=" + sortitem + entrstr + "&we_transaction=' . $this->transaction . '";
			}') .
			we_html_element::cssElement('.defaultfont a {color:black; text-decoration:none}
		');

		$colsArray = we_base_request::_(we_base_request::STRING, "viewclass") != "todo" ? array(
			array(200, 'subject', '[subject]'),
			array(170, 'date', '[date]'),
			array(120, 'sender', '[from]'),
			array(70, 'isread', '[is_read]'),
			) : array(
			array(200, 'subject', '[subject]'),
			array(170, 'deadline', '[deadline]'),
			array(120, 'priority', '[priority]'),
			array(70, 'status', '[status]'),
		);

		$table = new we_html_table(array(
			'style' => 'margin: 5px 0 0 0px',
			'border' => 0,
			'cellpadding' => 0,
			'cellspacing' => 0,
			'width' => '100%'), 1, count($colsArray) + 1);

		$table->setCol(0, 0, array('width' => 18), we_html_tools::pPixel(18, 1));
		for($i = 0; $i < count($colsArray); $i++){
			$table->setCol(0, $i + 1, array('class' => 'defaultfont', 'width' => $colsArray[$i][0]), '<a href="javascript:doSort(\'' . $colsArray[$i][1] . '\');"><b>' . g_l('modules_messaging', $colsArray[$i][2]) .
				'</b>&nbsp;' . (we_base_request::_(we_base_request::STRING, "si") == $colsArray[$i][1] ? self::sort_arrow("arrow_sortorder_" . we_base_request::_(we_base_request::STRING, 'so'), "") : we_html_tools::getPixel(1, 1)) . '</a>'
			);
		}

		return $this->getHTMLDocument(we_html_element::htmlBody($attribs = array('background' => IMAGE_DIR . 'backgrounds/header_with_black_line.gif'), $table->getHTML()), $extraHead);
	}

	protected function getHTMLEditorFooter(){

	}

	//some utility functions
	public static function sort_arrow($name, $href){
		$_image_path = IMAGE_DIR . 'modules/messaging/' . $name . '.gif';

		// Check if we have to create a form or href
		return $href ? '<a href="' . $href . '"><img src="' . $_image_path . '" border="0" alt="" /></a>' :
			'<input type="image" src="' . $_image_path . '" border="0" alt="" />';
	}

}
