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
/* the parent class of storagable webEdition classes */
we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT);

class we_export_view extends we_modules_view{
	var $export;
	var $editorBodyDoc;
	private $page;

	public function __construct($frameset){
		$topframe = "top.content";
		parent::__construct($frameset, $topframe);
		$this->export = new we_export_export();
	}

	function getCommonHiddens($cmds = array()){
		return
			parent::getCommonHiddens($cmds) .
			we_html_element::htmlHiddens(array(
				"table" => we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE),
				"ID" => $this->export->ID,
				"IsFolder" => $this->export->IsFolder,
				"selDocs" => $this->export->selDocs,
				"selTempl" => $this->export->selTempl,
				"selObjs" => $this->export->selObjs,
				"selClasses" => $this->export->selClasses,
				"selDocs_open" => we_base_request::_(we_base_request::INTLIST, "selDocs_open", ''),
				"selTempl_open" => we_base_request::_(we_base_request::INTLIST, "selTempl_open", ''),
				"selObjs_open" => we_base_request::_(we_base_request::INTLIST, "selObjs_open", ''),
				"selClasses_open" => we_base_request::_(we_base_request::INTLIST, "selClasses_open", '')
		));
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return we_html_element::jsElement('
var get_focus = 1;
var activ_tab = 1;
var hot= 0;
var scrollToVal=0;
var table = WE().consts.dirs.FILE_TABLE;
WE().consts.dirs.WE_EXPORT_MODULE_DIR="' . WE_EXPORT_MODULE_DIR . '";

WE().consts.g_l.exports={
	save_changed_export:"' . g_l('export', '[save_changed_export]') . '",
	delete_question:"' . g_l('export', '[delete_question]') . '",
	delete_group_question:"' . g_l('export', '[delete_group_question]') . '",
	no_perms:"' . we_message_reporting::prepareMsgForJS(g_l('export', '[no_perms]')) . '",
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('export', '[nothing_to_delete]')) . '",
	must_save:"' . we_message_reporting::prepareMsgForJS(g_l('export', '[must_save]')) . '",
	name_empty:"' . we_message_reporting::prepareMsgForJS(g_l('export', '[name_empty]')) . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('export', '[nothing_to_save]')) . '",
};
parent.document.title = "' . $title . '"
') . we_html_element::jsScript(WE_JS_MODULES_DIR . '/export/export_top.js');
	}

	function getJSProperty(){
		$selected = '';
		$opened = '';
		$arr = array(FILE_TABLE => "selDocs", TEMPLATES_TABLE => "selTempl");
		if(defined('OBJECT_TABLE')){
			$arr[OBJECT_FILES_TABLE] = 'selObjs';
			$arr[OBJECT_TABLE] = 'selClasses';
		}

		foreach($arr as $table => $elem){
			$items = makeArrayFromCSV($this->export->$elem);
			foreach($items as $item){
				$selected .= 'SelectedItems["' . $table . '"].push("' . $item . '");';
			}

			if(($open = we_base_request::_(we_base_request::STRING, $elem . '_open'))){
				$opened .= 'openFolders["' . $table . '"]="' . $open . '";';
			}
		}

		return we_html_element::jsElement('

var table = "' . we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE) . '";
var data={
	frameset:"' . $this->frameset . '"
};

function start() {
	' . $selected . $opened . ( $this->export->IsFolder == 0 ? '
	setHead(top.content.editor.edbody.table);' : '') . '
}') . we_html_element::jsScript(WE_JS_MODULES_DIR . 'export/export_prop.js');
	}

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, "cmd")){
			case "new_export":
				if(!permissionhandler::hasPerm("NEW_EXPORT")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				} else {
					$this->export = new we_export_export();
					echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edheader&text=' . urlencode($this->export->Text) . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edfooter";
						');
				}

				break;
			case "new_export_group":
				if(!permissionhandler::hasPerm("NEW_EXPORT")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				} else {
					$this->export = new we_export_export();
					$this->export->Text = g_l('export', '[newFolder]');
					$this->export->IsFolder = 1;
					echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edheader&text=' . urlencode($this->export->Text) . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edfooter";
						');
				}
				break;
			case "export_edit":
				if(!permissionhandler::hasPerm("EDIT_EXPORT")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				$this->export = new we_export_export(we_base_request::_(we_base_request::INT, "cmdid"));
				echo we_html_element::jsElement('
top.content.hot=0;
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edheader&text=' . urlencode($this->export->Text) . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=export&pnt=edfooter";
						');

				break;
			case "save_export":
				if(!permissionhandler::hasPerm("NEW_EXPORT")){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				$js = "";
				if($this->export->filenameNotValid($this->export->Text)){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				// check if filename is valid.
				if($this->export->exportToFilenameValid($this->export->Filename)){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', '[wrongfilename]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}

				if(!trim($this->export->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				$oldpath = $this->export->Path;
				// set the path and check it
				$this->export->setPath();
				if($this->export->pathExists($this->export->Path)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if($this->export->isSelf()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->export->ParentID > 0){
					$weAcQuery = new we_selector_query();
					$weAcResult = $weAcQuery->getItemById($this->export->ParentID, EXPORT_TABLE, array("IsFolder"));
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
				}
				if(!empty($this->export->Folder) && $this->export->ParentID > 0){
					$weAcQuery = new we_selector_query();
					$weAcResult = $weAcQuery->getItemById($this->export->Folder, FILE_TABLE, array("IsFolder"));
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						break;
					}
				}

				$newone = true;
				if($this->export->ID){
					$newone = false;
				}

				$this->export->save();

				if($this->export->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->export->Path){
					$db_tmp = new DB_WE();
					$this->db->query('SELECT ID FROM ' . EXPORT_TABLE . ' WHERE Path LIKE \'' . $this->db->escape($oldpath) . '%\' AND ID!=' . intval($this->export->ID) . ';');
					while($this->db->next_record()){
						$db_tmp->query('UPDATE ' . EXPORT_TABLE . ' SET Path=\'' . $this->export->evalPath($this->db->f("ID")) . '\' WHERE ID=' . $this->db->f("ID") . ';');
					}
				}

				$js = ($newone ?
						'top.content.treeData.makeNewEntry({id:\'' . $this->export->ID . '\',parentid:\'' . $this->export->ParentID . '\',text:\'' . $this->export->Text . '\',open:0,contenttype:\'' . ($this->export->IsFolder ? 'folder' : 'we/export') . '\',table:\'' . EXPORT_TABLE . '\'});' .
						'top.content.drawTree();' :
						'top.content.treeData.updateEntry({id:' . $this->export->ID . ',text:"' . $this->export->Text . '",parentid:"' . $this->export->ParentID . '"});'
					);
				echo we_html_element::jsElement(
					$js .
					'top.content.editor.edheader.location.reload();' .
					we_message_reporting::getShowMessageCall(g_l('export', ($this->export->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) .
					'top.content.hot=0;'
				);

				break;
			case "delete_export":
				if(!permissionhandler::hasPerm("DELETE_EXPORT")){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('export', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					return;
				}

				if($this->export->delete()){
					echo we_html_element::jsElement('
									top.content.treeData.deleteEntry(' . $this->export->ID . ');
									' . we_message_reporting::getShowMessageCall(g_l('export', ($this->export->IsFolder ? '[delete_group_ok]' : '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '
									top.content.we_cmd("home");
							');
					$this->export = new we_export_export();
				} else {
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('export', ($this->export->IsFolder == 1 ? '[delete_group_nok]' : '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR)
					);
				}

				break;
			case "start_export":
				we_exim_XMLExIm::unsetPerserves();
				$_REQUEST["cmd"] = "do_export";
				$this->export->ExportFilename = ($this->export->ExportTo === 'local' ? TEMP_PATH . $this->export->Filename : $_SERVER['DOCUMENT_ROOT'] . $this->export->ServerPath . "/" . $this->export->Filename);
				break;
			default:
		}

		$_SESSION['weS']['export_session'] = $this->export;
	}

	function processVariables(){//FIXME use table datatypes
		if(isset($_SESSION['weS']['export_session'])){
			$this->export = $_SESSION['weS']['export_session'];
		}

		if(isset($_SESSION['weS']['exportVars_session'])){
			unset($_SESSION['weS']['exportVars_session']);
		}

		if(is_array($this->export->persistent_slots)){
			foreach($this->export->persistent_slots as $varname){
				if(($v = we_base_request::_(we_base_request::STRING, $varname))){//FIXME: this is quiet for now....
					$this->export->{$varname} = $v;
				}
			}
		}

		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
	}

	public function getHomeScreen(){
		$hiddens["cmd"] = "home";
		$content = we_html_button::create_button('new_export', "javascript:top.we_cmd('new_export');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_EXPORT")) . '<br/>' .
			we_html_button::create_button('new_export_group', "javascript:top.we_cmd('new_export_group');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_EXPORT"));
		return parent::getHomeScreen("export", "export.gif", $content, we_html_element::htmlForm(array("name" => "we_form"), $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden("home", 0)
		));
	}

}
