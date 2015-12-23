<?php
/**
 * webEdition CMS
 *
 * $Rev: 9501 $
 * $Author: lukasimhof $
 * $Date: 2015-03-10 12:41:50 +0100 (Di, 10. Mär 2015) $
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
require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');
echo we_html_tools::getHtmlTop('webEdition') .
 STYLESHEET .
 we_html_element::cssLink(CSS_DIR . 'loginScreen.css') .
 '</head><body id="loginScreen">';

function defaultReset(){
	$_SESSION['resetTok'] = md5(uniqid(__FILE__, true));
	echo
	we_html_element::htmlDiv(array('style' => 'float: left;height: 50%;width: 1px;')) . we_html_element::htmlDiv(array('style' => 'clear:left;position:relative;top:-25%;'), we_html_element::htmlForm(array("action" => WEBEDITION_DIR . 'resetpwd.php', 'method' => 'post'), '
	<table id="mainTable">
		<tr><td colspan="2"><h2>' . g_l('global', '[changePass]') . '</h2></td></tr>
		<tr><td>' . g_l('global', '[username]') . '</td><td><input type="text" name="s[username]"/></td></tr>
		<tr><td>' . g_l('modules_users', '[email]') . '</td><td><input type="email" name="s[Email]"/></td></tr>
		<tr><td></td><td></td><td>' . we_html_button::create_button(we_html_button::SAVE, 'javascript:submit();') . '</td></tr>
	</table>
	<input type="hidden" name="type" value="mail"/>
	<input type="hidden" name="resetTok" value="' . $_SESSION['resetTok'] . '"/>'
	));
}

function resetPwd(){
	$uid = we_base_request::_(we_base_request::INT, 'user', 0);
	$token = we_base_request::_(we_base_request::STRING, 'token', 0);
	$_SESSION['resetTok'] = md5(uniqid(__FILE__, true));
	echo we_html_element::htmlDiv(array('style' => 'float: left;height: 50%;width: 1px;')) . we_html_element::htmlDiv(array('style' => 'clear:left;position:relative;top:-25%;'), we_html_element::htmlForm(array("action" => WEBEDITION_DIR . 'resetpwd.php', 'method' => 'post'), '
	<table id="mainTable">
		<tr><td colspan="2"><h2>' . g_l('global', '[changePass]') . '</h2></td></tr>
		<tr><td>' . g_l('global', '[newPass]') . '</td><td><input type="password" name="s[Password]"/></td></tr>
		<tr><td>' . g_l('global', '[newPass2]') . '</td><td><input type="password" name="s[Password2]"/></td></tr>
		<tr><td></td><td></td><td>' . we_html_button::create_button(we_html_button::SAVE, 'javascript:submit();') . '</td></tr>
	</table>
	<input type="hidden" name="type" value="mailreset"/>
	<input type="hidden" name="user" value="' . $uid . '"/>
	<input type="hidden" name="token" value="' . $token . '"/>
	<input type="hidden" name="resetTok" value="' . $_SESSION['resetTok'] . '"/>
'));
}

switch(we_base_request::_(we_base_request::STRING, 'type', '')){
	case 'mailreset':
		if(empty($_REQUEST['resetTok']) || empty($_SESSION['resetTok']) || $_REQUEST['resetTok'] != $_SESSION['resetTok']){
			echo g_l('global', '[CSRF][tokenInvalid]');
			break;
		}
		echo we_tag('customerResetPassword', array('type' => "resetFromMail", 'passwordRule' => '(.{6,20})'), '', true);

		if(we_tag('ifNotCustomerResetPassword')){
			echo g_l('global', '[pwd][changeFailed]') . '<br/>';
			if(we_tag('ifNotCustomerResetPassword', array('type' => "passwordMismatch"))){
				echo g_l('global', '[pass_not_confirmed]');
			} elseif(we_tag('ifNotCustomerResetPassword', array('type' => "passwordRule"))){
				echo g_l('global', '[pass_to_short]');
			} else if(we_tag('ifNotCustomerResetPassword', array('type' => 'token'))){
				echo g_l('global', '[pwd][invalidToken]');
				unset($_REQUEST['user']);
				defaultReset();
				break;
			}
			resetPwd();
		} else {
			echo g_l('global', '[pass_changed]') . '<a href="' . WEBEDITION_DIR . '">Zur Login-Seite</a>';
		}
		break;

	default:
		if(($uid = we_base_request::_(we_base_request::INT, 'user', 0)) && ($token = we_base_request::_(we_base_request::STRING, 'token', 0))){
			resetPwd();
		} else {
			defaultReset();
		}
		break;
	case 'mail':
		if(empty($_REQUEST['resetTok']) || empty($_SESSION['resetTok']) || $_REQUEST['resetTok'] != $_SESSION['resetTok']){
			echo g_l('global', '[CSRF][tokenInvalid]');
			break;
		}
		echo we_tag('customerResetPassword', array('type' => "email", 'required' => "username,Email", 'customerEmailField' => "Email", 'loadFields' => "First,Second,username"), '', true);

		if(we_tag('ifNotCustomerResetPassword')){
			echo g_l('global', '[pwd][changeFailed]') . '<br/>';
			if(we_tag('ifNotCustomerResetPassword', array('type' => "userNotExists"))){
				echo g_l('global', '[pwd][noSuchUser]');
			}
			defaultReset();
		} else {
			printf(g_l('global', '[pwd][noSuchUser]'), $_SESSION['webuser']['Email']);

			we_mail($_SESSION['webuser']['Email'], g_l('global', '[pwd][mailSubject]'), $_SESSION['webuser']['First'] . ' ' . $_SESSION['webuser']['Second'] . ' (' . $_SESSION['webuser']['username'] . '),
' . sprintf(g_l('global', '[pwd][resetMail]')), getServerUrl() . "\n" .
				we_tag('customerResetPasswordLink', array('plain' => true), '', true)
			);

			unset($_SESSION['webuser']);
		}
		break;
}
?>
</body>
</html>
