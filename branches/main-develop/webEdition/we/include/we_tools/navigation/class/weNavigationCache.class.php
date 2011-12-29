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

	static function createCacheDir(){
		$_cacheDir = $_SERVER['DOCUMENT_ROOT'] . CACHEDIR;
		if(!is_dir($_cacheDir)){
			we_util_File::createLocalFolder($_cacheDir);
		}
		return $_cacheDir;
	}

	static function cacheNavigationTree($id){
		weNavigationCache::cacheNavigationBranch($id);
		weNavigationCache::cacheRootNavigation();
	}

	static function cacheNavigationBranch($id){
		$_cacheDir = weNavigationCache::createCacheDir();

		$_id = $id;
		$_c = 0;
		while($_id != 0) {
			weNavigationCache::cacheNavigation($_id);
			$_id = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($_id), 'ParentID', new DB_WE());
			$_c++;
			if($_c > 99999){
				break;
			}
		}
	}

	static function cacheRootNavigation(){
		$_cacheDir = weNavigationCache::createCacheDir();

		$_naviItemes = new weNavigationItems();

		$_naviItemes->initById(0);

		$_content = serialize($_naviItemes->items);

		weFile::save($_cacheDir . 'navigation_0.php', $_content);

		$currentRulesStorage = $_naviItemes->currentRules; // Bug #4142
		foreach($currentRulesStorage as &$rule){
			$rule->deleteDB();
		}
		$_content = serialize($currentRulesStorage);
		unset($currentRulesStorage);

		weFile::save($_cacheDir . 'rules.php', $_content);
	}

	static function cacheNavigation($id){
		$_cacheDir = weNavigationCache::createCacheDir();
		$_naviItemes = new weNavigationItems();
		$_naviItemes->initById($id);
		weFile::save($_cacheDir . 'navigation_' . $id . '.phpz', serialize($_naviItemes->items));
	}

	static function getCacheFromParent($parentid){
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