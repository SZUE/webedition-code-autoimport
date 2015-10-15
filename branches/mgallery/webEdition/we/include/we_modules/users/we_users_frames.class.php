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
		$this->Tree = new we_users_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->treeFooterHeight = 40;
		$this->treeDefaultWidth = 224;
		$this->View = new we_users_view(WE_USERS_MODULE_DIR . 'edit_users_frameset.php', 'top.content');
	}

	function getJSCmdCode(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		if(isset($_SESSION['user_session_data'])){
			unset($_SESSION['user_session_data']);
		}

		return we_html_element::jsElement('
var loaded=0;
var hot=0;
var frameset="' . $this->frameset . '";
var g_l={
	save_changed_user:"' . g_l('modules_users', '[save_changed_user]') . '",
	give_org_name:"' . g_l('modules_users', '[give_org_name]') . '"
};
WE().consts.dirs.WE_USERS_MODULE_DIR="' . WE_USERS_MODULE_DIR . '";

parent.document.title = "' . $title . '";
var cgroup=' . ($_SESSION['user']['ID'] ? intval(f('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . $_SESSION["user"]["ID"])) : 0) . ';
') .
				we_html_element::jsScript(JS_DIR . 'we_modules/users/users_view.js');

	}

	function getHTMLFrameset(){
		return parent::getHTMLFrameset(
				$this->Tree->getJSTreeCode()
		);
	}

	function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			exit;
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		$rootjs = "";
		if(!$pid){
			$rootjs.=
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));';
		}
		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"cmd" => "no_cmd"));

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array(), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_element::jsElement($rootjs .
							$this->Tree->getJSLoadTree(we_users_tree::getItems($pid, $offset, $this->Tree->default_segment))
						)
					)
				)
		);
	}

	/* use parent
	  function getHTMLLeft(){}
	 *
	 */

	protected function getHTMLTreeFooter(){//TODO: js an customer anpassen oder umgekehrt!
		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"cmd" => "show_search"));

		$table = new we_html_table(array('class' => 'default', "style" => 'width:100%;margin-top:10px;'), 1, 1);
		$table->setCol(0, 0, array("nowrap" => null, "class" => "small"), we_html_element::jsElement($this->View->getJSSubmitFunction("cmd", "post")) .
			$hiddens .
			we_html_tools::htmlTextInput("keyword", 10, "", "", "", "text", "150px") .
			we_html_button::create_button(we_html_button::SEARCH, "javascript:top.content.we_cmd('search',document.we_form_treefooter.keyword.value);")
		);

		return we_html_element::htmlForm(array("name" => "we_form_treefooter"), $table->getHtml());
	}

	protected function getHTMLEditor(){//TODO: Throw out the the exeption for properties/edbody and use parent
		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&home=1', 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', '', '', false) .
				we_html_element::htmlIFrame('edbody',  $this->frameset . '?pnt=edbody&home=1', 'position: absolute; top: 40px; bottom: 40px; left: 0px; right: 0px;', 'border:0px;width:100%;height:100%;') .
				we_html_element::htmlIFrame('edfooter', $this->frameset . '?pnt=edfooter&home=1' . (($sid = we_base_request::_(we_base_request::INT, 'sid')) !== false ? '&sid=' . $sid : '&home=1'), 'position: absolute; bottom: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', '', '', false)
		);

		return $this->getHTMLDocument($body);
	}

	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){//FIXME: find one working condition
			echo we_html_element::htmlBody(array('style' => 'background-color:#F0EFF0;'), '');
			return;
		}
		$user_object = $_SESSION["user_session_data"];
		echo we_html_element::htmlBody(array('onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody'), $user_object->formHeader(we_base_request::_(we_base_request::INT, "tab", 0)));
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		$yuiSuggest = & weSuggest::getInstance();

		$user_object = (isset($_SESSION["user_session_data"]) ?
				$_SESSION["user_session_data"] :
				new we_users_user());

		echo $this->View->getJSProperty();
		$tab = we_base_request::_(we_base_request::INT, 'tab', 0);
		$permBranch = oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "perm_branch", 0));
		$_content = we_html_element::htmlHiddens(array(
				"ucmd" => "",
				"tab" => $tab,
				"oldtab" => $tab,
				"perm_branch" => $permBranch,
				"old_perm_branch" => $permBranch,
				"obj_name" => $user_object->Name,
				"uid" => $user_object->ID,
				"ctype" => oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "ctype", '')),
				"ctable" => oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "ctable", '')),
				"sd" => 0));

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

		$_content .= $yuiSuggest->getYuiJs();

		$_form = we_html_element::htmlForm(array(
				'name' => 'we_form',
				'method' => 'post',
				'autocomplete' => 'off',
				'onsubmit' => 'return false'
				), $_content);
		echo we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'loaded=1;', 'onunload' => 'doUnload()'), $_form);
	}

	protected function getHTMLEditorFooter(){
		return parent::getHTMLEditorFooter('save_user');
	}

}
