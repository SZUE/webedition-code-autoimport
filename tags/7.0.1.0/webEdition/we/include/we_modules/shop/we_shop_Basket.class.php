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
class we_shop_Basket{
	//FIXME: this is set back to public due to some shop restrictions, see #6530, #6954
	/**
	 * 	this array contains all shopping items
	 * 	a shopping item is an associated array containining
	 * 	'id'       => integer
	 * 	'type'     => w | o
	 * 	'variant'  => string
	 * 	'quantity' => integer //fr�her - jetzt umstellung auf float, feature #4875
	 * 	'serial'   => string
	 *  'customFields' => array
	 *
	 * @var array
	 */
	public $ShoppingItems = array();

	/**
	 * user can define custom fields saved with the order.
	 *
	 * @var array
	 */
	public $CartFields = array();
	public $orderID = 0;
	private $creationTime;

	function __construct(array $array){
		$this->setCartProperties($array);
	}

	function initCartFields(){
		if(isset($_REQUEST[WE_SHOP_CART_CUSTOM_FIELD]) && is_array($_REQUEST[WE_SHOP_CART_CUSTOM_FIELD])){
			foreach($_REQUEST[WE_SHOP_CART_CUSTOM_FIELD] as $key => $value){
				$this->CartFields[$key] = $value;
			}
		}
	}

	public function getCreationTime(){
		return $this->creationTime;
	}

	/**
	 * returns array of shoppingItems
	 *
	 * @return array
	 */
	function getShoppingItems(){
		return $this->ShoppingItems;
	}

	/**
	 * returns array of all shopping cartfields
	 *
	 * @return array
	 */
	function getCartFields(){
		return $this->CartFields;
	}

	function hasCartField($name){
		return isset($this->CartFields[$name]);
	}

	function getCartField($name){
		return isset($this->CartFields[$name]) ? $this->CartFields[$name] : '';
	}

	/**
	 * returns the items in the shopping cart and all custom cart fields
	 * former getProperties
	 *
	 * @return array
	 */
	function getCartProperties(){

		return array(
			'shoppingItems' => $this->getShoppingItems(),
			'cartFields' => $this->getCartFields()
		);
	}

	/**
	 * initialies the shoppingCart
	 * former name setProperties
	 *
	 * @param array $array
	 */
	private function setCartProperties(array $array){

		if(isset($array['shoppingItems']) && isset($array['cartFields'])){
			$this->ShoppingItems = $array['shoppingItems'];
			$this->CartFields = $array['cartFields'];
			$this->creationTime = isset($array['creationTime']) ? $array['creationTime'] : time();
		} else {
			$this->ShoppingItems = array();
			$this->CartFields = array();
			$this->creationTime = time();
		}
	}

	/**
	 * add an item to the array
	 *
	 * @param integer $id
	 * @param integer $quantity
	 * @param string $type
	 * @param string $variant
	 */
	function Add_Item($id, $quantity = 1, $type = we_shop_shop::DOCUMENT, $variant = '', $customFields = array()){

		// check if this item is already in the shoppingCart
		if(($key = $this->getShoppingItemIndex($id, $type, $variant, $customFields))){ // item already exists
			if($this->ShoppingItems[$key]['quantity'] + $quantity > 0){
				$this->ShoppingItems[$key]['quantity'] += $quantity;
			} else {
				$this->Del_Item($id, $type, $variant, $customFields);
			}
		} else { // add the item
			$key = str_replace('.', '', uniqid('we_cart_', true));

			if($quantity > 0){ // only add new item with positive number
				$item = array(
					'id' => $id,
					'type' => $type,
					'variant' => $variant,
					'quantity' => $quantity,
					'serial' => $this->getserial($id, $type, $variant, $customFields),
					'customFields' => $customFields
				);

				$this->ShoppingItems[$key] = $item;
			}
		}
	}

	/**
	 * returns size of shoppingCart
	 *
	 * @return integer
	 */
	function Get_Basket_Count(){
		return count($this->ShoppingItems);
	}

	/**
	 * returns shoppingItems
	 *
	 * @return array
	 */
	function Get_All_Data(){
		return $this->getCartProperties();
	}

	/**
	 * returns shoppingItem - serial
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $variant
	 * @return string
	 */
	function getserial($id, $type, $variant = false, $customFields = array()){
		$DB_WE = new DB_WE();
		$Record = array();

		switch($type){
			case we_shop_shop::DOCUMENT:
				// unfortunately this is not made with initDocById,
				// but its much faster -> so we use it
				$DB_WE->query('SELECT l.Name,IF(c.BDID>0,c.BDID,c.Dat) AS Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE l.DID=' . intval($id) . ' AND l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
				$Record = $DB_WE->getAllFirst(false);

				if($variant){
					we_base_variants::useVariantForShop($Record, $variant);
				}

				if(($hash = getHash('SELECT * FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $DB_WE, MYSQL_ASSOC))){
					foreach($hash as $key => $val){
						$Record['wedoc_' . $key] = $val;
					}
				}

				$Record['WE_PATH'] = $Record['wedoc_Path'] . ($variant ? '?' . we_base_constants::WE_VARIANT_REQUEST . '=' . $variant : '');
				$Record['WE_TEXT'] = $Record['wedoc_Text'];
				$Record['WE_VARIANT'] = $variant;
				$Record['WE_ID'] = intval($id);

				// at last add custom fields to record and to path
				if(!empty($customFields)){
					$Record['WE_PATH'] .= ($variant ? '&amp;' : '?');

					foreach($customFields as $name => $value){
						$Record[$name] = $value;
						$Record['WE_PATH'] .= WE_SHOP_ARTICLE_CUSTOM_FIELD . '[' . $name . ']=' . $value . '&amp;';
					}
				}
				break;
			case we_shop_shop::OBJECT:
				$classID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND ID=' . intval($id), '', $DB_WE);
				if(!$classID){
					t_e('fatal shop error', $id, $_REQUEST);
					return array();
				}

				$olv = new we_listview_object(0, 1, 0, '', false, $classID, '', '', ' ' . OBJECT_X_TABLE . $classID . '.OF_ID=' . $id, 0, 0, true, false, '', '', '', '', '', '', '', 0, '', '', '', '', TAGLINKS_DIRECTORYINDEX_HIDE, TAGLINKS_OBJECTSEOURLS);
				$olv->next_record();

				$Record = $olv->getDBRecord();

				if($variant){
					// init model to detect variants
					// :TODO: change this to match above version
					$obj = new we_objectFile();
					$obj->initByID($id, OBJECT_FILES_TABLE);

					we_base_variants::useVariantForShopObject($Record, $variant, $obj);

					// add variant to path ...
					$Record['we_WE_PATH'] .= '?' . we_base_constants::WE_VARIANT_REQUEST . '=' . $variant;
				}
				$Record['WE_VARIANT'] = $variant;
				$Record['we_obj'] = $id;

				// at last add custom fields to record and to path
				if(!empty($customFields)){
					foreach($customFields as $name => $value){
						$Record[$name] = $value;
						$Record['we_WE_PATH'] .= '&amp;' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '[' . $name . ']=' . $value;
					}
				}

				// when using objects all fields have 'we_' as prename
				if(isset($Record['we_' . WE_SHOP_VAT_FIELD_NAME])){
					$Record[WE_SHOP_VAT_FIELD_NAME] = $Record['we_' . WE_SHOP_VAT_FIELD_NAME];
					unset($Record['we_' . WE_SHOP_VAT_FIELD_NAME]);
				}
				if(isset($Record['we_' . WE_SHOP_CATEGORY_FIELD_NAME])){
					$Record[WE_SHOP_CATEGORY_FIELD_NAME] = $Record['we_' . WE_SHOP_CATEGORY_FIELD_NAME];
					unset($Record['we_' . WE_SHOP_CATEGORY_FIELD_NAME]);
				}
				break;
		}

		// at last add custom fields and vat to shopping card
		$Record[WE_SHOP_ARTICLE_CUSTOM_FIELD] = $customFields;

		return $Record;
	}

	/**
	 * returns amount of shopping items by key
	 *
	 * @param string $key
	 * @return integer
	 */
	function Get_Item_Quantity($key){
		return $this->ShoppingItems[$key]['quantity'];
	}

	/**
	 * remove item from shop
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $variant
	 */
	function Del_Item($id, $type, $variant = '', $customFields = array()){
		if(($key = $this->getShoppingItemIndex($id, $type, $variant, $customFields))){
			unset($this->ShoppingItems[$key]);
		}
	}

	/**
	 * resets the shoppingCart
	 *
	 */
	function Empty_Basket(){
		$this->ShoppingItems = array();
		$this->CartFields = array();
	}

	/**
	 * changes abilities of item in the shoppingCart
	 *
	 * @param integer $id
	 * @param integer $quantity
	 * @param string $type
	 * @param string $variant
	 */
	function Set_Item($id, $quantity = 1, $type = "w", $variant = '', $customFields = array()){
		if(($key = $this->getShoppingItemIndex($id, $type, $variant, $customFields))){ // item already in cart
			if($quantity > 0){
				$this->ShoppingItems[$key]['quantity'] = $quantity;
			} else {
				$this->Del_Item($id, $type, $variant, $customFields);
			}
		} else { // new item
			$this->Add_Item($id, $quantity, $type, $variant, $customFields);
		}
	}

	/**
	 * set cart item by the assoc array
	 *
	 * @param string $cart_id
	 * @param integer $cart_amount
	 */
	function Set_Cart_Item($cart_id, $cart_amount){
		if(isset($this->ShoppingItems[$cart_id])){
			$item = $this->ShoppingItems[$cart_id];
			$this->Set_Item($item['id'], $cart_amount, $item['type'], $item['variant'], $item['customFields']);
		}
	}

	/**
	 * returns key for shoppingItem or false
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $variant
	 * @return mixed
	 */
	function getShoppingItemIndex($id, $type = we_shop_shop::DOCUMENT, $variant = '', $customFields = array()){
		foreach($this->ShoppingItems as $index => $item){
			if($item['id'] == $id && $item['type'] == $type && $item['variant'] == $variant && $customFields == $item['customFields']){
				return $index;
			}
		}
		return false;
	}

	function getOrderID(){
		return $this->orderID;
	}

	function setOrderID($id){
		$this->orderID = $id;
	}

}
