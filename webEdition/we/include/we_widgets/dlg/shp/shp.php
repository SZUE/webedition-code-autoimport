<?php

/**
 * webEdition CMS
 *
 * $Rev: 6749 $
 * $Author: mokraemer $
 * $Date: 2013-10-08 11:11:15 +0200 (Di, 08 Okt 2013) $
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
// widget LAST MODIFIED

$aCols = $_REQUEST['we_cmd'];
require_once('../../shp/shp.php');

$sJsCode = "
var _sObjId='" . $_REQUEST['we_cmd'][0] . "';
var _sType='shp';
var _sTb='Shop';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}";

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[shop_dashboard][headline]').':&nbsp;'. $interval . STYLESHEET . we_html_element::jsElement(
				$sJsCode)) . we_html_element::htmlBody(
			array(
			"marginwidth" => 15,
			"marginheight" => 10,
			"leftmargin" => 15,
			"topmargin" => 10,
			"onload" => "if(parent!=self)init();"
			), we_html_element::htmlDiv(array(
				"id" => "shp"
				), we_html_element::htmlDiv(array('id' => 'shp_data'), $shopDashboard)
)));

