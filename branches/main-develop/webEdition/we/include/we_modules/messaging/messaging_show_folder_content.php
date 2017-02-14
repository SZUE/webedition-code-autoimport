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


$browser = we_base_browserDetect::inst();
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}

$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION['user']["ID"], $_SESSION['user']["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);

$tab = '';
$passed_dls = [];
foreach($messaging->selected_set as $key => $val){
	$tab .= '<tr onclick="check(\'' . $val['ID'] . '\')" style="cursor:pointer">
		<td id="td_' . $val['ID'] . '_cb" class="defaultfont" style="width:18px;text-align:left;padding-bottom:3px;"></td>';

	if($val['hdrs']['ClassName'] === 'we_todo'){
		if($val['hdrs']['Deadline'] < time()){
			$dl_passed = 1;
			$passed_dls[] = $val['ID'];
		} else {
			$dl_passed = 0;
		}

		$tab .= '<td id="td_' . $val['ID'] . '_0" style="width:200px;text-align:left" class="defaultfont">' . oldHtmlspecialchars($val['hdrs']['Subject']) . '</td>
			<td id="td_' . $val['ID'] . '_1" style="width:170px;text-align:left" class="defaultfont ' . ($dl_passed == 0 ? '' : 'defaultfontred') . '">' . date(g_l('date', '[format][default]'), $val['hdrs']['Deadline']) . '</td>
			<td id="td_' . $val['ID'] . '_2" style="width:140px;text-align:left" class="defaultfont"><a id="td_' . $val['ID'] . '_link_2" href="javascript:check(\'' . $val['ID'] . '\')">' . $val['hdrs']['Priority'] . '</a></td>
			<td id="td_' . $val['ID'] . '_3" style="width:40px;text-align:left" class="defaultfont">' . $val['hdrs']['status'] . '%</td>
			</tr>';
	} else {
		$tab .= '
				<td id="td_' . $val['ID'] . '_0" style="width:200px;text-align:left" class="defaultfont">' . oldHtmlspecialchars($val['hdrs']['Subject']) . '</td>
				<td id="td_' . $val['ID'] . '_1" style="width:170px;text-align:left" class="defaultfont">' . date(g_l('date', '[format][default]'), $val['hdrs']['Date']) . '</td>
				<td id="td_' . $val['ID'] . '_2" style="width:140px;text-align:left" class="defaultfont">' . $val['hdrs']['From'] . '</td>
				<td id="td_' . $val['ID'] . '_3" style="width:40px;text-align:left" class="defaultfont"><span class="fa fa-circle ' . ($val['hdrs']['seenStatus'] & we_messaging_proto::STATUS_READ ? 'msgRead' : 'msgUnRead') . '" name="read_' . $val['ID'] . '"></span></td>
			</tr>';
	}
}

echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement('
		var transaction="' . $transaction . '";
') .
	we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging_std.js') .
	we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/showFolder.js') .
	we_base_jsCmd::singleCmd('doHighLight', $passed_dls), we_html_element::htmlBody(['style' => "margin:5px 7px;"], '<table style="width:99%" class="default">' . $tab . '</table>')
);
