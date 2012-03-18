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
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");


//	this file gets the output from a none webEdition-Document on the same web-server
//	and parses all found links to webEdition cmds

we_html_tools::protect();

$fh = @fopen($_REQUEST["filepath"] . $seperator . urldecode($_REQUEST["paras"]), "rb");

if($fh){

	$content = "";

	while(!feof($fh)) {
		$content .= fgets($fh, 1024);
	}
	fclose($fh);
}

if(isset($content)){
	print we_SEEM::parseDocument($content, $_REQUEST["filepath"]);
} else{


	$_head = we_html_element::htmlHead(STYLESHEET);

	$_table = new we_html_table(array("cellpadding" => 0,
			"cellspacing" => 0,
			"border" => 0),
			4,
			2);
	$_table->setColContent(0, 0, we_html_tools::getPixel(20, 20));
	$_table->setCol(1, 1, array("class" => "defaultfont"), sprintf(g_l('SEEM', "[ext_doc_not_found]"), $_REQUEST["filepath"]) . "<br>");
	$_table->setColContent(2, 0, we_html_tools::getPixel(20, 6));

	//	there must be a navigation-history - so use it
	$_table->setColContent(3, 1, we_button::create_button("back", "javascript:top.weNavigationHistory.navigateBack();"));

	$_body = $_table->getHtml();
	$_body = we_html_element::htmlBody(array("style" => 'background-color:#F3F7FF;'), $_body);

	print we_html_element::htmlHtml($_head . $_body);
}

echo we_html_element::jsElement('parent.openedWithWE = 1;');
