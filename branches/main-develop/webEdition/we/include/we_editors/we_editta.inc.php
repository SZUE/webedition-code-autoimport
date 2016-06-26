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
we_html_tools::protect();


$nr = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2);
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', $we_transaction, 3);

$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : "";
$we_doc = we_document::initDoc('', $we_dt);

if(we_base_request::_(we_base_request::BOOL, "ok")){
	$we_doc->setElement($name . 'inlineedit', (we_base_request::_(we_base_request::BOOL, 'inlineedit') ? 'on' : 'off'));
	$we_doc->setElement($name . 'forbidphp', (we_base_request::_(we_base_request::BOOL, 'forbidphp') ? 'on' : 'off'));
	$we_doc->setElement($name . 'forbidhtml', (we_base_request::_(we_base_request::BOOL, 'forbidhtml') ? 'on' : 'off'));
	$we_doc->setElement($name . 'removefirstparagraph', (we_base_request::_(we_base_request::BOOL, 'removefirstparagraph') ? 'on' : 'off'));
	$we_doc->setElement($name . 'xml', (we_base_request::_(we_base_request::BOOL, 'xml') ? 'on' : 'off'));
	$we_doc->setElement($name . 'dhtmledit', (we_base_request::_(we_base_request::BOOL, 'dhtmledit') ? 'on' : 'off'));
	$we_doc->setElement($name . 'showmenus', (we_base_request::_(we_base_request::BOOL, 'showmenus') ? 'on' : 'off'));
	$we_doc->setElement($name . 'commands', we_base_request::_(we_base_request::STRING, 'commands'));
	$we_doc->setElement($name . 'contextmenu', we_base_request::_(we_base_request::STRING, 'contextmenu'));
	$we_doc->setElement($name . 'height', we_base_request::_(we_base_request::INT, 'height', 50));
	$we_doc->setElement($name . 'width', we_base_request::_(we_base_request::INT, 'width', 200));
	$we_doc->setElement($name . 'bgcolor', we_base_request::_(we_base_request::STRING, 'bgcolor', ''));
	$we_doc->setElement($name . 'class', we_base_request::_(we_base_request::STRING, 'class', ''));
	$we_doc->setElement($name . 'cssClasses', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'cssClasses', [])));
	$we_doc->setElement($name . 'fontnames', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'fontnames', [])));
	$we_doc->setElement($name . 'fontsizes', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'fontsizes', [])));
	$we_doc->setElement($name . 'formats', implode(',', we_base_request::_(we_base_request::STRING_LIST, 'formats', [])));
	$we_doc->setElement($name . 'tinyparams', we_base_request::_(we_base_request::RAW_CHECKED, 'tinyparams', ''));
	$we_doc->setElement($name . 'templates', we_base_request::_(we_base_request::INTLIST, 'templates', ''));
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

	$js = 'opener._EditorFrame.setEditorIsHot(true);'
		. ((we_base_browserDetect::isIE() || we_base_browserDetect::isOpera()) &&
		$we_doc->getElement($name . 'dhtmledit') === 'on' &&
		$we_doc->getElement($name . 'inlineedit') === 'on' ? 'opener.setScrollTo();opener.we_cmd("switch_edit_page",1,"' . $we_transaction . '");' :
			'opener.we_cmd("object_reload_entry_at_class","' . $we_transaction . '", "' . $nr . '");')
		. 'top.close();';
} else {
	$js = 'function okFn(){'
		. 'document.forms[0].submit();'
		. '}';
}

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::jsElement($js) . STYLESHEET);

$out = '<body onload="top.focus();" class="weDialogBody"><form name="we_form" method="post" action="' . $_SERVER['SCRIPT_NAME'] . '">' . we_html_element::htmlHidden('ok', 1);

foreach($_REQUEST['we_cmd'] as $k => $v){
	$out .= we_html_element::htmlHidden('we_cmd[' . $k . ']', $v);
}

// WYSIWYG && FORBIDHTML && FORBIDPHP
$onOffVals = array('off' => 'false', 'on' => 'true');
$selected = $we_doc->getElement($name . "dhtmledit") === "on" ? 'on' : 'off';
$wysiwyg = we_html_tools::htmlSelect("dhtmledit", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60) . we_html_element::htmlHidden('dhtmledit_orig', $we_doc->elements[$name . "dhtmledit"]["dat"]);

$selected = $we_doc->getElement($name . "forbidhtml") === "on" ? 'on' : 'off';
$forbidhtml = we_html_tools::htmlSelect("forbidhtml", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$selected = $we_doc->getElement($name . "forbidphp", "dat", 'on') === "on" ? 'on' : 'off';
$forbidphp = we_html_tools::htmlSelect("forbidphp", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$table = '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right">wysiwyg&nbsp;</td><td>' . $wysiwyg . '</td>
		<td class="defaultfont" style="text-align:right">forbidphp&nbsp;</td><td>' . $forbidphp . '</td>
		<td class="defaultfont" style="text-align:right">forbidhtml&nbsp;</td><td>' . $forbidhtml . '</td>
	</tr>
</table>';
$parts = array(
	array(
		"headline" => "",
		"html" => $table,
	)
);

// XML && REMOVEFIRSTPARAGRAPH
$selected = $we_doc->getElement($name . "xml", "dat", 'on') === "on" ? 'on' : 'off';
$xml = we_html_tools::htmlSelect("xml", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$selected = $we_doc->getElement($name . "removefirstparagraph", "dat", 'on') === "on" ? 'on' : 'off';
$removefirstparagraph = we_html_tools::htmlSelect("removefirstparagraph", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$table = '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right">xml&nbsp;</td><td>' . $xml . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
		<td class="defaultfont" style="text-align:right">removefirstparagraph&nbsp;</td><td>' . $removefirstparagraph . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);


// INLINEEDIT && SHOWMENUS
$selected = $we_doc->getElement($name . "inlineedit", "dat", 'on') === "on" ? 'on' : 'off';
$inlineedit = we_html_tools::htmlSelect("inlineedit", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60) . we_html_element::htmlHidden('inlineedit_orig', $we_doc->elements[$name . "inlineedit"]["dat"]);

$selected = $we_doc->getElement($name . "showmenus", "dat", 'on') === "on" ? 'on' : 'off';
$showmenus = we_html_tools::htmlSelect("showmenus", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$table = '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right">inlineedit&nbsp;</td><td>' . $inlineedit . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
		<td class="defaultfont" style="text-align:right">showmenus&nbsp;</td><td>' . $showmenus . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);


// WIDTH & HEIGHT & BGCOLOR
$table = '<table class="default">
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<colgroup><col style="width:70px;"/><col style="width:60px;"/><col style="width:95px;"/><col style="width:60px;"/><col style="width:140px;"/><col style="width:60px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="text-align:right;padding-bottom:6px;">width&nbsp;</td><td>' . we_html_tools::htmlTextInput('width', 24, $we_doc->getElement($name . "width"), 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right">height&nbsp;</td><td>' . we_html_tools::htmlTextInput('height', 24, $we_doc->getElement($name . "height"), 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right">bgcolor&nbsp;</td><td>' . we_html_tools::htmlTextInput('bgcolor', 24, $we_doc->getElement($name . "bgcolor"), 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
	</tr>
	<tr>
		<td class="defaultfont" style="text-align:right">class&nbsp;</td><td>' . we_html_tools::htmlTextInput('class', 24, $we_doc->getElement($name . "class"), 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" style="text-align:right"></td><td></td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

// COMMANDS && CONTEXTMENU
$select = we_html_tools::htmlSelect('tmp_commands', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, array('onchange' => "var elem=document.getElementById('commands'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));
$select_cm = we_html_tools::htmlSelect('tmp_contextmenu', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, array('onchange' => "var elem=document.getElementById('contextmenu'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));

$table = '<table class="default">
	<tr class="withBigSpace">
		<td class="defaultfont" style="vertical-align:top;text-align:right;width:90px;">commands&nbsp;</td><td colspan="5">' . $select . '<br/>' . we_class::htmlTextArea("commands", 3, 30, oldHtmlspecialchars($we_doc->getElement($name . "commands")), array('id' => "commands", 'style' => "width:392px;height:50px")) . '</td>	</tr>
	<tr>
		<td class="defaultfont" valign="top" align="right">contextmenu&nbsp;</td><td colspan="5">' . $select_cm . '<br/>' . we_class::htmlTextArea("contextmenu", 3, 30, oldHtmlspecialchars($we_doc->getElement($name . "contextmenu")), array('id' => "contextmenu", 'style' => "width:392px;height:50px")) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

// FONTNAMES
$select = we_html_tools::htmlSelect('tmp_fontnames', we_wysiwyg_editor::getAttributeOptions('fontnames'), 1, "", false, array('onchange' => "var elem=document.we_form.fontnames; var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));
$table = '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px;">fontnames&nbsp;</td><td colspan="5">' . $select . '<br/>' . we_html_tools::htmlTextInput('fontnames', 24, $we_doc->getElement($name . 'fontnames'), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

// FONTNAMES
$select = we_html_tools::htmlSelect('tmp_fontsizes', we_wysiwyg_editor::getAttributeOptions('fontsizes'), 1, "", false, array('onchange' => "var elem=document.we_form.fontsizes; var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));
$table = '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px">fontsizes&nbsp;</td><td colspan="5">' . $select . '<br/>' . we_html_tools::htmlTextInput('fontsizes', 24, $we_doc->getElement($name . 'fontsizes'), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

// FORMATS
$select = we_html_tools::htmlSelect('tmp_formats', we_wysiwyg_editor::getAttributeOptions('formats'), 1, "", false, array('onchange' => "var elem=document.we_form.formats; var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));
$table = '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px;">formats&nbsp;</td><td colspan="5">' . $select . '<br/>' . we_html_tools::htmlTextInput('formats', 24, $we_doc->getElement($name . 'formats'), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

// CLASSES
$table = '<table cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right" style="width:90px;">classes&nbsp;</td><td colspan="5">' . we_html_tools::htmlTextInput('cssClasses', 24, oldHtmlspecialchars($we_doc->getElement($name . "cssClasses")), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>';

$parts[] = array(
	'headline' => '',
	'html' => $table,
);

// TINYPARAMS
$table = '<table class="default">
	<colgroup><col style="width:90px;"/><col style="width:395px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="vertical-align:top;text-align:right">tinyparams&nbsp;</td><td colspan="5">' . we_html_tools::htmlTextInput('tinyparams', 24, $we_doc->getElement($name . "tinyparams"), 1024, '', 'text', 350, 0) . '</td>

	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

// TINY-TEMPLATES
$table = '<table class="default">
	<colgroup><col style="width:90px;"/><col style="width:395px;"/></colgroup>
	<tr>
		<td class="defaultfont" style="vertical-align:top;text-align:right">templates&nbsp;</td>
		<td colspan="5">' . we_html_tools::htmlTextInput('templates', 24, $we_doc->getElement($name . "templates"), 1024, '', 'text', 396, 0) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
);

$cancel_button = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");
$okbut = we_html_button::create_button(we_html_button::OK, "javascript:okFn();");
$buttons = we_html_button::position_yes_no_cancel($okbut, null, $cancel_button);
$out .= we_html_multiIconBox::getHTML("", $parts, 30, $buttons, -1, "", "", "", g_l('modules_object', '[textarea_field]') . ' "' . $we_doc->getElement($name) . '" - ' . g_l('modules_object', '[attributes]')) .
	'</form></body></html>';

echo $out;
