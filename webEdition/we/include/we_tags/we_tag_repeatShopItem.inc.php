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
function we_parse_tag_repeatShopItem($a, $content, $attribs){
	if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
		return $foo;
	}

	$attribs['_type'] = 'start';
	return '<?php ' . we_tag_tagParser::printTag('repeatShopItem', $attribs) . '; while($GLOBALS[\'lv\']->next_record()) {?>' . $content . '<?php } ' . we_tag_tagParser::printTag('repeatShopItem', ['_type' => 'stop']) . ';?>';
}

function we_tag_repeatShopItem(array $attribs){
	if(!defined('SHOP_ORDER_TABLE')){
		echo modulFehltError('Shop', __FUNCTION__);
		return;
	}
	$shopname = weTag_getAttribute('shopname', $attribs, '', we_base_request::STRING);

	//internal Attribute
	$intType = weTag_getAttribute('_type', $attribs, '', we_base_request::STRING);
	switch($intType){
		case 'start':
			if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
				echo $foo;
				return;
			}
			we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP);

			$_SESSION["we_shopname"] = $shopname;

			if(!isset($GLOBALS[$shopname]) || empty($GLOBALS[$shopname])){
				echo parseError(sprintf(g_l('parser', '[missing_createShop]'), 'repeatShopItem'));
			}
			$lv = new we_shop_shop($GLOBALS[$shopname]);
			we_pre_tag_listview($lv);
			break;
		case 'stop':
			we_post_tag_listview();
			break;
	}
}
