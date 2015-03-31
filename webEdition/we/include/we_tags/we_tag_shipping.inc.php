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
function we_tag_shipping($attribs){
	if(($foo = attributFehltError($attribs, "sum", __FUNCTION__))){
		return $foo;
	}

	$sumName = weTag_getAttribute('sum', $attribs, '', we_base_request::STRING);
	if(!isset($GLOBALS['summe'][$sumName])){
		return 0;
	}

	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);

	// shipping depends on total value of basket
	$orderVal = $GLOBALS['summe'][$sumName];
	$weShippingControl = we_shop_shippingControl::getShippingControl();

	// check if user is registered
	$customer = (we_tag('ifRegisteredUser') ? $_SESSION['webuser'] : false);

	$shippingCost = $weShippingControl->getShippingCostByOrderValue($orderVal, $customer);

	// get calculated value if needed
	// if user must NOT pay vat always return net prices
	$mustPayVat = we_tag('ifShopPayVat'); // alayways return net prices

	switch($type){
		case 'net':
			//no difference if $mustPayVat or not
			if(!$weShippingControl->isNet){
				// y = x * (100/116)
				$shippingCost = $shippingCost * (100 / ((1 + ($weShippingControl->vatRate / 100)) * 100) );
			}
			break;
		case 'gros':
			if($weShippingControl->isNet){
				// y = x * (1.16)
				$shippingCost = ($mustPayVat ?
						$shippingCost * (1 + ($weShippingControl->vatRate / 100)) :
						//return net prices
						$shippingCost * (100 / ((1 + ($weShippingControl->vatRate / 100)) * 100) )
					);
			}
			break;
		case 'vat':
			$shippingCost = ($mustPayVat ?
					($weShippingControl->isNet ?
						// y = x * 0.16
						$shippingCost * ($weShippingControl->vatRate / 100) :
						// y = x /116 * 16
						$shippingCost / ( ((1 + ($weShippingControl->vatRate / 100)) * 100) ) * $weShippingControl->vatRate) :
					0);
			break;
		default:
	}

	return we_util_Strings::formatNumber($shippingCost, weTag_getAttribute('num_format', $attribs, '', we_base_request::STRING));
}
