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
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

if(we_base_request::_(we_base_request::BOOL, "ok")){
	$we_doc->elements[$name . 'inlineedit']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'inlineedit');
	$we_doc->elements[$name . 'forbidphp']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'forbidphp');
	$we_doc->elements[$name . 'forbidhtml']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'forbidhtml');
	$we_doc->elements[$name . 'removefirstparagraph']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'removefirstparagraph');
	$we_doc->elements[$name . 'xml']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'xml');
	$we_doc->elements[$name . 'dhtmledit']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'dhtmledit');
	$we_doc->elements[$name . 'showmenus']['dat'] = we_base_request::_(we_base_request::TOGGLE, 'showmenus');
	$we_doc->elements[$name . 'commands']['dat'] = we_base_request::_(we_base_request::RAW, 'commands');
	$we_doc->elements[$name . 'contextmenu']['dat'] = we_base_request::_(we_base_request::RAW, 'contextmenu');
	$we_doc->elements[$name . 'height']['dat'] = we_base_request::_(we_base_request::INT, 'height', 50);
	$we_doc->elements[$name . 'width']['dat'] = we_base_request::_(we_base_request::INT, 'width', 200);
	$we_doc->elements[$name . 'bgcolor']['dat'] = we_base_request::_(we_base_request::STRING, 'bgcolor', '');
	$we_doc->elements[$name . 'class']['dat'] = we_base_request::_(we_base_request::STRING, 'class', '');
	$we_doc->elements[$name . 'cssClasses']['dat'] = we_base_request::_(we_base_request::RAW, 'cssClasses', '');
	$we_doc->elements[$name . 'tinyparams']['dat'] = we_base_request::_(we_base_request::RAW, 'tinyparams', '');
	$we_doc->elements[$name . 'templates']['dat'] = we_base_request::_(we_base_request::RAW, 'templates', '');
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

	$js = 'opener._EditorFrame.setEditorIsHot(true);'
		. ((we_base_browserDetect::isIE() || we_base_browserDetect::isOpera()) &&
		$we_doc->elements[$name . 'dhtmledit']['dat'] == 'on' &&
		$we_doc->elements[$name . 'inlineedit']['dat'] == 'on' ? 'opener.setScrollTo();opener.we_cmd("switch_edit_page",1,"' . $we_transaction . '");' :
			'opener.we_cmd("object_reload_entry_at_class","' . $we_transaction . '", "' . $nr . '");')
		. 'top.close();';
} else {
	$js = 'function okFn(){'
		. 'document.forms[0].submit();'
		. '}';
}

echo we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead(//FIXME: missing title
		we_html_tools::getHtmlInnerHead() . we_html_element::jsElement($js) . STYLESHEET), false);

$out = '<body onload="top.focus();" class="weDialogBody"><form name="we_form" method="post" action="' . $_SERVER['SCRIPT_NAME'] . '">' . we_html_tools::hidden('ok', 1);

foreach($_REQUEST['we_cmd'] as $k => $v){
	$out .= '<input type="hidden" name="we_cmd[' . $k . ']" value="' . $v . '" />';
}

// WYSIWYG && FORBIDHTML && FORBIDPHP
$onOffVals = array('off' => 'false', 'on' => 'true');
$selected = (isset($we_doc->elements[$name . "dhtmledit"]) && isset($we_doc->elements[$name . "dhtmledit"]["dat"]) && $we_doc->elements[$name . "dhtmledit"]["dat"] == "on") ? 'on' : 'off';
$wysiwyg = we_html_tools::htmlSelect("dhtmledit", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60) . we_html_tools::hidden('dhtmledit_orig', $we_doc->elements[$name . "dhtmledit"]["dat"]);

$selected = (isset($we_doc->elements[$name . "forbidhtml"]) && isset($we_doc->elements[$name . "forbidhtml"]["dat"]) && $we_doc->elements[$name . "forbidhtml"]["dat"] == "on") ? 'on' : 'off';
$forbidhtml = we_html_tools::htmlSelect("forbidhtml", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$selected = ( (!isset($we_doc->elements[$name . "forbidphp"]["dat"])) || $we_doc->elements[$name . "forbidphp"]["dat"] == "on" ? 'on' : 'off');
$forbidphp = we_html_tools::htmlSelect("forbidphp", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">wysiwyg&nbsp;</td><td>' . $wysiwyg . '</td>
		<td class="defaultfont" align="right">forbidphp&nbsp;</td><td>' . $forbidphp . '</td>
		<td class="defaultfont" align="right">forbidhtml&nbsp;</td><td>' . $forbidhtml . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';
$parts = array(
	array(
		"headline" => "",
		"html" => $table,
		"space" => 0,
	)
);

// XML && REMOVEFIRSTPARAGRAPH
$selected = ( (!isset($we_doc->elements[$name . "xml"]["dat"])) || $we_doc->elements[$name . "xml"]["dat"] == "on" ? 'on' : 'off');
$xml = we_html_tools::htmlSelect("xml", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$selected = ( (!isset($we_doc->elements[$name . "removefirstparagraph"]["dat"])) || $we_doc->elements[$name . "removefirstparagraph"]["dat"] == "on" ? 'on' : 'off');
$removefirstparagraph = we_html_tools::htmlSelect("removefirstparagraph", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">xml&nbsp;</td><td>' . $xml . '</td>
		<td class="defaultfont" align="right"></td><td></td>
		<td class="defaultfont" align="right">removefirstparagraph&nbsp;</td><td>' . $removefirstparagraph . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);


// INLINEEDIT && SHOWMENUS
$selected = ( (!isset($we_doc->elements[$name . "inlineedit"]["dat"])) || $we_doc->elements[$name . "inlineedit"]["dat"] == "on" ? 'on' : 'off');
$inlineedit = we_html_tools::htmlSelect("inlineedit", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60) . we_html_tools::hidden('inlineedit_orig', $we_doc->elements[$name . "inlineedit"]["dat"]);

$selected = ( (!isset($we_doc->elements[$name . "showmenus"]["dat"])) || $we_doc->elements[$name . "showmenus"]["dat"] == "on" ? 'on' : 'off');
$showmenus = we_html_tools::htmlSelect("showmenus", $onOffVals, 1, $selected, false, array('class' => "defaultfont"), 'value', 60);

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">inlineedit&nbsp;</td><td>' . $inlineedit . '</td>
		<td class="defaultfont" align="right"></td><td></td>
		<td class="defaultfont" align="right">showmenus&nbsp;</td><td>' . $showmenus . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);


// WIDTH & HEIGHT & BGCOLOR
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" align="right">width&nbsp;</td><td>' . we_html_tools::htmlTextInput('width', 24, $we_doc->elements[$name . "width"]["dat"], 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" align="right">height&nbsp;</td><td>' . we_html_tools::htmlTextInput('height', 24, $we_doc->elements[$name . "height"]["dat"], 5, '', 'number', 60, 0) . '</td>
		<td class="defaultfont" align="right">bgcolor&nbsp;</td><td>' . we_html_tools::htmlTextInput('bgcolor', 24, $we_doc->elements[$name . "bgcolor"]["dat"], 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" align="right"></td><td></td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 6) . '</td>
		<td>' . we_html_tools::getPixel(60, 6) . '</td>
		<td>' . we_html_tools::getPixel(95, 6) . '</td>
		<td>' . we_html_tools::getPixel(60, 6) . '</td>
		<td>' . we_html_tools::getPixel(140, 6) . '</td>
		<td>' . we_html_tools::getPixel(60, 6) . '</td>
	</tr>
	<tr>
		<td class="defaultfont" align="right">class&nbsp;</td><td>' . we_html_tools::htmlTextInput('class', 24, $we_doc->elements[$name . "class"]["dat"], 20, '', 'text', 60, 0) . '</td>
		<td class="defaultfont" align="right"></td><td></td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(70, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(95, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
		<td>' . we_html_tools::getPixel(140, 1) . '</td>
		<td>' . we_html_tools::getPixel(60, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);


// CLASSES
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">classes&nbsp;</td><td colspan="5">' . we_class::htmlTextArea("cssClasses", 3, 30, oldHtmlspecialchars((isset($we_doc->elements[$name . "cssClasses"]["dat"]) ? $we_doc->elements[$name . "cssClasses"]["dat"] : "")), array('style' => "width:415px;height:50px")) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(90, 1) . '</td>
		<td>' . we_html_tools::getPixel(395, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	'headline' => '',
	'html' => $table,
	'space' => 0,
);

// COMMANDS && CONTEXTMENU
$select = we_html_tools::htmlSelect('tmp_commands', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, array('onchange' => "var elem=document.getElementById('commands'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));
$select_cm = we_html_tools::htmlSelect('tmp_contextmenu', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, array('onchange' => "var elem=document.getElementById('contextmenu'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));

$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">commands&nbsp;</td><td colspan="5">' . $select . '<br/>' . we_class::htmlTextArea("commands", 3, 30, oldHtmlspecialchars((isset($we_doc->elements[$name . "commands"]["dat"]) ? $we_doc->elements[$name . "commands"]["dat"] : "")), array('id' => "commands", 'style' => "width:415px;height:50px")) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(90, 10) . '</td>
		<td>' . we_html_tools::getPixel(395, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont" valign="top" align="right">contextmenu&nbsp;</td><td colspan="5">' . $select_cm . '<br/>' . we_class::htmlTextArea("contextmenu", 3, 30, oldHtmlspecialchars((isset($we_doc->elements[$name . "contextmenu"]["dat"]) ? $we_doc->elements[$name . "contextmenu"]["dat"] : "")), array('id' => "contextmenu", 'style' => "width:415px;height:50px")) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(90, 1) . '</td>
		<td>' . we_html_tools::getPixel(395, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

// TINYPARAMS
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">tinyparams&nbsp;</td><td colspan="5">' . we_html_tools::htmlTextInput('tinyparams', 24, $we_doc->elements[$name . "tinyparams"]["dat"], 1024, '', 'text', 350, 0) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(90, 1) . '</td>
		<td>' . we_html_tools::getPixel(395, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

// TINY-TEMPLATES
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont" valign="top" align="right">templates&nbsp;</td>
		<td colspan="5">' . we_html_tools::htmlTextInput('templates', 24, $we_doc->elements[$name . "templates"]["dat"], 1024, '', 'text', 350, 0) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(90, 1) . '</td>
		<td>' . we_html_tools::getPixel(395, 1) . '</td>
	</tr>
</table>';

$parts[] = array(
	"headline" => "",
	"html" => $table,
	"space" => 0,
);

$cancel_button = we_html_button::create_button("cancel", "javascript:top.close()");
$okbut = we_html_button::create_button("ok", "javascript:okFn();");
$buttons = we_html_button::position_yes_no_cancel($okbut, null, $cancel_button);
$out .= we_html_multiIconBox::getHTML("", "100%", $parts, 30, $buttons, -1, "", "", "", g_l('modules_object', '[textarea_field]') . ' "' . $we_doc->elements[$name]['dat'] . '" - ' . g_l('modules_object', '[attributes]')) .
	'</form></body></html>';

print $out;
