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
if(!($trans = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 1))){
	exit();
}

we_html_tools::protect();

echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', 
	we_html_element::jsElement('var url="' . WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array(
			'we_cmd' => array(
				0 => 'save_document',
				1 => $trans,
				2 => 1,
				5 => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5),
				6 => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 6),
			),
			'we_transaction' => $trans,
			'we_complete_request' => 1
			), null, '&') .
		'";
new jsWindow(url,"templateSaveQuestion",-1,-1,400,170,true,false,true);
'), '<body></body>');
