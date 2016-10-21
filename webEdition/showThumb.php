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

if(($uniqid = we_base_request::_(we_base_request::RAW, 'u')) &&
	($we_transaction = we_base_request::_(we_base_request::TRANSACTION, 't', $we_transaction)) &&
	($thumbIDs = we_base_request::_(we_base_request::INTLISTA, 'id', []))){

	$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : '';
	$we_doc = we_document::initDoc($we_dt);
	session_write_close();

	$table = '<table class="default"><tr>';

	foreach($thumbIDs as $thumbid){
		$thumbObj = new we_thumbnail();
		$thumbObj->initByThumbID($thumbid, $we_doc->ID, $we_doc->Filename, $we_doc->Path, $we_doc->Extension, $we_doc->getElement('origwidth', 'bdid'), $we_doc->getElement('origheight', 'bdid'), $we_doc->getDocument());


		srand((double) microtime() * 1000000);
		$randval = rand();


		$useOrig = $thumbObj->isOriginal();

		if((!$useOrig) && $we_doc->ID && ($we_doc->DocChanged == false) && file_exists($thumbObj->getOutputPath(true))){
			$src = $thumbObj->getOutputPath(false, true);
		} else {
			$src = WEBEDITION_DIR . 'we_cmd.php?' . http_build_query([
					'we_cmd' => [
						0 => 'show_binaryDoc',
						1 => $we_doc->ContentType,
						2 => $we_transaction,
						3 => ($useOrig ? '' : $thumbid),
					],
					'rand' => $randval
			]);
		}

		$table .= '<td><image src="' . $src . '" style="width:' . $thumbObj->getOutputWidth() . 'px;height:' . $thumbObj->getOutputHeight() . 'px"/></td>';
	}

	$table .= '</tr></table>';
	echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(['style' => 'margin: 5px;'], $table));
}