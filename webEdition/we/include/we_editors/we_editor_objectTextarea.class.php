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
class we_editor_objectTextarea extends we_editor_base{
	private $name = '';

	public function __construct(we_contents_root $we_doc, $we_transaction){
		parent::__construct($we_doc);
		$nr = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
		$this->name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);

		if(we_base_request::_(we_base_request::BOOL, "ok")){
			$this->we_doc->setElement($this->name . 'inlineedit', (we_base_request::_(we_base_request::BOOL, 'inlineedit') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'forbidphp', (we_base_request::_(we_base_request::BOOL, 'forbidphp') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'forbidhtml', (we_base_request::_(we_base_request::BOOL, 'forbidhtml') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'removefirstparagraph', (we_base_request::_(we_base_request::BOOL, 'removefirstparagraph') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'xml', (we_base_request::_(we_base_request::BOOL, 'xml') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'dhtmledit', (we_base_request::_(we_base_request::BOOL, 'dhtmledit') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'showmenus', (we_base_request::_(we_base_request::BOOL, 'showmenus') ? 'on' : 'off'));
			$this->we_doc->setElement($this->name . 'commands', we_base_request::_(we_base_request::STRING, 'commands'));
			$this->we_doc->setElement($this->name . 'contextmenu', we_base_request::_(we_base_request::STRING, 'contextmenu'));
			$this->we_doc->setElement($this->name . 'height', we_base_request::_(we_base_request::INT, 'height', 50));
			$this->we_doc->setElement($this->name . 'width', we_base_request::_(we_base_request::INT, 'width', 200));
			$this->we_doc->setElement($this->name . 'bgcolor', we_base_request::_(we_base_request::STRING, 'bgcolor', ''));
			$this->we_doc->setElement($this->name . 'class', we_base_request::_(we_base_request::STRING, 'class', ''));
			$this->we_doc->setElement($this->name . 'cssClasses', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'cssClasses', [])));
			$this->we_doc->setElement($this->name . 'fontnames', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'fontnames', [])));
			$this->we_doc->setElement($this->name . 'fontsizes', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'fontsizes', [])));
			$this->we_doc->setElement($this->name . 'formats', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'formats', [])));
			$this->we_doc->setElement($this->name . 'tinyparams', we_base_request::_(we_base_request::RAW_CHECKED, 'tinyparams', ''));
			$this->we_doc->setElement($this->name . 'templates', we_base_request::_(we_base_request::INTLIST, 'templates', ''));
			$this->we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
			$this->jsCmd->addCmd('object_TextArea_apply');

			if((we_base_browserDetect::isIE() || we_base_browserDetect::isOpera()) &&
				$this->elStatus('dhtmledit') === 'on' &&
				$this->elStatus('inlineedit') === 'on'){
				$this->jsCmd->addCmd('object_switch_edit_page', 1, $we_transaction);
			} else {
				$this->jsCmd->addCmd('object_reload_entry_at_class', '', $we_transaction, $nr);
			}
			$this->jsCmd->addCmd('close');
		}
	}

	function elStatus($type){
		return $this->we_doc->getElement($this->name . $type, 'dat', 'on') === 'on' ? 'on' : 'off';
	}

	public function show(){

// WYSIWYG && FORBIDHTML && FORBIDPHP
		$onOffVals = ['off' => 'false', 'on' => 'true'];

		$parts = [
			["headline" => "",
				"html" => '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right">wysiwyg&nbsp;</td><td>' . we_html_tools::htmlSelect("dhtmledit", $onOffVals, 1, $this->elStatus("dhtmledit"), false, [
					'class' => "defaultfont"], 'value', 60) . we_html_element::htmlHidden('dhtmledit_orig', $this->we_doc->elements[$this->name . "dhtmledit"]["dat"]) . '</td>
		<td class="defaultfont" style="text-align:right">forbidphp&nbsp;</td><td>' . we_html_tools::htmlSelect("forbidphp", $onOffVals, 1, $this->elStatus("forbidphp"), false, [
					'class' => "defaultfont"], 'value', 60) . '</td>
		<td class="defaultfont" style="text-align:right">forbidhtml&nbsp;</td><td>' . we_html_tools::htmlSelect("forbidhtml", $onOffVals, 1, $this->elStatus("forbidhtml"), false, [
					'class' => "defaultfont"], 'value', 60) . '</td>
	</tr>
</table>',
			], [// XML && REMOVEFIRSTPARAGRAPH
				"headline" => "",
				"html" => '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right">xml&nbsp;</td><td>' . we_html_tools::htmlSelect("xml", $onOffVals, 1, $this->elStatus("xml"), false, ['class' => "defaultfont"], 'value', 60) . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
		<td class="defaultfont" style="text-align:right">removefirstparagraph&nbsp;</td><td>' . we_html_tools::htmlSelect("removefirstparagraph", $onOffVals, 1, $this->elStatus('removefirstparagraph'), false, [
					'class' => "defaultfont"], 'value', 60) . '</td>
	</tr>
</table>',
			], [// INLINEEDIT && SHOWMENUS
				"headline" => "",
				"html" => '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right">inlineedit&nbsp;</td><td>' . we_html_tools::htmlSelect("inlineedit", $onOffVals, 1, $this->elStatus("inlineedit"), false, [
					'class' => "defaultfont"], 'value', 60) . we_html_element::htmlHidden('inlineedit_orig', $this->we_doc->elements[$this->name . "inlineedit"]["dat"]) . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
		<td class="defaultfont" style="text-align:right">showmenus&nbsp;</td><td>' . we_html_tools::htmlSelect("showmenus", $onOffVals, 1, $this->elStatus("showmenus"), false, [
					'class' => "defaultfont"], 'value', 60) . '</td>
	</tr>
</table>',
			], [// WIDTH & HEIGHT & BGCOLOR
				"headline" => "",
				"html" => '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right;padding-bottom:6px;">width&nbsp;</td><td>' . we_html_tools::htmlTextInput('width', 24, $this->we_doc->getElement($this->name . "width"), 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right">height&nbsp;</td><td>' . we_html_tools::htmlTextInput('height', 24, $this->we_doc->getElement($this->name . "height"), 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right">bgcolor&nbsp;</td><td>' . we_html_tools::htmlTextInput('bgcolor', 24, $this->we_doc->getElement($this->name . "bgcolor"), 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
	</tr>
	<tr>
		<td class="defaultfont" style="text-align:right">class&nbsp;</td><td>' . we_html_tools::htmlTextInput('class', 24, $this->we_doc->getElement($this->name . "class"), 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
	</tr>
</table>',
			], [// COMMANDS && CONTEXTMENU
				"headline" => "",
				"html" => '<table class="default">
	<tr class="withBigSpace">
		<td class="defaultfont" style="vertical-align:top;text-align:right;width:90px;">commands&nbsp;</td><td colspan="5">' . we_html_tools::htmlSelect('tmp_commands', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, [
					'onchange' => "var elem=document.getElementById('commands'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"]) . '<br/>' . we_contents_base::htmlTextArea("commands", 3, 30, oldHtmlspecialchars($this->we_doc->getElement($this->name . "commands")), [
					'id' => "commands", 'style' => "width:392px;height:50px"]) . '</td>	</tr>
	<tr>
		<td class="defaultfont" valign="top" align="right">contextmenu&nbsp;</td><td colspan="5">' . we_html_tools::htmlSelect('tmp_contextmenu', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, [
					'onchange' => "var elem=document.getElementById('contextmenu'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"]) . '<br/>' . we_contents_base::htmlTextArea("contextmenu", 3, 30, oldHtmlspecialchars($this->we_doc->getElement($this->name . "contextmenu")), [
					'id' => "contextmenu", 'style' => "width:392px;height:50px"]) . '</td>
	</tr>
</table>',
			], [// FONTNAMES
				"headline" => "",
				"html" => '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px;">fontnames&nbsp;</td><td colspan="5">' .
				we_html_tools::htmlSelect('tmp_fontnames', we_wysiwyg_editor::getAttributeOptions('fontnames'), 1, "", false, ['onchange' => "var elem=document.we_form.fontnames; var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"]) . '<br/>' . we_html_tools::htmlTextInput('fontnames', 24, $this->we_doc->getElement($this->name . 'fontnames'), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>',
			], [// FONTSIZE
				"headline" => "",
				"html" => '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px">fontsizes&nbsp;</td><td colspan="5">' . we_html_tools::htmlSelect('tmp_fontsizes', we_wysiwyg_editor::getAttributeOptions('fontsizes'), 1, "", false, [
					'onchange' => "var elem=document.we_form.fontsizes; var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"]) . '<br/>' . we_html_tools::htmlTextInput('fontsizes', 24, $this->we_doc->getElement($this->name . 'fontsizes'), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>',
			], [// FORMATS
				"headline" => "",
				"html" => '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px;">formats&nbsp;</td><td colspan="5">' . we_html_tools::htmlSelect('tmp_formats', we_wysiwyg_editor::getAttributeOptions('formats'), 1, "", false, [
					'onchange' => "var elem=document.we_form.formats; var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"]) . '<br/>' . we_html_tools::htmlTextInput('formats', 24, $this->we_doc->getElement($this->name . 'formats'), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>',
			], [// CLASSES
				'headline' => '',
				'html' => '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px;">classes&nbsp;</td><td colspan="5">' . we_html_tools::htmlTextInput('cssClasses', 24, oldHtmlspecialchars($this->we_doc->getElement($this->name . "cssClasses")), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>',
			], [// TINYPARAMS
				'headline' => '',
				'html' => '<table class="default">
	<colgroup><col style="width:90px;"/><col style="width:395px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="vertical-align:top;text-align:right">tinyparams&nbsp;</td><td colspan="5">' . we_html_tools::htmlTextInput('tinyparams', 24, $this->we_doc->getElement($this->name . "tinyparams"), 1024, '', 'text', 350, 0) . '</td>

	</tr>
</table>',
			], [// TINY-TEMPLATES
				"headline" => "",
				"html" => '<table class="default">
	<colgroup><col style="width:90px;"/><col style="width:395px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="vertical-align:top;text-align:right">templates&nbsp;</td>
		<td colspan="5">' . we_html_tools::htmlTextInput('templates', 24, $this->we_doc->getElement($this->name . "templates"), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>',
			]
		];

		$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");
		$okbut = we_html_button::create_button(we_html_button::OK, "javascript:document.forms[0].submit();");
		$buttons = we_html_button::position_yes_no_cancel($okbut, null, $cancel_button);
		$out = we_html_element::htmlHidden('ok', 1);

		foreach($_REQUEST['we_cmd'] as $k => $v){
			$out .= we_html_element::htmlHidden('we_cmd[' . $k . ']', $v);
		}

		$out .= we_html_multiIconBox::getHTML("", $parts, 30, $buttons, -1, "", "", "", g_l('modules_object', '[textarea_field]') . ' "' . $this->we_doc->getElement($this->name) . '" - ' . g_l('modules_object', '[attributes]'));

		return $this->getPage($out, '', [
				'onload' => "top.focus();",
				'class' => "weDialogBody"], [
				'action' => $_SERVER['SCRIPT_NAME']
		]);
	}

}
