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
		$this->filter = '';
	}

	public function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		switch($what){
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
			default:
				parent::printHTML($what);
		}
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/category_selector.js');
	}

	protected function getFsQueryString($what){
		return $_SERVER['SCRIPT_NAME'] . 'what=' . $what . '&table=' . $this->table . '&id=' . $this->id . '&order=' . $this->order . '&noChoose=' . $this->noChoose;
	}

	protected function printHeaderTable(we_base_jsCmd $weCmd, $extra = '', $append = false){
		return '
<table class="selectorHeaderTable">
	<tr style="vertical-align:middle">
		<td class="defaultfont lookinText">' . g_l('fileselector', '[lookin]') . '</td>
		<td class="lookin"><select name="lookin" id="lookin" class="weSelect" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%"></select></td>
		<td>' . we_html_button::create_button('root_dir', "javascript:top.setRootDir();", true, 0, 0, '', '', $this->dir == intval($this->rootDirID), false) . '</td>
		<td>' . we_html_button::create_button('fa:btn_fs_back,fa-lg fa-level-up,fa-lg fa-tag', "javascript:top.goBackDir();", true, 0, 0, '', '', $this->dir == intval($this->rootDirID), false) . '</td>' .
			($this->userCanEditCat() ?
				'<td style="width:38px;">' . we_html_button::create_button('fa:btn_add_cat,fa-plus,fa-lg fa-tag', 'javascript:top.drawNewCat();', true, 0, 0, '', '', false, false) . '</td>' : '') .
			($this->userCanEditCat() ?
				'<td class="trash">' . we_html_button::create_button(we_html_button::TRASH, 'javascript:if(changeCatState==1){top.deleteEntry();}', true, 27, 22, '', '', false, false) . '</td>' : '') .
			'</tr>
</table>';
	}

	function userCanEditCat(){
		return permissionhandler::hasPerm('EDIT_KATEGORIE');
	}

	function userCanChangeCat(){
		return ($this->userCanEditCat() && $this->id > 0);
	}

	protected function setFramesetJavaScriptOptions(){
		parent::setFramesetJavaScriptOptions();
		$this->jsoptions['options']['userCanEditCat'] = intval($this->userCanEditCat());
		$this->jsoptions['data']['makeNewCat'] = false;
		$this->jsoptions['data']['we_editCatID'] = 0;
		$this->jsoptions['data']['noChoose'] = intval($this->noChoose);
		$this->jsoptions['data']['changeCatState'] = $this->userCanChangeCat();
	}

	function printCreateEntryHTML(){
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = trim($this->EntryText);
		$Path = ($txt ? (!intval($this->dir) ? '' : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), '', $this->db)) . '/' . $txt : '');

		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		if(empty($txt)){
			$weCmd->addCmd('msg', ['msg' => g_l('weEditor', '[category][filename_empty]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} else if(strpos($txt, ',') !== false){
			$weCmd->addCmd('msg', ['msg' => g_l('weEditor', '[category][name_komma]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} elseif(f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($Path) . '" LIMIT 1', '', $this->db) === '1'){
			$weCmd->addCmd('msg', ['msg' => sprintf(g_l('weEditor', '[category][response_path_exists]'), $Path), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} elseif(preg_match('|[\\\'"<>/]|', $txt)){
			$weCmd->addCmd('msg', ['msg' => sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $Path), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} else {
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter([
					'Category' => $txt,
					'ParentID' => intval($this->dir),
					'Text' => $txt,
					'Path' => $Path,
			]));
			$folderID = $this->db->getInsertId();
			$weCmd->addCmd('updateSelectData', [
				'currentPath' => $Path,
				'currentID' => $folderID,
				'makeNewCat' => false,
			]);
			$weCmd->addCmd('newCatSuccess');
		}
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setSelectorData($weCmd);

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function printHeaderHeadlines(){
		return '
<table class="headerLines" style="width:100%">
	<tr>
		<td class="selector bold" style="width:35%;padding-left:10px;"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[catname]') . '</a></td>
		<td class="selector bold" style="width:65%;padding-left:10px;">' . g_l('button', '[properties][value]') . '</td>
	</tr>
</table>';
	}

	function printDoRenameEntryHTML(){
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = trim($this->EntryText);
		$Path = ($txt ? (!intval($this->dir) ? '' : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), '', $this->db)) . '/' . $txt : '');
		$weCmd = new we_base_jsCmd();
		$weCmd->addCmd('clearEntries');

		if(!$txt){
			$weCmd->addCmd('msg', ['msg' => g_l('weEditor', '[category][filename_empty]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} elseif(strpos($txt, ',') !== false){
			$weCmd->addCmd('msg', ['msg' => g_l('weEditor', '[category][name_komma]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} elseif(f('SELECT 1 FROM ' . $this->db->escape($this->table) . ' WHERE Path="' . $this->db->escape($Path) . '" AND ID!=' . intval($this->we_editCatID) . ' LIMIT 1', '', $this->db)){
			$weCmd->addCmd('msg', ['msg' => sprintf(g_l('weEditor', '[category][response_path_exists]'), $Path), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} elseif(preg_match('|[\'"<>/]|', $txt)){
			$weCmd->addCmd('msg', ['msg' => sprintf(g_l('weEditor', '[category][we_filename_notValid]'), $Path), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		} elseif(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editCatID), '', $this->db) != $txt){
			$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter([
					'Category' => $txt,
					'ParentID' => intval($this->dir),
					'Text' => $txt,
					'Path' => $Path,
				]) .
				' WHERE ID=' . intval($this->we_editCatID));
			$this->renameChildrenPath($this->we_editCatID);
			$weCmd->addCmd('updateSelectData', [
				'currentPath' => $Path,
				'currentID' => $this->we_editCatID
			]);
			$weCmd->addCmd('postRenameCat');
		}
		$weCmd->addCmd('updateSelectData', [
			'makeNewCat' => false,
			'currentText' => ''
		]);
		$this->printCmdAddEntriesHTML($weCmd);
		$this->setSelectorData($weCmd);

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', $weCmd->getCmds(), we_html_element::htmlBody());
	}

	protected function printCmdHTML(we_base_jsCmd $weCmd){
		$weCmd->addCmd('setButtons', [['DelBut', $this->dir ? true : false]]);
		parent::printCmdHTML($weCmd);
	}

	private function renameChildrenPath($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		$db->query('UPDATE ' . CATEGORY_TABLE . ' SET Path=CONCAT(SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($id) . ',"/",Text) WHERE ParentID=' . intval($id));
		$db->query('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE ParentID=' . intval($id)); //IsFolder=1 AND
		$updates = $db->getAll(true);
		foreach($updates as $id){
			$this->renameChildrenPath($id, $db);
		}
	}

	function CatInUse($id, $IsDir, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		if(f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE FIND_IN_SET(' . intval($id) . ',Category) OR FIND_IN_SET(' . intval($id) . ',temp_category) LIMIT 1', '', $db) ||
			(defined('OBJECT_TABLE') && f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE FIND_IN_SET(' . intval($id) . ',Category) LIMIT 1', '', $db))){
			return true;
		}
		if($IsDir){
			return $this->DirInUse($id, $db);
		}

		return false;
	}

	function DirInUse($id, we_database_base $db = null){
		$db = $db ? : new DB_WE();
		if($this->CatInUse($id, 0, $db)){
			return true;
		}

		$db->query('SELECT ID FROM ' . $db->escape($this->table) . ' WHERE ParentID=' . intval($id));
		while($db->next_record()){
			if($this->CatInUse($db->f("ID"), 1/* $db->f("IsFolder") */)){
				return true;
			}
		}
		return false;
	}

	function printDoDelEntryHTML(){
		$weCmd = new we_base_jsCmd();

		if(($catsToDel = we_base_request::_(we_base_request::INTLISTA, 'todel', []))){
			$finalDelete = [];
			$catlistNotDeleted = "";
			$changeToParent = false;
			foreach($catsToDel as $id){
				$IsDir = 1; //f('SELECT IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), "", $this->db);
				if($this->CatInUse($id, $IsDir)){
					$catlistNotDeleted .= id_to_path($id, CATEGORY_TABLE) . '\n';
				} else {
					$finalDelete[] = ['id' => $id, 'IsDir' => $IsDir];
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
				$weCmd->addCmd('msg', ['msg' => g_l('fileselector', '[cat_in_use]') . '\n\n' . $catlistNotDeleted, 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
			}
			if($changeToParent){
				$this->dir = $this->values['ParentID'];
			}
			$this->id = $this->dir;
			$Path = ($this->id ? f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->id), '', $this->db) : '');
			$weCmd = new we_base_jsCmd();
			$weCmd->addCmd('clearEntries');
			$weCmd->addCmd('updateSelectData', [
				'makeNewCat' => false,
				'currentPath' => $Path,
				'currentID' => $this->id
			]);
			$this->printCmdAddEntriesHTML($weCmd);
			$weCmd->addCmd('setButtons', [['DelBut', $Path ? true : false]]);
			$this->setSelectorData($weCmd);
		}
		echo we_html_tools::getHtmlTop('', '', '', $weCmd->getCmds(), we_html_element::htmlBody());

		return;
	}

	function delDir($id){
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($id)); //IsFolder=1 AND
		$entries = $this->db->getAll(true);
		foreach($entries as $entry){
			$this->delDir($entry);
		}
		$this->db->query('SELECT ID FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($id));
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

		$okBut = (!$this->noChoose ? we_html_button::create_button(we_html_button::OK, 'javascript:press_ok_button();') : '');
		$cancelbut = we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.exit_close();');

		return '
<table id="footer">
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[catname]') . '</td>
		<td class="defaultfont" style="text-align:left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '</td>
	</tr>
</table><div id="footerButtons">' . ($okBut ? we_html_button::position_yes_no_cancel($okBut, null, $cancelbut) : $cancelbut) . '</div>';
	}

	protected function getFrameset(we_base_jsCmd $weCmd, $withPreview = false){
		$isMainChooser = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'we_selector_category' && !(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 3) || we_base_request::_(we_base_request::JS, 'we_cmd', false, 5));
		return '<body class="selector" onload="self.focus();">' .
			we_html_element::htmlDiv(['id' => 'fsheader'], $this->printHeaderHTML($weCmd)) .
			we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true, ($isMainChooser ? 'catproperties' : '')) .
			($isMainChooser ?
				we_html_element::htmlIFrame('fsvalues', $this->getFsQueryString(we_selector_file::PROPERTIES), '', '', '', true, ($isMainChooser ? 'catproperties' : '')) : ''
			) .
			we_html_element::htmlDiv(['id' => 'fsfooter'], $this->printFooterTable()) .
			we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
			'</body>';
	}

	function printChangeCatHTML(){
		if(!($catId = we_base_request::_(we_base_request::INT, "catid"))){
			return;
		}
		$db = $GLOBALS['DB_WE'];
		$result = getHash('SELECT Category,Title,Description,ParentID,Path FROM ' . CATEGORY_TABLE . ' WHERE ID=' . $catId, $db);
		$title = we_base_request::_(we_base_request::STRING, 'catTitle', $result["Title"]);
		$description = we_base_request::_(we_base_request::RAW, 'catDescription', $result["Description"]);
		$path = $result['Path'];
		$parentid = we_base_request::_(we_base_request::INT, 'FolderID', $result['ParentID']);
		$category = we_base_request::_(we_base_request::STRING, 'Category', $result['Category']);
		$targetPath = id_to_path($parentid, CATEGORY_TABLE);

		$weCmd = new we_base_jsCmd();
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
			$weCmd->addCmd('updateCatChooserButton', [$parentid, $parentPath]);
		} else {
			$path = ($parentid ? $targetPath : '') . '/' . $category;
		}
		$updateok = $db->query('UPDATE ' . CATEGORY_TABLE . ' SET ' . we_database_base::arraySetter([
				'Category' => $category,
				'Text' => $category,
				'Path' => $path,
				'ParentID' => $parentid,
				'Title' => $title,
				'Description' => $description,
			]) . ' WHERE ID=' . $catId);

		if($updateok){
			$this->renameChildrenPath($catId);

			$cat = new we_category($catId);
			$cat->registerMediaLinks();
			$weCmd->addCmd('msg', ['msg' => sprintf(g_l('weEditor', '[category][response_save_ok]'), $category), 'prio' => we_message_reporting::WE_MESSAGE_NOTICE]);
		} else {
			$weCmd->addCmd('msg', ['msg' => sprintf(g_l('weEditor', '[category][response_save_notok]'), $category), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
		}
		$weCmd->addCmd('setLookinDir');

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '',  $weCmd->getCmds(), we_html_element::htmlBody());
	}

	function printPropertiesHTML(){
		$showPrefs = we_base_request::_(we_base_request::INT, 'catid', 0);

		$path = $title = '';
		$variant = isset($_SESSION['weS']["we_catVariant"]) ? $_SESSION['weS']["we_catVariant"] : "default";
		$_SESSION['weS']["we_catVariant"] = $variant;
		$description = "";
		if($showPrefs){
			$result = getHash('SELECT c.ID,c.Category,c.Title,c.Description,c.Path,c.ParentID,cc.Path AS PPath FROM ' . CATEGORY_TABLE . ' c LEFT JOIN ' . CATEGORY_TABLE . ' cc ON c.ParentID=cc.ID WHERE c.ID=' . $showPrefs);

			$path = ($result['ParentID'] ? ($result['PPath']? : '/' ) : '/');

			$parentId = $result ? $result['ParentID'] : 0;
			$category = $result ? $result['Category'] : '';
			$catID = $result ? intval($result['ID']) : 0;
			$title = $result ? $result['Title'] : '';
			$description = $result ? $result['Description'] : '';

			$dir_chooser = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_file', document.we_form.elements.FolderID.value, '" . CATEGORY_TABLE . "', 'document.we_form.elements.FolderID.value', 'document.we_form.elements.FolderIDPath.value', '', '', '', '1', '', 'false', 1)");

			$yuiSuggest = &weSuggest::getInstance();
			$yuiSuggest->setAcId('Doc');
			$yuiSuggest->setTable(CATEGORY_TABLE);
			$yuiSuggest->setContentType('');
			$yuiSuggest->setInput('FolderIDPath', $path);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(false);
			$yuiSuggest->setResult('FolderID', $parentId);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setWidth(250);
			$yuiSuggest->setSelectButton($dir_chooser, 10);
			$yuiSuggest->setContainerWidth(350);

			$table = new we_html_table(['class' => 'default'], 6, 3);

			$table->setCol(0, 0, ['style' => 'width:100px; padding: 0px 0px 10px 0px;', 'class' => 'defaultfont'], '<b>' . g_l('weClass', '[category]') . '</b>');
			$table->setCol(0, 1, ['colspan' => 2, 'style' => 'width:350px; padding: 0px 0px 10px 0px;', 'class' => 'defaultfont'], we_html_tools::htmlTextInput("Category", 50, $category, "", ' id="category"', "text", 360));

			$table->setCol(1, 0, ['style' => 'width:100px; padding: 0px 0px 10px 0px;', 'class' => 'defaultfont'], "<b>ID</b>");
			$table->setCol(1, 1, ['colspan' => 2, 'style' => 'width:350px; padding: 0px 0px 10px 0px;', 'class' => 'defaultfont'], $catID);

			$table->setCol(2, 0, ["style" => "width:100px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], '<b>' . g_l('weClass', '[dir]') . '</b>');
			$table->setCol(2, 1, ["style" => "width:240px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], $yuiSuggest->getHTML());

			$table->setCol(3, 0, ["style" => "width:100px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], "<b>" . g_l('global', '[title]') . "</b>");
			$table->setCol(3, 1, ["colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], we_html_tools::htmlTextInput("catTitle", 50, $title, "", '', "text", 360));
			$table->setCol(4, 0, ["style" => "width:100px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], "<b>" . g_l('global', '[description]') . "</b>");
			$table->setCol(4, 1, ["colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], we_html_forms::weTextarea("catDescription", $description, ["bgcolor" => "white",
					"inlineedit" => "true",
					"wysiwyg" => "true",
					"width" => 450,
					"height" => 130,
					'commands' => 'prop,fontsize,xhtmlxtras,color,justify,list,link,table,insert,fullscreen,visibleborders,editsource'
					], true, 'autobr', true, true, true, true, true, ""));
			$table->setCol(5, 1, ["colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", 'class' => 'defaultfont'], we_html_button::create_button(we_html_button::SAVE, "javascript:top.saveOnKeyBoard();"));
		}

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
			we_html_element::jsScript(JS_DIR . 'selectors/category_selector.js') .
			weSuggest::getYuiFiles(), '<body class="defaultfont weDialogBody" style="padding: 15px 0 0 10px;">
' . ($showPrefs ? '
	<form action="' . $_SERVER["SCRIPT_NAME"] . '" name="we_form" method="post" target="fscmd"><input type="hidden" name="what" value="' . self::CHANGE_CAT . '" /><input type="hidden" name="catid" value="' . we_base_request::_(we_base_request::INT, 'catid', 0) . '" />
		' . $table->getHtml() .
				'</div></form>' : '' ) .
			(isset($yuiSuggest) ?
				$yuiSuggest->getYuiJs() : '') .
			'</body>');
	}

}
