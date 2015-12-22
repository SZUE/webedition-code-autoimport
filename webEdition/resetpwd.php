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
echo we_html_tools::getHtmlTop('webEdition') . STYLESHEET . '</head><body>';

switch(we_base_request::_(we_base_request::STRING, 'type', '')){
	case 'mailreset':
		if(empty($_REQUEST['resetTok']) || empty($_SESSION['resetTok']) || $_REQUEST['resetTok'] != $_SESSION['resetTok']){
			echo 'Token ungültig';
			break;
		}
		echo we_tag('customerResetPassword', array('type' => "resetFromMail"), '', true);

		if(we_tag('ifNotCustomerResetPassword')){
			echo 'Passwortänderung fehlgeschlagen';
			if(we_tag('ifNotCustomerResetPassword', array('type' => "passwordMismatch"))){
				echo 'Die eingegebenen Passwörter stimmen nicht überein';
			} elseif(we_tag('ifNotCustomerResetPassword', array('type' => "token"))){
				echo 'Der Rücksetz-Link war fehlerhaft. Stellen Sie bitte sicher das der Link vollständig ist und der Link nur einmal verwendet wurde. Bitte fordern Sie erneut ein Passwort an - der Link ist bereits ungültig!';
				unset($_REQUEST['user']);
			}
		} else {
			echo 'Passwort erfolgreich geändert! <a href="' . WEBEDITION_DIR . '">Zur Login-Seite</a>';
			break;
		}

	default:
		$_SESSION['resetTok'] = md5(uniqid(__FILE__, true));
		if(($uid = we_base_request::_(we_base_request::INT, 'user', 0)) && ($token = we_base_request::_(we_base_request::STRING, 'token', 0))){
			echo '<h2>Passwort zurücksetzen</h2>
<form method="post" action="' . WEBEDITION_DIR . 'resetpwd.php">
	<table>
		<tr><td>Passwort</td><td><input type="password" name="s[Password]"/></td></tr>
		<tr><td>Passwort wiederholen</td><td><input type="password" name="s[Password2]"/></td></tr>
		<tr><td colspan="2"><input type="submit" value="Passwort ändern"/></td></tr>
	</table>
	<input type="hidden" name="type" value="mailreset"/>
	<input type="hidden" name="user" value="' . $uid . '"/>
	<input type="hidden" name="token" value="' . $token . '"/>
	<input type="hidden" name="resetTok" value="' . $_SESSION['resetTok'] . '"/>
</form>';
		} else {
			echo '<h2>Passwort zurücksetzen</h2>
<form method="post" action="' . WEBEDITION_DIR . 'resetpwd.php">
	<table>
		<tr><td>Benutzername</td><td><input type="text" name="s[username]"/></td></tr>
		<tr><td>Email</td><td><input type="email" name="s[Email]"/></td></tr>
		<tr><td colspan="2"><input type="submit" value="Passwort ändern"/></td></tr>
	</table>
	<input type="hidden" name="type" value="mail"/>
	<input type="hidden" name="resetTok" value="' . $_SESSION['resetTok'] . '"/>
</form>';
		}
		break;
	case 'mail':
		if(empty($_REQUEST['resetTok']) || empty($_SESSION['resetTok']) || $_REQUEST['resetTok'] != $_SESSION['resetTok']){
			echo 'Token ungültig';
			break;
		}
		echo we_tag('customerResetPassword', array('type' => "email", 'required' => "username,Email", 'customerEmailField' => "Email", 'loadFields' => "First,Second,username"), '', true);

		if(we_tag('ifNotCustomerResetPassword')){
			echo 'Passwortänderung fehlgeschlagen';
		} else {
			echo 'Eine Mail wurde an die Mailadresse ' . $_SESSION['webuser']['Email'] . ' versandt';

			we_mail($_SESSION['webuser']['Email'], 'webEdition Passwort zurücksetzen', $_SESSION['webuser']['First'] . ' ' . $_SESSION['webuser']['Second'] . ' (' . $_SESSION['webuser']['username'] . '),
Sie haben soeben ein neues Passwort für "' . getServerUrl() . '" angefordert.
Sollte dies nicht der Fall sein, ignorieren Sie diese Mail.

Sie können Ihr Passwort über den folgenden Link zurücksetzen:

' . we_tag('customerResetPasswordLink', array('plain' => true), '', true)
			);

			unset($_SESSION['webuser']);
		}
		break;
}
?>
</body>
</html>
