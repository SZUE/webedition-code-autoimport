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
function we_tag_ifCat($attribs){
	$categories = weTag_getAttribute('categories', $attribs, weTag_getAttribute('category', $attribs, array(), we_base_request::STRING_LIST), we_base_request::STRING_LIST);
	$catids = weTag_getAttribute('categoryids', $attribs, array(), we_base_request::INTLISTA);
	if(!$categories && !$catids){
		if(($foo = attributFehltError($attribs, 'categories', __FUNCTION__))){
			echo $foo;
			return false;
		}
	}

	$parent = weTag_getAttribute('parent', $attribs, false, we_base_request::BOOL);
	$docAttr = weTag_getAttribute('doc', $attribs, 'self', we_base_request::STRING);

	if($docAttr === 'listview' && isset($GLOBALS['lv'])){
		$cat = $GLOBALS['lv']->f('wedoc_Category');
	} else {
		$doc = we_getDocForTag($docAttr);
		$cat = $doc->Category;
	}

	if($catids){
		if($parent){
			$categories = id_to_path($catids, CATEGORY_TABLE, $GLOBALS['DB_WE'], false, true);
		} else {
			//no need to query db
			$cat = array_filter(array_map('intval', explode(',', $cat)));
			$categories = array_filter($catids);
			return (array_intersect($cat, $categories) ? true : false);
		}
	}

	$DocCatsPaths = id_to_path($cat, CATEGORY_TABLE, $GLOBALS['DB_WE'], false, true, $parent);

	foreach($categories as $match){
		$match = '/' . ltrim($match, '/');
		if($parent){
			if(in_array($match, $DocCatsPaths) || in_array($match . '/', $DocCatsPaths)){
				return true;
			}
		} else if(in_array($match, $DocCatsPaths)){
			return true;
		}
	}
	return false;
}
