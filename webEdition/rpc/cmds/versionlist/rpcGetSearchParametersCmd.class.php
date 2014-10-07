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

		$pos = we_base_request::_(we_base_request::STRING, 'position', '');

		$foundItems = (isset($_SESSION['weS']['versions']['foundItems'])) ? $_SESSION['weS']['versions']['foundItems'] : 0;

		$_SESSION['weS']['versions']['anzahl'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'anzahl');
		$_SESSION['weS']['versions']['searchstart'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'searchstart');

		$GLOBALS['we_cmd_obj'] = 1;
		$vview = new we_versions_view();
		switch($pos){
			case "top":
				$code = $vview->getParameterTop($foundItems);
				break;
			case "bottom":
				$GLOBALS['setInputSearchstart'] = 1;
				$code = $vview->getParameterBottom($foundItems);
				break;
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
