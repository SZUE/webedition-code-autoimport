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
we_html_tools::protect();

function getContent(){
	return '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">' .
		we_html_tools::htmlDialogLayout('
						<table class="default">
							<tr><td class="defaultfont">' . g_l('global', '[oldPass]') . '</td></tr>
							<tr><td style="padding-bottom:5px;">' . we_html_tools::htmlTextInput('oldpasswd', 20, '', 32, '', 'password', 200) . '</td></tr>
							<tr><td class="defaultfont">' . g_l('global', '[newPass]') . '</td></tr>
							<tr><td style="padding-bottom:5x;">' . we_html_tools::htmlTextInput('newpasswd', 20, '', 32, 'onchange="comparePwd(\'newpasswd\',\'newpasswd2\')"', 'password', 200) . '</td></tr>
							<tr><td class="defaultfont">' . g_l('global', '[newPass2]') . '</td></tr>
							<tr><td>' . we_html_tools::htmlTextInput('newpasswd2', 20, '', 32, 'onchange="comparePwd(\'newpasswd\',\'newpasswd2\')"', 'password', 200) . '</td></tr>
						</table>', g_l('global', '[changePass]'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:document.forms[0].submit();'), null, we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();'))
		) . we_html_element::htmlHidden("cmd", "ok") . '</form>';
}

function getLoad(){
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
			$DB_WE->query('UPDATE ' . USER_TABLE . ' SET passwd="' . $pwd . '" WHERE ID=' . $_SESSION["user"]['ID'] . ' AND username="' . $DB_WE->escape($_SESSION["user"]["Username"]) . '"');
			$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_changed]'), we_message_reporting::WE_MESSAGE_NOTICE) .
				'top.close();';
		}
	}
	return (isset($js) ? we_html_element::jsElement($js) : '');
}

echo we_html_tools::getHtmlTop(g_l('global', '[changePass]'), '', '', STYLESHEET .
	we_html_element::jsElement('
function saveOnKeyBoard() {
	document.forms[0].submit();
	return true;
}
function closeOnEscape() {
	return true;
}

function comparePwd(f1,f2){
	var pwd1=document.getElementsByName(f1)[0];
	var pwd2=document.getElementsByName(f2)[0];
	if(!(new RegExp("' . SECURITY_USER_PASS_REGEX . '").test(pwd1.value))){
		pwd1.classList.add("weMarkInputError");
	}else{
		pwd1.classList.remove("weMarkInputError");
	if(pwd1.value!=pwd2.value){
		pwd2.classList.add("weMarkInputError");
	}else{
		pwd2.classList.remove("weMarkInputError");
	}
	}
}

'), we_html_element::htmlBody(array('style' => 'position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;text-align:center;', 'onload' => 'self.focus();')
		, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
			, we_html_element::htmlExIFrame('passwdcontent', getContent(), 'position:absolute;top:0px;bottom:1px;left:0px;right:0px;overflow: hidden;', 'weDialogBody') .
			getLoad()
)));
