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
//start autoloader!
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$cmd = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
we_html_tools::protect(null, ($cmd === 'loggingOut' ? WEBEDITION_DIR . 'index.php' : ''));

if(!$cmd){
	t_e('call without command, might be an error');
	exit();
}
$skipJS = false;

function findInclude($cmd){
	global $skipJS;
	switch($cmd){
		case ''://empty command
			exit();
		case 'we_selector_delete':
			if(isset($_SESSION['weS']['seemForOpenDelSelector']['Table'])){
				unset($_SESSION['weS']['seemForOpenDelSelector']['Table']);
			}
		//no break
		case 'we_selector_category':
		case 'we_selector_directory':
		case 'we_selector_document':
		case 'we_selector_file':
		case 'we_selector_image':
			we_selector_file::getSelectorFromRequest();
			return true;
		case 'selectorEdit':
			return 'we_editors/selectorEdit.inc.php';
		case 'selectorBrowse':
			return 'we_selectorBrowse.inc.php';
		case 'selectorBrowseCmd':
			return 'we_selectorBrowseCmd.inc.php';
		case 'we_fileupload_editor':
			we_fileupload_ui_editor::showFrameset();
			return true;
		case 'backupLog':
			we_backup_wizard::showLog();
			return true;
		case 'backup_cmd':
			we_backup_cmd::getHTML();
			return true;
		case 'phpinfo':
		case 'sysinfo':
			we_dialog_sysinfo::getDialog($cmd);
			return true;
		case 'versions_preview':
			$ver = new we_versions_preview();
			$ver->showHtml();
			return true;
		case 'versions_wizard':
			we_versions_wizard::showFrameset();
			return true;
		case 'versioning_log':
			we_versions_logView::showFrameset();
			return true;
		case 'import_files':
			$import_object = new we_import_files();
			echo $import_object->getHTML();
			return true;
		case 'loadSidebarDocument':
			we_sidebar_frames::getHTMLContent();
			return true;
		case 'siteImport':
		case 'siteImportCreateWePageSettings':
		case 'siteImportSaveWePageSettings':
		case 'updateSiteImportTable':
			$import_object = new we_import_site();
			echo $import_object->getHTML();
			return true;
		case 'loadTree':
			we_export_tree::loadTree();
			return true;
		case 'open_tag_wizzard':
			return 'weTagWizard/we_tag_wizzard.inc.php';
		case 'change_passwd':
			we_users_changePassword::showDialog();
			return true;
		case 'exit_delete':
		case 'exit_move':
		case 'home':
		case 'reset_home':
		case 'open_cockpit':
			we_main_cockpit::getEditor();
			return true;
		case 'logout':
			we_main_session::logout();
			return true;
		case 'loggingOut':
			we_main_session::loggingOut();
			return true;
		case 'openColorChooser':
			return 'we_editors/we_colorChooser.inc.php';
		case 'show_formmail_log':
			return 'we_editors/weFormmailLog.inc.php';
		case 'show_formmail_block_log':
			return 'we_editors/weFormmailBlockLog.inc.php';
		case 'add_dt_template':
		case 'change_docType':
		case 'deleteDocType':
		case 'deleteDocTypeok':
		case 'delete_dt_template':
		case 'doctypes':
		case 'dt_add_cat':
		case 'dt_delete_cat':
		case 'newDocType':
		case 'save_docType':
			we_dialog_doctypeEdit::getDialog();
			return true;
		case 'rebuild':
			$wiz=new we_rebuild_wizard();
			$wiz->showFrameset();
			return true;
		case 'help':
			return '';
		case 'info':
			we_dialog_info::getFullDialog();
			return true;
		case 'openPreferences':
			we_base_preferences::showFrameSet();
			return true;
		case 'editor_preferences':
			we_editor_preferences::show();
			return true;
		case 'editThumbs':
			return 'we_editors/we_thumbnails.inc.php';
		case 'editNewCollection':
			return 'we_editors/we_newCollection.inc.php';
		case 'editMetadataFields':
			return 'we_editors/edit_metadatafields.inc.php';
		case 'show':
			$GLOBALS['FROM_WE_SHOW_DOC'] = true;
			return 'we_showDocument.inc.php';
		case 'open_url_in_editor': // Beim ungewollten Verlassen (Klick auf Link im Bearbeitenmodus) des Editors wird die Location auf diese Seite weitergeleitet. Hier wird dann ein Kommando gebildet
			echo we_html_tools::getHtmlTop('', '', '', we_SEEM::getJavaScriptCommandForOneLink('<a href="' . we_base_request::_(we_base_request::URL, 'we_cmd', '', 1) . '">l</a>'), we_html_element::htmlBody());
			return true;
		case 'open_form_in_editor': // Formular wird an dieses Skript umgeleitet, hier wird ein Kommando daraus gebaut, um das Dokument korrekt zu �ffnen
			we_SEEM::openFormInEditor();
			return true;
		case 'open_extern_document'; // wird ben�tigt um ein externes Dokument aufzurufen
			we_SEEM::getExtDocFrameset();
			return true;
		case 'openExtDoc_footer':
			we_SEEM::getExtDocFooter();
			return true;
		case 'openExtDoc_header':
			we_SEEM::getExtDocHeader();
			return true;
		case 'openExtDoc_content':
			we_SEEM::getExtDocContent();
			return true;
		case 'edit_document_with_parameters':
			$GLOBALS['parastr'] = we_base_request::_(we_base_request::RAW_CHECKED, 'we_cmd', '', 4);
		case 'edit_document':
		case 'edit_folder':
		case 'new_document':
		case 'new_folder':
			return 'we_editors/we_edit_frameset.inc.php';
		case 'edit_include_document':
			$SEEM_edit_include = true;
			require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/webEdition.php');
			return true;
		case 'reload_edit_header':
		case 'load_edit_header':
			return 'we_editors/we_editor_header.inc.php';
		case 'load_edit_footer':
		case 'reload_editfooter':
			we_editor_footer::show();
			return true;
		case 'do_import':
			return 'we_editors/we_import_editor.inc.php';
		//case 'delete_alias':
		case 'weNaviEditor':
			we_navigation_navigation::naviEditor();
			return true;
		case 'add_cat':
		case 'add_entry_to_list':
		case 'add_link_to_linklist':
		case 'add_navi':
		case 'change_link':
		case 'change_linklist':
		case 'copyDocumentSelect':
		case 'copyDocument':
		case 'del_thumb':
		case 'delete_all_cats':
		case 'delete_all_navi':
		case 'delete_cat':
		case 'delete_link':
		case 'delete_linklist':
		case 'delete_list':
		case 'delete_navi':
		case 'doImage_convertGIF':
		case 'doImage_convertJPEG':
		case 'doImage_convertPNG':
		case 'doImage_crop':
		case 'do_add_thumbnails':
		case 'doctype_changed':
		case 'down_entry_at_list':
		case 'down_link_at_list':
		case 'insert_entry_at_list':
		case 'insert_link_at_linklist':
		case 'load_editor':
		case 'new_alias':
		case 'publish':
		case 'reload_hot_editpage':
		case 'reload_editpage':
		case 'remove_image':
		case 'resizeImage':
		case 'restore_defaults':
		case 'revert_published':
		case 'rotateImage':
		case 'save_document':
		case 'switch_edit_page':
		case 'template_changed':
		case 'unpublish':
		case 'up_entry_at_list':
		case 'up_link_at_list':
		case 'update_file':
		case 'update_image':
		case 'wrap_on_off':
		//variants
		case 'insert_variant':
		case 'move_variant_down':
		case 'move_variant_up':
		case 'preview_variant':
		case 'remove_variant':
			return 'we_editors/we_editor.inc.php';
		case 'edit_linklist':
		case 'edit_link':
		case 'edit_link_at_class':
		case 'edit_link_at_object':
			return 'we_editors/we_linklistedit.inc.php';
		case 'delete':
			if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 1)){
				we_base_delete::getDialog();
			} else {
				we_main_cockpit::getEditor();
			}
			return true;
		case 'move':
			if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 1)){
				we_base_move::getDialog();
			} else {
				we_main_cockpit::getEditor();
			}
			return true;
		case 'addToCollection':
			if(we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 1)){
				we_dialog_addToCollection::getDialog();
			} else {
				we_main_cockpit::getEditor();
			}
			return true;

		case 'delete_single_document':
		case 'do_delete':
			we_base_delete::getDialog();
			return true;
		case 'delInfo':
			we_base_delete::getDelInfo();
			return true;
		case 'delFrag':
			we_dialog_deleteProgress::getDialog();
			return true;
		case 'loadMultiEditor':
			we_main_multiContentFrame::getHTML();
			return true;
		case "showerrorlog":
			we_dialog_errorlog::getDialog();
			return true;
		case 'do_move':
		case 'move_single_document':
			we_base_move::getDialog();
			return true;
		case 'moveInfo':
			we_base_move::getMoveInfo();
			return true;
		case 'do_addToCollection':
			we_dialog_addToCollection::getDialog();
			return true;
		case 'show_binaryDoc':
			we_binaryDocument::showBinaryDoc();
			return true;
		case 'browse_server':
			return 'we_editors/we_sfileselector_frameset.inc.php';
		case 'make_backup':
			we_backup_wizard::showBackupFrameset();
			return true;
		case 'recover_backup':
			we_backup_wizard::showRecoverFrameset();
			return true;
		case 'messageConsole':
			we_dialog_messageConsole::getDialog();
			return true;
		case 'import':
			we_import_wizard::getFrameset();
			return true;
		case 'export':
			return 'we_modules/export/export_frameset.php';
		case 'copyFolder':
			we_dialog_copyFolder::getDialog();
			return true;
		case 'copyWeDocumentCustomerFilter':
			we_customer_copyWeDocumentFilter::getDialog();
			return true;
		case 'changeLanguageRecursive':
		case 'changeTriggerIDRecursive':
			we_document::changeRecursiveRequest($cmd);
			return true;
		case 'add_thumbnail':
			return 'we_editors/add_thumbnail.inc.php';
		case 'image_convertJPEG':
		case 'image_crop':
		case 'image_resize':
		case 'image_rotate':
			return 'we_editors/image_edit.inc.php';
		//  stuff about accessibility/validation
		case 'checkDocument':
			return 'we_editors/checkDocument.inc.php'; //  Here request is performed
		case 'customValidationService':
			return 'we_editors/customizeValidation.inc.php'; //  edit parameters
		case 'widget_cmd':
			we_main_cockpit::processCommand();
			return true;
		case 'tool_weSearch_edit':
			$_REQUEST['tool'] = 'weSearch';
			return 'we_tools/tools_frameset.php';
		case 'loadJSConsts':
			echo we_base_jsConstants::process(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1));
			$skipJS = true;
			return true;

		default:
			//	In we.inc.php all names of the active modules have already been searched
//	so we only have to use the array $GLOBALS['_we_active_integrated_modules']
			list($m, $m2) = explode('_', $cmd);
			$m = ($m === 'we' || $m === 'module' ? $m2 : $m);
			if(in_array($m, $GLOBALS['_we_active_integrated_modules'])){
				if(($INCLUDE = include(WE_MODULES_PATH . $m . '/we_cmd_' . $m . '.inc.php'))){
					return $INCLUDE;
				}
			}
			// search tools for command
			if(($INCLUDE = we_tool_lookup::getPhpCmdInclude())){
				return $INCLUDE;
			}
			//	In we.inc.php all names of the installed modules have already been searched
			//	so we only have to use the array $we_active_integrated_modules

			$mods = we_base_moduleInfo::getIntegratedModules(false);
			foreach($mods as $m){
				if($cmd == $m['name'] . '_edit' && !we_base_moduleInfo::isActive($m['name'])){
					$GLOBALS['moduleName'] = $m['text_short'];
					we_modules_frame::showNotActivatedMsg();
					return true;
				}
			}
			//	This is ONLY used in the edit-mode of the documents.
			//	This statement prevents the page from being reloaded.
			t_e('error', 'command \'' . $cmd . '\' not known!');
			exit('command \'' . $cmd . '\' not known!');
	}
}

if(($inc = findInclude($cmd))){
	if($inc !== true){
		require((substr($inc, 0, 5) === 'apps/' ? WEBEDITION_PATH : WE_INCLUDES_PATH) . $inc);
	}
	//  When pressing a link in edit-mode, the page is being reloaded from
	//  webedition. If a webedition link was pressed this page shall not be
	//  reloaded. All entries in this array represent values for we_cmd[0]
	//  when the javascript command shall NOT be inserted (p.ex while saving the file.)
	//	This is ONLY used in the edit-mode of the documents.
	//  This statement prevents the page from being reloaded
	switch($cmd){
		case 'siteImport':
		case 'import_images':
		case 'getWeDocFromID':
		case 'rebuild':
		case 'open_url_in_editor':
		case 'open_form_in_editor':
		case 'users_unlock':
		case 'edit_document':
		case 'load_editor':
		case 'load_edit_header':
		case 'load_edit_footer':
		case 'exchange':
		case 'validateDocument':
		case 'show':
		case 'we_fileupload_editor':
			break;
		default:
			if(!$skipJS){
				//FIXME: check if we can skip/remove this from all calls and assign to appropreate
				echo we_html_element::jsElement('parent.openedWithWE=true;');
			}
			break;
	}
}
