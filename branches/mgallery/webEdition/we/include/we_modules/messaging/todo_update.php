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
$heading = 'ToDo Status-update ...';
$arr = array('deadline' => mktime(we_base_request::_(we_base_request::INT, 'td_deadline_hour'), we_base_request::_(we_base_request::INT, 'td_deadline_minute'), 0, we_base_request::_(we_base_request::INT, 'td_deadline_month'), we_base_request::_(we_base_request::INT, 'td_deadline_day'), we_base_request::_(we_base_request::INT, 'td_deadline_year')));

$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);

if(($stat = we_base_request::_(we_base_request::INT, 'todo_status')) != $messaging->selected_message['hdrs']['status']){
	$arr['todo_status'] = $stat;
}

if(($com = we_base_request::_(we_base_request::STRING, 'todo_comment'))){
	$arr['todo_comment'] = $com;
}

$arr['todo_priority'] = we_base_request::_(we_base_request::INT, 'todo_priority');

$res = $messaging->used_msgobjs['we_todo']->update_status($arr, $messaging->selected_message['int_hdrs']);

$messaging->get_fc_data($messaging->Folder_ID, '', '', 0);

$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);
echo we_html_tools::getHtmlTop($heading) .
 STYLESHEET . we_html_element::jsElement('
			if (opener && opener.top && opener.top.content) {
				top.opener.top.content.update_messaging();
			}');
?>
</head>

<body class="weDialogBody">
	<?php
	$tbl = '<table style="text-align:center" class="default" width="100%">
					<tr>
						<td class="defaultfont" style="text-align:center">
							' . $res['msg'] . '</td>
					</tr>
				</table>';
	echo we_html_tools::htmlDialogLayout($tbl, $heading, we_html_button::create_button(we_html_button::OK, "javascript:top.window.close()"), "100%", 30, "", "hidden");
	?>
</body>

</html>