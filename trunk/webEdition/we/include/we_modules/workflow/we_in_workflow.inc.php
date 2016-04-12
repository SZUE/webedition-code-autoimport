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
$cmd2 = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 2);

if($cmd === 'ok'){
	$wf_text = we_base_request::_(we_base_request::STRING, 'wf_text');
	$wf_select = we_base_request::_(we_base_request::INT, 'wf_select');
	if(we_workflow_utility::insertDocInWorkflow($we_doc->ID, $we_doc->Table, $wf_select, $_SESSION["user"]["ID"], $wf_text)){
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][in_workflow_ok]');
		$msgType = we_message_reporting::WE_MESSAGE_NOTICE;
		switch($_SESSION['weS']['we_mode']){
			case we_base_constants::MODE_SEE:

				$script = "if(opener){opener.top.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');}else{top.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');}";
				break;
			default:
			case we_base_constants::MODE_NORMAL:
				$script = 'if(opener){
WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.location.reload();
}else{
WE().layout.weEditorFrameController.getActiveDocumentReference().frames.editFooter.location.reload();
}';
		}

		if($cmd2){ // make same new
			$we_doc->makeSameNew();
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
			$script .= 'opener.top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // wird in Templ eingef�gt
		} elseif(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO)){
			$script .= 'opener.top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // wird in Templ eingef�gt
		}
	} else {
		$msg = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][in_workflow_notok]');
		$msgType = we_message_reporting::WE_MESSAGE_ERROR;
		switch($_SESSION['weS']['we_mode']){
			case we_base_constants::MODE_SEE:
				$script = "opener.top.we_cmd('switch_edit_page'," . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');";
				break;
			default:
			case we_base_constants::MODE_NORMAL:
				$script = '';
		}
	}
	echo we_html_element::jsElement($script . we_message_reporting::getShowMessageCall($msg, $msgType) . 'self.close();');
}
echo STYLESHEET;
?>
</head>

<body class="weDialogBody">
	<div style="text-align:center">
		<?php
		if($cmd != 'ok'){
			$all = array();
			$wfDoc = ($we_doc->Table == FILE_TABLE ?
					we_workflow_utility::getWorkflowDocumentForDoc($GLOBALS['DB_WE'], $we_doc->DocType, $we_doc->Category, $we_doc->ParentID, $all) :
					we_workflow_utility::getWorkflowDocumentForObject($GLOBALS['DB_WE'], $we_doc->TableID, $we_doc->Category, $we_doc->ParentID, $all));
			$wfID = $wfDoc->workflowID;
			if($wfID){
				?>
				<form action="<?php echo WEBEDITION_DIR; ?>we_cmd.php" method="post"><?php
					$wf_select = '<select name="wf_select" size="1">';
					$wfs = we_workflow_utility::getAllWorkflows(we_workflow_workflow::STATE_ACTIVE, $we_doc->Table, $all);
					foreach($wfs as $wID => $wfname){
						$wf_select .= '<option value="' . $wID . '"' . (($wID == $wfID) ? ' selected' : '') . '>' . oldHtmlspecialchars($wfname) . "</option>\n";
					}
					$wf_select .= '</select>';

					$okbut = we_html_button::create_button(we_html_button::OK, "javascript:document.forms[0].submit()");
					$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");

					$content = '<table class="default">';

					//if(permissionhandler::hasPerm("PUBLISH")){
					$wf_textarea = '<textarea name="wf_text" rows="5" cols="50" style="left:10px;right:10px;height:150px;"></textarea>';
					$content .= '
<tr><td class="defaultfont">' . g_l('modules_workflow', '[workflow]') . '</td></tr>
<tr><td style="padding-bottom:5px;">' . $wf_select . '</td></tr>
';
					/* } else {
					  $wf_textarea = '<textarea name="wf_text" rows="7" cols="50" style="left:10px;right:10px;height:190px"></textarea>';
					  $content .= we_html_element::htmlHidden("wf_select", $wfID);
					  } */
					$content .= '
<tr><td class="defaultfont">' . g_l('modules_workflow', '[message]') . '</td></tr>
<tr><td>' . $wf_textarea . '</td></tr>
</table>';

					echo we_html_tools::htmlDialogLayout($content, g_l('modules_workflow', '[in_workflow]'), we_html_button::position_yes_no_cancel($okbut, '', $cancelbut)) .
					we_html_element::htmlHiddens(array(
						"cmd" => "ok",
						"we_cmd[0]" => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
						"we_cmd[1]" => $we_transaction,
						"we_cmd[2]" => $cmd2
					));
					?>
				</form>
				<?php
			} else {
				echo we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('modules_workflow', ($we_doc->Table == FILE_TABLE ? '[no_wf_defined]' : '[no_wf_defined_object]')), we_message_reporting::WE_MESSAGE_ERROR) .
					'top.close();');
			}
		}
		?>
	</div>
</body>

</html>