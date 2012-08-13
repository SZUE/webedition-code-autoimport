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
we_html_tools::protect();

we_html_tools::htmlTop(g_l('global', '[changePass]'));

function getContent(){
	return '

		<form target="passwdload" action="' . WEBEDITION_DIR . 'we_cmd.php" method="post">' .
		we_html_tools::htmlDialogLayout('
						<table border="0" cellpadding="0" cellspacing="0">
							<tr><td class="defaultfont">' . g_l('global', '[oldPass]') . '</td></tr>
							<tr><td>' . we_html_tools::htmlTextInput("oldpasswd", 20, "", "32", "", "password", 200) . '</td></tr>
							<tr><td>' . we_html_tools::getPixel(2, 5) . '</td></tr>
							<tr><td class="defaultfont">' . g_l('global', '[newPass]') . '</td></tr>
							<tr><td>' . we_html_tools::htmlTextInput("newpasswd", 20, "", "32", "", "password", 200) . '</td></tr>
							<tr><td>' . we_html_tools::getPixel(2, 5) . '</td></tr>
							<tr><td class="defaultfont">' . g_l('global', '[newPass2]') . '</td></tr>
							<tr><td>' . we_html_tools::htmlTextInput("newpasswd2", 20, "", "32", "", "password", 200) . '</td></tr>
						</table>', g_l('global', '[changePass]'), we_button::position_yes_no_cancel(
				we_button::create_button("save", "javascript:document.forms[0].submit();"), null, we_button::create_button("cancel", "javascript:top.close();"))
		) .
		'	<input type="hidden" name="cmd" value="ok" />
							<input type="hidden" name="we_cmd[0]" value="' . $_REQUEST['we_cmd'][0] . '" />
							<input type="hidden" name="we_cmd[1]" value="load" />' .
		'</form>';
}

function getLoad(){
	$DB_WE=$GLOBALS['DB_WE'];
	$oldpasswd = isset($_REQUEST["oldpasswd"]) ? $_REQUEST["oldpasswd"] : '';
	$newpasswd = isset($_REQUEST["newpasswd"]) ? $_REQUEST["newpasswd"] : '';
	$newpasswd2 = isset($_REQUEST["newpasswd2"]) ? $_REQUEST["newpasswd2"] : '';

	if(isset($_REQUEST["cmd"]) && ($_REQUEST["cmd"] == "ok")){
		$userData = getHash('SELECT UseSalt,passwd FROM ' . USER_TABLE . ' WHERE username="' . $DB_WE->escape($_SESSION["user"]["Username"]) . '"', $DB_WE);

		if(!we_user::comparePasswords($userData['UseSalt'], $_SESSION["user"]["Username"], $userData['passwd'], $oldpasswd)){
			$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_not_match]'), we_message_reporting::WE_MESSAGE_ERROR) . '
top.document.forms[0].elements["oldpasswd"].focus();
top.document.forms[0].elements["oldpasswd"].select();';
		} else if(strlen($newpasswd) < 4){
			$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_to_short]'), we_message_reporting::WE_MESSAGE_ERROR) . '
top.document.forms[0].elements["newpasswd"].focus();
top.document.forms[0].elements["newpasswd"].select();';
		} else if($newpasswd != $newpasswd2){
			$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_not_confirmed]'), we_message_reporting::WE_MESSAGE_ERROR) . '
top.document.forms[0].elements["newpasswd2"].focus();
top.document.forms[0].elements["newpasswd2"].select();';
		} else{
			$useSalt = 0;
			$DB_WE->query('UPDATE ' . USER_TABLE . ' SET passwd="' . $DB_WE->escape(we_user::makeSaltedPassword($useSalt, $_SESSION["user"]["Username"], $newpasswd)) . '", UseSalt=' . $useSalt . ' WHERE ID=' . $_SESSION["user"]['ID'] . ' AND username="' . $DB_WE->escape($_SESSION["user"]["Username"]) . '"');
			$js = we_message_reporting::getShowMessageCall(g_l('global', '[pass_changed]'), we_message_reporting::WE_MESSAGE_NOTICE) .
				'top.close();';
		}
	}
	return (isset($js) ? we_html_element::jsElement($js) : '') . '</head>	<body>';
}

function printFrameset(){
	print STYLESHEET .
		we_html_element::jsScript(JS_DIR . 'keyListener.js') .
		we_html_element::jsElement('
			function saveOnKeyBoard() {
				window.frames[0].document.forms[0].submit();
				return true;
			}
			function closeOnEscape() {
				return true;

			}

			self.focus();') .
		'</head>' .
		we_html_element::htmlBody(array('style' => 'margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;text-align:center;')
			, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
				, we_html_element::htmlExIFrame('passwdcontent', getContent(), 'position:absolute;top:0px;bottom:1px;left:0px;right:0px;overflow: hidden;') .
				we_html_element::htmlIFrame('passwdload', WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . (isset($_REQUEST['we_cmd'][0]) ? $_REQUEST['we_cmd'][0] : '') . '&we_cmd[1]=load', 'position:absolute;height:1px;bottom:0px;left:0px;right:0px;overflow: hidden;')
			)) . '</html>';
}

if(isset($_REQUEST['we_cmd'][1]) && ($_REQUEST['we_cmd'][1] == "load")){
	print getLoad() . '</body></html>';
} else{
	printFrameset();
}
