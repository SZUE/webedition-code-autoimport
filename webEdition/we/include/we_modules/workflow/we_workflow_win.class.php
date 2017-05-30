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
abstract class we_workflow_win{

	public static function showWin(){
		$cmd = we_base_request::_(we_base_request::RAW, 'cmd', '');
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0), 1);

		$wf_text = we_base_request::_(we_base_request::STRING, 'wf_text', '');
###### init document #########
		$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
		$we_doc = we_document::initDoc($we_dt);
		$jsCmd = new we_base_jsCmd();



		switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
			case 'workflow_isIn':
				$body = self::inWorkflowWindow($cmd, $we_transaction, $we_doc, $jsCmd, $wf_text);
				break;
			case 'workflow_pass':
				$body = self::passWorkflowWin($cmd, $we_transaction, $we_doc, $jsCmd, $wf_text);
				break;
			case 'workflow_decline':
				$body = self::declineWindow($cmd, $we_transaction, $we_doc, $jsCmd, $wf_text);
				break;
		}
		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(WE_JS_MODULES_DIR . 'workflow/we_workflow_win.js') . $jsCmd->getCmds(), we_html_element::htmlBody([
				'class' => "weDialogBody"
				], $body));
	}

	private static function declineWindow($cmd, $we_transaction, we_contents_root $we_doc, we_base_jsCmd $jsCmd, $wf_text){
		if($cmd === "ok"){
			$force = (!we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID']));

			if(we_workflow_utility::decline($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], $wf_text, $force)){
				$jsCmd->addMsg(g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][decline_workflow_ok]'), we_base_util::WE_MESSAGE_NOTICE);
				//	in SEEM-Mode back to Preview page
				if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
					$jsCmd->addCmd('switch_edit_page', we_base_constants::WE_EDITPAGE_PREVIEW, $we_transaction);
				} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){
					$jsCmd->addCmd('reloadFooter');
				}

				if(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO)){
					$jsCmd->addCmd('switch_edit_page', $we_doc->EditPageNr, $we_transaction); // will be inserted into the template
				}
			} else {
				$jsCmd->addMsg(g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][decline_workflow_notok]'), we_base_util::WE_MESSAGE_ERROR);
				//	in SEEM-Mode back to Preview page
				if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
					$jsCmd->addCmd('switch_edit_page', we_base_constants::WE_EDITPAGE_PREVIEW, $we_transaction);
				} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){

				}
			}
			$jsCmd->addCmd('close');
			return '';
		}
		$okbut = we_html_button::create_button(we_html_button::OK, "javascript:document.forms[0].submit()");
		$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");
		$button = we_html_button::position_yes_no_cancel($okbut, "", $cancelbut);

		return '<div style="text-align:center">
					<form action="' . WEBEDITION_DIR . '>we_cmd.php" method="post">' .
			we_html_tools::htmlDialogLayout('<table class="default">
	<tr><td class="defaultfont">' . g_l('modules_workflow', '[message]') . '</td></tr>
	<tr><td><textarea name="wf_text" rows="7" cols="50" style="width:360;height:190"></textarea></td></tr>
</table>', g_l('modules_workflow', '[decline_workflow]'), $button) . we_html_element::htmlHiddens(["cmd" => "ok",
				"we_cmd[0]" => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
				"we_cmd[1]" => $we_transaction]) .
			'</form>
			</div>';
	}

	private static function inWorkflowWindow($cmd, $we_transaction, we_contents_root $we_doc, we_base_jsCmd $jsCmd, $wf_text){
		$cmd2 = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 2);

		if($cmd === 'ok'){
			$wf_select = we_base_request::_(we_base_request::INT, 'wf_select');
			if(we_workflow_utility::insertDocInWorkflow($we_doc->ID, $we_doc->Table, $wf_select, $_SESSION['user']["ID"], $wf_text)){
				$jsCmd->addMsg(g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][in_workflow_ok]'), we_base_util::WE_MESSAGE_NOTICE);
				switch($_SESSION['weS']['we_mode']){
					case we_base_constants::MODE_SEE:
						$jsCmd->addCmd('switch_edit_page', we_base_constants::WE_EDITPAGE_PREVIEW, $we_transaction);
						break;
					default:
					case we_base_constants::MODE_NORMAL:
						$jsCmd->addCmd('reloadFooter');
				}

				if($cmd2){ // make same new
					$we_doc->makeSameNew();
					$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
					$jsCmd->addCmd('switch_edit_page', $we_doc->EditPageNr, $we_transaction); // wird in Templ eingef�gt
				} elseif(in_array($we_doc->EditPageNr, [we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO])){
					$jsCmd->addCmd('switch_edit_page', $we_doc->EditPageNr, $we_transaction); // wird in Templ eingef�gt
				}
			} else {
				$jsCmd->addMsg(g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][in_workflow_notok]'), we_base_util::WE_MESSAGE_ERROR);
				switch($_SESSION['weS']['we_mode']){
					case we_base_constants::MODE_SEE:
						$jsCmd->addCmd('switch_edit_page', we_base_constants::WE_EDITPAGE_PREVIEW, $we_transaction);
						break;
					default:
					case we_base_constants::MODE_NORMAL:
				}
			}
			$jsCmd->addCmd('close');
			return '';
		}

		$all = [];
		$wfDoc = ($we_doc->Table == FILE_TABLE ?
			we_workflow_utility::getWorkflowDocumentForDoc($GLOBALS['DB_WE'], $we_doc->DocType, $we_doc->Category, $we_doc->ParentID, $all) :
			we_workflow_utility::getWorkflowDocumentForObject($GLOBALS['DB_WE'], $we_doc->TableID, $we_doc->Category, $we_doc->ParentID, $all));
		$wfID = $wfDoc->workflowID;
		if(!$wfID){
			$jsCmd->addMsg(g_l('modules_workflow', ($we_doc->Table == FILE_TABLE ? '[no_wf_defined]' : '[no_wf_defined_object]')), we_base_util::WE_MESSAGE_ERROR);
			$jsCmd->addCmd('close');
			return '';
		}

		$wf_select = '<select name="wf_select">';
		$wfs = we_workflow_utility::getAllWorkflows(we_workflow_workflow::STATE_ACTIVE, $we_doc->Table, $all);
		foreach($wfs as $wID => $wfname){
			$wf_select .= '<option value="' . $wID . '"' . (($wID == $wfID) ? ' selected' : '') . '>' . oldHtmlspecialchars($wfname) . '</option>';
		}
		$wf_select .= '</select>';

		$okbut = we_html_button::create_button(we_html_button::OK, "javascript:document.forms[0].submit()");
		$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");

		return '
<div style="text-align:center">
		<form action="' . WEBEDITION_DIR . 'we_cmd.php" method="post">' .
			we_html_tools::htmlDialogLayout('<table class="default">
<tr><td class="defaultfont">' . g_l('modules_workflow', '[workflow]') . '</td></tr>
<tr><td style="padding-bottom:5px;">' . $wf_select . '</td></tr>
<tr><td class="defaultfont">' . g_l('modules_workflow', '[message]') . '</td></tr>
<tr><td><textarea name="wf_text" rows="5" cols="50" style="left:10px;right:10px;height:150px;"></textarea></td></tr>
</table>', g_l('modules_workflow', '[in_workflow]'), we_html_button::position_yes_no_cancel($okbut, '', $cancelbut)) .
			we_html_element::htmlHiddens(["cmd" => "ok",
				"we_cmd[0]" => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
				"we_cmd[1]" => $we_transaction,
				"we_cmd[2]" => $cmd2
			]) . '
		</form>
		</div>';
	}

	private static function passWorkflowWin($cmd, $we_transaction, we_contents_root $we_doc, we_base_jsCmd $jsCmd, $wf_text){
		if($cmd === "ok"){
			$force = (!we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID']));

			if(we_workflow_utility::approve($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], $wf_text, $force)){
				$jsCmd->addMsg(g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_ok]'), we_base_util::WE_MESSAGE_NOTICE);

				//	in SEEM-Mode back to Preview page
				switch($_SESSION['weS']['we_mode']){
					case we_base_constants::MODE_SEE:
						$jsCmd->addCmd('switch_edit_page', we_base_constants::WE_EDITPAGE_PREVIEW, $we_transaction);
						break;
					default:
					case we_base_constants::MODE_NORMAL:
						$jsCmd->addCmd('reloadFooter');
				}

				if(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO)){
					$jsCmd->addCmd('switch_edit_page', $we_doc->EditPageNr, $we_transaction); // wird in Templ eingef�gt
				}
			} else {
				$jsCmd->addMsg(g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_notok]'), we_base_util::WE_MESSAGE_ERROR);
				//	in SEEM-Mode back to Preview page
				switch($_SESSION['weS']['we_mode']){
					case we_base_constants::MODE_SEE:
						$jsCmd->addCmd('switch_edit_page', we_base_constants::WE_EDITPAGE_PREVIEW, $we_transaction);
						break;
					default:
					case we_base_constants::MODE_NORMAL:
				}
			}
			$jsCmd->addCmd('close');
			return '';
		}
		$okbut = we_html_button::create_button(we_html_button::OK, "javascript:document.forms[0].submit()");
		$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close()");

		return '<div style="text-align:center">
				<form action="' . WEBEDITION_DIR . 'we_cmd.php" method="post">' .
			we_html_tools::htmlDialogLayout('<table class="default">
<tr><td class="defaultfont">' . g_l('modules_workflow', '[message]') . '</td></tr>
<tr><td><textarea name="wf_text" rows="7" cols="50" style="left:10px;right:10px;height:190px"></textarea></td></tr>
</table>', g_l('modules_workflow', '[pass_workflow]'), we_html_button::position_yes_no_cancel($okbut, "", $cancelbut)) .
			we_html_element::htmlHiddens(["cmd" => "ok",
				"we_cmd[0]" => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
				"we_cmd[1]" => $we_transaction
			]) . '
				</form>
			</div>';
	}

}
