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
	var $jsOut_fieldTypesByName;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->treeDefaultWidth = 244;
		$this->module = 'customer';
		$this->showTreeHeader = true;
		$this->showTreeFooter = true;

		$this->Tree = new we_tree_customer($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_customer_view($frameset);
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
				return $this->getHTMLEditorFooter([
						we_html_button::SAVE => [['EDIT_CUSTOMER', 'NEW_CUSTOMER'], 'save_customer'],
						we_html_button::DELETE => [['DELETE_CUSTOMER'], 'delete_customer']
				]);
			case 'customer_admin':
				return $this->getHTMLCustomerAdmin();
			case 'branch_editor':
				return $this->getHTMLFieldEditor('branch', $mode);
			case 'field_editor':
				return $this->getHTMLFieldEditor('field', $mode);
			case 'sort_admin':
				return we_customer_add::getHTMLSortEditor($this);
			case 'settings':
				return $this->getHTMLSettings();
			case 'frameset':
				$this->View->customer->clearSessionVars();
				$this->View->settings->load(false);
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode() . we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_treeHeader.js'), ($sid = we_base_request::_(we_base_request::RAW, 'sid', false)) !== false ? '&sid=' . $sid : '');
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	//TODO: move editor-body relatetd stuff to weCustomerView! => note dependencies on jsOut_fieldTypesByName
	function getHTMLFieldsSelect($branch){
		$select = new we_html_select(['name' => 'branch']);

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

	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorHeader(0);
		}

		$tabs = new we_tabs();

		$branches_names = $this->View->customer->getBranchesNames();

		$tabs->addTab(we_base_constants::WE_ICON_PROPERTIES, false, "setTab('" . g_l('modules_customer', '[common]') . "');", ["id" => "common", 'title' => g_l('modules_customer', '[common]')]);
		$extraJS = 'var aTabs={' .
			"'" . g_l('modules_customer', '[common]') . "':'common',";
		$branchCount = 0;
		foreach($branches_names as $branch){
			$tabs->addTab('<i class="fa fa-lg fa-object-group"></i>', false, "setTab('" . $branch . "');", ['id' => 'branch_' . $branchCount, 'title' => $branch]);
			$extraJS .= "'" . $branch . "':'branch_" . $branchCount . "',";
			$branchCount++;
		}
		$tabs->addTab('<i class="fa fa-lg fa-object-ungroup"></i>', false, "setTab('" . g_l('modules_customer', '[other]') . "');", ['id' => 'other', 'title' => g_l('modules_customer', '[other]')]);
		$tabs->addTab('<i class="fa fa-lg fa-list"></i>', false, "setTab('" . g_l('modules_customer', '[all]') . "');", ['id' => 'all', 'title' => g_l('modules_customer', '[all]')]);
		$extraJS .= "'" . g_l('modules_customer', '[other]') . "':'other'," .
			"'" . g_l('modules_customer', '[all]') . "':'all',";
//((top.content.activ_tab=="' . g_l('modules_customer','[other]') . '") )

		if(defined('SHOP_TABLE')){
			$tabs->addTab('<i class="fa fa-lg fa-shopping-cart"></i>', false, "setTab('" . g_l('modules_customer', '[orderTab]') . "');", ['id' => 'orderTab', 'title' => g_l('modules_customer', '[orderTab]')]);
			$extraJS .= "'" . g_l('modules_customer', '[orderTab]') . "':'orderTab',";
		}
		if(defined('OBJECT_FILES_TABLE')){
			$tabs->addTab('<i class="fa fa-lg fa-file-o"></i>', false, "setTab('" . g_l('modules_customer', '[objectTab]') . "');", ['id' => 'objectTab', 'title' => g_l('modules_customer', '[objectTab]')]);
			$extraJS .= "'" . g_l('modules_customer', '[objectTab]') . "':'objectTab',";
		}
		$tabs->addTab('<i class="fa fa-lg fa-file"></i>', false, "setTab('" . g_l('modules_customer', '[documentTab]') . "');", ['id' => 'documentTab', 'title' => g_l('modules_customer', '[documentTab]')]);
		$extraJS .= "'" . g_l('modules_customer', '[documentTab]') . "':'documentTab'"
			. '};';

		/*
		  $table = new we_html_table(array("width" => '100%', 'class' => 'default'), 3, 1);
		  $table->setCol(1, 0, array('style'=>'vertical-align:top", "class" => "small", 'style' => 'padding-left:15px;padding-right:10px;'), we_html_element::htmlB(
		  g_l('modules_customer', '[customer]') . ":&nbsp;" . $this->View->customer->Username
		  )
		  );
		 */
		$extraJS .= 'function loaded(){
	weTabs.setFrameSize()
	if(top.content.activ_tab){
		document.getElementById(aTabs[top.content.activ_tab]).className="tabActive";
	}else{
		document.getElementById("common").className="tabActive";
	}
}';

		$text = $this->View->customer->Username;

		//TODO: we have the following body in several modules!
		$body = we_html_element::htmlBody(['onresize' => 'weTabs.setFrameSize()', 'onload' => 'loaded();', 'id' => 'eHeaderBody',], we_html_element::htmlDiv(['id' => 'main'], we_html_element::htmlDiv(['id' => 'headrow'], we_html_element::htmlNobr(
							we_html_element::htmlB(str_replace(' ', '&nbsp;', g_l('modules_customer', '[customer]')) . ':&nbsp;') .
							we_html_element::htmlSpan(['id' => 'h_path', 'class' => 'header_small'], '<b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b>'
							)
						)
					) .
					$tabs->getHTML()
				)
		);

		return $this->getHTMLDocument($body, we_tabs::getHeader('
function setTab(tab) {
	top.content.activ_tab=tab;
	parent.edbody.we_cmd(\'switchPage\',tab);
}' .
					$extraJS));
	}

	protected function getHTMLEditorBody(){
		$hiddens = ['cmd' => 'customer_edit', 'pnt' => 'edbody', 'activ_sort' => 0];

		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}

		$branch = we_base_request::_(we_base_request::STRING, 'branch', g_l('modules_customer', '[common]'));

		return $this->getHTMLDocument(we_html_element::htmlBody(
					['class' => 'weEditorBody', 'onload' => 'loaded=1', 'onunload' => 'doUnload()'], we_html_element::htmlForm(
						['name' => 'we_form', 'autocomplete' => 'off'], $this->View->getCommonHiddens($hiddens) .
						$this->View->getHTMLProperties($branch))), $this->View->getJSProperty());
	}

	protected function getHTMLTreeHeader(){
		$select = $this->View->getHTMLSortSelect();
		$select->setAttributes(['onchange' => 'applySort();', 'style' => 'width:150px']);
		$select->selectOption($this->View->settings->getSettings('default_sort_view'));

		$table = $select->getHtml() . we_html_button::create_button(we_html_button::RELOAD, "javascript:applySort();") .
			we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('show_sort_admin')");

		return we_html_element::htmlForm(['name' => "we_form_treeheader"], we_html_element::htmlHiddens([
					"pnt" => "treeheader",
					"pid" => 0,
					"cmd" => "no_cmd"]) .
				$table
		);
	}

	protected function getHTMLTreeFooter(){
		return $this->getHTMLSearchTreeFooter();
	}

	function getHTMLCustomerAdmin(){
		$branch = we_base_request::_(we_base_request::STRING, "branch", g_l('modules_customer', '[other]'));
		$branch_select = we_base_request::_(we_base_request::STRING, "branch", g_l('modules_customer', '[other]'));

		$select = $this->View->getHTMLBranchSelect(false);
		$select->setAttributes(['name' => "branch_select", "class" => 'weSelect', 'onchange' => "selectBranch()", "style" => "width:150px;"]);
		$select->selectOption($branch_select);

		$fields = $this->getHTMLFieldsSelect($branch);
		$fields->setAttributes(['name' => "fields_select", "size" => 15, "onchange" => '', "style" => "width:350px;height:250px;"]);
		//$hiddens = rray("name" => "field", "value" => ''));

		$buttons_table = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('open_add_field')") .
			we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('open_edit_field')") .
			we_html_button::create_button(we_html_button::DELETE, "javascript:we_cmd('delete_field')") .
			we_html_button::create_button(we_html_button::DIRUP, "javascript:we_cmd('move_field_up')") .
			we_html_button::create_button(we_html_button::DIRDOWN, "javascript:we_cmd('move_field_down')") .
			we_html_element::htmlSpan(['class' => "defaultfont lowContrast"], g_l('modules_customer', '[sort_edit_fields_explain]')) .
			we_html_button::create_button('reset', "javascript:we_cmd('reset_edit_order')");

		$table = new we_html_table(['class' => 'default', "width" => 500], 4, 5);

		$table->setCol(0, 0, ['class' => "defaultfont lowContrast", 'style' => 'padding-right:10px;'], g_l('modules_customer', '[branch]'));
		$table->setCol(0, 2, ['class' => "defaultfont lowContrast"], g_l('modules_customer', '[branch_select]'));
		$table->setCol(1, 0, ['style' => 'padding-right:10px;'], we_html_tools::htmlTextInput("branch", 48, $branch, '', 'style="width:350px;"'));
		$table->setCol(1, 2, ['style' => 'padding-right:10px;'], $select->getHtml());
		$table->setCol(1, 4, ['style' => 'padding-bottom:10px;'], we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('open_edit_branch')"));


		$table->setCol(2, 0, ['class' => "defaultfont lowContrast", 'style' => 'vertical-align:top;'], g_l('modules_customer', '[fields]'));
		$table->setCol(3, 0, ['style' => 'vertical-align:top;padding-right:10px;'], $fields->getHtml());
		$table->setCol(3, 2, ['style' => 'vertical-align:top;'], $buttons_table);

		return $this->getHTMLDocument(
				we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => 'self.focus();', 'style' => 'overflow:hidden'], we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_admin.js') .
					we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens([
							"cmd" => "switchBranch",
							"pnt" => "customer_admin"]) .
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_customer', '[field_admin]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()"))
					)
				)
		);
	}

	function getHTMLFieldEditor($type, $mode){
		$field = we_base_request::_(we_base_request::STRING, "field", '');
		$branch = we_base_request::_(we_base_request::STRING, "branch", g_l('modules_customer', '[other]'));

		$hiddens = we_html_element::htmlHiddens([
				"pnt" => "field_editor",
				"cmd" => "no_cmd",
				"branch" => "$branch",
				"art" => "$mode",
				($type === "field" ? "field" : '') => "$field"]);

		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");

		switch($type){
			case "branch":
				$hiddens.=we_html_element::htmlHidden("pnt", "branch_editor");
				$edit = new we_html_table(["width" => 300], 1, 2);
				$edit->setCol(0, 0, ['style' => 'vertical-align:middle;', "class" => "defaultfont lowContrast"], g_l('modules_customer', '[field_name]'));
				$edit->setCol(0, 1, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], we_html_tools::htmlTextInput("name", 26, $branch, '', ''));

				$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_branch')");
				break;
			default:
				$hiddens.=we_html_element::htmlHidden("pnt", "field_editor");
				$field_props = $this->View->getFieldProperties($field);

				$types = new we_html_select(['name' => "field_type", "class" => "weSelect", "style" => "width:200px;", 'onchange' => 'setStatusEncryption(this.value);']);
				$types->addOptions(array_combine(array_keys($this->View->settings->field_types), array_keys($this->View->settings->field_types)));
				if(isset($field_props["type"])){
					$types->selectOption($field_props["type"]);
					$curType = $field_props["type"];
				} else {
					$curType = 'input';
				}

				$enc = new we_html_select(['name' => "field_encrypt", "class" => "weSelect", "style" => "width:200px;"]);
				$enc->addOptions([0 => g_l('global', '[no]'), 1 => g_l('global', '[yes]')]);

				if(!empty($field_props['encrypt'])){
					$enc->selectOption(1);
				}

				$edit = new we_html_table(["width" => 300], 5, 2);

				$edit->setCol(0, 0, ['style' => 'vertical-align:middle;', "class" => "defaultfont lowContrast"], g_l('modules_customer', '[branch]'));
				$edit->setCol(0, 1, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], $branch);

				$edit->setCol(1, 0, ['style' => 'vertical-align:middle;', "class" => "defaultfont lowContrast"], g_l('modules_customer', '[field_name]'));
				$edit->setCol(1, 1, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], we_html_tools::htmlTextInput("name", 26, (isset($field_props['name']) ? $field_props['name'] : ''), '', ''));

				$edit->setCol(2, 0, ['style' => 'vertical-align:middle;', "class" => "defaultfont lowContrast"], g_l('modules_customer', '[field_type]'));

				$edit->setCol(2, 1, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], $types->getHtml());

				$edit->setCol(3, 0, ['style' => 'vertical-align:middle;', "class" => "defaultfont lowContrast"], g_l('modules_customer', '[field_default]'));
				$edit->setCol(3, 1, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], we_html_tools::htmlTextInput("field_default", 26, (isset($field_props['default']) ? $field_props['default'] : ''), '', ''));

				$edit->setCol(4, 0, ['style' => 'vertical-align:middle;', "class" => "defaultfont lowContrast"], g_l('modules_customer', '[encryptField]'));
				$edit->setCol(4, 1, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], $enc->getHtml());

				$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_field')");
		}

		return
			$this->getHTMLDocument(
				we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => 'self.focus();setStatusEncryption(\'' . $curType . '\');'], we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_admin.js') .
					we_html_element::htmlForm(['name' => 'we_form'], $hiddens .
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

	function getHTMLBox($content, $headline = "", $width = 100, $height = 50, $w = 25, $vh = 0, $ident = 0, $space = 5, $headline_align = "left", $content_align = "left"){
		$table = new we_html_table(["width" => $width, "height" => $height, "class" => 'default', 'style' => 'margin-left:' . intval($ident) . 'px;margin-top:' . intval($vh) . 'px;margin-bottom:' . ($w && $headline ? $vh : 0) . 'px;'], 1, 2);

		$table->setCol(0, 0, ["style" => 'vertical-align:middle;text-align:' . $headline_align . ';padding-right:' . $space . 'px;', "class" => "defaultfont lowContrast"], str_replace(" ", "&nbsp;", $headline));
		$table->setCol(0, 1, ["style" => 'vertical-align:middle;text-align:' . $content_align], $content);
		return $table->getHtml();
	}

	protected function getHTMLCmd(){
		if(($p = we_base_request::_(we_base_request::RAW, 'pid')) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
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

		return $this->getHTMLDocument(
				we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens([
							'pnt' => 'cmd',
							'cmd' => 'no_cmd'])
					)
				), we_html_element::jsElement(
					(we_base_request::_(we_base_request::STRING, 'error') ?
						we_message_reporting::getShowMessageCall(g_l('modules_customer', '[error_download_failed]'), we_message_reporting::WE_MESSAGE_ERROR) : '') .
					$this->Tree->getJSLoadTree($pid, we_tree_customer::getItems($pid, $offset, $this->Tree->default_segment, ($sort ? $sortField : ''))))
		);
	}

	function getHTMLSearch(){//TODO: this is popup search editor: make separate frameset for popups!
		$colspan = 4;

		$mode = we_base_request::_(we_base_request::INT, 'mode', 0);

		$hiddens = we_html_element::htmlHiddens([
				'pnt' => 'search',
				'cmd' => 'search',
				'search' => 1,
				'mode' => $mode]);

		$search_but = we_html_button::create_button(we_html_button::SEARCH, "javascript:we_cmd('search')");

		$search = new we_html_table(['class' => 'default', 'width' => 550, 'height' => 50], 4, 3);
		$search->setRow(0, ['style' => 'vertical-align:top']);
		$search->setCol(0, 0, ['class' => 'defaultfont', 'colspan' => 3, 'style' => 'padding-bottom: 3px;'], g_l('modules_customer', '[search_for]'));

		$select = new we_html_select(['name' => 'search_result', 'style' => 'width:550px;', 'onDblClick' => "opener.top.content.we_cmd('customer_edit',document.we_form.search_result.options[document.we_form.search_result.selectedIndex].value)", "size" => 20]);

		if($mode){
			we_customer_add::getHTMLSearch($this, $search, $select);
			$foundItems = $GLOBALS['advSearchFoundItems'] ? : 0;
		} else {
			$search->setCol(1, 0, ['style' => 'padding-bottom:5px;'], we_html_tools::htmlTextInput('keyword', 80, we_base_request::_(we_base_request::STRING, 'keyword', ''), '', 'onchange=""', 'text', '550px')
			);

			$sw = we_html_button::create_button(we_html_button::DIRRIGHT, "javascript:we_cmd('switchToAdvance')");

			$search->setCol(3, 0, ['style' => 'text-align:right', 'colspan' => $colspan], we_html_element::htmlDiv(['class' => 'defaultfont'], g_l('modules_customer', '[advanced_search]')) .
				$sw .
				$search_but
			);
			$hiddens.=we_html_element::htmlHidden('count', 1);

			$max_res = $this->View->settings->getMaxSearchResults();
			$result = [];
			if(($k = we_base_request::_(we_base_request::STRING, 'keyword')) && we_base_request::_(we_base_request::BOOL, 'search')){
				$result = $this->View->getSearchResults($k, $max_res);
			}

			$foundItems = count($result);

			foreach($result as $id => $text){
				$select->addOption($id, $text);
			}
		}

		$table = new we_html_table(['width' => 550, 'height' => 50], 3, 1);
		$table->setColContent(0, 0, $search->getHtml());
		$table->setCol(1, 0, ['class' => 'defaultfont'], g_l('modules_customer', '[num_data_sets]') . ($foundItems ? ' (' . $foundItems . ')' : ''));
		$table->setColContent(2, 0, $select->getHtml());

		return $this->getHTMLDocument(
				we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => ($mode ? '' : 'document.we_form.keyword.focus();')], we_html_element::jsScript(JS_DIR . 'utils/weDate.js') .
					we_html_tools::getCalendarFiles() .
					$this->View->getJSSearch() .
					we_html_element::jsElement(
						$this->jsOut_fieldTypesByName . "
var date_format_dateonly = '" . g_l('date', '[format][mysqlDate]') . "';
var fieldDate = new weDate(date_format_dateonly);
") .
					we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_functions.js') .
					we_html_element::htmlForm(['name' => 'we_form', 'onsubmit' => "we_cmd('search');return false;"], $hiddens .
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
		$default_sort_view_select->setAttributes(['name' => "default_sort_view", "style", "width:200px;"]);
		$default_sort_view_select->selectOption($this->View->settings->getSettings('default_sort_view'));

		$table = new we_html_table(['class' => 'default', 'style' => 'margin-right:10px;'], 5, 3);
		$cur = 0;
		$table->setCol($cur, 0, ['class' => 'defaultfont', 'style' => 'padding-right:30px;'], g_l('modules_customer', '[default_sort_view]') . ":&nbsp;");
		$table->setCol($cur, 2, ['class' => 'defaultfont'], $default_sort_view_select->getHtml());

		$table->setCol( ++$cur, 0, ['class' => 'defaultfont', 'style' => 'padding-right:30px;'], g_l('modules_customer', '[start_year]') . ":&nbsp;");
		$table->setCol($cur, 2, ['class' => 'defaultfont'], we_html_tools::htmlTextInput("start_year", 32, $this->View->settings->getSettings('start_year'), ''));

		$table->setCol( ++$cur, 0, ['class' => 'defaultfont', 'style' => 'padding-right:30px;'], g_l('modules_customer', '[treetext_format]') . ":&nbsp;");
		$table->setCol($cur, 2, ['class' => 'defaultfont'], we_html_tools::htmlTextInput("treetext_format", 32, $this->View->settings->getSettings('treetext_format'), ''));


		$default_order = new we_html_select(['name' => 'default_order', 'style' => 'width:250px;', 'class' => 'weSelect']);
		$default_order->addOption('', g_l('modules_customer', '[none]'));
		foreach($this->View->settings->OrderTable as $ord){
			$ordval = g_l('modules_customer', ($ord === 'ASC') ? '[ASC]' : '[DESC]');
			$default_order->addOption($ord, $ordval);
		}
		$default_order->selectOption($this->View->settings->getSettings('default_order'));

		$table->setCol( ++$cur, 0, ['class' => 'defaultfont', 'style' => 'padding-right:30px;'], g_l('modules_customer', '[default_order]') . ':&nbsp;');
		$table->setCol($cur, 2, ['class' => 'defaultfont'], $default_order->getHtml());

		$default_saveRegisteredUser_register = new we_html_select(['name' => 'default_saveRegisteredUser_register', 'style' => 'width:250px;', 'class' => 'weSelect']);
		$default_saveRegisteredUser_register->addOption('false', 'false');
		$default_saveRegisteredUser_register->addOption('true', 'true');
		$default_saveRegisteredUser_register->selectOption($this->View->settings->getPref('default_saveRegisteredUser_register'));

		$table->setCol( ++$cur, 0, ['class' => 'defaultfont', 'style' => 'padding-right:30px;'], '&lt;we:saveRegisteredUser register=&quot;');
		$table->setCol($cur, 2, ['class' => 'defaultfont'], $default_saveRegisteredUser_register->getHtml() . '&quot;/>');

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_settings')");

		$body = we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => 'self.focus();'], we_html_element::htmlForm(['name' => 'we_form'], we_html_tools::htmlDialogLayout(
						we_html_element::htmlHiddens([ "pnt" => "settings", "cmd" => '']) .
						$table->getHtml(), g_l('modules_customer', '[settings]'), we_html_button::position_yes_no_cancel($save, $close)
					)
				)
				. ($closeflag ? we_html_element::jsElement('top.close();') : '')
		);

		return $this->getHTMLDocument($body, we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/settings.js'));
	}

}
