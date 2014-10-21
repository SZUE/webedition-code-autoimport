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
//	footer for a none webEdition-Document opened with webEdition
//	the back button is only activated when there are documents in
//	the navigation history


$_table = new we_html_table(array("cellpadding" => 0,
	"cellspacing" => 0,
	"border" => 0), 2, 2);
$_table->setColContent(0, 0, we_html_tools::getPixel(20, 6));
$_table->setColContent(1, 1, we_html_button::create_button("back", "javascript:top.weNavigationHistory.navigateBack();"));


$_body = we_html_element::htmlBody(array("bgcolor" => "white",
			"background" => EDIT_IMAGE_DIR . "editfooterback.gif",
			"marginwidth" => 0,
			"marginheight" => 0,
			"leftmargin" => 0,
			"topmargin" => 0), $_table->getHtml());

echo we_html_element::htmlDocType() . we_html_element::htmlHtml(STYLESHEET_BUTTONS_ONLY . SCRIPT_BUTTONS_ONLY . $_body);
