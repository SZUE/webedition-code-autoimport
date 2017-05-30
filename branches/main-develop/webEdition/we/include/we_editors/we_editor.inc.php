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
$GLOBALS['we_EDITOR'] = true;

we_html_tools::protect();
// prevent persmissions overriding
$perms = $_SESSION['perms'];
// init document
$GLOBALS['we_transaction'] = (empty($we_transaction) ? //we_session assumes to have transaction in parameter 'we_transaction'
	we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 1)) : $we_transaction);

$we_doc = we_document::initDoc(isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : '');

$GLOBALS['we_responseJS'] = [];
$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$insertReloadFooter = we_editor_functions::processEditorCmd($we_doc, $cmd0);

//	if document is locked - only Preview mode is possible. otherwise show warning.
we_editor_functions::documentLocking($we_doc);


/*
 * if the document is a webEdition document, we save it with a temp-name (path of document+extension) and redirect
 * to the tmp-location. This is done for the content- and preview-editpage.
 * With html-documents this is only done for preview-editpage.
 * We need to do this, because, when the pages has for example jsp. content, it will be parsed right!
 * This is only done when the IsDynamic - PersistantSlot is false.
 */

if(
	!$we_doc->IsDynamic &&
	!empty($_POST) &&
	we_base_request::_(we_base_request::BOOL, 'we_complete_request') &&
	(
	$cmd0 != 'save_document' &&
	$cmd0 != 'publish' &&
	$cmd0 != 'unpublish' &&
	($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT &&
	(
	$we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW ||
	$we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT
	)
	) ||
	(
	$we_doc->ContentType == we_base_ContentTypes::HTML &&
	$we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW
	)
	)
){
//	t_e($cmd0, 'include');
	we_editor_functions::includeEditor($we_doc, $we_transaction, $insertReloadFooter);
	// prevent persmissions overriding
	$_SESSION['perms'] = $perms;
	return;
}

$we_JavaScript = [];

$wasNew = $showAlert = $isClose = $wasSaved = $saveTemplate = false;

switch($cmd0){
	case 'save_document':
		if(!$we_doc->ContentType){
			exit(' ContentType Missing !!! ');
		}
		if(($we_responseText = $we_doc->checkFieldsOnSave())){
			we_editor_functions::processSave($we_doc, $we_transaction, false, false, false, false, [], $we_responseText, we_base_util::WE_MESSAGE_ERROR);
			break;
		}

		$we_JavaScript[] = ['setEditorDocumentId', $we_doc->ID]; // save/ rename a document
		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				$saveTemplate = true;
				list($wasNew, $wasSaved, $we_responseText, $we_responseTextType) = we_editor_functions::saveTemplate($we_doc, $we_transaction, $we_JavaScript);
				break;
			case we_base_ContentTypes::APPLICATION:
				if((!we_base_permission::hasPerm('NEW_SONSTIGE')) && in_array($we_doc->Extension, we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::HTML))){
					$we_JavaScript = [];
					we_editor_functions::processSave($we_doc, $we_transaction, false, false, false, false, [], sprintf(g_l('weEditor', '[application/*][response_save_wrongExtension]'), $we_doc->Path, $we_doc->Extension), we_base_util::WE_MESSAGE_ERROR);
					break;
				}
			//falls through
			default:
				$wf_flag = false;
				$wasNew = (intval($we_doc->ID) == 0);
				$wasPubl = (!empty($we_doc->Published));
				if(!we_base_permission::hasPerm('ADMINISTRATOR') && $we_doc->ContentType != we_base_ContentTypes::OBJECT && $we_doc->ContentType != we_base_ContentTypes::OBJECT_FILE && !we_users_util::in_workspace($we_doc->ParentID, get_ws($we_doc->Table, true), $we_doc->Table)){
					we_editor_functions::saveInc($we_transaction, $we_doc, g_l('alert', '[' . FILE_TABLE . '][not_im_ws]'), we_base_util::WE_MESSAGE_ERROR, $we_JavaScript, false, false, $GLOBALS['we_responseJS'], false, false, !empty($GLOBALS['publish_doc']));

					exit();
				}
				if(!$we_doc->userCanSave()){
					we_editor_functions::saveInc($we_transaction, $we_doc, g_l('alert', '[access_denied]'), we_base_util::WE_MESSAGE_ERROR, $we_JavaScript, false, false, $GLOBALS['we_responseJS'], false, false, !empty($GLOBALS['publish_doc']));

					exit();
				}

				$oldID = $we_doc->ID;
				if($we_doc->we_save()){
					if($oldID == 0){
						$we_doc->lockDocument();
					}
					$wasSaved = true;
					switch($we_doc->ContentType){
						case we_base_ContentTypes::OBJECT:
							$we_JavaScript[] = ['updateVTab', OBJECT_FILES_TABLE];
							break;
						case we_base_ContentTypes::COLLECTION:
							$we_JavaScript[] = ['updateVTab', VFILE_TABLE];
							break;
					}
					$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_ok]'), $we_doc->Path);
					$we_responseTextType = we_base_util::WE_MESSAGE_NOTICE;

					if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 5)){
						if($we_doc->i_publInScheduleTable()){
							if(($foo = $we_doc->getNextPublishDate())){
								$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][autoschedule]'), date(g_l('date', '[format][default]'), $foo));
								$we_responseTextType = we_base_util::WE_MESSAGE_NOTICE;
							}
						} elseif($we_doc->we_publish()){
							if(defined('WORKFLOW_TABLE') && (we_workflow_utility::inWorkflow($we_doc->ID, $we_doc->Table))){
								we_workflow_utility::removeDocFromWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], '');
							}
							$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_ok]'), $we_doc->Path);
							$we_responseTextType = we_base_util::WE_MESSAGE_NOTICE;
// SEEM, here a doc is published
							$GLOBALS['publish_doc'] = true;
							switch($we_doc->EditPageNr){
								case we_base_constants::WE_EDITPAGE_PROPERTIES:
								case we_base_constants::WE_EDITPAGE_INFO:
								case we_base_constants::WE_EDITPAGE_PREVIEW:
									if($_SESSION['weS']['we_mode'] !== we_base_constants::MODE_SEE && (!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4))){
										$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];
										$GLOBALS['we_responseJS'][] = ['reload_editfooter', $we_transaction]; // reload the footer with the buttons
									}
							}
						} else {
							$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_notok]'), $we_doc->Path);
							$we_responseTextType = we_base_util::WE_MESSAGE_ERROR;
						}
					} else {
						$tmp = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
						if(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO && (!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4))) || $tmp){
							$we_responseText = $tmp ? '' : $we_responseText;
							$we_responseTextType = $tmp ? we_base_util::WE_MESSAGE_ERROR : $we_responseTextType;
							$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];

							switch($tmp){
								case 1:
									$we_JavaScript[] = ['workflow_isIn', $we_transaction, we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4)];
									$wf_flag = true;
									break;
								case 2:
									$we_JavaScript[] = ['workflow_pass', $we_transaction];
									$wf_flag = true;
									break;
								case 3:
									$we_JavaScript[] = ['workflow_decline', $we_transaction];
									$wf_flag = true;
									break;
								default:
							}
						}
// Bug Fix #2065 -> Reload Preview Page of other documents
						elseif($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW && $we_doc->ContentType == we_base_ContentTypes::APPLICATION){
							$we_JavaScript[] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];
						}
					}

					$we_JavaScript[] = $we_doc->getUpdateTreeScript(!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4), null, true);

					if($wasNew || (!$wasPubl)){
						if($we_doc->ContentType === we_base_ContentTypes::FOLDER){
							$we_JavaScript[] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];
						}
						$we_JavaScript[] = ['reload_editfooter', $we_transaction];
					}
					$we_JavaScript[] = ['we_setPath', $we_doc->Path, $we_doc->Text, intval($we_doc->ID), ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table != TEMPLATES_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published'))];


					if(!we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
						$we_JavaScript[] = ['setEditorDocumentId', $we_doc->ID];
					}
					if($we_doc->canHaveVariants(true)){
						we_base_variants::setVariantDataForModel($we_doc, true);
					}
				} else {
					$we_JavaScript = [];
					$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_notok]'), $we_doc->Path);
					$we_responseTextType = we_base_util::WE_MESSAGE_ERROR;
				}

				if(($js = we_base_request::_(we_base_request::JSON, 'we_cmd', '', 6))){
					$we_JavaScript[] = $js;
					$isClose = preg_match('|closeDocument|', $js);
				} else if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4) && (!$wf_flag)){
					$we_doc->makeSameNew();
					$we_JavaScript[] = ["we_setPath", $we_doc->Path, $we_doc->Text, intval($we_doc->ID), ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table != TEMPLATES_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published'))];
//	switch to propertiy page, when user is allowed to do so.
					switch($_SESSION['weS']['we_mode']){
						case we_base_constants::MODE_SEE:
							$showAlert = true; //	don't show confirm box in editor_save.inc
							$GLOBALS['we_responseJS'][] = ['switch_edit_page', (we_base_permission::hasPerm('CAN_SEE_PROPERTIES') ? we_base_constants::WE_EDITPAGE_PROPERTIES : $we_doc->EditPageNr),
								$we_transaction];
							break;
						case we_base_constants::MODE_NORMAL:
							$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];
							break;
					}
				}
		}

		if($wasNew){ // add to history
			$we_JavaScript[] = ['addHistory', $we_doc->Table, $we_doc->ID, $we_doc->ContentType];
		}

		we_editor_functions::processSave($we_doc, $we_transaction, $wasSaved, $saveTemplate, $isClose, $showAlert, $we_JavaScript, $we_responseText, $we_responseTextType);

		break;
	case 'unpublish':
		we_editor_functions::doUnpublish($we_doc, $we_transaction);
		break;
	default:
		//	t_e($cmd0, 'include2');
		we_editor_functions::includeEditorDefault($we_doc, $we_transaction, $insertReloadFooter);
		break;
}


// prevent persmissions overriding
$_SESSION['perms'] = $perms;
