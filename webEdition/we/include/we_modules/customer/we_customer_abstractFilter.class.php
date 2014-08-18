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

/**
 * Base Class for all Customer Filters (Model)
 *
 */
abstract class we_customer_abstractFilter{

	const OFF = 0;
	const ALL = 1;
	const SPECIFIC = 2;
	const FILTER = 3;
	const NONE = 4;
	const OP_EQ = 0;
	const OP_NEQ = 1;
	const OP_LESS = 2;
	const OP_LEQ = 3;
	const OP_GREATER = 4;
	const OP_GEQ = 5;
	const OP_STARTS_WITH = 6;
	const OP_ENDS_WITH = 7;
	const OP_CONTAINS = 8;
	const OP_IN = 9;
	const OP_NOT_CONTAINS = 10;
	const OP_NOT_IN = 11;

	/**
	 * Mode. Can be OFF, ALL, SPECIFIC, FILTER
	 *
	 * @var integer
	 */
	private $_mode = self::OFF;

	/**
	 * Array with customer ids. Only relevant when $_mode is SPECIFIC
	 *
	 * @var array
	 */
	var $_specificCustomers = array();

	/**
	 * Array with customer ids. Only relevant when $_mode is FILTER
	 *
	 * @var array
	 */
	private $_blackList = array();

	/**
	 * Array with customer ids. Only relevant when $_mode is FILTER
	 *
	 * @var array
	 */
	private $_whiteList = array();

	/**
	 * Array with filter Settings
	 *
	 * @var array
	 */
	private $_filter = array();

	/**
	 *
	 * @param integer $mode
	 * @param array $specificCustomers
	 * @param array $blackList
	 * @param array $whiteList
	 * @param array $filter
	 * @return we_customer_abstractFilter
	 */
	protected function __construct($mode = self::OFF, array $specificCustomers = array(), array $blackList = array(), array $whiteList = array(), array $filter = array()){
		$this->setMode($mode);
		$this->setSpecificCustomers($specificCustomers);
		$this->setBlackList($blackList);
		$this->setWhiteList($whiteList);
		$this->setFilter($filter);
	}

	/**
	 * checks if customer has access with the actual filter object
	 *
	 * @return boolean
	 */
	public function customerHasAccess(){
		switch($this->_mode){
			case self::OFF:
				return true;
			case self::ALL:
				return self::customerIsLogedIn();
			case self::NONE:
				return !self::customerIsLogedIn();
			case self::SPECIFIC:
				return self::customerIsLogedIn() && in_array($_SESSION['webuser']['ID'], $this->_specificCustomers);
			case self::FILTER:
				return self::customerIsLogedIn() && self::customerHasFilterAccess();
			default:
				return false;
		}
	}

	private static function evalSingleFilter($op, $key, $value){
		switch($op){
			case self::OP_EQ:
				return $_SESSION['webuser'][$key] == $value;
			case self::OP_NEQ:
				return $_SESSION['webuser'][$key] != $value;
			case self::OP_LESS:
				return $_SESSION['webuser'][$key] < $value;
			case self::OP_LEQ:
				return $_SESSION['webuser'][$key] <= $value;
			case self::OP_GREATER:
				return $_SESSION['webuser'][$key] > $value;
			case self::OP_GEQ:
				return $_SESSION['webuser'][$key] >= $value;
			case self::OP_STARTS_WITH:
				return (strpos($_SESSION['webuser'][$key], $value) === 0);
			case self::OP_ENDS_WITH:
				return self::endsWith($_SESSION['webuser'][$key], $value);
			case self::OP_CONTAINS:
				return self::contains($_SESSION['webuser'][$key], $value);
			case self::OP_NOT_CONTAINS:
				return !self::contains($_SESSION['webuser'][$key], $value);
			case self::OP_IN:
				return self::in($_SESSION['webuser'][$key], $value);
			case self::OP_NOT_IN:
				return !self::in($_SESSION['webuser'][$key], $value);
			default:
				t_e('invalid customer filter op: ' . $op);
				return false;
		}
	}

	/**
	 * Checks if customer matches $this->_filter array
	 *
	 * @return boolean
	 */
	private function customerHasFilterAccess(){
		if(in_array($_SESSION['webuser']['ID'], $this->_blackList)){
			return false;
		}
		if(in_array($_SESSION['webuser']['ID'], $this->_whiteList)){
			return true;
		}

		$hasPermission = false;
		$flag = false;
		$invalidFields = array();
		foreach($this->_filter as $_filter){
			if(!isset($_SESSION['webuser'][$_filter['field']])){
				$invalidFields[] = $_filter['field'];
				continue;
			}
			if($flag && $_filter['logic'] == 'AND'){
				$hasPermission&=self::evalSingleFilter($_filter['operation'], $_filter['field'], $_filter['value']);
			} else {
				if($hasPermission){
					break;
				}
				$hasPermission = self::evalSingleFilter($_filter['operation'], $_filter['field'], $_filter['value']);
			}
			$flag = true;
		}

		if(!empty($invalidFields)){
			t_e('Customerfilter on document ? has invalid Parameters, maybe deleted Customer fields: ' . implode(',', $invalidFields));
		}

		return $hasPermission;
	}

	public static function evalSingleFilterQuery($op, $key, $value){
		switch($op){
			case self::OP_EQ:
				return '`' . $key . '`="' . $value . '"';
			case self::OP_NEQ:
				return '`' . $key . '`!="' . $value . '"';
			case self::OP_LESS:
				return '`' . $key . '`<"' . $value . '"';
			case self::OP_LEQ:
				return '`' . $key . '`<="' . $value . '"';
			case self::OP_GREATER:
				return '`' . $key . '`>"' . $value . '"';
			case self::OP_GEQ:
				return '`' . $key . '`>="' . $value . '"';
			case self::OP_STARTS_WITH:
				return '`' . $key . '` LIKE "' . $value . '%"';
			case self::OP_ENDS_WITH:
				return '`' . $key . '` LIKE "%' . $value . '"';
			case self::OP_CONTAINS:
				return '`' . $key . '` LIKE "%' . $value . '%"';
			case self::OP_NOT_CONTAINS:
				return '`' . $key . '` NOT LIKE "%' . $value . '%"';
			case self::OP_IN:
				return 'FIND_IN_SET("' . $value . '",`' . $key . '`)';
			case self::OP_NOT_IN:
				return '!FIND_IN_SET("' . $value . '",`' . $key . '`)';
			default:
				t_e('invalid customer filter op: ' . $op);
				return 'FALSE';
		}
	}

	public static function getQueryFromFilter(array $filter){
		$flag = false;
		$ret = '';
		foreach($filter as $_filter){
			//FIXME: read webuser table to check for nonexistent fields
			$ret.=($flag ? ' ' . $_filter['logic'] . ' ' : '') . self::evalSingleFilterQuery($_filter['operation'], $_filter['field'], $_filter['value']);
			$flag = true;
		}
		return $ret ? '(' . $ret . ')' : '';
	}

	/**
	 * Creates and returns the filter array from request
	 *
	 * @static
	 * @return array
	 */
	static function getFilterFromRequest(){
		if(we_base_request::_(we_base_request::STRING, 'filterSelect_0') === false){
			return array();
		}

		$count = 0;
		$filter = array();
		while(true){
			if(($field = we_base_request::_(we_base_request::STRING, 'filterSelect_' . $count))){

				if(trim(($val = we_base_request::_(we_base_request::STRINGC, 'filterValue_' . $count)))){
					$filter[] = array(
						'logic' => (we_base_request::_(we_base_request::STRING, 'filterLogic_' . $count) == 'OR' ? ' OR ' : ' AND '),
						'field' => $field,
						'operation' => we_base_request::_(we_base_request::INT, 'filterOperation_' . $count),
						'value' => $val
					);
				}
				$count++;
			} else {
				return $filter;
			}
		}
	}

	/**
	 * Creates and returns the specificCustomers array from
	 *
	 * @static
	 * @return array
	 */
	static function getSpecificCustomersFromRequest(){
		if(!($name = we_base_request::_(we_base_request::STRING, 'specificCustomersEditControl'))){
			return array();
		}
		$customers = array();
		$i = 0;
		while(true){
			if(($val = we_base_request::_(we_base_request::STRINGC, $name . '_variant0_' . $name . '_item' . $i))){
				$customers[] = $val;
				$i++;
			} else {
				return weConvertToIds($customers, CUSTOMER_TABLE);
			}
		}
	}

	/**
	 * Creates and returns the black list array from
	 *
	 * @static
	 * @return array
	 */
	static function getBlackListFromRequest(){
		if(!($name = we_base_request::_(we_base_request::STRING, 'blackListEditControl'))){
			return array();
		}
		$blackList = array();

		$i = 0;
		while(true){
			if(($val = we_base_request::_(we_base_request::STRINGC, $name . '_variant0_' . $name . '_item' . $i))){
				$blackList[] = $val;
				$i++;
			} else {
				return weConvertToIds($blackList, CUSTOMER_TABLE);
			}
		}
	}

	/**
	 * Creates and returns the white list array from request
	 *
	 * @static
	 * @return array
	 */
	static function getWhiteListFromRequest(){
		if(!($name = we_base_request::_(we_base_request::STRING, 'whiteListEditControl'))){
			return array();
		}
		$whiteList = array();
		$i = 0;
		while(true){
			if(($val = we_base_request::_(we_base_request::STRINGC, $name . '_variant0_' . $name . '_item' . $i))){
				$whiteList[] = $val;
				$i++;
			} else {
				return weConvertToIds($whiteList, CUSTOMER_TABLE);
			}
		}
	}

	/**
	 * Checks if $haystack ends with $needle. If so, returns true, otherwise false
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @static
	 * @return boolean
	 */
	static function endsWith($haystack, $needle){
		$pos = strlen($haystack) - strlen($needle);
		return (strpos($haystack, $needle) === $pos);
	}

	/**
	 * Checks if $haystack contains $needle. If so, returns true, otherwise false
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @static
	 * @return boolean
	 */
	static function contains($haystack, $needle){
		return (strpos($haystack, $needle) !== false);
	}

	/**
	 * Checks if $value is one of the CSV Values of $comp
	 *
	 * @param string $value
	 * @param string $comp (CSV)
	 * @static
	 * @return boolean
	 */
	static function in($value, $comp){
		$comp = str_replace('\\,', '__WE_COMMA__', $comp);
		$arr = explode(',', $comp);
		foreach($arr as &$cur){
			$cur = trim(str_replace('__WE_COMMA__', ',', $cur));
		}
		$value = explode(',', $value);
		return count(array_intersect($value, $arr)) > 0;
	}

	/**
	 * Checks if Customer is logged in. Returns true or f alse
	 *
	 * @return boolean
	 */
	public static function customerIsLogedIn(){
		return isset($_SESSION) && isset($_SESSION['webuser']) && isset($_SESSION['webuser']['ID']) && $_SESSION['webuser']['ID'];
	}

	/**
	 * mutator method for $this->_mode
	 *
	 * @param integer $mode
	 */
	public function setMode($mode){
		$this->_mode = $mode;
	}

	/**
	 * accessor method for $this->_mode
	 *
	 * @return integer
	 */
	public function getMode(){
		return $this->_mode;
	}

	/**
	 * mutator method for $this->_specificCustomers
	 *
	 * @param array $specificCustomers
	 */
	public function setSpecificCustomers($specificCustomers){
		$this->_specificCustomers = $specificCustomers;
	}

	/**
	 * accessor method for $this->_specificCustomers
	 *
	 * @return array
	 */
	public function getSpecificCustomers(){
		return $this->_specificCustomers;
	}

	/**
	 * mutator method for $this->_blackList
	 *
	 * @param array $blackList
	 */
	public function setBlackList($blackList){
		$this->_blackList = $blackList;
	}

	/**
	 * accessor method for $this->_blackList
	 *
	 * @return array
	 */
	public function getBlackList(){
		return $this->_blackList;
	}

	/**
	 * mutator method for $this->_whiteList
	 *
	 * @param array $whiteList
	 */
	public function setWhiteList($whiteList){
		$this->_whiteList = $whiteList;
	}

	/**
	 * accessor method for $this->_whiteList
	 *
	 * @return array
	 */
	public function getWhiteList(){
		return $this->_whiteList;
	}

	/**
	 * mutator method for $this->_filter
	 *
	 * @param array $filter
	 */
	public function setFilter($filter){
		$this->_filter = $filter;
	}

	/**
	 * accessor method for $this->_filter
	 *
	 * @return array
	 */
	public function getFilter(){
		return $this->_filter;
	}

}
