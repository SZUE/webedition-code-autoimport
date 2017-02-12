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
	var $values = [];
	var $openerFormName = 'we_form';
	protected $order = 'Text';
	protected $canSelectDir = true;
	protected $language = '';
	var $rootDirID = 0;
	protected $filter = '';
	protected $useID;
	protected $title = '';
	protected $startID = 0;
	protected $multiple = true;
	protected $open_doc = 0;
	protected $textField = 'Text';

	public function __construct($id, $table = FILE_TABLE, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $rootDirID = 0, $multiple = true, $filter = '', $startID = 0){
		if(!isset($_SESSION['weS']['we_fs_lastDir'])){
			$_SESSION['weS']['we_fs_lastDir'] = [$table => 0];
		}

		$this->db = new DB_WE();
		$this->order = ($order ?: $this->order);
		$this->id = $id;
		$this->lastDir = isset($_SESSION['weS']['we_fs_lastDir'][$table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$table]) : 0;
//check table

		$this->table = $table;
		switch($this->table){
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				$this->fields = 'ID,ParentID,0 AS IsFolder,CONCAT(Text," (",Forename," ", Surname,")") AS Text,CONCAT("/",Username) AS Path';
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
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,IF(IsFolder,"' . we_base_ContentTypes::FOLDER . '","we/navigation") AS ContentType';
				break;
			case USER_TABLE:
				$this->fields = 'ID,ParentID,CONCAT(First," ", Second," (",username,")") AS Text,Path,IsFolder,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType';
				$this->textField = 'username';
				break;
			case defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : 'NEWSLETTER_TABLE':
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,IF(IsFolder,"we/folder","we/newsletter") AS ContentType';
				break;
			case defined('BANNER_TABLE') ? BANNER_TABLE : 'BANNER_TABLE':
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,IF(IsFolder,"folder","we/banner") AS ContentType';
				break;
			case defined('VOTING_TABLE') ? VOTING_TABLE : 'VOTING_TABLE':
				$this->fields = 'ID,ParentID,Text,Path,IsFolder,IF(IsFolder,"folder","we/voting") AS ContentType';
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
		$rootDirID = (($ws = get_ws($this->table, true)) ? reset($ws) : 0);

		$this->dir = $this->startID ?: ($setLastDir ? ( isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : $rootDirID ) : $rootDirID);
		$this->id = $this->dir;
		$this->path = '';

		$this->values = [
			'ParentID' => 0,
			'Text' => '/',
			'Path' => '/',
			'IsFolder' => 1
		];
	}

	protected function query(){
		$wsQuery = $this->table == NAVIGATION_TABLE && get_ws($this->table) ? ' ' . self::getWsQuery($this->table) : '';
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' ' .
			( ($this->filter && $this->table != CATEGORY_TABLE ? 'AND ContentType="' . $this->db->escape($this->filter) . '" ' : '' ) . $wsQuery ) .
			($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : ''));
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		switch($what){
			case self::BODY:
				$this->printBodyHTML();
				break;
			case self::CMD:
				$weCmd = new we_base_jsCmd();
				$this->printCmdHTML($weCmd);
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML($withPreview);
		}
	}

	private function printFramesetHTML($withPreview = true){
		$weCmd = new we_base_jsCmd();
		$this->jsoptions = [
			'options' => [
				'rootDirID' => $this->rootDirID,
				'table' => $this->table,
				'formtarget' => WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . get_class($this),
				'multiple' => intval($this->multiple),
				'open_doc' => $this->open_doc,
			],
			'data' => [
				'makeNewFolder' => false,
				'we_editDirID' => 0,
				'JSIDName' => $this->JSIDName,
				'JSTextName' => $this->JSTextName,
				'JSCommand' => $this->JSCommand,
			],
			'click' => [
				'oldID' => 0,
			]
		];
		$this->setFramesetJavaScriptOptions();
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setDirAndID(); //set correct directory
		echo we_html_tools::getHtmlTop($this->title, '', 'frameset', $this->getFramsetJSFile() .
			we_html_element::cssLink(CSS_DIR . 'selectors.css'), $this->getFrameset($weCmd, $withPreview) . $weCmd->getCmds());
	}

	protected function getFramsetJSFile(){
		return we_html_element::jsScript(JS_DIR . 'selectors/file_selector.js', '', ['id' => 'loadVarSelectors', 'data-selector' => setDynamicVar($this->jsoptions)]);
	}

	protected function setFramesetJavaScriptOptions(){
		if($this->id === 0){
			$this->path = '/';
		}
		$this->jsoptions['options']['canSelectDir'] = intval($this->canSelectDir);
		$this->jsoptions['data']['parentID'] = intval(($this->dir ? f('SELECT ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), '', $this->db) : 0));
		$this->jsoptions['data']['currentType'] = (isset($this->filter) ? $this->filter : "");
		$this->jsoptions['data']['currentDir'] = $this->dir;
		$this->jsoptions['data']['currentText'] = (isset($this->values["Text"]) ? $this->values["Text"] : '');
		$this->jsoptions['data']['currentID'] = $this->id;
		$this->jsoptions['data']['startPath'] = f('SELECT Path FROM ' . $GLOBALS['DB_WE']->escape($this->table) . ' WHERE ID=' . intval($this->dir)) ?: '/';
		$this->jsoptions['data']['currentPath'] = $this->path;
		$this->jsoptions['data']['order'] = $this->order;
		$this->jsoptions['data']['rootDirButsState'] = (($this->dir != 0));
	}

	protected function getFrameset(we_base_jsCmd $weCmd, $withPreview = false){
		return '<body class="selector" onload="startFrameset();">' .
			we_html_element::htmlDiv(['id' => 'fsheader'], $this->printHeaderHTML($weCmd)) .
			we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true) .
			we_html_element::htmlDiv(['id' => 'fsfooter'], $this->printFooterTable()) .
			we_html_element::htmlDiv(['id' => 'fspath', 'class' => 'radient']) .
			we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
			'</body>';
	}

	protected function getFsQueryString($what){
		return WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . get_class($this) . '&what=' . $what . '&table=' . $this->table . '&id=' . $this->id . '&order=' . $this->order . '&startID=' . $this->startID . '&filter=' . $this->filter;
	}

	protected function printBodyHTML(){
		echo we_html_tools::getHtmlTop('', '', '4Trans', we_html_element::cssLink(CSS_DIR . 'selectors.css') .
			$this->getFramsetJSFile(), we_html_element::htmlBody(['class' => "selectorBody", 'onload' => "top.writeBody(self.document.body);", 'onclick' => "weonclick(event);"]));
	}

	protected function printHeaderHTML(we_base_jsCmd $weCmd){
		$this->setDirAndID();
		$do = (!defined('OBJECT_TABLE')) || $this->table != OBJECT_TABLE;
		if($do){
			$this->setSelectorData($weCmd);
		}
		return
			($do ? $this->printHeaderTable($weCmd) : '') .
			$this->printHeaderHeadlines();
	}

	protected function printHeaderTable(we_base_jsCmd $weCmd, $extra = '', $append = false){
		return '
<table class="selectorHeaderTable">
	<tr style="vertical-align:middle">
		<td class="defaultfont lookinText">' . g_l('fileselector', '[lookin]') . '</td>
		<td class="lookin"><select name="lookin" id="lookin" class="weSelect" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">
		</select>
		</td>
		<td>' . we_html_button::create_button('root_dir', "javascript:if(top.fileSelect.data.rootDirButsState){top.setRootDir();}", '', 0, 0, "", "", ($this->dir == 0), false) . '</td>
		<td>' . we_html_button::create_button('fa:btn_fs_back,fa-lg fa-level-up,fa-lg fa-folder', "javascript:top.goBackDir();", '', 0, 0, "", "", ($this->dir == 0), false) . '</td>' .
			$extra .
			'</tr>
</table>';
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines">
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector filename"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></th>
		<th class="selector remain"></th>
	</tr>
</table>';
	}

	protected function printCmdHTML(we_base_jsCmd $weCmd){
		$weCmd->addCmd('clearEntries');
		$weCmd->addCmd('updateSelectData', [
			'currentPath' => $this->path,
			'parentID' => $this->values["ParentID"],
		]);
		$this->printCmdAddEntriesHTML($weCmd);
		$weCmd->addCmd('setButtons', [['RootDirButs', (intval($this->dir) != intval($this->rootDirID))]]);
		$this->setSelectorData($weCmd);
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function printCmdAddEntriesHTML(we_base_jsCmd $weCmd){
		$this->query();
		$entries = [];
		while($this->db->next_record()){
			$entries[] = [
				intval($this->db->f("ID")),
				str_replace(["\n", "\r"], "", $this->db->f('Text')),
				intval($this->db->f("IsFolder")),
				str_replace(["\n", "\r"], "", $this->db->f("Path")),
				$this->db->f("ContentType")
			];
		}
		$weCmd->addCmd('addEntries', $entries);
		$weCmd->addCmd('writeBody');
	}

	protected function setSelectorData(we_base_jsCmd $weCmd){
		$pid = $this->dir;
		$options = [];
		$c = 0;
		while($pid != 0){
			$c++;
			$hash = getHash('SELECT ID,`' . $this->textField . '` AS Text,ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($pid), $this->db);
			if($hash){
				$options[] = [$hash['Text'], $hash['ID']];
			}
			$pid = $hash['ParentID'];
			if($c > 500){
				$pid = 0;
			}
		}
		$options[] = ['/', 0];
		//we need to reverse the array, cause root is at the end
		$weCmd->addCmd('writeOptions', array_reverse($options));
	}

	protected function printFooterTable(){
		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();");
		$yes_button = we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();");
		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[name]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", 'style="width:100%" readonly="readonly"') . '</td>
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

	public static function getJSLangConsts(){
		return '
WE().consts.g_l.fileselector = {
	already_root: "' . we_message_reporting::prepareMsgForJS(g_l('fileselector', '[already_root]')) . '",
	date_format:"' . date(g_l('date', '[format][default]')) . '",
	deleteQuestion:"' . g_l('fileselector', '[deleteQuestion]') . '",
	edit_file_is_folder: "' . we_message_reporting::prepareMsgForJS(g_l('fileselector', '[edit_file_is_folder]')) . '",
	edit_file_nok: "' . we_message_reporting::prepareMsgForJS(g_l('fileselector', '[edit_file_nok]')) . '",
	folder:"' . g_l('contentTypes', '[folder]') . '",
	newFolderExport:"' . g_l('export', '[newFolder]') . '",
	newFolderNavigation:"' . g_l('navigation', '[newFolder]') . '",
	newFolderVoting:"' . g_l('modules_voting', '[newFolder]') . '",
	new_folder_name:"' . g_l('fileselector', '[new_folder_name]') . '",
	newbannergroup:"' . g_l('modules_banner', '[newbannergroup]') . '",
};';
	}

	public static function getJSConsts(){
		return 'WE().consts.selectors={
	CMD:' . self::CMD . ',
	CREATEFOLDER:' . self::CREATEFOLDER . ',
	CREATE_CAT:' . self::CREATE_CAT . ',
	DEL:' . self::DEL . ',
	DEL:' . self::DEL . ',
	DORENAMEFOLDER:' . self::DORENAMEFOLDER . ',
	DO_RENAME_ENTRY:' . self::DO_RENAME_ENTRY . ',
	NEWFOLDER:' . self::NEWFOLDER . ',
	PREVIEW:' . self::PREVIEW . ',
	PROPERTIES:' . self::PROPERTIES . ',
	RENAMEFOLDER:' . self::RENAMEFOLDER . ',
	SETDIR:' . self::SETDIR . ',
	VIEW_ICONS:"' . we_search_view::VIEW_ICONS . '",
  VIEW_LIST:"' . we_search_view::VIEW_LIST . '",
};
';
	}

	public static function getWsQuery($tab, $includingFolders = true){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return '';
		}

		if(!($ws = get_ws($tab, true))){
			return (($tab == NAVIGATION_TABLE || (defined('NEWSLETTER_TABLE') && $tab == NEWSLETTER_TABLE)) ? '' : ' OR RestrictOwners=0 ');
		}
		$paths = id_to_path($ws, $tab, null, true);
		$wsQuery = [];
		foreach($paths as $path){
			$parts = explode('/', $path);
			array_shift($parts);
			$last = array_pop($parts);
			$path = '/';
			foreach($parts as $part){

				$path .= $part;
				if($includingFolders){
					$wsQuery[] = 'Path = "' . $GLOBALS['DB_WE']->escape($path) . '"';
				} else {
					$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
				}
				$path .= '/';
			}
			$path .= $last;
			if($includingFolders){
				$wsQuery[] = 'Path = "' . $GLOBALS['DB_WE']->escape($path) . '"';
				$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
			} else {
				$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
			}
			$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
		}

		return ' AND (' . implode(' OR ', $wsQuery) . ')';
	}

	public static function getSelectorFromRequest(){
		$class = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);

		switch($class){
			case 'we_navigation_dirSelector':
				$fs = new we_navigation_dirSelector(we_base_request::_(we_base_request::INT, 'id', we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)), we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2), we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3), we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4), we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
				break;
			case 'we_banner_dirSelector':
				if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
					$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
				} else {
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$JSIDName = $JSTextName = $JSCommand = '';
				}

				$fs = new we_banner_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
				break;
			case 'we_banner_selector':
				if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1)) !== false){
					$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
				} else {
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$JSIDName = $JSTextName = $JSCommand = '';
				}

				$fs = new we_banner_selector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''));
				break;
			case 'we_newsletter_dirSelector':
				if(($cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', false, 4)) !== false){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1);
					$JSIDName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
					$JSTextName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
					$JSCommand = $cmd;
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 6);
					$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', 7, '');
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
				}

				$fs = new we_newsletter_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::STRING, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $rootDirID, $multiple);
				break;

			case 'we_export_dirSelector':
				if(($cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', false, 4)) !== false){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSIDName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2));
					$JSTextName = stripslashes(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
					$JSCommand = stripslashes($cmd);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
				}

				$fs = new we_export_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', ''), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
				break;

			case 'we_users_selector':
				we_html_tools::protect(['NEW_USER', 'NEW_GROUP', 'SAVE_USER', 'SAVE_GROUP', 'DELETE_USER', 'DELETE_GROUP']);
				if(($idname = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 1))){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);
					$JSIDName = $idname;
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
					$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 3);
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$filter = we_base_request::_(we_base_request::STRING, 'filter', '');
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
				}

				$fs = new we_users_selector($id, USER_TABLE, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), $rootDirID, $filter, $multiple);
				break;
			case 'we_voting_dirSelector':
				if(($cmd = we_base_request::_(we_base_request::CMD, 'we_cmd', false, 2)) !== false){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSIDName = $cmd;
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
				}

				$fs = new we_voting_dirSelector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''));
				break;

			case 'we_selector_image':
				if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', false, 2)) !== false){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
					$startID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 6);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
					$open_doc = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 9);
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 10);
					$canSelectDir = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 11);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$table = we_base_request::_(we_base_request::TABLE, 'table', (defined('FILE_TABLE') ? FILE_TABLE : 'FF'));
					$startID = we_base_request::_(we_base_request::INT, 'startID');
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$open_doc = we_base_request::_(we_base_request::BOOL, 'open_doc');
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
					$canSelectDir = we_base_request::_(we_base_request::BOOL, 'canSelectDir');
				}
				$fs = new we_selector_image($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $rootDirID, $open_doc ? ($table == (defined('FILE_TABLE') ? FILE_TABLE : 'FF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') : ($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') : false)) : false, $multiple, $canSelectDir, $startID);
				break;
			case 'we_customer_selector':
				$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
				$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
				if($JSIDName || $JSCommand){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
					$id = we_base_request::_(we_base_request::STRING, 'id', 0);
				}
				$fs = new we_customer_selector($id, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), $rootDirID, '', $multiple);
				break;
			case 'we_selector_category':
				$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
				$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
				$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
				if(we_base_request::_(we_base_request::CMD, 'we_cmd', '', 2)){
					$noChoose = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8);
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$noChoose = we_base_request::_(we_base_request::BOOL, 'noChoose');
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
				}
				$fs = new we_selector_category($id, CATEGORY_TABLE, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), we_base_request::_(we_base_request::INT, 'we_editCatID', 0), we_base_request::_(we_base_request::STRING, 'we_EntryText', ''), $rootDirID, $noChoose);
				break;
			case 'we_selector_document':
				if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2))){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
					$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 8);
					$open_doc = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 9);
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 10);
					$canSelectDir = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 11);
					$lang = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 12);
					if($filter === we_base_ContentTypes::IMAGE){
						t_e('notice', 'called incorrect selector');
					}
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$table = we_base_request::_(we_base_request::TABLE, 'table', (defined('FILE_TABLE') ? FILE_TABLE : 'FF'));
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$filter = we_base_request::_(we_base_request::STRING, 'filter', '');
					$open_doc = we_base_request::_(we_base_request::BOOL, 'open_doc');
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
					$canSelectDir = we_base_request::_(we_base_request::BOOL, 'canSelectDir');
					$lang = we_base_request::_(we_base_request::STRING, 'lang');
				}
				$fs = new we_selector_document($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $filter, $rootDirID, $open_doc ? ($table == (defined('FILE_TABLE') ? FILE_TABLE : 'FF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') : ($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OF') ? permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') : false)) : false, $multiple, $canSelectDir, 0, $lang);
				break;
			case 'we_selector_directory':
				if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2))){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
				}

				$fs = new we_selector_directory($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), 0, we_base_request::_(we_base_request::INT, 'we_editDirID', 0), we_base_request::_(we_base_request::STRING, 'we_FolderText', ''), $rootDirID, $multiple);
				break;
			case 'we_selector_delete':
				if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1))){
					$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 2);
				} else {
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);
				}
				$fs = new we_selector_delete($id, $table);
				break;
			case 'we_selector_file':
				if(($table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2))){
					$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
					$JSIDName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3);
					$JSTextName = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 4);
					$JSCommand = we_base_request::_(we_base_request::CMD, 'we_cmd', '', 5);
					$rootDirID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
					$filter = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 8);
					$multiple = we_base_request::_(we_base_request::BOOL, 'we_cmd', '', 9);
				} else {
					$JSIDName = $JSTextName = $JSCommand = '';
					$id = we_base_request::_(we_base_request::INT, 'id', 0);
					$table = we_base_request::_(we_base_request::TABLE, 'table', FILE_TABLE);
					$rootDirID = we_base_request::_(we_base_request::INT, 'rootDirID', 0);
					$filter = we_base_request::_(we_base_request::STRING, 'filter', '');
					$multiple = we_base_request::_(we_base_request::BOOL, 'multiple');
				}

				$fs = new we_selector_file($id, $table, $JSIDName, $JSTextName, $JSCommand, we_base_request::_(we_base_request::STRING, 'order', ''), $rootDirID, $multiple, $filter);
				break;
			default:
				t_e('selector ' . $class . ' not found');
				return'';
		}
		$fs->printHTML(we_base_request::_(we_base_request::INT, 'what', we_selector_file::FRAMESET));
	}

}
