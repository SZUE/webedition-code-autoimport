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

	/**
	 * initialize object with a naviagtion model
	 *
	 * @param we_navigation_navigation $navModel
	 */
	function initByNavModel(&$navModel){
		// convert navigation data into data the filter model needs

		$_custFilter = $navModel->CustomerFilter;
		$_useDocumentFilter = $navModel->UseDocumentFilter;

		$this->updateCustomerFilter($_custFilter);

		$_specCust = (isset($navModel->Customers) && is_array($navModel->Customers)) ? $navModel->Customers : array();

		$_mode = we_customer_abstractFilter::OFF;

		if($navModel->LimitAccess == 2){
			$_mode = we_customer_abstractFilter::NONE;
		} else if($navModel->LimitAccess == 1 && $navModel->ApplyFilter){
			$_mode = we_customer_abstractFilter::FILTER;
		} else if($navModel->LimitAccess && $navModel->AllCustomers == 1){
			$_mode = we_customer_abstractFilter::ALL;
		} else if($navModel->LimitAccess && count($_specCust) > 0){
			$_mode = we_customer_abstractFilter::SPECIFIC;
		}

		// end convert data

		$_whitelist = isset($navModel->WhiteList) && is_array($navModel->WhiteList) ? $navModel->WhiteList : array();
		$_blacklist = isset($navModel->BlackList) && is_array($navModel->BlackList) ? $navModel->BlackList : array();

		$this->setBlackList($_blacklist);
		$this->setWhiteList($_whitelist);
		$this->setFilter($_custFilter);
		$this->setMode($_mode);
		$this->setSpecificCustomers($_specCust);
		$this->setUseDocumentFilter($_useDocumentFilter);
	}

	/**
	 * initialize object with a navigation item
	 *
	 * @param we_navigation_item $navItem
	 */
	function initByNavItem(&$navItem){

		if($navItem->limitaccess == 0){
			$this->setMode(we_customer_abstractFilter::OFF);
		} else {
			if($navItem->limitaccess == 2){
				$this->setMode(we_customer_abstractFilter::NONE);
			} else {
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
		return we_base_request::_(we_base_request::RAW, 'wecf_useDocumentFilter');
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

			case we_customer_abstractFilter::NONE:
				$model->LimitAccess = 2;
				$model->ApplyFilter = 0;
				$model->AllCustomers = 0;
				break;

			default:
				$model->LimitAccess = 0;
		}
	}

	function updateByFilter(&$filterObj, $id, $table){
		$_limitAccess = 0;
		$_applyFilter = 0;
		$_allCustomers = 0;
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

			case we_customer_abstractFilter::NONE:
				$_limitAccess = 2;
				$_applyFilter = 0;
				$_allCustomers = 0;
				break;
		}


		$_customers = makeCSVFromArray($filterObj->getSpecificCustomers(), true);
		$_whiteList = makeCSVFromArray($filterObj->getWhiteList(), true);
		$_blackList = makeCSVFromArray($filterObj->getBlackList(), true);
		$_filter = serialize($filterObj->getFilter());

		$DB_WE = new DB_WE();
		$DB_WE->query('UPDATE ' . NAVIGATION_TABLE . ' SET ' .
			we_database_base::arraySetter(array(
				'LimitAccess' => $_limitAccess,
				'ApplyFilter' => $_applyFilter,
				'AllCustomers' => $_allCustomers,
				'Customers' => $_customers,
				'CustomerFilter' => $_filter,
				'BlackList' => $_blackList,
				'WhiteList' => $_whiteList
			)) .
			' WHERE UseDocumentFilter=1 AND ' . we_navigation_navigation::getNavCondition($id, $table));
	}

}
