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


$table = new we_html_table(array("border" => 0), 1, 2);
$table->setColContent(0, 1, we_html_button::create_button(we_html_button::BACK, "javascript:WE().layout.weNavigationHistory.navigateBack();"));



echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(["id" => "footerBody"], $table->getHtml())
);
