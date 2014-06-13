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

we_html_tools::protect();

if(($uniqid = we_base_request::_(we_base_request::RAW, 'u')) && ($we_transaction = we_base_request::_(we_base_request::TRANSACTION, 't', $we_transaction)) && ($id = we_base_request::_(we_base_request::INTLIST, 'id'))){

	$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : '';
	include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

	echo we_html_tools::getHtmlTop() .
	STYLESHEET . "</head>";

	$table = '<table border="0" cellpadding="5" cellspacing="0"><tr>';

	$thumbIDs = makeArrayFromCSV($id);
	foreach($thumbIDs as $thumbid){
		$thumbObj = new we_thumbnail();
		$thumbObj->initByThumbID($thumbid, $we_doc->ID, $we_doc->Filename, $we_doc->Path, $we_doc->Extension, $we_doc->getElement("origwidth"), $we_doc->getElement("origheight"), $we_doc->getDocument());


		srand((double) microtime() * 1000000);
		$randval = rand();


		$useOrig = $thumbObj->isOriginal();


		if((!$useOrig) && $we_doc->ID && ($we_doc->DocChanged == false) && file_exists($thumbObj->getOutputPath(true))){
			$src = $thumbObj->getOutputPath(false, true);
		} else {
			$src = WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(
					array(
						'we_cmd[0]' => 'show_binaryDoc',
						'we_cmd[1]' => $we_doc->ContentType,
						'we_cmd[2]' => $we_transaction,
						'we_cmd[3]' => ($useOrig ? '' : $thumbid),
						'rand' => $randval
			));
		}

		$table .= '<td><image src="' . $src . '" width="' . $thumbObj->getOutputWidth() . '" height="' . $thumbObj->getOutputHeight() . '" border="0"></td>';
	}

	$table .= '</tr></table>';

	print we_html_element::htmlBody(array("bgcolor" => "#ffffff", "style" => 'margin: 5px 5px 5px 5px'), $table) . "</html>";
}