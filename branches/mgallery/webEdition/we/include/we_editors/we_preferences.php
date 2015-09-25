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

$yuiSuggest = &weSuggest::getInstance();

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
	$_output = ($expand != -1 ? we_html_multiIconBox::getJS() : '');

	// Return HTML code of dialog
	return $_output . we_html_multiIconBox::getHTML($name, $content, 30, '', $expand, $show_text, $hide_text);
}

function getColorInput($name, $value, $disabled = false, $width = 20, $height = 20){
	return we_html_tools::hidden($name, $value) . '<table class="default" style="border:1px solid grey;margin:2px 0px;"><tr><td' .
		($disabled ? ' class="disabled"' : '') .
		' id="color_' . $name . '" ' .
		($value ? (' style="background-color:' . $value . ';"') : '') .
		'><a style="cursor:' .
		($disabled ? "default" : "pointer") .
		';" href="javascript:if(document.getElementById(&quot;color_' . $name . '&quot;).getAttribute(&quot;class&quot;)!=&quot;disabled&quot;) {we_cmd(\'openColorChooser\',\'' . $name . '\',document.we_form.elements[\'' . $name . '\'].value,&quot;opener.setColorField(\'' . $name . '\');&quot;);}"><span style="width:' . $width . 'px;height:' . $height . '"></span></a></td></tr></table>';
}

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
			we_loadLanguageConfig();
			return getWeFrontendLanguagesForBackend();

		case 'locale_default':
			we_loadLanguageConfig();
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
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
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
				$_generate_java_script = ($_SESSION['prefs']['weWidth'] != $settingvalue);

				$_SESSION['prefs']['weWidth'] = $settingvalue;

				if($_generate_java_script){
					$height = we_base_request::_(we_base_request::INT, 'newconf', 0, "weHeight");
					$GLOBALS['save_javascript'] .= "
parent.opener.top.resizeTo(" . $settingvalue . ", " . $height . ");
parent.opener.top.moveTo((screen.width / 2) - " . ($settingvalue / 2) . ", (screen.height / 2) - " . ($height / 2) . ");";
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
	var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
				_usedEditors[frameId].getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_CONTENT . ' ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);

				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
}
_multiEditorreload = true;';
			}
			return;

		case 'editorCodecompletion':
			$_SESSION['prefs'][$settingname] = is_array($settingvalue) ? we_serialize($settingvalue, 'json') : '';
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
	var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
		for (frameId in _usedEditors) {

			if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
				_usedEditors[frameId].getEditorEditPageNr() == ' . we_base_constants::WE_EDITPAGE_CONTENT . ' ) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);

				} else {
					_usedEditors[frameId].setEditorReloadNeeded(true);
				}
			}
		}
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
// reload current document => reload all open Editors on demand
var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
for (frameId in _usedEditors) {

	if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
		_usedEditors[frameId].setEditorIsActive(true);

	} else {
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
	}
}
_multiEditorreload = true;";
			}

		case 'locale_locales':
			return;
		case 'locale_default':
			if(($loc = we_base_request::_(we_base_request::STRING, 'newconf', '', 'locale_locales')) && ($def = we_base_request::_(we_base_request::STRING, 'newconf', '', 'locale_default'))){
				we_writeLanguageConfig($def, explode(',', $loc));
			}
			return;

		case 'WE_COUNTRIES_TOP':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, implode(',', array_keys(we_base_request::_(we_base_request::INT, 'newconf', 0, 'countries'), 2)), true, $comment);
			return;

		case 'WE_COUNTRIES_SHOWN':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, implode(',', array_keys(we_base_request::_(we_base_request::INT, 'newconf', 0, 'countries'), 1)), true, $comment);
			return;
		case 'SYSTEM_WE_SESSION_TIME':
			//check, that a session lasts at least 90 seconds, due to we-pings, this is sufficient for WE - other is up to the user.
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, max($settingvalue, 90), true, $comment);
			return;

		case 'WE_SEEM':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			if(intval($settingvalue) == constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, ($settingvalue == 1 ? 0 : 1), true, $comment);
			}
			return;

		case 'WE_LOGIN_HIDEWESTATUS':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			if($settingvalue != constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;
		case 'WE_LOGIN_WEWINDOW':
			if(constant($settingname) != $settingvalue){
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'SIDEBAR_DISABLED':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			if($settingvalue != SIDEBAR_DISABLED){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'SIDEBAR_DISABLED', $settingvalue, true, $comment);
			}

			$_sidebar_show_on_startup = we_base_request::_(we_base_request::BOOL, 'newconf', false, 'SIDEBAR_SHOW_ON_STARTUP');
			if(SIDEBAR_SHOW_ON_STARTUP != $_sidebar_show_on_startup){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'newconf[SIDEBAR_SHOW_ON_STARTUP]', $_sidebar_show_on_startup);
			}

			$_sidebar_document = we_base_request::_(we_base_request::INT, 'newconf', 0, 'SIDEBAR_DEFAULT_DOCUMENT');
			if(SIDEBAR_DEFAULT_DOCUMENT != $_sidebar_document){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'newconf[SIDEBAR_DEFAULT_DOCUMENT]', $_sidebar_document);
			}

			$_sidebar_width = we_base_request::_(we_base_request::INT, 'newconf', 0, 'SIDEBAR_DEFAULT_WIDTH');
			if(SIDEBAR_DEFAULT_WIDTH != $_sidebar_width){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'newconf[SIDEBAR_DEFAULT_WIDTH]', $_sidebar_width);
			}

			return;

		case 'DEFAULT_STATIC_EXT':
		case 'DEFAULT_DYNAMIC_EXT':
		case 'DEFAULT_HTML_EXT':
			if(constant($settingname) != $settingvalue){
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		//FORMMAIL RECIPIENTS
		case 'formmail_values':
			if($settingvalue){
				$_recipients = explode('<##>', $settingvalue);
				if($_recipients){
					foreach($_recipients as $_recipient){
						$_single_recipient = explode('<#>', $_recipient);

						if(isset($_single_recipient[0]) && ($_single_recipient[0] === '#')){
							if(!empty($_single_recipient[1])){
								$DB_WE->query('INSERT INTO ' . RECIPIENTS_TABLE . ' (Email) VALUES("' . $DB_WE->escape($_single_recipient[1]) . '")');
							}
						} else {
							if(!empty($_single_recipient[1]) && !empty($_single_recipient[0])){
								$DB_WE->query('UPDATE ' . RECIPIENTS_TABLE . ' SET Email="' . $DB_WE->escape($_single_recipient[1]) . '" WHERE ID=' . intval($_single_recipient[0]));
							}
						}
					}
				}
			}

			return;

		case 'formmail_deleted':
			if($settingvalue){
				$_formmail_deleted = explode(',', $settingvalue);
				foreach($_formmail_deleted as $del){
					$DB_WE->query('DELETE FROM ' . RECIPIENTS_TABLE . ' WHERE ID=' . intval($del));
				}
			}
			return;

		case 'active_integrated_modules':
			$GLOBALS['config_files']['conf_active_integrated_modules']['content'] = '<?php
$GLOBALS[\'_we_active_integrated_modules\'] = array(
\'' . implode("',\n'", we_base_request::_(we_base_request::STRING, 'newconf', array(), 'active_integrated_modules')) . '\'
);';
			return;

		case 'useproxy':
			if($settingvalue == 1){
				// Create/overwrite proxy settings file
				$host = we_base_request::_(we_base_request::STRING, 'newconf', '', "proxyhost");
				$port = we_base_request::_(we_base_request::INT, 'newconf', '', "proxyport");
				$user = we_base_request::_(we_base_request::STRING, 'newconf', '', "proxyuser");
				$pass = we_base_request::_(we_base_request::RAW_CHECKED, 'newconf', '', "proxypass");
				we_base_preferences::setConfigContent('proxysettings', '<?php
	define(\'WE_PROXYHOST\', "' . $host . '");
	define(\'WE_PROXYPORT\', ' . $port . ');
	define(\'WE_PROXYUSER\', "' . $user . '");
	define(\'WE_PROXYPASSWORD\', "' . str_replace('"', '', $pass) . '");'
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

		// ADVANCED
		case 'DB_CONNECT':
			$_file = &$GLOBALS['config_files']['conf_conf']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue);
			return;

		case 'DB_SET_CHARSET':
			$_file = &$GLOBALS['config_files']['conf_conf']['content'];

			if(!defined($settingname) || $settingvalue != constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'useauth':
			$_file = &$GLOBALS['config_files']['conf_conf']['content'];
			if($settingvalue == 1){
				// enable
				if(!(defined('HTTP_USERNAME')) || !(defined('HTTP_PASSWORD'))){
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_USERNAME', 'myUsername', false);
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_PASSWORD', 'myPassword', false);
				}

				$un = defined('HTTP_USERNAME') ? HTTP_USERNAME : '';
				$pw = defined('HTTP_PASSWORD') ? HTTP_PASSWORD : '';
				$un1 = we_base_request::_(we_base_request::STRING, 'newconf', '', 'HTTP_USERNAME');
				$pw1 = we_base_request::_(we_base_request::STRING, 'newconf', '', 'HTTP_PASSWORD');
				if($un != $un1 || $pw != $pw1){

					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_USERNAME', $un1);
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_PASSWORD', $pw1);
				}
			} else {
				// disable
				if(defined('HTTP_USERNAME') || defined('HTTP_PASSWORD')){
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_USERNAME', 'myUsername', false);
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_PASSWORD', 'myPassword', false);
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
			$_file = &$GLOBALS['config_files']['conf_global']['content'];

			if($settingvalue != constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'WE_ERROR_MAIL':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];

			if($settingvalue == 0 && WE_ERROR_MAIL == 1){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'WE_ERROR_MAIL', 0, true, $comment);
			} else if($settingvalue == 1 && WE_ERROR_MAIL == 0){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'WE_ERROR_MAIL', 1, true, $comment);
			}

			return;

		case 'WE_ERROR_MAIL_ADDRESS':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];

			if(WE_ERROR_MAIL_ADDRESS != $settingvalue){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'WE_ERROR_MAIL_ADDRESS', $settingvalue, true, $comment);
			}
			return;

		case 'ERROR_DOCUMENT_NO_OBJECTFILE':
			if(!defined($settingname) || constant($settingname) != $settingvalue){
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
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
		$_SESSION['weS']['versions']['logPrefsChanged'] = array();
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
	$yuiSuggest = & weSuggest::getInstance();
	$trueFalseArray = array('true' => 'true', 'false' => 'false');

	switch($selected_setting){
		case 'save':

			return create_dialog('', /* g_l('prefs', '[save_wait]'), */ array(
				array('headline' => '', 'html' => g_l('prefs', '[save]'), 'space' => 0)
			));

		case 'saved'://SAVED SUCCESSFULLY DIALOG
			return create_dialog('', /* g_l('prefs', '[saved_successfully]'), */ array(
				array('headline' => '', 'html' => g_l('prefs', '[saved]'), 'space' => 0)
			));

		case 'ui':
			//LANGUAGE
			$_settings = array();

			//	Look which languages are installed ...
			$_language_directory = dir(WE_INCLUDES_PATH . 'we_language');

			while(false !== ($entry = $_language_directory->read())){
				if($entry != '.' && $entry != '..'){
					if(is_dir(WE_INCLUDES_PATH . 'we_language/' . $entry)){
						$_language[$entry] = $entry;
					}
				}
			}
			global $_languages;

			if(!empty($_language)){ // Build language select box
				$_languages = new we_html_select(array('name' => 'newconf[Language]', 'class' => 'weSelect', 'onchange' => "document.getElementById('langnote').style.display='block'"));
				foreach($_language as $key => $value){
					$_languages->addOption($key, $value);
				}
				$_languages->selectOption(get_value('Language'));
				// Lang notice
				$langNote = '<div id="langnote" style="padding: 5px; background-color: rgb(221, 221, 221); width: 190px; display:none">
<table width="100%">
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
				$_settings[] = array('headline' => g_l('prefs', '[choose_language]'), 'html' => $_languages->getHtml() . '<br/><br/>' . $langNote, 'space' => 200, 'noline' => 1);
			} else { // Just one Language Installed, no select box needed
				foreach($_language as $key => $value){
					$_languages = $value;
				}
				// Build dialog
				$_settings[] = array('headline' => g_l('prefs', '[choose_language]'), 'html' => $_languages, 'space' => 200, 'noline' => 1);
			}

			$BackendCharset = new we_html_select(array('name' => 'newconf[BackendCharset]', 'class' => 'weSelect', 'onchange' => "document.getElementById('langnote').style.display='block'"));
			$c = we_base_charsetHandler::getAvailCharsets();
			foreach($c as $char){
				$BackendCharset->addOption($char, $char);
			}
			$BackendCharset->selectOption(get_value('BackendCharset'));
			$_settings[] = array('headline' => g_l('prefs', '[choose_backendcharset]'), 'html' => $BackendCharset->getHtml() . '<br/><br/>' . $langNote, 'space' => 200);


			// DEFAULT CHARSET
			if(we_base_preferences::userIsAllowed('DEFAULT_CHARSET')){
				$_charsetHandler = new we_base_charsetHandler();
				$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
				$charset = $GLOBALS['WE_BACKENDCHARSET'];
				$GLOBALS['weDefaultCharset'] = get_value('DEFAULT_CHARSET');
				$_defaultCharset = we_html_tools::htmlTextInput('newconf[DEFAULT_CHARSET]', 8, $GLOBALS['weDefaultCharset'], 255, '', 'text', 100);
				$_defaultCharsetChooser = we_html_tools::htmlSelect('DefaultCharsetSelect', $_charsets, 1, $GLOBALS['weDefaultCharset'], false, array("onchange" => "document.forms[0].elements['newconf[DEFAULT_CHARSET]'].value=this.options[this.selectedIndex].value;"), "value", 100, "defaultfont", false);
				$DEFAULT_CHARSET = '<table class="default"><tr><td>' . $_defaultCharset . '</td><td>' . $_defaultCharsetChooser . '</td></tr></table>';

				$_settings[] = array(
					'headline' => g_l('prefs', '[default_charset]'),
					'space' => 200,
					'html' => $DEFAULT_CHARSET
				);
			}

			//AMOUNT COLUMNS IN COCKPIT
			$cockpit_amount_columns = new we_html_select(array('name' => 'newconf[cockpit_amount_columns]', 'class' => 'weSelect'));
			for($i = 1; $i <= 10; $i++){
				$cockpit_amount_columns->addOption($i, $i);
			}
			$cockpit_amount_columns->selectOption(get_value('cockpit_amount_columns'));
			$_settings[] = array('headline' => g_l('prefs', '[cockpit_amount_columns]'), 'html' => $cockpit_amount_columns->getHtml(), 'space' => 200);

			/*			 * ***************************************************************
			 * SEEM
			 * *************************************************************** */

			if(we_base_preferences::userIsAllowed('WE_SEEM')){
				// Build maximize window
				$_seem_disabler = we_html_forms::checkbox(1, get_value('WE_SEEM') == 0 ? 1 : 0, 'newconf[WE_SEEM]', g_l('prefs', '[seem_deactivate]'));

				// Build dialog if user has permission
				$_settings[] = array('headline' => g_l('prefs', '[seem]'), 'html' => $_seem_disabler, 'space' => 200);
			}

			// SEEM start document
			if(we_base_preferences::userIsAllowed('seem_start_type')){

				// Cockpit
				$_document_path = $_object_path = '';
				$_document_id = $_object_id = 0;

				switch(get_value('seem_start_type')){
					default:
						$_seem_start_type = 0;
						break;
					case 'cockpit':
						$_SESSION['prefs']['seem_start_file'] = 0;
						$_SESSION['prefs']['seem_start_weapp'] = '';
						$_seem_start_type = 'cockpit';
						break;

					// Object
					case 'object':
						$_seem_start_type = 'object';
						if(get_value('seem_start_file') != 0){
							$_object_id = get_value('seem_start_file');
							$_get_object_paths = getPathsFromTable(OBJECT_FILES_TABLE, null, we_base_constants::FILE_ONLY, $_object_id);

							if(isset($_get_object_paths[$_object_id])){ //	seeMode start file exists
								$_object_path = $_get_object_paths[$_object_id];
							}
						}
						break;
					case 'weapp':
						$_seem_start_type = 'weapp';
						if(get_value('seem_start_weapp') != ''){
							$_seem_start_weapp = get_value('seem_start_weapp');
						}

						break;
					// Document
					case 'document':
						$_seem_start_type = 'document';
						if(get_value('seem_start_file') != 0){
							$_document_id = get_value('seem_start_file');
							$_get_document_paths = getPathsFromTable(FILE_TABLE, null, we_base_constants::FILE_ONLY, $_document_id);

							if(isset($_get_document_paths[$_document_id])){ //	seeMode start file exists
								$_document_path = $_get_document_paths[$_document_id];
							}
						}
						break;
				}

				$_start_type = new we_html_select(array('name' => 'newconf[seem_start_type]', 'class' => 'weSelect', 'id' => 'seem_start_type', 'onchange' => "show_seem_chooser(this.value);"));

				$showStartType = false;
				$permitedStartTypes = array('');
				$_start_type->addOption(0, '-');
				$_seem_cockpit_selectordummy = "<div id='selectordummy' style='height:24px;'>&nbsp;</div>";
				if(permissionhandler::hasPerm('CAN_SEE_QUICKSTART')){
					$_start_type->addOption('cockpit', g_l('prefs', '[seem_start_type_cockpit]'));
					$showStartType = true;
					$permitedStartTypes[] = 'cockpit';
				}

				$_seem_document_chooser = '';
				if(permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
					$_start_type->addOption('document', g_l('prefs', '[seem_start_type_document]'));
					$showStartType = true;
					// Build SEEM select start document chooser

					$yuiSuggest->setAcId('Doc');
					$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::HTML, we_base_ContentTypes::IMAGE)));
					$yuiSuggest->setInput('seem_start_document_name', $_document_path, '', get_value('seem_start_file'));
					$yuiSuggest->setMaxResults(20);
					$yuiSuggest->setMayBeEmpty(false);
					$yuiSuggest->setResult('seem_start_document', $_document_id);
					$yuiSuggest->setSelector(weSuggest::DocSelector);
					$yuiSuggest->setWidth(150);
					$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:select_seem_start()', true, 100, 22, '', '', false, false), 10);
					$yuiSuggest->setContainerWidth(259);

					$_seem_document_chooser = we_html_element::htmlSpan(array('id' => 'seem_start_document', 'style' => 'display:none'), $yuiSuggest->getHTML());
					$permitedStartTypes[] = 'document';
				}
				$_seem_object_chooser = '';
				if(defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
					$_start_type->addOption('object', g_l('prefs', '[seem_start_type_object]'));
					$showStartType = true;
					// Build SEEM select start object chooser

					$yuiSuggest->setAcId('Obj');
					$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::OBJECT_FILE);
					$yuiSuggest->setInput('seem_start_object_name', $_object_path, '', get_value('seem_start_file'));
					$yuiSuggest->setMaxResults(20);
					$yuiSuggest->setMayBeEmpty(false);
					$yuiSuggest->setResult('seem_start_object', $_object_id);
					$yuiSuggest->setSelector(weSuggest::DocSelector);
					$yuiSuggest->setTable(OBJECT_FILES_TABLE);
					$yuiSuggest->setWidth(150);
					$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, 'javascript:select_seem_start()', true, 100, 22, '', '', false, false), 10);
					$yuiSuggest->setContainerWidth(259);

					$_seem_object_chooser = we_html_element::htmlSpan(array('id' => 'seem_start_object', 'style' => 'display:none'), $yuiSuggest->getHTML());
					$permitedStartTypes[] = 'object';
				}
				$_start_weapp = new we_html_select(array('name' => 'newconf[seem_start_weapp]', 'class' => 'weSelect', 'id' => 'seem_start_weapp'));
				$_tools = we_tool_lookup::getAllTools(true, false);
				foreach($_tools as $_tool){
					if(!$_tool['appdisabled'] && permissionhandler::hasPerm($_tool['startpermission'])){
						$_start_weapp->addOption($_tool['name'], $_tool['text']);
					}
				}
				$_seem_weapp_chooser = '';
				if($_start_weapp->getOptionNum()){
					$_start_type->addOption('weapp', g_l('prefs', '[seem_start_type_weapp]'));
					if(!empty($_seem_start_weapp)){
						$_start_weapp->selectOption($_seem_start_weapp);
					}
					$weAPPSelector = $_start_weapp->getHtml();
					$_seem_weapp_chooser = we_html_element::htmlSpan(array('id' => 'seem_start_weapp', 'style' => 'display:none'), $weAPPSelector);
					$permitedStartTypes[] = 'weapp';
				}

				// Build final HTML code
				if($showStartType){
					if(in_array($_seem_start_type, $permitedStartTypes)){
						$_start_type->selectOption($_seem_start_type);
					} else {
						$_seem_start_type = $permitedStartTypes[0];
					}
					$_seem_html = new we_html_table(array('class' => 'default'), 2, 1);
					$_seem_html->setCol(0, 0, array('class' => 'defaultfont'), $_start_type->getHtml());
					$_seem_html->setCol(1, 0, array('style' => 'padding-top:5px;'), $_seem_cockpit_selectordummy . $_seem_document_chooser . $_seem_object_chooser . $_seem_weapp_chooser);
					$_settings[] = array('headline' => g_l('prefs', '[seem_startdocument]'), 'html' => $_seem_html->getHtml() . we_html_element::jsElement('show_seem_chooser("' . $_seem_start_type . '");'), "space" => 200);
				}

				// Build dialog if user has permission
			}

			/*			 * *******************************************************
			 * Sidebar
			 * ******************************************************* */
			if(we_base_preferences::userIsAllowed('SIDEBAR_DISABLED')){
				// Settings
				$_sidebar_disable = get_value('SIDEBAR_DISABLED');
				$_sidebar_show = ($_sidebar_disable) ? 'none' : 'block';

				$_sidebar_id = get_value('SIDEBAR_DEFAULT_DOCUMENT');
				$_sidebar_paths = getPathsFromTable(FILE_TABLE, null, we_base_constants::FILE_ONLY, $_sidebar_id);
				$_sidebar_path = '';
				if(isset($_sidebar_paths[$_sidebar_id])){
					$_sidebar_path = $_sidebar_paths[$_sidebar_id];
				}

				// Enable / disable sidebar
				$_sidebar_disabler = we_html_forms::checkbox(1, $_sidebar_disable, 'newconf[SIDEBAR_DISABLED]', g_l('prefs', '[sidebar_deactivate]'), false, 'defaultfont', "document.getElementById('sidebar_options').style.display=(this.checked?'none':'block');");

				// Show on Startup
				$_sidebar_show_on_startup = we_html_forms::checkbox(1, get_value('SIDEBAR_SHOW_ON_STARTUP'), 'newconf[SIDEBAR_SHOW_ON_STARTUP]', g_l('prefs', '[sidebar_show_on_startup]'), false, 'defaultfont', '');

				// Sidebar width
				$_sidebar_width = we_html_tools::htmlTextInput('newconf[SIDEBAR_DEFAULT_WIDTH]', 8, get_value('SIDEBAR_DEFAULT_WIDTH'), 255, "onchange=\"if ( isNaN( this.value ) ||  parseInt(this.value) < 100 ) { this.value=100; };\"", 'number', 150);
				$_sidebar_width_chooser = we_html_tools::htmlSelect('tmp_sidebar_width', array('' => '', 100 => 100, 150 => 150, 200 => 200, 250 => 250, 300 => 300, 350 => 350, 400 => 400), 1, '', false, array("onchange" => "document.forms[0].elements['newconf[SIDEBAR_DEFAULT_WIDTH]'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;"), "value", 100, "defaultfont");

				// Sidebar document
				$_sidebar_document_button = we_html_button::create_button(we_html_button::SELECT, 'javascript:selectSidebarDoc()');

				$yuiSuggest->setAcId('SidebarDoc');
				$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT);
				$yuiSuggest->setInput('ui_sidebar_file_name', $_sidebar_path);
				$yuiSuggest->setMaxResults(20);
				$yuiSuggest->setMayBeEmpty(true);
				$yuiSuggest->setResult('newconf[SIDEBAR_DEFAULT_DOCUMENT]', $_sidebar_id);
				$yuiSuggest->setSelector(weSuggest::DocSelector);
				$yuiSuggest->setWidth(150);
				$yuiSuggest->setSelectButton($_sidebar_document_button, 10);
				$yuiSuggest->setContainerWidth(259);

				// build html
				$_sidebar_html1 = new we_html_table(array('class' => 'default'), 1, 1);

				$_sidebar_html1->setCol(0, 0, null, $_sidebar_disabler);

				// build html
				$_sidebar_html2 = new we_html_table(array('class' => 'default', 'id' => 'sidebar_options', 'style' => 'display:' . $_sidebar_show), 8, 2);

				$_sidebar_html2->setCol(0, 0, array('colspan' => 3, 'height' => 10), '');
				$_sidebar_html2->setCol(1, 0, array('colspan' => 3, 'height' => 10), $_sidebar_show_on_startup);
				$_sidebar_html2->setCol(2, 0, array('colspan' => 3, 'height' => 10), '');
				$_sidebar_html2->setCol(3, 0, array('colspan' => 3, 'class' => 'defaultfont'), g_l('prefs', '[sidebar_width]'));
				$_sidebar_html2->setCol(4, 0, null, $_sidebar_width);
				$_sidebar_html2->setCol(4, 1, array('style' => 'padding-left:10px;'), $_sidebar_width_chooser);
				$_sidebar_html2->setCol(5, 0, array('colspan' => 3, 'height' => 10), '');
				$_sidebar_html2->setCol(6, 0, array('colspan' => 3, 'class' => 'defaultfont'), g_l('prefs', '[sidebar_document]'));
				$_sidebar_html2->setCol(7, 0, array('colspan' => 3), $yuiSuggest->getHTML());

				// Build dialog if user has permission
				$_settings[] = array('headline' => g_l('prefs', '[sidebar]'), 'html' => $_sidebar_html1->getHtml() . $_sidebar_html2->getHtml(), 'space' => 200);
			}


			// TREE

			$_tree_count = get_value('default_tree_count');
			$_file_tree_count = new we_html_select(array('name' => 'newconf[default_tree_count]', 'class' => 'weSelect'));
			$_file_tree_count->addOption(0, g_l('prefs', '[all]'));


			for($i = 10; $i < 51; $i+=10){
				$_file_tree_count->addOption($i, $i);
			}

			for($i = 100; $i < 501; $i+=100){
				$_file_tree_count->addOption($i, $i);
			}

			if(!$_file_tree_count->selectOption($_tree_count)){
				$_file_tree_count->addOption($_tree_count, $_tree_count);
				// Set selected extension
				$_file_tree_count->selectOption($_tree_count);
			}

			$_settings[] = array('headline' => g_l('prefs', '[tree_title]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[tree_count_description]'), we_html_tools::TYPE_INFO, 450) . '<br/>' . $_file_tree_count->getHtml(), 'space' => 200);


			//WINDOW DIMENSIONS

			if(get_value('sizeOpt') == 0){
				$_window_specify = false;
				$_window_max = true;
			} else {
				$_window_specify = true;
				$_window_max = false;
			}

			// Build maximize window
			$_window_max_code = we_html_forms::radiobutton(0, get_value('sizeOpt') == 0, 'newconf[sizeOpt]', g_l('prefs', '[maximize]'), true, 'defaultfont', "document.getElementsByName('newconf[weWidth]')[0].disabled = true;document.getElementsByName('newconf[weHeight]')[0].disabled = true;");

			// Build specify window dimension
			$_window_specify_code = we_html_forms::radiobutton(1, !(get_value('sizeOpt') == 0), 'newconf[sizeOpt]', g_l('prefs', '[specify]'), true, 'defaultfont', "document.getElementsByName('newconf[weWidth]')[0].disabled = false;document.getElementsByName('newconf[weHeight]')[0].disabled = false;");

			// Create specify window dimension input
			$_window_specify_table = new we_html_table(array('class' => 'default', 'style' => 'margin-top:10px;margin-left:50px;'), 4, 4);

			$_window_specify_table->setCol(0, 0, array('class' => 'defaultfont'), g_l('prefs', '[width]') . ':');
			$_window_specify_table->setCol(1, 0, array('class' => 'defaultfont'), g_l('prefs', '[height]') . ':');

			$_window_specify_table->setCol(0, 1, null, we_html_tools::htmlTextInput('newconf[weWidth]', 6, (get_value('sizeOpt') ? get_value('weWidth') : ''), 4, (get_value('sizeOpt') == 0 ? 'disabled="disabled"' : ""), "number", 60));
			$_window_specify_table->setCol(1, 1, null, we_html_tools::htmlTextInput('newconf[weHeight]', 6, (get_value('sizeOpt') ? get_value('weHeight') : ''), 4, (get_value('sizeOpt') == 0 ? 'disabled="disabled"' : ""), "number", 60));

			// Build apply current window dimension
			$_window_current_dimension_table = we_html_button::create_button('apply_current_dimension', "javascript:document.getElementsByName('newconf[sizeOpt]')[1].checked = true;document.getElementsByName('newconf[weWidth]')[0].disabled = false;document.getElementsByName('newconf[weHeight]')[0].disabled = false;document.getElementsByName('newconf[weWidth]')[0].value = parent.opener.top.window.outerWidth;document.getElementsByName('newconf[weHeight]')[0].value = parent.opener.top.window.outerHeight;", true);

			// Build final HTML code
			$_window_html = new we_html_table(array('class' => 'default'), 5, 1);
			$_window_html->setCol(0, 0, array('style' => 'padding-bttom:10px;'), $_window_max_code);
			$_window_html->setCol(2, 0, array('style' => 'padding-bttom:10px;'), $_window_specify_code . $_window_specify_table->getHtml());
			$_window_html->setCol(4, 0, array('style' => 'padding-left:50px;'), $_window_current_dimension_table);

			// Build dialog
			$_settings[] = array('headline' => g_l('prefs', '[dimension]'), 'html' => $_window_html->getHtml(), 'space' => 200);
			return create_dialog('', /* g_l('prefs', '[tab][ui]'), */ $_settings, -1);

		case 'defaultAttribs':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			/**
			 * inlineedit setting
			 */
			// Build select box

			$commands_default_tmp = we_html_tools::htmlSelect('tmp_commands', we_wysiwyg_editor::getEditorCommands(false), 1, "", false, array('onchange' => "var elem=document.getElementById('commands_default_id'); var txt = this.options[this.selectedIndex].text; if(elem.value.split(',').indexOf(txt)==-1){elem.value=(elem.value) ? (elem.value + ',' + txt) : txt;}this.selectedIndex=-1"));
			$COMMANDS_DEFAULT = we_html_tools::htmlTextInput('newconf[COMMANDS_DEFAULT]', 22, get_value('COMMANDS_DEFAULT'), '', 'id="commands_default_id"', 'text', 225, 0, '');

			$CSSAPPLYTO_DEFAULT = new we_html_select(array('name' => 'newconf[CSSAPPLYTO_DEFAULT]', 'class' => 'weSelect'));
			$CSSAPPLYTO_DEFAULT->addOption('all', 'all');
			$CSSAPPLYTO_DEFAULT->addOption('around', 'around');
			$CSSAPPLYTO_DEFAULT->addOption('wysiwyg', 'wysiwyg');
			$CSSAPPLYTO_DEFAULT->selectOption(get_value('CSSAPPLYTO_DEFAULT') ? : 'around');

			$wecmdenc1 = we_base_request::encCmd("document.forms[0].elements['newconf[IMAGESTARTID_DEFAULT]'].value");
			$wecmdenc2 = we_base_request::encCmd("document.forms[0].elements.imagestartid_default_text.value");
			$_acButton1 = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', document.forms[0].elements['newconf[IMAGESTARTID_DEFAULT]'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','', '" . we_base_ContentTypes::FOLDER . "', 1)");
			$_acButton2 = we_html_button::create_button(we_html_button::TRASH, 'javascript:document.forms[0].elements[\'newconf[IMAGESTARTID_DEFAULT]\'].value = 0;document.forms[0].elements.imagestartid_default_text.value = \'\'');

			$yuiSuggest->setAcId("doc2");
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput('imagestartid_default_text', (IMAGESTARTID_DEFAULT ? id_to_path(IMAGESTARTID_DEFAULT) : ''));
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('newconf[IMAGESTARTID_DEFAULT]', (IMAGESTARTID_DEFAULT ? : 0));
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setWidth(226);
			$yuiSuggest->setSelectButton($_acButton1, 10);
			$yuiSuggest->setTrashButton($_acButton2, 4);

			$_settings = array(
				array('headline' => g_l('prefs', '[default_php_setting]'), 'html' => getTrueFalseSelect('WE_PHP_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[xhtml_default]'), 'html' => getTrueFalseSelect('XHTML_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[inlineedit_default]'), 'html' => getTrueFalseSelect('INLINEEDIT_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[imagestartid_default]'), 'html' => $yuiSuggest->getHTML(), 'space' => 200),
				array('headline' => g_l('prefs', '[commands_default]'), 'html' => '<div>' . $commands_default_tmp . '</div><div style="margin-top:4px">' . $COMMANDS_DEFAULT . '</div>', 'space' => 200),
				array('headline' => g_l('prefs', '[removefirstparagraph_default]'), 'html' => getTrueFalseSelect('REMOVEFIRSTPARAGRAPH_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[showinputs_default]'), 'html' => getTrueFalseSelect('SHOWINPUTS_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[hidenameattribinweimg_default]'), 'html' => getYesNoSelect('HIDENAMEATTRIBINWEIMG_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[hidenameattribinweform_default]'), 'html' => getYesNoSelect('HIDENAMEATTRIBINWEFORM_DEFAULT'), 'space' => 200),
				array('headline' => g_l('prefs', '[replaceacronym]'), 'html' => getYesNoSelect('REPLACEACRONYM'), 'space' => 200),
				array('headline' => g_l('prefs', '[cssapplyto_default]'), 'html' => $CSSAPPLYTO_DEFAULT->getHtml(), 'space' => 200),
			);

			return create_dialog(''/* , 'we:tag Standards' g_l('prefs', '[tab][defaultAttribs]') */, $_settings, -1);

		case 'countries':
			if(!we_base_preferences::userIsAllowed('WE_COUNTRIES_DEFAULT')){
				break;
			}

			$_countries_default = we_html_tools::htmlTextInput('newconf[WE_COUNTRIES_DEFAULT]', 22, get_value('WE_COUNTRIES_DEFAULT'), '', '', 'text', 225);

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
			$tabC = new we_html_table(array('style' => 'border:1px solid black'), 1, 4);
			$i = 0;
			$tabC->setCol($i, 0, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_country]'));
			$tabC->setCol($i, 1, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_top]'));
			$tabC->setCol($i, 2, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_show]'));
			$tabC->setCol($i, 3, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_noshow]'));
			foreach($supported as $countrycode => $country){
				$i++;
				$tabC->addRow();
				$tabC->setCol($i, 0, array('class' => 'defaultfont'), CheckAndConvertISObackend($country));
				$tabC->setCol($i, 1, array('class' => 'defaultfont'), '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="2" ' . (in_array($countrycode, $countries_top) ? 'checked="checked"' : '') . ' > ');
				$tabC->setCol($i, 2, array('class' => 'defaultfont'), '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="1" ' . (in_array($countrycode, $countries_shown) ? 'checked="checked"' : '') . ' > ');
				$tabC->setCol($i, 3, array('class' => 'defaultfont'), '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="0" ' . (!in_array($countrycode, $countries_top) && !in_array($countrycode, $countries_shown) ? 'checked' : '') . ' > ');
			}

			$_settings = array(
				array('headline' => g_l('prefs', '[countries_headline]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[countries_information]'), we_html_tools::TYPE_INFO, 450, false), 'space' => 0, 'noline' => 1),
				array('headline' => g_l('prefs', '[countries_default]'), 'html' => $_countries_default, 'space' => 200, 'noline' => 1),
				array('headline' => '', 'html' => $tabC->getHtml(), 'space' => 0, 'noline' => 1),
			);
			// Build dialog element if user has permission
			return create_dialog(''/* , g_l('prefs', '[tab][countries]') */, $_settings);

		case 'language':
			if(!we_base_preferences::userIsAllowed('locale_default') && we_base_preferences::userIsAllowed('locale_locales')){
				break;
			}
			$default = get_value('locale_default');
			$locales = get_value('locale_locales');


			$postJs = we_html_element::jsElement('initLocale("' . $default . '");');

			$_hidden_fields = we_html_element::htmlHidden('newconf[locale_default]', $default, 'locale_default') .
				we_html_element::htmlHidden('newconf[locale_locales]', implode(',', array_keys($locales)), 'locale_locales');

			//Locales
			$_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'locale_temp_locales', 'size' => 10, 'id' => 'locale_temp_locales', 'style' => 'width: 340px'));
			$_select_box->addOptions($locales);

			$_enabled_buttons = (count($locales) > 0);


			// Create edit list
			$_editlist_table = new we_html_table(array('class' => 'default'), 1, 2);

			// Buttons
			$default = we_html_button::create_button('default', 'javascript:defaultLocale()', true, 100, 22, '', '', !$_enabled_buttons);
			$delete = we_html_button::create_button(we_html_button::DELETE, 'javascript:deleteLocale()', true, 100);

			$_editlist_table->setCol(0, 0, array('style' => 'padding-right:10px;'), $_hidden_fields . $_select_box->getHtml());
			$_editlist_table->setCol(0, 1, array('style' => 'vertical-align:top'), $default . $delete);

			// Add Locales
			// Languages
			$Languages = g_l('languages', '');
			$TopLanguages = array(
				'~de' => $Languages['de'],
				'~nl' => $Languages['nl'],
				'~en' => $Languages['en'],
				'~fi' => $Languages['fi'],
				'~fr' => $Languages['fr'],
				'~pl' => $Languages['pl'],
				'~ru' => $Languages['ru'],
				'~es' => $Languages['es'],
			);
			asort($Languages);
			asort($TopLanguages);
			$TopLanguages[''] = '---';
			$Languages = array_merge($TopLanguages, $Languages);

			$_languages = new we_html_select(array('name' => 'newconf[locale_language]', 'id' => 'locale_language', 'style' => 'width: 139px', 'class' => 'weSelect'));
			$_languages->addOptions($Languages);

			// Countries
			$Countries = g_l('countries', '');
			$TopCountries = array(
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
			);
			asort($Countries);
			asort($TopCountries);
			$TopCountries['~'] = '---';
			$Countries = array_merge(array('' => ''), $TopCountries, $Countries);

			$_countries = new we_html_select(array('name' => 'newconf[locale_country]', 'id' => 'locale_country', 'style' => 'width: 139px', 'class' => 'weSelect'));
			$_countries->addOptions($Countries);

			// Button
			$_add_button = we_html_button::create_button(we_html_button::ADD, 'javascript:addLocale()', true, 139);

			// Build final HTML code
			$_add_html = g_l('prefs', '[locale_languages]') . '<br />' .
				$_languages->getHtml() . '<br /><br />' .
				g_l('prefs', '[locale_countries]') . '<br />' .
				$_countries->getHtml() . '<br /><br />' .
				$_add_button;

			//Todo: remove: g_l('prefs', '[langlink_support_backlinks_information]'), g_l('prefs', '[langlink_support_backlinks]'),g_l('prefs', '[langlink_support_recursive_information]'),g_l('prefs', '[langlink_support_recursive]') g_l('prefs', '[langlink_abandoned_options]')
			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[locale_information]'), we_html_tools::TYPE_INFO, 450, false), 'space' => 0),
				array('headline' => '', 'html' => $_editlist_table->getHtml(), 'space' => 0),
				array('headline' => g_l('prefs', '[locale_add]'), 'html' => $_add_html, 'space' => 200),
				array('headline' => g_l('prefs', '[langlink_headline]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[langlink_information]'), we_html_tools::TYPE_INFO, 450, false), 'space' => 0, 'noline' => 1),
				array('headline' => g_l('prefs', '[langlink_support]'), 'html' => getTrueFalseSelect('LANGLINK_SUPPORT'), 'space' => 200, 'noline' => 1),
			);

			return create_dialog('', /* g_l('prefs', '[tab][language]'), */ $_settings) . $postJs;

		case 'extensions':
			//FILE EXTENSIONS
			if(!we_base_preferences::userIsAllowed('DEFAULT_HTML_EXT')){
				break;
			}

			// Get webEdition extensions
			$_we_extensions = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::WEDOCUMENT);

			// Build static webEdition extensions select box
			$_static_we_extensions = new we_html_select(array('name' => 'newconf[DEFAULT_STATIC_EXT]', 'class' => 'weSelect'));
			$_dynamic_we_extensions = new we_html_select(array('name' => 'newconf[DEFAULT_DYNAMIC_EXT]', 'class' => 'weSelect'));
			foreach($_we_extensions as $value){
				$_static_we_extensions->addOption($value, $value);
				$_dynamic_we_extensions->addOption($value, $value);
			}
			$_static_we_extensions->selectOption(get_value('DEFAULT_STATIC_EXT'));
			$_dynamic_we_extensions->selectOption(get_value('DEFAULT_DYNAMIC_EXT'));

			$_we_extensions_html = g_l('prefs', '[static]') . we_html_element::htmlBr() . $_static_we_extensions->getHtml() . we_html_element::htmlBr() . we_html_element::htmlBr() . g_l('prefs', '[dynamic]') . we_html_element::htmlBr() . $_dynamic_we_extensions->getHtml();

			// HTML extensions
			$_html_extensions = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::HTML);

			// Build static webEdition extensions select box
			$_static_html_extensions = new we_html_select(array('name' => 'newconf[DEFAULT_HTML_EXT]', 'class' => 'weSelect'));
			foreach($_html_extensions as $value){
				$_static_html_extensions->addOption($value, $value);
			}
			$_static_html_extensions->selectOption(get_value('DEFAULT_HTML_EXT'));

			$_html_extensions_html = g_l('prefs', '[html]') . '<br/>' . $_static_html_extensions->getHtml();

			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[extensions_information]'), we_html_tools::TYPE_INFO, 450, false), 'space' => 0),
				array('headline' => g_l('prefs', '[we_extensions]'), 'html' => $_we_extensions_html, 'space' => 200),
				array('headline' => g_l('prefs', '[html_extensions]'), 'html' => $_html_extensions_html, 'space' => 200, 'noline' => 1)
			);

			return create_dialog('', /* g_l('prefs', '[tab][extensions]'), */ $_settings);

		case 'editor':
			//EDITOR PLUGIN

			$_attr = ' class="defaultfont" style="width:150px;"';
			$_attr_dis = ' class="defaultfont" style="width:150px;color:grey;"';

			$_template_editor_mode = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorMode]', 'size' => 1, 'onchange' => 'displayEditorOptions(this.options[this.options.selectedIndex].value);'));
			$_template_editor_mode->addOption('textarea', g_l('prefs', '[editor_plaintext]'));
			$_template_editor_mode->addOption('codemirror2', g_l('prefs', '[editor_javascript2]'));
			//$_template_editor_mode->addOption('java', g_l('prefs', '[editor_java]'));
			$_template_editor_mode->selectOption(get_value('editorMode'));

			/**
			 * Editor font settings
			 */
			$_template_fonts = array(
				'Arial',
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
				'sans-serif');
			$_template_font_sizes = array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 22, 24, 32, 48, 72);

			$_template_editor_font_specify = (get_value('editorFontname') != '' && get_value('editorFontname') != 'none');
			$_template_editor_font_size_specify = (get_value('editorFontsize') != '' && get_value('editorFontsize') != -1);

			// Build specify font
			$_template_editor_font_specify_code = we_html_forms::checkbox(1, $_template_editor_font_specify, 'newconf[editorFont]', g_l('prefs', '[specify]'), true, 'defaultfont', "if (document.getElementsByName('newconf[editorFont]')[0].checked) { document.getElementsByName('newconf[editorFontname]')[0].disabled = false;document.getElementsByName('newconf[editorFontsize]')[0].disabled = false; } else { document.getElementsByName('newconf[editorFontname]')[0].disabled = true;document.getElementsByName('newconf[editorFontsize]')[0].disabled = true; }");

			$_template_editor_font_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorFontname]', 'size' => 1, 'style' => 'width: 135px;', ($_template_editor_font_specify ? 'enabled' : 'disabled') => ($_template_editor_font_specify ? 'enabled' : 'disabled')));

			/* 			$_colorsDisabled = true;

			  $_template_editor_fontcolor_selector = getColorInput('newconf[editorFontcolor]', get_value('editorFontcolor'), $_colorsDisabled);
			  $_template_editor_we_tag_fontcolor_selector = getColorInput('newconf[editorWeTagFontcolor]', get_value('editorWeTagFontcolor'), $_colorsDisabled);
			  $_template_editor_we_attribute_fontcolor_selector = getColorInput('newconf[editorWeAttributeFontcolor]', get_value('editorWeAttributeFontcolor'), $_colorsDisabled);
			  $_template_editor_html_tag_fontcolor_selector = getColorInput('newconf[editorHTMLTagFontcolor]', get_value('editorHTMLTagFontcolor'), $_colorsDisabled);
			  $_template_editor_html_attribute_fontcolor_selector = getColorInput('newconf[editorHTMLAttributeFontcolor]', get_value('editorHTMLAttributeFontcolor'), $_colorsDisabled);
			  $_template_editor_pi_tag_fontcolor_selector = getColorInput('newconf[editorPiTagFontcolor]', get_value('editorPiTagFontcolor'), $_colorsDisabled);
			  $_template_editor_comment_fontcolor_selector = getColorInput('newconf[editorCommentFontcolor]', get_value('editorCommentFontcolor'), $_colorsDisabled);
			 */
			foreach($_template_fonts as $font){
				$_template_editor_font_select_box->addOption($font, $font);
			}
			$_template_editor_font_select_box->selectOption($_template_editor_font_specify ? get_value('editorFontname') : 'Courier New');

			$_template_editor_font_sizes_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorFontsize]', 'size' => 1, 'style' => 'width: 135px;', ($_template_editor_font_size_specify ? 'enabled' : 'disabled') => ($_template_editor_font_size_specify ? 'enabled' : 'disabled')));
			foreach($_template_font_sizes as $key => $sz){
				$_template_editor_font_sizes_select_box->addOption($sz, $sz);
			}
			$_template_editor_font_sizes_select_box->selectOption($_template_editor_font_specify ? $_template_font_sizes[$key] : 11);

			$_template_editor_font_sizes_select_box->selectOption(get_value('editorFontsize'));


			$_template_editor_font_specify_table = '<table style="margin:0px 0px 20px 50px;" class="default">
	<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontname]') . '</td><td>' . $_template_editor_font_select_box->getHtml() . '</td></tr>
	<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontsize]') . '</td><td>' . $_template_editor_font_sizes_select_box->getHtml() . '</td></tr>
</table>';
			/*
			  $_template_editor_font_color_checkbox = we_html_forms::checkboxWithHidden(get_value('specify_jeditor_colors'), "newconf[specify_jeditor_colors]", g_l('prefs', '[editor_font_colors]'), false, "defaultfont", "setEditorColorsDisabled(!this.checked);");
			  $attr = ($_colorsDisabled ? $_attr_dis : $_attr);
			  $_template_editor_font_color_table = '<table id="editorColorTable" style="margin: 10px 0px 0px 50px;" class="default">
			  <tr><td id="label_editorFontcolor" ' . $attr . '>' . g_l('prefs', '[editor_normal_font_color]') . '</td><td>' . $_template_editor_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorWeTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_we_tag_font_color]') . '</td><td>' . $_template_editor_we_tag_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorWeAttributeFontcolor"' . $attr . '>' . g_l('prefs', '[editor_we_attribute_font_color]') . '</td><td>' . $_template_editor_we_attribute_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorHTMLTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_html_tag_font_color]') . '</td><td>' . $_template_editor_html_tag_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorHTMLAttributeFontcolor"' . $attr . '>' . g_l('prefs', '[editor_html_attribute_font_color]') . '</td><td>' . $_template_editor_html_attribute_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorPiTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_pi_tag_font_color]') . '</td><td>' . $_template_editor_pi_tag_fontcolor_selector . '</td></tr>
			  <tr><td id="label_editorCommentFontcolor"' . $attr . '>' . g_l('prefs', '[editor_comment_font_color]') . '</td><td>' . $_template_editor_comment_fontcolor_selector . '</td></tr>
			  </table>';
			 */
			$_template_editor_theme = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorTheme]', 'size' => 1));
			foreach(glob(WE_LIB_PATH . 'additional/CodeMirror/theme/*.css') as $filename){
				$theme = str_replace(array('.css', WE_LIB_PATH . 'additional/CodeMirror/theme/'), '', $filename);
				$_template_editor_theme->addOption($theme, $theme);
			}
			$_template_editor_theme->selectOption(get_value('editorTheme'));

			//Build activation of line numbers
			$_template_editor_linenumbers_code = we_html_forms::checkbox(1, get_value('editorLinenumbers'), 'newconf[editorLinenumbers]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');
			$_template_editor_highlightLine_code = we_html_forms::checkbox(1, get_value('editorHighlightCurrentLine'), 'newconf[editorHighlightCurrentLine]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');

			//Build activation of code completion
			$_template_editor_codecompletion_code = we_html_forms::checkbox(1, get_value('editorCodecompletion-WE'), 'editorCodecompletion0', 'WE-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][WE]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][WE]', get_value('editorCodecompletion-WE')) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlTag'), 'editorCodecompletion1', 'HTML-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlTag]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlTag]', get_value('editorCodecompletion-htmlTag')) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlDefAttr'), 'editorCodecompletion2', 'HTML-Default-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlDefAttr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlDefAttr]', get_value('editorCodecompletion-htmlDefAttr')) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlAttr'), 'editorCodecompletion3', 'HTML-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlAttr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlAttr]', get_value('editorCodecompletion-htmlAttr')) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-htmlJSAttr'), 'editorCodecompletion4', 'HTML-JS-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlJSAttr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlJSAttr]', get_value('editorCodecompletion-htmlJSAttr')) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-html5Tag'), 'editorCodecompletion5', 'HTML5-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][html5Tag]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][html5Tag]', get_value('editorCodecompletion-html5Tag')) .
				we_html_forms::checkbox(1, get_value('editorCodecompletion-html5Attr'), 'editorCodecompletion6', 'HTML5-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][html5Attr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][html5Attr]', get_value('editorCodecompletion-html5Attr'));


			$_template_editor_tabstop_code = we_html_forms::checkbox(1, get_value('editorShowTab'), 'editorShowTab', g_l('prefs', '[show]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorShowTab]\');') .
				we_html_tools::hidden('newconf[editorShowTab]', get_value('editorShowTab')) .
				'<table class="default">
				<tr><td class="defaultfont" style="width:200px;">' . g_l('prefs', '[editor_tabSize]') . '</td><td>' . we_html_tools::htmlTextInput("newconf[editorTabSize]", 2, get_value("editorTabSize"), 2, "", "number", 135) . '</td></tr>
			</table>';

			$_template_editor_Wrap_code = we_html_forms::checkbox(1, get_value('editorWrap'), 'editorWrap', g_l('prefs', '[editor_enable]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorWrap]\');') .
				we_html_tools::hidden('newconf[editorWrap]', get_value('editorWrap'));

			$_template_editor_autoIndent_code = we_html_forms::checkbox(1, get_value('editorAutoIndent'), 'editorAutoIndent', g_l('prefs', '[editor_enable]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorAutoIndent]\');') .
				we_html_tools::hidden('newconf[editorAutoIndent]', get_value('editorAutoIndent'));

			$_template_editor_tooltips_code = we_html_forms::checkbox(1, get_value('editorTooltips'), 'newconf[editorTooltips]', g_l('prefs', '[editorTooltips]'), true, 'defaultfont', '') .
				we_html_forms::checkbox(1, get_value('editorTooltipsIDs'), 'newconf[editorTooltipsIDs]', g_l('prefs', '[editorTooltipsIDs]'), true, 'defaultfont', '');

			$_template_editor_tooltip_font_specify = (get_value('editorTooltipFontname') != '' && get_value('editorTooltipFontname') != 'none');
			$_template_editor_tooltip_font_size_specify = (get_value('editorTooltipFontsize') != '' && get_value('editorTooltipFontsize') != -1);

			// Build specify font
			$_template_editor_tooltip_font_specify_code = we_html_forms::checkbox(1, $_template_editor_tooltip_font_specify, 'newconf[editorTooltipFont]', g_l('prefs', '[specify]'), true, 'defaultfont', 'if (document.getElementsByName(\'newconf[editorTooltipFont]\')[0].checked) { document.getElementsByName(\'newconf[editorTooltipFontname]\')[0].disabled = false;document.getElementsByName(\'newconf[editorTooltipFontsize]\')[0].disabled = false; } else { document.getElementsByName(\'newconf[editorTooltipFontname]\')[0].disabled = true;document.getElementsByName(\'newconf[editorTooltipFontsize]\')[0].disabled = true; }');

			$_template_editor_tooltip_font_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorTooltipFontname]', 'size' => 1, 'style' => 'width: 135px;', ($_template_editor_tooltip_font_specify ? 'enabled' : 'disabled') => ($_template_editor_tooltip_font_specify ? 'enabled' : 'disabled')));

			foreach($_template_fonts as $font){
				$_template_editor_tooltip_font_select_box->addOption($font, $font);
			}
			$_template_editor_tooltip_font_select_box->selectOption($_template_editor_tooltip_font_specify ? get_value('editorTooltipFontname') : 'Tahoma');

			$_template_editor_tooltip_font_sizes_select_box = new we_html_select(array('class' => 'weSelect editor editor_codemirror2', 'name' => 'newconf[editorTooltipFontsize]', 'size' => 1, 'style' => 'width: 135px;', ($_template_editor_tooltip_font_size_specify ? 'enabled' : 'disabled') => ($_template_editor_tooltip_font_size_specify ? 'enabled' : 'disabled')));
			$template_toolfont_sizes = array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
			foreach($template_toolfont_sizes as $sz){
				$_template_editor_tooltip_font_sizes_select_box->addOption($sz, $sz);
			}
			$_template_editor_tooltip_font_sizes_select_box->selectOption($_template_editor_tooltip_font_specify ? get_value("editor_tooltip_font_size") : 11);
			$_template_editor_tooltip_font_specify_table = '<table style="margin:0px 0px 20px 50px;" class="default">
				<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontname]') . '</td><td>' . $_template_editor_tooltip_font_select_box->getHtml() . '</td></tr>
				<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontsize]') . '</td><td>' . $_template_editor_tooltip_font_sizes_select_box->getHtml() . '</td></tr>
			</table>';

			//Build activation of integration of documentation
			$_template_editor_autoClose = we_html_forms::checkbox(1, get_value('editorDocuintegration'), 'newconf[editorDocuintegration]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '') .
//remove fonts not available
				we_html_element::jsScript(LIB_DIR . 'additional/fontdetect/fontdetect.js') .
				we_html_element::jsElement('
var detective = new Detector();
var elements=document.getElementsByName("newconf[editorFontname]")[0].children;
var elements2=document.getElementsByName("newconf[editorTooltipFontname]")[0].children;
for(i=0;i<elements.length; ++i){
	if(!detective.detect(elements[i].value)){
		elements[i].disabled="disabled";
		elements2[i].disabled="disabled";
	}
}');

			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[editor_information]'), we_html_tools::TYPE_INFO, 480, false), 'space' => 0),
				array('headline' => g_l('prefs', '[editor_mode]'), 'html' => $_template_editor_mode->getHtml(), 'space' => 150),
				array('class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('prefs', '[editor_font]'), 'html' => $_template_editor_font_specify_code . $_template_editor_font_specify_table, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_theme]'), 'html' => $_template_editor_theme->getHtml(), 'space' => 150),
//				array('class' => 'editor editor_java', 'headline' => g_l('prefs', '[editor_highlight_colors]'), 'html' => $_template_editor_font_color_checkbox . $_template_editor_font_color_table, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_linenumbers]'), 'html' => $_template_editor_linenumbers_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_highlightLine]'), 'html' => $_template_editor_highlightLine_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('global', '[wrapcheck]'), 'html' => $_template_editor_Wrap_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('prefs', '[editor_tabstop]'), 'html' => $_template_editor_tabstop_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_autoindent]'), 'html' => $_template_editor_autoIndent_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_completion]'), 'html' => $_template_editor_codecompletion_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_tooltips]'), 'html' => $_template_editor_tooltips_code . $_template_editor_tooltip_font_specify_code . $_template_editor_tooltip_font_specify_table, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_autoCloseTags]'), 'html' => $_template_editor_autoClose, 'space' => 150),
				//array('class'=>'editor editor_codemirror2','headline' => g_l('prefs', '[editor_docuclick]'), 'html' => $_template_editor_docuintegration_code, 'space' => 150),
			);

			return create_dialog("settings_editor_predefined", /* g_l('prefs', '[tab][editor]'), */ $_settings, count($_settings), g_l('prefs', '[show_predefined]'), g_l('prefs', '[hide_predefined]'));

		case "recipients":
			if(!we_base_preferences::userIsAllowed('FORMMAIL_VIAWEDOC')){
				break;
			}
			$_settings = array();
			//FORMMAIL RECIPIENTS
			if(we_base_preferences::userIsAllowed('FORMMAIL_BLOCK')){
				// Build dialog if user has permission
				$_settings[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[formmail_information]'), we_html_tools::TYPE_INFO, 450, false), "space" => 0);

				/**
				 * Recipients list
				 */
				$_select_box = new we_html_select(array("class" => "weSelect", "name" => "we_recipient", "size" => 10, "style" => "width: 340px;height:100px", "ondblclick" => "edit_recipient();"));

				$_enabled_buttons = false;

				$DB_WE->query('SELECT ID, Email FROM ' . RECIPIENTS_TABLE . ' ORDER BY Email');

				while($DB_WE->next_record()){
					$_enabled_buttons = true;
					$_select_box->addOption($DB_WE->f("ID"), $DB_WE->f("Email"));
				}

				// Create needed hidden fields
				$_hidden_fields = we_html_element::htmlHiddens(array(
						"newconf[formmail_values]" => "",
						"newconf[formmail_deleted]" => ""));

				// Create edit list
				$_editlist_table = new we_html_table(array('class' => 'default'), 2, 3);

				$_editlist_table->setCol(0, 0, array('style' => 'padding-right:10px;'), $_hidden_fields . $_select_box->getHtml());
				$_editlist_table->setCol(0, 2, array('style' => 'vertical-align:top;'), we_html_button::create_button(we_html_button::ADD, "javascript:add_recipient();") . we_html_button::create_button(we_html_button::EDIT, "javascript:edit_recipient();", true, 100, 22, "", "", !$_enabled_buttons, false) . we_html_button::create_button(we_html_button::DELETE, "javascript:delete_recipient();", true, 100, 22, "", "", !$_enabled_buttons, false));

				// Build dialog if user has permission
				$_settings[] = array("headline" => "", "html" => $_editlist_table->getHtml(), "space" => 0);
			}

			// formmail stuff


			if(we_base_preferences::userIsAllowed("FORMMAIL_CONFIRM")){
				$_formmail_confirm = new we_html_select(array("name" => "newconf[FORMMAIL_CONFIRM]", "style" => "width:88px;", "class" => "weSelect"));
				$_formmail_confirm->addOption(1, g_l('prefs', '[on]'));
				$_formmail_confirm->addOption(0, g_l('prefs', '[off]'));
				$_formmail_confirm->selectOption(get_value("FORMMAIL_CONFIRM") ? 1 : 0);

				$_settings[] = array('html' => $_formmail_confirm->getHtml(), "space" => 250, "headline" => g_l('prefs', '[formmailConfirm]'));

				$_formmail_log = new we_html_select(array("name" => "newconf[FORMMAIL_LOG]", "onchange" => "formmailLogOnOff()", "style" => "width:88px;", "class" => "weSelect"));
				$_formmail_log->addOption(1, g_l('prefs', '[yes]'));
				$_formmail_log->addOption(0, g_l('prefs', '[no]'));
				$_formmail_log->selectOption(get_value("FORMMAIL_LOG") ? 1 : 0);

				$_html = '<table class="default">
							<tr>
								<td>' . $_formmail_log->getHtml() . '</td>
								<td style="padding-left:10px;">' . we_html_button::create_button("logbook", 'javascript:we_cmd(\'show_formmail_log\')') . '</td>
							</tr>
						</table>';
				$_settings[] = array('html' => $_html, "space" => 250, "headline" => g_l('prefs', '[logFormmailRequests]'), "noline" => 1);

				$_isDisabled = (get_value("FORMMAIL_LOG") == 0);


				$_formmail_emptylog = new we_html_select(array("name" => "newconf[FORMMAIL_EMPTYLOG]", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_emptylog->setAttribute("disabled", "disabled");
				}
				$_formmail_emptylog->addOption(-1, g_l('prefs', '[never]'));
				$_formmail_emptylog->addOption(86400, g_l('prefs', '[1_day]'));
				$_formmail_emptylog->addOption(172800, sprintf(g_l('prefs', '[more_days]'), 2));
				$_formmail_emptylog->addOption(345600, sprintf(g_l('prefs', '[more_days]'), 4));
				$_formmail_emptylog->addOption(604800, g_l('prefs', '[1_week]'));
				$_formmail_emptylog->addOption(1209600, sprintf(g_l('prefs', '[more_weeks]'), 2));
				$_formmail_emptylog->addOption(2419200, sprintf(g_l('prefs', '[more_weeks]'), 4));
				$_formmail_emptylog->addOption(4838400, sprintf(g_l('prefs', '[more_weeks]'), 8));
				$_formmail_emptylog->addOption(9676800, sprintf(g_l('prefs', '[more_weeks]'), 16));
				$_formmail_emptylog->addOption(19353600, sprintf(g_l('prefs', '[more_weeks]'), 32));

				$_formmail_emptylog->selectOption(get_value("FORMMAIL_EMPTYLOG"));


				$_settings[] = array('html' => $_formmail_emptylog->getHtml(), "space" => 250, "headline" => g_l('prefs', '[deleteEntriesOlder]'));

				// formmail only via we doc //
				$_formmail_ViaWeDoc = new we_html_select(array("name" => "newconf[FORMMAIL_VIAWEDOC]", "style" => "width:88px;", "class" => "weSelect"));
				$_formmail_ViaWeDoc->addOption(1, g_l('prefs', '[yes]'));
				$_formmail_ViaWeDoc->addOption(0, g_l('prefs', '[no]'));
				$_formmail_ViaWeDoc->selectOption((get_value("FORMMAIL_VIAWEDOC") ? 1 : 0));

				$_settings[] = array('html' => $_formmail_ViaWeDoc->getHtml(), "space" => 250, "headline" => g_l('prefs', '[formmailViaWeDoc]'));

				// limit formmail requests //
				$_formmail_block = new we_html_select(array("name" => "newconf[FORMMAIL_BLOCK]", "onchange" => "formmailBlockOnOff()", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_block->setAttribute("disabled", "disabled");
				}
				$_formmail_block->addOption(1, g_l('prefs', '[yes]'));
				$_formmail_block->addOption(0, g_l('prefs', '[no]'));
				$_formmail_block->selectOption(get_value("FORMMAIL_BLOCK") ? 1 : 0);

				$_html = '<table class="default">
							<tr>
								<td>' . $_formmail_block->getHtml() . '</td>
								<td style="padding-left:10px;">' . we_html_button::create_button("logbook", 'javascript:we_cmd(\'show_formmail_block_log\')') . '</td>
							</tr>
						</table>';

				$_settings[] = array('html' => $_html, "space" => 250, "headline" => g_l('prefs', '[blockFormmail]'), "noline" => 1);

				$_isDisabled = $_isDisabled || (get_value("FORMMAIL_BLOCK") == 0);

				// table is IE fix. Without table IE has a gap on the left of the input
				$_formmail_trials = '<table class="default"><tr><td>' .
					we_html_tools::htmlTextInput("newconf[FORMMAIL_TRIALS]", 24, get_value("FORMMAIL_TRIALS"), "", "", "text", 88, 0, "", $_isDisabled) .
					'</td></tr></table>';

				$_settings[] = array('html' => $_formmail_trials, "space" => 250, "headline" => g_l('prefs', '[formmailTrials]'), "noline" => 1);

				if(!$_isDisabled){
					$_isDisabled = (get_value("FORMMAIL_BLOCK") == 0);
				}

				$_formmail_span = new we_html_select(array("name" => "newconf[FORMMAIL_SPAN]", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_span->setAttribute("disabled", "disabled");
				}
				$_formmail_span->addOption(60, g_l('prefs', '[1_minute]'));
				$_formmail_span->addOption(120, sprintf(g_l('prefs', '[more_minutes]'), 2));
				$_formmail_span->addOption(180, sprintf(g_l('prefs', '[more_minutes]'), 3));
				$_formmail_span->addOption(300, sprintf(g_l('prefs', '[more_minutes]'), 5));
				$_formmail_span->addOption(600, sprintf(g_l('prefs', '[more_minutes]'), 10));
				$_formmail_span->addOption(1200, sprintf(g_l('prefs', '[more_minutes]'), 20));
				$_formmail_span->addOption(1800, sprintf(g_l('prefs', '[more_minutes]'), 30));
				$_formmail_span->addOption(2700, sprintf(g_l('prefs', '[more_minutes]'), 45));
				$_formmail_span->addOption(3600, g_l('prefs', '[1_hour]'));
				$_formmail_span->addOption(7200, sprintf(g_l('prefs', '[more_hours]'), 2));
				$_formmail_span->addOption(14400, sprintf(g_l('prefs', '[more_hours]'), 4));
				$_formmail_span->addOption(28800, sprintf(g_l('prefs', '[more_hours]'), 8));
				$_formmail_span->addOption(86400, sprintf(g_l('prefs', '[more_hours]'), 24));

				$_formmail_span->selectOption(get_value("FORMMAIL_SPAN"));


				$_settings[] = array('html' => $_formmail_span->getHtml(), "space" => 250, "headline" => g_l('prefs', '[formmailSpan]'), "noline" => 1);
				$_formmail_blocktime = new we_html_select(array("name" => "newconf[FORMMAIL_BLOCKTIME]", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_blocktime->setAttribute("disabled", "disabled");
				}
				$_formmail_blocktime->addOption(60, g_l('prefs', '[1_minute]'));
				$_formmail_blocktime->addOption(120, sprintf(g_l('prefs', '[more_minutes]'), 2));
				$_formmail_blocktime->addOption(180, sprintf(g_l('prefs', '[more_minutes]'), 3));
				$_formmail_blocktime->addOption(300, sprintf(g_l('prefs', '[more_minutes]'), 5));
				$_formmail_blocktime->addOption(600, sprintf(g_l('prefs', '[more_minutes]'), 10));
				$_formmail_blocktime->addOption(1200, sprintf(g_l('prefs', '[more_minutes]'), 20));
				$_formmail_blocktime->addOption(1800, sprintf(g_l('prefs', '[more_minutes]'), 30));
				$_formmail_blocktime->addOption(2700, sprintf(g_l('prefs', '[more_minutes]'), 45));
				$_formmail_blocktime->addOption(3600, g_l('prefs', '[1_hour]'));
				$_formmail_blocktime->addOption(7200, sprintf(g_l('prefs', '[more_hours]'), 2));
				$_formmail_blocktime->addOption(14400, sprintf(g_l('prefs', '[more_hours]'), 4));
				$_formmail_blocktime->addOption(28800, sprintf(g_l('prefs', '[more_hours]'), 8));
				$_formmail_blocktime->addOption(86400, sprintf(g_l('prefs', '[more_hours]'), 24));
				$_formmail_blocktime->addOption(-1, g_l('prefs', '[ever]'));

				$_formmail_blocktime->selectOption(get_value("FORMMAIL_BLOCKTIME"));


				$_settings[] = array('html' => $_formmail_blocktime->getHtml(), "space" => 250, "headline" => g_l('prefs', '[blockFor]'), "noline" => 1);
			}

			return create_dialog("", /* g_l('prefs', '[formmail_recipients]'), */ $_settings, -1);

		case "modules":
			if(!we_base_preferences::userIsAllowed('active_integrated_modules')){
				break;
			}
			$_modInfos = we_base_moduleInfo::getIntegratedModules();

			$_html = '';

			foreach($_modInfos as $_modKey => $_modInfo){
				if(!isset($_modInfo["alwaysActive"])){
					$_modInfo["alwaysActive"] = null;
				}
				$onclick = "";
				if(!empty($_modInfo["childmodule"])){
					$onclick = "if(!this.checked){document.getElementById('newconf[active_integrated_modules][" . $_modInfo["childmodule"] . "]').checked=false;}";
				}
				if(!empty($_modInfo["dependson"])){
					$onclick = "if(this.checked){document.getElementById('newconf[active_integrated_modules][" . $_modInfo["dependson"] . "]').checked=true;}";
				}
				$_html .= we_html_forms::checkbox($_modKey, $_modInfo["alwaysActive"] || we_base_moduleInfo::isActive($_modKey), "newconf[active_integrated_modules][$_modKey]", $_modInfo["text"], false, "defaultfont", $onclick, $_modInfo["alwaysActive"]) . ($_modInfo["alwaysActive"] ? "<input type=\"hidden\" name=\"newconf[active_integrated_modules][$_modKey]\" value=\"$_modKey\" />" : "" ) . "<br />";
			}

			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[module_activation][information]'), we_html_tools::TYPE_INFO, 450, false), "space" => 0),
				array('headline' => g_l('prefs', '[module_activation][headline]'), "html" => $_html, "space" => 200)
			);

			return create_dialog('', /* g_l('prefs', '[module_activation][headline]'), */ $_settings, -1);

		case 'proxy':
			if(!we_base_preferences::userIsAllowed('useproxy')){
				break;
			}
			/**
			 * Proxy server
			 */
			// Check Proxy settings  ...
			$_proxy = get_value("proxy_proxy");

			$_use_proxy = we_html_forms::checkbox(1, $_proxy, "newconf[useproxy]", g_l('prefs', '[useproxy]'), false, "defaultfont", "set_state();");
			$_proxyaddr = we_html_tools::htmlTextInput("newconf[proxyhost]", 22, get_value("WE_PROXYHOST"), "", "", "text", 225, 0, "", !$_proxy);
			$_proxyport = we_html_tools::htmlTextInput("newconf[proxyport]", 22, get_value("WE_PROXYPORT"), "", "", "text", 225, 0, "", !$_proxy);
			$_proxyuser = we_html_tools::htmlTextInput("newconf[proxyuser]", 22, get_value("WE_PROXYUSER"), "", "", "text", 225, 0, "", !$_proxy);
			$_proxypass = we_html_tools::htmlTextInput("newconf[proxypass]", 22, get_value("WE_PROXYPASSWORD"), "", "", "password", 225, 0, "", !$_proxy);

			// Build dialog if user has permission

			$_settings = array(
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[proxy_information]'), we_html_tools::TYPE_INFO, 450, false), "space" => 0),
				array("headline" => g_l('prefs', '[tab][proxy]'), "html" => $_use_proxy, "space" => 200),
				array("headline" => g_l('prefs', '[proxyaddr]'), "html" => $_proxyaddr, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[proxyport]'), "html" => $_proxyport, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[proxyuser]'), "html" => $_proxyuser, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[proxypass]'), "html" => $_proxypass, "space" => 200, "noline" => 1),
			);
			// Build dialog element if user has permission
			return create_dialog("", /* g_l('prefs', '[tab][proxy]'), */ $_settings, -1);


		case "advanced":
			/*			 * *******************************************************************
			 * ATTRIBS
			 * ******************************************************************* */
			if(!permissionhandler::hasPerm("ADMINISTRATOR")){
				break;
			}
			/*
			  $WYSIWYG_TYPE = new we_html_select(array("name" => "newconf[WYSIWYG_TYPE]", "class" => "weSelect"));
			  $_options = array('tinyMCE' => 'tinyMCE', 'default' => 'webEdition Editor (deprecated))');
			  foreach($_options as $key => $val){
			  $WYSIWYG_TYPE->addOption($key, $val);
			  }
			  $WYSIWYG_TYPE->selectOption(get_value("WYSIWYG_TYPE"));
			  $_settings[] = array("headline" => g_l('prefs', '[wysiwyg_type]'), "html" => $WYSIWYG_TYPE->getHtml(), "space" => 200);

			  $WYSIWYG_TYPE_FRONTEND = new we_html_select(array("name" => "newconf[WYSIWYG_TYPE_FRONTEND]", "class" => "weSelect"));
			  $_options = array('tinyMCE' => 'tinyMCE', 'default' => 'webEdition Editor (deprecated))');
			  foreach($_options as $key => $val){
			  $WYSIWYG_TYPE_FRONTEND->addOption($key, $val);
			  }
			  $WYSIWYG_TYPE_FRONTEND->selectOption(get_value("WYSIWYG_TYPE_FRONTEND"));
			  $_settings[] = array("headline" => "Editor für textareas im Frontend", "html" => $WYSIWYG_TYPE_FRONTEND->getHtml(), "space" => 200);
			 */
			$_we_doctype_workspace_behavior = get_value("WE_DOCTYPE_WORKSPACE_BEHAVIOR");
			$_we_doctype_workspace_behavior_table = '<table class="default"><tr><td>' .
				we_html_forms::radiobutton(0, (!$_we_doctype_workspace_behavior), "newconf[WE_DOCTYPE_WORKSPACE_BEHAVIOR]", g_l('prefs', '[we_doctype_workspace_behavior_0]'), true, "defaultfont", "", false, g_l('prefs', '[we_doctype_workspace_behavior_hint0]'), 0, 430) .
				'</td></tr><tr><td style="padding-top:10px;">' .
				we_html_forms::radiobutton(1, $_we_doctype_workspace_behavior, "newconf[WE_DOCTYPE_WORKSPACE_BEHAVIOR]", g_l('prefs', '[we_doctype_workspace_behavior_1]'), true, "defaultfont", "", false, g_l('prefs', '[we_doctype_workspace_behavior_hint1]'), 0, 430) .
				'</td></tr></table>';

			$_settings[] = array("headline" => g_l('prefs', '[we_doctype_workspace_behavior]'), "html" => $_we_doctype_workspace_behavior_table, "space" => 200);

			if(we_base_preferences::userIsAllowed('WE_LOGIN_HIDEWESTATUS')){
				$_loginWEst_disabler = we_html_forms::checkbox(1, get_value('WE_LOGIN_HIDEWESTATUS') == 1 ? 1 : 0, 'newconf[WE_LOGIN_HIDEWESTATUS]', g_l('prefs', '[login][deactivateWEstatus]'));

				$_we_windowtypes = array(
					0 => g_l('prefs', '[login][windowtypeboth]'),
					1 => g_l('prefs', '[login][windowtypepopup]'),
					2 => g_l('prefs', '[login][windowtypesame]'));
				$_we_windowtypeselect = new we_html_select(array('name' => 'newconf[WE_LOGIN_WEWINDOW]', 'class' => 'weSelect'));
				foreach($_we_windowtypes as $key => $value){
					$_we_windowtypeselect->addOption($key, $value);
				}
				$_we_windowtypeselect->selectOption(get_value('WE_LOGIN_WEWINDOW'));
				// Build dialog if user has permission
				$_settings[] = array('headline' => g_l('prefs', '[login][login]'), 'html' => $_loginWEst_disabler . we_html_element::htmlBr() . g_l('prefs', '[login][windowtypes]') . we_html_element::htmlBr() . $_we_windowtypeselect->getHtml(), 'space' => 200);
			}

			if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
				$_Schedtrigger_setting = new we_html_select(array("name" => "newconf[SCHEDULER_TRIGGER]", "class" => "weSelect"));
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_PREDOC, g_l('prefs', '[we_scheduler_trigger][preDoc]')); //pre
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_POSTDOC, g_l('prefs', '[we_scheduler_trigger][postDoc]')); //post
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_CRON, g_l('prefs', '[we_scheduler_trigger][cron]')); //cron
				$_Schedtrigger_setting->selectOption(get_value("SCHEDULER_TRIGGER"));
				$tmp = $_Schedtrigger_setting->getHtml() . '<br style="clear:both;"/>' . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[we_scheduler_trigger][description]'), we_html_tools::TYPE_INFO, 450, false);
				$_settings[] = array("headline" => g_l('prefs', '[we_scheduler_trigger][head]'), "html" => $tmp, "space" => 200);
			}
			// Build select box
			$NAVIGATION_ENTRIES_FROM_DOCUMENT = new we_html_select(array("name" => "newconf[NAVIGATION_ENTRIES_FROM_DOCUMENT]", "class" => "weSelect"));
			for($i = 0; $i < 2; $i++){
				$NAVIGATION_ENTRIES_FROM_DOCUMENT->addOption($i, g_l('prefs', $i == 0 ? '[navigation_entries_from_document_folder]' : '[navigation_entries_from_document_item]'));
			}
			$NAVIGATION_ENTRIES_FROM_DOCUMENT->selectOption(get_value("NAVIGATION_ENTRIES_FROM_DOCUMENT") ? 1 : 0);
			$_settings[] = array("headline" => g_l('prefs', '[navigation_entries_from_document]'), "html" => $NAVIGATION_ENTRIES_FROM_DOCUMENT->getHtml(), "space" => 200);


			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH = new we_html_select(array("name" => "newconf[NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH]", "class" => "weSelect"));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->addOption(0, g_l('prefs', '[no]'));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->addOption(1, g_l('prefs', '[yes]'));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->selectOption(get_value("NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH") ? 1 : 0);
			$_settings[] = array("headline" => g_l('prefs', '[navigation_rules_continue]'), "html" => $NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->getHtml(), "space" => 200);

			return create_dialog("", /* g_l('prefs', '[tab][advanced]'), */ $_settings, -1);

		case "system":
			if(!permissionhandler::hasPerm("ADMINISTRATOR")){
				break;
			}

			$_we_max_upload_size = '<table class="default"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[FILE_UPLOAD_MAX_UPLOAD_SIZE]", 22, get_value("FILE_UPLOAD_MAX_UPLOAD_SIZE"), "", ' onkeypress="return IsDigit(event);"', "number", 60) . ' MB</td><td style="padding-left:20px;" class="small">' .
				g_l('prefs', '[upload][we_max_size_hint]') .
				'</td></tr></table>';

			// FILE UPLOAD
			$_fileuploader_use_legacy = we_html_forms::checkbox(1, get_value('FILE_UPLOAD_USE_LEGACY'), 'newconf[FILE_UPLOAD_USE_LEGACY]', g_l('prefs', '[upload][use_legacy]'), false, 'defaultfont', '');

			$_we_new_folder_mod = '<table class="default"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[WE_NEW_FOLDER_MOD]", 22, get_value("WE_NEW_FOLDER_MOD"), 3, ' onkeypress="return IsDigit(event);"', "text", 60) . '</td><td style="padding-left:20px;" class="small">' .
				g_l('prefs', '[we_new_folder_mod_hint]') .
				'</td></tr></table>';

			// Build db select box
			$_db_connect = new we_html_select(array('name' => 'newconf[DB_CONNECT]', 'class' => 'weSelect'));
			if(class_exists('mysqli', false)){
				$_db_connect->addOption('mysqli_connect', 'mysqli_connect');
				$_db_connect->addOption('mysqli_pconnect', 'mysqli_pconnect');
			}
			if(function_exists('mysql_connect')){ //only allow old connection method if new is not available
				$_db_connect->addOption('connect', 'connect (deprecated)');
				$_db_connect->addOption('pconnect', 'pconnect (deprecated)');
			}
			$_db_connect->selectOption(DB_CONNECT);

			// Build db charset select box
			$html_db_charset_information = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[db_set_charset_information]'), we_html_tools::TYPE_INFO, 450, false, 60) . "<br/>";
			$html_db_charset_warning = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[db_set_charset_warning]'), we_html_tools::TYPE_ALERT, 450, false, 60) . "<br/>";

			$_db_set_charset = new we_html_select(array('name' => 'newconf[DB_SET_CHARSET]', 'class' => 'weSelect'));

			$GLOBALS['DB_WE']->query('SHOW CHARACTER SET');

			$charsets = array('');
			while($GLOBALS['DB_WE']->next_record()){
				$charsets[] = $GLOBALS['DB_WE']->f('Charset');
			}
			sort($charsets);
			foreach($charsets as $charset){
				$_db_set_charset->addOption($charset, $charset);
			}

			if(defined('DB_SET_CHARSET') && DB_SET_CHARSET != ''){
				$_db_set_charset->selectOption(DB_SET_CHARSET);
			} else {
				$tmp = $GLOBALS['DB_WE']->getCurrentCharset();
				if($tmp){
					$_db_set_charset->selectOption($tmp);
					$_file = &$GLOBALS['config_files']['conf_global']['content'];
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'DB_SET_CHARSET', $tmp);
				}
			}

			// Check authentication settings  ...
			$_auth = get_value("HTTP_USERNAME");
			$_auth_user = get_value("HTTP_USERNAME");
			$_auth_pass = get_value("HTTP_PASSWORD");

			// Build dialog if user has permission
			$_use_auth = we_html_tools::hidden('newconf[useauth]', $_auth) .
				we_html_forms::checkbox(1, $_auth, "useauthEnabler", g_l('prefs', '[useauth]'), false, "defaultfont", "set_state_auth();");

			/**
			 * User name
			 */
			$_authuser = we_html_tools::htmlTextInput("newconf[HTTP_USERNAME]", 22, $_auth_user, "", "", "text", 225, 0, "", !$_auth);
			$_authpass = we_html_tools::htmlTextInput("newconf[HTTP_PASSWORD]", 22, $_auth_pass, "", "", "password", 225, 0, "", !$_auth);


			if(we_base_imageEdit::gd_version() > 0){ //  gd lib ist installiert
				$wecmdenc1 = we_base_request::encCmd("document.forms[0].elements['newconf[WE_THUMBNAIL_DIRECTORY]'].value");
				$_but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '" . we_base_ContentTypes::FOLDER . "', document.forms[0].elements['newconf[WE_THUMBNAIL_DIRECTORY]'].value, '')") : "";
				$_inp = we_html_tools::htmlTextInput("newconf[WE_THUMBNAIL_DIRECTORY]", 12, get_value("WE_THUMBNAIL_DIRECTORY"), "", "", "text", 125);
				$_thumbnail_dir = $_inp . $_but;
			} else { //  gd lib ist nicht installiert
				$_but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button(we_html_button::SELECT, "#", true, 100, 22, '', '', true) : "";
				$_inp = we_html_tools::htmlTextInput("newconf[WE_THUMBNAIL_DIRECTORY]", 12, get_value("WE_THUMBNAIL_DIRECTORY"), "", "", "text", 125, 0, '', true);
				$_thumbnail_dir = $_inp . $_but . '<br/>' . g_l('thumbnails', '[add_description_nogdlib]');
			}

			//  select if hooks can be executed
			$EXECUTE_HOOKS = new we_html_select(array("name" => "newconf[EXECUTE_HOOKS]", "class" => "weSelect"));
			$EXECUTE_HOOKS->addOption(0, g_l('prefs', '[no]'));
			$EXECUTE_HOOKS->addOption(1, g_l('prefs', '[yes]'));

			$EXECUTE_HOOKS->selectOption(get_value("EXECUTE_HOOKS") ? 1 : 0);

			$hooksHtml = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[hooks_information]'), we_html_tools::TYPE_INFO, 450, false) . '<br/>' .
				$EXECUTE_HOOKS->getHtml();

			$useSession = new we_html_select(array("name" => "newconf[SYSTEM_WE_SESSION]", "class" => "weSelect", 'onchange' => 'alert(\'' . g_l('prefs', '[session][crypt][alert]') . '\');'));
			$useSession->addOption(0, g_l('prefs', '[no]'));
			$useSession->addOption(1, g_l('prefs', '[yes]'));
			$useSession->selectOption(get_value("SYSTEM_WE_SESSION") ? 1 : 0);

			$sessionTime = '<table class="default"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[SYSTEM_WE_SESSION_TIME]", 22, abs(get_value("SYSTEM_WE_SESSION_TIME")), "", ' onkeypress="return IsDigit(event);"', "text", 60) . '</td><td style="padding-left:20px;" class="small">s</td></tr></table>';

			$cryptSession = new we_html_select(array("name" => 'newconf[SYSTEM_WE_SESSION_CRYPT]', 'class' => "weSelect", 'onchange' => 'alert(\'' . g_l('prefs', '[session][crypt][alert]') . '\');'));
			$cryptSession->addOption(0, g_l('prefs', '[no]'));
			$cryptSession->addOption(1, 'Transparent');
			$cryptSession->addOption(2, 'Cookie');
			$cryptSession->selectOption(get_value('SYSTEM_WE_SESSION_CRYPT'));

			$sessionHtml = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[session][information]'), we_html_tools::TYPE_INFO, 450, false) . '<br/>' .
				$useSession->getHtml();


			$_settings = array(
				array("headline" => g_l('prefs', '[upload][we_max_size]'), "html" => $_we_max_upload_size, "space" => 200),
				array('headline' => g_l('prefs', '[upload][title]'), 'html' => $_fileuploader_use_legacy, 'space' => 200),
				array("headline" => g_l('prefs', '[we_new_folder_mod]'), "html" => $_we_new_folder_mod, "space" => 200),
				array("headline" => g_l('prefs', '[db_connect]'), "html" => $_db_connect->getHtml(), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[db_set_charset]'), "html" => $html_db_charset_information . $_db_set_charset->getHtml() . $html_db_charset_warning, "space" => 200),
				array("headline" => g_l('prefs', '[auth]'), "html" => $_use_auth, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[authuser]'), "html" => $_authuser, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[authpass]'), "html" => $_authpass, "space" => 200),
				array("headline" => g_l('prefs', '[thumbnail_dir]'), "html" => $_thumbnail_dir, "space" => 200),
				array("headline" => g_l('prefs', '[hooks]'), "html" => $hooksHtml, "space" => 200),
				array("headline" => g_l('prefs', '[session][title]'), "html" => $sessionHtml, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[session][time]'), "html" => $sessionTime, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[session][crypt][title]'), "html" => $cryptSession->getHtml(), "space" => 200),
			);
			// Build dialog element if user has permission
			return create_dialog("", /* g_l('prefs', '[tab][system]'), */ $_settings, -1);

		case "seolinks":
			/*			 * *******************************************************************
			 * ATTRIBS
			 * ******************************************************************* */
			if(!permissionhandler::hasPerm("ADMINISTRATOR")){
				break;
			}
			// Build dialog if user has permission

			$_navigation_directoryindex_names = we_html_tools::htmlTextInput("newconf[NAVIGATION_DIRECTORYINDEX_NAMES]", 22, get_value("NAVIGATION_DIRECTORYINDEX_NAMES"), "", "", "text", 225);
			$wecmdenc1 = we_base_request::encCmd("document.forms[0].elements['newconf[ERROR_DOCUMENT_NO_OBJECTFILE]'].value");
			$wecmdenc2 = we_base_request::encCmd("document.forms[0].elements.error_document_no_objectfile_text.value");
			$_acButton1 = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', document.forms[0].elements['newconf[ERROR_DOCUMENT_NO_OBJECTFILE]'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','', '" . we_base_ContentTypes::WEDOCUMENT . "," . we_base_ContentTypes::HTML . "', 1)");
			$_acButton2 = we_html_button::create_button(we_html_button::TRASH, 'javascript:document.forms[0].elements[\'newconf[ERROR_DOCUMENT_NO_OBJECTFILE]\'].value = 0;document.forms[0].elements.error_document_no_objectfile_text.value = \'\'');

			$yuiSuggest->setAcId("doc2");
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
			$yuiSuggest->setInput('error_document_no_objectfile_text', ( ERROR_DOCUMENT_NO_OBJECTFILE ? id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE) : ''));
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('newconf[ERROR_DOCUMENT_NO_OBJECTFILE]', ( ERROR_DOCUMENT_NO_OBJECTFILE ? : 0));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($_acButton1, 10);
			$yuiSuggest->setTrashButton($_acButton2, 4);

			$_settings = array(
				array("headline" => g_l('prefs', '[general_directoryindex_hide]'), "html" => "", "space" => 480, "noline" => 1),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[navigation_directoryindex_description]'), we_html_tools::TYPE_INFO, 480), "noline" => 1),
				array("headline" => g_l('prefs', '[navigation_directoryindex_hide]'), "html" => getTrueFalseSelect('NAVIGATION_DIRECTORYINDEX_HIDE'), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[wysiwyglinks_directoryindex_hide]'), "html" => getTrueFalseSelect('WYSIWYGLINKS_DIRECTORYINDEX_HIDE'), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[navigation_directoryindex_names]'), "html" => $_navigation_directoryindex_names, "space" => 200, "noline" => 1),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_directoryindex_hide_description]'), we_html_tools::TYPE_INFO, 480), "noline" => 1),
				array("headline" => g_l('prefs', '[taglinks_directoryindex_hide]'), "html" => getTrueFalseSelect('TAGLINKS_DIRECTORYINDEX_HIDE'), "space" => 200),
				array("headline" => g_l('prefs', '[general_objectseourls]'), "html" => "", "space" => 480, "noline" => 1),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[objectseourls_lowercase_description]'), we_html_tools::TYPE_INFO, 480), "noline" => 1),
				array("headline" => g_l('prefs', '[objectseourls_lowercase]'), "html" => getTrueFalseSelect('OBJECTSEOURLS_LOWERCASE'), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[navigation_objectseourls]'), "html" => getTrueFalseSelect('NAVIGATION_OBJECTSEOURLS'), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[wysiwyglinks_objectseourls]'), "html" => getTrueFalseSelect('WYSIWYGLINKS_OBJECTSEOURLS'), "space" => 200, "noline" => 1),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_objectseourls_description]'), we_html_tools::TYPE_INFO, 480), "noline" => 1),
				array("headline" => g_l('prefs', '[taglinks_objectseourls]'), "html" => getTrueFalseSelect('TAGLINKS_OBJECTSEOURLS'), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[urlencode_objectseourls]'), "html" => getTrueFalseSelect('URLENCODE_OBJECTSEOURLS'), "space" => 200),
				array("headline" => g_l('prefs', '[general_seoinside]'), "noline" => 1),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_seoinside_description]'), we_html_tools::TYPE_INFO, 480), "noline" => 1),
				array("headline" => g_l('prefs', '[seoinside_hideineditmode]'), "html" => getTrueFalseSelect('SEOINSIDE_HIDEINEDITMODE'), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[seoinside_hideinwebedition]'), "html" => getTrueFalseSelect('SEOINSIDE_HIDEINWEBEDITION'), "space" => 200),
				array('headline' => g_l('prefs', '[error_no_object_found]'), 'html' => $yuiSuggest->getHTML(), 'space' => 200, "noline" => 1),
				array('headline' => g_l('prefs', '[suppress404code]'), 'html' => getTrueFalseSelect('SUPPRESS404CODE'), 'space' => 200, 'noline' => 0),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[force404redirect_description]'), we_html_tools::TYPE_INFO, 480), "noline" => 1),
				array('headline' => g_l('prefs', '[force404redirect]'), 'html' => getTrueFalseSelect('FORCE404REDIRECT'), 'space' => 200, 'noline' => 0),
			);
			return create_dialog('', /* g_l('prefs', '[tab][seolinks]'), */ $_settings, -1, '', '', null);

		case 'error_handling':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			/**
			 * Error handler
			 */
			$_foldAt = 4;

			// Create checkboxes
			$_template_error_handling_table = new we_html_table(array('class' => 'default'), 8, 1);
			$_template_error_handling_table->setCol(0, 0, null, we_html_forms::checkbox(1, get_value('DISABLE_TEMPLATE_CODE_CHECK'), 'DISABLE_TEMPLATE_CODE_CHECK', g_l('prefs', '[disable_template_code_check]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[DISABLE_TEMPLATE_CODE_CHECK]\');') .
				we_html_tools::hidden('newconf[DISABLE_TEMPLATE_CODE_CHECK]', get_value('DISABLE_TEMPLATE_CODE_CHECK')));

			// Create checkboxes
			$_we_error_handler = we_html_forms::checkbox(1, get_value('WE_ERROR_HANDLER'), 'newconf[WE_ERROR_HANDLER]', g_l('prefs', '[error_use_handler]'), false, 'defaultfont', 'set_state_error_handler();');

			// Error types
			// Create checkboxes
			$_error_handling_table = new we_html_table(array('class' => 'default'), 8, 1);

			$_error_handling_table->setCol(0, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_ERRORS'), 'newconf[WE_ERROR_ERRORS]', g_l('prefs', '[error_errors]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$_error_handling_table->setCol(2, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_WARNINGS'), 'newconf[WE_ERROR_WARNINGS]', g_l('prefs', '[error_warnings]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$_error_handling_table->setCol(4, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_NOTICES'), 'newconf[WE_ERROR_NOTICES]', g_l('prefs', '[error_notices]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$_error_handling_table->setCol(6, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_DEPRECATED'), 'newconf[WE_ERROR_DEPRECATED]', g_l('prefs', '[error_deprecated]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));

			// Create checkboxes
			$_error_display_table = new we_html_table(array('class' => 'default'), 8, 1);
			$_error_display_table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-left: 25px;'), we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[error_notices_warning]'), we_html_tools::TYPE_ALERT, 260));

			$_error_display_table->setCol(1, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_SHOW'), 'newconf[WE_ERROR_SHOW]', g_l('prefs', '[error_display]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$_error_display_table->setCol(3, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_LOG'), 'newconf[WE_ERROR_LOG]', g_l('prefs', '[error_log]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));
			$_error_display_table->setCol(5, 0, null, we_html_forms::checkbox(1, get_value('WE_ERROR_MAIL'), 'newconf[WE_ERROR_MAIL]', g_l('prefs', '[error_mail]'), false, 'defaultfont', '', !get_value('WE_ERROR_HANDLER')));

			// Create specify mail address input
			$_error_mail_specify_table = new we_html_table(array('class' => 'default', 'style' => 'margin-left:25px;'), 1, 4);

			$_error_mail_specify_table->setCol(0, 1, array('class' => 'defaultfont'), g_l('prefs', '[error_mail_address]') . ': ');
			$_error_mail_specify_table->setCol(0, 2, array('style' => 'text-align:left'), we_html_tools::htmlTextInput('newconf[WE_ERROR_MAIL_ADDRESS]', 6, (get_value('WE_ERROR_MAIL') ? get_value('WE_ERROR_MAIL_ADDRESS') : ''), 100, 'placeholder="mail@example"', 'email', 195));

			$_error_display_table->setCol(7, 0, null, $_error_mail_specify_table->getHtml());

			$_settings = array(
				array('headline' => g_l('prefs', '[templates]'), 'html' => $_template_error_handling_table->getHtml(), 'space' => 200),
				array('headline' => g_l('prefs', '[tab][error_handling]'), 'html' => $_we_error_handler, 'space' => 200),
				array('headline' => g_l('prefs', '[error_types]'), 'html' => $_error_handling_table->getHtml(), 'space' => 200),
				array('headline' => g_l('prefs', '[error_displaying]'), 'html' => $_error_display_table->getHtml(), 'space' => 200),
			);

			return create_dialog('settings_error_expert', /* g_l('prefs', '[tab][error_handling]'), */ $_settings, $_foldAt, g_l('prefs', '[show_expert]'), g_l('prefs', '[hide_expert]'));

		/*		 * *******************************************************************
		 * Validation (XHTML)
		 * ******************************************************************* */
		case 'message_reporting':

			$_val = get_value('message_reporting');

			$_html = "<input type=\"hidden\" id=\"message_reporting\" name=\"newconf[message_reporting]\" value=\"$_val\" />" . we_html_forms::checkbox(we_message_reporting::WE_MESSAGE_ERROR, 1, "message_reporting_errors", g_l('prefs', '[message_reporting][show_errors]'), false, "defaultfont", "handle_message_reporting_click();", true) . "<br />" .
				we_html_forms::checkbox(we_message_reporting::WE_MESSAGE_WARNING, $_val & we_message_reporting::WE_MESSAGE_WARNING, "message_reporting_warnings", g_l('prefs', '[message_reporting][show_warnings]'), false, "defaultfont", "handle_message_reporting_click();") . "<br />" .
				we_html_forms::checkbox(we_message_reporting::WE_MESSAGE_NOTICE, $_val & we_message_reporting::WE_MESSAGE_NOTICE, "message_reporting_notices", g_l('prefs', '[message_reporting][show_notices]'), false, "defaultfont", "handle_message_reporting_click();");

			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[message_reporting][information]'), we_html_tools::TYPE_INFO, 450, false), 'space' => 0),
				array('headline' => g_l('prefs', '[message_reporting][headline]'), 'html' => $_html, 'space' => 200),
			);

			return create_dialog('settings_message_reporting', /* g_l('prefs', '[tab][message_reporting]'), */ $_settings, -1);

		/*		 * *******************************************************************
		 * Validation (XHTML)
		 * ******************************************************************* */
		case 'validation':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			//  activate xhtml_debug
			$_xhtml_debug = we_html_forms::checkbox(1, get_value('XHTML_DEBUG'), 'setXhtml_debug', g_l('prefs', '[xhtml_debug_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[XHTML_DEBUG]\');disable_xhtml_fields(this.checked, mainXhtmlFields);disable_xhtml_fields((document.forms[0][\'setXhtml_show_wrong\'].checked && this.checked), showXhtmlFields);') .
				we_html_tools::hidden('newconf[XHTML_DEBUG]', get_value('XHTML_DEBUG'));

			//  activate xhtml_remove_wrong
			$_xhtml_remove_wrong = we_html_forms::checkbox(1, get_value('XHTML_REMOVE_WRONG'), 'setXhtml_remove_wrong', g_l('prefs', '[xhtml_remove_wrong]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'xhtml_remove_wrong\');', !get_value('XHTML_DEBUG')) .
				we_html_tools::hidden('newconf[XHTML_REMOVE_WRONG]', get_value('XHTML_REMOVE_WRONG'));

			//  activate xhtml_show_wrong
			$_xhtml_show_wrong = we_html_forms::checkbox(1, get_value('xhtml_show_wrong'), 'setXhtml_show_wrong', g_l('prefs', '[xhtml_show_wrong_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong]\');disable_xhtml_fields(this.checked,showXhtmlFields);', !get_value('XHTML_DEBUG')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong]', get_value('xhtml_show_wrong'));

			//  activate xhtml_show_wrong_text
			$_xhtml_show_wrong_text = we_html_forms::checkbox(1, get_value('xhtml_show_wrong_text'), 'setXhtml_show_wrong_text', g_l('prefs', '[xhtml_show_wrong_text_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong_text]\');', !get_value('xhtml_show_wrong')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong_text]', get_value('xhtml_show_wrong_text'));

			//  activate xhtml_show_wrong_text
			$_xhtml_show_wrong_js = we_html_forms::checkbox(1, get_value('xhtml_show_wrong_js'), 'setXhtml_show_wrong_js', g_l('prefs', '[xhtml_show_wrong_js_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong_js]\');', !get_value('xhtml_show_wrong')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong_js]', get_value('xhtml_show_wrong_js'));

			//  activate xhtml_show_wrong_text
			$_xhtml_show_wrong_error_log = we_html_forms::checkbox(1, get_value('xhtml_show_wrong_error_log'), 'setXhtml_show_wrong_error_log', g_l('prefs', '[xhtml_show_wrong_error_log_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong_error_log]\');', !get_value('xhtml_show_wrong')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong_error_log]', get_value('xhtml_show_wrong_error_log'));

			$_settings = array(
				array('html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[xhtml_debug_explanation]'), we_html_tools::TYPE_INFO, 450), 'space' => 0, 'noline' => 1),
				array('headline' => g_l('prefs', '[xhtml_debug_headline]'), 'html' => $_xhtml_debug, 'space' => 200, 'noline' => 1),
				array('html' => $_xhtml_remove_wrong, 'space' => 200),
				array('headline' => g_l('prefs', '[xhtml_show_wrong_headline]'), 'html' => '', 'space' => 400, 'noline' => 1),
				array('html' => $_xhtml_show_wrong, 'space' => 200, 'noline' => 1),
				array('html' => $_xhtml_show_wrong_text, 'space' => 220, 'noline' => 1),
				array('html' => $_xhtml_show_wrong_js, 'space' => 220, 'noline' => 1),
				array('html' => $_xhtml_show_wrong_error_log, 'space' => 220, 'noline' => 1),
			);

			return create_dialog('', /* g_l('prefs', '[tab][validation]'), */ $_settings, -1);

		case 'security':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				return;
			}
			$row = 0;
			$customer_table = new we_html_table(array('class' => 'default', 'id' => 'customer_table'), 9, 10);
			$customer_table->setCol($row, 0, array('class' => 'defaultfont', 'width' => '20px'), '');
			$customer_table->setCol($row, 1, array('class' => 'defaultfont', 'colspan' => 5), g_l('prefs', '[security][customer][disableLogins]') . ':');
			$customer_table->setCol($row, 6, array('width' => 300));
			$customer_table->setCol( ++$row, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][sameIP]'));
			$customer_table->setCol($row, 2, array('width' => '20px'));
			$customer_table->setCol($row, 3, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_IP]', 3, get_value('SECURITY_LIMIT_CUSTOMER_IP'), 3, '', 'number', 50));
			$customer_table->setCol($row, 4, array('class' => 'defaultfont', 'style' => 'width:2em;text-align:center'), '/');
			$customer_table->setCol($row, 5, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_IP_HOURS]', 3, get_value('SECURITY_LIMIT_CUSTOMER_IP_HOURS'), 3, '', 'number', 50));
			$customer_table->setCol($row, 6, array('class' => 'defaultfont'), 'h');

			$customer_table->setCol( ++$row, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][sameUser]'));
			$customer_table->setCol($row, 3, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_NAME]', 3, get_value('SECURITY_LIMIT_CUSTOMER_NAME'), 3, '', 'number', 50));
			$customer_table->setCol($row, 4, array('class' => 'defaultfont', 'style' => 'text-align:center;'), '/');
			$customer_table->setCol($row, 5, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_NAME_HOURS]', 3, get_value('SECURITY_LIMIT_CUSTOMER_NAME_HOURS'), 3, '', 'number', 50));
			$customer_table->setCol($row, 6, array('class' => 'defaultfont'), 'h');

			$customer_table->setCol( ++$row, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][errorPage]'));

			$wecmdenc1 = we_base_request::encCmd("document.forms[0].elements['newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]'].value");
			$wecmdenc2 = we_base_request::encCmd("document.forms[0].elements.SECURITY_LIMIT_CUSTOMER_REDIRECT_text.value");

			$yuiSuggest->setAcId('SECURITY_LIMIT_CUSTOMER_REDIRECT_doc');
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
			$yuiSuggest->setInput('SECURITY_LIMIT_CUSTOMER_REDIRECT_text', (SECURITY_LIMIT_CUSTOMER_REDIRECT ? id_to_path(SECURITY_LIMIT_CUSTOMER_REDIRECT) : ''));
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]', ( SECURITY_LIMIT_CUSTOMER_REDIRECT ? : 0));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(250);
			$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', document.forms[0].elements['newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','', '" . we_base_ContentTypes::WEDOCUMENT . "," . we_base_ContentTypes::HTML . "', 1)"), 10);
			$yuiSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, 'javascript:document.forms[0].elements[\'newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]\'].value = 0;document.forms[0].elements[\'SECURITY_LIMIT_CUSTOMER_REDIRECT_text\'].value = \'\''), 4);

			$customer_table->setCol($row, 3, array('class' => 'defaultfont', 'colspan' => 5), $yuiSuggest->getHTML());

			$customer_table->setCol( ++$row, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][slowDownLogin]'));
			$customer_table->setCol($row, 3, array(), we_html_tools::htmlTextInput('newconf[SECURITY_DELAY_FAILED_LOGIN]', 3, get_value('SECURITY_DELAY_FAILED_LOGIN'), 3, '', 'number', 50));
			$customer_table->setCol($row, 4, array(), 's');

			$customer_table->setCol( ++$row, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][deleteSession]'));

			$customer_table->setCol($row, 3, array(), we_html_tools::htmlSelect('newconf[SECURITY_DELETE_SESSION]', array(g_l('prefs', '[no]'), g_l('prefs', '[yes]')), 1, get_value('SECURITY_DELETE_SESSION')));

			$encryption = new we_html_select(array('name' => 'newconf[SECURITY_ENCRYPTION_TYPE_PASSWORD]', 'class' => 'weSelect'));
			$encryption->addOption(we_customer_customer::ENCRYPT_NONE, g_l('prefs', '[security][encryption][type][0]'));
			if(function_exists('mcrypt_module_open') && ($res = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_OFB, ''))){
				$encryption->addOption(we_customer_customer::ENCRYPT_SYMMETRIC, g_l('prefs', '[security][encryption][type][1]'));
				mcrypt_module_close($res);
			}

			$encryption->addOption(we_customer_customer::ENCRYPT_HASH, g_l('prefs', '[security][encryption][type][2]'), array());
			$encryption->selectOption(get_value('SECURITY_ENCRYPTION_TYPE_PASSWORD'));


			$encryptinfo = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[security][encryption][hint]'), we_html_tools::TYPE_ALERT, 450, false, 60) . '<br/>';
			$cryptkey = get_value('SECURITY_ENCRYPTION_KEY');
			$encryptionKey = we_html_tools::htmlTextInput('newconf[SECURITY_ENCRYPTION_KEY]', 30, ($cryptkey ? : bin2hex(we_customer_customer::cryptGetIV(56))), 112) . ' (hex)'; //+Button vorhandene Passwörter convertieren

			$storeSessionPassword = new we_html_select(array('name' => 'newconf[SECURITY_SESSION_PASSWORD]', 'class' => 'weSelect'));
			$storeSessionPassword->addOption(we_customer_customer::REMOVE_PASSWORD, g_l('prefs', '[security][storeSessionPassword][type][0]'));
			$storeSessionPassword->addOption(we_customer_customer::STORE_PASSWORD, g_l('prefs', '[security][storeSessionPassword][type][1]'));
			$storeSessionPassword->addOption(we_customer_customer::STORE_DBPASSWORD, g_l('prefs', '[security][storeSessionPassword][type][2]'));
			$storeSessionPassword->addOption(we_customer_customer::STORE_PASSWORD + we_customer_customer::STORE_DBPASSWORD, g_l('prefs', '[security][storeSessionPassword][type][3]'));
			$storeSessionPassword->selectOption(get_value('SECURITY_SESSION_PASSWORD'));

			$settings = array(
				array('headline' => g_l('perms_customer', '[perm_group_title]'), 'html' => $customer_table->getHtml(), 'space' => 120),
				array('headline' => g_l('prefs', '[security][encryption][title]'), 'html' => $encryptinfo . $encryption->getHtml(), 'space' => 120, 'noline' => 1),
				array('headline' => g_l('prefs', '[security][encryption][symmetricKey]'), 'html' => $encryptionKey, 'space' => 120),
				array('headline' => g_l('prefs', '[security][storeSessionPassword][title]'), 'html' => $storeSessionPassword->getHtml(), 'space' => 120),
			);
			return create_dialog('settings_security', /* g_l('prefs', '[tab][security]'), */ $settings);

		case 'email':
			/**
			 * Information
			 */
			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[mailer_information]'), we_html_tools::TYPE_INFO, 450, false), 'space' => 0)
			);

			if(permissionhandler::hasPerm('ADMINISTRATOR')){
				$_emailSelect = we_html_tools::htmlSelect('newconf[WE_MAILER]', array('php' => g_l('prefs', '[mailer_php]'), 'smtp' => g_l('prefs', '[mailer_smtp]')), 1, get_value('WE_MAILER'), false, array("onchange" => "var el = document.getElementById('smtp_table').style; if(this.value=='smtp') el.display='block'; else el.display='none';"), 'value', 300, 'defaultfont');

				$_smtp_table = new we_html_table(array('class' => 'default', 'id' => 'smtp_table', 'width' => 300, 'style' => 'display: ' . ((get_value('WE_MAILER') === 'php') ? 'none' : 'block') . ';'), 9, 3);
				$_smtp_table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-right:10px;'), g_l('prefs', '[smtp_server]'));
				$_smtp_table->setCol(0, 2, array('styke' => 'text-align:right'), we_html_tools::htmlTextInput('newconf[SMTP_SERVER]', 24, get_value('SMTP_SERVER'), 180, '', 'text', 180));
				$_smtp_table->setCol(2, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_port]'));
				$_smtp_table->setCol(2, 2, array('style' => 'text-align:right'), we_html_tools::htmlTextInput('newconf[SMTP_PORT]', 24, get_value('SMTP_PORT'), 180, '', 'text', 180));


				$_encryptSelect = we_html_tools::htmlSelect('newconf[SMTP_ENCRYPTION]', array(0 => g_l('prefs', '[smtp_encryption_none]'), 'ssl' => g_l('prefs', '[smtp_encryption_ssl]'), 'tls' => g_l('prefs', '[smtp_encryption_tls]')), 1, get_value('SMTP_ENCRYPTION'), false, array(), 'value', 180, 'defaultfont');

				$_smtp_table->setCol(4, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_encryption]'));
				$_smtp_table->setCol(4, 2, array('style' => 'text-align:left'), $_encryptSelect);


				$_auth_table = new we_html_table(array('class' => 'default', 'id' => 'auth_table', 'width' => 200, 'style' => 'display: ' . ((get_value('SMTP_AUTH') == 1) ? 'block' : 'none') . ';'), 4, 3);
				$_auth_table->setCol(0, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_username]'));
				$_auth_table->setCol(0, 2, array('style' => 'text-align:right'), we_html_tools::htmlTextInput('newconf[SMTP_USERNAME]', 14, get_value('SMTP_USERNAME'), 105, '', 'text', 105));
				$_auth_table->setCol(2, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_password]'));
				$_auth_table->setCol(2, 2, array('style' => 'text-align:right'), we_html_tools::htmlTextInput('newconf[SMTP_PASSWORD]', 14, get_value('SMTP_PASSWORD'), 105, '', 'password', 105));
				$_smtp_table->setCol(6, 0, array('class' => 'defaultfont', 'colspan' => 3), we_html_forms::checkbox(1, get_value('SMTP_AUTH'), 'newconf[SMTP_AUTH]', g_l('prefs', '[smtp_auth]'), false, 'defaultfont', "var el2 = document.getElementById('auth_table').style; if(this.checked) el2.display='block'; else el2.display='none';"));

				$_settings[] = array('headline' => g_l('prefs', '[mailer_type]'), 'html' => $_emailSelect, 'space' => 120, 'noline' => 1);
				$_settings[] = array('headline' => '', 'html' => $_smtp_table->getHtml(), 'space' => 120, 'noline' => 1);
			}

			return create_dialog('settings_email', /* g_l('prefs', '[email]'), */ $_settings);

		case 'versions':
			if(!permissionhandler::hasPerm('ADMINISTRATOR')){
				break;
			}

			$versionsPrefs = array(
				'ctypes' => array(
					we_base_ContentTypes::IMAGE => 'VERSIONING_IMAGE',
					we_base_ContentTypes::HTML => 'VERSIONING_TEXT_HTML',
					we_base_ContentTypes::WEDOCUMENT => 'VERSIONING_TEXT_WEBEDITION',
					we_base_ContentTypes::JS => 'VERSIONING_TEXT_JS',
					we_base_ContentTypes::CSS => 'VERSIONING_TEXT_CSS',
					we_base_ContentTypes::TEXT => 'VERSIONING_TEXT_PLAIN',
					we_base_ContentTypes::HTACESS => 'VERSIONING_TEXT_HTACCESS',
					we_base_ContentTypes::TEMPLATE => 'VERSIONING_TEXT_WETMPL',
					we_base_ContentTypes::FLASH => 'VERSIONING_FLASH',
					we_base_ContentTypes::QUICKTIME => 'VERSIONING_QUICKTIME',
					we_base_ContentTypes::VIDEO => 'VERSIONING_VIDEO',
					we_base_ContentTypes::AUDIO => 'VERSIONING_AUDIO',
					we_base_ContentTypes::APPLICATION => 'VERSIONING_SONSTIGE',
					we_base_ContentTypes::XML => 'VERSIONING_TEXT_XML',
					we_base_ContentTypes::OBJECT_FILE => 'VERSIONING_OBJECT',
				),
				'other' => array(
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
				)
			);

			//js
			$jsCheckboxCheckAll = '';

			foreach($versionsPrefs['ctypes'] as $v){
				$jsCheckboxCheckAll .= 'document.getElementById("newconf[' . $v . ']").checked = checked;';
			}

			$js = we_html_element::jsElement('
function checkAll(val) {
	checked=(val.checked)?1:0;
	' . $jsCheckboxCheckAll . ';
}
');

			$_SESSION['weS']['versions']['logPrefs'] = array();
			foreach($versionsPrefs as $v){
				foreach($v as $val){
					$_SESSION['weS']['versions']['logPrefs'][$val] = get_value($val);
				}
			}

			$checkboxes = we_html_forms::checkbox(1, false, 'version_all', g_l('prefs', '[version_all]'), false, 'defaultfont', 'checkAll(this);') . '<br/>';

			foreach($versionsPrefs['ctypes'] as $k => $v){
				$checkboxes .= we_html_forms::checkbox(1, get_value($v), 'newconf[' . $v . ']', g_l('contentTypes', '[' . $k . ']'), false, 'defaultfont', 'checkAllRevert(this);') . '<br/>';
			}

			$_versions_time_days = new we_html_select(array(
				'name' => 'newconf[VERSIONS_TIME_DAYS]',
				'class' => 'weSelect'
				)
			);

			$_versions_time_days->addOption(-1, '');
			$_versions_time_days->addOption(secondsDay, g_l('prefs', '[1_day]'));
			for($x = 2; $x <= 31; $x++){
				$_versions_time_days->addOption(($x * secondsDay), sprintf(g_l('prefs', '[more_days]'), $x));
			}
			$_versions_time_days->selectOption(get_value('VERSIONS_TIME_DAYS'));


			$_versions_time_weeks = new we_html_select(array(
				'name' => 'newconf[VERSIONS_TIME_WEEKS]',
				'class' => 'weSelect')
			);
			$_versions_time_weeks->addOption(-1, '');
			$_versions_time_weeks->addOption(secondsWeek, g_l('prefs', '[1_week]'));
			for($x = 2; $x <= 52; $x++){
				$_versions_time_weeks->addOption(($x * secondsWeek), sprintf(g_l('prefs', '[more_weeks]'), $x));
			}
			$_versions_time_weeks->selectOption(get_value('VERSIONS_TIME_WEEKS'));


			$_versions_time_years = new we_html_select(array(
				'name' => 'newconf[VERSIONS_TIME_YEARS]',
				'class' => 'weSelect'
				)
			);
			$_versions_time_years->addOption(-1, '');
			$_versions_time_years->addOption(secondsYear, g_l('prefs', '[1_year]'));
			for($x = 2; $x <= 10; $x++){
				$_versions_time_years->addOption(($x * secondsYear), sprintf(g_l('prefs', '[more_years]'), $x));
			}
			$_versions_time_years->selectOption(get_value('VERSIONS_TIME_YEARS'));
			$_versions_anzahl = we_html_tools::htmlTextInput('newconf[VERSIONS_ANZAHL]', 24, get_value('VERSIONS_ANZAHL'), 5, '', 'text', 50, 0, '');

			$_versions_create_publishing = we_html_forms::radiobutton(1, (get_value('VERSIONS_CREATE') == 1), 'newconf[VERSIONS_CREATE]', g_l('prefs', '[versions_create_publishing]'), true, 'defaultfont', '', false, '');
			$_versions_create_always = we_html_forms::radiobutton(0, (get_value('VERSIONS_CREATE') == 0), 'newconf[VERSIONS_CREATE]', g_l('prefs', '[versions_create_always]'), true, 'defaultfont', '', false, '');

			$_versions_time_days_tmpl = new we_html_select(array(
				'name' => 'newconf[VERSIONS_TIME_DAYS_TMPL]',
				'class' => 'weSelect'
				)
			);

			$_versions_time_days_tmpl->addOption(-1, '');
			$_versions_time_days_tmpl->addOption(secondsDay, g_l('prefs', '[1_day]'));
			for($x = 2; $x <= 31; $x++){
				$_versions_time_days_tmpl->addOption(($x * secondsDay), sprintf(g_l('prefs', '[more_days]'), $x));
			}
			$_versions_time_days_tmpl->selectOption(get_value('VERSIONS_TIME_DAYS_TMPL'));


			$_versions_time_weeks_tmpl = new we_html_select(array(
				'name' => 'newconf[VERSIONS_TIME_WEEKS_TMPL]',
				'class' => 'weSelect')
			);
			$_versions_time_weeks_tmpl->addOption(-1, '');
			$_versions_time_weeks_tmpl->addOption(secondsWeek, g_l('prefs', '[1_week]'));
			for($x = 2; $x <= 52; $x++){
				$_versions_time_weeks_tmpl->addOption(($x * secondsWeek), sprintf(g_l('prefs', '[more_weeks]'), $x));
			}
			$_versions_time_weeks_tmpl->selectOption(get_value('VERSIONS_TIME_WEEKS_TMPL'));

			$_versions_time_years_tmpl = new we_html_select(array(
				'name' => 'newconf[VERSIONS_TIME_YEARS_TMPL]',
				'class' => 'weSelect'
				)
			);
			$_versions_time_years_tmpl->addOption(-1, '');
			$_versions_time_years_tmpl->addOption(secondsYear, g_l('prefs', '[1_year]'));
			for($x = 2; $x <= 10; $x++){
				$_versions_time_years_tmpl->addOption(($x * secondsYear), sprintf(g_l('prefs', '[more_years]'), $x));
			}
			$_versions_time_years_tmpl->selectOption(get_value('VERSIONS_TIME_YEARS_TMPL'));
			$_versions_anzahl_tmpl = we_html_tools::htmlTextInput('newconf[VERSIONS_ANZAHL_TMPL]', 24, get_value('VERSIONS_ANZAHL_TMPL'), 5, '', 'text', 50, 0, '');
			$_versions_create_tmpl_publishing = we_html_forms::radiobutton(1, (get_value('VERSIONS_CREATE_TMPL') == 1), 'newconf[VERSIONS_CREATE_TMPL]', g_l('prefs', '[versions_create_tmpl_publishing]'), true, 'defaultfont', '', false, '');
			$_versions_create_tmpl_always = we_html_forms::radiobutton(0, (get_value('VERSIONS_CREATE_TMPL') == 0), 'newconf[VERSIONS_CREATE_TMPL]', g_l('prefs', '[versions_create_tmpl_always]'), true, 'defaultfont', '', false, '');
			$_versions_wizard = '<div style="float:left;">' . we_html_button::create_button('openVersionWizard', 'javascript:openVersionWizard()', true, 100, 22, '', '') . '</div>';


			$_settings = array(
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_activate_text]'), we_html_tools::TYPE_INFO, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[ContentType]'),
					'space' => 170,
					'html' => $checkboxes
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_time_text]'), we_html_tools::TYPE_INFO, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'html' => $_versions_time_days->getHtml() . ' ' . $_versions_time_weeks->getHtml() . ' ' . $_versions_time_years->getHtml(),
					'space' => 170,
					'headline' => g_l('prefs', '[versioning_time]')
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_anzahl_text]'), we_html_tools::TYPE_INFO, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[versioning_anzahl]'),
					'html' => $_versions_anzahl,
					'space' => 170
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_create_text]'), we_html_tools::TYPE_INFO, 470, false),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[versioning_create]'),
					'html' => $_versions_create_publishing . '<br/>' . $_versions_create_always,
					'space' => 170
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_templates_text]'), we_html_tools::TYPE_INFO, 470, false),
					'noline' => 0,
					'space' => 0
				),
				array(
					'html' => $_versions_time_days_tmpl->getHtml() . ' ' . $_versions_time_weeks_tmpl->getHtml() . ' ' . $_versions_time_years_tmpl->getHtml(),
					'space' => 170,
					'noline' => 1,
					'headline' => g_l('prefs', '[versioning_time]')
				),
				array(
					'headline' => g_l('prefs', '[versioning_anzahl]'),
					'html' => $_versions_anzahl_tmpl,
					'noline' => 1,
					'space' => 170
				),
				array(
					'headline' => g_l('prefs', '[versioning_create]'),
					'html' => $_versions_create_tmpl_publishing . '<br/>' . $_versions_create_tmpl_always,
					'space' => 170
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_wizard_text]'), we_html_tools::TYPE_INFO, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[versioning_wizard]'),
					'html' => $_versions_wizard,
					'space' => 170
				),
			);

			return create_dialog('', /* g_l('prefs', '[tab][validation]'), */ $_settings, -1, '', '', $js);
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
		$ret .= we_html_element::htmlDiv(array('id' => 'setting_' . $tab, 'style' => ($GLOBALS['tabname'] === 'setting_' . $tab ? '' : 'display: none;')), build_dialog($tab));
	}
	return $ret;
}

function getYesNoSelect($setting){
	$select = new we_html_select(array('name' => 'newconf[' . $setting . ']', 'class' => 'weSelect'));
	$select->addOption(0, g_l('prefs', '[no]'));
	$select->addOption(1, g_l('prefs', '[yes]'));
	$select->selectOption(get_value($setting) ? 1 : 0);

	return $select->getHtml();
}

function getTrueFalseSelect($setting){
	$select = new we_html_select(array('name' => 'newconf[' . $setting . ']', 'class' => 'weSelect'));
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
				$acResponse = $acQuery->getItemById($doc, FILE_TABLE, array('IsFolder'));
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
				$acResponse = $acQuery->getItemById($obj, OBJECT_FILES_TABLE, array('IsFolder'));
				if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
					$acError = true;
					$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
				}
			}
			break;
	}
	// check sidebar document
	if(!we_base_request::_(we_base_request::BOOL, 'newconf', false, 'SIDEBAR_DISABLED') && we_base_request::_(we_base_request::FILE, 'ui_sidebar_file_name')){
		$acResponse = $acQuery->getItemById(we_base_request::_(we_base_request::INT, 'newconf', 0, 'SIDEBAR_DEFAULT_DOCUMENT'), FILE_TABLE, array('IsFolder'));
		if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[sidebar]') . ' / ' . g_l('prefs', '[sidebar_document]'), g_l('prefs', '[tab][ui]')) . "\\n";
		}
	}
	// check doc for error on none existing objects
	if(we_base_request::_(we_base_request::FILE, 'error_document_no_objectfile_text')){
		$acResponse = $acQuery->getItemById(we_base_request::_(we_base_request::INT, 'newconf', 0, 'ERROR_DOCUMENT_NO_OBJECTFILE'), FILE_TABLE, array('IsFolder'));
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
?>
<script><!--
	var hot = false;
	var g_l = {
		language_already_exists: '<?php echo we_message_reporting::prepareMsgForJS(g_l('prefs', '[language_already_exists]')); ?>',
		language_country_missing: '<?php echo we_message_reporting::prepareMsgForJS(g_l('prefs', '[language_country_missing]')); ?>',
		cannot_delete_default_language: '<?php echo we_message_reporting::prepareMsgForJS(g_l('prefs', '[cannot_delete_default_language]')); ?>',
		max_name_recipient: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[max_name_recipient]')); ?>',
		recipient_exists: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[recipient_exists]')); ?>',
		not_entered_recipient: '<?php echo we_message_reporting::prepareMsgForJS(g_l('alert', '[not_entered_recipient]')); ?>',
		add_dictionary_question: '<?php echo g_l('prefs', '[add_dictionary_question]'); ?>',
		delete_recipient: '<?php echo g_l('alert', '[delete_recipient]'); ?>',
		recipient_new_name: '<?php echo g_l('alert', '[recipient_new_name]'); ?>',
		input_name: '<?php echo g_l('alert', '[input_name]'); ?>'
	};
	var modules = {
		SPELLCHECKER: '<?php echo intval(defined('SPELLCHECKER')); ?>'
	};
	var tables = {
		OBJECT_FILES_TABLE: '<?php echo (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''); ?>',
		FILE_TABLE: '<?php echo (defined('FILE_TABLE') ? FILE_TABLE : ''); ?>'
	};
	var perms = {
		CAN_SELECT_OTHER_USERS_FILES: '<?php echo (permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES') ? 0 : 1); ?>'
	};
	var contentTypes = {
		WEDOCUMENT: '<?php echo we_base_ContentTypes::WEDOCUMENT; ?>'
	};
	top.WE.g_l.prefs = g_l;
	var args = "";
	var url = "<?php echo WEBEDITION_DIR; ?> 'we_cmd.php?";
//--></</script><?php
echo STYLESHEET . we_html_element::jsScript(JS_DIR . 'windows.js') . weSuggest::getYuiFiles() .
 we_html_element::jsScript(JS_DIR . 'preferences.js');
if($doSave && !$acError){
	save_all_values();

	echo
	we_html_element::jsElement('
function doClose() {

var _multiEditorreload = false;
' . $save_javascript .
		(!$email_saved ? we_message_reporting::getShowMessageCall(g_l('prefs', '[error_mail_not_saved]'), we_message_reporting::WE_MESSAGE_ERROR) : we_message_reporting::getShowMessageCall(g_l('prefs', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE)) . '
var childs=top.document.getElementById("tabContainer").children;
childs[0].className="tabActive";
for(i=1;i<childs.length;++i){
	childs[i].className="tabNormal";
	}

	this.location = "' . WE_INCLUDES_DIR . 'we_editors/we_preferences.php";
	setTimeout(function(){
	top.document.getElementById("tabContainer").children[0].click();
	}, 1000);


	}
	') .
	'</head>' .
	we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'doClose()'), build_dialog('saved')) . '</html>';
} else {
	$_form = we_html_element::htmlForm(array('onSubmit' => 'return false;', 'name' => 'we_form', 'method' => 'post', 'action' => $_SERVER['SCRIPT_NAME']), we_html_element::htmlHidden('save_settings', 0) . render_dialog());

	$_we_cmd_js = we_html_element::jsElement('function we_cmd(){
		 for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
switch (arguments[0]){
case "browse_server":
new jsWindow(url,"browse_server",-1,-1,840,400,true,false,true);
break;
case "we_selector_image":
case "we_selector_document":
new jsWindow(url,"we_selector_document",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,false,true,true);
break;
case "show_formmail_log":
url = "' . WE_INCLUDES_DIR . 'we_editors/weFormmailLog.php"
new jsWindow(url,"we_selector_document",-1,-1,840,400,true,false,true);
break;
case "show_formmail_block_log":
url = "' . WE_INCLUDES_DIR . 'we_editors/weFormmailBlockLog.php"
new jsWindow(url,"we_selector_document",-1,-1,840,400,true,false,true);
break;
case "openColorChooser":
new jsWindow(url,"we_colorChooser",-1,-1,430,370,true,true,true);
break;

default:
			var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			parent.we_cmd.apply(this, args);

}
}

function setColorField(name) {
document.getElementById("color_" + name).style.backgroundColor=document.we_form.elements[name].value;
}' . ($acError ? we_message_reporting::getShowMessageCall(g_l('alert', '[field_in_tab_notvalid_pre]') . "\\n\\n" . $acErrorMsg . "\\n" . g_l('alert', '[field_in_tab_notvalid_post]'), we_message_reporting::WE_MESSAGE_ERROR) : ""));


	echo $_we_cmd_js . '</head>' .
	we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => 'startPrefs();'), $_form) .
	$yuiSuggest->getYuiJs() .
	'</html>';
}