<?php

class weShopVatRule {

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

	var $defaultValue = true;

	function weShopVatRule( $defaultValue, $stateField, $liableToVat, $notLiableToVat, $conditionalRules,$stateFieldIsISO='0' ) {

		$this->defaultValue = $defaultValue;
		$this->stateField = $stateField;
		$this->stateFieldIsISO = $stateFieldIsISO;
		$this->liableToVat = $liableToVat;
		$this->notLiableToVat = $notLiableToVat;
		$this->conditionalRules = $conditionalRules;
	}

	function executeVatRule($customer=false) {

		// now check all rules for the vat

		if ($customer) {

			if ( isset( $this->stateField ) && isset($customer[$this->stateField]) ) {

				$state = $customer[$this->stateField];

				// is state liableToVat
				if ( in_array($state, $this->liableToVat) ) {
					return true;
				}

				// is state not liable to vat
				if ( in_array($state, $this->notLiableToVat) ) {
					return false;
				}

				// now check additional fields
				foreach ($this->conditionalRules as $rule) {

					$ret = $rule['returnValue'] == 'true' ? true : false;

					$field = $customer[$rule['customerField']];

					if (in_array($state, $rule['states'])) {

						switch ($rule['condition']) {
							case 'is_empty':
								return (empty($field) ? $ret : !$ret);
							break;
							case 'is_set':
								return (!empty($field) ? $ret : !$ret);
							break;
						}
					}
				}
			}
		}

		return ($this->defaultValue == 'true' ? true : false);
	}

	function initByRequest(&$req) {

		return new weShopVatRule(
			$req['defaultValue'],
			$req['stateField'],
			weShopVatRule::makeArrayFromReq($req['liableToVat']),
			weShopVatRule::makeArrayFromReq($req['notLiableToVat']),
			weShopVatRule::makeArrayFromConditionField($req),
			$req['stateFieldIsISO']
		);
	}

	function getShopVatRule() {

		global $DB_WE;

		$query = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopVatRule"
		';

		$DB_WE->query($query);

		if ($DB_WE->next_record()) {

			return unserialize($DB_WE->f('strFelder'));

		} else {
			return new weShopVatRule(
				'true',
				'',
				array(),
				array(),
				array(
					array(
						'states' => array(),
						'customerField' => '',
						'condition' => '',
						'returnValue' => 1
					)
				),
				0
			);
		}
	}

	function makeArrayFromConditionField($req) {

		$retArr = array();

		for ($i=0;$i<sizeof($req['conditionalStates']); $i++) {

			$retArr[] = array(
				'states' => weShopVatRule::makeArrayFromReq($req['conditionalStates'][$i]),
				'customerField' => $req['conditionalCustomerField'][$i],
				'condition' => $req['conditionalCondition'][$i],
				'returnValue' => $req['conditionalReturn'][$i],
			);
		}
		return $retArr;
	}

	function makeArrayFromReq($req) {

		$entries = explode("\n", $req);
		$retArr = array();

		foreach ($entries as $entry) {
			if (trim($entry)) {
				$retArr[] = trim($entry);
			}
		}
		array_unique($retArr);
		return $retArr;
	}

	function save() {

		global $DB_WE;
		// check if already inserted
		$query = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopVatRule"';

		$DB_WE->query($query);

		if ($DB_WE->num_rows() > 0) {

			$query = 'UPDATE ' . ANZEIGE_PREFS_TABLE . ' set strFelder="' . $DB_WE->escape(serialize($this)) . '" WHERE strDateiname="weShopVatRule"';

		} else {
			$query = 'INSERT INTO ' . ANZEIGE_PREFS_TABLE . ' (strDateiname, strFelder) VALUES ("weShopVatRule", "' . $DB_WE->escape(serialize($this)) . '")';
		}

		if ($DB_WE->query($query)) {
			$q = 'SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLangauge"';
			$DB_WE->query($q);
			if ( $DB_WE->num_rows() > 0) {
				$DB_WE->next_record();
				$CLFields = unserialize($DB_WE->f("strFelder"));
				$CLFields['stateField'] =  $this->stateField;
				$CLFields['stateFieldIsISO'] =  $this->stateFieldIsISO;
				$DB_WE->query("UPDATE " . ANZEIGE_PREFS_TABLE . " SET strFelder = '" . $DB_WE->escape(serialize($CLFields)) . "' WHERE strDateiname ='shop_CountryLangauge'");
			}

			return true;
		} else {
			return false;
		}
	}

}
?>