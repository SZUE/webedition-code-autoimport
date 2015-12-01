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
$jsPrefs = "
var _sObjId='" . we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) . "';
var _sCls_=opener.document.getElementById(_sObjId+'_cls').value;";
$jsFile = we_html_element::jsScript(JS_DIR . 'widgets/dlg_prefs.js');

$oSctCls = new we_html_select(array(
	"name" => "sct_cls",
	"size" => 1,
	"class" => "defaultfont",
	"style" => "width:120px;border:#AAAAAA solid 1px"
		));
$oSctCls->insertOption(0, "white", g_l('cockpit', '[white]'));
$oSctCls->insertOption(1, "lightCyan", g_l('cockpit', '[lightcyan]'));
$oSctCls->insertOption(2, "blue", g_l('cockpit', '[blue]'));
$oSctCls->insertOption(3, "green", g_l('cockpit', '[green]'));
$oSctCls->insertOption(4, "orange", g_l('cockpit', '[orange]'));
$oSctCls->insertOption(5, "yellow", g_l('cockpit', '[yellow]'));
$oSctCls->insertOption(6, "red", g_l('cockpit', '[red]'));

$oSelCls = new we_html_table(array('class' => 'default'), 1, 2);
$oSelCls->setCol(0, 0, array("width" => 130, "class" => "defaultfont"), g_l('cockpit', '[bgcolor]'));
$oSelCls->setCol(0, 1, null, $oSctCls->getHTML());
