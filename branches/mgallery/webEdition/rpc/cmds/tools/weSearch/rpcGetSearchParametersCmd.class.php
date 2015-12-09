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
class rpcGetSearchParametersCmd extends rpcCmd{
	function execute(){

		$resp = new rpcResponse();

		we_html_tools::protect();

		$pos = we_base_request::_(we_base_request::STRING, 'position', '');
		$whichsearch = we_base_request::_(we_base_request::STRING, 'whichsearch', '');
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $whichsearch];
		$anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'anzahl' . $whichsearch);
		$searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'searchstart' . $whichsearch);

		$_SESSION['weS']['weSearch']['anzahl' . $whichsearch] = $anzahl;
		$_SESSION['weS']['weSearch']['searchstart' . $whichsearch] = $searchstart;

		$GLOBALS['we_cmd_obj'] = true;
		$sview = new we_search_view();
		$sview->Model->initByHttp($whichsearch); // FIXME: when moving searchProperties to search_search we init model there!

		switch($pos){
			case 'top':
				$code = $sview->getSearchParameterTop($foundItems, $whichsearch);
				break;
			case 'bottom':
				$GLOBALS['setInputSearchstart'] = 1;
				$code = $sview->getSearchParameterBottom($foundItems, $whichsearch);
				break;
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
