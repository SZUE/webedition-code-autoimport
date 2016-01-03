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
abstract class we_editor_footer{

	static function fileLocked($we_doc){
//	user
		$_username = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($we_doc->isLockedByUser()));

		$_messageTbl = new we_html_table(array("class" => 'default footertable'), 1, 3);

		//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", 'style' => 'margin-right:5px;')));
		$_messageTbl->setCol(0, 1, array("class" => "defaultfont"), sprintf(g_l('alert', '[file_locked_footer]'), $_username));
		$_messageTbl->setColContent(0, 2, (we_base_request::_(we_base_request::BOOL, "SEEM_edit_include") ? '' : we_html_button::create_button(we_html_button::RELOAD, "javascript:top.weNavigationHistory.navigateReload();")));

		echo we_html_tools::getHtmlTop('', '', '', STYLESHEET, we_html_element::htmlBody(array('id' => 'footerBody'), $_messageTbl->getHtml()));
	}

	static function fileInWorkspace(){
		$_messageTbl = new we_html_table(array("class" => 'default footertable'), 1, 3);
//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", 'style' => 'margin-right:5px;')));
		$_messageTbl->setCol(0, 1, array("class" => "defaultfont"), g_l('alert', '[' . FILE_TABLE . '][not_im_ws]'));

		echo we_html_tools::getHtmlTop('', '', '', STYLESHEET, we_html_element::htmlBody(array('id' => 'footerBody'), $_messageTbl->getHtml()));
	}

	static function fileNoSave(){
		$_messageTbl = new we_html_table(array("class" => 'default footertable'), 1, 2);
//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", 'style' => 'margin-right:5px;')));
		$_messageTbl->setCol(0, 1, array("class" => "defaultfont"), g_l('alert', '[file_no_save_footer]'));

		echo we_html_tools::getHtmlTop('', '', '', STYLESHEET, we_html_element::htmlBody(array('id' => 'footerBody'), $_messageTbl->getHtml()));
	}

	static function fileIsRestricted($we_doc){
		$_messageTbl = new we_html_table(array("class" => 'default footertable'), 1, 2);
//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", 'style' => 'margin-right:5px;')));
		$_messageTbl->setCol(0, 1, array("class" => "defaultfont"), str_replace("<br/>", " ", sprintf(g_l('alert', '[no_perms]'), f('SELECT Username FROM ' . USER_TABLE . ' WHERE ID=' . intval($we_doc->CreatorID)))));

		echo we_html_tools::getHtmlTop('', '', '', STYLESHEET, we_html_element::htmlBody(array('id' => 'footerBody'), $_messageTbl->getHtml()));
	}

	static function workflow($we_doc){
		if(we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]) || permissionhandler::hasPerm("PUBLISH")){

			$_table = ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
					we_workflow_view::showFooterForNormalMode($we_doc, $GLOBALS['showPubl']) :
					($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
						we_workflow_view::showFooterForSEEMMode($we_doc, $GLOBALS['showPubl']) : ''));

			$_we_form = we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), $_table);

			echo we_html_element::htmlBody(array('id' => 'footerBody'), $_we_form);
		} else {

			$_table = new we_html_table(array("class" => 'default footertable'), 1, 2);
			$_table->setColContent(0, 0, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif", 'style' => 'margin-right:16px;')));
			$_table->setCol(0, 1, array("class" => "defaultfont"), g_l('modules_workflow', '[doc_in_wf_warning]'));

			echo we_html_element::htmlBody(array('id' => 'footerBody'), $_table->getHtml());
		}
		echo '</html>';
	}

	private static function addDelButton($table, $we_doc, &$_pos){
		$_ctrlElem = getControlElement('button', 'delete'); //	look tag we:controlElement for details
		if(!$_ctrlElem || !$_ctrlElem['hide']){
			$table->addCol(2);
			$table->setCol(0, $_pos++, array('style' => 'vertical-align:top'), we_html_button::create_button(we_html_button::TRASH, "javascript:if(confirm('" . g_l('alert', '[delete_single][confirm_delete]') . "')){we_cmd('delete_single_document','','" . $we_doc->Table . "','1');}"));
		}
	}

	/**
	 * @return void
	 * @desc Prints the footer for the normal mode
	 */
	static function normalMode($we_doc, $we_transaction, $haspermNew, $showPubl){
		$_normalTable = new we_html_table(array("class" => 'default footertable'), 1, 1);
		$_pos = 0;

		if($we_doc->ID){
			switch($we_doc->ContentType){
				case we_base_ContentTypes::TEMPLATE:
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("fat:make_new_document,fa-lg fa-file", "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','','" . $we_doc->ID . "');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc(false);"));
					break;
				case we_base_ContentTypes::OBJECT:
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("make_new_object", "javascript:top.we_cmd('new','" . OBJECT_FILES_TABLE . "','','objectFile','" . $we_doc->ID . "');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc(false);"));
					break;
			}
		}

		if(defined('WORKFLOW_TABLE') && $we_doc->IsTextContentDoc && $we_doc->ID){
			//	Workflow button
			$_ctrlElem = getControlElement('button', 'workflow'); //	look tag we:controlElement for details

			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_normalTable->addCol(2);
				$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("fat:in_workflow,fa-lg fa-gears", "javascript:put_in_workflow();"));
			}
		}

		if($showPubl && $we_doc->ID && $we_doc->Published){
			//	Park button
			$_ctrlElem = getControlElement('button', 'unpublish'); //	look tag we:controlElement for details

			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_normalTable->addCol(2);
				$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("fat:unpublish,fa-lg fa-moon-o", "javascript:we_cmd('unpublish', '" . $we_transaction . "');"));
			}
		}

		switch($we_doc->ContentType){
			case we_base_ContentTypes::WEDOCUMENT:
			case we_base_ContentTypes::OBJECT:
			case we_base_ContentTypes::OBJECT_FILE:
			case we_base_ContentTypes::FOLDER:
			case we_base_ContentTypes::CLASS_FOLDER:
			case we_base_ContentTypes::COLLECTION:
				break;
			default:
				$_normalTable->addCol(2);
				if(we_base_moduleInfo::isActive(we_base_moduleInfo::EDITOR)){
					$_normalTable->setColContent(0, $_pos++, (stripos($we_doc->ContentType, 'text/') !== false ?
							we_html_button::create_button("fat:startEditor,fa-lg fa-external-link", "javascript:editSource();") :
							we_html_button::create_button("fat:startEditor,fa-lg fa-external-link", "javascript:editFile();"))
					);
				}
		}

		//	Save Button
		$_ctrlElem = getControlElement('button', 'save'); //	look tag we:controlElement for details
		if(!$_ctrlElem || !$_ctrlElem['hide']){
			$_normalTable->addCol(2);
			$_normalTable->setColContent(0, $_pos++, we_html_button::create_button(we_html_button::SAVE, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);we_save_document();"));
		}

		switch($we_doc->Table){
			case FILE_TABLE:
				$hasPerm = ($we_doc->IsFolder && permissionhandler::hasPerm('DELETE_DOC_FOLDER')) ||
					(!$we_doc->IsFolder && permissionhandler::hasPerm('DELETE_DOCUMENT'));
				break;
			case TEMPLATES_TABLE:
				$hasPerm = ($we_doc->IsFolder && permissionhandler::hasPerm('DELETE_TEMP_FOLDER')) ||
					(!$we_doc->IsFolder && permissionhandler::hasPerm('DELETE_TEMPLATE'));
				break;
			case OBJECT_FILES_TABLE:
				$hasPerm = (permissionhandler::hasPerm('DELETE_OBJECTFILE'));
				break;
			case OBJECT_TABLE:
				$hasPerm = ($we_doc->IsFolder && permissionhandler::hasPerm('DELETE_OBJECT'));
				break;
			case VFILE_TABLE:
				$hasPerm = ($we_doc->IsFolder && permissionhandler::hasPerm('DELETE_COLLECTION_FOLDER')) ||
					(!$we_doc->IsFolder && permissionhandler::hasPerm('DELETE_COLLECTION'));
				break;
			default:
				$hasPerm = false;
		}


		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				if(defined('VERSIONING_TEXT_WETMPL') && defined('VERSIONS_CREATE_TMPL') && VERSIONS_CREATE_TMPL && VERSIONING_TEXT_WETMPL){
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("fat:saveversion,fa-lg fa-save", "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);we_save_document();"));
				}
				if($hasPerm){
					self::addDelButton($_normalTable, $we_doc, $_pos);
				}

				$_normalTable->addCol(2);
				$_normalTable->setColContent(0, $_pos++, we_html_forms::checkbox("autoRebuild", false, "autoRebuild", g_l('global', '[we_rebuild_at_save]'), false, "defaultfont", " WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorAutoRebuild( (this.checked) ? true : false );"));
				break;
			default:
				if($showPubl){
					$_ctrlElem = getControlElement('button', 'publish');
					if(!$_ctrlElem || !$_ctrlElem['hide']){
						$text = we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && we_schedpro::saveInScheduler($GLOBALS['we_doc']) ? 'fat:saveInScheduler,fa-lg fa-clock-o' : we_html_button::PUBLISH;
						$_normalTable->addCol(2);
						$_normalTable->setColAttributes(0, $_pos, array('id' => 'publish_' . $GLOBALS['we_doc']->ID));
						$_normalTable->setColContent(0, $_pos++, we_html_button::create_button($text, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);we_save_document();"));
					}
				}
				if($hasPerm){
					self::addDelButton($_normalTable, $we_doc, $_pos);
				}
		}

		if($we_doc->IsTextContentDoc && $haspermNew){
			$_ctrlElem = getControlElement('checkbox', 'makeSameDoc');
			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_normalTable->addCol(2);
				$_normalTable->setCol(0, $_pos++, ( ($_ctrlElem && $_ctrlElem['hide'] ) ? ( array('style' => 'display:none') ) : array('style' => 'display:block')), we_html_forms::checkbox("makeSameDoc", ( $_ctrlElem ? $_ctrlElem['checked'] : false), "makeSameDoc", g_l('global', '[we_make_same][' . $we_doc->ContentType . ']'), false, "defaultfont", " WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeSameDoc( (this.checked) ? true : false );", ( $_ctrlElem ? $_ctrlElem['readonly'] : false)));
			}
		}

		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				if(permissionhandler::hasPerm("NEW_WEBEDITIONSITE") || permissionhandler::hasPerm("ADMINISTRATOR")){
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('global', '[we_new_doc_after_save]'), false, "defaultfont", "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc( (this.checked) ? true : false );"));
				}
				break;
			case we_base_ContentTypes::OBJECT:
				if(permissionhandler::hasPerm("NEW_OBJECTFILE") || permissionhandler::hasPerm("ADMINISTRATOR")){
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('modules_object', '[we_new_doc_after_save]'), false, "defaultfont", "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc( (this.checked) ? true : false );"));
				}
				break;
		}

		echo $_normalTable->getHtml();
	}

	/**
	 * @return void
	 * @desc prints the footer for the See-Mode
	 */
	static function SEEMode($we_doc, $we_transaction, $haspermNew, $showPubl){
		$_seeModeTable = new we_html_table(array("class" => 'default footertable'), 1, 1);
		$_pos = 0;

		//##############################	First buttons which are always needed
		//	Always button preview
		if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW){ // first button is always - preview, when exists
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top'), we_html_button::create_button("fat:preview,fa-lg fa-eye", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS["we_transaction"] . "');"));
		}

		// shop variants
		if(defined('SHOP_TABLE')){
			if($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT && in_array(we_base_constants::WE_EDITPAGE_VARIANTS, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->canHaveVariants(true) && $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_VARIANTS){ // first button is always - preview, when exists
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top'), we_html_button::create_button("shopVariants", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_VARIANTS . ",'" . $GLOBALS["we_transaction"] . "');"));
			}
		}

		//	image-documents have no preview but thumbnailview instead ...
		if($GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_THUMBNAILS && in_array(we_base_constants::WE_EDITPAGE_THUMBNAILS, $GLOBALS['we_doc']->EditPageNrs)){
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top'), we_html_button::create_button("thumbnails", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_THUMBNAILS . ",'" . $GLOBALS["we_transaction"] . "');"));
		}

		//	Button edit !!!
		if($GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_CONTENT && in_array(we_base_constants::WE_EDITPAGE_CONTENT, $GLOBALS['we_doc']->EditPageNrs)){ // then button "edit"
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top'), we_html_button::create_button(we_html_button::EDIT, "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_CONTENT . ", '" . $GLOBALS["we_transaction"] . "');"));
		}
		//	Button properties
		if(in_array(we_base_constants::WE_EDITPAGE_PROPERTIES, $GLOBALS['we_doc']->EditPageNrs) && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_SCHEDULER)){
			if(permissionhandler::isUserAllowedForAction("switch_edit_page", "we_base_constants::WE_EDITPAGE_PROPERTIES")){
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top'), we_html_button::create_button("properties", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_PROPERTIES . ", '" . $GLOBALS["we_transaction"] . "');"));
			}
		}

		// Button workspace
		if(in_array(we_base_constants::WE_EDITPAGE_WORKSPACE, $GLOBALS['we_doc']->EditPageNrs) && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES)){
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), we_html_button::create_button("workspace_button", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_WORKSPACE . ", '" . $GLOBALS["we_transaction"] . "');"));
		}


		//	Button scheduler
		if(in_array(we_base_constants::WE_EDITPAGE_SCHEDULER, $GLOBALS['we_doc']->EditPageNrs) && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES) &&
			we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && permissionhandler::hasPerm("CAN_SEE_SCHEDULER")){
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), we_html_button::create_button("fat:schedule_button,fa-lg fa-clock-o", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_SCHEDULER . ", '" . $GLOBALS["we_transaction"] . "');"));
		}

		//	Button put in workflow
		if(/* $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_PROPERTIES && */ $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_SCHEDULER && // then button "workflow"
			defined('WORKFLOW_TABLE') && $we_doc->IsTextContentDoc && $we_doc->ID){

			$_ctrlElem = getControlElement('button', 'workflow'); //	look tag we:controlElement for details

			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), we_html_button::create_button("fat:in_workflow,fa-lg fa-gears", "javascript:put_in_workflow();"));
			}
		}

		//###########################	Special buttons for special EDITPAGE
		//
		//	1. ONLY in PROPERTY page we need the button unpublish
		//
		if($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES && $showPubl && $we_doc->ID && $we_doc->Published){

			//	button unpublish
			$_ctrlElem = getControlElement('button', 'unpublish'); //	look tag we:controlElement for details
			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), we_html_button::create_button("fat:unpublish,fa-lg fa-moon-o", "javascript:we_cmd('unpublish', '" . $we_transaction . "');"));
			}
		}

		//
		//	2. we always need the buttons -> save and publish
		//
		$_ctrlElem = getControlElement('button', 'save'); //	look tag we:controlElement for details
		if(!$_ctrlElem || !$_ctrlElem['hide']){
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), we_html_button::create_button('fat:save,fa-lg fa-save', "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);we_save_document();"));
		}


		//
		// 3. public when save and make same doc new.
		//
		if($showPubl){
			$_ctrlElem = getControlElement('button', 'publish'); //	look tag we:controlElement for details

			if(!($_ctrlElem && $_ctrlElem['hide'])){

				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), we_html_button::create_button(we_html_button::PUBLISH, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);we_save_document();"));
			}
		}

		if($we_doc->IsTextContentDoc && $haspermNew && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_THUMBNAILS)){

			//	makesamedoc only when not in edit_include-window
			$_ctrlElem = getControlElement('checkbox', 'makeSameDoc');

			$showPubl_makeSamNew = ($_ctrlElem && $_ctrlElem['hide'] ? '<div style="display: hidden;">' : '') .
				we_html_forms::checkbox("makeSameDoc", ( $_ctrlElem ? $_ctrlElem['checked'] : false), "makeSameDoc", g_l('global', '[we_make_same][' . $we_doc->ContentType . ']'), false, "defaultfont", " WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeSameDoc( (this.checked) ? true : false );", ( $_ctrlElem ? $_ctrlElem['readonly'] : false)) .
				($_ctrlElem && $_ctrlElem['hide'] ? '</div>' : '');

			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array('style' => 'vertical-align:top;'), $showPubl_makeSamNew);
		}

		//
		//	4. show delete button to delete this document, not in edit_include-window
		//
		$canDelete = ( (!we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')) && (($we_doc instanceof we_objectFile) ? permissionhandler::hasPerm('DELETE_OBJECTFILE') : permissionhandler::hasPerm('DELETE_DOCUMENT')));
		if($canDelete){
			self::addDelButton($_seeModeTable, $we_doc, $_pos);
		}
		echo $_seeModeTable->getHtml();
	}

}
