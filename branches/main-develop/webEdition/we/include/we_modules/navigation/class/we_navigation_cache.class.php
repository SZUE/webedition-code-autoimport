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

	public static function getNavigationFilename($id){
		return WE_CACHE_PATH . 'navigation_' . $id;
	}

	static function delNavigationTree($id){
		static $deleted = [];
		if(in_array($id, $deleted)){
			return;
		}
		$db = new DB_WE();
		self::delCacheNavigationEntry(0);
		$c = 0;

		while($id != 0 && ++$c < 100 && !in_array($id, $deleted)){
			self::delCacheNavigationEntry($id);
			$deleted[] = $id;
			$id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($id), '', $db);
		}
	}

	static function delCacheNavigationEntry($id){
		we_base_file::delete(self::getNavigationFilename($id));
	}

	static function saveCacheNavigation($id, $naviItemes){
		//FIMXE: currently we need the classes, so we are unable to serialize as json!
		we_base_file::save(self::getNavigationFilename($id), we_serialize($naviItemes->items, SERIALIZE_PHP, false, 9));
	}

	static function getCacheFromFile($parentid){
		return (file_exists(($cache = self::getNavigationFilename($parentid))) ?
			we_unserialize(we_base_file::load($cache)) :
			false);
	}

	static function getCachedRule(){
		return (file_exists(($cache = WE_CACHE_PATH . 'navigation_rules')) ?
			we_unserialize(we_base_file::load($cache)) :
			false);
	}

	static function saveRules($rules){
		//FIMXE:	currently we need the classes, so we are unable to serialize as json!
		return we_base_file::save(WE_CACHE_PATH . 'navigation_rules', we_serialize($rules, SERIALIZE_PHP, false, 9));
	}

	/**
	 * Used on upgrade to remove all navigation entries
	 */
	static function clean($force = false){
		if(file_exists(WE_CACHE_PATH . 'cleannav')){
			unlink(WE_CACHE_PATH . 'cleannav');
			$force = true;
		}
		if($force && ($files = glob(WE_CACHE_PATH . 'navigation_*'))){
			foreach($files as $file){
				unlink($file);
			}
		}
	}

}
