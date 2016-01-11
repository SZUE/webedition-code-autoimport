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

		$_tools = $_toolsDirs = array();

		$_bd = WE_APPS_PATH;
		$_d = opendir($_bd);

		while(($_entry = readdir($_d))){
			$_toolsDirs[] = $_bd . '/' . $_entry;
		}
		closedir($_d);

		// include autoload function
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_autoload.inc.php');

		$lang = isset($GLOBALS['WE_LANGUAGE']) ? $GLOBALS['WE_LANGUAGE'] : we_core_Local::getComputedUILang();


		foreach($_toolsDirs as $_toolDir){
			$_metaFile = $_toolDir . '/conf/meta.conf.php';
			if(is_dir($_toolDir) && file_exists($_metaFile)){
				include($_metaFile);
				if(isset($metaInfo)){
					$langStr = '';
					if(isset($metaInfo['name'])){
						/*$translate = we_core_Local::addTranslation('default.xml', $metaInfo['name']);
						if(is_object($translate)){
							$langStr = $translate->_($metaInfo['name']);
						}*/
					}
					$metaInfo['text'] = oldHtmlspecialchars($langStr);
					if(!$includeDisabled && !empty($metaInfo['appdisabled'])){

					} else {
						$_tools[] = $metaInfo;
					}
					unset($metaInfo);
				}
			}
		}
		if($addInternTools){

			$internToolDir = WE_INCLUDES_PATH . 'we_tools/';
			$internTools = array('weSearch', 'navigation');

			foreach($internTools as $_toolName){
				$_metaFile = $internToolDir . $_toolName . '/conf/meta.conf.php';
				if(file_exists($_metaFile)){
					include($_metaFile);
					if(isset($metaInfo)){
						$metaInfo['text'] = $metaInfo['name'];
						$_tools[] = $metaInfo;
						unset($metaInfo);
					}
				}
			}
		}

		if(!defined('NO_SESS') && !$includeDisabled){
			$_SESSION['weS'][self::REGISTRY_NAME]['meta'] = $_tools;
		}
		if(!defined('NO_SESS') && $includeDisabled){
			$_SESSION['weS'][self::REGISTRY_NAME]['metaIncDis'] = $_tools;
		}

		return $_tools;
	}

	static function getToolProperties($name){

		$_tools = self::getAllTools(true, false, true);

		foreach($_tools as $_tool){
			if($_tool['name'] == $name){
				return $_tool;
			}
		}
		return array();
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
		$_tools = self::getAllTools(true, true);
		foreach($_tools as $_tool){
			if(stripos($cmd0, 'tool_' . $_tool['name'] . '_') === 0){
				$_REQUEST['tool'] = $_tool['name'];
				return ($_tool['name'] === 'weSearch' || $_tool['name'] === 'navigation' ?
						'we_tools/' : 'apps/' ) .
					$_tool['name'] . '/hook/we_phpCmdHook_' . $_tool['name'] . '.inc.php';
			}
		}

		return '';
	}

	static function getJsCmdInclude(array &$includes){
		$_tools = self::getAllTools(true, true);
		$cmd = '';
		//ob_start();
		foreach($_tools as $_tool){
			$cmd.='	 case "tool_' . $_tool['name'] . '_edit":
 			new (WE().util.jsWindow)(window,url,"tool_window",-1,-1,970,760,true,true,true,true);
		break;
';

			switch($_tool['name']){
				case 'weSearch':
					$path = WE_INCLUDES_DIR . 'we_tools/';
					break;
				default:
					$path = WEBEDITION_DIR . 'apps/';
			}
			$path.=$_tool['name'] . '/hook/we_jsCmdHook_' . $_tool['name'];
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $path . '.js')){
				$includes['tool_' . $_tool['name']] = $path . '.js';
			}
		}

		return 'function we_cmd_tools(args,url) {
	switch (args[0]) {
		' . $cmd . '
		default:
			return false;
	}
	return true;
}
';
	}

	static function getDefineInclude(){

		if(!defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['defineinclude'])){
			return $_SESSION['weS'][self::REGISTRY_NAME]['defineinclude'];
		}

		$_inc = array();
		$_tools = self::getAllTools();
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/conf/define.conf.php')){
				$_inc[] = WE_APPS_PATH . $_tool['name'] . '/conf/define.conf.php';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION['weS'][self::REGISTRY_NAME]['defineinclude'] = $_inc;
		}

		return $_inc;
	}

	function getExternTriggeredTasks(){

		if(!defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['ExternTriggeredTasks'])){
			//return $_SESSION['weS'][self::REGISTRY_NAME]['ExternTriggeredTasks'];
		}

		$_inc = array();
		$_tools = self::getAllTools();
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/externtriggered/tasks.php') && we_app_Common::isActive($_tool['name'])){
				$_inc[] = WE_APPS_PATH . $_tool['name'] . '/externtriggered/tasks.php';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION['weS'][self::REGISTRY_NAME]['ExternTriggeredTasks'] = $_inc;
		}

		return $_inc;
	}

	static function getTagDirs(){

		if(!defined('NO_SESS') && isset($_SESSION['weS'][self::REGISTRY_NAME]['tagdirs'])){
			return $_SESSION['weS'][self::REGISTRY_NAME]['tagdirs'];
		}

		$_inc = array();
		$_tools = self::getAllTools();
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/tags')){
				$_inc[] = WE_APPS_PATH . $_tool['name'] . '/tags';
			}
		}
		if(!defined('NO_SESS')){
			$_SESSION['weS'][self::REGISTRY_NAME]['tagdirs'] = $_inc;
		}

		return $_inc;
	}

	static function isActiveTag($filepath){
		return in_array(dirname($filepath), self::getTagDirs());
	}

	static function isTool($name, $includeDisabled = false){
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if($_tool['name'] == $name){
				return true;
			}
		}
		return in_array($name, $_tools);
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
		$_founds = array();
		$_tooldir = WE_APPS_PATH . $toolname . $subdir;
		$langs = getWELangs();
		if(self::isTool($toolname, $includeDisabled) && is_dir($_tooldir)){
			$_d = opendir($_tooldir);
			while(($_entry = readdir($_d))){
				if(is_dir($_tooldir . '/' . $_entry) && stristr($_entry, '.') === FALSE){
					$_founds[$langs[$_entry]] = $_tooldir . '/' . $_entry . '/default.xml';
				}
			}
			closedir($_d);
		}
		return $_founds;
	}

	static function getFileRegister($toolname, $subdir, $filematch, $rem_before = '', $rem_after = '', $includeDisabled = false){
		$_founds = array();
		$_tooldir = WE_APPS_PATH . $toolname . $subdir;
		if(self::isTool($toolname, $includeDisabled) && is_dir($_tooldir)){
			$_d = opendir($_tooldir);
			while(($_entry = readdir($_d))){
				if(!is_dir($_tooldir . '/' . $_entry) && stripos($_entry, $filematch) !== false){
					$_tagname = str_replace(array($rem_before, $rem_after), '', $_entry);
					$_founds[$_tagname] = $_tooldir . '/' . $_entry;
				}
			}
			closedir($_d);
		}
		return $_founds;
	}

	static function getToolTag($name, &$include, $includeDisabled = false){
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/tags/we_tag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $_tool['name'] . '/tags/we_tag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewTag($name, &$include, $includeDisabled = false){
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/tags/we_listviewtag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $_tool['name'] . '/tags/we_listviewtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewItemTag($name, &$include, $includeDisabled = false){
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/tags/we_listviewitemtag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $_tool['name'] . '/tags/we_listviewitemtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolTagWizard($name, &$include, $includeDisabled = false){
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/tagwizard/we_tag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $_tool['name'] . '/tagwizard/we_tag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getToolListviewTagWizard($name, &$include, $includeDisabled = false){
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/tagwizard/we_listviewtag_' . $name . '.inc.php')){
				$include = WE_APPS_PATH . $_tool['name'] . '/tagwizard/we_listviewtag_' . $name . '.inc.php';
				return true;
			}
		}
		return false;
	}

	static function getPermissionIncludes($includeDisabled = false){
		$_inc = array();
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/conf/permission.conf.php')){
				$_inc[] = WE_APPS_PATH . $_tool['name'] . '/conf/permission.conf.php';
			}
		}

		return $_inc;
	}

	static function getToolsForBackup($includeDisabled = false){
		$_inc = array();
		$_tools = self::getAllTools(false, false, $includeDisabled);
		foreach($_tools as $_tool){
			if(file_exists(WE_APPS_PATH . $_tool['name'] . '/conf/backup.conf.php')){
				if($_tool['maintable'] != ''){
					$_inc[] = $_tool['name'];
				}
			}
		}
		$_inc[] = 'weSearch';


		return $_inc;
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
		return array();
	}

	static function getFilesOfDir(&$allFiles, $baseDir){

		if(file_exists($baseDir)){

			$dh = opendir($baseDir);
			while(($entry = readdir($dh))){

				if($entry != '' && $entry != '.' && $entry != '..'){

					$_entry = $baseDir . '/' . $entry;

					if(!is_dir($_entry)){
						$allFiles[] = $_entry;
					}

					if(is_dir($_entry)){
						self::getFilesOfDir($allFiles, $_entry);
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

					$_entry = $baseDir . '/' . $entry;

					if(is_dir($_entry)){
						$allDirs[] = $_entry;
						self::getDirsOfDir($allDirs, $_entry);
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
		$_ignore = self::getIgnoreList();
		return in_array($toolname, $_ignore);
	}

	static function getModelClassName($name){
		if($name === 'weSearch' || $name === 'navigation'){
			include(WE_INCLUDES_PATH . 'we_tools/' . $name . '/conf/meta.conf.php');
			return $metaInfo['classname'];
		}

		$_tool = self::getToolProperties($name);
		if(!empty($_tool)){
			return $_tool['classname'];
		}

		return '';
	}

}

abstract
	class weToolLookup extends we_tool_lookup{

}
