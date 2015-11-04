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
$oTblCont = new we_html_table(array("id" => "m_" . $iCurrId . "_inline", "style" => "width:100%;",), 2, 2);
$oTblCont->setCol(0, 0, array("width" => 34, 'style' => 'vertical-align:middle;', "class" => "middlefont"), $msg_button);
$oTblCont->setCol(0, 1, array('style' => 'vertical-align:middle;'), we_html_element::htmlA(array(
		"href" => $msg_cmd,
		"class" => "middlefont",
		"style" => "font-weight:bold;text-decoration:none;"
		), $new_messages . " (" . we_html_element::htmlSpan(array(
			"id" => "msg_count"
			), $newmsg_count) . ")"));
$oTblCont->setCol(1, 0, array("width" => 34, 'style' => 'vertical-align:middle;', "class" => "middlefont"), $todo_button);
$oTblCont->setCol(1, 1, array('style' => 'vertical-align:middle;'), we_html_element::htmlA(array(
		"href" => $msg_cmd,
		"class" => "middlefont",
		"style" => "font-weight:bold;text-decoration:none;"
		), $new_tasks . " (" . we_html_element::htmlSpan(array(
			"id" => "task_count"
			), $newtodo_count) . ")"));
$aLang = array(
	g_l('cockpit', '[messaging]'), ""
);
$oTblDiv = $oTblCont->getHtml();
