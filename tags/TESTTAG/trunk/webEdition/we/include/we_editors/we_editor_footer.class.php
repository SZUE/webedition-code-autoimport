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

		$_messageTbl = new we_html_table(array("border" => 0,
			"cellpadding" => 0,
			"cellspacing" => 0), 2, 6);

		$refreshButton = (we_base_request::_(we_base_request::BOOL, "SEEM_edit_include") ? '' :
				we_html_button::create_button("refresh", "javascript:top.weNavigationHistory.navigateReload();"));

//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_tools::getPixel(20, 7));
		$_messageTbl->setColContent(1, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
		$_messageTbl->setColContent(1, 2, we_html_tools::getPixel(5, 2));
		$_messageTbl->setCol(1, 3, array("class" => "defaultfont"), sprintf(g_l('alert', '[file_locked_footer]'), $_username));
		$_messageTbl->setColContent(1, 4, we_html_tools::getPixel(5, 2));
		$_messageTbl->setColContent(1, 5, $refreshButton);


		$_head = we_html_element::htmlHead(we_html_element::jsElement('top.toggleBusy(0);') . STYLESHEET);
		$_body = we_html_element::htmlBody(array("background" => IMAGE_DIR . "edit/editfooterback.gif",
				"bgcolor" => "white"), $_messageTbl->getHtml());


		echo we_html_element::htmlDocType() . we_html_element::htmlHtml($_head . $_body);
	}

	static function fileInWorkspace(){
		$_messageTbl = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0), 2, 4);
//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_tools::getPixel(20, 7));
		$_messageTbl->setColContent(1, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
		$_messageTbl->setColContent(1, 2, we_html_tools::getPixel(5, 2));
		$_messageTbl->setCol(1, 3, array("class" => "defaultfont"), g_l('alert', '[' . FILE_TABLE . '][not_im_ws]'));


		$_head = we_html_element::htmlHead(we_html_element::jsElement('top.toggleBusy(0);'));
		$_body = we_html_element::htmlBody(array('background' => IMAGE_DIR . 'edit/editfooterback.gif',
				'bgcolor' => 'white'), $_messageTbl->getHtml());


		echo we_html_element::htmlDocType() . we_html_element::htmlHtml($_head . STYLESHEET . $_body);
	}

	static function fileNoSave(){
		$_messageTbl = new we_html_table(array("border" => 0,
			"cellpadding" => 0,
			"cellspacing" => 0), 2, 4);
//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_tools::getPixel(20, 7));
		$_messageTbl->setColContent(1, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
		$_messageTbl->setColContent(1, 2, we_html_tools::getPixel(5, 2));
		$_messageTbl->setCol(1, 3, array("class" => "defaultfont"), g_l('alert', '[file_no_save_footer]'));


		$_head = we_html_element::htmlHead(we_html_element::jsElement('top.toggleBusy(0);') . STYLESHEET);
		$_body = we_html_element::htmlBody(array("background" => IMAGE_DIR . "edit/editfooterback.gif",
				"bgcolor" => "white"), $_messageTbl->getHtml());

		echo we_html_element::htmlDocType() . we_html_element::htmlHtml($_head . $_body);
	}

	static function fileIsRestricted($we_doc){
		$_messageTbl = new we_html_table(array("border" => 0,
			"cellpadding" => 0,
			"cellspacing" => 0), 2, 4);
//	spaceholder
		$_messageTbl->setColContent(0, 0, we_html_tools::getPixel(20, 7));
		$_messageTbl->setColContent(1, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
		$_messageTbl->setColContent(1, 2, we_html_tools::getPixel(5, 2));
		$_messageTbl->setCol(1, 3, array("class" => "defaultfont"), str_replace("<br/>", " ", sprintf(g_l('alert', '[no_perms]'), f("SELECT Username FROM " . USER_TABLE . " WHERE ID='" . $we_doc->CreatorID . "'", "Username", $GLOBALS['DB_WE']))));


		$_head = we_html_element::htmlHead(we_html_element::jsElement('top.toggleBusy(0);') . STYLESHEET);
		$_body = we_html_element::htmlBody(array("background" => IMAGE_DIR . "edit/editfooterback.gif",
				"bgcolor" => "white"), $_messageTbl->getHtml());

		echo we_html_element::htmlDocType() . we_html_element::htmlHtml($_head . $_body);
	}

	static function workflow($we_doc){
		if(we_workflow_utility::isUserInWorkflow($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"]) || permissionhandler::hasPerm("PUBLISH")){

			$_table = ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
					we_workflow_view::showFooterForNormalMode($we_doc, $GLOBALS['showPubl']) :
					($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
						we_workflow_view::showFooterForSEEMMode($we_doc, $GLOBALS['showPubl']) : ''));

			$_we_form = we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), $_table);

			echo we_html_element::htmlBody(array(
				'style' => 'margin: 8px 0px 0px 8px;background: url(' . EDIT_IMAGE_DIR . 'editfooterback.gif);',
				), $_we_form);
		} else {

			$_table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 4);
			$_table->setColContent(0, 0, we_html_tools::getPixel(16, 2));
			$_table->setColContent(0, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
			$_table->setColContent(0, 2, we_html_tools::getPixel(16, 2));
			$_table->setCol(0, 3, array("class" => "defaultfont"), g_l('modules_workflow', '[doc_in_wf_warning]'));

			echo we_html_element::htmlBody(array(
				'style' => 'margin: 0px 8px 0px 8px;background: url("' . EDIT_IMAGE_DIR . 'editfooterback.gif")',
				), $_table->getHtml());
		}
		echo '</html>';
	}

	/**
	 * @return void
	 * @desc Prints the footer for the normal mode
	 */
	static function normalMode($we_doc, $we_transaction, $haspermNew, $showPubl){
		$_normalTable = new we_html_table(array("cellpadding" => 0,
			"cellspacing" => 0,
			"border" => 0), 1, 1);
		$_pos = 0;
		$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));

		if($we_doc->ID){
			switch($we_doc->ContentType){
				case we_base_ContentTypes::TEMPLATE:
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("make_new_document", "javascript:top.we_cmd('new','" . FILE_TABLE . "','','" . we_base_ContentTypes::WEDOCUMENT . "','','" . $we_doc->ID . "');_EditorFrame.setEditorMakeNewDoc(false);"));
					$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
					break;
				case "object":
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("make_new_object", "javascript:top.we_cmd('new','" . OBJECT_FILES_TABLE . "','','objectFile','" . $we_doc->ID . "');_EditorFrame.setEditorMakeNewDoc(false);"));
					$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
					break;
			}
		}

		if(defined('WORKFLOW_TABLE') && $we_doc->IsTextContentDoc && $we_doc->ID){

			//	Workflow button
			$_ctrlElem = getControlElement('button', 'workflow'); //	look tag we:controlElement for details

			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_normalTable->addCol(2);
				$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("in_workflow", "javascript:put_in_workflow();"));
				$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		if($showPubl && $we_doc->ID && $we_doc->Published){

			//	Park button
			$_ctrlElem = getControlElement('button', 'unpublish'); //	look tag we:controlElement for details

			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_normalTable->addCol(2);
				$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("unpublish", "javascript:we_cmd('unpublish', '" . $we_transaction . "');"));
				$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		switch($we_doc->ContentType){
			case we_base_ContentTypes::WEDOCUMENT:
			case 'object':
			case 'objectFile':
			case we_base_ContentTypes::FOLDER:
			case 'class_folder':
				break;
			default:
				$filename = preg_replace('|/' . $we_doc->Filename . '.*$|', $we_doc->Filename . $we_doc->Extension, $we_doc->Path);
				$_edit_source = we_html_element::jsElement('
function editSource(){
	if(top.plugin.editSource){
		top.plugin.editSource("' . $filename . '","' . $we_doc->ContentType . '");
	}else{
		we_cmd("initPlugin","top.plugin.editSource(\'' . $filename . '\',\'' . $we_doc->ContentType . '\')");
	}
}
function editFile(){
	if(top.plugin.editFile){
		top.plugin.editFile();
	}else{
		we_cmd("initPlugin","top.plugin.editFile();");
	}
}');

				$_normalTable->addCol(2);
				if(we_base_moduleInfo::isActive('editor')){
					$_normalTable->setColContent(0, $_pos++, (stripos($we_doc->ContentType, 'text/') !== false ?
							we_html_button::create_button("startEditor", "javascript:editSource();") :
							we_html_button::create_button("startEditor", "javascript:editFile();"))
					);

					$_normalTable->setColContent(0, $_pos++, $_edit_source . we_html_tools::getPixel(10, 20));
				}
		}

		//	Save Button
		$_ctrlElem = getControlElement('button', 'save'); //	look tag we:controlElement for details
		if(!$_ctrlElem || !$_ctrlElem['hide']){
			$_normalTable->addCol(2);
			$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("save", "javascript:_EditorFrame.setEditorPublishWhenSave(false);we_save_document();"));
			$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			//}
		}
		if($we_doc->ContentType == we_base_ContentTypes::TEMPLATE){
			if(defined('VERSIONING_TEXT_WETMPL') && defined('VERSIONS_CREATE_TMPL') && VERSIONS_CREATE_TMPL && VERSIONING_TEXT_WETMPL){
				$_normalTable->addCol(2);
				$_normalTable->setColContent(0, $_pos++, we_html_button::create_button("saveversion", "javascript:_EditorFrame.setEditorPublishWhenSave(true);we_save_document();"));
				$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}

			$_normalTable->addCol(2);
			$_normalTable->setColContent(0, $_pos++, we_html_forms::checkbox("autoRebuild", false, "autoRebuild", g_l('global', '[we_rebuild_at_save]'), false, "defaultfont", " _EditorFrame.setEditorAutoRebuild( (this.checked) ? true : false );"));
			$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		} else if($showPubl){
			$_ctrlElem = getControlElement('button', 'publish');
			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$text = we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && we_schedpro::saveInScheduler($GLOBALS['we_doc']) ? 'saveInScheduler' : 'publish';
				$_normalTable->addCol(2);
				$_normalTable->setColAttributes(0, $_pos, array('id' => 'publish_' . $GLOBALS['we_doc']->ID));
				$_normalTable->setColContent(0, $_pos++, we_html_button::create_button($text, "javascript:_EditorFrame.setEditorPublishWhenSave(true);we_save_document();"));
				$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}


		if($we_doc->IsTextContentDoc && $haspermNew){

			$_ctrlElem = getControlElement('checkbox', 'makeSameDoc');

			if(!$_ctrlElem || !$_ctrlElem['hide']){

				$_normalTable->addCol(2);
				$_normalTable->setCol(0, $_pos++, ( ($_ctrlElem && $_ctrlElem['hide'] ) ? ( array('style' => 'display:none') ) : array('style' => 'display:block')), we_html_forms::checkbox("makeSameDoc", ( $_ctrlElem ? $_ctrlElem['checked'] : false), "makeSameDoc", g_l('global', '[we_make_same][' . $we_doc->ContentType . ']'), false, "defaultfont", " _EditorFrame.setEditorMakeSameDoc( (this.checked) ? true : false );", ( $_ctrlElem ? $_ctrlElem['readonly'] : false)));
				$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		switch($we_doc->ContentType){
			case we_base_ContentTypes::TEMPLATE:
				if(permissionhandler::hasPerm("NEW_WEBEDITIONSITE") || permissionhandler::hasPerm("ADMINISTRATOR")){
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('global', '[we_new_doc_after_save]'), false, "defaultfont", "_EditorFrame.setEditorMakeNewDoc( (this.checked) ? true : false );"));
					$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
				}
				break;
			case "object":
				if(permissionhandler::hasPerm("NEW_OBJECTFILE") || permissionhandler::hasPerm("ADMINISTRATOR")){
					$_normalTable->addCol(2);
					$_normalTable->setColContent(0, $_pos++, we_html_forms::checkbox("makeNewDoc", false, "makeNewDoc", g_l('modules_object', '[we_new_doc_after_save]'), false, "defaultfont", "_EditorFrame.setEditorMakeNewDoc( (this.checked) ? true : false );"));
					$_normalTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
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
		$_seeModeTable = new we_html_table(array("cellpadding" => 0,
			"cellspacing" => 0,
			"border" => 0), 1, 1);
		$_pos = 0;
		$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));

		//##############################	First buttons which are always needed
		//	Always button preview
		if(in_array(we_base_constants::WE_EDITPAGE_PREVIEW, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_PREVIEW){ // first button is always - preview, when exists
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("preview", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS["we_transaction"] . "');"));
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}

		// shop variants
		if(defined('SHOP_TABLE')){
			if($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT && in_array(we_base_constants::WE_EDITPAGE_VARIANTS, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->canHaveVariants(true) && $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_VARIANTS){ // first button is always - preview, when exists
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("shopVariants", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_VARIANTS . ",'" . $GLOBALS["we_transaction"] . "');"));
				$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		//	image-documents have no preview but thumbnailview instead ...
		if($GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_THUMBNAILS && in_array(we_base_constants::WE_EDITPAGE_THUMBNAILS, $GLOBALS['we_doc']->EditPageNrs)){
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("thumbnails", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_THUMBNAILS . ",'" . $GLOBALS["we_transaction"] . "');"));
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}

		//	Button edit !!!
		if($GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_CONTENT && in_array(we_base_constants::WE_EDITPAGE_CONTENT, $GLOBALS['we_doc']->EditPageNrs)){ // then button "edit"
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("edit", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_CONTENT . ", '" . $GLOBALS["we_transaction"] . "');"));
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}
		//	Button properties
		if(in_array(we_base_constants::WE_EDITPAGE_PROPERTIES, $GLOBALS['we_doc']->EditPageNrs) && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_SCHEDULER)){
			if(permissionhandler::isUserAllowedForAction("switch_edit_page", "we_base_constants::WE_EDITPAGE_PROPERTIES")){
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("properties", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_PROPERTIES . ", '" . $GLOBALS["we_transaction"] . "');"));
				$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		// Button workspace
		if(in_array(we_base_constants::WE_EDITPAGE_WORKSPACE, $GLOBALS['we_doc']->EditPageNrs) && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES)){

			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("workspace_button", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_WORKSPACE . ", '" . $GLOBALS["we_transaction"] . "');"));
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}


		//	Button scheduler
		if(in_array(we_base_constants::WE_EDITPAGE_SCHEDULER, $GLOBALS['we_doc']->EditPageNrs) && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PROPERTIES) &&
			we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) && permissionhandler::hasPerm("CAN_SEE_SCHEDULER")){
			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("schedule_button", "javascript:parent.editHeader.we_cmd('switch_edit_page', " . we_base_constants::WE_EDITPAGE_SCHEDULER . ", '" . $GLOBALS["we_transaction"] . "');"));
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}

		//	Button put in workflow
		if(/* $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_PROPERTIES && */ $GLOBALS['we_doc']->EditPageNr != we_base_constants::WE_EDITPAGE_SCHEDULER && // then button "workflow"
			defined('WORKFLOW_TABLE') && $we_doc->IsTextContentDoc && $we_doc->ID){

			$_ctrlElem = getControlElement('button', 'workflow'); //	look tag we:controlElement for details

			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("in_workflow", "javascript:put_in_workflow();"));
				$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
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
				$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("unpublish", "javascript:we_cmd('unpublish', '" . $we_transaction . "');"));
				$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		//
		//	2. we always need the buttons -> save and publish
		//
		$_ctrlElem = getControlElement('button', 'save'); //	look tag we:controlElement for details
		if(!$_ctrlElem || !$_ctrlElem['hide']){

			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("save", "javascript:_EditorFrame.setEditorPublishWhenSave(false);we_save_document();"));
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}


		//
		// 3. public when save and make same doc new.
		//
		if($showPubl){
			$_ctrlElem = getControlElement('button', 'publish'); //	look tag we:controlElement for details

			if(!($_ctrlElem && $_ctrlElem['hide'])){

				$_seeModeTable->addCol(2);
				$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), we_html_button::create_button("publish", "javascript:_EditorFrame.setEditorPublishWhenSave(true);we_save_document();"));
				$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
			}
		}

		if($we_doc->IsTextContentDoc && $haspermNew && ($GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_CONTENT || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_PREVIEW || $GLOBALS['we_doc']->EditPageNr == we_base_constants::WE_EDITPAGE_THUMBNAILS)){

			//	makesamedoc only when not in edit_include-window
			$_ctrlElem = getControlElement('checkbox', 'makeSameDoc');

			$showPubl_makeSamNew = ($_ctrlElem && $_ctrlElem['hide'] ? '<div style="display: hidden;">' : '') .
				we_html_element::jsElement('
								if(!top.opener || !top.opener.win){
									document.writeln("<!--");
								}') .
				we_html_forms::checkbox("makeSameDoc", ( $_ctrlElem ? $_ctrlElem['checked'] : false), "makeSameDoc", g_l('global', '[we_make_same][' . $we_doc->ContentType . ']'), false, "defaultfont", " _EditorFrame.setEditorMakeSameDoc( (this.checked) ? true : false );", ( $_ctrlElem ? $_ctrlElem['readonly'] : false)) .
				we_html_element::jsElement('
								if(!top.opener || !top.opener.win){
									document.writeln(\'-\' + \'-\' + \'>\');
								}') .
				($_ctrlElem && $_ctrlElem['hide'] ? '</div>' : '');


			$_seeModeTable->addCol(2);
			$_seeModeTable->setCol(0, $_pos++, array("valign" => "top"), $showPubl_makeSamNew);
			$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
		}

		//
		//	4. show delete button to delete this document, not in edit_include-window
		//
		$canDelete = ( (!we_base_request::_(we_base_request::BOOL, 'SEEM_edit_include')) && (($we_doc instanceof we_objectFile) ? permissionhandler::hasPerm('DELETE_OBJECTFILE') : permissionhandler::hasPerm('DELETE_DOCUMENT')));
		if($canDelete){
			$_ctrlElem = getControlElement('button', 'delete'); //	look tag we:controlElement for details
			if(!$_ctrlElem || !$_ctrlElem['hide']){
				$_seeModeTable->addCol(2);

				$_seeModeTable->setColContent(0, $_pos++, we_html_tools::getPixel(10, 20));
				$_seeModeTable->setCol(0, $_pos++, array('valign' => 'top'), we_html_button::create_button("image:btn_function_trash", "javascript:if(confirm('" . g_l('alert', '[delete_single][confirm_delete]') . "')){we_cmd('delete_single_document','','" . $we_doc->Table . "','1');}"));
			}
		}
		echo $_seeModeTable->getHtml();
	}

}
