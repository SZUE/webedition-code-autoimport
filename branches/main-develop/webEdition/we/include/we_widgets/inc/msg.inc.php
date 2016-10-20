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
$oTblCont = new we_html_table(["id" => "m_" . $iCurrId . "_inline", "style" => "width:100%;",], 2, 2);
$oTblCont->setCol(0, 0, ["width" => 34, 'style' => 'vertical-align:middle;', "class" => "middlefont"], $msg_button);
$oTblCont->setCol(0, 1, ['style' => 'vertical-align:middle;'], we_html_element::htmlA(["href" => $msg_cmd,
		"class" => "middlefont bold",
		"style" => "text-decoration:none;"
		], $new_messages . " (" . we_html_element::htmlSpan(["id" => "msg_count"
			], $newmsg_count) . ")"));
$oTblCont->setCol(1, 0, ["width" => 34, 'style' => 'vertical-align:middle;', "class" => "middlefont"], $todo_button);
$oTblCont->setCol(1, 1, ['style' => 'vertical-align:middle;'], we_html_element::htmlA(["href" => $msg_cmd,
		"class" => "middlefont bold",
		"style" => "text-decoration:none;"
		], $new_tasks . " (" . we_html_element::htmlSpan(["id" => "task_count"
			], $newtodo_count) . ")"));
$aLang = [g_l('cockpit', '[messaging]'), ""];
$oTblDiv = $oTblCont->getHtml();
