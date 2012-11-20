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
function we_tag_ifCat($attribs){

	$categories = weTag_getAttribute('categories', $attribs);
	$category = weTag_getAttribute('category', $attribs);

	if(strlen($categories) == 0 && strlen($category) == 0){
		if(($foo = attributFehltError($attribs, 'categories', __FUNCTION__))){
			print($foo);
			return false;
		}
	}

	$parent = weTag_getAttribute('parent', $attribs, false, true);

	$docAttr = weTag_getAttribute('doc', $attribs, 'self');

	$match = $categories ? $categories : $category;
	$matchArray = makeArrayFromCSV($match);

	if($docAttr == 'listview' && isset($GLOBALS['lv'])){
		$DocCatsPaths = id_to_path($GLOBALS['lv']->f('wedoc_Category'), CATEGORY_TABLE, $GLOBALS['DB_WE'], true, false, $parent);
	} else{
		$doc = we_getDocForTag($docAttr);
		$DocCatsPaths = id_to_path($doc->Category, CATEGORY_TABLE, $GLOBALS['DB_WE'], true, false, $parent);
	}

	foreach($matchArray as $match){
		$match = '/' . ltrim($match, '/');
		if($parent){
			if(strpos($DocCatsPaths, ',' . $match . ',') !== false || strpos($DocCatsPaths, ',' . $match . '/') !== false){
				return true;
			}
		} else{
			if(!(strpos($DocCatsPaths, ',' . $match . ',') === false)){
				return true;
			}
		}
	}
	return false;
}
