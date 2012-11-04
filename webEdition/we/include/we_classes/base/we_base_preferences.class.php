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
include_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

class we_base_preferences{

	static function loadConfigs(){
		// First, read all needed files
		$GLOBALS['config_files'] = array(
			// we_conf.inc.php
			'conf' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_conf.inc.php',
				'content' => '',
			),
			// we_conf_global.inc.php
			'conf_global' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php',
				'content' => '',
			),
			// proxysettings.inc.php
			'proxysettings' => array(
				'filename' => WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php',
				'content' => '',
			),
			// we_active_integrated_modules.inc.php
			'active_integrated_modules' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php',
				'content' => '',
			),
		);
		foreach($GLOBALS['config_files'] as &$config){
			$config['content'] = weFile::load($config['filename']);
			$config['contentBak'] = $config['content'];
		}
	}

	static function setConfigContent($type, $content){
		$GLOBALS['config_files'][$type]['content'] = $content;
	}

	static function unsetConfig($type){
		unset($GLOBALS['config_files'][$type]);
	}

	/**
	 * Checks the global configuration file we_conf_global.inc.php if every needed value
	 * is available and adds missing values.
	 *
	 * @param          $values                                 array
	 *
	 * @return         void
	 */
	static function check_global_config($updateVersion = false){
		$values = $GLOBALS['configs']['global'];

		// Read the global configuration file
		$_file_name = WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php';
		$_file_name_backup = $_file_name . '.bak';
		// load & Cut closing PHP tag from configuration file
		$oldContent = $content = trim(str_replace('?>', '', weFile::load($_file_name)), "\n ");

		// Go through all needed values
		foreach($values as $define => $value){
			if(!preg_match('/define\(["\']' . $define . '["\'],/', $content)){
				// Add needed variable
				$content = weConfParser::changeSourceCode('add', $content, $define, $value[1], true, $value[0]);
			}
		}
		if($updateVersion){
			$content = weConfParser::changeSourceCode('define', $content, 'CONF_SAVED_VERSION', WE_VERSION, true);
		}
		// Check if we need to rewrite the config file
		if($content != $oldContent){
			weFile::save($_file_name_backup, $oldContent);
			weFile::save($_file_name, $content);
		}
	}

	static function userIsAllowed($setting){
		$configs = $GLOBALS['configs'];
		foreach($configs as $name => $config){
			if(isset($config[$setting])){
				switch($name){
					case 'global':
						return (isset($config[$setting][2]) ? we_hasPerm($config[$setting][2]) : we_hasPerm('ADMINISTRATOR'));
					case 'user':
						return true;
					default:
						return (isset($config[$setting][1]) ? we_hasPerm($config[$setting][1]) : we_hasPerm('ADMINISTRATOR'));
				}
			}
		}
	}

}