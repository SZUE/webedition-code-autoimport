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

$oTblCont = new we_html_table(array(
	"border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 1, 1);
$oTblCont->setCol(
	0, 0, null, we_html_element::htmlDiv(
		array(
		'id' => 'm_' . $iCurrId . '_inline',
		"style" => "width:" . $iWidth . "px;height:" . ($aPrefs[$aProps[0]]["height"] - 25) . "px;overflow:auto;"
		), we_html_element::htmlDiv(array('id' => 'shp_data'), $shopDashboard)
));
$aLang = array(
	g_l('cockpit', '[shop_dashboard][headline]').':&nbsp;'.$interval, ""
);