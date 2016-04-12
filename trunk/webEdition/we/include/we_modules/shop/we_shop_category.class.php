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
	public $DestPrinciple = 0; //make getter and set private
	public $CatFallbackState = 0;
	public $IsInactive = 0;
	//FIXME: move some of these static vars to session
	private static $isCategoryMode = -1;
	private static $shopCatDir = false;
	private static $shopCatIDs = array();
	private static $shopVatsByCategoryCountry = array();
	private static $shopCatMapping = array();
	private static $mustCheckIsInactive = -1;
	private static $destPrinciples = array();
	private static $activeShopCats = array();

	const IS_CAT_FALLBACK_TO_STANDARD = 1;
	const IS_CAT_FALLBACK_TO_ACTIVE = 2;
	const IS_CAT_FALLBACK_TO_WEDOCCAT = 3;
	const IS_CAT_FALLBACK_TO_WEDOCCAT_AND_ACTIVE = 5;
	const USE_IS_ACTIVE = true;

	function __construct($id = 0){
		parent::__construct($id);
		if($id && is_int($id)){
			$this->DestPrinciple = $this->getDestPrinciple();
			$this->IsInactive = $this->getIsInactive();
		}
	}

	public function we_save(){
		parent::we_save();
		$this->saveDestPrincipleToDB();
	}

	/**
	 * get field DestinationPrinciple of this shop category
	 *
	 * @return int
	 */
	private function getDestPrinciple(){
		if(!$this->ID){
			return 0;
		}
		$ids = self::getDestPrincipleFromDB(true);

		return in_array(intval($this->ID), $ids) ? 1 : 0;
	}

	/**
	 * get field IsInactive of this shop category
	 *
	 * @return int
	 */
	//FIXME: Combine getters, setters and save functions for IsInactive and DestPrinciple
	private function getIsInactive(){
		if(!$this->ID){
			return 0;
		}
		$ids = self::getIsInactiveFromDB(true);

		return in_array(intval($this->ID), $ids) ? 1 : 0;
	}

	/**
	 * save field DestinationPrinciple of this shop category
	 *
	 * @return void
	 */
	private function saveDestPrincipleToDB(){
		$ids = self::getDestPrincipleFromDB(true);
		if($this->DestPrinciple){
			if(in_array($this->ID, $ids)){
				return true;
			}
			$ids[] = $this->ID;
			sort($ids);
		} else {
			if(($k = array_search($this->ID, $ids, false)) === false){
				return true;
			}
			unset($ids[$k]);
		}

		return self::saveSettingDestPrinciple(implode(',', $ids));
	}

	/**
	 * save field IsInactive of this shop category
	 *
	 * @return void

	private function saveIsInactiveToDB(){
		$ids = self::getIsInactiveFromDB(true);
		if($this->IsInactive){
			if(in_array($this->ID, $ids)){
				return true;
			}
			$ids[] = $this->ID;
			sort($ids);
		} else {
			if(($k = array_search($this->ID, $ids, false)) === false){
				return true;
			}
			unset($ids[$k]);
		}

		return self::saveSettingIsInactive(implode(',', $ids));
	}
*/
	/**
	 * get csv or array containing ids of all shop categories with DestinationPrinciple = true
	 *
	 * @param bool $asArray
	 * @return csv or array of int
	 */
	public static function getDestPrincipleFromDB($asArray = false){
		if(!empty(self::$destPrinciples)){
			return $asArray ? self::$destPrinciples : implode(',', self::$destPrinciples);
		}

		$db = new DB_WE();
		$ids = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_destPrinciple"', '', $db, -1);
		self::$destPrinciples = explode(',', $ids);

		return $asArray ? self::$destPrinciples : $ids;
	}

	/**
	 * get csv or array containing ids of all shop categories with IsInactive = true
	 *
	 * @param bool $asArray
	 * @return csv or array of int
	 */
	public static function getIsInactiveFromDB($asArray = false){
		if(!empty(self::$activeShopCats)){
			return $asArray ? self::$activeShopCats : implode(',', self::$activeShopCats);
		}

		$db = new DB_WE();
		$ids = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_isInactive"', '', $db, -1);
		self::$activeShopCats = explode(',', $ids);

		return $asArray ? self::$activeShopCats : $ids;
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
	 * saves shop category field IsInactive of all shop categoeries
	 *
	 * @param csv of int $ids
	 * @return void
	 */
	public static function saveSettingIsInactive($ids){
		$db = new DB_WE();
		$arr = explode(',', trim($ids, ','));
		$val = '';
		foreach($arr as $id){
			$val .= $id ? intval($id) . ',' : '';
		}
		return $db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="shop", pref_name="shop_cats_isInactive", pref_value="' . trim($val, ',') . '"');
	}

	/**
	 * return shopCategoryDir defined in tblSettings as path or id
	 *
	 * @param bool $path
	 * @return int or string
	 */
	public static function getShopCatDir($path = false){
		if(self::$shopCatDir === false){
			$db = new DB_WE();
			self::$shopCatDir = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_dir"', '', $db, false);
		}

		return self::$shopCatDir ? ($path ? id_to_path(self::$shopCatDir, CATEGORY_TABLE) : self::$shopCatDir) : false;
	}

	/**
	 * return true when category mode is activated in shop prefs
	 *
	 * @return bool
	 */
	public static function isCategoryMode(){
		if(self::$isCategoryMode !== -1){
			return self::$isCategoryMode;
		}

		$db = new DB_WE();
		self::$isCategoryMode = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="category_mode"', '', $db, 0) ? true : false;

		return self::$isCategoryMode;
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
	public static function getShopCatById($id = 0, $wedocCategory = '', $useFallback = false){
		if(!$id && !$useFallback){
			return false;
		}

		$tmpValidID = self::checkGetValidID($id, $wedocCategory, $useFallback, true);
		if(!($validID = $tmpValidID['id'])){
			return false;
		}

		$cat = new self();
		$cat->load($validID);
		$cat->CatFallbackState = $tmpValidID['state'];

		$path = self::getShopCatDir(true);
		if(!$cat->isnew && strpos($cat->Path, $path) === 0){
			$cat->DestPrinciple = $cat->getDestPrinciple();

			return $cat;
		}

		return false;
	}

	/**
	 * returns all shop category ids, optionally with or without shop categories dir
	 *
	 * @param bool $incCatsDir: include shop categories dir
	 * @param int $dir: get categories from alternate dir than last saved in db
	 *
	 * @return array of int
	 */
	public static function getAllShopCatIDs($incCatsDir = true, $dir = 0){
		$ids = (self::$shopCatIDs = self::$shopCatIDs ? : self::getShopCatFieldsFromDir('ID', false, false, $dir, false, false));
		if($incCatsDir){
			array_unshift($ids, self::getShopCatDir());
		}

		return $ids;
	}

	/**
	 * writes mapping for inactive shop categories to static var $shopCatMapping
	 *
	 * @param array of int $inactives: ids of inactive shop catgories
	 *
	 * @return void
	 */
	private static function writeShopCatMapping($inactives){
		$paths = self::getShopCatFieldsFromDir('Path', false, false, 0, false, true, true, '', 'Path');
		$parentIDs = self::getShopCatFieldsFromDir('ParentID', false, false, 0, true, true, false, '', 'ID');
		asort($paths);

		self::$shopCatMapping[self::getShopCatDir()] = self::getShopCatDir();
		foreach($paths as $id => $path){
			self::$shopCatMapping[$id] = in_array($id, $inactives) ? self::$shopCatMapping[$parentIDs[$id]] : $id;
		}

		//debug
		/*
		  $arr = array();
		  foreach(self::$shopCatMapping as $id => $val){
		  $arr[$id] = !in_array($val, $inactives) ? "ok" : "bad";
		  }
		  unset($arr[self::getShopCatDir()]);
		  t_e("check is shopCatMapping ok", $arr, self::$shopCatMapping);
		 *
		 */
	}

	/**
	 * returns all shop categories (as defined by shop categories directory).
	 * Optionally it returns shop categories inside of some driectory defined by $dir.
	 * Use only, when we_shop_category objects are absolutely needed: if not, use getShopCatFieldsFromDir allFields=true.
	 *
	 * @param bool $assoc
	 * @param int $dir
	 * @return array of we_shop_category
	 */
	public static function getAllShopCats($incCatsDir = true, $assoc = false, $dir = 0){
		$ids = self::getAllShopCatIDs($incCatsDir, $dir);
		$ret = array();
		foreach($ids as $id){
			$cat = self::getShopCatById($id);
			if($assoc){
				$ret[$id] = $cat;
			} else {
				$ret[] = $cat;
			}
		}
		return $ret;
	}

	/**
	 * returns true if $id is some shop catregory's id
	 * Optionally it returns shop categories inside of some driectory defined by $dir.
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function isShopCategoryID($id){
		$ids = count(self::$shopCatIDs) ? self::$shopCatIDs : self::getAllShopCatIDs(false);
		$ids[] = self::getShopCatDir();

		return in_array(intval($id), $ids);
	}

	/**
	 * returns a valid shop category id computed by some param id considering all aloud falbacks
	 * Optionally it returns fallback state
	 *
	 * @param int $id
	 * @param bool $useFallback
	 * @param bool $activeOnly
	 * @param bool $getState
	 * @param int $id
	 *
	 * @return int $validID
	 */
	public static function checkGetValidID($id = 0, $wedocCategory = '', $useFallback = true, $getState = false){
		//$origID = $id;
		$state = 0;
		if(!$id || !self::isShopCategoryID($id)){
			if(!$useFallback){
				return false;
			}

			//look for shop category linked to we_doc
			$id = 0;
			foreach(explode(',', trim($wedocCategory, ',')) as $c){
				if(self::isShopCategoryID($c)){
					$id = $c;
					$state = self::IS_CAT_FALLBACK_TO_WEDOCCAT;
					break;
				}
			}

			//if still no valid id: get shopCatDir
			if(!$id){
				$id = self::getShopCatDir();
				$state = self::IS_CAT_FALLBACK_TO_STANDARD;
			}
		}
		//$interimsID = $id;

		if(self::USE_IS_ACTIVE && $id !== self::getShopCatDir()){
			$inactives = array();
			if(self::$mustCheckIsInactive === -1){
				$inactives = self::getIsInactiveFromDB(true);
				$numCats = count(self::getAllShopCatIDs(false));
				self::$mustCheckIsInactive = count($inactives) && $numCats !== count($inactives);
			}

			if(self::$mustCheckIsInactive === true){
				$inactives = $inactives ? : self::getIsInactiveFromDB(true);
				$tmpId = $id;
				if(!self::$shopCatMapping){
					self::writeShopCatMapping($inactives);
				}
				$id = isset(self::$shopCatMapping) ? self::$shopCatMapping[$id] : $id;
				$state += $tmpId === $id ? 0 : self::IS_CAT_FALLBACK_TO_ACTIVE;
			}
		}
		//t_e("getCheck", $origID, $interimsID, $id);

		return $getState ? array("id" => $id, "state" => $state) : $id;
	}

	public static function getInternalFieldname($field = 'ID'){
		$fieldMap = array(
			'id' => 'ID',
			'category' => 'Category',
			'path' => 'Path',
			'title' => 'Title',
			'description' => 'Description',
			'is_destinationprinciple' => 'DestPrinciple',
			'is_from doc_object' => 'is_from doc_object',
			'is_fallback_to_standard' => 'is_fallback_to_standard',
			'is_fallback_to_active' => 'is_fallback_to_active'
		);

		return in_array($field, $fieldMap) ? $field : (isset($fieldMap[$field]) ? $fieldMap[$field] : 'ID');
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
	static function getShopCatFieldsFromDir($field = '', $activeOnly = false, $allFields = false, $dir = 0, $includeDir = true, $assoc = true, $showpath = false, $rootdir = '', $order = ''){
		if(!($path = (id_to_path(($dir ? : self::getShopCatDir()), CATEGORY_TABLE)))){
			return array();
		}

		if($field === 'DestPrinciple' || $allFields){
			$destPrincipleIds = self::getDestPrincipleFromDB(true);
			$assoc = true;
		}

		if($field === 'IsInactive' || $allFields){
			$isInactiveIds = self::getIsInactiveFromDB(true);
			$assoc = true;
		}
		$tmpField = ($field === 'IsInactive' || $field === 'DestPrinciple' ? 'ID' : $field);

		$ret = parent::we_getCategories('', ',', $showpath, null, $rootdir, $tmpField, $path, true, ($activeOnly ? : $assoc), $allFields, $includeDir, ($order ? : ($field ? : 'ID')));
		if($activeOnly){
			$isInactiveIds = isset($isInactiveIds) ? $isInactiveIds : self::getIsInactiveFromDB(true);
			$numCats = count($ret) - ($includeDir ? 1 : 0);

			if(!empty($isInactiveIds) && count($isInactiveIds) !== $numCats){
				foreach($isInactiveIds as $k){
					unset($ret[$k]);
				}
			}
		}

		if($field === 'DestPrinciple' || $field === 'IsInactive' || $allFields){
			if(!$ret || !is_array($ret)){
				return false;
			}

			foreach($ret as $k => &$v){
				if(!$allFields && $field === 'DestPrinciple'){
					$v = in_array($k, $destPrincipleIds) ? 1 : 0;
				} elseif(!$allFields && $field === 'IsInactive'){
					$v = in_array($k, $isInactiveIds) ? 1 : 0;
				} else {
					$v['DestPrinciple'] = in_array($k, $destPrincipleIds) ? 1 : 0;
					$v['IsInactive'] = in_array($k, $isInactiveIds) ? 1 : 0;
				}
			}
		}

		return !$assoc ? array_merge($ret) : $ret;
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
	static function getShopCatFieldByID($id, $wedocCategory = '', $field = '', $showpath = false, $rootdir = '', $useFallback = true){
		$cat = self::getShopCatById($id, $wedocCategory, $useFallback);
		if(!$cat){
			return false;
		}

		switch($field = self::getInternalFieldname($field)){
			case 'Path':
				return !$showpath ? $cat->Category : (substr($cat->Path, strlen($rootdir)));
			case 'is_from doc_object':
				return ($cat->CatFallbackState === self::IS_CAT_FALLBACK_TO_WEDOCCAT || $cat->CatFallbackState === self::IS_CAT_FALLBACK_TO_WEDOCCAT_AND_ACTIVE) ? 1 : 0;
			case 'is_fallback_to_standard':
				return $cat->CatFallbackState === self::IS_CAT_FALLBACK_TO_STANDARD ? 1 : 0;
			case 'is_fallback_to_active' :
				return ($cat->CatFallbackState === self::IS_CAT_FALLBACK_TO_ACTIVE || $cat->CatFallbackState === self::IS_CAT_FALLBACK_TO_WEDOCCAT_AND_ACTIVE) ? 1 : 0;
			default:
				return $cat->$field;
		}
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
	private function getShopVatByCountry($country, $getRate = false, $getIsFallbackToStandard = false, $getIsFallbackToPrefs = false){
		if(!$country){
			return false;
		}

		//FIXME: make saving stuff in static vars more concise, maybe move to session
		if($getRate && isset(self::$shopVatsByCategoryCountry[$this->ID][$country])){
			return self::$shopVatsByCategoryCountry[$this->ID][$country];
		}

		if(!isset(self::$shopVatsByCategoryCountry[$this->ID])){
			self::$shopVatsByCategoryCountry[$this->ID] = array();
		}

		$vatID = f('SELECT id FROM ' . WE_SHOP_VAT_TABLE . ' WHERE territory="' . $this->db->escape($country) . '" AND FIND_IN_SET(' . intval($this->ID) . ', categories)', '', $this->db, -1);
		if($vatID && ($vat = we_shop_vat::getVatById($vatID))){
			self::$shopVatsByCategoryCountry[$this->ID][$country] = $vat;
			return $getRate ? $vat->vat : $vat;
		}
		if(($vat = we_shop_vats::getStandardShopVat())){
			self::$shopVatsByCategoryCountry[$this->ID][$country] = $vat;
			return $getIsFallbackToStandard ? true : ($getRate ? $vat->vat : $vat);
		}

		$shopPrefs = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $this->db, -1));
		if(($pref = $shopPrefs[1])){
			self::$shopVatsByCategoryCountry[$this->ID][$country] = false;
			if($getIsFallbackToPrefs){
				return true;
			}
			if($getRate){
				return $pref;
			}
			return new we_shop_vat(0, 'shop_pref', $pref, 1, '', '', '');
		}
		self::$shopVatsByCategoryCountry[$this->ID][$country] = false;

		return false;
	}

	/**
	 * returns we_shop_vat for determined by shop category id destination country
	 *
	 * @param int $id
	 * @param string $country
	 * @param bool $getRate
	 * @param bool $getIsFallbackToStandard
	 * @param bool $getIsFallbackToPrefs
	 * @return we_shop_vat
	 */
	public static function getShopVatByIdAndCountry($id = 0, $wedocCategory = '', $country = '', $getRate = false, $getIsFallbackToStandard = false, $getIsFallbackToPrefs = false, $useFallback = true){
		$validID = self::checkGetValidID($id, $wedocCategory, $useFallback);
		$country = $country && in_array(intval($validID), self::getDestPrincipleFromDB(true)) ? $country : self::getDefaultCountry(); // only get vat of current (customer) country, when shop category is DestPrinciple!

		if(!$getIsFallbackToStandard && !$getIsFallbackToPrefs && isset(self::$shopVatsByCategoryCountry[$validID][$country]) && ($vat = self::$shopVatsByCategoryCountry[$validID][$country])){
			return $getRate ? $vat->vat : $vat;
		}

		if(!($cat = self::getShopCatById($validID, '', false))){//we have validID so we do not have to validate again!
			return false;
		}

		return $cat->getShopVatByCountry($country, $getRate, $getIsFallbackToStandard, $getIsFallbackToPrefs);
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
		$customer = ($customer? :
				(isset($_SESSION['webuser']) ?
					$_SESSION['webuser'] :
					($customerId ?
						(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($GLOBALS[$customerId]))? :
							false) :
						false
					)
				)
			);

		if($customer){
			$customer = array_merge($customer, we_customer_customer::getEncryptedFields());
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
