<?php

/**
 * webEdition CMS
 *
 * $Rev: 13771 $
 * $Author: mokraemer $
 * $Date: 2017-05-20 01:13:43 +0200 (Sa, 20. Mai 2017) $
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
class we_doctype_view extends we_modules_view{

	function __construct(){
		parent::__construct();
		$this->Model = new we_docTypes();
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return
			parent::getJSTop() .
			we_html_element::jsScript(WE_JS_MODULES_DIR . '/doctype/doctype_top.js', "parent.document.title='" . $title . "'");
	}

	function getJSProperty(array $jsVars = []){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . '/doctype/doctype_prop.js');
	}

	function processCommands(we_base_jsCmd $jsCmd){
		switch(we_base_request::_(we_base_request::STRING, "we_cmd", '', 0)){
			case "save_docType":
				if(!we_base_permission::hasPerm("EDIT_DOCTYPE")){
					$jsCmd->addMsg(g_l('weClass', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$oldID = $this->Model->we_initSessDat($_SESSION['weS']['doctype_session']);
				if(preg_match('|[\'",]|', $this->Model->DocType)){
					$jsCmd->addMsg(g_l('alert', '[doctype_hochkomma]'), we_base_util::WE_MESSAGE_ERROR);
				} else if(!$this->Model->DocType){
					$jsCmd->addMsg(g_l('alert', '[doctype_empty]'), we_base_util::WE_MESSAGE_ERROR);
				} elseif(($id = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.DocType="' . $GLOBALS['DB_WE']->escape($this->Model->DocType) . '" LIMIT 1')) && ($this->Model->ID != $id)){
					$jsCmd->addMsg(sprintf(g_l('weClass', '[doctype_save_nok_exist]'), $this->Model->DocType), we_base_util::WE_MESSAGE_ERROR);
				} elseif($this->Model->we_save()){
					$jsCmd->addMsg(sprintf(g_l('weClass', '[doctype_save_ok]'), $this->Model->DocType), we_base_util::WE_MESSAGE_NOTICE);
					list($cmd, $val) = we_main_headermenu::getMenuReloadCode('top.opener.', true);
					$jsCmd->addCmd($cmd, $val);
					if($this->Model->wasUpdate){
						$jsCmd->addCmd('updateTreeEntry', ['id' => $this->Model->ID, 'parentid' => $this->Model->ParentID, 'text' => $this->Model->DocType]);
					} else {
						$jsCmd->addCmd('makeTreeEntry', ['id' => $this->Model->ID, 'parentid' => $this->Model->ParentID, 'text' => $this->Model->DocType, 'open' => false, 'contenttype' => 'we/doctype',
							'table' => DOC_TYPES_TABLE, 'published' => 1]);
					}
				} else {
					echo "ERROR";
				}
				break;
			case "newDocType":
				//leave empty object & save to session
				break;
			case "deleteDocTypeok":
				if(!we_base_permission::hasPerm("EDIT_DOCTYPE")){
					$jsCmd->addMsg(g_l('alert', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
				$name = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . $id);
				$del = false;
				if($name){
					if(f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE DocType=' . $id . ' LIMIT 1')){
						$jsCmd->addMsg(sprintf(g_l('weClass', '[doctype_delete_nok]'), $name), we_base_util::WE_MESSAGE_ERROR);
					} else {
						$GLOBALS['DB_WE']->query('DELETE FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . $id);

						// Fast Fix for deleting entries from tblLangLink: #5840
						$GLOBALS['DB_WE']->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblDocTypes" AND (DID=' . $id . ' OR LDID=' . $id . ')');
						$jsCmd->addMsg(sprintf(g_l('weClass', '[doctype_delete_ok]'), $name), we_base_util::WE_MESSAGE_NOTICE);
						$del = true;
					}
					if($del){
						if(($id = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' dt ORDER BY dt.DocType LIMIT 1'))){
							$this->Model->initByID($id, DOC_TYPES_TABLE);
						}
						list($cmd, $val) = we_main_headermenu::getMenuReloadCode('top.opener.', true);
						$jsCmd->addCmd($cmd, $val);
					} else {
						$this->Model->initByID(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), DOC_TYPES_TABLE);
					}
				}
				break;
			case 'add_dt_template':
				$this->Model->we_initSessDat($_SESSION['weS']['doctype_session']);
				$foo = array_merge(explode(',', $this->Model->Templates), we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1));
				$this->Model->Templates = implode(',', array_unique($foo));
				break;
			case 'delete_dt_template':
				$this->Model->we_initSessDat($_SESSION['weS']['doctype_session']);
				$foo = explode(',', $this->Model->Templates);
				$cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
				if($cmd1 && ($pos = array_search($cmd1, $foo, false)) !== false){
					unset($foo[$pos]);
				}
				if($this->Model->TemplateID == $cmd1){
					$this->Model->TemplateID = ($foo ? $foo[0] : 0);
				}
				$this->Model->Templates = implode(',', $foo);
				break;
			case "dt_add_cat":
				$this->Model->we_initSessDat($_SESSION['weS']['doctype_session']);
				if(($id = we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1))){
					$this->Model->addCat($id);
				}
				break;
			case "dt_delete_cat":
				$this->Model->we_initSessDat($_SESSION['weS']['doctype_session']);
				if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1))){
					$this->Model->delCat($id);
				}
				break;
			default:
				$id = (we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1) ?: 0);
				if(!$id){
					$dtq = we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']);
					$id = f('SELECT dt.ID FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where'] . ' LIMIT 1');
				}

				if($id){
					$this->Model->initByID($id, DOC_TYPES_TABLE);
				}
		}
		$this->Model->saveInSession($_SESSION['weS']['doctype_session']);
	}

	function processVariables(){

	}

	public function getHomeScreen(){
		$hiddens = ["cmd" => "home",
			'pnt' => 'edbody'
		];
		$content = we_html_button::create_button('new_doctype', "javascript:we_cmd('newDocType')");

		return parent::getActualHomeScreen("doctype", $content, we_html_element::htmlForm(['name' => 'we_form'], $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden("home", 0)));
	}

}
