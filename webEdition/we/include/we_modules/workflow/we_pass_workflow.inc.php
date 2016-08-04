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

if($cmd === "ok"){
	$wf_text = we_base_request::_(we_base_request::STRING, 'wf_text');
	$wf_select = we_base_request::_(we_base_request::RAW, 'wf_select', "");
	$force = (!we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID']));

	if(we_workflow_utility::approve($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], $wf_text, $force)){
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_ok]');
		$msgType = we_message_reporting::WE_MESSAGE_NOTICE;

		//	in SEEM-Mode back to Preview page
		switch($_SESSION['weS']['we_mode']){
			case we_base_constants::MODE_SEE:
				$script = "if(opener){opener.top.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');}else{opener.top.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');}";
				break;
			default:
			case we_base_constants::MODE_NORMAL:
				$script = 'if(opener){
WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.location.reload();
}else{
WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.location.reload();
}';
		}

		if(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO)){
			$script .= 'opener.top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // wird in Templ eingef�gt
		}
	} else {
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_notok]');
		$msgType = we_message_reporting::WE_MESSAGE_ERROR;
		//	in SEEM-Mode back to Preview page
		switch($_SESSION['weS']['we_mode']){
			case we_base_constants::MODE_SEE:
				$script = "opener.top.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
				break;
			default:
			case we_base_constants::MODE_NORMAL:
				$script = '';
		}
	}
	echo we_html_element::jsElement($script . we_message_reporting::getShowMessageCall($msg, $msgType) . ';top.close();');
}
?>
</head>
<body class="weDialogBody"><div style="text-align:center">
	<?php
	if($cmd !== "ok"){
		?>
		<form action="<?= WEBEDITION_DIR; ?>we_cmd.php" method="post">
			<?php
			$okbut = we_html_button::create_button(we_html_button::OK, "javascript:document.forms[0].submit()");
			$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");


			$content = '<table class="default">';
			$wf_textarea = '<textarea name="wf_text" rows="7" cols="50" style="left:10px;right:10px;height:190px"></textarea>';
			$content .= '<tr>
<td class="defaultfont">' . g_l('modules_workflow', '[message]') . '</td>
</tr>
<tr>
<td>' . $wf_textarea . '</td>
</tr>
</table>';

			echo we_html_tools::htmlDialogLayout($content, g_l('modules_workflow', '[pass_workflow]'), we_html_button::position_yes_no_cancel($okbut, "", $cancelbut)) .
			we_html_element::htmlHiddens(array(
				"cmd" => "ok",
				"we_cmd[0]" => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
				"we_cmd[1]" => $we_transaction
			));
			?>
		</form>
	<?php } ?>
</div>
</body>
</html>