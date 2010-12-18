<?php
/**
 * webEdition CMS
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

function we_tag_shipping($attribs, $content) {
	$foo = attributFehltError($attribs,"sum","shipping");if($foo) return $foo;

	$sumName = we_getTagAttribute('sum', $attribs);
	$num_format = we_getTagAttribute('num_format', $attribs);
	$type = we_getTagAttribute('type', $attribs, '');

	$nameTo = we_getTagAttribute("nameto", $attribs);
	$to = we_getTagAttribute("to", $attribs,'screen');

	$shippingCost = 0;

	// shipping depends on total value of basket
	if (isset($GLOBALS['summe'][$sumName])) {

		require_once(WE_SHOP_MODULE_DIR . 'weShippingControl.class.php');

		$orderVal = $GLOBALS['summe'][$sumName];
		$weShippingControl = weShippingControl::getShippingControl();

		if (we_tag('ifRegisteredUser',array(), '')) { // check if user is registered
			$customer = $_SESSION['webuser'];
		} else {
			$customer = false;
		}

		$shippingCost = $weShippingControl->getShippingCostByOrderValue($orderVal, $customer);

		// get calculated value if needed
		if ($type) {
			// if user must NOT pay vat always return net prices
			$mustPayVat = we_tag('ifShopPayVat',array(), ''); // alayways return net prices

			if ($mustPayVat) {

				switch ($type) {

					case 'net':
						if (!$weShippingControl->isNet) {
							// y = x * (100/116)
							$shippingCost = $shippingCost * (100/ ((1 + ($weShippingControl->vatRate/100)) * 100) );
						}
					break;

					case 'gros':
						if ($weShippingControl->isNet) {
							// y = x * (1.16)
							$shippingCost = $shippingCost * (1 + ($weShippingControl->vatRate/100));
						}
					break;

					case 'vat':
						if ($weShippingControl->isNet) {
							// y = x * 0.16
							$shippingCost = $shippingCost * ($weShippingControl->vatRate/100);
						} else {
							// y = x /116 * 16
							$shippingCost = $shippingCost / ( ((1 + ($weShippingControl->vatRate/100)) * 100) ) * $weShippingControl->vatRate;
						}
					break;
				}

			} else { // always return net prices
				switch ($type) {

					case 'gros':
					case 'net':
						if (!$weShippingControl->isNet) {
							// y = x * (100/116)
							$shippingCost = $shippingCost * (100/ ((1 + ($weShippingControl->vatRate/100)) * 100) );
						}
					break;
					case 'vat':
						$shippingCost = 0;
					break;
				}
			}
		}

		switch($num_format){
		case 'french':
			$shippingCost=number_format($shippingCost,2,","," ");
			break;
		case 'english':
			$shippingCost=number_format($shippingCost,2,".","");
			break;
		case 'swiss':
			$shippingCost=number_format($shippingCost,2,".", "'");
			break;
		case 'german':
		default:
			$shippingCost=number_format($shippingCost,2,",",".");
		}
		return we_redirect_tagoutput($shippingCost,$nameTo,$to);
	}
	return 0;
}
