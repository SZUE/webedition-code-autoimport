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

if(($hour = we_base_request::_(we_base_request::INT, 'td_deadline_hour')) !== false){
	$deadline = mktime($hour, we_base_request::_(we_base_request::INT, 'td_deadline_minute'), 0, we_base_request::_(we_base_request::INT, 'td_deadline_month'), we_base_request::_(we_base_request::INT, 'td_deadline_day'), we_base_request::_(we_base_request::INT, 'td_deadline_year'));
}

switch(we_base_request::_(we_base_request::STRING, "mode")){
	case 'forward':
		$arr = ['rcpts_string' => we_base_request::_(we_base_request::STRING, 'rcpts_string'),
			'deadline' => $deadline,
			'body' => we_base_request::_(we_base_request::STRING, 'mn_body')
		];
		$res = $messaging->forward($arr);
		$heading = g_l('modules_messaging', '[forwarding_todo]');
		$action = g_l('modules_messaging', '[forwarded_to]');
		$s_action = g_l('modules_messaging', '[todo_s_forwarded]');
		$n_action = g_l('modules_messaging', '[todo_n_forwarded]');
		break;
	case 'reject':
		$arr = ['body' => we_base_request::_(we_base_request::STRING, 'mn_body')
		];
		$res = $messaging->reject($arr);
		$heading = g_l('modules_messaging', '[rejecting_todo]');
		$action = g_l('modules_messaging', '[rejected_to]');
		$s_action = g_l('modules_messaging', '[todo_s_rejected]');
		$n_action = g_l('modules_messaging', '[todo_n_rejected]');
		break;
	default:
		$arr = ['rcpts_string' => we_base_request::_(we_base_request::STRING, 'rcpts_string'),
			'subject' => we_base_request::_(we_base_request::STRING, 'mn_subject'),
			'body' => we_base_request::_(we_base_request::STRING, 'mn_body'),
			'deadline' => $deadline,
			'status' => 0,
			'priority' => we_base_request::_(we_base_request::INT, 'mn_priority')
		];
		$res = $messaging->send($arr, "we_todo");
		$heading = g_l('modules_messaging', '[creating_todo]');
		$s_action = g_l('modules_messaging', '[todo_s_created]');
		$n_action = g_l('modules_messaging', '[todo_n_created]');
		break;
}

$res['ok'] = array_map('oldHtmlspecialchars', $res['ok']);
$res['failed'] = array_map('oldHtmlspecialchars', $res['failed']);
$res['err'] = array_map('oldHtmlspecialchars', $res['err']);

echo we_html_tools::getHtmlTop($heading, '', '', we_html_element::jsElement('top.opener.top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=messaging&pnt=cmd&mcmd=refresh_mwork&we_transaction=' . we_base_request::_(we_base_request::TRANSACTION, 'we_transaction') . '";' .
		($res['ok'] ? '
if (opener && opener.top && opener.top.content) {
	top.opener.top.content.update_messaging();
}' : '')
	), we_html_element::htmlBody(['class' => "weDialogBody"], we_html_tools::htmlDialogLayout('<table style="text-align:center">
		    <tr>
		      <td class="defaultfont" style="vertical-align:top">' . $s_action . ':</td>
		      <td class="defaultfont"><ul><li>' . (empty($res['ok']) ? g_l('modules_messaging', '[nobody]') : implode("</li>\n<li>", $res['ok'])) . '</li></ul></td>
		    </tr>
		    ' . (empty($res['failed']) ? '' : '<tr>
		        <td class="defaultfont" style="vertical-align:top">' . $n_action . ':</td>
		        <td class="defaultfont"><ul><li>' . implode("</li>\n<li>", $res['failed']) . '</li></ul></td>
		    </tr>') .
			(empty($res['err']) ? '' : '<tr>
		        <td class="defaultfont" style="vertical-align:top">' . g_l('modules_messaging', '[occured_errs]') . ':</td>
		        <td class="defaultfont"><ul><li>' . implode('</li><li>', $res['err']) . '</li></ul></td>
		    </tr>') . '
	    </table>
	', $heading, we_html_button::create_button(we_html_button::OK, "javascript:top.window.close()"), "100%", 30, "", "hidden")
));
