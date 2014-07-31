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
class rpcSelectorSuggestCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		$cmd1 = we_base_request::_(we_base_request::FILE, 'we_cmd', false, 1);
		$cmd2 = we_base_request::_(we_base_request::TABLE, 'we_cmd', false, 2);
		if(!$cmd1 || !$cmd2){
			exit();
		}

		$selectorSuggest = new we_selector_query();
		$contentTypes = explode(",", we_base_request::_(we_base_request::STRINGC, 'we_cmd', '', 3));
		$cmd4 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 4);
		$cmd5 = we_base_request::_(we_base_request::INT, 'we_cmd', '', 5);
		if($cmd4 && $cmd5){
			if($cmd2 == (defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : '-1') && $cmd4 == we_base_ContentTypes::TEMPLATE){
				$selectorSuggest->addCondition(array('AND', '!=', 'ID', $cmd5));
			}
		}
		$selectorSuggest->search($cmd1, $cmd2, $contentTypes, "", we_base_request::_(we_base_request::FILE, 'we_cmd', '', 6));
		$resp->setData("data", $selectorSuggest->getResult());

		return $resp;
	}

}
