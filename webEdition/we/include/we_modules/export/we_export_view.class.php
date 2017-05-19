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

	public function __construct(){
		parent::__construct();
		$this->export = new we_export_export();
	}

	function getCommonHiddens($cmds = []){
		return
			parent::getCommonHiddens($cmds) .
			we_html_element::htmlHiddens(["table" => we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE),
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
		]);
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return we_html_element::jsScript(WE_JS_MODULES_DIR . '/export/export_top.js', "parent.document.title='" . $title . "'");
	}

	function getJSProperty(array $jsVars = []){

		$arr = [
			FILE_TABLE => 'selDocs',
			TEMPLATES_TABLE => 'selTempl'
		];
		if(defined('OBJECT_TABLE')){
			$arr[OBJECT_FILES_TABLE] = 'selObjs';
			$arr[OBJECT_TABLE] = 'selClasses';
		}

		$dynVars = [
			'initialTreeData' => [
					'selectedItems' => [],
					'openFolders' => []
				],
			'modelProperties' => [
				'isFolder' => intval($this->export->IsFolder),
				'currentTable' => we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE)
			]
		];

		foreach($arr as $table => $elem){
			$dynVars['initialTreeData']['selectedItems'][$table] = array_map('intval', array_filter(explode(',', $this->export->$elem)));
			$dynVars['initialTreeData']['openFolders'][$table] = array_map('intval', array_filter(explode(',', we_base_request::_(we_base_request::STRING, $elem . '_open'))));
		}

		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'export/export_prop.js', '', ['id' => 'loadVarExport_prop', 'data-dynVars' => setDynamicVar($dynVars)]);
	}

	function processCommands(we_base_jsCmd $jscmd){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'new_export':
				if(!we_base_permission::hasPerm('NEW_EXPORT')){
					$jscmd->addMsg(g_l('export', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$this->export = new we_export_export();
				$jscmd->addCmd('loadHeaderFooter', $this->export->Text);

				break;
			case 'new_export_group':
				if(!we_base_permission::hasPerm('NEW_EXPORT')){
					$jscmd->addMsg(g_l('export', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$this->export = new we_export_export();
				$this->export->Text = g_l('export', '[newFolder]');
				$this->export->IsFolder = 1;
				$jscmd->addCmd('loadHeaderFooter', $this->export->Text);

				break;
			case 'export_edit':
				if(!we_base_permission::hasPerm('EDIT_EXPORT')){
					$jscmd->addMsg(g_l('export', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$this->export = new we_export_export(we_base_request::_(we_base_request::INT, "cmdid"));
				$jscmd->addCmd('loadHeaderFooter', $this->export->Text);

				break;
			case 'save_export':
				if(!we_base_permission::hasPerm('NEW_EXPORT')){
					$jscmd->addMsg(g_l('export', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}

				if(we_export_export::filenameNotValid($this->export->Text)){
					$jscmd->addMsg(g_l('export', '[wrongtext]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				// check if filename is valid.
				if(we_export_export::exportToFilenameValid($this->export->Filename)){
					$jscmd->addMsg(g_l('export', '[wrongfilename]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}

				if(!trim($this->export->Text)){
					$jscmd->addMsg(g_l('export', '[name_empty]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$oldpath = $this->export->Path;
				// set the path and check it
				$this->export->setPath();
				if($this->export->pathExists($this->export->Path)){
					$jscmd->addMsg(g_l('export', '[name_exists]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				if($this->export->isSelf()){
					$jscmd->addMsg(g_l('export', '[path_nok]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}

				if($this->export->ParentID > 0){
					$weAcQuery = new we_selector_query();
					$weAcResult = $weAcQuery->getItemById($this->export->ParentID, EXPORT_TABLE, ["IsFolder"]);
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						$jscmd->addMsg(g_l('export', '[path_nok]'), we_base_util::WE_MESSAGE_ERROR);
						break;
					}
				}
				if(!empty($this->export->Folder) && $this->export->ParentID > 0){
					$weAcQuery = new we_selector_query();
					$weAcResult = $weAcQuery->getItemById($this->export->Folder, FILE_TABLE, ["IsFolder"]);
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						$jscmd->addMsg(g_l('export', '[path_nok]'), we_base_util::WE_MESSAGE_ERROR);
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
					$this->db->query('SELECT ID FROM ' . EXPORT_TABLE . ' WHERE Path LIKE "' . $this->db->escape($oldpath) . '%" AND ID!=' . intval($this->export->ID));
					while($this->db->next_record()){
						$db_tmp->query('UPDATE ' . EXPORT_TABLE . ' SET Path="' . $this->export->evalPath($this->db->f("ID")) . '" WHERE ID=' . $this->db->f("ID"));
					}
				}

				if($newone){
					$jscmd->addCmd('makeTreeEntry', [
						'id' => $this->export->ID,
						'parentid' => $this->export->ParentID,
						'text' => $this->export->Text,
						'open' => false,
						'contenttype' => ($this->export->IsFolder ? we_base_ContentTypes::FOLDER : 'we/export'),
						'table' => EXPORT_TABLE
					]);
					$jscmd->addCmd('drawTree');
				} else {
					$jscmd->addCmd('updateTreeEntry', [
						'id' => $this->export->ID,
						'parentid' => $this->export->ParentID,
						'text' => $this->export->Text,
					]);
				}
				$jscmd->addMsg(g_l('export', ($this->export->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_base_util::WE_MESSAGE_NOTICE);
				$jscmd->addCmd('loadHeaderFooter', $this->export->Text);

				break;
			case "delete_export":
				if(!we_base_permission::hasPerm("DELETE_EXPORT")){
					$jscmd->addMsg(g_l('export', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					return;
				}

				if($this->export->delete()){
					$jscmd->addCmd('deleteTreeEntry', $this->export->ID);
					$jscmd->addMsg(g_l('export', ($this->export->IsFolder ? '[delete_group_ok]' : '[delete_ok]')), we_base_util::WE_MESSAGE_NOTICE);
					$jscmd->addCmd('home');
					$this->export = new we_export_export();
					break;
				}
				$jscmd->addMsg(g_l('export', ($this->export->IsFolder == 1 ? '[delete_group_nok]' : '[delete_nok]')), we_base_util::WE_MESSAGE_ERROR);

				break;
			case "start_export":
				we_exim_Export::unsetPreserves(); // reset exporter
				$_REQUEST["cmd"] = "do_export";
				$this->export->ExportFilename = ($this->export->ExportTo === 'local' ? TEMP_PATH . $this->export->Text : $_SERVER['DOCUMENT_ROOT'] . $this->export->ServerPath . "/" . $this->export->Text);
				break;
			default:
		}

		$_SESSION['weS']['export_session'] = $this->export;
	}

	function processVariables(){//FIXME use table datatypes or make like in we_export_wizard::getExportVars()
		if(isset($_SESSION['weS']['export_session'])){
			$this->export = $_SESSION['weS']['export_session'];
		}

		if(isset($_SESSION['weS']['exportVars_session'])){
			unset($_SESSION['weS']['exportVars_session']);
		}

		if(is_array($this->export->persistent_slots)){
			foreach($this->export->persistent_slots as $varname){
				if(($v = we_base_request::_(we_base_request::STRING, $varname, false)) !== false){//FIXME: this is quiet for now....
					$this->export->{$varname} = $v;
				}
			}
		}

		$this->page = we_base_request::_(we_base_request::INT, 'page', $this->page);
	}

	public function getHomeScreen(){
		$hiddens["cmd"] = "home";
		$content = we_html_button::create_button('new_export', "javascript:we_cmd('new_export');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_EXPORT")) . '<br/>' .
			we_html_button::create_button('new_export_group', "javascript:we_cmd('new_export_group');", '', 0, 0, "", "", !we_base_permission::hasPerm("NEW_EXPORT"));
		return parent::getActualHomeScreen("export", $content, we_html_element::htmlForm(['name' => 'we_form'], $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden("home", 0)
		));
	}

}
