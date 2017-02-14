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

we_html_tools::protect();
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}

$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION['user']["ID"], $_SESSION['user']["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);


$mode = we_base_request::_(we_base_request::STRING, 'mode');
if($mode === 're'){
	$compose = new we_messaging_format('re', $messaging->selected_message);
	$heading = g_l('modules_messaging', '[reply_message]');
} else {
	if(substr($mode, 0, 2) === 'u_'){
		$u = str_replace(substr($mode, 0, 2), '', $mode);
	}
	$compose = new we_messaging_format('new');
	$heading = g_l('modules_messaging', '[new_message]');
}

$compose->set_login_data($_SESSION['user']["ID"], $_SESSION['user']["Username"]);

echo we_html_tools::getHtmlTop('Messaging System - ' . g_l('modules_messaging', '[new_message]'), '', '', we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging.js') .
	we_base_jsCmd::singleCmd('setTrans', $transaction), we_html_element::htmlBody([
		'class' => "weDialogBody",
		'onload' => "document.compose_form.mn_body.focus()",
		'onunload' => "WE().util.jsWindow.prototype.closeAll(window);"
		], '<form action="' . WE_MESSAGING_MODULE_DIR . 'messaging_send_nm.php" name="compose_form" method="post">' .
		we_html_element::htmlHiddens(['we_transaction' => $transaction,
			'rcpts_string' => '',
			'mode' => $mode
		]) .
		we_html_tools::htmlDialogLayout('<table style="text-align:center;width:100%" cellpadding="6">
      <tr><td class="defaultfont lowContrast">' . g_l('modules_messaging', '[from]') . ':</td><td class="defaultfont">' . $compose->get_from() . '</td></tr>
      <tr><td class="defaultfont lowContrast"><a href="javascript:selectRecipient()">' . g_l('modules_messaging', '[recipients]') . ':</a></td><td>' . we_html_tools::htmlTextInput('mn_recipients', 40, (!isset($u) ? $compose->get_recipient_line() : $u)) . '</td></tr>
      <tr><td class="defaultfont lowContrast">' . g_l('modules_messaging', '[subject]') . ':</td><td>' . we_html_tools::htmlTextInput('mn_subject', 40, $compose->get_subject()) . '</td></tr>
      <tr><td colspan="2"><textarea cols="68" rows="15" name="mn_body" style="width:605px">' . $compose->get_msg_text() . '</textarea></td></tr>
    </table>', "<div style='padding:6px'>" . $heading . "</div>", we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_send()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close()")
			), "100%", 24, "", "hidden") .
		'</form>'
	)
);
