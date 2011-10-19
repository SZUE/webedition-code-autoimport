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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we_classes/modules/weModuleFrames.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");
include_once(WE_CUSTOMER_MODULE_DIR . "weCustomerView.php");
include_once(WE_CUSTOMER_MODULE_DIR . "weCustomerTree.php");

class weCustomerFrames extends weModuleFrames {

	var $View;
	var $jsOut_fieldTypesByName;

	function weCustomerFrames() {
		weModuleFrames::weModuleFrames(WE_CUSTOMER_MODULE_PATH . "edit_customer_frameset.php");
		$this->Tree = new weCustomerTree();
		$this->View = new weCustomerView(WE_CUSTOMER_MODULE_PATH . "edit_customer_frameset.php", "top.content");
		$this->setupTree(CUSTOMER_TABLE, "top.content", "top.content.resize.left.tree", "top.content.cmd");
		$this->module = "customer";
	}

	function getHTMLFrameset() {
		$this->View->customer->clearSessionVars();
		//$this->View->settings->clearSessionVars();
		$this->View->settings->load(false);
		return weModuleFrames::getHTMLFrameset();
	}

	function getHTMLResize() {

		$frameset = new we_htmlFrameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		if ($GLOBALS["BROWSER"] == "NN") {
			$frameset->setAttributes(array("cols" => '220,*'));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=left", "name" => "left", "noresize" => null));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=right" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "right", "noresize" => null));
		} else {
			$frameset->setAttributes(array("cols" => '220,*', "border" => "1", "frameborder" => "yes"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=left", "name" => "left"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=right" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "right"));
		}

		// set and return html code
		$body = $frameset->getHtmlCode() . "\n" . we_baseElement::getHtmlCode($noframeset);

		return $this->getHTMLDocument($body);
	}

	function getJSCmdCode() {
		return $this->View->getJSTop();
	}

	function getHTMLBranchSelect($with_common=true, $with_other=true) {
		$branches_names = array();
		$branches_names = $this->View->customer->getBranchesNames();

		$select = new we_htmlSelect(array("name" => "branch"));

		if ($with_common)
			$select->addOption(g_l('modules_customer','[common]'),g_l('modules_customer','[common]'));
		if ($with_other)
			$select->addOption(g_l('modules_customer','[other]'),g_l('modules_customer','[other]'));

		foreach ($branches_names as $branch) {
			$select->addOption($branch, $branch);
		}


		return $select;
	}

	function getHTMLFieldsSelect($branch) {
		$select = new we_htmlSelect(array("name" => "branch"));

		$fields_names = array();
		$fields_names = $this->View->customer->getFieldsNames($branch, $this->View->settings->getEditSort());
		$this->jsOut_fieldTypesByName = "\tvar fieldTypesByName = new Array();\n";
		foreach ($fields_names as $val) {
			$tmp = $this->View->getFieldProperties($val);
			$this->jsOut_fieldTypesByName .= "\tfieldTypesByName['$val'] = '" . (isset($tmp['type']) ? $tmp['type'] : "") . "';\n";
		}
		if (is_array($fields_names)) {
			foreach ($fields_names as $k => $field) {
				if ($this->View->customer->isProperty($field))
					$select->addOption($k, $this->View->settings->getPropertyTitle($field));
				else
					$select->addOption($k, $field);
			}
		}

		return $select;
	}

	function getHTMLSortSelect($include_no_sort=true) {
		$sort = new we_htmlSelect(array('name' => 'sort', 'class' => 'weSelect'));

		$sort_names = array_keys($this->View->settings->SortView);

		if ($include_no_sort)
			$sort->addOption(g_l('modules_customer','[no_sort]'),g_l('modules_customer','[no_sort]'));

		foreach ($sort_names as $k => $v) {
			$sort->addOption(htmlspecialchars($v), htmlspecialchars($v));
		}


		return $sort;
	}

	function getHTMLFieldControl($field, $value=null) {
		$props = $this->View->getFieldProperties($field);
		if (!($props['type'] == 'select' || $props['type'] == 'multiselect') && !$this->View->customer->ID && $value == null) {
			$value = $props['default'];
		}
		switch ($props['type']) {
			case 'input':
				return htmlTextInput($field, 32, stripslashes($value), '', "onchange=\"top.content.setHot();\" style='{width:240}'");
			case 'number':
				return htmlTextInput($field, 32, stripslashes($value), '', "onchange=\"top.content.setHot();\" style='{width:240}'",'number');
			case 'multiselect':
				$out = we_htmlElement::htmlHidden(array('name' => $field, 'value' => $value));
				$values=explode(',', $value);
				$defs = explode(',', $props['default']);
				$cnt=count($defs);
				$i=0;
				foreach ($defs as $def){
					$attribs=array('type'=>'checkbox','name'=>$field.'_multi_'.($i++),'value'=>$def, ($GLOBALS['BROWSER']=='IE'?'onclick':'onchange')=>'setMultiSelectData(\''.$field.'\','.$cnt.');');
					if(in_array($def,$values)){
						$attribs['checked']='checked';
					}
					$out .= we_htmlElement::htmlInput($attribs).$def.we_htmlElement::htmlBr();
				}

				return we_htmlElement::htmlDiv(array('style'=>'height: 80px;overflow: auto;width: 220px;border: 1px solid #000;padding: 3px;background: #FFFFFF;'),$out);//we_htmlElement::htmlB('not yet implemented');
			case 'country':
				$langcode = we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]);

				$countrycode = array_search($langcode, $GLOBALS['WE_LANGS_COUNTRIES']);
				$countryselect = new we_htmlSelect(array('name' => $field, 'size' => '1', 'style' => '{width:240;}', 'class' => 'wetextinput', 'onblur' => 'this.className=\'wetextinput\'', 'onfocus' => 'this.className=\'wetextinputselected\'', 'id' => ($field == 'Gruppe' ? 'yuiAcInputPathGroupX' : ''), 'onchange' => ($field == 'Gruppe' ? 'top.content.setHot();' : 'top.content.setHot();')));

				$topCountries = (defined('WE_COUNTRIES_TOP') ? explode(',', WE_COUNTRIES_TOP) : explode(',', "DE,AT,CH"));

				$topCountries = array_flip($topCountries);
				foreach ($topCountries as $countrykey => &$countryvalue) {
					$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
				}
				if (defined('WE_COUNTRIES_SHOWN')) {
					$shownCountries = explode(',', WE_COUNTRIES_SHOWN);
				} else {
					$shownCountries = explode(',', 'BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY');
				}
				$shownCountries = array_flip($shownCountries);
				foreach ($shownCountries as $countrykey => &$countryvalue) {
					$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
				}
				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
				asort($topCountries, SORT_LOCALE_STRING);
				asort($shownCountries, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);

				$content = '';
				if(defined('WE_COUNTRIES_DEFAULT') && WE_COUNTRIES_DEFAULT !=''){
					$countryselect->addOption('--', CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT));
				}
				foreach ($topCountries as $countrykey => &$countryvalue) {
					$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
				}
				if( !empty($topCountries) && !empty($shownCountries) ) {
					$countryselect->addOption('-', '----', array("disabled" => "disabled"));
				}

				foreach ($shownCountries as $countrykey => &$countryvalue) {
					$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
				}

				$countryselect->selectOption($value);
				return $countryselect->getHtmlCode();

				break;
			case 'language':
				if(isset($GLOBALS["weFrontendLanguages"]) && is_array($GLOBALS["weFrontendLanguages"]) ){
					$frontendL = $GLOBALS["weFrontendLanguages"];
					foreach ($frontendL as $lc => &$lcvalue) {
						$lccode = explode('_', $lcvalue);
						$lcvalue = $lccode[0];
					}
					$languageselect = new we_htmlSelect(array("name" => $field, "size" => "1", "style" => "{width:240;}", "class" => "wetextinput", "onblur" => "this.className='wetextinput'", "onfocus" => "this.className='wetextinputselected'", "id" => ($field == "Gruppe" ? "yuiAcInputPathGroupX" : ""), "onchange" => ($field == "Gruppe" ? "top.content.setHot();" : "top.content.setHot();")));
					foreach (g_l('languages','') as $languagekey => $languagevalue) {
						if (in_array($languagekey, $frontendL)) {
							$languageselect->addOption($languagekey, $languagevalue);
						}
					}
					$languageselect->selectOption($value);
					return $languageselect->getHtmlCode();
				} else {
					return 'no FrontendLanguages defined';
				}

				break;
			case 'select':

				$defs = explode(',', $props['default']);
				if (!in_array($value, $defs)) {
					$defs = array_merge(array($value), $defs);
				}

				$select = new we_htmlSelect(array("name" => $field, "size" => "1", "style" => "{width:240;}", "class" => "wetextinput", "onblur" => "this.className='wetextinput'", "onfocus" => "this.className='wetextinputselected'", "id" => ($field == "Gruppe" ? "yuiAcInputPathGroupX" : ""), "onchange" => ($field == "Gruppe" ? "top.content.setHot();" : "top.content.setHot();")));
				foreach ($defs as $def)
					$select->addOption($def, $def);
				$select->selectOption($value);
				return $select->getHtmlCode();
				break;
			case 'textarea':
				return we_htmlElement::htmlTextArea(array("name" => $field, "style" => "{width:240}", "class" => "wetextarea", "onblur" => "this.className='wetextarea'", "onfocus" => "this.className='wetextareaselected'"), stripslashes($value));
				break;
			case 'dateTime':
			case 'date':
				$out = we_htmlElement::htmlHidden(array('name' => $field, 'value' => $value));

				if (empty($value)) {
					$value = $this->View->settings->getSettings('start_year') . '-01-01';
				}
				$date_format = ($props['type'] == 'dateTime' ? DATE_FORMAT : DATE_ONLY_FORMAT);
				$value = $this->View->settings->getDate($value, $date_format);
				$format = ($props['type'] == 'dateTime' ? g_l('weEditorInfo','[date_format]') : g_l('weEditorInfo','[date_only_format]'));
				$out.=we_htmlElement::jsElement('
					function populateDate_' . $field . '(){
						var year=document.we_form.' . $field . '_select_year.options[document.we_form.' . $field . '_select_year.selectedIndex].text;
						var month=document.we_form.' . $field . '_select_month.options[document.we_form.' . $field . '_select_month.selectedIndex].text;
						var day=document.we_form.' . $field . '_select_day.options[document.we_form.' . $field . '_select_day.selectedIndex].text;

						var datevar=new Date();
						datevar.setFullYear(year);
						datevar.setDate(day);
						datevar.setMonth(month-1);

						' . (we_getHourPos($format) != -1 ?
																'datevar.setHours(document.we_form.' . $field . '_select_hour.options[document.we_form.' . $field . '_select_hour.selectedIndex].text);' :
																'')
												. '

						' . (we_getMinutePos($format) != -1 ?
																'datevar.setMinutes(document.we_form.' . $field . '_select_minute.options[document.we_form.' . $field . '_select_minute.selectedIndex].text);' :
																'')
												. '

						document.we_form.' . $field . '.value=formatDate(datevar,\'' . addslashes($date_format) . '\');
					}
				');
				$out.=getPixel(5, 5) . $this->getDateInput2($field . "_select%s", $value, false, $format, "populateDate_$field()", "defaultfont", $this->View->settings->getSettings('start_year')) . getPixel(5, 5);
				return $out;
				break;
			case 'password':
				return htmlTextInput($field, 32, $value, '', 'onchange="top.content.setHot();" style="{width:240}" autocomplete="off" ', 'password');
				break;
			case 'img':

				$imgId = abs($value);

				include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_document.inc.php');
				$out = we_document::getFieldByVal($imgId, 'img');

				$out = '
					<table border="0" cellpadding="2" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
						<tr>
							<td class="weEditmodeStyle" colspan="2" align="center">' . $out . '
								<input type="hidden" name="' . $field . '" value="' . $imgId . '" /></td>
						</tr>
						<tr>
							<td class="weEditmodeStyle" colspan="2" align="center">';
				//javascript:we_cmd('openDocselector', '" . $imgId . "', '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'" . $field . "\\'].value', '', 'opener.refreshForm()', '" . session_id() . "', '', 'image/*', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")
				$wecmdenc1= we_cmd_enc("document.forms['we_form'].elements['" . $field . "'].value");
				$wecmdenc2= '';
				$wecmdenc3= we_cmd_enc("opener.refreshForm()");
				$out .= we_button::create_button_table(array(we_button::create_button('image:btn_select_image', "javascript:we_cmd('openDocselector', '" . $imgId . "', '" . FILE_TABLE . "','".$wecmdenc1."','','".$wecmdenc3."','" . session_id() . "', '', 'image/*', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")", true), we_button::create_button('image:btn_function_trash', "javascript:document.we_form.elements['$field'].value='';refreshForm();", true)), 5) .
								'</td>
						</tr>
					</table>';

				return $out;

				break;

			default: return htmlTextInput($field, 32, stripslashes($value), "", "onchange=\"top.content.setHot();\" style='{width:240}'");
		}
		return null;
	}

	function getHTMLEditorHeader() {
		$extraJS = "var aTabs=new Array;\n";

		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/we_tabs.class.inc.php');

		if (isset($_REQUEST['home'])) {
			return $this->getHTMLDocument(we_htmlElement::htmlBody(array('bgcolor' => '#FFFFFF', 'background' => '/webEdition/images/backgrounds/bgGrayLineTop.gif'), ''));
		}

		$tabs = new we_tabs();

		$branches_names = array();
		$branches_names = $this->View->customer->getBranchesNames();

		$tabs->addTab(new we_tab('#', g_l('modules_customer','[common]'),'TAB_NORMAL', "setTab('" . g_l('modules_customer','[common]') . "');", array("id" => "common")));
		$extraJS .= "aTabs['" . g_l('modules_customer','[common]'). "']='common';\n";
		$branchCount = 0;
		foreach ($branches_names as $branch) {
			$tabs->addTab(new we_tab("#", $branch, 'TAB_NORMAL', "setTab('" . $branch . "');", array("id" => "branch_" . $branchCount)));
			$extraJS .= "aTabs['" . $branch . "']='branch_" . $branchCount . "';\n";
			$branchCount++;
		}
		$tabs->addTab(new we_tab('#', g_l('modules_customer','[other]'),'TAB_NORMAL', "setTab('" . g_l('modules_customer','[other]') . "');", array("id" => "other")));
		$extraJS .= "aTabs['" . g_l('modules_customer','[other]'). "']='other';\n";
		$tabs->addTab(new we_tab("#", g_l('modules_customer','[all]'), 'TAB_NORMAL', "setTab('" . g_l('modules_customer','[all]') . "');", array("id" => "all")));
		$extraJS .= "aTabs['" . g_l('modules_customer','[all]'). "']='all';\n";
//((top.content.activ_tab=="' . g_l('modules_customer','[other]') . '") ? TAB_ACTIVE : TAB_NORMAL)
		$js = we_htmlElement::jsElement('
				function setTab(tab) {
					' . $this->topFrame . '.activ_tab=tab;
					parent.edbody.we_cmd(\'switchPage\',tab);
				}
				top.content.hloaded = 1;
		');

		if (defined('SHOP_TABLE')) {
			$tabs->addTab(new we_tab("#", g_l('modules_customer','[orderTab]'), 'TAB_NORMAL', "setTab('" . g_l('modules_customer','[orderTab]') . "');", array("id" => "orderTab")));
			$extraJS .= "aTabs['" . g_l('modules_customer','[orderTab]') . "']='orderTab';\n";
		}
		if (defined('OBJECT_FILES_TABLE')) {
			$tabs->addTab(new we_tab("#", $l_customer["objectTab"], 'TAB_NORMAL', "setTab('" . $l_customer["objectTab"] . "');", array("id" => "objectTab")));
			$extraJS .= "aTabs['" . $l_customer["objectTab"] . "']='objectTab';\n";
		}
		$tabs->addTab(new we_tab("#", $l_customer["documentTab"], 'TAB_NORMAL', "setTab('" . $l_customer["documentTab"] . "');", array("id" => "documentTab")));
		$extraJS .= "aTabs['" . $l_customer["documentTab"] . "']='documentTab';\n";


		$tabs->onResize();
		$tabsHead = $tabs->getHeader();
		$tabsBody = $tabs->getJS();
		$tabsHead .= $js;
//		$js.= $tabsHead;


		$table = new we_htmlTable(array("width" => "3000", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 3, 1);

		$table->setCol(0, 0, array(), getPixel(1, 3));

		$table->setCol(1, 0, array("valign" => "top", "class" => "small"),
						getPixel(15, 2) .
						we_htmlElement::htmlB(
										g_l('modules_customer','[customer]') . ":&nbsp;" . $this->View->customer->Username .
										we_htmlElement::htmlImg(array("align" => "absmiddle", "height" => "19", "width" => "1600", "src" => IMAGE_DIR . "pixel.gif"))
						)
		);

		$extraJS .= 'if(top.content.activ_tab) document.getElementById(aTabs[top.content.activ_tab]).className="tabActive"; else document.getElementById("common").className="tabActive"';

		//$text = ($this->View->customer->Gruppe ? "/".$this->View->customer->Gruppe : "") . $this->View->customer->Path;
		$text = $this->View->customer->Username;
		$body = we_htmlElement::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"),
										'<div id="main" >' . getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", we_htmlElement::htmlB(g_l('modules_customer','[customer]'))) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b></span></nobr></div>' . getPixel(100, 3) .
										$tabs->getHTML() .
										'</div>' . we_htmlElement::jsElement($extraJS)
						//$js.
						//$table->getHtmlCode() .
						//$tabsBody
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	function getHTMLEditorBody() {
		$hiddens = array("cmd" => "edit_customer", "pnt" => "edbody", "activ_sort" => "0");

		if (isset($_REQUEST["home"]) && $_REQUEST["home"]) {
			$hiddens["cmd"] = "home";
			$GLOBALS["we_print_not_htmltop"] = true;
			$GLOBALS["we_head_insert"] = $this->View->getJSProperty();
			$GLOBALS["we_body_insert"] = we_htmlElement::htmlForm(array("name" => "we_form"),
											$this->View->getCommonHiddens($hiddens) . we_htmlelement::htmlHidden(array("name" => "home", "value" => "0"))
			);
			$GLOBALS["mod"] = "customer";
			ob_start();
			include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_modules/home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}

		$branch = (isset($_REQUEST['branch']) && $_REQUEST['branch'] != '' ? $_REQUEST['branch'] : g_l('modules_customer','[common]'));

		$body = we_htmlElement::htmlBody(array("class" => "weEditorBody", "onLoad" => "loaded=1", "onunload" => "doUnload()"),
										we_htmlElement::htmlForm(array("name" => "we_form"), $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties($branch))
		);

		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	function getHTMLEditorFooter() {
		if (isset($_REQUEST['home'])) {
			return $this->getHTMLDocument(we_htmlElement::htmlBody(array("bgcolor" => "#EFf0EF"), ""));
		}


		$table1 = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "300"), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), getPixel(1600, 10));

		$table2 = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "300"), 1, 2);
		$table2->setRow(0, array("valign" => "middle"));
		$table2->setCol(0, 0, array("nowrap" => null), getPixel(5, 5));
		$table2->setCol(0, 1, array("nowrap" => null),
						we_button::create_button("save", "javascript:we_save();")
		);


		return $this->getHTMLDocument(
						we_htmlElement::jsElement("
					function we_save() {
						top.content.we_cmd('save_customer');

					}
					") .
						we_htmlElement::htmlBody(array('bgcolor' => 'white', 'background' => '/webEdition/images/edit/editfooterback.gif', 'marginwidth' => '0', 'marginheight' => '0', 'leftmargin' => '0', 'topmargin' => '0'),
										we_htmlElement::htmlForm(array(), $table1->getHtmlCode() . $table2->getHtmlCode())
						)
		);
	}

	function getHTMLProperties($preselect='') {
		include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_classes/html/we_multibox.inc.php');
		$parts = array();

		$out = '';
		$entry = '';
		$branches = array();
		$common = array();
		$other = array();

		$common['ID'] = $this->View->customer->ID;
		$this->View->customer->getBranches($branches, $common, $other, $this->View->settings->getEditSort());



		if ($preselect == g_l('modules_customer','[common]')|| $preselect == g_l('modules_customer','[all]')) {
			$table = new we_htmlTable(array("width" => "300", "height" => "50", "cellpadding" => "10", "cellspacing" => "0", "border" => "0"), 1, 2);
			$r = 0;
			$c = 0;
			$table->setRow(0, array("valign" => "top"));
			foreach ($common as $pk => $pv) {
				$pv = stripslashes($pv);

				if ($this->View->customer->isInfoDate($pk)) {
					$pv = ($pv == '' || !is_numeric($pv)) ? 0 : $pv;
					$table->setCol($r, $c, array("class" => "defaultfont"), htmlFormElementTable(($pv != "0" ? we_htmlElement::htmlDiv(array("class" => "defaultgray"), date(g_l('weEditorInfo',"[date_format]"), $pv)) : "-" . getPixel(100, 5)), $this->View->settings->getPropertyTitle($pk)));
				} else {
					switch ($pk) {
						case 'ID':
							$table->setCol($r, $c, array("class" => "defaultfont"), htmlFormElementTable(($pv != "0" ? we_htmlElement::htmlDiv(array("class" => "defaultgray"), $pv) : "-" . getPixel(100, 5)), $this->View->settings->getPropertyTitle($pk)));
							$c++;
							$table->setCol($r, $c, array("class" => "defaultfont"), "");
							break;
						case 'LoginDenied':
							$table->setCol($r, $c, array("class" => "defaultfont"), htmlFormElementTable(we_htmlElement::htmlDiv(array("class" => "defaultgray"), we_forms::checkbox(1, $pv, "LoginDenied", g_l('modules_customer','[login_denied]'), false, "defaultfont", "top.content.setHot();")), $this->View->settings->getPropertyTitle($pk)));
							break;
						case 'AutoLoginDenied':
							$table->setCol($r, $c, array("class" => "defaultfont"), htmlFormElementTable(we_htmlElement::htmlDiv(array("class" => "defaultgray"), we_forms::checkbox(1, $pv, "AutoLoginDenied", g_l('modules_customer','[login_denied]'), false, "defaultfont", "top.content.setHot();")), $this->View->settings->getPropertyTitle($pk)));
							break;
						case 'AutoLogin':
							$table->setCol($r, $c, array("class" => "defaultfont"), htmlFormElementTable(we_htmlElement::htmlDiv(array("class" => "defaultgray"), we_forms::checkbox(1, $pv, "AutoLogin", g_l('modules_customer','[autologin_request]'), false, "defaultfont", "top.content.setHot();")), $this->View->settings->getPropertyTitle($pk)));
							break;
						case 'Password':
							$table->setCol($r, $c, array(), htmlFormElementTable(htmlTextInput($pk, 32, $pv, "", "onchange=\"top.content.setHot();\" ", (we_hasPerm('CUSTOMER_PASSWORD_VISIBLE') ? 'text' : 'password'), "240px"), $this->View->settings->getPropertyTitle($pk)));
							break;
						case 'Username':
							$inputattribs = ' id="yuiAcInputPathName" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()"';
							$table->setCol($r, $c, array(), htmlFormElementTable(htmlTextInput($pk, 32, $pv, "", "onchange=\"top.content.setHot();\" " . $inputattribs, "text", "240px"), $this->View->settings->getPropertyTitle($pk)));
							break;
						default:
								$inputattribs = '';
								$table->setCol($r, $c, array(), htmlFormElementTable(htmlTextInput($pk, 32, $pv, "", "onchange=\"top.content.setHot();\" " . $inputattribs, "text", "240px"), $this->View->settings->getPropertyTitle($pk)));
					}
				}

				$c++;
				if ($c > 1) {
					$r++;
					$table->addRow();
					$table->setRow($r, array("valign" => "top"));
				}
				if ($c > 1)
					$c = 0;
			}

			array_push($parts, array(
					"headline" => ($preselect == g_l('modules_customer','[all]') ? g_l('modules_customer','[common]') : g_l('modules_customer','[data]')),
					"html" => $table->getHtmlCode(),
					"space" => 120)
			);
		}
		if ($preselect == g_l('modules_customer','[orderTab]')) {

			include_once(WE_SHOP_MODULE_DIR . 'shopFunctions.inc.php');

			$orderStr = getCustomersOrderList($this->View->customer->ID, false);

			array_push($parts, array(
					"html" => $orderStr,
					"space" => 0
							)
			);
		}
		if ($preselect == g_l('modules_customer','[objectTab]')) {
			$query ='SELECT * FROM '.OBJECT_FILES_TABLE.' WHERE '.OBJECT_FILES_TABLE.'.WebUserID = '.$this->View->customer->ID. ' ORDER BY '.OBJECT_FILES_TABLE.'.Path';
			$DB_WE = new DB_WE();
			$DB_WE->query($query);
			$objectStr='';
			if ($DB_WE->num_rows()) {
				$objectStr.='<table class="defaultfont" width="600">';
				$objectStr.='<tr><td>&nbsp;</td> <td><b>' . $g_l('modules_customer','[ID]') . '</b></td><td><b>' . g_l('modules_customer','[Filename]') . '</b></td><td><b>' . g_l('modules_customer','[Aenderungsdatum]') . '</b></td>';
				while ($DB_WE->next_record()) {
					$objectStr.='<tr>';
					$objectStr.='<td>'.we_button::create_button('image:btn_edit_edit', "javascript: if(top.opener.top.doClickDirect){top.opener.top.doClickDirect(".$DB_WE->f('ID').",'".$DB_WE->f('ContentType'). "','tblObjectFiles'); }" ).'</td>';
					$objectStr.='<td>'.$DB_WE->f('ID').'</td>';
					$objectStr.='<td title="'.$DB_WE->f('Path').'">'.$DB_WE->f('Text').'</td>';
					if ($DB_WE->f('Published')) {
						if ($DB_WE->f('ModDate')> $DB_WE->f('Published') ) {
							$class='changeddefaultfont';
						} else {
							$class='defaultfont';
						}
					} else {

						$class='npdefaultfont';
					}
					$objectStr.='<td class="'.$class.'">'.date('d.m.Y H:i',$DB_WE->f('ModDate')).'</td>';
					$objectStr.='</tr>';
				}
				$objectStr.='</table>';
			} else {
				$objectStr=g_l('modules_customer','[NoObjects]');

			}
			//$objectStr = getCustomersObjectList($this->View->customer->ID, false);

			array_push($parts, array(
					"html" => $objectStr,
					"space" => 0
							)
			);
		}
		if ($preselect == g_l('modules_customer','[documentTab]')) {

			$query ='SELECT * FROM '.FILE_TABLE.' WHERE '.FILE_TABLE.'.WebUserID = '.$this->View->customer->ID.' ORDER BY '.FILE_TABLE.'.Path';
			$DB_WE = new DB_WE();
			$DB_WE->query($query);
			$documentStr='';
			if ($DB_WE->num_rows()) {
				$documentStr.='<table class="defaultfont" width="600">';
				$documentStr.='<tr><td>&nbsp;</td> <td><b>' . g_l('modules_customer','[ID]') . '</b></td><td><b>' . g_l('modules_customer','[Filename]') . '</b></td><td><b>' . g_l('modules_customer','[Aenderungsdatum]') . '</b></td><td><b>' . g_l('modules_customer','[Titel]') . '</b></td>';
				$documentStr.='</tr>';
				$db_we2 = new DB_WE();
				while ($DB_WE->next_record()) {
					$documentStr.='<tr>';
					$documentStr.='<td>'.we_button::create_button('image:btn_edit_edit', "javascript: if(top.opener.top.doClickDirect){top.opener.top.doClickDirect(".$DB_WE->f('ID').",'".$DB_WE->f('ContentType'). "','tblFile'); }" ).'</td>';

					$documentStr.='<td>'.$DB_WE->f('ID').'</td>';

					$documentStr.='<td title="'.$DB_WE->f('Path').'">'.$DB_WE->f('Text').'</td>';
					if ($DB_WE->f('Published')) {
						if ($DB_WE->f('ModDate')> $DB_WE->f('Published') ) {
							$class='changeddefaultfont';
						} else {
							$class='defaultfont';
						}
					} else {

						$class='npdefaultfont';
					}
					$documentStr.='<td class="'.$class.'">'.date('d.m.Y H:i',$DB_WE->f('ModDate')).'</td>';
					$titel='';$beschreibung='';
					$query2 ='SELECT '.CONTENT_TABLE.'.Dat AS Inhalt, '.LINK_TABLE.'.Name AS Name FROM '.FILE_TABLE.', '.LINK_TABLE.','.CONTENT_TABLE.'
WHERE '.FILE_TABLE.'.ID='.LINK_TABLE.'.DID AND '.LINK_TABLE.'.CID='.CONTENT_TABLE.'.ID AND '.LINK_TABLE.".Name='Title' AND ".
LINK_TABLE.".DocumentTable='".FILE_TABLE."' AND ".FILE_TABLE.'.ID='.$DB_WE->f('ID');
					//$documentStr.='<td>'.$query2.'</td>';


					$db_we2->query($query2);
					if($db_we2->next_record()){
						$titel= $db_we2->f('Inhalt');
					}
					$query2 ='SELECT '.CONTENT_TABLE.'.Dat AS Inhalt, '.LINK_TABLE.'.Name AS Name FROM '.FILE_TABLE.', '.LINK_TABLE.','.CONTENT_TABLE.'
WHERE '.FILE_TABLE.'.ID='.LINK_TABLE.'.DID AND '.LINK_TABLE.'.CID='.CONTENT_TABLE.'.ID AND '.LINK_TABLE.".Name='Description' AND ".
LINK_TABLE.".DocumentTable='".FILE_TABLE."' AND ".FILE_TABLE.'.ID='.$DB_WE->f('ID');
					//$documentStr.='<td>'.$query2.'</td>';
					$db_we2->query($query2);
					if($db_we2->next_record()){
						$beschreibung= $db_we2->f('Inhalt');
					}

					$documentStr.='<td title="'.$beschreibung.'">'.$titel.'</td>';
					$documentStr.='</tr>';
				}
				$documentStr.='</table>';
			} else {
				$documentStr=g_l('modules_customer','[NoDocuments]');
			}
			//$documentStr = getCustomersDocumentList($this->View->customer->ID, false);

			array_push($parts, array(
					"html" => $documentStr,
					"space" => 0
							)
			);
		}
		if ($preselect == g_l('modules_customer','[other]') || $preselect == g_l('modules_customer','[all]')) {

			$table = new we_htmlTable(array("width" => "500", "height" => "50", "cellpadding" => "10", "cellspacing" => "0", "border" => "0"), 1, 2);
			$r = 0;
			$c = 0;
			$table->setRow(0, array("valign" => "top"));
			foreach ($other as $k => $v) {
				$control = $this->getHTMLFieldControl($k, $v);
				if ($control != "") {
					$table->setCol($r, $c, array(), htmlFormElementTable($control, $k));
					$c++;
					if ($c > 1) {
						$r++;
						$table->addRow();
						$table->setRow($r, array("valign" => "top"));
					}
					if ($c > 1)
						$c = 0;
				}
			}
			array_push($parts, array(
					"headline" => ($preselect == g_l('modules_customer','[all]') ? g_l('modules_customer','[other]') : g_l('modules_customer','[data]')),
					"html" => $table->getHtmlCode(),
					"space" => 120)
			);
		}

		foreach ($branches as $bk => $branch) {
			if ($preselect != "" && $preselect != g_l('modules_customer','[all]')) {
				if ($bk != $preselect)
					continue;
			}
			$table = new we_htmlTable(array("width" => "500", "height" => "50", "cellpadding" => "10", "cellspacing" => "0", "border" => "0"), 1, 2);
			$r = 0;
			$c = 0;
			$table->setRow(0, array("valign" => "top"));
			foreach ($branch as $k => $v) {
				$control = $this->getHTMLFieldControl($bk . "_" . $k, $v);
				if ($control != "") {

					$table->setCol($r, $c, array(), htmlFormElementTable($control, $k));

					$c++;
					if ($c > 1) {
						$r++;
						$table->addRow();
						$table->setRow($r, array("valign" => "top"));
					}
					if ($c > 1)
						$c = 0;
				}
			}
			array_push($parts, array(
					"headline" => ($preselect == g_l('modules_customer','[all]') ? $bk : g_l('modules_customer','[data]')),
					"html" => $table->getHtmlCode(),
					"space" => 120)
			);
		}
		$out = we_multiIconBox::getHTML("", 680, $parts, 30);

		return $out;
	}

	function getHTMLLeft() {

		$frameset = new we_htmlFrameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "40,*,40"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=treeheader", "name" => "treeheader", "noresize" => null, "scrolling" => "no"));

		$frameset->addFrame(array("src" => WEBEDITION_DIR . "treeMain.php", "name" => "tree", "noresize" => null, "scrolling" => "auto"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=treefooter", "name" => "treefooter", "noresize" => null, "scrolling" => "no"));

		// set and return html code
		$body = $frameset->getHtmlCode() . "\n" . we_baseElement::getHtmlCode($noframeset);

		return $this->getHTMLDocument($body);
	}

	function getHTMLTreeHeader() {

		include_once(WE_CUSTOMER_MODULE_DIR . "weCustomerAdd.php");
		return weCustomerAdd::getHTMLTreeHeader($this);
	}

	function getHTMLTreeFooter() {


		$hiddens = we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "treefooter")) .
						we_htmlElement::htmlHidden(array("name" => "cmd", "value" => "show_search"));

		$table = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "3000"), 2, 1);
		$table->setCol(0, 0, array("valign" => "top"), we_htmlElement::htmlImg(array("align" => "absmiddle", "height" => "10", "width" => "1600", "src" => IMAGE_DIR . "pixel.gif")));
		$table->setCol(1, 0, array("nowrap" => null, "class" => "small"),
						we_htmlElement::jsElement($this->View->getJSSubmitFunction("treefooter")) .
						$hiddens .
						we_button::create_button_table(
										array(
												htmlTextInput("keyword", 10, "", "", "", "text", "150px"),
												we_button::create_button("image:btn_function_search", "javascript:submitForm()")
										)
						)
		);

		$body = we_htmlElement::htmlBody(array("bgcolor" => "white", "background" => "/webEdition/images/edit/editfooterback.gif", "marginwidth" => "5", "marginheight" => "0", "leftmargin" => "5", "topmargin" => "0"),
										we_htmlElement::htmlForm(array("name" => "we_form"), $table->getHtmlCode())
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLCustomerAdmin() {
		if (isset($_REQUEST["branch"]))
			$branch = $_REQUEST["branch"];
		else
			$branch=g_l('modules_customer','[other]');
		if (isset($_REQUEST["branch_select"]))
			$branch_select = $_REQUEST["branch"];
		else
			$branch_select=g_l('modules_customer','[other]');


		$select = $this->getHTMLBranchSelect(false);
		$select->setAttributes(array("name" => "branch_select", "class" => "weSelect", "onChange" => "selectBranch()", "style" => "width:150"));
		$select->selectOption($branch_select);

		$fields = $this->getHTMLFieldsSelect($branch);
		$fields->setAttributes(array("name" => "fields_select", "size" => "15", "onChange" => "", "style" => "width:310;height:250"));
		$hiddens = we_htmlElement::htmlHidden(array("name" => "field", "value" => ""));

		$buttons_table = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 12, 1);
		$buttons_table->setCol(0, 0, array(), we_button::create_button("add", "javascript:we_cmd('open_add_field')"));
		$buttons_table->setCol(1, 0, array(), getPixel(1, 5));
		$buttons_table->setCol(2, 0, array(), we_button::create_button("edit", "javascript:we_cmd('open_edit_field')"));
		$buttons_table->setCol(3, 0, array(), getPixel(1, 5));
		$buttons_table->setCol(4, 0, array(), we_button::create_button("delete", "javascript:we_cmd('delete_field')"));
		$buttons_table->setCol(5, 0, array(), getPixel(1, 15));
		$buttons_table->setCol(6, 0, array(), we_button::create_button("image:btn_direction_up", "javascript:we_cmd('move_field_up')"));
		$buttons_table->setCol(7, 0, array(), getPixel(1, 5));
		$buttons_table->setCol(8, 0, array(), we_button::create_button("image:btn_direction_down", "javascript:we_cmd('move_field_down')"));
		$buttons_table->setCol(9, 0, array("class" => "defaultgray"), g_l('modules_customer','[sort_edit_fields_explain]'));
		$buttons_table->setCol(10, 0, array(), getPixel(1, 5));
		$buttons_table->setCol(10, 0, array(), we_button::create_button("reset", "javascript:we_cmd('reset_edit_order')"));
		$table = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "500"), 5, 5);

		$table->setCol(0, 0, array("class" => "defaultgray"), g_l('modules_customer','[branch]'));
		$table->setCol(0, 1, array(), getPixel(10, 10));
		$table->setCol(0, 2, array("class" => "defaultgray"), g_l('modules_customer','[branch_select]'));
		$table->setCol(1, 0, array(), htmlTextInput("branch", 48, $branch, "", 'style="width:310"'));
		$table->setCol(1, 1, array(), getPixel(10, 10));
		$table->setCol(1, 2, array(), $select->getHtmlCode());
		$table->setCol(1, 3, array(), getPixel(10, 10));
		$table->setCol(1, 4, array(), we_button::create_button("image:btn_edit_edit", "javascript:we_cmd('open_edit_branch')"));

		$table->setCol(2, 0, array(), getPixel(10, 10));

		$table->setCol(3, 0, array("class" => "defaultgray", "valign" => "top"), g_l('modules_customer','[fields]'));
		$table->setCol(4, 0, array("valign" => "top"), $fields->getHtmlCode());
		$table->setCol(4, 1, array("valign" => "top"), getPixel(10, 10));
		$table->setCol(4, 2, array("valign" => "top"), $buttons_table->getHtmlCode());

		$out = we_htmlElement::htmlBody(array("class" => "weDIalogBody"),
										we_htmlElement::jsElement("", array("src" => JS_DIR . "windows.js")) .
										we_htmlElement::jsElement("self.focus();") .
										we_htmlElement::jsElement($this->View->getJSAdmin()) .
										we_htmlElement::htmlForm(array("name" => "we_form"),
														we_htmlElement::htmlHidden(array("name" => "cmd", "value" => "switchBranch")) .
														we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "customer_admin")) .
														htmlDialogLayout($table->getHtmlCode(), g_l('modules_customer','[field_admin]'), we_button::create_button("close", "javascript:self.close()"))
										)
		);

		return $this->getHTMLDocument($out);
	}

	function getHTMLFieldEditor($type, $mode) {
		if (isset($_REQUEST["field"]))
			$field = $_REQUEST["field"];
		else
			$field="";

		if (isset($_REQUEST["branch"]))
			$branch = $_REQUEST["branch"];
		else
			$branch=g_l('modules_customer','[other]');


		$hiddens = we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "field_editor")) .
						we_htmlElement::htmlHidden(array("name" => "cmd", "value" => "no_cmd")) .
						we_htmlElement::htmlHidden(array("name" => "branch", "value" => "$branch")) .
						we_htmlElement::htmlHidden(array("name" => "art", "value" => "$mode")) .
						($type == "field" ? we_htmlElement::htmlHidden(array("name" => "field", "value" => "$field")) : "");

		$cancel = we_button::create_button("cancel", "javascript:self.close();");

		if ($type == "branch") {
			$hiddens.=we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "branch_editor"));
			$edit = new we_htmlTable(array("border" => "0", "cellpadding" => "3", "cellspacing" => "3", "width" => "300"), 1, 2);
			$edit->setCol(0, 0, array("valign" => "middle", "class" => "defaultgray"), g_l('modules_customer','[field_name]'));
			$edit->setCol(0, 1, array("valign" => "middle", "class" => "defaultfont"), htmlTextInput("name", 26, $branch, '', ''));

			$save = we_button::create_button("save", "javascript:we_cmd('save_branch')");
		} else {
			$field_props = $this->View->getFieldProperties($field);

			$types = new we_htmlSelect(array("name" => "field_type", "class" => "weSelect", "style" => "width:200"));
			$types->addOptions(count($this->View->settings->field_types), array_keys($this->View->settings->field_types), array_keys($this->View->settings->field_types));
			if (isset($field_props["type"]))
				$types->selectOption($field_props["type"]);

			$hiddens.=we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "field_editor"));

			$edit = new we_htmlTable(array("border" => "0", "cellpadding" => "3", "cellspacing" => "3", "width" => "300"), 4, 2);

			$edit->setCol(0, 0, array("valign" => "middle", "class" => "defaultgray"), g_l('modules_customer','[branch]'));
			$edit->setCol(0, 1, array("valign" => "middle", "class" => "defaultfont"), $branch);

			$edit->setCol(1, 0, array("valign" => "middle", "class" => "defaultgray"), g_l('modules_customer','[field_name]'));
			$edit->setCol(1, 1, array("valign" => "middle", "class" => "defaultfont"), htmlTextInput("name", 26, (isset($field_props["name"]) ? $field_props["name"] : ""), "", ''));

			$edit->setCol(2, 0, array("valign" => "middle", "class" => "defaultgray"), g_l('modules_customer','[field_type]'));

			$edit->setCol(2, 1, array("valign" => "middle", "class" => "defaultfont"), $types->getHtmlCode());

			$edit->setCol(3, 0, array("valign" => "middle", "class" => "defaultgray"), g_l('modules_customer','[field_default]'));
			$edit->setCol(3, 1, array("valign" => "middle", "class" => "defaultfont"), htmlTextInput("field_default", 26, (isset($field_props["default"]) ? $field_props["default"] : ""), "", ''));

			$save = we_button::create_button("save", "javascript:we_cmd('save_field')");
		}

		$out = we_htmlElement::htmlBody(array("class" => "weDialogBody"),
										we_htmlElement::jsElement($this->View->getJSAdmin()) .
										we_htmlElement::jsElement("self.focus();") .
										we_htmlElement::htmlForm(array("name" => "we_form"),
														$hiddens .
														htmlDialogLayout($edit->getHtmlCode(), (
																		$type == "branch" ?
																						(g_l('modules_customer','[edit_branche]')) :
																						($mode == "edit" ? g_l('modules_customer','[edit_field]') :g_l('modules_customer','[add_field]'))
																		),
																		we_button::position_yes_no_cancel($save, null, $cancel)
														)
										)
		);

		return $this->getHTMLDocument($out);
	}

	function getHTMLCmd() {
		$out = "";
		if (isset($_REQUEST["pid"])) {
				$pid = ($GLOBALS['WE_BACKENDCHARSET'] == 'UTF-8') ?
					utf8_encode($_REQUEST["pid"]):
					$_REQUEST["pid"];
		}else{
			exit;
		}

		if (isset($_REQUEST['sort'])){
			$sort=($_REQUEST['sort'] == g_l('modules_customer','[no_sort]')?0:1);
		}else {
			if ($this->View->settings->getSettings("default_sort_view") != g_l('modules_customer','[no_sort]')) {
				$sort = 1;
				$_REQUEST["sort"] = $this->View->settings->getSettings('default_sort_view');
			}else{
				$sort=0;
			}
		}

		$offset = (isset($_REQUEST["offset"])) ? $_REQUEST["offset"] : 0;

		include_once(WE_CUSTOMER_MODULE_DIR . "weCustomerTreeLoader.php");

		$rootjs = "";
		if (!$pid){
			$rootjs.='
		' . $this->Tree->topFrame . '.treeData.clear();
		' . $this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));
		';
		}

		$hiddens = we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
						we_htmlElement::htmlHidden(array("name" => "cmd", "value" => "no_cmd"));

		$out.=we_htmlElement::htmlBody(array("bgcolor" => "white", "marginwidth" => "10", "marginheight" => "10", "leftmargin" => "10", "topmargin" => "10"),
										we_htmlElement::htmlForm(array("name" => "we_form"),
														$hiddens .
														we_htmlElement::jsElement($rootjs . $this->Tree->getJSLoadTree(weCustomerTreeLoader::getItems($pid, $offset, $this->Tree->default_segment, ($sort ? $_REQUEST["sort"] : ""))))
										)
		);
		return $this->getHTMLDocument($out);
	}

	function getHTMLSortEditor() {
		include_once(WE_CUSTOMER_MODULE_DIR . "weCustomerAdd.php");
		return weCustomerAdd::getHTMLSortEditor($this);
	}

	function getHTMLSearch() {
		$colspan = 4;

		$mode = isset($_REQUEST["mode"]) ? $_REQUEST["mode"] : 0;

		$hiddens = we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "search")) .
						we_htmlElement::htmlHidden(array("name" => "cmd", "value" => "search")) .
						we_htmlElement::htmlHidden(array("name" => "search", "value" => "1")) .
						we_htmlElement::htmlHidden(array("name" => "mode", "value" => $mode));

		$search_but = we_button::create_button("image:btn_function_search", "javascript:we_cmd('search')");

		$search = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "550", "height" => "50"), 4, 3);
		$search->setRow(0, array("valign" => "top"));
		$search->setCol(0, 0, array("class" => "defaultfont", "colspan" => "3", "style" => "padding-bottom: 3px;"), g_l('modules_customer','[search_for]'));

		$select = new we_htmlSelect(array("name" => "search_result", "style" => "width:550px", "onDblClick" => "opener." . $this->topFrame . ".we_cmd('edit_customer',document.we_form.search_result.options[document.we_form.search_result.selectedIndex].value)", "size" => 20));

		if ($mode) {
			include_once(WE_CUSTOMER_MODULE_DIR . "weCustomerAdd.php");
			weCustomerAdd::getHTMLSearch($this, $search, $select);
		} else {
			$search->setCol(1, 0, array(),
							htmlTextInput("keyword", 80, (isset($_REQUEST["keyword"]) ? $_REQUEST["keyword"] : ""), "", 'onchange=""', "text", "550px")
			);

			$sw = null;
			$sw = we_button::create_button("image:btn_direction_right", "javascript:we_cmd('switchToAdvance')");

			$search->setCol(2, 0, array(), getPixel(5, 5));
			$search->setCol(3, 0, array("align" => "right", "colspan" => $colspan),
							we_button::create_button_table(
											array(
													we_htmlElement::htmlDiv(array("class" => "defaultfont"), g_l('modules_customer','[advanced_search]')),
													$sw,
													$search_but
											)
							)
			);
			$hiddens.=we_htmlElement::htmlHidden(array("name" => "count", "value" => 1));

			$max_res = $this->View->settings->getMaxSearchResults();
			$result = array();
			if (isset($_REQUEST["keyword"]) && isset($_REQUEST["search"]) && $_REQUEST["keyword"] && $_REQUEST["search"])
				$result = $this->View->getSearchResults($_REQUEST["keyword"], $max_res);
			foreach ($result as $id => $text)
				$select->addOption($id, $text);
		}

		$table = new we_htmlTable(array("border" => "0", "cellpadding" => "2", "cellspacing" => "0", "width" => "550", "height" => "50"), 3, 1);
		$table->setCol(0, 0, array(), $search->getHtmlCode());
		$table->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_customer','[search_result]'));
		$table->setCol(2, 0, array(), $select->getHtmlCode());
		$calenderJS =
						$out = we_htmlElement::htmlBody(array("class" => "weDialogBody", "onLoad" => ($mode ? "" : "document.we_form.keyword.focus();")),
										we_htmlElement::linkElement(array("rel" => "stylesheet", "type" => "text/css", "href" => JS_DIR . "jscalendar/skins/aqua/theme.css", "title" => "Aqua")) .
										we_htmlElement::jsElement("", array("src" => JS_DIR . "utils/weDate.js")) .
										//we_htmlElement::jsElement("",array("src"=>JS_DIR."jscalendar/lang/calendar.js")).
										we_htmlElement::jsElement("", array("src" => JS_DIR . "jscalendar/calendar.js")) .
										we_htmlElement::jsElement("", array("src" => JS_DIR . "jscalendar/calendar-setup.js")) .
										we_htmlElement::jsElement("", array("src" => WEBEDITION_DIR . "we/include/we_language/" . $GLOBALS["WE_LANGUAGE"] . "/calendar.js")) .
										we_htmlElement::jsElement($this->View->getJSSearch()) .
										we_htmlElement::jsElement("$this->jsOut_fieldTypesByName
	var date_format_dateonly = '" . g_l('date','[format][mysqlDate]') . "';
	var fieldDate = new weDate(date_format_dateonly);

	function showDatePickerIcon(fieldNr) {
		document.getElementsByName('value_'+fieldNr)[0].style.display = 'none';
		document.getElementsByName('value_date_'+fieldNr)[0].style.display = '';
		document.getElementById('date_picker_'+fieldNr).style.display = '';
		document.getElementById('dpzell_'+fieldNr).style.display = '';
	}

	function hideDatePickerIcon(fieldNr) {
		document.getElementsByName('value_'+fieldNr)[0].style.display = '';
		document.getElementsByName('value_date_'+fieldNr)[0].style.display = 'none';
		document.getElementById('date_picker_'+fieldNr).style.display = 'none';
		document.getElementById('dpzell_'+fieldNr).style.display = 'none';
	}

	function isDateField(fieldNr){
		selBranch = document.getElementsByName('branch_'+fieldNr)[0].value;
		selField  = document.getElementsByName('field_'+fieldNr)[0].value;
		selField  = selField.substring(selBranch.length+1,selField.length);
		if(fieldTypesByName[selField] == 'date') showDatePickerIcon(fieldNr);
		else hideDatePickerIcon(fieldNr);
	}

	function lookForDateFields(){
		for(i = 0; i < document.getElementsByName('count')[0].value; i++){
			selBranch = document.getElementsByName('branch_'+i)[0].value;
			selField  = document.getElementsByName('field_'+i)[0].value;
			selField  = selField.substring(selBranch.length+1,selField.length);
			if(fieldTypesByName[selField] == 'date') {
				if(document.getElementsByName('value_'+i)[0].value != '') {
					document.getElementById('value_date_'+i).value = fieldDate.timestempToDate(document.getElementsByName('value_'+i)[0].value);

				}
				showDatePickerIcon(i);
			}
			Calendar.setup({inputField:'value_date_'+i,ifFormat:date_format_dateonly,button:'date_picker_'+i,align:'Tl',singleClick:true});
		}
	}

	function transferDateFields() {
		for(i = 0; i < document.getElementsByName('count')[0].value; i++){
			selBranch = document.getElementsByName('branch_'+i)[0].value;
			selField  = document.getElementsByName('field_'+i)[0].value;
			selField  = selField.substring(selBranch.length+1,selField.length);
			if(fieldTypesByName[selField] == 'date' && document.getElementById('value_date_'+i).value != '') {
				document.getElementsByName('value_'+i)[0].value = fieldDate.dateToTimestemp(document.getElementById('value_date_'+i).value);
			}
		}
	}
	"
										) .
										we_htmlElement::htmlForm(array("name" => "we_form"),
														$hiddens .
														htmlDialogLayout(
																		$table->getHtmlCode(),
																		g_l('modules_customer','[search]'),
																		we_button::position_yes_no_cancel(null, we_button::create_button("close", "javascript:self.close();")),
																		"100%",
																		"30",
																		"558"
														)
										) .
										((isset($_REQUEST['mode']) && $_REQUEST['mode']) ? we_htmlElement::jsElement("
	setTimeout('lookForDateFields()', 1);
					") : "")
		);
		return $this->getHTMLDocument($out);
	}

	function getHTMLSettings() {
		$closeflag = false;

		if (isset($_REQUEST["cmd"])) {
			if ($_REQUEST["cmd"] == "save_settings") {
				$this->View->processCommands();
				$closeflag = true;
			}
		}

		$default_sort_view_select = $this->getHTMLSortSelect();
		$default_sort_view_select->setAttributes(array("name" => "default_sort_view", "style", "width:200px"));
		$default_sort_view_select->selectOption($this->View->settings->getSettings('default_sort_view'));

		$table = new we_htmlTable(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 5, 3);

		$table->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_customer','[default_sort_view]') . ":&nbsp;");
		$table->setCol(0, 1, array(), getPixel(5, 30));
		$table->setCol(0, 2, array("class" => "defaultfont"), $default_sort_view_select->getHtmlCode());

		$table->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_customer','[start_year]') . ":&nbsp;");
		$table->setCol(1, 1, array(), getPixel(5, 30));
		$table->setCol(1, 2, array("class" => "defaultfont"), htmlTextInput("start_year", 32, $this->View->settings->getSettings('start_year'), ""));

		$table->setCol(2, 0, array("class" => "defaultfont"), g_l('modules_customer','[treetext_format]') . ":&nbsp;");
		$table->setCol(2, 1, array(), getPixel(5, 30));
		$table->setCol(2, 2, array("class" => "defaultfont"), htmlTextInput("treetext_format", 32, $this->View->settings->getSettings('treetext_format'), ""));


		$default_order = new we_htmlSelect(array('name' => 'default_order', 'style' => 'width:250px;', 'class' => 'weSelect'));
		$default_order->addOption('', g_l('modules_customer','[none]'));
		foreach ($this->View->settings->OrderTable as $ord) {
			$ordval = ($ord == 'ASC') ? g_l('modules_customer','[ASC]') : g_l('modules_customer','[DESC]');
			$default_order->addOption($ord, $ordval);
		}
		$default_order->selectOption($this->View->settings->getSettings('default_order'));

		$table->setCol(3, 0, array('class' => 'defaultfont'), g_l('modules_customer','[default_order]') . ':&nbsp;');
		$table->setCol(3, 1, array(), getPixel(5, 30));
		$table->setCol(3, 2, array('class' => 'defaultfont'), $default_order->getHtmlCode());

		$default_saveRegisteredUser_register = new we_htmlSelect(array('name' => 'default_saveRegisteredUser_register', 'style' => 'width:250px;', 'class' => 'weSelect'));
		$default_saveRegisteredUser_register->addOption('false', 'false');
		$default_saveRegisteredUser_register->addOption('true', 'true');
		$default_saveRegisteredUser_register->selectOption($this->View->settings->getPref('default_saveRegisteredUser_register'));

		$table->setCol(4, 0, array('class' => 'defaultfont'), '&lt;we:saveRegisteredUser register=&quot;');
		$table->setCol(4, 1, array(), getPixel(5, 30));
		$table->setCol(4, 2, array('class' => 'defaultfont'), $default_saveRegisteredUser_register->getHtmlCode().'&quot;/>');

		$close = we_button::create_button("close", "javascript:self.close();");
		$save = we_button::create_button("save", "javascript:we_cmd('save_settings')");

		$body = we_htmlElement::htmlBody(array("class" => "weDialogBody"),
										we_htmlElement::htmlForm(array("name" => "we_form"),
														htmlDialogLayout(
																		we_htmlElement::htmlHidden(array("name" => "pnt", "value" => "settings")) .
																		we_htmlElement::htmlHidden(array("name" => "cmd", "value" => "")) .
																		$table->getHtmlCode() .
																		getPixel(5, 10),
																		g_l('modules_customer','[settings]'),
																		we_button::position_yes_no_cancel($save, $close)
														)
										)
										. ($closeflag ? we_htmlElement::jsElement('top.close();') : "")
		);

		return $this->getHTMLDocument($body, we_htmlElement::jsElement($this->View->getJSSettings()));
	}

	function getDateInput2($name, $time="", $setHot=false, $format="", $onchange="", $class="defaultfont", $from_year=1970) {
		// removed attribute setHot

		if (is_array($time)) {
			$day = isset($time["day"]) ? $time["day"] : date("d");
			$month = isset($time["month"]) ? $time["month"] : date("m");
			$year = isset($time["year"]) ? $time["year"] : date("Y");
			$hour = isset($time["hours"]) ? $time["hours"] : date("H");
			$minute = isset($time["minutes"]) ? $time["minutes"] : date("i");
		} else {
			return "";
		}

		$name = ereg_replace('^(.+)]$', '\1%s]', $name);
		if (($format == '') || we_getDayPos($format) != -1) {
			$daySelect = '<select class="weSelect" name="' . sprintf($name, "_day") . '" size="1" onChange="' . $onchange . '">';
			for ($i = 1; $i <= 31; $i++) {
				$daySelect .= '<option' . ($time ? (($day == $i) ? ' selected="selected"' : '') : '') . '>' . sprintf("%02d", $i) . '</option>';
			}
			$daySelect .= '</select>';
		} else {
			$daySelect = '';
		}

		if (($format == '') || we_getMonthPos($format) != -1) {
			$monthSelect = '<select class="weSelect" name="' . sprintf($name, "_month") . '" size="1" onChange="' . $onchange . '">';
			for ($i = 1; $i <= 12; $i++) {
				$monthSelect .= '<option' . ($time ? (($month == $i) ? ' selected="selected"' : '') : '') . '>' . sprintf("%02d", $i) . '</option>';
			}
			$monthSelect .= '</select>';
		} else {
			$monthSelect = '';
		}

		if (($format == '') || we_getYearPos($format) != -1) {
			$yearSelect = '<select class="weSelect" name="' . sprintf($name, "_year") . '" size="1" onChange="' . $onchange . '">';
			for ($i = $from_year; $i <= abs(date("Y") + 100); $i++) { //Temp-Fix 5471
				$yearSelect .= '<option' . ($time ? (($year == $i) ? ' selected="selected"' : '') : '') . '>' . sprintf("%04d", $i) . '</option>';
			}
			$yearSelect .= '</select>';
		} else {
			$yearSelect = '';
		}

		if (($format == '') || we_getHourPos($format) != -1) {
			$hourSelect = '<select class="weSelect" name="' . sprintf($name, "_hour") . '" size="1" onChange="' . $onchange . '">';
			for ($i = 0; $i <= 23; $i++) {
				$hourSelect .= '<option' . ($time ? (($hour == $i) ? ' selected="selected"' : '') : '') . '>' . sprintf("%02d", $i) . '</option>';
			}
			$hourSelect .= '</select>';
		} else {
			$hourSelect = '';
		}

		if (($format == '') || we_getMinutePos($format) != -1) {
			$minSelect = '<select class="weSelect" name="' . sprintf($name, "_minute") . '" size="1" onChange="' . $onchange . '">';
			for ($i = 0; $i <= 59; $i++) {
				$minSelect .= '<option' . ($time ? (($minute == $i) ? ' selected="selected"' : '') : '') . '>' . sprintf("%02d", $i) . '</option>';
			}
			$minSelect .= '</select>';
		} else {
			$minSelect = '';
		}


		$retVal = '<table cellpadding=0 cellspacing=0 border=0>
';
		if ($daySelect || $monthSelect || $yearSelect) {
			$retVal .= '<tr>
	<td>
		' . ($daySelect ? $daySelect . "&nbsp;" : hidden(sprintf($name, "_day"), $day)) .
							($monthSelect ? $monthSelect . "&nbsp;" : hidden(sprintf($name, "_month"), $month)) .
							($yearSelect ? $yearSelect . "&nbsp;" : hidden(sprintf($name, "_year"), $year)) . '
	</td>
</tr>
';
		} else {
			$retVal .= hidden(sprintf($name, "_day"), $day) .
							hidden(sprintf($name, "_month"), $month) .
							hidden(sprintf($name, "_year"), $year) . '
';
		}
		if ($hourSelect || $minSelect) {
			$retVal .= '<tr>
	<td>
		' . ($hourSelect ? $hourSelect . "&nbsp;" : hidden(sprintf($name, "_hour"), $hour)) .
							($minSelect ? $minSelect . "&nbsp;" : hidden(sprintf($name, "_minute"), $minute)) . '
	</td>
</tr>
';
		} else {
			$retVal .= hidden(sprintf($name, "_hour"), (isset($hour) ? $hour : 0)) .
							hidden(sprintf($name, "_minute"), (isset($minute) ? $minute : 0)) . '
';
		}
		$retVal .= '</table>
	';
		return $retVal;
	}

}
