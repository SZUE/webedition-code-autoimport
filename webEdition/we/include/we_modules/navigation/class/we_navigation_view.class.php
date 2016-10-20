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
class we_navigation_view extends we_modules_view{
	var $icon_pattern = '';
	var $page = 1;

	public function __construct($frameset){
		$frameset = WEBEDITION_DIR . 'we_showMod.php?mod=navigation';
		parent::__construct($frameset);
		$this->Model = new we_navigation_navigation();
	}

	function getCommonHiddens($cmds = []){
		return
			parent::getCommonHiddens($cmds) .
			we_html_element::htmlHiddens(array(
				'vernr' => (isset($cmds['vernr']) ? $cmds['vernr'] : 0),
		));
	}

	function getJSTop(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view.js');
	}

	function getJSProperty(){
		$objFields = [];
		if(defined('OBJECT_TABLE') && $this->Model->DynamicSelection === we_navigation_navigation::DYN_CLASS){
			$class = new we_object();
			$class->initByID($this->Model->ClassID, OBJECT_TABLE);
			$fields = $class->getAllVariantFields();

			foreach(array_keys($fields) as $key){
				$objFields[] = '"' . substr($key, strpos($key, "_") + 1) . '": "' . $key . '"';
			}
		}

		return we_html_element::jsElement('
var data={
	IsFolder:' . intval($this->Model->IsFolder) . ',
};

var weNavTitleField = [' . implode(',', $objFields) . '];'
			) . we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view_prop.js');
	}

	function getEditNaviPosition(){
		$this->db->query('SELECT Ordn,Text FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . $this->Model->ParentID . ' ORDER BY Ordn');
		$values = $this->db->getAllFirst(false);
		$values[-1] = g_l('navigation', '[end]');
		return $values;
	}

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'module_navigation_new':
			case 'module_navigation_new_group':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}
				$this->Model = new we_navigation_navigation();
				$this->Model->IsFolder = we_base_request::_(we_base_request::STRING, 'cmd') === 'module_navigation_new_group' ? 1 : 0;
				$this->Model->ParentID = we_base_request::_(we_base_request::INT, 'ParentID', 0);
				echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=edheader&text=' . urlencode($this->Model->Text) . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=edfooter";');
				break;
			case 'module_navigation_edit':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				$this->Model = new we_navigation_navigation(we_base_request::_(we_base_request::INT, 'cmdid'));

				if(!$this->Model->isAllowedForUser()){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = true;
					break;
				}
				echo we_html_element::jsElement('
top.content.editor.edheader.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=edheader&text=' . urlencode($this->Model->Text) . '";
top.content.editor.edfooter.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=navigation&pnt=edfooter";
if(top.content.treeData){
	top.content.treeData.unselectNode();
	top.content.treeData.selectNode(' . $this->Model->ID . ');
}');
				break;
			case 'module_navigation_save':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION') && !permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if(we_navigation_navigation::filenameNotValid($this->Model->Text)){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if(!trim($this->Model->Text)){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				$oldpath = $this->Model->Path;
				// set the path and check it
				$this->Model->setPath();
				if($this->Model->pathExists($this->Model->Path)){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if($this->Model->isSelf() || !$this->Model->isAllowedForUser()){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS && $this->Model->TitleField != ""){
					$classFields = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . " WHERE ID=" . intval($this->Model->ClassID), "DefaultValues", $this->db));
					if(is_array($classFields) && !empty($classFields)){
						$fieldsByNamePart = [];
						foreach(array_keys($classFields) as $key){
							if(($pos = strpos($key, "_")) && (substr($key, 0, $pos) != "object")){
								$fieldsByNamePart[substr($key, $pos + 1)] = $key;
							}
						}
						if(!key_exists($this->Model->TitleField, $fieldsByNamePart) && !key_exists($this->Model->TitleField, $classFields)){
							echo we_message_reporting::jsMessagePush(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						}
					} else {
						echo we_message_reporting::jsMessagePush(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					}
				}

				$newone = $this->Model->ID == 0;

				$dynamic = '';
				if($this->Model->ID && $this->Model->IsFolder){
					$dynamic = f('SELECT Selection FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'Selection', $this->Model->db);
				}

				$this->Model->save();

				if($this->Model->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->Model->Path){
					$db_tmp = new DB_WE();
					$this->db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE \'' . $this->db->escape($oldpath) . '%\' AND ID!=' . intval($this->Model->ID));
					while($this->db->next_record()){
						$db_tmp->query('UPDATE ' . NAVIGATION_TABLE . ' SET Path="' . $this->db->escape($this->Model->evalPath($this->db->f("ID"))) . '" WHERE ID=' . intval($this->db->f("ID")));
					}
				}

				$js = ($newone ?
					'top.content.treeData.makeNewEntry({id:\'' . $this->Model->ID . '\',parentid:\'' . $this->Model->ParentID . '\',text:\'' . addslashes($this->Model->Text) . '\',open:0,contenttype:\'' . ($this->Model->IsFolder ? 'folder' : 'we/navigation') . '\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $this->Model->Ordn . '});' :
					'top.content.treeData.updateEntry({id:' . $this->Model->ID . ',text:\'' . addslashes($this->Model->Text) . '\',parentid:' . $this->Model->ParentID . ',order:\'' . $this->Model->Depended . '\',tooltip:' . $this->Model->ID . '});');

				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$old_items = [];
					if($this->Model->hasDynChilds()){
						$old_items = $this->Model->depopulateGroup();
						foreach($old_items as $id){
							$js .= 'top.content.treeData.deleteEntry(' . $id['ID'] . ');';
						}
					}
					$items = $this->Model->populateGroup($old_items);
					foreach($items as $k => $item){
						$js .= 'top.content.treeData.makeNewEntry({id:\'' . $item['id'] . '\',parentid:\'' . $this->Model->ID . '\',text:\'' . addslashes($item['text']) . '\',open:0,contenttype:\'we/navigation\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $k . '});';
					}
				}
				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_NODYNAMIC){
					$old_items = [];
					if($this->Model->hasDynChilds()){
						$old_items = $this->Model->depopulateGroup();
						foreach($old_items as $id){
							$js .= 'top.content.treeData.deleteEntry(' . $id['ID'] . ');';
						}
					}
				}
				$delaycmd = we_base_request::_(we_base_request::STRING, 'delayCmd');

				echo we_html_element::jsElement($js . 'top.content.editor.edheader.location.reload();' .
					we_message_reporting::getShowMessageCall(g_l('navigation', ($this->Model->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '
top.content.hot=0;
if(top.content.makeNewDoc) {
	setTimeout(top.content.we_cmd,100,"module_navigation_' . (($this->Model->IsFolder == 1) ? 'new_group' : 'new') . '");
}' .
					($delaycmd ?
						'top.content.we_cmd("' . implode('","', $delaycmd) . '");' :
						''
					)
				);

				if($delaycmd){
					unset($_REQUEST['delayCmd']);
				}

				break;
			case 'module_navigation_delete':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();');

				if(!permissionhandler::hasPerm('DELETE_NAVIGATION')){
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
				if($this->Model->delete()){
					echo we_html_element::jsElement('
top.content.treeData.deleteEntry(' . $this->Model->ID . ');
setTimeout(top.we_showMessage,500,"' . g_l('navigation', ($this->Model->IsFolder == 1 ? '[group_deleted]' : '[navigation_deleted]')) . '", WE().consts.message.WE_MESSAGE_NOTICE, window);');
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = 1;
					$_REQUEST['pnt'] = 'edbody';
				} else {
					echo we_message_reporting::jsMessagePush(g_l('navigation', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR);
				}
				break;
			case 'switchPage':

				break;
			case 'move_abs':
				if($this->Model->reorderAbs(we_base_request::_(we_base_request::INT, 'pos'))){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText .= '<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}

					echo we_html_element::jsElement('top.content.moveAbs(' . $this->Model->Ordn . ',' . $this->Model->ParentID . ',\'' . addcslashes($posText, '\'') . '\');');
				}
				break;
			case 'move_up' :
				if($this->Model->reorderUp()){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText .= '<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					echo we_html_element::jsElement('top.content.moveUp(' . $this->Model->Ordn . ',' . $this->Model->ParentID . ',\'' . addcslashes($posText, '\'') . '\');');
				}
				break;
			case 'move_down' :
				if($this->Model->reorderDown()){
					$parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'ParentID', $this->db);
					$num = f('SELECT MAX(Ordn) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($parentid), '', $this->db);
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText .= '<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					echo we_html_element::jsElement('top.content.moveDown(' . $this->Model->Ordn . ',' . $this->Model->ParentID . ',\'' . addcslashes($posText, '\'') . '\',' . $num . ');');
				}
				break;
			case 'populate':
				$items = $this->Model->populateGroup();
				$js = '';
				foreach($items as $k => $item){
					$js .= 'top.content.treeData.deleteEntry(' . $item['id'] . ');
						top.content.treeData.makeNewEntry({id:\'' . $item['id'] . '\',parentid:\'' . $this->Model->ID . '\',text:\'' . addslashes($item['text']) . '\',open:0,contenttype:\'we/navigation\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $k . '});';
				}
				echo we_html_element::jsElement($js . we_message_reporting::getShowMessageCall(g_l('navigation', '[populate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;
			case 'depopulate':
				$items = $this->Model->depopulateGroup();
				$js = '';
				foreach($items as $id){
					$js .= 'top.content.treeData.deleteEntry(' . $id['ID'] . ');';
				}
				echo we_html_element::jsElement($js . we_message_reporting::getShowMessageCall(g_l('navigation', '[depopulate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE));
				$this->Model->Selection = we_navigation_navigation::SELECTION_NODYNAMIC;
				$this->Model->saveField('Selection');
				break;
			case 'create_template':
				echo we_html_element::jsElement(
					'top.content.opener.top.we_cmd("new","' . TEMPLATES_TABLE . '","","' . we_base_ContentTypes::TEMPLATE . '","","' . base64_encode($this->Model->previewCode) . '");
					');
				break;
			case 'populateFolderWs':
				$prefix = '';
				$values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				$js = '';

				if($values){
					foreach($values as $id => $path){
						$js .= 'top.content.editor.edbody.document.we_form.WorkspaceID.options[top.content.editor.edbody.document.we_form.WorkspaceID.options.length] = new Option("' . $path . '",' . $id . ');';
					}
					echo we_html_element::jsElement('top.content.populateFolderWs("values");' . $js);
				} elseif(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){
					echo we_html_element::jsElement('top.content.populateFolderWs("workspace");');
				} else {
					echo we_html_element::jsElement('top.content.populateFolderWs("noWorkspace","' . $prefix . '");');
				}

				break;
			case 'populateWorkspaces':
				$objFields = '';
				if($this->Model->DynamicSelection == we_navigation_navigation::DYN_CLASS){
					if(defined('OBJECT_TABLE')){

						$class = new we_object();
						$class->initByID($this->Model->ClassID, OBJECT_TABLE);
						$fields = $class->getAllVariantFields();
						foreach($fields as $key => $val){
							$objFields .= 'top.content.editor.edbody.weNavTitleField["' . substr($key, strpos($key, "_") + 1) . '"] = "' . $key . '";';
						}
					}
				}

				if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$values = we_navigation_dynList::getWorkspacesForClass($this->Model->ClassID);
					$prefix = 'Class';
				} else {
					$values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
					$prefix = '';
				}

				if($values){ // if the class has workspaces
					$js = '';
					foreach($values as $id => $path){
						$js .= 'top.content.editor.edbody.document.we_form.WorkspaceID' . $prefix . '.options[top.content.editor.edbody.document.we_form.WorkspaceID' . $prefix . '.options.length] = new Option("' . $path . '",' . $id . ');';
					}
					echo we_html_element::jsElement($objFields . 'top.content.populateWorkspaces("values","' . $prefix . '");' . $js);
				} else { // if the class has no workspaces
					echo we_html_element::jsElement($objFields . 'top.content.populateWorkspaces("' .
						(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID) ?
							'workspace' :
							'noWorkspace') .
						'","' . $prefix . '");');
				}
				break;
			case 'populateText':
				if(!$this->Model->Text && $this->Model->Selection == we_navigation_navigation::SELECTION_STATIC && $this->Model->SelectionType === we_navigation_navigation::STYPE_CATLINK){
					$cat = new we_category();
					$cat->load($this->Model->LinkID);

					if(isset($cat->Title)){
						echo we_html_element::jsElement('top.content.editor.edbody.document.we_form.Text.value = "' . addslashes($cat->Title) . '";');
					}
				}
				break;

			default:
		}

		$_SESSION['weS']['navigation_session'] = $this->Model;
	}

	function processVariables(){
		if(isset($_SESSION['weS']['navigation_session']) && $_SESSION['weS']['navigation_session'] instanceof we_navigation_navigation){
			$this->Model = $_SESSION['weS']['navigation_session'];
		}

		if(defined('CUSTOMER_TABLE')){
			if(($mode = we_base_request::_(we_base_request::INT, 'wecf_mode')) !== false){
				we_navigation_customerFilter::translateModeToNavModel($mode, $this->Model);
			}
			$this->Model->Customers = we_customer_abstractFilter::getSpecificCustomersFromRequest();
			$this->Model->BlackList = we_customer_abstractFilter::getBlackListFromRequest();
			$this->Model->WhiteList = we_customer_abstractFilter::getWhiteListFromRequest();
			$this->Model->CustomerFilter = we_customer_abstractFilter::getFilterFromRequest();
			$this->Model->UseDocumentFilter = we_navigation_customerFilter::getUseDocumentFilterFromRequest();
		}

		$categories = [];

		if(($name = we_base_request::_(we_base_request::STRING, 'CategoriesControl')) && ($cnt = we_base_request::_(we_base_request::INT, 'CategoriesCount')) !== false){
			for($i = 0; $i < $cnt; $i++){
				if(($cat = we_base_request::_(we_base_request::STRING, $name . '_variant0_' . $name . '_item' . $i)) !== false){
					$categories[] = $cat;
				}
			}
			$this->Model->Categories = $categories;
		}

		if(($field = we_base_request::_(we_base_request::STRING, 'SortField')) !== false){
			if($field){
				$this->Model->Sort = [['field' => $field,
						'order' => we_base_request::_(we_base_request::STRING, 'SortOrder')
						]
				];
			} else {
				$this->Model->Sort = '';
			}
		}

		if(is_array($this->Model->persistent_slots)){
			foreach($this->Model->persistent_slots as $key => $type){
				if(($val = we_base_request::_($type, $key, '-1')) !== '-1'){
					$this->Model->$key = $val;
				}
			}
		}

		if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
			if(($wid = we_base_request::_(we_base_request::INT, 'WorkspaceIDClass')) !== false){
				$this->Model->WorkspaceID = $wid;
			}

			if(($par = we_base_request::_(we_base_request::URL, 'dynamic_Parameter')) !== false){
				$this->Model->Parameter = $par;
			}

			if($this->Model->DynamicSelection == we_navigation_navigation::DYN_CATEGORY && ($url = we_base_request::_(we_base_request::URL, 'dynamic_Url')) !== false){
				$this->Model->Url = $url;
				$this->Model->UrlID = we_base_request::_(we_base_request::INT, 'dynamic_UrlID', 0);
				$this->Model->LinkSelection = we_base_request::_(we_base_request::STRING, 'dynamic_LinkSelection');
				$this->Model->CatParameter = we_base_request::_(we_base_request::STRING, 'dynamic_CatParameter');
			}
		}


		if($this->Model->IsFolder == 0){
			$this->Model->Charset = $this->Model->findCharset($this->Model->ParentID);
		}

		if(($code = we_base_request::_(we_base_request::RAW_CHECKED, 'previewCode'))){
			$this->Model->previewCode = $code;
		}

		if(($page = we_base_request::_(we_base_request::INT, "page")) !== false){
			$this->page = ($this->Model->IsFolder && $page != 1 && $page != 3 ? 1 : $page);
		}
	}

	function getItems($id){
		$db = new DB_WE();

		$db->query('SELECT ID,Text FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($id) . ' AND Depended=1 ORDER BY Ordn;');
		return $db->getAllFirst(false);
	}

	public function getHomeScreen(){
		$hiddens['cmd'] = 'home';

		$createNavigation = we_html_button::create_button('new_item', "javascript:we_cmd('module_navigation_new');", '', 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
		$createNavigationGroup = we_html_button::create_button('new_folder', "javascript:we_cmd('module_navigation_new_group');", '', 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
		$content = $createNavigation . '<br/>' . $createNavigationGroup;

		return parent::getActualHomeScreen('navigation', 'navigation.gif', $content, we_html_element::htmlForm(array('name' => 'we_form'), $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden('home', '0')));
	}

}
