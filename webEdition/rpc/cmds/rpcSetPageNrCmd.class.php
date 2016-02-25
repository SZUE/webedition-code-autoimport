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
class rpcSetPageNrCmd extends we_rpc_cmd{

	function execute(){
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'transaction', '');
		if(isset($_SESSION['weS']['we_data'][$we_transaction])){
			$_SESSION['weS']['we_data'][$we_transaction][0]['EditPageNr'] = we_base_request::_(we_base_request::INT, 'editPageNr');
		}
		$resp = new we_rpc_response();
		return $resp;
	}

}
