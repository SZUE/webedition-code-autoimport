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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

//NOTE: only add "newConf" to entries set in $GLOBALS['configs']. All "temporary" entries should remain in main-Request-Scope

we_html_tools::protect();

$weSuggest = &weSuggest::getInstance();

define('secondsDay', 86400);
define('secondsWeek', 604800);
define('secondsYear', 31449600);

$save_javascript = '';
$GLOBALS['editor_reloaded'] = false;
$email_saved = true;
$tabname = we_base_request::_(we_base_request::STRING, 'tabname', 'setting_ui');

/**
 * This function returns the HTML code of a dialog.
 *
 * @param          string                                  $name
 * @param          string                                  $title
 * @param          array                                   $content
 * @param          int                                     $expand             (optional)
 * @param          string                                  $show_text          (optional)
 * @param          string                                  $hide_text          (optional)
 * @param          bool                                    $cookie             (optional)
 * @param          string                                  $JS                 (optional)
 *
 * @return         string
 */
function create_dialog($name, array $content, $expand = -1, $show_text = '', $hide_text = ''){
	$output = ($expand != -1 ? we_html_multiIconBox::getJS() : '');

	// Return HTML code of dialog
	return $output . we_html_multiIconBox::getHTML($name, $content, 30, '', $expand, $show_text, $hide_text);
}

/*
  function getColorInput($name, $value, $disabled = false, $width = 20, $height = 20){
  return we_html_element::htmlHidden($name, $value) . '<table class="default" style="border:1px solid grey;margin:2px 0px;"><tr><td' .
  ($disabled ? ' class="disabled"' : '') .
  ' id="color_' . $name . '" ' .
  ($value ? (' style="background-color:' . $value . ';"') : '') .
  '><a style="cursor:' .
  ($disabled ? "default" : "pointer") .
  ';" href="javascript:if(document.getElementById(&quot;color_' . $name . '&quot;).getAttribute(&quot;class&quot;)!=&quot;disabled&quot;) {we_cmd(\'openColorChooser\',\'' . $name . '\',document.we_form.elements[\'' . $name . '\'].value,&quot;opener.setColorField(\'' . $name . '\');&quot;);}"><span style="width:' . $width . 'px;height:' . $height . '"></span></a></td></tr></table>';
  } */

/**
 * This functions return either the saved option or the changed one.
 *
 * @param          string                                  $settingvalue
 *
 * @see            return_value()
 *
 * @return         unknown
 */
function get_value($settingname){
	$all = explode('-', $settingname);
	$settingname = $all[0];
	switch($settingname){
		case 'specify_jeditor_colors':
			return (isset($_SESSION['prefs'][$settingname]) ? $_SESSION['prefs'][$settingname] : 1);

		case 'seem_start_type':
			if(($_SESSION['prefs']['seem_start_type'] === 'document' || $_SESSION['prefs']['seem_start_type'] === 'object') && $_SESSION['prefs']['seem_start_file'] == 0){
				return 'cockpit';
			}
			return $_SESSION['prefs']['seem_start_type'];

		case 'locale_locales':
			return getWeFrontendLanguagesForBackend();

		case 'locale_default':
			return $GLOBALS['weDefaultFrontendLanguage'];

		case 'proxy_proxy':
			// Check for settings file
			if(file_exists(WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php')){
				require_once(WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php');
			}
			return defined('WE_PROXYHOST');

		case 'message_reporting':
			return (!empty($_SESSION['prefs']['message_reporting'])) ? $_SESSION['prefs']['message_reporting'] : (we_message_reporting::WE_MESSAGE_ERROR + we_message_reporting::WE_MESSAGE_WARNING + we_message_reporting::WE_MESSAGE_NOTICE);

		default:
			if(isset($GLOBALS['configs']['user'][$settingname])){
				if(isset($all[1])){
					//handle subkey
					$tmp = we_unserialize(isset($_SESSION['prefs'][$settingname]) ? $_SESSION['prefs'][$settingname] : $GLOBALS['configs']['user'][$settingname][0]);
					return isset($tmp[$all[1]]) ? $tmp[$all[1]] : 0;
				}
				return (isset($_SESSION['prefs'][$settingname]) ? $_SESSION['prefs'][$settingname] : $GLOBALS['configs']['user'][$settingname][0]);
			}

			//if not found in global_config or other config - simply return '' - this should not happen - should we return something more error-specific?
			return defined($settingname) ?
				constant($settingname) :
				(isset($GLOBALS['configs']['global'][$settingname]) ? $GLOBALS['configs']['global'][$settingname][2] :
				(isset($GLOBALS['configs']['other'][$settingname]) ? $GLOBALS['configs']['other'][$settingname][1] :
				'')
				);
	}
}

/**
 * This functions saves an option in the current session.
 *
 * @param          string                                  $settingvalue
 * @param          string                                  $settingname
 *
 * @see            save_all_values
 * @see            we_base_preferences::changeSourceCode()
 *
 * @return         bool
 */
function remember_value($settingvalue, $settingname, $comment = ''){
	$DB_WE = $GLOBALS['DB_WE'];
	if(isset($GLOBALS['configs']['user'][$settingname]) && $settingvalue == null){ //checkboxes -> unchecked - all other values are set by the form
		$settingvalue = 0;
	}

	//check for user-setting
	switch($settingname){
		default:
			if(isset($GLOBALS['configs']['user'][$settingname])){
				$_SESSION['prefs'][$settingname] = ($settingvalue == null ? 0 : $settingvalue);
			} else {
				$file = &$GLOBALS['config_files']['conf_global']['content'];
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;
		case 'seem_start_file'://don't do anything here
			return;
		case 'seem_start_type':
			switch($settingvalue){
				case 'document':
					$tmp = $_SESSION['prefs']['seem_start_file'] = we_base_request::_(we_base_request::INT, 'seem_start_document');
					$_SESSION['prefs'][$settingname] = ($tmp ? $settingvalue : 'cockpit');
					break;
				case 'object':
					$tmp = $_SESSION['prefs']['seem_start_file'] = we_base_request::_(we_base_request::INT, 'seem_start_object');
					$_SESSION['prefs'][$settingname] = ($tmp ? $settingvalue : 'cockpit');
					break;
				default:
					$_SESSION['prefs'][$settingname] = $settingvalue;
					break;
			}
			return;
		case 'sizeOpt':
			if($settingvalue == 0){
				$_SESSION['prefs']['weWidth'] = 0;
				$_SESSION['prefs']['weHeight'] = 0;
				$_SESSION['prefs']['sizeOpt'] = 0;
			} else if(($settingvalue == 1) && we_base_request::_(we_base_request::INT, 'newconf', false, 'weWidth') !== false && we_base_request::_(we_base_request::INT, 'newconf', false, 'weHeight') !== false){
				$_SESSION['prefs']['sizeOpt'] = 1;
			}

			return;

		case 'weWidth':
			if($_SESSION['prefs']['sizeOpt'] == 1){
				$generate_java_script = ($_SESSION['prefs']['weWidth'] != $settingvalue);

				$_SESSION['prefs']['weWidth'] = $settingvalue;

				if($generate_java_script){
					$height = we_base_request::_(we_base_request::INT, 'newconf', 0, "weHeight");
					$GLOBALS['save_javascript'] .= "
setNewWESize(" . $settingvalue . ", " . $height . ");";
				}
			}
			return;

		case 'weHeight':
			if($_SESSION['prefs']['sizeOpt'] == 1){
				$_SESSION['prefs'][$settingname] = $settingvalue;
			}
			return;

		case 'editorFont':
			if(intval($settingvalue) == 0){
				$_SESSION['prefs']['editorFontname'] = 'none';
				$_SESSION['prefs']['editorFontsize'] = -1;
				$_SESSION['prefs']['editorFont'] = 0;
			} else if(($settingvalue == 1) && we_base_request::_(we_base_request::STRING, 'newconf', '', 'editorFontname') && we_base_request::_(we_base_request::INT, 'newconf', 0, 'editorFontsize')){
				$_SESSION['prefs']['editorFont'] = 1;
			}

			if(!$GLOBALS['editor_reloaded']){
				$GLOBALS['editor_reloaded'] = true;

				// editor font has changed - mark all editors to reload!
				$GLOBALS['save_javascript'] .= '
if (!_multiEditorreload) {
	reloadUsedEditors();
}
_multiEditorreload = true;';
			}
			return;

		case 'editorCodecompletion':
			$_SESSION['prefs'][$settingname] = is_array($settingvalue) ? we_serialize($settingvalue, SERIALIZE_JSON) : '';
			return;
		case 'editorFontname':
		case 'editorFontsize':
			if($_SESSION['prefs']['editorFont'] == 1){
				$_SESSION['prefs'][$settingname] = $settingvalue;
			}

			return;

		case 'editorTooltipFont':
			if(intval($settingvalue) == 0){
				$_SESSION['prefs']['editorTooltipFontname'] = 'none';
				$_SESSION['prefs']['editorTooltipFontsize'] = -1;
				$_SESSION['prefs']['editorTooltipFont'] = 0;
			} else if(($settingvalue == 1) && we_base_request::_(we_base_request::STRING, 'newconf', '', 'editorTooltipFontname') && we_base_request::_(we_base_request::INT, 'newconf', 0, 'editorTooltipFontsize')){
				$_SESSION['prefs']['editorTooltipFont'] = 1;
			}

			if(!$GLOBALS['editor_reloaded']){
				$GLOBALS['editor_reloaded'] = true;

				// editor tooltip font has changed - mark all editors to reload!
				$GLOBALS['save_javascript'] .= '
if (!_multiEditorreload) {
reloadUsedEditors();
}
_multiEditorreload = true;';
			}

			return;

		case 'Language': //Handle both
			$_SESSION['prefs'][$settingname] = $settingvalue;
			$_SESSION['prefs']['BackendCharset'] = we_base_request::_(we_base_request::STRING, 'newconf', '', 'BackendCharset');


			if($settingvalue != $GLOBALS['WE_LANGUAGE'] || $_SESSION['prefs']['BackendCharset'] != $GLOBALS['WE_BACKENDCHARSET']){

				// complete webEdition reload: anpassen nach Wegfall der Frames
				$GLOBALS['save_javascript'] .= "
reloadUsedEditors(true);
_multiEditorreload = true;";
			}

		case 'locale_locales':
			return;
		case 'locale_default':
			if(($loc = we_base_request::_(we_base_request::STRING, 'newconf', '', 'locale_locales')) && ($def = we_base_request::_(we_base_request::STRING, 'newconf', '', 'locale_default'))){
				we_base_preferences::we_writeLanguageConfig($def, explode(',', $loc));
			}
			return;

		case 'WE_COUNTRIES_TOP':
			$file = &$GLOBALS['config_files']['conf_global']['content'];
			$file = we_base_preferences::changeSourceCode('define', $file, $settingname, implode(',', array_keys(we_base_request::_(we_base_request::INT, 'newconf', 0, 'countries'), 2)), true, $comment);
			return;

		case 'WE_COUNTRIES_SHOWN':
			$file = &$GLOBALS['config_files']['conf_global']['content'];
			$file = we_base_preferences::changeSourceCode('define', $file, $settingname, implode(',', array_keys(we_base_request::_(we_base_request::INT, 'newconf', 0, 'countries'), 1)), true, $comment);
			return;
		case 'SYSTEM_WE_SESSION_TIME':
			//check, that a session lasts at least 90 seconds, due to we-pings, this is sufficient for WE - other is up to the user.
			$file = &$GLOBALS['config_files']['conf_global']['content'];
			$file = we_base_preferences::changeSourceCode('define', $file, $settingname, max($settingvalue, 90), true, $comment);
			return;

		case 'WE_SEEM':
			$file = &$GLOBALS['config_files']['conf_global']['content'];
			if(intval($settingvalue) == constant($settingname)){
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, ($settingvalue == 1 ? 0 : 1), true, $comment);
			}
			return;

		case 'WE_LOGIN_HIDEWESTATUS':
			$file = &$GLOBALS['config_files']['conf_global']['content'];
			if($settingvalue != constant($settingname)){
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;
		case 'WE_LOGIN_WEWINDOW':
			if(constant($settingname) != $settingvalue){
				$file = &$GLOBALS['config_files']['conf_global']['content'];
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'SIDEBAR_DISABLED':
			$file = &$GLOBALS['config_files']['conf_global']['content'];
			if($settingvalue != SIDEBAR_DISABLED){
				$file = we_base_preferences::changeSourceCode('define', $file, 'SIDEBAR_DISABLED', $settingvalue, true, $comment);
			}

			$sidebar_show_on_startup = we_base_request::_(we_base_request::BOOL, 'newconf', false, 'SIDEBAR_SHOW_ON_STARTUP');
			if(SIDEBAR_SHOW_ON_STARTUP != $sidebar_show_on_startup){
				$file = we_base_preferences::changeSourceCode('define', $file, 'newconf[SIDEBAR_SHOW_ON_STARTUP]', $sidebar_show_on_startup);
			}

			$sidebar_document = we_base_request::_(we_base_request::INT, 'newconf', 0, 'SIDEBAR_DEFAULT_DOCUMENT');
			if(SIDEBAR_DEFAULT_DOCUMENT != $sidebar_document){
				$file = we_base_preferences::changeSourceCode('define', $file, 'newconf[SIDEBAR_DEFAULT_DOCUMENT]', $sidebar_document);
			}

			$sidebar_width = we_base_request::_(we_base_request::INT, 'newconf', 0, 'SIDEBAR_DEFAULT_WIDTH');
			if(SIDEBAR_DEFAULT_WIDTH != $sidebar_width){
				$file = we_base_preferences::changeSourceCode('define', $file, 'newconf[SIDEBAR_DEFAULT_WIDTH]', $sidebar_width);
			}

			return;

		case 'DEFAULT_STATIC_EXT':
		case 'DEFAULT_DYNAMIC_EXT':
		case 'DEFAULT_HTML_EXT':
			if(constant($settingname) != $settingvalue){
				$file = &$GLOBALS['config_files']['conf_global']['content'];
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;

		//FORMMAIL RECIPIENTS
		case 'formmail_values':
			if($settingvalue){
				$recipients = explode('<##>', $settingvalue);
				if($recipients){
					foreach($recipients as $recipient){
						$single_recipient = explode('<#>', $recipient);

						if(isset($single_recipient[0]) && ($single_recipient[0] === '#')){
							if(!empty($single_recipient[1])){
								$DB_WE->query('INSERT INTO ' . RECIPIENTS_TABLE . ' (Email) VALUES("' . $DB_WE->escape($single_recipient[1]) . '")');
							}
						} else {
							if(!empty($single_recipient[1]) && !empty($single_recipient[0])){
								$DB_WE->query('UPDATE ' . RECIPIENTS_TABLE . ' SET Email="' . $DB_WE->escape($single_recipient[1]) . '" WHERE ID=' . intval($single_recipient[0]));
							}
						}
					}
				}
			}

			return;

		case 'formmail_deleted':
			if($settingvalue){
				$formmail_deleted = explode(',', $settingvalue);
				foreach($formmail_deleted as $del){
					$DB_WE->query('DELETE FROM ' . RECIPIENTS_TABLE . ' WHERE ID=' . intval($del));
				}
			}
			return;

		case 'active_integrated_modules':
			$GLOBALS['config_files']['conf_active_integrated_modules']['content'] = '<?php
$GLOBALS[\'_we_active_integrated_modules\'] = [
\'' . implode("',\n'", we_base_request::_(we_base_request::STRING, 'newconf', [], 'active_integrated_modules')) . '\'
];';
			return;

		case 'useproxy':
			if($settingvalue == 1){
				// Create/overwrite proxy settings file
				$host = we_base_request::_(we_base_request::STRING, 'newconf', '', "proxyhost");
				$port = we_base_request::_(we_base_request::INT, 'newconf', '', "proxyport");
				$user = we_base_request::_(we_base_request::STRING, 'newconf', '', "proxyuser");
				$pass = str_replace('"', '', we_base_request::_(we_base_request::RAW_CHECKED, 'newconf', '', "proxypass"));
				$pass = ($pass === we_customer_customer::NOPWD_CHANGE ? (defined('WE_PROXYPASSWORD') ? WE_PROXYPASSWORD : '') : $pass);
				we_base_preferences::setConfigContent('proxysettings', '<?php
	define(\'WE_PROXYHOST\', "' . $host . '");
	define(\'WE_PROXYPORT\', ' . $port . ');
	define(\'WE_PROXYUSER\', "' . $user . '");
	define(\'WE_PROXYPASSWORD\', "' . $pass . '");'
				);
			} else {
				// Delete proxy settings file
				if(file_exists(WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php')){
					unlink(WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php');
				}
				we_base_preferences::unsetConfig('proxysettings');
			}
			return;

		case 'proxyhost':
		case 'proxyport':
		case 'proxyuser':
		case 'proxypass':
			return;
		case 'SMTP_PASSWORD':
			if($settingvalue !== we_customer_customer::NOPWD_CHANGE){
				$file = &$GLOBALS['config_files']['conf_global']['content'];
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;

		// ADVANCED
		case 'DB_CONNECT':
			$file = &$GLOBALS['config_files']['conf_conf']['content'];
			$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue);
			return;

		case 'DB_SET_CHARSET':
			$file = &$GLOBALS['config_files']['conf_conf']['content'];

			if(!defined($settingname) || $settingvalue != constant($settingname)){
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'useauth':
			$file = &$GLOBALS['config_files']['conf_conf']['content'];
			if($settingvalue == 1){
				// enable
				if(!(defined('HTTP_USERNAME')) || !(defined('HTTP_PASSWORD'))){
					$file = we_base_preferences::changeSourceCode('define', $file, 'HTTP_USERNAME', 'myUsername', false);
					$file = we_base_preferences::changeSourceCode('define', $file, 'HTTP_PASSWORD', 'myPassword', false);
				}

				$un = defined('HTTP_USERNAME') ? HTTP_USERNAME : '';
				$pw = defined('HTTP_PASSWORD') ? HTTP_PASSWORD : '';
				$un1 = we_base_request::_(we_base_request::STRING, 'newconf', '', 'HTTP_USERNAME');
				$pw1 = we_base_request::_(we_base_request::STRING, 'newconf', '', 'HTTP_PASSWORD');
				if($un != $un1 || $pw1 != we_customer_customer::NOPWD_CHANGE){

					$file = we_base_preferences::changeSourceCode('define', $file, 'HTTP_USERNAME', $un1);
					$file = we_base_preferences::changeSourceCode('define', $file, 'HTTP_PASSWORD', ($pw1 === we_customer_customer::NOPWD_CHANGE ? $pw : $pw1));
				}
			} else {
				// disable
				if(defined('HTTP_USERNAME') || defined('HTTP_PASSWORD')){
					$file = we_base_preferences::changeSourceCode('define', $file, 'HTTP_USERNAME', 'myUsername', false);
					$file = we_base_preferences::changeSourceCode('define', $file, 'HTTP_PASSWORD', 'myPassword', false);
				}
			}
			return;

		case 'HTTP_USERNAME':
		case 'HTTP_PASSWORD':
			return;

		//ERROR HANDLING
		case 'WE_ERROR_HANDLER':
		case 'WE_ERROR_NOTICES':
		case 'WE_ERROR_DEPRECATED':
		case 'WE_ERROR_WARNINGS':
		case 'WE_ERROR_ERRORS':
		case 'WE_ERROR_SHOW':
		case 'WE_ERROR_LOG':
			$file = &$GLOBALS['config_files']['conf_global']['content'];

			if($settingvalue != constant($settingname)){
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'WE_ERROR_MAIL':
			$file = &$GLOBALS['config_files']['conf_global']['content'];

			if(!$settingvalue && WE_ERROR_MAIL){
				$file = we_base_preferences::changeSourceCode('define', $file, 'WE_ERROR_MAIL', 0, true, $comment);
			} else if($settingvalue && !WE_ERROR_MAIL){
				$file = we_base_preferences::changeSourceCode('define', $file, 'WE_ERROR_MAIL', 1, true, $comment);
			}
			return;

		case 'WE_ERROR_MAIL_ADDRESS':
			$file = &$GLOBALS['config_files']['conf_global']['content'];

			if(WE_ERROR_MAIL_ADDRESS != $settingvalue){
				$file = we_base_preferences::changeSourceCode('define', $file, 'WE_ERROR_MAIL_ADDRESS', $settingvalue, true, $comment);
			}
			return;

		case 'ERROR_DOCUMENT_NO_OBJECTFILE':
			if(!defined($settingname) || constant($settingname) != $settingvalue){
				$file = &$GLOBALS['config_files']['conf_global']['content'];
				$file = we_base_preferences::changeSourceCode('define', $file, $settingname, $settingvalue, true, $comment);
			}
			return;
	}
}

/**
 * This functions saves all options.
 *
 * @see            remember_value()
 *
 * @return         void
 */
function save_all_values(){
	we_base_preferences::loadConfigs();
	//set config to latest version
	$_REQUEST['newconf']['CONF_SAVED_VERSION'] = WE_SVNREV;
	// Second, change sourcecodes of the configfiles
	foreach($GLOBALS['configs'] as $name => $conf){
		foreach($conf as $key => $default){
			switch($name){
				case 'conf':
				case 'global'://no settings in session
					if(we_base_preferences::userIsAllowed($key)){
						remember_value(we_base_request::_($default[1], 'newconf', null, $key), $key, $default[0]);
					}
					break;
				case 'user':
					remember_value(we_base_request::_($default[0], 'newconf', null, $key), $key);
					break;
				default:
					if(we_base_preferences::userIsAllowed($key)){
						remember_value(we_base_request::_($default[0], 'newconf', null, $key), $key);
					}
					break;
			}
		}
	}
	if(isset($_SESSION['weS']['versions']) && isset($_SESSION['weS']['versions']['logPrefs'])){
		$_SESSION['weS']['versions']['logPrefsChanged'] = [];
		foreach(array_keys($_SESSION['weS']['versions']['logPrefs']) as $k){
			if(isset($_REQUEST['newconf'][$k])){
				if($_SESSION['weS']['versions']['logPrefs'][$k] != $_REQUEST['newconf'][$k]){
					$_SESSION['weS']['versions']['logPrefsChanged'][$k] = $_REQUEST['newconf'][$k];
				}
			} elseif($_SESSION['weS']['versions']['logPrefs'][$k] != ''){
				$_SESSION['weS']['versions']['logPrefsChanged'][$k] = '';
			}
		}

		if(($_SESSION['weS']['versions']['logPrefsChanged'])){
			$versionslog = new we_versions_log();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logPrefsChanged'], we_versions_log::VERSIONS_PREFS);
		}
		unset($_SESSION['weS']['versions']['logPrefs']);
		unset($_SESSION['weS']['versions']['logPrefsChanged']);
	}

	//SAVE CHANGES
	// Third save all changes of the config files
	we_base_preferences::saveConfigs();
}

/**
 * This builds every single dialog (of a tab).
 *
 * @param          $selected_setting                       string              (optional)
 *
 * @see            render_dialog()
 *
 * @return         string
 */
function build_dialog($selected_setting = 'ui'){
	global $DB_WE;
	$weSuggest = & weSuggest::getInstance();

	switch($selected_setting){
		case 'save':

			return create_dialog('', [['headline' => '', 'html' => g_l('prefs', '[save]'),]
			]);

		case 'saved'://SAVED SUCCESSFULLY DIALOG
			return create_dialog('', [['headline' => '', 'html' => g_l('prefs', '[saved]'),]
			]);

		case 'ui':
			//LANGUAGE
			$settings = [];

			//	Look which languages are installed ...
			$language_directory = dir(WE_INCLUDES_PATH . 'we_language');

			while(false !== ($entry = $language_directory->read())){
				if($entry != '.' && $entry != '..'){
					if(is_dir(WE_INCLUDES_PATH . 'we_language/' . $entry)){
						$language[$entry] = $entry;
					}
				}
			}
			global $languages;

			if(!empty($language)){ // Build language select box
				$languages = new we_html_select(['name' => 'newconf[Language]', 'class' => 'weSelect', 'onchange' => "document.getElementById('langnote').style.display='block'"]);
				foreach($language as $key => $value){
					$languages->addOption($key, $value);
				}
				$languages->selectOption(get_value('Language'));
				// Lang notice
				$langNote = '<div id="langnote" style="padding: 5px; background-color: rgb(221, 221, 221); width: 190px; display:none">
<table style="width:100%">
<tbody>
<tr>
<td style="padding-right: 10px;vertical-align:top">
	<span class="fa-stack fa-lg" style="font-size: 14px;color:#007de3;"><i class="fa fa-circle fa-stack-2x" ></i><i class="fa fa-info fa-stack-1x fa-inverse"></i></span>
</td>
<td class="middlefont">' . g_l('prefs', '[language_notice]') . '
</td>
</tr>
</tbody>
</table>
</div>';
				// Build dialog
				$settings[] = ['headline' => g_l('prefs', '[choose_language]'), 'html' => $languages->getHtml() . '<br/><br/>' . $langNote, 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1];
			} else { // Just one Language Installed, no select box needed
				foreach($language as $key => $value){
					$languages = $value;
				}
				// Build dialog
				$settings[] = ['headline' => g_l('prefs', '[choose_language]'), 'html' => $languages, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1];
			}

			$BackendCharset = new we_html_select(['name' => 'newconf[BackendCharset]', 'class' => 'weSelect', 'onchange' => "document.getElementById('langnote').style.display='block'"]);
			$c = we_base_charsetHandler::getAvailCharsets();
			foreach($c as $char){
				$BackendCharset->addOption($char, $char);
			}
			$BackendCharset->selectOption(get_value('BackendCharset'));
			$settings[] = ['headline' => g_l('prefs', '[choose_backendcharset]'), 'html' => $BackendCharset->getHtml() . '<br/><br/>' . $langNote, 'space' => we_html_multiIconBox::SPACE_BIG];


			// DEFAULT CHARSET
			if(we_base_preferences::userIsAllowed('DEFAULT_CHARSET')){
				$charsetHandler = new we_base_charsetHandler();
				$charsets = $charsetHandler->getCharsetsForTagWizzard();
				$charset = $GLOBALS['WE_BACKENDCHARSET'];
				$GLOBALS['weDefaultCharset'] = get_value('DEFAULT_CHARSET');
				$defaultCharset = we_html_tools::htmlTextInput('newconf[DEFAULT_CHARSET]', 8, $GLOBALS['weDefaultCharset'], 255, '', 'text', 100);
				$defaultCharsetChooser = we_html_tools::htmlSelect('DefaultCharsetSelect', $charsets, 1, $GLOBALS['weDefaultCharset'], false, ['onchange' => "document.forms[0].elements['newconf[DEFAULT_CHARSET]'].value=this.options[this.selectedIndex].value;"], "value", 100, "defaultfont", false);
				$DEFAULT_CHARSET = '<table class="default"><tr><td>' . $defaultCharset . '</td><td>' . $defaultCharsetChooser . '</td></tr></table>';

				$settings[] = ['headline' => g_l('prefs', '[default_charset]'),
					'space' => we_html_multiIconBox::SPACE_BIG,
					'html' => $DEFAULT_CHARSET
				];
			}

			//AMOUNT COLUMNS IN COCKPIT
			$cockpit_amount_columns = new we_html_select(['name' => 'newconf[cockpit_amount_columns]', 'class' => 'weSelect']);
			for($i = 1; $i <= 10; $i++){
				$cockpit_amount_columns->addOption($i, $i);
			}
			$cockpit_amount_columns->selectOption(get_value('cockpit_amount_columns'));
			$settings[] = ['headline' => g_l('prefs', '[cockpit_amount_columns]'), 'html' => $cockpit_amount_columns->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG];

			/*			 * ***************************************************************
			 * SEEM
			 * *************************************************************** */

			if(we_base_preferences::userIsAllowed('WE_SEEM')){
				// Build maximize window
				$seem_disabler = we_html_forms::checkbox(1, get_value('WE_SEEM') == 0 ? 1 : 0, 'newconf[WE_SEEM]', g_l('prefs', '[seem_deactivate]'));

				// Build dialog if user has permission
				$settings[] = ['headline' => g_l('prefs', '[seem]'), 'html' => $seem_disabler, 'space' => we_html_multiIconBox::SPACE_BIG];
			}

			// SEEM start document
			if(we_base_preferences::userIsAllowed('seem_start_type')){

				// Cockpit
				$document_path = $object_path = '';
				$document_id = $object_id = 0;

				switch(get_value('seem_start_type')){
					default:
						$seem_start_type = 0;
						break;
					case 'cockpit':
						$_SESSION['prefs']['seem_start_file'] = 0;
						$_SESSION['prefs']['seem_start_weapp'] = '';
						$seem_start_type = 'cockpit';
						break;

					// Object
					case 'object':
						$seem_start_type = 'object';
						if(get_value('seem_start_file') != 0){
							$object_id = get_value('seem_start_file');
							$get_object_paths = getPathsFromTable(OBJECT_FILES_TABLE, $GLOBALS['DB_WE'], we_base_constants::FILE_ONLY, $object_id);

							if(isset($get_object_paths[$object_id])){ //	seeMode start file exists
								$object_path = $get_object_paths[$object_id];
							}
						}
						break;
					case 'weapp':
						$seem_start_type = 'weapp';
						if(get_value('seem_start_weapp') != ''){
							$seem_start_weapp = get_value('seem_start_weapp');
						}

						break;
					// Document
					case 'document':
						$seem_start_type = 'document';
						if(get_value('seem_start_file') != 0){
							$document_id = get_value('seem_start_file');
							$get_document_paths = getPathsFromTable(FILE_TABLE, $GLOBALS['DB_WE'], we_base_constants::FILE_ONLY, $document_id);

							if(isset($get_document_paths[$document_id])){ //	seeMode start file exists
								$document_path = $get_document_paths[$document_id];
							}
						}
						break;
				}

				$start_type = new we_html_select(['name' => 'newconf[seem_start_type]', 'class' => 'weSelect', 'id' => 'seem_start_type', 'onchange' => "show_seem_chooser(this.value);"]);

				$showStartType = false;
				$permitedStartTypes = [''];
				$start_type->addOption(0, '-');
				$seem_cockpit_selectordummy = "<div id='selectordummy' style='height:24px;'>&nbsp;</div>";
				if(permissionhandler::hasPerm('CAN_SEE_QUICKSTART')){
					$start_type->addOption('cockpit', g_l('prefs', '[seem_start_type_cockpit]'));
					$showStartType = true;
					$permitedStartTypes[] = 'cockpit';
				}

				$seem_document_chooser = '';
				if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
					$start_type->addOption('document', g_l('prefs', '[seem_start_type_document]'));
					$showStartType = true;
					// Build SEEM select start document chooser

					$weSuggest->setAcId('Doc');
					$weSuggest->setContentType([we_base_ContentTypes::FOLDER, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::HTML, we_base_ContentTypes::IMAGE]);
					$weSuggest->setInput('seem_start_document_name', $document_path, [], get_value('seem_start_file'));
					$weSuggest->setMaxResults(20);
					$weSuggest->setRequired(true);
					$weSuggest->setResult('seem_start_document', $document_id);
					$weSuggest->setSelector(weSuggest::DocSelector);
					$weSuggest->setWidth(150);
					$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:select_seem_start()', '', 0, 0, '', '', false, false), 10);

					$seem_document_chooser = we_html_element::htmlSpan(['id' => 'seem_start_document', 'style' => 'display:none'], $weSuggest->getHTML());
					$permitedStartTypes[] = 'document';
				}
				$seem_object_chooser = '';
				if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
					$start_type->addOption('object', g_l('prefs', '[seem_start_type_object]'));
					$showStartType = true;
					// Build SEEM select start object chooser

					$weSuggest->setAcId('Obj');
					$weSuggest->setContentType('folder,' . we_base_ContentTypes::OBJECT_FILE);
					$weSuggest->setInput('seem_start_object_name', $object_path, [], get_value('seem_start_file'));
					$weSuggest->setMaxResults(20);
					$weSuggest->setRequired(true);
					$weSuggest->setResult('seem_start_object', $object_id);
					$weSuggest->setSelector(weSuggest::DocSelector);
					$weSuggest->setTable(OBJECT_FILES_TABLE);
					$weSuggest->setWidth(150);
					$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:select_seem_start()', '', 0, 0, '', '', false, false), 10);

					$seem_object_chooser = we_html_element::htmlSpan(['id' => 'seem_start_object', 'style' => 'display:none'], $weSuggest->getHTML());
					$permitedStartTypes[] = 'object';
				}
				$start_weapp = new we_html_select(['name' => 'newconf[seem_start_weapp]', 'class' => 'weSelect', 'id' => 'seem_start_weapp']);
				$tools = we_tool_lookup::getAllTools(true, false);
				foreach($tools as $tool){
					if(!$tool['appdisabled'] && permissionhandler::hasPerm($tool['startpermission'])){
						$start_weapp->addOption($tool['name'], $tool['text']);
					}
				}
				$seem_weapp_chooser = '';
				if($start_weapp->getOptionNum()){
					$start_type->addOption('weapp', g_l('prefs', '[seem_start_type_weapp]'));
					if(!empty($seem_start_weapp)){
						$start_weapp->selectOption($seem_start_weapp);
					}
					$weAPPSelector = $start_weapp->getHtml();
					$seem_weapp_chooser = we_html_element::htmlSpan(['id' => 'seem_start_weapp', 'style' => 'display:none'], $weAPPSelector);
					$permitedStartTypes[] = 'weapp';
				}

				// Build final HTML code
				if($showStartType){
					if(in_array($seem_start_type, $permitedStartTypes)){
						$start_type->selectOption($seem_start_type);
					} else {
						$seem_start_type = $permitedStartTypes[0];
					}
					$seem_html = new we_html_table(['class' => 'default'], 2, 1);
					$seem_html->setCol(0, 0, ['class' => 'defaultfont'], $start_type->getHtml());
					$seem_html->setCol(1, 0, ['style' => 'padding-top:5px;'], $seem_cockpit_selectordummy . $seem_document_chooser . $seem_object_chooser . $seem_weapp_chooser);
					$settings[] = ['headline' => g_l('prefs', '[seem_startdocument]'), 'html' => $seem_html->getHtml() . we_html_element::jsElement('show_seem_chooser("' . $seem_start_type . '");'),
						'space' => we_html_multiIconBox::SPACE_BIG];
				}

				// Build dialog if user has permission
			}
			$val = get_value('message_reporting');

			$html = "<input type=\"hidden\" id=\"message_reporting\" name=\"newconf[message_reporting]\" value=\"$val\" />" . we_html_forms::checkbox(we_message_reporting::WE_MESSAGE_ERROR, 1, "message_reporting_errors", g_l('prefs', '[message_reporting][show_errors]'), false, "defaultfont", "handle_message_reporting_click();", true) . "<br />" .
				we_html_forms::checkbox(we_message_reporting::WE_MESSAGE_WARNING, $val & we_message_reporting::WE_MESSAGE_WARNING, "message_reporting_warnings", g_l('prefs', '[message_reporting][show_warnings]'), false, "defaultfont", "handle_message_reporting_click();") . "<br />" .
				we_html_forms::checkbox(we_message_reporting::WE_MESSAGE_NOTICE, $val & we_message_reporting::WE_MESSAGE_NOTICE, "message_reporting_notices", g_l('prefs', '[message_reporting][show_notices]'), false, "defaultfont", "handle_message_reporting_click();");

			$settings[] = ['headline' => g_l('prefs', '[message_reporting][headline]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[message_reporting][information]'), we_html_tools::TYPE_HELP, false, false),
				'html' => $html, 'space' => we_html_multiIconBox::SPACE_BIG];

			/*			 * *******************************************************
			 * Sidebar
			 * ******************************************************* */
			if(we_base_preferences::userIsAllowed('SIDEBAR_DISABLED')){
				// Settings
				$sidebar_disable = get_value('SIDEBAR_DISABLED');
				$sidebar_show = ($sidebar_disable) ? 'none' : 'block';

				$sidebar_id = get_value('SIDEBAR_DEFAULT_DOCUMENT');
				$sidebar_paths = getPathsFromTable(FILE_TABLE, $GLOBALS['DB_WE'], we_base_constants::FILE_ONLY, $sidebar_id);
				$sidebar_path = '';
				if(isset($sidebar_paths[$sidebar_id])){
					$sidebar_path = $sidebar_paths[$sidebar_id];
				}

				// Enable / disable sidebar
				$sidebar_disabler = we_html_forms::checkbox(1, $sidebar_disable, 'newconf[SIDEBAR_DISABLED]', g_l('prefs', '[sidebar_deactivate]'), false, 'defaultfont', "document.getElementById('sidebar_options').style.display=(this.checked?'none':'block');");

				// Show on Startup
				$sidebar_show_on_startup = we_html_forms::checkbox(1, get_value('SIDEBAR_SHOW_ON_STARTUP'), 'newconf[SIDEBAR_SHOW_ON_STARTUP]', g_l('prefs', '[sidebar_show_on_startup]'), false, 'defaultfont', '');

				// Sidebar width
				$sidebar_width = we_html_tools::htmlTextInput('newconf[SIDEBAR_DEFAULT_WIDTH]', 8, get_value('SIDEBAR_DEFAULT_WIDTH'), 255, "onchange=\"if ( isNaN( this.value ) ||  parseInt(this.value) < 100 ) { this.value=100; };\"", 'number', 90);
				$sidebar_width_chooser = we_html_tools::htmlSelect('tmp_sidebar_width', ['' => '', 100 => 100, 150 => 150, 200 => 200, 250 => 250, 300 => 300, 350 => 350, 400 => 400], 1, '', false, [
						'onchange' => "document.forms[0].elements['newconf[SIDEBAR_DEFAULT_WIDTH]'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"], "value", 100, "defaultfont");

				// Sidebar document
				$weSuggest->setAcId('SidebarDoc');
				$weSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT);
				$weSuggest->setInput('ui_sidebar_file_name', $sidebar_path);
				$weSuggest->setMaxResults(20);
				$weSuggest->setResult('newconf[SIDEBAR_DEFAULT_DOCUMENT]', $sidebar_id);
				$weSuggest->setSelector(weSuggest::DocSelector);
				$weSuggest->setWidth(150);
				$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:selectSidebarDoc()'));

				// build html
				$sidebar_html1 = new we_html_table(['class' => 'default'], 1, 1);

				$sidebar_html1->setCol(0, 0, null, $sidebar_disabler);

				// build html
				$sidebar_html2 = new we_html_table(['class' => 'default', 'id' => 'sidebar_options', 'style' => 'display:' . $sidebar_show], 8, 2);

				$sidebar_html2->setCol(0, 0, ['colspan' => 3, 'height' => 10], '');
				$sidebar_html2->setCol(1, 0, ['colspan' => 3, 'height' => 10], $sidebar_show_on_startup);
				$sidebar_html2->setCol(2, 0, ['colspan' => 3, 'height' => 10], '');
				$sidebar_html2->setCol(3, 0, ['colspan' => 3, 'class' => 'defaultfont'], g_l('prefs', '[sidebar_width]'));
				$sidebar_html2->setCol(4, 0, null, $sidebar_width);
				$sidebar_html2->setCol(4, 1, ['style' => 'padding-left:10px;'], $sidebar_width_chooser);
				$sidebar_html2->setCol(5, 0, ['colspan' => 3, 'height' => 10], '');
				$sidebar_html2->setCol(6, 0, ['colspan' => 3, 'class' => 'defaultfont'], g_l('prefs', '[sidebar_document]'));
				$sidebar_html2->setCol(7, 0, ['colspan' => 3], $weSuggest->getHTML());

				// Build dialog if user has permission
				$settings[] = ['headline' => g_l('prefs', '[sidebar]'), 'html' => $sidebar_html1->getHtml() . $sidebar_html2->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG];
			}


			// TREE

			$tree_count = get_value('default_tree_count');
			$file_tree_count = new we_html_select(['name' => 'newconf[default_tree_count]', 'class' => 'weSelect']);
			$file_tree_count->addOption(0, g_l('prefs', '[all]'));


			for($i = 10; $i < 51; $i += 10){
				$file_tree_count->addOption($i, $i);
			}

			for($i = 100; $i < 501; $i += 100){
				$file_tree_count->addOption($i, $i);
			}

			if(!$file_tree_count->selectOption($tree_count)){
				$file_tree_count->addOption($tree_count, $tree_count);
				// Set selected extension
				$file_tree_count->selectOption($tree_count);
			}

			$settings[] = ['headline' => g_l('prefs', '[tree_title]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[tree_count_description]'), we_html_tools::TYPE_HELP),
				'html' => $file_tree_count->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG];


			//WINDOW DIMENSIONS

			if(get_value('sizeOpt') == 0){
				$window_specify = false;
				$window_max = true;
			} else {
				$window_specify = true;
				$window_max = false;
			}

			// Build maximize window
			$window_max_code = we_html_forms::radiobutton(0, get_value('sizeOpt') == 0, 'newconf[sizeOpt]', g_l('prefs', '[maximize]'), true, 'defaultfont', "document.getElementsByName('newconf[weWidth]')[0].disabled = true;document.getElementsByName('newconf[weHeight]')[0].disabled = true;");

			// Build specify window dimension
			$window_specify_code = we_html_forms::radiobutton(1, !(get_value('sizeOpt') == 0), 'newconf[sizeOpt]', g_l('prefs', '[specify]'), true, 'defaultfont', "document.getElementsByName('newconf[weWidth]')[0].disabled = false;document.getElementsByName('newconf[weHeight]')[0].disabled = false;");

			// Create specify window dimension input
			$window_specify_table = new we_html_table(['class' => 'default', 'style' => 'margin-top:10px;margin-left:50px;'], 2, 2);

			$window_specify_table->setCol(0, 0, ['class' => 'defaultfont'], g_l('prefs', '[width]') . ':');
			$window_specify_table->setCol(1, 0, ['class' => 'defaultfont'], g_l('prefs', '[height]') . ':');

			$window_specify_table->setCol(0, 1, null, we_html_tools::htmlTextInput('newconf[weWidth]', 6, (get_value('sizeOpt') ? get_value('weWidth') : ''), 4, (get_value('sizeOpt') == 0 ? 'disabled="disabled"' : ""), "number", 60));
			$window_specify_table->setCol(1, 1, null, we_html_tools::htmlTextInput('newconf[weHeight]', 6, (get_value('sizeOpt') ? get_value('weHeight') : ''), 4, (get_value('sizeOpt') == 0 ? 'disabled="disabled"' : ""), "number", 60));

			// Build apply current window dimension
			$window_current_dimension_table = we_html_button::create_button('apply_current_dimension', "javascript:document.getElementsByName('newconf[sizeOpt]')[1].checked = true;document.getElementsByName('newconf[weWidth]')[0].disabled = false;document.getElementsByName('newconf[weHeight]')[0].disabled = false;document.getElementsByName('newconf[weWidth]')[0].value = parent.opener.top.window.outerWidth;document.getElementsByName('newconf[weHeight]')[0].value = parent.opener.top.window.outerHeight;");

			// Build final HTML code
			$window_html = new we_html_table(['class' => 'default withSpace'], 3, 1);
			$window_html->setCol(0, 0, ['style' => 'padding-bttom:10px;'], $window_max_code);
			$window_html->setCol(1, 0, ['style' => 'padding-bttom:10px;'], $window_specify_code . $window_specify_table->getHtml());
			$window_html->setCol(2, 0, ['style' => 'padding-left:50px;'], $window_current_dimension_table);

			// Build dialog
			$settings[] = ['headline' => g_l('prefs', '[dimension]'), 'html' => $window_html->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG];
			return create_dialog('', $settings, -1);

		case 'defaultAttribs':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			/**
			 * inlineedit setting
			 */
			// Build select box

			$commands_default_tmp = we_html_tools::htmlSelect('tmp_commands', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, ['onchange' => "var elem=document.getElementById('commands_default_id'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"]);
			$COMMANDS_DEFAULT = we_html_tools::htmlTextInput('newconf[COMMANDS_DEFAULT]', 22, get_value('COMMANDS_DEFAULT'), '', 'id="commands_default_id"', 'text', 225, 0, '');

			$CSSAPPLYTO_DEFAULT = new we_html_select(['name' => 'newconf[CSSAPPLYTO_DEFAULT]', 'class' => 'weSelect']);
			$CSSAPPLYTO_DEFAULT->addOption('all', 'all');
			$CSSAPPLYTO_DEFAULT->addOption('around', 'around');
			$CSSAPPLYTO_DEFAULT->addOption('wysiwyg', 'wysiwyg');
			$CSSAPPLYTO_DEFAULT->selectOption(get_value('CSSAPPLYTO_DEFAULT') ?: 'around');

			$weSuggest->setAcId("doc2");
			$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$weSuggest->setInput('imagestartid_default_text', (IMAGESTARTID_DEFAULT ? id_to_path(IMAGESTARTID_DEFAULT) : ''));
			$weSuggest->setMaxResults(20);
			$weSuggest->setResult('newconf[IMAGESTARTID_DEFAULT]', (IMAGESTARTID_DEFAULT ?: 0));
			$weSuggest->setSelector(weSuggest::DirSelector);
			$weSuggest->setWidth(226);
			$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory', document.forms[0].elements['newconf[IMAGESTARTID_DEFAULT]'].value, '" . FILE_TABLE . "', 'newconf[IMAGESTARTID_DEFAULT]','imagestartid_default_text')"));
			$weSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.forms[0].elements['newconf[IMAGESTARTID_DEFAULT]'].value = 0;document.forms[0].elements.imagestartid_default_text.value=''"));

			$settings = [
				['headline' => g_l('prefs', '[default_php_setting]'), 'html' => getTrueFalseSelect('WE_PHP_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[xhtml_default]'), 'html' => getTrueFalseSelect('XHTML_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[inlineedit_default]'), 'html' => getTrueFalseSelect('INLINEEDIT_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[imagestartid_default]'), 'html' => $weSuggest->getHTML(), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[commands_default]'), 'html' => '<div>' . $commands_default_tmp . '</div><div style="margin-top:4px">' . $COMMANDS_DEFAULT . '</div>',
					'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[removefirstparagraph_default]'), 'html' => getTrueFalseSelect('REMOVEFIRSTPARAGRAPH_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[showinputs_default]'), 'html' => getTrueFalseSelect('SHOWINPUTS_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[hidenameattribinweimg_default]'), 'html' => getYesNoSelect('HIDENAMEATTRIBINWEIMG_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[hidenameattribinweform_default]'), 'html' => getYesNoSelect('HIDENAMEATTRIBINWEFORM_DEFAULT'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[replaceacronym]'), 'html' => getYesNoSelect('REPLACEACRONYM'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[cssapplyto_default]'), 'html' => $CSSAPPLYTO_DEFAULT->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG],
			];

			return create_dialog('', $settings, -1);

		case 'countries':
			if(!we_base_preferences::userIsAllowed('WE_COUNTRIES_DEFAULT')){
				break;
			}

			$countries_default = we_html_tools::htmlTextInput('newconf[WE_COUNTRIES_DEFAULT]', 22, get_value('WE_COUNTRIES_DEFAULT'), '', '', 'text', 225);

			$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
			$langcode = array_search($lang[0], getWELangs());
			$countrycode = array_search($langcode, getWECountries());
			$supported = we_base_country::getTranslationList(we_base_country::TERRITORY, $langcode);
			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
			asort($supported, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);
			$countries_top = explode(',', get_value('WE_COUNTRIES_TOP'));
			$countries_shown = explode(',', get_value('WE_COUNTRIES_SHOWN'));
			$tabC = new we_html_table(['style' => 'border:1px solid black'], 1, 4);
			$i = 0;
			$tabC->setCol($i, 0, ['class' => 'defaultfont bold'], g_l('prefs', '[countries_country]'));
			$tabC->setCol($i, 1, ['class' => 'defaultfont bold'], g_l('prefs', '[countries_top]'));
			$tabC->setCol($i, 2, ['class' => 'defaultfont bold'], g_l('prefs', '[countries_show]'));
			$tabC->setCol($i, 3, ['class' => 'defaultfont bold'], g_l('prefs', '[countries_noshow]'));
			foreach($supported as $countrycode => $country){
				$i++;
				$tabC->addRow();
				$tabC->setCol($i, 0, ['class' => 'defaultfont'], CheckAndConvertISObackend($country));
				$tabC->setCol($i, 1, ['class' => 'defaultfont'], '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="2" ' . (in_array($countrycode, $countries_top) ? 'checked="checked"' : '') . ' > ');
				$tabC->setCol($i, 2, ['class' => 'defaultfont'], '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="1" ' . (in_array($countrycode, $countries_shown) ? 'checked="checked"' : '') . ' > ');
				$tabC->setCol($i, 3, ['class' => 'defaultfont'], '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="0" ' . (!in_array($countrycode, $countries_top) && !in_array($countrycode, $countries_shown) ? 'checked' : '') . ' > ');
			}

			$settings = [
				['headline' => g_l('prefs', '[countries_headline]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[countries_information]'), we_html_tools::TYPE_INFO, 450, false),
					'noline' => 1],
				['headline' => g_l('prefs', '[countries_default]'), 'html' => $countries_default, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				['headline' => '', 'html' => $tabC->getHtml(), 'noline' => 1],
			];
			// Build dialog element if user has permission
			return create_dialog('', $settings);

		case 'language':
			if(!we_base_preferences::userIsAllowed('locale_default') && we_base_preferences::userIsAllowed('locale_locales')){
				break;
			}
			$default = get_value('locale_default');
			$locales = get_value('locale_locales');


			$postJs = we_html_element::jsElement('initLocale("' . $default . '");');

			$hidden_fields = we_html_element::htmlHidden('newconf[locale_default]', $default, 'locale_default') .
				we_html_element::htmlHidden('newconf[locale_locales]', implode(',', array_keys($locales)), 'locale_locales');

			//Locales
			$select_box = new we_html_select(['class' => 'weSelect', 'name' => 'locale_temp_locales', 'size' => 10, 'id' => 'locale_temp_locales', 'style' => 'width: 340px']);
			$select_box->addOptions($locales);

			$enabled_buttons = (count($locales) > 0);


			// Create edit list
			$editlist_table = new we_html_table(['class' => 'default'], 1, 2);

			// Buttons
			$default = we_html_button::create_button('default', 'javascript:defaultLocale()', '', 0, 0, '', '', !$enabled_buttons);
			$delete = we_html_button::create_button(we_html_button::DELETE, 'javascript:deleteLocale()');

			$editlist_table->setCol(0, 0, ['style' => 'padding-right:10px;'], $hidden_fields . $select_box->getHtml());
			$editlist_table->setCol(0, 1, ['style' => 'vertical-align:top'], $default . $delete);

			// Add Locales
			// Languages
			$Languages = g_l('languages', '');
			$TopLanguages = [
				'~de' => $Languages['de'],
				'~nl' => $Languages['nl'],
				'~en' => $Languages['en'],
				'~fi' => $Languages['fi'],
				'~fr' => $Languages['fr'],
				'~pl' => $Languages['pl'],
				'~ru' => $Languages['ru'],
				'~es' => $Languages['es'],
			];
			asort($Languages);
			asort($TopLanguages);
			$TopLanguages[''] = '---';
			$Languages = array_merge($TopLanguages, $Languages);

			$languages = new we_html_select(['name' => 'newconf[locale_language]', 'id' => 'locale_language', 'style' => 'width: 139px', 'class' => 'weSelect']);
			$languages->addOptions($Languages);

			// Countries
			$Countries = g_l('countries', '');
			$TopCountries = [
				'~DE' => $Countries['DE'],
				'~CH' => $Countries['CH'],
				'~AT' => $Countries['AT'],
				'~NL' => $Countries['NL'],
				'~GB' => $Countries['GB'],
				'~US' => $Countries['US'],
				'~FI' => $Countries['FI'],
				'~FR' => $Countries['FR'],
				'~PL' => $Countries['PL'],
				'~RU' => $Countries['RU'],
				'~ES' => $Countries['ES'],
			];
			asort($Countries);
			asort($TopCountries);
			$TopCountries['~'] = '---';
			$Countries = array_merge(['' => ''], $TopCountries, $Countries);

			$countries = new we_html_select(['name' => 'newconf[locale_country]', 'id' => 'locale_country', 'style' => 'width: 139px', 'class' => 'weSelect']);
			$countries->addOptions($Countries);

			// Button
			$add_button = we_html_button::create_button(we_html_button::ADD, 'javascript:addLocale()');

			// Build final HTML code
			$add_html = g_l('prefs', '[locale_languages]') . '<br />' .
				$languages->getHtml() . '<br /><br />' .
				g_l('prefs', '[locale_countries]') . '<br />' .
				$countries->getHtml() . '<br /><br />' .
				$add_button;

			//Todo: remove: g_l('prefs', '[langlink_support_backlinks_information]'), g_l('prefs', '[langlink_support_backlinks]'),g_l('prefs', '[langlink_support_recursive_information]'),g_l('prefs', '[langlink_support_recursive]') g_l('prefs', '[langlink_abandoned_options]')
			$settings = [
				['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[locale_information]'), we_html_tools::TYPE_INFO, 450, false),],
				['headline' => '', 'html' => $editlist_table->getHtml(),],
				['headline' => g_l('prefs', '[locale_add]'), 'html' => $add_html, 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[langlink_headline]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[langlink_information]'), we_html_tools::TYPE_INFO, 450, false),
					'noline' => 1],
				['headline' => g_l('prefs', '[langlink_support]'), 'html' => getTrueFalseSelect('LANGLINK_SUPPORT'), 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
			];

			return create_dialog('', $settings) . $postJs;

		case 'editor':
			//EDITOR PLUGIN

			$attr = ' class="defaultfont" style="width:150px;"';
			//$attr_dis = ' class="defaultfont" style="width:150px;color:grey;"';

			$template_editor_mode = new we_html_select(['class' => 'weSelect', 'name' => 'newconf[editorMode]', 'onchange' => 'displayEditorOptions(this.options[this.options.selectedIndex].value);']);
			$template_editor_mode->addOption('textarea', g_l('prefs', '[editor_plaintext]'));
			$template_editor_mode->addOption('codemirror2', g_l('prefs', '[editor_javascript2]'));
			//$template_editor_mode->addOption('java', g_l('prefs', '[editor_java]'));
			$template_editor_mode->selectOption(get_value('editorMode'));

			/**
			 * Editor font settings
			 */
			$template_fonts = ['Arial',
				'Andale Mono',
				'Consolas',
				'Courier New',
				'DejaVu Sans Mono',
				'Droid Sans Mono',
				'FreeMono',
				'Helvetica',
				'Inconsolata',
				'Letter Gothic',
				'Liberation Mono',
				'Menlo',
				'Monaco',
				'Mono',
				'Nimbus Mono L',
				'Tahoma',
				'Verdana',
				'serif',
				'sans-serif'];
			$template_font_sizes = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 22, 24, 32, 48, 72];

			$template_editor_font_specify = (get_value('editorFontname') != '' && get_value('editorFontname') != 'none');
			$template_editor_font_size_specify = (get_value('editorFontsize') != '' && get_value('editorFontsize') != -1);

			// Build specify font
			$template_editor_font_specify_code = we_html_forms::checkbox(1, $template_editor_font_specify, 'newconf[editorFont]', g_l('prefs', '[specify]'), true, 'defaultfont', "if (document.getElementsByName('newconf[editorFont]')[0].checked) { document.getElementsByName('newconf[editorFontname]')[0].disabled = false;document.getElementsByName('newconf[editorFontsize]')[0].disabled = false; } else { document.getElementsByName('newconf[editorFontname]')[0].disabled = true;document.getElementsByName('newconf[editorFontsize]')[0].disabled = true; }");

			$template_editor_font_select_box = new we_html_select(['class' => 'weSelect', 'name' => 'newconf[editorFontname]', 'style' => 'width: 135px;', ($template_editor_font_specify ? 'enabled' : 'disabled') => ($template_editor_font_specify ? 'enabled' : 'disabled')]);

			/* 			$colorsDisabled = true;

			  $template_editor_fontcolor_selector = getColorInput('newconf[editorFontcolor]', get_value('editorFontcolor'), $colorsDisabled);
			  $template_editor_we_tag_fontcolor_selector = getColorInput('newconf[editorWeTagFontcolor]', get_value('editorWeTagFontcolor'), $colorsDisabled);
			  $template_editor_we_attribute_fontcolor_selector = getColorInput('newconf[editorWeAttributeFontcolor]', get_value('editorWeAttributeFontcolor'), $colorsDisabled);
			  $template_editor_html_tag_fontcolor_selector = getColorInput('newconf[editorHTMLTagFontcolor]', get_value('editorHTMLTagFontcolor'), $colorsDisabled);
			  $template_editor_html_attribute_fontcolor_selector = getColorInput('newconf[editorHTMLAttributeFontcolor]', get_value('editorHTMLAttributeFontcolor'), $colorsDisabled);
			  $template_editor_pi_tag_fontcolor_selector = getColorInput('newconf[editorPiTagFontcolor]', get_value('editorPiTagFontcolor'), $colorsDisabled);
			  $template_editor_comment_fontcolor_selector = getColorInput('newconf[editorCommentFontcolor]', get_value('editorCommentFontcolor'), $colorsDisabled);
			 */
			foreach($template_fonts as $font){
				$template_editor_font_select_box->addOption($font, $font);
			}
			$template_editor_font_select_box->selectOption($template_editor_font_specify ? get_value('editorFontname') : 'Courier New');

			$template_editor_font_sizes_select_box = new we_html_select(['class' => 'weSelect', 'name' => 'newconf[editorFontsize]', 'style' => 'width: 135px;', ($template_editor_font_size_specify ? 'enabled' : 'disabled') => ($template_editor_font_size_specify ? 'enabled' : 'disabled')]);
			foreach($template_font_sizes as $key => $sz){
				$template_editor_font_sizes_select_box->addOption($sz, $sz);
			}
			$template_editor_font_sizes_select_box->selectOption($template_editor_font_specify ? $template_font_sizes[$key] : 11);

			$template_editor_font_sizes_select_box->selectOption(get_value('editorFontsize'));


			$template_editor_font_specify_table = '<table style="margin:0px 0px 20px 50px;" class="default">
	<tr><td' . $attr . '>' . g_l('prefs', '[editor_fontname]') . '</td><td>' . $template_editor_font_select_box->getHtml() . '</td></tr>
	<tr><td' . $attr . '>' . g_l('prefs', '[editor_fontsize]') . '</td><td>' . $template_editor_font_sizes_select_box->getHtml() . '</td></tr>
</table>';
			/*
			  $template_editor_font_color_checkbox = we_html_forms::checkboxWithHidden(get_value('specify_jeditor_colors'), "newconf[specify_jeditor_colors]", g_l('prefs', '[editor_font_colors]'), false, "defaultfont", "setEditorColorsDisabled(!this.checked);");
			  $attr = ($colorsDisabled ? $attr_dis : $attr);
			  $template_editor_font_color_table = '<table id="editorColorTable" style="margin: 10px 0px 0px 50px;" class="default">
			  <tr><td id="label_editorFontcolor" ' . $attr . '>' . g_l('prefs', '[editor_normal_font_color]') . '</td><td>' . $template_editor_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorWeTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_we_tag_font_color]') . '</td><td>' . $template_editor_we_tag_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorWeAttributeFontcolor"' . $attr . '>' . g_l('prefs', '[editor_we_attribute_font_color]') . '</td><td>' . $template_editor_we_attribute_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorHTMLTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_html_tag_font_color]') . '</td><td>' . $template_editor_html_tag_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorHTMLAttributeFontcolor"' . $attr . '>' . g_l('prefs', '[editor_html_attribute_font_color]') . '</td><td>' . $template_editor_html_attribute_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorPiTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_pi_tag_font_color]') . '</td><td>' . $template_editor_pi_tag_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorCommentFontcolor"' . $attr . '>' . g_l('prefs', '[editor_comment_font_color]') . '</td><td>' . $template_editor_comment_fontcolor_selector . '</td></tr>
			  </table>';
			 */
			$template_editor_theme = new we_html_select(['class' => 'weSelect', 'name' => 'newconf[editorTheme]']);
			foreach(glob(WE_LIB_PATH . 'additional/CodeMirror/theme/*.css') as $filename){
				$theme = str_replace(['.css', WE_LIB_PATH . 'additional/CodeMirror/theme/'], '', $filename);
				$template_editor_theme->addOption($theme, $theme);
			}
			$template_editor_theme->selectOption(get_value('editorTheme'));

			//Build activation of line numbers
			$template_editor_linenumbers_code = we_html_forms::checkbox(1, get_value('editorLinenumbers'), 'newconf[editorLinenumbers]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');
			$template_editor_highlightLine_code = we_html_forms::checkbox(1, get_value('editorHighlightCurrentLine'), 'newconf[editorHighlightCurrentLine]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');

			//Build activation of code completion
			$template_editor_codecompletion_code = we_html_element::htmlHiddens([
					'newconf[editorCodecompletion][WE]' => get_value('editorCodecompletion-WE'),
					'newconf[editorCodecompletion][htmlTag]' => get_value('editorCodecompletion-htmlTag'),
					'newconf[editorCodecompletion][htmlDefAttr]' => get_value('editorCodecompletion-htmlDefAttr'),
					'newconf[editorCodecompletion][htmlAttr]' => get_value('editorCodecompletion-htmlAttr'),
					'newconf[editorCodecompletion][htmlJSAttr]' => get_value('editorCodecompletion-htmlJSAttr'),
					'newconf[editorCodecompletion][html5Tag]' => get_value('editorCodecompletion-html5Tag'),
					'newconf[editorCodecompletion][html5Attr]' => get_value('editorCodecompletion-html5Attr')
				]) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-WE'), 'editorCodecompletion0', 'WE-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][WE]\');') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlTag'), 'editorCodecompletion1', 'HTML-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlTag]\');') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlDefAttr'), 'editorCodecompletion2', 'HTML-Default-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlDefAttr]\');') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlAttr'), 'editorCodecompletion3', 'HTML-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlAttr]\');') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlJSAttr'), 'editorCodecompletion4', 'HTML-JS-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlJSAttr]\');') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-html5Tag'), 'editorCodecompletion5', 'HTML5-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][html5Tag]\');') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-html5Attr'), 'editorCodecompletion6', 'HTML5-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][html5Attr]\');');


			$template_editor_tabstop_code = '<table class="default">
				<tr><td colspan="2">' .
				we_html_forms::checkbox(1, get_value('editorShowTab'), 'editorShowTab', g_l('prefs', '[editor_tabstop]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorShowTab]\');') .
				we_html_element::htmlHidden('newconf[editorShowTab]', get_value('editorShowTab')) .
				'</td></tr>
				<tr><td class="defaultfont" style="width:200px;">' . g_l('prefs', '[editor_tabSize]') . '</td><td>' . we_html_tools::htmlTextInput("newconf[editorTabSize]", 2, get_value("editorTabSize"), 2, "", "number", 135) . '</td></tr>
				<tr><td colspan="2">' .
				we_html_forms::checkbox(1, get_value('editorAutoIndent'), 'editorAutoIndent', g_l('prefs', '[editor_autoindent]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorAutoIndent]\');') .
				we_html_element::htmlHidden('newconf[editorAutoIndent]', get_value('editorAutoIndent')) .
				'</td></tr>
				<tr><td colspan="2">' .
				we_html_forms::checkbox(1, get_value('editorIndentSpaces'), 'editorIndentSpaces', g_l('prefs', '[editor][indentSpaces]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorIndentSpaces]\');') .
				we_html_element::htmlHidden('newconf[editorIndentSpaces]', get_value('editorIndentSpaces')) .
				'</td></tr>
				<tr><td colspan="2">' .
				we_html_forms::checkbox(1, get_value('editorShowSpaces'), 'editorShowSpaces', g_l('prefs', '[editor][showSpaces]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorShowSpaces]\');') .
				we_html_element::htmlHidden('newconf[editorShowSpaces]', get_value('editorShowSpaces')) .
				'</td></tr>
			</table>';

			$template_editor_Wrap_code = we_html_forms::checkbox(1, get_value('editorWrap'), 'editorWrap', g_l('prefs', '[editor_enable]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorWrap]\');') .
				we_html_element::htmlHidden('newconf[editorWrap]', get_value('editorWrap'));


			$template_editor_tooltips_code = we_html_forms::checkbox(1, get_value('editorTooltips'), 'newconf[editorTooltips]', g_l('prefs', '[editorTooltips]'), true, 'defaultfont', '') .
				'<br/>' .
				we_html_forms::checkbox(1, get_value('editorTooltipsIDs'), 'newconf[editorTooltipsIDs]', g_l('prefs', '[editorTooltipsIDs]'), true, 'defaultfont', '');

			$template_editor_tooltip_font_specify = (get_value('editorTooltipFontname') != '' && get_value('editorTooltipFontname') != 'none');
			$template_editor_tooltip_font_size_specify = (get_value('editorTooltipFontsize') != '' && get_value('editorTooltipFontsize') != -1);

			// Build specify font
			$template_editor_tooltip_font_specify_code = we_html_forms::checkbox(1, $template_editor_tooltip_font_specify, 'newconf[editorTooltipFont]', g_l('prefs', '[specify]'), true, 'defaultfont', 'if (document.getElementsByName(\'newconf[editorTooltipFont]\')[0].checked) { document.getElementsByName(\'newconf[editorTooltipFontname]\')[0].disabled = false;document.getElementsByName(\'newconf[editorTooltipFontsize]\')[0].disabled = false; } else { document.getElementsByName(\'newconf[editorTooltipFontname]\')[0].disabled = true;document.getElementsByName(\'newconf[editorTooltipFontsize]\')[0].disabled = true; }');

			$template_editor_tooltip_font_select_box = new we_html_select(['class' => 'weSelect', 'name' => 'newconf[editorTooltipFontname]', 'style' => 'width: 135px;',
				($template_editor_tooltip_font_specify ? 'enabled' : 'disabled') => ($template_editor_tooltip_font_specify ? 'enabled' : 'disabled')]);

			foreach($template_fonts as $font){
				$template_editor_tooltip_font_select_box->addOption($font, $font);
			}
			$template_editor_tooltip_font_select_box->selectOption($template_editor_tooltip_font_specify ? get_value('editorTooltipFontname') : 'Tahoma');

			$template_editor_tooltip_font_sizes_select_box = new we_html_select(['class' => 'weSelect editor editor_codemirror2', 'name' => 'newconf[editorTooltipFontsize]',
				'style' => 'width: 135px;', ($template_editor_tooltip_font_size_specify ? 'enabled' : 'disabled') => ($template_editor_tooltip_font_size_specify ? 'enabled' : 'disabled')]);
			$template_toolfont_sizes = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
			foreach($template_toolfont_sizes as $sz){
				$template_editor_tooltip_font_sizes_select_box->addOption($sz, $sz);
			}
			$template_editor_tooltip_font_sizes_select_box->selectOption($template_editor_tooltip_font_specify ? get_value("editor_tooltip_font_size") : 11);
			$template_editor_tooltip_font_specify_table = '<table style="margin:0px 0px 20px 50px;" class="default">
				<tr><td' . $attr . '>' . g_l('prefs', '[editor_fontname]') . '</td><td>' . $template_editor_tooltip_font_select_box->getHtml() . '</td></tr>
				<tr><td' . $attr . '>' . g_l('prefs', '[editor_fontsize]') . '</td><td>' . $template_editor_tooltip_font_sizes_select_box->getHtml() . '</td></tr>
			</table>';

			//Build activation of integration of documentation
			$template_editor_autoClose = we_html_forms::checkbox(1, get_value('editorDocuintegration'), 'newconf[editorDocuintegration]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '') .
//remove fonts not available
				we_html_element::jsScript(LIB_DIR . 'additional/fontdetect/fontdetect.js', 'checkFonts()');
			$settings = [
				['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[editor_information]'), we_html_tools::TYPE_INFO, 480, false),],
				['headline' => g_l('prefs', '[editor_mode]'), 'html' => $template_editor_mode->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('prefs', '[editor_font]'), 'html' => $template_editor_font_specify_code . $template_editor_font_specify_table,
					'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_theme]'), 'html' => $template_editor_theme->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2],
//				array('class' => 'editor editor_java', 'headline' => g_l('prefs', '[editor_highlight_colors]'), 'html' => $template_editor_font_color_checkbox . $template_editor_font_color_table, 'space' => we_html_multiIconBox::SPACE_MED2),
				['class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_linenumbers]'), 'html' => $template_editor_linenumbers_code, 'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_highlightLine]'), 'html' => $template_editor_highlightLine_code, 'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('global', '[wrapcheck]'), 'html' => $template_editor_Wrap_code, 'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('prefs', '	[editor][indent]'), 'html' => $template_editor_tabstop_code, 'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_completion]'), 'html' => $template_editor_codecompletion_code, 'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_tooltips]'), 'html' => $template_editor_tooltips_code . '<br/>' . $template_editor_tooltip_font_specify_code . '<br/>' . $template_editor_tooltip_font_specify_table,
					'space' => we_html_multiIconBox::SPACE_MED2],
				['class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_autoCloseTags]'), 'html' => $template_editor_autoClose, 'space' => we_html_multiIconBox::SPACE_MED2],
				//array('class'=>'editor editor_codemirror2','headline' => g_l('prefs', '[editor_docuclick]'), 'html' => $template_editor_docuintegration_code, 'space' => we_html_multiIconBox::SPACE_MED2),
			];

			return create_dialog("settings_editor_predefined", $settings, count($settings), g_l('prefs', '[show_predefined]'), g_l('prefs', '[hide_predefined]'));

		case "recipients":
			if(!we_base_preferences::userIsAllowed('FORMMAIL_VIAWEDOC')){
				break;
			}
			$settings = [];
			//FORMMAIL RECIPIENTS
			if(we_base_preferences::userIsAllowed('FORMMAIL_BLOCK')){
				// Build dialog if user has permission
				$settings[] = ["headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[formmail_information]'), we_html_tools::TYPE_INFO, 450, false),];

				/**
				 * Recipients list
				 */
				$select_box = new we_html_select(['class' => "weSelect", "name" => "we_recipient", 'size' => 10, 'style' => "width: 340px;height:100px", "ondblclick" => "edit_recipient();"]);

				$enabled_buttons = false;

				$DB_WE->query('SELECT ID, Email FROM ' . RECIPIENTS_TABLE . ' ORDER BY Email');

				while($DB_WE->next_record()){
					$enabled_buttons = true;
					$select_box->addOption($DB_WE->f("ID"), $DB_WE->f("Email"));
				}

				// Create needed hidden fields
				$hidden_fields = we_html_element::htmlHiddens(["newconf[formmail_values]" => "",
						"newconf[formmail_deleted]" => ""]);

				// Create edit list
				$editlist_table = new we_html_table(['class' => 'default'], 2, 3);

				$editlist_table->setCol(0, 0, ['style' => 'padding-right:10px;'], $hidden_fields . $select_box->getHtml());
				$editlist_table->setCol(0, 2, ['style' => 'vertical-align:top;'], we_html_button::create_button(we_html_button::ADD, "javascript:add_recipient();") . we_html_button::create_button(we_html_button::EDIT, "javascript:edit_recipient();", '', 0, 0, "", "", !$enabled_buttons, false) . we_html_button::create_button(we_html_button::DELETE, "javascript:delete_recipient();", '', 0, 0, "", "", !$enabled_buttons, false));

				// Build dialog if user has permission
				$settings[] = ["headline" => "", "html" => $editlist_table->getHtml(),];
			}

			// formmail stuff


			if(we_base_preferences::userIsAllowed("FORMMAIL_CONFIRM")){
				$formmail_confirm = new we_html_select(['name' => "newconf[FORMMAIL_CONFIRM]", 'style' => "width:88px;", "class" => "weSelect"]);
				$formmail_confirm->addOption(1, g_l('prefs', '[on]'));
				$formmail_confirm->addOption(0, g_l('prefs', '[off]'));
				$formmail_confirm->selectOption(get_value("FORMMAIL_CONFIRM") ? 1 : 0);

				$settings[] = ['html' => $formmail_confirm->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[formmailConfirm]')];

				$formmail_log = new we_html_select(['name' => "newconf[FORMMAIL_LOG]", "onchange" => "formmailLogOnOff()", 'style' => "width:88px;", "class" => "weSelect"]);
				$formmail_log->addOption(1, g_l('prefs', '[yes]'));
				$formmail_log->addOption(0, g_l('prefs', '[no]'));
				$formmail_log->selectOption(get_value("FORMMAIL_LOG") ? 1 : 0);

				$html = '<table class="default">
							<tr>
								<td>' . $formmail_log->getHtml() . '</td>
								<td style="padding-left:10px;">' . we_html_button::create_button('logbook', "javascript:we_cmd('show_formmail_log')") . '</td>
							</tr>
						</table>';
				$settings[] = ['html' => $html, 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[logFormmailRequests]'), 'noline' => 1];

				$isDisabled = (get_value("FORMMAIL_LOG") == 0);


				$formmail_emptylog = new we_html_select(['name' => "newconf[FORMMAIL_EMPTYLOG]", 'style' => "width:88px;", "class" => "weSelect"]);
				if($isDisabled){
					$formmail_emptylog->setAttribute("disabled", "disabled");
				}
				$formmail_emptylog->addOption(-1, g_l('prefs', '[never]'));
				$formmail_emptylog->addOption(86400, g_l('prefs', '[1_day]'));
				$formmail_emptylog->addOption(172800, sprintf(g_l('prefs', '[more_days]'), 2));
				$formmail_emptylog->addOption(345600, sprintf(g_l('prefs', '[more_days]'), 4));
				$formmail_emptylog->addOption(604800, g_l('prefs', '[1_week]'));
				$formmail_emptylog->addOption(1209600, sprintf(g_l('prefs', '[more_weeks]'), 2));
				$formmail_emptylog->addOption(2419200, sprintf(g_l('prefs', '[more_weeks]'), 4));
				$formmail_emptylog->addOption(4838400, sprintf(g_l('prefs', '[more_weeks]'), 8));
				$formmail_emptylog->addOption(9676800, sprintf(g_l('prefs', '[more_weeks]'), 16));
				$formmail_emptylog->addOption(19353600, sprintf(g_l('prefs', '[more_weeks]'), 32));

				$formmail_emptylog->selectOption(get_value("FORMMAIL_EMPTYLOG"));


				$settings[] = ['html' => $formmail_emptylog->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[deleteEntriesOlder]')];

				// formmail only via we doc //
				$formmail_ViaWeDoc = new we_html_select(['name' => "newconf[FORMMAIL_VIAWEDOC]", 'style' => "width:88px;", "class" => "weSelect"]);
				$formmail_ViaWeDoc->addOption(1, g_l('prefs', '[yes]'));
				$formmail_ViaWeDoc->addOption(0, g_l('prefs', '[no]'));
				$formmail_ViaWeDoc->selectOption((get_value("FORMMAIL_VIAWEDOC") ? 1 : 0));

				$settings[] = ['html' => $formmail_ViaWeDoc->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[formmailViaWeDoc]')];

				// limit formmail requests //
				$formmail_block = new we_html_select(['name' => "newconf[FORMMAIL_BLOCK]", "onchange" => "formmailBlockOnOff()", 'style' => "width:88px;", "class" => "weSelect"]);
				if($isDisabled){
					$formmail_block->setAttribute("disabled", "disabled");
				}
				$formmail_block->addOption(1, g_l('prefs', '[yes]'));
				$formmail_block->addOption(0, g_l('prefs', '[no]'));
				$formmail_block->selectOption(get_value("FORMMAIL_BLOCK") ? 1 : 0);

				$html = '<table class="default">
							<tr>
								<td>' . $formmail_block->getHtml() . '</td>
								<td style="padding-left:10px;">' . we_html_button::create_button('logbook', "javascript:we_cmd('show_formmail_block_log')") . '</td>
							</tr>
						</table>';

				$settings[] = ['html' => $html, 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[blockFormmail]'), 'noline' => 1];

				$isDisabled = $isDisabled || (get_value("FORMMAIL_BLOCK") == 0);

				// table is IE fix. Without table IE has a gap on the left of the input
				$formmail_trials = '<table class="default"><tr><td>' .
					we_html_tools::htmlTextInput("newconf[FORMMAIL_TRIALS]", 24, get_value("FORMMAIL_TRIALS"), "", "", "text", 88, 0, "", $isDisabled) .
					'</td></tr></table>';

				$settings[] = ['html' => $formmail_trials, 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[formmailTrials]'), 'noline' => 1];

				if(!$isDisabled){
					$isDisabled = (get_value("FORMMAIL_BLOCK") == 0);
				}

				$formmail_span = new we_html_select(['name' => "newconf[FORMMAIL_SPAN]", 'style' => "width:88px;", "class" => "weSelect"]);
				if($isDisabled){
					$formmail_span->setAttribute("disabled", "disabled");
				}
				$formmail_span->addOption(60, g_l('prefs', '[1_minute]'));
				$formmail_span->addOption(120, sprintf(g_l('prefs', '[more_minutes]'), 2));
				$formmail_span->addOption(180, sprintf(g_l('prefs', '[more_minutes]'), 3));
				$formmail_span->addOption(300, sprintf(g_l('prefs', '[more_minutes]'), 5));
				$formmail_span->addOption(600, sprintf(g_l('prefs', '[more_minutes]'), 10));
				$formmail_span->addOption(1200, sprintf(g_l('prefs', '[more_minutes]'), 20));
				$formmail_span->addOption(1800, sprintf(g_l('prefs', '[more_minutes]'), 30));
				$formmail_span->addOption(2700, sprintf(g_l('prefs', '[more_minutes]'), 45));
				$formmail_span->addOption(3600, g_l('prefs', '[1_hour]'));
				$formmail_span->addOption(7200, sprintf(g_l('prefs', '[more_hours]'), 2));
				$formmail_span->addOption(14400, sprintf(g_l('prefs', '[more_hours]'), 4));
				$formmail_span->addOption(28800, sprintf(g_l('prefs', '[more_hours]'), 8));
				$formmail_span->addOption(86400, sprintf(g_l('prefs', '[more_hours]'), 24));

				$formmail_span->selectOption(get_value("FORMMAIL_SPAN"));


				$settings[] = ['html' => $formmail_span->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[formmailSpan]'), 'noline' => 1];
				$formmail_blocktime = new we_html_select(['name' => "newconf[FORMMAIL_BLOCKTIME]", 'style' => "width:88px;", "class" => "weSelect"]);
				if($isDisabled){
					$formmail_blocktime->setAttribute("disabled", "disabled");
				}
				$formmail_blocktime->addOption(60, g_l('prefs', '[1_minute]'));
				$formmail_blocktime->addOption(120, sprintf(g_l('prefs', '[more_minutes]'), 2));
				$formmail_blocktime->addOption(180, sprintf(g_l('prefs', '[more_minutes]'), 3));
				$formmail_blocktime->addOption(300, sprintf(g_l('prefs', '[more_minutes]'), 5));
				$formmail_blocktime->addOption(600, sprintf(g_l('prefs', '[more_minutes]'), 10));
				$formmail_blocktime->addOption(1200, sprintf(g_l('prefs', '[more_minutes]'), 20));
				$formmail_blocktime->addOption(1800, sprintf(g_l('prefs', '[more_minutes]'), 30));
				$formmail_blocktime->addOption(2700, sprintf(g_l('prefs', '[more_minutes]'), 45));
				$formmail_blocktime->addOption(3600, g_l('prefs', '[1_hour]'));
				$formmail_blocktime->addOption(7200, sprintf(g_l('prefs', '[more_hours]'), 2));
				$formmail_blocktime->addOption(14400, sprintf(g_l('prefs', '[more_hours]'), 4));
				$formmail_blocktime->addOption(28800, sprintf(g_l('prefs', '[more_hours]'), 8));
				$formmail_blocktime->addOption(86400, sprintf(g_l('prefs', '[more_hours]'), 24));
				$formmail_blocktime->addOption(-1, g_l('prefs', '[ever]'));

				$formmail_blocktime->selectOption(get_value("FORMMAIL_BLOCKTIME"));


				$settings[] = ['html' => $formmail_blocktime->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG, "headline" => g_l('prefs', '[blockFor]'), 'noline' => 1];
			}

			return create_dialog("", $settings, -1);

		case 'modules':
			if(!we_base_preferences::userIsAllowed('active_integrated_modules')){
				break;
			}
			$modInfos = we_base_moduleInfo::getAllModules();

			$html = '';

			foreach($modInfos as $modKey => $modInfo){
				if(!isset($modInfo["alwaysActive"])){
					$modInfo["alwaysActive"] = null;
				}
				$onclick = "";
				if(!empty($modInfo["childmodule"])){
					$onclick = "if(!this.checked){document.getElementById('newconf[active_integrated_modules][" . $modInfo["childmodule"] . "]').checked=false;}";
				}
				if(!empty($modInfo["dependson"])){
					$onclick = "if(this.checked){document.getElementById('newconf[active_integrated_modules][" . $modInfo["dependson"] . "]').checked=true;}";
				}
				if(!$modInfo["alwaysActive"]){
					$html .= we_html_forms::checkbox($modKey, $modInfo["alwaysActive"] || we_base_moduleInfo::isActive($modKey), "newconf[active_integrated_modules][$modKey]", $modInfo["text"], false, "defaultfont", $onclick, $modInfo["alwaysActive"]) . ($modInfo["alwaysActive"] ? "<input type=\"hidden\" name=\"newconf[active_integrated_modules][$modKey]\" value=\"$modKey\" />" : "" ) . "<br />";
				}
			}

			$settings = [
				['headline' => g_l('prefs', '[module_activation][headline]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[module_activation][information]'), we_html_tools::TYPE_HELP, false, false)
					, "html" => $html, 'space' => we_html_multiIconBox::SPACE_BIG]
			];

			return create_dialog('', $settings, -1);

		case 'proxy':
			if(!we_base_preferences::userIsAllowed('useproxy')){
				break;
			}
			/**
			 * Proxy server
			 */
			// Check Proxy settings  ...
			$proxy = get_value("proxy_proxy");

			$use_proxy = we_html_forms::checkbox(1, $proxy, "newconf[useproxy]", g_l('prefs', '[useproxy]'), false, "defaultfont", "set_state();");
			$proxyaddr = we_html_tools::htmlTextInput("newconf[proxyhost]", 22, get_value("WE_PROXYHOST"), "", "", "text", 225, 0, "", !$proxy);
			$proxyport = we_html_tools::htmlTextInput("newconf[proxyport]", 22, get_value("WE_PROXYPORT"), "", "", "text", 225, 0, "", !$proxy);
			$proxyuser = we_html_tools::htmlTextInput("newconf[proxyuser]", 22, get_value("WE_PROXYUSER"), "", 'autocomplete="off"', "text", 225, 0, "", !$proxy);
			$proxypass = we_html_tools::htmlTextInput("newconf[proxypass]", 22, we_customer_customer::NOPWD_CHANGE, "", 'autocomplete="off"', "password", 225, 0, "", !$proxy);

			// Build dialog if user has permission

			$settings = [
				["headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[proxy_information]'), we_html_tools::TYPE_INFO, 450, false), 'noline' => 1],
				["headline" => g_l('prefs', '[tab][proxy]'), "html" => $use_proxy, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[proxyaddr]'), "html" => $proxyaddr, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[proxyport]'), "html" => $proxyport, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[proxyuser]'), "html" => $proxyuser, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[proxypass]'), "html" => $proxypass, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
			];
			// Build dialog element if user has permission
			return create_dialog("", $settings, -1);


		case "advanced":
			/*			 * *******************************************************************
			 * ATTRIBS
			 * ******************************************************************* */
			if(!permissionhandler::hasPerm("ADMINISTRATOR")){
				break;
			}
			/*
			  $WYSIWYG_TYPE = new we_html_select(array('name' => "newconf[WYSIWYG_TYPE]", "class" => "weSelect"));
			  $options = array('tinyMCE' => 'tinyMCE', 'default' => 'webEdition Editor (deprecated))');
			  foreach($options as $key => $val){
			  $WYSIWYG_TYPE->addOption($key, $val);
			  }
			  $WYSIWYG_TYPE->selectOption(get_value("WYSIWYG_TYPE"));
			  $settings[] = array("headline" => g_l('prefs', '[wysiwyg_type]'), "html" => $WYSIWYG_TYPE->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG);

			  $WYSIWYG_TYPE_FRONTEND = new we_html_select(array('name' => "newconf[WYSIWYG_TYPE_FRONTEND]", "class" => "weSelect"));
			  $options = array('tinyMCE' => 'tinyMCE', 'default' => 'webEdition Editor (deprecated))');
			  foreach($options as $key => $val){
			  $WYSIWYG_TYPE_FRONTEND->addOption($key, $val);
			  }
			  $WYSIWYG_TYPE_FRONTEND->selectOption(get_value("WYSIWYG_TYPE_FRONTEND"));
			  $settings[] = array("headline" => "Editor für textareas im Frontend", "html" => $WYSIWYG_TYPE_FRONTEND->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG);
			 */
			$we_doctype_workspace_behavior = get_value("WE_DOCTYPE_WORKSPACE_BEHAVIOR");
			$we_doctype_workspace_behavior_table = '<table class="default"><tr><td>' .
				we_html_forms::radiobutton(0, (!$we_doctype_workspace_behavior), "newconf[WE_DOCTYPE_WORKSPACE_BEHAVIOR]", g_l('prefs', '[we_doctype_workspace_behavior_0]'), true, "defaultfont", "", false, g_l('prefs', '[we_doctype_workspace_behavior_hint0]'), 0, 430) .
				'</td></tr><tr><td style="padding-top:10px;">' .
				we_html_forms::radiobutton(1, $we_doctype_workspace_behavior, "newconf[WE_DOCTYPE_WORKSPACE_BEHAVIOR]", g_l('prefs', '[we_doctype_workspace_behavior_1]'), true, "defaultfont", "", false, g_l('prefs', '[we_doctype_workspace_behavior_hint1]'), 0, 430) .
				'</td></tr></table>';

			$settings[] = ["headline" => g_l('prefs', '[we_doctype_workspace_behavior]'), "html" => $we_doctype_workspace_behavior_table, 'space' => we_html_multiIconBox::SPACE_BIG];

			if(we_base_preferences::userIsAllowed('WE_LOGIN_HIDEWESTATUS')){
				$loginWEst_disabler = we_html_forms::checkbox(1, get_value('WE_LOGIN_HIDEWESTATUS') == 1 ? 1 : 0, 'newconf[WE_LOGIN_HIDEWESTATUS]', g_l('prefs', '[login][deactivateWEstatus]'));

				$we_windowtypes = [0 => g_l('prefs', '[login][windowtypeboth]'),
					1 => g_l('prefs', '[login][windowtypepopup]'),
					2 => g_l('prefs', '[login][windowtypesame]')];
				$we_windowtypeselect = new we_html_select(['name' => 'newconf[WE_LOGIN_WEWINDOW]', 'class' => 'weSelect']);
				foreach($we_windowtypes as $key => $value){
					$we_windowtypeselect->addOption($key, $value);
				}
				$we_windowtypeselect->selectOption(get_value('WE_LOGIN_WEWINDOW'));
				// Build dialog if user has permission
				$settings[] = ['headline' => g_l('prefs', '[login][login]'), 'html' => $loginWEst_disabler . we_html_element::htmlBr() . g_l('prefs', '[login][windowtypes]') . we_html_element::htmlBr() . $we_windowtypeselect->getHtml(),
					'space' => we_html_multiIconBox::SPACE_BIG];
			}

			if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
				$_Schedtrigger_setting = new we_html_select(['name' => "newconf[SCHEDULER_TRIGGER]", "class" => "weSelect"]);
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_PREDOC, g_l('prefs', '[we_scheduler_trigger][preDoc]')); //pre
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_POSTDOC, g_l('prefs', '[we_scheduler_trigger][postDoc]')); //post
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_CRON, g_l('prefs', '[we_scheduler_trigger][cron]')); //cron
				$_Schedtrigger_setting->selectOption(get_value("SCHEDULER_TRIGGER"));
				$tmp = $_Schedtrigger_setting->getHtml();
				$settings[] = ["headline" => g_l('prefs', '[we_scheduler_trigger][head]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[we_scheduler_trigger][description]'), we_html_tools::TYPE_HELP),
					"html" => $tmp, 'space' => we_html_multiIconBox::SPACE_BIG];
			}
			// Build select box
			$NAVIGATION_ENTRIES_FROM_DOCUMENT = new we_html_select(['name' => "newconf[NAVIGATION_ENTRIES_FROM_DOCUMENT]", "class" => "weSelect"]);
			for($i = 0; $i < 2; $i++){
				$NAVIGATION_ENTRIES_FROM_DOCUMENT->addOption($i, g_l('prefs', $i == 0 ? '[navigation_entries_from_document_folder]' : '[navigation_entries_from_document_item]'));
			}
			$NAVIGATION_ENTRIES_FROM_DOCUMENT->selectOption(get_value("NAVIGATION_ENTRIES_FROM_DOCUMENT") ? 1 : 0);
			$settings[] = ["headline" => g_l('prefs', '[navigation_entries_from_document]'), "html" => $NAVIGATION_ENTRIES_FROM_DOCUMENT->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG];


			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH = new we_html_select(['name' => "newconf[NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH]", "class" => "weSelect"]);
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->addOption(0, g_l('prefs', '[no]'));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->addOption(1, g_l('prefs', '[yes]'));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->selectOption(get_value("NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH") ? 1 : 0);
			$settings[] = ["headline" => g_l('prefs', '[navigation_rules_continue]'), "html" => $NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG];

			return create_dialog("", $settings, -1);

		case "system":
			if(!permissionhandler::hasPerm("ADMINISTRATOR")){
				break;
			}
			//FILE EXTENSIONS
			// Get webEdition extensions
			$we_extensions = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::WEDOCUMENT);

			// Build static webEdition extensions select box
			$static_we_extensions = new we_html_select(['name' => 'newconf[DEFAULT_STATIC_EXT]', 'class' => 'weSelect']);
			$dynamic_we_extensions = new we_html_select(['name' => 'newconf[DEFAULT_DYNAMIC_EXT]', 'class' => 'weSelect']);
			foreach($we_extensions as $value){
				$static_we_extensions->addOption($value, $value);
				$dynamic_we_extensions->addOption($value, $value);
			}
			$static_we_extensions->selectOption(get_value('DEFAULT_STATIC_EXT'));
			$dynamic_we_extensions->selectOption(get_value('DEFAULT_DYNAMIC_EXT'));

			$we_extensions_html = g_l('prefs', '[static]') . we_html_element::htmlBr() . $static_we_extensions->getHtml() . we_html_element::htmlBr() . we_html_element::htmlBr() . g_l('prefs', '[dynamic]') . we_html_element::htmlBr() . $dynamic_we_extensions->getHtml();

			// HTML extensions
			$html_extensions = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::HTML);

			// Build static webEdition extensions select box
			$static_html_extensions = new we_html_select(['name' => 'newconf[DEFAULT_HTML_EXT]', 'class' => 'weSelect']);
			foreach($html_extensions as $value){
				$static_html_extensions->addOption($value, $value);
			}
			$static_html_extensions->selectOption(get_value('DEFAULT_HTML_EXT'));

			$html_extensions_html = g_l('prefs', '[html]') . '<br/>' . $static_html_extensions->getHtml();


			//	array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[extensions_information]'), we_html_tools::TYPE_INFO, 450, false), ),


			$we_max_upload_size = '<table class="default"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[FILE_UPLOAD_MAX_UPLOAD_SIZE]", 22, get_value("FILE_UPLOAD_MAX_UPLOAD_SIZE"), "", ' onkeypress="return WE().util.IsDigit(event);"', "number", 60) . ' MB</td><td style="padding-left:20px;" class="small">' .
				g_l('prefs', '[upload][we_max_size_hint]') .
				'</td></tr></table>';

			$we_new_folder_mod = '<table class="default"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[WE_NEW_FOLDER_MOD]", 22, get_value("WE_NEW_FOLDER_MOD"), 3, ' onkeypress="return WE().util.IsDigit(event);"', "text", 60) . '</td><td style="padding-left:20px;" class="small">' .
				g_l('prefs', '[we_new_folder_mod_hint]') .
				'</td></tr></table>';

			// Build db select box
			$db_connect = new we_html_select(['name' => 'newconf[DB_CONNECT]', 'class' => 'weSelect']);
			if(class_exists('mysqli', false)){
				$db_connect->addOption('mysqli_connect', 'mysqli_connect');
				$db_connect->addOption('mysqli_pconnect', 'mysqli_pconnect');
			}
			if(function_exists('mysql_connect')){ //only allow old connection method if new is not available
				$db_connect->addOption('connect', 'connect (deprecated)');
				$db_connect->addOption('pconnect', 'pconnect (deprecated)');
			}
			$db_connect->selectOption(DB_CONNECT);

			// Build db charset select box
			$html_db_charset_information = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[db_set_charset_information]'), we_html_tools::TYPE_HELP);
			$html_db_charset_warning = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[db_set_charset_warning]'), we_html_tools::TYPE_ALERT, false);

			$db_set_charset = new we_html_select(['name' => 'newconf[DB_SET_CHARSET]', 'class' => 'weSelect']);

			$GLOBALS['DB_WE']->query('SHOW CHARACTER SET');

			$charsets = [''];
			while($GLOBALS['DB_WE']->next_record()){
				$charsets[] = $GLOBALS['DB_WE']->f('Charset');
			}
			sort($charsets);
			foreach($charsets as $charset){
				$db_set_charset->addOption($charset, $charset);
			}

			if(defined('DB_SET_CHARSET') && DB_SET_CHARSET != ''){
				$db_set_charset->selectOption(DB_SET_CHARSET);
			} else {
				$tmp = $GLOBALS['DB_WE']->getCurrentCharset();
				if($tmp){
					$db_set_charset->selectOption($tmp);
					$file = &$GLOBALS['config_files']['conf_global']['content'];
					$file = we_base_preferences::changeSourceCode('define', $file, 'DB_SET_CHARSET', $tmp);
				}
			}

			// Check authentication settings  ...
			$auth = get_value("HTTP_USERNAME");
			$auth_user = get_value("HTTP_USERNAME");
			$auth_pass = get_value("HTTP_PASSWORD");

			// Build dialog if user has permission
			$use_auth = we_html_element::htmlHidden('newconf[useauth]', $auth) .
				we_html_forms::checkbox(1, $auth, "useauthEnabler", g_l('prefs', '[useauth]'), false, "defaultfont", "set_state_auth();");

			/**
			 * User name
			 */
			$authuser = we_html_tools::htmlTextInput("newconf[HTTP_USERNAME]", 22, $auth_user, "", 'autocomplete="off"', "text", 225, 0, "", !$auth);
			$authpass = we_html_tools::htmlTextInput("newconf[HTTP_PASSWORD]", 22, we_customer_customer::NOPWD_CHANGE, "", 'autocomplete="off"', "password", 225, 0, "", !$auth);


			if(we_base_imageEdit::gd_version() > 0){ //  gd lib ist installiert
				$but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server', 'newconf[WE_THUMBNAIL_DIRECTORY]']', '" . we_base_ContentTypes::FOLDER . "', document.forms[0].elements['newconf[WE_THUMBNAIL_DIRECTORY]'].value, '')") : "";
				$inp = we_html_tools::htmlTextInput("newconf[WE_THUMBNAIL_DIRECTORY]", 12, get_value("WE_THUMBNAIL_DIRECTORY"), "", "", "text", 125);
				$thumbnail_dir = $inp . $but;
			} else { //  gd lib ist nicht installiert
				$but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button(we_html_button::SELECT, "#", '', 0, 0, '', '', true) : "";
				$inp = we_html_tools::htmlTextInput("newconf[WE_THUMBNAIL_DIRECTORY]", 12, get_value("WE_THUMBNAIL_DIRECTORY"), "", "", "text", 125, 0, '', true);
				$thumbnail_dir = $inp . $but . '<br/>' . g_l('thumbnails', '[add_description_nogdlib]');
			}

			//  select if hooks can be executed
			$EXECUTE_HOOKS = new we_html_select(['name' => "newconf[EXECUTE_HOOKS]", "class" => "weSelect"]);
			$EXECUTE_HOOKS->addOption(0, g_l('prefs', '[no]'));
			$EXECUTE_HOOKS->addOption(1, g_l('prefs', '[yes]'));

			$EXECUTE_HOOKS->selectOption(get_value("EXECUTE_HOOKS") ? 1 : 0);

			$hooksHtml = $EXECUTE_HOOKS->getHtml() . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[hooks_information]'), we_html_tools::TYPE_HELP);

			$useSession = new we_html_select(['name' => "newconf[SYSTEM_WE_SESSION]", "class" => "weSelect", 'onchange' => 'top.we_showMessage(\'' . g_l('prefs', '[session][crypt][alert]') . '\', WE().consts.message.WE_MESSAGE_ERROR);']);
			$useSession->addOption(0, g_l('prefs', '[no]'));
			$useSession->addOption(1, g_l('prefs', '[yes]'));
			$useSession->selectOption(get_value("SYSTEM_WE_SESSION") ? 1 : 0);

			$sessionTime = '<table class="default"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[SYSTEM_WE_SESSION_TIME]", 22, abs(get_value("SYSTEM_WE_SESSION_TIME")), "", ' onkeypress="return WE().util.IsDigit(event);"', "text", 60) . '</td><td style="padding-left:20px;" class="small">s</td></tr></table>';

			$cryptSession = new we_html_select(['name' => 'newconf[SYSTEM_WE_SESSION_CRYPT]', 'class' => "weSelect", 'onchange' => 'top.we_showMessage(\'' . g_l('prefs', '[session][crypt][alert]') . '\', WE().consts.message.WE_MESSAGE_ERROR);']);
			$cryptSession->addOption(0, g_l('prefs', '[no]'));
			$cryptSession->addOption(1, 'Transparent');
			$cryptSession->addOption(2, 'Cookie');
			$cryptSession->selectOption(get_value('SYSTEM_WE_SESSION_CRYPT'));

			$sessionHtml = $useSession->getHtml() . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[session][information]'), we_html_tools::TYPE_HELP);


			$settings = [
				['headline' => g_l('prefs', '[we_extensions]'), 'html' => $we_extensions_html, 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[html_extensions]'), 'html' => $html_extensions_html, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[upload][we_max_size]'), "html" => $we_max_upload_size, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[we_new_folder_mod]'), "html" => $we_new_folder_mod, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[db_connect]'), "html" => $db_connect->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[db_set_charset]'), "html" => $db_set_charset->getHtml() . $html_db_charset_information . $html_db_charset_warning, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[auth]'), "html" => $use_auth, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[authuser]'), "html" => $authuser, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[authpass]'), "html" => $authpass, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[thumbnail_dir]'), "html" => $thumbnail_dir, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[hooks]'), "html" => $hooksHtml, 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[session][title]'), "html" => $sessionHtml, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[session][time]'), "html" => $sessionTime, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[session][crypt][title]'), "html" => $cryptSession->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG],
			];
			// Build dialog element if user has permission
			return create_dialog("", $settings, -1);

		case "seolinks":
			/*			 * *******************************************************************
			 * ATTRIBS
			 * ******************************************************************* */
			if(!permissionhandler::hasPerm("ADMINISTRATOR")){
				break;
			}
			// Build dialog if user has permission

			$navigation_directoryindex_names = we_html_tools::htmlTextInput("newconf[NAVIGATION_DIRECTORYINDEX_NAMES]", 22, get_value("NAVIGATION_DIRECTORYINDEX_NAMES"), "", "", "text", 225);

			$weSuggest->setAcId("doc2");
			$weSuggest->setContentType(we_base_ContentTypes::FOLDER . ',' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
			$weSuggest->setInput('error_document_no_objectfile_text', ( ERROR_DOCUMENT_NO_OBJECTFILE ? id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE) : ''));
			$weSuggest->setMaxResults(20);
			$weSuggest->setResult('newconf[ERROR_DOCUMENT_NO_OBJECTFILE]', ( ERROR_DOCUMENT_NO_OBJECTFILE ?: 0));
			$weSuggest->setSelector(weSuggest::DocSelector);
			$weSuggest->setWidth(300);
			$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.forms[0].elements['newconf[ERROR_DOCUMENT_NO_OBJECTFILE]'].value, '" . FILE_TABLE . "', 'newconf[ERROR_DOCUMENT_NO_OBJECTFILE]','error_document_no_objectfile_text','','','', '" . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML . "', 1)"));
			$weSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.forms[0].elements['newconf[ERROR_DOCUMENT_NO_OBJECTFILE]'].value = 0;document.forms[0].elements.error_document_no_objectfile_text.value = ''"));

			$settings = [
				["headline" => g_l('prefs', '[general_directoryindex_hide]'), "html" => "", 'noline' => 1],
				["html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[navigation_directoryindex_description]'), we_html_tools::TYPE_INFO, 480), 'noline' => 1],
				["headline" => g_l('prefs', '[navigation_directoryindex_hide]'), "html" => getTrueFalseSelect('NAVIGATION_DIRECTORYINDEX_HIDE'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["headline" => g_l('prefs', '[wysiwyglinks_directoryindex_hide]'), "html" => getTrueFalseSelect('WYSIWYGLINKS_DIRECTORYINDEX_HIDE'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["headline" => g_l('prefs', '[navigation_directoryindex_names]'), "html" => $navigation_directoryindex_names, 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_directoryindex_hide_description]'), we_html_tools::TYPE_INFO, 480), 'noline' => 1],
				["headline" => g_l('prefs', '[taglinks_directoryindex_hide]'), "html" => getTrueFalseSelect('TAGLINKS_DIRECTORYINDEX_HIDE'), 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[general_objectseourls]'), "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[objectseourls_lowercase_description]'), we_html_tools::TYPE_HELP),
					'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[objectseourls_lowercase]'), "html" => getTrueFalseSelect('OBJECTSEOURLS_LOWERCASE'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["headline" => g_l('prefs', '[navigation_objectseourls]'), "html" => getTrueFalseSelect('NAVIGATION_OBJECTSEOURLS'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["headline" => g_l('prefs', '[wysiwyglinks_objectseourls]'), "html" => getTrueFalseSelect('WYSIWYGLINKS_OBJECTSEOURLS'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_objectseourls_description]'), we_html_tools::TYPE_INFO, 480), 'noline' => 1],
				["headline" => g_l('prefs', '[taglinks_objectseourls]'), "html" => getTrueFalseSelect('TAGLINKS_OBJECTSEOURLS'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["headline" => g_l('prefs', '[urlencode_objectseourls]'), "html" => getTrueFalseSelect('URLENCODE_OBJECTSEOURLS'), 'space' => we_html_multiIconBox::SPACE_BIG],
				["headline" => g_l('prefs', '[general_seoinside]'), "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_seoinside_description]'), we_html_tools::TYPE_HELP),
					'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				["headline" => g_l('prefs', '[seoinside_hideineditmode]'), "html" => getTrueFalseSelect('SEOINSIDE_HIDEINEDITMODE'), 'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1],
				["headline" => g_l('prefs', '[seoinside_hideinwebedition]'), "html" => getTrueFalseSelect('SEOINSIDE_HIDEINWEBEDITION'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[error_no_object_found]'), 'html' => $weSuggest->getHTML(), 'space' => we_html_multiIconBox::SPACE_BIG, 'noline' => 1],
				['headline' => g_l('prefs', '[suppress404code]'), 'html' => getTrueFalseSelect('SUPPRESS404CODE'), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[force404redirect]'), 'html' => getTrueFalseSelect('FORCE404REDIRECT') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[force404redirect_description]'), we_html_tools::TYPE_HELP),
					'space' => we_html_multiIconBox::SPACE_BIG],
			];
			return create_dialog('', $settings, -1, '', '', null);

		case 'error_handling':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			/**
			 * Error handler
			 */
			$foldAt = 4;

			// Create checkboxes
			$we_error_handler = we_html_forms::checkbox(1, get_value('WE_ERROR_HANDLER'), 'newconf[WE_ERROR_HANDLER]', g_l('prefs', '[error_use_handler]'), false, 'defaultfont', 'set_state_error_handler();');

			// Error types
			// Create checkboxes
			$error_handling_table = new we_html_table(['class' => 'default'], 8, 1);

			$error_handling_table->setCol(0, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_ERRORS'), 'newconf[WE_ERROR_ERRORS]', g_l('prefs', '[error_errors]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$error_handling_table->setCol(2, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_WARNINGS'), 'newconf[WE_ERROR_WARNINGS]', g_l('prefs', '[error_warnings]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$error_handling_table->setCol(4, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_NOTICES'), 'newconf[WE_ERROR_NOTICES]', g_l('prefs', '[error_notices]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$error_handling_table->setCol(6, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_DEPRECATED'), 'newconf[WE_ERROR_DEPRECATED]', g_l('prefs', '[error_deprecated]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));

			// Create checkboxes
			$error_display_table = new we_html_table(['class' => 'default'], 8, 1);
			$error_display_table->setCol(0, 0, ['class' => 'defaultfont', 'style' => 'padding-left: 25px;'], we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[error_notices_warning]'), we_html_tools::TYPE_ALERT, 260));

			$error_display_table->setCol(1, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_SHOW'), 'newconf[WE_ERROR_SHOW]', g_l('prefs', '[error_display]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$error_display_table->setCol(3, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_LOG'), 'newconf[WE_ERROR_LOG]', g_l('prefs', '[error_log]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$error_display_table->setCol(5, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_MAIL'), 'newconf[WE_ERROR_MAIL]', g_l('prefs', '[error_mail]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));

			// Create specify mail address input
			$error_mail_specify_table = new we_html_table(['class' => 'default', 'style' => 'margin-left:25px;'], 1, 4);

			$error_mail_specify_table->setCol(0, 1, ['class' => 'defaultfont'], g_l('prefs', '[error_mail_address]') . ': ');
			$error_mail_specify_table->setCol(0, 2, ['style' => 'text-align:left'], we_html_tools::htmlTextInput('newconf[WE_ERROR_MAIL_ADDRESS]', 6, get_value('WE_ERROR_MAIL_ADDRESS'), 100, 'placeholder="mail@example"', 'email', 195));

			$error_display_table->setCol(7, 0, null, $error_mail_specify_table->getHtml());

			$settings = [
				//array('headline' => g_l('prefs', '[templates]'), 'html' => $template_error_handling_table->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG),
				['headline' => g_l('prefs', '[tab][error_handling]'), 'html' => $we_error_handler, 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[error_types]'), 'html' => $error_handling_table->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG],
				['headline' => g_l('prefs', '[error_displaying]'), 'html' => $error_display_table->getHtml(), 'space' => we_html_multiIconBox::SPACE_BIG],
			];

			return create_dialog('settings_error_expert', $settings, $foldAt, g_l('prefs', '[show_expert]'), g_l('prefs', '[hide_expert]'));

		case 'security':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				return;
			}
			$row = 0;
			$customer_table = new we_html_table(['class' => 'default', 'id' => 'customer_table'], 9, 10);
			$customer_table->setCol($row, 0, ['class' => 'defaultfont', 'width' => '20px'], '');
			$customer_table->setCol($row, 1, ['class' => 'defaultfont', 'colspan' => 5], g_l('prefs', '[security][customer][disableLogins]') . ':');
			$customer_table->setCol($row, 6, ['width' => 300]);
			$customer_table->setCol(++$row, 1, ['class' => 'defaultfont'], g_l('prefs', '[security][customer][sameIP]'));
			$customer_table->setCol($row, 2, ['width' => '20px']);
			$customer_table->setCol($row, 3, [], we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_IP]', 3, get_value('SECURITY_LIMIT_CUSTOMER_IP'), 3, '', 'number', 50));
			$customer_table->setCol($row, 4, ['class' => 'defaultfont', 'style' => 'width:2em;text-align:center'], '/');
			$customer_table->setCol($row, 5, [], we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_IP_HOURS]', 3, get_value('SECURITY_LIMIT_CUSTOMER_IP_HOURS'), 3, '', 'number', 50));
			$customer_table->setCol($row, 6, ['class' => 'defaultfont'], 'h');

			$customer_table->setCol(++$row, 1, ['class' => 'defaultfont'], g_l('prefs', '[security][customer][sameUser]'));
			$customer_table->setCol($row, 3, [], we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_NAME]', 3, get_value('SECURITY_LIMIT_CUSTOMER_NAME'), 3, '', 'number', 50));
			$customer_table->setCol($row, 4, ['class' => 'defaultfont', 'style' => 'text-align:center;'], '/');
			$customer_table->setCol($row, 5, [], we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_NAME_HOURS]', 3, get_value('SECURITY_LIMIT_CUSTOMER_NAME_HOURS'), 3, '', 'number', 50));
			$customer_table->setCol($row, 6, ['class' => 'defaultfont'], 'h');

			$customer_table->setCol(++$row, 1, ['class' => 'defaultfont'], g_l('prefs', '[security][customer][errorPage]'));

			$weSuggest->setAcId('SECURITY_LIMIT_CUSTOMER_REDIRECT_doc');
			$weSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
			$weSuggest->setInput('SECURITY_LIMIT_CUSTOMER_REDIRECT_text', (SECURITY_LIMIT_CUSTOMER_REDIRECT ? id_to_path(SECURITY_LIMIT_CUSTOMER_REDIRECT) : ''));
			$weSuggest->setMaxResults(20);
			$weSuggest->setResult('newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]', ( SECURITY_LIMIT_CUSTOMER_REDIRECT ?: 0));
			$weSuggest->setSelector(weSuggest::DocSelector);
			$weSuggest->setWidth(250);
			$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', document.forms[0].elements['newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]'].value, '" . FILE_TABLE . "', 'newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]','SECURITY_LIMIT_CUSTOMER_REDIRECT_text','','','', '" . we_base_ContentTypes::WEDOCUMENT . "," . we_base_ContentTypes::HTML . "', 1)"), 10);
			$weSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.forms[0].elements['newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]'].value = 0;document.forms[0].elements.SECURITY_LIMIT_CUSTOMER_REDIRECT_text.value = ''"), 4);

			$customer_table->setCol($row, 3, ['class' => 'defaultfont', 'colspan' => 5], $weSuggest->getHTML());

			$customer_table->setCol(++$row, 1, ['class' => 'defaultfont'], g_l('prefs', '[security][customer][slowDownLogin]'));
			$customer_table->setCol($row, 3, [], we_html_tools::htmlTextInput('newconf[SECURITY_DELAY_FAILED_LOGIN]', 3, get_value('SECURITY_DELAY_FAILED_LOGIN'), 3, '', 'number', 50));
			$customer_table->setCol($row, 4, [], 's');

			$customer_table->setCol(++$row, 1, ['class' => 'defaultfont'], g_l('prefs', '[security][customer][deleteSession]'));

			$customer_table->setCol($row, 3, [], we_html_tools::htmlSelect('newconf[SECURITY_DELETE_SESSION]', [g_l('prefs', '[no]'), g_l('prefs', '[yes]')], 1, get_value('SECURITY_DELETE_SESSION')));

			$encryption = new we_html_select(['name' => 'newconf[SECURITY_ENCRYPTION_TYPE_PASSWORD]', 'class' => 'weSelect']);
			$encryption->addOption(we_customer_customer::ENCRYPT_NONE, g_l('prefs', '[security][encryption][type][0]'));
			if(function_exists('mcrypt_module_open') && ($res = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_OFB, ''))){
				$encryption->addOption(we_customer_customer::ENCRYPT_SYMMETRIC, g_l('prefs', '[security][encryption][type][1]'));
				mcrypt_module_close($res);
			}

			$encryption->addOption(we_customer_customer::ENCRYPT_HASH, g_l('prefs', '[security][encryption][type][2]'), []);
			$encryption->selectOption(get_value('SECURITY_ENCRYPTION_TYPE_PASSWORD'));


			$encryptinfo = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[security][encryption][hint]'), we_html_tools::TYPE_ALERT, false);
			$cryptkey = get_value('SECURITY_ENCRYPTION_KEY');
			$encryptionKey = we_html_tools::htmlTextInput('newconf[SECURITY_ENCRYPTION_KEY]', 0, ($cryptkey ?: bin2hex(we_customer_customer::cryptGetIV(56))), 112, '', 'text', '20em') . ' (hex)'; //+Button vorhandene Passwörter convertieren

			$storeSessionPassword = new we_html_select(['name' => 'newconf[SECURITY_SESSION_PASSWORD]', 'class' => 'weSelect']);
			$storeSessionPassword->addOption(we_customer_customer::REMOVE_PASSWORD, g_l('prefs', '[security][storeSessionPassword][type][0]'));
			$storeSessionPassword->addOption(we_customer_customer::STORE_PASSWORD, g_l('prefs', '[security][storeSessionPassword][type][1]'));
			$storeSessionPassword->addOption(we_customer_customer::STORE_DBPASSWORD, g_l('prefs', '[security][storeSessionPassword][type][2]'));
			$storeSessionPassword->addOption(we_customer_customer::STORE_PASSWORD + we_customer_customer::STORE_DBPASSWORD, g_l('prefs', '[security][storeSessionPassword][type][3]'));
			$storeSessionPassword->selectOption(get_value('SECURITY_SESSION_PASSWORD'));

			$settings = [
				['headline' => g_l('perms_customer', '[perm_group_title]'), 'html' => $customer_table->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED],
				['headline' => g_l('prefs', '[security][encryption][title]'), 'html' => $encryption->getHtml() . $encryptinfo, 'space' => we_html_multiIconBox::SPACE_MED, 'noline' => 1],
				['headline' => g_l('prefs', '[security][encryption][symmetricKey]'), 'html' => $encryptionKey, 'space' => we_html_multiIconBox::SPACE_MED],
				['headline' => g_l('prefs', '[security][storeSessionPassword][title]'), 'html' => $storeSessionPassword->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED],
				['headline' => g_l('prefs', '[security][userPassRegex][title]'), 'html' => we_html_tools::htmlTextInput('newconf[SECURITY_USER_PASS_REGEX]', 0, get_value('SECURITY_USER_PASS_REGEX'), 100, '', 'text', '20em'),
					'space' => we_html_multiIconBox::SPACE_MED],
			];
			return create_dialog('settings_security', $settings);

		case 'email':
			/**
			 * Information
			 */
			$settings = [
				['headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[mailer_information]'), we_html_tools::TYPE_INFO, 450, false),]
			];

			if(permissionhandler::hasPerm('ADMINISTRATOR')){
				$emailSelect = we_html_tools::htmlSelect('newconf[WE_MAILER]', ['php' => g_l('prefs', '[mailer_php]'), 'smtp' => g_l('prefs', '[mailer_smtp]')], 1, get_value('WE_MAILER'), false, [
						'onchange' => "var el = document.getElementById('smtp_table').style;var el2=document.getElementById('auth_table').style;var elAuth=document.getElementsByName('newconf[SMTP_AUTH]')[0]; if(this.value=='smtp'){ el.display='block'; el2.display=(elAuth.checked?'block':'none');}else{ el.display='none';el2.display='none';}"], 'value', 300, 'defaultfont');

				$smtp_table = new we_html_table(['class' => 'default', 'id' => 'smtp_table', 'width' => 300, 'style' => 'display: ' . ((get_value('WE_MAILER') === 'php') ? 'none' : 'block') . ';'], 4, 2);
				$smtp_table->setCol(0, 0, ['class' => 'defaultfont', 'style' => 'padding-right:10px;'], g_l('prefs', '[smtp_server]'));
				$smtp_table->setCol(0, 1, ['style' => 'text-align:right'], we_html_tools::htmlTextInput('newconf[SMTP_SERVER]', 24, get_value('SMTP_SERVER'), 180, '', 'text', 180));
				$smtp_table->setCol(1, 0, ['class' => 'defaultfont'], g_l('prefs', '[smtp_port]'));
				$smtp_table->setCol(1, 1, ['style' => 'text-align:right'], we_html_tools::htmlTextInput('newconf[SMTP_PORT]', 24, get_value('SMTP_PORT'), 180, '', 'text', 180));


				$encryptSelect = we_html_tools::htmlSelect('newconf[SMTP_ENCRYPTION]', [0 => g_l('prefs', '[smtp_encryption_none]'), 'ssl' => g_l('prefs', '[smtp_encryption_ssl]'),
						'tls' => g_l('prefs', '[smtp_encryption_tls]')], 1, get_value('SMTP_ENCRYPTION'), false, [], 'value', 180, 'defaultfont');

				$smtp_table->setCol(2, 0, ['class' => 'defaultfont'], g_l('prefs', '[smtp_encryption]'));
				$smtp_table->setCol(2, 1, ['style' => 'text-align:right'], $encryptSelect);
				$smtp_table->setCol(3, 0, ['class' => 'defaultfont', 'colspan' => 3], we_html_forms::checkbox(1, get_value('SMTP_AUTH'), 'newconf[SMTP_AUTH]', g_l('prefs', '[smtp_auth]'), false, 'defaultfont', "var el2 = document.getElementById('auth_table').style; if(this.checked) el2.display='block'; else el2.display='none';"));

				$auth_table = new we_html_table(['class' => 'default', 'id' => 'auth_table', 'width' => 200, 'style' => 'display: ' . ((get_value('SMTP_AUTH') == 1) ? 'block' : 'none') . ';'], 2, 2);
				$auth_table->setCol(0, 0, ['class' => 'defaultfont'], g_l('prefs', '[smtp_username]'));
				$auth_table->setCol(0, 1, ['style' => 'text-align:right'], we_html_tools::htmlTextInput('newconf[SMTP_USERNAME]', 14, get_value('SMTP_USERNAME'), 105, 'placeholder="' . g_l('prefs', '[smtp_username]') . '"', 'text', 180));
				$auth_table->setCol(1, 0, ['class' => 'defaultfont'], g_l('prefs', '[smtp_password]'));
				$auth_table->setCol(1, 1, ['style' => 'text-align:right'], we_html_tools::htmlTextInput('newconf[SMTP_PASSWORD]', 14, get_value('SMTP_PASSWORD') ? we_customer_customer::NOPWD_CHANGE : '', 105, 'placeholder="' . g_l('prefs', '[smtp_password]') . '"', 'password', 180));
			}

			return create_dialog('settings_email', $settings);

		case 'versions':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			$versionsPrefs = [
				'ctypes' => [
					we_base_ContentTypes::IMAGE => 'VERSIONING_IMAGE',
					we_base_ContentTypes::HTML => 'VERSIONING_TEXT_HTML',
					we_base_ContentTypes::WEDOCUMENT => 'VERSIONING_TEXT_WEBEDITION',
					we_base_ContentTypes::JS => 'VERSIONING_TEXT_JS',
					we_base_ContentTypes::CSS => 'VERSIONING_TEXT_CSS',
					we_base_ContentTypes::TEXT => 'VERSIONING_TEXT_PLAIN',
					we_base_ContentTypes::HTACCESS => 'VERSIONING_TEXT_HTACCESS',
					we_base_ContentTypes::TEMPLATE => 'VERSIONING_TEXT_WETMPL',
					we_base_ContentTypes::FLASH => 'VERSIONING_FLASH',
					we_base_ContentTypes::VIDEO => 'VERSIONING_VIDEO',
					we_base_ContentTypes::AUDIO => 'VERSIONING_AUDIO',
					we_base_ContentTypes::APPLICATION => 'VERSIONING_SONSTIGE',
					we_base_ContentTypes::XML => 'VERSIONING_TEXT_XML',
					we_base_ContentTypes::OBJECT_FILE => 'VERSIONING_OBJECT',
				],
				'other' => [
					'VERSIONS_TIME_DAYS' => 'VERSIONS_TIME_DAYS',
					'VERSIONS_TIME_WEEKS' => 'VERSIONS_TIME_WEEKS',
					'VERSIONS_TIME_YEARS' => 'VERSIONS_TIME_YEARS',
					'VERSIONS_ANZAHL' => 'VERSIONS_ANZAHL',
					'VERSIONS_CREATE' => 'VERSIONS_CREATE',
					'VERSIONS_CREATE_TMPL' => 'VERSIONS_CREATE_TMPL',
					'VERSIONS_TIME_DAYS_TMPL' => 'VERSIONS_TIME_DAYS_TMPL',
					'VERSIONS_TIME_WEEKS_TMPL' => 'VERSIONS_TIME_WEEKS_TMPL',
					'VERSIONS_TIME_YEARS_TMPL' => 'VERSIONS_TIME_YEARS_TMPL',
					'VERSIONS_ANZAHL_TMPL' => 'VERSIONS_ANZAHL_TMPL'
				]
			];

			//js
			$jsCheckboxCheckAll = '';

			foreach($versionsPrefs['ctypes'] as $v){
				$jsCheckboxCheckAll .= 'document.getElementById("newconf[' . $v . ']").checked = checked;';
			}

			$_SESSION['weS']['versions']['logPrefs'] = [];
			foreach($versionsPrefs as $v){
				foreach($v as $val){
					$_SESSION['weS']['versions']['logPrefs'][$val] = get_value($val);
				}
			}

			$checkboxes = we_html_element::jsElement('
function checkAll(val) {
	checked=(val.checked)?1:0;
	' . $jsCheckboxCheckAll . ';
}
') .
				we_html_forms::checkbox(1, false, 'version_all', g_l('prefs', '[version_all]'), false, 'defaultfont', 'checkAll(this);') . '<br/>';

			foreach($versionsPrefs['ctypes'] as $k => $v){
				$checkboxes .= we_html_forms::checkbox(1, get_value($v), 'newconf[' . $v . ']', g_l('contentTypes', '[' . $k . ']'), false, 'defaultfont', 'checkAllRevert(this);') . '<br/>';
			}

			$versions_time_days = new we_html_select(['name' => 'newconf[VERSIONS_TIME_DAYS]', 'class' => 'weSelect']);

			$versions_time_days->addOption(-1, '');
			$versions_time_days->addOption(secondsDay, g_l('prefs', '[1_day]'));
			for($x = 2; $x <= 31; $x++){
				$versions_time_days->addOption(($x * secondsDay), sprintf(g_l('prefs', '[more_days]'), $x));
			}
			$versions_time_days->selectOption(get_value('VERSIONS_TIME_DAYS'));


			$versions_time_weeks = new we_html_select(['name' => 'newconf[VERSIONS_TIME_WEEKS]', 'class' => 'weSelect']);
			$versions_time_weeks->addOption(-1, '');
			$versions_time_weeks->addOption(secondsWeek, g_l('prefs', '[1_week]'));
			for($x = 2; $x <= 52; $x++){
				$versions_time_weeks->addOption(($x * secondsWeek), sprintf(g_l('prefs', '[more_weeks]'), $x));
			}
			$versions_time_weeks->selectOption(get_value('VERSIONS_TIME_WEEKS'));


			$versions_time_years = new we_html_select(['name' => 'newconf[VERSIONS_TIME_YEARS]', 'class' => 'weSelect']);
			$versions_time_years->addOption(-1, '');
			$versions_time_years->addOption(secondsYear, g_l('prefs', '[1_year]'));
			for($x = 2; $x <= 10; $x++){
				$versions_time_years->addOption(($x * secondsYear), sprintf(g_l('prefs', '[more_years]'), $x));
			}
			$versions_time_years->selectOption(get_value('VERSIONS_TIME_YEARS'));
			$versions_anzahl = we_html_tools::htmlTextInput('newconf[VERSIONS_ANZAHL]', 24, get_value('VERSIONS_ANZAHL'), 5, '', 'text', 50, 0, '');

			$versions_create_publishing = we_html_forms::radiobutton(1, (get_value('VERSIONS_CREATE') == 1), 'newconf[VERSIONS_CREATE]', g_l('prefs', '[versions_create_publishing]'), true, 'defaultfont', '', false, '');
			$versions_create_always = we_html_forms::radiobutton(0, (get_value('VERSIONS_CREATE') == 0), 'newconf[VERSIONS_CREATE]', g_l('prefs', '[versions_create_always]'), true, 'defaultfont', '', false, '');

			$versions_time_days_tmpl = new we_html_select(['name' => 'newconf[VERSIONS_TIME_DAYS_TMPL]', 'class' => 'weSelect']);

			$versions_time_days_tmpl->addOption(-1, '');
			$versions_time_days_tmpl->addOption(secondsDay, g_l('prefs', '[1_day]'));
			for($x = 2; $x <= 31; $x++){
				$versions_time_days_tmpl->addOption(($x * secondsDay), sprintf(g_l('prefs', '[more_days]'), $x));
			}
			$versions_time_days_tmpl->selectOption(get_value('VERSIONS_TIME_DAYS_TMPL'));


			$versions_time_weeks_tmpl = new we_html_select(['name' => 'newconf[VERSIONS_TIME_WEEKS_TMPL]', 'class' => 'weSelect']);
			$versions_time_weeks_tmpl->addOption(-1, '');
			$versions_time_weeks_tmpl->addOption(secondsWeek, g_l('prefs', '[1_week]'));
			for($x = 2; $x <= 52; $x++){
				$versions_time_weeks_tmpl->addOption(($x * secondsWeek), sprintf(g_l('prefs', '[more_weeks]'), $x));
			}
			$versions_time_weeks_tmpl->selectOption(get_value('VERSIONS_TIME_WEEKS_TMPL'));

			$versions_time_years_tmpl = new we_html_select(['name' => 'newconf[VERSIONS_TIME_YEARS_TMPL]', 'class' => 'weSelect']);
			$versions_time_years_tmpl->addOption(-1, '');
			$versions_time_years_tmpl->addOption(secondsYear, g_l('prefs', '[1_year]'));
			for($x = 2; $x <= 10; $x++){
				$versions_time_years_tmpl->addOption(($x * secondsYear), sprintf(g_l('prefs', '[more_years]'), $x));
			}
			$versions_time_years_tmpl->selectOption(get_value('VERSIONS_TIME_YEARS_TMPL'));
			$versions_anzahl_tmpl = we_html_tools::htmlTextInput('newconf[VERSIONS_ANZAHL_TMPL]', 24, get_value('VERSIONS_ANZAHL_TMPL'), 5, '', 'text', 50, 0, '');
			$versions_create_tmpl_publishing = we_html_forms::radiobutton(1, (get_value('VERSIONS_CREATE_TMPL') == 1), 'newconf[VERSIONS_CREATE_TMPL]', g_l('prefs', '[versions_create_tmpl_publishing]'), true, 'defaultfont', '', false, '');
			$versions_create_tmpl_always = we_html_forms::radiobutton(0, (get_value('VERSIONS_CREATE_TMPL') == 0), 'newconf[VERSIONS_CREATE_TMPL]', g_l('prefs', '[versions_create_tmpl_always]'), true, 'defaultfont', '', false, '');
			$versions_wizard = '<div style="float:left;">' . we_html_button::create_button('openVersionWizard', 'javascript:openVersionWizard()', '', 0, 0, '', '') . '</div>';

			return create_dialog('', [
				['headline' => g_l('prefs', '[ContentType]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_activate_text]'), we_html_tools::TYPE_HELP),
					'space' => we_html_multiIconBox::SPACE_BIG,
					'html' => $checkboxes
				],
				['html' => $versions_time_days->getHtml() . ' ' . $versions_time_weeks->getHtml() . ' ' . $versions_time_years->getHtml(),
					'space' => we_html_multiIconBox::SPACE_BIG,
					'headline' => g_l('prefs', '[versioning_time]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_time_text]'), we_html_tools::TYPE_HELP)
				],
				['headline' => g_l('prefs', '[versioning_anzahl]'),
					'html' => $versions_anzahl,
					'space' => we_html_multiIconBox::SPACE_BIG
				],
				['headline' => g_l('prefs', '[versioning_create]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_create_text]'), we_html_tools::TYPE_HELP, false, false),
					'html' => $versions_create_publishing . '<br/>' . $versions_create_always,
					'space' => we_html_multiIconBox::SPACE_BIG
				],
				['html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_templates_text]'), we_html_tools::TYPE_INFO, 470, false),
					'noline' => 1,
				],
				['html' => $versions_time_days_tmpl->getHtml() . ' ' . $versions_time_weeks_tmpl->getHtml() . ' ' . $versions_time_years_tmpl->getHtml(),
					'space' => we_html_multiIconBox::SPACE_BIG,
					'noline' => 1,
					'headline' => g_l('prefs', '[versioning_time]')
				],
				['headline' => g_l('prefs', '[versioning_anzahl]'),
					'html' => $versions_anzahl_tmpl,
					'noline' => 1,
					'space' => we_html_multiIconBox::SPACE_BIG
				],
				['headline' => g_l('prefs', '[versioning_create]'),
					'html' => $versions_create_tmpl_publishing . '<br/>' . $versions_create_tmpl_always,
					'space' => we_html_multiIconBox::SPACE_BIG
				],
				['headline' => g_l('prefs', '[versioning_wizard]') . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_wizard_text]'), we_html_tools::TYPE_HELP),
					'html' => $versions_wizard,
					'space' => we_html_multiIconBox::SPACE_BIG
				],
				], -1, '', '');
	}

	return 'No rights.';
}

/**
 * This functions renders the complete dialog.
 *
 * @return         string
 */
function render_dialog(){
	// Check configuration file for all needed variables => since included in startup, nothing should change
	we_base_preferences::check_global_config();
	$tabs = array_keys($GLOBALS['tabs']);
	$tabs[] = 'save';
	$ret = '';

	foreach($tabs as $tab){
		$ret .= we_html_element::htmlDiv(['id' => 'setting_' . $tab, 'style' => ($GLOBALS['tabname'] === 'setting_' . $tab ? '' : 'display: none;')], build_dialog($tab));
	}
	return $ret;
}

function getYesNoSelect($setting){
	$select = new we_html_select(['name' => 'newconf[' . $setting . ']', 'class' => 'weSelect']);
	$select->addOption(0, g_l('prefs', '[no]'));
	$select->addOption(1, g_l('prefs', '[yes]'));
	$select->selectOption(get_value($setting) ? 1 : 0);

	return $select->getHtml();
}

function getTrueFalseSelect($setting){
	$select = new we_html_select(['name' => 'newconf[' . $setting . ']', 'class' => 'weSelect']);
	$select->addOption(0, 'false');
	$select->addOption(1, 'true');
	$select->selectOption(get_value($setting) ? 1 : 0);

	return $select->getHtml();
}

echo we_html_tools::getHtmlTop();

$doSave = false;
$acError = false;
$acErrorMsg = '';
// Check if we need to save settings
if(we_base_request::_(we_base_request::BOOL, 'save_settings')){
	$acQuery = new we_selector_query();

	// check seemode start document | object
	switch(we_base_request::_(we_base_request::STRING, 'seem_start_type')){
		case 'document':
			$doc = we_base_request::_(we_base_request::INT, 'seem_start_document', 0);
			if(!$doc){
				$acError = true;
				$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
			} else {
				$acResponse = $acQuery->getItemById($doc, FILE_TABLE, ['IsFolder']);
				if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
					$acError = true;
					$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
				}
			}
			break;
		case 'weapp':
			$app = we_base_request::_(we_base_request::STRING, 'newconf', '', 'seem_start_weapp');
			if(!$app){
				$acError = true;
				$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
			}
			break;
		case 'object':
			$obj = we_base_request::_(we_base_request::INT, 'seem_start_object', 0);
			if(!$obj){
				$acError = true;
				$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
			} else {
				$acResponse = $acQuery->getItemById($obj, OBJECT_FILES_TABLE, ['IsFolder']);
				if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
					$acError = true;
					$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
				}
			}
			break;
	}
	// check sidebar document
	if(!we_base_request::_(we_base_request::BOOL, 'newconf', false, 'SIDEBAR_DISABLED') && we_base_request::_(we_base_request::FILE, 'ui_sidebar_file_name')){
		$acResponse = $acQuery->getItemById(we_base_request::_(we_base_request::INT, 'newconf', 0, 'SIDEBAR_DEFAULT_DOCUMENT'), FILE_TABLE, ['IsFolder']);
		if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[sidebar]') . ' / ' . g_l('prefs', '[sidebar_document]'), g_l('prefs', '[tab][ui]')) . "\\n";
		}
	}
	// check doc for error on none existing objects
	if(we_base_request::_(we_base_request::FILE, 'error_document_no_objectfile_text')){
		$acResponse = $acQuery->getItemById(we_base_request::_(we_base_request::INT, 'newconf', 0, 'ERROR_DOCUMENT_NO_OBJECTFILE'), FILE_TABLE, ['IsFolder']);
		if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[error_no_object_found]'), g_l('prefs', '[tab][error_handling]')) . "\\n";
		}
	}
	// check if versioning number is correct
	if(($cnt = we_base_request::_(we_base_request::INT, 'newconf', 0, 'VERSIONS_ANZAHL'))){
		if(!(abs($cnt) == $cnt && $cnt > 0)){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[versioning_anzahl]'), g_l('prefs', '[tab][versions]')) . "\\n";
		}
	}
	$doSave = true;
}

echo we_html_element::jsScript(JS_DIR . 'preferences.js');
if($doSave && !$acError){
	save_all_values();

	echo
	we_html_element::jsElement('
function doCloseDyn() {
	var _multiEditorreload = false;
' . $save_javascript .
		(!$email_saved ? we_message_reporting::getShowMessageCall(g_l('prefs', '[error_mail_not_saved]'), we_message_reporting::WE_MESSAGE_ERROR) : we_message_reporting::getShowMessageCall(g_l('prefs', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE)) . '
	}
	') .
	'</head>' .
	we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'doClose()'], build_dialog('saved')) . '</html>';
} else {
	$form = we_html_element::htmlForm(['onSubmit' => 'return false;', 'name' => 'we_form', 'method' => 'post', 'action' => $_SERVER['SCRIPT_NAME']], we_html_element::htmlHidden('save_settings', 0) . render_dialog());


	echo ($acError ?
		we_message_reporting::jsMessagePush(g_l('alert', '[field_in_tab_notvalid_pre]') . "\\n\\n" . $acErrorMsg . "\\n" . g_l('alert', '[field_in_tab_notvalid_post]'), we_message_reporting::WE_MESSAGE_ERROR) : '') .
	'</head>' .
	we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'startPrefs();'], $form) .
	'</html>';
}
