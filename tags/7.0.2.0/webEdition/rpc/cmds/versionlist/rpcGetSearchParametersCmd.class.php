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
class rpcGetSearchParametersCmd extends we_rpc_cmd{

	function execute(){

		$resp = new we_rpc_response();

		$pos = we_base_request::_(we_base_request::STRING, 'position', '');

		if(($trans = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0))){
			$we_dt = isset($_SESSION['weS']['we_data'][$trans]) ? $_SESSION['weS']['we_data'][$trans] : '';
		}
		$class = $we_dt[0]['ClassName']; //we_base_request::_(we_base_request::STRING, 'classname');
		$document = new $class;
		$document->we_initSessDat($we_dt);
		$versionsView = new we_versions_view($document->versionsModel);

		$GLOBALS['we_cmd_obj'] = 1;
		$foundItems = (isset($_SESSION['weS']['versions']['foundItems'])) ? $_SESSION['weS']['versions']['foundItems'] : 0;
		switch($pos){
			case "top":
				$code = $versionsView->getParameterTop($foundItems);
				break;
			case "bottom":
				$GLOBALS['setInputSearchstart'] = 1;
				$code = $versionsView->getParameterBottom($foundItems);
				break;
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
