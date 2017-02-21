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
abstract class we_editor_functions{

	public static function processEditorCmd($we_doc, $cmd0){
		switch($cmd0){
			case 'load_editor':
// set default tab for creating new imageDocuments to "metadata":
				if($we_doc->ContentType == we_base_ContentTypes::IMAGE && $we_doc->ID == 0){
					$_SESSION['weS']['EditPageNr'] = $we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
				}
				break;
			case 'resizeImage':
				$we_doc->resizeImage(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3));
				break;
			case 'rotateImage':
				$we_doc->rotateImage(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
				break;
			case 'del_thumb':
				$we_doc->del_thumbnails(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'do_add_thumbnails':
				$we_doc->add_thumbnails(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1));
				break;
			case 'copyDocumentSelect':
			case 'copyDocument':
				$we_doc->InWebEdition = true;
				if($we_doc->copyDoc(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)) && $we_doc instanceof we_template){
					return we_base_jsCmd::singleCmd('reloadMainEditor');
				}
				break;
			case 'delete_list':
				$we_doc->removeEntryFromList(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 2), we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
				break;
			case 'insert_entry_at_list':
				$we_doc->insertEntryAtList(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 2), we_base_request::_(we_base_request::INT, 'we_cmd', 1, 3));
				break;
			case 'up_entry_at_list':
				$we_doc->upEntryAtList(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 2), we_base_request::_(we_base_request::INT, 'we_cmd', 1, 4));
				break;
			case 'down_entry_at_list':
				$we_doc->downEntryAtList(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 2), we_base_request::_(we_base_request::INT, 'we_cmd', 1, 4));
				break;
			case 'up_link_at_list':
				$we_doc->upEntryAtLinklist(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2));
				break;
			case 'down_link_at_list':
				$we_doc->downEntryAtLinklist(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2));
				break;
			case 'add_entry_to_list':
				$we_doc->addEntryToList(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::INT, 'we_cmd', 1, 2));
				break;
			case 'add_link_to_linklist':
				$GLOBALS['we_list_inserted'] = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
				$we_doc->addLinkToLinklist($GLOBALS['we_list_inserted']);
				break;
			case 'delete_linklist':
				$we_doc->removeLinkFromLinklist(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2), we_base_request::_(we_base_request::CMD, 'we_cmd', '', 3));
				break;
			case 'insert_link_at_linklist':
				$GLOBALS['we_list_insertedNr'] = abs(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2));
				$GLOBALS['we_list_inserted'] = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
				$we_doc->insertLinkAtLinklist($GLOBALS['we_list_inserted'], $GLOBALS['we_list_insertedNr']);
				break;
			case 'change_linklist':
				$we_doc->changeLinklist(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
				break;
			case 'change_link':
				$we_doc->changeLink(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
				break;
			case 'doctype_changed':
				$we_doc->changeDoctype('', true);
				$jsCmd = new we_base_jsCmd();
				$jsCmd->addCmd('reload_editfooter', $GLOBALS['we_transaction']);
				$jsCmd->addCmd('reload_edit_header', $GLOBALS['we_transaction']);
				return $jsCmd->getCmds();
			case 'template_changed':
				$we_doc->changeTemplate();
				$jsCmd = new we_base_jsCmd();
				$jsCmd->addCmd('reload_editfooter', $GLOBALS['we_transaction']);
				$jsCmd->addCmd('reload_edit_header', $GLOBALS['we_transaction']);
				return $jsCmd->getCmds();
			case 'remove_image':
				$we_doc->remove_image(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
				break;
			case 'wrap_on_off':
				$_SESSION['weS']['we_wrapcheck'] = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 1);
				$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_CONTENT;
				break;
			case 'users_add_owner':
				$we_doc->add_owner(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1));
				break;
			case 'users_del_owner':
				$we_doc->del_owner(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'users_add_user':
				$we_doc->add_user(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1));
				break;
			case 'users_del_user':
				$we_doc->del_user(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'users_del_all_owners':
				$we_doc->del_all_owners();
				break;
			case 'customer_applyWeDocumentCustomerFilterFromFolder':
				$we_doc->applyWeDocumentCustomerFilterFromFolder();
				break;
			case 'restore_defaults':
				$we_doc->restoreDefaults();
				break;
			case 'object_add_workspace':
				$we_doc->add_workspace(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 1));
				break;
			case 'object_del_workspace':
				$we_doc->del_workspace(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'object_ws_from_class':
				$we_doc->ws_from_class();
				break;
			case 'switch_edit_page':
				$_SESSION['weS']['EditPageNr'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
				$we_doc->EditPageNr = $_SESSION['weS']['EditPageNr'];
				if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
					return we_base_jsCmd::singleCmd('reload_editfooter', $GLOBALS['we_transaction']);
				}
				break;
			case 'delete_link':
				$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
				$we_doc->delElement($name);
				break;
			case 'add_cat':
				$we_doc->addCat(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', 0, 1));
				break;
			case 'delete_cat':
				$we_doc->delCat(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'object_changeTempl_ob':
				$we_doc->changeTempl_ob(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2));
				break;
			case 'delete_all_cats':
				$we_doc->Category = '';
				break;
			case 'schedule_add':
				$we_doc->add_schedule();
				break;
			case 'schedule_del':
				$we_doc->del_schedule(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'schedule_delete_schedcat':
				$we_doc->delete_schedcat(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2));
				break;
			case 'schedule_delete_all_schedcats':
				$we_doc->schedArr[we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)]['CategoryIDs'] = '';
				break;
			case 'schedule_add_schedcat':
				$we_doc->add_schedcat(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2));
				break;
			case 'doImage_convertGIF':
				$we_doc->convert('gif');
				break;
			case 'doImage_convertPNG':
				$we_doc->convert('png');
				break;
			case 'doImage_convertJPEG':
				$we_doc->convert('jpg', we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'doImage_crop':
				$filename = TEMP_PATH . we_base_file::getUniqueId();
				copy($we_doc->getElement('data'), $filename);
//$filename = weFile::saveTemp($we_doc->getElement('data'));

				$x = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
				$y = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
				$width = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3);
				$height = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);

				$img = Image_Transform::factory('GD');
				if(PEAR::isError($stat = $img->load($filename))){
					trigger_error($stat->getMessage() . ' Filename: ' . $filename);
				}
				if(PEAR::isError($stat = $img->crop($width, $height, $x, $y))){
					trigger_error($stat->getMessage() . ' Filename: ' . $filename);
				}
				if(PEAR::isError($stat = $img->save($filename))){
					trigger_error($stat->getMessage() . ' Filename: ' . $filename);
				}

				$we_doc->setElement('data', $filename);
				$we_doc->setElement('width', $width, 'attrib', 'bdid');
				$we_doc->setElement('origwidth', $width, 'attrib', 'bdid');
				$we_doc->setElement('height', $height, 'attrib', 'bdid');
				$we_doc->setElement('origheight', $height, 'attrib', 'bdid');
				$we_doc->DocChanged = true;
				break;
			case 'object_add_css':
				$we_doc->add_css(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', 0, 1));
				break;
			case 'object_del_css':
				$we_doc->del_css(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'add_navi':
				$we_doc->addNavi(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4));
				break;
			case 'delete_navi':
				$we_doc->delNavi(we_base_request::_(we_base_request::FILE, 'we_cmd', '', 1));
				break;
			case 'delete_all_navi':
				$we_doc->delAllNavi();
				break;
			case 'revert_published':
				$we_doc->revert_published();
				break;
		}
		return '';
	}

	/*
	 * if the document is a webEdition document, we save it with a temp-name (path of document+extension) and redirect
	 * to the tmp-location. This is done for the content- and preview-editpage.
	 * With html-documents this is only done for preview-editpage.
	 * We need to do this, because, when the pages has for example jsp. content, it will be parsed right!
	 * This is only done when the IsDynamic - PersistantSlot is false.
	 */

	public static function includeEditor($we_doc, $we_transaction, $insertReloadFooter){//this is really unclear what is done here
		$we_include = $we_doc->editor();
		ob_start();
		if($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT){
//remove all already parsed names
			$we_doc->resetUsedElements();
		}
		include((substr(strtolower($we_include), 0, strlen($_SERVER['DOCUMENT_ROOT'])) == strtolower($_SERVER['DOCUMENT_ROOT']) ?
						'' : WE_INCLUDES_PATH) .
				$we_include);
		$docContents = ob_get_clean();
		//usedElementNames is set after include
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
//  SEEM the file
//  but only, if we are not in the template-editor
		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				$contents = $docContents;
				break;
			default:
				$contents = we_SEEM::parseDocument($docContents);

				$contents = (strpos($contents, '</head>') ?
						str_replace('</head>', $insertReloadFooter . '</head>', $contents) :
						$insertReloadFooter . $contents);
		}

		switch($we_doc->Extension){
			case '.js':
			case '.css':
			case '.wml':
			case '.xml':
				$we_ext = '.html';
				break;
			default:
				$we_ext = $we_doc->Extension;
		}

		$tempName = TEMP_DIR . we_base_file::getUniqueId() . $we_ext;
		$fullName = $_SERVER['DOCUMENT_ROOT'] . $tempName;
		we_base_file::insertIntoCleanUp($fullName, 0);

		ob_start();
		//FIXME: eval, document was included, what needs to evaluated
		eval('?>' . str_replace('<?xml', '<?= \'<?xml\'; ?>', $contents));
		$evaled = ob_get_clean();
//
// --> Glossary Replacement
//

		$glossarHTML = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['we_editmode']) || (isset($GLOBALS['we_editmode']) && !$GLOBALS['we_editmode'])) && isset($we_doc->InGlossar) && $we_doc->InGlossar == 0) ?
				we_glossary_replace::replace($evaled, $we_doc->Language) :
				$evaled);

		if(!empty($GLOBALS['we_editmode'])){
			$matches = [];
			preg_match_all('|<form( name="we_form")|i', $glossarHTML, $matches, PREG_PATTERN_ORDER);
			if($matches && !empty($matches[0])){
				//find the number of we-forms
				$all = count($matches[0]);
				$no = count(array_filter($matches[1]));
				if($no > 1){
					//sth very bad must have happend to have 2 we forms in one page
					$warn = $no . ' ' . g_l('parser', '[form][we]');
					t_e($warn, str_replace('.html', '.tmpl', $we_doc->Path));
				}
				if($all - $no){
					$warn = $no . ' ' . g_l('parser', '[form][duplicate]');
					t_e($warn, str_replace('.html', '.tmpl', $we_doc->Path));
				}
			}
		}
		we_base_file::save($fullName, $glossarHTML);

		header('Location: ' . WEBEDITION_DIR . 'showTempFile.php?charset=' . (empty($GLOBALS['CHARSET']) ? DEFAULT_CHARSET : $GLOBALS['CHARSET']) . '&file=' . str_replace(WEBEDITION_DIR, '', $tempName));
	}

	private static function unPublishInc($we_transaction, $we_responseText = '', $we_responseTextType = '', we_base_jsCmd $jsCmd = null, $we_responseJS = []){
		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'editor_save.js', '', ['id' => 'loadVarEditor_save', 'data-editorSave' => setDynamicVar([
						'we_editor_save' => false,
						'we_transaction' => $we_transaction,
						'isSEEMode' => $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE,
						'we_JavaScript' => [],
						'we_responseText' => $we_responseText,
						'we_responseTextType' => $we_responseTextType,
						'we_responseJS' => $we_responseJS,
			])]) . ($jsCmd ? $jsCmd->getCmds() : ''), we_html_element::htmlBody());
	}

	public static function saveInc($we_transaction, $we_doc, $we_responseText = '', $we_responseTextType = '', array $we_JavaScript = [], $wasSaved = false, $saveTemplate = false, $we_responseJS = [
	], $isClose = false, $showAlert = false, $publish_doc = false){
		$reload = [];
		if(!empty($wasSaved)){
			// DOC was saved, mark open tabs to reload if necessary
			// was saved - not hot anymore

			switch($we_doc->ContentType){
				case we_base_ContentTypes::FOLDER:
					if($we_doc->wasMoved()){
						$reload[$we_doc->Table] = implode(',', $GLOBALS['DB_WE']->getAllq('SELECT f.ID FROM ' . $we_doc->Table . ' f INNER JOIN ' . LOCK_TABLE . ' l ON f.ID=l.ID AND l.tbl="' . stripTblPrefix($we_doc->Table) . '" WHERE f.Path LIKE "' . $we_doc->Path . '/%"', true));
					}
					break;

				case we_base_ContentTypes::TEMPLATE: // #538 reload documents based on this template
					$reloadDocsTempls = we_rebuild_base::getTemplAndDocIDsOfTemplate($we_doc->ID, false, false, true, true);

					// reload all documents based on this template
					$reload[FILE_TABLE] = implode(',', $reloadDocsTempls['documentIDs']);
					//no need to reload the edit tab, since this is not changed & Preview is always regenerated
//			$reload[TEMPLATES_TABLE] = implode(',', $reloadDocsTempls['templateIDs']);

					break;
				case we_base_ContentTypes::OBJECT:
					$GLOBALS['DB_WE']->query('SELECT of.ID FROM ' . OBJECT_FILES_TABLE . ' of INNER JOIN ' . LOCK_TABLE . ' l ON of.ID=l.ID AND l.tbl="' . stripTblPrefix(OBJECT_FILES_TABLE) . '" WHERE of.IsFolder=0 AND of.TableID=' . intval($we_doc->ID));
					$reload[OBJECT_FILES_TABLE] = implode(',', $GLOBALS['DB_WE']->getAll(true));
			}
		}

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'editor_save.js', '', ['id' => 'loadVarEditor_save', 'data-editorSave' => setDynamicVar([
						'we_editor_save' => true,
						'we_transaction' => $we_transaction,
						'isHot' => ($we_responseText && $we_responseTextType == we_message_reporting::WE_MESSAGE_ERROR),
						'wasSaved' => $wasSaved,
						'wasPublished' => !empty($we_doc->Published),
						'isPublished' => $publish_doc,
						'isSEEMode' => $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE,
						'reloadEditors' => array_filter($reload),
						'ContentType' => $we_doc->ContentType,
						'docID' => $we_doc->ID,
						'EditPageNr' => $we_doc->EditPageNr,
						'saveTmpl' => $saveTemplate,
						'isClose' => $isClose,
						'showAlert' => $showAlert,
						'we_responseText' => $we_responseText,
						'we_responseTextType' => $we_responseTextType,
						//FIXME:we_JavaScript is evaled
						'we_JavaScript' => $we_JavaScript,
						'we_cmd5' => we_base_request::_(we_base_request::JSON, 'we_cmd', '', 5), // this is we_responseJS through save-template-question
						'we_responseJS' => $we_responseJS,
						'docHasPreview' => in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs),
			])]), we_html_element::htmlBody());
	}

	public static function templateSave($we_transaction){

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsElement('var url=WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?' . http_build_query(
								['we_cmd' => [
								0 => 'save_document',
								1 => $we_transaction,
								2 => 1,
								5 => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 5), //is json64
								6 => we_base_request::_(we_base_request::RAW, 'we_cmd', '', 6), //is json64
							],
							'we_transaction' => $we_transaction,
							'we_complete_request' => 1
								], null, '&') .
						'";
new (WE().util.jsWindow)(window, url,"templateSaveQuestion",WE().consts.size.dialog.smaller,WE().consts.size.dialog.tiny,true,false,true);
'), '<body></body>');
	}

	public static function templateSaveQuestion($we_transaction, $isTemplatesUsedByThisTemplate, $nrDocsUsedByThisTemplate, $we_responseJS){
		$we_cmd6 = we_base_request::_(we_base_request::JSON, 'we_cmd', '', 6);

		$alerttext = ($isTemplatesUsedByThisTemplate ?
				g_l('alert', '[template_save_warning2]') :
				sprintf((g_l('alert', ($nrDocsUsedByThisTemplate == 1) ? '[template_save_warning1]' : '[template_save_warning]')), $nrDocsUsedByThisTemplate)
				);

		echo we_html_tools::getHtmlTop(g_l('global', '[question]'), '', '', we_html_element::jsScript(JS_DIR . 'template_save_question.js', '', ['id' => 'loadVarTemplate_save_question',
					'data-editorSave' => setDynamicVar([
						'we_transaction' => $we_transaction,
						'we_responseJS' => $we_responseJS,
						'we_cmd6' => $we_cmd6
			])]), we_html_element::htmlBody(['class' => "weEditorBody", 'onload' => "self.focus();", 'onblur' => "self.focus()"], we_html_tools::htmlYesNoCancelDialog($alerttext, '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', true, true, true, 'pressed_yes_button()', 'pressed_no_button()', 'pressed_cancel_button()')
				)
		);
	}

	public static function processSave($we_doc, $we_transaction, $wasSaved, $saveTemplate, $isClose, $showAlert, $we_responseText = '', $we_responseTextType = ''){
		$we_responseText .= $we_doc->getErrMsg();
		if($_SERVER['REQUEST_METHOD'] === 'POST' && !we_base_request::_(we_base_request::BOOL, 'we_complete_request')){
			//will show the message
			echo we_message_reporting::jsMessagePush(g_l('weEditor', '[incompleteRequest]'), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session

		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			we_schedpro::trigger_schedule();
			$we_JavaScript[] = ['setEditorDocumentId', $we_doc->ID]; // save/ rename a document
		}
		self::saveInc($we_transaction, $we_doc, $we_responseText, $we_responseTextType, $we_JavaScript, $wasSaved, $saveTemplate, (!empty($GLOBALS['we_responseJS']) ? $GLOBALS['we_responseJS'] : []), $isClose, $showAlert, !empty($GLOBALS["publish_doc"]));
	}

	//if document is locked - only Preview mode is possible. otherwise show warning.
	public static function documentLocking($we_doc){
		$userID = $we_doc->isLockedByUser();
		if($userID != 0 && $userID != $_SESSION['user']['ID'] && $we_doc->ID){ // document is locked
			if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs) && !$we_doc instanceof we_template){
				$we_doc->EditPageNr = we_base_constants::WE_EDITPAGE_PREVIEW;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PREVIEW;
			} else if($we_doc instanceof we_folder){
				$target = in_array(we_base_constants::WE_EDITPAGE_DOCLIST, $we_doc->EditPageNrs) ? we_base_constants::WE_EDITPAGE_DOCLIST : we_base_constants::WE_EDITPAGE_FIELDS;
				$we_doc->EditPageNr = $target;
				$_SESSION['weS']['EditPageNr'] = $target;
			} else {
				$we_doc->showLockedWarning($userID);
			}
		} elseif($userID != $_SESSION['user']['ID'] && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE && $we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW){
// lock document, if in seeMode and EditMode !!, don't lock when already locked
			$we_doc->lockDocument();
		}
	}

	public static function doUnpublish($we_doc, $we_transaction){
		if(!$we_doc->Published){
			self::unPublishInc($we_transaction, sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_not_published]'), $we_doc->Path), we_message_reporting::WE_MESSAGE_ERROR);
			return;
		}
		$jsCmd = new we_base_jsCmd();
		if($we_doc->we_unpublish()){
			$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_unpublish_ok]'), $we_doc->Path);
			$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
			if($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO){
				$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction]; // wird in Templ eingef?gt
			}
//	When unpublishing a document stay where u are.
//	uncomment the following line to switch to preview page.
			$GLOBALS['we_responseJS'][] = ['reload_editfooter', $we_transaction];
			$jsCmd->addCmd('setEditorDocumentId', $we_doc->ID);
			$we_doc->getUpdateTreeScript(true, $jsCmd); // save/ rename a document
		} else {
			$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_unpublish_notok]'), $we_doc->Path);
			$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
		}
		if($_SERVER['REQUEST_METHOD'] === 'POST' && !we_base_request::_(we_base_request::BOOL, 'we_complete_request')){
			$we_responseText = g_l('weEditor', '[incompleteRequest]');
			$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
		} else {
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
		}
		self::unPublishInc($we_transaction, $we_responseText, $we_responseTextType, $jsCmd, $GLOBALS['we_responseJS']);
	}

	public static function includeEditorDefault($we_doc, $we_transaction, $insertReloadFooter){
		$we_include = $we_doc->editor();
		if(!$we_include){ // object does not handle html-output, so we need to include a template( return value)
			exit('Nothing to include ...');
		}

		/* At this point complete requests are not common
		  if(!isset($_REQUEST['we_complete_request'])){
		  $we_responseText = g_l('weEditor', '[incompleteRequest]');
		  $we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
		  } */
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
		if($_SERVER['DOCUMENT_ROOT'] && substr(strtolower($we_include), 0, strlen($_SERVER['DOCUMENT_ROOT'])) == strtolower($_SERVER['DOCUMENT_ROOT'])){

			ob_start();
			if(!defined('WE_CONTENT_TYPE_SET')){
				$charset = $we_doc->getElement('Charset') ?: DEFAULT_CHARSET; //	send charset which might be determined in template
				define('WE_CONTENT_TYPE_SET', 1);
				we_html_tools::headerCtCharset('text/html', $charset);
			}
			$we_include = (file_exists($we_include) ? $we_include : WE_INCLUDES_PATH . 'we_editors/' . we_template::NO_TEMPLATE_INC);
			include($we_include);
			$contents = ob_get_clean();

//  SEEM the file
//  but only, if we are not in the template-editor
			switch($we_doc->ContentType){
				case we_base_ContentTypes::TEMPLATE:
					if($we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE){
						echo $contents;
						break;
					}
				default:
					$tmpCnt = we_SEEM::parseDocument($contents);

// insert $reloadFooter at right place
					$tmpCntHTML = (strpos($tmpCnt, '</head>')) ?
							str_replace('</head>', $insertReloadFooter . '</head>', $tmpCnt) :
							$insertReloadFooter . $tmpCnt;

// --> Start Glossary Replacement

					$useGlossary = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_ID'] == $GLOBALS['we_doc']->ID)) && (isset($we_doc->InGlossar) && $we_doc->InGlossar == 0) && (!isset($GLOBALS['we_editmode']) || (isset($GLOBALS['we_editmode']) && !$GLOBALS['we_editmode'])) && we_glossary_replace::useAutomatic());

					echo ($useGlossary ? we_glossary_replace::doReplace($tmpCntHTML, $GLOBALS['we_doc']->Language) : $tmpCntHTML);
			}
		} else {
//  These files were edited only in source-code mode, so no seeMode is needed.
			include((preg_match('#^' . WEBEDITION_DIR . 'we/#', $we_include) ? $_SERVER['DOCUMENT_ROOT'] : WE_INCLUDES_PATH) . $we_include);
			echo $insertReloadFooter;
		}
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
		if(isset($GLOBALS['we_file_to_delete_after_include'])){
			we_base_file::deleteLocalFile($GLOBALS['we_file_to_delete_after_include']);
		}
	}

}
