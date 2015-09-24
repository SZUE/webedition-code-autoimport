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
class we_shop_vatRule{
	public $stateField;
	var $stateFieldIsISO;
	var $liableToVat;
	var $notLiableToVat;
	// a rule is an array containing
	//		states -> array
	//		customerField -> string
	//		condition -> string
	//		returnValue -> 0 | 1
	var $conditionalRules;
	var $defaultValue = 'true';

	private function __construct($defaultValue, $stateField, $liableToVat, $notLiableToVat, $conditionalRules, $stateFieldIsISO = '0'){

		$this->defaultValue = $defaultValue;
		$this->stateField = $stateField;
		$this->stateFieldIsISO = $stateFieldIsISO;
		$this->liableToVat = $liableToVat;
		$this->notLiableToVat = $notLiableToVat;
		$this->conditionalRules = $conditionalRules;
	}

	public function executeVatRule($customer = false, $state = ''){
		// now check all rules for the vat

		if($customer || $state){
			if(isset($this->stateField) && (isset($customer[$this->stateField]) || $state)){
				$state = isset($customer[$this->stateField]) ? $customer[$this->stateField] : ($state ? : false);

				// is state liableToVat
				if(in_array($state, $this->liableToVat)){
					return true;
				}

				// is state not liable to vat
				if(in_array($state, $this->notLiableToVat)){
					return false;
				}

				// if we have $state but no $customer: return here
				if(!$customer){
					return ($this->defaultValue === 'true' ? true : false);
				}

				// now check additional fields
				foreach($this->conditionalRules as $rule){
					$ret = $rule['returnValue'] === 'true' ? true : false;
					$field = $rule['customerField'] ? $customer[$rule['customerField']] : '';

					if(in_array($state, $rule['states'])){
						switch($rule['condition']){
							case 'is_empty':
								return (empty($field) ? $ret : !$ret);
							case 'is_set':
								return (!empty($field) ? $ret : !$ret);
						}
					}
				}
			}
		}

		return ($this->defaultValue === 'true' ? true : false);
	}

	public static function initByRequest(){
		return new self(
			we_base_request::_(we_base_request::STRING, 'defaultValue'), we_base_request::_(we_base_request::STRING, 'stateField'), self::makeArrayFromReq(we_base_request::_(we_base_request::STRING, 'liableToVat')), self::makeArrayFromReq(we_base_request::_(we_base_request::STRING, 'notLiableToVat')), self::makeArrayFromConditionField(), we_base_request::_(we_base_request::STRING, 'stateFieldIsISO')
		);
	}

	public static function getShopVatRule(){
		if(($strFelder = f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="weShopVatRule"'))){
			//FIX old class names
			return we_unserialize(strtr($strFelder, array('O:13:"weShopVatRule":' => 'O:15:"we_shop_vatRule":', 'O:13:"weshopvatrule"' => 'O:15:"we_shop_vatRule":')));
		}
		return new self('true', '', array(), array(), array(
			array(
				'states' => array(),
				'customerField' => '',
				'condition' => '',
				'returnValue' => 1
			)
			), 0
		);
	}

	public static function getStateField(){
		$rule = self::getShopVatRule();

		return $rule->stateField;
	}

	private static function makeArrayFromConditionField(){
		$retArr = array();
		$conditionalStates = we_base_request::_(we_base_request::STRING, 'conditionalStates');
		$conditionalCustomerField = we_base_request::_(we_base_request::STRING, 'conditionalCustomerField');
		$conditionalCondition = we_base_request::_(we_base_request::STRING, 'conditionalCondition');
		$conditionalReturn = we_base_request::_(we_base_request::STRING, 'conditionalReturn');
		foreach($conditionalStates as $i => $cs){
			$retArr[] = array(
				'states' => self::makeArrayFromReq($cs),
				'customerField' => $conditionalCustomerField[$i],
				'condition' => $conditionalCondition[$i],
				'returnValue' => $conditionalReturn[$i],
			);
		}
		return $retArr;
	}

	public static function makeArrayFromReq($req){
		return array_unique(array_filter(array_map('trim', explode("\n", $req))));
	}

	function save(){
		$DB_WE = $GLOBALS['DB_WE'];

		if($DB_WE->query('REPLACE ' . WE_SHOP_PREFS_TABLE . ' set strFelder="' . $DB_WE->escape(we_serialize($this)) . '", strDateiname="weShopVatRule"')){
			$strFelder = f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', '', $DB_WE);
			if($strFelder !== ''){
				$DB_WE->next_record();
				$CLFields = we_unserialize($strFelder);
				$CLFields['stateField'] = $this->stateField;
				$CLFields['stateFieldIsISO'] = $this->stateFieldIsISO;
				$DB_WE->query('UPDATE ' . WE_SHOP_PREFS_TABLE . " SET strFelder='" . $DB_WE->escape(we_serialize($CLFields)) . "' WHERE strDateiname ='shop_CountryLanguage'");
			}

			return true;
		}
		return false;
	}

}
