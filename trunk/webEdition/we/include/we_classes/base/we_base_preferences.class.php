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
require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

class we_base_preferences{

	static function loadConfigs(){
		// First, read all needed files
		$GLOBALS['config_files'] = array(
			// we_conf.inc.php
			'conf_conf' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_conf.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			),
			// we_conf_global.inc.php
			'conf_global' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			),
			// proxysettings.inc.php
			'conf_proxysettings' => array(
				'filename' => WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			),
			// we_active_integrated_modules.inc.php
			'conf_active_integrated_modules' => array(
				'filename' => WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			),
		);
		foreach($GLOBALS['config_files'] as &$config){
			$config['content'] = we_base_file::load($config['filename']);
			$config['contentBak'] = $config['content'];
			$config['contentDef'] = we_base_file::load($config['filename'] . '.default');
		}
		//finally add old session prefs
		$GLOBALS['config_files']['oldPrefs'] = isset($_SESSION['prefs']) ? $_SESSION['prefs'] : array();
	}

	static function setConfigContent($type, $content){
		$GLOBALS['config_files']['conf_' . $type]['content'] = $content;
	}

	static function unsetConfig($type){
		unset($GLOBALS['config_files']['conf_' . $type]);
	}

	/**
	 * Checks the global configuration file we_conf_global.inc.php if every needed value
	 * is available and adds missing values.
	 *
	 * @param
	 *
	 * @return         void
	 */
	static function check_global_config($updateVersion = false, $file = '', $leave = array()){
		self::loadConfigs();
		$processedConfigs = ($file ?
				array('global' => 'contentBak') :
				array('global' => 'contentBak', 'conf' => 'contentBak'));
		foreach($processedConfigs as $conf => $dataField){
			// Read the global configuration file
			$file_name = $GLOBALS['config_files']['conf_' . $conf]['filename'];
			//if we don't have content, make sure, we have at least an php-tag
			$oldContent = (trim(str_replace('?>', '', $GLOBALS['config_files']['conf_' . $conf][$dataField]), "\n ")? : "<?php \n");

			if($file && $file != $file_name){//=> this is intentended only for we_conf_global
				//we have the data from a backup file. We need to change e.g. DB-Settings, HTTP-User, ...
				$content = we_base_file::load($file);
				//leave settings in their current state
				foreach($leave as $settingname){
					$content = self::changeSourceCode('define', $content, $settingname, constant($settingname), true);
				}
				//moved constants
				$content = self::changeSourceCode('define', $content, 'DB_SET_CHARSET', '', false);
			} else {
				$content = $oldContent;
			}

			// load & Cut closing PHP tag from configuration file
			$content = trim(str_replace(array('?>', "\n\n\n\n", "\n\n\n"), array('', "\n\n", "\n\n"), $content), "\n ");

			// Go through all needed values
			foreach($GLOBALS['configs'][$conf] as $define => $value){
				if(!preg_match('/define\(["\']' . $define . '["\'],/', $content)){
					// Add needed variable
					$active = ($conf == 'global' ? true : defined($define));
					$content = self::changeSourceCode('add', $content, $define, (defined($define) ? constant($define) : $value[2]), $active, $value[0], isset($value[3]) && $conf != 'global'/* access restriction */ ? $value[3] : false);
					//define it in running session
					if(!defined($define) && $active){
						define($define, $value[2]);
					}
				}
			}
			if($conf == 'global' && $updateVersion){
				$content = self::changeSourceCode('define', $content, 'CONF_SAVED_VERSION', str_replace(array('$Rev$'), '', WE_SVNREV), true);
			}
			$GLOBALS['config_files']['conf_' . $conf]['contentBak'] = $oldContent;
			$GLOBALS['config_files']['conf_' . $conf]['content'] = $content;
		}
// Check if we need to rewrite the config file
		self::saveConfigs();
	}

	static function saveConfigs(){
		if(!isset($GLOBALS['configs'])){
			t_e('no config set');
			return;
		}
		foreach($GLOBALS['config_files'] as $file){
			if(isset($file['content']) && $file['content'] != $file['contentBak']){ //only save if anything changed
				we_base_file::save($file['filename'] . '.bak', $file['contentBak']);
				we_base_file::save($file['filename'], trim($file['content'], "\n "));
			}
		}

		$tmp = array_diff_assoc($_SESSION['prefs'], $GLOBALS['config_files']['oldPrefs']);
		if(!empty($tmp)){
			we_users_user::writePrefs($_SESSION['prefs']['userID'], $GLOBALS['DB_WE']);
		}
		unset($GLOBALS['config_files']);
	}

	static function userIsAllowed($setting){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		$configs = $GLOBALS['configs'];
		foreach($configs as $name => $config){
			if(isset($config[$setting])){
				switch($name){
					case 'global':
						return (isset($config[$setting][3]) ? permissionhandler::hasPerm($config[$setting][3]) : permissionhandler::hasPerm('ADMINISTRATOR'));
					case 'user':
						return true;
					default:
						return (isset($config[$setting][2]) ? permissionhandler::hasPerm($config[$setting][2]) : permissionhandler::hasPerm('ADMINISTRATOR'));
				}
			}
		}
	}

	public static function changeSourceCode($type, $text, $key, $value, $active = true, $comment = '', $encode = false){
		switch($type){
			case 'add':
				return trim($text, "\n\t ") . "\n\n" .
					self::makeDefine($key, $value, $active, $comment, $encode);
			case 'define':
				$match = array();
				if(preg_match('|/?/?define\(\s*(["\']' . preg_quote($key) . '["\'])\s*,\s*([^\r\n]+)\);[\r\n]?|Ui', $text, $match)){
					return str_replace($match[0], self::makeDefine($key, $value, $active), $text);
				}
		}

		return $text;
	}

	private static function makeDefine($key, $val, $active = true, $comment = '', $encode = false){
		return ($comment ? '//' . $comment . "\n" : '') . ($active ? '' : "//") . 'define(\'' . $key . '\', ' .
			($encode ? 'base64_decode(\'' . base64_encode($val) . '\')' :
				(is_bool($val) || $val === 'true' || $val === 'false' ? ($val ? 'true' : 'false') :
					(!is_numeric($val) ? '"' . self::_addSlashes($val) . '"' : intval($val)))
			) . ');';
	}

	private static function _addSlashes($in){
		return str_replace(array("\\", '"', "\$"), array("\\\\", '\"', "\\\$"), $in);
	}

	/**
	 * This function returns preference for given name; Checks first the users preferences and then global
	 *
	 * @param          string                                  $name
	 *
	 * @see            getAllGlobalPrefs()
	 *
	 * @return         string
	 */
	public function getUserPref($name){
		return (isset($_SESSION['prefs'][$name]) ?
				$_SESSION['prefs'][$name] :
				(defined($name) ? constant($name) : ''));
	}

	/**
	 * The function saves the user pref in the session and the database; The function works with user preferences only
	 *
	 * @param          string                                  $name
	 * @param          string                                  $value
	 *
	 * @see            setUserPref()
	 *
	 * @return         boolean
	 */
	public function setUserPref($name, $value){
		if(isset($_SESSION['prefs'][$name]) && isset($_SESSION['prefs']['userID']) && $_SESSION['prefs']['userID']){
			$_SESSION['prefs'][$name] = $value;
			we_users_user::writePrefs($_SESSION['prefs']['userID'], new DB_WE());
			return true;
		}
		return false;
	}

}
