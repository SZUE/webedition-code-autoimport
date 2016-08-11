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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');
we_html_tools::protect();

$oRdoSort = array(
	we_html_forms::radiobutton(0, 0, "rdo_sort", g_l('cockpit', '[by_pubdate]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(1, 0, "rdo_sort", g_l('cockpit', '[by_valid_from]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(2, 0, "rdo_sort", g_l('cockpit', '[by_valid_until]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(3, 0, "rdo_sort", g_l('cockpit', '[by_priority]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(4, 1, "rdo_sort", g_l('cockpit', '[alphabetic]'), true, "defaultfont", "", false, "", 0, "")
);

$sort = new we_html_table(array('class' => 'default'), 3, 3);
$sort->setCol(0, 0, array("width" => 145, 'style' => 'padding-right:10px;'), $oRdoSort[0]);
$sort->setCol(0, 2, array("width" => 145), $oRdoSort[3]);
$sort->setCol(1, 0, null, $oRdoSort[1]);
$sort->setCol(1, 2, null, $oRdoSort[4]);
$sort->setCol(2, 0, null, $oRdoSort[2]);

$parts = array(
	array(
		"headline" => g_l('cockpit', '[sorting]'),
		"html" => $sort->getHTML(),
		'space' => we_html_multiIconBox::SPACE_MED
	)
);

$oRdoDisplay = array(
	we_html_forms::radiobutton(0, 1, "rdo_display", g_l('cockpit', '[all_notes]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(1, 0, "rdo_display", g_l('cockpit', '[only_valid]'), true, "defaultfont", "", false, "", 0, ""),
);

$display = new we_html_table(array('class' => 'default'), 1, 3);
$display->setCol(0, 0, array("width" => 145, 'style' => 'padding-right:10px;'), $oRdoDisplay[0]);
$display->setCol(0, 2, array("width" => 145), $oRdoDisplay[1]);

$parts[] = array(
	"headline" => g_l('cockpit', '[display]'),
	"html" => $display->getHTML(),
	'space' => we_html_multiIconBox::SPACE_MED
);

$oRdoDate = array(
	we_html_forms::radiobutton(0, 1, "rdo_date", g_l('cockpit', '[by_pubdate]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(1, 0, "rdo_date", g_l('cockpit', '[by_valid_from]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(2, 0, "rdo_date", g_l('cockpit', '[by_valid_until]'), true, "defaultfont", "", false, "", 0, "")
);

$date = new we_html_table(array('class' => 'default'), 3, 1);
$date->setCol(0, 0, array(
	"width" => 145
		), $oRdoDate[0]);
$date->setCol(1, 0, null, $oRdoDate[1]);
$date->setCol(2, 0, null, $oRdoDate[2]);

$parts[] = array(
	"headline" => g_l('cockpit', '[display_date]'),
	"html" => $date->getHTML(),
	'space' => we_html_multiIconBox::SPACE_MED
);

$oRdoPrio = array(
	we_html_forms::radiobutton(0, 0, "rdo_prio", g_l('cockpit', '[high]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(1, 0, "rdo_prio", g_l('cockpit', '[medium]'), true, "defaultfont", "", false, "", 0, ""),
	we_html_forms::radiobutton(2, 1, "rdo_prio", g_l('cockpit', '[low]'), true, "defaultfont", "", false, "", 0, "")
);

$prio = new we_html_table(array('class' => 'default'), 3, 3);
$prio->setCol(0, 0, array("width" => 70, 'style' => 'padding-right:10px;'), $oRdoPrio[0]);

$prio->setCol(0, 2, array("width" => 20), '<i class="fa fa-dot-circle-o" style="color:red"></i>');
$prio->setCol(1, 0, null, $oRdoPrio[1]);
$prio->setCol(1, 2, null, '<i class="fa fa-dot-circle-o" style="color:#F2F200"></i>');
$prio->setCol(2, 0, null, $oRdoPrio[2]);
$prio->setCol(2, 2, null, '<i class="fa fa-dot-circle-o" style="color:green"></i>');

$parts[] = array(
	"headline" => g_l('cockpit', '[default_priority]'), "html" => $prio->getHTML(), 'space' => we_html_multiIconBox::SPACE_MED
);

$oSctValid = we_html_tools::htmlSelect("sct_valid", array(
			g_l('cockpit', '[always]'), g_l('cockpit', '[from_date]'), g_l('cockpit', '[period]')
				), 1, g_l('cockpit', '[always]'), false, array('style' => "width:120px;", 'onchange' => ""), 'value', 120);

$parts[] = array(
	"headline" => g_l('cockpit', '[default_validity]'), "html" => $oSctValid, 'space' => we_html_multiIconBox::SPACE_MED
);

list($pad_header_enc, ) = explode(',', we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
$pad_header = base64_decode($pad_header_enc);
$DB_WE = new DB_WE();
$DB_WE->query('SELECT	distinct(WidgetName) FROM ' . NOTEPAD_TABLE . ' WHERE UserID=' . intval($_SESSION['user']['ID']));
$options = array(
	$pad_header => $pad_header, g_l('cockpit', '[change]') => g_l('cockpit', '[change]')
);
while($DB_WE->next_record()){
	$options[$DB_WE->f('WidgetName')] = $DB_WE->f('WidgetName');
}
$oSctTitle = we_html_tools::htmlSelect("sct_title", array_unique($options), 1, "", false, array('id' => "title", 'onchange' => ""), 'value');
$parts[] = array(
	"headline" => g_l('cockpit', '[title]'), "html" => $oSctTitle, 'space' => we_html_multiIconBox::SPACE_MED
);
$parts[] = array(
	"headline" => g_l('cockpit', '[bg_color]'), "html" => $oSctCls->getHTML(), 'space' => we_html_multiIconBox::SPACE_MED
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

echo we_html_tools::getHtmlTop(g_l('cockpit', '[notepad]'), '', '', STYLESHEET .
		we_html_element::jsScript(JS_DIR . "weCombobox.js") .
		$jsFile .
		we_html_element::jsElement($jsPrefs) .
		we_html_element::jsScript(JS_DIR . 'widgets/pad.js'), we_html_element::htmlBody(
				array(
			"class" => "weDialogBody", "onload" => "initDlg();"
				), we_html_element::htmlForm(array(
					"onsubmit" => "return false;"
						), we_html_multiIconBox::getHTML("padProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[notepad]')))));
