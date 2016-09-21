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
abstract class we_tool_lookup{
	const REGISTRY_NAME = 'weToolsRegistry';

	static function getAllTools($force = false, $addInternTools = false, $includeDisabled = false){

		if(!$force && !$includeDisabled && !defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['meta'])){
			return $_SESSION['weS'][self::REGISTRY_NAME]['meta'];
		}
		if(!$force && $includeDisabled && !defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['metaIncDis'])){
			return $_SESSION['weS'][self::REGISTRY_NAME]['metaIncDis'];
		}

		$tools = $toolsDirs = [];

		$bd = WE_APPS_PATH;
		$d = opendir($bd);

		while(($entry = readdir($d))){
			$toolsDirs[] = $bd . '/' . $entry;
		}
		closedir($d);

		// include autoload function
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_autoload.inc.php');

		$lang = isset($GLOBALS['WE_LANGUAGE']) ? $GLOBALS['WE_LANGUAGE'] : we_core_Local::getComputedUILang();

		foreach($toolsDirs as $toolDir){
			$metaFile = $toolDir . '/conf/meta.conf.php';
			if(is_dir($toolDir) && file_exists($metaFile)){
				include($metaFile);
				if(isset($metaInfo)){
					$langStr = empty($metaInfo['name']) ? '' : $metaInfo['name'];
					$metaInfo['text'] = oldHtmlspecialchars($langStr);
					if(!$includeDisabled && !empty($metaInfo['appdisabled'])){

					} else {
						$tools[] = $metaInfo;
					}
					unset($metaInfo);
				}
			}
		}
		if($addInternTools){

			$internToolDir = WE_INCLUDES_PATH . 'we_tools/';
			$internTools = array('weSearch', 'navigation');

			foreach($internTools as $toolName){
				$metaFile = $internToolDir . $toolName . '/conf/meta.conf.php';
				if(file_exists($metaFile)){
					include($metaFile);
					if(isset($metaInfo)){
						$metaInfo['text'] = $metaInfo['name'];
						$tools[] = $metaInfo;
						unset($metaInfo);
					}
				}
			}
		}

		if(!defined('NO_SESS') && !$includeDisabled){
			$_SESSION['weS'][self::REGISTRY_NAME]['meta'] = $tools;
		}
		if(!defined('NO_SESS') && $includeDisabled){
			$_SESSION['weS'][self::REGISTRY_NAME]['metaIncDis'] = $tools;
		}

		return $tools;
	}

	static function getToolProperties($name){
		$tools = self::getAllTools(true, false, true);

		foreach($tools as $tool){
			if($tool['name'] == $name){
				return $tool;
			}
		}
		return [];
	}

	static function getPhpCmdInclude(){
		$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
		if(!$cmd0){
			return '';
		}
		//FIX for charset in tools, due to not started session
		$tmps = explode('_', $cmd0);
		switch(isset($tmps[1]) ? $tmps[1] : ''){
			case 'weSearch':
				$_REQUEST['tool'] = $tmps[1];
				return 'we_tools/' . $tmps[1] . '/hook/we_phpCmdHook_' . $tmps[1] . '.inc.php';
			case 'navigation':
				//FIMXE: remove this
				$_REQUEST['mod'] = 'navigation';
				return 'we_modules/navigation/hook/we_phpCmdHook_' . $tmps[1] . '.inc.php';
		}
		$tools = self::getAllTools(true, true);
		foreach($tools as $tool){
			if(stripos($cmd0, 'tool_' . $tool['name'] . '_') === 0){
				$_REQUEST['tool'] = $tool['name'];
				return ($tool['name'] === 'weSearch' || $tool['name'] === 'navigation' ?
						'we_tools/' : 'apps/' ) .
					$tool['name'] . '/hook/we_phpCmdHook_' . $tool['name'] . '.inc.php';
			}
		}

		return '';
	}

	static function getJsCmdInclude(array &$includes){
		$tools = self::getAllTools(true, true);

		foreach($tools as $tool){
			switch($tool['name']){
				case 'weSearch':
					$path = WE_INCLUDES_DIR . 'we_tools/';
					break;
				default:
					$path = WEBEDITION_DIR . 'apps/';
			}
			$path.=$tool['name'] . '/hook/we_jsCmdHook_' . $tool['name'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $path . '.js')){
				$includes['tool_' . $tool['name']] = $path . '.js';
			}
		}
	}

	static function getDefineInclude(){

		if(!defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['defineinclude'])){
			return $_SESSION['weS'][self::REGISTRY_NAME]['defineinclude'];
		}

		$inc = [];
		$tools = self::getAllTools();
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/conf/define.conf.php')){
				$inc[] = WE_APPS_PATH . $tool['name'] . '/conf/define.conf.php';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION['weS'][self::REGISTRY_NAME]['defineinclude'] = $inc;
		}

		return $inc;
	}

	function getExternTriggeredTasks(){

		if(!defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['ExternTriggeredTasks'])){
			//return $_SESSION['weS'][self::REGISTRY_NAME]['ExternTriggeredTasks'];
		}

		$inc = [];
		$tools = self::getAllTools();
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/externtriggered/tasks.php') && we_app_Common::isActive($tool['name'])){
				$inc[] = WE_APPS_PATH . $tool['name'] . '/externtriggered/tasks.php';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION['weS'][self::REGISTRY_NAME]['ExternTriggeredTasks'] = $inc;
		}

		return $inc;
	}

	static function getTagDirs(){

		if(!defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['tagdirs'])){
			return $_SESSION['weS'][self::REGISTRY_NAME]['tagdirs'];
		}

		$inc = [];
		$tools = self::getAllTools();
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/tags')){
				$inc[] = WE_APPS_PATH . $tool['name'] . '/tags';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION['weS'][self::REGISTRY_NAME]['tagdirs'] = $inc;
		}

		return $inc;
	}

	static function isActiveTag($filepath){
		return in_array(dirname($filepath), self::getTagDirs());
	}

	static function isTool($name, $includeDisabled = false){
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if($tool['name'] == $name){
				return true;
			}
		}
		return in_array($name, $tools);
	}

	static function getCmdInclude($namespace, $name, $cmd){
		return WE_APPS_PATH . $name . '/service/cmds' . $namespace . 'rpc' . $cmd . 'Cmd.class.php';
	}

	static function getViewInclude($protocol, $namespace, $name, $view){
		return WE_APPS_PATH . $name . '/service/views/' . $protocol . $namespace . 'rpc' . $view . 'View.class.php';
	}

	static function getAllToolTags($toolname, $includeDisabled = false){
		return self::getFileRegister($toolname, '/tags', 'we_tag_', 'we_tag_', '.inc.php', $includeDisabled);
	}

	static function getAllToolTagWizards($toolname, $includeDisabled = false){
		return self::getFileRegister($toolname, '/tagwizard', 'we_tag_', 'we_tag_', '.inc.php', $includeDisabled);
	}

	static function getAllToolServices($toolname, $includeDisabled = false){
		return self::getFileRegister($toolname, '/service/cmds', '^rpc', 'rpc', 'Cmd.class.php', $includeDisabled);
	}

	static function getAllToolLanguages($toolname, $subdir = '/lang', $includeDisabled = false){
		$founds = [];
		$tooldir = WE_APPS_PATH . $toolname . $subdir;
		$langs = getWELangs();
		if(self::isTool($toolname, $includeDisabled) && is_dir($tooldir)){
			$d = opendir($tooldir);
			while(($entry = readdir($d))){
				if(is_dir($tooldir . '/' . $entry) && stristr($entry, '.') === FALSE){
					$founds[$langs[$entry]] = $tooldir . '/' . $entry . '/default.xml';
				}
			}
			closedir($d);
		}
		return $founds;
	}

	static function getFileRegister($toolname, $subdir, $filematch, $rem_before = '', $rem_after = '', $includeDisabled = false){
		$founds = [];
		$tooldir = WE_APPS_PATH . $toolname . $subdir;
		if(self::isTool($toolname, $includeDisabled) && is_dir($tooldir)){
			$d = opendir($tooldir);
			while(($entry = readdir($d))){
				if(!is_dir($tooldir . '/' . $entry) && stripos($entry, $filematch) !== false){
					$tagname = str_replace(array($rem_before, $rem_after), '', $entry);
					$founds[$tagname] = $tooldir . '/' . $entry;
				}
			}
			closedir($d);
		}
		return $founds;
	}

	static function getToolTag($name, &$include, $includeDisabled = false){
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/tags/we_tag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $tool['name'] . '/tags/we_tag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewTag($name, &$include, $includeDisabled = false){
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/tags/we_listviewtag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $tool['name'] . '/tags/we_listviewtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewItemTag($name, &$include, $includeDisabled = false){
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/tags/we_listviewitemtag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $tool['name'] . '/tags/we_listviewitemtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolTagWizard($name, &$include, $includeDisabled = false){
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/tagwizard/we_tag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $tool['name'] . '/tagwizard/we_tag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewTagWizard($name, &$include, $includeDisabled = false){
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/tagwizard/we_listviewtag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $tool['name'] . '/tagwizard/we_listviewtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getPermissionIncludes($includeDisabled = false){
		$inc = [];
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/conf/permission.conf.php')){
				$inc[] = WE_APPS_PATH . $tool['name'] . '/conf/permission.conf.php';
			}
		}

		return $inc;
	}

	static function getToolsForBackup($includeDisabled = false){
		$inc = [];
		$tools = self::getAllTools(false, false, $includeDisabled);
		foreach($tools as $tool){
			if(file_exists(WE_APPS_PATH . $tool['name'] . '/conf/backup.conf.php')){
				if($tool['maintable'] != ''){
					$inc[] = $tool['name'];
				}
			}
		}
		$inc[] = 'weSearch';


		return $inc;
	}

	static function getBackupTables($name){
		$toolFolder = (($name === 'weSearch' || $name === 'navigation') ?
				WE_INCLUDES_PATH . 'we_tools/' :
				WE_APPS_PATH);
		if(file_exists($toolFolder . $name . '/conf/backup.conf.php')){
			include($toolFolder . $name . '/conf/backup.conf.php');
			if(!empty($toolTables)){
				return $toolTables;
			}
		}
		return [];
	}

	static function getFilesOfDir(&$allFiles, $baseDir){

		if(file_exists($baseDir)){

			$dh = opendir($baseDir);
			while(($entry = readdir($dh))){

				if($entry != '' && $entry != '.' && $entry != '..'){

					$entry = $baseDir . '/' . $entry;

					if(!is_dir($entry)){
						$allFiles[] = $entry;
					}

					if(is_dir($entry)){
						self::getFilesOfDir($allFiles, $entry);
					}
				}
			}
			closedir($dh);
		}
	}

	static function getDirsOfDir(&$allDirs, $baseDir){

		if(file_exists($baseDir)){

			$dh = opendir($baseDir);
			while(($entry = readdir($dh))){

				if($entry != '' && $entry != '.' && $entry != '..'){

					$entry = $baseDir . '/' . $entry;

					if(is_dir($entry)){
						$allDirs[] = $entry;
						self::getDirsOfDir($allDirs, $entry);
					}
				}
			}
			closedir($dh);
		}
	}

	static function getIgnoreList(){
		return array('doctype', 'category', 'navigation', 'toolfactory', 'weSearch');
	}

	static function isInIgnoreList($toolname){
		$ignore = self::getIgnoreList();
		return in_array($toolname, $ignore);
	}

	static function getModelClassName($name){
		$tool = self::getToolProperties($name);
		return (empty($tool) ? '' : $tool['classname']);
	}

}

abstract class weToolLookup extends we_tool_lookup{

}
