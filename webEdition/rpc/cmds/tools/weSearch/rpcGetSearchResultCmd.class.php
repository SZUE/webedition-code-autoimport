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
require_once(WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

class rpcGetSearchResultCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		$whichsearch = we_base_request::_(we_base_request::STRING, 'whichsearch', '');
		$setView = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'setView' . $whichsearch);

		$GLOBALS['we_cmd_obj'] = $_SESSION['weS']['weSearch'];

		$sview = new we_search_view();

		// FIXME: let view initialize its model
		if(isset($_SESSION['weS'][$sview->toolName . '_session'])){
			$model = $_SESSION['weS'][$sview->toolName . '_session'];
		} else {
			$model = new we_search_model;
		}
		$model->initByHttp($whichsearch, true);
		$sview->Model = $model;
		$sview->rpcCmd = 'GetSearchResult';
		$content = $sview->searchclass->searchProperties($whichsearch, $sview->Model);
		$code = $sview->tabListContent($setView, $content, $class = 'middlefont', $whichsearch);

		$resp->setData('data', $code);

		return $resp;
	}

}
