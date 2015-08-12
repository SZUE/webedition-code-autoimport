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
	const CACHEDIR = WE_CACHE_PATH;

	//FIXME: use we_cache_file to save navidata

	public static function getNavigationFilename($id){
		return WE_CACHE_PATH . 'navigation_' . $id . '.php';
	}

	static function delNavigationTree($id){
		static $deleted = array();
		if(in_array($id, $deleted)){
			return;
		}
		$db = new DB_WE();
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
		we_base_file::delete(self::getNavigationFilename($id));
	}

	static function saveCacheNavigation($id, $_naviItemes){
		//FIMXE:	currently we need the classes, so we are unable to serialize as json!
		we_base_file::save(self::getNavigationFilename($id), we_serialize($_naviItemes->items, 'serialize', false, 9));
	}

	static function getCacheFromFile($parentid){
		$_cache = self::getNavigationFilename($parentid);

		if(file_exists($_cache)){
			$data = we_base_file::load($_cache);
			//FIXME: we change this, as we don't support old navigation caches
			return $data ? we_unserialize($data[0] === 'x' ? gzuncompress($data) : gzinflate($data)) : array();
		}
		return false;
	}

	static function getCachedRule(){//FIXME: this file is never written!
		$_cache = WE_CACHE_PATH . 'rules.php';
		if(file_exists($_cache)){
			return we_base_file::load($_cache);
		}
		return false;
	}

	/**
	 * Used on upgrade to remove all navigation entries
	 */
	static function clean($force = false){
		if(file_exists(WE_CACHE_PATH . 'cleannav')){
			unlink(WE_CACHE_PATH . 'cleannav');
			$force = true;
		}
		if($force){
			foreach(glob(WE_CACHE_PATH . 'navigation_*') as $file){
				unlink($file);
			}
		}
	}

}
