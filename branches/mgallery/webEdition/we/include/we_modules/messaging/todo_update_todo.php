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

echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[wintitle]') . ' - Update Status') .
 STYLESHEET;
?>
<script><!--
function do_confirm() {
		document.update_todo_form.submit();
	}

	function doUnload() {
		WE().util.jsWindow.prototype.closeAll(window);
	}
//-->
</script>
</head>
<body class="weDialogBody" onunload="doUnload();">
	<?php
	$heading = g_l('modules_messaging', '[todo_status_update]');
	$compose = new we_messaging_format('update', $messaging->selected_message);
	$compose->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	?>
	<form action="<?php echo WE_MESSAGING_MODULE_DIR; ?>todo_update.php" name="update_todo_form" method="post">
		<?php
		echo we_html_element::htmlHiddens(array(
			'we_transaction' => $transaction,
			'rcpts_string' => '',
			'mode' => we_base_request::_(we_base_request::STRING, 'mode')
		));

		$prio = $compose->get_priority();
		$parts = array(
			array("headline" => g_l('modules_messaging', '[assigner]'),
				"html" => $compose->get_from(),
				"space" => 120,
				"noline" => 1
			),
			array("headline" => g_l('modules_messaging', '[subject]'),
				"html" => $compose->get_subject(),
				"space" => 120,
				"noline" => 1
			),
			array("headline" => g_l('modules_messaging', '[deadline]'),
				"html" => we_html_tools::getDateInput2('td_deadline%s', $compose->get_deadline()),
				"space" => 120,
				"noline" => 1
			),
			array("headline" => g_l('modules_messaging', '[status]'),
				"html" => we_html_tools::htmlTextInput('todo_status', 4, $messaging->selected_message['hdrs']['status']) . ' %',
				"space" => 120,
				"noline" => 1
			),
			array("headline" => g_l('modules_messaging', '[priority]'),
				"html" => we_html_tools::html_select('todo_priority', 1, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), $compose->get_priority()),
				"space" => 120,
			),
			array("headline" => "",
				"html" => $compose->get_msg_text(),
				"space" => 0,
				"noline" => 1
			),
			array("headline" => "",
				"html" => $compose->get_todo_history(),
				"space" => 0,
			),
			array("headline" => g_l('modules_messaging', '[comment]'),
				"html" => '<textarea cols="40" rows="8" name="todo_comment"></textarea>',
				"space" => 120,
			)
		);

		$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_confirm();"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:top.window.close()")
		);
		echo we_html_multiIconBox::getHTML("todoStatusUpdate", $parts, 30, $buttons, -1, "", "", false, $heading);
		?>
	</form>
</body>
</html>