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
we_html_tools::protect();
if($cmd == "ok"){
	$wf_text = we_base_request::_(we_base_request::STRING, "wf_text");
	$wf_select = we_base_request::_(we_base_request::RAW, "wf_select", "");
	$force = (!we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]));

	$ok = we_workflow_utility::decline($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"], $wf_text, $force);

	if($ok){

		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][decline_workflow_ok]');
		$msgType = we_message_reporting::WE_MESSAGE_NOTICE;

		//	in SEEM-Mode back to Preview page
		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){

			$script = "opener.top.we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
		} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){

			$script = 'opener.top.weEditorFrameController.getActiveDocumentReference().frames[3].location.reload();';
		}

		if(($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == WE_EDITPAGE_INFO)){
			$script .= 'opener.top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // will be inserted into the template
		}
	} else {
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][decline_workflow_notok]');
		$msgType = we_message_reporting::WE_MESSAGE_ERROR;
		//	in SEEM-Mode back to Preview page
		if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){

			$script = "opener.top.we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
		} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){

			$script = '';
		}
	}
	print we_html_element::jsElement($script . we_message_reporting::getShowMessageCall($msg, $msgType) . 'self.close();');
}
print STYLESHEET;
?>
</head>

<body class="weDialogBody">
	<center>
		<?php if($cmd != 'ok'){ ?>
			<form action="<?php print WEBEDITION_DIR; ?>we_cmd.php" method="post">
				<?php
				$okbut = we_html_button::create_button("ok", "javascript:document.forms[0].submit()");
				$cancelbut = we_html_button::create_button("cancel", "javascript:top.close()");
				$content = '<table border="0" cellpadding="0" cellspacing="0">';
				$wf_textarea = '<textarea name="wf_text" rows="7" cols="50" style="width:360;height:190"></textarea>';
				$content .= '
						<tr>
							<td class="defaultfont">
								' . g_l('modules_workflow', '[message]') . '</td>
						</tr>
						<tr>
							<td>
								' . $wf_textarea . '</td>
						</tr>
					</table>';

				$_button = we_html_button::position_yes_no_cancel($okbut, "", $cancelbut);
				$frame = we_html_tools::htmlDialogLayout($content, g_l('modules_workflow', '[decline_workflow]'), $_button);
				print $frame;
				print '
						<input type="hidden" name="cmd" value="ok" />
						<input type="hidden" name="we_cmd[0]" value="' . $_REQUEST['we_cmd'][0] . '" />
						<input type="hidden" name="we_cmd[1]" value="' . $we_transaction . '" />';
				?>
			</form>
		<?php } ?>
	</center>

</body>

</html>