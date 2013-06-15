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
require_once(WE_MODULES_PATH . 'shop/we_conf_shop.inc.php');

function we_tag_showShopItemNumber($attribs){
	if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
		return $foo;
	}

	$shopname = weTag_getAttribute('shopname', $attribs);
	$option = weTag_getAttribute('option', $attribs, false, true);
	$inputfield = weTag_getAttribute('inputfield', $attribs, false, true);
	$type = weTag_getAttribute('type', $attribs);
	$xml = weTag_getAttribute('xml', $attribs, false, true);
	$num_format = weTag_getAttribute('num_format', $attribs);
	$floatquantities = weTag_getAttribute('floatquantities', $attribs, false, true);

	if(!isset($GLOBALS[$shopname]) || empty($GLOBALS[$shopname])){
		return parseError(sprintf(g_l('parser', '[missing_createShop]'), 'showShopItemNumber'));
	}

	$attr = removeAttribs($attribs, array('option', 'inputfield', 'type', 'start', 'stop', 'shopname', 'nameto', 'to', 'floatquantities', '$num_format'));

	// $type of the field
	$articleType = 'w';

	if(isset($GLOBALS['lv']->Record['OF_ID'])){
		$articleType = 'o';
	}

	$itemQuantity = (isset($GLOBALS['lv']) && isset($GLOBALS['lv']->ShoppingCartKey) ?
			$GLOBALS[$shopname]->Get_Item_Quantity($GLOBALS['lv']->ShoppingCartKey) :
			0);

	if($option || ($type == 'select')){

		$start = weTag_getAttribute('start', $attribs, 0);
		$stop = weTag_getAttribute('stop', $attribs, 10);
		$step = weTag_getAttribute('step', $attribs, 1);
		$stop = ($floatquantities ?
				( $stop > $itemQuantity ? $stop : $itemQuantity) :
				( intval($stop) > intval($itemQuantity) ? $stop : $itemQuantity));

		$attr['name'] = 'shop_cart_id[' . $GLOBALS['lv']->ShoppingCartKey . ']';
		$attr['size'] = 1;
		$attr['xml'] = $xml;

		$out = '';
		while($start <= $stop) {
			$out .= ($itemQuantity == $start ?
					getHtmlTag('option', array('xml' => $xml, 'value' => $start, 'selected' => 'selected'), $start) :
					getHtmlTag('option', array('xml' => $xml, 'value' => $start), $start));

			$start = $start + $step;
		}
		return getHtmlTag('select', $attr, $out, true) . getHtmlTag('input', array('type' => 'hidden', 'name' => 't', 'value' => time()));
	} else if($inputfield || ($type == 'textinput')){
		$itemQuantity = ($floatquantities ? we_util_Strings::formatNumber($itemQuantity, $num_format, 2) : intval($itemQuantity));
		$attr = array_merge($attr, array('type' => 'text', 'name' => 'shop_cart_id[' . $GLOBALS['lv']->ShoppingCartKey . ']', 'size' => 2, 'value' => $itemQuantity));
		return getHtmlTag('input', $attr) . getHtmlTag('input', array('type' => 'hidden', 'name' => 't', 'value' => time()));
	} else{
		return ($floatquantities ? we_util_Strings::formatNumber($itemQuantity, $num_format, 2) : intval($itemQuantity));
	}
}