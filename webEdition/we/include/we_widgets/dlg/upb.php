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

$oChbxDocs = we_html_forms::checkbox(0, true, "chbx_type", g_l('cockpit', '[documents]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxObjs = we_html_forms::checkbox(0, true, "chbx_type", g_l('cockpit', '[objects]'), true, "defaultfont", "", false, "", 0, 0);

$dbTableType = '<table><tr>' .
	(defined('FILE_TABLE') ? '<td>' . $oChbxDocs . ' </td>' : '') .
	(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm("CAN_SEE_OBJECTFILES") ? "<td>" . $oChbxObjs . "</td>" : '') .
	"</tr></table>";

$parts = array(
	array("headline" => g_l('cockpit', '[type]'), "html" => $dbTableType, 'space' => we_html_multiIconBox::SPACE_MED),
	array("headline" => "", "html" => $oSelCls->getHTML(),),
);

$save_button = we_html_button::create_button(we_html_button::SAVE, "javascript:save();", false, 0, 0);
$preview_button = we_html_button::create_button(we_html_button::PREVIEW, "javascript:preview();", false, 0, 0);
$cancel_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:exit_close();");
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML("mfdProps", $parts, 30, $buttons, -1, "", "", "", g_l('cockpit', '[unpublished]'));

echo we_html_tools::getHtmlTop(g_l('cockpit', '[unpublished]'), '', '', STYLESHEET .
	$jsFile .
	we_html_element::jsElement($jsPrefs) .
	we_html_element::jsScript(JS_DIR . 'widgets/upb.js')
	, we_html_element::htmlBody(
		array("class" => "weDialogBody", "onload" => "init();"), we_html_element::htmlForm("", $sTblWidget)));
