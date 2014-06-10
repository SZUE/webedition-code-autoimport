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
class we_navigation_cache{

	const CACHEDIR = '/webEdition/we/include/we_modules/navigation/cache/';

	static $rebuildRootCnt = 0;

	static function delNavigationTree($id){
		static $deleted = array();
		if(in_array($id, $deleted)){
			return;
		}
		$db=new DB_WE();
		self::delCacheNavigationEntry(0);
		//self::cacheRootNavigation();
		$_id = $id;
		$_c = 0;
		while($_id != 0){
			self::delCacheNavigationEntry($_id);
			$deleted[] = $_id;
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), '', $db);
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	static function cacheNavigationTree($id){
		we_navigation_cache::cacheNavigationBranch($id);
		//weNavigationCache::cacheRootNavigation();
	}

	static function cacheNavigationBranch($id){
		$_id = $id;
		$_c = 0;
		$db = new DB_WE();
		while($_id != 0){
			self::cacheNavigation($_id);
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), 'ParentID', $db);
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	static function cacheNavigation($id){
		$_naviItemes = new we_navigation_items();
		$_naviItemes->initById($id);
		self::saveCacheNavigation($id, $_naviItemes);
	}

	static function delCacheNavigationEntry($id){
		we_base_file::delete($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $id . '.php');
	}

	static function saveCacheNavigation($id, $_naviItemes){
		we_base_file::save($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $id . '.php', gzdeflate(serialize($_naviItemes->items), 9));
	}

	static function getCacheFromFile($parentid){
		$_cache = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $parentid . '.php';

		if(file_exists($_cache)){
			return @unserialize(@gzinflate(we_base_file::load($_cache)));
		}
		return false;
	}

	static function getCachedRule(){
		$_cache = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'rules.php';
		if(file_exists($_cache)){
			return $navigationRulesStorage = we_base_file::load($_cache);
		}
		return false;
	}

	/**
	 * Used on upgrade to remove all navigation entries
	 */
	static function clean($force = false){
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'clean')){
			unlink($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'clean');
			$force = true;
		}
		if($force){
			$files = scandir($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR);
			foreach($files as $file){
				if(strpos($file, 'navigation_') === 0){
					unlink($_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . $file);
				}
			}
		}
	}

}
