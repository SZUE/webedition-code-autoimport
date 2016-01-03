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
exit();
//currently unused

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();
$messaging = new we_messaging_messaging($_SESSION['weS']['we_data']['we_messagin_setting']);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data']['we_messagin_setting']);
echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[settings]')) .
 STYLESHEET;

if(we_base_request::_(we_base_request::STRING, 'mcmd') === 'save_settings' && ($cstep = we_base_request::_(we_base_request::STRING, 'check_step'))){
	if($messaging->save_settings(array('check_step' => $cstep))){
		echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE));
		?>
		</head>
		<body onload="window.close();"></body>
		</html>
		<?php
		exit;
	}
}
?>
<script><!--
function save() {
		document.settings.submit();
	}
//-->
</script>

<body class="weDialogBody">
	<form name="settings" action="<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_settings.php" method="post">
		<?php
		if(($transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'))){
			echo we_html_tools::hidden('we_transaction', $transaction);
		}
		echo we_html_tools::hidden('mcmd', 'save_settings');

		$heading = g_l('modules_messaging', '[settings]');
		$t_vals = array('-1' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '10' => 10, '15' => 15, '30' => 30, '45' => 45, '60' => 60);
		$check_step = $messaging->get_settings();

		$input_tbl = '<table>
<tr>
    <td class="defaultfont">' . g_l('modules_messaging', '[check_step]') . '</td>
    <td>' . we_html_tools::html_select('check_step', 1, $t_vals, $check_step) . '</td>
	<td class="defaultfont">' . g_l('modules_messaging', '[minutes]') . '</td>
</tr>
</table>';

		$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:save()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close();")
				)
		;

		echo we_html_tools::htmlDialogLayout($input_tbl, $heading, $_buttons);
		?>
	</form>
</body>
</html>