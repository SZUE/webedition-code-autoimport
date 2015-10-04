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

$aProps = array(
	0,
	0,
	0,
	we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)
);
require_once('../../mod/upb.php');

$sTb = g_l('cockpit', ($bTypeDoc && $bTypeObj ? '[upb_docs_and_objs]' : ($bTypeDoc ? '[upb_docs]' : ($bTypeObj ? '[upb_objs]' : '[upb_docs_and_objs]'))));

$jsCode = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
var _sType='upb';
var _sTb='" . $sTb . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}
";

echo we_html_tools::getHtmlTop(g_l('cockpit', '[unpublished]'), '', '', STYLESHEET . we_html_element::jsElement(
		$jsCode), we_html_element::htmlBody(
		array(
		"marginwidth" => 15,
		"marginheight" => 10,
		"leftmargin" => 15,
		"topmargin" => 10,
		"onload" => 'if(parent!=self){init();}WE().util.setIconOfDocClass(document,"upbIcon");'
		), we_html_element::htmlDiv(array(
			"id" => "upb"
			), $ct)));
