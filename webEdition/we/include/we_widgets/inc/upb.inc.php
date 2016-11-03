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
$oTblDiv = we_html_element::htmlDiv(["id" => "m_" . $iCurrId . "_inline",
		'style' => "width:" . $iWidth . "px;height:" . ($aPrefs[$aProps[0]]["height"] - 25) . "px;overflow:auto;"
		], $ct) . we_html_element::jsElement("WE().util.setIconOfDocClass(document,'upbIcon');");
$bTypeDoc = (bool) $aProps[3]{0};
$bTypeObj = (bool) $aProps[3]{1};
$sTb = g_l('cockpit', ($bTypeDoc && $bTypeObj ? '[upb_docs_and_objs]' : ($bTypeDoc ? '[upb_docs]' : ($bTypeObj ? '[upb_objs]' : '[upb_docs_and_objs]'))));
$aLang = [$sTb, ""];
