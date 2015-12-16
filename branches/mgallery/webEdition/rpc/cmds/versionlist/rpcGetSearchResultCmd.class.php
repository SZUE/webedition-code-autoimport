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

		$anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'anzahl');
		$searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'searchstart');

		$GLOBALS['we_cmd_obj'] = 1;
		$view = new we_versions_view();
		$content = $view->getVersionsOfDoc();
		//$sview = new we_search_view();
		$code = $view->tabListContent($searchstart, $anzahl, $content);

		$resp->setData("data", $code);

		return $resp;
	}

}
