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

$mode = we_base_request::_(we_base_request::STRING, "mode", '');
if(!we_base_request::_(we_base_request::TRANSACTION, 'we_transaction')){
	exit();
}

echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[wintitle]')) .
 STYLESHEET;
?>

<script><!--
	rcpt_sel = [];

	function update_rcpts() {
		var rcpt_str = rcpt_sel[0][2];
		document.compose_form.mn_recipients.value = rcpt_str;
	}

	function selectRecipient() {
		var rs = encodeURI(document.compose_form.mn_recipients.value);
		new (WE().util.jsWindow)(window, WE().consts.dirs.WE_MESSAGING_MODULE_DIR + "messaging_usel.php?we_transaction=<?php echo $transaction; ?>&maxsel=1&rs=" + rs, "messaging_usel", -1, -1, 530, 420, true, false, true, false);
	}

	function do_send() {
<?php if($mode != 'reject'){ ?>
			rcpt_s = encodeURI(document.compose_form.mn_recipients.value);
			document.compose_form.rcpts_string.value = rcpt_s;
<?php } ?>
		document.compose_form.submit();
	}

	function doUnload() {
		WE().util.jsWindow.prototype.closeAll(window);
	}
//-->
</script>
</head>

<body class="weDialogBody" <?php echo ($mode === 'reject' ? '' : 'onload="document.compose_form.mn_subject.focus()"') ?> onunload="doUnload();">
	<?php
	switch($mode){
		case 'forward':
			$compose = new we_messaging_format('forward', $messaging->selected_message);
			$heading = g_l('modules_messaging', '[forward_todo]');
			break;
		case 'reject':
			$compose = new we_messaging_format('reject', $messaging->selected_message);
			$heading = g_l('modules_messaging', '[reject_todo]');
			break;
		default:
			$compose = new we_messaging_format('new');
			$heading = g_l('modules_messaging', '[new_todo]');
	}
	$compose->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	?>
	<form action="<?php echo WE_MESSAGING_MODULE_DIR; ?>todo_send_ntodo.php" name="compose_form" method="post">
		<?php
		echo we_html_tools::hidden('we_transaction', $transaction);
		echo we_html_tools::hidden('rcpts_string', '');
		echo we_html_tools::hidden('mode', $mode);

		if($mode === 'reject'){
			$tbl = '
<table cellpadding="6">
		<tr>
		<td class="defaultfont lowContrast">
			' . g_l('modules_messaging', '[from]') . ':</td>
		<td class="defaultfont">
			' . $compose->get_from() . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">
			' . g_l('modules_messaging', '[reject_to]') . ':</a></td>
		<td class="defaultfont">
			' . $compose->get_recipient_line() . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">
			' . g_l('modules_messaging', '[subject]') . ':</td>
		<td class="defaultfont">
			' . oldHtmlspecialchars($compose->get_subject()) . '</td>
	</tr>
</table>
<table cellpadding="6">';
		} else {
			$tbl = '
<table cellpadding="6">
	<tr>
		<td class="defaultfont lowContrast">
			' . g_l('modules_messaging', '[assigner]') . ':</td>
		<td class="defaultfont">
			' . $compose->get_from() . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">
			<a href="javascript:selectRecipient()">' . g_l('modules_messaging', '[recipient]') . ':</a></td>
		<td>
			' . we_html_tools::htmlTextInput('mn_recipients', 40, ($mode === 'forward' ? '' : $_SESSION["user"]["Username"])) . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">
			' . g_l('modules_messaging', '[subject]') . ':</td>
		<td>
			' . we_html_tools::htmlTextInput('mn_subject', 40, $compose->get_subject()) . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">
			' . g_l('modules_messaging', '[deadline]') . ':</td>
		<td>
			' . we_html_tools::getDateInput('td_deadline%s', $compose->get_deadline()) . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast">' . g_l('modules_messaging', '[priority]') . ':</td>
		<td>' . we_html_tools::html_select('mn_priority', 1, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10)) . '</td>
	</tr>
</table>
<table cellpadding="6">';
		}
		if($mode != 'new'){
			$tbl .= '
<tr>
	<td class="defaultfont">' . $compose->get_msg_text() . '</td>
</tr>
<tr>
	<td class="defaultfont">' . $compose->get_todo_history() . '</td>
</tr>';
		}
		$tbl .= '
	<tr>
		<td>
			<textarea cols="68" rows="10" name="mn_body" style="width:624px"></textarea></td>
	</tr>
</table>';
		$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_send()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:top.window.close()")
		);
		echo we_html_tools::htmlDialogLayout($tbl, "<div style='padding:6px'>" . $heading . "</div>", $buttons, 100, 24);
		?>
	</form>
</body>
</html>