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
class rpcGetCategoryCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		$_error = array();
		// check for necessory params
		if(!($obj=weRequest('string', 'obj'))){
			$_error[] = "Missing field obj";
		}
		if(!weRequest('string', 'cats')){
			$_error[] = "Missing field cats";
		}
		if(weRequest('string', 'part') == "table" && (!weRequest('bool', 'target'))){
			$_error[] = "Missing target for table";
		}

		if($_error){
			$resp->setData("error", $_error);
		} else {
			//$part = weRequest('string', 'part',"rows");
			$target = weRequest('string', 'target', $obj . "CatTable");
			$catField = weRequest('string', 'catfield', '');
			$categories = $this->getCategory($obj, weRequest('intList', 'cats', ''), $catField);
			$categories = strtr($categories, array("\r" => '', "\n" => ''));
			$resp->setData("elementsById", array($target => array("innerHTML" => addslashes($categories)))
			);
		}
		return $resp;
	}

	function getCategory($obj, $categories, $catField = ""){
		$cats = new MultiDirChooser2(410, $categories, "delete_" . $obj . "Cat", "", "", "Icon,Path", CATEGORY_TABLE);
		$cats->setRowPrefix($obj);
		$cats->setCatField($catField);
		return $cats->getTableRows();
	}

}
