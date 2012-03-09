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
class we_shop_shop{

//FIXME: is this classname really correct?!
	var $ClassName = "we_listview_shoppingCart";
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

	function __construct($shoppingCart){
		if(isobject($shoppingCart)){
			$this->ShoppingCart = $shoppingCart;
			$this->ShoppingCartItems = $shoppingCart->getShoppingItems();
		}else{
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
					if($key == WE_SHOP_VAT_FIELD_NAME){
						$this->Record[$key] = $value;
					} else{
						$this->Record[preg_replace('#^we_#', '', $key)] = $value;
					}
				}
			}
			$this->count++;
			return true;
		}
		return false;
	}

	function f($key){
		if(isset($this->Record[$key])){
			return $this->Record[$key];
		}
		return '';
	}

	function getCustomFieldsAsRequest(){

		$ret = '';

		foreach($this->ActItem['customFields'] as $key => $value){
			$ret .= "&" . WE_SHOP_ARTICLE_CUSTOM_FIELD . "[$key]=$value";
		}

		return $ret;
	}

}
