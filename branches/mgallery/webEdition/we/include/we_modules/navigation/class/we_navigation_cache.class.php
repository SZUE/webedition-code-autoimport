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


	static function delCacheNavigationEntry($id){
		we_base_file::delete(self::getNavigationFilename($id));
	}

	static function saveCacheNavigation($id, $_naviItemes){
		//FIMXE:	currently we need the classes, so we are unable to serialize as json!
		we_base_file::save(self::getNavigationFilename($id), we_serialize($_naviItemes->items, 'serialize', false, 9));
	}

	static function getCacheFromFile($parentid){
		return (file_exists(($_cache = self::getNavigationFilename($parentid))) ?
				we_unserialize(we_base_file::load($_cache)) :
				false);
	}

	static function getCachedRule(){
		return (file_exists(($_cache = WE_CACHE_PATH . 'navigation_rules.php')) ?
				we_unserialize(we_base_file::load($_cache)) :
				false);
	}

	static function saveRules($rules){
		return we_base_file::save(WE_CACHE_PATH . 'navigation_rules.php', we_serialize($rules, 'serialize', false, 9));
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
