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
class we_shop_shop{

	const DOCUMENT = 'w';
	const OBJECT = 'o';

	var $DB_WE;
	var $IDs = array();
	var $count = 0;
	var $Record = array();
	var $anz = 0;
	var $type;
	var $ShoppingCart;
	var $ShoppingCartItems;
	var $ShoppingCartKey = '';
	var $ActItem;

	const ignoredEditFields = 'ID,Username,Password,MemberSince,LastLogin,LastAccess,ParentID,Path,IsFolder,Text,AutoLogin,AutoLoginDenied,ModifiedBy,ModifyDate';
	const ignoredExtraShowFields = 'Forename,Surname';

	public function __construct($shoppingCart){
		if(is_object($shoppingCart)){
			$this->ShoppingCart = $shoppingCart;
			$this->ShoppingCartItems = $shoppingCart->getShoppingItems();
		} else {
			t_e('called with non object');
		}

		$this->IDs = array_keys($this->ShoppingCartItems);
	}

	function next_record(){
		$shoppingCartItems = $this->ShoppingCartItems;
		$this->anz = count($this->IDs);

		if($this->count < count($this->IDs)){
			$cartKey = $this->IDs[$this->count];
			$this->ShoppingCartKey = $cartKey;

			$shoppingItem = $shoppingCartItems[$cartKey];
			$this->ActItem = $shoppingItem;

			$this->Record = array();
			foreach($shoppingItem['serial'] as $key => $value){
				if(!is_int($key)){
					$this->Record[(($key == WE_SHOP_VAT_FIELD_NAME || $key == WE_SHOP_CATEGORY_FIELD_NAME) ? $key : preg_replace('#^we_#', '', $key))] = $value;
				}
			}
			$this->count++;
			return true;
		}
		return false;
	}

	function f($key){
		return (isset($this->Record[$key]) ? $this->Record[$key] : '');
	}

	function getCustomFieldsAsRequest(){
		$ret = '';
		foreach($this->ActItem['customFields'] as $key => $value){
			$ret .= "&" . WE_SHOP_ARTICLE_CUSTOM_FIELD . "[$key]=$value";
		}
		return $ret;
	}

	public function getDBRecord(){
		return (is_object($this->DB_WE) ? $this->DB_WE->getRecord() : array());
	}

	public function getDBf($field){
		return (is_object($this->DB_WE) ? $this->DB_WE->f($field) : '');
	}

	static function getAllOrderYears(){
		$GLOBALS['DB_WE']->query('SELECT DISTINCT YEAR(DateOrder) AS a FROM ' . SHOP_TABLE . ' ORDER BY a DESC');
		$years = $GLOBALS['DB_WE']->getAll(true);
		if(array_search(date('Y'), $years) === false){
			array_unshift($years, date('Y'));
		}
		return $years;
	}

}
