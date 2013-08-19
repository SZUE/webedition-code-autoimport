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
function we_tag_ifShopVat($attribs){
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		print $foo;
		return false;
	}
	$id = weTag_getAttribute('id', $attribs, -1);

	$vatId = (isset($GLOBALS['lv']) && $GLOBALS['lv']->f(WE_SHOP_VAT_FIELD_NAME) ?
			$GLOBALS['lv']->f(WE_SHOP_VAT_FIELD_NAME) :
			$GLOBALS['we_doc']->getElement(WE_SHOP_VAT_FIELD_NAME));


	if(!$vatId){
		$shopVat = weShopVats::getStandardShopVat();
		if($shopVat){
			$vatId = $shopVat->id;
		}
	}
	return ($id == $vatId);
}
