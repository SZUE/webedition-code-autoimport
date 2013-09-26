<?php

abstract class weModuleInfo{

	private static $we_available_modules = '';

	private static function init(){
		if(empty(self::$we_available_modules)){
			self::$we_available_modules = include(WE_INCLUDES_PATH . 'we_available_modules.inc.php');
		}
	}

	static function _orderModules($a, $b){
		return (strcmp($a['text'], $b['text']));
	}

	/**
	 * Orders a hash array of the scheme of we_available_modules
	 *
	 * @param hash $array
	 */
	static function orderModuleArray(&$array){
		uasort($array, array('weModuleInfo', '_orderModules'));
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
	 * returns hash with all buyable modules
	 *
	 * @return hash
	 */
	static function getNoneIntegratedModules(){
		self::init();

		$retArr = array();

		foreach(self::$we_available_modules as $key => $modInfo){
			if($modInfo['integrated'] == false){
				$retArr[$key] = $modInfo;
			}
		}

		return $retArr;
	}

	/**
	 * @param string $mKey
	 * @return boolean
	 */
	static function isModuleInstalled($mKey){
		self::init();
		return (in_array($mKey, self::$we_available_modules) || $mKey == 'editor');
	}

	/**
	 * returns hash of all integrated modules
	 * @return hash
	 */
	static function getIntegratedModules($active = null){
		self::init();

		$retArr = array();

		foreach(self::$we_available_modules as $key => $modInfo){
			if($modInfo['integrated'] == true){

				if($active === null){
					$retArr[$key] = $modInfo;
				} else if(self::isActive($key) == $active){
					$retArr[$key] = $modInfo;
				}
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

		if(self::$we_available_modules[$modulekey]["inModuleMenu"] && self::isActive($modulekey)){
			return true;
		}

		//}

		return false;
	}

	static function isActive($modul){
		self::init();
		return in_array($modul, $GLOBALS['_we_active_integrated_modules']);
	}

	static function getModuleData($module){
		self::init();
		if(isset(self::$we_available_modules[$module])){
			return self::$we_available_modules[$module];
		}
	}

}