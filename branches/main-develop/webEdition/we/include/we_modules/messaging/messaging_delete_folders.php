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

$jsCmd = new we_base_jsCmd();
echo we_html_tools::getHtmlTop().
	we_html_element::jsScript(WE_JS_MODULES_DIR . 'messaging/messaging.js');

if(we_base_request::_(we_base_request::STRING, 'mcmd') === 'delete_folders'){
	$folders = we_base_request::_(we_base_request::INTLISTA, 'folders', []);

	if($folders){
		$res = $messaging->delete_folders($folders);
		$v = array_shift($res);
		if($v > 0){
			$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);
			?>
			<script><!--
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=messaging&pnt=cmd&we_transaction=<?= $transaction ?>&mcmd=delete_folders&folders=<?= implode(',', $v) ?>';
				top.content.we_cmd('messaging_start_view', '', '<?= we_base_request::_(we_base_request::TABLE, 'table', ''); ?>');
				//-->
			</script>
			</head>
			<body></body>
			</html>
			<?php
			exit;
		}
		$jsCmd->addMsg(g_l('modules_messaging', '[err_delete_folders]'), we_message_reporting::WE_MESSAGE_ERROR);
	}
}

$form = '<form name="we_form" method="post">' .
	we_html_element::htmlHiddens(['we_transaction' => $transaction,
		'folders' => '',
		'mcmd' => 'delete_folders'
	]) .
	'</form>';

$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_delete()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:top.content.we_cmd('messaging_start_view')")
);

echo $jsCmd->getCmds();
?>
</head>
<body style="border-top:1px solid black;margin:10px;">
	<?=
	we_html_tools::htmlMessageBox(400, 120, "<span class=\"defaultfont\">" . g_l('modules_messaging', '[deltext]') . "</span>", g_l('modules_messaging', '[rm_folders]'), $buttons) .
	$form
	?>
</body>

</html>