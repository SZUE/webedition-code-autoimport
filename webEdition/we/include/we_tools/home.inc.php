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
$row = 0;
$starttable = new we_html_table(["cellpadding" => 7], 3, 1);
$starttable->setCol($row++, 0, ['class' => "defaultfont titleline", "colspan" => 3], $title);
$starttable->setCol($row++, 0, ['class' => 'defaultfont', "colspan" => 3], "");
$starttable->setCol($row++, 0, ['style' => "text-align:center"], $content);

$tooldir = ($tool === 'weSearch' ? WE_INCLUDES_DIR . 'we_tools/' : WE_APPS_DIR);

echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(CSS_DIR . 'tools_home.css') .
	we_html_element::cssLink(CSS_DIR . 'tools_home.css') . $GLOBALS["we_head_insert"], we_html_element::htmlBody(['class' => "home", 'onload' => "loaded = true;var we_is_home = 1;"], '
	<div id="tabelle">' . $starttable->getHtml() . '</div>
	<div id="modimage"><img src="' . $tooldir . $tool . '/layout/home.gif" style="width:335px;height:329px" /></div>' .
		$GLOBALS["we_body_insert"]
));
