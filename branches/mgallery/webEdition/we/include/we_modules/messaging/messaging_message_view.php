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
$messaging->get_mv_data(we_base_request::_(we_base_request::INT, 'id'));
$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);

if(!($messaging->selected_message)){
	exit;
}

$format = new we_messaging_format('view', $messaging->selected_message);
$format->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);

we_html_tools::protect();

echo we_html_tools::getHtmlTop() .
 STYLESHEET;
?>
<script type="text/javascript"><!--
	function todo_markdone() {
		top.content.cmd.location = '<?php echo WE_MESSAGING_MODULE_DIR; ?>edit_messaging_frameset.php?pnt=cmd&mcmd=todo_markdone&we_transaction=<?php echo $transaction; ?>';
			}
//-->
</script>
</head>
<body class="weDialogBody"><?php
	if(isset($messaging->selected_message['hdrs']['ClassName']) && $messaging->selected_message['hdrs']['ClassName'] === 'we_todo'){
		$parts = array(
			array("headline" => g_l('modules_messaging', '[subject]'),
				"html" => "<b>" . oldHtmlspecialchars($format->get_subject()) . "</b>",
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[deadline]'),
				"html" => $format->get_deadline(),
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[status]'),
				"html" => '<table class="default"><tr><td class="defaultfont">' . $messaging->selected_message['hdrs']['status'] . '%</td>'.
				($messaging->selected_message['hdrs']['status'] < 100 ? '<td>' . we_html_button::create_button("percent100", "javascript:todo_markdone()") . '</td>' : '') . '</tr></table>',
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[created_by]'),
				"html" => $format->get_from(),
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[assigned_by]'),
				"html" => $format->get_assigner(),
				"noline" => 1,
				"space" => 140
			),
			array("headline" => g_l('modules_messaging', '[creation_date]'),
				"html" => $format->get_date(),
				"space" => 140
			),
			array("headline" => "",
				"html" => $format->get_msg_text(),
				"space" => 0
			)
		);

		if(isset($messaging->selected_message['hdrs']['ClassName']) && $messaging->selected_message['hdrs']['ClassName'] === 'we_todo' && ($h = $format->get_todo_history())){
			$parts[] = array("headline" => "",
				"html" => $format->get_todo_history(),
				"noline" => 1,
				"space" => 0
			);
		}
	} else { //	Message
		$parts = array(
			array("headline" => g_l('modules_messaging', '[subject]'),
				"html" => "<b>" . oldHtmlspecialchars($format->get_subject()) . "</b>",
				"noline" => 1,
				"space" => 80
			),
			array("headline" => g_l('modules_messaging', '[from]'),
				"html" => $format->get_from(),
				"noline" => 1,
				"space" => 80
			),
			array("headline" => g_l('modules_messaging', '[date]'),
				"html" => $format->get_date(),
				"noline" => (empty($messaging->selected_message['hdrs']['To']) ? null : 1),
				"space" => 80
			)
		);

		if(!empty($messaging->selected_message['hdrs']['To'])){
			$parts[] = array("headline" => g_l('modules_messaging', '[recipients]'),
				"html" => oldHtmlspecialchars($messaging->selected_message['hdrs']['To']),
				"space" => 80
			);
		}

		$parts[] = array("headline" => "",
			"html" => $format->get_msg_text(),
			"noline" => 1,
			"space" => 0
		);
	}

	echo we_html_multiIconBox::getJS() .
	we_html_multiIconBox::getHTML("weMessageView", "100%", $parts, 30, "", -1, "", "", false, g_l('modules_messaging', (isset($messaging->selected_message['hdrs']['ClassName']) && $messaging->selected_message['hdrs']['ClassName'] === 'we_todo' ? '[type_todo]' : '[type_message]')));
	?>
</body>
</html>
