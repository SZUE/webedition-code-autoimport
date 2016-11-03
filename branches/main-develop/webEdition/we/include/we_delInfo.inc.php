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
if(isset($_SESSION['weS']['delete_files_nok']) && is_array($_SESSION['weS']['delete_files_nok'])){
	$table = new we_html_table(['style' => 'margin:10px;', 'class' => 'defaultfont default'], 1, 2);
	foreach($_SESSION['weS']['delete_files_nok'] as $i => $data){
		$table->setCol($i, 0, ['style' => 'padding-top:2px;'], (isset($data["ContentType"]) ? we_html_element::jsElement('document.write(WE().util.getTreeIcon("' . $data["ContentType"] . '"))') : ""));
		$table->setCol($i, 1, null, str_replace($_SERVER['DOCUMENT_ROOT'], "", $data["path"]));
		$table->addRow();
	}
	unset($_SESSION['weS']['delete_files_nok']);
}

$parts = [
	["headline" => we_html_tools::htmlAlertAttentionBox($_SESSION['weS']['delete_files_info'], we_html_tools::TYPE_ALERT, 500),
		"html" => "",
		'space' => we_html_multiIconBox::SPACE_SMALL,
		'noline' => 1
	],
	["headline" => "",
		"html" => we_html_element::htmlDiv(['class' => "blockWrapper", 'style' => "width: 475px; height: 350px; border:1px #dce6f2 solid;"], $table->getHtml()),
		'space' => we_html_multiIconBox::SPACE_SMALL
	],
];
unset($_SESSION['weS']['delete_files_info']);

$buttons = new we_html_table(['class' => 'default defaultfont', 'style' => "text-align:right"], 1, 1);
$buttons->setCol(0, 0, null, we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();"));
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', we_html_element::htmlBody(['class' => "weDialogBody"], we_html_multiIconBox::getHTML("", $parts, 30, $buttons->getHtml())
	)
);
