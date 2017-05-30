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

	private static function fileLocked(we_contents_root $we_doc){
//	user
		$username = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($we_doc->isLockedByUser()));

		$messageTbl = new we_html_table(['class' => 'default footertable'], 1, 3);

		//	spaceholder
		$messageTbl->setColContent(0, 0, '<span class="fa-stack fa-lg" style="color:#F2F200;margin-right:5px;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>');
		$messageTbl->setCol(0, 1, ['class' => 'defaultfont'], sprintf(g_l('alert', '[file_locked_footer]'), $username));
		$messageTbl->setColContent(0, 2, (we_base_request::_(we_base_request::BOOL, "SEEM_edit_include") ? '' : we_html_button::create_button(we_html_button::RELOAD, "javascript:WE().layout.weNavigationHistory.navigateReload();") .
				we_html_button::create_button(we_html_button::UNLOCK, "javascript:top.we_cmd('requestUnlock','" . $username . "'," . $we_doc->ID . ",'" . stripTblPrefix($we_doc->Table) . "');")
		));

		echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(['id' => 'footerBody'], $messageTbl->getHtml()));
	}

	private static function fileInWorkspace(){
		$messageTbl = new we_html_table(['class' => 'default footertable'], 1, 3);
//	spaceholder
		$messageTbl->setColContent(0, 0, '<span class="fa-stack fa-lg" style="color:#F2F200;margin-right:5px;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>');
		$messageTbl->setCol(0, 1, ['class' => 'defaultfont'], g_l('alert', '[' . FILE_TABLE . '][not_im_ws]'));

		echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(['id' => 'footerBody'], $messageTbl->getHtml()));
	}

	private static function fileNoSave(){
		$messageTbl = new we_html_table(['class' => 'default footertable'], 1, 2);
//	spaceholder
		$messageTbl->setColContent(0, 0, '<span class="fa-stack fa-lg" style="color:#F2F200;margin-right:5px;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>');
		$messageTbl->setCol(0, 1, ['class' => 'defaultfont'], g_l('alert', '[file_no_save_footer]'));

		echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(['id' => 'footerBody'], $messageTbl->getHtml()));
	}

	private static function fileIsRestricted(we_contents_root $we_doc){
		$messageTbl = new we_html_table(['class' => 'default footertable'], 1, 2);
//	spaceholder
		$messageTbl->setColContent(0, 0, '<span class="fa-stack fa-lg" style="color:#F2F200;margin-right:5px;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>');
		$messageTbl->setCol(0, 1, ['class' => 'defaultfont'], str_replace("<br/>", " ", sprintf(g_l('alert', '[no_perms]'), f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($we_doc->CreatorID)))));

		echo we_html_tools::getHtmlTop('', '', '', '', we_html_element::htmlBody(['id' => 'footerBody'], $messageTbl->getHtml()));
	}

	private static function workflow(we_contents_root $we_doc){
		if(we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']["ID"]) || we_base_permission::hasPerm("PUBLISH")){

			$table = ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
				we_workflow_view::showFooterForNormalMode($we_doc, $GLOBALS['showPubl']) :
				($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
				we_workflow_view::showFooterForSEEMMode($we_doc, $GLOBALS['showPubl']) : ''));

			$we_form = we_html_element::htmlForm(['name' => 'we_form', "method" => "post"], $table);

			return we_html_element::htmlBody(['id' => 'footerBody'], $we_form);
		}

		$table = new we_html_table(['class' => 'default footertable'], 1, 2);
		$table->setColContent(0, 0, '<span class="fa-stack fa-lg" style="color:#F2F200;margin-right:16px;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>');
		$table->setCol(0, 1, ['class' => 'defaultfont'], g_l('modules_workflow', '[doc_in_wf_warning]'));

		return we_html_element::htmlBody(['id' => 'footerBody'], $table->getHtml());
	}

	private static function addDelButton($table, we_contents_root $we_doc, &$pos){
		$ctrlElem = self::getControlElement($we_doc, 'button', 'delete'); //	look tag we:controlElement for details
		if(!$ctrlElem || !$ctrlElem['hide']){
			$table->addCol(2);
			$table->setColContent(0, $pos++, we_html_button::create_button(we_html_button::TRASH, "javascript:WE().util.showConfirm(window, '', WE().consts.g_l.main.delete_single_confirm_delete+doc.Path,['delete_single_document','',doc.Table,1]);"));
		}
	}

	private static function addCopyButton($table, we_contents_root $we_doc, &$pos){
		$ctrlElem = self::getControlElement($we_doc, 'button', 'copy'); //	look tag we:controlElement for details
		if((!$ctrlElem || !$ctrlElem['hide']) && $we_doc->ID){
			$table->addCol(2);

			$table->setColContent(0, $pos++, we_html_button::create_button('fa:btn_function_copy,fa-lg fa-copy', "javascript:we_cmd('cloneDocument');"));
		}
	}

	/**
	 * @return void
	 * @desc Prints the footer for the normal mode
	 */
	private static function normalMode(we_contents_root $we_doc, $we_transaction, $haspermNew, $showPubl){
		$normalTable = new we_html_table(['class' => 'default footertable'], 1, 1);
		$pos = 0;

		if($we_doc->ID){
			switch($we_doc->ContentType){
				case we_base_ContentTypes::TEMPLATE:
					$normalTable->addCol(2);
					$normalTable->setColContent(0, $pos++, we_html_button::create_button('fat:make_new_document,fa-lg fa-file', "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','','" . $we_doc->ID . "');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc(false);"));
					break;
				case we_base_ContentTypes::OBJECT:
					$normalTable->addCol(2);
					$normalTable->setColContent(0, $pos++, we_html_button::create_button('make_new_object', "javascript:top.we_cmd('new','" . OBJECT_FILES_TABLE . "','','objectFile','" . $we_doc->ID . "');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc(false);"));
					break;
			}
		}

		if(defined('WORKFLOW_TABLE') && $we_doc->IsTextContentDoc && $we_doc->ID){
			//	Workflow button
			$ctrlElem = self::getControlElement($we_doc, 'button', 'workflow'); //	look tag we:controlElement for details

			if(!$ctrlElem || !$ctrlElem['hide']){
				$normalTable->addCol(2);
				$normalTable->setColContent(0, $pos++, we_html_button::create_button('fat:in_workflow,fa-lg fa-gears', "javascript:put_in_workflow('" . stripTblPrefix($we_doc->Table) . "');"));
			}
		}

		if($showPubl && $we_doc->ID && $we_doc->Published){
			//	Park button
			$ctrlElem = self::getControlElement($we_doc, 'button', 'unpublish'); //	look tag we:controlElement for details

			if(!$ctrlElem || !$ctrlElem['hide']){
				$normalTable->addCol(2);
				$normalTable->setColContent(0, $pos++, we_html_button::create_button('fat:unpublish,fa-lg fa-moon-o', "javascript:we_cmd('unpublish', '" . $we_transaction . "');"));
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
				$normalTable->addCol(2);
				if(we_base_moduleInfo::isActive(we_base_moduleInfo::EDITOR)){
					$normalTable->setColContent(0, $pos++, (stripos($we_doc->ContentType, 'text/') !== false ?
							we_html_button::create_button('fat:startEditor,fa-lg fa-external-link', "javascript:editSource();") :
							we_html_button::create_button('fat:startEditor,fa-lg fa-external-link', "javascript:editFile();"))
					);
				}
		}

		//	Save Button
		$ctrlElem = self::getControlElement($we_doc, 'button', 'save'); //	look tag we:controlElement for details
		if(!$ctrlElem || !$ctrlElem['hide']){
			$normalTable->addCol(2);
			$normalTable->setColContent(0, $pos++, we_html_button::create_button(we_html_button::SAVE, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);we_save_document();"));
		}

		switch($we_doc->Table){
			case FILE_TABLE:
				$hasPerm = ($we_doc->IsFolder && we_base_permission::hasPerm('DELETE_DOC_FOLDER')) ||
					(!$we_doc->IsFolder && we_base_permission::hasPerm('DELETE_DOCUMENT'));
				break;
			case TEMPLATES_TABLE:
				$hasPerm = ($we_doc->IsFolder && we_base_permission::hasPerm('DELETE_TEMP_FOLDER')) ||
					(!$we_doc->IsFolder && we_base_permission::hasPerm('DELETE_TEMPLATE'));
				break;
			case OBJECT_FILES_TABLE:
				$hasPerm = (we_base_permission::hasPerm('DELETE_OBJECTFILE'));
				break;
			case OBJECT_TABLE:
				$hasPerm = ($we_doc->IsFolder && we_base_permission::hasPerm('DELETE_OBJECT'));
				break;
			case VFILE_TABLE:
				$hasPerm = ($we_doc->IsFolder && we_base_permission::hasPerm('DELETE_COLLECTION_FOLDER')) ||
					(!$we_doc->IsFolder && we_base_permission::hasPerm('DELETE_COLLECTION'));
				break;
			default:
				$hasPerm = false;
		}


		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				if(defined('VERSIONING_TEXT_WETMPL') && defined('VERSIONS_CREATE_TMPL') && VERSIONS_CREATE_TMPL && VERSIONING_TEXT_WETMPL){
					$normalTable->addCol(2);
					$normalTable->setColContent(0, $pos++, we_html_button::create_button('fat:saveversion,fa-lg fa-save', "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);we_save_document();"));
				}
				if($hasPerm){
					self::addDelButton($normalTable, $we_doc, $pos);
				}

				$normalTable->addCol(2);
				$normalTable->setColContent(0, $pos++, we_html_forms::checkbox("autoRebuild", false, "autoRebuild", g_l('global', '[we_rebuild_at_save]'), false, "defaultfont", " WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorAutoRebuild( (this.checked) ? true : false );"));
				break;
			default:
				if($showPubl){
					$ctrlElem = self::getControlElement($we_doc, 'button', 'publish');
					if(!$ctrlElem || !$ctrlElem['hide']){
						$text = we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && we_schedpro::saveInScheduler($we_doc) ? 'fat:saveInScheduler,fa-lg fa-clock-o' : we_html_button::PUBLISH;
						$normalTable->addCol(2);
						$normalTable->setColAttributes(0, $pos, ['id' => 'publish_' . $we_doc->ID]);
						$normalTable->setColContent(0, $pos++, we_html_button::create_button($text, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);we_save_document();"));
					}
				}
				if($we_doc->ContentType !== we_base_ContentTypes::FOLDER && $we_doc->ContentType !== we_base_ContentTypes::OBJECT){
					self::addCopyButton($normalTable, $we_doc, $pos);
				}
				if($hasPerm && $we_doc->ID){
					self::addDelButton($normalTable, $we_doc, $pos);
				}
		}

		if(($we_doc->IsTextContentDoc/* || $we_doc->IsFolder */) && $haspermNew){
			$ctrlElem = self::getControlElement($we_doc, 'checkbox', 'makeSameDoc');
			if(!$ctrlElem || !$ctrlElem['hide']){
				$normalTable->addCol(2);
				$normalTable->setCol(0, $pos++, ( ($ctrlElem && $ctrlElem['hide'] ) ? ( ['style' => 'display:none'] ) : ['style' => 'display:block']), we_html_forms::checkbox("makeSameDoc", ( $ctrlElem ? $ctrlElem['checked'] : false), "makeSameDoc", g_l('global', '[we_make_same][' . $we_doc->ContentType . ']'), false, "defaultfont", " WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeSameDoc( (this.checked) ? true : false );", ( $ctrlElem ? $ctrlElem['readonly'] : false)));
			}
		}

		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				if(we_base_permission::hasPerm("NEW_WEBEDITIONSITE")){
					$normalTable->addCol(2);
					$normalTable->setColContent(0, $pos++, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('global', '[we_new_doc_after_save]'), false, "defaultfont", "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc( (this.checked) ? true : false );"));
				}
				break;
			case we_base_ContentTypes::OBJECT:
				if(we_base_permission::hasPerm("NEW_OBJECTFILE")){
					$normalTable->addCol(2);
					$normalTable->setColContent(0, $pos++, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('modules_object', '[we_new_doc_after_save]'), false, "defaultfont", "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeNewDoc( (this.checked) ? true : false );"));
				}
				break;
		}

		return $normalTable->getHtml();
	}

	/**
	 * @return void
	 * @desc prints the footer for the See-Mode
	 */
	private static function SEEMode(we_contents_root $we_doc, $we_transaction, $haspermNew, $showPubl){
		$seeModeTable = new we_html_table(['class' => 'default footertable'], 1, 1);
		$pos = 0;

		//##############################	First buttons which are always needed
		//	Always button preview
		if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs) && $we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW){ // first button is always - preview, when exists
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, [], we_html_button::create_button('fat:preview,fa-lg fa-eye', "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');"));
		}

		// variants
		if($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT && in_array(we_base_constants::WE_EDITPAGE_VARIANTS, $we_doc->EditPageNrs) && $we_doc->canHaveVariants(true) && $we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_VARIANTS){ // first button is always - preview, when exists
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, [], we_html_button::create_button('shopVariants', "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_VARIANTS . ",'" . $we_transaction . "');"));
		}

		//	image-documents have no preview but thumbnailview instead ...
		if($we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_THUMBNAILS && in_array(we_base_constants::WE_EDITPAGE_THUMBNAILS, $we_doc->EditPageNrs)){
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, [], we_html_button::create_button('thumbnails', "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_THUMBNAILS . ",'" . $we_transaction . "');"));
		}

		//	Button edit !!!
		if($we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_CONTENT && in_array(we_base_constants::WE_EDITPAGE_CONTENT, $we_doc->EditPageNrs)){ // then button "edit"
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, [], we_html_button::create_button(we_html_button::EDIT, "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_CONTENT . ", '" . $we_transaction . "');"));
		}
		//	Button properties
		if(in_array(we_base_constants::WE_EDITPAGE_PROPERTIES, $we_doc->EditPageNrs) && ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_SCHEDULER)){
			if(we_base_permission::isUserAllowedForAction("switch_edit_page", we_base_constants::WE_EDITPAGE_PROPERTIES)){
				$seeModeTable->addCol(2);
				$seeModeTable->setCol(0, $pos++, [], we_html_button::create_button('properties', "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_PROPERTIES . ", '" . $we_transaction . "');"));
			}
		}

		// Button workspace
		if(in_array(we_base_constants::WE_EDITPAGE_WORKSPACE, $we_doc->EditPageNrs) && ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES)){
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], we_html_button::create_button('workspace_button', "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_WORKSPACE . ", '" . $we_transaction . "');"));
		}


		//	Button scheduler
		if(in_array(we_base_constants::WE_EDITPAGE_SCHEDULER, $we_doc->EditPageNrs) && ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES) &&
			we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && we_base_permission::hasPerm("CAN_SEE_SCHEDULER")){
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], we_html_button::create_button('fat:schedule_button,fa-lg fa-clock-o', "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_SCHEDULER . ", '" . $we_transaction . "');"));
		}

		//	Button put in workflow
		if(/* $we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_PROPERTIES && */ $we_doc->EditPageNr != we_base_constants::WE_EDITPAGE_SCHEDULER && // then button "workflow"
			defined('WORKFLOW_TABLE') && $we_doc->IsTextContentDoc && $we_doc->ID){

			$ctrlElem = self::getControlElement($we_doc, 'button', 'workflow'); //	look tag we:controlElement for details

			if(!$ctrlElem || !$ctrlElem['hide']){
				$seeModeTable->addCol(2);
				$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], we_html_button::create_button('fat:in_workflow,fa-lg fa-gears', "javascript:put_in_workflow('" . stripTblPrefix($we_doc->Table) . "');"));
			}
		}

		//###########################	Special buttons for special EDITPAGE
		//
		//	1. ONLY in PROPERTY page we need the button unpublish
		//
		if($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES && $showPubl && $we_doc->ID && $we_doc->Published){

			//	button unpublish
			$ctrlElem = self::getControlElement($we_doc, 'button', 'unpublish'); //	look tag we:controlElement for details
			if(!$ctrlElem || !$ctrlElem['hide']){
				$seeModeTable->addCol(2);
				$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], we_html_button::create_button('fat:unpublish,fa-lg fa-moon-o', "javascript:we_cmd('unpublish', '" . $we_transaction . "');"));
			}
		}

		//
		//	2. we always need the buttons -> save and publish
		//
		$ctrlElem = self::getControlElement($we_doc, 'button', 'save'); //	look tag we:controlElement for details
		if(!$ctrlElem || !$ctrlElem['hide']){
			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], we_html_button::create_button('fat:save,fa-lg fa-save', "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);we_save_document();"));
		}


		//
		// 3. public when save and make same doc new.
		//
		if($showPubl){
			$ctrlElem = self::getControlElement($we_doc, 'button', 'publish'); //	look tag we:controlElement for details

			if(!($ctrlElem && $ctrlElem['hide'])){

				$seeModeTable->addCol(2);
				$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], we_html_button::create_button(we_html_button::PUBLISH, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);we_save_document();"));
			}
		}

		if(($we_doc->IsTextContentDoc/* || $we_doc->IsFolder */) && $haspermNew && ($we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_THUMBNAILS)){

			//	makesamedoc only when not in edit_include-window
			$ctrlElem = self::getControlElement($we_doc, 'checkbox', 'makeSameDoc');

			$showPubl_makeSamNew = ($ctrlElem && $ctrlElem['hide'] ? '<div style="display: hidden;">' : '') .
				we_html_forms::checkbox("makeSameDoc", ( $ctrlElem ? $ctrlElem['checked'] : false), "makeSameDoc", g_l('global', '[we_make_same][' . $we_doc->ContentType . ']'), false, "defaultfont", " WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorMakeSameDoc( (this.checked) ? true : false );", ( $ctrlElem ? $ctrlElem['readonly'] : false)) .
				($ctrlElem && $ctrlElem['hide'] ? '</div>' : '');

			$seeModeTable->addCol(2);
			$seeModeTable->setCol(0, $pos++, ['style' => 'vertical-align:top;'], $showPubl_makeSamNew);
		}

		//
		//	4. show delete button to delete this document, not in edit_include-window
		//
		$canDelete = ( (!we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')) && (($we_doc instanceof we_objectFile) ? we_base_permission::hasPerm('DELETE_OBJECTFILE') : we_base_permission::hasPerm('DELETE_DOCUMENT')));
		if($canDelete && $we_doc->ID){
			self::addDelButton($seeModeTable, $we_doc, $pos);
		}
		return $seeModeTable->getHtml();
	}

	private static function hasNewPerm(we_contents_root $we_doc){
		//	Check permissions for buttons
		switch($we_doc->ContentType){
			case we_base_ContentTypes::HTML:
				return we_base_permission::hasPerm("NEW_HTML");

			case we_base_ContentTypes::WEDOCUMENT:
				return we_base_permission::hasPerm("NEW_WEBEDITIONSITE");
			case we_base_ContentTypes::OBJECT_FILE:
				return we_base_permission::hasPerm("NEW_OBJECTFILE");
			case we_base_ContentTypes::FOLDER:
				switch($we_doc->Table){
					case FILE_TABLE:
						return we_base_permission::hasPerm('NEW_DOC_FOLDER');
					case TEMPLATES_TABLE:
						return we_base_permission::hasPerm('NEW_TEMP_FOLDER');
					case defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
						return we_base_permission::hasPerm('NEW_OBJECTFILE_FOLDER');
				}
		}
		return false;
	}

	private static function getControlElement(we_contents_root $we_doc, $type, $name){
		if(isset($we_doc->controlElement) && is_array($we_doc->controlElement)){

			return (isset($we_doc->controlElement[$type][$name]) ?
				$we_doc->controlElement[$type][$name] :
				false);
		}
		return false;
	}

	private static function inWorkflow(we_contents_root $doc){
		if(!defined('WORKFLOW_TABLE') || !$doc->IsTextContentDoc){
			return false;
		}
		return ($doc->ID ? we_workflow_utility::inWorkflow($doc->ID, $doc->Table) : false);
	}

	private static function checkUserAccess(we_contents_root $we_doc){
		switch($we_doc->userHasAccess()){
			case we_contents_root::USER_HASACCESS : //	all is allowed, creator or owner
				break;

			case we_contents_root::FILE_NOT_IN_USER_WORKSPACE : //	file is not in workspace of user
				self::fileInWorkspace();
				exit();

			case we_contents_root::USER_NO_PERM : //	access is restricted and user has no permission
				self::fileIsRestricted($we_doc);
				exit;

			case we_contents_root::FILE_LOCKED : //	file is locked by another user
				self::fileLocked($we_doc);
				exit;

			case we_contents_root::USER_NO_SAVE : //	user has not the right to save the file.
				self::fileNoSave();
				exit;
		}
	}

	public static function show(){
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 1);

// init document
		$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
		$we_doc = we_document::initDoc($we_dt);
		self::checkUserAccess($we_doc);

//	preparations of needed vars
		$showPubl = we_base_permission::hasPerm("PUBLISH") && $we_doc->userCanSave() && $we_doc->IsTextContentDoc;
		$reloadPage = (bool) (($showPubl || $we_doc->ContentType == we_base_ContentTypes::TEMPLATE) && (!$we_doc->ID));
		$haspermNew = self::hasNewPerm($we_doc);

		$showGlossaryCheck = 0; /* (!empty($_SESSION['prefs']['force_glossary_check']) &&
		  ( $we_doc->ContentType == we_base_ContentTypes::WEDOCUMENT || $we_doc->ContentType === we_base_ContentTypes::OBJECT_FILE ) ? 1 : 0);
		 */
//	added for we:controlElement type="button" name="save" hide="true"
		$ctrlElem = self::getControlElement($we_doc, 'button', 'save');

		$canWeSave = $we_doc->userCanSave();

		if($canWeSave &&
			(($ctrlElem && $ctrlElem['hide']) ||
			(defined('WORKFLOW_TABLE') && self::inWorkflow($we_doc) && (!we_workflow_utility::canUserEditDoc($we_doc->ID, $we_doc->Table, $_SESSION['user']["ID"])))
			)){
			$canWeSave = false;
		}

// publish for templates to save in version
		$pass_publish = $canWeSave && ($showPubl || ($we_doc->ContentType == we_base_ContentTypes::TEMPLATE && defined('VERSIONING_TEXT_WETMPL') && defined('VERSIONS_CREATE_TMPL') && VERSIONS_CREATE_TMPL && VERSIONING_TEXT_WETMPL)) ? [
			['publishWhenSave']] : [];

		$doc = [
			'we_transaction' => $we_transaction,
			'ID' => intval($we_doc->ID),
			'Path' => $we_doc->Path,
			'Text' => $we_doc->Text,
			'Table' => $we_doc->Table,
			'contentType' => $we_doc->ContentType,
			'editFilename' => preg_replace('|/' . $we_doc->Filename . '.*$|', $we_doc->Filename . (isset($we_doc->Extension) ? $we_doc->Extension : ''), $we_doc->Path),
			'makeSameDocCheck' => intval(($we_doc->IsTextContentDoc/* || $we_doc->IsFolder */) && $haspermNew && (!self::inWorkflow($we_doc))),
			'isTemplate' => $we_doc->Table == TEMPLATES_TABLE,
			'isFolder' => $we_doc->ContentType == we_base_ContentTypes::FOLDER,
			'isBinary' => $we_doc->isBinary(),
			'classname' => ($we_doc->Published == 0 ? 'notpublished' : (!in_array($we_doc->Table, [TEMPLATES_TABLE, VFILE_TABLE]) && $we_doc->ModDate > $we_doc->Published ? 'changed' : 'published')),
			'_showGlossaryCheck' => $showGlossaryCheck,
			'weCanSave' => $canWeSave,
			'ctrlElem' => 0,
			'reloadOnSave' => $reloadPage,
			'pass_publish' => $pass_publish,
		];

		if(($we_doc->IsTextContentDoc /* || $we_doc->IsFolder */) && $haspermNew && //	$js_permnew
			($_SESSION['weS']['we_mode'] != we_base_constants::MODE_SEE || $we_doc->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT)){ // not in SeeMode or in editmode
			$ctrlElem = self::getControlElement($we_doc, 'checkbox', 'makeSameDoc');
			$doc['ctrlElem'] = ($ctrlElem ? ($ctrlElem["checked"] ? 1 : 2) : 3);
		}

//	Document is in workflow
		if(self::inWorkflow($we_doc)){
			$body = self::workflow($we_doc);
		} else {
			$_SESSION['weS']['seemForOpenDelSelector'] = [
				'ID' => $we_doc->ID,
				'Table' => $we_doc->Table
			];

			if($we_doc->userCanSave()){
				switch($_SESSION['weS']['we_mode']){
					default:
					case we_base_constants::MODE_NORMAL: // open footer for NormalMode
						$content = self::normalMode($we_doc, $we_transaction, $haspermNew, $showPubl);
						break;
					case we_base_constants::MODE_SEE: // open footer for SeeMode
						$content = self::SEEMode($we_doc, $we_transaction, $haspermNew, $showPubl);
						break;
				}
			} else if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){

				$noPermTable = new we_html_table(['class' => 'default footertable'], 1, 2);
				$noPermTable->setColContent(0, 0, '<span class="fa-stack fa-lg" style="color:#F2F200;margin-right:10px;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>');
				$noPermTable->setColContent(0, 1, g_l('SEEM', '[no_permission_to_edit_document]'));
				$content = $noPermTable->getHtml();
			} else {
				$content = '';
			}

			$body = we_html_element::htmlBody(array_merge([
					'id' => "footerBody",
					'onload' => "we_footerLoaded();",
						], $we_doc->getEditorBodyAttributes(we_contents_root::EDITOR_FOOTER))
					, we_html_element::htmlForm([
						'name' => "we_form",
						'action' => '',
						'onsubmit' => (!empty($we_doc->IsClassFolder) ? 'sub();return false;' : '')
						], we_html_element::htmlHidden('sel', $we_doc->ID) .
						$content
			));
		}
		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'we_editor_footer.js', '', ['id' => 'loadVarEditor_footer', 'data-doc' => setDynamicVar($doc)]), $body);
	}

}
