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
class we_shop_vats{
	function getAllShopVATs(){
		if(!isset($GLOBALS['weShopVats']['getAllVats'])){
			$GLOBALS['DB_WE']->query('SELECT id,text,vat,standard,territory,textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' ORDER BY territory');
			$GLOBALS['weShopVats']['getAllVats'] = array();

			while($GLOBALS['DB_WE']->next_record()){
				$GLOBALS['weShopVats']['getAllVats'][$GLOBALS['DB_WE']->f('id')] = new we_shop_vat($GLOBALS['DB_WE']->f('id'), $GLOBALS['DB_WE']->f('text'), $GLOBALS['DB_WE']->f('vat'), ($GLOBALS['DB_WE']->f('standard') ? 1 : 0), $GLOBALS['DB_WE']->f('territory'), $GLOBALS['DB_WE']->f('textProvince'), $GLOBALS['DB_WE']->f('categories'));
			}
		}

		return $GLOBALS['weShopVats']['getAllVats'];
	}

	function getShopVATById($id){
		if(!isset($GLOBALS['weShopVats']['getShopVATById'][$id])){
			$GLOBALS['weShopVats']['getShopVATById'][$id] = we_shop_vat::getVatById($id);
		}

		return $GLOBALS['weShopVats']['getShopVATById'][$id];
	}

	function getVatRateForSite($id = false, $fallBackToStandard = true, $standard = ''){
		if($id){
			$weShopVat = we_shop_vats::getShopVATById($id);
		}

		if(!isset($weShopVat) || !$weShopVat){
			$weShopVat = we_shop_vats::getStandardShopVat();
		}

		return ($weShopVat ? $weShopVat->vat : $standard);
	}

	//TODO: check calls and make static 
	function getStandardShopVat(){
		if(!isset($GLOBALS['weShopVats']['getStandardShopVat'])){
			$data = getHash('SELECT id, text, vat, standard, territory, textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' WHERE standard=1');

			$GLOBALS['weShopVats']['getStandardShopVat'] = ($data ?
					new we_shop_vat($data['id'], $data['text'], $data['vat'], true, $data['territory'], $data['textProvince'], $data['categories']) :
					false);
		}

		return $GLOBALS['weShopVats']['getStandardShopVat'];
	}

	function saveWeShopVAT($weShopVat){

		// 1st - change standard for every entry
		if($weShopVat->standard == 1){
			$query = 'UPDATE ' . WE_SHOP_VAT_TABLE . ' SET standard = 0 WHERE 1';
			$GLOBALS['DB_WE']->query($query);
		}

		return $weShopVat->save();
	}

	function deleteVatById($id){
		$vat = new we_shop_vat($id);

		return $vat->delete();
	}

	public static function getVatByCountryCategory($country, $category){
		$db = $GLOBALS['DB_WE'];
		$country = $country ? : f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_location"', '', $db, -1);

		if(!($category = intval($category)) || !($country = $db->escape($country))){
			return false;
		}

		//for debugging purpose only: when num_rows > 1 we have an inconsistent tblshopvat!
		$db->query('SELECT id,text,vat,standard,territory,textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' WHERE territory="' . $country . '" AND FIND_IN_SET(' . $category . ', categories)');
		if($db->num_rows() > 1){
			t_e("function getVatByCountryCategory", "number of results: " . $db->num_rows());
		}
		//end debug

		$hash = getHash('SELECT id,text,vat,standard,territory,textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' WHERE territory="' . $country . '" AND FIND_IN_SET(' . $category . ', categories)');
		if($hash){
			return new we_shop_vat($hash['id'], $hash['text'], $hash['vat'], ($hash['standard'] ? 1 : 0), $hash['territory'], $hash['textProvince'], $hash['categories']);
		} else if(($standard = self::getStandardShopVat())){
			return $standard;
		} else {
			$shopPrefs = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"'));
			if(($pref = $shopPrefs[1])){
				new we_shop_vat(0, 'shop_pref', $pref, 1, '', '', '');
			}
		}
		t_e("function getVatByCountryCategory", "neither vat found nor standard");

		return false;
	}
}
