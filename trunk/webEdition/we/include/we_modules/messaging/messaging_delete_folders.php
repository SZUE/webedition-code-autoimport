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

echo we_html_tools::getHtmlTop() .
 STYLESHEET;

$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}


$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);
?>
<script><!--
	function we_submitForm(target, url) {
		var f = self.document.we_form;
		if (!f.checkValidity()) {
			top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
			return false;
		}

		var sel = "";
		for (var i = 1; i <= top.treeData.len; i++) {
			if (top.treeData[i].checked)
				sel += (top.treeData[i].name + ",");
		}
		if (!sel) {
			top.we_showMessage(WE().consts.g_l.main.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
			return;
		}
		sel = sel.substring(0, sel.length - 1);
		f.sel.value = sel;
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
		return true;
	}

	function do_delete() {
		document.we_form.folders.value = top.content.entries_selected.join(",");
		document.we_form.submit();
	}

<?php
if(we_base_request::_(we_base_request::STRING, 'mcmd') === 'delete_folders'){
	$folders = we_base_request::_(we_base_request::INTLISTA, 'folders', array());

	if($folders){
		$res = $messaging->delete_folders($folders);
		$v = array_shift($res);
		if($v > 0){
			$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);
			?>
				top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=messaging&pnt=cmd&we_transaction=<?php echo $transaction ?>&mcmd=delete_folders&folders=<?php echo implode(',', $v) ?>';
				top.content.we_cmd('messaging_start_view', '', '<?php echo we_base_request::_(we_base_request::TABLE, 'table', ''); ?>');
				//-->
			</script>
			</head>
			<body></body>
			</html>
			<?php
			exit;
		}
		echo we_message_reporting::getShowMessageCall(g_l('modules_messaging', '[err_delete_folders]'), we_message_reporting::WE_MESSAGE_ERROR);
	}
}
?>
//-->
</script>
<?php
$form = '<form name="we_form" method="post">' .
	we_html_element::htmlHiddens(array(
		'we_transaction' => $transaction,
		'folders' => '',
		'mcmd' => 'delete_folders'
	)) .
	'</form>';

$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:do_delete()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:top.content.we_cmd('messaging_start_view')")
);
?>
</head>
<body style="background-color: white;border-top:1px solid black;margin:10px;">
	<?php
	echo we_html_tools::htmlMessageBox(400, 120, "<span class=\"defaultfont\">" . g_l('modules_messaging', '[deltext]') . "</span>", g_l('modules_messaging', '[rm_folders]'), $buttons) .
	$form
	?>
</body>

</html>