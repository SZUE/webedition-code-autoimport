<?php

/**
 * webEdition CMS
 *
 * $Rev: 8605 $
 * $Author: mokraemer $
 * $Date: 2014-11-22 12:26:45 +0100 (Sa, 22 Nov 2014) $
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

	private function getDestPrincipleFromSettings(){
		if(!$this->ID){
			return 0;
		}

		$ids = self::getSettingDestPrinciple(true);

		return in_array(intval($this->ID), $ids) ? 1 : 0;
	}

	private function saveDestPrinciple(){
		$ids = self::getSettingDestPrinciple(true);
		if($this->DestPrinciple){
			if(in_array($this->ID, $ids)){
				return true;
			}
			$ids[] = $this->ID;
			sort($ids);
		} else {
			if(($k = array_search($this->ID, $ids)) === false) {
				return true;
			}
			unset($ids[$k]);
		}

		return self::saveSettingDestPrinciple(implode(',', $ids));
	}
	
	public function __toString(){
		return serialize($this);
	}

	public static function getSettingDestPrinciple($asArray = false){
		$db = new DB_WE();
		$ids = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_destPrinciple"', '', $db, -1);

		return $asArray ? explode(',', $ids) : $ids;
	}

	public static function saveSettingDestPrinciple($ids){
		$db = new DB_WE();
		$arr = explode(',', trim($ids, ','));
		$val = '';
		foreach($arr as $id){
			$val .= $id ? intval($id) . ',' : '';
		}
		return $db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="shop", pref_name="shop_cats_destPrinciple", pref_value="' . trim($val, ',') . '"');
	}

	public static function getShopCatsDir($path = false){
		$db = new DB_WE();
		$dir = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_dir"', '', $db, -1);

		return $dir ? ($path ? id_to_path($dir, CATEGORY_TABLE) : $dir) : false;
	}

	public static function saveShopCatsDir($id){
		$db = new DB_WE();

		return $db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="shop", pref_name="shop_cats_dir", pref_value=' . intval($id));
	}

	public static function getShopCategoryById($id = 0){
		if(!$id){
			return false;
		}

		$path = self::getShopCatsDir(true);
		$cat = new self(intval($id));

		if(!$cat->isnew && strpos($cat->Path, $path . '/') === 0){
			return $cat;
		}

		return false;
	}

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

	/* FIXME: base the following methods on shopcat objects instead of geting data from db directly! */
	static function getFieldFromAll($catfield = '', $complete = false, $catsDir = 0, $catsPath = '', $asArray = true, $assoc = true, $tokken = ',', $showpath = false, $rootdir = '/', $noDirs = false, we_database_base $db = null, $catIDs = ''){
		if(!($path = $catsPath ? : (id_to_path(($catsDir ? : self::getShopCatsDir()), CATEGORY_TABLE)))){
			return false;
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

	static function getFieldFromIDs($catIDs, $catfield = '', $complete = false, $catsDir = 0, $catsPath = '', $asArray = false, $assoc = false, $tokken = ',', $showpath = false, $rootdir = '/', $noDirs = false, we_database_base $db = null){
		if(!$catIDs){
			return $asArray ? array() : '';
		}
		//$db = $db ? : new DB_WE();

		return self::getFieldFromAll($catfield, $complete, $catsDir, $catsPath, $asArray, $assoc, $tokken, $showpath, $rootdir, $noDirs, $db, $catIDs);
	}

	//FIXME: we need getVat and getVatField!
	public static function getVatByCategory($category, $country = '', $field = ''){
		$db = $GLOBALS['DB_WE'];

		//prio for country: 1) $counry (for test purpose only) 2) webuser session, 3) tblSettings => shop_location
		$country = $country ? : f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_location"', '', $db, -1);

		if(!($category = intval($category)) || !$country){
			return false;
		}

		//for debugging purpose only: when num_rows > 1 we have an inconsistent tblshopvat!
		/*
		$db->query('SELECT id,text,vat,standard,territory,textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' WHERE territory="' . $db->escape($country) . '" AND FIND_IN_SET(' . $category . ', categories)');
		if($db->num_rows() > 1){
			t_e("function getVatByCountryCategory", "number of results: " . $db->num_rows());
		}
		 * 
		 */
		//end debug

		$hash = getHash('SELECT id, text, vat, standard, territory, textProvince, categories FROM ' . WE_SHOP_VAT_TABLE . ' WHERE territory="' . $db->escape($country) . '" AND FIND_IN_SET(' . $category . ', categories)');
		if($hash){
			//$vat = new we_shop_vat($hash['id'], $hash['text'], $hash['vat'], ($hash['standard'] ? 1 : 0), $hash['territory'], $hash['textProvince'], $hash['categories']);
			return isset($hash[$field]) ? $hash[$field] : '';
		} else if(($standard = we_shop_vats::getStandardShopVat())){
			return $standard;
		} else {
			$shopPrefs = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"'));
			if(($pref = $shopPrefs[1])){
				new we_shop_vat(0, 'shop_pref', $pref, 1, '', '', '');
			}
		}
		t_e("function getVatByCountryCategory", "neither vat found nor standard");

		return false;
	}
}
