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
class rpcGetSearchResultCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		we_html_tools::protect();

		$setView = we_base_request::_(we_base_request::INT, 'we_cmd', '', 'setView');

		if(($trans = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0))){
			$we_dt = isset($_SESSION['weS']['we_data'][$trans]) ? $_SESSION['weS']['we_data'][$trans] : '';
		}
		$doc = we_base_request::_(we_base_request::STRING, 'classname');
		$_document = new $doc;
		$_document->we_initSessDat($we_dt);

		$GLOBALS['we_cmd_obj'] = $_document;

		$content = doclistView::searchProperties();
		$sview = new we_search_view();
		$code = $sview->tabListContent($setView, $content, $class = 'middlefont', 'doclist');

		$resp->setData('data', $code);

		return $resp;
	}

}
