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
class we_users_frames extends we_modules_frame{

	function __construct(){
		parent::__construct(WE_USERS_MODULE_DIR . "edit_users_frameset.php");
		$this->module = 'users';
		$this->useMainTree = false;
		$this->treeFooterHeight = 40;
		$this->treeDefaultWidth = 224;
		$this->View = new we_users_view(WE_USERS_MODULE_DIR . 'edit_users_frameset.php', 'top.content');
	}

	function getJSCmdCode(){
		return $this->View->getJSTop_tmp();
	}

	protected function getDoClick(){
		return 'function doClick(id,ct,table){
	top.content.we_cmd(\'display_user\',id,ct,table);
	}';
	}

	function getJSTreeCode(){ //TODO: move to new class weUsersTree
		$jsInit = '
var table="' . USER_TABLE . '";
var tree_icon_dir="' . TREE_ICON_DIR . '";
var tree_img_dir="' . TREE_IMAGE_DIR . '";
var we_dir="' . WEBEDITION_DIR . '";
' . parent::getTree_g_l();

		$jsCode = '
function loadData() {
	menuDaten.clear();
';

		if(permissionhandler::hasPerm("NEW_USER") || permissionhandler::hasPerm("NEW_GROUP") || permissionhandler::hasPerm("SAVE_USER") || permissionhandler::hasPerm("SAVE_GROUP") || permissionhandler::hasPerm("DELETE_USER") || permissionhandler::hasPerm("DELETE_GROUP")){
			if(permissionhandler::hasPerm("ADMINISTRATOR")){
				$parent_path = '/';
				$startloc = 0;
			} else {
				$foo = getHash('SELECT Path,ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION["user"]["ID"]), $this->db);
				$parent_path = str_replace("\\", "/", dirname($foo["Path"]));
				$startloc = $foo["ParentID"];
			}

			$jsCode .= 'startloc=' . $startloc . ';';

			$this->db->query('SELECT ID,ParentID,Text,Type,Permissions,LoginDenied FROM ' . USER_TABLE . " WHERE Path LIKE '" . $this->db->escape($parent_path) . "%' ORDER BY Text ASC");

			while($this->db->next_record()){
				if($this->db->f('Type') == we_users_user::TYPE_USER_GROUP){
					$jsCode .= "menuDaten.add(new dirEntry('folder'," . $this->db->f('ID') . ',' . $this->db->f("ParentID") . ",'" . addslashes($this->db->f("Text")) . "',false,'group','" . USER_TABLE . "',1));";
				} else {
					$p = unserialize($this->db->f("Permissions"));
					$jsCode .= "menuDaten.add(new urlEntry('" . ($this->db->f('Type') == we_users_user::TYPE_ALIAS ? 'user_alias.gif' : 'user.gif') . "'," . $this->db->f("ID") . "," . $this->db->f("ParentID") . ",'" . addslashes($this->db->f("Text")) . "','" . ($this->db->f("Type") == we_users_user::TYPE_ALIAS ? 'alias' : 'user') . "','" . USER_TABLE . "','" . (isset($p["ADMINISTRATOR"]) && $p["ADMINISTRATOR"]) . "','" . $this->db->f("LoginDenied") . "'));";
				}
			}
		}

		$jsCode .= '
}
';

		return we_html_element::jsElement($jsInit) .
				we_html_element::jsScript(JS_DIR . 'users_tree.js') .
				we_html_element::jsElement($jsCode);
	}

	function getHTMLFrameset(){//TODO: use parent as soon as userTree.class exists
		$extraHead = $this->getJSCmdCode() . $this->getJSTreeCode();
		return parent::getHTMLFrameset($extraHead);
	}

	function getHTMLCmd(){
		$this->View->processCommands();
	}

	/* use parent
	  function getHTMLLeft(){}
	 *
	 */

	protected function getHTMLTreeFooter(){//TODO: js an customer anpassen oder umgekehrt!
		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
				we_html_element::htmlHidden(array("name" => "cmd", "value" => "show_search"));

		$table = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "style" => 'width:100%;margin-top:10px;'), 1, 1);
		$table->setCol(0, 0, array("nowrap" => null, "class" => "small"), we_html_element::jsElement($this->View->getJSSubmitFunction("cmd", "post")) .
				$hiddens .
				we_html_button::create_button_table(
						array(
							we_html_tools::htmlTextInput("keyword", 10, "", "", "", "text", "150px"),
							we_html_button::create_button("image:btn_function_search", "javascript:top.content.we_cmd('search',document.we_form_treefooter.keyword.value);")
						)
				)
		);

		return we_html_element::htmlForm(array("name" => "we_form_treefooter"), $table->getHtml());
	}

	protected function getHTMLEditor(){//TODO: Throw out the the exeption for properties/edbody and use parent
		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&home=1', 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;') .
						we_html_element::htmlIFrame('edbody', WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=users', 'position: absolute; top: 40px; bottom: 40px; left: 0px; right: 0px; overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;') .
						we_html_element::htmlIFrame('edfooter', $this->frameset . '?pnt=edfooter&home=1' . (($sid = we_base_request::_(we_base_request::INT, 'sid')) !== false ? '&sid=' . $sid : '&home=1'), 'position: absolute; bottom: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;')
		);

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){//FIXME: find one working condition
			echo we_html_element::htmlBody(array('style' => 'background-color:#F0EFF0;'), '');
		} else {
			$user_object = $_SESSION["user_session_data"];
			echo we_html_element::htmlBody(array('onresize' => 'setFrameSize()', 'onload' => 'setFrameSize()', 'id' => 'eHeaderBody'), $user_object->formHeader(we_base_request::_(we_base_request::INT, "tab", 0)));
		}
	}

	protected function getHTMLEditorBody(){
		$yuiSuggest = & weSuggest::getInstance();

		$user_object = (isset($_SESSION["user_session_data"]) ?
						$_SESSION["user_session_data"] :
						new we_users_user());

		echo $this->View->getJSProperty();
		$tab = we_base_request::_(we_base_request::INT, 'tab', 0);
		$permBranch = oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "perm_branch", 0));
		$_content = we_html_element::htmlHidden(array("name" => "ucmd", "value" => "",)) .
				we_html_element::htmlHidden(array("name" => "tab", "value" => $tab)) .
				we_html_element::htmlHidden(array("name" => "oldtab", "value" => $tab)) .
				we_html_element::htmlHidden(array("name" => "perm_branch", "value" => $permBranch)) .
				we_html_element::htmlHidden(array("name" => "old_perm_branch", "value" => $permBranch)) .
				we_html_element::htmlHidden(array("name" => "obj_name", "value" => $user_object->Name,)) .
				we_html_element::htmlHidden(array("name" => "uid", "value" => $user_object->ID,)) .
				we_html_element::htmlHidden(array("name" => "ctype", "value" => oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "ctype", '')))) .
				we_html_element::htmlHidden(array("name" => "ctable", "value" => oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "ctable", '')))) .
				we_html_element::htmlHidden(array("name" => "sd", "value" => 0,));

		if($user_object){
			if(($oldTab = we_base_request::_(we_base_request::INT, 'oldtab')) !== false && ($oldBranch = we_base_request::_(we_base_request::STRING, 'old_perm_branch')) !== false){

				$user_object->preserveState($oldTab, $oldBranch);
				$_SESSION["user_session_data"] = $user_object;
			}
			if(($start = we_base_request::_(we_base_request::INT, 'seem_start_file')) !== false){
				$_SESSION["save_user_seem_start_file"][we_base_request::_(we_base_request::INT, "uid")] = $start;
			}
			$_content .= $user_object->formDefinition($tab, $permBranch);
		}

		$_content .= $yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs();

		$_form = we_html_element::htmlForm(array(
					'name' => 'we_form',
					'method' => 'post',
					'autocomplete' => 'off',
					'onsubmit' => 'return false'
						), $_content);
		echo we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'loaded=1;', 'onunload' => 'doUnload()'), $_form);
	}

	protected function getHTMLEditorFooter(){
		if(isset($_SESSION["user_session_data"])){
			$user_object = $_SESSION["user_session_data"];
		}

		return parent::getHTMLEditorFooter('save_user');
	}

}
