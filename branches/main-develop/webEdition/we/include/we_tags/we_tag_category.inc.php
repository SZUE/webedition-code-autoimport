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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_category($attribs){

	// initialize possible Attributes
	$delimiter = weTag_getAttribute("delimiter", $attribs);
	if($delimiter === ""){
		$delimiter = weTag_getAttribute("tokken", $attribs, "-");
	}

	$rootdir = weTag_getAttribute("rootdir", $attribs);
	$showpath = weTag_getAttribute("showpath", $attribs, false, true);
	$docAttr = weTag_getAttribute("doc", $attribs);
	$field = weTag_getAttribute("field", $attribs);
	$id = intval(weTag_getAttribute("id", $attribs));
	$separator = weTag_getAttribute("separator", $attribs, "/");
	$onlyindir = weTag_getAttribute("onlyindir", $attribs);

	// end initialize possible Attributes
	if($id){
		$category = str_replace(
			"\\,", ",", we_getCatsFromIDs($id, $delimiter, $showpath, $GLOBALS['DB_WE'], $rootdir, $field, $onlyindir));
		return str_replace("/", $separator, $category);
	}

	$isInListview = isset($GLOBALS["lv"]) && (!$docAttr);

	if($isInListview){
		// get cats from listview object
		switch($GLOBALS["lv"]->ClassName){
			case "we_listview_object" :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
				break;
			case "we_search_listview" :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
				break;
			default :
				$catIDs = $GLOBALS["lv"]->f("wedoc_Category");
		}

		$category = $catIDs ? str_replace(
				"\\,", ",", we_getCatsFromIDs($catIDs, $delimiter, $showpath, $GLOBALS['DB_WE'], $rootdir, $field, $onlyindir)) : "";
		return str_replace("/", $separator, $category);
	} else{
		$doc = we_getDocForTag($docAttr, false);
		$category = str_replace(
			"\\,", ",", we_getCatsFromDoc($doc, $delimiter, $showpath, $GLOBALS['DB_WE'], $rootdir, $field, $onlyindir));
		return str_replace("/", $separator, $category);
	}
}
