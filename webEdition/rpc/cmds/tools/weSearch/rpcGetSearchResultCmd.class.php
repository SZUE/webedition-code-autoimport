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
		$whichsearch = weRequest('string', 'whichsearch', '');
		$setView = weRequest('string', 'we_cmd', '', 'setView' . $whichsearch);

		$_REQUEST['we_cmd']['obj'] = unserialize($_SESSION['weSearch_session']);

		$content = we_search_view::searchProperties($whichsearch);

		$code = we_search_view::tabListContent($setView, $content, $class = 'middlefont', $whichsearch);

		$resp->setData('data', $code);

		return $resp;
	}

}
