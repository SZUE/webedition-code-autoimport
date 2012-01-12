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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weNavigationCache{
	const CACHEDIR='/webEdition/we/include/we_tools/navigation/cache/';
	static $rebuildRootCnt=0;

	static function createCacheDir(){
		$_cacheDir = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR;
		if(!is_dir($_cacheDir)){
			we_util_File::createLocalFolder($_cacheDir);
		}
		return $_cacheDir;
	}

	static function delNavigationTree($id){
		if(!self::$rebuildRootCnt){//is increased in next line
			return;
		}
		self::delCacheNavigationEntry(0);
		self::cacheRootNavigation();
		$_id = $id;
		$_c = 0;
		while($_id != 0) {
			self::delCacheNavigationEntry($_id);
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), 'ParentID', new DB_WE());
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	static function cacheNavigationTree($id){
		weNavigationCache::cacheNavigationBranch($id);
		weNavigationCache::cacheRootNavigation();
	}

	static function cacheNavigationBranch($id){
		$_cacheDir = self::createCacheDir();

		$_id = $id;
		$_c = 0;
		$db=new DB_WE();
		while($_id != 0) {
			self::cacheNavigation($_id);
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), 'ParentID', $db);
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	static function cacheRootNavigation(){
		if(!self::$rebuildRootCnt++){
			return;
		}
		$_cacheDir = self::createCacheDir();

		$_naviItemes = new weNavigationItems();

		$_naviItemes->initById(0);

		self::saveCacheNavigation(0, $_naviItemes);

		$currentRulesStorage = $_naviItemes->currentRules; // Bug #4142
		foreach($currentRulesStorage as &$rule){
			$rule->deleteDB();
		}
		$_content = serialize($currentRulesStorage);
		unset($currentRulesStorage);

		weFile::save($_cacheDir . 'rules.php', $_content);
	}

	static function cacheNavigation($id){
		$_naviItemes = new weNavigationItems();
		$_naviItemes->initById($id);
		self::saveCacheNavigation($id,$_naviItemes);
	}

	static function delCacheNavigationEntry($id){
		$_cacheDir = weNavigationCache::createCacheDir();
		weFile::delete($_cacheDir . 'navigation_' . $id . '.php');
	}

	static function saveCacheNavigation($id,$_naviItemes){
		$_cacheDir = weNavigationCache::createCacheDir();
		weFile::save($_cacheDir . 'navigation_' . $id . '.php', serialize($_naviItemes->items));
	}

	static function getCacheFromFile($parentid){
		$_cache = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'navigation_' . $parentid . '.php';

		if(file_exists($_cache)){
			return unserialize(weFile::load($_cache));
		}
		return false;
	}

	static function getCachedRule(){
		$_cache = $_SERVER['DOCUMENT_ROOT'] . self::CACHEDIR . 'rules.php';
		if(file_exists($_cache)){
			return $navigationRulesStorage = weFile::load($_cache);
		}
		return false;
	}

}