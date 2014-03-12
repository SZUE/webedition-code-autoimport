<?php

class we_shop_vatRule{

	var $stateField;
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

	function __construct($defaultValue, $stateField, $liableToVat, $notLiableToVat, $conditionalRules, $stateFieldIsISO = '0'){

		$this->defaultValue = $defaultValue;
		$this->stateField = $stateField;
		$this->stateFieldIsISO = $stateFieldIsISO;
		$this->liableToVat = $liableToVat;
		$this->notLiableToVat = $notLiableToVat;
		$this->conditionalRules = $conditionalRules;
	}

	function executeVatRule($customer = false){
		// now check all rules for the vat

		if($customer){
			if(isset($this->stateField) && isset($customer[$this->stateField])){
				$state = $customer[$this->stateField];

				// is state liableToVat
				if(in_array($state, $this->liableToVat)){
					return true;
				}

				// is state not liable to vat
				if(in_array($state, $this->notLiableToVat)){
					return false;
				}

				// now check additional fields
				foreach($this->conditionalRules as $rule){
					$ret = $rule['returnValue'] == 'true' ? true : false;
					$field = $customer[$rule['customerField']];

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

		return ($this->defaultValue == 'true' ? true : false);
	}

	function initByRequest(&$req){

		return new we_shop_vatRule(
				$req['defaultValue'], $req['stateField'], we_shop_vatRule::makeArrayFromReq($req['liableToVat']), we_shop_vatRule::makeArrayFromReq($req['notLiableToVat']), we_shop_vatRule::makeArrayFromConditionField($req), $req['stateFieldIsISO']
		);
	}

	function getShopVatRule(){
		if(($strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopVatRule"'))){
			//FIX old class names
			unserialize(strtr(array('O:13:"weShopVatRule":' => 'O:15:"we_shop_vatRule":', $strFelder)));
		} else {
			return new we_shop_vatRule('true', '', array(), array(), array(
				array(
					'states' => array(),
					'customerField' => '',
					'condition' => '',
					'returnValue' => 1
				)
					), 0
			);
		}
	}

	function makeArrayFromConditionField($req){
		$retArr = array();

		for($i = 0; $i < count($req['conditionalStates']); $i++){
			$retArr[] = array(
				'states' => we_shop_vatRule::makeArrayFromReq($req['conditionalStates'][$i]),
				'customerField' => $req['conditionalCustomerField'][$i],
				'condition' => $req['conditionalCondition'][$i],
				'returnValue' => $req['conditionalReturn'][$i],
			);
		}
		return $retArr;
	}

	function makeArrayFromReq($req){

		$entries = explode("\n", $req);
		$retArr = array();

		foreach($entries as $entry){
			if(trim($entry)){
				$retArr[] = trim($entry);
			}
		}
		array_unique($retArr);
		return $retArr;
	}

	function save(){
		$DB_WE = $GLOBALS['DB_WE'];

		if($DB_WE->query('REPLACE ' . ANZEIGE_PREFS_TABLE . ' set strFelder="' . $DB_WE->escape(serialize($this)) . '", strDateiname="weShopVatRule"')){
			$strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $DB_WE);
			if($strFelder !== ''){
				$DB_WE->next_record();
				$CLFields = unserialize($strFelder);
				$CLFields['stateField'] = $this->stateField;
				$CLFields['stateFieldIsISO'] = $this->stateFieldIsISO;
				$DB_WE->query('UPDATE ' . ANZEIGE_PREFS_TABLE . " SET strFelder='" . $DB_WE->escape(serialize($CLFields)) . "' WHERE strDateiname ='shop_CountryLanguage'");
			}

			return true;
		} else {
			return false;
		}
	}

}
