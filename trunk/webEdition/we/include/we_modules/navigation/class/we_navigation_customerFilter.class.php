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
 * Filter model class for navigation tool
 *
 */
class we_navigation_customerFilter extends we_customer_abstractFilter{
	var $_useDocumentFilter = true;

	public function __construct($mode = self::OFF, array $specificCustomers = array(), array $blackList = array(), array $whiteList = array(), array $filter = array()){
		parent::__construct($mode, $specificCustomers, $blackList, $whiteList, $filter);
	}

	/**
	 * initialize object with a naviagtion model
	 *
	 * @param we_navigation_navigation $navModel
	 */
	function initByNavModel(&$navModel){
		// convert navigation data into data the filter model needs

		$_custFilter = $navModel->CustomerFilter;

		$this->updateCustomerFilter($_custFilter);

		$_specCust = (isset($navModel->Customers) && is_array($navModel->Customers)) ? $navModel->Customers : array();

		$_mode = we_customer_abstractFilter::OFF;
		if($navModel->LimitAccess){
			if($navModel->LimitAccess == 2){
				$_mode = we_customer_abstractFilter::NOT_LOGGED_IN_USERS;
			} else if($navModel->LimitAccess == 1 && $navModel->ApplyFilter){
				$_mode = we_customer_abstractFilter::FILTER;
			} else if($navModel->AllCustomers == 1){
				$_mode = we_customer_abstractFilter::ALL;
			} else if(count($_specCust) > 0){
				$_mode = we_customer_abstractFilter::SPECIFIC;
			}
		}

		// end convert data

		$this->setBlackList(isset($navModel->BlackList) && is_array($navModel->BlackList) ? $navModel->BlackList : array());
		$this->setWhiteList(isset($navModel->WhiteList) && is_array($navModel->WhiteList) ? $navModel->WhiteList : array());
		$this->setSpecificCustomers($_specCust);

		$this->setFilter($_custFilter);
		$this->setMode($_mode);
		$this->setUseDocumentFilter($navModel->UseDocumentFilter);
	}

	/**
	 * initialize object with a navigation item
	 *
	 * @param we_navigation_item $navItem
	 */
	function initByNavItem(&$navItem){
		switch($navItem->limitaccess){
			case 0:
				$this->setMode(we_customer_abstractFilter::OFF);
				return;
			case 2:
				$this->setMode(we_customer_abstractFilter::NOT_LOGGED_IN_USERS);
				return;
			case 1:
			default:
				if(isset($navItem->customers['filter']) && is_array($navItem->customers['filter']) && count($navItem->customers['filter'])){
					$this->setMode(we_customer_abstractFilter::FILTER);
					$_custFilter = $navItem->customers['filter'];
					$this->updateCustomerFilter($_custFilter);
					$this->setFilter($_custFilter);

					if(isset($navItem->customers['blacklist']) && is_array($navItem->customers['blacklist']) && count($navItem->customers['blacklist'])){
						$this->setBlackList($navItem->customers['blacklist']);
					}
					if(isset($navItem->customers['whitelist']) && is_array($navItem->customers['whitelist']) && count($navItem->customers['whitelist'])){
						$this->setWhiteList($navItem->customers['whitelist']);
					}
				} else if(isset($navItem->customers['id']) && is_array($navItem->customers['id']) && count($navItem->customers['id'])){
					$this->setMode(we_customer_abstractFilter::SPECIFIC);
					$this->setSpecificCustomers($navItem->customers['id']);
				} else {
					$this->setMode(we_customer_abstractFilter::ALL);
				}
		}
	}

	/**
	 * converts old style (prior we 5.1) navigation filters to new format
	 *
	 * @param array $_custFilter
	 */
	function updateCustomerFilter(&$_custFilter){
		if(isset($_custFilter['AND']) && isset($_custFilter['OR'])){ // old style filter => convert into new style
			$_newFilter = array();
			foreach($_custFilter['AND'] as $_f){
				$_newFilter[] = array(
					'logic' => 'AND',
					'field' => $_f['operand1'],
					'operation' => $_f['operator'],
					'value' => $_f['operand2']
				);
			}
			foreach($_custFilter['OR'] as $_f){
				$_newFilter[] = array(
					'logic' => 'OR',
					'field' => $_f['operand1'],
					'operation' => $_f['operator'],
					'value' => $_f['operand2']
				);
			}
			$_custFilter = $_newFilter;
		}
	}

	function getUseDocumentFilter(){
		return $this->_useDocumentFilter;
	}

	function setUseDocumentFilter($useDocumentFilter){
		$this->_useDocumentFilter = $useDocumentFilter;
	}

	static function getUseDocumentFilterFromRequest(){
		return we_base_request::_(we_base_request::BOOL, 'wecf_useDocumentFilter');
	}

	function translateModeToNavModel($mode, &$model){
		switch($mode){

			case we_customer_abstractFilter::FILTER:
				$model->LimitAccess = 1;
				$model->ApplyFilter = 1;
				$model->AllCustomers = 1;
				break;

			case we_customer_abstractFilter::SPECIFIC:
				$model->LimitAccess = 1;
				$model->ApplyFilter = 0;
				$model->AllCustomers = 0;
				break;

			case we_customer_abstractFilter::ALL:
				$model->LimitAccess = 1;
				$model->ApplyFilter = 0;
				$model->AllCustomers = 1;
				break;

			case we_customer_abstractFilter::NOT_LOGGED_IN_USERS:
				$model->LimitAccess = 2;
				$model->ApplyFilter = 0;
				$model->AllCustomers = 0;
				break;

			default:
				$model->LimitAccess = 0;
		}
	}

	function updateByFilter(&$filterObj, $id, $table){
		switch($filterObj->getMode()){
			case we_customer_abstractFilter::FILTER:
				$_limitAccess = 1;
				$_applyFilter = 1;
				$_allCustomers = 1;
				break;

			case we_customer_abstractFilter::SPECIFIC:
				$_limitAccess = 1;
				$_applyFilter = 0;
				$_allCustomers = 0;
				break;

			case we_customer_abstractFilter::ALL:
				$_limitAccess = 1;
				$_applyFilter = 0;
				$_allCustomers = 1;
				break;

			case we_customer_abstractFilter::NOT_LOGGED_IN_USERS:
				$_limitAccess = 2;
				$_applyFilter = 0;
				$_allCustomers = 0;
				break;
			default:
				$_limitAccess = 0;
				$_applyFilter = 0;
				$_allCustomers = 0;
		}


		$DB_WE = new DB_WE();
		$DB_WE->query('UPDATE ' . NAVIGATION_TABLE . ' SET ' .
			we_database_base::arraySetter(array(
				'LimitAccess' => $_limitAccess,
				'ApplyFilter' => $_applyFilter,
				'AllCustomers' => $_allCustomers,
				'Customers' => implode(',', $filterObj->getSpecificCustomers()),
				//FIXME: this is due to customerfilter
				'CustomerFilter' => we_serialize($filterObj->getFilter(), SERIALIZE_PHP),
				'BlackList' => implode(',', $filterObj->getBlackList()),
				'WhiteList' => implode(',', $filterObj->getWhiteList())
			)) .
			' WHERE UseDocumentFilter=1 AND ' . we_navigation_navigation::getNavCondition($id, $table));
	}

}
