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
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 1);

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
$we_doc = we_document::initDoc($we_dt);

if(we_workflow_utility::approve($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], '', true)){
	if(($time = $we_doc->i_publInScheduleTable())){
		$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][autoschedule]'), date(g_l('date', '[format][default]'), $time));

		$we_responseTextType = we_base_util::WE_MESSAGE_NOTICE;
	} else if($we_doc->we_publish()){
		$we_JavaScript = [
			['setEditorDocumentId(', $we_doc->ID],
			$we_doc->getUpdateTreeScript(true, null, true)
		];
		$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_ok]'), $we_doc->Path);
		$we_responseTextType = we_base_util::WE_MESSAGE_NOTICE;
		if(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO)){
			$GLOBALS['we_responseJS'][] = ['switch_edit_page', $this->EditPageNr, $GLOBALS["we_transaction"]]; // wird in Templ eingef�gt
		}
		$we_JavaScript[] = ['reload_editfooter',$we_transaction];
		$we_JavaScript[] = ["setEditorDocumentId", $we_doc->ID];
		$we_JavaScript[] = $we_doc->getUpdateTreeScript(true, null, true);
	} else {
		$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_notok]'), $we_doc->Path);
		$we_responseTextType = we_base_util::WE_MESSAGE_ERROR;
	}
} else {
	$we_responseText = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_notok]');
	$we_responseTextType = we_base_util::WE_MESSAGE_ERROR;
}

we_editor_functions::saveInc($we_transaction, $GLOBALS['we_doc'], $we_responseText, $we_responseTextType, $we_JavaScript, !empty($wasSaved), !empty($saveTemplate), (!empty($GLOBALS['we_responseJS']) ? $GLOBALS['we_responseJS'] : [
		]), isset($isClose) && $isClose, (isset($showAlert) && $showAlert), !empty($GLOBALS["publish_doc"]));
