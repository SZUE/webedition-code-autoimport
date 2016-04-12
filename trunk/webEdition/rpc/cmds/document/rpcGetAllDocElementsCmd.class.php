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
class rpcGetAllDocElementsCmd extends we_rpc_cmd{

	var $Parameters = array('docid');

	function execute(){

		$resp = new we_rpc_response();

		$_doc = new we_webEditionDocument();
		$_doc->initByID(we_base_request::_(we_base_request::INT, 'docid',0));

		$resp->setData('elements', $_doc->elements);

		return $resp;
	}

}
