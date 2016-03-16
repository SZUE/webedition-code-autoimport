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
class we_selector_file{
	const FRAMESET = 0;
	const FOOTER = 2;
	const BODY = 3;
	const CMD = 4;
	const SETDIR = 5;
	const CREATE_CAT = 7;
	const NEWFOLDER = 7;
	const CREATEFOLDER = 8;
	const DO_RENAME_CAT = 9;
	const RENAMEFOLDER = 9;
	const DO_RENAME_ENTRY = 10;
	const DORENAMEFOLDER = 10;
	const DEL = 11;
	const PREVIEW = 11;
	const PROPERTIES = 12;
	const CHANGE_CAT = 13;
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
	var $tableHeadlines = '';
	var $JSCommand = '';
	var $JSTextName;
	var $JSIDName;
	protected $db;
	var $sessionID = '';
	protected $fields = '';
	var $values = array();
	var $openerFormName = 'we_form';
	protected $order = 'Text';
	protected $canSelectDir = true;
	var $rootDirID = 0;
	protected $filter = '';
	protected $useID;
	protected $title = '';
	protected $startID = 0;
	protected $multiple = true;
	protected $open_doc = 0;

	public function __construct($id, $table = FILE_TABLE, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $rootDirID = 0, $multiple = true, $filter = '', $startID = 0){
		if(!isset($_SESSION['weS']['we_fs_lastDir'])){
			$_SESSION['weS']['we_fs_lastDir'] = array($table => 0);
		}

		$this->db = new DB_WE();
		$this->order = ($order ? : $this->order);
		$this->id = $id;
		$this->lastDir = isset($_SESSION['weS']['we_fs_lastDir'][$table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$table]) : 0;
//check table

		$this->table = $table;
		switch($this->table){
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				$this->fields = 'ID,ParentID,IsFolder,CONCAT(Text," (",Forename," ", Surname,")") AS Text,CONCAT("/",Username) AS Path';
				break;
			case FILE_TABLE:
			case TEMPLATES_TABLE:
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,ContentType';
				break;
			case CATEGORY_TABLE:
				$this->fields = 'ID,ParentID,Text,Path,1 AS IsFolder,IF(EXISTS(SELECT * FROM ' . CATEGORY_TABLE . ' cc WHERE cc.ParentID=' . CATEGORY_TABLE . '.ID),"we/categories","we/category") AS ContentType';
				break;
			case NAVIGATION_TABLE:
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,IF(IsFolder,"folder","we/navigation") AS ContentType';
				break;
			case USER_TABLE:
				$this->fields = 'ID,ParentID,CONCAT(First," ", Second," (",Text,")") AS Text,Path,IsFolder,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType';
				break;
			case defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE':
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,(IF(IsFolder,"we/folder","we/newsletter") AS ContentType';
				break;
			default:
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,ContentType';
		}

		$this->JSIDName = $JSIDName;
		$this->JSTextName = $JSTextName;
		$this->JSCommand = $JSCommand;
		$this->rootDirID = intval($rootDirID);
		$this->filter = $filter;
		$this->startID = $startID;
		$this->multiple = $multiple;
		$this->setDirAndID();
		$this->setTableLayoutInfos();
	}

	protected function setDirAndID(){
		if($this->id > 0 &&
			// get default Directory
			($data = getHash('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), $this->db))){

			// getValues of selected Dir
			$this->values = $data;

			$this->dir = ($this->values['IsFolder'] ?
					$this->id :
					$this->values['ParentID']);

			$this->path = $this->values['Path'];
			return;
		}

		$this->setDefaultDirAndID($this->id === 0 ? false : true);
	}

	protected function setDefaultDirAndID($setLastDir){
		$this->dir = $this->startID ? : ($setLastDir ? ( isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0 ) : 0);
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
		return $pid && $this->isIDInFolder($pid, $folderID, $db);
	}

	function query(){
		$wsQuery = $this->table == NAVIGATION_TABLE && get_ws($this->table) ? ' ' . getWsQueryForSelector($this->table) : '';
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' ' .
			( ($this->filter && $this->table != CATEGORY_TABLE ? 'AND ContentType="' . $this->db->escape($this->filter) . '" ' : '' ) . $wsQuery ) .
			($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : ''));
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		switch($what){
			case self::BODY:
				$this->printBodyHTML();
				break;
			case self::CMD:
				$this->printCmdHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML($withPreview);
		}
	}

	function printFramesetHTML($withPreview = true){
		$this->setDirAndID(); //set correct directory
		echo we_html_tools::getHtmlTop($this->title, '', 'frameset') .
		$this->getFramesetJavaScriptDef() .
		$this->getFramsetJSFile() .
		$this->getExitOpen() .
		we_html_element::jsElement($this->printCmdAddEntriesHTML() . 'self.focus();');
		?>
		</head><?php
		echo $this->getFrameset($withPreview);
	}

	protected function getFramsetJSFile(){
		return we_html_element::jsScript(JS_DIR . 'selectors/file_selector.js');
	}

	protected function getFramesetJavaScriptDef(){
		$startPath = f('SELECT Path FROM ' . $GLOBALS['DB_WE']->escape($this->table) . ' WHERE ID=' . intval($this->dir))? : '/';
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
var parentID=' . intval(($this->dir ? f('SELECT ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), '', $this->db) : 0)) . ';
var table="' . $this->table . '";
var order="' . $this->order . '";
var entries = [];
var clickCount=0;
var wasdblclick=false;
var tout=null;
var mk=null;

var options={
  rootDirID:' . $this->rootDirID . ',
	table:"' . $this->table . '",
	formtarget:"' . $_SERVER["SCRIPT_NAME"] . '",
	rootDirID:' . $this->rootDirID . ',
	multiple:' . intval($this->multiple) . ',
	needIEEscape:' . intval(we_base_browserDetect::isIE() && $GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8') . ',
	open_doc:"' . $this->open_doc . '"
};

WE().consts.selectors={
	DORENAMEFOLDER:"' . self::DORENAMEFOLDER . '",
	CREATEFOLDER:"' . self::CREATEFOLDER . '",
	CMD:' . self::CMD . ',
	DEL:' . self::DEL . ',
	PROPERTIES:' . self::PROPERTIES . ',
	PREVIEW:' . self::PREVIEW . ',
	NEWFOLDER:' . self::NEWFOLDER . ',
	RENAMEFOLDER:' . self::RENAMEFOLDER . ',
	CREATE_CAT:' . self::CREATE_CAT . ',
	DO_RENAME_ENTRY:' . self::DO_RENAME_ENTRY . ',
	SETDIR:' . self::SETDIR . ',
	VIEW_ICONS:"' . we_search_view::VIEW_ICONS . '",
  VIEW_LIST:"' . we_search_view::VIEW_LIST . '",
};

var g_l={
	deleteQuestion:\'' . g_l('fileselector', '[deleteQuestion]') . '\',
	new_folder_name:"' . g_l('fileselector', '[new_folder_name]') . '",
	date_format:"' . date(g_l('date', '[format][default]')) . '",
	folder:"' . g_l('contentTypes', '[folder]') . '"
};
');
	}

	protected function getFrameset(){
		return
			STYLESHEET .
			we_html_element::cssLink(CSS_DIR . 'selectors.css') .
			$this->getFramsetJSFile() .
			'<body class="selector" onload="startFrameset();">' .
			we_html_element::htmlDiv(array('id' => 'fsheader'), $this->printHeaderHTML()) .
			we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true) .
			we_html_element::htmlDiv(array('id' => 'fsfooter'), $this->printFooterTable()) .
			we_html_element::htmlDiv(array('id' => 'fspath', 'class' => 'radient'), we_html_element::jsElement('document.write( (top.startPath === undefined || top.startPath === "") ? "/" : top.startPath);')) .
			we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
			'</body>
</html>';
	}

	protected function getExitOpen(){
		$frameRef = $this->JSTextName && strpos($this->JSTextName, ".document.") > 0 ? substr($this->JSTextName, 0, strpos($this->JSTextName, ".document.") + 1) : "";
		return we_html_element::jsElement('
function exit_open(){' .
				($this->JSIDName ? '
	opener.' . $this->JSIDName . '=top.currentID;' :
					''
				) .
				($this->JSTextName ? '
	opener.' . $this->JSTextName . '= top.currentID ? top.currentPath : "";
	if((opener.parent!==undefined) && (opener.parent.frames.editHeader!==undefined)) {
			if(currentType!="")	{
				switch(currentType){
					case "noalias":
						setTabsCurPath = "@"+currentText;
						break;
					default:
						setTabsCurPath = top.currentPath;
				}
				if(getEntry(top.currentID).isFolder){
					opener.parent.frames.editHeader.weTabs.setTitlePath("",setTabsCurPath);
				}else{
					opener.parent.frames.editHeader.weTabs.setTitlePath(setTabsCurPath);
				}
			}
	}
	if(opener.' . $frameRef . 'YAHOO!==undefined && opener.' . $frameRef . 'YAHOO.autocoml!==undefined) {  opener.' . $frameRef . 'YAHOO.autocoml.selectorSetValid(opener.' . str_replace('.value', '.id', $this->JSTextName) . '); }
	' : '') .
				($this->JSCommand ? '	' . $this->JSCommand . ';' : '') .
				'	self.close();
	}'
		);
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . 'what=' . $what . '&table=' . $this->table . '&id=' . $this->id . '&order=' . $this->order . '&startID=' . $this->startID . '&filter=' . $this->filter;
	}

	protected function printBodyHTML(){
		echo we_html_tools::getHtmlTop('', '', '4Trans') .
		STYLESHEET .
		we_html_element::cssLink(CSS_DIR . 'selectors.css') .
		$this->getFramsetJSFile() .
		'</head><body class="selectorBody" onload="top.writeBody(self.document.body);" onclick="weonclick(event);"></body></html>';
	}

	protected function printHeaderHTML(){
		$this->setDirAndID();
		$do = (!defined('OBJECT_TABLE')) || $this->table != OBJECT_TABLE;
		return
			we_html_element::jsElement($this->printHeaderJSDef()) .
			($do ? $this->printHeaderTable() : '') .
			we_html_element::jsElement(($do ? $this->printCMDWriteAndFillSelectorHTML(false) : '')) .
			$this->printHeaderHeadlines();
	}

	protected function printHeaderTable($extra = ''){
		return '
<table class="selectorHeaderTable">
	<tr style="vertical-align:middle">
		<td class="defaultfont lookinText">' . g_l('fileselector', '[lookin]') . '</td>
		<td class="lookin"><select name="lookin" id="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">
		</select>
		</td>
		<td>' . we_html_button::create_button('root_dir', "javascript:if(rootDirButsState){top.setRootDir();}", false, 40, 22, "", "", ($this->dir == 0), false) . '</td>
		<td>' . we_html_button::create_button('fa:btn_fs_back,fa-lg fa-level-up,fa-lg fa-folder', "javascript:top.goBackDir();", false, 40, 22, "", "", ($this->dir == 0), false) . '</td>' .
			$extra .
			'</tr>
</table>';
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines">
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector filename"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></th>
		<th class="selector remain"></th>
	</tr>
</table>';
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
				'top.disableRootDirButs();' :
				'top.enableRootDirButs();') .
			'top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";
');
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){
			$ret.= 'top.addEntry(' . $this->db->f("ID") . ',"' . addcslashes(str_replace(array("\n", "\r"), "", $this->db->f("Text")), '"') . '",' . $this->db->f("IsFolder") . ',"' . addcslashes(str_replace(array("\n", "\r"), "", $this->db->f("Path")), '"') . '","' . $this->db->f("ContentType") . '");';
		}
		return $ret;
	}

	protected function printCMDWriteAndFillSelectorHTML($withWrite = true){
		$pid = $this->dir;
		$out = '';
		$c = 0;
		while($pid != 0){
			$c++;
			$this->db->query('SELECT ID,Text,ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($pid));
			if($this->db->next_record()){
				$out = 'top.addOption("' . $this->db->f('Text') . '",' . $this->db->f('ID') . ');' . $out;
			}
			$pid = $this->db->f('ParentID');
			if($c > 500){
				$pid = 0;
			}
		}
		return ($withWrite ? 'top.writeBody(top.fsbody.document.body);' : '') . '
top.clearOptions();
top.addOption("/",0);' .
			$out . '
top.selectIt();';
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();");
		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
	</tr>
</table>
<div id="footerButtons">' . we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button) . '</div>';
	}

	private function setTableLayoutInfos(){
		//FIXME: should we add a column for extension?
		switch($this->table){
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
			case TEMPLATES_TABLE:
				$this->col2js = 1;
				$this->tableHeadlines = "
<th class='selector treeIcon'></th>
<th class='selector filename'><a href='#' onclick='javascript:top.orderIt(\"Text\");'>" . g_l('fileselector', '[filename]') . "</a></th>
<th class='selector title'>ID</th>
<th class='selector modddate'><a href='#' onclick='javascript:top.orderIt(\"ModDate\");'>" . g_l('fileselector', '[modified]') . "</a></th>
<th class='selector remain'></th>";
				break;
			default:
				$this->col2js = 0;
				$this->tableHeadlines = "
<th class='selector treeIcon'></th>
<th class='selector filename'><a href='#' onclick='javascript:top.orderIt(\"Text\");'>" . g_l('fileselector', '[filename]') . "</a></th>
<th class='selector title'>" . g_l('fileselector', '[title]') . "</th>
<th class='selector moddate'><a href='#' onclick='javascript:top.orderIt(\"ModDate\");'>" . g_l('fileselector', '[modified]') . "</a></th>
<th class='selector remain'></th>";
		}
	}

}
