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
class rpcGetDocElementCmd extends rpcCmd{

	var $Parameters = array('docid', 'element');

	function execute(){
		$resp = new rpcResponse();

		$_doc = new we_webEditionDocument();

		$_doc->initByID(weRequest('int', 'docid',0));

		$resp->setData($_REQUEST['element'], $_doc->getElement($_REQUEST['element']));

		return $resp;
	}

}
