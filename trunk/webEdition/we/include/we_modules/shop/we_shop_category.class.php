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

	static function setDestPrinciple($replaceAll = true){
		//coming soon
	}

	static function getShopCatsDir($path = false){
		$db = new DB_WE();
		$dir = f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_dir"', '', $db, -1);

		return $dir ? ($path ? id_to_path($dir, CATEGORY_TABLE) : $dir) : false;
	}

	static function getShopCategories($catfield = '', $complete = false, $catsDir = 0, $catsPath = '', $asArray = true, $assoc = true, $tokken = ',', $showpath = false, $rootdir = '/', $noDirs = false, we_database_base $db = null, $catIDs = ''){
		if(!($path = $catsPath ? : (id_to_path(($catsDir ? : self::getShopCatsDir()), CATEGORY_TABLE)))){
			return false;
		}
		//$catIDs = !$catIDs ? '' : trim(implode(',', array_walk(explode(',', $catIDs), 'intval')), ',');

		if($complete || $catfield === 'destPrinciple'){
			$db = $db ? : new DB_WE();
			$destPrincipleIds = explode(',', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_cats_destPrinciple"', '', $db, -1));

			if($catfield === 'destPrinciple' && !$complete){
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
				$val['destPrinciple'] = in_array($k, $destPrincipleIds) ? 1 : 0;
			}
		}

		return $ret;
	}

	static function getShopCategoriesFromIDs($catIDs, $catfield = '', $complete = false, $catsDir = 0, $catsPath = '', $asArray = false, $assoc = false, $tokken = ',', $showpath = false, $rootdir = '/', $noDirs = false, we_database_base $db = null){
		if(!$catIDs){
			return $asArray ? array() : '';
		}
        //$db = $db ? : new DB_WE();

		return self::getShopCategories($catfield, $complete, $catsDir, $catsPath, $asArray, $assoc, $tokken, $showpath, $rootdir, $noDirs, $db, $catIDs);
	}
}
