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
	/* TODO: als long as we send html results we can send the parameters togehter with results as an empty 
			 <span data-searchstart="x" data-number="y" data-disableNext="true" data-disableBack="false" data-text="1-10 von27">
			 and let ajax callback do the rest.
			 Later on we send all the serach staff as json and let js do the rest.
	*/
	function execute(){
		$resp = new rpcResponse();

		$pos = we_base_request::_(we_base_request::STRING, 'position', '');
		$foundItems = (isset($_SESSION['weS']['weSearch']['foundItems'])) ? $_SESSION['weS']['weSearch']['foundItems'] : 0;

		if(($trans = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0))){
			$we_dt = isset($_SESSION['weS']['we_data'][$trans]) ? $_SESSION['weS']['we_data'][$trans] : '';
		}
		$doc = 'we_folder'; //we_base_request::_(we_base_request::STRING, 'classname', 'we_folder');
		$_document = new $doc;
		$_document->we_initSessDat($we_dt);
		$doclistView = new $_document->doclistViewClass(new $_document->doclistSearchClass($_document->doclistModel));

		switch($pos){
			case "top":
				$code = $doclistView->getSearchParameterTop($foundItems);
				break;
			case "bottom":
				$GLOBALS['setInputSearchstart'] = 1;
				$code = $doclistView->getSearchParameterBottom(FILE_TABLE, $foundItems);
		}

		$resp->setData("data", $code);

		return $resp;
	}

}
