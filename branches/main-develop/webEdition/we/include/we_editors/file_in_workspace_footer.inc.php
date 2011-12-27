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

include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_inc_min.inc.php');


	$_messageTbl = new we_html_table(	array(	"border"      => 0,
											"cellpadding" => 0,
											"cellspacing" => 0),
									2,
									4);
	//	spaceholder
	$_messageTbl->setColContent(0,0, we_html_tools::getPixel(20,7));
	$_messageTbl->setColContent(1,1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
	$_messageTbl->setColContent(1,2, we_html_tools::getPixel(5,2));
	$_messageTbl->setCol(1,3, array("class" => "defaultfont"), g_l('alert','['.FILE_TABLE.'][not_im_ws]'));


	$_head = we_html_element::htmlHead(we_html_element::jsElement("\n<!--\ntop.toggleBusy(0);\n-->\n"));
	$_body = we_html_element::htmlBody(	array(	"background" => "/webEdition/images/edit/editfooterback.gif",
												"bgcolor"    => "white"),
										$_messageTbl->getHtml());


	print we_html_element::htmlHtml($_head . STYLESHEET . "\n" . $_body);
