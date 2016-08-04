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
if(is_array($_SESSION['weS']['we_data'][$transaction])){

	$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
	$messaging->set_login_data($_SESSION['user']["ID"], $_SESSION['user']["Username"]);
	$messaging->init($_SESSION['weS']['we_data'][$transaction]);

	$arr = array(
		'rcpts_string' => we_base_request::_(we_base_request::EMAIL, 'rcpts_string'),
		'subject' => we_base_request::_(we_base_request::STRING, 'mn_subject'),
		'body' => we_base_request::_(we_base_request::STRING, 'mn_body')
	);

	$res = $messaging->send($arr);
} else {
	$errs = [];
	$rcpts = array(urldecode(we_base_request::_(we_base_request::STRING, 'rcpts_string'))); /* user names */
	$res = we_messaging_message::newMessage($rcpts, we_base_request::_(we_base_request::STRING, 'mn_subject'), we_base_request::_(we_base_request::STRING, 'mn_body'), $errs);
}

echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[message_send]'));

if($res['ok']){
	if(substr(we_base_request::_(we_base_request::STRING, "mode"), 0, 2) != 'u_'){
		echo we_html_element::jsElement('
                            if (opener && opener.top && opener.top.content) {
                                opener.top.content.update_messaging();
                            }');
	}
}
?>
</head>

<body class="weDialogBody">
	<?php
	$tbl = '<table style="text-align:center;width:100%" cellpadding="7" cellspacing="3">';
	if($res['ok']){
		$tbl .= '<tr>
<td class="defaultfont" style="vertical-align:top">' . g_l('modules_messaging', '[s_sent_to]') . ':</td>
<td class="defaultfont"><ul>';

		foreach($res['ok'] as $ok){
			$tbl .= '<li>' . oldHtmlspecialchars($ok) . '</li>';
		}

		$tbl .= '</ul></td></tr>';
	}

	if($res['failed']){
		$tbl .= '<tr>
<td class="defaultfont" style="vertical-align:top">' . g_l('modules_messaging', '[n_sent_to]') . ':</td>
<td class="defaultfont"><ul>';

		foreach($res['failed'] as $failed){
			$tbl .= '<li>' . oldHtmlspecialchars($failed) . '</li>';
		}

		$tbl .= '</ul>
</td></tr>';
	}

	if($res['err']){
		$tbl .= '<tr>
<td class="defaultfont" style="vertical-align:top">' . g_l('modules_messaging', '[occured_errs]') . ':</td>
<td class="defaultfont"><ul><li>' . implode('</li><li>', $res['err']) . '</li></ul></td>
</tr>';
	}

	$tbl .= '</table>';
	echo we_html_tools::htmlDialogLayout($tbl, g_l('modules_messaging', '[message_send]') . '...', we_html_button::create_button(we_html_button::OK, "javascript:window.close()"), "100%", 20, "", "hidden");
	?>
</body>

</html>