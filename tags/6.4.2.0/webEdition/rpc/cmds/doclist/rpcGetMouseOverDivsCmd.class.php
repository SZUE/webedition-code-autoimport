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
class rpcGetMouseOverDivsCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		we_html_tools::protect();

		$whichsearch = we_base_request::_(we_base_request::STRING, 'whichsearch', '');
		$setView = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'setView');
		$anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'anzahl');
		$searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'searchstart');

		if(($trans = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0))){
			$we_dt = isset($_SESSION['weS']['we_data'][$trans]) ? $_SESSION['weS']['we_data'][$trans] : '';
		}
		$doc = we_base_request::_(we_base_request::STRING, 'classname');
		$_document = new $doc;
		$_document->we_initSessDat($we_dt);

		$GLOBALS['we_cmd_obj'] = $_document;

		if($setView == 1){
			$content = doclistView::searchProperties();

			$x = $searchstart + $anzahl;
			if($x > count($content)){
				$x = $x - ($x - count($content));
			}

			$code = we_search_view::makeMouseOverDivs($x, $content, $whichsearch);
		} else {
			$code = '';
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
