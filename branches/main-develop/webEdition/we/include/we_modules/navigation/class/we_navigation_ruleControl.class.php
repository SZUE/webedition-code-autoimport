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
class we_navigation_ruleControl{
	var $NavigationRule;

	public function __construct(){
		$this->NavigationRule = new we_navigation_rule();
	}

	function processCommands(we_base_jsCmd $jscmd){
		$js = '';
		$html = '';

		switch(we_base_request::_(we_base_request::STRING, 'cmd', '')){

			case "save_navigation_rule" :
				$isNew = $this->NavigationRule->isnew; // navigationID = 0
				$save = true;

				$this->NavigationRule->NavigationName = trim($this->NavigationRule->NavigationName);

				// 1st check if name is allowed
				//FIXME: is this correct on UTF-8??
				/* 					if(!preg_match(
				  '%^[äöüßa-z0-9_-]+$%i', $this->NavigationRule->NavigationName)){
				  $js = we_message_reporting::getShowMessageCall(
				  g_l('navigation', '[rules][invalid_name]'), we_message_reporting::WE_MESSAGE_ERROR);
				  $save = false;
				  } */

				// 2ns check if another element has same name
				$db = new DB_WE();

				if(f('SELECT 1 FROM ' . NAVIGATION_RULE_TABLE . ' WHERE NavigationName="' . $db->escape($this->NavigationRule->NavigationName) . '" AND ID!=' . intval($this->NavigationRule->ID) . ' LIMIT 1', '', $db)){
					$jscmd->addMsg(sprintf(g_l('navigation', '[rules][name_exists]'), $this->NavigationRule->NavigationName), we_message_reporting::WE_MESSAGE_ERROR);
					$save = false;
				}

				if($save && $this->NavigationRule->save()){

					$js = "doc = top.frames.content;
doc.weSelect." . ($isNew ? 'addOption' : 'updateOption') . "('navigationRules', " . $this->NavigationRule->ID . ", '" . $this->NavigationRule->NavigationName . "');
doc.weSelect.selectOption('navigationRules', " . $this->NavigationRule->ID . ");
doc.weInput.setValue('ID', " . $this->NavigationRule->ID . ");";
					$jscmd->addMsg(sprintf(g_l('navigation', '[rules][saved_successful]'), $this->NavigationRule->NavigationName), we_message_reporting::WE_MESSAGE_NOTICE);
				}
				break;

			case "delete_navigation_rule" :
				if($this->NavigationRule->delete()){

					$js = "var doc = top.frames.content;
doc.weSelect.removeOption('navigationRules', " . $this->NavigationRule->ID . ", '" . $this->NavigationRule->NavigationName . "');
doc.weInput.setValue('ID', 0);";
				}
				break;

			case "navigation_edit_rule" :
				$this->NavigationRule = new we_navigation_rule();
				$this->NavigationRule->initByID(we_base_request::_(we_base_request::INT, 'ID'));

				$FolderIDPath = ($this->NavigationRule->FolderID ? id_to_path($this->NavigationRule->FolderID, FILE_TABLE) : '');
				$ClassIDPath = (defined('OBJECT_TABLE') && $this->NavigationRule->ClassID ? id_to_path($this->NavigationRule->ClassID, OBJECT_TABLE) : '');
				$NavigationIDPath = htmlspecialchars_decode($this->NavigationRule->NavigationID ? id_to_path($this->NavigationRule->NavigationID, NAVIGATION_TABLE) : '', ENT_NOQUOTES);

				// workspaces:
				$workspaceList = ['{"text":"' . g_l('navigation', '[no_entry]') . '","value":"0"}'];
				$selectWorkspace = '';
				if(defined('OBJECT_TABLE') && $this->NavigationRule->ClassID){
					$workspaces = $this->getWorkspacesByClassID($this->NavigationRule->ClassID);

					foreach($workspaces as $key => $value){
					    if($key > 0){ // avoid dublicate keys: workspace '/' (0) already set as 'no_entry'
                            $workspaceList[] = '{"text":"' . $value . '","value":"' . $key . '"}';
                        }
					}
					$selectWorkspace = 'doc.weSelect.selectOption("WorkspaceID", "' . $this->NavigationRule->WorkspaceID . '" );';
				}

				// categories
				$catJs = '';
				if($this->NavigationRule->Categories){

					$catIds = makeArrayFromCSV($this->NavigationRule->Categories);

					foreach($catIds as $catId){
						if(($path = id_to_path($catId, CATEGORY_TABLE))){
							$catJs .= 'doc.categories_edit.addItem();doc.categories_edit.setItem(0,(doc.categories_edit.itemCount-1),"' . $path . '");
							';
						}
					}
				}

				$js = "
var doc = top.frames.content;
doc.clearNavigationForm();

doc.weInput.setValue('ID', " . $this->NavigationRule->ID . ");
doc.weInput.setValue('NavigationName', '" . $this->NavigationRule->NavigationName . "');

doc.weInput.setValue('NavigationID', '" . $this->NavigationRule->NavigationID . "');
doc.weInput.setValue('NavigationIDPath', '" . $NavigationIDPath . "');

doc.weInput.setValue('FolderID', '" . $this->NavigationRule->FolderID . "');
doc.weInput.setValue('FolderIDPath', '" . $FolderIDPath . "');


doc.weSelect.selectOption('SelectionType', '" . $this->NavigationRule->SelectionType . "');
doc.switchType('" . $this->NavigationRule->SelectionType . "');

doc.weInput.setValue('DoctypeID', '" . $this->NavigationRule->DoctypeID . "');

doc.weInput.setValue('ClassID', '" . $this->NavigationRule->ClassID . "');
doc.weInput.setValue('ClassIDPath', '" . $ClassIDPath . "');

doc.removeAllCats();
$catJs
doc.categories_edit.showVariant(0);
doc.weInput.setValue('CategoriesCount', doc.categories_edit.itemCount);


var optionList = [" . implode(',', $workspaceList) . "];
doc.weSelect.setOptions('WorkspaceID', optionList);
$selectWorkspace";
				break;

			case "get_workspaces" :

				if(defined('OBJECT_TABLE') && ($classid = we_base_request::_(we_base_request::INT, 'ClassID'))){
					$workspaces = $this->getWorkspacesByClassID($classid);
					$optionList = ['{"text":"' . g_l('navigation', '[no_entry]') . '","value":"0"}'];

					foreach($workspaces as $key => $value){
						$optionList[] = '{"text":"' . $value . '","value":"' . $key . '"}';
					}

					$js = '
var doc = top.frames.content;
var optionList = [' . implode(',', $optionList) . '];
doc.weSelect.setOptions("WorkspaceID", optionList);
';
				}

				break;
			default:
				return;
		}

		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', $jscmd->getCmds() . we_html_element::jsElement($js), '<body>' . $html . '</body>');
		exit();
	}

	function getWorkspacesByClassID($classId){
		$workspaces = [];

		if($classId){
			$workspaces = we_navigation_dynList::getWorkspacesForClass($classId);
			asort($workspaces);
		}
		return $workspaces;
	}

	function processVariables(){
		$this->NavigationRule->processVariables();
	}

	static function getAllNavigationRules(){
		//FIXME: add cache?
		$db = new DB_WE();
		$db->query('SELECT * FROM ' . NAVIGATION_RULE_TABLE . ' ORDER BY ID');

		$navigationRules = [];

		while($db->next_record()){
			$navigationRules[] = new we_navigation_rule($db->Record);
		}
		return $navigationRules;
	}

}
