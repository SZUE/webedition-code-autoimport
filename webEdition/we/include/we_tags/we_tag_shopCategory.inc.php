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
	$pref = getHash('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE pref_name="shop_cats_dir"', $db);

	$fromid = weTag_getAttribute('id', $attribs);
	$fromdoc = weTag_getAttribute('fromdoc', $attribs, false, true);
	$showpath = weTag_getAttribute('showpath', $attribs, false, true);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$onlyindir = id_to_path($pref['pref_value'], CATEGORY_TABLE, $db);
	$field = weTag_getAttribute('field', $attribs);

	$attribs['onlyindir'] = $onlyindir;
	$attribs['fromTag'] = 'shopcategory';

	if($GLOBALS['we_editmode'] && !($fromid || $fromdoc)){
		$attribs['_name_orig'] = WE_SHOP_CATEGORY_FIELD_NAME;
		$attribs['field'] = 'PATH';
		return we_tag_category($attribs);
	} else {
		if($GLOBALS['we_editmode']){
			$attribs['field'] = 'ID';
			$catIDs = explode(',', trim(we_tag_category($attribs), ','));
			if(count($catIDs)){

				return we_html_element::htmlHidden(array('name' => 'we_' . $GLOBALS['we_doc']->Name . '_category[we_shopCategory]', 'value' => $catIDs[0])) . 
						we_category::we_getCatsFromIDs($catIDs[0], ',', $showpath, $db, $rootdir, $field, $onlyindir, false);
			}
		} else {
			$catIDs = we_category::we_getCatsFromIDs($GLOBALS['we_doc']->getElement(WE_SHOP_CATEGORY_FIELD_NAME), ',', $showpath, $db, $rootdir, $field, $onlyindir, true);

			return count($catIDs) ? $catIDs[0] : '';
		}
	}
}