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
	$id = weTag_getAttribute('id', $attribs, false, we_base_request::INT);
	$fromdoc = weTag_getAttribute('fromdoc', $attribs, false, we_base_request::BOOL);
	$showpath = weTag_getAttribute('showpath', $attribs, false, we_base_request::BOOL);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '', we_base_request::FILE);
	$field = weTag_getAttribute('field', $attribs, '', we_base_request::STRING);
	$dosave = weTag_getAttribute('dosave', $attribs, true, we_base_request::BOOL);

	$fieldMap = array(
		'id' => 'ID',
		'category' => 'Category',
		'path' => 'Path',
		'title' => 'Title',
		'description' => 'Description',
		'is_destinationprinciple' => 'DestPrinciple',
		'is_fallback_to_standard' => 'is_fallback_to_standard',
		'is_fallback_to_active' => 'is_fallback_to_active'
	);
	$field = isset($fieldMap[$field]) ? $fieldMap[$field] : 'ID';

	$ret = '';
	if($GLOBALS['we_editmode']){
		$attribs['onlyindir'] = we_shop_category::getShopCatDir(true);
		$attribs['fromTag'] = 'shopcategory';

		if(!($id || $fromdoc)){
			$attribs['_name_orig'] = WE_SHOP_CATEGORY_FIELD_NAME;
			$attribs['field'] = 'PATH';
			$attribs['showpath'] = true;

			return we_tag_category($attribs);
		}

		$attribs['field'] = 'ID';
		$catIDs = explode(',', trim(we_tag_category($attribs), ','));
		if(count($catIDs) && $dosave){
			$ret .= we_html_element::htmlHidden(array('name' => 'we_' . $GLOBALS['we_doc']->Name . '_category[' . WE_SHOP_CATEGORY_FIELD_NAME . ']', 'value' => $catIDs[0]));
		}
	}

	$shopCatId = $GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0;
	$ret .= we_shop_category::getShopCatFieldByID($shopCatId, $field, $showpath, $rootdir, true, !we_shop_category::USE_IS_ACTIVE);

	return $ret;
}
