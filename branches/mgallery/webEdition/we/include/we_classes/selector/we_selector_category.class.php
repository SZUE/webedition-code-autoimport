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
class we_selector_category extends we_selector_file{

	private $we_editCatID = '';
	private $EntryText = '';
	private $noChoose = false;

	function __construct($id, $table = FILE_TABLE, $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $we_editCatID = '', $EntryText = '', $rootDirID = 0, $noChoose = false){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $rootDirID);
		$this->title = g_l('fileselector', '[catSelector][title]');

		$this->we_editCatID = $we_editCatID;
		$this->EntryText = $EntryText;
		$this->noChoose = $noChoose;
		$this->multiple = true;
		$this->fields.= ',IF(IsFolder,"folder.gif","cat.gif") AS Icon';
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
			case self::CREATEFOLDER:
				$this->printCreateEntryHTML(1);
				break;
			case self::DO_RENAME_ENTRY:
				$this->printDoRenameEntryHTML();
				break;
			case self::CREATE_CAT:
				$this->printCreateEntryHTML(0);
				break;
			case self::DO_RENAME_CAT:
				$this->printDoRenameEntryHTML();
				break;
			case self::DEL:
				$this->printDoDelEntryHTML();
				break;
			case self::PROPERTIES:
				$this->printPropertiesHTML();
				break;
			case self::CHANGE_CAT:
				$this->printchangeCatHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
				we_html_element::jsScript(JS_DIR . 'selectors/category_selector.js');
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "?what=$what&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . "&noChoose=" . $this->noChoose;
	}

	protected function printHeaderTable(){
		return '
<table class="selectorHeaderTable">
	<tr valign="middle">
		<td class="defaultfont lookinText">' . g_l('fileselector', '[lookin]') . '</td>
		<td class="lookin"><select name="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">' . $this->printHeaderOptions() . '</select></td>
		<td>' . we_html_button::create_button("root_dir", "javascript:top.setRootDir();", true, 0, 0, '', '', $this->dir == intval($this->rootDirID), false) . '</td>
		<td>' . we_html_button::create_button("image:btn_fs_back", "javascript:top.goBackDir();", true, 0, 0, '', '', $this->dir == intval($this->rootDirID), false) . '</td>' .
				($this->userCanEditCat() ?
						'<td>' . we_html_button::create_button("image:btn_new_dir", 'javascript:top.drawNewFolder();', true, 0, 0, '', '', false, false) . '</td>
		<td width="38">' . we_html_button::create_button("image:btn_add_cat", 'javascript:top.drawNewCat();', true, 0, 0, '', '', false, false) . '</td>' : '') .
				($this->userCanEditCat() ?
						'<td class="trash">' . we_html_button::create_button("image:btn_function_trash", 'javascript:if(changeCatState==1){top.deleteEntry();}', true, 27, 22, '', '', false, false) . '</td>' : '') .
				'</tr>
</table>';
	}

	function userCanEditCat(){
		return permissionhandler::hasPerm('EDIT_KATEGORIE');
	}

	function userCanChangeCat(){
		return ($this->userCanEditCat() && $this->id > 0);
	}

	protected function printHeaderJSDef(){
		return 'var changeCatState=' . ($this->userCanChangeCat() ? 1 : 0) . ';';
	}

	protected function getWriteBodyHead(){
		return we_html_element::jsElement('
var ctrlpressed=false;
var shiftpressed=false;
var inputklick=false;
var wasdblclick=false;
var tout=null;
function weonclick(e){
if(top.makeNewFolder || top.makeNewCat || top.we_editCatID){
if(!inputklick){' . (we_base_browserDetect::isIE() && $GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ?
								'document.we_form.we_EntryText.value=escape(top.fsbody.document.we_form.we_EntryText_tmp.value);' :
								'document.we_form.we_EntryText.value=top.fsbody.document.we_form.we_EntryText_tmp.value;') . '
top.makeNewFolder=top.makeNewCat=top.we_editCatID=false;
document.we_form.submit();
}else{
inputklick=false;
}
}else{
inputklick=false;
if(document.all){
if(e.ctrlKey || e.altKey){
ctrlpressed=true;
}
if(e.shiftKey){
shiftpressed=true;
}
}else{
if(e.altKey || e.metaKey || e.ctrlKey){
ctrlpressed=true;
}
if(e.shiftKey){
shiftpressed=true;
}
}
if((self.shiftpressed==false) && (self.ctrlpressed==false)){
	top.unselectAllFiles();
}
}
}');
	}

	protected function printFramesetJSFunctioWriteBody(){//FIXME:cuttext
		return '';
	}

	protected function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
		function queryString(what,id,o,we_editCatID){
		if(!o){
		o=top.order;
		}
		if(!we_editCatID){
		we_editCatID="";
		}
		return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&rootDirID=' . $this->rootDirID . '&table=' . $this->table . '&id=\'+id+(o ? ("&order="+o) : "")+(we_editCatID ? ("&we_editCatID="+we_editCatID) : "");
		}');
	}

	function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
var makeNewFolder=false;
var hot=0; // this is hot for category edit!!
var makeNewCat=false;
var we_editCatID="";
var old=0;
var noChoose=' . intval($this->noChoose) . ';
var perms={
	"EDIT_KATEGORIE":' . intval(permissionhandler::hasPerm("EDIT_KATEGORIE")) . '
};

var g_l={
	"deleteQuestion":\'' . g_l('fileselector', '[deleteQuestion]') . '\',
	"new_folder_name":"' . g_l('fileselector', '[new_folder_name]') . '",
	"new_cat_name":"' . g_l('fileselector', '[new_cat_name]') . '",
};
options.userCanEditCat=' . intval($this->userCanEditCat()) . ';
');
	}

	function printCreateEntryHTML($what = 0){
		echo we_html_tools::getHtmlTop();
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = trim($this->EntryText);
		$Path = ($txt ? (!intval($this->dir) ? '' : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), '', $this->db)) . '/' . $txt : '');

		$js = 'top.clearEntries();';
		if(empty($txt)){
			$js.= we_message_reporting::getShowMessageCall(g_l('weEditor', ($what == 1 ? '[folder][filename_empty]' : '[category][filename_empty]')), we_message_reporting::WE_MESSAGE_ERROR);
		} else if(strpos($txt, ',') !== false){
			$js.=we_message_reporting::getShowMessageCall(g_l('weEditor', '[category][name_komma]'), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($Path) . '" LIMIT 1', '', $this->db) === '1'){
			$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', ($what == 1 ? '[folder][response_path_exists]' : '[category][response_path_exists]')), $Path), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(preg_match('|[\\\'"<>/]|', $txt)){
			$js.= we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $Path), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
						'Category' => $txt,
						'ParentID' => intval($this->dir),
						'Text' => $txt,
						'Path' => $Path,
						'IsFolder' => intval($what),
						'Icon' => (($what == 1) ? we_base_ContentTypes::FOLDER_ICON : 'cat.gif'),
			)));
			$folderID = $this->db->getInsertId();
			$js.='top.currentPath = "' . $Path . '";
top.currentID = "' . $folderID . '";
top.hot = 1; // this is hot for category edit!!

if(top.currentID){
	top.fsheader.enableDelBut();
	top.showPref(top.currentID);
}';
		}

		echo we_html_element::jsElement(
				$js .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				'top.makeNewFolder = false;
top.selectFile(top.currentID);') .
		'</head><body></body></html>';
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines" width="100%">
	<tr>
		<td width="35%" class="selector" style="padding-left:10px;"><b><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[catname]') . '</a></b></td>
		<td width="65%" class="selector" style="padding-left:10px;"><b>' . g_l('button', '[properties][value]') . '</b></td>
	</tr>
	<tr>
		<td width="35%"></td>
		<td width="65%"></td>
	</tr>
</table>';
	}

	function printDoRenameEntryHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop();
		$what = f('SELECT IsFolder FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->we_editCatID), '', $this->db);
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = trim($this->EntryText);
		$Path = ($txt ? (!intval($this->dir) ? '' : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), 'Path', $this->db)) . '/' . $txt : '');
		$js = 'top.clearEntries();';

		if(empty($txt)){
			$js.=we_message_reporting::getShowMessageCall(g_l('weEditor', ($what == 1 ? '[folder]' : '[category]') . '[filename_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(strpos($txt, ',') !== false){
			$js.=we_message_reporting::getShowMessageCall(g_l('weEditor', '[category][name_komma]'), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(f('SELECT 1 FROM ' . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($Path) . "' AND ID!=" . intval($this->we_editCatID) . ' LIMIT 1', '', $this->db)){
			$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', ($what == 1 ? '[folder]' : '[category]') . '[response_path_exists]'), $Path), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(preg_match('|[\'"<>/]|', $txt)){
			$js.=we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $Path), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editCatID), 'Text', $this->db) != $txt){
			$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter(array(
						'Category' => $txt,
						'ParentID' => intval($this->dir),
						'Text' => $txt,
						'Path' => $Path,
					)) .
					' WHERE ID=' . intval($this->we_editCatID));
			if(f('SELECT IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editCatID), '', $this->db)){
				$this->renameChildrenPath($this->we_editCatID);
			}
			$js.='top.currentPath = "' . $Path . '";

top.hot = 1; // this is hot for category edit!!
top.currentID = "' . $this->we_editCatID . '";
if(top.currentID){
	top.fsheader.enableDelBut();
	top.showPref(top.currentID);
}';
		}

		echo we_html_element::jsElement(
				$js .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				'top.fsfooter.document.we_form.fname.value = "";
top.selectFile(' . $this->we_editCatID . ');top.makeNewFolder = 0;') .
		'</head><body></body></html>';
	}

	protected function printCmdHTML(){
		echo we_html_element::jsElement('
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				(intval($this->dir) == 0 ? '
top.fsheader.disableRootDirButs();
top.fsheader.disableDelBut();' : '
top.fsheader.enableRootDirButs();
top.fsheader.enableDelBut();' ) . '
top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";');
	}

	function renameChildrenPath($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$path = f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($id));
		$db->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT("' . $path . '","/",Text) ParentID=' . intval($id));
		$db->query('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE IsFolder=1 AND ParentID=' . intval($id));
		$updates = $db->getAll(true);
		foreach($updates as $id){
			$this->renameChildrenPath($id, $db);
		}
	}

	function CatInUse($id, $IsDir, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		if($IsDir){
			return $this->DirInUse($id, $db);
		}
		if(f('SELECT 1  FROM ' . FILE_TABLE . ' WHERE FIND_IN_SET(' . intval($id) . ',Category) OR FIND_IN_SET(' . intval($id) . ',temp_category) LIMIT 1', '', $db)){
			return true;
		}
		if(defined('OBJECT_TABLE') && f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE FIND_IN_SET(' . intval($id) . ',Category) LIMIT 1', '', $db)){
			return true;
		}

		return false;
	}

	function DirInUse($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		if($this->CatInUse($id, 0, $db)){
			return true;
		}

		$db->query('SELECT ID,IsFolder FROM ' . $db->escape($this->table) . ' WHERE ParentID=' . intval($id));
		while($db->next_record()){
			if($this->CatInUse($db->f("ID"), $db->f("IsFolder"))){
				return true;
			}
		}
		return false;
	}

	function printDoDelEntryHTML(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop();

		if(($catsToDel = we_base_request::_(we_base_request::INTLISTA, 'todel', array()))){
			$finalDelete = array();
			$catlistNotDeleted = "";
			$changeToParent = false;
			foreach($catsToDel as $id){
				$IsDir = f('SELECT IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), "", $this->db);
				if($this->CatInUse($id, $IsDir)){
					$catlistNotDeleted .= id_to_path($id, CATEGORY_TABLE) . "\\n";
				} else {
					$finalDelete[] = array('id' => $id, 'IsDir' => $IsDir);
				}
			}
			if($finalDelete){
				foreach($finalDelete as $foo){
					if($foo['IsDir']){
						$this->delDir($foo['id']);
					} else {
						$this->delEntry($foo['id']);
					}
					if($this->dir == $foo['id']){
						$changeToParent = true;
					}
				}
			}
			if($catlistNotDeleted){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('fileselector', '[cat_in_use]') . '\n\n' . $catlistNotDeleted, we_message_reporting::WE_MESSAGE_ERROR));
			}
			if($changeToParent){
				$this->dir = $this->values['ParentID'];
			}
			$this->id = $this->dir;
			$hash = ($this->id ?
							getHash('SELECT Path,Text FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->id), $this->db) :
							array('Path' => '', 'Text' => ''));
			$Path = $hash['Path'];
			$Text = $hash['Text'];

			echo we_html_element::jsElement(
					'top.clearEntries();' .
					$this->printCmdAddEntriesHTML() .
					$this->printCMDWriteAndFillSelectorHTML() .
					'top.makeNewFolder = false;
top.currentPath = "' . $Path . '";
top.currentID = "' . $this->id . '";
top.selectFile(' . $this->id . ');
if(top.currentID && top.fsfooter.document.we_form.fname.value != ""){
	top.fsheader.enableDelBut();
}');
		}
		echo '</head><body></body></html>';

		return;
	}

	function delDir($id){
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($id));
		$entries = $this->db->getAll(true);
		foreach($entries as $entry){
			$this->delDir($entry);
		}
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=0 AND ParentID=' . intval($id));
		$entries = $this->db->getAll(true);
		$entries[] = $id;
		$this->delEntry($entries);
	}

	function delEntry($id){
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID IN (' . (is_array($id) ? implode(',', $id) : intval($id)) . ')');
	}

	protected function printFooterTable(){
		if($this->values['Text'] === '/'){
			$this->values['Text'] = '';
		}
		$csp = $this->noChoose ? 4 : 5;

		$okBut = (!$this->noChoose ? we_html_button::create_button('ok', 'javascript:press_ok_button();') : '');
		$cancelbut = we_html_button::create_button('close', 'javascript:top.exit_close();');
		$buttons = ($okBut ? we_html_button::position_yes_no_cancel($okBut, null, $cancelbut) : $cancelbut);
		return '
<table class="footer">
	<tr>
		<td></td>
		<td class="defaultfont"><b>' . g_l('fileselector', '[catname]') . '</b></td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
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

	protected function getFrameset(){
		$isMainChooser = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'openCatselector' && !(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 3) || we_base_request::_(we_base_request::JS, 'we_cmd', false, 5));
		return
				STYLESHEET .
				we_html_element::cssLink(CSS_DIR . 'selectors.css') .
				'<body class="selector">' .
				we_html_element::htmlIFrame('fsheader', $this->getFsQueryString(we_selector_file::HEADER), '', '', '', false) .
				we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true, ($isMainChooser ? 'catproperties' : '')) .
				($isMainChooser ?
						we_html_element::htmlIFrame('fsvalues', $this->getFsQueryString(we_selector_file::PROPERTIES), '', '', '', true) : ''
				) .
				we_html_element::htmlIFrame('fsfooter', $this->getFsQueryString(we_selector_file::FOOTER), '', '', '', false) .
				we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
				'</body>
</html>';
	}

	function printChangeCatHTML(){
		if(!($catId = we_base_request::_(we_base_request::INT, "catid"))){
			return;
		}
		$db = $GLOBALS['DB_WE'];
		$result = getHash('SELECT Category,Title,Description,ParentID,Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . $catId, $db);
		$title = we_base_request::_(we_base_request::STRING, "catTitle", $result["Title"]);
		$description = we_base_request::_(we_base_request::RAW, "catDescription", $result["Description"]);
		$path = $result['Path'];
		$parentid = we_base_request::_(we_base_request::INT, 'FolderID', $result['ParentID']);
		$category = we_base_request::_(we_base_request::STRING, 'Category', $result['Category']);
		$targetPath = id_to_path($parentid, CATEGORY_TABLE);

		$js = '';
		if(preg_match('|^' . preg_quote($path, '|') . '|', $targetPath) || preg_match('|^' . preg_quote($path, '|') . '/|', $targetPath)){
			// Verschieben nicht m�glich
			$parentid = $result['ParentID'];

			if($parentid == 0){
				$parentPath = '/';
				$path = '/' . $category;
			} else {
				$tmp = explode('/', $path);
				array_pop($tmp);
				$parentPath = implode('/', $tmp);
				$path = $parentPath . '/' . $category;
			}
			$js = "top.frames.fsvalues.document.we_form.elements.FolderID.value = '" . $parentid . "';top.frames.fsvalues.document.we_form.elements.FolderIDPath.value = '" . $parentPath . "';";
		} else {
			$path = ($parentid ? $targetPath : '') . '/' . $category;
		}
		$updateok = $db->query('UPDATE ' . CATEGORY_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'Category' => $category,
					'Text' => $category,
					'Path' => $path,
					'ParentID' => $parentid,
					'Title' => $title,
					'Description' => $description,
					'Catfields' => serialize(array('default' => array('Title' => $title, 'Category' => $category)))//FIXME: remove in 6.5
				)) . ' WHERE ID=' . $catId);

		$updateok &= $this->saveFileLinks($catId, we_wysiwyg_editor::reparseInternalLinks($description));

		if($updateok){
			$this->renameChildrenPath($catId);
		}
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop() .
		we_html_element::jsElement($js . 'top.setDir(top.fsheader.document.we_form.elements.lookin.value);' .
				($updateok ? we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][response_save_ok]'), $category), we_message_reporting::WE_MESSAGE_NOTICE) : we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][response_save_notok]'), $category), we_message_reporting::WE_MESSAGE_ERROR) )
		) .
		'</head><body></body></html>';
	}

	function printPropertiesHTML(){
		$showPrefs = we_base_request::_(we_base_request::INT, 'catid', 0);

		$path = $title = '';
		$variant = isset($_SESSION['weS']["we_catVariant"]) ? $_SESSION['weS']["we_catVariant"] : "default";
		$_SESSION['weS']["we_catVariant"] = $variant;
		$description = "";
		if($showPrefs){
			$db = new DB_WE();
			$result = getHash('SELECT ID,Category,Title,Description,Path,ParentID FROM ' . CATEGORY_TABLE . ' WHERE ID=' . $showPrefs, $db);

			$path = ($result["ParentID"] ?
							(f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($result["ParentID"]), '', $db)? :
									'/'
							) :
							'/');

			$parentId = $result ? $result["ParentID"] : 0;
			$category = $result ? $result["Category"] : '';
			$catID = $result ? intval($result["ID"]) : 0;
			$title = $result ? $result['Title'] : '';
			$description = $result ? $result["Description"] : '';

			$dir_chooser = we_html_button::create_button('select', "javascript:we_cmd('openSelector', document.we_form.elements.FolderID.value, '" . CATEGORY_TABLE . "', 'document.we_form.elements.FolderID.value', 'document.we_form.elements.FolderIDPath.value', '', '', '', '1', '', 'false', 1)");

			$yuiSuggest = &weSuggest::getInstance();
			$yuiSuggest->setAcId('Doc');
			$yuiSuggest->setTable(CATEGORY_TABLE);
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput('FolderIDPath', $path);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(false);
			$yuiSuggest->setResult('FolderID', $parentId);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setWidth(250);
			$yuiSuggest->setSelectButton($dir_chooser, 10);
			$yuiSuggest->setContainerWidth(350);

			$table = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0), 4, 3);

			$table->setCol(0, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), '<b>' . g_l('weClass', '[category]') . '</b>');
			$table->setCol(0, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), we_html_tools::htmlTextInput("Category", 50, $category, "", ' id="category"', "text", 360));

			$table->setCol(1, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), "<b>ID</b>");
			$table->setCol(1, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), $catID);

			$table->setCol(2, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), '<b>' . g_l('weClass', '[dir]') . '</b>');
			$table->setCol(2, 1, array("style" => "width:240px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), $yuiSuggest->getHTML());

			$table->setCol(3, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), "<b>" . g_l('global', '[title]') . "</b>");
			$table->setCol(3, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), we_html_tools::htmlTextInput("catTitle", 50, $title, "", '', "text", 360));

			$ta = we_html_tools::htmlFormElementTable(we_html_forms::weTextarea("catDescription", $description, array("bgcolor" => "white", "inlineedit" => "true", "wysiwyg" => "true", "width" => 450, "height" => 130), true, 'autobr', true, "", true, true, true, false, ""), "<b>" . g_l('global', '[description]') . "</b>", "left", "defaultfont", "", "", "", "", "", 0);
			$saveBut = we_html_button::create_button("save", "javascript:weWysiwygSetHiddenText();we_checkName();");
		}

		we_html_tools::protect();

		echo we_html_tools::getHtmlTop() .
		STYLESHEET . we_html_element::jsScript(JS_DIR . 'we_textarea.js') . we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
function we_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?";
		for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]){
		case "openSelector":
			new jsWindow(url,"we_selector",-1,-1,' . self::WINDOW_SELECTOR_WIDTH . ',' . self::WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
			break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			parent.we_cmd.apply(this, args);

	}
}
function we_checkName() {
	var regExp = /\'|"|>|<|\\\|\\//;
	if(regExp.test(document.getElementById("category").value)) {' .
				we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $path), we_message_reporting::WE_MESSAGE_ERROR) . '
	} else {
		document.we_form.submit();
	}
}') .
		weSuggest::getYuiFiles() .
		'</head><body class="defaultfont weDialogBody" style="padding: 15px 0 0 10px;">
' . ($showPrefs ? '
	<form onsubmit="weWysiwygSetHiddenText();"; action="' . $_SERVER["SCRIPT_NAME"] . '" name="we_form" method="post" target="fscmd"><input type="hidden" name="what" value="' . self::CHANGE_CAT . '" /><input type="hidden" name="catid" value="' . we_base_request::_(we_base_request::INT, 'catid', 0) . '" />
		' . $table->getHtml() . "<br/>" . $ta . "<br/>" . $saveBut . '
	</div>' : '' ) .
		(isset($yuiSuggest) ?
				$yuiSuggest->getYuiJs() : '') .
		'</body></html>';
	}

	function saveFileLinks($id, $fileLinks){// FIXME: use object property for $id and $fileLinks
		// FIXME: maybe move this function to sme new fileLink class
		$db = $GLOBALS['DB_WE'];
		$ret = $db->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($id) . ' AND DocumentTable="' . stripTblPrefix(CATEGORY_TABLE) . '" AND type="media"');
		if(!empty($fileLinks)){
			$whereType = 'AND ContentType IN ("' . we_base_ContentTypes::APPLICATION . '","' . we_base_ContentTypes::FLASH . '","' . we_base_ContentTypes::IMAGE . '","' . we_base_ContentTypes::QUICKTIME . '","' . we_base_ContentTypes::VIDEO . '")';
			$db->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE ID IN (' . implode(',', array_unique($fileLinks)) . ') ' . $whereType);
			$fileLinks = array();
			while($db->next_record()){
				$fileLinks[] = $db->f('ID');
			}
		}

		if(!empty($fileLinks)){
			foreach(array_unique($fileLinks) as $remObj){
				$ret &= $db->query('INSERT INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
							'ID' => $id,
							'DocumentTable' => stripTblPrefix(CATEGORY_TABLE),
							'type' => 'media',
							'remObj' => $remObj,
							'remTable' => stripTblPrefix(FILE_TABLE),
							'position' => 0,
				)));
			}
		}

		return $ret;
	}

}
