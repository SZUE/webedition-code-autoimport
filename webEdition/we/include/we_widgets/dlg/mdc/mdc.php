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
// widget MY DOCUMENTS
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$all = explode(';', we_base_request::_(we_base_request::RAW_CHECKED, 'we_cmd', '', 1));
if(count($all) > 1){
	list($dir, $dt_tid, $cats) = $all;
} else {
	$dir = $all[0];
	$dt_tid = $cats = '';
}
$aCsv = array(
	0, //unused - compatibility
	we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
	$dir,
	$dt_tid,
	$cats
);
require_once('../../mod/mdc.inc.php');
$cmd4 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4);

$js = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 5) . "';
var _sType='mdc';
var _sTb='" . ($cmd4 ? : g_l('cockpit', (($_binary{1} ? '[my_objects]' : '[my_documents]')))) . "';

function init(){
	parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
}";

echo we_html_tools::getHtmlTop(g_l('cockpit', '[my_documents]'), '', '', STYLESHEET .
	we_html_element::jsElement($js), we_html_element::htmlBody(
		array(
			'style'=>'margin:10px 15px;',
		"onload" => 'if(parent!=self){init();}WE().util.setIconOfDocClass(document,"mdcIcon");'
		), we_html_element::htmlDiv(array(
			"id" => "mdc"
			), $mdc)));


