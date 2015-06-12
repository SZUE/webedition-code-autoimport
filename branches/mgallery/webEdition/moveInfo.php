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
we_html_tools::protect();

if(isset($_SESSION['weS']['move_files_nok']) && is_array($_SESSION['weS']['move_files_nok'])){
	$table = new we_html_table(array('style' => 'margin:10px;', "cellpadding" => 0, "cellspacing" => 0, "border" => 0, "class" => "defaultfont"), 0, 4);
	foreach($_SESSION['weS']['move_files_nok'] as $i => $data){
		$table->addRow();
		$table->setCol($i, 0, null, we_html_tools::getPixel(10, 2));
		$table->setCol($i, 1, null, (isset($data["ContentType"]) ? we_html_element::jsElement('getTreeIcon("' . $data["ContentType"] . '")') : ""));
		$table->setCol($i, 2, null, we_html_tools::getPixel(10, 2));
		$table->setCol($i, 3, null, str_replace($_SERVER['DOCUMENT_ROOT'], "", $data["path"]));
	}
}

$parts = array(
	array(
		"headline" => we_html_tools::htmlAlertAttentionBox(str_replace("\\n", '', sprintf(g_l('alert', '[move_of_files_failed]'), "")), we_html_tools::TYPE_ALERT, 500),
		"html" => "",
		"space" => 10,
		"noline" => 1),
	array(
		"headline" => "",
		"html" => we_html_element::htmlDiv(array("class" => "blockWrapper", "style" => "width: 475px; height: 350px; border:1px #dce6f2 solid;"), $table->getHtml()),
		"space" => 10),
);

$buttons = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "align" => "right", "border" => 0, "class" => "defaultfont"), 1, 1);
$buttons->setCol(0, 0, null, we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();"));
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET, we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
			we_html_multiIconBox::getHTML("", "100%", $parts, 30, $buttons->getHtml())
		)
	)
);
