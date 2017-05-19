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
		$this->Tree = new we_tree_users($this->jsCmd);
		$this->showTreeFooter = true;
		$this->View = new we_users_view($frameset, 'top.content');
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::RAW, "pid")) === false){
			//use this to get js code
			return $this->getHTMLDocument(we_html_element::htmlBody(), $this->jsCmd->getCmds());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);
		$this->jsCmd->addCmd('loadTree', ['clear' => !$pid, 'items' => we_tree_users::getItems($pid, $offset, $this->Tree->default_segment)]);

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens(["pnt" => "cmd",
							"cmd" => "no_cmd"])
					)
				)
		);
	}

	/* use parent
	  function getHTMLLeft(){}
	 *
	 */

	protected function getHTMLTreeFooter(){
		return $this->getHTMLSearchTreeFooter();
	}

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){//FIXME: find one working condition
			return parent::getHTMLEditorHeader(0);
		}
		$user_object = $_SESSION["user_session_data"];
		return $this->getHTMLDocument(we_html_element::htmlBody(['onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody']), $user_object->formHeader(we_base_request::_(we_base_request::INT, "tab", 0)));
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		$weSuggest = & we_gui_suggest::getInstance();

		$user_object = (isset($_SESSION["user_session_data"]) ?
			$_SESSION["user_session_data"] :
			new we_users_user());

		$js = $this->View->getJSProperty();
		$tab = we_base_request::_(we_base_request::INT, 'tab', 0);
		$permBranch = oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "perm_branch", 0));
		$content = we_html_element::htmlHiddens([
				'cmd' => '',
				"tab" => $tab,
				"oldtab" => $tab,
				"perm_branch" => $permBranch,
				"old_perm_branch" => $permBranch,
				"obj_name" => $user_object->Name,
				"uid" => $user_object->ID,
				"ctype" => oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "ctype", '')),
				"ctable" => oldHtmlspecialchars(we_base_request::_(we_base_request::STRING, "ctable", '')),
				"sd" => 0]);

		if($user_object){
			if(($oldTab = we_base_request::_(we_base_request::INT, 'oldtab')) !== false && ($oldBranch = we_base_request::_(we_base_request::STRING, 'old_perm_branch')) !== false){

				$user_object->preserveState($oldTab, $oldBranch);
				$_SESSION["user_session_data"] = $user_object;
			}
			if(($start = we_base_request::_(we_base_request::INT, 'seem_start_file')) !== false){
				$_SESSION["save_user_seem_start_file"][we_base_request::_(we_base_request::INT, "uid")] = $start;
			}
			$content .= $user_object->formDefinition($this->jsCmd, $tab, $permBranch);
		}

		$form = we_html_element::htmlForm(['name' => 'we_form',
				'method' => 'post',
				'autocomplete' => 'off',
				'onsubmit' => 'return false'
				], $content);
		return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody', 'onload' => 'loaded=1;', 'onunload' => 'doUnload()'], $form), $js);
	}

	function getHTMLSearch(){
		$keyword = we_base_request::_(we_base_request::RAW, 'keyword', "");
		$arr = explode(' ', strToLower($keyword));
		$DB_WE = $GLOBALS['DB_WE'];

		$array_and = [];
		$array_or = [];
		$array_not = [];

		for($i = 0; $i < count($arr); $i++){
			switch($arr[$i]){
				case 'not':
					$i++;
					$array_not[] = $arr[$i];
					break;
				case 'or':
					$i++;
					$array_or[] = $arr[$i];
					break;
				case 'and':
					$i++;
				//no break
				default:
					$array_and[] = $arr[$i];
					break;
			}
		}
		$condition = "";
		foreach($array_and as $value){
			$value = $DB_WE->escape($value);
			$condition .= ($condition ? ' AND ' : '') .
				'(First LIKE "%' . $value . '%" OR Second LIKE "%' . $value . '%" OR username LIKE "%' . $value . '%" OR Address LIKE "%' . $value . '%" OR City LIKE "%' . $value . '%" OR State LIKE "%' . $value . '%" OR Country LIKE "%' . $value . '%" OR Tel_preselection LIKE "%' . $value . '%" OR Fax_preselection LIKE "%' . $value . '%" OR Telephone LIKE "%' . $value . '%" OR Fax LIKE "%' . $value . '%" OR Description LIKE "%' . $value . '%")';
		}
		foreach($array_or as $value){
			$value = $DB_WE->escape($value);
			$condition .= ($condition ? ' OR ' : '') .
				'(First LIKE "%' . $value . '%" OR Second LIKE "%' . $value . '%" OR username LIKE "%' . $value . '%" OR Address LIKE "%' . $value . '%" OR City LIKE "%' . $value . '%" OR State LIKE "%' . $value . '%" OR Country LIKE "%' . $value . '%" OR Tel_preselection LIKE "%' . $value . '%" OR Fax_preselection LIKE "%' . $value . '%" OR Telephone LIKE "%' . $value . '%" OR Fax LIKE "%' . $value . '%" OR Description LIKE "%' . $value . '%")';
		}
		foreach($array_not as $value){
			$value = $DB_WE->escape($value);
			$condition .= ($condition ? ' AND NOT ' : '') .
				'(First LIKE "%' . $value . '%" OR Second LIKE "%' . $value . '%" OR username LIKE "%' . $value . '%" OR Address LIKE "%' . $value . '%" OR City LIKE "%' . $value . '%" OR State LIKE "%' . $value . '%" OR Country LIKE "%' . $value . '%" OR Tel_preselection LIKE "%' . $value . '%" OR Fax_preselection LIKE "%' . $value . '%" OR Telephone LIKE "%' . $value . '%" OR Fax LIKE "%' . $value . '%" OR Description LIKE "%' . $value . '%")';
		}

		$DB_WE->query('SELECT ID,username FROM ' . USER_TABLE . ($condition ? ' WHERE ' . $condition : '') . ' ORDER BY username');

		$select = '<div style="background-color:white;width:520px;height:220px;"/>';
		if($DB_WE->num_rows()){
			$select = '<select name="search_results" size="20" style="width:520px;height:220px;" ondblclick="top.opener.top.we_cmd(\'check_user_display\',document.we_form.search_results.value); top.close();">';
			while($DB_WE->next_record()){
				$select .= '<option value="' . $DB_WE->f("ID") . '">' . $DB_WE->f("Text") . '</option>';
			}
			$select .= '</select>';
		}

		$buttons = we_html_button::position_yes_no_cancel(
				we_html_button::create_button(we_html_button::EDIT, "javascript:top.opener.top.we_cmd('check_user_display',document.we_form.search_results.value); if(document.we_form.search_results.value){top.close()}"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
		);

		$content = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('keyword', 24, $keyword, "", "", "text", 485), g_l('modules_users', '[search_for]'), "left", "defaultfont", we_html_button::create_button(we_html_button::SEARCH, "javascript:document.we_form.submit();")
			) . '<div style="height:20px;"></div>' .
			we_html_tools::htmlFormElementTable($select, g_l('modules_users', '[search_result]'));

		return $this->getHTMLDocument(we_html_element::htmlBody(['class' => 'weEditorBody', 'style' => "margin:10px 20px;"], we_html_element::htmlForm(['name' => 'we_form',
						'method' => 'post'], we_html_element::htmlHiddens([
							'mod' => 'users',
							'pnt' => 'search']) .
						we_html_tools::htmlDialogLayout($content, g_l('modules_users', '[search]'), $buttons))
		));
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'edfooter':
				return $this->getHTMLEditorFooter([
						we_html_button::SAVE => [['NEW_GROUP', 'NEW_USER', 'SAVE_USER', 'SAVE_GROUP'], 'save_user'],
						we_html_button::DELETE => [['DELETE_USER', 'DELETE_GROUP'], 'delete_user']
				]);
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

}
