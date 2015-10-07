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
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);


echo we_html_tools::getHtmlTop('Messaging System - ' . g_l('modules_messaging', '[new_message]')) .
 STYLESHEET;
?>

<script><!--
	rcpt_sel = [];

	function update_rcpts() {
		var rcpt_str = "";

		for (i = 0; i < rcpt_sel.length; i++) {
			rcpt_str += rcpt_sel[i][2];
			if (i != rcpt_sel.length - 1) {
				rcpt_str += ", ";
			}
		}

		document.compose_form.mn_recipients.value = rcpt_str;
	}

	function selectRecipient() {
		var rs = encodeURI(document.compose_form.mn_recipients.value);

		new jsWindow("<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_usel.php?we_transaction=<?php echo $transaction; ?>&rs=" + rs, "messaging_usel", -1, -1, 530, 420, true, false, true, false);
		//	    opener.top.add_win(msg_usel);
	}

	function do_send() {
		rcpt_s = encodeURI(document.compose_form.mn_recipients.value);
		document.compose_form.rcpts_string.value = rcpt_s;
		document.compose_form.submit();
	}

//-->
</script>
</head>

<body class="weDialogBody" onload="document.compose_form.mn_body.focus()" onunload="jsWindowCloseAll();">
	<?php
	$mode = we_base_request::_(we_base_request::STRING, 'mode');
	if($mode === 're'){
		$compose = new we_messaging_format('re', $messaging->selected_message);
		$heading = g_l('modules_messaging', '[reply_message]');
	} else {
		if(substr($mode, 0, 2) === 'u_'){
			$_u = str_replace(substr($mode, 0, 2), '', $mode);
		}
		$compose = new we_messaging_format('new');
		$heading = g_l('modules_messaging', '[new_message]');
	}

	$compose->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	?>
  <form action="<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_send_nm.php" name="compose_form" method="post">
		<?php
		echo we_html_tools::hidden('we_transaction', $transaction) .
		we_html_tools::hidden('rcpts_string', '') .
		we_html_tools::hidden('mode', $mode);

		$tbl = '<table style="text-align:center" cellpadding="6" width="100%">
      <tr><td class="defaultgray">' . g_l('modules_messaging', '[from]') . ':</td><td class="defaultfont">' . $compose->get_from() . '</td></tr>
      <tr><td class="defaultgray"><a href="javascript:selectRecipient()">' . g_l('modules_messaging', '[recipients]') . ':</a></td><td>' . we_html_tools::htmlTextInput('mn_recipients', 40, (!isset($_u) ? $compose->get_recipient_line() : $_u)) . '</td></tr>
      <tr><td class="defaultgray">' . g_l('modules_messaging', '[subject]') . ':</td><td>' . we_html_tools::htmlTextInput('mn_subject', 40, $compose->get_subject()) . '</td></tr>
      <tr><td colspan="2"><textarea cols="68" rows="15" name="mn_body" style="width:605px">' . $compose->get_msg_text() . '</textarea></td></tr>
    </table>';

		$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_send()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:window.close()")
		);

		echo we_html_tools::htmlDialogLayout($tbl, "<div style='padding:6px'>" . $heading . "</div>", $_buttons, "100%", 24, "", "hidden");
		?>
	</form>
</body>
</html>