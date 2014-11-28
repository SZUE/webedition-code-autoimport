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
require_once (WE_INCLUDES_PATH . 'we_tags/we_tag_category.inc.php');

function we_tag_shopCategory($attribs){
	$db = $GLOBALS['DB_WE'];

	$fromid = weTag_getAttribute('id', $attribs);
	$fromdoc = weTag_getAttribute('fromdoc', $attribs, false, true);
	$showpath = weTag_getAttribute('showpath', $attribs, false, true);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$onlyindir = we_shop_category::getShopCatsDir(true);
	$show = weTag_getAttribute('show', $attribs, 'category');
	$field = weTag_getAttribute(($show === 'vat' ? 'vatfield' : 'catfield'), $attribs);

	$attribs['onlyindir'] = $onlyindir;
	$attribs['fromTag'] = 'shopcategory';

	if($GLOBALS['we_editmode'] && !($fromid || $fromdoc)){
		$attribs['_name_orig'] = WE_SHOP_CATEGORY_FIELD_NAME;
		$attribs['field'] = 'PATH';
		$attribs['showpath'] = true;
		return we_tag_category($attribs);
	} else {
		$ret = $show === 'vat' ? 'show vat' : 
				we_shop_category::getShopCategoriesFromIDs($GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME), $field, false, 0, $onlyindir, false, false, ',', $showpath, $rootdir);

		if($GLOBALS['we_editmode']){
			$attribs['field'] = 'ID';
			$catIDs = explode(',', trim(we_tag_category($attribs), ','));
			if(count($catIDs)){
				$ret .= we_html_element::htmlHidden(array('name' => 'we_' . $GLOBALS['we_doc']->Name . '_category[we_shopCategory]', 'value' => $catIDs[0]));// . 
			}
		}

		return $ret;
	}
}