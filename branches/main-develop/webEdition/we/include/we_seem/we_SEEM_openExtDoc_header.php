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


    include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");

    we_html_tools::protect();
    //	Header for a none webEdition document opened with webEdition

	$_webEditionSiteUrl = getServerUrl() . "/webEdition/site";

	if( strpos($_REQUEST["url"], $_webEditionSiteUrl) === 0 ){
		$_errormsg = g_l('SEEM',"[ext_doc_tmp]");
	} else {
		$_errormsg = sprintf(g_l('SEEM',"[ext_doc]"),$_REQUEST["url"]);
	}

	$_table = new we_html_table(	array(	"cellpadding" => 0,
										"cellspacing" => 0,
										"border"      => 0),
								2,
								4);

	$_table->setColContent(0, 0, we_html_tools::getPixel(20,6));
	$_table->setColContent(1, 0, we_html_tools::getPixel(1,1));
	$_table->setColContent(1, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", "width" => 25, "height" => 27)));
	$_table->setColContent(1, 2, we_html_tools::getPixel(9,1));
	$_table->setCol(1, 3, array("class" => "middlefontred"), $_errormsg );


    $_body = $_table->getHtml();
	$_head = STYLESHEET;


    $_body = we_html_element::htmlBody(	array(	"bgcolor"      => "white",
    											"background"   => IMAGE_DIR . "backgrounds/header_with_black_lines.gif",
    											"marginwidth"  => 0,
    											"marginheight" => 0,
    											"leftmargin"   => 0,
    											"topmargin"    => 0),
										$_body);

	print we_html_element::htmlHtml("\n" . $_head . "\n" . $_body );
