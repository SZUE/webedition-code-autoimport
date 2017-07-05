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
class we_thumbnail_view extends we_modules_view{

	function __construct(){
		parent::__construct();
		$this->Model = new we_thumbnail_thumbnail();
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return
			parent::getJSTop() .
			we_html_element::jsScript(WE_JS_MODULES_DIR . '/thumbnail/thumbnail_top.js', "parent.document.title='" . $title . "'");
	}

	function getJSProperty(array $jsVars = []){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . '/thumbnail/thumbnail_prop.js');
	}

	function processCommands(we_base_jsCmd $jsCmd){
		switch(we_base_request::_(we_base_request::STRING, "we_cmd", '', 0)){
			case "we_save":
				if(!we_base_permission::hasPerm('ADMINISTRATOR')){
					$jsCmd->addMsg(g_l('weClass', '[no_perms]'), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
				$this->Model->we_initSessDat($_SESSION['weS']['thumbnail_session']);
				if(preg_match('|[\'",]|', $this->Model->Name)){
					$jsCmd->addMsg(g_l('alert', '[thumbnail_hochkomma]'), we_base_util::WE_MESSAGE_ERROR);
				} else if(!$this->Model->Name){
					$jsCmd->addMsg(g_l('alert', '[thumbnail_empty]'), we_base_util::WE_MESSAGE_ERROR);
				} elseif(f('SELECT ID FROM ' . THUMBNAILS_TABLE. ' WHERE Name="' . $GLOBALS['DB_WE']->escape($this->Model->Name) . '" AND ID!='.intval($this->Model->ID).' LIMIT 1')){
					$jsCmd->addMsg(sprintf(g_l('alert', '[thumbnail_exists]'), $this->Model->Name), we_base_util::WE_MESSAGE_ERROR);
				} elseif($this->Model->we_save()){
					$jsCmd->addMsg(sprintf(g_l('thumbnails', '[saved_successfully]'), $this->Model->Name), we_base_util::WE_MESSAGE_NOTICE);
					if($this->Model->wasUpdate){
						$jsCmd->addCmd('updateTreeEntry', ['id' => $this->Model->ID, 'parentid' => $this->Model->ParentID, 'text' => $this->Model->Name]);
					} else {
						$jsCmd->addCmd('makeTreeEntry', ['id' => $this->Model->ID, 'parentid' => $this->Model->ParentID, 'text' => $this->Model->Name, 'open' => false, 'contenttype' => 'we/thumbnail',
							'table' => THUMBNAILS_TABLE, 'published' => 1]);
					}
				} else {
					echo "ERROR";
				}
				break;
			default:
				$id = (we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1) ?: 0);

				if($id){
					$this->Model->initByID($id, THUMBNAILS_TABLE);
				}
		}
		$this->Model->saveInSession($_SESSION['weS']['thumbnail_session']);
	}

	function processVariables(){

	}

	public function getHomeScreen(){
		$hiddens = ["cmd" => "home",
			'pnt' => 'edbody'
		];

		$content = we_html_button::create_button('add', "javascript:we_cmd('add_thumbnail')");

		return parent::getActualHomeScreen("thumbnail", $content, we_html_element::htmlForm(['name' => 'we_form'], $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden("home", 0)));
	}

}
