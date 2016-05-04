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
class we_voting_frames extends we_modules_frame{
	var $_space_size = 150;
	var $_width_size = 535;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = "voting";
		$this->Tree = new we_voting_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_voting_view($frameset);
	}

	function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case "export_csv":
				return $this->getHTMLExportCsvMessage();
			case "exportGroup_csv":
				return $this->getHTMLExportGroupCsvMessage();
			case "reset_ipdata":
				return $this->getHTMLResetIPData();
			/* case "reset_logdata":
			  return $this->getHTMLResetLogData(); */
			case "show_log":
				return ($this->View->voting->LogDB ?
						$this->getHTMLShowLogNew() :
						$this->getHTMLShowLogOld()
					);
			case "delete_log":
				return $this->getHTMLDeleteLog();
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset($extraHead = '', $extraUrlParams = ''){
		$this->View->voting->clearSessionVars();
		return parent::getHTMLFrameset($this->Tree->getJSTreeCode());
	}

	protected function getHTMLEditorHeader(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array('class' => 'home'), ''), we_html_element::cssLink(CSS_DIR . 'tools_home.css'));
		}

		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab(g_l('modules_voting', '[property]'), '((' . $this->topFrame . '.activ_tab==1) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('1');", array("id" => "tab_1")));
		if(!$this->View->voting->IsFolder){
			$we_tabs->addTab(new we_tab(g_l('modules_voting', '[inquiry]'), '((' . $this->topFrame . '.activ_tab==2) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('2');", array("id" => "tab_2")));
			$we_tabs->addTab(new we_tab(g_l('modules_voting', '[options]'), '((' . $this->topFrame . '.activ_tab==3) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('3');", array("id" => "tab_3")));

			if($this->View->voting->ID){
				$we_tabs->addTab(new we_tab(g_l('modules_voting', '[result]'), '((' . $this->topFrame . '.activ_tab==4) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('4');", array("id" => "tab_4")));
			}
		}

		$tabsHead = we_tabs::getHeader('
				function setTab(tab) {
					parent.edbody.toggle("tab"+' . $this->topFrame . '.activ_tab);
					parent.edbody.toggle("tab"+tab);
					' . $this->topFrame . '.activ_tab=tab;
					self.focus();
				}
				' . ($this->View->voting->ID ? '' : $this->topFrame . '.activ_tab=1;')
		);


		/* $table = new we_html_table(array("width" => '100%', 'class' => 'default'), 3, 1);

		  $table->setCol(0, 0, array('style'=>'vertical-align:top", "class" => "small"),
		  we_html_element::htmlB(
		  g_l('modules_voting', ($this->View->voting->IsFolder ? '[group]' : '[voting]')) . ':&nbsp;' . $this->View->voting->Text .

		  )
		  ); */

		$extraJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(array("onresize" => "weTabs.setFrameSize()", "onload" => "weTabs.setFrameSize()", "id" => "eHeaderBody"), '<div id="main"><div id="headrow"><b>' . str_replace(" ", "&nbsp;", g_l('modules_voting', ($this->View->voting->IsFolder ? '[group]' : '[voting]'))) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $this->View->voting->Path) . '</b></span></div>' .
				$we_tabs->getHTML() .
				'</div>' . we_html_element::jsElement($extraJS)
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->View->getHomeScreen();
		}

		$hiddens = array('cmd' => 'voting_edit', 'pnt' => 'edbody', 'vernr' => we_base_request::_(we_base_request::INT, 'vernr', 0));

		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onload" => "loaded=1;setMultiEdits();", "onunload" => "doUnload()"), we_html_element::htmlForm(array("name" => "we_form", "onsubmit" => "return false"), $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties()));

		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	protected function getHTMLEditorFooter($btn_cmd = '', $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		return $this->getHTMLDocument(
				we_html_element::jsElement('
					function we_save() {
						top.content.we_cmd("save_voting");
					}') .
				we_html_element::htmlBody(array("id" => "footerBody"), we_html_element::htmlForm(array(), we_html_button::create_button(we_html_button::SAVE, "javascript:we_save()", true, 100, 22, '', '', (!permissionhandler::hasPerm('NEW_VOTING') && !permissionhandler::hasPerm('EDIT_VOTING'))))
				)
		);
	}

	function getPercent($total, $value, $precision = 0){
		$result = ($total ? round(($value * 100) / $total, $precision) : 0);
		return we_base_util::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']));
	}

	function getHTMLVariant(){
		$prefix = '';
		$del_but = addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:top . content . setHot(); #####placeHolder#####'));
		$del_but1 = addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:top.content.setHot();if(answers_edit.itemCount>answers_edit.minCount) #####placeHolder#####; else callAnswerLimit();'));

		$_Imagecmd = addslashes("we_cmd('we_selector_document',document.we_form.elements['" . $prefix . "UrlID'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'" . $prefix . "UrlID\\'].value','document.we_form.elements[\\'" . $prefix . "UrlIDPath\\'].value','opener." . $this->topFrame . ".mark()','',0,'" . we_base_ContentTypes::WEDOCUMENT . "'," .
			(permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') ? 0 : 1) . ')');

		$sel_but = addslashes(we_html_button::create_button(we_html_button::TRASH, 'javascript:top.content.setHot();'));

		$js = we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js') .
			we_html_element::jsScript(JS_DIR . 'utils/multi_editMulti.js');

		$variant_js = ' function callAnswerLimit() {
				' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[answer_limit]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}

			function setMultiEdits() {';

		if($this->View->voting->IsFolder == 0){
			$variant_js .=
				'question_edit = new multi_edit("question",document.we_form,1,"",' . ($this->_width_size) . ',true);
				answers_edit = new multi_editMulti("answers",document.we_form,0,"' . $del_but1 . '",' . ($this->_width_size - 32) . ',true);
				answers_edit.SetImageIDText("' . g_l('modules_voting', '[imageID_text]') . '");
				answers_edit.SetMediaIDText("' . g_l('modules_voting', '[mediaID_text]') . '");
				answers_edit.SetSuccessorIDText("' . g_l('modules_voting', '[successorID_text]') . '");';

			for($j = 0; $j < count($this->View->voting->QASet[0]['answers']); $j++){
				$variant_js .= 'answers_edit.addItem("2");';
			}

			foreach($this->View->voting->QASet as $variant => $value){
				$variant_js .=
					'question_edit.addVariant();
				   answers_edit.addVariant();';
				foreach($value as $k => $v){
					switch($k){
						case 'question':
							$variant_js .= 'question_edit.setItem("' . $variant . '",0,"' . $v . '");';
							break;
						case 'answers':
							foreach($v as $akey => $aval){
								if((isset($this->View->voting->QASetAdditions[$variant]) && isset($this->View->voting->QASetAdditions[$variant]['imageID'][$akey]))){
									$aval2 = $this->View->voting->QASetAdditions[$variant]['imageID'][$akey];
									$aval3 = $this->View->voting->QASetAdditions[$variant]['mediaID'][$akey];
									$aval4 = $this->View->voting->QASetAdditions[$variant]['successorID'][$akey];
								} else {
									$aval2 = $aval3 = $aval4 = '';
								}
								$variant_js .=
									'answers_edit.setItem("' . $variant . '","' . $akey . '","' . $aval . '");
								answers_edit.setItemImageID("' . $variant . '","' . $akey . '","' . $aval2 . '");
								answers_edit.setItemMediaID("' . $variant . '","' . $akey . '","' . $aval3 . '");
								answers_edit.setItemSuccessorID("' . $variant . '","' . $akey . '","' . $aval4 . '");';
							}
							break;
					}
				}
			}

			$variant_js .= '
answers_edit.delRelatedItems=true;
question_edit.showVariant(0);
answers_edit.showVariant(0);
question_edit.showVariant(' . we_base_request::_(we_base_request::INT, 'vernr', 0) . ');
answers_edit.showVariant(' . we_base_request::_(we_base_request::INT, 'vernr', 0) . ');
answers_edit.SetMinCount(' . ($this->View->voting->AllowFreeText ? 1 : 2) . ');
answers_edit.' . ($this->View->voting->AllowImages ? 'show' : 'hide') . 'Images();
answers_edit.' . ($this->View->voting->AllowMedia ? 'show' : 'hide') . 'Media();
answers_edit.' . ($this->View->voting->AllowSuccessors ? 'show' : 'hide') . 'Successors();';
		}


		$variant_js .= ' owners_label = new multi_edit("owners",document.we_form,0,"' . $del_but . '",' . ($this->_width_size - 10) . ',false);
			owners_label.addVariant();';
		if(is_array($this->View->voting->Owners)){
			$this->View->voting->Owners = array_filter($this->View->voting->Owners);
			foreach($this->View->voting->Owners as $owner){
				$foo = f('SELECT IsFolder FROM ' . USER_TABLE . ' WHERE ID=' . intval($owner), '', $this->db);

				$variant_js .=
					'owners_label.addItem();
					owners_label.setItem(0,(owners_label.itemCount-1),WE().util.getTreeIcon("' . ($foo ? 'folder' : 'we/user') . '")+" ' . id_to_path($owner, USER_TABLE) . '");';
			}
		}
		$variant_js .=
			' owners_label.showVariant(0);
			iptable_label = new multi_edit("iptable",document.we_form,0,"' . $del_but . '",' . ($this->_width_size - 10) . ',false);
			iptable_label.addVariant();';

		if(is_array($this->View->voting->BlackList)){
			foreach($this->View->voting->BlackList as $ip){

				$variant_js .=
					'top.content.setHot();
					iptable_label.addItem();
					iptable_label.setItem(0,(iptable_label.itemCount-1),"' . $ip . '");';
			}
		}
		$variant_js .=
			'iptable_label.showVariant(0);
	}';

		return $js . we_html_element::jsElement($variant_js);
	}

	function getHTMLTab1(){
		$yuiSuggest = & weSuggest::getInstance();
		$table = new we_html_table(array('id' => 'ownersTable', 'style' => 'display: ' . ($this->View->voting->RestrictOwners ? 'block' : 'none') . ';'), 3, 2);
		$table->setCol(0, 1, array('colspan' => 2, 'class' => 'defaultfont'), g_l('modules_voting', '[limit_access_text]'));
		$table->setColContent(1, 1, we_html_element::htmlDiv(array('id' => 'owners', 'class' => 'multichooser', 'style' => 'width: ' . ($this->_width_size - 10) . 'px; height: 60px; border: #AAAAAA solid 1px;')));
		$idname = 'owner_id';
		$textname = 'owner_text';
		$cmd1 = "document.forms[0].elements['" . $idname . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc5 = we_base_request::encCmd("fillIDs();opener.we_cmd('users_add_owner',top.allPaths,top.allIsFolder);");
		$table->setCol(2, 0, array('colspan' => 2, 'style' => 'text-align:right'), we_html_element::htmlHiddens(array(
				$idname => '',
				$textname => '')) .
			we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot(); we_cmd('we_users_selector','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "',''," . $cmd1 . ",'" . $wecmdenc5 . "','','',1);")
		);

		$parts = array(
			array(
				'headline' => g_l('modules_voting', '[property]'),
				'html' => we_html_element::htmlHiddens(array(
					'owners_name' => '',
					'owners_count' => 0,
					'newone' => ($this->View->voting->ID == 0 ? 1 : 0))) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->View->voting->Text, '', 'style="width: ' . $this->_width_size . 'px;" id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.weTabs.setTitlePath(this.value)"'), g_l('modules_voting', '[headline_name]')) .
				we_html_element::htmlBr() .
				$this->getHTMLDirChooser() .
				weSuggest::getYuiFiles() . $yuiSuggest->getYuiJs() .
				we_html_element::htmlBr() .
				(!$this->View->voting->IsFolder ? we_html_tools::htmlFormElementTable(we_html_tools::getDateInput('PublishDate%s', $this->View->voting->PublishDate, false, '', 'top.content.setHot();'), g_l('modules_voting', '[headline_publish_date]')) : ''),
				'space' => $this->_space_size,
				'noline' => 1),
			array(
				'headline' => '',
				'html' => we_html_forms::checkboxWithHidden($this->View->voting->RestrictOwners ? true : false, 'RestrictOwners', g_l('modules_voting', '[limit_access]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'ownersTable\')'),
				'space' => $this->_space_size,
				'noline' => 1
			),
			array(
				'headline' => '',
				'html' => $table->getHtml(),
				'space' => $this->_space_size
			)
		);

		if($this->View->voting->IsFolder){
			$parts[] = array(
				'headline' => g_l('modules_voting', '[control]'),
				'html' => we_html_button::formatButtons(we_html_button::create_button('logbook', "javascript:we_cmd('show_log')") . we_html_button::create_button(we_html_button::DELETE, "javascript:we_cmd('delete_log')")),
				'space' => $this->_space_size,
				'noline' => 1
			);


			$ok = we_html_button::create_button('export', "javascript:we_cmd('exportGroup_csv')");

			$export_box = new we_html_table(array('class' => 'default', 'style' => 'margin-top:10px;'), 12, 1);

			$export_box->setCol(1, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlFormElementTable($this->formFileChooser($this->_width_size - 130, 'csv_dir', '/', '', we_base_ContentTypes::FOLDER), g_l('export', '[dir]')));

			$lineend = new we_html_select(array('name' => 'csv_lineend', 'size' => 1, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
			$lineend->addOption('windows', g_l('export', '[windows]'));
			$lineend->addOption('unix', g_l('export', '[unix]'));
			$lineend->addOption('mac', g_l('export', '[mac]'));

			$_charsetHandler = new we_base_charsetHandler();
			$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
			$_importCharset = we_html_tools::htmlTextInput('the_charset', 8, '', 255, "", "text", 200);
			$_importCharsetChooser = we_html_tools::htmlSelect("ImportCharsetSelect", $_charsets, 1, '', false, array("onchange" => "document.forms[0].elements.the_charset.value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"), "value", 325, "defaultfont", false);
			$import_Charset = '<table class="default"><tr><td>' . $_importCharset . '</td><td>' . $_importCharsetChooser . '</td></tr></table>';



			$delimiter = new we_html_select(array('name' => 'csv_delimiter', 'size' => 1, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
			$delimiter->addOption(';', g_l('export', '[semicolon]'));
			$delimiter->addOption(',', g_l('export', '[comma]'));
			$delimiter->addOption(':', g_l('export', '[colon]'));
			$delimiter->addOption('\t', g_l('export', '[tab]'));
			$delimiter->addOption(' ', g_l('export', '[space]'));

			$enclose = new we_html_select(array('name' => 'csv_enclose', 'size' => 1, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
			$enclose->addOption(0, g_l('export', '[double_quote]'));
			$enclose->addOption(1, g_l('export', '[single_quote]'));

			$export_box->setCol(3, 0, array("class" => "defaultfont", 'style' => 'padding-bottom:5px;'), we_html_tools::htmlFormElementTable($lineend->getHtml(), g_l('export', '[csv_lineend]')));
			$export_box->setCol(5, 0, array("class" => "defaultfont", 'style' => 'padding-bottom:5px;'), we_html_tools::htmlFormElementTable($import_Charset, g_l('modules_voting', '[csv_charset]')));
			$export_box->setCol(7, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlFormElementTable($delimiter->getHtml(), g_l('export', '[csv_delimiter]')));
			$export_box->setCol(9, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlFormElementTable($enclose->getHtml(), g_l('export', '[csv_enclose]')));
			$export_box->setCol(11, 0, array(), $ok);

			$parts[] = array(
				"headline" => g_l('modules_voting', '[export]'),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[export_txt]'), we_html_tools::TYPE_INFO, $this->_width_size) .
				$export_box->getHtml(),
				'space' => $this->_space_size
			);

			return $parts;
		}

		$activeTime = new we_html_select(array('name' => 'ActiveTime', 'class' => 'weSelect', 'size' => 1, 'style' => 'width:200', 'onchange' => 'top.content.setHot(); if(this.value!=0) setVisible(\'valid\',true); else setVisible(\'valid\',false);'));
		$activeTime->addOption((0), g_l('modules_voting', '[always]'));
		$activeTime->addOption((1), g_l('modules_voting', '[until]'));
		$activeTime->selectOption($this->View->voting->ActiveTime);

		$table = new we_html_table(array(), 4, 2);
		$table->setCol(0, 0, array('colspan' => 2), we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[valid_txt]'), we_html_tools::TYPE_INFO, $this->_width_size, false, 133));
		$table->setCol(1, 0, array('colspan' => 2), we_html_forms::checkboxWithHidden($this->View->voting->Active ? true : false, 'Active', g_l('modules_voting', '[active_till]'), false, 'defaultfont', 'toggle(\'activetime\');if(!this.checked) setVisible(\'valid\',false); else if(document.we_form.ActiveTime.value==1) setVisible(\'valid\',true); else setVisible(\'valid\',false);'));

		$table->setColContent(2, 1, we_html_element::htmlDiv(array('id' => 'activetime', 'style' => 'display: ' . ($this->View->voting->Active ? 'block' : 'none') . ';'), $activeTime->getHtml()
			)
		);
		$table->setColContent(3, 1, we_html_element::htmlDiv(array('id' => 'valid', 'style' => 'display: ' . ($this->View->voting->Active && $this->View->voting->ActiveTime ? 'block' : 'none') . ';'), we_html_tools::htmlFormElementTable(we_html_tools::getDateInput('Valid%s', $this->View->voting->Valid, false, '', 'top.content.setHot();'), "")
			)
		);

		$parts[] = array(
			'headline' => g_l('modules_voting', '[valid]'),
			'html' => $table->getHtml(),
			'space' => $this->_space_size,
			'noline' => 1
		);


		return $parts;
	}

	function getHTMLTab2(){
		$successor_box = new we_html_table(array('class' => 'default', 'style' => 'margin-top:10px;'), 2, 1);
		$successor_box->setCol(1, 0, array(), we_html_tools::htmlFormElementTable($this->formFileChooser($this->_width_size - 130, 'Successor', '/', '', ''), g_l('modules_voting', '[voting-successor]')));


		$displaySuccessor = ($this->View->voting->AllowSuccessor ? 'block' : 'none');

		$parts = array(
			array(
				'headline' => g_l('modules_voting', '[headline_datatype]'),
				'html' =>
				we_html_forms::checkboxWithHidden($this->View->voting->IsRequired ? true : false, 'IsRequired', g_l('modules_voting', '[IsRequired]'), false, 'defaultfont', 'top.content.setHot();') .
				we_html_forms::checkboxWithHidden($this->View->voting->AllowFreeText ? true : false, 'AllowFreeText', g_l('modules_voting', '[AllowFreeText]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleMinCount();') .
				we_html_forms::checkboxWithHidden($this->View->voting->AllowImages ? true : false, 'AllowImages', g_l('modules_voting', '[AllowImages]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleImages();') .
				we_html_forms::checkboxWithHidden($this->View->voting->AllowMedia ? true : false, 'AllowMedia', g_l('modules_voting', '[AllowMedia]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleMedia();') .
				we_html_forms::checkboxWithHidden($this->View->voting->AllowSuccessor ? true : false, 'AllowSuccessor', g_l('modules_voting', '[AllowSuccessor]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'Successor\')') .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Successor', '', $this->View->voting->Successor, '', 'style="width: ' . $this->_width_size . 'px;display:' . $displaySuccessor . '" id="Successor" onchange="top.content.setHot();" '), '') .
				we_html_forms::checkboxWithHidden($this->View->voting->AllowSuccessors ? true : false, 'AllowSuccessors', g_l('modules_voting', '[AllowSuccessors]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleSuccessors();')
				,
				'space' => $this->_space_size
			)
		);

		$select = new we_html_select(array('name' => 'selectVar', 'class' => 'weSelect', 'onchange' => 'top.content.setHot();question_edit.showVariant(this.value);answers_edit.showVariant(this.value);document.we_form.vernr.value=this.value;refreshTexts();', 'style' => 'width:' . ($this->_width_size - 64) . 'px;'));
		foreach(array_keys($this->View->voting->QASet) as $variant){
			$select->addOption($variant, g_l('modules_voting', '[variant]') . ' ' . ($variant + 1));
		}
		$select->selectOption(we_base_request::_(we_base_request::INT, 'vernr', 0));

		$table = new we_html_table(array('class' => 'default'), 1, 3);
		$table->setColContent(0, 0, $select->getHtml());
		$table->setColContent(0, 1, we_html_button::create_button(we_html_button::PLUS, "javascript:top.content.setHot();question_edit.addVariant();answers_edit.addVariant();question_edit.showVariant(question_edit.variantCount-1);answers_edit.showVariant(answers_edit.variantCount-1);document.we_form.selectVar.options[document.we_form.selectVar.options.length] = new Option('" . g_l('modules_voting', '[variant]') . " '+question_edit.variantCount,question_edit.variantCount-1,false,true);"));
		$table->setColContent(0, 2, we_html_button::create_button(we_html_button::TRASH, "javascript:top.content.setHot();if(question_edit.variantCount>1){ question_edit.deleteVariant(document.we_form.selectVar.selectedIndex);answers_edit.deleteVariant(document.we_form.selectVar.selectedIndex);document.we_form.selectVar.options.length--;document.we_form.selectVar.selectedIndex=question_edit.currentVariant;refreshTexts();} else {" . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[variant_limit]'), we_message_reporting::WE_MESSAGE_ERROR) . "}"));
		$table->setColAttributes(0, 1, array("style" => "padding:0 5px;"));
		$selectCode = $table->getHtml();

		$table = new we_html_table(array('class' => 'default'), 5, 1);

		$table->setColContent(0, 0, $selectCode);
		$table->setCol(2, 0, array('padding-top:10px;'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('id' => 'question')), g_l('modules_voting', '[inquiry_question]')));
		$table->setCol(4, 0, array('padding-top:10px;'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('id' => 'answers')), g_l('modules_voting', '[inquiry_answers]')));

		$parts[] = array(
			'headline' => g_l('modules_voting', '[headline_data]'),
			'html' => we_html_element::htmlHiddens(array(
				'question_name' => '',
				'variant_count' => 0,
				'answers_name' => '',
				'item_count' => 0,
				'iptable_name' => '',
				'iptable_count' => 0)) .
			$table->getHtml() .
			we_html_button::create_button(we_html_button::PLUS, "javascript:top.content.setHot();answers_edit.addItem()"),
			'space' => $this->_space_size
		);

		return $parts;
	}

	function getHTMLTab3(){
		$parts = array();


		$selectTime = new we_html_select(array('name' => 'RevoteTime', 'class' => 'weSelect', 'size' => 1, 'style' => 'width:200', 'onchange' => 'top.content.setHot(); if(this.value==0) setVisible(\'method_table\',false); else setVisible(\'method_table\',true);'));
		$selectTime->addOption((-1), g_l('modules_voting', '[never]'));
		$selectTime->addOption((86400), g_l('modules_voting', '[one_day]'));
		$selectTime->addOption((3600), g_l('modules_voting', '[one_hour]'));
		$selectTime->addOption((1800), g_l('modules_voting', '[thirthty_minutes]'));
		$selectTime->addOption((900), g_l('modules_voting', '[feethteen_minutes]'));
		$selectTime->addOption((0), g_l('modules_voting', '[always]'));
		$selectTime->selectOption($this->View->voting->RevoteTime);

		$table = new we_html_table(array('id' => 'method_table', 'style' => 'display: ' . ($this->View->voting->RevoteTime == 0 ? 'none' : 'block')), 10, 2);
		$table->setCol(0, 0, array('colspan' => 2), we_html_tools::htmlAlertAttentionBox(
				we_html_element::htmlB(g_l('modules_voting', '[cookie_method]')) . we_html_element::htmlBr() .
				g_l('modules_voting', '[cookie_method_help]') .
				we_html_element::htmlBr() . we_html_element::htmlB(g_l('modules_voting', '[ip_method]')) . we_html_element::htmlBr() .
				g_l('modules_voting', '[ip_method_help]'), we_html_tools::TYPE_INFO, ($this->_width_size - 3), false, 100
			)
		);


		$table->setCol(2, 0, array('colspan' => 2), we_html_forms::radiobutton(1, ($this->View->voting->RevoteControl == 1 ? true : false), 'RevoteControl', g_l('modules_voting', '[cookie_method]'), true, "defaultfont", "top.content.setHot();"));
		$table->setCol(3, 0, array('style' => 'padding-left:10px;'), we_html_forms::checkboxWithHidden($this->View->voting->FallbackIp ? true : false, 'FallbackIp', g_l('modules_voting', '[fallback]'), false, "defaultfont", "top.content.setHot();"));
		$table->setCol(5, 0, array('colspan' => 2, 'style' => 'padding-top:10px;'), we_html_forms::radiobutton(0, ($this->View->voting->RevoteControl == 0 ? true : false), 'RevoteControl', g_l('modules_voting', '[ip_method]'), true, "defaultfont", "top.content.setHot();"));

		$datasize = f('SELECT (LENGTH(Revote)+LENGTH(RevoteUserAgent)) AS Size FROM ' . VOTING_TABLE, 'Size', $this->db);

		$table->setColContent(6, 1, we_html_forms::checkboxWithHidden($this->View->voting->UserAgent ? true : false, 'UserAgent', g_l('modules_voting', '[save_user_agent]'), false, "defaultfont", "top.content.setHot();"));

		$table->setCol(7, 1, array('id' => 'delete_ip_data', 'style' => 'display: ' . ($datasize > 0 ? 'block' : 'none')), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('modules_voting', '[delete_ipdata_text]'), we_html_element::htmlSpan(array('id' => 'ip_mem_size'), $datasize)), we_html_tools::TYPE_INFO, ($this->_width_size - 20), false, 100) .
			we_html_button::create_button(we_html_button::DELETE, 'javascript:we_cmd(\'reset_ipdata\')')
		);
		$table->setCol(9, 0, array('colspan' => 2, 'style' => 'padding-top:10px;'), we_html_forms::radiobutton(2, ($this->View->voting->RevoteControl == 2 ? true : false), 'RevoteControl', g_l('modules_voting', '[userid_method]'), true, "defaultfont", "top.content.setHot();"));


		$parts[] = array(
			'headline' => g_l('modules_voting', '[headline_revote]'),
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[time_after_voting_again_help]'), we_html_tools::TYPE_INFO, $this->_width_size, false, 100) .
			we_html_element::htmlBr() .
			we_html_tools::htmlFormElementTable($selectTime->getHtml(), g_l('modules_voting', '[time_after_voting_again]')) .
			we_html_element::htmlBr() .
			$table->getHtml(),
			'space' => $this->_space_size
		);

		$table = we_html_element::htmlDiv(array('id' => 'LogData', 'style' => 'display: ' . ($this->View->voting->Log ? 'block' : 'none') . ';'), we_html_button::formatButtons(we_html_button::create_button('logbook', 'javascript:we_cmd(\'show_log\')') . we_html_button::create_button(we_html_button::DELETE, 'javascript:we_cmd(\'delete_log\')'))
		);

		$parts[] = array(
			'headline' => g_l('modules_voting', '[control]'),
			'html' => we_html_forms::checkboxWithHidden($this->View->voting->Log ? true : false, 'Log', g_l('modules_voting', '[voting_log]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'LogData\')') .
			$table,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$parts[] = array(
			'headline' => '',
			'html' => we_html_forms::checkboxWithHidden($this->View->voting->RestrictIP ? true : false, 'RestrictIP', g_l('modules_voting', '[forbid_ip]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'RestrictIPDiv\')'),
			'space' => $this->_space_size,
			'noline' => 1
		);


		$table = new we_html_table(array('id' => 'RestrictIPDiv', 'style' => 'display: ' . ($this->View->voting->RestrictIP ? 'block' : 'none') . ';'), 2, 1);
		$table->setCol(0, 0, array('style' => 'padding-left:10px;'), we_html_element::htmlDiv(array('id' => 'iptable', 'class' => 'blockWrapper', 'style' => 'width: ' . ($this->_width_size - 10) . 'px; height: 60px; border: #AAAAAA solid 1px;padding: 5px;')));

		$table->setCol(1, 0, array('colspan' => 2, 'style' => 'text-align:right'), we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:top.content.setHot(); removeAll()") .
			we_html_button::create_button(we_html_button::ADD, "javascript:top.content.setHot(); newIp()")
		);


		$parts[] = array(
			'headline' => '',
			'html' => we_html_element::jsElement('
function removeAll(){
	for(var i=0;i<iptable_label.itemCount+1;i++){
		iptable_label.delItem(i);
	}
}

function newIp(){
	var ip = prompt("' . g_l('modules_voting', '[new_ip_add]') . '","");


	var re = new RegExp("[a-zA-Z|,]");
	var m = ip.match(re);
	if(m != null){
		' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_valid_ip]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		return;
	}

	var re = new RegExp("^(([0-2|\*]?[0-9|\*]{1,2}\.){3}[0-2|\*]?[0-9|\*]{1,2})");

	var m = ip.match(re);

	if(m != null){

		var p = ip.split(".");
		for (var i = 0; i < p.length; i++) {
				var t = p[i];
				t.replace("*","");
				if(parseInt(t)>255) {
					' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_valid_ip]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					return false;
				}
			}

		iptable_label.addItem();
		iptable_label.setItem(0,(iptable_label.itemCount-1),ip);
		iptable_label.showVariant(0);
	} else {
		' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_valid_ip]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}') . $table->getHtml(),
			'space' => $this->_space_size
		);

		return $parts;
	}

	function getHTMLTab4(){
		$parts = array();

		$total_score = array_sum($this->View->voting->Scores);

		$version = we_base_request::_(we_base_request::INT, 'vernr', 0);

		$table = new we_html_table(array('class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'), 1, 5);
		if(isset($this->View->voting->QASet[$version])){
			$table->setCol(0, 0, array('colspan' => 5, 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlSpan(array('id' => 'question_score'), oldHtmlspecialchars(stripslashes($this->View->voting->QASet[$version]['question'])))));
		}
		$i = 1;
		if(isset($this->View->voting->QASet[$version])){
			foreach($this->View->voting->QASet[$version]['answers'] as $key => $value){
				if(!isset($this->View->voting->Scores[$key])){
					$this->View->voting->Scores[$key] = 0;
				}

				$percent = we_voting_frames::getPercent($total_score, $this->View->voting->Scores[$key], 2);

				$pb = new we_progressBar($percent);
				$pb->setName('item' . $key);
				$pb->setStudWidth(10);
				$pb->setStudLen(150);

				$table->addRow();
				$table->setRow($key + 1, array("id" => "row_scores_$key"));
				$table->setCol($i, 0, array('style' => 'width: ' . ($this->_width_size - 150) . 'px'), we_html_element::htmlSpan(array('id' => 'answers_score_' . $key), oldHtmlspecialchars(stripslashes($value))));
				$table->setColContent($i, 1, $pb->getJSCode() . $pb->getHTML());
				$table->setColContent($i, 2, '&nbsp;');
				$table->setColContent($i, 3, we_html_tools::htmlTextInput('scores_' . $key, 4, $this->View->voting->Scores[$key], '', 'id="scores_' . $key . '" onKeyUp="var r=parseInt(this.value);if(isNaN(r)) this.value=' . $this->View->voting->Scores[$key] . '; else{ this.value=r;document.we_form.scores_changed.value=1;}refreshTotal();"'));
				$i++;
			}
		}
		$table->addRow();
		$table->setColContent($i, 0, we_html_element::htmlB(g_l('modules_voting', '[total_voting]') . ':') . we_html_element::htmlHidden("updateScores", false, 'updateScores'));
		$table->setCol($i, 3, array('colspan' => 3), we_html_element::htmlB(we_html_element::htmlSpan(array('id' => 'total'), $total_score)));

		$butt = we_html_button::create_button('reset_score', "javascript:top.content.setHot();resetScores();");

		$js = we_html_element::jsElement('
function resetScores(){
	if(confirm("' . g_l('modules_voting', '[result_delete_alert]') . '")) {
		for(var i=0;i<' . ($i - 1) . ';i++){
			document.we_form.elements["scores_"+i].value = 0;
		}
		document.we_form.scores_changed.value=1;
		refreshTotal();
	} else {}
}

function refreshTotal(){
	var total=0;
	for(var i=0;i<' . ($i - 1) . ';i++){
		total += parseInt(document.we_form.elements["scores_"+i].value);
	}

	var t = document.getElementById("total");
	t.innerHTML = total;

	for(var i=0;i<' . ($i - 1) . ';i++){
		percent = (total!=0?
			Math.round((parseInt(document.we_form.elements["scores_"+i].value)/total) * 100):
			0);
	}
}

function refreshTexts(){
	var t = document.getElementById("question_score");
	t.innerHTML = document.we_form[question_edit.name+"_item0"].value;
	for(i=0;i<answers_edit.itemCount;i++){
		var t = document.getElementById("answers_score_"+i);
		t.innerHTML = document.we_form[answers_edit.name+"_item"+i].value;
	}
}');

		$parts[] = array(
			"headline" => g_l('modules_voting', '[inquiry]'),
			"html" => $js .
			we_html_element::htmlHidden('scores_changed', 0) .
			$table->getHTML() .
			we_html_element::htmlBr() . $butt,
			'space' => $this->_space_size
		);


		$ok = we_html_button::create_button('export', "javascript:we_cmd('export_csv')");

		$export_box = new we_html_table(array('class' => 'default', 'style' => 'margin-top:10px;'), 10, 1);

		$export_box->setCol(1, 0, array('style' => 'padding-bottom:5px;'), we_html_tools::htmlFormElementTable($this->formFileChooser($this->_width_size - 130, 'csv_dir', '/', '', we_base_ContentTypes::FOLDER), g_l('export', '[dir]')));

		$lineend = new we_html_select(array('name' => 'csv_lineend', 'size' => 1, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
		$lineend->addOption('windows', g_l('export', '[windows]'));
		$lineend->addOption('unix', g_l('export', '[unix]'));
		$lineend->addOption('mac', g_l('export', '[mac]'));

		$delimiter = new we_html_select(array('name' => 'csv_delimiter', 'size' => 1, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
		$delimiter->addOption(';', g_l('export', '[semicolon]'));
		$delimiter->addOption(',', g_l('export', '[comma]'));
		$delimiter->addOption(':', g_l('export', '[colon]'));
		$delimiter->addOption('\t', g_l('export', '[tab]'));
		$delimiter->addOption(' ', g_l('export', '[space]'));

		$enclose = new we_html_select(array('name' => 'csv_enclose', 'size' => 1, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
		$enclose->addOption(0, g_l('export', '[double_quote]'));
		$enclose->addOption(1, g_l('export', '[single_quote]'));

		$export_box->setCol(3, 0, array("class" => "defaultfont"), we_html_tools::htmlFormElementTable($lineend->getHtml(), g_l('export', '[csv_lineend]')));
		$export_box->setCol(5, 0, array('padding-top:5px;'), we_html_tools::htmlFormElementTable($delimiter->getHtml(), g_l('export', '[csv_delimiter]')));
		$export_box->setCol(7, 0, array('padding-top:5px;'), we_html_tools::htmlFormElementTable($enclose->getHtml(), g_l('export', '[csv_enclose]')));
		$export_box->setCol(9, 0, array('padding-top:5px;'), $ok);



		$parts[] = array(
			"headline" => g_l('modules_voting', '[export]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[export_txt]'), we_html_tools::TYPE_INFO, $this->_width_size) .
			$export_box->getHtml(),
			'space' => $this->_space_size
		);

		return $parts;
	}

	function getHTMLProperties($preselect = ''){// TODO: move to weVotingView
		$t = we_base_request::_(we_base_request::INT, 'tabnr', 1);
		$tabNr = ($this->View->voting->IsFolder && $t != 1) ? 1 : $t;

		$out = we_html_element::jsElement('
var table = "' . FILE_TABLE . '";
function toggle(id){
	var elem = document.getElementById(id);
	if(elem.style.display == "none") elem.style.display = "block";
	else elem.style.display = "none";
}
function setVisible(id,visible){
	var elem = document.getElementById(id);
	if(visible==true) elem.style.display = "block";
	else elem.style.display = "none";
}');

		$out .= we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab1(), 30, '', -1, '', '', false, $preselect)) .
			(!$this->View->voting->IsFolder ?
				(
				we_html_element::htmlDiv(array('id' => 'tab2', 'style' => ($tabNr == 2 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect)) .
				we_html_element::htmlDiv(array('id' => 'tab3', 'style' => ($tabNr == 3 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect)) .
				we_html_element::htmlDiv(array('id' => 'tab4', 'style' => ($tabNr == 4 ? '' : 'display: none')), we_html_multiIconBox::getHTML('', $this->getHTMLTab4(), 30, '', -1, '', '', false, $preselect))
				) : '') .
			$this->getHTMLVariant();

		return $out;
	}

	function getHTMLDirChooser(){
		$path = id_to_path($this->View->voting->ParentID, VOTING_TABLE);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.ParentID.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.ParentPath.value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:top.content.setHot(); we_cmd('we_voting_dirSelector',document.we_form.elements.ParentID.value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','')");
		$width = 416;

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('PathGroup');
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput('ParentPath', $path, 'onchange=top.content.setHot();');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('ParentID', ($this->View->voting->ParentID ? : 0));
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setTable(VOTING_TABLE);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setLabel(g_l('modules_voting', '[group]'));

		return $yuiSuggest->getHTML();
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, "pid")) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		$offset = we_base_request::_(we_base_request::INT, "offset", 0);

		$rootjs = '';
		if(!$pid){
			$rootjs.=
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));';
		}

		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"cmd" => "no_cmd"));

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array(), we_html_element::htmlForm(
						array("name" => "we_form"), $hiddens . we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(!$pid, we_voting_tree::getItemsFromDB($pid, $offset, $this->Tree->default_segment)))
					)
				)
		);
	}

	private function getHTMLExportCsvMessage(){
		$link = we_base_request::_(we_base_request::FILE, "lnk");
		if($link === false){
			return;
		}

		$table = new we_html_table(array('class' => 'default withSpace'), 3, 1);

		$table->setCol(0, 0, array("class" => "defaultfont"), sprintf(g_l('modules_voting', '[csv_export]'), $link));
		$table->setCol(1, 0, array("class" => "defaultfont"), we_backup_wizard::getDownloadLinkText());
		$table->setCol(2, 0, array("class" => "defaultfont"), we_html_element::htmlA(array("href" => getServerUrl(true) . $link, 'download' => basename($link)), g_l('modules_voting', '[csv_download]')));

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_element::htmlForm(
					array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden("group", "") .
					we_html_tools::htmlDialogLayout(
						$table->getHtml(), g_l('modules_voting', '[csv_download]'), we_html_button::formatButtons($close), "100%", 30, 350
					)
				)
		);

		return $this->getHTMLDocument($body);
	}

	private function getHTMLExportGroupCsvMessage(){
		$link = we_base_request::_(we_base_request::FILE, "lnk");
		if($link === false){
			return;
		}
		$table = new we_html_table(array('class' => 'default withSpace'), 3, 1);
		$table->setCol(0, 0, array("class" => "defaultfont"), sprintf(g_l('modules_voting', '[csv_export]'), $link));
		$table->setCol(1, 0, array("class" => "defaultfont"), we_backup_wizard::getDownloadLinkText());
		$table->setCol(2, 0, array("class" => "defaultfont"), we_html_element::htmlA(array("href" => getServerUrl(true) . $link), g_l('modules_voting', '[csv_download]')));

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden("group", '') .
					we_html_tools::htmlDialogLayout(
						$table->getHtml(), g_l('modules_voting', '[csv_download]'), we_html_button::formatButtons($close), "100%", 30, 350
					)
				)
		);

		return $this->getHTMLDocument($body);
	}

	private function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, "", 'readonly onchange="top.content.setHot();"', "text", $width, 0), "", "left", "defaultfont", "", permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	private function getHTMLResetIPData(){
		$this->View->voting->resetIpData();

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_tools::htmlDialogLayout(
					we_html_element::htmlSpan(array('class' => 'defaultfont'), g_l('modules_voting', '[data_deleted_info]')), g_l('modules_voting', '[voting]'), we_html_button::formatButtons($close)
				)
		);
		return $this->getHTMLDocument($body);
	}

	private function getHTMLDeleteLog(){
		$this->View->voting->deleteLogData();

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_tools::htmlDialogLayout(
					we_html_element::htmlSpan(array('class' => 'defaultfont'), g_l('modules_voting', '[data_deleted_info]')), g_l('modules_voting', '[voting]'), we_html_button::formatButtons($close)
				)
		);
		return $this->getHTMLDocument($body);
	}

	private function getHTMLShowLogOld(){
		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$refresh = we_html_button::create_button(we_html_button::REFRESH, "javascript:location.reload();");

		$voting = new we_voting_voting();
		$voting->load($this->View->voting->ID);

		if(!is_array($voting->LogData)){
			$log = we_unserialize($voting->LogData);
		} else {
			$log = array();
		}

		$headline = array(
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[time]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[ip]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[user_agent]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[cookie]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[log_fallback]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[status]'))),
		);

		$content = array();

		$count = 15;
		$size = count($log);

		$nextprev = "";

		if($size > 0){
			$size--;
			$start = min(max(we_base_request::_(we_base_request::INT, 'start', $size), 0), $size);
			$back = min($start + $count, $size);
			$next = max($start - $count, -1);

			for($i = $start; $i > $next; $i--){
				if($i < 0){
					break;
				}
				$data = $log[$i];

				if($data['status'] != we_voting_voting::SUCCESS){
					switch($data['status']){
						case we_voting_voting::ERROR :
							$mess = g_l('modules_voting', '[log_error]');
							break;
						case we_voting_voting::ERROR_ACTIVE :
							$mess = g_l('modules_voting', '[log_error_active]');
							break;
						case we_voting_voting::ERROR_REVOTE :
							$mess = g_l('modules_voting', '[log_error_revote]');
							break;
						case we_voting_voting::ERROR_BLACKIP :
							$mess = g_l('modules_voting', '[log_error_blackip]');
							break;
						default:
							$mess = g_l('modules_voting', '[log_error]');
					}
					$mess = we_html_element::htmlSpan(array('style' => 'color: red;'), $mess);
				} else {
					$mess = g_l('modules_voting', '[log_success]');
				}

				$content[] = array(
					array('dat' => date(g_l('weEditorInfo', '[date_format]'), $data['time'])),
					array('dat' => $data['ip']),
					array('dat' => $data['agent']),
					array('dat' => g_l('modules_voting', $data['cookie'] ? '[enabled]' : '[disabled]')),
					array('dat' => g_l('global', $data['fallback'] ? '[yes]' : '[no]')),
					array('dat' => $mess),
				);
			}

			$nextprev = '<table style="margin-top: 10px;" class="default"><tr><td>' .
				($start < $size ?
					we_html_button::create_button(we_html_button::BACK, WEBEDITION_DIR . 'we_showMod.php?mod=voting&pnt=show_log&start=' . $back) : //bt_back
					we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true)
				) . '</td><td style="text-align:center;width:120px;" class="defaultfont"><b>' . ($size - $start + 1) . "&nbsp;-&nbsp;" .
				($size - $next) .
				"&nbsp;" . g_l('global', '[from]') . " " . ($size + 1) . '</b></td><td>' .
				($next > 0 ?
					we_html_button::create_button(we_html_button::NEXT, WEBEDITION_DIR . 'we_showMod.php?mod=voting&pnt=show_log&start=' . $next) : //bt_next
					we_html_button::create_button(we_html_button::NEXT, "", "", 100, 22, "", "", true)
				) .
				"</td></tr></table>";

			$parts = array(
				array(
					'headline' => '',
					'html' => we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . $nextprev,
					'noline' => 1
				)
			);
		} else {
			$parts = array(
				array(
					'headline' => '',
					'html' => we_html_element::htmlSpan(array('class' => 'middlefont lowContrast'), g_l('modules_voting', '[log_is_empty]')) .
					we_html_element::htmlBr() .
					we_html_element::htmlBr(),
					'noline' => 1
				)
			);
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_multiIconBox::getHTML("show_log_data", $parts, 30, we_html_button::position_yes_no_cancel($refresh, $close), -1, '', '', false, g_l('modules_voting', '[voting]'), "", 558)
		);
		return $this->getHTMLDocument($body);
	}

	private function getHTMLShowLogNew(){
		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$refresh = we_html_button::create_button(we_html_button::REFRESH, "javascript:location.reload();");

		$voting = new we_voting_voting();
		$voting->load($this->View->voting->ID);
		$log = $voting->loadDB($voting->ID);


		$headline = array(
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-session]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-id]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[time]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[ip]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[user_agent]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[cookie]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[log_fallback]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[status]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerID]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerText]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-successor]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-additionalfields]'))),
		);

		$content = array();

		$count = 15;
		$size = count($log);

		$nextprev = "";

		if($size > 0){
			$size--;
			$start = min(max(we_base_request::_(we_base_request::INT, 'start', $size), 0), $size);
			$back = min($start + $count, $size);
			$next = max($start - $count, -1);

			for($i = $start; $i > $next; $i--){
				if($i < 0){
					break;
				}
				$data = $log[$i];

				if($data['status'] != we_voting_voting::SUCCESS){
					switch($data['status']){
						case we_voting_voting::ERROR :
							$mess = g_l('modules_voting', '[log_error]');
							break;
						case we_voting_voting::ERROR_ACTIVE :
							$mess = g_l('modules_voting', '[log_error_active]');
							break;
						case we_voting_voting::ERROR_REVOTE :
							$mess = g_l('modules_voting', '[log_error_revote]');
							break;
						case we_voting_voting::ERROR_BLACKIP :
							$mess = g_l('modules_voting', '[log_error_blackip]');
							break;
						default:
							$mess = g_l('modules_voting', '[log_error]');
					}
					$mess = we_html_element::htmlSpan(array('style' => 'color: red;'), $mess);
				} else {
					$mess = g_l('modules_voting', '[log_success]');
				}

				$addData = we_unserialize($data['additionalfields']);
				$addDataString = "";
				if(is_array($addData) && !empty($addData)){
					foreach($addData as $key => $value){
						$addDataString .= $key . ': ' . $value . '<br />';
					}
				}

				$content[] = array(
					array('dat' => $data['votingsession']),
					array('dat' => $data['voting']),
					array('dat' => date(g_l('weEditorInfo', '[date_format]'), $data['time'])),
					array('dat' => $data['ip']),
					array('dat' => $data['agent']),
					array('dat' => g_l('modules_voting', $data['cookie'] ? '[enabled]' : '[disabled]')),
					array('dat' => g_l('global', $data['fallback'] ? '[yes]' : '[no]')),
					array('dat' => $mess),
					array('dat' => $data['answer']),
					array('dat' => $data['answertext']),
					array('dat' => $data['successor']),
					array('dat' => $addDataString),
				);
			}

			$nextprev = '<table style="margin-top: 10px;" class="default"><tr><td>' .
				($start < $size ?
					we_html_button::create_button(we_html_button::BACK, WEBEDITION_DIR . 'we_showMod.php?mod=voting&pnt=show_log&start=' . $back) : //bt_back
					we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true)
				) . "</td><td style='text-align:center' class='defaultfont' width='120'><b>" . ($size - $start + 1) . "&nbsp;-&nbsp;" .
				($size - $next) .
				"&nbsp;" . g_l('global', '[from]') . " " . ($size + 1) . '</b></td><td>' .
				($next > 0 ?
					we_html_button::create_button(we_html_button::NEXT, WEBEDITION_DIR . 'we_showMod.php?mod=voting&pnt=show_log&start=' . $next) : //bt_next
					we_html_button::create_button(we_html_button::NEXT, "", "", 100, 22, "", "", true)
				) .
				"</td></tr></table>";

			$parts = array(
				array(
					'headline' => '',
					'html' => we_html_tools::htmlDialogBorder4(1000, 300, $content, $headline) . $nextprev,
					'noline' => 1
				)
			);
		} else {
			$parts = array(
				array(
					'headline' => '',
					'html' => we_html_element::htmlSpan(array('class' => 'middlefont lowContrast'), g_l('modules_voting', '[log_is_empty]')) .
					we_html_element::htmlBr() .
					we_html_element::htmlBr(),
					'noline' => 1
				)
			);
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_multiIconBox::getHTML("show_log_data", $parts, 30, we_html_button::position_yes_no_cancel($refresh, $close), -1, '', '', false, g_l('modules_voting', '[voting]'), "", 558)
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLShowGroupLog(){//FIXME: unused??
		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$refresh = we_html_button::create_button(we_html_button::REFRESH, "javascript:location.reload();");

		$voting = new we_voting_voting();
		$voting->load($this->View->voting->ID);
		$log = $voting->loadDB($voting->ID);


		$headline = array(
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[time]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[ip]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[user_agent]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[cookie]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[log_fallback]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[status]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerID]'))),
			array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerText]'))),
		);

		$content = array();

		$count = 15;
		$size = count($log);

		$nextprev = "";

		if($size > 0){
			$size--;
			$start = min(max(we_base_request::_(we_base_request::INT, 'start', $size), 0), $size);
			$back = min($start + $count, $size);
			$next = max($start - $count, -1);

			for($i = $start; $i > $next; $i--){
				if($i < 0){
					break;
				}
				$data = $log[$i];

				if($data['status'] != we_voting_voting::SUCCESS){
					switch($data['status']){
						case we_voting_voting::ERROR :
							$mess = g_l('modules_voting', '[log_error]');
							break;
						case we_voting_voting::ERROR_ACTIVE :
							$mess = g_l('modules_voting', '[log_error_active]');
							break;
						case we_voting_voting::ERROR_REVOTE :
							$mess = g_l('modules_voting', '[log_error_revote]');
							break;
						case we_voting_voting::ERROR_BLACKIP :
							$mess = g_l('modules_voting', '[log_error_blackip]');
							break;
						default:
							$mess = g_l('modules_voting', '[log_error]');
					}
					$mess = we_html_element::htmlSpan(array('style' => 'color: red;'), $mess);
				} else {
					$mess = g_l('modules_voting', '[log_success]');
				}

				$content[] = array(
					array('dat' => date(g_l('weEditorInfo', '[date_format]'), $data['time'])),
					array('dat' => $data['ip']),
					array('dat' => $data['agent']),
					array('dat' => g_l('modules_voting', $data['cookie'] ? '[enabled]' : '[disabled]')),
					array('dat' => g_l('global', $data['fallback'] ? '[yes]' : '[no]')),
					array('dat' => $mess),
					array('dat' => $data['answer']),
					array('dat' => $data['answertext']),
				);
			}

			$nextprev = '<table style="margin-top: 10px;" class="default"><tr><td>' .
				($start < $size ?
					we_html_button::create_button(we_html_button::BACK, WEBEDITION_DIR . 'we_showMod.php?mod=voting&pnt=show_log&start=' . $back) : //bt_back
					we_html_button::create_button(we_html_button::BACK, "", false, 100, 22, "", "", true)
				) . "</td><td style='text-align:center' class='defaultfont' width='120'><b>" . ($size - $start + 1) . "&nbsp;-&nbsp;" .
				($size - $next) .
				"&nbsp;" . g_l('global', '[from]') . ' ' . ($size + 1) . '</b></td><td>' .
				($next > 0 ?
					we_html_button::create_button(we_html_button::NEXT, WEBEDITION_DIR . 'we_showMod.php?mod=voting&pnt=show_log&start=' . $next) : //bt_next
					we_html_button::create_button(we_html_button::NEXT, "", "", 100, 22, "", "", true)
				) .
				'</td></tr></table>';

			$parts = array(
				array(
					'headline' => '',
					'html' => we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . $nextprev,
					'noline' => 1
				)
			);
		} else {
			$parts = array(
				array(
					'headline' => '',
					'html' => we_html_element::htmlSpan(array('class' => 'middlefont lowContrast'), g_l('modules_voting', '[log_is_empty]')) .
					we_html_element::htmlBr() .
					we_html_element::htmlBr(),
					'noline' => 1
				)
			);
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_multiIconBox::getHTML("show_log_data", $parts, 30, we_html_button::position_yes_no_cancel($refresh, $close), -1, '', '', false, g_l('modules_voting', '[voting]'), "", 558)
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLDeleteGroupLog(){
		$this->View->voting->deleteGroupLogData();

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_tools::htmlDialogLayout(
						we_html_element::htmlSpan(array('class' => 'defaultfont'), g_l('modules_voting', '[data_deleted_info]')), g_l('modules_voting', '[voting]'), we_html_button::formatButtons($close))
				)
		);
	}

}
