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
// widget LAST MODIFIED

$aCols = we_base_request::_(we_base_request::STRING, 'we_cmd');
require_once('../../mod/mfd.php');

$sJsCode = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
var _sType='mfd';
var _sTb='" . g_l('cockpit', '[last_modified]') . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}";

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[last_modified]')) . STYLESHEET . we_html_element::jsElement(
				$sJsCode)) . we_html_element::htmlBody(
			array(
			"marginwidth" => 15,
			"marginheight" => 10,
			"leftmargin" => 15,
			"topmargin" => 10,
			"onload" => "if(parent!=self)init();"
			), we_html_element::htmlDiv(array(
				"id" => "mfd"
				), we_html_element::htmlDiv(array('id' => 'mfd_data'), $lastModified)
)));
