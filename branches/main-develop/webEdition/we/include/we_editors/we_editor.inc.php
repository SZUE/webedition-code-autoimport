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
$we_EDITOR = true;

we_html_tools::protect();
// prevent persmissions overriding
$perms = $_SESSION['perms'];
// init document
if(!isset($we_transaction) || !$we_transaction){//we_session assumes to have transaction in parameter 'we_transaction'
	$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 1));
}
$GLOBALS['we_transaction'] = $we_transaction;
$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : '';

$we_doc = we_document::initDoc($we_dt);

function processEditorCmd($we_doc, $cmd0){
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
		case 'copyDocument':
			$we_doc->InWebEdition = true;
			return $we_doc->copyDoc(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
		case 'delete_list':
			$we_doc->removeEntryFromList(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 2), we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3));
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
			$we_doc->removeLinkFromLinklist(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1), we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2), we_base_request::_(we_base_request::RAW, 'we_cmd', '', 3));
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
			return we_html_element::jsElement('try{parent.editFooter.location.reload();parent.editHeader.location.reload();}catch(exception){};');
		case 'template_changed':
			$we_doc->changeTemplate();
			return we_html_element::jsElement('try{parent.editFooter.location.reload();parent.editHeader.location.reload();}catch(exception){};');
		case 'remove_image':
			$we_doc->remove_image(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
			break;
		case 'wrap_on_off':
			$_SESSION['weS']['we_wrapcheck'] = we_base_request::_(we_base_request::BOO, 'we_cmd', false, 1);
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
		case 'object_add_extraworkspace':
			$we_doc->add_extraWorkspace(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
			break;
		case 'object_del_extraworkspace':
			$we_doc->del_extraWorkspace(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
			break;
		case 'object_ws_from_class':
			$we_doc->ws_from_class();
			break;
		case 'switch_edit_page':
			$_SESSION['weS']['EditPageNr'] = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
			$we_doc->EditPageNr = $_SESSION['weS']['EditPageNr'];
			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
				return STYLESHEET . we_html_element::jsElement('try{parent.editFooter.location.reload();}catch(exception){};');
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
			$we_doc->setElement('width', $width, 'attrib');
			$we_doc->setElement('origwidth', $width, 'attrib');
			$we_doc->setElement('height', $height, 'attrib');
			$we_doc->setElement('origheight', $height, 'attrib');
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

//if document is locked - only Preview mode is possible. otherwise show warning.
function documentLocking($we_doc){
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

/*
 * if the document is a webEdition document, we save it with a temp-name (path of document+extension) and redirect
 * to the tmp-location. This is done for the content- and preview-editpage.
 * With html-documents this is only done for preview-editpage.
 * We need to do this, because, when the pages has for example jsp. content, it will be parsed right!
 * This is only done when the IsDynamic - PersistantSlot is false.
 */

function includeEditor($we_doc, $we_transaction){//this is really unclear what is done here
	$we_include = $we_doc->editor();
	ob_start();
	if($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT){
//remove all already parsed names
		$we_doc->resetUsedElements();
	}
	include((substr(strtolower($we_include), 0, strlen($_SERVER['DOCUMENT_ROOT'])) == strtolower($_SERVER['DOCUMENT_ROOT']) ?
			'' : WE_INCLUDES_PATH) .
		$we_include);
	$contents = ob_get_clean();
	//usedElementNames is set after include
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
//  SEEM the file
//  but only, if we are not in the template-editor
	if($we_doc->ContentType != we_base_ContentTypes::TEMPLATE){
		$contents = we_SEEM::parseDocument($contents);

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
	$contents = ob_get_clean();
//
// --> Glossary Replacement
//

	if(defined('GLOSSARY_TABLE') && (!isset($GLOBALS['we_editmode']) || (isset($GLOBALS['we_editmode']) && !$GLOBALS['we_editmode'])) && isset($we_doc->InGlossar) && $we_doc->InGlossar == 0){
		$contents = we_glossary_replace::replace($contents, $we_doc->Language);
	}

	if(!empty($GLOBALS['we_editmode'])){
		$matches = [];
		preg_match_all('|<form( name="we_form")|i', $contents, $matches, PREG_PATTERN_ORDER);
		if($matches && !empty($matches[0])){
			//find the number of we-forms
			$all = count($matches[0]);
			$no = count(array_filter($matches[1]));
			if($no > 1){
				//sth very bad must have happend to have 2 we forms in one page
				$warn = $no . ' ' . g_l('parser', '[form][we]');
				t_e($warn, str_replace('.html', '.tmpl', $we_doc->Path));
				$contents = preg_replace('|<form|', '<p class="bold" style="background-color:red;color:white;">' . htmlentities($warn) . '</p><form', $contents, 1);
			}
			if($all - $no){
				$warn = $no . ' ' . g_l('parser', '[form][duplicate]');
				t_e($warn, str_replace('.html', '.tmpl', $we_doc->Path));
				$contents = preg_replace('|<form|', '<p class="bold" style="background-color:red;color:white;">' . htmlentities($warn) . '</p><form', $contents, 1);
			}
		}
	}
	we_base_file::save($fullName, $contents);

	header('Location: ' . WEBEDITION_DIR . 'showTempFile.php?charset=' . (empty($GLOBALS['CHARSET']) ? DEFAULT_CHARSET : $GLOBALS['CHARSET']) . '&file=' . str_replace(WEBEDITION_DIR, '', $tempName));
}

function doUnpublish($we_doc, $we_transaction){
	if(!$we_doc->Published){
		we_editor_save::publishInc($we_transaction, sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_not_published]'), $we_doc->Path), we_message_reporting::WE_MESSAGE_ERROR);
		return;
	}
	if($we_doc->we_unpublish()){
		$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_unpublish_ok]'), $we_doc->Path);
		$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
		if($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO){
			$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction]; // wird in Templ eingef?gt
		}
//	When unpublishing a document stay where u are.
//	uncomment the following line to switch to preview page.
		$GLOBALS['we_responseJS'][] = ['reload_editfooter'];

		$we_JavaScript = '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');' . $we_doc->getUpdateTreeScript() . ';'; // save/ rename a document
	} else {
		$we_JavaScript = '';
		$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_unpublish_notok]'), $we_doc->Path);
		$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
	}
	if($_SERVER['REQUEST_METHOD'] === 'POST' && !we_base_request::_(we_base_request::BOOL, 'we_complete_request')){
		$we_responseText = g_l('weEditor', '[incompleteRequest]');
		$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
	} else {
		$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
	}
	we_editor_save::publishInc($we_transaction, $we_responseText, $we_responseTextType, $we_JavaScript);
}

function includeEditorDefault($we_doc, $we_transaction){
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
			$charset = $we_doc->getElement('Charset') ? : DEFAULT_CHARSET; //	send charset which might be determined in template
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
				$tmpCntnt = we_SEEM::parseDocument($contents);

// insert $reloadFooter at right place
				$tmpCntnt = (strpos($tmpCntnt, '</head>')) ?
					str_replace('</head>', $insertReloadFooter . '</head>', $tmpCntnt) :
					$insertReloadFooter . $tmpCntnt;

// --> Start Glossary Replacement

				$useGlossary = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_ID'] == $GLOBALS['we_doc']->ID)) && (isset($we_doc->InGlossar) && $we_doc->InGlossar == 0) && (!isset($GLOBALS['we_editmode']) || (isset($GLOBALS['we_editmode']) && !$GLOBALS['we_editmode'])) && we_glossary_replace::useAutomatic());
				echo ($useGlossary ? we_glossary_replace::doReplace($tmpCntnt, $GLOBALS['we_doc']->Language) : $tmpCntnt);
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

$wasNew = false;
$GLOBALS['we_responseJS'] = [];
$insertReloadFooter = processEditorCmd($we_doc, we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0));

//	if document is locked - only Preview mode is possible. otherwise show warning.
documentLocking($we_doc);


/*
 * if the document is a webEdition document, we save it with a temp-name (path of document+extension) and redirect
 * to the tmp-location. This is done for the content- and preview-editpage.
 * With html-documents this is only done for preview-editpage.
 * We need to do this, because, when the pages has for example jsp. content, it will be parsed right!
 * This is only done when the IsDynamic - PersistantSlot is false.
 */
$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
if(
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
	) &&
	!$we_doc->IsDynamic &&
	!empty($_POST) &&
	we_base_request::_(we_base_request::BOOL, 'we_complete_request')
){
	includeEditor($we_doc, $we_transaction);
} else {
	$we_JavaScript = '';
	switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
		case 'save_document':
			if(!$we_doc->ContentType){
				exit(' ContentType Missing !!! ');
			}
			$saveTemplate = true;
			if(($we_responseText = $we_doc->checkFieldsOnSave())){
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
				$saveTemplate = false;
			} else {
				$we_JavaScript = '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');'; // save/ rename a document
				if($we_doc->ContentType == we_base_ContentTypes::TEMPLATE){
					if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 8)){
// if  we_cmd[8] is set, it means that 'automatic rebuild' was clicked
// so we need to check we_cmd[3] (means save immediately) and we_cmd[4] (means rebuild immediately)
						$_REQUEST['we_cmd'][3] = 1;
						$_REQUEST['we_cmd'][4] = 1;
					}
					if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 5)){ //Save in version
						$_REQUEST['we_cmd'][5] = false;
						$we_doc->we_publish();
					}

//used in demo version to remove this code
####TEMPLATE_SAVE_CODE2_START###
					$TEMPLATE_SAVE_CODE2 = true;
					$arr = we_rebuild_base::getTemplAndDocIDsOfTemplate($we_doc->ID, true, true);
					$nrDocsUsedByThisTemplate = count($arr['documentIDs']);
					$isTemplatesUsedByThisTemplate = $we_doc->ID && f('SELECT 1 FROM ' . TEMPLATES_TABLE . ' WHERE MasterTemplateID=' . $we_doc->ID . ' LIMIT 1');
					$somethingNeedsToBeResaved = ($nrDocsUsedByThisTemplate + $isTemplatesUsedByThisTemplate) > 0;

					if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 2)){
//this is the second call to save_document (see next else command)
						include(WE_INCLUDES_PATH . 'we_editors/we_template_save_question.inc.php'); // this includes the gui for the save question dialog
						$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
						exit();
					} else if(!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 3) && $somethingNeedsToBeResaved){
// this happens when the template is saved and there are documents which use the template and "automatic rebuild" is not checked!
						include(WE_INCLUDES_PATH . 'we_TemplateSave.inc.php'); // this calls again we_cmd with save_document and sets we_cmd[2]
						$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
						exit();
					} else {
//this happens when we_cmd[3] is set and not we_cmd[2]
						$oldID = $we_doc->ID;
						if($we_doc->we_save()){
							if($oldID == 0){
								$we_doc->lockDocument();
							}
							$wasSaved = true;
							$wasNew = (intval($we_doc->ID) == 0) ? true : false;
							$we_JavaScript .= "WE().layout.we_setPath(_EditorFrame,'" . $we_doc->Path . "', '" . $we_doc->Text . "', " . intval($we_doc->ID) . ",'" . ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table != TEMPLATES_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')) . "');" .
								'_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');' . $we_doc->getUpdateTreeScript() . ';'; // save/ rename a document
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_ok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
							if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4)){
// this happens when the documents which uses the templates has to be rebuilt. (if user clicks "yes" at template save question or if automatic rebuild was set)
								if($somethingNeedsToBeResaved){
									$we_JavaScript .= '_EditorFrame.setEditorIsHot(false);
top.openWindow(\'' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_filter&templateID=' . $we_doc->ID . '&responseText=' . rawurlencode(sprintf($we_responseText, $we_doc->Path)) . '\',\'resave\',-1,-1,600,130,0,true);';
									$we_responseText = '';
								}
							}
						} else {
// we got an error while saving the template
							$we_JavaScript = '';
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_notok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
						}
					}
####TEMPLATE_SAVE_CODE2_END###
					if(!isset($TEMPLATE_SAVE_CODE2) || !$TEMPLATE_SAVE_CODE2){
						$we_responseText = g_l('weEditor', '[text/weTmpl][no_template_save]');
						$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
						include(WE_INCLUDES_PATH . 'we_editors/we_editor_save.inc.php');
						exit();
					}
//FIXME: is this safe??? Code-Injection!
					if(($js = we_base_request::_(we_base_request::JS, 'we_cmd', '', 6))){
						$we_JavaScript .= $js;
					}
				} else {
					if((!permissionhandler::hasPerm('NEW_SONSTIGE')) && $we_doc->ContentType == we_base_ContentTypes::APPLICATION && in_array($we_doc->Extension, we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::HTML))){
						$we_JavaScript = '';
						$we_responseText = sprintf(g_l('weEditor', '[application/*][response_save_wrongExtension]'), $we_doc->Path, $we_doc->Extension);
						$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
					} else {

						$wf_flag = false;
						$wasNew = (intval($we_doc->ID) == 0) ? true : false;
						$wasPubl = (!empty($we_doc->Published)) ? true : false;
						if(!permissionhandler::hasPerm('ADMINISTRATOR') && $we_doc->ContentType != we_base_ContentTypes::OBJECT && $we_doc->ContentType != we_base_ContentTypes::OBJECT_FILE && !we_users_util::in_workspace($we_doc->ParentID, get_ws($we_doc->Table, true), $we_doc->Table)){
							$we_responseText = g_l('alert', '[' . FILE_TABLE . '][not_im_ws]');
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
							include(WE_INCLUDES_PATH . 'we_editors/we_editor_save.inc.php');
							exit();
						}
						if(!$we_doc->userCanSave()){
							$we_responseText = g_l('alert', '[access_denied]');
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
							include(WE_INCLUDES_PATH . 'we_editors/we_editor_save.inc.php');
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
									$we_JavaScript .= "if(top.treeData.table=='" . OBJECT_FILES_TABLE . "'){top.we_cmd('loadVTab', top.treeData.table, 0);}";
									break;
								case we_base_ContentTypes::COLLECTION:
									$we_JavaScript .= "if(top.treeData.table==='" . VFILE_TABLE . "'){top.we_cmd('loadVTab', top.treeData.table, 0);}";
									break;
							}
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_ok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;

							if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 5)){
								if($we_doc->i_publInScheduleTable()){
									if(($foo = $we_doc->getNextPublishDate())){
										$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][autoschedule]'), date(g_l('date', '[format][default]'), $foo));
										$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
									}
								} elseif($we_doc->we_publish()){
									if(defined('WORKFLOW_TABLE') && (we_workflow_utility::inWorkflow($we_doc->ID, $we_doc->Table))){
										we_workflow_utility::removeDocFromWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], '');
									}
									$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_ok]'), $we_doc->Path);
									$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
// SEEM, here a doc is published
									$GLOBALS['publish_doc'] = true;
									switch($we_doc->EditPageNr){
										case we_base_constants::WE_EDITPAGE_PROPERTIES:
										case we_base_constants::WE_EDITPAGE_INFO:
										case we_base_constants::WE_EDITPAGE_PREVIEW:
											if($_SESSION['weS']['we_mode'] !== we_base_constants::MODE_SEE && (!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4))){
												$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];
												$GLOBALS['we_responseJS'][] = ['reload_editfooter']; // reload the footer with the buttons
											}
									}
								} else {
									$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_notok]'), $we_doc->Path);
									$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
								}
							} else {
								$tmp = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 7);
								if(($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_INFO && (!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4))) || $tmp){
									$we_responseText = $tmp ? '' : $we_responseText;
									$we_responseTextType = $tmp ? we_message_reporting::WE_MESSAGE_ERROR : $we_responseTextType;
									$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];

									switch($tmp){
										case 1:
											$we_JavaScript .= 'top.we_cmd("workflow_isIn","' . $we_transaction . '","' . we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4) . '");';
											$wf_flag = true;
											break;
										case 2:
											$we_JavaScript .= 'top.we_cmd("workflow_pass","' . $we_transaction . '");';
											$wf_flag = true;
											break;
										case 3:
											$we_JavaScript .= 'top.we_cmd("workflow_decline","' . $we_transaction . '");';
											$wf_flag = true;
											break;
										default:
									}
								}
// Bug Fix #2065 -> Reload Preview Page of other documents
								elseif($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW && $we_doc->ContentType == we_base_ContentTypes::APPLICATION){
									$we_JavaScript .= 'top.we_cmd("switch_edit_page",' . $we_doc->EditPageNr . ',"' . $we_transaction . '");';
								}
							}

							$we_JavaScript .= $we_doc->getUpdateTreeScript(!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4));

							if($wasNew || (!$wasPubl)){

								$we_JavaScript .= ($we_doc->ContentType === "folder" ? 'top.we_cmd("switch_edit_page",' . $we_doc->EditPageNr . ',"' . $we_transaction . '");' : '') .
									'_EditorFrame.getDocumentReference().frames.editFooter.location.reload();';
							}
							$we_JavaScript .= "WE().layout.we_setPath(_EditorFrame,'" . $we_doc->Path . "','" . $we_doc->Text . "', " . intval($we_doc->ID) . ",'" . ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table != TEMPLATES_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')) . "');";


							if(!we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
								$we_JavaScript .= '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');';
							}

							if(($we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType === we_base_ContentTypes::OBJECT_FILE) && $we_doc->canHaveVariants(true)){
								we_base_variants::setVariantDataForModel($we_doc, true);
							}
						} else {
							$we_JavaScript = '';
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_notok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
						}
					}
					if(($js = we_base_request::_(we_base_request::JS, 'we_cmd', '', 6))){
						$we_JavaScript .= $js;
						$isClose = preg_match('|closeDocument|', $js);
					} else if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 4) && (!$wf_flag)){
						$we_doc->makeSameNew();
						$we_JavaScript .= "WE().layout.we_setPath(_EditorFrame,'" . $we_doc->Path . "','" . $we_doc->Text . "', " . intval($we_doc->ID) . ",'" . ($we_doc->Published == 0 ? 'notpublished' : ($we_doc->Table != TEMPLATES_TABLE && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')) . "');";
//	switch to propertiy page, when user is allowed to do so.
						switch($_SESSION['weS']['we_mode']){
							case we_base_constants::MODE_SEE:
								$showAlert = true; //	don't show confirm box in editor_save.inc
								$GLOBALS['we_responseJS'][] = ['switch_edit_page', (permissionhandler::hasPerm('CAN_SEE_PROPERTIES') ? we_base_constants::WE_EDITPAGE_PROPERTIES : $we_doc->EditPageNr), $we_transaction];
								break;
							case we_base_constants::MODE_NORMAL:
								$GLOBALS['we_responseJS'][] = ['switch_edit_page', $we_doc->EditPageNr, $we_transaction];
								break;
						}
					}
				}

				if($wasNew){ // add to history
					$we_JavaScript .= "WE().layout.weNavigationHistory.addDocToHistory('" . $we_doc->Table . "', " . $we_doc->ID . ", '" . $we_doc->ContentType . "');";
				}
			}
			$we_responseText.=$we_doc->getErrMsg();
			if($_SERVER['REQUEST_METHOD'] === 'POST' && !we_base_request::_(we_base_request::BOOL, 'we_complete_request')){
				//will show the message
				echo we_message_reporting::jsMessagePush(g_l('weEditor', '[incompleteRequest]'), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			}
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session

			if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
				we_schedpro::trigger_schedule();
				$we_JavaScript .= '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');'; // save/ rename a document
			}
			include(WE_INCLUDES_PATH . 'we_editors/we_editor_save.inc.php');
			break;
		case 'unpublish':
			doUnpublish($we_doc, $we_transaction);
			break;
		default:
			includeEditorDefault($we_doc, $we_transaction);
	}
}

// prevent persmissions overriding
$_SESSION['perms'] = $perms;
