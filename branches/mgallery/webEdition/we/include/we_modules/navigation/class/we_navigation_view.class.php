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
	var $navigation;
	var $icon_pattern = '';
	var $page = 1;
	var $Model;

	public function __construct(){
		$frameset = WE_MODULES_DIR . 'navigation/edit_navigation_frameset.php';
		$topframe = 'top.content';
		parent::__construct($frameset, $topframe);
		$this->Model = new we_navigation_navigation();
	}

	function getCommonHiddens($cmds = array()){
		return
			parent::getCommonHiddens($cmds) .
			we_html_element::htmlHiddens(array(
				'vernr' => (isset($cmds['vernr']) ? $cmds['vernr'] : 0),
				'delayCmd' => (isset($cmds['delayCmd']) ? $cmds['delayCmd'] : ''),
				'delayParam' => (isset($cmds['delayParam']) ? $cmds['delayParam'] : '')
		));
	}

	function getJSTop(){
		return
			parent::getJSTop() .
			we_html_element::jsElement('
WE().consts.g_l.navigation.view={
	documents:"' . g_l('navigation', '[documents]') . '",
	objects:"' . g_l('navigation', '[objects]') . '",
	categories:"' . g_l('navigation', '[categories]') . '",
	docLink:"' . g_l('navigation', '[docLink]') . '",
	urlLink:"' . g_l('navigation', '[urlLink]') . '",
	objLink:"' . g_l('navigation', '[objLink]') . '",
	catLink:"' . g_l('navigation', '[catLink]') . '",
	populate_question:"' . g_l('navigation', '[populate_question]') . '",
	depopulate_question:"' . g_l('navigation', '[depopulate_question]') . '",
	save_populate_question:"' . g_l('navigation', '[save_populate_question]') . '",
	delete_alert:"' . g_l('navigation', '[delete_alert]') . '",
	reset_customerfilter_question:"' . g_l('navigation', '[reset_customerfilter_question]') . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('navigation', '[nothing_to_save]')) . '",
	nothing_selected:"' . we_message_reporting::prepareMsgForJS(g_l('navigation', '[nothing_selected]')) . '",
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('navigation', '[nothing_to_delete]')) . '",
	no_perms:"' . we_message_reporting::prepareMsgForJS(g_l('navigation', '[no_perms]')) . '",
};

WE().consts.navigation={
	STYPE_CLASS:"' . we_navigation_navigation::STYPE_CLASS . '",
	STYPE_DOCTYPE:"' . we_navigation_navigation::STYPE_DOCTYPE . '",
	STYPE_CATEGORY:"' . we_navigation_navigation::STYPE_CATEGORY . '",
	STYPE_DOCLINK:"' . we_navigation_navigation::STYPE_DOCLINK . '",
	STYPE_URLLINK:"' . we_navigation_navigation::STYPE_URLLINK . '",
	STYPE_OBJLINK:"' . we_navigation_navigation::STYPE_OBJLINK . '",
	STYPE_CATLINK:"' . we_navigation_navigation::STYPE_CATLINK . '",
	SELECTION_DYNAMIC:"' . we_navigation_navigation::SELECTION_DYNAMIC . '",
	SELECTION_STATIC:"' . we_navigation_navigation::SELECTION_STATIC . '",
	LSELECTION_INTERN:"' . we_navigation_navigation::LSELECTION_INTERN . '",
	LSELECTION_EXTERN:"' . we_navigation_navigation::LSELECTION_EXTERN . '",
};
var data={
	frameset:"' . $this->frameset . '",
};') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view.js');
	}

	function getJSProperty(){
		$out = parent::getJSProperty();
		$_objFields = array();
		if($this->Model->SelectionType == we_navigation_navigation::STYPE_CLASS){
			if(defined('OBJECT_TABLE')){

				$_class = new we_object();
				$_class->initByID($this->Model->ClassID, OBJECT_TABLE);
				$_fields = $_class->getAllVariantFields();

				foreach(array_keys($_fields) as $_key){
					$_objFields[] = '"' . substr($_key, strpos($_key, "_") + 1) . '": "' . $_key . '"';
				}
			}
		}

		return $out . we_html_element::jsElement('
var data={
	frameset:"' . $this->frameset . '",
	IsFolder:' . intval($this->Model->IsFolder) . ',
};

var weNavTitleField = [' . implode(',', $_objFields) . '];'
			) . we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view_prop.js');
	}

	function getJSSubmitFunction(){
		return '';
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
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				$this->Model = new we_navigation_navigation();
				$this->Model->IsFolder = we_base_request::_(we_base_request::STRING, 'cmd') === 'module_navigation_new_group' ? 1 : 0;
				$this->Model->ParentID = we_base_request::_(we_base_request::INT, 'ParentID', 0);
				echo we_html_element::jsElement('
top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->Model->Text) . '";
top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
				break;
			case 'module_navigation_edit':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$this->Model = new we_navigation_navigation(we_base_request::_(we_base_request::INT, 'cmdid'));

				if(!$this->Model->isAllowedForUser()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = true;
					break;
				}
				echo we_html_element::jsElement('
top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->Model->Text) . '";
top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
if(top.content.treeData){
	top.content.treeData.unselectnode();
	top.content.treeData.selectnode(' . $this->Model->ID . ');
}');
				break;
			case 'module_navigation_save':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION') && !permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Model->filenameNotValid($this->Model->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if(!trim($this->Model->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$oldpath = $this->Model->Path;
				// set the path and check it
				$this->Model->setPath();
				if($this->Model->pathExists($this->Model->Path)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Model->isSelf() || !$this->Model->isAllowedForUser()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Model->SelectionType == we_navigation_navigation::STYPE_CLASS && $this->Model->TitleField != ""){
					$_classFields = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . " WHERE ID=" . intval($this->Model->ClassID), "DefaultValues", $this->db));
					if(is_array($_classFields) && count($_classFields) > 0){
						$_fieldsByNamePart = array();
						foreach(array_keys($_classFields) as $_key){
							if(($_pos = strpos($_key, "_")) && (substr($_key, 0, $_pos) != "object")){
								$_fieldsByNamePart[substr($_key, $_pos + 1)] = $_key;
							}
						}
						if(!key_exists($this->Model->TitleField, $_fieldsByNamePart) && !key_exists($this->Model->TitleField, $_classFields)){
							echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
					} else {
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
				}

				$newone = $this->Model->ID == 0;

				$_dynamic = '';
				if($this->Model->ID && $this->Model->IsFolder){
					$_dynamic = f('SELECT Selection FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'Selection', $this->Model->db);
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
						'top.content.makeNewEntry({id:\'' . $this->Model->ID . '\',parentid:\'' . $this->Model->ParentID . '\',text:\'' . addslashes($this->Model->Text) . '\',open:0,contenttype:\'' . ($this->Model->IsFolder ? 'folder' : 'we/navigation') . '\',table:\'' . NAVIGATION_TABLE . '\',published:0,order:' . $this->Model->Ordn . '});' :
						'top.content.updateEntry({id:' . $this->Model->ID . ',text:\'' . addslashes($this->Model->Text) . '\',parentid:' . $this->Model->ParentID . ',order:\'' . $this->Model->Depended . '\',tooltip:' . $this->Model->ID . '});');

				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$_old_items = array();
					if($this->Model->hasDynChilds()){
						$_old_items = $this->Model->depopulateGroup();
						foreach($_old_items as $_id){
							$js .= 'top.content.deleteEntry(' . $_id['ID'] . ');';
						}
					}
					$_items = $this->Model->populateGroup($_old_items);
					foreach($_items as $_k => $_item){
						$js .= 'top.content.makeNewEntry({id:\'' . $_item['id'] . '\',parentid:\'' . $this->Model->ID . '\',text:\'' . addslashes($_item['text']) . '\',open:0,contenttype:\'we/navigation\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $_k . '});';
					}
				}
				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_NODYNAMIC){
					$_old_items = array();
					if($this->Model->hasDynChilds()){
						$_old_items = $this->Model->depopulateGroup();
						foreach($_old_items as $_id){
							$js .= 'top.content.deleteEntry(' . $_id['ID'] . ');';
						}
					}
				}
				$delaycmd = we_base_request::_(we_base_request::JS, 'delayCmd');

				echo we_html_element::jsElement($js . 'top.content.editor.edheader.location.reload();' .
					we_message_reporting::getShowMessageCall(g_l('navigation', ($this->Model->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '
top.content.hot=0;
if(top.content.makeNewDoc) {
	setTimeout("top.content.we_cmd(\"module_navigation_' . (($this->Model->IsFolder == 1) ? 'new_group' : 'new') . '\",100)");
}' .
					($delaycmd ?
						'top.content.we_cmd("' . $delaycmd . '"' . (($dp = we_base_request::_(we_base_request::INT, 'delayParam')) ? ',"' . $dp . '"' : '' ) . ');' :
						''
					)
				);

				if($delaycmd){
					$_REQUEST['delayCmd'] = '';
					$_REQUEST['delayParam'] = '';
				}

				break;
			case 'module_navigation_delete':

				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();');

				if(!permissionhandler::hasPerm('DELETE_NAVIGATION')){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					return;
				}
				if($this->Model->delete()){
					echo we_html_element::jsElement('
top.content.deleteEntry(' . $this->Model->ID . ');
setTimeout(function(){' . we_message_reporting::getShowMessageCall(g_l('navigation', ($this->Model->IsFolder == 1 ? '[group_deleted]' : '[navigation_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '},500);
');
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = 1;
					$_REQUEST['pnt'] = 'edbody';
				} else {
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('navigation', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
				}
				break;
			case 'switchPage':

				break;
			case 'move_abs':
				if($this->Model->reorderAbs(we_base_request::_(we_base_request::INT, 'pos'))){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText.='<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}

					echo we_html_element::jsElement('
top.content.editor.edbody.document.we_form.Ordn.value=' . $this->Model->Ordn . ';
top.content.reloadGroup(' . $this->Model->ParentID . ');
WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");

if(top.content.editor.edbody.document.we_form.Ordn.value==0){
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "disabled");
} else {
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");
}
top.content.editor.edbody.document.we_form.Position.innerHTML=\'' . $posText . '\';'
					);
				}
				break;
			case 'move_up' :
				if($this->Model->reorderUp()){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText.='<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					echo we_html_element::jsElement('
top.content.editor.edbody.document.we_form.Ordn.value=' . $this->Model->Ordn . ';
top.content.reloadGroup(' . $this->Model->ParentID . ');
WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");

if(top.content.editor.edbody.document.we_form.Ordn.value==1){
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "disabled");
} else {
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");
}
top.content.editor.edbody.document.we_form.Position.innerHTML=\'' . $posText . '\';'
					);
				}
				break;
			case 'move_down' :
				if($this->Model->reorderDown()){
					$_parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'ParentID', $this->db);
					$_num = f('SELECT MAX(Ordn) as OrdCount FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($_parentid), 'OrdCount', $this->db);
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText.='<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					echo we_html_element::jsElement('
top.content.editor.edbody.document.we_form.Ordn.value=' . $this->Model->Ordn . ';
top.content.reloadGroup(' . $this->Model->ParentID . ');
WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");
if(top.content.editor.edbody.document.we_form.Ordn.value==' . ($_num + 1) . '){
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "disabled");
} else {
	WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
}
top.content.editor.edbody.document.we_form.Position.innerHTML=\'' . $posText . '\';'
					);
				}
				break;
			case 'populate':
				$_items = $this->Model->populateGroup();
				$_js = '';
				foreach($_items as $_k => $_item){
					$_js .= 'top.content.deleteEntry(' . $_item['id'] . ');
						top.content.makeNewEntry({id:\'' . $_item['id'] . '\',parentid:\'' . $this->Model->ID . '\',text:\'' . addslashes($_item['text']) . '\',open:0,contenttype:\'we/navigation\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $_k . '});';
				}
				echo we_html_element::jsElement(
					$_js .
					we_message_reporting::getShowMessageCall(g_l('navigation', '[populate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;
			case 'depopulate':
				$_items = $this->Model->depopulateGroup();
				$_js = '';
				foreach($_items as $_id){
					$_js .= 'top.content.deleteEntry(' . $_id . ');
						';
				}
				$_js .= we_message_reporting::getShowMessageCall(g_l('navigation', '[depopulate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE);
				echo we_html_element::jsElement($_js);
				$this->Model->Selection = we_navigation_navigation::SELECTION_NODYNAMIC;
				$this->Model->saveField('Selection');
				break;
			case 'dyn_preview':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('
						url = "' . WE_INCLUDES_DIR . 'we_modules/navigation/edit_navigation_frameset.php?pnt=dyn_preview";
						new (WE().util.jsWindow)(top.window, url,"we_navigation_dyn_preview",-1,-1,480,350,true,true,true);'
				);
				break;
			case 'create_template':
				echo we_html_element::jsElement(
					'top.content.opener.top.we_cmd("new","' . TEMPLATES_TABLE . '","","' . we_base_ContentTypes::TEMPLATE . '","","' . base64_encode($this->Model->previewCode) . '");
					');
				break;
			case 'populateFolderWs':
				$_prefix = '';
				$_values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				$_js = '';

				if($_values){

					foreach($_values as $_id => $_path){
						$_js .= 'top.content.editor.edbody.document.we_form.FolderWsID.options[top.content.editor.edbody.document.we_form.FolderWsID.options.length] = new Option("' . $_path . '",' . $_id . ');';
					}
					echo we_html_element::jsElement(
						'top.content.editor.edbody.setVisible("objLinkFolderWorkspace",true);
							top.content.editor.edbody.document.we_form.FolderWsID.options.length = 0;
							' . $_js . '
						');
				} elseif(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){
					echo we_html_element::jsElement(
						'top.content.editor.edbody.document.we_form.FolderWsID.options.length = 0;
								top.content.editor.edbody.document.we_form.FolderWsID.options[top.content.editor.edbody.document.we_form.FolderWsID.options.length] = new Option("/",0);
								top.content.editor.edbody.document.we_form.FolderWsID.selectedIndex = 0;
								top.content.editor.edbody.setVisible("objLinkFolderWorkspace",true);'
					);
				} else {
					echo we_html_element::jsElement(
						'top.content.editor.edbody.setVisible("objLinkFolderWorkspace' . $_prefix . '",false);
								top.content.editor.edbody.document.we_form.FolderWsID.options.length = 0;
								top.content.editor.edbody.document.we_form.FolderWsID.options[top.content.editor.edbody.document.we_form.FolderWsID.options.length] = new Option("-1",-1);
								top.content.editor.edbody.document.we_form.LinkID.value = "";
								top.content.editor.edbody.document.we_form.LinkPath.value = "";
								' . we_message_reporting::getShowMessageCall(g_l('navigation', '[no_workspace]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							');
				}

				break;
			case 'populateWorkspaces':

				$_objFields = "\n";
				if($this->Model->SelectionType == we_navigation_navigation::STYPE_CLASS){
					$__fields = array();
					if(defined('OBJECT_TABLE')){

						$_class = new we_object();
						$_class->initByID($this->Model->ClassID, OBJECT_TABLE);
						$_fields = $_class->getAllVariantFields();
						$_objFields = "\n";
						foreach($_fields as $_key => $val){
							$_objFields .= 'top.content.editor.edbody.weNavTitleField["' . substr($_key, strpos($_key, "_") + 1) . '"] = "' . $_key . '";' . "\n";
						}
					}
				}

				$_prefix = '';

				if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$_values = we_navigation_dynList::getWorkspacesForClass($this->Model->ClassID);
					$_prefix = 'Class';
				} else {
					$_values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				}

				$_js = '';

				if($_values){ // if the class has workspaces
					foreach($_values as $_id => $_path){
						$_js .= 'top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options[top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options.length] = new Option("' . $_path . '",' . $_id . ');';
					}
					echo we_html_element::jsElement(
						$_objFields .
						'top.content.editor.edbody.setVisible("objLinkWorkspace' . $_prefix . '",true);
							top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options.length = 0;
							' . $_js . '
						');
				} else { // if the class has no workspaces
					if(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){
						echo we_html_element::jsElement(
							$_objFields .
							'top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options.length = 0;
								top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options[top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options.length] = new Option("/",0);
								top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.selectedIndex = 0;
								//top.content.editor.edbody.setVisible("objLinkWorkspace' . $_prefix . '",false);'
						);
					} else {
						echo we_html_element::jsElement(
							$_objFields .
							'top.content.editor.edbody.setVisible("objLinkWorkspace' . $_prefix . '",false);
								top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options.length = 0;
								top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options[top.content.editor.edbody.document.we_form.WorkspaceID' . $_prefix . '.options.length] = new Option("-1",-1);
								top.content.editor.edbody.document.we_form.LinkID.value = "";
								top.content.editor.edbody.document.we_form.LinkPath.value = "";
								' . we_message_reporting::getShowMessageCall(g_l('navigation', '[no_workspace]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							');
					}
				}
				break;
			case 'populateText':
				if(!$this->Model->Text && $this->Model->Selection == we_navigation_navigation::SELECTION_STATIC && $this->Model->SelectionType == we_navigation_navigation::STYPE_CATLINK){
					$_cat = new we_category();
					$_cat->load($this->Model->LinkID);

					if(isset($_cat->Title)){
						echo we_html_element::jsElement('top.content.editor.edbody.document.we_form.Text.value = "' . addslashes($_cat->Title) . '";');
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

		$_categories = array();

		if(($name = we_base_request::_(we_base_request::STRING, 'CategoriesControl')) && ($cnt = we_base_request::_(we_base_request::INT, 'CategoriesCount')) !== false){
			for($i = 0; $i < $cnt; $i++){
				if(($cat = we_base_request::_(we_base_request::STRING, $name . '_variant0_' . $name . '_item' . $i)) !== false){
					$_categories[] = $cat;
				}
			}
			$this->Model->Categories = $_categories;
		}

		if(($field = we_base_request::_(we_base_request::STRING, 'SortField')) !== false){
			if($field){
				$this->Model->Sort = array(
					array(
						'field' => $field,
						'order' => we_base_request::_(we_base_request::STRING, 'SortOrder')
					)
				);
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

			if($this->Model->SelectionType == we_navigation_navigation::STYPE_CATEGORY && ($url = we_base_request::_(we_base_request::URL, 'dynamic_Url')) !== false){
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
		$_db = new DB_WE();

		$_db->query('SELECT ID,Text FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($id) . ' AND Depended=1 ORDER BY Ordn;');
		return $_db->getAllFirst(false);
	}

	public function getHomeScreen(){
		$hiddens['cmd'] = 'home';
		$GLOBALS['we_head_insert'] = $this->getJSProperty();
		$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden('home', '0'));

		$createNavigation = we_html_button::create_button('new_item', "javascript:we_cmd('module_navigation_new');", true, 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
		$createNavigationGroup = we_html_button::create_button('new_folder', "javascript:we_cmd('module_navigation_new_group');", true, 0, 0, "", "", !permissionhandler::hasPerm('EDIT_NAVIGATION'));
		$content = $createNavigation . '<br/>' . $createNavigationGroup;

		return parent::getHomeScreen('navigation', 'navigation.gif', $content);
	}

}
