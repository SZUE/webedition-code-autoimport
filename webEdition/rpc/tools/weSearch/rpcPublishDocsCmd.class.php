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
class rpcPublishDocsCmd extends we_rpc_cmd{

	function execute(){

		$db = $GLOBALS['DB_WE'];

		we_html_tools::protect();

		$docs = [];

		$arr = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
		if($arr){
			$allDocs = explode(",", $arr);
			foreach($allDocs as $k => $v){
				$teile = explode("_", $v, 2);
				$docs[$teile[1]][] = $teile[0];
			}
		}
		foreach($docs as $k => $v){
			if(!empty($v)){
				foreach($v as $key => $val){
					$ContentType = f('SELECT ContentType FROM `' . $db->escape($k) . '` WHERE ID=' . intval($val), 'ContentType', $db);
					$object = we_exim_contentProvider::getInstance($ContentType, $val, $k);
					/* bugs #6189 & 4859
					  we_temporaryDocument::delete($object->ID,$db);
					  $object->initByID($object->ID);
					  $object->ModDate = $object->Published;
					 */
					$_SESSION['weS']['versions']['doPublish'] = true;
					$object->we_save();
					$object->we_publish();
					if(defined('WORKFLOW_TABLE') && $object->ContentType == we_base_ContentTypes::WEDOCUMENT){
						if(we_workflow_utility::inWorkflow($object->ID, $object->Table)){
							we_workflow_utility::removeDocFromWorkflow($object->ID, $object->Table, $_SESSION['user']["ID"], "");
						}
					}
					unset($_SESSION['weS']['versions']['doPublish']);
				}
			}
		}

		return new we_rpc_response();
	}

}
