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
//	build table for login screen.

abstract class we_dialog_info{

	public static function getDialog($isLogin = false, $loginSuccess = false){
		$we_version = '';
		if(!$isLogin){
			$we_version .= ((defined('WE_VERSION_NAME') && WE_VERSION_NAME != '') ? WE_VERSION_NAME : WE_VERSION) . ' (' . WE_VERSION .
				((defined('WE_SVNREV') && WE_SVNREV != '0000') ? ', SVN-Revision: ' . WE_SVNREV : '') . (defined('WE_VERSION_HOTFIX_NR') && WE_VERSION_HOTFIX_NR ? ' , h' . WE_VERSION_HOTFIX_NR : '') . ')' .
				((defined('WE_VERSION_SUPP') && WE_VERSION_SUPP != '') ? ' ' . g_l('global', '[' . WE_VERSION_SUPP . ']') : '') .
				((defined('WE_VERSION_SUPP_VERSION') && WE_VERSION_SUPP_VERSION != 0) ? WE_VERSION_SUPP_VERSION : '');
		}

		if($isLogin && WE_LOGIN_HIDEWESTATUS){

		} else {
			switch(strtolower(WE_VERSION_SUPP)){
				case "rc":
					$extra = "RC";
					break;
				case "alpha":
					$extra = "ALPHA";
					break;
				case "beta":
					$extra = "BETA";
					break;
				case "nightly":
				case "weekly":
				case "nightly-build":
					$extra = "NIGHTLY";
					break;
				case "preview":
				case "dp":
					$extra = "PREVIEW";
					break;
				case "trunk":
				case "svn":
					$extra = "SVN";
					break;
			}
		}
		$table = new we_html_table(['id' => 'mainTable'], 8, 1);
		$actRow = 0;
//	First row with background
		$table->setCol($actRow++, 0, ['class' => 'logo'], '<a href="http://www.webedition.org" target="_blank"  title="www.webedition.org"><img src="' . IMAGE_DIR . 'webedition.svg"/></a>' . (isset($extra) ? '<div id="versionSpec">' . $extra . '</div>' : ''));

		if($we_version){
//	3rd Version
			$table->setCol($actRow++, 0, ['class' => 'small row5'], "Version: " . $we_version);
		}


//	5th credits
		$table->setCol($actRow++, 0, ['class' => "defaultfont small row5"], '<div id="credits">' .
			g_l('global', '[developed_further_by]') . ': <a href="http://www.webedition.org/" target="_blank" ><strong>webEdition e.V.</strong></a>' /* .
			  g_l('global', '[with]') . ' <b><a href="http://credits.webedition.org/?language=' . $GLOBALS["WE_LANGUAGE"] . '" target="_blank" >' . g_l('global', '[credits_team]') . '</a></b>' */);

//	7th agency
		if(is_readable(WEBEDITION_PATH . 'agency.php')){
			include_once(WEBEDITION_PATH . 'agency.php');
			$table->setCol($actRow++, 0, ['class' => "defaultfont small row10"], $_agency);
		}

		$loginRow = 0;

		if($loginSuccess){
			$loginTable = new we_html_table(['class' => "plainTable"], 4, 1);
			$loginTable->setCol($loginRow++, 0, ['class' => "small"], we_html_baseElement::getHtmlCode(new we_html_baseElement('label', true, ["for" => "username"], g_l('global', '[username]'))));
			$loginTable->setCol($loginRow++, 0, [], we_html_tools::htmlTextInput('WE_LOGIN_username', 25, '', 255, 'id="username" placeholder="' . g_l('global', '[username]') . '" ', 'text', 0, 0));
			$loginTable->setCol($loginRow++, 0, ['class' => "small row5"], we_html_baseElement::getHtmlCode(new we_html_baseElement('label', true, ["for" => 'password'], g_l('global', '[password]'))));
			$loginTable->setCol($loginRow++, 0, [], we_html_tools::htmlTextInput('WE_LOGIN_password', 25, '', 255, 'id="password" placeholder="' . g_l('global', '[password]') . '" ', 'password', 0, 0));
			$loginTable->setCol($loginRow++, 0, [], '<a href="' . WEBEDITION_DIR . 'resetpwd.php">' . g_l('global', '[pwd][forgotten]') . '</a>');

			$table->addRow(2);
			$table->setCol($actRow++, 0, ['class' => 'spaceTable'], $loginTable->getHtml());


			//	mode-table
			$modetable = new we_html_table(['class' => 'plainTable modeTable'], 1, 3);

			$loginButton = we_html_button::create_button('fat:login,fa-lg fa-sign-in', we_html_button::WE_FORM . ':loginForm', '', 0, 0, "document.getElementById('mainTable').style.display='none';document.getElementById('loading').style.display='block';");
			if(!WE_SEEM){ //	deactivate See-Mode
				if(WE_LOGIN_WEWINDOW){
					$modetable->setCol(0, 0, [], '');
					$modetable->setCol(0, 1, ['style' => 'text-align:right;vertical-align:bottom;', "rowspan" => 2], (WE_LOGIN_WEWINDOW == 1 ? '<input type="hidden" name="popup" value="popup"/>' : '') . $loginButton);
				} else {
					$modetable->setCol(0, 0, [], we_html_forms::checkbox('popup', getValueLoginMode('popup'), 'popup', g_l('SEEM', '[popup]')));
					$modetable->setCol(0, 1, ['style' => 'text-align:right;vertical-align:bottom;', "rowspan" => 2], we_html_element::htmlHidden("mode", "normal") . $loginButton);
				}
			} else { //	normal login
				if(WE_SEEM){
//	15th Mode
					$table->setCol($actRow++, 0, ['class' => "small"], g_l('SEEM', '[start_mode]'));
				}
				switch(WE_LOGIN_WEWINDOW){
					case 0:
						$we_login_type = we_html_forms::checkbox('popup', getValueLoginMode('popup'), 'popup', g_l('SEEM', '[popup]'));
						break;
					case 1:
						$we_login_type = '<input type="hidden" name="popup" value="popup"/>';
						break;
					default:
						$we_login_type = '';
				}

				// if button is between these radio boces, they can not be reachable with <tab>
				$modetable->setCol(0, 0, [], '<table class="default">
		<tr><td>' . $we_login_type . '</td></tr>' .
					'<tr><td>' . we_html_forms::radiobutton(we_base_constants::MODE_NORMAL, getValueLoginMode(we_base_constants::MODE_NORMAL), 'mode', g_l('SEEM', '[start_mode_normal]'), true, 'small') .
					'</td></tr>
		<tr><td>' . we_html_forms::radiobutton(we_base_constants::MODE_SEE, getValueLoginMode(we_base_constants::MODE_SEE), 'mode', '<abbr title="' . g_l('SEEM', '[start_mode_seem_acronym]') . '">' . g_l('SEEM', '[start_mode_seem]') . '</abbr>', true, "small") .
					'</td></tr>
		</table>');
				$modetable->setCol(0, 1, ['style' => 'text-align:right;vertical-align:bottom', 'rowspan' => 3], $loginButton);
			}

			//	16th
			$table->setCol($actRow++, 0, ['class' => "small"], $modetable->getHtml());
		} else if($isLogin && !$loginSuccess){
			srand((double) microtime() * 1000000);
			$r = rand();

			$table->addRow(2);

			//	9th Login ok
			$table->setCol($actRow++, 0, ['class' => "small spaceTable"], g_l('global', '[loginok]'));

			//	11th back button
			$table->setCol($actRow++, 0, ["width" => (432 - 30), "class" => "small", 'style' => 'text-align:right;padding-bottom:15px'], we_html_button::create_button('back_to_login', WEBEDITION_DIR . 'index.php?r=' . $r));
		}

		return $table->getHtml() . (!$loginSuccess ? '' :
			we_html_element::htmlDiv(['id' => 'loading'], '<i class="fa fa-5x fa-spinner fa-pulse"></i>')
			);
	}

	public static function getFullDialog(){
		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::cssLink(CSS_DIR . 'loginScreen.css') .
			we_html_element::cssLink(CSS_DIR . 'infoScreen.css'), we_html_element::htmlBody([
				'id' => 'infoScreen',
				'onload' => "self.focus();"
				], self::getDialog()
		));
	}

}
