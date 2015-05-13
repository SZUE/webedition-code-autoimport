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


//	this file gets the output from a none webEdition-Document on the same web-server
//	and parses all found links to webEdition cmds

we_html_tools::protect();
$path = we_base_request::_(we_base_request::URL, "filepath");
if(($content = we_base_file::load($path . '?' . urldecode(we_base_request::_(we_base_request::RAW, "paras", '')))) !== false){
	echo we_SEEM::parseDocument($content);
} else {
	$_table = new we_html_table(array("cellpadding" => 0,
		"cellspacing" => 0,
		"border" => 0), 4, 2);
	$_table->setColContent(0, 0, we_html_tools::getPixel(20, 20));
	$_table->setCol(1, 1, array("class" => "defaultfont"), sprintf(g_l('SEEM', '[ext_doc_not_found]'), $path) . "<br/>");
	$_table->setColContent(2, 0, we_html_tools::getPixel(20, 6));

	//	there must be a navigation-history - so use it
	$_table->setColContent(3, 1, we_html_button::create_button(we_html_button::BACK, "javascript:top.weNavigationHistory.navigateBack();"));

	echo we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(STYLESHEET .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
			we_html_element::jsScript(JS_DIR . 'attachKeyListener.js')
		) .
		we_html_element::htmlBody(array("style" => 'background-color:#F3F7FF;'), $_table->getHtml())
	);
}

echo we_html_element::jsElement('parent.openedWithWE = 1;');
