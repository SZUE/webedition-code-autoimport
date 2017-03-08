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
abstract class we_users_changePassword{

	private static function getContent(){
		return '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">' .
				we_html_tools::htmlDialogLayout('
						<table class="default">
							<tr><td class="defaultfont">' . g_l('global', '[oldPass]') . '</td></tr>
							<tr><td style="padding-bottom:5px;">' . we_html_tools::htmlTextInput('oldpasswd', 20, '', 32, '', 'password', 200) . '</td></tr>
							<tr><td>' . we_html_tools::htmlAlertAttentionBox(SECURITY_USER_PASS_DESC, we_html_tools::TYPE_INFO) . '</td></tr>
							<tr><td class="defaultfont">' . g_l('global', '[newPass]') . '</td></tr>
							<tr><td style="padding-bottom:5x;"><div id="badPwd" style="display:none;" class="arrow_box">' . g_l('global', '[pass_to_short]') . '</div>' . we_html_tools::htmlTextInput('newpasswd', 20, '', 32, 'onchange="setPwdErr(comparePwd(\'newpasswd\',\'newpasswd2\'));"', 'password', 200) . '</td></tr>
							<tr><td class="defaultfont"><div id="badPwd2" style="display:none;" class="arrow_box">' . g_l('global', '[pass_not_confirmed]') . '</div>' . g_l('global', '[newPass2]') . '</td></tr>
							<tr><td>' . we_html_tools::htmlTextInput('newpasswd2', 20, '', 32, 'onchange="setPwdErr(comparePwd(\'newpasswd\',\'newpasswd2\'));"', 'password', 200) . '</td></tr>
						</table>', g_l('global', '[changePass]'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:document.forms[0].submit();'), '', we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();'))
				) . we_html_element::htmlHidden('cmd', 'ok') . '</form>';
	}

	private static function getLoad(){
		$DB_WE = $GLOBALS['DB_WE'];
		$oldpasswd = we_base_request::_(we_base_request::RAW_CHECKED, 'oldpasswd', '');
		$newpasswd = we_base_request::_(we_base_request::RAW_CHECKED, 'newpasswd', '');
		$newpasswd2 = we_base_request::_(we_base_request::RAW_CHECKED, 'newpasswd2', '');

		if(we_base_request::_(we_base_request::STRING, 'cmd') === 'ok'){
			$userData = f('SELECT passwd FROM ' . USER_TABLE . ' WHERE username="' . $DB_WE->escape($_SESSION['user']['Username']) . '"');

			if(!we_users_user::comparePasswords($_SESSION['user']['Username'], $userData, $oldpasswd)){
				$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_not_match]'), we_message_reporting::WE_MESSAGE_ERROR) . '
top.document.forms[0].elements.oldpasswd.focus();
top.document.forms[0].elements.oldpasswd.select();';
			} else if(!preg_match('/' . addcslashes(SECURITY_USER_PASS_REGEX, '/') . '/', $newpasswd)){
				$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_to_short]'), we_message_reporting::WE_MESSAGE_ERROR) . '
top.document.forms[0].elements.newpasswd.focus();
top.document.forms[0].elements.newpasswd.select();';
			} else if($newpasswd != $newpasswd2){
				$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_not_confirmed]'), we_message_reporting::WE_MESSAGE_ERROR) . '
top.document.forms[0].elements.newpasswd2.focus();
top.document.forms[0].elements.newpasswd2.select();';
			} else {
				//essential leave this line
				$pwd = $DB_WE->escape(we_users_user::makeSaltedPassword($newpasswd));
				$DB_WE->query('UPDATE ' . USER_TABLE . ' SET passwd="' . $pwd . '" WHERE ID=' . $_SESSION['user']['ID'] . ' AND username="' . $DB_WE->escape($_SESSION['user']["Username"]) . '"');
				$cmd = new we_base_jsCmd();
				$cmd->addMsg(g_l('global', '[pass_changed]'), we_message_reporting::WE_MESSAGE_NOTICE);
				$cmd->addCmd('close');
				return $cmd->getCmds();
			}
		}
		return (isset($js) ? we_html_element::jsElement($js) : '');
	}

	public static function showDialog(){
		echo we_html_tools::getHtmlTop(g_l('global', '[changePass]'), '', '', we_html_element::jsScript(JS_DIR . 'comparePwd.js', '', ['id' => 'loadVarComparePwd', 'data-passwd' => setDynamicVar([
						'pwdCheck' => SECURITY_USER_PASS_REGEX
			])]), we_html_element::htmlBody(['class' => 'weDialogBody', 'onload' => 'self.focus();'], we_html_element::htmlExIFrame('passwdcontent', self::getContent(), 'position:absolute;top:0px;bottom:1px;left:0px;right:0px;') .
						self::getLoad()
		));
	}

}
