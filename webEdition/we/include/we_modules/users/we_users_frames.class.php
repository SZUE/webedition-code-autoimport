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

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = 'users';
		$this->Tree = new we_tree_users($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->showTreeFooter = true;
		$this->treeDefaultWidth = 224;
		$this->View = new we_users_view($frameset, 'top.content');
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			//use this to get js code
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		$rootjs = "";
		if(!$pid){
			$rootjs.=
				'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));';
		}
		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"cmd" => "no_cmd"));

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array(), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_element::jsElement($rootjs .
							$this->Tree->getJSLoadTree(!$pid, we_tree_users::getItems($pid, $offset, $this->Tree->default_segment))
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

		$table = $hiddens .
			we_html_tools::htmlTextInput("keyword", 10, "", "", 'placeholder="' . g_l('buttons_modules_message', '[search][alt]') . '"', "text", "120px") .
			we_html_button::create_button(we_html_button::SEARCH, "javascript:top.content.we_cmd('search',document.we_form_treefooter.keyword.value);");

		return we_html_element::jsElement($this->View->getJSSubmitFunction("cmd")) . we_html_element::htmlForm(array("name" => "we_form_treefooter"), $table);
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){//FIXME: find one working condition
			return parent::getHTMLEditorHeader(0);
		}
		$user_object = $_SESSION["user_session_data"];
		return $this->getHTMLDocument(we_html_element::htmlBody(array('onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody')), $user_object->formHeader(we_base_request::_(we_base_request::INT, "tab", 0)));
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		$yuiSuggest = & weSuggest::getInstance();

		$user_object = (isset($_SESSION["user_session_data"]) ?
				$_SESSION["user_session_data"] :
				new we_users_user());

		$js = $this->View->getJSProperty();
		$tab = we_base_request::_(we_base_request::INT, 'tab', 0);
		$permBranch = oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "perm_branch", 0));
		$content = we_html_element::htmlHiddens(array(
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
			$content .= $user_object->formDefinition($tab, $permBranch);
		}

		$content .= $yuiSuggest->getYuiJs();

		$form = we_html_element::htmlForm(array(
				'name' => 'we_form',
				'method' => 'post',
				'autocomplete' => 'off',
				'onsubmit' => 'return false'
				), $content);
		return $this->getHTMLDocument(we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'loaded=1;', 'onunload' => 'doUnload()'), $form), $js);
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'edfooter':
				return $this->getHTMLEditorFooter('save_user');
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

}
