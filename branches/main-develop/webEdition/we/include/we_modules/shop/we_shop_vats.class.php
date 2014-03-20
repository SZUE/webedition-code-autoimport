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
class we_shop_vats{

	// some arrays for caching results

	function getAllShopVATs(){
		if(!isset($GLOBALS['weShopVats']['getAllVats'])){
			$GLOBALS['DB_WE']->query('SELECT * FROM ' . WE_SHOP_VAT_TABLE);
			$GLOBALS['weShopVats']['getAllVats'] = array();

			while($GLOBALS['DB_WE']->next_record()){
				$GLOBALS['weShopVats']['getAllVats'][$GLOBALS['DB_WE']->f('id')] = new we_shop_vat($GLOBALS['DB_WE']->f('id'), $GLOBALS['DB_WE']->f('text'), $GLOBALS['DB_WE']->f('vat'), ($GLOBALS['DB_WE']->f('standard') ? 1 : 0));
			}
		}
		return $GLOBALS['weShopVats']['getAllVats'];
	}

	function getShopVATById($id){
		if(!isset($GLOBALS['weShopVats']['getShopVATById']["$id"])){
			$data = getHash('SELECT id,text,vat,standard FROM ' . WE_SHOP_VAT_TABLE . ' WHERE id=' . intval($id));
			$GLOBALS['weShopVats']['getShopVATById'][$id] = ($data ?
					new we_shop_vat($data['id'], $data['text'], $data['vat'], ($data['standard'] ? true : false)) :
					false);
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

	function getStandardShopVat(){
		if(!isset($GLOBALS['weShopVats']['getStandardShopVat'])){
			$data = getHash('SELECT id,text,vat FROM ' . WE_SHOP_VAT_TABLE . ' WHERE standard=1');

			$GLOBALS['weShopVats']['getStandardShopVat'] = ($data ?
					new we_shop_vat($data['id'], $data['text'], $data['vat'], true) :
					false);
		}

		return $GLOBALS['weShopVats']['getStandardShopVat'];
	}

	function saveWeShopVAT($weShopVat){

		// 1st - change standard for every entry
		if($weShopVat->standard == 1){

			// delete all other standard values
			$query = 'UPDATE ' . WE_SHOP_VAT_TABLE . ' SET standard = 0 WHERE 1';
			$GLOBALS['DB_WE']->query($query);
		}

		$set = we_database_base::arraySetter(array(
				'text' => $weShopVat->text,
				'vat' => $weShopVat->vat,
				standard => $weShopVat->standard
		));
		if($weShopVat->id == 0){ // insert a new vat
			if($GLOBALS['DB_WE']->query('INSERT INTO ' . WE_SHOP_VAT_TABLE . ' SET ' . $set)){
				return $GLOBALS['DB_WE']->getInsertId();
			}
		} else { // update existing vat
			if($GLOBALS['DB_WE']->query('UPDATE ' . WE_SHOP_VAT_TABLE . ' SET ' . $set . '	WHERE id=' . intval($weShopVat->id))){
				return $weShopVat->id;
			}
		}

		return false;
	}

	function deleteVatById($id){
		return $GLOBALS['DB_WE']->query('DELETE FROM ' . WE_SHOP_VAT_TABLE . ' WHERE id=' . intval($id));
	}

}
