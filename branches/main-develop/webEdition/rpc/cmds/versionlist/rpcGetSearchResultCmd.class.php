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
class rpcGetSearchResultCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		//FIXME: not needed??
		//$we_transaction = $_REQUEST['we_cmd']['we_transaction'];

		/*$mode = $_REQUEST['we_cmd']['mode'];
		$order = $_REQUEST['we_cmd']['order'];*/
		$anzahl = intval($_REQUEST['we_cmd']['anzahl']);
		$searchstart = intval($_REQUEST['we_cmd']['searchstart']);

		$_REQUEST['we_cmd']['obj'] = 1;
		$view = new weVersionsView();
		$content = $view->getVersionsOfDoc();

		$code = weVersionsView::tabListContent($searchstart, $anzahl, $content);

		$resp->setData("data", $code);

		return $resp;
	}

}
