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
function we_tag_category($attribs){
	// initialize possible Attributes
	$delimiter = weTag_getAttribute('delimiter', $attribs, weTag_getAttribute('tokken', $attribs, ',', we_base_request::RAW), we_base_request::RAW);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '', we_base_request::FILE);
	$showpath = weTag_getAttribute('showpath', $attribs, false, we_base_request::BOOL);
	$docAttr = weTag_getAttribute('doc', $attribs, '', we_base_request::STRING);
	$field = weTag_getAttribute('field', $attribs, '', we_base_request::STRING);//FIXME: this is bool!
	$name = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::INTLIST);
	$separator = weTag_getAttribute('separator', $attribs, '/', we_base_request::RAW);
	$onlyindir = weTag_getAttribute('onlyindir', $attribs, '', we_base_request::FILE);
	$fromTag = weTag_getAttribute('fromTag', $attribs, 'category', we_base_request::STRING);
	$shopCatIDs = weTag_getAttribute('shopCatIDs', $attribs, -1, we_base_request::INTLIST);
	// end initialize possible Attributes

	if($id){
		$catIDs = $id;
	} elseif($name){
		if($GLOBALS['we_editmode'] && $name){
			$_REQUEST['we_' . $GLOBALS['we_doc']->Name . '_category[' . $name . ']'] = $GLOBALS['we_doc']->getElement($name);
			$attribs['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_category[' . $name . ']';
			$attribs['type'] = 'request';
			$attribs['rootdir'] = $onlyindir;
			$attribs['fromTag'] = $fromTag;
			$doc = we_getDocForTag($docAttr, false);
			$attribs['catIDs'] = $fromTag === 'shopcategory' ? $shopCatIDs : $doc->Category;

			return we_tag('categorySelect', $attribs);
		}
		$catIDs = $GLOBALS['we_doc']->getElement($name);
	} elseif(isset($GLOBALS['lv']) && $docAttr === 'listview'){
		$catIDs = $GLOBALS['lv']->f('wedoc_Category');
	} else {
		$doc = we_getDocForTag($docAttr, false);
		$catIDs = $doc->Category;
	}
	$catIDs = implode(',', array_filter(array_map('intval', explode(',', $catIDs))));
	$category = array_filter(we_category::we_getCatsFromIDs($catIDs, $delimiter, $showpath, $GLOBALS['DB_WE'], $rootdir, $field, $onlyindir, true));

	if(!$category){
		return '';
	}

	foreach($category as &$cat){
		$cat = str_replace('/', $separator, $cat);
	}
	return implode($delimiter, $category);
}
