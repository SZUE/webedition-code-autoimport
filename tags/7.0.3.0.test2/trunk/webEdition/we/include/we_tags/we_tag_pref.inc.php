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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_pref(array $attribs){
	if(($foo = attributFehltError($attribs, array('type' => false, 'name' => false), __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);

	switch(($type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING))){
		case 'shop':
			if(($foo = attributFehltError($attribs, array('field' => false), __FUNCTION__))){
				return $foo;
			}
			$field = weTag_getAttribute('field', $attribs, '', we_base_request::STRING);
			switch($name){
				case 'vatRule':
					$vat = we_shop_vatRule::getShopVatRule();
					if(isset($vat->$field)){
						return is_array($vat->$field) ? implode(',', $vat->$field) : '';
					}
					break;
				case 'shippingControl':
					$ship = we_shop_shippingControl::getShippingControl();
					if(isset($ship->$field)){
						return is_array($ship->$field) ? implode(',', $ship->$field) : '';
					}
					break;
				case 'statusMails':
					$ship = we_shop_statusMails::getShopStatusMails();
					if(isset($ship->$field)){
						return is_array($ship->$field) ? implode(',', $ship->$field) : '';
					}
					break;
				case 'pref':
			}

			break;
		case 'banner':
			switch($name){
				case 'DefaultBanner':
					return f('SELECT bannerID FROM ' . BANNER_TABLE . ' b JOIN ' . SETTINGS_TABLE . ' p ON p.pref_value=b.ID WHERE p.tool="banner" AND p.pref_name="DefaultBannerID"');
			}
			break;
		case 'newsletter':
			static $newsSet = false;
			$newsSet = $newsSet ? : we_newsletter_view::getSettings();
			if(isset($newsSet[$name])){
				return $newsSet[$name];
			}
			break;
	}
	t_e('pref not found in module', $attribs);
}
