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

function we_tag_ifShopPayVat($attribs,$content) {
	require_once(WE_SHOP_MODULE_DIR . 'weShopVatRule.class.php');
	include_once(WE_CUSTOMER_MODULE_DIR . "we_customertag.inc.php");
	$customerid = we_getTagAttribute("customerid",$attribs);
	$weShopVatRule = weShopVatRule::getShopVatRule();
	if (we_tag('ifRegisteredUser',array(), '')) {
		$customer = $_SESSION['webuser'];
	} else {
		if ($customerid){
			$cus= new we_customertag($customerid);
			$customerarray = $cus->object->DB_WE->Record;
			if ($customerarray){
				$customer = $customerarray;
			} else {
				$customer = false;
			}
		} else {
			$customer = false;
		}
	}


	return $weShopVatRule->executeVatRule($customer);

}
