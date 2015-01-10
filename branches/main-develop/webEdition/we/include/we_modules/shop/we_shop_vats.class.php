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
	public static function getAllShopVATs(){
		if(!isset($GLOBALS['weShopVats']['getAllVats'])){
			$GLOBALS['DB_WE']->query('SELECT id,text,vat,standard,territory,textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' ORDER BY vat');
			$GLOBALS['weShopVats']['getAllVats'] = array();

			while($GLOBALS['DB_WE']->next_record()){
				$GLOBALS['weShopVats']['getAllVats'][$GLOBALS['DB_WE']->f('id')] = new we_shop_vat($GLOBALS['DB_WE']->f('id'), $GLOBALS['DB_WE']->f('text'), $GLOBALS['DB_WE']->f('vat'), ($GLOBALS['DB_WE']->f('standard') ? 1 : 0), $GLOBALS['DB_WE']->f('territory'), $GLOBALS['DB_WE']->f('textProvince'), $GLOBALS['DB_WE']->f('categories'));
			}
		}
		usort($GLOBALS['weShopVats']['getAllVats'], function($a, $b){
			return ($ret = strcmp($a->textTerritorySortable, $b->textTerritorySortable)) !== 0 ? $ret : ($a->vat > $b->vat ? 1 : -1);
		});

		return $GLOBALS['weShopVats']['getAllVats'];
	}

	public static function getShopVATById($id){
		if(!isset($GLOBALS['weShopVats']['getShopVATById'][$id])){
			$GLOBALS['weShopVats']['getShopVATById'][$id] = we_shop_vat::getVatById($id);
		}

		return $GLOBALS['weShopVats']['getShopVATById'][$id];
	}

	public static function getVatRateForSite($id = false, $fallBackToStandard = true, $standard = ''){
		if($id){
			$weShopVat = we_shop_vats::getShopVATById($id);
		}

		if(!isset($weShopVat) || !$weShopVat){
			$weShopVat = we_shop_vats::getStandardShopVat();
		}

		return ($weShopVat ? $weShopVat->vat : ($standard && is_int($standard) ? $standard : false));
	}

	public static function getStandardShopVat(){
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
}
