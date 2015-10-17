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
class we_customer_frames extends we_modules_frame{
	var $View;
	var $jsOut_fieldTypesByName;
	public $module = 'customer';
	protected $treeHeaderHeight = 40;
	protected $treeFooterHeight = 40;
	protected $treeDefaultWidth = 244;

	public function __construct(){
		parent::__construct(WE_MODULES_DIR . 'show.php?mod=customer');
		$this->Tree = new we_customer_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_customer_view();
	}

	public function getHTMLDocumentHeader($what = '', $mode = ''){
		//We need to set this (and in corresponding frames, since the data in database is formated this way
		if(!($mode === 'export' && we_base_request::_(we_base_request::INT, "step") == 5)){
			return parent::getHTMLDocumentHeader(DEFAULT_CHARSET);
		}
		return parent::getHTMLDocumentHeader();
	}

	function getHTML($what = '', $mode = 0, $step = 0){
		switch($what){
			case 'edfooter':
				return $this->getHTMLEditorFooter('save_customer');
			case 'customer_admin':
				return $this->getHTMLCustomerAdmin();
			case 'branch_editor':
				return $this->getHTMLFieldEditor('branch', $mode);
			case 'field_editor':
				return $this->getHTMLFieldEditor('field', $mode);
			case 'sort_admin':
				return we_customer_add::getHTMLSortEditor($this);
			case 'search':
				return $this->getHTMLSearch();
			case 'settings':
				return $this->getHTMLSettings();
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$this->View->customer->clearSessionVars();
		$this->View->settings->load(false);
		$extraHead = $this->Tree->getJSTreeCode() .
			$this->View->getJSTreeHeader();

		$sid = we_base_request::_(we_base_request::RAW, 'sid', false);
		$extraUrlParams = $sid !== false ? '&sid=' . $sid : '';

		return parent::getHTMLFrameset($extraHead, $extraUrlParams);
	}

	function getJSCmdCode(){
		return $this->View->getJSTop();
	}

	//TODO: move editor-body relatetd stuff to weCustomerView! => note dependencies on jsOut_fieldTypesByName
	function getHTMLFieldsSelect($branch){
		$select = new we_html_select(array('name' => 'branch'));

		$fields_names = $this->View->customer->getFieldsNames($branch, $this->View->settings->getEditSort());
		$this->jsOut_fieldTypesByName = 'var fieldTypesByName = [];';
		foreach($fields_names as $val){
			$tmp = $this->View->getFieldProperties($val);
			$this->jsOut_fieldTypesByName .= "fieldTypesByName['" . $val . "'] = '" . (isset($tmp['type']) ? $tmp['type'] : '') . "';";
		}
		if(is_array($fields_names)){
			foreach($fields_names as $k => $field){
				$select->addOption($k, ($this->View->customer->isProperty($field) ?
						$this->View->settings->getPropertyTitle($field) :
						$field)
				);
			}
		}

		return $select;
	}

	protected function getHTMLEditorHeader(){
		$extraJS = 'var aTabs={';

		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('bgcolor' => '#F0EFF0'), ''));
		}

		$tabs = new we_tabs();

		$branches_names = $this->View->customer->getBranchesNames();

		$tabs->addTab(new we_tab(g_l('modules_customer', '[common]'), we_tab::NORMAL, "setTab('" . g_l('modules_customer', '[common]') . "');", array("id" => "common")));
		$extraJS .= "'" . g_l('modules_customer', '[common]') . "':'common',";
		$branchCount = 0;
		foreach($branches_names as $branch){
			$tabs->addTab(new we_tab($branch, we_tab::NORMAL, "setTab('" . $branch . "');", array("id" => "branch_" . $branchCount)));
			$extraJS .= "'" . $branch . "':'branch_" . $branchCount . "',";
			$branchCount++;
		}
		$tabs->addTab(new we_tab(g_l('modules_customer', '[other]'), we_tab::NORMAL, "setTab('" . g_l('modules_customer', '[other]') . "');", array("id" => "other")));
		$tabs->addTab(new we_tab(g_l('modules_customer', '[all]'), we_tab::NORMAL, "setTab('" . g_l('modules_customer', '[all]') . "');", array("id" => "all")));
		$extraJS .= "'" . g_l('modules_customer', '[other]') . "':'other'," .
			"'" . g_l('modules_customer', '[all]') . "':'all',";
//((top.content.activ_tab=="' . g_l('modules_customer','[other]') . '") ? TAB_ACTIVE : TAB_NORMAL)

		if(defined('SHOP_TABLE')){
			$tabs->addTab(new we_tab(g_l('modules_customer', '[orderTab]'), we_tab::NORMAL, "setTab('" . g_l('modules_customer', '[orderTab]') . "');", array("id" => "orderTab")));
			$extraJS .= "'" . g_l('modules_customer', '[orderTab]') . "':'orderTab',";
		}
		if(defined('OBJECT_FILES_TABLE')){
			$tabs->addTab(new we_tab(g_l('modules_customer', '[objectTab]'), we_tab::NORMAL, "setTab('" . g_l('modules_customer', '[objectTab]') . "');", array("id" => "objectTab")));
			$extraJS .= "'" . g_l('modules_customer', '[objectTab]') . "':'objectTab',";
		}
		$tabs->addTab(new we_tab(g_l('modules_customer', '[documentTab]'), we_tab::NORMAL, "setTab('" . g_l('modules_customer', '[documentTab]') . "');", array("id" => "documentTab")));
		$extraJS .= "'" . g_l('modules_customer', '[documentTab]') . "':'documentTab'"
			. '};';

		/*
		  $table = new we_html_table(array("width" => '100%', 'class' => 'default'), 3, 1);
		  $table->setCol(1, 0, array('style'=>'vertical-align:top", "class" => "small", 'style' => 'padding-left:15px;padding-right:10px;'), we_html_element::htmlB(
		  g_l('modules_customer', '[customer]') . ":&nbsp;" . $this->View->customer->Username
		  )
		  );
		 */
		$extraJS .= 'if(top.content.activ_tab) document.getElementById(aTabs[top.content.activ_tab]).className="tabActive"; else document.getElementById("common").className="tabActive"';

		$text = $this->View->customer->Username;

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(array('onresize' => 'weTabs.setFrameSize()', 'onload' => 'weTabs.setFrameSize()', 'id' => 'eHeaderBody',), we_html_element::htmlDiv(array('id' => 'main'), we_html_element::htmlDiv(array('id' => 'headrow'), we_html_element::htmlNobr(
							we_html_element::htmlB(str_replace(' ', '&nbsp;', g_l('modules_customer', '[customer]')) . ':&nbsp;') .
							we_html_element::htmlSpan(array('id' => 'h_path', 'class' => 'header_small'), '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
							)
						)
					) .
					$tabs->getHTML()
				) .
				we_html_element::jsElement($extraJS)
		);

		return $this->getHTMLDocument($body, we_tabs::getHeader() .
				we_html_element::jsElement('
function setTab(tab) {
	top.content.activ_tab=tab;
	parent.edbody.we_cmd(\'switchPage\',tab);
}'));
	}

	protected function getHTMLEditorBody(){
		$hiddens = array('cmd' => 'customer_edit', 'pnt' => 'edbody', 'activ_sort' => 0);

		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}

		$branch = we_base_request::_(we_base_request::STRING, 'branch', g_l('modules_customer', '[common]'));

		return $this->getHTMLDocument(we_html_element::htmlBody(
					array('class' => 'weEditorBody', 'onload' => 'loaded=1', 'onunload' => 'doUnload()'), we_html_element::htmlForm(
						array('name' => 'we_form', 'autocomplete' => 'off'), $this->View->getCommonHiddens($hiddens) .
						$this->View->getHTMLProperties($branch))), $this->View->getJSProperty());
	}

	protected function getHTMLTreeHeader(){
		return we_customer_add::getHTMLTreeHeader($this);
	}

	protected function getHTMLTreeFooter(){
		$hiddens = we_html_element::htmlHiddens(array(
				'pnt' => 'cmd',
				'cmd' => 'show_search'));

		$table = new we_html_table(array('class' => 'default', "width" => '100%'), 1, 1);
		$table->setCol(0, 0, array("nowrap" => null, "class" => "small"), we_html_element::jsElement($this->View->getJSSubmitFunction("cmd", "post")) .
			$hiddens .
			we_html_tools::htmlTextInput("keyword", 10, '', '', '', "text", "150px") .
			we_html_button::create_button(we_html_button::SEARCH, "javascript:submitForm('cmd', '', '', 'we_form_treefooter')")
		);

		return we_html_element::htmlForm(array("name" => "we_form_treefooter", "target" => "cmd"), $table->getHtml());
	}

	function getHTMLCustomerAdmin(){
		$branch = we_base_request::_(we_base_request::STRING, "branch", g_l('modules_customer', '[other]'));
		$branch_select = we_base_request::_(we_base_request::STRING, "branch", g_l('modules_customer', '[other]'));

		$select = $this->View->getHTMLBranchSelect(false);
		$select->setAttributes(array("name" => "branch_select", "class" => 'weSelect', 'onchange' => "selectBranch()", "style" => "width:150px;"));
		$select->selectOption($branch_select);

		$fields = $this->getHTMLFieldsSelect($branch);
		$fields->setAttributes(array("name" => "fields_select", "size" => 15, "onchange" => '', "style" => "width:350px;height:250px;"));
		//$hiddens = rray("name" => "field", "value" => ''));

		$buttons_table = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('open_add_field')") .
			we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('open_edit_field')") .
			we_html_button::create_button(we_html_button::DELETE, "javascript:we_cmd('delete_field')") .
			we_html_button::create_button(we_html_button::DIRUP, "javascript:we_cmd('move_field_up')") .
			we_html_button::create_button(we_html_button::DIRDOWN, "javascript:we_cmd('move_field_down')") .
			we_html_element::htmlSpan(array("class" => "defaultgray"), g_l('modules_customer', '[sort_edit_fields_explain]')) .
			we_html_button::create_button("reset", "javascript:we_cmd('reset_edit_order')");

		$table = new we_html_table(array('class' => 'default', "width" => 500), 4, 5);

		$table->setCol(0, 0, array("class" => "defaultgray", 'style' => 'padding-right:10px;'), g_l('modules_customer', '[branch]'));
		$table->setCol(0, 2, array("class" => "defaultgray"), g_l('modules_customer', '[branch_select]'));
		$table->setCol(1, 0, array('style' => 'padding-right:10px;'), we_html_tools::htmlTextInput("branch", 48, $branch, '', 'style="width:350px;"'));
		$table->setCol(1, 2, array('style' => 'padding-right:10px;'), $select->getHtml());
		$table->setCol(1, 4, array('style' => 'padding-bottom:10px;'), we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('open_edit_branch')"));


		$table->setCol(2, 0, array("class" => "defaultgray", 'style' => 'vertical-align:top;'), g_l('modules_customer', '[fields]'));
		$table->setCol(3, 0, array('style' => 'vertical-align:top;padding-right:10px;'), $fields->getHtml());
		$table->setCol(3, 2, array('style' => 'vertical-align:top;'), $buttons_table);

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();', 'style' => 'overflow:hidden'), $this->View->getJSAdmin() .
					we_html_element::htmlForm(array("name" => "we_form"), we_html_element::htmlHiddens(array(
							"cmd" => "switchBranch",
							"pnt" => "customer_admin")) .
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_customer', '[field_admin]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()"))
					)
				)
		);
	}

	function getHTMLFieldEditor($type, $mode){
		$field = we_base_request::_(we_base_request::STRING, "field", '');
		$branch = we_base_request::_(we_base_request::STRING, "branch", g_l('modules_customer', '[other]'));

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "field_editor",
				"cmd" => "no_cmd",
				"branch" => "$branch",
				"art" => "$mode",
				($type === "field" ? "field" : '') => "$field"));

		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");

		switch($type){
			case "branch":
				$hiddens.=we_html_element::htmlHidden("pnt", "branch_editor");
				$edit = new we_html_table(array("width" => 300), 1, 2);
				$edit->setCol(0, 0, array('style' => 'vertical-align:middle;', "class" => "defaultgray"), g_l('modules_customer', '[field_name]'));
				$edit->setCol(0, 1, array('style' => 'vertical-align:middle;', "class" => "defaultfont"), we_html_tools::htmlTextInput("name", 26, $branch, '', ''));

				$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_branch')");
				break;
			default:
				$hiddens.=we_html_element::htmlHidden("pnt", "field_editor");
				$field_props = $this->View->getFieldProperties($field);

				$types = new we_html_select(array("name" => "field_type", "class" => "weSelect", "style" => "width:200px;", 'onchange' => 'setStatusEncryption(this.value);'));
				$types->addOptions(array_combine(array_keys($this->View->settings->field_types), array_keys($this->View->settings->field_types)));
				if(isset($field_props["type"])){
					$types->selectOption($field_props["type"]);
					$curType = $field_props["type"];
				} else {
					$curType = 'input';
				}

				$enc = new we_html_select(array("name" => "field_encrypt", "class" => "weSelect", "style" => "width:200px;"));
				$enc->addOptions(array(0 => g_l('global', '[no]'), 1 => g_l('global', '[yes]')));

				if(!empty($field_props['encrypt'])){
					$enc->selectOption(1);
				}

				$edit = new we_html_table(array("width" => 300), 5, 2);

				$edit->setCol(0, 0, array('style' => 'vertical-align:middle;', "class" => "defaultgray"), g_l('modules_customer', '[branch]'));
				$edit->setCol(0, 1, array('style' => 'vertical-align:middle;', "class" => "defaultfont"), $branch);

				$edit->setCol(1, 0, array('style' => 'vertical-align:middle;', "class" => "defaultgray"), g_l('modules_customer', '[field_name]'));
				$edit->setCol(1, 1, array('style' => 'vertical-align:middle;', "class" => "defaultfont"), we_html_tools::htmlTextInput("name", 26, (isset($field_props['name']) ? $field_props['name'] : ''), '', ''));

				$edit->setCol(2, 0, array('style' => 'vertical-align:middle;', "class" => "defaultgray"), g_l('modules_customer', '[field_type]'));

				$edit->setCol(2, 1, array('style' => 'vertical-align:middle;', "class" => "defaultfont"), $types->getHtml());

				$edit->setCol(3, 0, array('style' => 'vertical-align:middle;', "class" => "defaultgray"), g_l('modules_customer', '[field_default]'));
				$edit->setCol(3, 1, array('style' => 'vertical-align:middle;', "class" => "defaultfont"), we_html_tools::htmlTextInput("field_default", 26, (isset($field_props['default']) ? $field_props['default'] : ''), '', ''));

				$edit->setCol(4, 0, array('style' => 'vertical-align:middle;', "class" => "defaultgray"), g_l('modules_customer', '[encryptField]'));
				$edit->setCol(4, 1, array('style' => 'vertical-align:middle;', "class" => "defaultfont"), $enc->getHtml());

				$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_field')");
		}

		return
			$this->getHTMLDocument(
				we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();setStatusEncryption(\'' . $curType . '\');'), $this->View->getJSAdmin() .
					we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_tools::htmlDialogLayout($edit->getHtml(), (
							$type === "branch" ?
								(g_l('modules_customer', '[edit_branche]')) :
								g_l('modules_customer', ($mode === "edit" ? '[edit_field]' : '[add_field]'))
							), we_html_button::position_yes_no_cancel($save, null, $cancel)
						)
					)
				)
		);
	}

	function getHTMLCmd(){
		$p = we_base_request::_(we_base_request::RAW, 'pid');
		if($p === false){
			exit();
		}
		$pid = ($GLOBALS['WE_BACKENDCHARSET'] === 'UTF-8') ?
			utf8_encode($p) :
			$p;

		$sortField = we_base_request::_(we_base_request::STRING, 'sort');
		if($sortField !== false){
			$sort = ($sortField == g_l('modules_customer', '[no_sort]') ? 0 : 1);
		} elseif($this->View->settings->getSettings("default_sort_view") != g_l('modules_customer', '[no_sort]')){
			$sort = 1;
			$sortField = $this->View->settings->getSettings('default_sort_view');
		} else {
			$sort = 0;
		}

		$offset = we_base_request::_(we_base_request::INT, 'offset', 0);

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"cmd" => "no_cmd"));

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array(), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_element::jsElement(
							(we_base_request::_(we_base_request::STRING, 'error') ?
								we_message_reporting::getShowMessageCall(g_l('modules_customer', '[error_download_failed]'), we_message_reporting::WE_MESSAGE_ERROR) : '') .
							$this->Tree->getJSLoadTree($pid, we_customer_tree::getItems($pid, $offset, $this->Tree->default_segment, ($sort ? $sortField : ''))))
					)
				)
		);
	}

	function getHTMLSearch(){//TODO: this is popup search editor: make separate frameset for popups!
		$colspan = 4;

		$mode = we_base_request::_(we_base_request::INT, 'mode', 0);

		$hiddens = we_html_element::htmlHiddens(array(
				'pnt' => 'search',
				'cmd' => 'search',
				'search' => 1,
				'mode' => $mode));

		$search_but = we_html_button::create_button(we_html_button::SEARCH, "javascript:we_cmd('search')");

		$search = new we_html_table(array('class' => 'default', 'width' => 550, 'height' => 50), 4, 3);
		$search->setRow(0, array('style' => 'vertical-align:top'));
		$search->setCol(0, 0, array('class' => 'defaultfont', 'colspan' => 3, 'style' => 'padding-bottom: 3px;'), g_l('modules_customer', '[search_for]'));

		$select = new we_html_select(array('name' => 'search_result', 'style' => 'width:550px;', 'onDblClick' => "opener.top.content.we_cmd('customer_edit',document.we_form.search_result.options[document.we_form.search_result.selectedIndex].value)", "size" => 20));

		$foundItems = 0;
		if($mode){
			we_customer_add::getHTMLSearch($this, $search, $select);
			$foundItems = 0;
		} else {
			$search->setCol(1, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlTextInput('keyword', 80, we_base_request::_(we_base_request::STRING, 'keyword', ''), '', 'onchange=""', 'text', '550px')
			);

			$sw = we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:we_cmd('switchToAdvance')");

			$search->setCol(3, 0, array('style' => 'text-align:right', 'colspan' => $colspan), we_html_element::htmlDiv(array('class' => 'defaultfont'), g_l('modules_customer', '[advanced_search]')) .
				$sw .
				$search_but
			);
			$hiddens.=we_html_element::htmlHidden('count', 1);

			$max_res = $this->View->settings->getMaxSearchResults();
			$result = array();
			if(($k = we_base_request::_(we_base_request::STRING, 'keyword')) && we_base_request::_(we_base_request::BOOL, 'search')){
				$result = $this->View->getSearchResults($k, $max_res);
			}

			$foundItems = count($result);

			foreach($result as $id => $text){
				$select->addOption($id, $text);
			}
		}

		$table = new we_html_table(array('width' => 550, 'height' => 50), 3, 1);
		$table->setCol(0, 0, array(), $search->getHtml());
		$table->setCol(1, 0, array('class' => 'defaultfont'), g_l('modules_customer', '[num_data_sets]') . ($foundItems ? ' (' . $foundItems . ')' : ''));
		$table->setCol(2, 0, array(), $select->getHtml());

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => ($mode ? '' : 'document.we_form.keyword.focus();')), we_html_element::jsScript(JS_DIR . 'utils/weDate.js') .
					we_html_tools::getCalendarFiles() .
					$this->View->getJSSearch() .
					we_html_element::jsElement(
						$this->jsOut_fieldTypesByName . "
var date_format_dateonly = '" . g_l('date', '[format][mysqlDate]') . "';
var fieldDate = new weDate(date_format_dateonly);
") .
					we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_functions.js') .
					we_html_element::htmlForm(array('name' => 'we_form'), $hiddens .
						we_html_tools::htmlDialogLayout(
							$table->getHtml(), g_l('modules_customer', '[search]'), we_html_button::position_yes_no_cancel(null, we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")), "100%", 30, 558
						)
					) .
					(we_base_request::_(we_base_request::BOOL, 'mode') ? we_html_element::jsElement("setTimeout(lookForDateFields, 1);") : '')
				)
		);
	}

	function getHTMLSettings(){
		if(we_base_request::_(we_base_request::STRING, "cmd") === "save_settings"){
			$this->View->processCommands();
			$closeflag = true;
		} else {
			$closeflag = false;
		}

		$default_sort_view_select = $this->View->getHTMLSortSelect();
		$default_sort_view_select->setAttributes(array("name" => "default_sort_view", "style", "width:200px;"));
		$default_sort_view_select->selectOption($this->View->settings->getSettings('default_sort_view'));

		$table = new we_html_table(array('class' => 'default', 'style' => 'margin-right:10px;'), 5, 3);
		$cur = 0;
		$table->setCol($cur, 0, array("class" => "defaultfont", 'style' => 'padding-right:30px;'), g_l('modules_customer', '[default_sort_view]') . ":&nbsp;");
		$table->setCol($cur, 2, array("class" => "defaultfont"), $default_sort_view_select->getHtml());

		$table->setCol( ++$cur, 0, array("class" => "defaultfont", 'style' => 'padding-right:30px;'), g_l('modules_customer', '[start_year]') . ":&nbsp;");
		$table->setCol($cur, 2, array("class" => "defaultfont"), we_html_tools::htmlTextInput("start_year", 32, $this->View->settings->getSettings('start_year'), ''));

		$table->setCol( ++$cur, 0, array("class" => "defaultfont", 'style' => 'padding-right:30px;'), g_l('modules_customer', '[treetext_format]') . ":&nbsp;");
		$table->setCol($cur, 2, array("class" => "defaultfont"), we_html_tools::htmlTextInput("treetext_format", 32, $this->View->settings->getSettings('treetext_format'), ''));


		$default_order = new we_html_select(array('name' => 'default_order', 'style' => 'width:250px;', 'class' => 'weSelect'));
		$default_order->addOption('', g_l('modules_customer', '[none]'));
		foreach($this->View->settings->OrderTable as $ord){
			$ordval = g_l('modules_customer', ($ord === 'ASC') ? '[ASC]' : '[DESC]');
			$default_order->addOption($ord, $ordval);
		}
		$default_order->selectOption($this->View->settings->getSettings('default_order'));

		$table->setCol( ++$cur, 0, array('class' => 'defaultfont', 'style' => 'padding-right:30px;'), g_l('modules_customer', '[default_order]') . ':&nbsp;');
		$table->setCol($cur, 2, array('class' => 'defaultfont'), $default_order->getHtml());

		$default_saveRegisteredUser_register = new we_html_select(array('name' => 'default_saveRegisteredUser_register', 'style' => 'width:250px;', 'class' => 'weSelect'));
		$default_saveRegisteredUser_register->addOption('false', 'false');
		$default_saveRegisteredUser_register->addOption('true', 'true');
		$default_saveRegisteredUser_register->selectOption($this->View->settings->getPref('default_saveRegisteredUser_register'));

		$table->setCol( ++$cur, 0, array('class' => 'defaultfont', 'style' => 'padding-right:30px;'), '&lt;we:saveRegisteredUser register=&quot;');
		$table->setCol($cur, 2, array('class' => 'defaultfont'), $default_saveRegisteredUser_register->getHtml() . '&quot;/>');

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_settings')");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_element::htmlForm(array("name" => "we_form"), we_html_tools::htmlDialogLayout(
						we_html_element::htmlHiddens(array(
							"pnt" => "settings",
							"cmd" => '')) .
						$table->getHtml(), g_l('modules_customer', '[settings]'), we_html_button::position_yes_no_cancel($save, $close)
					)
				)
				. ($closeflag ? we_html_element::jsElement('top.close();') : '')
		);

		return $this->getHTMLDocument($body, we_html_element::jsElement($this->View->getJSSettings()));
	}

}
