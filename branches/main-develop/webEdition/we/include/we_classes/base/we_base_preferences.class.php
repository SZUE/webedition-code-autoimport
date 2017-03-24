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
		$GLOBALS['config_files'] = ['conf_conf' => ['filename' => WE_INCLUDES_PATH . 'conf/we_conf.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			],
			// we_conf_global.inc.php
			'conf_global' => ['filename' => WE_INCLUDES_PATH . 'conf/we_conf_global.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			],
			// proxysettings.inc.php
			'conf_proxysettings' => ['filename' => WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			],
			// we_active_integrated_modules.inc.php
			'conf_active_integrated_modules' => ['filename' => WE_INCLUDES_PATH . 'conf/we_active_integrated_modules.inc.php',
				'content' => '',
				'contentBak' => '',
				'contentDef' => '',
			],
		];
		foreach($GLOBALS['config_files'] as &$config){
			$config['content'] = we_base_file::load($config['filename']);
			$config['contentBak'] = $config['content'];
			$config['contentDef'] = we_base_file::load($config['filename'] . '.default') ?: '<?php ';
		}
		//finally add old session prefs
		$GLOBALS['config_files']['oldPrefs'] = isset($_SESSION['prefs']) ? $_SESSION['prefs'] : [];
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
	static function check_global_config($updateVersion = false, $file = '', $leave = []){
		self::loadConfigs();
		$processedConfigs = ($file ?
				['global' => 'contentBak'] :
				['global' => 'contentDef', 'conf' => 'contentDef']);

		$moveToConf = ['DB_SET_CHARSET'];
		foreach($processedConfigs as $conf => $dataField){
			// Read the global configuration file
			$file_name = $GLOBALS['config_files']['conf_' . $conf]['filename'];
			//if we don't have content, make sure, we have at least an php-tag
			$oldContent = (trim(str_replace('?>', '', $GLOBALS['config_files']['conf_' . $conf]['contentBak']), "\n ") ?: "<?php \n");

			if($file && $file != $file_name){//=> this is intentended only for we_conf_global
				//we have the data from a backup file. We need to change e.g. DB-Settings, HTTP-User, ...
				$content = we_base_file::load($file);
				//leave settings in their current state
				foreach($leave as $settingname){
					$active = in_array($settingname, $moveToConf) ? ($conf === 'conf') : true;
					$content = self::changeSourceCode('define', $content, $settingname, (defined($settingname) ? constant($settingname) : ''), $active);
				}
			} else {
				$content = $GLOBALS['config_files']['conf_' . $conf][$dataField];
			}

			// load & Cut closing PHP tag from configuration file
			$content = trim(str_replace(['?>', "\n\n\n\n", "\n\n\n"], ['', "\n\n", "\n\n"], $content), "\n ");

			// Go through all needed values
			foreach($GLOBALS['configs'][$conf] as $define => $value){
				if(!preg_match('/define\(["\']' . $define . '["\'],/', $content)){
					// Add needed variable
					$active = in_array($define, $moveToConf) ? ($conf === 'conf') : ($conf == 'global' ? true : defined($define));
					$content = self::changeSourceCode('add', $content, $define, (defined($define) ? constant($define) : $value[2]), $active, $value[0], isset($value[3]) && $conf != 'global'/* access restriction */ ? $value[3] : false);
					//define it in running session
					if(!defined($define) && $active){
						define($define, $value[2]);
					}
				}
			}
			if($conf == 'global' && $updateVersion){
				$content = self::changeSourceCode('define', $content, 'CONF_SAVED_VERSION', str_replace(['$Rev$'], '', WE_SVNREV), true);
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

		$tmp = !empty($_SESSION['prefs']) && is_array($_SESSION['prefs']) ? array_diff_assoc($_SESSION['prefs'], $GLOBALS['config_files']['oldPrefs']) : [];
		if(!empty($tmp)){
			we_users_user::writePrefs($_SESSION['prefs']['userID'], $GLOBALS['DB_WE']);
		}
		unset($GLOBALS['config_files']);
	}

	static function userIsAllowed($setting){
		if(we_base_permission::hasPerm('ADMINISTRATOR')){
			return true;
		}
		$configs = $GLOBALS['configs'];
		foreach($configs as $name => $config){
			if(isset($config[$setting])){
				switch($name){
					case 'global':
						return (isset($config[$setting][3]) ? we_base_permission::hasPerm($config[$setting][3]) : we_base_permission::hasPerm('ADMINISTRATOR'));
					case 'user':
						return true;
					default:
						return (isset($config[$setting][2]) ? we_base_permission::hasPerm($config[$setting][2]) : we_base_permission::hasPerm('ADMINISTRATOR'));
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
				$match = [];
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
		return str_replace(["\\", '"', "\$"], ["\\\\", '\"', "\\\$"], $in);
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
	public static function getUserPref($name){
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
	public static function setUserPref($name, $value){
		if(isset($_SESSION['prefs'][$name]) && !empty($_SESSION['prefs']['userID'])){
			$_SESSION['prefs'][$name] = $value;
			we_users_user::writePrefs($_SESSION['prefs']['userID'], new DB_WE());
			return true;
		}
		return false;
	}

	public static function we_writeLanguageConfig($default, $available = []){
		$locales = '';
		sort($available);
		foreach($available as $Locale){
			$locales .= "	'" . $Locale . "',\n";
		}

		return we_base_file::save(WE_INCLUDES_PATH . 'conf/we_conf_language.inc.php', '<?php
/**
 * webEdition CMS configuration file
 * NOTE: this file is regenerated, so any extra contents will be overwritten
 */

$GLOBALS[\'weFrontendLanguages\'] = [
' . $locales . '
];

$GLOBALS[\'weDefaultFrontendLanguage\'] = \'' . $default . '\';'
						, 'w+'
		);
	}

	public static function writeDefaultLanguageConfig(){
		$file = WE_INCLUDES_PATH . 'conf/we_conf_language.inc.php';
		if(!file_exists($file) || !is_file($file)){
			self::we_writeLanguageConfig((WE_LANGUAGE === 'Deutsch' || WE_LANGUAGE === 'Deutsch_UTF-8' ? 'de_DE' : 'en_GB'), ['de_DE', 'en_GB']);
		}
	}

	public static function showFrameSet(){
		$tabname = we_base_request::_(we_base_request::STRING, "tabname", we_base_request::_(we_base_request::STRING, 'we_cmd', "setting_ui", 1));

// generate the tabs
		$we_tabs = new we_tabs();
		$validTabs = [];

		foreach($GLOBALS['tabs'] as $name => $list){
			list($icon, $perm) = $list;
			if(empty($perm) || we_base_permission::hasPerm($perm)){
				$we_tabs->addTab(g_l('prefs', '[tab][' . $name . ']'),$icon?:'', ($tabname === 'setting_' . $name), "'" . $name . "'");
				$validTabs[] = $name;
			}
		}

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
				we_html_element::jsScript(JS_DIR . 'preferences_frameset.js', 'self.focus();', ['id' => 'loadVarPreferences_frameset', 'data-prefData' => setDynamicVar([
						'tabs' => array_keys($GLOBALS['tabs']),
						'validTabs' => $validTabs,
			])]), we_html_element::htmlBody(['id' => 'weMainBody', 'onload' => 'weTabs.setFrameSize()', 'onresize' => 'weTabs.setFrameSize()']
						, we_html_element::htmlDiv(['style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;']
								, we_html_element::htmlExIFrame('navi', '<div id="main">' . $we_tabs->getHTML() . '</div>', 'right:0px;') .
								we_html_element::htmlIFrame('content', WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=editor_preferences&" . ($tabname ? "tabname=" . $tabname : ""), 'position:absolute;top:22px;bottom:40px;left:0px;right:0px;overflow: hidden;', 'border:0px;width:100%;height:100%;overflow: scroll;') .
								we_html_element::htmlExIFrame('we_preferences_footer', self::getPreferencesFooter(), 'position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;')
		)));
	}

	private static function getPreferencesFooter(){
		$okbut = we_html_button::create_button(we_html_button::SAVE, 'javascript:we_save();');
		$cancelbut = we_html_button::create_button(we_html_button::CLOSE, 'javascript:top.close()');

		return we_html_element::htmlDiv(['class' => 'weDialogButtonsBody', 'style' => 'height:100%;'], we_html_button::position_yes_no_cancel($okbut, '', $cancelbut, 10, '', '', 0));
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.prefs = {
		add_dictionary_question: "' . g_l('prefs', '[add_dictionary_question]') . '",
		cannot_delete_default_language: "' . we_message_reporting::prepareMsgForJS(g_l('prefs', '[cannot_delete_default_language]')) . '",
		clear_block_entry_question:"' . g_l('prefs', '[clear_block_entry_question]') . '",
		clear_log_question:"' . g_l('prefs', '[clear_log_question]') . '",
		delete_recipient: "' . g_l('alert', '[delete_recipient]') . '",
		input_name: "' . g_l('alert', '[input_name]') . '",
		language_already_exists: "' . we_message_reporting::prepareMsgForJS(g_l('prefs', '[language_already_exists]')) . '",
		language_country_missing: "' . we_message_reporting::prepareMsgForJS(g_l('prefs', '[language_country_missing]')) . '",
		max_name_recipient: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[max_name_recipient]')) . '",
		not_entered_recipient: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[not_entered_recipient]')) . '",
		recipient_exists: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[recipient_exists]')) . '",
		recipient_new_name: "' . g_l('alert', '[recipient_new_name]') . '",
	};';
	}

}
