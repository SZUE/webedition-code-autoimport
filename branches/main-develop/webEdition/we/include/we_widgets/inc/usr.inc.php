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
list($num, $usr) = we_users_online::getUsers();
$oTblCont = new we_html_table(
	["id" => "m_" . $iCurrId . "_inline",
	'style' => "width:" . $iWidth . "px;",
	], 1, 1);
$oTblCont->setCol(0, 0, null, '<div id="users_online">' . $usr . '</div>');
$aLang = [g_l('cockpit', '[users_online]'), ' (<span id="num_users">' . $num . '</span>)'];

$oTblDiv = $oTblCont->getHtml();
