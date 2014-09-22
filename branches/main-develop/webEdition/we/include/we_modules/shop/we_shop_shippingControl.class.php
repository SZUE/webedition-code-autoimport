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
class we_shop_shippingControl{

	var $stateField = '';
	var $isNet = true;
	var $vatId = 0;
	var $shippings = array();
	var $vatRate = 0;

	private function __construct($stateField, $isNet, $vatId, $shippings){
		$this->stateField = $stateField;
		$this->isNet = $isNet;
		$this->vatId = $vatId;
		$this->shippings = $shippings;

		$this->vatRate = we_shop_vats::getVatRateForSite($vatId);
	}

	public static function getShippingControl(){
		$data = f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="weShippingControl"');

		if($data){
			$shippingControl = unserialize(strtr($data, array('O:17:"weShippingControl"' => 'O:' . strlen(__CLASS__) . ':"' . __CLASS__ . '"', 'O:10:"weShipping"' => 'O:' . strlen('we_shop_shipping') . ':"we_shop_shipping"')));
			$shippingControl->vatRate = we_shop_vats::getVatRateForSite($shippingControl->vatId);
			return $shippingControl;
		}
		return new self('', 1, 1, array());
	}

	function setByRequest($req){//FIXME: bad this is unchecked
		// this function inits a new entry, also it could change existing items
		$this->stateField = $req['stateField'];
		$this->isNet = $req['isNet'];
		$this->vatId = $req['vatId'];

		if(isset($req['weShippingId'])){

			$newShipping = new we_shop_shipping(
					$req['weShippingId'], $req['weShipping_text'], we_shop_vatRule::makeArrayFromReq($req['weShipping_countries']), $req['weShipping_cartValue'], $req['weShipping_shipping'], ($req['weShipping_default'] == '1' ? 1 : 0)
			);
			$this->shippings[$req['weShippingId']] = $newShipping;

			if($newShipping->default){
				foreach($this->shippings as $id => &$shipping){
					if($id != $req['weShippingId']){
						$shipping->default = 0;
					}
				}
			}
		}
	}

	function save(){
		$DB_WE = $GLOBALS['DB_WE'];

		return $DB_WE->query('REPLACE INTO ' . WE_SHOP_PREFS_TABLE . ' SET ' .
						we_database_base::arraySetter(array(
							'strDateiname' => 'weShippingControl',
							'strFelder' => serialize($this)
		)));
	}

	function delete($id){
		if(isset($this->shippings[$id])){
			unset($this->shippings[$id]);
		}
		$this->save();
	}

	function getShippingById($id){
		return $this->shippings[$id];
	}

	function getDefaultShipping(){

		foreach($this->shippings as $shipping){
			if($shipping->default){
				return $shipping;
			}
		}
		return false;
	}

	function getShippingCostByOrderValue($orderValue, $customer = false){

		if($customer){
			// foreach, search the shipping

			if(isset($customer[$this->stateField])){

				foreach($this->shippings as $key => $tmpShipping){
					if(in_array($customer[$this->stateField], $tmpShipping->countries)){
						$shipping = $tmpShipping;
						continue;
					}
				}
			}
		}
		if(!isset($shipping)){ // take default shipping
			$shipping = $this->getDefaultShipping();
		}

		if($shipping){

			$shippingId = 0;

			for($i = 0; $i < count($shipping->cartValue); $i++){

				if($shipping->cartValue[$i] > $orderValue){
					continue;
				} else {
					$shippingId = $i;
				}
			}
			return $shipping->shipping[$shippingId];
		}
		return 0;
	}

}
