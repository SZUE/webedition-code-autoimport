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

		$pos = weRequest('string', 'position', '');

		$foundItems = (isset($_SESSION['weS']['weSearch']['foundItems'])) ? $_SESSION['weS']['weSearch']['foundItems'] : 0;

		$_SESSION['weS']['weSearch']['anzahl'] = weRequest('int', 'we_cmd', 0, 'anzahl');
		$_SESSION['weS']['weSearch']['searchstart'] = weRequest('int', 'we_cmd', 0, 'searchstart');

		$_REQUEST['we_cmd']['obj'] = true;

		if($pos == "top"){
			$code = doclistView::getSearchParameterTop($foundItems);
		}
		if($pos == "bottom"){
			$_REQUEST['we_cmd']['setInputSearchstart'] = 1;
			$code = doclistView::getSearchParameterBottom($foundItems);
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
