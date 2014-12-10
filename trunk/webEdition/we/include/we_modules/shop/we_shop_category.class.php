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
class we_shop_category extends we_category{
	public $DestPrinciple = 0;

	function __construct($id = 0){
		parent::__construct($id);
		if($id && is_int($id)){
			$this->DestPrinciple = $this->getDestPrincipleFromSettings();
		}
	}

	public function we_save(){
		parent::we_save();
		$this->saveDestPrinciple();
	}

	/**
	 * get field DestinationPrinciple of this shop category
	 *
	 * @return int
	 */
	private function getDestPrincipleFromSettings(){
		if(!$this->ID){
			return 0;
		}

		$ids = self::getSettingDestPrinciple(true);

		return in_array(intval($this->ID), $ids) ? 1 : 0;
	}

	/**
	 * save field DestinationPrinciple of this shop category
	 *
	 * @return void
	 */
	private function saveDestPrinciple(){
		$ids = self::getSettingDestPrinciple(true);
		if($this->DestPrinciple){
			if(in_array($this->ID, $ids)){
				return true;
			}
			$ids[] = $this->ID;
			sort($ids);
		} else {
			if(($k = array_search($this->ID, $ids)) === false){
				return true;
			}
			unset($ids[$k]);
		}

		return self::saveSettingDestPrinciple(implode(',', $ids));
	}

	public function __toString(){
		return serialize($this);
	}

	/**
	 * get csv or array containing ids of all shop categories with DestinationPrinciple = true
	 *
	 * @param bool $asArray
	 * @return csv or array of int
	 */
	public static function getSettingDestPrinciple($asArray = false){
		$db = new DB_WE();
		$ids = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_destPrinciple"', '', $db, -1);

		return $asArray ? explode(',', $ids) : $ids;
	}

	/**
	 * saves shop category field DestinationPrinciple of all shop categoeries
	 *
	 * @param csv of int $ids
	 * @return void
	 */
	public static function saveSettingDestPrinciple($ids){
		$db = new DB_WE();
		$arr = explode(',', trim($ids, ','));
		$val = '';
		foreach($arr as $id){
			$val .= $id ? intval($id) . ',' : '';
		}
		return $db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="shop", pref_name="shop_cats_destPrinciple", pref_value="' . trim($val, ',') . '"');
	}

	/**
	 * return shopCategoryDir defined in tblSettings as path or id
	 *
	 * @param bool $path
	 * @return int or string
	 */
	public static function getShopCatsDir($path = false){
		$db = new DB_WE();
		$dir = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_dir"', '', $db, -1);

		return $dir ? ($path ? id_to_path($dir, CATEGORY_TABLE) : $dir) : false;
	}

	/**
	 * saves shopCategoryDir to tblSettings
	 *
	 * @param int $id
	 * @return void
	 */
	public static function saveShopCatsDir($id){
		$db = new DB_WE();

		return $db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="shop", pref_name="shop_cats_dir", pref_value=' . intval($id));
	}

	/**
	 * returns shop category by id, false if there is no shop category of this id
	 *
	 * @param int $id
	 * @return we_shop_category
	 */
	public static function getShopCategoryById($id = 0){
		if(!$id){
			return false;
		}

		$cat = new self();
		if(!$cat->load($id)){
			return false;
		}

		$path = self::getShopCatsDir(true);
		if(!$cat->isnew && strpos($cat->Path, $path . '/') === 0){
			return $cat;
		}

		return false;
	}

	/**
	 * returns all shop categories (as defined by shop categories directory).
	 * Optionally it returns shop categories inside of some driectory defined by $dir.
	 *
	 * @param bool $assoc
	 * @param int $dir
	 * @return array of we_shop_category
	 */
	public static function getAllShopCategories($assoc = false, $dir = 0){
		$ids = self::getFieldFromAll('ID', false, $dir, '', true, true);
		$ret = array();
		foreach($ids as $id){
			if($assoc){
				$ret[$id] = new self(intval($id));
			} else {
				$ret[] = new self(intval($id));
			}
		}

		return $ret;
	}

	/**
	 * returns field $catfield of all shop categories.
	 *
	 * @param string $catfield
	 * @param bool $complete
	 * @param int $catsDir
	 * @param string $catsPath
	 * @param bool $asArray
	 * @param bool $assoc
	 * @param string $tokken
	 * @param bool $showpath
	 * @param string $rootdir
	 * @param bool $noDirs
	 * @param object $db
	 * @return array of string
	 */
	static function getFieldFromAll($catfield = '', $complete = false, $catsDir = 0, $catsPath = '', $asArray = true, $assoc = true, $tokken = ',', $showpath = false, $rootdir = '/', $noDirs = false, we_database_base $db = null, $catIDs = ''){
		/* FIXME: base on shopcat objects instead of geting data from db directly! */

		if(!($path = $catsPath ? : (id_to_path(($catsDir ? : self::getShopCatsDir()), CATEGORY_TABLE)))){
			return $asArray ? array() : false;
		}
		//$catIDs = !$catIDs ? '' : trim(implode(',', array_walk(explode(',', $catIDs), 'intval')), ',');

		if($complete || $catfield === 'DestPrinciple'){
			$db = $db ? : new DB_WE();
			$destPrincipleIds = explode(',', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_destPrinciple"', '', $db, -1));

			if($catfield === 'DestPrinciple' && !$complete){
				$whereID = $catIDs ? ' AND ID IN(' . $catIDs . ')' : '';
				if(!$db->query('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Path LIKE "' . $path . '/%"' . $whereID)){
					return false;
				}

				$ret = array();
				while($db->next_record()){
					$data = $db->getRecord();
					if($assoc){
						$ret[$data['ID']] = in_array($data['ID'], $destPrincipleIds) ? 1 : 0;
					} else {
						$ret[] = in_array($data['ID'], $destPrincipleIds) ? 1 : 0;
					}
				}

				return $asArray ? $ret : makeCSVFromArray($ret, false, $tokken);
			}
		}

		$asArray = ($complete || in_array($catfield, array('Title', 'Description'))) ? true : $asArray;
		$ret = parent::we_getCategories($catIDs, $tokken, $showpath, $db, $rootdir, $catfield, $path, $asArray, $assoc, $noDirs, $complete);

		if($complete){
			foreach($ret as $k => &$val){
				$val['DestPrinciple'] = in_array($k, $destPrincipleIds) ? 1 : 0;
			}
		}

		return $ret;
	}

	/**
	 * returns field $catfield of shop categories defined by their ids.
	 *
	 * @param string $catIDs
	 * @param string $catfield
	 * @param bool $complete
	 * @param int $catsDir
	 * @param string $catsPath
	 * @param bool $asArray
	 * @param bool $assoc
	 * @param string $tokken
	 * @param bool $showpath
	 * @param string $rootdir
	 * @param bool $noDirs
	 * @param object $db
	 * @return array of string
	 */
	static function getFieldFromIDs($catIDs, $catfield = '', $complete = false, $catsDir = 0, $catsPath = '', $asArray = false, $assoc = false, $tokken = ',', $showpath = false, $rootdir = '/', $noDirs = false, we_database_base $db = null){
		/* FIXME: base on shopcat objects instead of geting data from db directly! */

		if(!$catIDs){
			return $asArray ? array() : '';
		}
		//$db = $db ? : new DB_WE();

		return self::getFieldFromAll($catfield, $complete, $catsDir, $catsPath, $asArray, $assoc, $tokken, $showpath, $rootdir, $noDirs, $db, $catIDs);
	}

	/**
	 * returns we_shop_vat for determined by destination country
	 *
	 * @param int $id
	 * @param string $country
	 * @param bool $getRate
	 * @param bool $getIsFallbackToStandard
	 * @param bool $getIsDefaultFromPrefs
	 * @return we_shop_vat
	 */
	public function getVatByCountry($country, $getRate = false, $getIsFallbackToStandard = false, $getIsFallbackToPrefs = false){
		if(!$country){
			return false;
		}

		if($getRate && isset($GLOBALS['weShopCategories']['getShopVATByCatCountry'][$this->ID][$country])){
			return $GLOBALS['weShopCategories']['getShopVATByCatCountry'][$this->ID][$country];
		}

		$vatID = f('SELECT id FROM ' . WE_SHOP_VAT_TABLE . ' WHERE territory="' . $this->db->escape($country) . '" AND FIND_IN_SET(' . intval($this->ID) . ', categories)', '', $this->db, -1);
		if($vatID && ($vat = we_shop_vat::getVatById($vatID))){
			$GLOBALS['weShopCategories']['getShopVATByCatCountry'][$this->ID][$country] = $vat->vat;
			return $getRate ? $vat->vat : $vat;
		}

		if(($vat = we_shop_vats::getStandardShopVat())){
			$GLOBALS['weShopCategories']['getShopVATByCatCountry'][$this->ID][$country] = $vat->vat;
			return $getIsFallbackToStandard ? true : ($getRate ? $vat->vat : $vat);
		}

		$shopPrefs = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $this->db, -1));
		if(($pref = $shopPrefs[1])){
			$GLOBALS['weShopCategories']['getShopVATByCatCountry'][$this->ID][$country] = $pref;
			if($getIsFallbackToPrefs){
				return true;
			}
			if($getRate){
				return $pref;
			}
			return new we_shop_vat(0, 'shop_pref', $pref, 1, '', '', '');
		}

		$GLOBALS['weShopCategories']['getShopVATByCatCountry'][$this->ID][$country] = false;

		return false;
	}

	/**
	 * returns we_shop_vat for determined by shop category id destination country
	 *
	 * @param int $id
	 * @param string $country
	 * @param bool $getRate
	 * @param bool $getIsFallbackToStandard
	 * @param bool $getIsDefaultFromPrefs
	 * @return we_shop_vat
	 */
	public static function getVatByIdAndCountry($id = 0, $country = '', $getRate = false, $getIsFallbackToStandard = false, $getIsFallbackToPrefs = false){
		if(!$id || !$country){
			return false;
		}

		if($getRate && isset($GLOBALS['weShopCategories']['getShopVATByCatCountry'][$id][$country]) && ($vat = $GLOBALS['weShopCategories']['getShopVATByCatCountry'][$id][$country] !== false)){
			return $vat;
		}

		if(!($cat = self::getShopCategoryById($id))){
			return false;
		}

		return $cat->getVatByCountry($country, $getRate, $getIsFallbackToStandard, $getIsFallbackToPrefs);
	}

	/**
	 * returns content of a customer's country field. Country fallback is used when param useFallback is true.
	 * Optionally teh function returns an array with country, customer and flag isFallback.
	 *
	 * @param bool $useFallback
	 * @param object $customer
	 * @param int $customerId
	 * @param bool $getAllData
	 * @return object
	 */
	public static function getCountryFromCustomer($useFallback = false, $customer = false, $customerId = 0, $getAllData = false){
		if(!$customer){
			if(isset($_SESSION['webuser'])){
				$customer = $_SESSION['webuser'];
			} elseif($customerId){
				$cust = new we_customer_customertag($GLOBALS[$customerId]);
				$carray = $cust->getDBRecord();
				unset($cust);
				$customer = ($carray ? : false);
			}
		}

		if($customer){
			$stateField = we_shop_vatRule::getStateField();
			if(isset($customer[$stateField]) && ($c = $customer[$stateField])){
				return $getAllData ? array('country' => $c, 'isFallback' => false, "customer" => $customer) : $c;
			}
		}

		if($useFallback && ($c = self::getDefaultCountry())){
			return $getAllData ? array('country' => $c, "isFallback" => true, "customer" => false) : $c;
		}

		return $getAllData ? array('country' => false, "isFallback" => false, "customer" => $customer ? true : false) : false;
	}

	/**
	 * returns country defined in shop prefs as shop location
	 *
	 * @return string
	 */
	public static function getDefaultCountry(){
		return ($c = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_location"', '', $GLOBALS['DB_WE'], -1) ? : false);
	}

}
