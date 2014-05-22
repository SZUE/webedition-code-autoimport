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
class rpcResetFailedCustomerLoginsCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		$custid = weRequest('int', 'custid', 0);
		$db = $GLOBALS['DB_WE'];
		$user = f('SELECT Username FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . $custid);
		if($user){
			$db->query('UPDATE ' . FAILED_LOGINS_TABLE . ' SET isValid="false" WHERE UserTable="tblWebUser" AND Username="' . $user . '"');
			$resp->setData('data', 'true');
			$resp->setData('value', '0 / ' . SECURITY_LIMIT_CUSTOMER_NAME);
		} else {
			$resp->setData('data', 'false');
		}
		return $resp;
	}

}
