<?php

abstract class we_base_moduleInfo{
	const BANNER = 'banner';
	const COLLECTION = 'collection';
	const CUSTOMER = 'customer';
	const EDITOR = 'editor';
	const EXPORT = 'export';
	const GLOSSARY = 'glossary';
	const NAVIGATION = 'navigation';
	const NEWSLETTER = 'newsletter';
	const OBJECT = 'object';
	const SCHEDULER = 'schedule';
	const SEARCH = 'weSearch';
	const SHOP = 'shop';
	const SPELLCHECKER = 'spellchecker';
	const USERS = 'users';
	const VOTING = 'voting';
	const WORKFLOW = 'workflow';

	private static $we_available_modules = [];
	private static $userEnabledModules = [];
	private static $activeModules = [];

	private static function init(){
		if(!self::$we_available_modules){
			self::$we_available_modules = include(WE_INCLUDES_PATH . 'we_available_modules.inc.php');
			include_once(WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php');
			if(empty($GLOBALS['_we_active_integrated_modules'])){
				include_once(WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php.default');
			}
			self::$userEnabledModules = $GLOBALS['_we_active_integrated_modules'];
		}
	}

	public static function getUserEnabledModules(){
		self::init();
		return self::$userEnabledModules;
	}

	/**
	 * Orders a hash array of the scheme of we_available_modules
	 *
	 * @param hash $array
	 */
	static function orderModuleArray(&$array){
		uasort($array, function ($a, $b){
			return (strcmp($a['text'], $b['text']));
		});
	}

	/**
	 * returns hash with All modules
	 *
	 * @return hash
	 */
	static function getAllModules(){
		self::init();
		return self::$we_available_modules;
	}

	/**
	 * returns hash of all modules
	 * @return hash
	 */
	static function getIntegratedModules($active){
		self::init();
		$retArr = [];

		foreach(self::$we_available_modules as $key => $modInfo){
			if(self::isActive($key) == $active){
				$retArr[$key] = $modInfo;
			}
		}

		return $retArr;
	}

	/**
	 * returns whether a module is in the menu or not
	 * @param string $modulekey
	 * @return boolean
	 */
	static function showModuleInMenu($modulekey){
		self::init();
		// show a module, if
		// - it is active
		// - if it is in module window

		return (self::$we_available_modules[$modulekey]['inModuleMenu'] && self::isActive($modulekey));
	}

	static function isActive($modul){
		self::init();
		return in_array($modul, self::$activeModules);
	}

	static function getAllActiveModules(){
		return self::$activeModules;
	}

	static function loadConfigs(){
		self::init();
		self::$activeModules = [];
		foreach(self::$we_available_modules as $modul => $conf){
			if($conf['alwaysActive'] || in_array($modul, self::$userEnabledModules)){
				self::$activeModules[] = $modul;
				if(file_exists(WE_MODULES_PATH . $modul . '/we_conf_' . $modul . '.inc.php')){
					require_once (WE_MODULES_PATH . $modul . '/we_conf_' . $modul . '.inc.php');
				}
			}
		}
	}

	static function getModuleData($module){
		self::init();
		if(isset(self::$we_available_modules[$module])){
			return self::$we_available_modules[$module];
		}
		return false;
	}

}
