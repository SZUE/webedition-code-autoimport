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
				), we_html_element::htmlDiv(array('id' => 'mfd_data'), $lastModified)
));
$aLang = array(
	g_l('cockpit', "[last_modified]"), ""
);
