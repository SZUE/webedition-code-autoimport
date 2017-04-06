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

	public static function getNavigationFilename($id, $full = true){
		return ($full ? WE_CACHE_PATH : '') . 'navigation_' . $id;
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
		we_cache_file::save(self::getNavigationFilename($id, false), $naviItemes->items, 0, SERIALIZE_PHP);
	}

	static function getCacheFromFile($parentid){
		return we_cache_file::load(self::getNavigationFilename($parentid, false));
	}

	static function getCachedRule(){
		return we_cache_file::load('navigation_rules');
	}

	static function saveRules($rules){
		we_cache_file::save('navigation_rules', $rules, 0, SERIALIZE_PHP);
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
			we_cache_file::clean('navigation_*');
		}
	}

}
