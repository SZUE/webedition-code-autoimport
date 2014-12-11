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
abstract class we_selector_file{
	const FRAMESET = 0;
	const HEADER = 1;
	const FOOTER = 2;
	const BODY = 3;
	const CMD = 4;
	const WINDOW_SELECTOR_WIDTH = 900;
	const WINDOW_SELECTOR_HEIGHT = 685;
	const WINDOW_DIRSELECTOR_WIDTH = 900;
	const WINDOW_DIRSELECTOR_HEIGHT = 600;
	const WINDOW_DOCSELECTOR_WIDTH = 900;
	const WINDOW_DOCSELECTOR_HEIGHT = 685;
	const WINDOW_CATSELECTOR_WIDTH = 900;
	const WINDOW_CATSELECTOR_HEIGHT = 638;
	const WINDOW_DELSELECTOR_WIDTH = 900;
	const WINDOW_DELSELECTOR_HEIGHT = 600;

	var $dir = 0;
	var $id = 0;
	var $path = '/';
	var $lastdir = '';
	protected $table = FILE_TABLE;
	var $tableSizer = '';
	var $tableHeadlines = '';
	var $JSCommand = '';
	var $JSTextName;
	var $JSIDName;
	protected $db;
	var $sessionID = '';
	protected $fields = 'ID,ParentID,Text,Path,IsFolder,Icon';
	var $values = array();
	var $openerFormName = 'we_form';
	protected $order = 'Text';
	protected $canSelectDir = true;
	var $rootDirID = 0;
	protected $filter = '';
	var $col2js;
	protected $title = '';

	public function __construct($id, $table = FILE_TABLE, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $rootDirID = 0, $filter = ''){

		if(!isset($_SESSION['weS']['we_fs_lastDir'])){
			$_SESSION['weS']['we_fs_lastDir'] = array($table => 0);
		}

		$this->order = ($order ? : $this->order);

		$this->db = new DB_WE();
		$this->id = $id;
		$this->lastDir = isset($_SESSION['weS']['we_fs_lastDir'][$table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$table]) : 0;
//check table

		$this->table = $table;

		$this->JSIDName = $JSIDName;
		$this->JSTextName = $JSTextName;
		$this->JSCommand = $JSCommand;
		$this->rootDirID = intval($rootDirID);
		$this->filter = $filter;
		$this->setDirAndID();
		$this->setTableLayoutInfos();
	}

	protected function setDirAndID(){
		$id = $this->id;
		if($id > 0){
			// get default Directory
			$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($id));

			// getValues of selected Dir
			if($this->db->next_record()){
				$this->values = $this->db->getRecord();

				$this->dir = ($this->values['IsFolder'] ?
						$id :
						$this->values['ParentID']);

				$this->path = $this->values['Path'];
				return;
			}
		}
		$this->setDefaultDirAndID($id === 0 ? false : true);
	}

	protected function setDefaultDirAndID($setLastDir){
		$this->dir = $setLastDir ? ( isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0 ) : 0;
		$this->id = $this->dir;

		$this->path = '';

		$this->values = array(
			'ParentID' => 0,
			'Text' => '/',
			'Path' => '/',
			'IsFolder' => 1
		);
	}

	function isIDInFolder($ID, $folderID, we_database_base $db = null){
		if($folderID == $ID){
			return true;
		}
		$db = ($db ? : new DB_WE());
		$pid = f('SELECT ParentID FROM ' . $db->escape($this->table) . ' WHERE ID=' . intval($ID), '', $db);
		if($pid == $folderID){
			return true;
		}
		if($pid != 0){
			return $this->isIDInFolder($pid, $folderID);
		}
		return false;
	}

	function query(){
		$wsQuery = $this->table == NAVIGATION_TABLE && get_ws($this->table) ? ' ' . getWsQueryForSelector($this->table) : '';
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' ' .
			( ($this->filter ? ($this->table == CATEGORY_TABLE ? 'AND IsFolder = "' . $this->db->escape($this->filter) . '" ' : 'AND ContentType = "' . $this->db->escape($this->filter) . '" ') : '' ) . $wsQuery ) .
			($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : ''));
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	function next_record(){
		return $this->db->next_record();
	}

	function f($key){
		return $this->db->f($key);
	}

	function printHTML($what = we_selector_file::FRAMESET){
		switch($what){
			case self::HEADER:
				$this->printHeaderHTML();
				break;
			case self::FOOTER:
				$this->printFooterHTML();
				break;
			case self::BODY:
				$this->printBodyHTML();
				break;
			case self::CMD:
				$this->printCmdHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	function getFramesetJavaScriptIncludes(){
		// overwrite
	}

	protected function printFramesetRootDirFn(){
		return we_html_element::jsElement('function setRootDir(){	setDir(0);}');
	}

	function getExitClose(){
		return we_html_element::jsElement('
function exit_close(){
	if(top.opener.top.opener && top.opener.top.opener.top.toggleBusy){
		top.opener.top.opener.top.toggleBusy();
	}else if(top.opener.top.toggleBusy){
		top.opener.top.toggleBusy();
	}
	self.close();
}');
	}

	private function getJS_keyListenerFunctions(){
		return we_html_element::jsElement('
function applyOnEnter(evt) {
	_elemName = "target";
	if ( typeof(evt["srcElement"]) != "undefined" ) { // IE
		_elemName = "srcElement";
	}

	if (	!( evt[_elemName].tagName == "SELECT" ||
			 ( evt[_elemName].tagName == "INPUT" && evt[_elemName].name != "fname" )
		) ) {
		top.fsfooter.press_ok_button();
		return true;
	}

}
function closeOnEscape() {
	top.exit_close();

}');
	}

	function printFramesetHTML(){
		$this->setDirAndID();//set correct directory
		echo we_html_tools::getHtmlTop($this->title, '', 'frameset') .
		implodeJS(
			we_html_element::jsScript(JS_DIR . 'keyListener.js') .
			$this->getFramesetJavaScriptIncludes() .
			we_html_element::jsElement('var weSelectorWindow = true;') .
			$this->getFramesetJavaScriptDef() .
			$this->getJS_keyListenerFunctions() .
			$this->getExitClose() .
			we_html_element::jsElement('
function in_array(needle,haystack){
	for(var i=0;i<haystack.length;i++){
		if(haystack[i] == needle) return true;
	}
	return false;
}') .
			$this->getExitOpen() .
			$this->printFramesetJSDoClickFn() .
			$this->printFramesetJSsetDir() .
			we_html_element::jsElement('
function orderIt(o){
	if(order == o){
		order=o+" DESC";
	}else{
		order=o;
	}
	top.fscmd.location.replace(top.queryString(' . we_selector_file::CMD . ',top.currentDir,order));
}

function goBackDir(){
	setDir(parentID);
}

function getEntry(id){
	for(var i=0;i<entries.length;i++){
		if(entries[i].ID == id) return entries[i];
	}
	return new entry(0,"","/",1,"/");
}

function cutText(text,l){
	if(text.length > l){
		return text.substring(0,l-8) + "..." + text.substring(text.length-5,text.length);
	}else{
		return text;
	}
}') .
			$this->printFramesetRootDirFn() .
			$this->printFramesetSelectFileHTML() .
			$this->printFramesetUnselectFileHTML() .
			$this->printFramesetSelectFilesFromHTML() .
			$this->printFramesetGetFirstSelectedHTML() .
			$this->printFramesetGetPositionByIDHTML() .
			$this->printFramesetIsFileSelectedHTML() .
			$this->printFramesetUnselectAllFilesHTML() .
			$this->printFramesetJSFunctions() .
			we_html_element::jsElement('self.focus();')
		);
		?>
		</head>
		<?php
		echo $this->getFrameset();
	}

	function printFramesetUnselectFileHTML(){

	}

	function printFramesetSelectFilesFromHTML(){

	}

	function printFramesetGetFirstSelectedHTML(){

	}

	function printFramesetGetPositionByIDHTML(){

	}

	function printFramesetIsFileSelectedHTML(){

	}

	function printFramesetUnselectAllFilesHTML(){

	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function selectFile(id){
	e = getEntry(id);
	top.fsfooter.document.we_form.fname.value = e.text;
	currentText = e.text;
	currentPath = e.path;
	currentID = id;
}');
	}

	function getFramesetJavaScriptDef(){
		$startPathQuery = new DB_WE();
		$startPathQuery->query('SELECT Path FROM ' . $startPathQuery->escape($this->table) . ' WHERE ID=' . intval($this->dir));
		$startPath = $startPathQuery->next_record() ? $startPathQuery->f('Path') : '/';
		if($this->id == 0){
			$this->path = '/';
		}
		return we_html_element::jsElement('
var currentID="' . $this->id . '";
var currentDir="' . $this->dir . '";
var currentPath="' . $this->path . '";
var currentText="' . (isset($this->values["Text"]) ? $this->values["Text"] : '') . '";
var currentType="' . (isset($this->filter) ? $this->filter : "") . '";

var startPath="' . $startPath . '";


var parentID=' . intval(($this->dir ?
						f('SELECT ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), 'ParentID', $this->db) :
						0)) . ';
var table="' . $this->table . '";
var order="' . $this->order . '";

var entries = new Array();

var clickCount=0;
var wasdblclick=0;
var tout=null;
var mk=null;');
	}

	protected function getFrameset(){
		return '<frameset rows="67,*,65,0" border="0">
	<frame src="' . $this->getFsQueryString(we_selector_file::HEADER) . '" name="fsheader" noresize scrolling="no">
    <frame src="' . $this->getFsQueryString(we_selector_file::BODY) . '" name="fsbody" noresize scrolling="auto">
    <frame src="' . $this->getFsQueryString(we_selector_file::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
    <frame src="about:blank"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>';
	}

	protected function getExitOpen(){
		$out = '
function exit_open(){' . ($this->JSIDName ? '
	opener.' . $this->JSIDName . '=currentID;' : '');

		if($this->JSTextName){
			$frameRef = strpos($this->JSTextName, ".document.") > 0 ? substr($this->JSTextName, 0, strpos($this->JSTextName, ".document.") + 1) : "";
			$out .= 'opener.' . $this->JSTextName . '= currentID ? currentPath : "";
					if((!!opener.parent) && (!!opener.parent.frames[0]) && (!!opener.parent.frames[0].setPathGroup)) {
							if(currentType!="")	{
								switch(currentType){
									case "noalias":
										setTabsCurPath = "@"+currentText;
										break;
									default:
										setTabsCurPath = currentPath;
								}
								if(getEntry(currentID).isFolder) opener.parent.frames[0].setPathGroup(setTabsCurPath);
								else opener.parent.frames[0].setPathName(setTabsCurPath);
								opener.parent.frames[0].setTitlePath();
							}
					}
					if(!!opener.' . $frameRef . 'YAHOO && !!opener.' . $frameRef . 'YAHOO.autocoml) {  opener.' . $frameRef . 'YAHOO.autocoml.selectorSetValid(opener.' . str_replace('.value', '.id', $this->JSTextName) . '); }
					';
		}
		$out .= ($this->JSCommand ?
				'	' . str_replace('WE_PLUS', '+', $this->JSCommand) . ';' : '') .
			'	self.close();
	}';
		return we_html_element::jsElement($out);
	}

	protected function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}
	}else{
		selectFile(id);
	}
}');
	}

	protected function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id){
	e = getEntry(id);
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	currentText = e.text;
	top.fsfooter.document.we_form.fname.value = e.text;
	top.fscmd.location.replace(top.queryString(' . we_selector_file::CMD . ',id));
}');
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . '?what='.$what.'&table=' . $this->table . '&id=' . $this->id . '&order=' . $this->order . '&filter=' . $this->filter;
	}

	protected function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
function queryString(what,id,o){
	if(!o) o=top.order;
	return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&table=' . $this->table . '&id=\'+id+"&order="+o+"&filter=' . $this->filter . '";
}');
	}

	protected static function makeWriteDoc($html){
		$html = explode("\n", strtr(implodeJS($html), array('\'' => '\\\'', 'script' => 'scr\'+\'ipt', '#\'' => '\'','#\\\'' => '\'',)));
		$ret = '';
		foreach($html as $cur){
			$ret.=(substr($cur, 0, 1) === '#' ? substr($cur, 1) : "d.writeln('" . $cur . "');");
		}
		return $ret;
	}

	protected function printFramesetJSFunctioWriteBody(){
		ob_start();
		?><script type="text/javascript"><!--
					function writeBody(d) {
				d.open();
		<?php
		echo self::makeWriteDoc(we_html_tools::getHtmlTop('', '', '4Trans', true) . STYLESHEET_SCRIPT . '
<style type="text/css">
body{
background-color:white;
margin:0px;
}
a:link,a:visited,a:hover,a:active
{color:#000;}
</style>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0">');
		?>
				for (i = 0; i < entries.length; i++) {
					d.writeln('<tr>');
					d.writeln('<td class="selector" align="center">');
					var link = '<a title="' + entries[i].text + '" href="javascript://"';
					if (entries[i].isFolder) {
						link += ' onDblClick="this.blur();top.wasdblclick=1;clearTimeout(tout);top.doClick(' + entries[i].ID + ',1);return true;"';
					}
					link += ' onclick="this.blur();tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(' + entries[i].ID + ',0);}else{top.wasdblclick=0;}\',300);return true">' + "\n";
					d.writeln(link + '<img src="<?php echo TREE_ICON_DIR; ?>' + entries[i].icon + '" width="16" height="18" border="0"></a>');
					d.writeln('</td>');
					d.writeln('<td class="selector" title="' + entries[i].text + '">');
					d.writeln(link + cutText(entries[i].text, 70) + '</a>');
					d.writeln('</td></tr>');
					d.writeln('<tr>');
					d.writeln('<td width="25"><?php echo we_html_tools::getPixel(25, 2) ?></td>');
					d.writeln('<td><?php echo we_html_tools::getPixel(200, 2) ?></td></tr>');
				}
				d.writeln('</table></body>');
				d.close();
			}
			//-->
		</script>
		<?php
		return ob_get_clean();
	}

	protected function printFramesetJSFunctionEntry(){
		return we_html_element::jsElement('
function entry(ID,icon,text,isFolder,path){
	this.ID=ID;
	this.icon=icon;
	this.text=text;
	this.isFolder=isFolder;
	this.path=path;
}');
	}

	protected function printFramesetJSFunctionAddEntry(){
		return we_html_element::jsElement('
function addEntry(ID,icon,text,isFolder,path){
	entries[entries.length] = new entry(ID,icon,text,isFolder,path);
}');
	}

	private function printFramesetJSFunctionClearEntry(){
		return we_html_element::jsElement('
function clearEntries(){
	entries = new Array();
}');
	}

	protected function printFramesetJSFunctionAddEntries(){
		$ret = '';
		while($this->next_record()){
			$ret.= 'addEntry(' . $this->f('ID') . ',"' . $this->f('Icon') . '","' . addcslashes($this->f('Text'), '"') . '",' . ($this->f('IsFolder') | 0) . ',"' . addcslashes($this->f('Path'), '"') . '");';
		}
		return we_html_element::jsElement($ret);
	}

	protected function printFramesetJSFunctions(){
		$this->query();
		return
			$this->printFramesetJSFunctioWriteBody() .
			$this->printFramesetJSFunctionQueryString() .
			$this->printFramesetJSFunctionEntry() .
			$this->printFramesetJSFunctionAddEntry() .
			$this->printFramesetJSFunctionClearEntry() .
			$this->printFramesetJSFunctionAddEntries();
	}

	protected function printBodyHTML(){
		echo we_html_element::htmlDocType() . '<html><head>' . we_html_tools::getJSErrorHandler() . '</head>
				<body style="background-color:white" onload="top.writeBody(self.document);"></body></html>';
	}

	function printHeaderHTML(){
		$this->setDirAndID();
		echo we_html_tools::getHtmlTop() .
		STYLESHEET .
		$this->printHeaderJSIncluddes() .
		we_html_element::jsElement(
			$this->printHeaderJSDef() .
			$this->printHeaderJS() . '
function clearOptions(){
	 var a=document.forms["we_form"].elements["lookin"];
	 for(var i=a.options.length-1;i >= 0;i--){
		a.options[i] = null;
	 }
}
function addOption(txt,id){
		var a=document.forms["we_form"].elements["lookin"];
		a.options[a.options.length]=new Option(txt,id);
		if(a.options.length>0) a.selectedIndex=a.options.length-1;
		else a.selectedIndex=0;

}
function selectIt(){
		var a=document.forms["we_form"].elements["lookin"];
		a.selectedIndex=a.options.length-1;
}') . '
</head>
<style type="text/css">
body{
	background-image:url(\'' . IMAGE_DIR . 'backgrounds/radient.gif\');
	background-color:#bfbfbf;
	background-repeat:repeat;
	margin:0px;
}
a:link,a:visited,a:hover,a:active
{color:#000;}
</style>
	<body>
		<form name="we_form" method="post">' .
		((!defined('OBJECT_TABLE')) || $this->table != OBJECT_TABLE ?
			$this->printHeaderTable() .
			$this->printHeaderLine() : '') .
		$this->printHeaderHeadlines() .
		$this->printHeaderLine() .
		'		</form>
	</body>
</html>';
	}

	protected function printHeaderTableSpaceRow(){
		return '<tr><td colspan="9">' . we_html_tools::getPixel(5, 10) . '</td></tr>';
	}

	protected function printHeaderTableExtraCols(){
		// overwrite
	}

	protected function printHeaderOptions(){
		$pid = $this->dir;
		$out = '';
		$c = 0;
		$z = 0;
		while($pid != 0){
			$c++;
			$this->db->query('SELECT ID,Text,ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($pid));
			if($this->db->next_record()){
				$out = '<option value="' . $this->db->f('ID') . '"' . (($z == 0) ? ' selected="selected"' : '') . '>' . $this->db->f('Text') . '</option>' . $out;
				$z++;
			}
			$pid = $this->db->f('ParentID');
			if($c > 500){
				$pid = 0;
			}
		}
		return '<option value="0">/</option>' . $out;
	}

	protected function printHeaderTable(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">' .
			$this->printHeaderTableSpaceRow() . '
	<tr valign="middle">
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="70" class="defaultfont"><b>' . g_l('fileselector', '[lookin]') . '</b></td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td>
		<select name="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">' .
			$this->printHeaderOptions() . '
		</select>
		</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_html_button::create_button("root_dir", "javascript:if(rootDirButsState){top.setRootDir();}", false, 40, 22, "", "", ($this->dir == 0), false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_html_button::create_button("image:btn_fs_back", "javascript:top.goBackDir();", false, 40, 22, "", "", ($this->dir == 0), false) . '</td>' .
			$this->printHeaderTableExtraCols() .
			'<td width="10">' . we_html_tools::getPixel(10, 29) . '</td></tr>' .
			$this->printHeaderTableSpaceRow() . '
</table>';
	}

	function printHeaderHeadlines(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>' . we_html_tools::getPixel(25, 14) . '</td>
		<td class="selector"><b><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></b></td>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td>' . we_html_tools::getPixel(200, 1) . '</td>
	</tr>
</table>';
	}

	private function printHeaderLine(){
		return '<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr><td><img src="' . IMAGE_DIR . 'umr_h_small.gif" width="100%" height="2" border="0" /></td></tr>
</table>';
	}

	protected function printHeaderJS(){
		return we_html_button::create_state_changer(false) . '
function disableRootDirButs(){
	root_dir_enabled = switch_button_state("root_dir", "root_dir_enabled", "disabled");
	btn_fs_back_enabled = switch_button_state("btn_fs_back", "back_enabled", "disabled", "image");
	rootDirButsState = 0;
}
function enableRootDirButs(){
	root_dir_enabled = switch_button_state("root_dir", "root_dir_enabled", "enabled");
	btn_fs_back_enabled = switch_button_state("btn_fs_back", "back_enabled", "enabled", "image");
	rootDirButsState = 1;
}';
	}

	private function printHeaderJSIncluddes(){
		return we_html_element::jsScript(JS_DIR . 'images.js');
	}

	protected function printHeaderJSDef(){
		return 'var rootDirButsState = ' . (($this->dir == 0) ? 0 : 1) . ';';
	}

	protected function printCmdHTML(){
		echo we_html_element::jsElement('
top.clearEntries();' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			(($this->dir) == 0 ?
				'top.fsheader.disableRootDirButs();' :
				'top.fsheader.enableRootDirButs();') .
			'top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";
');
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->next_record()){
			$ret.= 'top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . addcslashes(str_replace(array("\n", "\r"), "", $this->f("Text")), '"') . '",' . $this->f("IsFolder") . ',"' . addcslashes(str_replace(array("\n", "\r"), "", $this->f("Path")), '"') . '");';
		}
		return $ret;
	}

	protected function printCMDWriteAndFillSelectorHTML(){
		$pid = $this->dir;
		$out = '';
		$c = 0;
		while($pid != 0){
			$c++;
			$this->db->query('SELECT ID,Text,ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($pid));

			if($this->db->next_record()){
				$out = 'top.fsheader.addOption("' . $this->db->f('Text') . '",' . $this->db->f('ID') . ');' . $out;
			}
			$pid = $this->db->f('ParentID');
			if($c > 500){
				$pid = 0;
			}
		}
		return '
top.writeBody(top.fsbody.document);

top.fsheader.clearOptions();
top.fsheader.addOption("/",0);' .
			$out . '
top.fsheader.selectIt();';
	}

	protected function printFooterHTML(){
		echo we_html_tools::getHtmlTop() .
		STYLESHEET . implodeJS(
			$this->printFooterJSDef() .
			$this->printFooterJS()) . '
<style type="text/css">
body{
	margin:0px;
	background-color:#bfbfbf;
	background-repeat:repeat;
	background-image: url(\'' . IMAGE_DIR . 'backgrounds/radient.gif\');
}
a:link,a:visited,a:hover,a:active
{color:#000;}
</style>
</head>
	<body>
	<form name="we_form" target="fscmd">' .
		$this->printFooterTable() . '
	</form>
	</body>
</html>';
	}

	protected function printFooterJSDef(){
		return we_html_element::jsElement("
function press_ok_button() {
	if(document.we_form.fname.value==''){
		top.exit_close();
	}else{
		top.exit_open();
	};
}");
	}

	protected function printFooterJS(){
		// do nothing here, overwrite!
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button("cancel", "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button("ok", "javascript:press_ok_button();");
		$buttons = we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button);
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="5"><img src="' . IMAGE_DIR . 'umr_h_small.gif" width="100%" height="2" border="0" /></td>
	</tr>
	<tr>
		<td colspan="5">' . we_html_tools::getPixel(5, 5) . '</td>
	</tr>
	<tr>
		<td></td>
		<td class="defaultfont">
			<b>' . g_l('fileselector', '[name]') . '</b>
		</td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '
		</td>
		<td></td>
	</tr>
	<tr>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
		<td width="70">' . we_html_tools::getPixel(70, 5) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
		<td>' . we_html_tools::getPixel(5, 5) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
	</tr>
	</table><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right">' . $buttons . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
	</tr>
</table>';
	}

	private function setTableLayoutInfos(){
		switch($this->table){
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
			case TEMPLATES_TABLE:
				$this->col2js = "entries[i].ID";
				$this->tableSizer = "<td>" . we_html_tools::getPixel(25, 1) . "</td><td>" . we_html_tools::getPixel(350, 1) . "</td><td>" . we_html_tools::getPixel(70, 1) . "</td><td>" . we_html_tools::getPixel(150, 1) . "</td>";
				$this->tableHeadlines = "
<td></td>
<td class='selector'><b><a href='#' onclick='javascript:top.orderIt(\"Text\");'>" . g_l('fileselector', '[filename]') . "</a></b></td>
<td class='selector'>&nbsp;<b>ID</b></td>
<td class='selector'>&nbsp;<b><a href='#' onclick='javascript:top.orderIt(\"ModDate\");'>" . g_l('fileselector', '[modified]') . "</a></b></td>
				";
				break;
			default:
				$this->col2js = "entries[i].title";
				$this->tableSizer = "<td>" . we_html_tools::getPixel(25, 1) . "</td><td>" . we_html_tools::getPixel(200, 1) . "</td><td>" . we_html_tools::getPixel(220, 1) . "</td><td>" . we_html_tools::getPixel(150, 1) . "</td>";
				$this->tableHeadlines = "
<td></td>
<td class='selector'><b><a href='#' onclick='javascript:top.orderIt(\"Text\");'>" . g_l('fileselector', '[filename]') . "</a></b></td>
<td class='selector'><b>" . g_l('fileselector', '[title]') . "</b></td>
<td class='selector'><b><a href='#' onclick='javascript:top.orderIt(\"ModDate\");'>" . g_l('fileselector', '[modified]') . "</a></b></td>
				";
		}
	}

}
