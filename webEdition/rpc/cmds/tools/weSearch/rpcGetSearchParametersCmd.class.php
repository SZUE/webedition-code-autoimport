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
 * @package    webEdition_rpc
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class rpcGetSearchParametersCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		we_html_tools::protect();


		$pos = $_REQUEST['position'];
		$whichsearch = $_REQUEST['whichsearch'];
		$foundItems = $_SESSION['weS']['weSearch']['foundItems' . $whichsearch . ''];
		$anzahl = $_REQUEST['we_cmd']['anzahl' . $whichsearch . ''];
		$searchstart = $_REQUEST['we_cmd']['searchstart' . $whichsearch . ''];

		$_SESSION['weS']['weSearch']['anzahl' . $whichsearch . ''] = $anzahl;
		$_SESSION['weS']['weSearch']['searchstart' . $whichsearch . ''] = $searchstart;

		$_REQUEST['we_cmd']['obj'] = true;

		if($pos == "top"){
			$code = we_search_view::getSearchParameterTop($foundItems, $whichsearch);
		}
		if($pos == "bottom"){
			$_REQUEST['we_cmd']['setInputSearchstart'] = 1;
			$code = we_search_view::getSearchParameterBottom($foundItems, $whichsearch);
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
