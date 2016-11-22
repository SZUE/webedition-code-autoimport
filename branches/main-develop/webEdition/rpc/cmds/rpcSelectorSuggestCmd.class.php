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
class rpcSelectorSuggestCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();
		$query = we_base_request::_(we_base_request::FILE, 'we_cmd', false, 'query');
		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', false, 'table');
		if(!$query || !$table){
			exit();
		}

		$selectorSuggest = new we_selector_query();
		$contentTypes = explode(",", we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'contenttypes'));
		$currentDocumentType = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 'currentDocumentType');
		$currentDocumentID = we_base_request::_(we_base_request::INT, 'we_cmd', '', 'currentDocumentID');
		if($currentDocumentType && $currentDocumentID){
			if($table == (defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : '-1') && $currentDocumentType == we_base_ContentTypes::TEMPLATE){
				$selectorSuggest->addCondition('AND', '!=', 'ID', $currentDocumentID);
			}
		}
		$selectorSuggest->search($query, $table, $contentTypes,we_base_request::_(we_base_request::INT, 'we_cmd', 0, 'max'), we_base_request::_(we_base_request::FILE, 'we_cmd', '', 'basedir'));
		$resp->setData('data', $selectorSuggest->getResult());

		return $resp;
	}

}
