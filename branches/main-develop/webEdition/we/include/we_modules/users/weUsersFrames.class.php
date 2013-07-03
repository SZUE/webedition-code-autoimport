<?php

/**
 * webEdition CMS
 *
 * $Rev: 6072 $
 * $Author: lukasimhof $
 * $Date: 2013-04-30 15:32:40 +0200 (Di, 30 Apr 2013) $
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weUsersFrames extends weModuleFrames {

	var $db;
	var $View;
	var $frameset;

	public $module = "users";
	protected $useMainTree = false;
	protected $treeFooterHeight = 40;
	protected $treeDefaultWidth = 204;

	function __construct($frameset){
		parent::__construct(WE_USERS_MODULE_DIR . "edit_users_frameset.php");
		$this->View = new weUsersView(WE_USERS_MODULE_DIR . "edit_users_frameset.php", "top.content");
	}

	function getHTMLLeftDiv(){
		return parent::getHTMLLeftDiv(true);
	}

	function getJSCmdCode(){

		return $this->View->getJSTop_tmp();
		//. we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}

	function getJSTreeCode(){ //TODO: move to new class weUsersTree
		$jsCode = '
var menuDaten = new container();
var count = 0;
var folder=0;
var table="' . USER_TABLE . '";

function drawEintraege() {
	fr = top.content.tree.window.document;
	fr.open();
	fr.charset = "' . DEFAULT_CHARSET . '";
	fr.writeln("<HTML><HEAD>' . addslashes(we_html_element::htmlMeta(array('http-equiv' => 'content-type', 'content' => 'text/html; charset=' . DEFAULT_CHARSET))) . '");
	fr.writeln("<SCRIPT type = \"text/javascript\"><!--");
	fr.writeln("clickCount=0;");
	fr.writeln("wasdblclick=0;");
	fr.writeln("tout=null");
	fr.writeln("function doClick(id,ct,table){");
	fr.writeln("top.content.we_cmd(\'display_user\',id,ct,table);");
	fr.writeln("}");
	fr.writeln("top.content.loaded=1;");
	fr.writeln("//-->");
	fr.writeln("</"+"SCRIPT>");
	fr.writeln("<LINK type=\"text/css\" rel=\"styleSheet\" href=\"' . CSS_DIR . 'global.php\">");
	fr.writeln("<LINK type=\"text/css\" rel=\"styleSheet\" href=\"' . CSS_DIR . 'we_button.css\">");
	fr.write("</HEAD>");
	fr.write("<BODY BGCOLOR=\"#F3F7FF\" LINK=\"#000000\" ALINK=\"#000000\" VLINK=\"#000000\" leftmargin=5 topmargin=5 marginheight=5 marginwidth=5>\n");
	fr.write("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\"tree\">\n<NOBR>\n");
	zeichne(top.content.startloc,"");
	fr.write("</NOBR>\n</td></tr></table>\n");
	fr.write("</BODY>\n</HTML>");
	fr.close();
}

function zeichne(startEntry,zweigEintrag) {
	var nf = search(startEntry);
	var ai = 1;
	while (ai <= nf.laenge) {
		fr.write(zweigEintrag);
		if (nf[ai].typ == "user") {
			if(ai == nf.laenge)
				fr.write("&nbsp;&nbsp;<IMG SRC=' . TREE_IMAGE_DIR . 'kreuzungend.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
			else
				fr.write("&nbsp;&nbsp;<IMG SRC=' . TREE_IMAGE_DIR . 'kreuzung.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>");
			if(nf[ai].name != -1) {
				fr.write("<a name=\'_"+nf[ai].name+"\' href=\"javascript://\" onClick=\"doClick("+nf[ai].name+",\'"+nf[ai].contentType+"\',\'"+nf[ai].table+"\');return true;\" BORDER=0>");
			}
			fr.write("<IMG SRC=' . ICON_DIR . '"+nf[ai].icon+" WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 alt=\"' . g_l('tree', '[edit_statustext]') . '\">");
			fr.write("</a>");
			fr.write("&nbsp;<a name=\'_"+nf[ai].name+"\' href=\"javascript://\" onClick=\"doClick("+nf[ai].name+",\'"+nf[ai].contentType+"\',\'"+nf[ai].table+"\');return true;\"><font color=\""+((nf[ai].contentType=="alias") ? "#006DB8" : (parseInt(nf[ai].denied)?"red":"black")) +"\">"+(parseInt(nf[ai].published) ? "<b>" : "") + "<label title=\'"+nf[ai].name+"\'>" + nf[ai].text + "</label>" +(parseInt(nf[ai].published) ? "</b>" : "")+ "</font></A>&nbsp;&nbsp;<BR>\n");
		}
		else {
			var newAst = zweigEintrag;
			var zusatz = (ai == nf.laenge) ? "end" : "";

			if (nf[ai].offen == 0) {
				fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',1)\" BORDER=0><IMG SRC=' . TREE_IMAGE_DIR . 'auf"+zusatz+".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"' . g_l('tree', '[open_statustext]') . '\"></A>");
				var zusatz2 = "";
			}else {
				fr.write("&nbsp;&nbsp;<A href=\"javascript:top.content.openClose(\'" + nf[ai].name + "\',0)\" BORDER=0><IMG SRC=' . TREE_IMAGE_DIR . 'zu"+zusatz+".gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"' . g_l('tree', '[close_statustext]') . '\"></A>");
				var zusatz2 = "open";
			}
			fr.write("<a name=\'_"+nf[ai].name+"\' href=\"javascript://\" onClick=\"doClick("+nf[ai].name+",\'"+nf[ai].contentType+"\',\'"+nf[ai].table+"\');return true;\" BORDER=0>");
			fr.write("<IMG SRC=' . ICON_DIR . 'usergroup"+zusatz2+".gif WIDTH=16 HEIGHT=18 align=absmiddle BORDER=0 Alt=\"' . g_l('tree', '[edit_statustext]') . '\">");
			fr.write("</a>");
			fr.write("<A name=\'_"+nf[ai].name+"\' HREF=\"javascript://\" onClick=\"doClick("+nf[ai].name+",\'"+nf[ai].contentType+"\',\'"+nf[ai].table+"\');return true;\">");
			fr.write("&nbsp;<b><label title=\'"+nf[ai].name+"\'>" + nf[ai].text + "</label></b>");
			fr.write("</a>");
			fr.write("&nbsp;&nbsp;<BR>\n");
			if (nf[ai].offen) {
				if(ai == nf.laenge)
					newAst = newAst + "<IMG SRC=' . TREE_IMAGE_DIR . 'leer.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
				else
					newAst = newAst + "<IMG SRC=' . TREE_IMAGE_DIR . 'strich2.gif WIDTH=19 HEIGHT=18 align=absmiddle BORDER=0>";
				zeichne(nf[ai].name,newAst);
			}
		}
		ai++;
	}
}

function makeNewEntry(icon,id,pid,txt,offen,ct,tab,pub,denied) {
	if(table == tab) {
		if(ct=="folder"){
			menuDaten.addSort(new dirEntry(icon,id,pid,txt,offen,ct,tab));
		}else{
			menuDaten.addSort(new urlEntry(icon,id,pid,txt,ct,tab,pub,denied));
		}
		drawEintraege();
	}
}

function updateEntry(id,pid,text,pub,denied) {
	var ai = 1;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ=="folder") || (menuDaten[ai].typ=="user"))
			if (menuDaten[ai].name==id) {
				menuDaten[ai].vorfahr=pid;
				menuDaten[ai].text=text;
				menuDaten[ai].published=pub;
				menuDaten[ai].denied=denied;
			}
		ai++;
	}
	drawEintraege();
}

function deleteEntry(id) {
	var ai = 1;
	var ind=0;
	while (ai <= menuDaten.laenge) {
		if ((menuDaten[ai].typ=="folder") || (menuDaten[ai].typ=="user"))
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
		if ((menuDaten[ai].typ == "root") || (menuDaten[ai].typ == "folder"))
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
		if ((menuDaten[ai].typ == "folder") || (menuDaten[ai].typ == "user"))
			if (menuDaten[ai].vorfahr == eintrag){
				nf.add(menuDaten[ai]);
			}
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

function containerClear() {
	this.laenge =0;
}

function addSort(object) {
	this.laenge++;
	for(var i=this.laenge; i>0; i--) {
		if(i > 1 && this[i-1].text.toLowerCase() > object.text.toLowerCase() ) {
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

function dirEntry(icon,name,vorfahr,text,offen,contentType,table) {
	this.icon=icon;
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

function urlEntry(icon,name,vorfahr,text,contentType,table,published,denied) {
	this.icon=icon;
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
		';

		function readChilds($pid){//is this ever called?
			$db_temp = new DB_WE();
			$db_temp->query('SELECT ID,username,ParentID,Type,Permissions FROM ' . USER_TABLE . ' WHERE Type=1 AND ParentID=' . intval($pid) . " ORDER BY username ASC");
			while($db_temp->next_record()) {
				$GLOBALS['entries'][$db_temp->f("ID")]["username"] = $db_temp->f("username");
				$GLOBALS['entries'][$db_temp->f("ID")]["ParentID"] = $db_temp->f("ParentID");
				$GLOBALS['entries'][$db_temp->f("ID")]["Type"] = $db_temp->f("Type");
				$GLOBALS['entries'][$db_temp->f("ID")]["Permissions"] = substr($db_temp->f("Permissions"), 0, 1);
				if($db_temp->f("Type") == 1){
					readChilds($db_temp->f("ID"));
				}
			}
		}

		$jsCode .= '
		function loadData() {
			menuDaten.clear();
		';

		$entries = array();
		if(we_hasPerm("NEW_USER") || we_hasPerm("NEW_GROUP") || we_hasPerm("SAVE_USER") || we_hasPerm("SAVE_GROUP") || we_hasPerm("DELETE_USER") || we_hasPerm("DELETE_GROUP")){
			if(we_hasPerm("ADMINISTRATOR")){
				$parent_path = '/';
				$startloc = 0;
			} else{
				$foo = getHash('SELECT Path,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION["user"]["ID"]), $this->db);
				$parent_path = str_replace("\\", "/", dirname($foo["Path"]));
				$startloc = $foo["ParentID"];
			}

			$jsCode .= 'startloc=' . $startloc . ';';

			$this->db->query('SELECT ID,ParentID,Text,Type,Permissions,LoginDenied FROM ' . USER_TABLE . " WHERE Path LIKE '" . $this->db->escape($parent_path) . "%' ORDER BY Text ASC");

			while($this->db->next_record()) {
				if($this->db->f("Type") == 1){
					$jsCode .= "  menuDaten.add(new dirEntry('folder','" . $this->db->f("ID") . "','" . $this->db->f("ParentID") . "','" . addslashes($this->db->f("Text")) . "',false,'group','" . USER_TABLE . "',1));";
				} else{
					$p = unserialize($this->db->f("Permissions"));
					$jsCode .= "  menuDaten.add(new urlEntry('" . ($this->db->f("Type") == 2 ? 'user_alias.gif' : 'user.gif') . "','" . $this->db->f("ID") . "','" . $this->db->f("ParentID") . "','" . addslashes($this->db->f("Text")) . "','" . ($this->db->f("Type") == 2 ? 'alias' : 'user') . "','" . USER_TABLE . "','" . (isset($p["ADMINISTRATOR"]) && $p["ADMINISTRATOR"]) . "','" . $this->db->f("LoginDenied") . "'));";
				}
			}
		}

		$jsCode .= '
		}

		function start() {
			loadData();
			drawEintraege();
		}

		var startloc=0;

		self.focus();
		';

		return we_html_element::jsElement($jsCode);
	}

	function getHTMLFrameset(){//TODO: use parent as soon as userTree.class exists
		$extraHead = $this->getJSCmdCode() .
			$this->getJSTreeCode();

		$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', "onload" => "start();")
			, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
				, we_html_element::htmlExIFrame('header', self::getHTMLHeader(WE_INCLUDES_PATH .'java_menu/modules/module_menu_users.inc.php', 'users'), 'position:absolute;top:0px;height:32px;left:0px;right:0px;') .
				we_html_element::htmlIFrame('resize', $this->frameset . '?pnt=resize', 'position:absolute;top:32px;bottom:1px;left:0px;right:0px;overflow: hidden;') .
				we_html_element::htmlIFrame('cmd', $this->frameset . '?pnt=cmd', 'position:absolute;bottom:0px;height:1px;left:0px;right:0px;overflow: hidden;')
				)
		);
		return parent::getHTMLFrameset($extraHead);
	}

	function getHTMLCmd(){
		$this->View->processCommands();
	}

	/* use parent
	function getHTMLLeft(){}
	 *
	 */

	function getHTMLTreeFooter(){//TODO: js an customer anpassen oder umgekehrt!
		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "show_search"));

		$table = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "width" => 3000), 2, 1);
		$table->setCol(0, 0, array("valign" => "top"), we_html_tools::getPixel(1600, 10));
		$table->setCol(1, 0, array("nowrap" => null, "class" => "small"), we_html_element::jsElement($this->View->getJSSubmitFunction("cmd", "post")) .
			$hiddens .
			we_button::create_button_table(
				array(
					we_html_tools::htmlTextInput("keyword", 10, "", "", "", "text", "150px"),
					we_button::create_button("image:btn_function_search", "javascript:top.content.we_cmd('search',document.we_form_treefooter.keyword.value);")
				)
			)
		);

		return we_html_element::htmlForm(array("name" => "we_form_treefooter"), $table->getHtml());
	}

	function getHTMLEditor(){
		?>
		</head>
		<frameset rows="40,*,40" framespacing="0" border="0" frameborder="no">
			<frame src="<?php print $this->frameset . '?pnt=edheader&home=1'; ?>" name="edheader" noresize scrolling=no>
			<frame src="<?php print WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=mod_home&mod=users" name="properties" scrolling=auto>
			<frame src="<?php print $this->frameset . '?pnt=edfooter&home=1'; ?>" name="edfooter" scrolling=no>

		</frameset>
		<noframes>
			<body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
			</body>
		</noframes>
		<body style="background-color: orange"></body>
		</html>
		<?php
	}

	function getHTMLEditorHeader(){
		if(isset($_REQUEST['home'])){//FIXME: find one working condition
			print we_html_element::htmlBody(array('style' => 'background-color:#F0EFF0;'),'');
		} else{
			$user_object = new we_user();
			$user_object->setState($_SESSION["user_session_data"]);
			print we_html_element::htmlBody(array('style' => 'background:white url(' . IMAGE_DIR . 'backgrounds/header_with_black_line.gif); margin-top: 0; margin-left: 0;', 'onload' => 'setFrameSize()', 'onresize' => 'setFrameSize()'),
				$user_object->formHeader(isset($_REQUEST["tab"]) ? $_REQUEST["tab"] : 0));
		}
	}

	function getHTMLEditorBody(){
		$yuiSuggest = & weSuggest::getInstance();
		$user_object = new we_user();
		if(isset($_SESSION["user_session_data"])){
			$user_object->setState($_SESSION["user_session_data"]);
		}
		print $this->View->getJSProperty();

		$_content = we_html_element::htmlHidden($attribs = array("name" => "ucmd", "value" => "",)).
			we_html_element::htmlHidden($attribs = array("name" => "tab", "value" => isset($_REQUEST["tab"]) ? intval($_REQUEST["tab"]) : "",)).
			we_html_element::htmlHidden($attribs = array("name" => "oldtab", "value" => isset($_REQUEST["tab"]) ? intval($_REQUEST["tab"]) : 0,)).
			we_html_element::htmlHidden($attribs = array("name" => "perm_branch", "value" => (isset($_REQUEST["perm_branch"]) && $_REQUEST["perm_branch"]) ? oldHtmlspecialchars($_REQUEST["perm_branch"]) : 0,)).
			we_html_element::htmlHidden($attribs = array("name" => "old_perm_branch", "value" => (isset($_REQUEST["perm_branch"]) && $_REQUEST["perm_branch"]) ? oldHtmlspecialchars($_REQUEST["perm_branch"]) : 0,)).
			we_html_element::htmlHidden($attribs = array("name" => "obj_name", "value" => $user_object->Name,)).
			we_html_element::htmlHidden($attribs = array("name" => "uid", "value" => $user_object->ID,)).
			we_html_element::htmlHidden($attribs = array("name" => "ctype", "value" => isset($_REQUEST["ctype"]) ? oldHtmlspecialchars($_REQUEST["ctype"]) : "",)).
			we_html_element::htmlHidden($attribs = array("name" => "ctable", "value" => isset($_REQUEST["ctable"]) ? oldHtmlspecialchars($_REQUEST["ctable"]) : "",)).
			we_html_element::htmlHidden($attribs = array("name" => "sd", "value" => 0,));

		if($user_object){
			if(isset($_REQUEST["oldtab"]) && isset($_REQUEST["old_perm_branch"])){ // && isset($_REQUEST["old_perm_branch"]) added for 4705
				$user_object->preserveState($_REQUEST["oldtab"], $_REQUEST["old_perm_branch"]);
				$_SESSION["user_session_data"] = $user_object->getState();
			}
			if(isset($_REQUEST["seem_start_file"])){
				$_SESSION["save_user_seem_start_file"][$_REQUEST["uid"]] = $_REQUEST["seem_start_file"];
			}
			$_content .= $user_object->formDefinition(isset($_REQUEST["tab"]) ? $_REQUEST["tab"] : "", isset($_REQUEST["perm_branch"]) ? $_REQUEST["perm_branch"] : 0);
		}

		$_content .= $yuiSuggest->getYuiCss();
		$_content .= $yuiSuggest->getYuiJs();

		$_form_attribs = array(
			'name' => 'we_form',
			'method' => 'post',
			'onsubmit' => 'return false'
		);

		$_form = we_html_element::htmlForm($_form_attribs, $_content);
		print we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'loaded=1;', 'onUnload' => 'doUnload()'), $_form);
	}

	function getHTMLEditorFooter(){
		if(isset($_SESSION["user_session_data"])){
			$user_object = new we_user();
			$user_object->setState($_SESSION["user_session_data"]);
		}

		return parent::getHTMLEditorFooter('save_user');
	}

}
