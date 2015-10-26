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
	$showpath = weTag_getAttribute('showpath', $attribs, false, we_base_request::BOOL);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '', we_base_request::FILE);
	$field = weTag_getAttribute('field', $attribs, '', we_base_request::STRING);
	$dosave = weTag_getAttribute('dosave', $attribs, true, we_base_request::BOOL);

	$ret = '';
	if($GLOBALS['we_editmode']){
		if(!$id){
			$attribs['fromTag'] = 'shopcategory';
			$attribs['_name_orig'] = WE_SHOP_CATEGORY_FIELD_NAME;
			$attribs['field'] = 'PATH';
			$attribs['showpath'] = true;
			$attribs['firstentry'] = ' ';
			$attribs['shopCatIDs'] = implode(',', we_shop_category::getShopCatFieldsFromDir('ID', true));

			return we_tag_category($attribs);
		}

		if($id && $dosave){
			$ret .= we_html_element::htmlHidden('we_' . $GLOBALS['we_doc']->Name . '_category[' . WE_SHOP_CATEGORY_FIELD_NAME . ']', $id);
		}
	}

	$shopCatId = $GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME) ? : 0;
	$ret .= we_shop_category::getShopCatFieldByID($shopCatId, $GLOBALS['we_doc']->Category, $field, $showpath, $rootdir, true);

	return $ret;
}
