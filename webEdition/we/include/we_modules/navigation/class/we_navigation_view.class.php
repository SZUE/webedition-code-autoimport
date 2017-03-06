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
				we_html_element::htmlHiddens(['vernr' => (isset($cmds['vernr']) ? $cmds['vernr'] : 0),
		]);
	}

	function getJSTop(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view.js');
	}

	function getJSProperty(array $jsVars = []){
		$objFields = [];
		if(defined('OBJECT_TABLE') && $this->Model->DynamicSelection === we_navigation_navigation::DYN_CLASS){
			$class = new we_object();
			$class->initByID($this->Model->ClassID, OBJECT_TABLE);
			$fields = $class->getAllVariantFields();

			foreach(array_keys($fields) as $key){
				$objFields[] = '"' . substr($key, strpos($key, "_") + 1) . '": "' . $key . '"';
			}
		}

		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view_prop.js', '', ['id' => 'loadVarViewProp', 'data-prop' => setDynamicVar(array_merge(
									$jsVars, [
						'weNavTitleField' => $objFields,
						'IsFolder' => intval($this->Model->IsFolder),
						'selfNaviPath' => $this->Model->Path,
						'selfNaviId' => $this->Model->ID,
		]))]);
	}

	function getEditNaviPosition(){
		$this->db->query('SELECT Ordn,Text FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . $this->Model->ParentID . ' ORDER BY Ordn');
		$values = $this->db->getAllFirst(false);
		$values[-1] = g_l('navigation', '[end]');
		return $values;
	}

	function processCommands(we_base_jsCmd $jscmd){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'module_navigation_new':
			case 'module_navigation_new_group':
				if(!we_base_permission::hasPerm('EDIT_NAVIGATION')){
					$jscmd->addMsg(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}
				$this->Model = new we_navigation_navigation();
				$this->Model->IsFolder = we_base_request::_(we_base_request::STRING, 'cmd') === 'module_navigation_new_group' ? 1 : 0;
				$this->Model->ParentID = we_base_request::_(we_base_request::INT, 'ParentID', 0);
				$jscmd->addCmd('editLoad', $this->Model->Text);
				break;
			case 'module_navigation_edit':
				if(!we_base_permission::hasPerm('EDIT_NAVIGATION')){
					$jscmd->addMsg(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				$this->Model = new we_navigation_navigation(we_base_request::_(we_base_request::INT, 'cmdid'));

				if(!$this->Model->isAllowedForUser()){
					$jscmd->addMsg(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = true;
					break;
				}
				$jscmd->addCmd('editLoad', $this->Model->Text, $this->Model->ID);
				break;
			case 'module_navigation_save':
				if(!we_base_permission::hasPerm('EDIT_NAVIGATION') && !we_base_permission::hasPerm('EDIT_NAVIGATION')){
					$jscmd->addMsg(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if(we_navigation_navigation::filenameNotValid($this->Model->Text)){
					$jscmd->addMsg(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if(!trim($this->Model->Text)){
					$jscmd->addMsg(g_l('navigation', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				$oldpath = $this->Model->Path;
				// set the path and check it
				$this->Model->setPath();
				if($this->Model->pathExists($this->Model->Path)){
					$jscmd->addMsg(g_l('navigation', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
					break;
				}

				if($this->Model->isSelf() || !$this->Model->isAllowedForUser()){
					$jscmd->addMsg(g_l('navigation', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR);
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
							$jscmd->addMsg(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						}
					} else {
						$jscmd->addMsg(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR);
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

				if($newone){
					$jscmd->addCmd('makeTreeEntry', [
						'id' => $this->Model->ID,
						'parentid' => $this->Model->ParentID,
						'text' => $this->Model->Text,
						'open' => false,
						'contenttype' => ($this->Model->IsFolder ? we_base_ContentTypes::FOLDER : 'we/navigation'),
						'table' => NAVIGATION_TABLE,
						'published' => 1,
						'order' => $this->Model->Ordn
					]);
				} else {
					$jscmd->addCmd('updateTreeEntry', [
						'id' => $this->Model->ID,
						'parentid' => $this->Model->ParentID,
						'text' => $this->Model->Text,
						'order' => $this->Model->Ordn,
						'tooltip' => $this->Model->ID
					]);
				}

				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$old_items = [];
					if($this->Model->hasDynChilds()){
						$old_items = $this->Model->depopulateGroup();
						foreach($old_items as $id){
							$jscmd->addCmd('deleteTreeEntry', $id['ID']);
						}
					}
					$items = $this->Model->populateGroup($old_items);
					foreach($items as $k => $item){
						$jscmd->addCmd('makeTreeEntry', [
							'id' => $item['id'],
							'parentid' => $this->Model->ID,
							'text' => $item['text'],
							'open' => false,
							'contenttype' => 'we/navigation',
							'table' => NAVIGATION_TABLE,
							'published' => 1,
							'order' => $k
						]);
					}
				}
				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_NODYNAMIC){
					$old_items = [];
					if($this->Model->hasDynChilds()){
						$old_items = $this->Model->depopulateGroup();
						foreach($old_items as $id){
							$jscmd->addCmd('deleteTreeEntry', $id['ID']);
						}
					}
				}
				$delaycmd = we_base_request::_(we_base_request::STRING, 'delayCmd');

				$jscmd->addMsg(g_l('navigation', ($this->Model->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE);
				$jscmd->addCmd('saveReload', $this->Model->IsFolder == 1);
				if($delaycmd){
					$jscmd->addCmd('we_cmd', $delaycmd);
					unset($_REQUEST['delayCmd']);
				}

				break;
			case 'module_navigation_delete':

				if(!we_base_permission::hasPerm('DELETE_NAVIGATION')){
					$jscmd->addMsg(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
					return;
				}
				if($this->Model->delete()){
					$jscmd->addCmd('deleteTreeEntry', $this->Model->ID);
					$jscmd->addMsg(g_l('navigation', ($this->Model->IsFolder == 1 ? '[group_deleted]' : '[navigation_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE);
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = 1;
					$_REQUEST['pnt'] = 'edbody';
				} else {
					$jscmd->addMsg(g_l('navigation', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR);
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
					$jscmd->addCmd('we_cmd', ['moveAbs', $this->Model->Ordn, $this->Model->ParentID, $posText]);
				}
				break;
			case 'move_up' :
				if($this->Model->reorderUp()){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText .= '<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					$jscmd->addCmd('we_cmd', ['moveUp', $this->Model->Ordn, $this->Model->ParentID, $posText]);
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
					$jscmd->addCmd('we_cmd', ['moveDown', $this->Model->Ordn, $this->Model->ParentID, $posText]);
				}
				break;
			case 'populate':
				$items = $this->Model->populateGroup();
				$js = '';
				foreach($items as $k => $item){
					$jscmd->addCmd('deleteTreeEntry', $item['id']);
					$jscmd->addCmd('makeTreeEntry', [
						'id' => $item['id'],
						'parentid' => $this->Model->ID,
						'text' => $item['text'],
						'open' => false,
						'contenttype' => 'we/navigation',
						'table' => NAVIGATION_TABLE,
						'published' => 1,
						'order' => $k
					]);
				}
				$jscmd->addMsg(g_l('navigation', '[populate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE);
				break;
			case 'depopulate':
				$items = $this->Model->depopulateGroup();
				foreach($items as $id){
					$jscmd->addCmd('deleteTreeEntry', $id['ID']);
				}
				$jscmd->addMsg(g_l('navigation', '[depopulate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE);
				$this->Model->Selection = we_navigation_navigation::SELECTION_NODYNAMIC;
				$this->Model->saveField('Selection');
				break;
			case 'create_template':
				$jscmd->addCmd('we_cmd', ["new", TEMPLATES_TABLE, "", we_base_ContentTypes::TEMPLATE, "", base64_encode($this->Model->previewCode)]);
				break;
			case 'populateFolderWs':
				$values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);

				if($values){
					$jscmd->addCmd('we_cmd', ['doPopulateFolderWs', 'values', $values]);
				} elseif(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){
					$jscmd->addCmd('we_cmd', ['doPopulateFolderWs', 'workspace']);
				} else {
					$jscmd->addCmd('we_cmd', ['doPopulateFolderWs', "noWorkspace"]);
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
					$jscmd->addCmd('doPopulateFolderWs', 'values', $prefix, $values);
				} elseif(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){ // if the class has no workspaces
					$jscmd->addCmd('populateWorkspaces', 'workspace', $prefix);
				} else {
					$jscmd->addCmd('populateWorkspaces', "noWorkspace", $prefix);
				}
				break;
			case 'populateText':
				if(!$this->Model->Text && $this->Model->Selection == we_navigation_navigation::SELECTION_STATIC && $this->Model->SelectionType === we_navigation_navigation::STYPE_CATLINK){
					$cat = new we_category();
					$cat->load($this->Model->LinkID);

					if(isset($cat->Title)){
						$jscmd->addCmd('setTitle', $cat->Title);
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

		if(($name = we_base_request::_(we_base_request::STRING, 'CategoriesControl'))){
			$i = 0;
			while(($cat = we_base_request::_(we_base_request::STRING, $name . '_variant0_' . $name . '_item' . $i))){
				$categories[] = $cat;
				$i++;
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

		$createNavigation = we_html_button::create_button('new_item', "javascript:we_cmd('module_navigation_new');", '', 0, 0, "", "", !we_base_permission::hasPerm('EDIT_NAVIGATION'));
		$createNavigationGroup = we_html_button::create_button('new_folder', "javascript:we_cmd('module_navigation_new_group');", '', 0, 0, "", "", !we_base_permission::hasPerm('EDIT_NAVIGATION'));
		$content = $createNavigation . '<br/>' . $createNavigationGroup;

		return parent::getActualHomeScreen('navigation', 'navigation.gif', $content, we_html_element::htmlForm(['name' => 'we_form'], $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden('home', '0')));
	}

}
